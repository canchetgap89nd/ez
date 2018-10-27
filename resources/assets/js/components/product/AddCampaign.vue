<template>
	<div>
		<div class="modal-content">
	        <div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal">&times;</button>
	            <h4 class="modal-title text-center">
	            	<span class="title-modal-cus">Thêm chiến dịch khuyến mãi</span>
	            </h4>
	        </div>
	        <div class="modal-body">
	        	<div class="form-horizontal">
	            	<div class="form-group">
	            		<label class="control-label col-lg-2" for="name-camp">Tên chiến dịch</label>
					    <div class="col-lg-9">
				      		<input type="text" class="form-control" v-model="newCamp.name" placeholder="Nhập tên chiến dịch khuyến mãi">
					    </div>
	            	</div>
	            	<div class="form-group">
	            		<label class="control-label col-lg-2" for="name-camp">Mức giảm giá</label>
					    <div class="col-lg-2">
				      		<div class="inNumber-has-bt">
								<input class="form-control" min="0" v-model="newCamp.discount" type="text">
								<span class="unit">%</span>
								<span class="cont-btn-in">
									<span class="ac-number up-number" @click="newCamp.discount++">
										<i class="fa fa-plus"></i>
									</span>
									<span class="ac-number low-number" @click="newCamp.discount--">
										<i class="fa fa-minus"></i>
									</span>
								</span>
							</div>
					    </div>
					    <label class="control-label col-lg-2">Thời gian</label>
					    <div class="col-lg-6">
					    	<div class="radio pull-left">
							  	<label>
							    	<input type="radio" v-model="newCamp.typeTime" value="1"> 
							    	Đặt thời gian
						  		</label>
							</div>
							<div class="date-sales-add">
								<input id="time_range_sale" :disabled="newCamp.typeTime == 2" class="form-control form-control-cus picker-cus" type="text">
	    						<i class="glyphicon glyphicon-calendar icon-pickdate"></i>
							</div>
							<div class="radio">
							  	<label>
							    	<input type="radio" v-model="newCamp.typeTime" value="2"> Đến khi hết sản phẩm
						  		</label>
							</div>
					    </div>
	            	</div>
	            	<div class="form-group">
	            		<label class="control-label col-lg-2" for="name-camp">Chọn sản phẩm</label>
					    <div class="col-lg-4">
				      		<search-product></search-product>
					    </div>
	            	</div>
	        	</div>
	        	
	            <div>
	                <table class="table table-head-black table-row-notBorder">
						<thead>
							<tr>
								<th class="cs-ellipsis-clm">Mã SP</th>
								<th class="cs-ellipsis-clm text-center">Hình ảnh</th>
								<th class="cs-ellipsis-clm" style="min-width: 80px">Tên sản phẩm</th>
								<th class="tcs-ellipsis-clm text-right">Còn trong kho</th>
								<th class="tcs-ellipsis-clm text-right">Đơn giá (VNĐ)</th>
								<th class="tcs-ellipsis-clm text-right">Giá sau KM (VNĐ)</th>
								<th class="tcs-ellipsis-clm text-center"></th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(product, index) in newCamp.products" :class="{ 'bg-bold-border': !(product.parent_id > 0) }">
								<td class="tcs-ellipsis-clm">
									{{ product.prod_code }}
								</td>
								<td class="tcs-ellipsis-clm text-center">
									<a href="#" class="a-thumb-tb">
										<img :src="product.prod_thumb">
									</a>
								</td>
								<td class="tcs-ellipsis-clm" style="min-width: 80px">
									<div class="name-product-mana">
										{{ product.prod_name }}
									</div>
									<div class="relate-des" v-if="product.parent_id > 0">
										{{ product.properties }}
									</div>
									<div class="relate-des" v-else>
										{{ product.count_childs }} sản phẩm cùng loại
									</div>
								</td>
								<td class="tcs-ellipsis-clm text-right">
									{{ product.prod_quantity - product.count_sold }}
								</td>
								<td class="tcs-ellipsis-clm text-right">
									{{ parseFloat(product.prod_price).toLocaleString() }}
								</td>
								<td class="tcs-ellipsis-clm text-right">
									{{ (parseFloat(product.prod_price) - (parseFloat(product.prod_price) * parseFloat(newCamp.discount) / 100)).toLocaleString() }}
								</td>
								<td class="tcs-ellipsis-clm text-center">
									<span title="Xóa sản phẩm" @click="removeProdNewCamp(index)" class="ac-row-tb del-row-table">
										<i class="fa fa-close"></i>
									</span>
								</td>
							</tr>
						</tbody>
					</table>
	            </div>
	        </div>
	        <div class="modal-footer" v-if="newCamp.isCreating">
	        	<process></process>
	        </div>
	        <div class="modal-footer" v-else>
	        	<div class="count-total pull-left">
			        Tổng {{ newCamp.products.length }} sản phẩm
			    </div>
	        	<button class="btn btn-success" @click="processCreateCamp()">Xác nhận</button>
	        	<button class="btn btn-danger" @click="resetCreateCamp()">Hủy bỏ</button>
	        </div>
	    </div>
	</div>
