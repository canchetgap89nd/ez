<template>
	<div>
		<div class="panel panel-custom panel-grid no-padding-top">
			<div class="panel-heading">
				<div class="tool-head">
					<div class="sort-cont item-control-tool no-margin-top">
						<a href="#" @click="getAddCate()" class="txt-color-green a-btn-head">Thêm danh mục</a>
					</div>
					<div class="date-pick-cont item-control-tool no-margin-top pull-right">
						<input type="text" id="choose-date-product" placeholder="Tìm kiếm danh mục" v-model.lazy="keywordCate" v-debounce="500" class="form-control form-control-cus picker-cus">
						<span v-if="isSearching" class="icon-searching">
							<i class="fa fa-spinner fa-pulse fa-fw"></i>
							<span class="sr-only">Loading...</span>
						</span>
						<i v-else class="glyphicon glyphicon-search icon-pickdate"></i>
					</div>
				</div>
			</div>
			<circle3-load v-if="isLoading"></circle3-load>
			<div v-else>
				<div class="panel-body">
					<div class="list-cates" v-if="cates.data.length > 0">
						<div class="row-cates">
							<div class="col-lg-2" v-for="(cate, index) in cates.data" v-if="index <= 5">
								<div class="cate-image-item">
									<div class="thumb-cate">
										<a href="#">
											<img v-if="cate.cate_thumb" :src="cate.cate_thumb" :title="cate.cate_name">
											<img v-else :src="'images/package.png'" :title="cate.cate_name">
										</a>
										<div class="des-cate">
											<div class="inner-des-cate">
												{{ cate.cate_des }}
												<div class="action_cate">
													<i class="fa fa-edit" @click="editCate(cate.id)"></i>
													<i class="fa fa-close" @click="confirmDeleteCate(cate.id)"></i>
												</div>
											</div>
										</div>
									</div>
									<div class="overview-cate">
										<h3>
											<a href="#">{{ cate.cate_name }}</a>
										</h3>
										<div class="icon-number">
											<span class="type-total type-total-t2">
												<i class="fa fa-cube"></i>
												<span class="total-interactive">{{ cate.products_count }}</span>
											</span>
											<span class="type-total type-total-t2">
												<i class="fa fa-shopping-cart"></i>
												<span class="total-interactive">{{ totalOrders(cate.products) }}</span>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row-cates">
							<div class="col-lg-2" v-for="(cate, index) in cates.data" v-if="index > 5 && index <= 11">
								<div class="cate-image-item">
									<div class="thumb-cate">
										<a href="#">
											<img v-if="cate.cate_thumb" :src="cate.cate_thumb" :title="cate.cate_name">
											<img v-else src="images/package.png" :title="cate.cate_name">
										</a>
										<div class="des-cate">
											<div class="inner-des-cate">
												{{ cate.cate_des }}
												<div class="action_cate">
													<i class="fa fa-edit" @click="editCate(cate.id)"></i>
													<i class="fa fa-close" @click="confirmDeleteCate(cate.id)"></i>
												</div>
											</div>
										</div>
									</div>
									<div class="overview-cate">
										<h3>
											<a href="#">{{ cate.cate_name }}</a>
										</h3>
										<div class="icon-number">
											<span class="type-total type-total-t2">
												<i class="fa fa-cube"></i>
												<span class="total-interactive">{{ cate.products_count }}</span>
											</span>
											<span class="type-total type-total-t2">
												<i class="fa fa-shopping-cart"></i>
												<span class="total-interactive">{{ totalOrders(cate.products) }}</span>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="text-center" v-else>
						Danh mục trống. Hãy nhấp vào "Thêm danh mục" để tạo danh mục mới
					</div>
					<div class="under-table">
					    <div class="count-total">
					        Tổng {{ cates.total }} danh mục
					    </div>
					    <paginate
						  	:page-count="cates.last_page"
						  	:click-handler="pickPage"
						  	:initial-page="cates.current_page - 1"
					  		:prev-text="'Prev'"
					  		:next-text="'Next'"
					  		:container-class="'paginate-cont'"
					  		:page-class="'page-item'"
					  		:prev-link-class="'per-page previous'"
					  		:next-link-class="'per-page next'"
					  		:prev-class="'per-page previous'"
					  		:next-class="'per-page next'" v-show="cates.last_page > 1">

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
		</div>

		<div class="modal fade" id="confirm-del-cate" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
		  	<div class="modal-dialog modal-sm" role="document">
		  		<circle-load v-if="loadingCofirm"></circle-load>
		    	<div class="modal-content" v-else>
		    		<div class="modal-body">
		    			<p>{{ titleConfirm }}</p>
		    		</div>
		    		<div class="modal-footer" v-if="isDoing">
	    				<process></process>
		    		</div>
		    		<div class="modal-footer" v-else>
			      		<button class="btn btn-success" @click="destroyCate()">Đồng ý</button>
			      		<button class="btn btn-default" @click="cancelDestroy()">Không</button>
		    		</div>
		    	</div>
		  	</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import { EventBus } from '../../helpers/bus'
	import { functionHelper } from '../../helpers/myfunction'
	import {momentLocale} from '../../helpers/momentfix'
	import { get } from '../../helpers/send'
	import { post } from '../../helpers/send'
	import { del } from '../../helpers/send'
	import {formatPrice} from '../../helpers/numeralfix'
	import {valuePrice} from '../../helpers/numeralfix'
	import {numeral} from '../../helpers/numeralfix'
	import Circle3 from '../loading/Circle3.vue'
	import Circle from '../loading/Circle.vue'
	import Process from '../loading/Process.vue'
	import Paginate from 'vuejs-paginate'
	import debounce from '../../helpers/directive'

	export default {
		components: {
			'circle3-load': Circle3,
			'circle-load': Circle,
			'process': Process,
			'paginate': Paginate
		},
		data() {
			return {
				cates: {
					data: [],
					pages: [],
					total: 0
				},
				keywordCate: '',
				isSearching: false,
				isLoading: false,
				loadingCofirm: false,
				titleConfirm: '',
				isDoing: false
			}
		},
		directives: {debounce},
		mounted() {
			EventBus.$on('get-list-cate', () => {
				this.getListCate();
			})
		},
		created() {
			this.getListCate();
		},
		watch: {
			keywordCate() {
				this.searchCate();
			}
		},
		methods: {
			pickPage(pageNum) {
				let path = this.cates.path + '?page=' + pageNum;
				this.turnPage(pageNum);
			},
			getListCate() {
				this.isLoading = true;
				get('/api/category/get/list')
				.then((res) => {
					if (res.data) {
						this.cates = res.data;
						this.cates.pages = functionHelper.makePages(res.data.path, res.data.last_page);
						this.isLoading = false;
					}
				})
				.catch((err) => {
					this.isLoading = false;
					$.notify("Lỗi tải danh mục sản phẩm", 'error');
				})
			},
			turnPage(url) {
				this.isLoading = true;
				get(url)
				.then((res) => {
					this.cates = res.data;
					this.cates.pages = functionHelper.makePages(res.data.path, res.data.last_page);
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải trang!', 'error');
					this.isLoading = false;
				});
			},
			totalOrders(products) {
				let total = 0;
				products.forEach((val) => {
					total += val.orders_count;
				})
				return total;
			},
			searchCate: function() {
				this.isSearching = true;
				get('/api/category/get/list?keyword='+this.keywordCate)
				.then((res) => {
					if (res.data) {
						this.cates = res.data;
						this.cates.pages = functionHelper.makePages(res.data.path, res.data.last_page);
						this.isSearching = false;
					}
				})
				.catch((err) => {
					this.isSearching = false;
					$.notify("Lỗi tải danh mục sản phẩm", 'error');
				})
			},
			getAddCate() {
				$("#add-new-category").modal('show');
			},
			confirmDeleteCate(id) {
				this.loadingCofirm = true;
		  		$("#confirm-del-cate").modal('show');
				this.idItemOfAction = id;
				get('/api/category/products/quantity/'+id)
				.then((res) => {
					if (res.data.quantity >= 0) {
						this.titleConfirm = 'Danh mục này đang có ' +res.data.quantity+ ' sản phẩm. Bạn có chắc muốn xóa danh mục này không?';
					} else 
					if (res.data.errors) {
						$.notify(res.data.message, 'error');
					}
					this.loadingCofirm = false;
				})
				.catch((err) => {
					this.loadingCofirm = false;
					$.notify("Lỗi tải thông tin danh mục", "error");
				});
			},
			destroyCate() {
				this.isDoing = true;
				let id = this.idItemOfAction;
				del("/api/category/destroy/"+id)
				.then((res) => {
					if (res.data.deleted) {
						this.getListCate();
						$.notify('Xóa danh mục thành công', 'success');
					} else {
						$.notify(res.data.message, 'error');
					}
					this.cancelDestroy();
				})
				.catch((err) => {
					this.isDoing = false;
					$.notify('Lỗi xóa danh mục', 'error');
				})
			},
		  	cancelDestroy() {
		  		this.idItemOfAction = null;
		  		$("#confirm-del-cate").modal('hide');
		  	},
		  	editCate(id) {
		  		EventBus.$emit('get-edit-cate', id);
		  	}
		},
		beforeDestroy () {
			EventBus.$off('get-list-cate')
		}
	}
</script>