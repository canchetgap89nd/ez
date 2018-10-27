<template>
	<div>
		<div class="modal-content">
	        <div class="modal-header">
	            <h4 class="modal-title text-center">
	            	<span class="title-modal-cus">Phiếu nhập hàng mới</span>
	            </h4>
	        </div>
	        <div v-if="importProd.process" class="text-center">
				<div class="lds-css ng-scope" style="display: inline-block;"> 
				    <div style="width:100%;height:100%" class="lds-eclipse"> 
				        <div></div> 
				    </div> 
				</div>
			</div>
	        <div class="modal-body">
	        	<div class="row">
	            	<div class="col-lg-4">
		            	<div class="form-group">
		            		<label>Chọn sản phẩm</label>
						  	<search-product></search-product>
		            	</div>
	            	</div>
	        	</div>
	            <div>
	                <table class="table table-head-black table-row-notBorder">
						<thead>
							<tr>
								<th class="cs-ellipsis-clm">Mã SP</th>
								<th class="text-center cs-ellipsis-clm">Hình ảnh</th>
								<th class="cs-ellipsis-clm" style="min-width: 80px">Tên sản phẩm</th>
								<th class="cs-ellipsis-clm text-right">Còn trong kho</th>
								<th class="cs-ellipsis-clm text-right">Nhập thêm</th>
								<th class="cs-ellipsis-clm text-right">Giá nhập</th>
								<th class="cs-ellipsis-clm text-right">Tổng tiền</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(product, index) in importProd.productsChecked" :class="{ 'bg-bold-border': !product.parent_id }">
								<td class="cs-ellipsis-clm">
									{{ product.prod_code }}
								</td>
								<td class="text-center cs-ellipsis-clm">
									<a href="javascript:;" class="a-thumb-tb">
										<img :src="product.prod_thumb">
									</a>
								</td>
								<td class="cs-ellipsis-clm" style="min-width: 80px">
									<div class="name-product-mana">
										{{ product.prod_name }}
									</div>
									<div class="relate-des" v-if="! product.parent_id">
										{{ product.count_childs ? product.count_childs : 0 }} sản phẩm cùng loại
									</div>
									<div v-else class="relate-des">
										{{ product.properties }}
									</div>
								</td>
								<td class="cs-ellipsis-clm text-right">
									<label style="margin-top: 8px">
										{{ product.prod_quantity - product.count_sold }}
									</label>
								</td>
								<td class="cs-ellipsis-clm text-right">
									<div class="inNumber-has-bt">
										<input type="text" value="0" class="form-control" min="1" v-model="product.count_imp">
										<span class="cont-btn-in">
											<span class="ac-number up-number" @click="product.count_imp++">
												<i class="fa fa-plus"></i>
											</span>
											<span class="ac-number low-number" @click="product.count_imp--">
												<i class="fa fa-minus"></i>
											</span>
										</span>
									</div>
								</td>
								<td class="cs-ellipsis-clm text-right" style="min-width: 80px">
									<input type="text" class="form-control" v-model="product.prod_price_imp" @input="fixPrice($event, product, 'prod_price_imp')">
								</td>
								<td class="cs-ellipsis-clm text-right">
									{{ formatPrice(valuePrice(product.prod_price_imp) * product.count_imp) }}
								</td>
								<td class="cs-ellipsis-clm text-center">
									<span class="ac-row-tb del-row-table" @click="removeProdImp(index)">
										<i class="fa fa-close mar-top-10"></i>
									</span>
								</td>
							</tr>
						</tbody>
						<tfoot v-show="importProd.productsChecked.length > 0">
							<tr>
								<th class="cs-ellipsis-clm">Tổng</th>
								<th class="text-center"></th>
								<th class="text-center"></th>
								<th class="text-center"></th>
								<th class="cs-ellipsis-clm text-right">{{ totalQuantityImport }}</th>
								<th class="text-center"></th>
								<th class="cs-ellipsis-clm text-right">{{ formatPrice(totalAmountImport) }}</th>
								<th></th>
							</tr>
						</tfoot>
					</table>
	            </div>
	        </div>
	        <div class="modal-footer" v-if="importProd.isImporting">
	        	<process></process>
	        </div>
	        <div class="modal-footer" v-else>
	        	<button class="btn btn-success" @click="processImport()">Xác nhận</button>
	        	<button class="btn btn-danger" @click="resetImport()">Hủy bỏ</button>
	        </div>
	    </div>
	</div>
