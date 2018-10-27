<template>
	<div>
		<div class="container">
			<div class="row">
				<div class="panel panel-custom">
					<div class="panel-heading">
						<div class="tool-head">
							<div class="sort-cont item-control-tool">
								<span class="btn-sort-table icon-action-headTable" title="Sắp xếp theo tên" @click="sortBy == 'asc' ? sortBy = 'desc' : sortBy = 'asc'">
									<i class="fa fa-sort-alpha-asc" v-if="sortBy == 'asc'"></i>
									<i class="fa fa-sort-alpha-desc" v-else></i>
								</span>
							</div>
							<div class="sort-cont item-control-tool">
								<span title="Lọc khách hàng có email" class="btn-message-cus icon-action-headTable" @click="hasMail = !hasMail">
									<i class="fa fa-envelope" v-if="hasMail"></i>
									<i class="fa fa-envelope eas-op" v-else></i>
								</span>
							</div>
							<div class="sort-cont item-control-tool">
								<span title="Lọc khách hàng có số điện thoại" class="btn-phone-cus icon-action-headTable" @click="hasPhone = !hasPhone">
									<i class="fa fa-volume-control-phone" v-if="hasPhone"></i>
									<i class="fa fa-volume-control-phone eas-op" v-else></i>
								</span>
							</div>
							<div class="sort-cont item-control-tool">
								<span title="Lọc khách hàng đã có đơn hàng" class="btn-shopping-cus icon-action-headTable" @click="hasOrder = !hasOrder">
									<i class="fa fa-shopping-cart" v-if="hasOrder"></i>
									<i class="fa fa-shopping-cart eas-op" v-else></i>
								</span>
							</div>
							<div class="date-pick-cont item-control-tool">
								<input title="Lọc khách hàng hoạt động trong khoảng thời gian" type="text" id="pick_date_customer" class="form-control form-control-cus picker-cus">
	    						<i class="glyphicon glyphicon-calendar icon-pickdate"></i>
							</div>
							<div class="item-control-tool">
								<div class="dropdown dropdown-cus">
								    <button style="width: 125px" title="Lọc khách hàng theo nhóm" class="btn btn-default dropdown-toggle btn-type-search" type="button" data-toggle="dropdown">{{ titleGroup ? titleGroup : 'Chọn nhóm' }}
								    <span class="caret"></span></button>
								    <input type="text" placeholder="Tìm kiếm tên khách hàng" class="form-control search-has-type form-control-cus" v-model.lazy="keyword" v-debounce="500">
								    <span class="icon-searchHas-type" @click="getListCustomer()">
										<i class="fa fa-search"></i>
									</span>
								    <ul class="dropdown-menu">
								    	<li @click="pickGroup()"><a href="#">Tất cả</a></li>
								      	<li v-for="group in groups" @click="pickGroup(group)"><a href="#">{{ group.group_name }}</a></li>
								    </ul>
							  	</div>
							</div>
							<div class="sort-cont item-control-tool action-rightHead-table" v-if="role.name == 'ADMINSTRATOR'">
								<a href="/customers/export/xlsx" target="_blank">
									<span title="Tải xuống danh sách khách hàng" class="download-list icon-action-headTable" @click="exportTable()">
										<i class="fa fa-download"></i>
									</span>
								</a>
							</div>
							<div class="sort-cont item-control-tool action-rightHead-table">
								<span title="Thêm khách hàng" class="add-cus icon-action-headTable" @click="getCreateCustomer()">
									<i class="fa fa-user-plus"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<circle3-loading v-if="processing"></circle3-loading>
						<div v-else>
							<table class="table table-hover table-head-black" id="tb-customers-list">
								<thead>
									<tr>
										<th class="text-center" style="width: 50px">
											<div class="md-checkbox">
												<input id="check-all-customer" type="checkbox" value="1" v-model="checkAll">
												<label for="check-all-customer">
													
												</label>
											</div>
										</th>
										<th class="cs-ellipsis-clm">Khách hàng</th>
										<th class="cs-ellipsis-clm">Số ĐT</th>
										<th class="cs-ellipsis-clm">Địa chỉ</th>
										<th class="cs-ellipsis-clm">Email</th>
										<th class="cs-ellipsis-clm text-center">Hoạt động</th>
										<th class="cs-ellipsis-clm">Lần HĐ cuối cùng</th>
										<th class="cs-ellipsis-clm text-right">Đã chi</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="customer in customers.data" v-show="!customer.hide">
										<td class="text-center" style="width: 50px">
											<div class="md-checkbox">
												<input type="checkbox" v-model="entities" :value="customer.id" :id="'cus_'+customer.id">
												<label :for="'cus_'+customer.id"></label>
											</div>
										</td>
										<td class="cs-ellipsis-clm underline-text-btn" :title="customer.name_cus" @click="detailCustomer(customer.id)">{{ customer.name_cus }}</td>
										<td class="cs-ellipsis-clm">{{ customer.phone_cus }}</td>
										<td class="cs-ellipsis-clm">{{ customer.address_cus }}</td>
										<td class="cs-ellipsis-clm">{{ customer.email_cus }}</td>
										<td class="cs-ellipsis-clm text-center">
											{{ customer.comments_count }} <i class="fa fa-comment" title="cuộc hội thoại"></i>
											 - {{ customer.orders_count }} <i class="fa fa-shopping-cart" title="đơn hàng"></i>
										</td>
										<td class="cs-ellipsis-clm">{{ formatTime(customer.updated_at) }}</td>
										<td class="cs-ellipsis-clm text-right">{{ totalPay(customer.payments) }}</td>
									</tr>
								</tbody>
								<tfoot style="background-color: transparent" v-show="entities.length">
									<tr>
										<td class="text-center">
											<span title="Xóa khách hàng đã chọn" class="icon-action-headTable" @click="deleteCustomers()">
												<i class="fa fa-trash"></i>
											</span>
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
						<div class="under-table" v-if="customers.data.length > 0">
							<div class="count-total">
								Tổng {{ customers.total }}
							</div>
							<paginate
							  	:page-count="customers.last_page"
							  	:click-handler="pickPage"
							  	:initial-page="customers.current_page - 1"
						  		:prev-text="'Prev'"
						  		:next-text="'Next'"
						  		:container-class="'paginate-cont'"
						  		:page-class="'page-item'"
						  		:prev-link-class="'per-page previous'"
						  		:next-link-class="'per-page next'"
						  		:prev-class="'per-page previous'"
						  		:next-class="'per-page next'" v-if="customers.last_page > 1">

						  		<span slot="prevContent">
						  			<i class="fa fa-arrow-left"></i>
						  		</span>
						  		<span slot="nextContent">
						  			<i class="fa fa-arrow-right"></i>
						  		</span>
							</paginate>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- modal add customer -->
	  	<div class="modal fade" id="create-customer-modal" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
		    <div class="modal-dialog modal-lg">
		    	
		      	<add-customer></add-customer>
		      
		    </div>
	  	</div>
	  	<!-- end modal add customer -->
		
		<!-- modal detail customer -->
	  	<div class="modal fade" id="detail-customer-modal" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
		    <div class="modal-dialog modal-lg">
		    	
		      	<detail-customer></detail-customer>
		      
		    </div>
	  	</div>
	</div>
