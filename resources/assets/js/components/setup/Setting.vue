<template>
	<div>
		<setting-panel></setting-panel>
	</div>
</template>
<script type="text/javascript">
	
	import SettingPanel from './SettingPanel.vue'

	export default {
		components: {
			'setting-panel': SettingPanel
		},
		mounted () {
			document.title = 'Cài đặt';
		}
	}
</script>