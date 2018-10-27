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
		<div class="box-chart">
			<canvas id="communicateChart" width="400" height="400"></canvas>
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

	export default {
		data() {
			const hSpacing = '15px';
			return {
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
		      	},
		      	pageId: '',
		      	typeMode: 1,
		      	conversChart: null,
				dataConvers: [],
				times: [],
			}
		},
		mounted() {
			var ctx3 = document.getElementById("communicateChart").getContext("2d");
			this.conversChart = new Chart(ctx3, {
			    type: 'line',
			    data: {
	                labels: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"],
	                datasets: [{
	                    label: "Bình luận bởi khách",
	                    backgroundColor: '#b50e0e',
	                    borderColor: '#ef1313',
	                    data: [],
	                    fill: false,
	                }, {
	                    label: "Bình luận bởi page",
	                    fill: false,
	                    backgroundColor: '#055622',
	                    borderColor: '#00b140',
	                    data: [],
	                }, {
	                    label: "Tin nhắn bởi khách",
	                    fill: false,
	                    backgroundColor: '#caba03',
	                    borderColor: '#f0de18',
	                    data: [],
	                }]
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
			                ticks: {
			                    beginAtZero:true
			                }
			            }]
			        },
			        title: {
			            display: true,
			            text: 'Thống kê số lượng trên trang',
			            fontSize: 20
			        },
			        legend: {
			        	display: true,
			        	position: 'bottom'
			        }
			    },
			});
			this.selectedDate.start = momentLocale().subtract(7, 'days').format('YYYY-MM-DD');
			this.selectedDate.end = momentLocale().format('YYYY-MM-DD');
			this.getDataConversation();
		},
		computed: {
			pages() {
				return this.$store.state.pages;
			}
		},
		watch: {
			pageId() {
				this.getDataConversation();
			},
			typeMode() {
				this.getDataConversation();
			},
			selectedDate() {
				this.getDataConversation();
			}
		},
		methods: {
			getDataConversation() {
				let timeFrom = momentLocale(this.selectedDate.start).format('YYYY-MM-DD');
				let timeTo = momentLocale(this.selectedDate.end).format('YYYY-MM-DD');
				new Promise((resolve, reject) => {
					get('../../api/statistic/conversations'+
						'?pageId='+this.pageId+
						'&timeFrom='+timeFrom+
						'&timeTo='+timeTo+
						'&typeMode='+this.typeMode)
					.then((res) => {
						this.dataConvers = res.data;
						this.updateDateChart();
					})
					.catch((err) => {
						if (err.response.status == 302) {
							notifier.alert(err.response.data.message);
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
				let timeIndex = momentLocale(this.selectedDate.start).format('YYYY-MM-DD');
				timeIndex = momentLocale(timeIndex).startOf('day');
				this.dataConvers.forEach((val, ind) => {
					arr1.push(val.commentsOfCus);
					arr2.push(val.commentsOfPage);
					arr3.push(val.messagesOfCus);
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
				this.times = labels;
				this.conversChart.data.labels = labels;
				this.conversChart.data.datasets[0].data = arr1;
				this.conversChart.data.datasets[1].data = arr2;
				this.conversChart.data.datasets[2].data = arr3;
				this.conversChart.update();
			}
		}
	}
</script>