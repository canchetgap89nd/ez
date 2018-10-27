<template>
	<div>
		<div class="modal-content" v-if="isLoading">
			<circle-load></circle-load>
		</div>
		<div class="modal-content" v-else>
	        <div class="modal-header">
	            <h4 class="modal-title text-center">
	            	<span class="title-modal-cus">Chỉnh sửa thuộc tính sản phẩm</span>
	            </h4>
	        </div>
	        <div class="modal-body">
	        	<div class="form">
	        		<div class="form-group">
	        			<label>Tên thuộc tính</label>
	        			<input type="text" class="form-control" autofocus v-model="propEdit.name" placeholder="Nhập tên danh mục">
	        		</div>
	        		<div class="form-group">
	        			<label style="display: block">Giá trị</label>
	        			<div class="ls-cur-val">
	        				<span v-for="(item, index) in propEdit.vals" :class="{ 'label label-success': item.status == 'ACTIVE',  'label label-default': item.status == 'NONE' }">
	        					{{ item.value }}
	        					<span class="remove-value" @click="deleteValue(index)" v-if="item.status == 'NONE'" title="Xóa giá trị">x</span>
	        					<span class="remove-value disable" v-else title="Đang có sản phẩm sử dụng giá trị này">x</span>
	        				</span>
	        			</div>
	        			<v-input-tag :tags.sync="vals" placeholder="Nhập thêm giá trị thuộc tính"></v-input-tag>
	        			<p class="help-block">Nhập giá trị của thuộc tính rồi ấn Enter</p>
	        		</div>
	        	</div>
	        </div>
	        <div class="modal-footer" v-if="propEdit.isUpdating">
	        	<process></process>
	        </div>
	        <div class="modal-footer" v-else>
	        	<button class="btn btn-success" @click="updateProperty()" :disabled="propEdit.isUpdating">Cập nhật</button>
	        	<button class="btn btn-danger" @click="resetEditProp()">Hủy bỏ</button>
	        </div>
	    </div>
	</div>
</template>
<script type="text/javascript">
	
	import { post } from '../../helpers/send'
	import { get } from '../../helpers/send'
	import { del } from '../../helpers/send'
	import { EventBus } from '../../helpers/bus'
	import InputTag from 'vue-input-tag'
	import Circle from '../loading/Circle.vue'
	import Process from '../loading/Process.vue'

	export default {
		components: {
			'circle-load': Circle,
			'process': Process,
			'v-input-tag': InputTag,
		},
		data() {
			return {
				propEdit: {
					name: null,
					vals: [],
					isUpdating: false
				},
				vals: [],
				isLoading: false
			}
		},
		mounted() {
			EventBus.$on("get-edit-prop", (id) => {
				this.editProperties(id);
			})
		},
		methods: {
			resetEditProp() {
				this.vals = [];
				this.propEdit.name = null;
				this.propEdit.vals = null;
				$("#edit-property").modal("hide");
			},
			editProperties(id) {
				$("#edit-property").modal('show');
				this.isLoading = true;
				get('/api/product/properties/edit/'+id)
				.then((res) => {
					if (res.data.id) {
						this.propEdit.name = res.data.prop_name;
						this.propEdit.vals = res.data.vals;
						this.propEdit.id = id;
					}
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải thông tin thuộc tính', 'error');
					this.isLoading = false;
				})
			},
			deleteValue(index) {
				let id = this.propEdit.vals[index].id;
				del('/api/product/properties/values/destroy/'+id)
				.then((res) => {
					if (res.data.deleted) {
						this.propEdit.vals.splice(index, 1);
						$.notify("Xóa giá trị thuộc tính thành công", "success");
					} else {
						$.notify(res.data.message, "error");
					}
				})
				.catch((err) => {
					$.notify('Lỗi xóa giá trị thuộc tính', 'error');
				})
			},
			updateProperty() {
				this.propEdit.isUpdating = true;
				let id = this.propEdit.id;
				post('/api/product/properties/update/'+id, {
					prop_name: this.propEdit.name,
					prop_val: this.vals
				})
				.then((res) => {
					if (res.data.updated) {
						EventBus.$emit('get-list-property');
						$.notify('Cập nhật thuộc tính sản phẩm thành công!', 'success');
						$("#edit-property").modal("hide");
					}
					this.propEdit.isUpdating = false;
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (var k in errors) {
							$.notify(errors[k], 'error');
						};
						this.propEdit.isUpdating = false;
					}
				})
			}
		},
		beforeDestroy () {
			EventBus.$off('get-edit-prop')
		}
	}
</script>