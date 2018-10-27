<template>
	<div class="modal-content" v-if="isLoading">
		<circle-load></circle-load>
	</div>
	<div class="modal-content" v-else>
		<div class="modal-header">
			<button type="button" class="close" @click="reset()">&times;</button>
            <h4 class="modal-title text-center">
            	<span class="title-modal-cus">LỊCH SỬ XUẤT - NHẬP SẢN PHẨM</span>
            </h4>
        </div>
		<div class="modal-body">
        	<table class="table table-striped">
        		<thead>
        			<tr>
        				<th class="text-center">Thời gian</th>
        				<th class="text-center">Hành động</th>
        				<th class="text-center">Thay đổi</th>
        				<th class="text-center">Tồn kho sau thay đổi</th>
        			</tr>
        		</thead>
        		<tbody>
        			<tr v-for="item in items">
        				<td class="text-center">
        					{{ formatTime(item.created_at, "HH:mm DD/MM/YYYY") }}
        				</td>
        				<td class="text-center">
        					<span class="txt-color-green" v-if="item.status == 'IMPORT'">Nhập</span>
        					<span class="txt-color-red" v-else>Xuất</span>
        				</td>
        				<td class="text-center">
        					<span v-if="item.status == 'IMPORT'">+ {{ item.pivot.quantity_prod }}</span>
        					<span v-else>- {{ item.pivot.quantity_ex }}</span>
        				</td>
        				<td class="text-center">
        					<span v-if="item.status == 'IMPORT'">{{ item.pivot.inventory_prod + item.pivot.quantity_prod}}</span>
        					<span v-else>{{ item.pivot.inventory_ex }}</span>
        				</td>
        			</tr>
        		</tbody>
        	</table>
        </div>
	</div>
</template>

<script type="text/javascript">

	import { get } from '../../helpers/send'
	import { EventBus } from '../../helpers/bus'
	import Circle from '../loading/Circle.vue'
	import {momentLocale} from '../../helpers/momentfix'

	export default {
		components: {
			'circle-load': Circle,
		},
		data() {
			return {
				isLoading: false,
				items: []
			}
		},
		mounted() {
			EventBus.$on('historyProduct', (id) => {
				this.getHistory(id);
			})
		},
		methods: {
			getHistory(id) {
				$("#changeHistoryProduct").modal("show");
				this.isLoading = true;
				get('/api/product/history/changes/'+id)
				.then((res) => {
					this.items = res.data;
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải lịch sử xuất nhập kho','error');
				})
			},
			reset() {
				this.items = [];
				this.isLoading = false;
				$("#changeHistoryProduct").modal("hide");
			},
			formatTime(time, formater) {
				return momentLocale(time).format(formater);
			}
		},
		beforeDestroy () {
			EventBus.$off('historyProduct')
		}
	}
</script>