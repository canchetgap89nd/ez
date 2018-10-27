<template>
	<div style="width: 100%; height: 100%">
		<div class="inner-cl-cus" id="innerClCusPanel">
			<ul class="tabs-cus">
			    <li class="active"><a class="tab-citem" data-toggle="tab" href="#board-info">
			    	Thông tin
			    </a></li>
			    <li><a data-toggle="tab" class="tab-citem" href="#board-order">Tạo đơn</a></li>
		  	</ul>
			<div class="box-cl-cus tab-content">
				<div class="tab-pane fade in active" id="board-info">
					<info-cus></info-cus>
				</div>
				<div class="tab-pane fade in" id="board-order">
					<bill-cus></bill-cus>
				</div>
			</div>
		</div>
		<span class="icon-collapse collapse-cl-cus" @click="toggleCustomerPanel()">
			<i class="fa fa-angle-double-left" id="icShowCusPanel"></i>
		</span>
	</div>
</template>
<script type="text/javascript">
	
	import BasicInfoCus from './BasicInfoCus.vue'
	import BillInfoCus from './BillInfoCus.vue'
	import { EventBus } from '../../../helpers/bus'

	export default {
		components: {
			'info-cus': BasicInfoCus,
			'bill-cus': BillInfoCus
		},
		data() {
			return {
				showPanel: false
			}
		},
		methods: {
			toggleCustomerPanel() {
				this.showPanel = !this.showPanel
				if (this.showPanel) {
					$('#icShowCusPanel').addClass('fa-rotate-180')
					$('#ic_edithead_chat').addClass('fa-rotate-180')
					$('#checkboxToggle').prop('checked', true)
					$("#innerClCusPanel").show()
				}else{
					$('#icShowCusPanel').removeClass('fa-rotate-180')
					$('#ic_edithead_chat').removeClass('fa-rotate-180')
					$('#checkboxToggle').prop('checked', false)
					$("#innerClCusPanel").hide()
				}
			}
		}
	}
</script>
<style type="text/css">
	.ss-main .ss-content .ss-list {
	    max-height: 130px;
	}
	.item-select {
	    width: 100px;
	    float: left;
	    margin-left: 3px;
	}
	.sale-label {
	    position: absolute;
	    top: 15px;
	    font-size: 10px;
	    color: #E74C3C;
	    right: 60px;
	}
	.btn_qAdd_product {
	    position: absolute;
	    top: 13px;
	    right: 12px;
	    font-size: 16px;
	}
</style>