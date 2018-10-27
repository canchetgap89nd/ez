<template>
	<div>
		<div class="modal-content">
	        <div class="modal-header">
	            <h4 class="modal-title text-center">
	            	<span class="title-modal-cus">Thêm thuộc tính cho sản phẩm</span>
	            </h4>
	        </div>
	        <div class="modal-body">
	        	<div class="form">
	        		<div class="form-group">
	        			<label>Tên thuộc tính</label>
	        			<input type="text" class="form-control" autofocus v-model="newProp.name" placeholder="Nhập tên thuộc tính">
	        		</div>
	        		<div class="form-group">
	        			<label style="display: block">Giá trị</label>
	        			<v-input-tag :tags.sync="newProp.prop_val" placeholder="Nhập giá trị thuộc tính"></v-input-tag>
	        			<p class="help-block">Nhập giá trị của thuộc tính rồi ấn Enter</p>
	        		</div>
	        	</div>
	        </div>
	        <div class="modal-footer">
	        	<span class="processing-icon" v-show="newProp.isCreating">
					<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
					<span class="sr-only">Loading...</span>
	        	</span>
	        	<button class="btn btn-success" @click="createProp()" :disabled="newProp.isCreating">Xác nhận</button>
	        	<button class="btn btn-danger" @click="resetAddNewProp()">Hủy bỏ</button>
	        </div>
	    </div>
	</div>
</template>
<script type="text/javascript">
	
	import { post } from '../../helpers/send'
	import { EventBus } from '../../helpers/bus'
	import InputTag from 'vue-input-tag'

	export default {
		components: {
			'v-input-tag': InputTag,
		},
		data() {
			return {
				newProp: {
					name: null,
					prop_val: [],
					isCreating: false,
				}
			}
		},
		mounted() {
			EventBus.$on('get-add-property', () => {
				this.getAddProp();
			});
		},
		methods: {
			getAddProp() {
				$('#add-new-properties').modal('show');
			},
			createProp() {
				this.newProp.isCreating = true;
				post('/api/product/properties/create', {
					prop_name: this.newProp.name,
					prop_val: this.newProp.prop_val
				})
				.then((res) => {
					if (res.data.created) {
						EventBus.$emit('get-list-property');
						this.resetAddNewProp();
						$("#add-new-properties").modal('hide');
						$.notify('Tạo thuộc tính sản phẩm thành công!', 'success');
						this.$store.commit('pushProp', res.data.prop);
					}
					this.newProp.isCreating = false;
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (var k in errors) {
							$.notify(errors[k], 'error');
						};
						this.newProp.isCreating = false;
					}
				})
			},
			resetAddNewProp() {
				this.newProp.prop_val = [];
				this.newProp.name = null;
				$("#add-new-properties").modal('hide');
			}
		},
		beforeDestroy () {
			EventBus.$off('get-add-property')
		}
	}

</script>