<template>
	<div>
		<div class="modal-content" v-if="isLoading">
			<circle-load></circle-load>
		</div>
		<div class="modal-content" v-else>
	        <div class="modal-header">
	        	<button type="button" class="close" @click="resetUpdateCamp()">&times;</button>
	            <h4 class="modal-title text-center">
	            	<span class="title-modal-cus">Thông tin khuyến mãi</span>
	            </h4>
	        </div>
	        <div class="modal-body">
	        	<div class="form-horizontal">
	            	<div class="form-group">
	            		<label class="control-label col-lg-2" for="name-camp">Tên chiến dịch</label>
					    <div class="col-lg-9">
				      		<input type="text" class="form-control" :disabled="detailCamp.status == 'STOP' || role.name == 'SALER'" v-model="detailCamp.name" placeholder="Nhập tên chiến dịch khuyến mãi">
					    </div>
	            	</div>
	            	<div class="form-group">
	            		<label class="control-label col-lg-2" for="name-camp">Mức giảm giá</label>
					    <div class="col-lg-2">
				      		<div class="inNumber-has-bt">
								<input class="form-control" :disabled="detailCamp.status == 'STOP' || role.name == 'SALER'" v-model="detailCamp.discount" type="text">
								<span class="unit">%</span>
								<span class="cont-btn-in" v-show="detailCamp.status != 'STOP' && role.name != 'SALER'">
									<span class="ac-number up-number" @click="detailCamp.discount++">
										<i class="fa fa-plus"></i>
									</span>
									<span class="ac-number low-number" @click="detailCamp.discount--">
										<i class="fa fa-minus"></i>
									</span>
								</span>
							</div>
					    </div>
					    <label class="control-label col-lg-2">Thời gian</label>
					    <div class="col-lg-6">
					    	<div class="radio pull-left">
							  	<label>
							    	<input type="radio" :disabled="detailCamp.status == 'STOP' || role.name == 'SALER'" v-model="detailCamp.typeTime" value="1"> 
							    	Đặt thời gian
						  		</label>
							</div>
							<div class="date-sales-add">
								<input id="time_sale_edit" :disabled="detailCamp.typeTime == 2 || role.name == 'SALER'" class="form-control form-control-cus picker-cus" type="text">
	    						<i class="glyphicon glyphicon-calendar icon-pickdate"></i>
							</div>
							<div class="radio">
							  	<label>
							    	<input type="radio" :disabled="detailCamp.status == 'STOP' || role.name == 'SALER'" v-model="detailCamp.typeTime" value="2"> Đến khi hết sản phẩm
						  		</label>
							</div>
					    </div>
	            	</div>
	            	<div class="form-group" v-show="detailCamp.status != 'STOP' && role.name != 'SALER'">
	            		<label class="control-label col-lg-2" for="name-camp">Chọn sản phẩm</label>
					    <div class="col-lg-4">
			      			<search-product></search-product>
					    </div>
	            	</div>
	        	</div>
	        	
	            <div class="box-table">
	                <table class="table table-head-black table-row-notBorder">
						<thead>
							<tr>
								<th class="text-center">Mã SP</th>
								<th class="text-center">Hình ảnh</th>
								<th class="text-center">Tên sản phẩm</th>
								<th class="text-center">Còn trong kho</th>
								<th class="text-center">Đơn giá (VNĐ)</th>
								<th class="text-center">Giá sau KM (VNĐ)</th>
								<th class="text-center" v-show="detailCamp.status != 'STOP'"></th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(product, index) in detailCamp.products" :class="{ 'bg-bold-border': product.count_childs > 0 }">
								<td class="text-center">
									{{ product.prod_code }}
								</td>
								<td class="text-center">
									<a href="#" class="a-thumb-tb">
										<img :src="product.prod_thumb">
									</a>
								</td>
								<td>
									<div class="name-product-mana">
										{{ product.prod_name }}
									</div>
									<div class="relate-des" v-if="product.parent_id > 0">
										({{ product.properties }})
									</div>
									<div class="relate-des" v-else>
										{{ product.count_childs }} sản phẩm cùng loại
									</div>
								</td>
								<td class="text-center">
									{{ product.prod_quantity - product.count_sold }}
								</td>
								<td class="text-center">
									{{ parseFloat(product.prod_price).toLocaleString() }}
								</td>
								<td class="text-center">
									{{ (parseFloat(product.prod_price) - (parseFloat(product.prod_price) * parseFloat(detailCamp.discount) / 100)).toLocaleString() }}
								</td>
								<td class="text-center" v-show="detailCamp.status != 'STOP' && role.name != 'SALER'"> 
									<span title="Xóa sản phẩm" @click="removeProdDetailCamp(index)" class="ac-row-tb del-row-table">
										<i class="fa fa-close"></i>
									</span>
								</td>
							</tr>
							<tr v-for="(product, index) in detailCamp.newProducts" :class="{ 'bg-bold-border': product.count_childs > 0 }">
								<td class="text-center">
									{{ product.prod_code }}
								</td>
								<td class="text-center">
									<a href="#" class="a-thumb-tb">
										<img :src="product.prod_thumb">
									</a>
								</td>
								<td>
									<div class="name-product-mana">
										{{ product.prod_name }}
									</div>
									<div class="relate-des" v-show="product.parent_id > 0">
										({{ product.properties }})
									</div>
								</td>
								<td class="text-center">
									{{ product.prod_quantity - product.count_sold }}
								</td>
								<td class="text-center">
									{{ parseFloat(product.prod_price).toLocaleString() }}
								</td>
								<td class="text-center">
									{{ (parseFloat(product.prod_price) - (parseFloat(product.prod_price) * parseFloat(detailCamp.discount) / 100)).toLocaleString() }}
								</td>
								<td class="text-center"> 
									<span title="Xóa sản phẩm" @click="removeNewPropCamp(index)" class="ac-row-tb del-row-table">
										<i class="fa fa-close"></i>
									</span>
								</td>
							</tr>
						</tbody>
					</table>
	            </div>
	        </div>
	        <div class="modal-footer" v-if="detailCamp.isUpdating">
	        	<process></process>
	        </div>
	        <div class="modal-footer" v-else>
	        	<div class="count-total pull-left">
			        Tổng {{ totalProductsDetailCamp }} sản phẩm
			    </div>
			    <div v-show="role.name != 'SALER'">
		        	<button class="btn btn-danger" :disabled="detailCamp.isUpdating" @click="pauseCamp()" v-show="detailCamp.status != 'STOP'">Dừng</button>
		        	<button class="btn btn-success" :disabled="detailCamp.isUpdating" v-show="detailCamp.status != 'STOP'" @click="processUpdateCampaign()">Cập nhật</button>
		        	<button class="btn btn-success" :disabled="detailCamp.isUpdating" v-show="detailCamp.status == 'STOP' && detailCamp.typeTime == 1" @click="reCampaign()">Chạy lại</button>
		        	<button class="btn btn-default" v-show="detailCamp.status == 'STOP'" @click="resetUpdateCamp()">Đóng</button>
        		</div>
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
	import Circle from '../loading/Circle.vue'
	import {momentLocale} from '../../helpers/momentfix'
	import calendarOption from '../../helpers/calendarOption'
	
	export default {
		components: {
			'search-product': SearchProduct,
			'process': Process,
			'circle-load': Circle,
		},
		props: ['user', 'role'],
		data() {
			return {
				detailCamp: {
					isUpdating: false,
					id: null,
					products: [],
					name: null,
					discount: 0,
					typeTime: 2,
					startTime: null,
					endTime: null,
					newProducts: [],
					removeProds: []
				},
				isLoading: false,
				startTime: '',
				endTime: ''
			}
		},
		computed: {
			totalProductsDetailCamp() {
				return this.detailCamp.products.length;
			},
		},
		mounted() {
			EventBus.$on('get-detail-campaign', (id) => {
				this.getDetailCamp(id);
			})
			EventBus.$on('choose-product', (product) => {
				let check1 = functionHelper.checkIdInArray(product.id, this.detailCamp.products);
		  		let check2 = functionHelper.checkIdInArray(product.id, this.detailCamp.newProducts);
		  		var openModal = $("#detail-sales-campaign").hasClass('in');
		  		if (!check1 && !check2 && openModal) {
		  			if (!product.hasSale) {
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
	  					this.detailCamp.newProducts.push(product);
		  			} else {
		  				$.notify('Sản phẩm này hiện đang chạy với một chương trình khuyến mãi khác', 'warn');
		  			}
		  		}
			})
		},
		updated() {
			$('#time_sale_edit').daterangepicker({
				"locale": calendarOption,
				"timePicker": true,
				"timePicker24Hour": true,
			 	"timePickerSeconds": true,
				startDate: this.startTime,
				endDate: this.endTime,
			    "opens": "left"
			}, 
			(start, end, label) => {
			    this.detailCamp.startTime = start.format('YYYY-MM-DD HH:mm:ss');
			    this.detailCamp.endTime = end.format('YYYY-MM-DD HH:mm:ss');
			});
		},
		methods: {
			formatTime(time, formater) {
				return momentLocale(time).format(formater);
			},
			getDetailCamp(id) {
				this.isLoading = true;
				$("#detail-sales-campaign").modal('show');
				get('/api/campaign/'+id)
				.then((res) => {
					if (res.data) {
						let camp = res.data.camp;
						this.detailCamp.id = camp.id;
						this.detailCamp.name = camp.camp_name;
						this.detailCamp.discount = camp.perc_disc;
						this.detailCamp.typeTime = camp.sold_out ? 2 : 1;
						this.detailCamp.products = res.data.products;
						this.detailCamp.status = this.statusOfCamp(camp);

						this.startTime = momentLocale().format('DD-MM-YYYY HH:mm:ss');
						this.endTime = momentLocale().format('DD-MM-YYYY HH:mm:ss');

						if (this.detailCamp.typeTime == 1) {
							this.startTime = this.formatTime(camp.start_time, 'DD-MM-YYYY HH:mm:ss');
							this.endTime = this.formatTime(camp.end_time, 'DD-MM-YYYY HH:mm:ss');
						}
					}
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify("Lỗi tải thông tin khuyến mãi", "error");
					this.isLoading = false;
				})
			},
			statusOfCamp(camp) {
				let status = 'STOP';
				if (camp.status == 1) {
					if (camp.sold_out) {
						status = 'RUNNING';
					} else {
						let now = momentLocale().unix();
						let start = momentLocale(camp.start_time).unix();
						let end = momentLocale(camp.end_time).unix();
						if (start <= now && now <= end) {
							status = 'RUNNING';
						} else 
						if (now < start) {
							status = 'SCHEDULE';
						}
					}
				}
				return status;
			},
			processUpdateCampaign() {
				let newProducts = [];
				for (var i = 0; i < this.detailCamp.newProducts.length; i++) {
					newProducts.push(this.detailCamp.newProducts[i].id);
				}
				let form = {
					id: this.detailCamp.id,
					name: this.detailCamp.name,
					discount: this.detailCamp.discount,
					startTime: this.detailCamp.startTime,
					endTime: this.detailCamp.endTime,
					typeTime: this.detailCamp.typeTime,
					newProducts: newProducts,
					removeProds: this.detailCamp.removeProds
				}
				this.updateCampaign(form);
			},
			resetUpdateCamp() {
				this.detailCamp.id = null;
				this.detailCamp.name = null;
				this.detailCamp.discount = 0;
				this.detailCamp.typeTime = 2;
				this.detailCamp.startTime = null;
				this.detailCamp.endTime = null;
				this.detailCamp.status = null;
				this.detailCamp.products = [];
				this.detailCamp.newProducts = [];
				this.detailCamp.removeProds = [];
				this.startTime = '';
				this.endTime = '';
				$("#detail-sales-campaign").modal('hide');
			},
			updateCampaign(form) {
				this.detailCamp.isUpdating = true;
				post('/api/campaign/update/'+form.id, form)
				.then((res) => {
					if (res.data.updated) {
						$.notify('Cập nhật chiến dịch thành công', 'success');
						this.resetUpdateCamp();
						EventBus.$emit('get-list-campaign');
						this.detailCamp.isUpdating = false;
					} else {
						$.notify(res.data.message, 'error');
						this.detailCamp.isUpdating = false;
					}
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
						this.detailCamp.isUpdating = false;
					}
				})
			},
			pauseCamp() {
				this.detailCamp.isUpdating = true;
				post('/api/campaign/pause/'+this.detailCamp.id)
				.then((res) => {
					if (res.data.updated) {
						$.notify('Đã cập nhật khuyến mãi thành công', 'success');
						EventBus.$emit('get-list-campaign');
						this.resetUpdateCamp();
						this.detailCamp.isUpdating = false;
					} else {
						$.notify(res.data.message, 'error');
					}
				})	
				.catch((err) => {
					this.detailCamp.isUpdating = false;
					$.notify('Lỗi cập nhật khuyến mãi', 'error');
				})
			},
			reCampaign() {
				this.detailCamp.isUpdating = true;
				post('/api/campaign/run/again/'+this.detailCamp.id, {
					startTime: this.detailCamp.startTime,
					endTime: this.detailCamp.endTime
				})
				.then((res) => {
					if (res.data.updated) {
						EventBus.$emit('get-list-campaign');
						this.resetUpdateCamp();
						$.notify('Khuyến mãi đã được chạy lại', 'success');
					} else {
						$.notify(res.data.message, 'error');
					}
					this.detailCamp.isUpdating = false;
				})
				.catch((err) => {
					if (err.response.status === 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
						this.detailCamp.isUpdating = false;
					}
				})
			},
			removeProdDetailCamp(index) {
				if (this.detailCamp.products.length > 1 || this.detailCamp.newProducts > 1) {
					this.detailCamp.removeProds.push(this.detailCamp.products[index].id);
					this.detailCamp.products.splice(index, 1);
				}
			},
		},
		beforeDestroy () {
			EventBus.$off('get-detail-campaign')
			EventBus.$off('choose-product')
		}
	}
</script>