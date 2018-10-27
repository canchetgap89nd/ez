<template>
	<div>
		<div class="panel panel-custom no-padding-top">
			<div class="panel-heading">
				<div class="tool-head">
					<ul class="tab-child-t1 container-tab-control">
						<li>
							<router-link tag="div" :to="{ name: 'topPost'}" class="tab-control tab-type-statistic">
								Bài viết
							</router-link>
						</li>
						<li>
							<router-link tag="div" :to="{ name: 'topProduct'}" class="tab-control tab-type-statistic">
								Sản phẩm
							</router-link>
						</li>
					</ul>
				</div>
			</div>
			<div class="panel-body no-padding-top">
				<div class="tab-content">
					<router-view></router-view>
				</div>
			</div>
		</div>
	</div>
</template>