<template>
	<div>
		<div class="inner-cont" v-if="isLoading">
			<circle-load></circle-load>
		</div>
		<div class="inner-cont" v-else>
			<div v-if="reports.data.length > 0">
				<div class="title">
					<span>Danh sách đen</span>
				</div>
				<div class="clear-fix"></div>
				<table class="table table-striped table-head-black">
					<thead>
						<tr class="th-style">
							<th class="text-center">Tên</th>
							<th class="text-center">Ngày tháng</th>
							<th class="text-center">Link Facebook</th>
							<th class="text-center">Số điện thoại</th>
							<th class="text-center">Nội dung báo cáo</th>
							<th class="text-center" v-show="role.name == 'ADMINSTRATOR'">Hành động</th>

						</tr>
					</thead>
					<tbody>
						<tr v-for="(report, index) in reports.data">
							<td class="text-center">
								<input v-if="report.edit" type="text" v-model="report.name_cus">
								<span v-else>
									{{ report.name_cus }}
								</span>
							</td>
							<td class="text-center">{{ formatDate(report.created_at, 'DD/MM/YYYY') }}</td>
							<td class="text-center">
								<a v-show="report.fb_id_cus" target="_blank" :href="'https://www.facebook.com/'+ report.fb_id_cus" style="text-decoration: underline">Link</a>
							</td>
							<td class="text-center">
								<input v-if="report.edit" type="text" v-model="report.phone_cus">
								<span v-else>
									{{ report.phone_cus }}
								</span>
							</td>
							<td class="text-center">
								<input type="text" v-if="report.edit" v-model="report.des_report">
								<span v-else>
									{{ report.des_report }}
								</span>
							</td>
							<td class="text-center" v-show="role.name == 'ADMINSTRATOR'">
								<a class="ac-item-tb" v-show="!report.edit" @click="report.edit = true">
									<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
								</a>
								<a class="ac-item-tb text-success" v-show="report.edit" @click="report.edit = false">
									<i class="fa fa-check" aria-hidden="true"></i>
								</a>
								<a class="ac-item-tb" @click="removeReport(index)">
									<span class="fa-times-style">
										<i class="fa fa-times" aria-hidden="true"></i>
									</span>
								</a>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="foot">
                    <div class="under-table">
                        <div class="paginate-cont">
					        <div class="per-page previous" @click="turnPage(reports.prev_page_url)" v-if="reports.prev_page_url">
					            <span>
									<i class="fa fa-arrow-left"></i>
								</span>
					        </div>
					        <span v-for="page in reports.pages" v-show="reports.last_page > 1">
								<div class="page-item active" v-if="page.num == reports.current_page">
									<span class="txt-page">{{ page.num }}</span>
								</div>
								<div class="page-item" v-else @click="turnPage(page.link)">
									<span class="txt-page">{{ page.num }}</span>
								</div>
							</span>
					        <div class="per-page next" v-if="reports.next_page_url" @click="turnPage(reports.next_page_url)">
					            <span>
									<i class="fa fa-arrow-right"></i>
								</span>
					        </div>
					    </div>
                    </div>
                </div>

				<circle-load v-show="isSaving"></circle-load>
                <button type="button" v-show="!isSaving && role.name == 'ADMINSTRATOR'" class="btn-cs" @click="updateReports()">Lưu cài đặt</button>
			</div>
			<div class="col-md-12 text-center" v-else>
				<h4>Không có khách hàng nào trong danh sách đen.</h4>
				<p class="help-block">Danh sách đen được tạo ra để cảnh báo các khách hàng có hành vi gian lận nhằm mục đích bảo vệ người bán hàng. Để thêm khách hàng vào danh sách đen, bạn click chuột vào biểu tượng hình con bọ trong của sổ [Hội thoại] để báo xấu khách hàng</p>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import SlideBar from './SlideBar.vue'
	import { get } from '../../helpers/send'
	import { post } from '../../helpers/send'
	import { del } from '../../helpers/send'
	import Circle from '../loading/Circle.vue'
	import {functionHelper} from '../../helpers/myfunction'
	import {momentLocale} from "../../helpers/momentfix"

	export default {
		components: {
			'slide-bar': SlideBar,
			'circle-load': Circle
		},
		computed: {
			role() {
				return this.$store.state.role;
			},
			user() {
				return this.$store.state.user;
			}
		},
		data() {
			return {
				isLoading: false,
				reports: {
					data: [],
					pages: []
				},
				isSaving: false
			}
		},
		created() {
			this.getBlackList();
		},
		methods: {
			getBlackList() {
				this.isLoading = true;
				get('../../api/setting/black-list/all')
				.then((res) => {
					let reports = res.data.data;
					for (var i = 0; i < reports.length; i++) {
						reports[i].edit = false;
					}
					this.reports = res.data;
					this.reports.pages = functionHelper.makePages(res.data.path, res.data.last_page);
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải danh sách đen', 'error');
					this.isLoading = false;
				})
			},
			turnPage(url) {
				this.isLoading = true;
				get(url)
				.then((res) => {
					if (res.data.data) {
						this.reports = res.data;
						this.reports.pages = functionHelper.makePages(res.data.path, res.data.last_page);
					}
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify("Lỗi tải danh sách đen", "error");
					this.isLoading = false;
				})
			},
			formatDate(date, formater) {
				return momentLocale(date).format(formater);
			},
			updateReports() {
				this.isSaving = true;
				let reports = this.reports.data;
				post("../../api/setting/black-list/update", {
					reports: reports
				})
				.then((res) => {
					if (res.data.updated) {
						this.getBlackList();
						$.notify('Cập nhật thành công', 'success');
					}
					this.isSaving = false;
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
					}
					this.isSaving = false;
				})
			},
			removeReport(index) {
				if (confirm('Bạn có chắc muốn xóa báo xấu này')) {
					this.isSaving = true;
					let id = this.reports.data[index].id;
					del('../../api/setting/black-list/destroy/'+id)
					.then((res) => {
						if (res.data.deleted) {
							this.reports.data.splice(index, 1);
							$.notify('Xóa thành công', 'success');
						}
						this.isSaving = false;
					})
					.catch((err) => {
						$.notify('Lỗi xóa báo xấu', 'error');
						this.isSaving = false;
					})
				}
			}
		}
	}
</script>