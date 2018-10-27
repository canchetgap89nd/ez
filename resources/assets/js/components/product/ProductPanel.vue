<template>
	<div>
		<div class="cont-of-tab">
			<div class="container">
				<div class="row">
					<ul class="tab-head tab-product-manager container-tab-control">
						<li class="active" @click="getListCate()" v-if="role.name != 'SALER'">
							<a class="tab-order-control" data-toggle="tab" href="#category-manager-board">
								Quản lý danh mục
							</a>
						</li>

						<li @click="getListProducts()" v-if="role.name != 'SALER'">
							<a class="tab-order-control" data-toggle="tab" href="#product-manager-board">
								Quản lý sản phẩm
							</a>
						</li>
						<li @click="getListProperties()" v-if="role.name != 'SALER'">
							<a class="tab-order-control" data-toggle="tab" href="#properties-manager-board">
								Quản lý thuộc tính
							</a>
						</li>
						<li @click="getListImport()" v-if="role.name != 'SALER'">
							<a class="tab-order-control" data-toggle="tab" href="#warehousing-manager-board">
								Quản lý nhập kho
							</a>
						</li>
						<li @click="getListExport()" v-if="role.name != 'SALER'">
							<a class="tab-order-control" data-toggle="tab" href="#export-product-board">
								Quản lý xuất kho
							</a>
						</li>
						<li @click="getListCampaign()" :class="{'active': role.name == 'SALER'}">
							<a class="tab-order-control" data-toggle="tab" href="#sales-manager-board">
								Quản lý khuyến mãi
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="tab-content">
					<div class="tab-pane fade in active" id="category-manager-board" v-if="role.name != 'SALER'">
						<cate-manager></cate-manager>
					</div>
					<div class="tab-pane fade in" id="product-manager-board" v-if="role.name != 'SALER'">
						<product-manager :user="user" :role="role"></product-manager>
					</div>
					<div class="tab-pane fade in" id="properties-manager-board" v-if="role.name != 'SALER'">
						<property-manager></property-manager>
					</div>
					<div class="tab-pane fade in" id="warehousing-manager-board" v-if="role.name != 'SALER'">
						<import-product></import-product>
					</div>
					<div class="tab-pane fade in" id="export-product-board" v-if="role.name != 'SALER'">
						<export-product></export-product>
					</div>
					<div :class="{'tab-pane fade in': role.name != 'SALER', 'tab-pane fade in active': role.name == 'SALER'}" id="sales-manager-board">
						<campaign></campaign>
					</div>
				</div>
			</div>
		</div>

		<!-- begin modal add new category -->
		<div class="modal fade modal-not-row" id="add-new-category" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
		    <div class="modal-dialog">

		        <!-- Modal content-->
		        <add-cate></add-cate>

		    </div>
		</div>
		<!-- end modal add new category -->

		<!-- begin modal edit category -->
		<div class="modal fade modal-not-row" id="edit-cate-modal" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
		    <div class="modal-dialog">

		        <!-- Modal content-->
		        <edit-cate></edit-cate>

		    </div>
		</div>
		<!-- end modal edit category -->

		<!-- begin modal add new product -->
		<div class="modal fade" id="add-product-new" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
		    <div class="modal-dialog modal-lg">
		        <!-- Modal content-->
		        <add-product></add-product>
		    </div>
		</div>
		<!-- end modal add new product -->

		<!-- begin modal edit product -->
		<div class="modal fade" id="modal-edit-product" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
		    <div class="modal-dialog modal-lg">
		        <!-- Modal content-->
		        <edit-product></edit-product>
		    </div>
		</div>
		<!-- end modal edit product -->

		<!-- begin modal add warehouse -->
		<div class="modal fade modal-not-row" id="add-warehouse" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg">

			    <!-- Modal content-->
			    <add-import></add-import>

			</div>
		</div>
		<!-- end modal add warehouse -->

		<!-- begin modal infomation warehouse -->
		<div class="modal fade modal-not-row" id="info-warehouse" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg">

			    <!-- Modal content-->
			    <detail-import></detail-import>

			</div>
		</div>
		<!-- end modal infomation warehouse -->

		<!-- begin modal add sales campaign -->
		<div class="modal fade modal-not-row" id="add-sales-campaign" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg">

			    <!-- Modal content-->
			    <add-campaign></add-campaign>

			</div>
		</div>
		<!-- end modal add sales campaign -->

		<!-- begin modal detail sales campaign -->
		<div class="modal fade modal-not-row" id="detail-sales-campaign" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg">

			    <!-- Modal content-->
			    <detail-campaign :user="user" :role="role"></detail-campaign>

			</div>
		</div>
		<!-- end modal detail sales campaign -->

		<!-- begin modal add new properties for product -->
		<div class="modal fade modal-not-row" id="add-new-properties" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">

			    <!-- Modal content-->
			    <add-property></add-property>

			</div>
		</div>
		<!-- end modal add new properites for product -->

		<!-- begin modal edit properties for product -->
		<div class="modal fade modal-not-row" id="edit-property" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">

			    <!-- Modal content-->
			    <edit-property></edit-property>

			</div>
		</div>
		<!-- end modal edit properites for product -->

		<!-- begin modal change history of product -->
		<div class="modal fade modal-not-row" id="changeHistoryProduct" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">

			    <!-- Modal content-->
			    <change-history></change-history>

			</div>
		</div>
		<!-- begin modal change history of product -->

	</div>
