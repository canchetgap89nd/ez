<template>
	<div>
		<div class="modal-content" v-if="isLoading">
			<circle-load></circle-load>
		</div>
		<div class="modal-content" v-else>
		    <div class="modal-header">
		        <h4 class="modal-title text-center">
		        	<span class="title-modal-cus">Chỉnh sửa sản phẩm</span>
		        </h4>
		    </div>
		    <div class="modal-body">
		        <div class="row">
		        	<div class="col-lg-4">
		        		<div class="form-group ls-c-ad">
	            			<label>
	            				Chọn danh mục
	            			</label>
	                		<select id="cates_edit_prod">
	                			<option data-placeholder="true"></option>
	                			<option v-for="(cate, index) in cates" :selected="cate.id == productAc.product.cate" :value="cate.id">{{ cate.cate_name }}</option>
	                		</select>
	            		</div>
	            		<a class="btn_qAdd_newCate" title="Thêm danh mục" href="#" @click="getAddCategory()">
			   				<i class="fa fa-plus-circle txt-color-green"></i>
			   			</a>
		        		<div class="form-group text-center">
		        			<div class="thumb-new-product" id="dropzone-thumb-edit">
		        				<div class="image-preview" id="choose-other-thumb">
								  	<label for="image-upload">
								  		<span class="icon-add-img">
								  			<i class="fa fa-plus"></i>
								  		</span>
								  		<span class="txt-add-img">Thêm ảnh</span>
								  	</label>
								  	<div class="dropzone-previews thumb-previews" id="thumb-other">
								  		<div class="dz-preview" id="current-thumb" v-if="productAc.product.thumb">
								  			<div class="dz-image">
								  				<img title="Click vào để thay đổi ảnh" :src="productAc.product.thumb" style="width: 100%">
								  			</div>
								  		</div>
								  	</div>
								</div>
		        			</div>
		        		</div>
		        	</div>
		        	<div class="col-lg-4">
		        		<div class="form-group">
		        			<label>Tên sản phẩm</label>
		    				<input type="text" v-model="productAc.product.name" placeholder="Nhập tên sản phẩm" class="form-control">
		        		</div>
		        		<div class="form-group">
		        			<label>Giá nhập ({{ productAc.product.priceImp }} VNĐ)</label>
		        			<input type="text" :disabled="productAc.childs.length > 0" v-model="productAc.product.priceImp" @input="fixPrice($event, productAc.product, 'priceImp')" placeholder="Giá nhập của sản phẩm" class="form-control">
		        		</div>
		        		<div class="form-group">
		        			<label>Số lượng: {{ productAc.product.quantity }}</label>
		        		</div>
		        	</div>
		        	<div class="col-lg-4">
		        		<div class="form-group">
		        			<label>Mã sản phẩm</label>
		    				<input type="text" :disabled="productAc.childs.length > 0" v-model="productAc.product.code" @input="forceUppercase($event, productAc.product, 'code')" placeholder="Bỏ trống hoặc nhập mã thủ công" class="form-control">
		        		</div>
		        		<div class="form-group">
		        			<label>Đơn giá ({{ productAc.product.price }} VNĐ)</label>
		    				<input type="text" :disabled="productAc.childs.length > 0" v-model="productAc.product.price" @input="fixPrice($event, productAc.product, 'price')" placeholder="Giá sản phẩm" class="form-control">
		        		</div>
		        		<div class="form-group">
		        			<label>Tồn kho: {{ productAc.product.quantity - productAc.product.count_sold }}</label>
		        		</div>
		        	</div>
		        	<div class="col-lg-offset-4 col-lg-8" v-show="productAc.props.length > 0">
		        		<div class="sel-add-properties">
							<select id="list_props_editProd">
						    	<option data-placeholder="true"></option>
						    	<option v-for="(prop, index) in props" :value="index">
					    			{{ prop.prop_name }}
						    	</option>
						    </select>
		        		</div>
					   	<a class="btn_add_newProp" title="Thêm thuộc tính" href="#" @click="getAddProp()">
					   		<i class="fa fa-plus-circle txt-color-green"></i>
					   	</a>
		        		<div class="cont-properties">
		        			<div class="pro-entity" v-for="prop in productAc.props">
		        				<span>{{ prop.prop_name }}</span>
		        				<span class="remove-prop" @click="removeProp(prop)">
		        					<i class="fa fa-close"></i>
		        				</span>
		        			</div>
		        		</div>
		        	</div>
		        </div>
		        <div class="box-table ls-prod-childs" v-show="productAc.childs.length > 0">
		            <table class="table table-head-black table-row-notBorder" v-if="productAc.props.length">
						<thead>
							<tr>
								<th class="cs-ellipsis-clm text-center">Ảnh</th>
								<th class="cs-ellipsis-clm text-right">Mã sản phẩm</th>
								<th class="cs-ellipsis-clm text-right" v-for="prop in productAc.props">
									{{ prop.prop_name }}
								</th>
								<th class="cs-ellipsis-clm text-right">Số lượng</th>
								<th class="cs-ellipsis-clm text-right">Tồn kho</th>
								<th class="cs-ellipsis-clm text-right">Giá nhập</th>
								<th class="cs-ellipsis-clm text-right">Đơn giá</th>
								<th class="cs-ellipsis-clm text-center"></th>
							</tr>
						</thead>
						<tbody v-show="productAc.childs.length > 0">
							<tr v-for="(child, index) in productAc.childs" class="bg-bold-border">
								<td class="text-center">
									<div class="contAddImgChild">
										<div class="img-preview-prop" @click="openUpImg(child, index)">
											<div class="contImgLs" v-if="child.prod_thumb">
												<img :src="child.prod_thumb">
												<div class="numImgTotal" v-show="child.images.length + child.imagesNew.length > 1">
													+{{child.images.length + child.imagesNew.length - 1}}
												</div>
											</div>
											<i class="fa fa-picture-o" v-else></i>
										</div>
									  	<div class="lsImgProdChilds" v-show="child.uploadImg">
									  		<div class="dropzonelsHas">
										  		<div class="imgHasPreviews" v-for="(image, indChi) in child.images">
								    				<div class="itemDz">
								    					<div class="csImg">
								        					<img :src="image.image_src" :title="productAc.product.prod_name" style="width: 100%">
								    					</div>
								    					<div class="text-center">
									    					<a href="javascript:;" class="dz-remove" @click="delImageChild(indChi, image)">
									        					<span title="Xóa ảnh" class="fa fa-trash remo-img" style="font-size: 13px"></span>
									    					</a>
								    					</div>
								    				</div>
												</div>
									  		</div>
											<vue-dropzone v-on:vdropzone-removed-file="removeImageProd" v-on:vdropzone-complete="changeIconDelete" v-on:vdropzone-success="successUpload" class="dropzonels" id="dropzone" ref="myVueDropzone" :options="dropzoneOptions">
			    							</vue-dropzone>
			    							<!-- <div class="iconAddMoreImg" id="addImageProd">
			    								<div class="innerIconPlus">
			    									<i class="fa fa-plus"></i>
			    									<div class="txtIconPlus">Tải lên</div>
			    								</div>
			    							</div> -->
			    							<span class="icClUpImg" @click="closeUpImg(child)">
			    								<i class="fa fa-close"></i>
			    							</span>
									  	</div>
									</div>
								</td>
								<td class="cs-ellipsis-clm text-right">
									<div class="itTaHasImg text-right">
										<input type="text" class="form-control" @input="forceUppercase($event, child, 'code')" placeholder="Nhập mã" v-model="child.code">
									</div>
								</td>
								<td class="cs-ellipsis-clm text-right" v-for="prop in productAc.props">
									<div class="itTaHasImg">
										<select class="form-control" v-model="child.props[prop.id]">
											<option v-for="item in prop.has_value" :value="item.id">{{ item.value }}</option>
										</select>
									</div>
								</td>
								<td class="cs-ellipsis-clm text-right">
									<div class="itTaHasImg">
										<label style="margin-top: 8px" class="text-center">{{ child.quantity }}</label>
									</div>
								</td>
								<td class="cs-ellipsis-clm text-right">
									<div class="itTaHasImg">
										<label style="margin-top: 8px" class="text-center">{{ child.quantity - child.count_sold }}</label>
									</div>
								</td>
								<td class="cs-ellipsis-clm text-right">
									<div class="itTaHasImg">
										<input type="text" class="form-control" @input="fixPrice($event, child, 'priceImp')" v-model="child.priceImp">
									</div>
								</td>
								<td class="cs-ellipsis-clm text-right">
									<div class="itTaHasImg">
										<input type="text" class="form-control" @input="fixPrice($event, child, 'price')" v-model="child.price">
									</div>
								</td>
								<td class="cs-ellipsis-clm text-center">
									<div class="itTaHasImg" style="margin-top: 18px;">
										<span class="ac-row-tb del-row-table" title="Xóa sản phẩm" @click="removeRow(child, index)">
											<i class="fa fa-close"></i>
										</span> 
										<span class="ac-row-tb edit-row-table" title="Lịch sử xuất nhập kho" @click="getHistoryProduct(child.id)">
											<i class="fa fa fa-eye"></i>
										</span>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
		        </div>
		        <div class="box-table" v-show="productAc.props.length > 0">
		        	<a href="#" class="add-row-newProduct" @click="addRowProduct()">
		        		<i class="fa fa-plus-circle txt-color-green"></i>
		        		Thêm sản phẩm
		        	</a>
		        </div>
		        <div class="box-table">
		        	<div class="add-listImg-product" id="mor-img">
				      	<div class="image-preview" id="ad-mor-img">
						  	<label for="image-upload">
						  		<span class="icon-add-img">
						  			<i class="fa fa-plus"></i>
						  		</span>
						  		<span class="txt-add-img">Thêm ảnh</span>
						  	</label>
						</div>
					</div>
					<div class="dropzone-previews list-images-preview" id="list-edit-prod">
						<span id="list-current-images">
		    				<div v-for="(image, index) in productAc.images" class="dz-preview">
		    					<div class="dz-image">
		        					<img :src="image.image_src" :title="productAc.product.prod_name" style="width: 100%">
		    					</div>
		    					<a href="javascript:;" class="dz-remove" @click="delImageHas(image, index)">
		        					<span title="Xóa ảnh" class="fa fa-trash remo-img" style="font-size: 1.5em"></span>
		    					</a>
		    				</div>
						</span>
						<span id="list-new-images">
							
						</span>
					</div>
		        </div>
		    </div>
		    <div class="modal-footer">
		    	<span class="processing-icon" v-show="productAc.isUpdating">
					<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
					<span class="sr-only">Loading...</span>
		    	</span>
		    	<a v-show="!productAc.childs.length" href="javascript:;" @click="getHistoryProduct(productAc.product.id)" style="margin-right: 15px;">Lịch sử xuất nhập kho</a>
				<button class="btn btn-success" @click="processUpdate()" :disabled="productAc.isUpdating">Cập nhật</button>
				<button class="btn btn-danger" @click="reset()">Hủy bỏ</button>
		    </div>
		</div>
	</div>
