<template>
	<div :class="'ss-main ss-' + mainId ">
        <div @click="openSearch()" class="ss-single-selected">
        	<span class="placeholder">
        		<span class="ss-disabled">Chọn sản phẩm</span>
        	</span>
        	<span :class="'ss-arrow ss-arrow-'+ mainId">
        		<span class="arrow-down"></span>
        	</span>
        </div>
        <div :class="'ss-content ss-content-' + mainId ">
            <div class="ss-search">
                <input type="search" placeholder="Tìm kiếm" v-model.lazy="keyword" v-debounce="500" tabindex="0">
            </div>
            <div class="ss-list">
                <div class="ss-option text-center" v-show="searching">
                	<i class="fa fa-spinner fa-pulse fa-fw"></i>
					<span class="sr-only">Loading...</span>
                </div>
                <!-- <div class="ss-option ss-disabled" data-id="14758305">Hà Nội</div> -->
                <div v-for="product in products" class="ss-option" @click="chooseProduct(product)">
                	<div class="title-row">
                		{{ product.prod_name }} {{ product.properties ? '(' + product.properties + ')' : '' }} - {{ product.prod_code }}
                	</div>
                	<div class="des-row" v-if="product.hasSale">
                		<span class="del-price">
                			{{ parseFloat(product.prod_price).toLocaleString() }} đ
                		</span>
                		<span class="real-price">
                			{{ parseFloat(product.prod_price - ((product.prod_price * product.perc_disc)/100)).toLocaleString() }} đ
                		</span>
                	</div>
                	<div v-else class="des-row">
                		<span class="real-price">
                			{{ parseFloat(product.prod_price).toLocaleString() }} đ
                		</span>
                	</div>
                	<div class="sale-info" v-if="product.hasSale">
                		Sale: <span>{{ product.perc_disc }}%</span>
                	</div>
                	<div class="des-row">
                		<span class="quantity-storage">
                			Còn {{ parseInt(product.prod_quantity) - parseInt(product.count_sold) }} SP trong kho
                		</span>
                	</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
	
	import { EventBus } from '../helpers/bus'
	import { get } from '../helpers/send'
	import {momentLocale} from '../helpers/momentfix'
	import debounce from '../helpers/directive'

	export default {
		data() {
			return {
				products: [],
				keyword: '',
				searching: false
			}
		},
		watch: {
			keyword() {
				this.searchProducts();
			}
		},
		directives: {debounce},
		created() {
			this.mainId = Math.floor((Math.random() * 1000000) + 1);
		},
		mounted() {
			let thisIns = this;
			$(document).click(function(e) {
			    if(($(e.target).attr('class') != 'ss-main ss-'+thisIns.mainId) && !$(".ss-main.ss-"+thisIns.mainId).find(e.target).length) {
		        	$(".ss-arrow-" + thisIns.mainId + " span").removeClass('arrow-up');
					$(".ss-arrow-" + thisIns.mainId + " span").addClass('arrow-down');
			        $(".ss-content-"+thisIns.mainId).removeClass('ss-open');
			    } 
			});
		},
		methods: {
			openSearch() {
				let content = $(".ss-content.ss-content-"+this.mainId);
				let arrow = $(".ss-arrow-" + this.mainId + " span");
				if (content.attr('class') == 'ss-content ss-content-'+this.mainId) {
					content.addClass('ss-open');
					arrow.removeClass('arrow-down');
					arrow.addClass('arrow-up');
				} else {
					content.removeClass('ss-open');
					arrow.removeClass('arrow-up');
					arrow.addClass('arrow-down');
				}
			},
			closeSearch() {
				let content = $(".ss-content.ss-content-"+this.mainId);
				let arrow = $(".ss-arrow-" + this.mainId + " span");
				if (content.attr('class') == 'ss-content ss-content-'+this.mainId+ ' ss-open') {
					content.removeClass('ss-open');
					arrow.removeClass('arrow-up');
					arrow.addClass('arrow-down');
				}
			},
			searchProducts: function() {
				this.keyword = this.keyword.trim();
				this.searching = true;
				get('/api/product/search?keyword='+this.keyword)
				.then((res) => {
					this.searching = false;
					if (res.data) {
						for (var i = 0; i < res.data.length; i++) {
							res.data[i].hasSale = false;
							if (res.data[i].camp_status) {
								if (!res.data[i].sold_out) {
									let start = momentLocale(res.data[i].start_time).unix();
									let end = momentLocale(res.data[i].end_time).unix();
									if (start <= momentLocale().unix() && momentLocale().unix() < end) {
										res.data[i].hasSale = true;
									}
								} else {
									if (res.data[i].prod_quantity - res.data[i].count_sold > 0) {
										res.data[i].hasSale = true;
									}
								}
							}
						}
						this.products = res.data;
					}
				})
				.catch((err) => {
					$.notify('Lỗi tải sản phẩm', 'error');
					this.searching = false;
				})
			},
			chooseProduct(product) {
				EventBus.$emit('choose-product', product);
				this.closeSearch();
			}
		}
	}
</script>