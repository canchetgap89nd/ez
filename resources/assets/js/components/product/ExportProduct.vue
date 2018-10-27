<template>
	<div>
		<div class="panel panel-custom no-padding-top">
			<div class="panel-heading">
				<div class="tool-head">
					<div class="sort-cont item-control-tool action-rightHead-table no-margin-top" v-show="role.name == 'ADMINSTRATOR'">
						<a href="/exp-product/export/xlsx" target="_blank">
							<span class="download-list icon-action-headTable">
								<i class="fa fa-download"></i>
							</span>
						</a>
					</div>
					<div class="date-pick-cont item-control-tool pull-right no-margin-top">
						<input type="text" id="pick-date-export" class="form-control form-control-cus picker-cus">
						<i class="glyphicon glyphicon-calendar icon-pickdate"></i>
					</div>
				</div>
			</div>
			<circle3-load v-if="isLoading"></circle3-load>
			<div v-else class="panel-body no-padding-top">
				<table class="table table-striped table-head-black table-row-notBorder">
					<thead>
						<tr>
							<th class="cs-ellipsis-clm">Mã xuất</th>
							<th class="cs-ellipsis-clm" style="min-width: 60px">Thời gian xuất hàng</th>
							<th class="cs-ellipsis-clm">Người xuất hàng</th>
							<th class="cs-ellipsis-clm text-right">Số lượng</th>
							<th class="cs-ellipsis-clm text-right">Tồn kho</th>
							<th class="cs-ellipsis-clm text-right">Tổng tiền</th>
							<th class="cs-ellipsis-clm text-center">Trạng thái</th>
						</tr>
					</thead>
					<tbody>
						<tr class="item-import" v-for="exp in listExport.data" @click="getDetailExport(exp.id)">
							<td class="cs-ellipsis-clm">{{ exp.export_code }}</td>
							<td class="cs-ellipsis-clm" style="min-width: 60px">{{ formatTime(exp.created_at, 'HH:mm DD/MM/YYYY') }}</td>
							<td class="tcs-ellipsis-clm">{{ exp.user_ex }}</td>
							<td class="cs-ellipsis-clm text-right">{{ exp.quantity_ex }}</td>
							<td class="cs-ellipsis-clm text-right">{{ exp.inventory_ex }}</td>
							<td class="cs-ellipsis-clm text-right">{{ formatPrice(exp.amount_ex) }}</td>
							<td class="cs-ellipsis-clm text-center">
								<span class="txt-color-green" v-if="exp.status_ex == 'EXPORTED'">
									Đã xuất
								</span>
								<span class="txt-color-red" v-if="exp.status == 'CANCEL'">
									Đã hủy
								</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="under-table">
				<div class="count-total">
			        Tổng {{ listExport.total }} phiếu xuất
			    </div>
			    <paginate
				  	:page-count="listExport.last_page"
				  	:click-handler="pickPage"
				  	:initial-page="listExport.current_page - 1"
			  		:prev-text="'Prev'"
			  		:next-text="'Next'"
			  		:container-class="'paginate-cont'"
			  		:page-class="'page-item'"
			  		:prev-link-class="'per-page previous'"
			  		:next-link-class="'per-page next'"
			  		:prev-class="'per-page previous'"
			  		:next-class="'per-page next'" v-if="listExport.last_page > 1">

			  		<span slot="prevContent">
			  			<i class="fa fa-arrow-left"></i>
			  		</span>
			  		<span slot="nextContent">
			  			<i class="fa fa-arrow-right"></i>
			  		</span>
				</paginate>
			</div>
		</div>

		<!-- begin modal infomation export  -->
		<div class="modal fade modal-not-row" id="detail-export-modal" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg">

			    <!-- Modal content-->
			    <detail-export></detail-export>

			</div>
		</div>
		<!-- end modal infomation export -->
	</div>
</template>
<script type="text/javascript">
	
	import { EventBus } from '../../helpers/bus'
	import { functionHelper } from '../../helpers/myfunction'
	import {momentLocale} from '../../helpers/momentfix'
	import { get } from '../../helpers/send'
	import { post } from '../../helpers/send'
	import DetailExport from './DetailExport.vue'
	import {formatPrice} from '../../helpers/numeralfix'
	import {valuePrice} from '../../helpers/numeralfix'
	import {numeral} from '../../helpers/numeralfix'
	import Circle3 from '../loading/Circle3.vue'
	import calendarOption from '../../helpers/calendarOption'
	import Paginate from 'vuejs-paginate'
	
	export default {
		components: {
			'detail-export': DetailExport,
			'circle3-load': Circle3
		},
		data() {
			return {
				listExport: {
					pages: [],
					data: []
				},
				isLoading: false,
				timeFrom: '',
				timeTo: ''
			}
		},
		mounted() {
			EventBus.$on('list-ex-product', () => {
				this.getListExport();
			})
		},
		computed: {
			role() {
				return this.$store.state.role;
			},
			user() {
				return this.$store.state.user;
			}
		},
		updated() {
			$('#pick-date-export').daterangepicker(
			{
			    "locale": calendarOption,
				"timePicker": true,
				"timePicker24Hour": true,
			 	"timePickerSeconds": true,
			    "opens": "left",
			    "drops": "down",
			    "alwaysShowCalendars": true
			}, 
			(start, end, label) => {
			    this.timeFrom = start.format('YYYY-MM-DD HH:mm:ss');
			    this.timeTo = end.format('YYYY-MM-DD HH:mm:ss');
			    this.getListExport();
			});
		},
		methods: {
			pickPage(pageNum) {
				let path = this.listExport.path + '?page=' + pageNum;
				this.turnPage(path);
			},
			formatPrice(price) {
				return formatPrice(price);
			},
			formatTime(time, formater) {
				return momentLocale(time).format(formater);
			},
			getListExport() {
				this.isLoading = true;
				get('api/product/export/list?timeFrom='+this.timeFrom+'&timeTo='+this.timeTo)
				.then((res) => {
					if (res.data.data) {
						this.listExport = res.data;
						this.isLoading = false;
					}
				})
				.catch((err) => {
					$.notify("Lỗi tải danh sách xuất kho", "error");
					this.isLoading = false;
				})
			},
			turnPage(url) {
				this.isLoading = true;
				get(url)
				.then((res) => {
					this.isLoading = false;
					if (res.data.data) {
						this.listExport = res.data;
					}
				})
				.catch((err) => {
					$.notify("Lỗi tải trang xuất kho", "error");
					this.isLoading = false;
				})
			},
			getDetailExport(id) {
				EventBus.$emit('get-detail-export', id);
			}	
		},
		destroyed() {
			EventBus.$off('list-ex-product');
		}
	}
</script>