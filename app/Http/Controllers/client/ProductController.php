<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Property;
use App\models\PropertyValue;
use App\models\Category;
use App\models\ProductProperty;
use App\models\CateProduct;
use App\models\ProductOrder;
use App\models\Product;
use App\models\Image;
use App\models\ProductsImport;
use App\models\ImportProduct;
use App\models\CateProductImport;
use App\models\ExportProduct;
use App\models\ProductExport;
use Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\ImportProductsRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\CreateExportRequest;
use Illuminate\Support\Facades\DB;
use App\User;

class ProductController extends Controller
{
    public function getProperties(Request $request)
    {
        $keyword = $request->keyword;
        if ($keyword) {
            return Auth::user()->propUser()
                                ->where('prop_name', 'LIKE', "%$keyword%")
                                ->paginate(10)
                                ->toJson();
        }
        return Auth::user()->propUser()->paginate(10)->toJson();
    }

    public function getEditProp($id)
    {
        $prop = Auth::user()->propUser()->where('id', $id)->first();
        if ($prop) {
            $vals = $prop->hasValue()->get();
            $checkActive = false;
            foreach ($vals as $k => $val) {
                $productsOf = $val->products()->count();
                $vals[$k]->status = $productsOf > 0 ? "ACTIVE" : "NONE";
                if ($productsOf > 0 && !$checkActive) {
                    $checkActive = true;
                }
            }
            $prop->vals = $vals;
            $prop->status = $checkActive ? "ACTIVE" : "NONE";
        }
        return response()->json($prop);
    }

    public function destroyValueProp($id)
    {
        $user = Auth::user();
        $val = $user->propertiesValue()->find($id);
        if ($val) {
            $countPropHas = ProductProperty::where('prop_value_id', $val->id)->count();
            if ($countPropHas > 0) {
                return response()->json([
                    'deleted' => false,
                    'message' => 'Có sản phẩm đang sử dụng thuộc tính này',
                    'count_prod' => $countPropHas 
                ]);
            }
            $val->delete();
            return response()->json([
                'deleted' => true
            ]);
        }
        return response()->json([
            'deleted' => false,
            'message' => 'Không tìm thấy giá trị'
        ]);
    }

    public function updateProp($id, Request $request)
    {
        $user = Auth::user();
        $request->validate(
            [
                'prop_name' => 'required',
                'prop_val' => 'nullable|array'
            ],
            [
                'prop_name.required' => 'Vui lòng điền tên thuộc tính',
                'prop_name.array' => 'Giá trị thêm vào phải là mảng'
            ]
        );
        $propName = $request->prop_name;
        $propVal = $request->prop_val;
        $user->propUser()->find($id)->update([
            'prop_name' => $propName
        ]);
        $prop = $user->propUser()->find($id);
        $arrInsert = [];
        foreach ($propVal as $item) {
            $item = [
                'prop_id' => $prop->id,
                'value' => trim($item),
                'user_id' => $user->adminId(),
                'staff_id' => $user->id
            ];
            array_push($arrInsert, $item);
        }
        PropertyValue::insert($arrInsert);
        return response()->json([
            'updated' => true
        ]);
    }

    public function destroyProperty($id)
    {
        $prop = Auth::user()->propUser()->find($id);
        if ($prop) {
            $vals = $prop->hasValue()->get();
            $checkActive = false;
            foreach ($vals as $k => $val) {
                $productsOf = $val->products()->count();
                if ($productsOf > 0) {
                    $checkActive = true;
                    break;
                }
            }
            if ($checkActive) {
                return response()->json([
                    'deleted' => false,
                    'message' => 'Có sản phẩm đang sử dụng thuộc tính'
                ]);
            } else {
                $this->deletePropertiesCore($id);
                return response()->json([
                    'deleted' => true
                ]);
            }
        }
        return response()->json([
            'deleted' => false,
            'message' => 'Không tìm thấy thuộc tính'
        ]);
    }

    private function deletePropertiesCore($id)
    {
        PropertyValue::where('prop_id', $id)->delete();
        Property::find($id)->delete();
    }

    public function createProp(Request $request)
    {
        $user = Auth::user();
    	$request->validate(
            [
        		'prop_name' => 'required',
        		'prop_val' => 'required|array'
            ],
            [
                'prop_name.required' => 'Vui lòng điền tên thuộc tính',
                'prop_val.required' => 'Vui lòng điền giá trị của thuộc tính',
                'prop_name.array' => 'Giá trị thêm vào phải là mảng'
            ]
        );
    	$propName = $request->prop_name;
    	$propVal = $request->prop_val;
        $newProp = Property::create([
            'prop_name' => $propName,
            'user_id' => $user->adminId(),
            'staff_id' => $user->id
        ]);
        $arrInsert = [];
    	foreach ($propVal as $item) {
            $item = [
                'prop_id' => $newProp->id,
                'value' => trim($item),
                'user_id' => $user->adminId(),
                'staff_id' => $user->id
            ];
            array_push($arrInsert, $item);
    	}
        PropertyValue::insert($arrInsert);
    	$newProp = Property::with('hasValue')->find($newProp->id);
    	return response()->json([
    		'created' => true,
    		'prop' => $newProp
    	]);
    }

    public function getAddProduct()
    {
    	$props = Auth::user()->propUser()->select('id', 'prop_name')->get();
    	foreach ($props as $k => $prop) {
    		$props[$k]->value = $prop->hasValue()->select('id', 'value')->get(); 
    	}
    	$cates = Auth::user()->catesUser()->get();
    	return response()->json([
    		'props' => $props,
    		'cates' => $cates
    	]);
    }

