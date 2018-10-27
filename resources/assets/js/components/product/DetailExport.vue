<template>
	<div>
		<div class="modal-content" v-if="isLoading">
			<circle-loading></circle-loading>
		</div>
		<div class="modal-content" v-else>
	        <div class="modal-header">
	            <h4 class="modal-title text-center">
	            	<span class="title-modal-cus">Thông tin phiếu xuất hàng</span>
	            </h4>
	        </div>
	        
	        <div class="modal-body">
	        	<div class="row">
	            	<div class="col-lg-4">
		            	<div class="form-group">
		            		<label>Người xuất</label>
		            		<p class="help-block">{{ info.user_ex }}</p>
		            	</div>
	            	</div>
	            	<div class="col-lg-4">
		            	<div class="form-group">
		            		<label>Thời gian xuất</label>
		            		<p class="help-block">{{ info.created_at }}</p>
		            	</div>
	            	</div>
	        	</div>
	            <div v-show="products.length > 0">
	                <table class="table table-head-black table-row-notBorder">
						<thead>
							<tr>
								<th class="cs-ellipsis-clm">Mã SP</th>
								<th class="cs-ellipsis-clm text-center">Hình ảnh</th>
								<th class="cs-ellipsis-clm" style="min-width: 80px">Tên sản phẩm</th>
								<th class="cs-ellipsis-clm text-right">Còn trong kho</th>
								<th class="cs-ellipsis-clm text-right">SL xuất</th>
								<th class="cs-ellipsis-clm text-right">Giá xuất</th>
								<th class="cs-ellipsis-clm text-right">Tổng tiền</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="product in products" :class="{ 'bg-bold-border': !product.parent_id }">
								<td class="cs-ellipsis-clm">
									{{ product.prod_code }}
								</td>
								<td class="cs-ellipsis-clm text-center">
									<a href="javascript:;" class="a-thumb-tb" v-if="product.prod_thumb">
										<img :src="product.prod_thumb">
									</a>
								</td>
								<td class="cs-ellipsis-clm" style="min-width: 80px">
									<div class="name-product-mana">
										{{ product.prod_name }}
									</div>
									<div v-if="product.properties" class="relate-des">
										({{ product.properties }})
									</div>
								</td>
								<td class="cs-ellipsis-clm text-right">
									{{ product.inventory_ex }}
								</td>
								<td class="cs-ellipsis-clm text-right">
									{{ product.quantity_ex }}
								</td>
								<td class="cs-ellipsis-clm text-right">
									{{ formatPrice(product.price_ex) }}
								</td>
								<td class="cs-ellipsis-clm text-right">
									{{ formatPrice(product.price_ex * product.quantity_ex) }}
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th class="cs-ellipsis-clm">Tổng</th>
								<th class="text-center"></th>
								<th class="text-center"></th>
								<th class="text-center"></th>
								<th class="cs-ellipsis-clm text-right">{{ total_quantity }}</th>
								<th class="text-center"></th>
								<th class="cs-ellipsis-clm text-right">{{ formatPrice(total_amount) }}</th>
							</tr>
						</tfoot>
					</table>
	            </div>
	        </div>
	        <div class="modal-footer">
	        	<button class="btn btn-default" data-dismiss="modal">Đóng</button>
	        </div>
	    </div>
	</div>
</template>
<script type="text/javascript">
	
	import { EventBus } from '../../helpers/bus'
	import { get } from '../../helpers/send'
	import {momentLocale} from '../../helpers/momentfix'
	import {formatPrice} from '../../helpers/numeralfix'
	import Circle from '../loading/Circle.vue'

	export default {
		components: {
			'circle-loading': Circle
		},
		data() {
			return {
				info: {},
				products: [],
				isLoading: false
			}
		},
		mounted() {
			EventBus.$on('get-detail-export', (id) => {
				this.getDetailExport(id);
				$("#detail-export-modal").modal("show");
			})
		},
		computed: {
			total_quantity() {
				let total = 0;
				for (var i = this.products.length - 1; i >= 0; i--) {
					total += this.products[i].quantity_ex;
				}
				return total;
			},
			total_amount() {
				let total = 0;
				for (var i = this.products.length - 1; i >= 0; i--) {
					total += this.products[i].quantity_ex * this.products[i].price_ex;
				}
				return total;
			}
		},
		methods: {
			getDetailExport(id) {
				this.isLoading = true;
				get('/api/product/export/'+id)
				.then((res) => {
					if (res.data.info) {
						res.data.info.created_time = momentLocale(res.data.info.created_at).format('HH:mm DD-MM-YYYY');
						this.info = res.data.info;
						this.products = res.data.products;
					}
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải thông tin phiếu xuất', 'error');
					this.isLoading = false;
				})
			},
			formatPrice(price) {
				return formatPrice(price);
			}
		},
		beforeDestroy () {
			EventBus.$off('get-detail-export')
		}
	}
</script>