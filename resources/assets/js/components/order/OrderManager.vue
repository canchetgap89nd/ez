<template>
	<div>
		<div class="panel-heading panel-head-hasTab">
			<div class="tool-head">
				<div class="cont-radio-head">
					<div class="item-radio">
						<label class="control-custom-radio">
							Ngày tạo
							<input type="radio" v-model="checkDate" value="CREATE">
							<div class="radio-custom">
								<div class="inner-radio-cus" v-show="checkDate == 'CREATE'"></div>
							</div>
						</label>
					</div>
					<div class="item-radio">
						<label class="control-custom-radio">
							Ngày cập nhật
							<input type="radio" v-model="checkDate" value="UPDATE">
							<div class="radio-custom">
								<div class="inner-radio-cus" v-show="checkDate == 'UPDATE'"></div>
							</div>
						</label>
					</div>
				</div>
				<div class="date-pick-cont item-control-tool">
					<input type="text" id="pick-date-order" class="form-control form-control-cus picker-cus">
					<i class="glyphicon glyphicon-calendar icon-pickdate"></i>
				</div>
				<div class="status-order-cont item-control-tool">
					<select class="form-control form-control-cus picker-cus select-cus" title="Trạng thái đơn" v-model="statusOrder">
						<option value="">Trạng thái</option>
						<option value="NEW">Mới</option>
						<option value="CONFIRM">Chờ giao hàng</option>
						<option value="SENDING">Đang giao hàng</option>
						<option value="SENT">Đã giao hàng</option>
						<option value="REFUNDING">Hoàn đơn</option>
						<option value="REFUNDED">Đã hoàn đơn</option>
						<option value="CANCELED">Hủy</option>
					</select>
					<i class="fa fa-caret-down icon-dropdown-sel"></i>
				</div>
				<div class="staff-cont item-control-tool">
					<select class="form-control form-control-cus picker-cus select-cus" v-model="staff">
						<option value="">Nhân viên</option>
						<option :value="user.id">{{ user.name }}</option>
						<option v-for="mem in mems" :value="mem.id">{{ mem.name }}</option>
					</select>
					<i class="fa fa-caret-down icon-dropdown-sel"></i>
				</div>
				<div class="search-cont item-control-tool">
					<input type="text" placeholder="Nhập mã đơn" v-model.lazy="code" v-debounce="500" class="form-control form-control-cus control-group-cus">
					<span class="btn-search-group" @click="getListOrders()">
						<i class="fa fa-search"></i>
					</span>
				</div>
				<div class="add-order-cont item-control-tool pull-right" v-if="role.name == 'ADMINSTRATOR' || role.name == 'SALER' || role.name == 'MANAGER'">
					<button class="btn btn-add-order form-control-cus" @click="getCreateOrder()">Thêm đơn hàng</button>
				</div>
			</div>
		</div>
		<circle3-load v-if="isLoading"></circle3-load>
		<div v-else class="panel-body">
			<div v-if="orders.data.length > 0">
				<table class="table table-hover table-head-black">
					<thead>
						<tr>
							<th class="cs-ellipsis-clm">Mã đơn</th>
							<th class="cs-ellipsis-clm">Nhân viên</th>
							<th class="cs-ellipsis-clm text-right">Giá trị</th>
							<th class="cs-ellipsis-clm">Ngày tạo</th>
							<th class="cs-ellipsis-clm">Ngày cập nhật</th>
							<th class="cs-ellipsis-clm text-center">Trạng thái đơn</th>
							<th class="cs-ellipsis-clm text-right">Thanh toán</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(order, index) in orders.data">
							<td class="cs-ellipsis-clm">
								<span class="detail-order" @click="getEditOrder(order.id)">
									<span class="file-icon-tb">
										<i class="fa fa-file"></i>
									</span>
									<span class="order-code">{{ order.order_code }}</span>
								</span>
							</td>
							<td class="cs-ellipsis-clm">
								{{ order.name }}
							</td>
							<td class="cs-ellipsis-clm text-right">
								{{ parseFloat(order.total_amount).toLocaleString() }}
							</td>
							<td class="cs-ellipsis-clm">{{ order.created_at }}</td>
							<td class="cs-ellipsis-clm">{{ order.updated_at }}</td>
							<td class="cs-ellipsis-clm text-center">
								<select class="form-control select-cus-order" @change="quickChangeStatus(index)" :style="'color: '+order.color" v-model="order.status_order" :disabled="role.name == 'STORAGER'">
									<option v-show="order.status_order == 'NEW'" style="color: #000;" value="NEW">Đơn mới</option>
									<option v-show="order.status_order == 'NEW'" style="color: #9a9a9a;" value="CONFIRM">Chờ giao hàng</option>
									<option v-show="order.status_order == 'NEW' || order.status_order == 'CONFIRM'" style="color: #f7b26c;" value="SENDING">Đang giao</option>
									<option v-show="order.status_order == 'NEW' || order.status_order == 'CONFIRM' || order.status_order == 'SENDING'" style="color: #00b140;" value="SENT">Đã giao hàng</option>
									<option v-show="order.status_order == 'SENT' || order.status_order == 'SENDING'" style="color: #c6a910;" value="REFUNDING">Khách trả lại</option>
									<option v-show="order.status_order == 'SENDING' || order.status_order == 'SENT' || order.status_order == 'REFUNDING'" style="color: #996b58;" value="REFUNDED">Đã nhận lại</option>
									<option v-show="order.status_order == 'NEW' || order.status_order == 'CONFIRM'" style="color: #ab0202;" value="CANCELED">Hủy</option>
								</select>
							</td>
							<td class="cs-ellipsis-clm text-right">{{ parseFloat(order.total_pay).toLocaleString() }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div v-else class="text-center loading-container-e">
				Không có đơn hàng nào. Hãy Click vào "Thêm đơn hàng" hoặc tạo đơn hàng ở mục Hội thoại để thêm đơn hàng mới
			</div>
			<div class="under-table">
			    <div class="count-total">
			        Tổng {{ orders.total }} đơn hàng
			    </div>
			    <paginate
				  	:page-count="orders.last_page"
				  	:click-handler="pickPage"
				  	:initial-page="orders.current_page - 1"
			  		:prev-text="'Prev'"
			  		:next-text="'Next'"
			  		:container-class="'paginate-cont'"
			  		:page-class="'page-item'"
			  		:prev-link-class="'per-page previous'"
			  		:next-link-class="'per-page next'"
			  		:prev-class="'per-page previous'"
			  		:next-class="'per-page next'" v-if="orders.last_page > 1">

			  		<span slot="prevContent">
			  			<i class="fa fa-arrow-left"></i>
			  		</span>
			  		<span slot="nextContent">
			  			<i class="fa fa-arrow-right"></i>
			  		</span>
				</paginate>
			</div>
		</div>

		<!-- Modal -->

	  	<!-- modal detail order -->
	  	<div class="modal fade" id="detail-order-modal" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
	  		<div class="modal-dialog modal-lg">
	  			
	  			<!-- modal content -->
	  			<edit-order :user="user" :role="role"></edit-order>

	  		</div>
	  	</div>
	  	<!-- end modal detail order -->

		<!-- modal add new order -->
	  	<div class="modal fade" id="new-order-modal" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
	  		<div class="modal-dialog modal-lg">
	  			
	  			<!-- modal content -->
	  			<add-order></add-order>

	  		</div>
	  	</div>
	  	<!-- end add new order -->

	  	<!-- Modal remove payment -->
		<div class="modal fade" id="quick-remove-payment" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
		   	<div class="modal-dialog">
		      	<!-- Modal content-->
		      	<div class="modal-content">
		         	<div class="modal-body">
		            	<p>Khách hàng đã thanh toán tổng cộng {{ parseFloat(infoOrder.total_pay).toLocaleString() }} VNĐ. Bạn có muốn hủy thanh toán này? (Vui lòng điền số tiền hoàn lại cho khách)</p>
		            	<div class="form-group">
		            		<input type="text" class="form-control" v-model="form.amount_dis" placeholder="Nhập vào số tiền hủy" @input="fixPrice($event, form, 'amount_dis')">
		            		<p class="help-block">Số tiền này sẽ bị trừ vào doanh thu</p>
		            	</div>
		         	</div>
		         	<div class="modal-footer">
		         		<span class="processing-icon" v-show="isUpdating">
							<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
							<span class="sr-only">Loading...</span>
			        	</span>
		            	<button type="button" :disabled="isUpdating" class="btn btn-danger" @click="destroyOrder()">Hủy đơn</button>
		            	<button type="button" class="btn btn-default" @click="cancelDestroy()">Bỏ qua</button>
		         	</div>
		      	</div>
		   	</div>
		</div>

	  	<!-- End modal -->

	</div>
</template>
<script type="text/javascript">
	
	import EditOrder from './EditOrder.vue'
	import AddOrder from './AddOrder.vue'
	import { EventBus } from '../../helpers/bus'
	import {functionHelper} from '../../helpers/myfunction'
	import {momentLocale} from "../../helpers/momentfix"
	import {get} from "../../helpers/send"
	import {post} from "../../helpers/send"
	import {formatPrice} from '../../helpers/numeralfix'
	import {valuePrice} from '../../helpers/numeralfix'
	import {numeral} from '../../helpers/numeralfix'
	import calendarOption from '../../helpers/calendarOption'
	import Circle3 from '../loading/Circle3.vue'
	import Paginate from 'vuejs-paginate'
	import debounce from '../../helpers/directive'

	export default {
		components: {
			'edit-order': EditOrder,
			'add-order': AddOrder,
			'circle3-load': Circle3,
			'paginate': Paginate
		},
		computed: {
			role() {
				return this.$store.state.role;
			},
			user() {
				return this.$store.state.user;
			},
			mems() {
				return this.$store.state.mems;
			}
		},
		data() {
			return {
				orders: {
					data: [],
					pages: []
				},
				infoOrder: {
					total_pay: 0
				},
				form: {
					status: null,
					amount_dis: 0
				},
				isLoading: false,
				isUpdating: false,
				checkDate: 'CREATE',
				statusOrder: '',
				code: '',
				timeFrom: '',
				timeTo: '',
				staff: ''
			}
		},
		directives: {debounce},
		created() {
			this.getListOrders();
		},
		watch: {
			checkDate() {
				this.getListOrders();
			},
			statusOrder() {
				this.getListOrders();
			},
			staff() {
				this.getListOrders();
			},
			code() {
				this.getListOrders();
			}
		},
		mounted() {
			EventBus.$on('list-orders', () => {
				this.getListOrders();
			})
			EventBus.$on('close-edit-order', () => {
				this.closeEditOrder();
			})
			EventBus.$on('close-add-order', () => {
				this.closeAddOrder();
			})
		},
		updated() {
			$('#pick-date-order').daterangepicker(
			{
			    "locale": calendarOption,
				"timePicker": true,
				"timePicker24Hour": true,
			 	"timePickerSeconds": true,
			    "opens": "center",
			    "drops": "down",
			    "alwaysShowCalendars": true
			}, 
			(start, end, label) => {
			    this.timeFrom = start.format('YYYY-MM-DD HH:mm:ss');
			    this.timeTo = end.format('YYYY-MM-DD HH:mm:ss');
			    this.getListOrders();
			});
		},
		methods: {
			pickPage(pageNum) {
				let path = this.orders.path + '?page='+ pageNum;
				this.turnPage(path);
			},
			fixPrice(e, o, prop) {
				e.target.value = numeral(e.target.value).format('0,0');
			    this.$set(o, prop, e.target.value);
			},
			getListOrders: function() {
				this.isLoading = true;
				get('api/orders/get'+
					'?checkDate='+this.checkDate+
					'&statusOrder='+this.statusOrder+
					'&code='+this.code+
					'&timeFrom='+this.timeFrom+
					'&timeTo='+this.timeTo+
					'&staff='+this.staff)
				.then((res) => {
					if (res.data.data) {
						for (var i = 0; i < res.data.data.length; i++) {
							res.data.data[i].created_at = momentLocale(res.data.data[i].created_at).format("HH:mm DD-MM-YYYY");
							res.data.data[i].updated_at = momentLocale(res.data.data[i].updated_at).format("HH:mm DD-MM-YYYY");
							switch (res.data.data[i].status_order) {
								case 'NEW':
									res.data.data[i].color = "#000";
									break;
								case 'CONFIRM':
									res.data.data[i].color = "#9a9a9a";
									break;
								case 'SENDING':
									res.data.data[i].color = "#f7b26c";
								 	break;
								case 'SENT':
									res.data.data[i].color = "#00b140";
									break;
								case 'REFUNDING':
									res.data.data[i].color = "#c6a910";
									break;
								case 'REFUNDED':
									res.data.data[i].color = "#996b58";
									break;
								case 'CANCELED':
									res.data.data[i].color = "#ab0202";
									break;
								default:
									res.data.data[i].color = "#000";
									break;

							}
						}
						this.orders = res.data;
						this.orders.pages = functionHelper.makePages(res.data.path, res.data.last_page);
					}
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify("Lỗi tải danh sách đơn hàng", "error");
					this.isLoading = false;
				})
			},
			turnPage(url) {
				this.isLoading = true;
				get(url)
				.then((res) => {
					if (res.data.data) {
						for (var i = 0; i < res.data.data.length; i++) {
							res.data.data[i].created_at = momentLocale(res.data.data[i].created_at).format("HH:mm DD-MM-YYYY");
							res.data.data[i].updated_at = momentLocale(res.data.data[i].updated_at).format("HH:mm DD-MM-YYYY");
							switch (res.data.data[i].status_order) {
								case 'NEW':
									res.data.data[i].color = "#000";
									break;
								case 'CONFIRM':
									res.data.data[i].color = "#9a9a9a";
									break;
								case 'SENDING':
									res.data.data[i].color = "#f7b26c";
								 	break;
								case 'SENT':
									res.data.data[i].color = "#00b140";
									break;
								case 'REFUNDING':
									res.data.data[i].color = "#c6a910";
									break;
								case 'REFUNDED':
									res.data.data[i].color = "#996b58";
									break;
								case 'CANCELED':
									res.data.data[i].color = "#ab0202";
									break;
								default:
									res.data.data[i].color = "#000";
									break;

							}
						}
						this.orders = res.data;
						this.orders.pages = functionHelper.makePages(res.data.path, res.data.last_page);
					}
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify("Lỗi tải danh sách đơn hàng", "error");
					this.isLoading = false;
				})
			},
			getCreateOrder() {
				$("#new-order-modal").modal('show');
			},
			getEditOrder(id) {
				$("#detail-order-modal").modal("show");
				EventBus.$emit('process-edit-order', id);
			},
			closeEditOrder() {
				$("#detail-order-modal").modal("hide");
			},
			closeAddOrder() {
				$("#new-order-modal").modal('hide');
			},
			resetQuickUpdate() {
				this.form.status = null;
				this.form.amount_dis = 0;
			},
			quickUpdate(id) {
				this.isUpdating = true;
				post('api/orders/quick/update/'+id, this.form)
				.then((res) => {
					if (res.data.updated) {
						$.notify("Cập nhật đơn thành công", "success");
					}
					this.resetQuickUpdate();
					this.getListOrders();
					this.isUpdating = false;
					EventBus.$emit('get-count-order-new');
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (var k in errors) {
							$.notify(errors.message, 'error');
						}
					} else if (err.response.status == 302) {
						$.notify(err.response.data.message, 'error');
					}
					this.getListOrders();
					this.isUpdating = false;
				})
			},
			destroyOrder() {
				let id = this.infoOrder.id;
				this.form.status = "CANCELED";
				this.form.amount_dis = valuePrice(this.form.amount_dis);
				$("#quick-remove-payment").modal("hide");
				this.quickUpdate(id);
			},
			quickChangeStatus(index) {
				let id = this.orders.data[index].id;
				let status = this.orders.data[index].status_order;
				if (status === 'CANCELED') {
					this.infoOrder = this.orders.data[index];
					$('#quick-remove-payment').modal('show');
				} else 
				if (status != 'NEW') {
					this.form.status = status;
					this.quickUpdate(id);
				}
			},
			cancelDestroy() {
				$("#quick-remove-payment").modal('hide');
				this.getListOrders();
			}
		},
		beforeDestroy () {
			EventBus.$off('list-orders')
			EventBus.$off('close-edit-order')
			EventBus.$off('close-add-order')
		}
	}
</script>