</template>
<script type="text/javascript">

	import { EventBus } from '../../helpers/bus'
	import { functionHelper } from '../../helpers/myfunction'
	import {momentLocale} from '../../helpers/momentfix'
	import { get } from '../../helpers/send'
	import { post } from '../../helpers/send'
	import { del } from '../../helpers/send'
	import {formatPrice} from '../../helpers/numeralfix'
	import {valuePrice} from '../../helpers/numeralfix'
	import {numeral} from '../../helpers/numeralfix'
	import Circle from '../loading/Circle.vue'
	import SlimSelect from 'slim-select'
	import vue2Dropzone from 'vue2-dropzone'
	import {getCookie} from '../../helpers/cookies'

	export default {
		components: {
			'circle-load': Circle,
			vueDropzone: vue2Dropzone
		},
		data() {
			return {
				isLoading: false,
				dropzoneThumb: null,
				dropzoneListImage: null,
				isDoing: false,
				productAc: {
					product: {
						id: null,
						cate: null,
						name: null,
						code: null,
						price: null,
						priceImp: null,
						quantity: null,
						thumb: null,
					},
					images: [],
					props: [],
					childs: [],
					process: false,
					isCreating: false,
					isUpdating: false,
					childsDel: [],
					thumbNew: null,
					imagesNew: [],
					imagesDel: []
				},
				dropzoneOptions: {
		          	url: '/api/product/uploads/images',
		          	thumbnailWidth: 60,
		          	thumbnailHeight: 60,
		          	maxFilesize: 0.5,
		          	headers: {
		          		'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
						'Authorization': 'Bearer ' + getCookie('tk_cs')
		          	},
		          	addRemoveLinks: true,
		          	dictDefaultMessage: '<div class="iconAddMoreImg" id="addImageProd">'+
		    								'<div class="innerIconPlus">'+
		    									'<i class="fa fa-plus"></i>'+
		    									'<div class="txtIconPlus">Tải lên</div>'+
		    								'</div>'+
		    							'</div>'
		      	},
				childActive: null
			}
		},
		computed: {
			cates() {
				return this.$store.state.cates;
			},
			props() {
				return this.$store.state.props;
			}
		},
		mounted() {
			EventBus.$on('get-edit-product', (id) => {
				this.editProduct(id);
			})
			EventBus.$on('update-props', (prop) => {
				this.props.push(prop);
			})
		},
		updated() {
			let thisIns = this;
			let findSe = $("#cates_edit_prod");
			let findSel = $("#list_props_editProd");
			if (findSe.length > 0 && findSel.length > 0) {
				new SlimSelect({
				  	select: '#cates_edit_prod',
				  	placeholder: 'Chọn danh mục',
				  	searchHighlight: true,
				  	onChange: (info) => {
				  		if (info) {
				  			let id = info.value;
				  			this.productAc.product.cate = id;
				  		}
				  	}
				});
				new SlimSelect({
				  	select: '#list_props_editProd',
				  	placeholder: 'Chọn thuộc tính',
				  	searchHighlight: true,
				  	onChange: (info) => {
				  		if (info) {
				  			let index = info.value;
					  		let prop = this.props[index];
					  		this.pickProp(prop);
				  		}
				  	}
				})
			}

			let findDzl = $('div#mor-img');
			let findDz = $('div#dropzone-thumb-edit');
			if (!this.isDoing && findDzl.length > 0 && findDz.length > 0 && $("#modal-edit-product").hasClass('in')) {
				$('#thumb-other').addClass('dropzone');
				$("div#dropzone-thumb-edit").dropzone({ 
					url: "/api/product/uploads/images",
					previewsContainer: "#thumb-other",
					headers: { 
						'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
						'Authorization': 'Bearer ' + getCookie('tk_cs') 
					},
					clickable: "#choose-other-thumb",
					addRemoveLinks: true,
					maxFiles: 1,
					acceptedMimeTypes: '.jpeg,.png,.jpg',
					maxFilesize: '3',
					init: function() {
						thisIns.dropzoneThumb = this;
						this.on("addedfile", function(file) {
			                thisIns.productAc.product.thumb = null;
			            });
			            this.on("complete", function(file) {
			                $(".dz-remove").html("<span title='Xóa ảnh' class='fa fa-trash remo-img' style='font-size: 1.5em'></span>");
			            });
			            this.on("removedfile", function(file) {
			            	let path = thisIns.productAc.thumbNew ? thisIns.productAc.thumbNew.path : '';
			            	if (path) {
	            				thisIns.deleteImage(path);
			            	}
	            			thisIns.productAc.thumbNew = {};
			            });
			            this.on("success", function(file, response) {
			            	if (response.success) {
			            		let newImg = {
			            				path: response.path,
			            				name: file.name
			            			}
			            		thisIns.productAc.thumbNew = newImg;
			            	}
			            });
			        }
				});

				$('#list-edit-prod').addClass('dropzone');
				$("div#mor-img").dropzone({ 
					url: "/api/product/uploads/images",
					previewsContainer: "#list-new-images",
					headers: { 
						'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
						'Authorization': 'Bearer ' + getCookie('tk_cs')
					},
					clickable: "#ad-mor-img",
					addRemoveLinks: true,
					acceptedMimeTypes: '.jpeg,.png,.jpg',
					maxFilesize: '3',
					init: function() {
						thisIns.dropzoneListImage = this;
			            this.on("complete", function(file) {
			                $(".dz-remove").html("<span title='Xóa ảnh' class='fa fa-trash remo-img' style='font-size: 1.5em'></span>");
			            });
			            this.on("removedfile", function(file) {
			            	let imagesNew = thisIns.productAc.imagesNew;
			            	for (var i = 0; i < imagesNew.length; i++) {
			            		if (imagesNew[i].name == file.name) {
			            			let path = imagesNew[i].path;
			            			thisIns.deleteImage(path);
			            			thisIns.productAc.imagesNew.splice(i, 1);
			            		}
			            	}
			            });
			            this.on("success", function(file, response) {
			            	if (response.success) {
			            		let newImg = {
			            				path: response.path,
			            				name: file.name
			            			}
			            		thisIns.productAc.imagesNew.push(newImg);
			            	}
			            });
			        }
				});
				this.isDoing = true;
			}
		},
		methods: {
			fixPrice(e, o, prop) {
			    e.target.value = numeral(e.target.value).format('0,0');
			    this.$set(o, prop, e.target.value);
		  	},
		  	forceUppercase(e, o, prop) {
			    const start = e.target.selectionStart;
			    e.target.value = e.target.value.toUpperCase();
			    this.$set(o, prop, e.target.value);
		  	},
			editProduct(id) {
				$("#modal-edit-product").modal('show');
				this.isLoading = true;
				get("api/product/edit/"+id)
				.then((res) => {
					if (res.data) {
						this.productAc.product.cate = res.data.cates[0] ? res.data.cates[0].id : null;
						this.productAc.product.id = res.data.id;
						this.productAc.product.name = res.data.prod_name;
						let pr = res.data.prod_price;
						let prImp = res.data.prod_price_imp;
						this.productAc.product.price = parseFloat(pr).toLocaleString();
						this.productAc.product.priceImp = parseFloat(prImp).toLocaleString();
						this.productAc.product.quantity = res.data.prod_quantity;
						this.productAc.product.count_sold = res.data.count_sold;
						this.productAc.product.code = res.data.prod_code;
						this.productAc.product.thumb = res.data.prod_thumb;
						this.productAc.images = res.data.images;

						let childs = res.data.childs;
						let prodProps = [];
						childs.forEach((val) => {
							let propsChild = val.properties;
							propsChild.forEach((val2) => {
								prodProps.push(val2.prop_id);
							})
						})

						prodProps.forEach((val) => {
							let index = functionHelper.getIndexHasIdInArray(val, this.props);
							if (index != null) {
								let hasInArr = this.productAc.props.some((propItem) => {
									return propItem.id === this.props[index].id;
								})
								if (!hasInArr) {
									this.productAc.props.push(this.props[index]);
								}
							}
						})

						let tg = {};
						for (let k in childs) {
							let props = childs[k].properties;
							tg = {};
							tg.id = childs[k].id;
							tg.props = {};
							tg.code = childs[k].prod_code;
							tg.uploadImg = false;
							tg.prod_thumb = childs[k].prod_thumb;
							tg.images = childs[k].images;
							tg.imagesNew = [];
							tg.imagesDel = [];
							tg.count_sold = childs[k].count_sold;
							tg.price = parseFloat(childs[k].prod_price).toLocaleString();
							tg.priceImp = parseFloat(childs[k].prod_price_imp).toLocaleString();
							tg.quantity = childs[k].prod_quantity;
							for (var a = 0; a < props.length; a++) {
								let index = props[a].prop_id;
								tg.props[index] = props[a].id;
							}
							this.productAc.childs.push(tg);
						}
					}
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify("Lỗi tải trang chỉnh sửa sản phẩm", 'error');
					this.isLoading = false;
				})
			},
			reset() {
				$('#list-new-images').empty();
				$("#thumb-previews").empty();
				$("#list-images-prod").empty();
				this.productAc.product.cate = null;
				this.productAc.product.name = null;
				this.productAc.product.code = null;
				this.productAc.product.price = null;
				this.productAc.product.priceImp = null;
				this.productAc.product.quantity = null;
				this.productAc.product.thumb = null;
				this.productAc.images = [];
				this.productAc.childs = [];
				this.productAc.props = [];
				this.productAc.isUpdating = false;
				this.productAc.isCreating = false;
				this.productAc.childsDel = [];
				this.productAc.thumbNew = null;
				this.productAc.imagesNew = [];
				this.productAc.imagesDel = [];
				this.dropzoneThumb.destroy();
				this.dropzoneListImage.destroy();
				this.allCates = [];
				this.isLoading = false;
				this.isDoing = false;
				$("#modal-edit-product").modal('hide');
			},
			getListProducts() {
				EventBus.$emit('get-list-products');
			},
			getAddCategory() {
				$('#modal-edit-product').modal('hide');
				$('#add-new-category').modal('show');
			},
			getAddProp() {
				EventBus.$emit('get-add-property');
			},
			addRowProduct() {
				let parentPrice = this.productAc.product.price;
				let parentPriceImp = this.productAc.product.priceImp;
				let child = {
					code: null,
					quantity: 0,
					count_sold: 0,
					price: parentPrice,
					priceImp: parentPriceImp,
					props: {},
					id: null,
					prod_thumb: null,
					uploadImg: false,
					images: [],
					imagesNew: [],
					imagesDel: []
				}
				this.productAc.childs.push(child);
			},
			removeRow(child, index) {
				this.productAc.childsDel.push(child.id);
				this.productAc.childs.splice(index, 1);
			},
			pickProp(prop) {
				if (this.productAc.childs.length == 0) {
					this.productAc.product.price = 0;
					this.productAc.product.priceImp = 0;
					this.productAc.product.code = null;
					this.addRowProduct();
				}
				if (! functionHelper.checkIdInArray(prop.id, this.productAc.props)) {
					this.productAc.props.push(prop);
				}
			},
			removeProp(prop) {
				for (var i = 0; i < this.productAc.props.length; i++) {
					if (this.productAc.props[i].id == prop.id) {
						this.productAc.props.splice(i, 1);
						break;
					}
				}
				if (this.productAc.props.length == 0) {
					for (var i = 0; i < this.productAc.childs.length; i++) {
						this.productAc.childsDel.push(this.productAc.childs[i].id);
					}
					this.productAc.childs = [];
					this.productAc.product.quantity = 0;
				}
			},
			deleteImage(path) {
				del('/api/product/destroy/images', {
					path: path
				})
				.then((res) => {
					if (res.data.success) {
						$.notify('Đã xóa hình ảnh', 'success');
					}
				})
				.catch((err) => {
					$.notify('Lỗi xóa hình ảnh', 'error');
				})
			},
			delImageHas(image, index) {
				this.productAc.imagesDel.push(image.id);
				this.productAc.images.splice(index, 1);
			},
			processUpdate() {
				this.productAc.isUpdating = true;
				let formSend = {
						product: this.productAc.product,
						childs: {},
						images_remove: this.productAc.imagesDel,
						images_new: this.productAc.imagesNew,
						thumb_new: this.productAc.thumbNew,
						childs_del: this.productAc.childsDel
					}
				formSend.product.price = valuePrice(formSend.product.price);
				formSend.product.priceImp = valuePrice(formSend.product.priceImp);
				for (var i = 0; i < this.productAc.childs.length; i++) {
					this.removePropOfProd(this.productAc.childs[i].props, i);						
					let child = this.productAc.childs[i];
					formSend.childs[i] = 	{
												id: child.id,
												name: formSend.product.name,
												code: child.code,
												price: valuePrice(child.price),
												priceImp: valuePrice(child.priceImp),
												props: child.props,
												imagesNew: child.imagesNew,
												imagesDel: child.imagesDel,
												prod_thumb: child.prod_thumb
											};
				}
				this.updateProduct(formSend);
			},
			removePropOfProd(propsChild,index) {
				let props = this.productAc.props;
				let result = {};
				for (let k in propsChild) {
					let check = false;
					for (var j = 0; j < props.length; j++) {
						if (k == props[j].id) {
							check = true;
							break;
						}
					}
					if (check) {
						result[k] = propsChild[k];
					}
				}
				this.productAc.childs[index].props = result;
			},
			updateProduct(form) {
				post('/api/product/update/'+form.product.id, form)
				.then((res) => {
					if (res.data.updated) {
						this.reset();
						this.productAc.isUpdating = false;
						this.getListProducts();
						$("#modal-edit-product").modal("hide");
						$.notify('Cập nhật sản phẩm thành công', 'success');
					} else {
						$.notify(res.data.message, 'error');
						this.productAc.isUpdating = false;
					}
				})
				.catch((err) => {
					if (err.response.status == 422) {
						if (err.response.status == 422) {
							this.productAc.isUpdating = false;
							let errors = err.response.data.errors;
							for (var k in errors) {
								$.notify(errors[k], 'error');
							};
						}
					}
				})
			},
			getAddCategory() {
				this.reset();
				$('#add-new-category').modal('show');
			},
			successUpload(file, response) {
				if (response.success) {
            		let newImg = {
            				path: response.path,
            				name: file.name
            			}
            		let index = this.childActive;
            		this.productAc.childs[index].imagesNew.push(newImg);
            		this.productAc.childs[index].prod_thumb = response.path;
            		
            	}
			},
			changeIconDelete() {
				$(".dz-remove").html("<span title='Xóa ảnh' class='fa fa-trash remo-img' style='font-size: 13px'></span>");
			},
			openUpImg(child, index) {
				this.productAc.childs.forEach((val) => {
					val.uploadImg = false;
				})
				child.uploadImg = true;
				this.childActive = index;
			},
			closeUpImg(child) {
				child.uploadImg = false;
			},
			removeImageProd(file, response) {
				let ind = this.childActive;
				if (this.productAc.childs[ind]) {
					this.productAc.childs[ind].imagesNew.forEach((val, index) => {
						if (val.name == file.name) {
	            			this.deleteImage(val.path);
	            			this.productAc.childs[ind].imagesNew.splice(index, 1);
						}
					})
				}
			},
			delImageChild(ind, img) {
				let index = this.childActive;
        		this.productAc.childs[index].images.splice(ind, 1);
        		this.productAc.childs[index].imagesDel.push(img.id);
			},
			getHistoryProduct(id) {
				EventBus.$emit("historyProduct", id);
			}
		},
		beforeDestroy () {
			EventBus.$off('get-edit-product')
			EventBus.$off('update-props')
		}
	}
</script>