</template>
<script type="text/javascript">

	import { EventBus } from '../../helpers/bus'
	import { functionHelper } from '../../helpers/myfunction'
	import {formatPrice} from '../../helpers/numeralfix'
	import {valuePrice} from '../../helpers/numeralfix'
	import {numeral} from '../../helpers/numeralfix'
	import { get } from '../../helpers/send'
	import { post } from '../../helpers/send'
	import Process from '../loading/Process.vue'
	import {momentLocale} from '../../helpers/momentfix'
	import SearchProduct from '../../views/SearchProduct.vue'
	
	export default {
		components: {
			'search-product': SearchProduct,
			'process': Process
		},
		data() {
			return {
				importProd: {
					productsChecked: [],
					isImporting: false
				}
			}
		},
		computed: {
			totalAmountImport() {
				let total = 0;
				for (var i = 0; i < this.importProd.productsChecked.length; i++) {
					total += (this.importProd.productsChecked[i].count_imp * valuePrice(this.importProd.productsChecked[i].prod_price_imp));
				}
				return total;
			},
			totalQuantityImport() {
				let total = 0;
				for (var i = 0; i < this.importProd.productsChecked.length; i++) {
					total += this.importProd.productsChecked[i].count_imp;
				}
				return total;
			}
		},
		mounted() {
			EventBus.$on('get-add-import', () => {
				this.getImportProduct();
			})
			EventBus.$on('choose-product', (product) => {
		  		if (! functionHelper.checkIdInArray(product.id, this.importProd.productsChecked)) {
		  			product.prod_price_imp = formatPrice(product.prod_price_imp);
		  			let prod = {
		  				id: product.id,
		  				count_imp: 0,
		  				prod_code: product.prod_code,
		  				prod_thumb: product.prod_thumb,
		  				prod_name: product.prod_name,
		  				prod_quantity: product.prod_quantity,
		  				prod_price_imp: product.prod_price_imp,
		  				count_childs: product.count_childs,
		  				parent_id: product.parent_id,
		  				properties: product.properties,
		  				count_sold: product.count_sold
		  			}
					this.importProd.productsChecked.push(prod);
		  		}
			})
		},
		methods: {
			fixPrice(e, o, prop) {
			    e.target.value = numeral(e.target.value).format('0,0');
			    this.$set(o, prop, e.target.value);
		  	},
		  	formatPrice(price) {
		  		return formatPrice(price);
		  	},
		  	valuePrice(price) {
		  		return valuePrice(price);
		  	},
			getImportProduct() {
				$('#add-warehouse').modal('show');
			},
			removeProdImp(index) {
				this.importProd.productsChecked.splice(index, 1);
			},
			processImport() {
				let productsImp = [];
				let products = this.importProd.productsChecked;
				for (var i = 0; i < products.length; i++) {
					let tg = {
							id: products[i].id,
							quantity_import: products[i].count_imp,
							priceImp: valuePrice(products[i].prod_price_imp)
						}
					productsImp.push(tg);
				}
				let form = {
						products: productsImp
					}
				this.importProducts(form);
			},
			importProducts(form) {
				this.importProd.isImporting = true;
				post('/api/product/import', form)
				.then((res) => {
					if (res.data.imported) {
						EventBus.$emit('get-list-import');
						$.notify('Đã nhập hàng thành công!', 'success');
						this.resetImport();
						this.importProd.isImporting = false;
					} else
					if (res.data.errors) {
						$.notify(res.data.message, 'error');
						this.importProd.isImporting = false;
					}
				})
				.catch((err) => {
					if (err.response.status == 422) {
						if (err.response.status == 422) {
							this.importProd.isImporting = false;
							let errors = err.response.data.errors;
							for (var k in errors) {
								$.notify(errors[k], 'error');
							};
						}
					}
				})
			},
			resetImport() {
				this.importProd.productsChecked = [];
				this.importProd.isImporting = false;
				$('#add-warehouse').modal('hide');
			},
		},
		beforeDestroy () {
			EventBus.$off('get-add-import')
			EventBus.$off('choose-product')
		}
	}
</script>