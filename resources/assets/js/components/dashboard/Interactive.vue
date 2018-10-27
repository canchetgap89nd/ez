<template>
	<div class="conversations-page">
		<slide-bar></slide-bar>
		<!-- End slide bar left -->
		<div class="box-content">
			<!-- Begin column conversation -->
			<div class="column-conver">
				<div class="inner-conver">
					<search-bar></search-bar>
					<div class="conver-container">
				        <div class="box-conver">
		                	<chat-item :conversations="conversations.data"></chat-item>
				        </div>

				        <div class="more-convers text-center" v-show="conversations.next">
				        	<span v-if="isLoaddingMore">
								<i class="fa fa-spinner fa-pulse fa-fw"></i>
								<span class="sr-only">Loading...</span>
							</span>
				            <span v-else class="txt-watchmore" @click="loadMore()">Xem thêm</span>
				        </div>
				    </div>
				</div>
			</div>
			<!-- End column conversation -->
			<input type="checkbox" class="toggleInp" id="checkboxToggle">
			<div class="column-chat" id="clChatPanel">

				<chat-board></chat-board>

			</div>

			<div class="column-cus" id="clCusPanel">
				<customer-quickly></customer-quickly>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">

	import Auth from '../../store/auth'
	import { get, post } from '../../helpers/send'
	import { EventBus } from '../../helpers/bus'
	import SlideBar from './SlideBar.vue'
	import SearchBar from './SearchBar.vue'
	import ChatBoard from './ChatBoard.vue'
	import CustomerQuickly from './customerInfo/CustomerQuickly.vue'
	import moment from 'moment'
	import ChatItem from './ChatItem.vue'

	export default {
		components: {
			'slide-bar': SlideBar,
			'search-bar': SearchBar,
			'chat-board': ChatBoard,
			'customer-quickly': CustomerQuickly,
			'chat-item': ChatItem,
		},
		computed: {
			pages() {
				return this.$store.state.pages;
			},
			setting() {
				return this.$store.state.setting;
			}
		},
		data() {
			return {
				active_el: '',
				conversations: {
					data: [],
					next: null
				},
				beginChat: false,
				search: {
					pageIdsTar: '',
					groupId: '',
					keyword: '',
					unread: false,
					comment: false,
					message: false,
					phone: false,
					fbPostId: '',
					timeFrom: '',
					timeTo: '',
					postId: ''
				},
				isLoaddingMore: false
			}
		},
		created() {
			this.getListConversations();
		},
		mounted() {
			this.sortConversations();
			EventBus.$on('begin-chat', () => {
				this.beginChat = true;
			})
            EventBus.$on('updateConversation', (conversation) => {
                this.updateConversation(conversation);
            })
            EventBus.$on('filter-conversation', (payload) => {
            	this.filterConversations(payload);
            })
            EventBus.$on('getListConversations', () => {
            	this.getListConversations();
            })
            EventBus.$on('markRead', (converId) => {
            	this.markRead(converId);
            })
            EventBus.$on('updateJustReply', (conversation) => {
		        this.updateConversation(conversation)
	      	})
            document.title = 'Hội thoại';
		},
		methods: {
			getListConversations() {
				Auth.initialize();
	        	get('/api/conversations/list', {
	        		keyword: this.search.keyword,
	        		groupId: this.search.groupId,
	        		unread: this.search.unread,
	        		comment: this.search.comment,
	        		message: this.search.message,
	        		phone: this.search.phone,
	        		postId: this.search.postId,
	        		timeFrom: this.search.timeFrom,
	        		timeTo: this.search.timeTo,
	        		pageIdsTar: this.search.pageIdsTar
	        	})
	            .then((res) => {
	                let response = res.data;
	                this.conversations.next = response.next_page_url;
	                this.conversations.data = response.data;
	                this.sortConversations();
	            })
	            .catch((err) => {
	                $.notify('Không thể tải dữ liệu người dùng, vui lòng làm mới lại trang!', 'error');
	            })
			},
			markRead(converId) {
				let countClear = 0;
				this.conversations.data.forEach((val) => {
					if (val.id == converId) {
						countClear++;
						val.unread_count = 0;
						val.unread_count = 0;
					}
				})
				this.$store.dispatch('minusUnread', countClear);
			},
			sortConversations() {
				let conversations = this.conversations.data;
				if (this.setting.priority_cmt_has_key) {
					this.$set(this.conversations, 'data', this.sortConversationByKeyTime(conversations));
				} else {
					this.$set(this.conversations, 'data', this.sortConversationByTime(conversations));
				}
			},
			sortConversationByTime(conversations) {
                for (var i = 0; i < conversations.length - 1; i++) {
                    for (var j = i + 1; j < conversations.length; j++) {
                        let a = this.convertToUnixTime(conversations[i].updated_time);
                        let b = this.convertToUnixTime(conversations[j].updated_time);
                        if (a < b) {
                            let tg = conversations[i];
                            conversations[i] = conversations[j];
                            conversations[j] = tg;
                        }
                    }
                }
                return conversations;
            },
            sortConversationByKeyTime(conversations) {
                let hasKey = [],
                	unread = [],
                    notKey = [];

                for (var i = 0; i < conversations.length; i++) {
                    let check = false;
                    for (let k in this.setting.key_cmt_priority) {
                        let has = conversations[i].last_message.search(this.setting.key_cmt_priority[k])
                        if (has !== -1) {
                        	if (conversations[i].comments_unread_count || conversations[i].messages_unread_count) {
                        		hasKey.push(conversations[i]);
	                            check = true;
	                            break;
                        	}
                        }
                    }

                    if (!check) {
                    	if (conversations[i].comments_unread_count || conversations[i].messages_unread_count) {
                    		unread.push(conversations[i]);
                    	} else {
                        	notKey.push(conversations[i]);
                    	}
                    }
                }

                for (var i = 0; i < hasKey.length - 1; i++) {
                    for (var j = i + 1; j < hasKey.length; j++) {
                        let a = this.convertToUnixTime(hasKey[i].updated_time);
                        let b = this.convertToUnixTime(hasKey[j].updated_time);
                        if (a < b) {
                            let tg = hasKey[i];
                            hasKey[i] = hasKey[j];
                            hasKey[j] = tg;
                        }
                    }
                }

                for (var i = 0; i < unread.length - 1; i++) {
                    for (var j = i + 1; j < unread.length; j++) {
                        let a = this.convertToUnixTime(unread[i].updated_time);
                        let b = this.convertToUnixTime(unread[j].updated_time);
                        if (a < b) {
                            let tg = unread[i];
                            unread[i] = unread[j];
                            unread[j] = tg;
                        }
                    }
                }

                for (var i = 0; i < notKey.length - 1; i++) {
                    for (var j = i + 1; j < notKey.length; j++) {
                        let a = this.convertToUnixTime(notKey[i].updated_time);
                        let b = this.convertToUnixTime(notKey[j].updated_time);
                        if (a < b) {
                            let tg = notKey[i];
                            notKey[i] = notKey[j];
                            notKey[j] = tg;
                        }
                    }
                }

                let ind = 0;
                for (var i = 0; i < hasKey.length; i++) {
                    conversations[ind] = hasKey[i];
                    ind++;
                }
                for (var i = 0; i < unread.length; i++) {
                    conversations[ind] = unread[i];
                    ind++;
                }
                for (var i = 0; i < notKey.length; i++) {
                    conversations[ind] = notKey[i];
                    ind++;
                }

                return conversations;
            },
            convertToUnixTime(time) {
            	return moment(time * 1000).unix()
            },
			updateConversation(conversation) {
				let show = false;
				for (let k in this.pages) {
					if (this.pages[k].fb_page_id == parseInt(conversation.fb_page_id)) {
						show = true;
						break;
					}
				}
				if (show) {
	                let has = false;
	                this.conversations.data.forEach((el) => {
	                    if (parseInt(el.id) == parseInt(conversation.id)) {
	                        el.updated_time = conversation.updated_time;
	                        el.last_message = conversation.last_message;
	                        el.customer = conversation.customer;
	                        el.unread_count = conversation.unread_count;
	                        el.has_key = conversation.has_key;
	                        el.has_phone = conversation.has_phone;
	                        el.has_note = conversation.has_note;
	                        el.has_order = conversation.has_order;
	                        el.is_multiple_chat = conversation.is_multiple_chat;
	                        el.urnead = conversation.urnead;
	                        el.unreply = conversation.unreply;
	                        has = true;
	                    }
	                })
	                if (!has) {this.conversations.data.unshift(conversation)};
	                this.sortConversations();
				}
            },
            filterConversations(data) {
            	switch (data.status) {
            		case "ALL":
            			this.search.keyword = '';
            			this.search.groupId = '';
            			this.search.unread = false;
            			this.search.comment = false;
            			this.search.message = false;
            			this.search.phone = false;
            			this.search.postId = '';
            			this.search.timeFrom = '';
            			this.search.timeTo = '';
            			this.getListConversations();
            			break;
            		case "KEY":
            			this.search.keyword = data.value;
            			this.getListConversations();
            			break;
            		case "GROUP":
            			this.search.groupId = data.value;
            			this.getListConversations();
            			break;
					case "UNREAD":
						this.search.unread = data.value;
						this.getListConversations();
						break;
					case "COMMENT":
						this.search.comment = data.value;
						this.getListConversations();
						break;
					case "MESSAGE":
						this.search.message = data.value;
						this.getListConversations();
						break;
					case "PHONE":
						this.search.phone = data.value;
						this.getListConversations();
						break;
					case "POST":
						this.search.postId = data.value;
						this.getListConversations();
						break;
					case "DATE":
						this.search.timeFrom = data.date.timeFrom;
						this.search.timeTo = data.date.timeTo;
						this.getListConversations();
						break;
					case "PAGE":
						this.search.pageIdsTar = data.value.join();
						this.getListConversations();
						break;
					default: break;
				}
            },
            loadMore() {
                let url = this.conversations.next;
                if (url) {
                	this.isLoaddingMore = true;
                    get(url, {
		        		keyword: this.search.keyword,
		        		groupId: this.search.groupId,
		        		unread: this.search.unread,
		        		comment: this.search.comment,
		        		message: this.search.message,
		        		phone: this.search.phone,
		        		postId: this.search.postId,
		        		timeFrom: this.search.timeFrom,
		        		timeTo: this.search.timeTo,
		        		pageIdsTar: this.search.pageIdsTar
		        	})
                    .then((res) => {
                        if (res.data.data.length) {
                            let response = res.data;
			                if (response.data.length) {
			                    response.data.forEach((val) => {
			                        this.conversations.data.push(val);
			                    })
			                    this.conversations.next = response.next_page_url;
			                }
			                this.isLoaddingMore = false;
                        }
                    })
                }
            }
		},
		beforeDestroy () {
            EventBus.$off('begin-chat')
            EventBus.$off('updateConversation')
            EventBus.$off('filter-conversation')
            EventBus.$off('getListConversations')
            EventBus.$off('markRead')
            EventBus.$off('updateJustReply')
        }
	}
</script>
<style type="text/css" scoped>
	/* width */
    ::-webkit-scrollbar {
        width: 5px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
        box-shadow: inset 0 0 5px #ddd; 
        border-radius: 10px;
    }
     
    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #000; 
        border-radius: 10px;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #00b140; 
    }
    .more-convers {
    	margin: 5px 0;
    }
    .more-convers .txt-watchmore {
    	cursor: pointer;
    	color: #929292;
    }
</style>