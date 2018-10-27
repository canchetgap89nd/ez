<template>
	<div>
		<div class="panel panel-custom no-padding-top">
			<div class="panel-heading">
				<div class="tool-head">
					<div class="sort-cont item-control-tool no-margin-top">
						<a href="#" @click="getImportProduct()" class="txt-color-green a-btn-head">Nhập hàng</a>
					</div>
					<div class="sort-cont item-control-tool action-rightHead-table no-margin-top" v-show="role.name == 'ADMINSTRATOR'">
						<a href="/imp-product/export/xlsx" target="_blank">
							<span class="download-list icon-action-headTable">
								<i class="fa fa-download"></i>
							</span>
						</a>
					</div>
					<div class="date-pick-cont item-control-tool pull-right no-margin-top">
						<input type="text" id="pick-date-import" class="form-control form-control-cus picker-cus">
						<i class="glyphicon glyphicon-calendar icon-pickdate"></i>
					</div>
				</div>
			</div>
			<circle3-load v-if="isLoading"></circle3-load>
			<div v-else>
				<div class="panel-body no-padding-top">
					<table class="table table-striped table-head-black table-row-notBorder">
						<thead>
							<tr>
								<th class="text-center cs-ellipsis-clm">Mã nhập</th>
								<th class="text-center cs-ellipsis-clm">Nhập lúc</th>
								<th class="text-center cs-ellipsis-clm">Người nhập</th>
								<th class="text-center cs-ellipsis-clm">Nhập thêm</th>
								<th class="text-center cs-ellipsis-clm">Tồn kho</th>
								<th class="text-center cs-ellipsis-clm">Tổng tiền</th>
								<th class="text-center cs-ellipsis-clm">Trạng thái</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(item, index) in listImports.data" @click="getImportEntity(item.id)" data-toggle="modal" data-target="#info-warehouse" data-backdrop="static" data-keyboard="false" class="item-import">
								<td class="text-center cs-ellipsis-clm">{{ item.import_code }}</td>
								<td class="text-center cs-ellipsis-clm">{{ formatTime(item.created_time, 'HH:mm DD-MM-YYYY') }}</td>
								<td class="text-center cs-ellipsis-clm">{{ item.username_import }}</td>
								<td class="text-center cs-ellipsis-clm">{{ item.total_quantity }}</td>
								<td class="text-center cs-ellipsis-clm">{{ item.inventory }}</td>
								<td class="text-center cs-ellipsis-clm">{{ formatPrice(item.total_amount) }}</td>
								<td class="text-center cs-ellipsis-clm">
									<span class="txt-color-green" v-if="item.status == 'IMPORT'">
										Đã nhập
									</span>
									<span class="txt-color-red" v-if="item.status == 'CANCEL'">
										Đã hủy
									</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="under-table">
				<div class="count-total">
			        Tổng {{ listImports.total }} phiếu nhập
			    </div>
				<div class="paginate-cont">
					<paginate
					  	:page-count="listImports.last_page"
					  	:click-handler="pickPage"
					  	:initial-page="listImports.current_page - 1"
				  		:prev-text="'Prev'"
				  		:next-text="'Next'"
				  		:container-class="'paginate-cont'"
				  		:page-class="'page-item'"
				  		:prev-link-class="'per-page previous'"
				  		:next-link-class="'per-page next'"
				  		:prev-class="'per-page previous'"
				  		:next-class="'per-page next'" v-if="listImports.last_page > 1">

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
</template>
<script type="text/javascript">

	import { EventBus } from '../../helpers/bus'
	import { functionHelper } from '../../helpers/myfunction'
	import { get } from '../../helpers/send'
	import { post } from '../../helpers/send'
	import calendarOption from '../../helpers/calendarOption'
	import Circle3 from '../loading/Circle3.vue'
	import {momentLocale} from '../../helpers/momentfix'
	import {formatPrice} from '../../helpers/numeralfix'
	import Paginate from 'vuejs-paginate'
	import debounce from '../../helpers/directive'

	export default {
		components: {
			'circle3-load': Circle3,
			'paginate': Paginate
		},
		data() {
			return {
				listImports: {
					data: []
				},
				isLoading: false,
				timeFrom: '',
				timeTo: ''
			}
		},
		directives: {debounce},
		mounted() {
			EventBus.$on('get-list-import', () => {
				this.getListImport();
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
			$('#pick-date-import').daterangepicker(
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
			    this.getListImport();
			});
		},
		methods: {
			pickPage(pageNum) {
				let path = this.listImports.path + '?page=' + pageNum;
				this.turnPage(path);
			},
			getListImport() {
				this.isLoading = true;
				get('/api/product/import/list?timeFrom='+this.timeFrom+'&timeTo='+this.timeTo)
				.then((res) => {
					if (res.data) {
						this.listImports = res.data;
					}
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải danh sách phiếu nhập hàng');
					this.isLoading = false;
				})
			},
			turnPage(url) {
				this.isLoading = true;
				get(url)
				.then((res) => {
					this.listImports = res.data;
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải trang!', 'error');
					this.isLoading = false;
				});
			},
			formatTime(time, formater) {
				return momentLocale(time).format(formater);
			},
			formatPrice(price) {
				return formatPrice(price);
			},
			getImportProduct() {
				EventBus.$emit('get-add-import');
			},
			getImportEntity(id) {
				EventBus.$emit('get-detail-import', id);
			},
		},
		beforeDestroy () {
			EventBus.$off('get-list-import')
		}
	}
</script>