    public function doUploads(Request $request)
    {
        $request->validate(
            [
             'file' => 'required|mimes:jpeg,png,jpg|max:3000',
            ],
            [
                'file.mimes' => 'Vui lòng chọn ảnh có định dạng .jpeg, .jpg hoặc .png',
                'file.max' => 'Vui lòng chọn ảnh có kích cỡ nhỏ hơn 3M',
            ]
         );
        $path = $request->file('file')->store('public/images/products/'. Auth::id());
        $path = "/storage/".substr($path, 7);
        return response()->json([
            'success' => true,
            'path' => $path
        ]);
    }

    public function destroyImage(Request $request)
    {
        $request->validate([
            'path' => 'required'
        ]);
        $path = $request->path;
        $exist = $this->clearImage($path);
        if ($exist) {
            return response()->json(['success' => true]);
        }
        return response()->json([
            'errors' => true,
            'message' => 'không tìm thấy ảnh'
        ]);
    }

    public function createProduct(CreateProductRequest $request)
    {
        $product = $request->product;
        $childs = $request->childs;
        $images = $request->images;
        $user = User::find(Auth::id());
        $userId = $user->id;

        $name = $product['name'];
        $code = $product['code'] ? strtoupper($product['code']) : $this->generalCode();
        $price = formatPriceValue($product['price']);
        $priceImp = formatPriceValue($product['priceImp']);
        $thumb = $product['thumb']['path'];
        $parent = 0;
        $quantity = $product['quantity'] ? $product['quantity'] : 0;
        $countChilds = count($childs);
        $newProdId = $this->createProductCore($user, $name, $code, $priceImp, $price, $thumb, 0, $countChilds, $parent);

        $importId = null;
        $totalAmount = 0;
        if ($quantity > 0) {
            $importId = $this->createInfoImportProducts($user, $quantity, 0, 0);
        }

        if (count($childs) === 0) {
            $totalAmount = intval($quantity)*intval($priceImp);
        }

        $cate = $user->catesUser()->find($product['cate']);
        if ($cate) {
            if ((! count($childs) > 0) && ($importId !== null) && ($importId > 0)) {
                $this->createProductsImport($newProdId, $importId, $quantity, $priceImp, 0);
            }

            $this->createCateProd($product['cate'], $newProdId);
            //update category 
            $dataCate['totalProd'] = count($childs) > 0 ? intval($cate->total_prods) + count($childs) : intval($cate->total_prods) + 1;
            $dataCate['thumb'] = $product['thumb']['path'];
            $this->updateInfoOfCate($cate->id, $dataCate);
        }

        foreach ($images as $image) {
            $this->createProdImage($image['path'], $newProdId);
        }

        foreach ($childs as $child) {
            $properties = '';
            foreach ($child['props'] as $value) {
                $prop_val = PropertyValue::find($value);
                if ($prop_val) {
                    $properties .= $prop_val->value.'/ ';
                }
            }
            $properties = trim($properties);
            $len = strlen($properties);
            if ($len) {
                $properties = substr($properties, 0, $len - 1);
            }

            $name = $product['name'];
            $code = $child['code'] ? strtoupper($child['code']) : $this->generalCode();
            $price = formatPriceValue($child['price']);
            $priceImp = formatPriceValue($child['priceImp']);
            $thumb = $child['lastImg'] ? $child['lastImg'] : $product['thumb']['path'];
            $parent = $newProdId;
            $quantity = $child['quantity'] ? $child['quantity'] : 0;
            $newChildId = $this->createProductCore($user, $name, $code, $priceImp, $price, $thumb, 0, 0, $parent, $properties);

            if ($cate) {
                $this->createCateProd($product['cate'], $newChildId);
            }

            foreach ($child['props'] as $value) {
                $this->createProdProp($newChildId, $value);
            }

            if (($importId !== null) && ($importId > 0)) {
                $this->createProductsImport($newChildId, $importId, $quantity, $priceImp, 0);
            }

            $totalAmount += intval($quantity) * intval($priceImp);

            foreach ($child['images'] as $img) {
                $this->createProdImage($img['path'], $newChildId);
            }
        }

        if ($importId !== null) {
            $importion = ImportProduct::where('id', $importId)->update([
                'total_amount' => $totalAmount
            ]);
        }

        return response()->json([
            'created' => true
        ]);
    }

    public function createCateProd(int $cateId, int $prodId)
    {
        $newCateProd = new CateProduct;
        $newCateProd->cate_id = $cateId;
        $newCateProd->product_id = $prodId;
        $newCateProd->save();
        return $newCateProd->id;
    }

    public function createProdProp(int $prodId, int $propValId)
    {
        $prodProp = ProductProperty::where('prod_id', $prodId)
                                    ->where('prop_value_id', $propValId)
                                    ->first();
        $checkValue = PropertyValue::find($propValId);
        if (! $prodProp && $checkValue) {
            $newProdProp = new ProductProperty;
            $newProdProp->prod_id = $prodId;
            $newProdProp->prop_value_id = $propValId;
            $newProdProp->save();
            return $newProdProp->id;
        }
        return null;
    }

    public function deletePropOfProd(int $prodId, array $propUpdate) 
    {
        $prodProp = ProductProperty::where('prod_id', $prodId)
                                    ->get();
        foreach ($prodProp as $item) {
            if (! in_array($item->prop_value_id, $propUpdate)) {
                $item->delete();
            }
        }
    }

    public function createProdImage($path, int $prodId)
    {
        $newImg = new Image;
        $newImg->image_src = $path;
        $newImg->target_id = $prodId;
        $newImg->type = "PRODUCT";
        $newImg->save();
        return $newImg->id;
    }

