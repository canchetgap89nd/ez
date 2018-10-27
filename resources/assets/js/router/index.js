import Vue from 'vue'
import VueRouter from 'vue-router'

import SettingRouter from './modules/setting'
import StatisticRouter from './modules/statistic'
import { concat } from 'lodash'
import Interactive from '../components/dashboard/Interactive.vue'
import Customer from '../components/customer/Customer.vue'
import Order from '../components/order/Order.vue'
import Product from '../components/product/Product.vue'
import NotFound from '../components/errors/NotFound.vue'

Vue.use(VueRouter)

const DefaultRoute = [
	{
		name: 'conversations', path: '/conversations', component: Interactive, meta: {title: 'Hội thoại'}
	},

	{
		name: 'customer', path: '/customers', component: Customer, meta: {title: 'Khách hàng'}
	},

	{
		name: 'order', path: '/orders', component: Order, meta: {title: 'Đơn hàng'}
	},

	{
		name: 'product', path: '/product', component: Product, meta: {title: 'Sản phẩm'}
	},

	{
		name: 'notFound404', path: '/not-found', component: NotFound, meta: {title: 'Chốt Sale'}
	}
]

const router = new VueRouter({
	mode: "history",
	routes: concat(
	    DefaultRoute,
	    SettingRouter,
	    StatisticRouter
  	)
})

export default router