<template>
	<div>
		<div class="cont-of-tab">
			<div class="container">
				<div class="row">
					<ul class="tab-head tab-statistic container-tab-control">
						<li>
							<router-link tag="div" :to="{ name: 'chartBoard'}" class="tab-control tab-order-control">
								Thống kê
							</router-link>
						</li>
						<li>
							<router-link tag="div" :to="{ name: 'topBoard'}" class="tab-control tab-order-control">
								Top 10
							</router-link>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="tab-content">
					<router-view></router-view>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		mounted () {
			document.title = 'Thống kê';
		}
	}
</script>