    public function generalCode()
    {
        $user = Auth::user();
        $count = $user->productUsers()->count();
        $count = $count + 1;
        $num = $code = str_pad($count, 3, '0', STR_PAD_LEFT);
        $check = $user->productUsers()->where('prod_code', $num)->count();
        $index = 1;
        while ($check) {
            $code = $num . strtoupper(str_random($index));
            $check = $user->productUsers()->where('prod_code', $code)->count();
            $index++;
        }
        return $code;
    }

    public function getListProducts(Request $request)
    {
        $sortBy = $request->sortBy;
        $timeFrom = $request->timeFrom;
        $timeTo = $request->timeTo;
        $status = $request->status;
        $category = $request->category;
        $keyword = $request->keyword;
        $typeDate = 'created_at';

        $arrWhere = [
            ['products.status', 'like', "%$status%"],
            ['products.parent_id', '=', 0]
        ];

        if ($timeFrom) {
            $timeFrom = date("Y-m-d H:i:s", strtotime($timeFrom));
            array_push($arrWhere, ['products.'.$typeDate, '>=', $timeFrom]);
        }
        if ($timeTo) {
            $timeTo = date("Y-m-d H:i:s", strtotime($timeTo));
            array_push($arrWhere, ['products.'.$typeDate, '<=', $timeTo]);
        }
        if ($category) {
            array_push($arrWhere, ['categories.id', '=', $category]);
        }

        $products = Auth::user()
                        ->productsUser()
                        ->where($arrWhere)
                        ->where('products.prod_name', 'like', "%$keyword%")
                        ->count();

        if ($products > 0) {

            return Auth::user()
                        ->productsUser()
                        ->where($arrWhere)
                        ->where('products.prod_name', 'like', "%$keyword%")
                        ->orderBy($typeDate, $sortBy)
                        ->paginate(10)
                        ->toJson();
        }

        return Auth::user()
                    ->productsUser()
                    ->where($arrWhere)
                    ->where('products.prod_code', 'like', "%$keyword%")
                    ->orderBy($typeDate, $sortBy)
                    ->paginate(10)
                    ->toJson();

    }

    public function getProductChilds($id)
    {
        return Auth::user()
                    ->productsUser()
                    ->where('products.parent_id', $id)
                    ->get()
                    ->toJson();
    }


    public function findProduct($id)
    {
        $products = Auth::user()->productsUser()
                                ->leftJoin('cate_products', 'cate_products.product_id', '=', 'products.id')
                                ->leftJoin('categories', 'categories.id', '=', 'cate_products.cate_id')
                                ->select('products.*', 'categories.cate_name')
                                ->where('products.id', $id)
                                ->first();
        return $products;
    }

    public function getEditProduct($id)
    {
        $user = Auth::user();
        $product = $user->productUsers()->with(['parent', 'properties', 'cates', 'images', 'childs' => function($query) {
                    $query->with('images', 'properties');
                }])->find($id);
        return response()->json($product);
    }

    public function updateCateOfProd($cateId, $prodId)
    {
        $cateProd = CateProduct::where('product_id', $prodId)->first();
        if ($cateProd) {
            $cateProd->cate_id = $cateId;
            $cateProd->product_id = $prodId;
            $cateProd->save();
            return $cateProd->id;
        }
        return null;
    }

    public function updateInfoOfCate($cateId, array $data)
    {
        $name = isset($data['cateName']) ? $data['cateName'] : null;
        $thumb = isset($data['thumb']) ? $data['thumb'] : null;
        $des = isset($data['des']) ? $data['des'] : null;
        $parentId = isset($data['parentId']) && ($data['parentId'] >= 0) ? $data['parentId'] : null;
        $totalProd = isset($data['totalProd']) && ($data['totalProd'] >= 0) ? $data['totalProd'] : null;
        $totalOrder = isset($data['totalOrder']) && ($data['totalOrder'] >= 0) ? $data['totalOrder'] : null;
        $totalAmount = isset($data['totalAmount']) && ($data['totalAmount'] >= 0) ? $data['totalAmount'] : null;

        $cate = Category::find($cateId);
        if ($cate) {
            $cate->cate_name = ($name !== null) ? $name : $cate->cate_name;
            $cate->cate_thumb = ($thumb !== null) ? $thumb : $cate->cate_thumb;
            $cate->cate_des = ($des !== null) ? $des : $cate->cate_des;
            $cate->parent_id = ($parentId !== null) && ($parentId >= 0) ? $parentId : $cate->parent_id;
            $cate->total_prods = ($totalProd !== null) && ($totalProd >= 0) ? $totalProd : $cate->total_prods;
            $cate->total_order = ($totalOrder !== null) && ($totalOrder >= 0) ? $totalOrder : $cate->total_order;
            $cate->total_amount = ($totalAmount !== null) && ($totalAmount >= 0) ? $totalAmount : $cate->total_amount;
            $cate->save();
            return $cate->id;
        }
        return null;
    }

    /**
     * make properties label for properties column of record product table
     * @param  array  $data ex: $data[propertyId => propertyValueId]
     * @return [string]       properties label
     */
    public function makeLabelProperties(array $data)
    {
        $properties = '';
        foreach ($data as $valueId) {
            $prop_val = PropertyValue::find($valueId);
            if ($prop_val) {
                $properties .= $prop_val->value.'/';
            }
        }
        $properties = substr($properties, 0, strlen($properties) - 1);
        return $properties;
    }

