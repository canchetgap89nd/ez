<template>
	<div class="modal-content" v-if="isLoading">
		<circle-load></circle-load>
	</div>
	<div class="modal-content" v-else>
        <div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title text-center">
            	<span class="title-modal-cus">Thêm sản phẩm mới</span>
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
            	<div class="col-lg-4">
            		<div class="form-group ls-c-ad">
            			<label>
            				Chọn danh mục
            			</label>
                		<select id="list_cates_newProd">
                			<option data-placeholder="true"></option>
                			<option v-for="(cate, index) in cates" :selected="cate.id == productAc.product.cate" :value="cate.id">{{ cate.cate_name }}</option>
                		</select>
            		</div>
            		<a class="btn_qAdd_newCate" title="Thêm danh mục" href="#" @click="getAddCategory()">
		   				<i class="fa fa-plus-circle txt-color-green"></i>
		   			</a>
            		<div class="form-group text-center">
            			<div class="thumb-new-product">
            				<div class="image-preview" id="thumb-imageNew-p">
							  	<label for="image-upload" id="change-img">
							  		<span class="icon-add-img">
							  			<i class="fa fa-plus"></i>
							  		</span>
							  		<span class="txt-add-img">Thêm ảnh</span>
							  	</label>
							  	<div class="dropzone-previews thumb-previews" id="thumb-previews">
							  		
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
            			<input type="text" :disabled="productAc.props.length > 0" v-model="productAc.product.priceImp" @input="fixPrice($event, productAc.product, 'priceImp')" placeholder="Giá nhập của sản phẩm" class="form-control">
            		</div>
            		<div class="form-group">
            			<label>Số lượng</label>
        				<input type="number" min="0" v-if="productAc.props.length > 0" v-model="totalProduct" :disabled="productAc.props.length > 0" placeholder="Nhập số lượng sản phẩm" class="form-control">
        				<input type="number" min="0" v-else v-model="productAc.product.quantity" placeholder="Nhập số lượng sản phẩm" class="form-control">
            		</div>
            	</div>
            	<div class="col-lg-4">
            		<div class="form-group">
            			<label>Mã sản phẩm</label>
        				<input type="text" :disabled="productAc.props.length > 0" v-model="productAc.product.code" @input="forceUppercase($event, productAc.product, 'code')" placeholder="Bỏ trống hoặc nhập mã thủ công" class="form-control">
            		</div>
            		<div class="form-group">
            			<label>Đơn giá ({{ productAc.product.price }} VNĐ)</label>
        				<input type="text" :disabled="productAc.props.length > 0" v-model="productAc.product.price" @input="fixPrice($event, productAc.product, 'price')" placeholder="Giá sản phẩm" class="form-control">
            		</div>
            	</div>
            	<div class="col-lg-offset-4 col-lg-8">
            		<div class="sel-add-properties">
						<select id="list_props_newProd">
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
            				<span class="remove-prop" @click="removeProp(prop, 'ADD')">
            					<i class="fa fa-close"></i>
            				</span>
            			</div>
            		</div>
            	</div>
            </div>
            <div class="box-table ls-prod-childs">
                <table class="table table-head-black table-row-notBorder" v-show="productAc.props.length">
					<thead>
						<tr>
							<th class="cs-ellipsis-clm text-center">Ảnh</th>
							<th class="cs-ellipsis-clm text-right">Mã sản phẩm</th>
							<th class="cs-ellipsis-clm text-right" v-for="prop in productAc.props">
								{{ prop.prop_name }}
							</th>
							<th class="cs-ellipsis-clm text-right">Số lượng</th>
							<th class="cs-ellipsis-clm text-right">Giá nhập</th>
							<th class="cs-ellipsis-clm text-right">Đơn giá</th>
							<th class="cs-ellipsis-clm text-center"></th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(child, index) in productAc.childs" class="bg-bold-border">
							<td class="text-center">
								<div class="contAddImgChild">
									<div class="img-preview-prop" @click="openUpImg(child, index)">
										<div class="contImgLs" v-if="child.lastImg">
											<img :src="child.lastImg">
											<div class="numImgTotal" v-show="child.images.length > 1">
												+{{child.images.length - 1}}
											</div>
										</div>
										<i class="fa fa-picture-o" v-else></i>
									</div>
								  	<div class="lsImgProdChilds" v-show="child.uploadImg">
										<vue-dropzone v-on:vdropzone-removed-file="removeImageProd" v-on:vdropzone-complete="changeIconDelete" v-on:vdropzone-success="successUpload" class="dropzonels" :id="'dropzoneChild_'+index" ref="dropzoneChild" :options="dropzoneOptions">
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
								<div class="itTaHasImg">
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
									<input type="number" class="form-control" min="0" v-model="child.quantity">
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
								<div class="itTaHasImg">
									<span class="ac-row-tb del-row-table" @click="removeRow(child, index, 'ADD')">
										<i class="fa fa-close mar-top-10"></i>
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
            	<div class="add-listImg-product">
			      	<div id="list-images-product" class="image-preview">
					  	<label for="image-upload" id="add-image-more">
					  		<span class="icon-add-img">
					  			<i class="fa fa-plus"></i>
					  		</span>
					  		<span class="txt-add-img">Thêm ảnh</span>
					  	</label>
					</div>
    			</div>
    			<div class="list-images-preview dropzone-previews" id="list-images-prod">
    				
    			</div>
            </div>
        </div>
        <div class="modal-footer">
        	<span class="processing-icon" v-show="productAc.isCreating">
				<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
				<span class="sr-only">Loading...</span>
        	</span>
			<button class="btn btn-success" :disabled="productAc.isCreating" @click="processCreateProduct()">Đồng ý</button>
			<button class="btn btn-danger" @click="reset()">Hủy bỏ</button>
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
	// import 'vue2-dropzone/dist/vue2Dropzone.css'

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
		          	maxFilesize: 1,
		          	headers: {
		          		'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
						'Authorization': 'Bearer ' + getCookie('tk_cs')
		          	},
		          	addRemoveLinks: true,
		          	dictDefaultMessage: '<div class="iconAddMoreImg">'+
		    								'<div class="innerIconPlus">'+
		    									'<i class="fa fa-plus"></i>'+
		    									'<div class="txtIconPlus">Tải lên</div>'+
		    								'</div>'+
		    							'</div>'
		      	},
		      	showListImg: false,
		      	childActive: null
			}
		},
		computed: {
			totalProduct() {
				let total = 0;
				for (var i = 0; i < this.productAc.childs.length; i++) {
					total += parseInt(this.productAc.childs[i].quantity);
				}
				return total;
			},
			props() {
				return this.$store.state.props;
			},
			cates() {
				return this.$store.state.cates;
			}
		},
		mounted() {
			EventBus.$on('get-add-product', (cate = null) => {
				if (cate !== null) {this.productAc.product.cate = cate};
				this.getAddProduct();
			});
			$('.lsImgProdChilds').on('click', function(event){
			    event.stopPropagation();
			});
		},
		updated() {
			let thisIns = this;
			let findSe = $("#list_cates_newProd");
			let findSel = $("#list_props_newProd");
			if (findSe.length > 0 && findSel.length > 0) {
				new SlimSelect({
				  	select: '#list_cates_newProd',
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
				  	select: '#list_props_newProd',
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

			let findDzl = $('#list-images-prod');
			let findDz = $('div#thumb-imageNew-p');
			if (!this.isDoing && findDzl.length > 0 && findDz.length > 0 && $("#add-product-new").hasClass('in')) {
				$('#list-images-prod').addClass('dropzone');
				$("div#list-images-product").dropzone({ 
					url: "/api/product/uploads/images",
					previewsContainer: "#list-images-prod",
					headers: { 
						'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
						'Authorization': 'Bearer ' + getCookie('tk_cs') 
					},
					clickable: "#add-image-more",
					addRemoveLinks: true,
					acceptedMimeTypes: '.jpeg,.png,.jpg',
					maxFilesize: '3',
					init: function() {
						thisIns.dropzoneListImage = this;
						this.on("error" , function(file, response) {
							$.notify(response.errors.file[0], 'error');
						})
			            this.on("complete", function(file) {
			                $(".dz-remove").html("<span title='Xóa ảnh' class='fa fa-trash remo-img' style='font-size: 1.5em'></span>");
			            });
			            this.on("removedfile", function(file) {
			            	for (var i = 0; i < thisIns.productAc.images.length; i++) {
			            		if (thisIns.productAc.images[i].name == file.name) {
			            			let path = thisIns.productAc.images[i].path;
			            			thisIns.deleteImage(path);
			            			thisIns.productAc.images.splice(i, 1);
			            		}
			            	}
			            });
			            this.on("success", function(file, response) {
			            	if (response.success) {
			            		let newImg = {
			            				path: response.path,
			            				name: file.name
			            			}
			            		thisIns.productAc.images.push(newImg);
			            	}
			            });
			        }
				});
				$('#thumb-previews').addClass('dropzone');
				$("div#thumb-imageNew-p").dropzone({
					url: "/api/product/uploads/images",
					previewsContainer: "#thumb-previews",
					headers: { 
						'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
						'Authorization': 'Bearer ' + getCookie('tk_cs') 
					},
					addRemoveLinks: true,
					clickable: "#change-img",
					maxFiles: 1,
					acceptedMimeTypes: '.jpeg,.png,.jpg',
					maxFilesize: '3',
					init: function() {
						thisIns.dropzoneThumb = this;
						this.on("error" , function(file, response) {
							if (response.errors) {
								$.notify(response.errors.file[0], 'error');
							}
						})
			            this.on("complete", function(file) {
			                $(".dz-remove").html("<span title='Xóa ảnh' class='fa fa-trash remo-img' style='font-size: 1.5em'></span>");
			            });
			            this.on("removedfile", function(file) {
			            	let path = thisIns.productAc.product.thumb ? thisIns.productAc.product.thumb.path : '';
			            	if (path) {
			            		thisIns.deleteImage(path);
			            	}
			            	thisIns.productAc.product.thumb = null;
			            });
			            this.on("success", function(file, response) {
			            	if (response.success) {
			            		let newImg = {
			            				path: response.path,
			            				name: file.name
			            			}
			            		thisIns.productAc.product.thumb = newImg;
			            	}
			            });
			        }
				})
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
			    // e.target.setSelectionRange(start, start);
		  	},
			getAddProduct() {
				$("#add-product-new").modal('show');
				this.isLoading = true;
				get('/api/product/add')
				.then((res) => {
					if (res.data) {
						this.allCates = res.data.cates;
						this.isLoading = false;
					}
				})
				.catch((err) => {
					$.notify('Lỗi tải trang tạo sản phẩm', 'error')
				})
			},
			processCreateProduct() {
				if (this.productAc.childs.length > 0) {
					this.productAc.product.quantity = this.totalProduct;
				}
				this.productAc.product.price = valuePrice(this.productAc.product.price);
				this.productAc.product.priceImp = valuePrice(this.productAc.product.priceImp);
				for (var i = 0; i < this.productAc.childs.length; i++) {
					let child = this.productAc.childs[i];
					this.productAc.childs[i].id = child.id;
					this.productAc.childs[i].name = this.productAc.product.name;
					this.productAc.childs[i].code = child.code;
					this.productAc.childs[i].price = valuePrice(child.price);
					this.productAc.childs[i].priceImp = valuePrice(child.priceImp);
					this.productAc.childs[i].quantity = child.quantity;
					this.productAc.childs[i].props = child.props;
				}
				let form = {
					product: this.productAc.product,
					childs: this.productAc.childs,
					images: this.productAc.images
				};
				this.createNewProduct(form);
			},
			createNewProduct(form) {
				this.productAc.isCreating = true;
				post('/api/product/create', form)
				.then((res) => {
					if (res.data.created) {
						this.getListProducts();
						this.productAc.isCreating = false;
						this.reset();
						$("#add-product-new").modal("hide");
						$.notify('Tạo sản phẩm thành công', 'success');
					} else {
						$.notify(res.data.message, 'error');
						this.productAc.isCreating = false;
					}
				})
				.catch((err) => {
					if (err.response.status == 422) {
						this.productAc.isCreating = false;
						let errors = err.response.data.errors;
						for (var k in errors) {
							$.notify(errors[k], 'error');
						};
					}
				})
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
				this.allCates = [];
				this.isDoing = false;
				this.dropzoneThumb.destroy();
				this.dropzoneListImage.destroy();
				$("#add-product-new").modal('hide');
			},
			getListProducts() {
				EventBus.$emit('get-list-products');
			},
			getAddCategory() {
				this.reset();
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
					price: parentPrice,
					priceImp: parentPriceImp,
					props: {},
					id: null,
					uploadImg: false,
					images: [],
					lastImg: null
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
			successUpload(file, response) {
				if (response.success) {
            		let newImg = {
            				path: response.path,
            				name: file.name
            			}
            		let index = this.childActive;
            		this.productAc.childs[index].images.push(newImg);
            		this.productAc.childs[index].lastImg = response.path;
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
					this.productAc.childs[ind].images.forEach((val, index) => {
						if (val.name == file.name) {
	            			this.deleteImage(val.path);
	            			this.productAc.childs[ind].images.splice(index, 1);
						}
					})
				}
			}
		},
		beforeDestroy () {
			EventBus.$off('get-add-product')
		}
	}
</script>