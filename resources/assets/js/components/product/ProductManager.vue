<template>
	<div>
		<div class="panel panel-custom no-padding-top">
			<div class="panel-heading">
				<div class="tool-head">
					<div class="sort-cont item-control-tool">
						 <div class="dropdown">
					  		<span title="Sắp xếp theo thời gian tạo sản phẩm" class="btn-sort-table icon-action-headTable" @click="sortBy == 'desc' ? sortBy = 'asc' : sortBy = 'desc'">
								<i class="fa fa-sort-alpha-desc" v-if="sortBy == 'desc'"></i>
								<i class="fa fa-sort-alpha-asc" v-if="sortBy == 'asc'"></i>
							</span>
						</div> 
					</div>
					<div class="sort-cont item-control-tool mar-top-5">
						<a href="#" class="txt-color-green a-btn-head" @click="getAddProduct()">Thêm sản phẩm</a>
					</div>
					<div class="date-pick-cont item-control-tool" title="Tìm sản phẩm theo thời gian tạo">
						<input type="text" id="pick-date-product" class="form-control form-control-cus picker-cus">
						<i class="glyphicon glyphicon-calendar icon-pickdate"></i>
					</div>
					<div class="select-page-cont item-control-tool">
						<select title="Tìm sản phẩm theo tình trạng sản phẩm" class="form-control form-control-cus picker-cus select-cus" v-model="status">
							<option value="">Tình trạng SP</option>
							<option value="HAS">Còn hàng</option>
							<option value="CLEAR">Hết hàng</option>
						</select>
						<i class="fa fa-caret-down icon-dropdown-sel"></i>
					</div>
					<div class="item-control-tool ">
						<div class="dropdown dropdown-cus">
						    <button title="Tìm sản phẩm theo danh mục" class="btn btn-default dropdown-toggle btn-type-search" type="button" data-toggle="dropdown">{{ category ? titleCate : 'Chọn danh mục' }}
						    <span class="caret"></span></button>
						    <input type="text" placeholder="Tìm kiếm tên/mã SP" class="form-control search-has-type form-control-cus" v-model.lazy="keyword" v-debounce="500">
						    <span class="icon-searchHas-type">
								<i class="fa fa-search"></i>
							</span>
						    <ul class="dropdown-menu" style="max-height: 200px; overflow: auto;">
						    	<li v-show="loadingCates">
						    		<a href="#" class="text-center">
						    			<i class="fa fa-spinner fa-pulse fa-fw"></i>
										<span class="sr-only">Loading...</span>
						    		</a>
						    	</li>
						    	<li>
						    		<a href="#" @click="getProductsCate()">Chọn danh mục</a>
						    	</li>
					      		<li v-for="cate in cates" @click="getProductsCate(cate)"><a href="#">{{ cate.cate_name }}</a></li>
						    </ul>
					  	</div>
					</div>
					<div class="sort-cont item-control-tool action-rightHead-table" v-show="role.name == 'ADMINSTRATOR'">
						<a href="/products/export/xlsx" title="Tải về danh sách sản phẩm">
							<span class="download-list icon-action-headTable">
								<i class="fa fa-download"></i>
							</span>
						</a>
					</div>
				</div>
			</div>
			<circle3-load v-if="isLoading"></circle3-load>
			<div v-else>
				<div class="panel-body no-padding-top" v-if="products.data.length > 0">
					<table class="table table-condensed table-head-black">
						<thead>
							<tr>
								<th class="text-center">
									<div class="md-checkbox">
										<input type="checkbox" v-model="checkAll" :value="1" id="check_all">
										<label for="check_all"></label>
									</div>
								</th>
								<th class="cs-ellipsis-clm">Danh mục</th>
								<th class="cs-ellipsis-clm">Mã SP</th>
								<th class="ellipsis-column" style="min-width: 80px;">Tên SP</th>
								<th class="text-center">Hình ảnh</th>
								<th class="cs-ellipsis-clm text-right">Số lượng</th>
								<th class="cs-ellipsis-clm text-right">Đã bán</th>
								<th class="cs-ellipsis-clm text-right">Tồn kho</th>
								<th class="cs-ellipsis-clm text-right">Đơn giá (₫)</th>
								<th class="text-center">Tình trạng</th>
								<th class="text-center">Tác vụ</th>
							</tr>
						</thead>
						<tbody v-for="(product, index) in products.data">
							<tr :class="{ 'bg-bold-border': ! product.parent_id, '': product.parent_id }">
								<td class="text-center">
									<div class="md-checkbox">
										<input type="checkbox" v-model="productsCheck" :id="'prod_'+product.id" :value="product.id">
										<label :for="'prod_'+product.id"></label>
									</div>
								</td>
								<td class="cs-ellipsis-clm">
									{{ product.cate_name }}
								</td>
								<td class="cs-ellipsis-clm">{{ product.prod_code }}</td>
								<td class="cs-ellipsis-clm" style="min-width: 80px;">
									<div class="name-product-mana">
										{{ product.prod_name }}
									</div>
									<div class="relate-des" v-show="product.count_childs > 0">
										{{ product.count_childs + ' sản phẩm cùng loại' }}
										<i class="fa fa-toggle-down watch-childs" :id="'watch_childs_'+product.id" @click="loadChilds(index)"></i>
									</div>
								</td>
								<td class="cs-ellipsis-clm text-center">
									<a href="#" class="a-thumb-tb">
										<img :src="product.prod_thumb">
									</a>
								</td>
								<td class="cs-ellipsis-clm text-right">{{ product.prod_quantity }}</td>
								<td class="cs-ellipsis-clm text-right">{{ product.count_sold }}</td>
								<td class="cs-ellipsis-clm text-right">{{ product.prod_quantity - product.count_sold }}</td>
								<td class="cs-ellipsis-clm text-right">{{ product.count_childs > 0 ? '-' : parseFloat(product.prod_price).toLocaleString() }}</td>
								<td class="text-center">
									<div>
										<span class="txt-color-green" v-if="product.status == 'HAS'">Còn hàng</span>
										<span class="txt-color-red" v-if="product.status == 'CLEAR'">Hết hàng</span>
									</div>
								</td>
								<td class="text-center">
									<div>
										<span class="ac-row-tb edit-row-table" @click="editProduct(product)">
											<i class="fa fa-edit"></i>
										</span>
										<span class="ac-row-tb del-row-table" @click="confirmDeleteProduct(product.id)">
											<i class="fa fa-close"></i>
										</span>
									</div>
								</td>
							</tr>
							<tr v-for="child in products.data[index].childs">
								<td></td>
								<td></td>
								<td class="cs-ellipsis-clm">{{ child.prod_code }}</td>
								<td class="cs-ellipsis-clm" style="min-width: 130px;">
									<div class="name-product-mana">
										{{ child.prod_name }}
									</div>
									<div class="relate-des">
										( {{ child.properties }} )
									</div>
								</td>
								<td class="cs-ellipsis-clm text-center">
									<a href="#" class="a-thumb-tb">
										<img :src="child.prod_thumb">
									</a>
								</td>
								<td class="cs-ellipsis-clm text-right">{{ child.prod_quantity }}</td>
								<td class="cs-ellipsis-clm text-right">{{ child.count_sold }}</td>
								<td class="cs-ellipsis-clm text-right">{{ child.prod_quantity - child.count_sold }}</td>
								<td class="cs-ellipsis-clm text-right">{{ parseFloat(child.prod_price).toLocaleString() }}</td>
								<td class="text-center">
									<div>
										<span class="txt-color-green" v-if="child.status == 'HAS'">Còn hàng</span>
										<span class="txt-color-red" v-if="child.status == 'CLEAR'">Hết hàng</span>
									</div>
								</td>
								<td class="text-center">
									<div>
										<span class="ac-row-tb del-row-table" @click="confirmDeleteProduct(child.id)">
											<i class="fa fa-close"></i>
										</span>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
					<div class="row" v-show="productsCheck.length > 0">
						<div class="form-group col-sm-2">
							<select class="form-control" v-model="pushCate">
								<option value="">Thêm danh mục</option>
								<option v-for="cate in cates" :value="cate.id">{{ cate.cate_name }}</option>
							</select>
						</div>
						<div class="form-group col-sm-2">
							<span v-if="isSavingCate">
								<i class="fa fa-spinner fa-pulse fa-fw"></i>
								<span class="sr-only">Loading...</span>
							</span>
							<button v-else type="button" class="btn btn-success" @click="pushProductsToCate()">OK</button>
						</div>
					</div>
				</div>
				<div class="text-center" v-else>
					Chưa có sản phẩm nào. Hãy nhấp vào "Thêm sản phẩm" để tạo sản phẩm mới
				</div>
				<div class="under-table">
					<div class="count-total">
						Tổng {{ products.total }} sản phẩm
					</div>
					<paginate
					  	:page-count="products.last_page"
					  	:click-handler="pickPage"
					  	:initial-page="products.current_page - 1"
				  		:prev-text="'Prev'"
				  		:next-text="'Next'"
				  		:container-class="'paginate-cont'"
				  		:page-class="'page-item'"
				  		:prev-link-class="'per-page previous'"
				  		:next-link-class="'per-page next'"
				  		:prev-class="'per-page previous'"
				  		:next-class="'per-page next'" v-if="products.last_page > 1">

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

		<!-- modal -->
		<div class="modal fade" id="confirm-del-product" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
		  	<div class="modal-dialog modal-sm" role="document">
		    	<div class="modal-content">
		    		<div class="modal-body">
		    			<p>{{ titleConfirm }}</p>
		    		</div>
		    		<div class="modal-footer" v-if="isDoing">
	    				<process></process>
		    		</div>
		    		<div class="modal-footer" v-else>
			      		<button class="btn btn-success" @click="destroyProduct()">Đồng ý</button>
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
	import {formatPrice} from '../../helpers/numeralfix'
	import {valuePrice} from '../../helpers/numeralfix'
	import {numeral} from '../../helpers/numeralfix'
	import Circle3 from '../loading/Circle3.vue'
	import Process from '../loading/Process.vue'
	import calendarOption from '../../helpers/calendarOption'
	import Paginate from 'vuejs-paginate'
	import debounce from '../../helpers/directive'

	export default {
		components: {
			'circle3-load': Circle3,
			'process': Process,
			'paginate': Paginate
		},
		data() {
			return {
				loadingCates: false,
				cates: [],
				products: {
					pages: [],
					data: []
				},
				isLoading: false,
				idItemOfAction: null,
				titleConfirm: '',
				loadingConfirm: false,
				isDoing: false,
				sortBy: 'desc',
				timeFrom: '',
				timeTo: '',
				status: '',
				category: '',
				keyword: '',
				pushCate: '',
				productsCheck: [],
				checkAll: 0,
				titleCate: '',
				isSavingCate: false
			}
		},
		directives: {debounce},
		created() {
			this.loadCates();
		},
		mounted() {
			EventBus.$on('get-list-products', () => {
				this.getListProducts();
			})
		},
		computed: {
			user() {
				return this.$store.state.user;
			},
			role() {
				return this.$store.state.role;
			}
		},
		updated() {
			$('#pick-date-product').daterangepicker(
			{
			    "locale": calendarOption,
				"timePicker": true,
				"timePicker24Hour": true,
			 	"timePickerSeconds": true,
			    "opens": "center",
			    "drops": "down",
			    "alwaysShowCalendars": true
			}, 
			(start, end, label) => {
			    this.timeFrom = start.format('YYYY-MM-DD HH:mm:ss');
			    this.timeTo = end.format('YYYY-MM-DD HH:mm:ss');
			    this.getListProducts();
			});
		},
		watch: {
			sortBy() {
				this.getListProducts();
			},
			status() {
				this.getListProducts();
			},
			category() {
				this.getListProducts();
			},
			checkAll() {
				if (this.checkAll) {
					this.products.data.forEach((val) => {
						this.productsCheck.push(val.id);
					})
				} else {
					this.productsCheck = [];
				}
			},
			keyword() {
				this.getListProducts();
			}
		},
		methods: {
			formatPrice(price) {
				return formatPrice(price);
			},
			formatTime(time,formater) {
				return momentLocale(time).format(formater);
			},
			pickPage(pageNum) {
				let path = this.products.path + '?page='+ pageNum;
				this.turnPage(path);
			},
			getListProducts: function() {
				this.isLoading = true;
				get('/api/product/get/list?sortBy='+this.sortBy+'&timeFrom='+this.timeFrom+'&timeTo='+this.timeTo+'&status='+this.status+'&category='+this.category+'&keyword='+this.keyword)
				.then((res) => {
					if (res.data) {
						this.products = res.data;
					}
					this.isLoading = false;
				})
				.catch('Lỗi tải trang sản phẩm','error');
			},
			loadChilds(index) {
				let id = this.products.data[index].id;
				if (this.products.data[index].childs && this.products.data[index].childs.length) {
					this.$set(this.products.data[index], 'childs', []);
					$("#watch_childs_"+id).removeClass('fa-rotate-180');
				} else {
					$("#watch_childs_"+id).addClass('fa-rotate-180');
					get('/api/product/'+id+'/childs')
					.then((res) => {
						this.$set(this.products.data[index], 'childs', res.data)
					})
					.catch((err) => {
						$.notify("Lỗi tải sản phẩm", "error");
					})
				}
			},
			turnPage(url) {
				get(url)
				.then((res) => {
					this.products = res.data;
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải trang!', 'error');
					this.isLoading = false;
				});
			},
			getAddProduct() {
				EventBus.$emit('get-add-product');
			},
			editProduct(product) {
				EventBus.$emit('get-edit-product', product.id);
			},
			confirmDeleteProduct(id) {
				this.idItemOfAction = id;
				this.openConfirm('Bạn có chắc muốn xóa sản phẩm này');
			},
			openConfirm(title) {
				this.titleConfirm = title;
				$("#confirm-del-product").modal('show');
			},
			destroyProduct() {
				let id = this.idItemOfAction;
				this.isDoing = true;
				post('/api/product/destroy/'+id)
				.then((res) => {
					if (res.data.deleted) {
						this.getListProducts();
						$.notify('Xóa sản phẩm thành công', 'success');
					} else 
					if (res.data.error) {
						$.notify(res.data.message, 'error');
					} else $.notify(res.data, 'error');
					this.isDoing = false;
					this.idItemOfAction = null;
					$("#confirm-del-product").modal('hide');
				})
				.catch((err) => {
					$.notify("Lỗi xóa sản phẩm", "error");
					this.isDoing = false;
				})
			},
			cancelDestroy() {
				$("#confirm-del-product").modal('hide');
				this.idItemOfAction = null;
				this.isDoing = false;
			},
			loadCates: function() {
				this.loadingCates = true;
				get('api/category/get/all')
				.then((res) => {
					this.cates = res.data;
					this.loadingCates = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải danh mục', 'error');
					this.loadingCates = false;
				})
			},
			getProductsCate(cate = null) {
				if (cate === null) {
					this.category = '';
					this.titleCate = "Chọn danh mục";
				} else {
					this.category = cate.id;
					this.titleCate = cate.cate_name;
				}
			},
			pushProductsToCate() {
				this.isSavingCate = true;
				post('/api/product/pushToCate', {
					prodIds: this.productsCheck,
					cateId: this.pushCate
				})
				.then((res) => {
					if (res.data.success) {
						this.getListProducts();
						$.notify('Cập nhật danh mục thành công', 'success');
						this.isSavingCate = false;
					}
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
					} else {
						$.notify('Lỗi thêm danh mục', 'error');
					}
					this.isSavingCate = false;
				})
			} 
		},
		beforeDestroy () {
			EventBus.$off('get-list-products')
		}
	}
</script>