    public function updateProduct($id, UpdateProductRequest $request)
    {
        $parent = $request->product;
        $childs = $request->childs;
        $images_remove = $request->images_remove;
        $images_new = $request->images_new;
        $thumb_new = $request->thumb_new;
        $childs_del = $request->childs_del;
        $user = User::find(Auth::id());
        $userId = $user->id;

        $product = $user->productUsers()->find($parent['id']);
        if ($product) {
            $quantityParent = 0;

            $cate = Auth::user()->catesUser()
                                ->where('id', $parent['cate'])
                                ->first();
            if ($cate) {
                //add and update childs
                $totalChilds = 0;
                $childsNew = 0;
                foreach ($childs as $child) {
                    $productChild = $user->productUsers()->find($child['id']);
                    $properties = $this->makeLabelProperties($child['props']);

                    if ($productChild) {
                        $totalChilds++;
                        $dataChild['name'] = $child['name'];
                        $dataChild['price'] = $child['price'];
                        $dataChild['priceImp'] = $child['priceImp'];
                        $dataChild['code'] = $child['code'];
                        $dataChild['properties'] = $properties;
                        if ($child['prod_thumb']) {
                            $dataChild['thumb'] = $child['prod_thumb'];
                        } else 
                        if ($thumb_new) {
                            $dataChild['thumb'] = $thumb_new['path'];
                        }
                        $quantityParent += $productChild->prod_quantity;
                        $this->updateProductCore($productChild->id, $user, $dataChild);

                        if (! $this->updateCateOfProd($cate->id, $productChild->id)) {
                            $this->createCateProd($cate->id, $productChild->id);
                        }

                        //update or create properties new for product childs
                        foreach ($child['props'] as $value) {
                            $this->createProdProp($productChild->id, $value);
                        }

                        //delete properties of product
                        $this->deletePropOfProd($productChild->id, $child['props']);

                        //add new image for product
                        foreach ($child['imagesNew'] as $image) {
                            $this->createProdImage($image['path'], $productChild->id);
                        }

                        //delete image of product
                        foreach ($child['imagesDel'] as $item) {
                            $img = Image::where('type', 'PRODUCT')
                                        ->where('id', $item)
                                        ->first();
                            if ($img) {
                                $this->clearImage($img->image_src);
                                $img->delete();
                            }
                        }

                    } else {
                        $childsNew++;
                        $totalChilds++;
                        //create new product child
                        $childCode = isset($child['code']) && $child['code'] ? $child['code'] : $this->generalCode();
                        $thumbChild = $child['prod_thumb'] ? $child['prod_thumb'] : $product->prod_thumb;
                        $newChildId = $this->createProductCore($user, $product->prod_name, $childCode, $child['priceImp'], $child['price'], $thumbChild, 0, 0, $product->id, $properties);

                        //add new product child in category
                        $this->createCateProd($cate->id, $newChildId);

                        //create properties for new product child
                        foreach ($child['props'] as $value) {
                            $this->createProdProp($newChildId, $value);
                        }

                        //add new image for product
                        foreach ($child['imagesNew'] as $image) {
                            $this->createProdImage($image['path'], $newChildId);
                        }
                    }
                }

                //delete childs
                foreach ($childs_del as $item) {
                    $prodChild = $user->productUsers()->find($item);
                    if ($prodChild) {
                        $childsNew--;
                        $totalChilds--;
                        $this->deleteProductCore($prodChild);
                    }
                }

                //update cateogory
                if (count($childs) === 0) {
                    $childsNew++;
                    $totalChilds++;
                }

                //update cate for parent
                if (! $this->updateCateOfProd($cate->id, $product->id)) {
                    $this->createCateProd($cate->id, $product->id);
                }
                
                $currentCate = $product->cates()->first();
                if ($currentCate && ($currentCate->id != $cate->id)) {
                    //update new category of product
                    $dataCate['totalProd'] = $cate->total_prods + $totalChilds;
                    $this->updateInfoOfCate($cate->id, $dataCate);
                    //update old category of product
                    $dataCurrentCate['totalProd'] = $currentCate->total_prods - $totalChilds;
                    $this->updateInfoOfCate($currentCate->id, $dataCurrentCate);
                } else {
                    //update new category of product
                    $dataCate['totalProd'] = $cate->total_prods + $childsNew;
                    $this->updateInfoOfCate($cate->id, $dataCate);
                    //update old category of product
                    $dataCurrentCate['totalProd'] = $currentCate->total_prods - $childsNew;
                    $this->updateInfoOfCate($currentCate->id, $dataCurrentCate);
                }
            }

            $data['name'] = $parent['name'];
            $data['price'] = formatPriceValue($parent['price']);
            $data['priceImp'] = formatPriceValue($parent['priceImp']);
            $data['code'] = strtoupper($parent['code']);
            $data['quantity'] = count($childs) > 0 ? $quantityParent : $parent['quantity'];
            $data['count_childs'] = count($childs);
            if ($thumb_new) {
                $data['thumb'] = $thumb_new['path'];
            }
            $this->updateProductCore($product->id, $user, $data);

            //add new image for product
            foreach ($images_new as $image) {
                $this->createProdImage($image['path'], $product->id);
            }

            //delete image of product
            foreach ($images_remove as $item) {
                $img = Image::where('type', 'PRODUCT')
                            ->where('id', $item)
                            ->first();
                if ($img) {
                    $this->clearImage($img->image_src);
                    $img->delete();
                }
            }

            return response()->json([
                'updated' => true
            ]);
        }

        return response()->json([
            'updated' => false,
            'message' => 'Không tìm thấy sản phẩm'
        ]);
    }

