<template>
	<div style="position: relative">
		<div style="display: inline-block; width: 100%; margin-bottom: 15px;">
			<div class="select-page-cont item-control-tool no-margin-top">
				<select class="form-control form-control-cus picker-cus select-cus" v-model="pageId">
					<option value="">Tất cả</option>
					<option value="ALL">Tất cả Fanpage</option>
					<option value="NOTPAGE">Tất cả ngoài Fanpage</option>
					<option :value="page.id" v-for="page in pages">{{ page.page_name }}</option>
				</select>
				<i class="fa fa-caret-down icon-dropdown-sel"></i>
			</div>
			<div class="select-page-cont item-control-tool no-margin-top">
				<select class="form-control form-control-cus picker-cus select-cus" title="Thống kê" v-model="typeMode">
					<option :value="1">Theo ngày</option>
					<option :value="2">Theo tháng</option>
					<option :value="3">Theo năm</option>
				</select>
				<i class="fa fa-caret-down icon-dropdown-sel"></i>
			</div>
			<div class="date-pick-cont item-control-tool no-margin-top">
				<v-date-picker class="datepicker-cs"
				  mode='range'
				  v-model='selectedDate'
				  show-caps :theme-styles='themeStyles' is-double-paned>
				</v-date-picker>
			</div>
		</div>
		<div>
			<canvas id="sales-chart" height="400px" width="400px"></canvas>
		</div>
		<div class="tb-container-cs">
			<table class="table table-hover">
				<thead>
					<tr>
						<th class="text-center">Thời gian</th>
						<th class="text-center">
							Doanh số <br>
							(X)
						</th>
						<th class="text-center">
							Khuyến mãi <br>
							(a)
						</th>
						<th class="text-center">
							Giảm giá <br>
							(b)
						</th>
						<th class="text-center">
							Doanh thu <br>
							(Y) = (X) - (a) - (b)
						</th>
						<th class="text-center">
							Giá vốn <br>
							(c)
						</th>
						<th class="text-center">
							Lợi nhuận <br>
							(Z) = (Y) - (c)
						</th>
						<th class="text-center">
							Vận chuyển
						</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(time, index) in times">
						<td class="text-center">
							{{time}}
						</td>
						<td class="text-center">
							{{ formatPrice(dataSales[index].backAmount) }}
						</td>
						<td class="text-center">
							{{ formatPrice(dataSales[index].saleAmount) }}
						</td>
						<td class="text-center">
							{{ formatPrice(dataSales[index].discount) }}
						</td>
						<td class="text-center">
							{{ formatPrice(dataSales[index].revenue) }}
						</td>
						<td class="text-center">
							{{ formatPrice(dataSales[index].originVal) }}
						</td>
						<td class="text-center">
							{{ formatPrice(dataSales[index].profit) }}
						</td>
						<td class="text-center">
							{{ formatPrice(dataSales[index].shipFee) }}
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th class="text-center">Trung bình</th>
						<th class="text-center">{{ formatPrice(avgBackAmount) }}</th>
						<th class="text-center">{{ formatPrice(avgSaleAmount) }}</th>
						<th class="text-center">{{ formatPrice(avgDiscount) }}</th>
						<th class="text-center">{{ formatPrice(avgRevenue) }}</th>
						<th class="text-center">{{ formatPrice(avgOriginVal) }}</th>
						<th class="text-center">{{ formatPrice(avgProfit) }}</th>
						<th class="text-center">{{ formatPrice(avgShipFee) }}</th>
					</tr>
					<tr>
						<th class="text-center">Tổng</th>
						<th class="text-center">{{ formatPrice(totalBackAmount) }}</th>
						<th class="text-center">{{ formatPrice(totalSaleAmount) }}</th>
						<th class="text-center">{{ formatPrice(totalDiscount) }}</th>
						<th class="text-center">{{ formatPrice(totalRevenue) }}</th>
						<th class="text-center">{{ formatPrice(totalOriginVal) }}</th>
						<th class="text-center">{{ formatPrice(totalProfit) }}</th>
						<th class="text-center">{{ formatPrice(totalShipFee) }}</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import Chart from 'chart.js'
	import calendarOption from '../../helpers/calendarOption'
	import { get } from '../../helpers/send'
	import {momentLocale} from '../../helpers/momentfix'
	import {formatPrice} from '../../helpers/numeralfix'
	import Vue from 'vue';
	import VCalendar from 'v-calendar';
	import 'v-calendar/lib/v-calendar.min.css';
	import AWN from "awesome-notifications"
	var notifier = new AWN();

	Vue.use(VCalendar, {
  		firstDayOfWeek: 2,
  		locale: "vi-VN",
  		datePickerTintColor: "#00b140",
  		popoverContentOffset: 14
	});

	export default {
		data() {
			const hSpacing = '15px';
			return {
				salesChart: null,
				dataSales: [],
				times: [],
				pageId: '',
				typeMode: 1,
				themeStyles: {
			        wrapper: {
			          background: '#fff',
			          color: '#000',
			          border: '0',
			          borderRadius: '5px',
			          boxShadow: '0 4px 8px 0 rgba(0, 0, 0, 0.14), 0 6px 20px 0 rgba(0, 0, 0, 0.13)'
			        },
			        header: {
			          padding: `20px ${hSpacing}`,
			        },
			        headerHorizontalDivider: {
			          borderTop: 'solid rgba(255, 255, 255, 0.2) 1px',
			          width: '80%',
			        },
			        weekdays: {
			          padding: `20px ${hSpacing} 5px ${hSpacing}`,
			        },
			        weeks: {
			          padding: `0 ${hSpacing} ${hSpacing} ${hSpacing}`,
			        },
		      	},
		      	selectedDate: {
			        start: '',
			        end: ''
		      	}
			}
		},
		watch: {
			pageId() {
				this.getDataSale();
			},
			typeMode() {
				this.getDataSale();
			},
			selectedDate() {
				this.getDataSale();
			}
		},
		mounted() {
			var ctx2 = document.getElementById("sales-chart").getContext("2d");
			this.salesChart = new Chart(ctx2, {
			    type: 'line',
			    data: {
	                labels: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
	                datasets: [
	                	{
		                    label: "Doanh số",
		                    backgroundColor: '#ff6384',
		                    borderColor: '#ff6384',
		                    data: [],
		                    fill: false,
		                    yAxisID: 'A'
	                	},
	                	{
		                    label: "Khuyến mại",
		                    backgroundColor: '#36a2eb',
		                    borderColor: '#36a2eb',
		                    data: [],
		                    fill: false,
		                    yAxisID: 'A'
	                	},
	                	{
		                    label: "Giảm giá",
		                    backgroundColor: '#cc65fe',
		                    borderColor: '#cc65fe',
		                    data: [],
		                    fill: false,
		                    yAxisID: 'A'
	                	},
	                	{
		                    label: "Doanh thu",
		                    backgroundColor: '#ffce56',
		                    borderColor: '#ffce56',
		                    data: [],
		                    fill: false,
		                    yAxisID: 'A'
	                	},
	                	{
		                    label: "Giá vốn",
		                    backgroundColor: '#B9770E',
		                    borderColor: '#B9770E',
		                    data: [],
		                    fill: false,
		                    yAxisID: 'A'
	                	},
	                	{
		                    label: "Lợi nhuận",
		                    backgroundColor: '#5B2C6F',
		                    borderColor: '#5B2C6F',
		                    data: [],
		                    fill: false,
		                    yAxisID: 'A'
	                	},
	                	{
		                    label: "Vận chuyển",
		                    backgroundColor: '#1A5276',
		                    borderColor: '#1A5276',
		                    data: [],
		                    fill: false,
		                    yAxisID: 'A'
	                	}
	                ]
	            },
			    options: {
			    	layout: {
			            padding: {
			                left: 0,
			                right: 0,
			                top: 0,
			                bottom: 715
			            }
			        },
			        scales: {
			            yAxes: [{
					        id: 'A',
					        type: 'linear',
					        position: 'left',
					        scaleLabel: {
						        display: true,
						        labelString: 'Doanh thu - VNĐ'
					      	}
					      }]
			        },
			        title: {
			            display: true,
			            text: 'Thống kê hoạt động bán hàng trên trang',
			            fontSize: 20
			        },
			        legend: {
			        	display: true
			        }
			    },
			});
			this.selectedDate.start = momentLocale().subtract(7, 'days').format('YYYY-MM-DD');
			this.selectedDate.end = momentLocale().format('YYYY-MM-DD');
			this.getDataSale();
		},
		computed: {
			pages() {
				return this.$store.state.pages;
			},
			totalBackAmount() {
				let total = 0;
				this.dataSales.forEach((val) => {
					total += val.backAmount;
				})
				return total;
			},
			avgBackAmount() {
				let total = 0;
				this.dataSales.forEach((val) => {
					total += val.backAmount;
				})
				let lg = this.times.length ? this.times.length : 1;
				return total/lg;
			},
			totalSaleAmount() {
				let total = 0;
				this.dataSales.forEach((val) => {
					total += val.saleAmount;
				})
				return total;
			},
			avgSaleAmount() {
				let total = 0;
				this.dataSales.forEach((val) => {
					total += val.saleAmount;
				})
				let lg = this.times.length ? this.times.length : 1;
				return total/lg;
			},
			totalDiscount() {
				let total = 0;
				this.dataSales.forEach((val) => {
					total += val.discount;
				})
				return total;
			},
			avgDiscount() {
				let total = 0;
				this.dataSales.forEach((val) => {
					total += val.discount;
				})
				let lg = this.times.length ? this.times.length : 1;
				return total/lg;
			},
			totalRevenue() {
				let total = 0;
				this.dataSales.forEach((val) => {
					total += val.revenue;
				})
				return total;
			},
			avgRevenue() {
				let total = 0;
				this.dataSales.forEach((val) => {
					total += val.revenue;
				})
				let lg = this.times.length ? this.times.length : 1;
				return total/lg;
			},
			totalOriginVal() {
				let total = 0;
				this.dataSales.forEach((val) => {
					total += val.originVal;
				})
				return total;
			},
			avgOriginVal() {
				let total = 0;
				this.dataSales.forEach((val) => {
					total += val.originVal;
				})
				let lg = this.times.length ? this.times.length : 1;
				return total/lg;
			},
			totalProfit() {
				let total = 0;
				this.dataSales.forEach((val) => {
					total += val.profit;
				})
				return total;
			},
			avgProfit() {
				let total = 0;
				this.dataSales.forEach((val) => {
					total += val.profit;
				})
				let lg = this.times.length ? this.times.length : 1;
				return total/lg;
			},
			totalShipFee() {
				let total = 0;
				this.dataSales.forEach((val) => {
					total += val.shipFee;
				})
				return total;
			},
			avgShipFee() {
				let total = 0;
				this.dataSales.forEach((val) => {
					total += val.shipFee;
				})
				let lg = this.times.length ? this.times.length : 1;
				return total/lg;
			}
		},
		methods: {
			formatPrice(price) {
				return formatPrice(price);
			},
			getDataSale() {
				let timeFrom = momentLocale(this.selectedDate.start).format('YYYY-MM-DD');
				let timeTo = momentLocale(this.selectedDate.end).format('YYYY-MM-DD');
				new Promise((resolve, reject) => {
					get('../../api/statistic/sales'+
						'?timeFrom='+timeFrom+
						'&timeTo='+timeTo+
						'&typeMode='+this.typeMode+
						'&pageId='+this.pageId)
					.then((res) => {
						this.dataSales = res.data;
						this.updateDateChart();
					})
					.catch((err) => {
						if (err.response.status == 302) {
							$.notify(err.response.data.message, 'error');
						} else {
							$.notify('Lỗi tải dữ liệu', 'error');
						}
					})
	    		})
	    		notifier.asyncBlock(Promise.resolve("all done"));
			},
			updateDateChart() {
				let labels = [];
				let arr1 = [];
				let arr2 = [];
				let arr3 = [];
				let arr4 = [];
				let arr5 = [];
				let arr6 = [];
				let arr7 = [];
				let timeIndex = momentLocale(this.selectedDate.start).format('YYYY-MM-DD');
				timeIndex = momentLocale(timeIndex).startOf('day');
				this.dataSales.forEach((val, ind) => {
					arr1.push(val.backAmount);
					arr2.push(val.saleAmount);
					arr3.push(val.discount);
					arr4.push(val.revenue);
					arr5.push(val.originVal);
					arr6.push(val.profit);
					arr7.push(val.shipFee);
					if (this.typeMode == 1) {
						labels.push(momentLocale(timeIndex).format('DD/MM/YYYY'));
						timeIndex = momentLocale(timeIndex).add(1, 'days');
					} else 
					if (this.typeMode == 2) {
						labels.push(momentLocale(timeIndex).format('MM/YYYY'));
						timeIndex = momentLocale(timeIndex).add(1, 'months');
					} else
					if (this.typeMode == 3) {
						labels.push(momentLocale(timeIndex).format('YYYY'));
						timeIndex = momentLocale(timeIndex).add(1, 'years');
					}
				})
				this.salesChart.data.labels = labels;
				this.times = labels;
				this.salesChart.data.datasets[0].data = arr1;
				this.salesChart.data.datasets[1].data = arr2;
				this.salesChart.data.datasets[2].data = arr3;
				this.salesChart.data.datasets[3].data = arr4;
				this.salesChart.data.datasets[4].data = arr5;
				this.salesChart.data.datasets[5].data = arr6;
				this.salesChart.data.datasets[6].data = arr7;
				this.salesChart.update();
			}
		}
	}

</script>