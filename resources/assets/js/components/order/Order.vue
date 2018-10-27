<template>
	<div>
		<order-panel></order-panel>
	</div>
</template>
<script type="text/javascript">
		
	import OrderPanel from './OrderPanel.vue'

	export default {
		components: {
			'order-panel': OrderPanel
		},
		mounted () {
			document.title = 'Đơn hàng';
		}
	}

</script>