    public function destroyProduct($id)
    {   
        $product = Auth::user()->productUsers()->find($id);
        if ($product) {
            $this->deleteProductCore($product);
            return response()->json([
                'deleted' => true
            ]);
        }
        return response()->json([
                'error' => true,
                'message' => 'Không tìm thấy sản phẩm'
        ]);
    }

    public function deleteProductCore($product)
    {
        $ids = [];
        if (! $product->parent_id > 0) {
            $childs = Product::where('parent_id', $product->id)->get();
            foreach ($childs as $child) {
                $this->deleteCateProduct($child->id);
                $this->deleteProductProperties($child->id);
                $this->deleteImagesProduct($child->id);
                $this->clearImage($child->prod_thumb);
                array_push($ids, $child->id);
                $child->delete();
            }
        }
        $this->deleteCateProduct($product->id, true);
        $this->deleteProductProperties($product->id);
        $this->deleteImagesProduct($product->id);
        $this->clearImage($product->prod_thumb);
        $parent = Product::find($product->parent_id);
        if ($parent) {
            $user = User::find($parent->user_id);
            $dataParent['count_childs'] = $parent->count_childs - 1;
            $dataParent['quantity'] = $parent->prod_quantity - $product->prod_quantity;
            $dataParent['count_sold'] = $parent->count_sold - $product->count_sold;
            $this->updateProductCore($parent->id, $user, $dataParent);
        }
        array_push($ids, $product->id);
        $product->delete();
        return $ids;
    }

    public function deleteCateProduct(int $prodId, bool $parent = false)
    {
        $prodCate = CateProduct::where('product_id', $prodId)
                                ->get();
        foreach ($prodCate as $item) {
            $cate = Category::find($item->cate_id);
            $cate->total_prods--;
            $cate->cate_thumb = '';
            $cate->save();
            $item->delete();
        }
        return true;
    }

    public function deleteProductProperties($prodId)
    {
        $prodProp = ProductProperty::where('prod_id', $prodId)->get();
        foreach ($prodProp as $item) {
            $item->delete();
        }
        return true;
    }

    public function deleteImagesProduct($prodId)
    {
        $images = Image::where('target_id', $prodId)->where('type', 'PRODUCT')->get();
        foreach ($images as $item) {
            $this->clearImage($item->image_src);
            $item->delete();
        }
    }

    public function clearImage($path)
    {
        $realPath = 'public/'.substr($path, 8); //change path from /storage/.. to /public/...
        $exist = Storage::disk('local')->exists($realPath);
        if ($exist) {
            Storage::disk('local')->delete($realPath);
            return true;
        }
        return false;
    }

    public function getProductsTodo()
    {
        return Auth::user()->productsUser()->where('products.count_childs', 0)->get()->toJson();
    }

    public function updateProductCore(int $id, User $user, array $data)
    {
        $name = isset($data['name']) ? $data['name'] : null;
        $code = isset($data['code']) ? strtoupper(trim($data['code'])) : null;
        $price = isset($data['price']) ? formatPriceValue($data['price']) : null;
        $priceImp = isset($data['priceImp']) ? formatPriceValue($data['priceImp']) : null;
        $quantity = isset($data['quantity']) && ($data['quantity'] >= 0) ? $data['quantity'] : null;
        $thumb = isset($data['thumb']) ? $data['thumb'] : null;
        $countChilds = isset($data['count_childs']) && ($data['count_childs'] >= 0) ? $data['count_childs'] : null;
        $properties = isset($data['properties']) ? $data['properties'] : null;
        $countSold = isset($data['count_sold']) && ($data['count_sold'] >= 0) ? $data['count_sold'] : null;

        $product = $user->productUsers()->find($id);
        if ($product) {
            $product->prod_name = ($name !== null) ? $name : $product->prod_name;
            $product->prod_code = ($code !== null) ? $code : $product->prod_code;
            $product->prod_price = ($price !== null) && ($price >= 0) ? $price : $product->prod_price;
            $product->prod_price_imp = ($priceImp !== null) && ($priceImp >= 0) ? $priceImp : $product->prod_price_imp;
            $product->prod_quantity = $realQuantity = ($quantity !== null) && ($quantity >= 0) ? $quantity : $product->prod_quantity;
            $product->prod_thumb = ($thumb !== null) ? $thumb : $product->prod_thumb;
            $product->count_childs = ($countChilds !== null) && ($countChilds >= 0) ? $countChilds : $product->count_childs;
            $product->properties = ($properties !== null) ? $properties : $product->properties;
            $product->count_sold = $realCountSold = ($countSold !== null) && ($countSold >= 0) ? $countSold : $product->count_sold;
            $product->status = ((intval($realQuantity) - intval($realCountSold)) > 0) ? "HAS" : "CLEAR";
            $product->save();
            return $product->id;
        }
        return false;
    }

    public function createProductCore(User $user, $name, $code, $priceImp, $price, $thumb = '', $quantity = 0, $countChilds = 0, $parentId = 0, $properties = '', $countSold = 0)
    {
        $product = new Product; 
        $product->prod_name = $name;
        $product->user_id = $user->adminId();
        $product->staff_id = $user->id;
        $product->parent_id = $parentId;
        $product->prod_code = $code ? strtoupper(trim($code)) : $this->generalCode();
        $product->prod_price_imp = $priceImp;
        $product->prod_price = $price;
        $product->prod_quantity = $quantity ? $quantity : 0;
        $product->prod_thumb = $thumb;
        $product->count_childs = $countChilds;
        $product->properties = $properties;
        $product->count_sold = $countSold;
        $product->status = intval($quantity) - intval($countSold) > 0 ? "HAS" : "CLEAR";
        $product->save();
        return $product->id;
    }

