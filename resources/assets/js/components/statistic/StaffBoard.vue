<template>
	<div>
		<div style="display: inline-block; width: 100%; margin-bottom: 15px;">
			<div class="select-page-cont item-control-tool no-margin-top">
				<select class="form-control form-control-cus picker-cus select-cus" v-model="pageId">
					<option value="">Tất cả</option>
					<option :value="page.id" v-for="page in pages">{{ page.page_name }}</option>
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
		<div class="box-chart">
			<div class="col-lg-6">
				<div class="two-pie-line">
					<canvas id="replyChart" width="400" height="400"></canvas>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="two-pie-line">
					<canvas id="revenueChart" width="400" height="400"></canvas>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import { get } from '../../helpers/send'
	import {momentLocale} from '../../helpers/momentfix'
	import AWN from "awesome-notifications"
	var notifier = new AWN();
	import Vue from 'vue';
	import VCalendar from 'v-calendar';
	import 'v-calendar/lib/v-calendar.min.css';

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
				replyChart: null,
				revenueChart: null,
				dataInteractive: null,
				dataRevenue: null,
				pageId: '',
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
		mounted() {
			var ctx4 = document.getElementById("replyChart").getContext("2d");
			this.replyChart = new Chart(ctx4,{
			    type: 'pie',
			    type: 'pie',
			    data: {
				    datasets: [{
				        data: [],
					    backgroundColor: []
				    }],

				    labels: []
				},
				options: {
					title: {
			            display: true,
			            text: 'Tỷ lệ trả lời theo nhân viên',
			            fontSize: 20
			        },
			        legend: {
			        	display: true,
			        	position: 'bottom'
			        },
			        animation: {
			            onComplete: function() {
			            	var ctx = this.chart.ctx;
					        ctx.font = '15px Roboto';
					        ctx.fillStyle = "#fff"; // or #00b140
					        ctx.textAlign = "center";
					        ctx.textBaseline = "bottom";

					        this.data.datasets.forEach(function (dataset) {
					        	
					            for (var i = 0; i < dataset.data.length; i++) {

							      	var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model,
							              total = dataset._meta[Object.keys(dataset._meta)[0]].total,
							              mid_radius = model.innerRadius + (model.outerRadius - model.innerRadius)/2,
							              start_angle = model.startAngle,
							              end_angle = model.endAngle,
							              mid_angle = start_angle + (end_angle - start_angle)/2;

					          		var x = mid_radius * Math.cos(mid_angle);
						          	var y = mid_radius * Math.sin(mid_angle);



						          	var val = dataset.data[i];
						          	var percent = String(Math.round(val/total*100)) + "%";

						          	if(val != 0) {
							            // Display percent in another line, line break doesn't work for fillText
							            ctx.fillText(percent, model.x + x, model.y + y + 15);
						          	}
							    }
					        })
			            }
			        }
				}
			});
			var ctx5 = document.getElementById("revenueChart").getContext("2d");
			this.revenueChart = new Chart(ctx5,{
			    type: 'pie',
			    data: {
				    datasets: [{
				        data: [],
					    backgroundColor: []
				    }],

				    labels: []
				},
				options: {
					title: {
			            display: true,
			            text: 'Tỷ lệ doanh thu theo nhân viên',
			            fontSize: 20
			        },
			        legend: {
			        	display: true,
			        	position: 'bottom'
			        },
			        animation: {
			            onComplete: function() {
			            	var ctx = this.chart.ctx;
					        ctx.font = '15px Roboto';
					        ctx.fillStyle = "#fff"; // or #00b140
					        ctx.textAlign = "center";
					        ctx.textBaseline = "bottom";

					        this.data.datasets.forEach(function (dataset) {
					        	
					            for (var i = 0; i < dataset.data.length; i++) {

							      	var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model,
							              total = dataset._meta[Object.keys(dataset._meta)[0]].total,
							              mid_radius = model.innerRadius + (model.outerRadius - model.innerRadius)/2,
							              start_angle = model.startAngle,
							              end_angle = model.endAngle,
							              mid_angle = start_angle + (end_angle - start_angle)/2;

					          		var x = mid_radius * Math.cos(mid_angle);
						          	var y = mid_radius * Math.sin(mid_angle);



						          	var val = dataset.data[i];
						          	var percent = String(Math.round(val/total*100)) + "%";

						          	if(val != 0) {
							            // Display percent in another line, line break doesn't work for fillText
							            ctx.fillText(percent, model.x + x, model.y + y + 15);
						          	}
							    }
					        })
			            }
			        }
				}
			});

			this.selectedDate.start = momentLocale().subtract(7, 'days').format('YYYY-MM-DD');
			this.selectedDate.end = momentLocale().format('YYYY-MM-DD');
			this.getDataStaff();
		},
		computed: {
			pages() {
				return this.$store.state.pages;
			}
		},
		watch: {
			pageId() {
				this.getDataStaff();
			},
			selectedDate() {
				this.getDataStaff();
			}
		},
		methods: {
			getDataStaff() {
				let timeFrom = momentLocale(this.selectedDate.start).format('YYYY-MM-DD');
				let timeTo = momentLocale(this.selectedDate.end).format('YYYY-MM-DD');
				new Promise((resolve, reject) => {
					get('../../api/statistic/staffData'+
						'?pageId='+this.pageId+
						'&timeFrom='+timeFrom+
						'&timeTo='+timeTo)
					.then((res) => {
						this.dataInteractive = res.data.interactive;
						this.dataRevenue = res.data.revenue;
						this.updateChart();
					})
	    		})
	    		notifier.asyncBlock(Promise.resolve("all done"));
			},
			updateInteractiveChart() {
				let labels = [];
				let backgrounds = [];
				let data = [];
				this.dataInteractive.staffs.forEach((val) => {
					labels.push(val.staff.name);
					backgrounds.push(this.getColor());
					data.push(val.comments_count + val.messages_count);
				})
				labels.push('Fanpage');
				backgrounds.push(this.getColor());
				data.push(this.dataInteractive.pageOf);
				this.replyChart.data.labels = labels;
				this.replyChart.data.datasets[0].backgroundColor = backgrounds;
				this.replyChart.data.datasets[0].data = data;
				this.replyChart.update();
			},
			updateRevenueChart() {
				let labels = [];
				let backgrounds = [];
				let data = [];
				this.dataRevenue.staffs.forEach((val) => {
					labels.push(val.staff.name);
					backgrounds.push(this.getColor());
					data.push(val.revenue);
				})
				labels.push('Fanpage');
				backgrounds.push(this.getColor());
				data.push(this.dataRevenue.pageOf);
				this.revenueChart.data.labels = labels;
				this.revenueChart.data.datasets[0].backgroundColor = backgrounds;
				this.revenueChart.data.datasets[0].data = data;
				this.revenueChart.update();
			},
			getColor() {
			    return '#' + Math.random().toString(16).slice(2, 8);
		  	},
		  	updateChart() {
		  		this.updateInteractiveChart();
		  		this.updateRevenueChart();
		  	}
		}
	}
</script>