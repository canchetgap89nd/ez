<template>
	<div>
		<div class="inner-cont" v-if="isLoading">
			<circle-load></circle-load>
		</div>
		<div class="inner-cont" v-else>
			<div class="title">
				<span>Nhãn khách hàng</span>
            </div>
            <div class="clear-fix"></div>
            <div class="title">
				<p>Nhãn giúp bạn theo dõi và tìm cuộc trò chuyện. hãy tạo nhãn riêng của bạn và áp dụng chúng cho mọi người dựa trên nội dung họ đã nhắn.</p>
			</div>
            <div class="pk-area" v-show="role.name == 'ADMINSTRATOR' || role.name == 'SALER'">
            	<div class="box-lab pull-left">
            		<div id="bt-showpick" class="cl-oval" :style="'background-color: '+color.hex" @click="togglePicker()"></div>
            		<div class="content-gr">
            			<input type="text" placeholder="Nhập tên nhóm khách hàng" v-model="groupNew.group_name" class="inp-nag">
            		</div>
            	</div>
            	<div class="ad-bt pull-left">
            		<button type="button" v-if="edit && !isCreating" class="btn-cs-sm" style="background-color: #00b140" @click="updateGroup()">Cập nhật</button>
            		<button type="button" v-if="edit && !isCreating" class="btn-cs-sm" @click="reset()">Hủy</button>
            		<button type="button" v-if="!edit && !isCreating" class="btn-cs-sm" @click="addGroup()" :disabled="isCreating">Thêm</button>
            		<circle-load v-show="isCreating" style="margin-top: 6px;"></circle-load>
            	</div>
            	<div class="picker-board">
            		<swatches-picker ref="colorPicker" v-model="color" v-show="displayPicker"></swatches-picker>
            	</div>
            </div>
            <div class="ls-area">
            	<div class="row">
            		<div class="col-md-5" v-for="(group, index) in groups">
            			<div class="box-lab">
	                		<div id="bt-showpick" @click="editGroup(index)" class="cl-oval" :style="'background-color: '+group.group_color"></div>
	                		<div class="content-gr">
	                			<span>{{ group.group_name }}</span>
	                			<span v-show="(role.name == 'ADMINSTRATOR' || role.name == 'SALER') && group.user_id !== 0" class="ic-del" @click="removeGroup(index)">
	                				<i class="fa fa-close"></i>
	                			</span>
	                		</div>
                		</div>
            		</div>
            	</div>
            </div>
		</div>
	</div>
</template>
<script type="text/javascript">
		
	import SlideBar from './SlideBar.vue'
	import { Swatches } from 'vue-color'
	import { get } from '../../helpers/send'
	import { post } from '../../helpers/send'
	import { del } from '../../helpers/send'
	import Circle from '../loading/Circle.vue'

	export default {
		components: {
			'slide-bar': SlideBar,
			'swatches-picker': Swatches,
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
				color: {
					hex: '#000'
				},
				displayPicker: false,
				groups: [],
				groupNew: {
					group_name: null,
					group_color: null
				},
				edit: false,
				isCreating: false,
				isLoading: false
			}
		},
		created() {
			this.getListGroup();
		},
		methods: {
			getListGroup() {
				this.isLoading = true;
				get('../../api/setting/groups-customer/all')
				.then((res) => {
					this.groups = res.data;
					this.isLoading = false;
				}) 
				.catch((err) => {
					$.notify('Lỗi tải trang', 'error');
					this.isLoading = false;
				})
			},
			setColor(color) {
				this.color = color.hex;
			},
			showPicker() {
				document.addEventListener('click', this.documentClick);
				this.displayPicker = true;
			},
			hidePicker() {
				document.removeEventListener('click', this.documentClick);
				this.displayPicker = false;
			},
			togglePicker() {
				this.displayPicker ? this.hidePicker() : this.showPicker();
			},
			documentClick(e) {
				var el = this.$refs.colorPicker,
					bt = $("#bt-showpick"),
					target = e.target;

				if((el.$el !== target) && (target !== bt[0]) && !el.$el.contains(target)) {
					this.hidePicker()
				}
			},
			removeGroup(index) {
				let id = this.groups[index].id;
				del('../../api/setting/groups-customer/destroy/'+id)
				.then((res) => {
					if (res.data.deleted) {
						$.notify('Xóa nhóm khách hàng thành công', 'success');
						this.groups.splice(index, 1);
					}
				})
				.catch((err) => {
					$.notify('Lỗi xóa nhóm khách hàng', 'error');
				})
			},
			addGroup() {
				this.isCreating = true;

				let group = {
					group_name: this.groupNew.group_name,
					group_color: this.color.hex
				}
				
				post('../../api/setting/groups-customer/create', group)
				.then((res) => {
					let group = res.data.group;
					if (group) {
						this.groups.push(group);
						this.reset();
						$.notify('Tạo nhóm khách hàng thành công', 'success');
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
			editGroup(index) {
				if ((this.groups[index].user_id !== 0) && (this.role.name == 'ADMINSTRATOR' || this.role.name == 'SALER')) {
					this.color.hex = this.groups[index].group_color;
					this.groupNew.group_name = this.groups[index].group_name;
					this.groupNew.id = this.groups[index].id;
					this.groupNew.index = index;
					this.edit = true;
				}
			},
			reset() {
				this.edit = false;
				this.groupNew.group_name = null;
				this.groupNew.group_color = null;
			},
			updateGroup() {
				this.isCreating = true;
				this.groupNew.group_color = this.color.hex;
				let id = this.groupNew.id;
				post('../../api/setting/groups-customer/update/'+id, this.groupNew)
				.then((res) => {
					if (res.data.updated) {
						$.notify('Cập nhật nhóm khách hàng thành công', 'success');
						this.reset();
						this.getListGroup();
					}
					this.isCreating = false;
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
					} else
					if (err.response.status == 302) {
						$.notify(err.response.data.message, 'error');
					}
					this.isCreating = false;
				})
			}
		}
	}
</script>