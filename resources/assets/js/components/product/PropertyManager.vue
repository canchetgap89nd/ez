<template>
	<div>
		<div class="panel panel-custom no-padding-top">
			<div class="panel-heading">
				<div class="tool-head">
					<div class="sort-cont item-control-tool mar-top-5">
						<a href="#" class="txt-color-green a-btn-head" @click="getAddProperty()">Thêm thuộc tính</a>
					</div>
					<div class="date-pick-cont item-control-tool no-margin-top pull-right">
						<input type="text" id="choose-date-product" v-model.lazy="searchKeyProp" v-debounce="500" placeholder="Tìm kiếm thuộc tính" class="form-control form-control-cus picker-cus">
						<i class="glyphicon glyphicon-search icon-pickdate"></i>
					</div>
				</div>
			</div>
			<circle3-load v-if="isLoading"></circle3-load>
			<div v-else>
				<div class="panel-body no-padding-top" v-if="properties.data.length > 0">
					<table class="table table-condensed table-head-black">
						<thead>
							<tr>
								<th class="text-center cs-ellipsis-clm">Tên thuộc tính</th>
								<th class="text-center cs-ellipsis-clm">Tác vụ</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(prop, index) in properties.data">
								<td class="text-center cs-ellipsis-clm">
									{{ prop.prop_name }}
								</td>
								<td class="text-center cs-ellipsis-clm">
									<span class="ac-row-tb edit-row-table" title="Chỉnh sửa" @click="getEditProperty(prop.id)">
										<i class="fa fa-edit"></i>
									</span>
									<span class="ac-row-tb del-row-table" title="Xóa thuộc tính" @click="deleteProperty(prop.id)">
										<i class="fa fa-close"></i>
									</span>
								</td>
							</tr>
						</tbody>
					</table>
					
				</div>
				<div class="text-center" v-else>
					Chưa có thuộc tính sản phẩm nào. Hãy nhấp vào "Thêm thuộc tính" để tạo thuộc tính mới cho sản phẩm
				</div>
			</div>
			<div class="under-table">
				<paginate
				  	:page-count="properties.last_page"
				  	:click-handler="pickPage"
				  	:initial-page="properties.current_page - 1"
			  		:prev-text="'Prev'"
			  		:next-text="'Next'"
			  		:container-class="'paginate-cont'"
			  		:page-class="'page-item'"
			  		:prev-link-class="'per-page previous'"
			  		:next-link-class="'per-page next'"
			  		:prev-class="'per-page previous'"
			  		:next-class="'per-page next'" v-if="properties.last_page > 1">

			  		<span slot="prevContent">
			  			<i class="fa fa-arrow-left"></i>
			  		</span>
			  		<span slot="nextContent">
			  			<i class="fa fa-arrow-right"></i>
			  		</span>

				</paginate>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import { get } from '../../helpers/send'
	import { post } from '../../helpers/send'
	import { del } from '../../helpers/send'
	import { functionHelper } from '../../helpers/myfunction'
	import { EventBus } from '../../helpers/bus'
	import Circle3 from '../loading/Circle3.vue'
	import Paginate from 'vuejs-paginate'
	import debounce from '../../helpers/directive'

	export default {
		components: {
			'circle3-load': Circle3,
			'paginate': Paginate
		},
		data() {
			return {
				properties: {
					data: [],
					pages: []
				},
				isLoading: false,
				searchKeyProp: ''
			}
		},
		watch: {
			searchKeyProp() {
				this.getListProperties();
			}
		},
		directives: {debounce},
		mounted() {
			EventBus.$on("get-list-property", () => {
				this.getListProperties();
			})
		},
		methods: {
			pickPage(pageNum) {
				let path = this.properties.path + '?page=' + pageNum;
				this.turnPage(path);
			},
			getListProperties() {
				this.isLoading = true;
				get('/api/product/properties?keyword='+this.searchKeyProp)
				.then((res) => {
					this.isLoading = false;
					if (res.data.data) {
						this.properties = res.data;
						this.properties.pages = functionHelper.makePages(res.data.path, res.data.last_page);
					}
				})
				.catch((err) => {
					this.isLoading = false;
					$.notify('Lỗi tải trang thuộc tính sản phẩm', 'error');
				})
			},
			turnPage(url) {
				this.isLoading = true;
				get(url)
				.then((res) => {
					this.properties = res.data;
					this.properties.pages = functionHelper.makePages(res.data.path, res.data.last_page);
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải trang!', 'error');
					this.isLoading = false;
				});
			},
			getAddProperty() {
				EventBus.$emit('get-add-property');
			},
			deleteProperty(id) {
				del('/api/product/properties/destroy/'+id)
				.then((res) => {
					if (res.data.deleted) {
						this.getListProperties();
						$.notify("Xóa thuộc tính sản phẩm thành công", "success");
					} else {
						$.notify(res.data.message, "error");
					}
				})
				.catch((err) => {
					$.notify("Lỗi xóa thuộc tính sản phẩm", "error");

				})
			},
			getEditProperty(id) {
				EventBus.$emit('get-edit-prop', id);
			}
		},
		beforeDestroy () {
			EventBus.$off('get-list-property')
		}	
	}
</script>