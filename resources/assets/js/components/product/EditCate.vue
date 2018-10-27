<template>
	<div>
		<div class="modal-content text-center" v-if="isLoading">
			<circle-load></circle-load>
		</div>
		<div class="modal-content" v-else>
		    <div class="modal-header">
		        <h4 class="modal-title text-center">
		        	<span class="title-modal-cus">Thông tin danh mục</span>
		        </h4>
		    </div>
		    <div class="modal-body">
		    	<div class="form">
		    		<div class="form-group">
		    			<label>Tên danh mục</label>
		    			<input type="text" class="form-control" placeholder="Nhập tên danh mục" v-model="cateEdit.name">
		    		</div>
		    		<div class="form-group">
		    			<label>Mô tả cho danh mục</label>
		    			<textarea class="form-control" v-model="cateEdit.des" placeholder="Nhập mô tả cho danh mục..."></textarea>
		    		</div>
		    	</div>
		    </div>
		    <div class="modal-footer">
		    	<span class="processing-icon" v-show="cateEdit.isUpdating">
					<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
					<span class="sr-only">Loading...</span>
		    	</span>
		    	<button type="button" :disabled="cateEdit.isUpdating" class="btn btn-success" @click="updateCate()">Cập nhật</button>
		    	<button type="button" class="btn btn-danger" data-dismiss="modal">Hủy bỏ</button>
		    </div>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import Circle from '../loading/Circle.vue'
	import { post } from '../../helpers/send'
	import { get } from '../../helpers/send'
	import { EventBus } from '../../helpers/bus'

	export default {
		components: {
			'circle-load': Circle
		},
		data() {
			return {
				cateEdit: {
					id: null,
					name: null,
					des: null,
					isUpdating: false
				},
				isLoading: false
			}
		},
		mounted() {
			EventBus.$on('get-edit-cate', (id) => {
				this.editCate(id);
			})
		},
		methods: {
			editCate(id) {
				$("#edit-cate-modal").modal("show");
				this.isLoading = true;
				get("/api/category/"+id)
				.then((res) => {
					if (res.data.id) {
						this.cateEdit.id = res.data.id;
						this.cateEdit.name = res.data.cate_name;
						this.cateEdit.des = res.data.cate_des;
					}
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify("Lỗi tải thông tin danh mục", 'error');
					this.isLoading = false;
				})
			},
			updateCate() {
				this.cateEdit.isUpdating = true;
				post("/api/category/update/"+this.cateEdit.id, {
					cate_name: this.cateEdit.name,
					cate_des: this.cateEdit.des
				})
				.then((res) => {
					this.cateEdit.isUpdating = false;
					$.notify('Cập nhật thành công', 'success');
					$("#edit-cate-modal").modal("hide");
					EventBus.$emit('get-list-cate');
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
						this.cateEdit.isUpdating = false;
					}
					$.notify('Lỗi cập nhật', 'error');
				})
			},
		},
		beforeDestroy () {
			EventBus.$off('get-edit-cate')
		}
	}
</script>