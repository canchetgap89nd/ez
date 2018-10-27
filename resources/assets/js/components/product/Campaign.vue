<template>
	<div>
		<div class="panel panel-custom no-padding-top">
			<div class="panel-heading">
				<div class="tool-head">
					<div class="sort-cont item-control-tool no-margin-top" v-show="role.name != 'SALER'">
						<a href="#" data-backdrop="static" data-keyboard="false" @click="getAddCampaign()" class="txt-color-green a-btn-head">Thêm khuyến mãi</a>
					</div>
					<div class="sort-cont item-control-tool action-rightHead-table no-margin-top" v-show="role.name == 'ADMINSTRATOR'">
						<a href="/campaigns/export/xlsx" target="_blank">
							<span class="download-list icon-action-headTable">
								<i class="fa fa-download"></i>
							</span>
						</a>
					</div>
					<div class="date-pick-cont item-control-tool pull-right no-margin-top">
						<input type="text" id="pick_date_camp" class="form-control form-control-cus picker-cus">
						<i class="glyphicon glyphicon-calendar icon-pickdate"></i>
					</div>
					<div class="date-pick-cont item-control-tool no-margin-top pull-right">
						<input type="text" id="choose-date-product" placeholder="Nhập tên chiến dịch" v-model.lazy="keyword" v-debounce="500" class="form-control form-control-cus picker-cus">
						<i class="glyphicon glyphicon-search icon-pickdate"></i>
					</div>
				</div>
			</div>
			<circle3-load v-if="isLoading"></circle3-load>
			<div v-else>
				<div class="panel-body no-padding-top" v-if="camps.data.length > 0">
					<table class="table table-striped table-head-black table-row-notBorder">
						<thead>
							<tr>
								<th class="cs-ellipsis-clm">Tên khuyến mại</th>
								<th class="cs-ellipsis-clm text-right">Đã bán được</th>
								<th class="cs-ellipsis-clm text-right">Đã chi cho khuyến mại</th>
								<th class="cs-ellipsis-clm text-center">Tình trạng</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(camp, index) in camps.data" class="item-import" @click="getDetailCamp(camp.id)">
								<td class="cs-ellipsis-clm">{{ camp.camp_name }}</td>
								<td class="cs-ellipsis-clm text-right">{{ camp.count_sell }}</td>
								<td class="cs-ellipsis-clm text-right">{{ formatPrice(camp.spent_money) }}</td>
								<td class="cs-ellipsis-clm text-center">
									<span class="txt-color-green" v-if="camp.status == 'RUNNING'">Đang chạy</span>
									<span class="txt-color-red" v-if="camp.status == 'STOP'">Đã dừng</span>
									<span class="text-default" v-if="camp.status == 'SCHEDULE'">Sẵn sàng</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="text-center" v-else>
					Chưa có chiến dịch khuyến mãi nào.
				</div>
			</div>
			<div class="under-table">
				<div class="count-total">
			        Tổng {{ camps.total }} chiến dịch
			    </div>
			    <paginate
				  	:page-count="camps.last_page"
				  	:click-handler="pickPage"
			  		:prev-text="'Prev'"
			  		:next-text="'Next'"
			  		:container-class="'paginate-cont'"
			  		:page-class="'page-item'"
			  		:prev-link-class="'per-page previous'"
			  		:next-link-class="'per-page next'"
			  		:prev-class="'per-page previous'"
			  		:next-class="'per-page next'" v-if="camps.last_page > 1">

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
	
	import {post} from '../../helpers/send'
	import {get} from '../../helpers/send'
	import calendarOption from '../../helpers/calendarOption'
	import {formatPrice} from '../../helpers/numeralfix'
	import {valuePrice} from '../../helpers/numeralfix'
	import {numeral} from '../../helpers/numeralfix'
	import {momentLocale} from '../../helpers/momentfix'
	import {functionHelper} from '../../helpers/myfunction'
	import { EventBus } from '../../helpers/bus'
	import Circle3 from '../loading/Circle3.vue'
	import Paginate from 'vuejs-paginate'
	import debounce from '../../helpers/directive'

	export default {
		components: {
			'circle3-load': Circle3,
			'paginate': Paginate
		},
		data() {
			return {
				camps: {
					data: [],
					pages: []
				},
				isLoading: false,
				timeFrom: '',
				timeTo: '',
				status: '',
				keyword: ''
			}
		},
		directives: {debounce},
		mounted () {
			EventBus.$on('get-list-campaign', () => {
				this.getListCampaign();
			})
		},
		created() {
			this.getListCampaign();
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
			$('#pick_date_camp').daterangepicker(
			{
			    "locale": calendarOption,
				"timePicker": true,
				"timePicker24Hour": true,
			 	"timePickerSeconds": true,
			    "opens": "left"
			}, 
			(start, end, label) => {
			    this.timeFrom = start.format('YYYY-MM-DD HH:mm:ss');
			    this.timeTo = end.format('YYYY-MM-DD HH:mm:ss');
			    this.getListCampaign();
			});
		},
		watch: {
			status() {
				this.getListCampaign();
			},
			keyword() {
				this.getListCampaign();
			}
		},
		methods: {
			pickPage(pageNum) {
				let path = this.camps.path + '?page='+ pageNum;
				this.turnPage(path);
			},
			formatPrice(price) {
				return formatPrice(price);
			},
			getListCampaign: function() {
				this.isLoading = true;
				get('/api/campaign/list?timeFrom='+this.timeFrom+'&timeTo='+this.timeTo+'&status='+this.status+'&keyword='+this.keyword)
				.then((res) => {
					if (res.data) {
						for (var i = 0; i < res.data.data.length; i++) {
							let status = this.statusOfCamp(res.data.data[i]);
						 	res.data.data[i].status = status;
						}
						this.camps = res.data;
					}
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải trang khuyến mãi', 'error');
					this.isLoading = false;
				});
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
			turnPage(url) {
				get(url)
				.then((res) => {
					for (var i = 0; i < res.data.data.length; i++) {
						let status = this.statusOfCamp(res.data.data[i]);
					 	res.data.data[i].status = status;
					}
					this.camps = res.data;
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải trang!', 'error');
					this.isLoading = false;
				});
			},
			getAddCampaign() {
				EventBus.$emit('get-add-campaign');
			},
			getDetailCamp(id) {
				EventBus.$emit('get-detail-campaign', id);
			}
		},
		beforeDestroy () {
			EventBus.$off('get-list-campaign')
		}
	}
</script>