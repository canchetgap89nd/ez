import Setting from '../../../components/setup/Setting.vue'
import GeneralSetting from '../../../components/setup/GeneralSetting.vue'
import GroupCustomerSetting from '../../../components/setup/GroupCustomerSetting.vue'
import QuickReplySetting from '../../../components/setup/QuickReplySetting.vue'
import BlackList from '../../../components/setup/BlackList.vue'
import FilterInfo from '../../../components/setup/FilterInfo.vue'
import MixSetting from '../../../components/setup/MixSetting.vue'
import Extensions from '../../../components/setup/Extensions.vue'
import CreatePost from '../../../components/setup/CreatePost'
import EditPost from '../../../components/setup/EditPost'
import PublishedPost from '../../../components/setup/PublishedPost'
import SchedulePost from '../../../components/setup/SchedulePost'
import AutoReply from '../../../components/setup/auto/AutoReply'
import UpgradeAccount from '../../../components/setup/account/UpgradeAccount'

export default [
	{
		name: 'setting',
		path: '/setting/',
		component: Setting, 
		meta: {
			title: 'Cài đặt'
		},
		redirect: {
			name: 'generalSet'
		},
		children: [
			{
				path: 'basic/generalSetting',
				name: 'generalSet',
				component: GeneralSetting
			},
			{
				path: 'basic/groupsSetting',
				name: 'groupSet',
				component: GroupCustomerSetting
			},
			{
				path: 'basic/quickReplySetting',
				name: 'quickReplySet',
				component: QuickReplySetting
			},
			{
				path: 'basic/blackListSetting',
				name: 'blackListSet',
				component: BlackList
			},
			{
				path: 'advance/FilterInfoSetting',
				name: 'FilterInfoSet',
				component: FilterInfo
			},
			{
				path: 'advance/MixSetting',
				name: 'MixSet',
				component: MixSetting
			},
			{
				path: 'advance/extensions',
				name: 'extensions',
				redirect: {
			        name: 'createPost'
		      	},
				component: Extensions,
				children: [
					{
						path: 'create-post',
						name: 'createPost',
						component: CreatePost
					},
					{
						path: 'published-post',
						name: 'publishedPost',
						component: PublishedPost
					},
					{
						path: 'schedule-post',
						name: 'schedulePost',
						component: SchedulePost
					},
					{
						path: 'edit-post/:id',
						name: 'editPost',
						component: EditPost
					}
				]
			},
			{
				path: 'advance/autoReply',
				name: 'setupAutoReply',
				component: AutoReply
			},
			{
				name: 'setupAccount',
				path: 'account',
				component: UpgradeAccount
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