</template>
<script type="text/javascript">
	
	import { EventBus } from '../../helpers/bus'
	import ExportProduct from './ExportProduct.vue'
	import ProductManager from './ProductManager.vue'
	import AddProduct from './AddProduct.vue'
	import EditProduct from './EditProduct.vue'
	import CateManager from './CateManager.vue'
	import AddCate from './AddCate.vue'
	import EditCate from './EditCate.vue'
	import PropertyManager from './PropertyManager.vue'
	import AddProperty from './AddProperty.vue'
	import EditProperty from './EditProperty.vue'
	import ImportProduct from './ImportProduct.vue'
	import AddImport from './AddImport.vue'
	import DetailImport from './DetailImport.vue'
	import Campaign from './Campaign.vue'
	import AddCampaign from './AddCampaign.vue'
	import DetailCampaign from './DetailCampaign.vue'
	import ChangeHistory from './ChangeHistory.vue'

	export default {
		components: {
			'export-product': ExportProduct,
			'product-manager': ProductManager,
			'add-product': AddProduct,
			'edit-product': EditProduct,
			'cate-manager': CateManager,
			'add-cate': AddCate,
			'edit-cate': EditCate,
			'property-manager': PropertyManager,
			'add-property': AddProperty,
			'edit-property': EditProperty,
			'import-product': ImportProduct,
			'add-import': AddImport,
			'detail-import': DetailImport,
			'campaign': Campaign,
			'add-campaign': AddCampaign,
			'detail-campaign': DetailCampaign,
			'change-history': ChangeHistory
		},
		computed: {
			role() {
				return this.$store.state.role;
			},
			user() {
				return this.$store.state.user;
			}
		},
		mounted() {
			$('.modal').on('hidden.bs.modal', function (e) {
			    if($('.modal').hasClass('in')) {
			    	$('body').addClass('modal-open');
			    }    
			});
		},
		methods: {
		  	getListProducts() {
		  		EventBus.$emit('get-list-products');
		  	},
			getListCate() {
				EventBus.$emit('get-list-cate');
			},
			getListImport() {
				EventBus.$emit('get-list-import');
			},
			getListCampaign() {
				EventBus.$emit('get-list-campaign');
			},
			getListProperties() {
				EventBus.$emit('get-list-property');
			},
			getListExport() {
				EventBus.$emit('list-ex-product');
			}
		}
	}
</script>
<style type="text/css">
	.list-images-preview {
	    display: inline-block;
	    padding: 0;
	    border: none;
	}
	.thumb-previews {
	    height: 100%;
	    width: 100%;
	    padding: 0;
	    border: none;
	}
	.thumb-previews .dz-preview {
		margin: 0;
	}
	.thumb-previews .dz-preview .dz-image img {
		width: 100%
	}
	.thumb-previews .dz-error-message {
	    position: absolute;
	 	top: 0 !important;
	}
	.remo-img {
		color: #ddd;
		transition: all 0.3s ease;
		cursor: pointer;
	}
	.remo-img:hover {
		color: #bdbaba;
	}
	.item-import {
		cursor: pointer;
	}
	.list-props {
		max-height: 200px;
		overflow: auto;
	}
	.btn_add_newProp {
	    display: inline-block;
	    font-size: 20px;
	    padding: 0 10px;
	    float: left;
	}
	.ls-cur-val {
		margin-bottom: 10px;
	}
	.ls-cur-val .label {
		margin-right: 5px;
	}
	.ls-cur-val .label .remove-value {
		margin-left: 5px;
		cursor: pointer;
	}
	.ls-cur-val .label .remove-value.disable {
		cursor: not-allowed;
	}
	.ls-c-ad {
	    width: 80%;
    	float: left;
	}
	.btn_qAdd_newCate {
	    display: inline-block;
	    font-size: 20px;
	    padding: 0 10px;
        position: absolute;
    	top: 25px;
    	right: 10%;
	}
</style>