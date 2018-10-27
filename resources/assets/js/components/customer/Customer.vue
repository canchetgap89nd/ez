<template>
	<div>
		<customer-panel></customer-panel>
	</div>
</template>

<script type="text/javascript">	
	import CustomerPanel from './CustomerPanel.vue'

	export default {
		components: {
			'customer-panel': CustomerPanel
		},
		mounted () {
			document.title = 'Khách hàng';
		}
	}
</script>