    public function importProducts(ImportProductsRequest $request)
    {
        $imports = $request->products;
        $user = User::find(Auth::id());

        $totalInventory = 0;
        $totalAmount = 0;
        $totalQuantity = 0;
        $products = [];

        foreach ($imports as $key => $item) {
            $product = $user->productUsers()->find($item['id']);
            if ($product) {
                $products[$key] = $product;
                $totalInventory += intval($product->prod_quantity) - intval($product->count_sold);
                $totalAmount += intval($item['priceImp'])*intval($item['quantity_import']);
                $totalQuantity += $item['quantity_import'];
            }
        }

        $importId = $this->createInfoImportProducts($user, $totalQuantity, $totalInventory, $totalAmount);

        foreach ($imports as $key => $prod) {
            $product = $products[$key];
            if ($product) {
                $inventoryOfProd = intval($product->prod_quantity) - intval($product->count_sold);
                $this->createProductsImport($prod['id'], $importId, $prod['quantity_import'], $prod['priceImp'], $inventoryOfProd);
            }
        }

        return response()->json([
            'imported' => true
        ]);
    }

    public function generalCodeImport()
    {
        $user = Auth::user();
        $count = $user->importsUser()->count();
        $count = $count + 1;
        $num = $code = str_pad($count, 3, '0', STR_PAD_LEFT);
        $check = $user->importsUser()->where('import_code', $num)->count();
        $index = 1;
        while ($check) {
            $code = $num . strtoupper(str_random($index));
            $check = $user->importsUser()->where('import_code', $code)->count();
            $index++;
        }
        return $code;
    }

    public function createInfoImportProducts(User $user, $quantity, $inventory, $totalAmount, $canDestroy = true)
    {
        $newInfo = new ImportProduct;
        $newInfo->import_code = $this->generalCodeImport();
        $newInfo->user_id = $user->adminId();
        $newInfo->staff_id = $user->id;
        $newInfo->total_quantity = $quantity;
        $newInfo->inventory = $inventory;
        $newInfo->total_amount = $totalAmount;
        $newInfo->can_destroy = $canDestroy;
        $newInfo->status = "IMPORT";
        $newInfo->save();
        return $newInfo->id;
    }

    /**
     * make list product of import 
     * @param  int     $prodId    id product
     * @param  int     $importId  [id import]
     * @param  [type]  $quantity  [quantity of product]
     * @param  [type]  $priceImp  [price import of product]
     * @param  [type]  $inventory [quantity on storage of product]
     * @param  boolean $up        [up quantity of product or not up]
     * @return null or id of new record
     */
    public function createProductsImport(int $prodId, int $importId, $quantity, $priceImp, $inventory, $up = true)
    {
        $product = Product::find($prodId);
        if ($product) {
            if ($up) {
                $user = User::find(Auth::id());
                $dataUpdate['quantity'] = $product->prod_quantity + $quantity;
                $dataUpdate['priceImp'] = $priceImp;
                $this->updateProductCore($prodId, $user, $dataUpdate);
                //update for parent of product
                if ($product->parent_id) {
                    $parent = Product::find($product->parent_id);
                    if ($parent) {
                        $dataParent['quantity'] = $parent->prod_quantity + $quantity;
                        $this->updateProductCore($parent->id, $user, $dataParent);
                    }
                }
            }

            $newProd = new ProductsImport;
            $newProd->import_id = $importId;
            $newProd->prod_name = $product->prod_name;
            $newProd->prod_code = $product->prod_code;
            $newProd->properties = $product->properties;
            $newProd->prod_id = $prodId;
            $newProd->quantity_prod = $quantity;
            $newProd->price_imp = $priceImp;
            $newProd->inventory_prod = $inventory;
            $newProd->save();
            return $newProd->id;
        }
        return null;
    }

    public function findInfoImport($id)
    {
        $info = Auth::user()->imports()
                            ->where('import_products.id', $id)
                            ->first();
        if ($info && $info->status === 'CANCEL') {
            $info->user_cancel = ImportProduct::find($info->id)->userCancel()->select('name')->first();
            return $info;
        }
        return $info;
    }

    public function getListImports(Request $request)
    {
        $timeFrom = $request->timeFrom;
        $timeTo = $request->timeTo;

        $arrWhere = [];

        if ($timeFrom) {
            $timeFrom = date("Y-m-d H:i:s", strtotime($timeFrom));
            array_push($arrWhere, ['import_products.created_at', '>=', $timeFrom]);

        }
        if ($timeTo) {
            $timeTo = date("Y-m-d H:i:s", strtotime($timeTo));
            array_push($arrWhere, ['import_products.created_at', '<=', $timeTo]);
        }

        return Auth::user()
                    ->imports()
                    ->where($arrWhere)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->toJson();
    }

    public function getDetailProductImport($id)
    {
        $prods = ProductsImport::where('import_id', $id)->get();
        foreach ($prods as $key => $prod) {
            $product = Product::find($prod->prod_id);
            if ($product) {
                $prods[$key]->prod_thumb = $product->prod_thumb;
            } else {
                $prods[$key]->prod_thumb = '';
            }
        }
        return $prods;
    }

    public function getEntityImport($id)
    {
        $info = $this->findInfoImport($id);
        $products = [];
        $cates = [];
        if ($info) {
            $products = $this->getDetailProductImport($id);
        }
        return response()->json([
            'products' => $products,
            'info' => $info
        ]);
    }

