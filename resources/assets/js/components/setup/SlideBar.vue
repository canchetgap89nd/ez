<template>
	<div class="sidenav">
		<div class="innerSidenav">
			<div class="blockSidenav">
				<h3 class="title-block">
					<i class="fa fa-caret-right" v-show="checkRouter('basic', $route.path)"></i>
					<span>Cấu hình</span>
				</h3>
				<div class="boxSubMenu">
					<router-link tag="a" :to="{ name: 'generalSet'}" class="ttChild1">Cài đặt chung</router-link>
					<router-link tag="a" :to="{ name: 'groupSet'}" class="ttChild1">Nhãn khách hàng</router-link>
					<router-link tag="a" :to="{ name: 'quickReplySet'}" class="ttChild1">Trả lời nhanh</router-link>
					<router-link tag="a" :to="{ name: 'blackListSet'}" class="ttChild1">Danh sách đen</router-link>
				</div>
			</div>
			<div class="blockSidenav">
				<h3 class="title-block">
					<i class="fa fa-caret-right" v-show="checkRouter('advance', $route.path)"></i>
					<span>Nâng cao</span>
				</h3>
				<div class="boxSubMenu">
					<router-link v-if="role.name == 'ADMINSTRATOR' || role.name == 'MANAGER'" tag="a" :to="{ name: 'FilterInfoSet'}" class="ttChild1">Lọc thông tin</router-link>
					<router-link v-if="role.name == 'ADMINSTRATOR' || role.name == 'MANAGER'" tag="a" :to="{ name: 'extensions'}" class="ttChild1">Lên lịch đăng bài</router-link>
					<router-link v-if="role.name == 'ADMINSTRATOR' || role.name == 'MANAGER'" tag="a" :to="{ name: 'setupAutoReply'}" class="ttChild1">Tự động trả lời</router-link>
					<router-link v-if="role.name == 'ADMINSTRATOR' || role.name == 'MANAGER'" tag="a" :to="{ name: 'MixSet'}" class="ttChild1">Tính năng phát triển</router-link>
				</div>
			</div>
			<div class="blockSidenav" v-if="role.name == 'ADMINSTRATOR'">
				<h3 class="title-block">
					<i class="fa fa-caret-right" v-show="checkRouter('account', $route.path)"></i>
					<span>Tài khoản</span>
				</h3>
				<div class="boxSubMenu">
					<router-link tag="a" :to="{ name: 'setupAccount'}" class="ttChild1" title="Nâng cấp và gia hạn tài khoản">Nâng cấp TK</router-link>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import {mapGetters} from 'vuex'

	export default {
		computed: {
			...mapGetters([
				'role'
			])
		},
		methods: {
			checkRouter(box, path) {
				var regx = new RegExp(box, 'g')
				var has = path.match(regx)
				var result = false
				if (has) {
					result = true
				}
				return result
			}
		}
	}
</script>
<style type="text/css">
	.sidenav a.router-link-active {
	    color: #00b140 !important;
	}
</style>