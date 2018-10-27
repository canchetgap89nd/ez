<template>
	<div>
		<div class="modal-content">
	        <div class="modal-header">
	            <h4 class="modal-title text-center">
	            	<span class="title-modal-cus">Thêm danh mục sản phẩm</span>
	            </h4>
	        </div>
	        <div class="modal-body">
	        	<div class="form">
	        		<div class="form-group">
	        			<label>Đặt tên cho danh mục</label>
	        			<input type="text" class="form-control" placeholder="Nhập tên danh mục" v-model="newCate.name">
	        		</div>
	        		<div class="form-group">
	        			<label>Mô tả cho danh mục</label>
	        			<textarea class="form-control" v-model="newCate.des" placeholder="Nhập mô tả cho danh mục..."></textarea>
	        		</div>
	        	</div>
	        </div>
	        <div class="modal-footer" v-if="newCate.isCreating">
	        	<process></process>
	        </div>
	        <div class="modal-footer" v-else>
	        	<button type="button" class="btn btn-success" @click="createCate()">Xác nhận</button>
	        	<button type="button" class="btn btn-danger" data-dismiss="modal">Hủy bỏ</button>
	        </div>
	    </div>

	    <div class="modal fade" id="add-product-in-cate" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
		  	<div class="modal-dialog modal-sm" role="document">
		    	<div class="modal-content">
		    		<div class="modal-body">
		    			<p>{{ titleConfirm }}</p>
		    		</div>
		    		<div class="modal-footer">
			      		<button class="btn btn-success" @click="addProduct()">Đồng ý</button>
			      		<button class="btn btn-default" @click="cancelAdd()">Không</button>
		    		</div>
		    	</div>
		  	</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import { post } from '../../helpers/send'
	import { EventBus } from '../../helpers/bus'
	import Process from '../loading/Process.vue'

	export default {
		components: {
			'process': Process
		},
		data() {
			return {
				newCate: {
					name: null,
					des: null,
					isCreating: false,
					id: null
				},
				loadingConfirm: false,
				titleConfirm: '',
			}
		},
		methods: {
			createCate() {
				this.newCate.isCreating = true;
				post('/api/category/create', {
					cate_name: this.newCate.name,
					cate_des: this.newCate.des
				})
				.then((res) => {
					if (res.data.created) {
						this.$store.commit('pushCate', res.data.data);
						this.newCate.id = res.data.data.id;
						EventBus.$emit('get-list-cate');
						$.notify('Tạo mới danh mục thành công', 'success');
						this.newCate.isCreating = false;
						this.openConfirm('Bạn có muốn tạo sản phẩm cho danh mục vừa tạo hay không?');
					}
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (var k in errors) {
							$.notify(errors[k], 'error');
						};
						this.newCate.isCreating = false;
					}
				})
			},
			resetAddCate() {
				this.newCate.name = null;
				this.newCate.des = null;
				this.newCate.isCreating = false;
				this.newCate.id = null;
			},
			openConfirm(title) {
				this.titleConfirm = title;
				$("#add-product-in-cate").modal('show');
			},
			addProduct() {
				EventBus.$emit('get-list-products');
				EventBus.$emit('get-add-product', this.newCate.id);
				$("#add-product-in-cate").modal('hide');
				$("#add-new-category").modal('hide');
				$('a[href="#product-manager-board"').tab('show');
				this.resetAddCate();
			},
			cancelAdd() {
				$("#add-product-in-cate").modal('hide');
				$("#add-new-category").modal('hide');
				this.resetAddCate();
			}
		}
	}
</script>