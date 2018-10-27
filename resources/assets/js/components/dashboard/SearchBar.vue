<template>
	<div class="head-conver">
		<div class="search-bar">
			<div class="dropdown dropdown-cus">
			    <button class="btn btn-default dropdown-toggle btn-type-search" type="button" data-toggle="dropdown">{{ groupSearch ? groupSearch.group_name : "Tất cả" }}
			    <span class="caret"></span></button>
			    <input placeholder="Tìm kiếm" @keyup="search(keyword)" class="form-control search-has-type form-control-cus" v-model="keyword" type="text">
			    <span class="icon-searchHas-type" @click="search()">
					<i class="el-icon-search"></i>
				</span>
			    <ul class="dropdown-menu list-group-search">
			    	<li>
			    		<a href="#" @click="pickGroup()">Tất cả</a>
			    	</li>
			      	<li v-for="group in groups">
				      	<a href="#" v-text="group.group_name" @click="pickGroup(group)"></a>
			      	</li>
			    </ul>
		  	</div>
		  	<div class="markReadContainer">
			  	<span class="icon-box" title="Đánh dấu tất cả đã đọc" @click="markReadAll()">
					<i class="fa fa-eye"></i>
				</span>
		  	</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import {get} from '../../helpers/send'
	import {post} from '../../helpers/send'
	import { EventBus } from '../../helpers/bus'
	import {debounce} from 'lodash'
	import AWN from "awesome-notifications"
  	var notifier = new AWN();

	export default {
		data() {
			return {
				keyword: '',
				groupSearch: null
			}
		},
		computed: {
			groups() {
				return this.$store.state.groups;
			}
		},
		methods: {
			search: debounce((keyword) => {
				let payload = {};
            	payload.status = "KEY";
				payload.value = keyword;
            	EventBus.$emit('filter-conversation', payload);
			}, 300),
            pickGroup(group = null) {
            	this.groupSearch = group;
            	let payload = {
            		status: "GROUP",
            		value: ''
            	}
            	if (group) {
	            	payload.status = 'GROUP';
	            	payload.value = group.id;
            	}
            	EventBus.$emit('filter-conversation', payload);
            },
            markReadAll() {
            	post('/api/conversations/markRead')
            	.then((res) => {
            		if (res.data.success) {
            			document.title = "Hội thoại";
            			$.notify('Đã đánh dấu đọc tất cả', 'success');
            			EventBus.$emit('getListConversations');
            			this.$store.dispatch('clearUnread');
            		}
            	})
            }
		}
	}
</script>