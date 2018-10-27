import Statistic from '../../../components/statistic/Statistic.vue'
import ChartBoard from '../../../components/statistic/ChartBoard.vue'
import TopBoard from '../../../components/statistic/TopBoard.vue'
import SummaryChart from '../../../components/statistic/SummaryChart.vue'
import SaleBoard from '../../../components/statistic/SaleBoard.vue'
import ConversationBoard from '../../../components/statistic/ConversationBoard.vue'
import StaffBoard from '../../../components/statistic/StaffBoard.vue'
import TopPost from '../../../components/statistic/TopPost.vue'
import TopProduct from '../../../components/statistic/TopProduct.vue'

export default [
	{
		name: 'statistic',
		path: '/statistic/',
		component: Statistic,
		redirect: {
			name: 'summaryChart'
		},
		children: [
			{
				path: 'chartBoard/',
				name: 'chartBoard',
				redirect: {
					name: 'summaryChart'
				},
				component: ChartBoard,
				children: [
					{
						path: 'summaryChart',
						name: 'summaryChart',
						component: SummaryChart,
					},
					{
						path: 'saleBoard',
						name: 'saleBoard',
						component: SaleBoard,
					},
					{
						path: 'conversationBoard',
						name: 'conversationBoard',
						component: ConversationBoard,
					},
					{
						path: 'staffBoard',
						name: 'staffBoard',
						component: StaffBoard,
					},
				]
			},
			{
				path: 'topBoard/',
				name: 'topBoard',
				component: TopBoard,
				redirect: {
					name: 'topPost'
				},
				children: [
					{
						path: 'topPost',
						name: 'topPost',
						component: TopPost,
					},
					{
						path: 'topProduct',
						name: 'topProduct',
						component: TopProduct,
					}
				]
			},
			{
				path: '*',
				redirect: {
					name: 'notFound404'
				}
			}
		]
	}
]