    public function cancelImportion($id)
    {
        $user = User::find(Auth::id());
        $imp = $user->imports()->where('import_products.id', $id)->first();
        $checkCount = true;
        if ($imp) {
            $impInfo = ImportProduct::find($imp->id);
            $products = $impInfo->products()->get();
            $productsHas = [];
            foreach ($products as $item) {
                $product = Product::find($item->prod_id);
                if ($product) {
                    $entity['product'] = $product;
                    $entity['quantityImp'] = $item->quantity_prod;
                    array_push($productsHas, $entity);
                    unset($entity);
                    $inventory = $product->prod_quantity - $product->count_sold - $item->quantity_prod;
                    if ($inventory < 0) {
                        $checkCount = false;
                        break;
                    }
                }
            }
            if ($checkCount) {
                foreach ($productsHas as $entity) {
                    $data['quantity'] = $entity['product']->prod_quantity - $entity['product']->count_sold - $entity['quantityImp'];
                    if ($data['quantity'] >= 0) {
                        $this->updateProductCore($entity['product']->id, $user, $data);
                        $this->updateProductCore($entity['product']->parent_id, $user, $data);
                    }
                }
                $impInfo->status = "CANCEL";
                $impInfo->user_id_cancel = $user->id;
                $impInfo->save();
                return response()->json([
                    'canceled' => true
                ]);
            }
            return response()->json([
                'canceled' => false,
                'message' => 'Không thể hủy! Số lượng những sản phẩm này trong kho không đủ'
            ]);
        }
        return response()->json([
            'canceled' => false,
            'message' => 'Không tìm thấy phiếu nhập'
        ]);
    }

    public function searchProducts(Request $request) 
    {
        $keyword = $request->keyword;
        $products = Auth::user()->productsAndCamp()
                                ->where('count_childs', 0)
                                ->where('prod_name', 'LIKE', "%$keyword%")
                                ->get();
        if (count($products) == 0) {
            $products = Auth::user()->productsAndCamp()
                                ->where('count_childs', 0)
                                ->where('prod_code', 'LIKE', "%$keyword%")
                                ->get();
        }
        return response()->json($products);
    }

    /**
     * create export product record
     * @param  array  $data [user_id, quantity_ex, inventory_ex, amount_ex]
     * @return id of new export product
     */
    public function createExport(User $user, array $data)
    {
        $new = new ExportProduct;
        $new->export_code = $this->generalCodeExport();
        $new->user_id = $user->adminId();
        $new->staff_id = $user->id;
        $new->quantity_ex = $data['quantity_ex'];
        $new->inventory_ex = $data['inventory_ex'];
        $new->amount_ex = $data['amount_ex'];
        $new->status_ex = "EXPORTED";
        $new->save();
        return $new->id;
    }

    /**
     * create products of export 
     * @param  array  $data [description]
     * @return id new record product_exports
     */
    public function createProductExport(array $data)
    {
        $product = Product::find($data['prod_id']);
        $user = User::find(Auth::id());
        if ($product) {
            $dataUpdate['count_sold'] = $product->count_sold + $data['quantity_ex'];
            $this->updateProductCore($data['prod_id'], $user, $dataUpdate);
            //update for parent of product
            if ($product->parent_id) {
                $parent = Product::find($product->parent_id);
                if ($parent) {
                    $dataParent['count_sold'] = $parent->count_sold + $data['quantity_ex'];
                    $this->updateProductCore($product->parent_id, $user, $dataParent);
                }
            }

            $newProd = new ProductExport;
            $newProd->prod_name = $product->prod_name;
            $newProd->prod_code = $product->prod_code;
            $newProd->properties = $product->properties;
            $newProd->export_id = $data['export_id'];
            $newProd->prod_id = $data['prod_id'];
            $newProd->quantity_ex = $data['quantity_ex'];
            $newProd->price_ex = $data['price_ex'];
            $newProd->inventory_ex = $data['inventory_ex'];
            $newProd->save();
            return $newProd->id;
        }
        return null;
    }

    /**
     * get list export product 
     * @return 
     */
    public function getListExport(Request $request)
    {
        $timeFrom = $request->timeFrom;
        $timeTo = $request->timeTo;

        $arrWhere = [];

        if ($timeFrom) {
            $timeFrom = date("Y-m-d H:i:s", strtotime($timeFrom));
            array_push($arrWhere, ['export_products.created_at', '>=', $timeFrom]);

        }
        if ($timeTo) {
            $timeTo = date("Y-m-d H:i:s", strtotime($timeTo));
            array_push($arrWhere, ['export_products.created_at', '<=', $timeTo]);
        }

        return Auth::user()
                    ->exports()
                    ->where($arrWhere)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->toJson();
    }

    public function generalCodeExport()
    {
        $user = Auth::user();
        $count = $user->exportsUser()->count();
        $count = $count + 1;
        $num = $code = str_pad($count, 3, '0', STR_PAD_LEFT);
        $check = $user->exportsUser()->where('export_code', $num)->count();
        $index = 1;
        while ($check) {
            $code = $num . strtoupper(str_random($index));
            $check = $user->exportsUser()->where('export_code', $code)->count();
            $index++;
        }
        return $code;
    }

    public function createExportBallot(CreateExportRequest $request)
    {
        $products = $request->products;
        $infoExport = $request->infoExport;
        $user = User::find(Auth::id());
        $exportId = $this->createExport($user, $infoExport);
        foreach ($products as $product) {
            $product['export_id'] = $exportId;
            $this->createProductExport($product);
        }
        return response()->json([
            'id' => $exportId
        ]);
    }

    public function getDetailExport($id)
    {
        $info = Auth::user()->exportsUser()->first($id);
        $products = [];
        if ($info) {
            $products = $this->getListProductExport($id);
        }
        return response()->json([
            'info' => $info,
            'products' => $products
        ]);
    }

