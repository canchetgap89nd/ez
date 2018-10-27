<template>
	<div>
		<product-panel></product-panel>
	</div>
</template>

<script type="text/javascript">
	
	import { get } from '../../helpers/send'
	import ProductPanel from './ProductPanel.vue'

	export default {
		components: {
			'product-panel': ProductPanel
		},
		mounted() {
			document.title = 'Sản phẩm';
			get('/api/product/sumary/info')
			.then((res) => {
				this.$store.commit('upProps', res.data.props);
				this.$store.commit('upCates', res.data.cates);
			})
		}
	}
</script>