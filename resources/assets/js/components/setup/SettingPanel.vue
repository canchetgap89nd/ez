<template>
	<div>
		<slide-bar></slide-bar>
		<div class="set-cont-cs">
			<router-view></router-view>
		</div>
	</div>
</template>

<script type="text/javascript">
	
	import SlideBar from './SlideBar.vue'

	export default {
		components: {
			'slide-bar': SlideBar
		}
	}
</script>