<template>
	<div>
		<div class="inner-cont" v-if="isLoading">
			<circle-load></circle-load>
		</div>
		<div class="inner-cont" v-else>
			<div class="title">
				<span>Trả lời nhanh</span>
            </div>
            <div class="clear-fix"></div>
            <div class="title">
				<p>Chức năng giúp bạn trả lời nhanh tin nhắn, bình luận của khách hàng theo các câu trả lời mà bạn
					đã cài đặt sẵn trên Chốt Sale.</p>
			</div>
			<div class="title-two">
				<label style="color: red">Chú ý:</label>
				<div class="nt-mar">
					<p>Nhập <strong>[FULL_NAME]</strong> để chèn đầy đủ họ tên khách hàng.</p>
					<p>Ví dụ: Chào mừng [FULL_NAME] đã đến với Chốt Sale.</p>
					<p>
						Để sử dụng tags bạn chỉ cần gõ phím #tuviettat rồi tìm câu trả lời phù hợp với câu hỏi của
							khách hàng.
					</p>
				</div>
			</div>
            <div class="clear-fix"></div>

            <div class="form-add" v-show="role.name == 'ADMINSTRATOR' || role.name == 'SALER'">
				<div class="form-add-one">
					<input type="text" class="form-style-one" v-model="ansNew.quick_text" placeholder="Nhập tiêu đề vào đây">
				</div>
				<div class="form-add-two">
					<input type="text" class="form-style-two" v-model="ansNew.answer_text" placeholder="Nhập nội dung vào đây">
				</div>
				<div class="form-add-button">
					<button type="button" v-show="!edit && !isCreating" @click="createAnswer()" class="btn-cs">Thêm</button>
					<button v-show="edit && !isCreating" type="button" @click="updateAnswer()" class="btn-cs">Cập nhật</button>
					<button type="button" class="btn-cs" v-show="edit && !isCreating" style="background-color: rgb(160, 148, 148)" @click="reset()">Hủy</button>
					<circle-load v-show="isCreating" style="margin-top: 18px;"></circle-load>
				</div>

			</div>

			<h3 class="ttTopTb">Danh sách trả lời nhanh</h3>

			<table class="table table-head-black tableNotBorder">
				<thead>
					<tr>
						<th class="text-left line-first">Từ viết tắt</th>
						<th class="text-left">Nội dung</th>
						<th class="text-center" v-show="role.name == 'ADMINSTRATOR' || role.name == 'SALER'">Hành động</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(ans, index) in answers" class="table-style">
						<td class="text-left line-first">{{ ans.quick_text }}</td>
						<td class="text-left">{{ ans.answer_text }}
						</td>
						<td class="text-center" v-show="role.name == 'ADMINSTRATOR' || role.name == 'SALER'">
							<a href="javascript:;" class="ac-item-tb" v-show="ans.user_id > 0" @click="editAnswer(index)" >
								<i class="fa fa-pencil-square-o"></i>
							</a>
							<a href="javascript:;" class="ac-item-tb" v-show="ans.user_id > 0" @click="removeAnswer(index)" ><span class="fa-times-style"><i class="fa fa-times" aria-hidden="true"></i></span></a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import SlideBar from './SlideBar.vue'
	import { get } from '../../helpers/send'
	import { post } from '../../helpers/send'
	import { del } from '../../helpers/send'
	import Circle from '../loading/Circle.vue'

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
				answers: [],
				ansNew: {
					quick_text: null,
					answer_text: null,
					id: null
				},
				edit: false,
				isCreating: false,
				isLoading: false
			}
		},
		created() {
			this.getListQuickReply();
		},
		methods: {
			getListQuickReply() {
				this.isLoading = true;
				get('../../api/setting/quick-reply/get')
				.then((res) => {
					this.answers = res.data;
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải trang', 'error');
					this.isLoading = false;
				})
			},
			createAnswer() {
				this.isCreating = true;

				post('../../api/setting/quick-reply/create', this.ansNew)
				.then((res) => {
					let answer = res.data.answer;
					if (answer) {
						this.answers.push(answer);
						$.notify('Tạo thành công', 'success');
						this.reset();
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
			},
			editAnswer(index) {
				this.edit = true;
				this.ansNew.id = this.answers[index].id;
				this.ansNew.quick_text = this.answers[index].quick_text;
				this.ansNew.answer_text = this.answers[index].answer_text;
			},
			reset() {
				this.ansNew.quick_text = null;
				this.ansNew.answer_text = null;
				this.ansNew.id = null;
				this.edit = false;
			},
			removeAnswer(index) {
				if (confirm('Bạn có chắc muốn xóa câu trả lời mẫu này')) {
					let id = this.answers[index].id;

					del('../../api/setting/quick-reply/destroy/'+id)
					.then((res) => {
						if (res.data.deleted) {
							$.notify('Xóa thành công', 'success');
							this.answers.splice(index, 1);
						}
					})
					.catch((err) => {
						if (err.response.status == 302) {
							$.notify(err.response.data.message, 'error');
						}
					})
				}
			},
			updateAnswer(index) {
				this.isCreating = true;
				let id = this.ansNew.id;
				post('../../api/setting/quick-reply/update/'+id, this.ansNew)
				.then((res) => {
					if (res.data.updated) {
						this.reset();
						this.getListQuickReply();
						$.notify('Cập nhật thành công', 'success');
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
		}
	}
</script>
<style type="text/css" scoped>
	.ttTopTb {
	    color: #000000;
    	font-size: 25px;
	}
	.nt-mar {
		margin-left: 20px;
	}
</style>