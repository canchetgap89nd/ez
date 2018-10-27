<template>
	<div>
		<div class="modal-content" v-if="isLoading">
			<circle-load></circle-load>
		</div>
		<div class="modal-content" v-else>
	        <div class="modal-header">
	            <h4 class="modal-title text-center">
	            	<span class="title-modal-cus">Thông tin phiếu nhập hàng</span>
	            </h4>
	        </div>
	        <div class="modal-body">
	        	<div class="row">
	            	<div class="col-lg-3">
		            	<div class="form-group">
		            		<label>Người nhập</label>
		            		<p class="help-block">{{ infoImport.info.username_import }}</p>
		            	</div>
	            	</div>
	            	<div class="col-lg-3">
		            	<div class="form-group">
		            		<label>Thời gian nhập</label>
		            		<p class="help-block">{{ formatTime(infoImport.info.created_time, 'HH:mm:ss DD-MM-YYYY') }}</p>
		            	</div>
	            	</div>
	            	<div class="col-lg-3" v-show="infoImport.info.status == 'CANCEL'">
		            	<div class="form-group">
		            		<label>Người hủy</label>
		            		<p class="help-block" v-if="infoImport.info.user_cancel">{{ infoImport.info.user_cancel.name }}</p>
		            	</div>
	            	</div>
	            	<div class="col-lg-3" v-show="infoImport.info.status == 'CANCEL'">
		            	<div class="form-group">
		            		<label>Thời gian hủy</label>
		            		<p class="help-block">{{ formatTime(infoImport.info.updated_time, 'HH:mm:ss DD-MM-YYYY') }}</p>
		            	</div>
	            	</div>
	        	</div>
	            <div v-show="infoImport.products.length > 0">
	                <table class="table table-head-black table-row-notBorder">
						<thead>
							<tr>
								<th class="cs-ellipsis-clm">Mã SP</th>
								<th class="text-center cs-ellipsis-clm">Hình ảnh</th>
								<th class="cs-ellipsis-clm">Tên sản phẩm</th>
								<th class="cs-ellipsis-clm text-right">Còn trong kho</th>
								<th class="cs-ellipsis-clm text-right">Nhập thêm</th>
								<th class="cs-ellipsis-clm text-right">Giá nhập (VNĐ)</th>
								<th class="cs-ellipsis-clm text-right">Tổng tiền (VNĐ)</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="product in infoImport.products" :class="{ 'bg-bold-border': !product.parent_id }">
								<td class="cs-ellipsis-clm">
									{{ product.prod_code }}
								</td>
								<td class="text-center cs-ellipsis-clm">
									<a href="javascript:;" class="a-thumb-tb" v-if="product.prod_thumb">
										<img :src="product.prod_thumb">
									</a>
								</td>
								<td class="cs-ellipsis-clm">
									<div class="name-product-mana">
										{{ product.prod_name }}
									</div>
									<div v-if="product.properties" class="relate-des">
										({{ product.properties }})
									</div>
								</td>
								<td class="cs-ellipsis-clm text-right">
									{{ product.inventory_prod }}
								</td>
								<td class="cs-ellipsis-clm text-right">
									{{ product.quantity_prod }}
								</td>
								<td class="cs-ellipsis-clm text-right">
									{{ formatPrice(product.price_imp) }}
								</td>
								<td class="cs-ellipsis-clm text-right">
									{{ formatPrice(product.price_imp * product.quantity_prod) }}
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th class="cs-ellipsis-clm">Tổng</th>
								<th class="text-center"></th>
								<th class="text-center"></th>
								<th class="text-center"></th>
								<th class="cs-ellipsis-clm text-right">{{ infoImport.info.total_quantity }}</th>
								<th class="text-center"></th>
								<th class="cs-ellipsis-clm text-right">{{ formatPrice(infoImport.info.total_amount) }}</th>
							</tr>
						</tfoot>
					</table>
	            </div>
	        </div>
	        <div class="modal-footer" v-if="isSaving">
	        	<process></process>
	        </div>
	        <div class="modal-footer" v-else>
	        	<button v-if="(infoImport.info.status == 'IMPORT') && infoImport.info.can_destroy" class="btn btn-danger" @click="cancelImportion(infoImport.info.id)">Hủy phiếu</button>
	        	<button class="btn btn-default" @click="reset()">Đóng</button>
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
	import Circle from '../loading/Circle.vue'
	import {momentLocale} from '../../helpers/momentfix'
	import SearchProduct from '../../views/SearchProduct.vue'

	export default {
		components: {
			'process': Process,
			'circle-load': Circle,
		},
		data() {
			return {
				infoImport: {
					info: {},
					products: []
				},
				isLoading: false,
				isSaving: false
			}
		},
		mounted() {
			EventBus.$on('get-detail-import', (id) => {
				this.getImportEntity(id);
			})
		},
		methods: {
			formatTime(time, formater) {
				return momentLocale(time).format(formater);
			},
			formatPrice(price) {
		  		return formatPrice(price);
		  	},
			getImportEntity(id) {
				this.isLoading = true;
				get('/api/product/import/get/'+id)
				.then((res) => {
					if (res.data.info) {
						this.infoImport.info = res.data.info;
						this.infoImport.products = res.data.products;
					}
					this.isLoading = false;
				})
			},
			cancelImportion(id) {
				this.isSaving = true;
				post('/api/product/import/cancel/'+id)
				.then((res) => {
					if (res.data.canceled) {
						EventBus.$emit('get-list-import');
						$.notify('Hủy nhập hàng thành công', 'success');
						this.reset();
						this.isSaving = false;
					} else {
						if (res.data.message) {
							$.notify(res.data.message, 'error');
						}
						this.isSaving = false;
					}
				})
				.catch((err) => {
					$.notify("Lỗi hủy phiếu nhập hàng", "error");
					this.isSaving = false;
				})
			},
			reset() {
				$("#info-warehouse").modal("hide");
				this.infoImport.info = {};
				this.infoImport.products = [];
			}
		},
		beforeDestroy () {
			EventBus.$off('get-detail-import')
		}
	}

</script>