</template>
<script type="text/javascript">
	
	import AddCustomer from './AddCustomer.vue'
	import DetailCustomer from './DetailCustomer.vue'
	import Circle3 from '../loading/Circle3.vue'
	import { EventBus } from '../../helpers/bus'
	import {get} from '../../helpers/send'
	import {post} from '../../helpers/send'
	import {del} from '../../helpers/send'
	import {momentLocale} from '../../helpers/momentfix'
	import {formatPrice} from '../../helpers/numeralfix'
	import {functionHelper} from '../../helpers/myfunction'
	import calendarOption from '../../helpers/calendarOption'
	import AWN from "awesome-notifications"
	var notifier = new AWN();
	import Paginate from 'vuejs-paginate'
	import debounce from '../../helpers/directive'
	
	export default {

		components: {
			'add-customer': AddCustomer,
			'circle3-loading': Circle3,
			'detail-customer': DetailCustomer,
			'paginate': Paginate
		},
		directives: {debounce},
		computed: {
			role() {
				return this.$store.state.role;
			},
			groups() {
				return this.$store.state.groups;
			}
		},
		data() {
			return {
				customers: {
					data: [],
					pages: []
				},
				processing: false,
				hasPhone: false,
				hasOrder: false,
				hasMail: false,
				groupId: '',
				sortBy: 'asc',
				groupBy: '',
				titleGroup: '',
				timeFrom: '',
				timeTo: '',
				keyword: '',
				checkAll: 0,
				entities: []
			}
		},
		watch: {
			hasPhone() {
				this.getListCustomer();
			},
			hasMail() {
				this.getListCustomer();
			},
			sortBy() {
				this.getListCustomer();
			},
			keyword() {
				this.getListCustomer();
			},
			hasOrder() {
				this.getListCustomer();
			},
			groupBy() {
				this.getListCustomer();
			},
			checkAll() {
				if (this.checkAll) {
					this.customers.data.forEach((val) => {
						this.entities.push(val.id);
					})
				} else {
					this.entities = [];
				}
			},
		},
		created() {
			this.getListCustomer();
		},
		mounted() {
			EventBus.$on('get-list-customers', () => {
				this.getListCustomer();
			})
		},
		updated() {
			$('#pick_date_customer').daterangepicker(
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
			    this.getListCustomer();
			});
		},
		methods: {
			pickPage(pageNum) {
				let path = this.customers.path + '?page=' + pageNum;
				this.turnPage(path);
			},
			getCreateCustomer() {
				EventBus.$emit('get-add-customer');
			},
			getListCustomer: function() {
				this.processing = true;
				get('api/customers/list'+
					'?sortBy='+this.sortBy+
					'&hasPhone='+this.hasPhone+
					'&hasMail='+this.hasMail+
					'&hasOrder='+this.hasOrder+
					'&keyword='+this.keyword+
					'&groupBy='+this.groupBy+
					'&timeFrom='+this.timeFrom+
					'&timeTo='+this.timeTo)
				.then((res) => {
					if (res.data.data) {
						this.customers = res.data;
					}
					this.processing = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải trang khách hàng', 'error');
					this.processing = false;
				})
			},
			totalPay(payments) {
				var total = 0;
				payments.forEach((val) => {
					if (val.refund) {
						total -= val.amount_pay;
					} else {
						total += val.amount_pay;
					}
				})
				return formatPrice(total);
			},
			formatTime(time) {
				return momentLocale(time).format('DD/MM/YYYY');
			},
			formatPrice(price) {
				return formatPrice(price);
			},
			turnPage(url) {
				this.pickGroup();
				this.hasOrder = false;
				this.processing = true;
				get(url)
				.then((res) => {
					if (res.data.data) {
						this.customers = res.data;
					}
					this.processing = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải trang khách hàng', 'error');
					this.processing = false;
				})
			},
			detailCustomer(id) {
				EventBus.$emit('detail-customer', id);
			},
			pickGroup(group = null) {
				if (group !== null) {
					this.titleGroup = group.group_name;
					this.groupBy = group.id;
				} else {
					this.titleGroup = '';
					this.groupBy = '';
				}
			},
			filterHasOrder() {
				if (this.hasOrder) {
					this.customers.data.forEach((val) => {
						if (!val.orders_count) {val.hide = true};
					})
				} else {
					this.customers.data.forEach((val) => {
						val.hide = false;
					})
				}
			},
			deleteCustomers() {
				notifier.confirm('Bạn có chắc muốn xóa khách hàng đã chọn', () => {
					del('api/customers/destroy', {ids: this.entities})
					.then((res) => {
						if (res.data.success) {
							notifier.success('Xóa thành công');
							this.getListCustomer();
						}
					})
					.catch((err) => {
						notifier.alert('Lỗi xóa');
					})
				})
			}
		},
		beforeDestroy () {
			EventBus.$off('get-list-customers')
		}
	}

</script>
<style type="text/css">
	.eas-op {
		opacity: 0.2;
	}
</style>