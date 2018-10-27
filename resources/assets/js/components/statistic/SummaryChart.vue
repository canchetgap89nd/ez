<template>
	<div>
		<div style="display: inline-block; width: 100%">
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
				<select class="form-control form-control-cus picker-cus select-cus" title="So với" v-model="typeCompare">
					<option :value="1">Tháng trước</option>
					<option :value="2">Năm trước</option>
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
		<div style="display: inline-block; width: 100%; margin-top: 15px;">
			<div class="summary-column">
				<div class="title-summary">
					<h2>Tóm tắt hoạt động trên trang</h2>
				</div>
				<div class="summary-content">
					<div class="box-summary-enity">
						<div class="title-box-summary">
							<span class="text-inside">
								Tổng bình luận và tin nhắn trên trang
							</span>
						</div>
						<div class="content-box-summary">
							<div class="inner-box-summary">
								<div class="summary-properties pull-left">
									<h1 v-text="current.comments_count"></h1>
									<span>Bình luận</span>
								</div>
								<div class="summary-properties pull-right">
									<h1 v-text="current.messages_count"></h1>
									<span>Tin nhắn</span>
								</div>
							</div>
						</div>
					</div>
					<div class="box-summary-enity">
						<div class="title-box-summary">
							<span class="text-inside">
								Tổng giá trị đơn hàng bán được
							</span>
							<span class="up-down-percent" v-show="compareTo.products_count_sell">
								<i class="fa fa-caret-up" v-if="diffValueOrder > 0"></i>
								<i class="fa fa-caret-down" v-else></i>
								{{ diffValueOrder }}%
							</span>
						</div>
						<div class="content-box-summary">
							<div class="inner-box-summary">
								<div class="summary-properties pull-left">
									<h1 v-text="formatPrice(current.total_value_orders)"></h1>
									<span>VNĐ</span>
								</div>
							</div>
						</div>
					</div>
					<div class="box-summary-enity">
						<div class="title-box-summary">
							<span class="text-inside">
								Tổng sản phẩm bán được
							</span>
							<span class="up-down-percent" v-show="compareTo.total_value_orders">
								<i class="fa fa-caret-up" v-if="diffProduct > 0"></i>
								<i class="fa fa-caret-down" v-else></i>
								{{ diffValueOrder }}%
							</span>
						</div>
						<div class="content-box-summary">
							<div class="inner-box-summary">
								<div class="summary-properties pull-left">
									<h1 v-text="current.products_count_sell"></h1>
									<span>Sản phẩm</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="chart-board">
				<canvas id="summary-chart" width="600px" height="600px"></canvas>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import { get } from '../../helpers/send'
	import {momentLocale} from '../../helpers/momentfix'
	import {formatPrice} from '../../helpers/numeralfix'
	import { EventBus } from '../../helpers/bus'
	import calendarOption from '../../helpers/calendarOption'
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
				pageId: '',
				typeCompare: 1,
				summaryChart: null,
				current: {
					comments_count: 0,
					messages_count: 0,
					products_count_sell: 0,
					total_value_orders: 0
				},
				compareTo: {
					comments_count: 0,
					messages_count: 0,
					products_count_sell: 0,
					total_value_orders: 0
				},
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
				this.getSummary();
			},
			typeCompare() {
				this.getSummary();
			},
			selectedDate() {
				this.getSummary();
			}
		},
		computed: {
			pages() {
				return this.$store.state.pages;
			},
			diffComment() {
				let curr = this.current.comments_count;
				let compare = this.compareTo.comments_count;
				let minus = curr - compare;
				if (compare) {
					return minus/compare*100;
				}
				return minus;
			},
			diffMessage() {
				let curr = this.current.messages_count;
				let compare = this.compareTo.messages_count;
				let minus = curr - compare;
				if (compare) {
					return minus/compare*100;
				}
				return minus;
			},
			diffProduct() {
				let curr = this.current.products_count_sell;
				let compare = this.compareTo.products_count_sell;
				let minus = curr - compare;
				if (compare) {
					return minus/compare*100;
				}
				return minus;
			},
			diffValueOrder() {
				let curr = this.current.total_value_orders;
				let compare = this.compareTo.total_value_orders;
				let minus = curr - compare;
				if (compare) {
					return minus/compare*100;
				}
				return minus;
			}
		},
		mounted() {
			var ctx1 = document.getElementById("summary-chart").getContext("2d");
			this.summaryChart = new Chart(ctx1, {
			    type: 'bar',
			    data: {
			        labels: ["Mới", "Chờ giao hàng", "Hàng đang giao", "Đã giao hàng", "Khách trả lại", "Đã nhận lại hàng", "Đơn bị hủy"],
			        datasets: [{
			            label: 'đơn',
			            data: [0, 0, 0, 0, 0, 0, 0, 0],
			            backgroundColor: [
			                '#00b140',
			                '#00b140',
			                '#00b140',
			                '#00b140',
			                '#00b140',
			                '#00b140',
			                '#00b140',
			                '#00b140'
			            ]
			        }]
			    },
			    options: {
			    	layout: {
			            padding: {
			                left: 0,
			                right: 0,
			                top: 0,
			                bottom: 190
			            }
			        },
			    	animation: {
			            onComplete: function() {
			            	var ctx = this.chart.ctx;
					        ctx.font = '15px FontAwesome';
					        ctx.fillStyle = "#ff0000"; // or #00b140
					        ctx.textAlign = "center";
					        ctx.textBaseline = "bottom";

					      //   this.data.datasets.forEach(function (dataset) {
					        	
					      //       for (var i = 0; i < dataset.data.length; i++) {
							    //   if(dataset.hidden === true && dataset._meta[Object.keys(dataset._meta)[0]].hidden !== false){ continue; }
							    //   var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model;
							    //   if(dataset.data[i] !== null){
							    //     ctx.fillText('\uf0d7' + ' ' + dataset.data[i], model.x - 1, model.y - 5); //or icon uf0d8
							    //   }
							    // }
					      //   })
			            }
			        },
			        scales: {
			            yAxes: [{
			                ticks: {
			                    beginAtZero:true
			                }
			            }]
			        },
			        title: {
			            display: true,
			            text: 'Tình trạng đơn hàng trên trang',
			            fontSize: 20
			        },
			        legend: {
			        	display: false
			        },
		      		responsive: true,
		      		maintainAspectRatio: false
			    },
			});
			this.selectedDate.start = momentLocale().subtract(7, 'days').format('YYYY-MM-DD');
			this.selectedDate.end = momentLocale().format('YYYY-MM-DD');
			this.getSummary();
		},
		methods: {
			formatPrice(price) {
				return formatPrice(price);
			},
			getSummary() {
				let timeFrom = momentLocale(this.selectedDate.start).format('YYYY-MM-DD');
				let timeTo = momentLocale(this.selectedDate.end).format('YYYY-MM-DD');
				new Promise((resolve, reject) => {
					get('../../api/statistic/summary'+
						'?pageId='+this.pageId+
						'&timeFrom='+timeFrom+
						'&timeTo='+timeTo+
						'&typeCompare='+this.typeCompare)
					.then((res) => {
						this.current = res.data.current;
						if (res.data.compareTo.length) {
							this.compareTo = res.data.compareTo;
						}
						this.updateDataOfChart();
					})
	    		})
	    		notifier.asyncBlock(Promise.resolve("all done"));
			},
			updateDataOfChart() {
				this.summaryChart.data.datasets[0].data.forEach((val, ind) => {
					switch (ind) {
						case 0:
							this.summaryChart.data.datasets[0].data[ind] = this.current.sort_orders['newOrder'];
							break;
						case 1:
							this.summaryChart.data.datasets[0].data[ind] = this.current.sort_orders['confirmOrder'];
							break;
						case 2:
							this.summaryChart.data.datasets[0].data[ind] = this.current.sort_orders['sendingOrder'];
							break;
						case 3:
							this.summaryChart.data.datasets[0].data[ind] = this.current.sort_orders['sentOrder'];
							break;
						case 4:
							this.summaryChart.data.datasets[0].data[ind] = this.current.sort_orders['refundingOrder'];
							break;
						case 5:
							this.summaryChart.data.datasets[0].data[ind] = this.current.sort_orders['refundedOrder'];
							break;
						case 6:
							this.summaryChart.data.datasets[0].data[ind] = this.current.sort_orders['cancelOrder'];
							break;
						default: break;
					}
				})
				this.summaryChart.update();
			}
		}
	}
</script>