    public function getListProductExport($id)
    {
        $prods = ProductExport::where('export_id', $id)->get();
        foreach ($prods as $key => $prod) {
            $product = Product::find($prod->prod_id);
            if ($product) {
                $prods[$key]->prod_thumb = $product->prod_thumb;
            } else {
                $prods[$key]->prod_thumb = '';
            }
        }
        return $prods;
    }

    public function pushProductsToCate(Request $request)
    {
        $request->validate(
            [
                'prodIds' => 'required|array',
                'cateId' => 'required'
            ],
            [
                'prodIds.required' => 'Vui lòng chọn sản phẩm',
                'cateId.required' => 'Vui lòng chọn danh mục',
            ]
        );
        $user = Auth::user();
        $prodIds = $request->prodIds;
        $cateId = $request->cateId;
        $cate = $user->catesUser()->find($cateId);
        if ($cate) {
            foreach ($prodIds as $id) {
                $product = $user->productUsers()->with('childs')->find($id);
                if ($product) {
                    $item = $product->cates()->detach();
                    $product->cates()->attach($cateId);
                    $childs = $product->childs;
                    foreach ($childs as $child) {
                        $child->cates()->detach();
                        $child->cates()->attach($cateId);
                    }
                }
            }
            return response()->json([
                'success' => true
            ]);
        }
        return response()->json([
            'message' => 'Không tìm thấy đổi tượng danh mục'
        ], 302);
    }

    public function exportProductList($type)
    {
        $products = Auth::user()
                            ->productsUser()
                            ->get()
                            ->toArray();
        $products = json_decode(json_encode($products), true);
        $header =  ['ID SP', 'Mã SP', 'Tên SP', 'ID loại SP', 'ID người tạo', 'Giá bán SP (VNĐ)', 'Giá nhập SP (VNĐ)', 'Tổng SP đã nhập', 'Link ảnh', 'Số sản phẩm cùng loại', 'Thuộc tính SP', 'Số SP đã bán', 'Tình trạng (HAS - còn/ CLEAR - hết)', 'Thời gian tạo', 'Thời gian cập nhật gần nhất', 'Danh mục', 'ID danh mục'];
        array_unshift($products, $header);

        return \Excel::create('product_list', function($excel) use ($products) {

            $excel->sheet('products', function($sheet) use ($products)

            {

                $sheet->fromArray($products, null, 'A1', false, false);

            });

        })->download($type);
    }

    public function exportImportList($type)
    {
        $imports = Auth::user()
                            ->imports()
                            ->get()
                            ->toArray();
        $imports = json_decode(json_encode($imports), true);

        $header =  ['ID phiếu nhập', 'Mã', 'ID người nhập', 'ID người hủy', 'Số lượng nhập', 'Còn trong kho (trước khi nhập)', 'Tổng tiền nhập (VNĐ)', 'Có thể hủy (1 - có, 2 - không)', 'Trạng thái (IMPORT - Đã nhập, CANCEL - Đã hủy)', 'Thời gian nhập', 'Thời gian cập nhật cuối', 'Người nhập'];
        array_unshift($imports, $header);

        return \Excel::create('imports_list', function($excel) use ($imports) {

            $excel->sheet('imports', function($sheet) use ($imports)

            {

                $sheet->fromArray($imports, null, 'A1', false, false);

            });

        })->download($type);
    }

    public function exportFileListExport($type)
    {
        $exports = Auth::user()
                            ->exports()
                            ->get()
                            ->toArray();
        $exports = json_decode(json_encode($exports), true);

        $header =  ['ID phiếu xuất', 'Mã', 'ID người nhập', 'SL xuất', 'SL còn trong kho (sau khi đã xuất)', 'Tổng tiền xuất (VNĐ)', 'Trạng thái (EXPORTED - Đã xuất, CANCEL - Đã hủy)', 'Thời gian xuất', 'Thời gian cập nhật cuối', 'Người xuất'];
        array_unshift($exports, $header);

        return \Excel::create('exports_list', function($excel) use ($exports) {

            $excel->sheet('exports', function($sheet) use ($exports)

            {

                $sheet->fromArray($exports, null, 'A1', false, false);

            });

        })->download($type);
    }

    public function getInfoSummaryProduct()
    {
        $user = Auth::user();
        $cates = $user->catesUser()->get();
        $props = $user->propUser()->with('hasValue')->get();
        return response()->json([
            'cates' => $cates,
            'props' => $props
        ]);
    }
    /**
     * get history of product
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getChangeHistory($id)
    {
        $user = Auth::user();
        $product = $user->productUsers()->with(['exports' => function($query) {
            $query->withPivot('quantity_ex', 'inventory_ex')->orderBy('created_at', 'desc');
        }, 'imports' => function($query) {
            $query->withPivot('quantity_prod', 'inventory_prod')->orderBy('created_at', 'desc');
        }])->find($id)->toArray();
        if ($product) {
            $exports = $product['exports'];
            $imports = $product['imports'];
            $history = array_merge($exports, $imports);
            for ($i=0; $i < count($history) - 1; $i++) { 
                for ($j=$i + 1; $j < count($history); $j++) {
                    $time1 = strtotime($history[$i]['created_at']);
                    $time2 = strtotime($history[$j]['created_at']);
                    if ($time1 < $time2) {
                        $tg = $history[$i];
                        $history[$i] = $history[$j];
                        $history[$j] = $tg;
                    }
                }
            }
            return response()->json($history);
        }
        return response()->json([
            'message' => 'Không tìm thấy sản phẩm'
        ], 302);
    }

    public function findProductOfUser($id)
    {
        $user = Auth::user();
        return $user->productUsers()->find($id);
    }

}
