<template>
	<div>
		<div class="inner-cont" v-if="isLoading">
			<circle-load></circle-load>
		</div>
		<div class="inner-cont" v-else>
			<div class="title">
				<span>Cài đặt chung</span>
			</div>
			<div class="clear-fix"></div>

			<div class="left-content">
				<label class="switch">
					<input type="checkbox" v-model="hide_all_cmt">
					<span class="slider round"></span>
				</label>
				<p>Ẩn tất cả comment</p>
				<div class="clear-fix"></div>

				<label class="switch">
					<input type="checkbox" v-model="hide_cmt_has_phone">
					<span class="slider round"></span>
				</label>
				<p>Ẩn comment chứa số điện thoại</p>
				<div class="clear-fix"></div>

				<label class="switch">
					<input type="checkbox" v-model="hide_cmt_has_key">
					<span class="slider round"></span>
				</label>
				<p>Ẩn comment chứa từ khóa</p>
				<div class="clear-fix"></div>

				<div class="list-box" v-show="hide_cmt_has_key">
					<div class="form-group">
						<v-input-tag :read-only="!hide_cmt_has_key" :tags.sync="key_cmt_hide" placeholder="Nhập từ khóa"></v-input-tag>
					</div>
				</div>
				<div class="clear-fix"></div>

				<label class="switch">
					<input type="checkbox" v-model="auto_like">
					<span class="slider round"></span>
				</label>
				<p>Tự động nhấn Like khi khách hàng comment</p>
				<div class="clear-fix"></div>

				<label class="switch">
					<input type="checkbox" v-model="ping_notify">
					<span class="slider round"></span>
				</label>
				<p>Âm thanh thông báo</p>
				<p>
					<a @click="ringNotify()" class="cs-st-link">
						Nghe thử
					</a>
				</p>
				<div class="clear-fix"></div>
				
				<circle-load v-show="isSaving" style="display: inline-block"></circle-load>
				<button type="button" class="btn-cs" v-show="!isSaving && role.name == 'ADMINSTRATOR'" @click="saveSetting()">Lưu cài đặt</button>
			</div>

			<div class="right-content">
				<label class="switch">
					<input type="checkbox" v-model="priority_cmt_has_key">
					<span class="slider round"></span>
				</label>
				<p>Ưu tiên hiển thị nội dung chứa từ khóa lên trước</p>
				<div class="clear-fix"></div>
				<div class="list-box" v-show="priority_cmt_has_key">
					<v-input-tag :read-only="!priority_cmt_has_key" :tags.sync="key_cmt_priority" placeholder="Nhập từ khóa"></v-input-tag>
				</div>

				<div class="clear-fix"></div>

				<label class="switch">
                    <input type="checkbox" v-model="filter_email">
                    <span class="slider round"></span>
                </label>
                <p>Lọc lấy Email</p>
                <div class="clear-fix"></div>

                <label class="switch">
                    <input type="checkbox" v-model="filter_phone">
                    <span class="slider round"></span>
                </label>
                <p>Lọc lấy số điện thoại</p>
                <div class="clear-fix"></div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import InputTag from 'vue-input-tag'
	import { get } from '../../helpers/send'
	import { post } from '../../helpers/send'
	import Circle from '../loading/Circle.vue'

	export default {
		components: {
			'v-input-tag': InputTag,
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
				id: null,
				key_cmt_hide: [],
				key_cmt_priority: [],
				hide_all_cmt: false,
				hide_cmt_has_phone: false,
				hide_cmt_has_key: false,
				auto_like: false,
				ping_notify: false,
				priority_cmt_has_key: false,
				filter_email: false,
				filter_phone: false,
				isSaving: false,
				isLoading: false
			}
		},
		created() {
			this.getSetting();
		},
		methods: {
			getSetting() {
				this.isLoading = true;
				get('../../api/setting/general/data')
				.then((res) => {
					if (res.data.id) {
						this.fillData(res.data);
					}
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải trang', 'error');
					this.isLoading = false;
				})
			},
			saveSetting() {
				this.isSaving = true;
				post('../../api/setting/general/update', this.$data)
				.then((res) => {
					if (res.data.updated) {
						this.fillData(res.data.data);
						$.notify('Lưu thành công', 'success');
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
			fillData(data) {
				this.id = data.id;
				this.key_cmt_hide = data.key_cmt_hide;
				this.hide_all_cmt = data.hide_all_cmt;
				this.hide_cmt_has_key = data.hide_cmt_has_key;
				this.hide_cmt_has_phone = data.hide_cmt_has_phone;
				this.auto_like = data.auto_like;
				this.ping_notify = data.ping_notify;
				this.priority_cmt_has_key = data.priority_cmt_has_key;
				this.key_cmt_priority = data.key_cmt_priority;
				this.filter_email = data.filter_email;
				this.filter_phone = data.filter_phone;
			},
			ringNotify() {
				let sound = document.createElement("audio");
			    sound.src = '/sound/Notification-02.mp3';
			    sound.setAttribute("preload", "auto");
			    sound.setAttribute("controls", "none");
			    sound.style.display = "none";
			    sound.play();
			}
		}
	}
</script>