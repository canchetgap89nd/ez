<template>
	<div>
		<div class="panel-heading panel-head-hasTab">
			<div class="tool-head">
				<div class="sort-cont item-control-tool">
					<span class="btn-sort-table" @click="sortBy == 'asc' ? sortBy = 'desc' : sortBy = 'asc'">
						<i class="fa fa-sort-alpha-asc" v-if="sortBy == 'asc'"></i>
						<i class="fa fa-sort-alpha-desc" v-if="sortBy == 'desc'"></i>
					</span>
				</div>
				<div class="date-pick-cont item-control-tool">
					<input type="text" id="pick_date_transport" class="form-control form-control-cus picker-cus">
					<i class="glyphicon glyphicon-calendar icon-pickdate"></i>
				</div>
				<div class="status-order-cont item-control-tool">
					<select class="form-control form-control-cus picker-cus select-cus" v-model="statusOrder">
						<option value="">Trạng thái</option>
						<option value="CONFIRM">Chờ giao hàng</option>
						<option value="SENDING">Đang giao hàng</option>
						<option value="SENT">Đã giao hàng</option>
					</select>
					<i class="fa fa-caret-down icon-dropdown-sel"></i>
				</div>
				<div class="item-control-tool">
					<select class="form-control form-control-cus picker-cus select-cus" v-model="provinceAc">
						<option value="">Tỉnh/TP</option>
						<option v-for="province in provinces" :value="province.id">{{ province.name }}</option>
					</select>
					<i class="fa fa-caret-down icon-dropdown-sel"></i>
				</div>
				<div class="item-control-tool">
					<select class="form-control form-control-cus picker-cus select-cus" v-model="districtAc">
						<option value="">Quận/huyện</option>
						<option v-for="district in districts" :value="district.id">{{ district.name_district }}</option>
					</select>
					<i class="fa fa-caret-down icon-dropdown-sel"></i>
				</div>
				<div class="item-control-tool">
					<select class="form-control form-control-cus picker-cus select-cus" v-model="wardAc">
						<option value="">Xã/phường</option>
						<option v-for="ward in wards" :value="ward.id">{{ ward.name_ward }}</option>
					</select>
					<i class="fa fa-caret-down icon-dropdown-sel"></i>
				</div>
				<div class="search-cont item-control-tool search-tp1">
					<input type="text" placeholder="Nhập mã đơn" class="form-control form-control-cus control-group-cus" v-model.lazy="code" v-debounce="500">
					<span class="btn-search-group">
						<i class="fa fa-search"></i>
					</span>
				</div>
				<div class="sort-cont item-control-tool action-rightHead-table entity-last" v-if="role.name == 'ADMINSTRATOR'">
					<a title="Tải xuống danh sách giao hàng" target="_blank" href="/transports/export/xlsx" class="download-list icon-action-headTable">
						<i class="fa fa-download"></i>
					</a>
				</div>
			</div>
		</div>
		<circle3-load v-if="isLoading"></circle3-load>
		<div v-else class="panel-body">
			<div v-if="transports.data.length > 0">
				<table class="table table-hover table-head-black">
					<thead>
						<tr>
							<th class="cs-ellipsis-clm">Mã đơn</th>
							<th class="cs-ellipsis-clm" style="min-width: 60px">Thời gian</th>
							<th class="cs-ellipsis-clm text-right">Giá trị</th>
							<th class="cs-ellipsis-clm text-right">Số tiền cần thu</th>
							<th class="cs-ellipsis-clm text-center" style="min-width: 60px">Trạng thái đơn</th>
							<th class="cs-ellipsis-clm">Địa chỉ</th>
							<th class="cs-ellipsis-clm">Phường / xã</th>
							<th class="cs-ellipsis-clm">Quận</th>
							<th class="cs-ellipsis-clm">Tỉnh / Tp</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="transport in transports.data">
							<td class="cs-ellipsis-clm">
								<span class="file-icon-tb">
									<i class="fa fa-file"></i>
								</span>
								{{ transport.order_code }}
							</td>
							<td class="cs-ellipsis-clm" style="min-width: 60px">{{ transport.created_at }}</td>
							<td class="cs-ellipsis-clm text-right">{{ parseFloat(transport.total_amount).toLocaleString() }}</td>
							<td class="cs-ellipsis-clm text-right">{{ parseFloat(transport.amount).toLocaleString() }}</td>
							<td class="cs-ellipsis-clm text-center" style="min-width: 60px">
								<select class="form-control select-cus-order" :disabled="role.name == 'STORAGER'" @change="quickUpdate(transport.order_id, transport.status_order)" :style="'color: '+transport.color" v-model="transport.status_order">
									<option v-show="transport.status_order == 'NEW'" style="color: #9a9a9a;" value="CONFIRM">Chờ giao hàng</option>
									<option v-show="transport.status_order == 'NEW' || transport.status_order == 'CONFIRM'" style="color: #f7b26c;" value="SENDING">Đang giao</option>
									<option v-show="transport.status_order == 'NEW' || transport.status_order == 'CONFIRM' || transport.status_order == 'SENDING'" style="color: #00b140;" value="SENT">Đã giao hàng</option>
								</select>
							</td>
							<td class="cs-ellipsis-clm">{{ transport.ad_receive }}</td>
							<td class="cs-ellipsis-clm">{{ transport.name_ward }}</td>
							<td class="cs-ellipsis-clm">{{ transport.name_district }}</td>
							<td class="cs-ellipsis-clm">{{ transport.name }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div v-else class="text-center loading-container-e">
				Không có đơn hàng nào. Hãy nhấp vào "Thêm đơn hàng" ở tab Quản lý đơn hàng để thêm đơn hàng mới
			</div>
			<div class="under-table">
			    <div class="count-total">
			        Tổng {{ transports.total }} đơn giao hàng
			    </div>
			    <paginate
				  	:page-count="transports.last_page"
				  	:click-handler="pickPage"
				  	:initial-page="transports.current_page - 1"
			  		:prev-text="'Prev'"
			  		:next-text="'Next'"
			  		:container-class="'paginate-cont'"
			  		:page-class="'page-item'"
			  		:prev-link-class="'per-page previous'"
			  		:next-link-class="'per-page next'"
			  		:prev-class="'per-page previous'"
			  		:next-class="'per-page next'" v-if="transports.last_page > 1">

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
</template>
<script type="text/javascript">
	
	import { EventBus } from '../../helpers/bus'
	import calendarOption from '../../helpers/calendarOption'
	import {momentLocale} from "../../helpers/momentfix"
	import {get} from "../../helpers/send"
	import {post} from "../../helpers/send"
	import {functionHelper} from '../../helpers/myfunction'
	import Circle3 from '../loading/Circle3.vue'
	import Paginate from 'vuejs-paginate'
	import debounce from '../../helpers/directive'

	export default {
		components: {
			'circle3-load': Circle3,
			'paginate': Paginate
		},
		computed: {
			role() {
				return this.$store.state.role;
			}
		},
		directives: {debounce},
		data() {
			return {
				isLoading: false,
				transports: {
					data: [],
					pages: []
				},
				provinces: [],
				districts: [],
				wards: [],
				provinceAc: '',
				districtAc: '',
				wardAc: '',
				code: '',
				statusOrder: '',
				timeFrom: '',
				timeTo: '',
				checkDate: 'CREATE',
				sortBy: 'desc'
			}
		},
		watch: {
			provinceAc() {
				this.districtAc = '';
				this.getListTransports();
				if (this.provinceAc) {this.getDistricts()}
			},
			districtAc() {
				this.wardAc = '';
				this.getListTransports();
				if (this.districtAc) {this.getWards()}
			},
			wardAc() {
				this.getListTransports();
			},
			sortBy() {
				this.getListTransports();
			},
			statusOrder() {
				this.getListTransports();
			},
			code() {
				this.getListTransports();
			}
		},
		created() {
			this.getListTransports();
			this.getProvinces();
		},
		mounted() {
			EventBus.$on('list-transport', () => {
				this.getListTransports();
			})
		},
		updated() {
			$('#pick_date_transport').daterangepicker(
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
			    this.getListTransports();
			});
		},
		methods: {
			pickPage(pageNum) {
				let path = this.transports.path + '?page='+ pageNum;
				this.turnPage(path);
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
						this.transports = res.data;
					}
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify("Lỗi tải danh sách giao hàng", "error");
					this.isLoading = false;
				})
			},
			getListTransports: function() {
				this.isLoading = true;
				get('api/transports/get?checkDate='+this.checkDate+'&statusOrder='+this.statusOrder+'&province='+this.provinceAc+'&district='+this.districtAc+'&ward='+this.wardAc+'&code='+this.code+'&timeFrom='+this.timeFrom+'&timeTo='+this.timeTo+'&sortBy='+this.sortBy)
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
						this.transports = res.data;
					}
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify("Lỗi tải danh sách giao hàng", "error");
					this.isLoading = false;
				})
			},
			quickUpdate(id, status) {
				let form = {status: status};
				post('api/orders/quick/update/'+id, form)
				.then((res) => {
					if (res.data.updated) {
						$.notify("Cập nhật đơn thành công", "success");
					}
					this.getListTransports();
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
					this.getListTransports();
				})
			},
			getProvinces() {
				get('api/provinces/get')
				.then((res) => {
					this.provinces = res.data;
				})
				.catch((err) => {
					$.notify('Lỗi tải danh sách tỉnh/thành', 'error');
				})
			},
			getDistricts() {
				get('api/districts/get/'+this.provinceAc)
				.then((res) => {
					this.districts = res.data;
				})
				.catch((err) => {
					$.notify('Lỗi tải danh sách quận/huyện', 'error')
				})
			},
			getWards() {
				get('api/wards/get/'+this.districtAc)
				.then((res) => {
					this.wards = res.data;
				})
				.catch((err) => {
					$.notify("Lỗi tải danh sách xã/phường");
				})
			}
		},
		beforeDestroy () {
			EventBus.$off('list-transport')
		}
	}
</script>