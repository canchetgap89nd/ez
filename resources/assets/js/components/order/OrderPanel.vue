<template>
	<div class="container">
		<div class="row">
			<div class="panel panel-custom no-padding-top">
				<div class="container-tab-panel">
					<ul class="tab-head container-tab-control no-padding-bot" target-container="container-order-transport">
						<li class="active" @click="getListOrders()">
							<a class="tab-control tab-order-control" data-toggle="tab" href="#tab-order-manager">
								Quản lý đơn hàng
							</a>
							<span class="badge badge-alert badge-manager-tab" v-show="orderNewCount > 0">{{ orderNewCount }}</span>
						</li>
						<li @click="getListTransports()">
							<a class="tab-control tab-order-control" data-toggle="tab" href="#tab-transport-manager">
								Quản lý giao hàng
							</a>
						</li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane fade in active" id="tab-order-manager">
							<orders-manager :role="role" :user="user"></orders-manager>
						</div>
						<div class="tab-pane fade in" id="tab-transport-manager">
							<transport-manager :role="role" :user="user"></transport-manager>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">

	import OrderManager from "./OrderManager.vue"
	import TransportManager from "./TransportManager.vue"
	import { EventBus } from '../../helpers/bus'
	import {get} from "../../helpers/send"

	export default {
		components: {
			'orders-manager': OrderManager,
			'transport-manager': TransportManager,
		},
		props: ['user', 'role'],
		data() {
			return {
				orderNewCount: 0
			}
		},
		created() {
			this.getNewOrderCount();
		},
		mounted() {
			EventBus.$on('count-new-order', (data) => {
				if (data.up) {
					this.orderNewCount += data.count;
				}
			})
			EventBus.$on('get-count-order-new', () => {
				this.getNewOrderCount();
			})
		},
		methods: {
			getListTransports() {
				EventBus.$emit('list-transport');
			},
			getListOrders() {
				EventBus.$emit('list-orders');
			},
			getNewOrderCount() {
				get('api/orders/order-new/count')
				.then((res) => {
					if (res.data.count_new) {
						this.orderNewCount = res.data.count_new;
					}
				})
				.catch((err) => {
					$.notify('Cập nhật tình trạng đơn mới bị lỗi', 'error');
				})
			}
		},
		beforeDestroy () {
			EventBus.$off('count-new-order')
			EventBus.$off('get-count-order-new')
		}
	}
</script>