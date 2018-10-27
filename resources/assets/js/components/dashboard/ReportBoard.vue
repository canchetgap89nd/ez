<template>
	<div class="modal-content text-center" v-if="isLoading">
		<cirlce-load></cirlce-load>
	</div>
    <div class="modal-content" v-else>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Báo xấu khách hàng {{ customer ? customer.name_cus : '' }}</h4>
        </div>
        <div class="modal-body">
        	<div class="form-group" v-if="reports.length > 0">
        		<label>Danh sách báo xấu của khách hàng này</label>
        	</div>
        	<div class="form-group" v-else>
        		<label>Khách hàng này chưa có lần nào bị báo xấu</label>
        	</div>
        	<table class="table" v-show="reports.length > 0">
        		<thead>
        			<tr>
        				<th class="text-center">STT</th>
        				<th class="text-center">Số ĐT</th>
        				<th class="text-center">Facebook</th>
        				<th class="text-center">Tiêu đề</th>
        				<th class="text-center">Nội dung</th>
        			</tr>
        		</thead>
        		<tbody>
        			<tr v-for="(report, index) in reports">
        				<td class="text-center">{{ index + 1 }}</td>
        				<td class="text-center">{{ report.phone_cus }}</td>
        				<td class="text-center">
        					<a target="_blank" :href="'https://www.facebook.com/'+report.fb_id_cus">Link</a>
        				</td>
        				<td class="text-center">{{ report.title_report }}</td>
        				<td class="text-center">{{ report.des_report }}</td>
        			</tr>
        		</tbody>
        	</table>
        	<div class="form-group">
        		<label>Thêm thông tin báo xấu</label>
        	</div>
            <div class="form-group">
            	<label class="control-label">Tiêu đề</label>
            	<input type="text" v-model="title_report" placeholder="Tiêu đề hành vi báo cáo" class="form-control">
            </div>
            <div class="form-group">
            	<label class="control-label">Nội dung báo cáo</label>
            	<textarea class="form-control" v-model="des_report" placeholder="Nhập nội dung báo cáo"></textarea>
            </div>
            <div class="form-group">
            	<label class="control-label">Số điện thoại</label>
            	<input type="text" v-model="phone_cus" placeholder="Nhập số điện thoại" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal" v-show="!isCreating" @click="reset()">Hủy</button>
            <button type="button" class="btn btn-primary" @click="create()" :disabled="isCreating">Gửi</button>
        </div>
    </div>
	
</template>

<script type="text/javascript">
	
	import { post } from '../../helpers/send'
	import { get } from '../../helpers/send'
	import Circle from '../loading/Circle.vue'
	import { EventBus } from '../../helpers/bus'

	export default {
		components: {
			'cirlce-load': Circle
		},
		props: ['customer'],
		data() {
			return {
				title_report: null,
				des_report: null,
				phone_cus: null,
				isCreating: false,
				reports: [],
				isLoading: false
			}
		},
		mounted() {
			EventBus.$on('get-create-report', () => {
				$("#modal-report").modal('show');
				if (this.customer) {
					this.getReportsCustomer();
				}
			})
		},
		methods: {
			getReportsCustomer() {
				this.isLoading = true;
				get('api/setting/black-list/customer?fb_id_cus='+this.customer.fb_id_cus+'&phone_cus='+this.customer.phone_cus)
				.then((res) => {
					this.reports = res.data.reports;
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải trang báo xấu khách hàng', 'error');
					this.isLoading = false;
				})
			},
			create() {
				if (customer) {
					this.isCreating = true;
					let customer = {
						fb_id_cus: this.customer.fb_id_cus,
						name_cus: this.customer.name_cus,
						phone_cus: this.phone_cus,
						email_cus: this.customer.email_cus,
						title_report: this.title_report,
						des_report: this.des_report
					}
					post('api/setting/black-list/create', customer)
					.then((res) => {
						if (res.data.created) {
							this.reset();
							$.notify('Báo xấu khách hàng thành công', 'success');
							$("#modal-report").modal('hide');
							EventBus.$emit('addReports', res.data.data);
						}
						this.isCreating = false;
					})
					.catch((err) => {
						if (err.response.status == 422) {
							let errors = err.response.data.errors;
							for (let k in errors) {
								$.notify(errors[k], 'error');
							}
						}
						this.isCreating = false;
					})
				}
			},
			reset() {
				this.title_report = null;
				this.des_report = null;
				this.phone_cus = null;
			}
		},
		beforeDestroy () {
			EventBus.$off('get-create-report')
		}
	}
</script>