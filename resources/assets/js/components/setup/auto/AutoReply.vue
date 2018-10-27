<template>
	<div class="autoContainer">
		<div class="innerAuto">
		 	<div class="inner-cont">
				<div class="title">
					<span>Tự động trả lời</span>
				</div>
				<div class="bodySet">
					<div class="left-content">
						<div class="itemSet">
							<div class="radioCsGroup">
								<label class="switch">
									<input type="checkbox" v-model="auto_comment">
									<span class="slider round"></span>
								</label>
								<p>Tự động trả lời bình luận</p>
							</div>
							<div class="list-box" v-show="auto_comment">
								<div class="form-group">
									<textarea class="form-control inpAuto" v-model="content_comment" rows="3" placeholder="Nhập nội dung bình luận"></textarea>
								</div>
								<div class="checkHasTime display-flex">
									<label class="switch">
										<input type="checkbox" v-model="has_time_comment">
										<span class="slider round"></span>
									</label>
									<p>Đặt thời gian</p>
								</div>
								<div class="timeSet display-flex" v-show="has_time_comment">
									<el-time-select
									    placeholder="Bắt đầu"
									    v-model="time_start_comment"
									    :picker-options="{
									      	start: '00:00',
									      	step: '00:30',
									      	end: '24:00'
									    }">
								  	</el-time-select>
								  	<span style="margin: 0 10px;">đến</span>
								  	<el-time-select
									    placeholder="Kết thúc"
									    v-model="time_end_comment"
									    :picker-options="{
									      	start: '00:00',
									      	step: '00:30',
									      	end: '24:00'
									    }">
								  	</el-time-select>
								</div>
							</div>
							
						</div>
					</div>
					<div class="right-content">
						<div class="itemSet">
							<label class="switch">
								<input type="checkbox" v-model="auto_inbox">
								<span class="slider round"></span>
							</label>
							<p>Tự động gửi tin nhắn khi khách hàng bình luận</p>
							<div class="clear-fix"></div>
							<div class="list-box" v-show="auto_inbox">
								<div class="form-group">
									<textarea class="form-control inpAuto" rows="3" v-model="content_inbox" placeholder="Nhập nội dung tin nhắn"></textarea>
								</div>
								<div class="checkHasTime display-flex">
									<label class="switch">
										<input type="checkbox" v-model="has_time_inbox">
										<span class="slider round"></span>
									</label>
									<p>Đặt thời gian</p>
								</div>
								<div class="timeSet display-flex" v-show="has_time_inbox">
									<el-time-select
									    placeholder="Bắt đầu"
									    v-model="time_start_inbox"
									    :picker-options="{
									      	start: '00:00',
									      	step: '00:30',
									      	end: '24:00'
									    }">
								  	</el-time-select>
								  	<span style="margin: 0 10px;">đến</span>
								  	<el-time-select
									    placeholder="Kết thúc"
									    v-model="time_end_inbox"
									    :picker-options="{
									      	start: '00:00',
									      	step: '00:30',
									      	end: '24:00'
									    }">
								  	</el-time-select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="bodySet">
					<div style="margin-top: 30px;">
						<p class="txt-black">
							Lưu ý: Copy [FULL_NAME] và dán vào phần mà bạn muốn hiện thị chính xác TÊN KHÁCH HÀNG.
						</p>
						<p class="txt-black">
							Ví dụ: Chào mừng [FULL_NAME] đã đến với Chốt Sale
						</p>
					</div>
				</div>
				<div class="footSet">
					<circle-load v-show="isSaving" style="display: inline-block"></circle-load>
					<button type="button" class="btn-cs" v-show="!isSaving && role.name == 'ADMINSTRATOR'" @click="saveSetting()">Lưu cài đặt</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	import InputTag from 'vue-input-tag'
	import moment from 'moment'
	import Circle from '../../loading/Circle.vue'
	import {mapGetters} from 'vuex'
	import { get } from '../../../helpers/send'
	import { post } from '../../../helpers/send'
	export default {
		components: {
			'v-input-tag': InputTag,
			'circle-load': Circle
		},
		computed: {
			...mapGetters(['user', 'role'])
		},
		data () {
			return {
				auto_comment: false,
				auto_inbox: false,
				content_comment: '',
				content_inbox: '',
				isSaving: false,
				has_time_comment: false,
				time_start_comment: '',
				time_end_comment: '',
				has_time_inbox: false,
				time_start_inbox: '',
				time_end_inbox: ''
			}
		},
		mounted () {
			this.getSetting()
		},
		methods: {
			getSetting () {
				get('../../../api/setting/auto-reply')
					.then((res) => {
						let response = res.data
						this.auto_comment = response.auto_comment
						this.auto_inbox = response.auto_inbox
						this.content_comment = response.content_comment
						this.content_inbox = response.content_inbox
						this.has_time_comment = response.has_time_comment
						this.has_time_inbox = response.has_time_inbox
						this.time_start_comment = response.time_start_comment ? moment(response.time_start_comment, "HH:mm:ss").format('HH:mm') : ''
						this.time_end_comment = response.time_end_comment ? moment(response.time_end_comment, "HH:mm:ss").format('HH:mm') : ''
						this.time_start_inbox = response.time_start_inbox ? moment(response.time_start_inbox, "HH:mm:ss").format('HH:mm') : ''
						this.time_end_inbox = response.time_end_inbox ? moment(response.time_end_inbox, "HH:mm:ss").format('HH:mm') : ''
					})
					.catch((err) => {
						$.notify('Lỗi tải trang cài đặt', 'error')
					})
			},
			saveSetting () {
				this.isSaving = true
				let payload = {
					auto_comment: this.auto_comment,
					auto_inbox: this.auto_inbox,
					content_comment: this.content_comment,
					content_inbox: this.content_inbox,
					has_time_comment: this.has_time_comment,
					has_time_inbox: this.has_time_inbox,
					time_start_comment: this.time_start_comment,
					time_end_comment: this.time_end_comment,
					time_start_inbox: this.time_start_inbox,
					time_end_inbox: this.time_end_inbox
				}
				post('../../../api/setting/auto-reply', payload)
					.then((res) => {
						$.notify('Cập nhật thành công', 'success')
						this.isSaving = false
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
			}
		}
	}
</script>