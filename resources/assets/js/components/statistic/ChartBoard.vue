<template>
	<div>
		<div class="panel panel-custom no-padding-top">
			<div class="panel-heading">
				<div class="tool-head">
					<ul class="tab-child-t1 container-tab-control" target-container="container-chart-board" id="tab-statistic-overview">
						<li @click="tabActive = 'SUMMARY'">
							<router-link tag="div" :to="{ name: 'summaryChart'}" class="tab-control tab-type-statistic">
								Tổng quan
							</router-link>
						</li>
						<li  @click="tabActive = 'SALES'">
							<router-link tag="li" :to="{ name: 'saleBoard'}" class="tab-control tab-type-statistic">
								Bán hàng
							</router-link>
						</li>
						<li>
							<router-link tag="li" :to="{ name: 'conversationBoard'}" class="tab-control tab-type-statistic">
								Hội thoại
							</router-link>
						</li>
						<li>
							<router-link tag="li" :to="{ name: 'staffBoard'}" class="tab-control tab-type-statistic">
								Nhân viên
							</router-link>
						</li>
					</ul>
				</div>
			</div>
			<div class="panel-body">
				<div class="tab-content">
					<router-view></router-view>
				</div>
			</div>
		</div>
	</div>
</template>