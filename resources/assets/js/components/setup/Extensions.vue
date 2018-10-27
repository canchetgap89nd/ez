<template>
	<div v-show="role.name == 'ADMINSTRATOR'">
		<div class="inner-cont">
			<div class="row">
				<div class="col-md-12">
					<div class="body-bl-st">
						<ul class="navCs">
						    <li class="active">
						    	<router-link :to="{ name: 'createPost'}" tag="a">Đăng bài</router-link>
						    </li>
						    <li>
						    	<router-link :to="{ name: 'publishedPost'}" tag="a">Bài viết đã đăng</router-link>
						    </li>
						    <li>
						    	<router-link :to="{ name: 'schedulePost'}" tag="a">Bài viết đã lên lịch</router-link>
						    </li>
						    <li v-for="(tab, index) in tabs">
						    	<router-link :to="{ name: tab.name, params: { id: tab.id }}" tag="a">Chỉnh sửa</router-link>
						    </li>
						</ul>

						<div class="tab-content">
					    	<div class="innerBox">
					    		<router-view></router-view>	
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">

	import {mapGetters} from 'vuex'
	import { EventBus } from '../../helpers/bus'

	export default {
		name: 'extensions',
		computed: {
			...mapGetters([
				'role',
				'user'
			])
		},
		data () {
			return {
				tabs: [] // tabs:[{name: ,id: }]
			}
		},
		mounted () {
			EventBus.$on('addTabEdit', (tab) => {
				this.tabs = []
				this.tabs.push(tab)
			})
			EventBus.$on('clearTabEdit', () => {
				this.tabs = []
			})
		}
	}
</script>
<style type="text/css" scoped>
	.innerBox {
		margin: 20px 0;
	}
	.navCs {
		margin-bottom: 0;
		padding-left: 0;
		list-style: none;
		border-bottom: 1px solid #ddd;
		display: flex;
		justify-content: flex-start;
	}
	.navCs li {
		margin-bottom: -1px;
	}
	.navCs li a {
		padding: 10px 15px;
		line-height: 20px;
		border-radius: 4px 4px 0 0;
		color: #555;
	}
	.navCs li a:hover {
		background-color: #eee;
		border-color: #eee #eee #ddd;
	}
	.router-link-exact-active, .router-link-active, .router-link-exact-active:hover , .router-link-active:hover {
		background-color: #fff !important;
		border: 1px solid #ddd !important;
		border-bottom-color: transparent !important;
		cursor: default !important;
	}
</style>