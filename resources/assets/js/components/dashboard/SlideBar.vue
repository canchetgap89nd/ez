<template>
	<div class="slide-bar">
		<ul class="tab-slidebar">
		 	<li>
		 		<a :class="{'item-bar active tooltip-cs': all, 'tooltip-cs item-bar': !all }" @click="filterConversation('ALL')">
		 			<i class="material-icons">update</i>
		 			<span class="tooltiptext tooltip-right">Tất cả</span>
		 		</a>
		 	</li>
		 	<li>
		 		<a href="javascript:;" :class="{'item-bar active tooltip-cs': unread, 'tooltip-cs item-bar': !unread }" @click="filterConversation('UNREAD')">
		 			<i class="material-icons">call_missed</i>
		 			<span class="tooltiptext tooltip-right">Chưa đọc</span>
		 		</a>
		 	</li>
		 	<li>
		 		<a href="javascript:;" :class="{'item-bar tooltip-cs active': comment, 'tooltip-cs item-bar': !comment }" @click="filterConversation('COMMENT')">
		 			<i class="material-icons">forum</i>
		 			<span class="tooltiptext tooltip-right">Bình luận</span>
		 		</a>
		 	</li>
		 	<li>
		 		<a href="javascript:;" :class="{'item-bar active tooltip-cs': message, 'tooltip-cs item-bar': !message }" @click="filterConversation('MESSAGE')">
		 			<i class="material-icons">email</i>
		 			<span class="tooltiptext tooltip-right">Tin nhắn</span>
		 		</a>
		 	</li>
		 	<li>
		 		<a href="javascript:;" :class="{'item-bar active tooltip-cs': phone, 'tooltip-cs item-bar': !phone }" @click="filterConversation('PHONE')">
		 			<i class="material-icons">phone_in_talk</i>
		 			<span class="tooltiptext tooltip-right">Có số ĐT</span>
		 		</a>
		 	</li>
		 	<li>
			  	<el-dropdown :hide-on-click="false" trigger="click">
				  	<span class="el-dropdown-link">
				    	<a href="javascript:;" :class="{'item-bar active tooltip-cs': postId, 'tooltip-cs item-bar': !postId}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				 			<i class="fa fa-facebook"></i>
				 			<span class="tooltiptext tooltip-right">Thuộc bài viết</span>
				 		</a>
				  	</span>
				  	<el-dropdown-menu slot="dropdown">
				    	<el-dropdown-item>
				    		<input type="text" style="min-width: 200px" v-model="postId" @keyup="pickPost()" class="form-control" placeholder="Nhập ID bài viết rồi ấn enter">
				    	</el-dropdown-item>
				  	</el-dropdown-menu>
				</el-dropdown>
		 	</li>
		 	<li>
		 		<el-dropdown :hide-on-click="false" trigger="click">
				  	<span class="el-dropdown-link">
				    	<a href="#" :class="{'item-bar active tooltip-cs': date, 'item-bar tooltip-cs': !date }">
				 			<i class="fa fa-calendar"></i>
				 			<span class="tooltiptext tooltip-right">Khoảng thời gian</span>
				 		</a>
				  	</span>
				  	<el-dropdown-menu slot="dropdown">
				    	<el-dropdown-item>
				    		<el-date-picker
						      v-model="dateCf"
						      type="datetimerange"
						      range-separator="đến"
						      start-placeholder="Bắt đầu"
						      end-placeholder="Kết thúc"
						      @change="filterConversation('DATE')">
						    </el-date-picker>
				    	</el-dropdown-item>
				  	</el-dropdown-menu>
				</el-dropdown>
		 	</li>
		</ul>
	</div>
</template>
<script type="text/javascript">
	
	import { EventBus } from '../../helpers/bus'
	import calendarOption from '../../helpers/calendarOption'
	import { momentLocale } from '../../helpers/momentfix'
	import {debounce} from 'lodash'

	export default {
		props: ['pages'],
		data() {
			return {
				all: true,
				unread: false,
				comment: false,
				message: false,
				phone: false,
				post: false,
				date: false,
				postId: null,
				dateCf: [new Date(), new Date()],
			}
		},
		methods: {
			filterConversation(st, data = null) {
				let payload = {};
				switch (st) {
					case "ALL":
						payload.status = "ALL";
						this.all = payload.value = true;
						this.unread = false;
						this.comment = false;
						this.message = false;
						this.phone = false;
						this.post = false;
						this.date = false;
						break;
					case "UNREAD":
						this.unread = payload.value = !this.unread;
						this.all = false;
						payload.status = "UNREAD";
						break;
					case "COMMENT":
						this.comment = payload.value = !this.comment;
						this.all = false;
						payload.status = "COMMENT";
						break;
					case "MESSAGE":
						this.message = payload.value = !this.message;
						this.all = false;
						payload.status = "MESSAGE";
						break;
					case "PHONE":
						payload.status = "PHONE";
						this.all = false;
						this.phone = payload.value = !this.phone;
						break;
					case "POST":
						payload.status = "POST";
						this.all = false;
						payload.value = this.postId;
						break;
					case "DATE":
						payload.status = "DATE";
						payload.date = {};
						payload.date.timeFrom = momentLocale(this.dateCf[0]).format('YYYY-MM-DD HH:mm:ss');
						payload.date.timeTo = momentLocale(this.dateCf[1]).format('YYYY-MM-DD HH:mm:ss');
						this.all = false;
						this.date = payload.hide = true;
						break;
					default: break;
				}
				EventBus.$emit('filter-conversation', payload);
			},
			pickPost: debounce(function() {
				this.filterConversation("POST", this.postId);
			}, 500)
		}
	}
</script>
<style type="text/css">
	.thumnail-active {
	    position: absolute;
	    bottom: 18px;
	    right: 5px;
	    width: 15px;
	    height: 15px;
	    display: inline-block;
	}
	.thumnail-active img {
		width: 100%;
		border-radius: 50%;
	}
	.ipct-hid {
		opacity: 0;
	    display: none;
	    visibility: hidden;
	    position: absolute;
	    top: 15px;
	    left: 100%;
	    z-index: 999999;
	    transition: all 0.4s ease;
	    transform: translateX(-60px);
	    min-width: 
	}
	.ipct-hid.open {
		opacity: 1;
		display: block;
		visibility: visible;
		transform: translateX(0);
	}
	#time_range_conver {
		min-width: 330px;
	}
</style>