</template>
<script type="text/javascript">

	import SearchProduct from '../../views/SearchProduct.vue'
	import { EventBus } from '../../helpers/bus'
	import { functionHelper } from '../../helpers/myfunction'
	import {formatPrice} from '../../helpers/numeralfix'
	import {valuePrice} from '../../helpers/numeralfix'
	import {numeral} from '../../helpers/numeralfix'
	import { get } from '../../helpers/send'
	import { post } from '../../helpers/send'
	import Process from '../loading/Process.vue'
	import {momentLocale} from '../../helpers/momentfix'
	import calendarOption from '../../helpers/calendarOption'
	
	export default {
		components: {
			'search-product': SearchProduct,
			'process': Process
		},
		data() {
			return {
				newCamp: {
					products: [],
					name: null,
					discount: 0,
					typeTime: 2,
					startTime: null,
					endTime: null,
					isCreating: false,
				},
			}
		},
		mounted() {
			EventBus.$on('get-add-campaign', () => {
				this.getAddCampaign();
			})
			EventBus.$on('choose-product', (product) => {
				var openModal = $("#add-sales-campaign").hasClass('in');
				var hasId = functionHelper.checkIdInArray(product.id, this.newCamp.products);
				if (!hasId && openModal) {
					if (product.hasSale) {
						$.notify('Sản phẩm này hiện đang chạy với một chương trình khuyến mãi khác', 'warn');
					} else {
			  			let prod = {
			  				id: product.id,
			  				prod_code: product.prod_code,
			  				prod_thumb: product.prod_thumb,
			  				prod_name: product.prod_name,
			  				prod_quantity: product.prod_quantity,
			  				prod_price_imp: product.prod_price_imp,
			  				prod_price: product.prod_price,
			  				count_childs: product.count_childs,
			  				parent_id: product.parent_id,
			  				properties: product.properties,
			  				count_sold: product.count_sold
			  			}
			  			this.newCamp.products.push(prod);
					}
		  		}
			})
		},
		updated() {
			$('#time_range_sale').daterangepicker(
			{
			    "locale": calendarOption,
				"timePicker": true,
				"timePicker24Hour": true,
			 	"timePickerSeconds": true,
			    startDate: momentLocale().format('DD-MM-YYYY HH::mm:ss'),
			    minDate: momentLocale().format('DD-MM-YYYY HH::mm:ss'),
			    "opens": "left"
			}, 
			(start, end, label) => {
			    this.newCamp.startTime = start.format('YYYY-MM-DD HH:mm:ss');
			    this.newCamp.endTime = end.format('YYYY-MM-DD HH:mm:ss');
			});
		},
		methods: {
			getAddCampaign() {
				$('#add-sales-campaign').modal('show');
			},
			removeProdNewCamp(index) {
				this.newCamp.products.splice(index, 1);
			},
			createCampaign(form) {
				this.newCamp.isCreating = true;
				post('/api/campaign/create', form)
				.then((res) => {
					if (res.data.created) {
						EventBus.$emit('get-list-campaign');
						$.notify("Tạo chiến dịch thành công", 'success');
						this.resetCreateCamp();
					}
					this.newCamp.isCreating = false;
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
						this.newCamp.isCreating = false;
					}
				})
			},
			processCreateCamp() {
				let products = [];
				for (var i = 0; i < this.newCamp.products.length; i++) {
					products.push(this.newCamp.products[i].id);
				}
				let form = {
					name: this.newCamp.name,
					discount: this.newCamp.discount,
					startTime: this.newCamp.startTime,
					endTime: this.newCamp.endTime,
					typeTime: this.newCamp.typeTime,
					products: products
				}
				this.createCampaign(form);
			},
			resetCreateCamp() {
				this.newCamp.name = null;
				this.newCamp.discount = 0;
				this.newCamp.typeTime = 2;
				this.newCamp.startTime = null;
				this.newCamp.endTime = null;
				this.newCamp.products = [];
				this.newCamp.process = false;
				this.newCamp.productsHas = [];
				$("#add-sales-campaign").modal("hide");
			},
		},
		beforeDestroy () {
			EventBus.$off('get-add-campaign')
			EventBus.$off('choose-product')
		}
	}
</script>