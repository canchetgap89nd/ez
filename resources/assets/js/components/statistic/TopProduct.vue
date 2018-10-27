<template>
	<div>
		<div class="panel panel-black panel-grid">
			<div class="panel-heading">
				<span class="title-top">
					Top 10 sản phẩm có doanh thu cao nhất
				</span>
			</div>
			<div class="panel-body">
				<div class="col-lg-6 left-cl">
					<div class="entity-post-top entity-has-thumb" v-for="(prod, index) in products" v-show="index < 5">
						<div class="sort-number">
							<span :class="{'txt-number-sort top1-st': index == 0, 'txt-number-sort top2-st': index == 1, 'txt-number-sort top3-st': index == 2, 'txt-number-sort': index > 2 }">{{ index + 1 }}</span>
						</div>
						<div class="thumb-product-top">
							<a href="#">
								<img :src="prod.prod_thumb">
							</a>
						</div>
						<div class="detail-post-top"> 
							<div class="title-postTop-box">
								<span class="name-product-top">{{ prod.prod_code }}</span>
							</div>
							<div class="des-post-top">
								{{prod.prod_name}} {{ prod.properties }}
							</div>
							<div class="more-infoPost-top">
								<span class="interactive-post-top">
									<span class="type-total type-total-t2">
										<i class="fa fa-usd"></i>
										<span class="total-interactive">{{ formatPrice(prod.prod_price * prod.prod_quantity) }}</span>
									</span>
									<span class="type-total type-total-t2">
										<i class="fa fa-shopping-cart"></i>
										<span class="total-interactive">{{ prod.orders_count }}</span>
									</span>
									<span class="type-total type-total-t2">
										<i class="fa fa-cube"></i>
										<span class="total-interactive">{{ prod.count_sold }}</span>
									</span>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 right-cl">
					<div class="entity-post-top entity-has-thumb" v-for="(prod, index) in products" v-show="index > 4">
						<div class="sort-number">
							<span class="txt-number-sort">{{ index + 1 }}</span>
						</div>
						<div class="thumb-product-top">
							<a href="#">
								<img :src="prod.prod_thumb">
							</a>
						</div>
						<div class="detail-post-top"> 
							<div class="title-postTop-box">
								<span class="name-product-top">{{ prod.prod_code }}</span>
							</div>
							<div class="des-post-top">
								{{prod.prod_name}} {{ prod.properties }}
							</div>
							<div class="more-infoPost-top">
								<span class="interactive-post-top">
									<span class="type-total type-total-t2">
										<i class="fa fa-usd"></i>
										<span class="total-interactive">{{ formatPrice(prod.prod_price * prod.prod_quantity) }}</span>
									</span>
									<span class="type-total type-total-t2">
										<i class="fa fa-shopping-cart"></i>
										<span class="total-interactive">{{ prod.orders_count }}</span>
									</span>
									<span class="type-total type-total-t2">
										<i class="fa fa-cube"></i>
										<span class="total-interactive">{{ prod.count_sold }}</span>
									</span>
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
	
	import { get } from '../../helpers/send'
	import {momentLocale} from '../../helpers/momentfix'
	import {formatPrice} from '../../helpers/numeralfix'
	import AWN from "awesome-notifications"
	var notifier = new AWN();

	export default {
		data() {
			return {
				products: []
			}
		},
		mounted() {
			new Promise((resolve, reject) => {
    		})
			get('../../api/statistic/top10Product')
			.then((res) => {
				this.products = res.data;
    			notifier.asyncBlock(Promise.resolve("all done"));
			})
		},
		methods: {
			formatPrice(price) {
				return formatPrice(price);
			}
		}
	}
</script>