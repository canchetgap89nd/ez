<template>
	<div>
		<div class="modal-content modal-detail-order">
			<div class="modal-body">
				<div class="cont-send-receive">
					<div class="col-md-6">
						<div class="send-cus-info">
							<h2 class="title-block-info">
								<span class="txt-title-blockInfo">Thông tin khách hàng</span>
							</h2>
							<div class="body-block-info">
								<div class="avatar-left-info">
									<div class="avatar-detail-order">
										<img src="images/man.png">
									</div>
									<div class="name-under-avatar">
										{{ infoCus.name }}
									</div>
								</div>
								<div class="info-order-add info-block-left">
			        			<div class="info-row-add">
			        				<div class="title-row-order">
			        					<span class="icon-row-order">
			        						<i class="fa fa-user"></i>
			        					</span>
			        				</div>
			        				<div class="detail-row-order">
		        						<input type="text" :disabled="notFillInfoCus" placeholder="Nhập họ tên" v-model="infoCus.name" class="form-control input-sm">
			        				</div>
			        			</div>
			        			<div class="info-row-add">
			        				<div class="title-row-order">
			        					<span class="icon-row-order icon-phone-order">
			        						<i class="fa fa-mobile-phone"></i>
			        					</span>
			        				</div>
			        				<div class="detail-row-order">
		        						<input type="text" :disabled="notFillInfoCus" placeholder="Nhập số điện thoại" @keyup="searchCustomer()" v-model="infoCus.phone" class="form-control input-sm">
		        						<span @click="removeCus()" v-show="infoCus.id" class="remove-cus">
		        							<i class="fa fa-close"></i>
		        						</span>
		        						<ul class="dropdown-menu menu-cus-suggest" v-show="customersSuggest.length > 0">
										    <li v-for="cus in customersSuggest">
										    	<a href="javascript:;" @click="takeInfoCus(cus)">{{ cus.name_cus }}</a>
										    </li>
								  		</ul>
			        				</div>
			        			</div>
			        			<div class="info-row-add">
			        				<div class="title-row-order">
			        					<span class="icon-row-order">
			        						<i class="fa fa-envelope"></i>
			        					</span>
			        				</div>
			        				<div class="detail-row-order">
		        						<input type="email" :disabled="notFillInfoCus" placeholder="Nhập địa chỉ email" class="form-control input-sm" v-model="infoCus.email">
			        				</div>
			        			</div>
			        			<div class="info-row-add">
			        				<div class="title-row-order">
			        					<span class="icon-row-order icon-place-order">
			        						<i class="fa fa-map-marker"></i>
			        					</span>
			        				</div>
			        				<div class="detail-row-order">
		        						<input type="text" :disabled="notFillInfoCus" placeholder="Nhập địa chỉ" class="form-control input-sm" v-model="infoCus.address">
			        				</div>
			        			</div>
			        		</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="receive-cus-info">
							<h2 class="title-block-info">
								<span class="txt-title-blockInfo">Thông tin nhận hàng</span>
							</h2>
							<div class="body-block-info">
								<div class="avatar-left-info">
									<div class="avatar-receive-order">
										<img src="images/receive-none.png">
									</div>
								</div>
								<div class="info-order-add info-block-left">
				        			<div class="info-row-add">
				        				<div class="title-row-order">
				        					<span class="icon-row-order">
				        						<i class="fa fa-user"></i>
				        					</span>
				        				</div>
				        				<div class="detail-row-order">
				        					<input type="text" placeholder="Nhập họ tên" class="form-control input-sm" v-model="infoOrder.nameReceive">
				        				</div>
				        			</div>
				        			<div class="info-row-add">
				        				<div class="title-row-order">
				        					<span class="icon-row-order icon-phone-order">
				        						<i class="fa fa-mobile-phone"></i>
				        					</span>
				        				</div>
				        				<div class="detail-row-order">
				        					<input type="text" placeholder="Nhập số điện thoại" class="form-control input-sm" v-model="infoOrder.phoneReceive">
				        				</div>
				        			</div>
				        			<div class="info-row-add">
				        				<div class="title-row-order">
				        					<span class="icon-row-order">
				        						<i class="fa fa-envelope"></i>
				        					</span>
				        				</div>
				        				<div class="detail-row-order">
				        					<input type="email" placeholder="Nhập địa chỉ email" class="form-control input-sm" v-model="infoOrder.emailReceive">
				        				</div>
				        			</div>
				        			<div class="info-row-add">
				        				<div class="title-row-order">
				        					<span class="icon-row-order icon-place-order" style="margin-left: -4px">
				        						<i class="fa fa-home"></i>
				        					</span>
				        				</div>
				        				<div class="detail-row-order">
				        					<input type="text" placeholder="Nhập thêm địa chỉ VD: số nhà 15" class="form-control input-sm" id="receive-address" v-model="infoOrder.addressReceive">
				        				</div>
				        			</div>
				        			<div class="info-row-add">
				        				<div class="title-row-order">
				        					<span class="icon-row-order icon-place-order">
				        						<i class="fa fa-map-marker"></i>
				        					</span>
				        				</div>
				        				<div class="detail-row-order">
				        					<search-address></search-address>
				        				</div>
				        			</div>
			        			</div>
							</div>
						</div>
					</div>
				</div>
				<div class="cont-order-listCus">
					<h2 class="title-block-info">
					<span class="txt-title-blockInfo">Thông tin sản phẩm</span>
				</h2>
				<div class="body-block-info">
					<div class="cont-table-custom">
						<div class="inner-cont-tableCustom">
							<table class="table-custom">
								<thead>
									<tr>
										<th></th>
										<th class="cs-ellipsis-clm">
											Sản phẩm
										</th>
										<th class="cs-ellipsis-clm text-right">
											Số lượng
										</th>
										<th class="cs-ellipsis-clm text-right">
											Đơn giá
										</th>
										<th class="cs-ellipsis-clm text-right">
											KM (%)
										</th>
										<th class="cs-ellipsis-clm text-right">
											Giá sau KM
										</th>
										<th class="cs-ellipsis-clm text-right">
											Thành tiền
										</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(product, index) in products">
										<td class="cs-ellipsis-clm text-center thumb-product">
											<img :src="product.thumb">
										</td>
										<td class="cs-ellipsis-clm">
											<div>
												<span class="txt-name-productOrder" :title="product.name">
													{{ product.name }}
												</span>
											</div>
											<div style="position: relative;">
												<span class="text-blur" v-if="product.properties">
													( {{ product.properties }} )
												</span>
											</div>
										</td>
										<td class="cs-ellipsis-clm text-right quantity-numberOrder">
											<input type="number" class="form-control" min="1" v-model="product.quantity">
										</td>
										<td class="cs-ellipsis-clm text-right">{{ parseFloat(product.price).toLocaleString() }}</td>
										<td class="cs-ellipsis-clm text-right">
											{{ product.hasSale ? product.percDisc : 0 }}
										</td>
										<td class="cs-ellipsis-clm text-right">{{ parseFloat(product.priceSale).toLocaleString() }}</td>
										<td class="cs-ellipsis-clm text-right">{{ parseFloat(product.priceSale * product.quantity).toLocaleString() }}</td>
										<td class="cs-ellipsis-clm text-center">
											<span class="del-orderCus" title="Xóa SP khỏi danh sách" @click="removeProduct(index)">
												<i class="fa fa-minus-square"></i>
											</span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="contUnder-table-total">
						<table class="table-total-under">
							<thead>
								<tr>
									<th class="thumb-product"></th>
									<th>
										<search-product></search-product>
									</th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th class="total-cell"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<th>Tổng tiền</th>
									<td class="text-center"></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="total-cell">{{ totalMoney.toLocaleString() }}</td>
								</tr>
								<tr>
									<td></td>
									<th>Tổng tiền sau KM</th>
									<td class="text-center"></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="total-cell">{{ totalMoneyHasSale.toLocaleString() }}</td>
								</tr>
								<tr>
									<td></td>
									<th>Giảm giá</th>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="total-cell">
										<input type="text" v-model="infoOrder.discount" @input="fixPrice($event, infoOrder, 'discount')">
									</td>
								</tr>
								<tr>
									<td></td>
									<th>Vận chuyển</th>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="total-cell">
										<input type="text" v-model="infoOrder.shipFee" @input="fixPrice($event, infoOrder, 'shipFee')">
									</td>
								</tr>
								<tr class="row-bold">
									<td></td>
									<th class="txt-color-green text-bold">Số tiền cần thanh toán</th>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="total-cell">{{ parseFloat(totalAmount).toLocaleString() }}</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="note-underTable-order">
						<div class="inner-note">
							<span>Ghi chú:</span>
							<textarea class="form-control" v-model="infoOrder.note" placeholder="Đây là ghi chú của đơn hàng ..."></textarea>
						</div>
					</div>
					<div class="infoTransport-order">
						<h2 class="title-block-info">
								<span class="txt-title-blockInfo">Thông tin vận chuyển</span>
							</h2>
							<div class="inner-infoTransport-order">
							<div class="note-transport">
								<label>Ghi chú: </label>
								<textarea class="form-control" rows="1" placeholder="Ghi chú vận chuyển..." v-model="transport.note"></textarea>
							</div>
							<div class="price-transport">
								<input type="text" class="form-control" v-model="transport.amount" placeholder="Nhập số tiền..." @input="fixPrice($event, transport, 'amount')">
								<label>Số tiền</label>
							</div>
							</div>
					</div>
				</div>
				</div>
			</div>
			<div v-if="isCreating" class="modal-footer modal-footer-order text-center">
				<span class="processing-icon" v-show="isCreating">
					<i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
					<span class="sr-only">Loading...</span>
	        	</span>
			</div>
			<div class="modal-footer modal-footer-order" v-else>
				<button class="btn btn-confirm-order" @click="createNewOrder()">Tạo đơn</button>
				<button class="btn-dismiss-order" data-dismiss="modal" @click="resetCreateOrder()">Hủy đơn</button>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">

	import {formatPrice} from '../../helpers/numeralfix'
	import {valuePrice} from '../../helpers/numeralfix'
	import {numeral} from '../../helpers/numeralfix'
	import {functionHelper} from '../../helpers/myfunction'
	import {momentLocale} from "../../helpers/momentfix"
	import {get} from "../../helpers/send"
	import {post} from "../../helpers/send"
	import SlimSelect from 'slim-select'
	import SearchProduct from '../../views/SearchProduct.vue'
	import SearchAddress from '../../views/SearchAddress.vue'
	import { EventBus } from '../../helpers/bus'

	export default {
		components: {
			'search-product': SearchProduct,
			'search-address': SearchAddress
		},
		data() {
			return {
				provinceActive: null,
				districtActive: null,
				wardActive: null,
				infoCus: {
					name: null,
					phone: null,
					email: null,
					address: null
				},
				infoOrder: {
					totalValue: 0,
					valueHasSale: 0,
					totalAmount: 0,
					discount: 0,
					shipFee: 0,
					note: null,
					nameReceive: null,
					phoneReceive: null,
					emailReceive: null,
					addressReceive: null
				},
				products: [],
				payments: [],
				transport: {
					note: null,
					amount: 0
				},
				isProcessing: false,
				isCreating: false,
				timeOutSearchCus: null,
				customersSuggest: [],
				notFillInfoCus: false
			}
		},
		computed: {
			totalMoney() {
				let total = 0;
				for (var i = 0; i < this.products.length; i++) {
					total += this.products[i].price * this.products[i].quantity;
				}
				return total;
			},
			totalMoneyHasSale() {
				let total = 0;
				for (var i = 0; i < this.products.length; i++) {
					total += this.products[i].priceSale * this.products[i].quantity;
				}
				return total;
			},
			totalAmount() {
				this.transport.amount = this.totalPay;
				return this.totalMoneyHasSale + valuePrice(this.infoOrder.shipFee) - valuePrice(this.infoOrder.discount);
			},
			totalPay() {
				let pay = 0;
				for (var i = 0; i < this.payments.length; i++) {
					pay += valuePrice(this.payments[i].amount);
				}
				return pay;
			},
			totalDebt() {
				return this.totalAmount - this.totalPay;
			}
		},
		mounted() {
			EventBus.$on('choose-product', (product) => {
				let check = functionHelper.checkIdInArray(product.id, this.products);
				if (!check) {
					let priceSale = product.hasSale ? product.prod_price - (product.prod_price * product.perc_disc/100) : product.prod_price;
					let prod = {
						name: product.prod_name,
						campId: product.camp_id,
						thumb: product.prod_thumb,
						quantity: 1,
						price: product.prod_price,
						priceSale: priceSale,
						percDisc: product.perc_disc,
						hasSale: product.hasSale,
						id: product.id,
						properties: product.properties
					}
					this.products.push(prod);
				}
			})
			EventBus.$on('address-pick', (ad) => {
				this.infoOrder.provinceId = ad.province_id;
				this.infoOrder.districtId = ad.district_id;
				this.infoOrder.wardId = ad.ward_id;
			})
		},
		methods: {
			fixPrice(e, o, prop) {
			    e.target.value = numeral(e.target.value).format('0,0');
			    this.$set(o, prop, e.target.value);
		  	},
			addRowPay() {
				let newPay = {
					amount: 0,
				}
				this.payments.push(newPay);
			},
			processForCreateOrder() {
				this.infoOrder.discount = valuePrice(this.infoOrder.discount);
				this.infoOrder.shipFee = valuePrice(this.infoOrder.shipFee);
				this.infoOrder.totalValue = this.totalMoney;
				this.infoOrder.valueHasSale = this.totalMoneyHasSale;
				this.infoOrder.totalAmount = this.totalAmount;
				this.infoOrder.totalPay = this.totalPay;
				this.transport.amount = valuePrice(this.transport.amount);
				for (var i = 0; i < this.payments.length; i++) {
					this.payments[i].amount = valuePrice(this.payments[i].amount);
				}
				let form = {
					infoCus: this.infoCus,
					infoOrder: this.infoOrder,
					products: this.products,
					payments: this.payments,
					transport: this.transport
				}
				return form;
			},
			createNewOrder() {
				this.isCreating = true;
				let form = this.processForCreateOrder();
				post('api/orders/create', form)
				.then((res) => {
					if (res.data.id) {
						$.notify('Tạo đơn hàng thành công', 'success');
						$("#new-order-modal").modal('hide');
						EventBus.$emit('list-orders');
						EventBus.$emit('get-count-order-new');
						this.resetCreateOrder();
					}
					this.isCreating = false;
				})
				.catch((err) => {
					let status = err.response.status;
					if (status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
					} else 
					if (status == 302) {
						$.notify(err.response.data.message, 'error');
					}
					this.isCreating = false;
				})
			},
			removeRowPay(index) {
				this.payments.splice(index, 1);
			},
			resetCreateOrder() {
				this.infoCus.name = null;
				this.infoCus.phone = null;
				this.infoCus.email = null;
				this.infoCus.address = null;
				this.infoCus.id = '';
				this.customersSuggest = [];
				this.notFillInfoCus = false;
				this.infoOrder.totalValue = 0;
				this.infoOrder.valueHasSale = 0;
				this.infoOrder.totalAmount = 0;
				this.infoOrder.discount = 0;
				this.infoOrder.shipFee = 0;
				this.infoOrder.note = null;
				this.infoOrder.nameReceive = null;
				this.infoOrder.phoneReceive = null;
				this.infoOrder.emailReceive = null;
				this.infoOrder.addressReceive = null;
				this.products = [];
				this.payments = [];
				this.transport.note = null;
				this.transport.amount = 0;
			},
			removeProduct(index) {
				this.products.splice(index, 1);
			},
			searchCustomer: function() {
				clearTimeout(this.timeOutSearchCus);
				this.timeOutSearchCus = setTimeout(() => {
					if (this.infoCus.phone) {
						get('api/customers/search?phone='+this.infoCus.phone)
						.then((res) => {
							this.customersSuggest = res.data;
						})
					} else {
						this.removeCus();
					}
				}, 500);
			},
			takeInfoCus(customer) {
				this.infoCus.name = customer.name_cus;
				this.infoCus.phone = customer.phone_cus;
				this.infoCus.email = customer.email_cus;
				this.infoCus.address = customer.address_cus;
				this.infoCus.id = customer.id;
				this.customersSuggest = [];
				this.notFillInfoCus = true;
			},
			removeCus() {
				this.infoCus.name = '';
				this.infoCus.phone = '';
				this.infoCus.email = '';
				this.infoCus.address = '';
				this.infoCus.id = '';
				this.customersSuggest = [];
				this.notFillInfoCus = false;
			}
		},
		beforeDestroy () {
			EventBus.$off('choose-product')
			EventBus.$off('address-pick')
		}
	}
</script>
<style type="text/css">
	.menu-cus-suggest {
		display: block;
		max-height: 200px;
		overflow-y: auto;
		overflow-x: hidden;
	}
	.remove-cus {
	    position: absolute;
	    top: 5px;
	    right: 5px;
	    cursor: pointer;
	}
</style>