<template>
	<div class="inner-board-order">
		<div class="box-info-or">
			<h3 class="title-box-cus">Khách hàng: {{ customerOnChat ? customerOnChat.name_cus : '' }}</h3>
			<div class="cont-info-or form-horizontal">
				<div class="form-group">
					<label for="name-receive" class="title-info control-label col-lg-2">
						<i class="fa fa-user"></i>
					</label>
					<div class="col-lg-10">
						<input type="text" v-model="infoOrder.nameReceive" placeholder="Nhập tên người nhận" class="form-control input-sm">
					</div>
				</div>
				<div class="form-group">
					<label class="title-info control-label col-lg-2">
						<i class="fa fa-phone"></i>
					</label>
					<div class="col-lg-10">
						<input type="text" placeholder="Nhập số điện thoại người nhận" class="form-control input-sm" v-model="infoOrder.phoneReceive">
					</div>
				</div>
				<div class="form-group">
					<label class="title-info control-label col-lg-2">
						<i class="fa fa-map-marker"></i>
					</label>
					<div class="col-lg-10">
						<input type="text" placeholder="Nhập thêm địa chỉ: VD số nhà 12" class="form-control input-sm" id="address-receive" v-model="infoOrder.addressReceive">
					</div>
				</div>
			</div>
			<div class="cont-ad">
			 	<search-address></search-address>
			</div>
		</div>
		<div class="box-product-or">
			<div class="box-search-pr">
			    <search-product></search-product>
			</div>
		    <!-- <a class="btn_qAdd_product" title="Thêm sản phẩm" href="#" @click="quickAddProduct()">
   				<i class="fa fa-plus-circle txt-color-green"></i>
   			</a> -->
			<ul class="list-product">
				<li v-for="(product, index) in productsOrder">
					<div class="product-info">
						<h3 class="name-product" v-text="product.name"></h3>
						<p class="des-product" v-if="product.properties">{{ '(' + product.properties + ')' }}</p>
						<span class="del-product" @click="removeProductOrder(index)">
							<i class="fa fa-close"></i>
						</span>
					</div>
					<span class="entity-x">X</span>
					<div class="product-quantity">
						<input type="number" min="1" class="inp-quantity" v-model="product.quantity">
					</div>
					<div class="price-product">
						{{ (product.priceSale * product.quantity).toLocaleString() }}
						<span class="sale-label" v-if="product.hasSale">sale</span>
					</div>
					<span class="entity-equal">=</span>
				</li>
			</ul>
		</div>
		<div class="box-total-or">
			<div class="total-row">
				<div class="title-total">
					Tổng sau KM
				</div>
				<div class="num-total">
					{{ valueHasSale.toLocaleString() }}
				</div>
			</div>
			<div class="total-row">
				<div class="title-total">
					Giảm giá
				</div>
				<div class="num-total">
					<input type="text" class="input-total" v-model="infoOrder.discount" @input="fixPrice($event, infoOrder, 'discount')">
				</div>
			</div>
			<div class="total-row">
				<div class="title-total">
					Vận chuyển
				</div>
				<div class="num-total">
					<input type="text" class="input-total" v-model="infoOrder.shipFee" @input="fixPrice($event, infoOrder, 'shipFee')">
				</div>
			</div>
		</div>
		<div class="total-quantity">
			<div class="total-sumary">
				<div class="title-total">
					Thành tiền
				</div>
				<div class="num-total">
					{{ totalAmount.toLocaleString() }}
				</div>
			</div>
			<div class="note-order">
				<input type="text" class="inp-note-order" placeholder="Ghi chú đơn hàng" v-model="infoOrder.note">
			</div>
			<div class="action-order text-center" v-if="isCreatingOrder">
				<i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
				<span class="sr-only">Loading...</span>
			</div>
			<div class="action-order" v-else>
				<div class="ac-or-item send-order">
					<a href="javascript:;" class="sen-btn" @click="sendOrder()">Gửi</a>
				</div>
				<div class="ac-or-item create-order dropup">
					<a href="javascript:;" class="create-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tạo đơn</a>
					<ul class="dropdown-menu">
						<li>
							<a href="javascript:;" @click="createOrder()">Tạo đơn mới</a>
						</li>
						<li>
							<a href="javascript:;" @click="confirmOrder()">Xác nhận đơn</a>
						</li>
					</ul>
				</div>
				<div class="ac-or-item re-create-order">
					<a href="javascript:;" class="re-create-btn" @click="resetOrder()">Tạo lại</a>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">

	import { EventBus } from '../../../helpers/bus'
	import { post } from '../../../helpers/send'
	import { get } from '../../../helpers/send'
	import { del } from '../../../helpers/send'
	import { functionHelper } from '../../../helpers/myfunction'
	import moment from 'moment'
	import SlimSelect from 'slim-select'
	import SearchProduct from '../../../views/SearchProduct.vue'
	import SearchAddress from '../../../views/SearchAddress.vue'
	import {formatPrice} from '../../../helpers/numeralfix'
	import {valuePrice} from '../../../helpers/numeralfix'
	import {numeral} from '../../../helpers/numeralfix'
	import {momentLocale} from '../../../helpers/momentfix'
	import {mapGetters, mapActions} from 'vuex'

	export default {
		components: {
			'search-product': SearchProduct,
			'search-address': SearchAddress
		},
		data() {
			return {
				productsOrder: [],
				infoOrder: {
					nameReceive: null,
					phoneReceive: null,
					addressReceive: null,
					totalAmount: 0,
					shipFee: 0,
					discount: 0,
					totalValue: 0,
					valueHasSale: 0,
					note: null
				},
				isCreatingOrder: false
			}
		},
		watch: {
			customerOnChat () {
				this.infoOrder.nameReceive = this.customerOnChat.name_cus ? this.customerOnChat.name_cus : null;
				this.infoOrder.phoneReceive = this.customerOnChat.phone_cus ? this.customerOnChat.phone_cus : null;
			}
		},
		computed: {
			...mapGetters(
				'conversation', ['customerOnChat']
			),
			totalValue() {
				let total = 0;
				for (let i = 0; i < this.productsOrder.length; i++) {
					let item = this.productsOrder[i];
					total += parseFloat(item.quantity) * parseFloat(item.price);
				}
				return total;
			},
			valueHasSale() {
				let total = 0;
				for (let i = 0; i < this.productsOrder.length; i++) {
					let item = this.productsOrder[i];
					total += parseFloat(item.quantity) * parseFloat(item.priceSale);
				}
				return total;
			},
			totalAmount() {
				return this.valueHasSale - valuePrice(this.infoOrder.discount) + valuePrice(this.infoOrder.shipFee);
			}
		},
		mounted() {
			EventBus.$on('choose-product', (product) => {
				let check = functionHelper.checkIdInArray(product.id, this.productsOrder);
				if (!check) {
					let realPrice = product.hasSale ? product.prod_price - (product.prod_price * product.perc_disc/100) : product.prod_price;
					let campId = product.hasSale ? product.camp_id : null;
					let newProd = {
						id: product.id,
						name: product.prod_name,
						price: product.prod_price,
						priceSale: realPrice,
						properties: product.properties,
						hasSale: product.hasSale,
						percDisc: product.perc_disc,
						campId: campId,
						quantity: 1
					}
					this.productsOrder.push(newProd);
				}
			})
			EventBus.$on('address-pick', (ad) => {
				this.infoOrder.provinceId = ad.province_id;
				this.infoOrder.districtId = ad.district_id;
				this.infoOrder.wardId = ad.ward_id;
			})
		},

		methods: {
			...mapActions('conversation', ['setCustomerOnChat']),
			fixPrice(e, o, prop) {
			    e.target.value = numeral(e.target.value).format('0,0');
			    this.$set(o, prop, e.target.value);
		  	},
			resetOrder() {
				this.infoOrder.addressReceive = null; 
				this.productsOrder = [],
				this.infoOrder.totalAmount = 0;
				this.infoOrder.shipFee = 0;
				this.infoOrder.discount = 0;
				this.infoOrder.totalValue = 0;
				this.infoOrder.valueHasSale = 0;
				this.infoOrder.note = '';
			},
			processCreateOrder() {
				this.infoOrder.totalValue = this.totalValue;
				this.infoOrder.valueHasSale = this.valueHasSale;
				this.infoOrder.discount = valuePrice(this.infoOrder.discount);
				this.infoOrder.shipFee = valuePrice(this.infoOrder.shipFee);
				this.infoOrder.totalAmount = this.totalAmount;
				let form = {
					infoOrder: this.infoOrder,
					products: this.productsOrder,
					infoCus: this.customerOnChat,
					fbPageId: this.customerOnChat.fb_page_id
				}
				return form;
			},
			createOrder() {
				this.isCreatingOrder = true;
				let form = this.processCreateOrder();
				post('/api/orders/create/quick', form)
				.then((res) => {
					if (res.data.id) {
						this.updateInfoCustomer(res.data.customer_id)
						$.notify('Tạo đơn hàng thành công', 'success');
						this.resetOrder();
						EventBus.$emit('getListConversations');
					}
					this.isCreatingOrder = false;
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
					}
					this.isCreatingOrder = false;
				})
			},
			confirmOrder() {
				this.isCreatingOrder = true;
				let form = this.processCreateOrder();
				post('/api/orders/create/confirm/quick', form)
				.then((res) => {
					if (res.data.id) {
						this.updateInfoCustomer(res.data.customer_id)
						$.notify('Tạo đơn hàng thành công', 'success');
						this.resetOrder();
						EventBus.$emit('getListConversations');
					}
					this.isCreatingOrder = false;
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
					} else
					if (err.response.status === 500) {
						$.notify(err.response.data.message, 'error');
					}
					this.isCreatingOrder = false;
				})
			},
			removeProductOrder(index) {
				this.productsOrder.splice(index, 1);
			},
			sendOrder() {
				let form = this.processCreateOrder();
				let message =	'Thông tin người nhận hàng: Tên > ' + form.infoOrder.nameReceive + '; Số ĐT > '+ form.infoOrder.phoneReceive +'; Địa chỉ > '+ form.infoOrder.addressReceive +' - Thông tin sản phẩm: ';

				for (var i = 0; i < form.products.length; i++) {
					message += 'Tên SP > '+ form.products[i].name +'; Số lượng > '+ form.products[i].quantity +'; Đơn giá > '+ (form.products[i].priceSale).toLocaleString() +' - ';
				}
				message += 'Tổng tiền sau KM: '+ form.infoOrder.valueHasSale +' - Giảm giá: '+ form.infoOrder.discount +' - Phí vận chuyển: '+ form.infoOrder.shipFee +' - Tổng tiền phải trả: '+ form.infoOrder.totalAmount;
				EventBus.$emit('sendOrderMessage', message);
			},
			updateInfoCustomer (customerId) {
		        get('api/customers/infomation/' + customerId)
	          		.then((res) => {
		            	this.setCustomerOnChat(res.data)
	          		})
	      	}
		}
	}
</script>