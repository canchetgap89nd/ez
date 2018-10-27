<template>
	<transition name="modal">
	    <div class="modal-mask">
	      	<div class="modal-wrapper">
	        	 <!-- Modal content-->
	        	<div class="modal-dialog">
			      	<div class="modal-content">
			        	<div class="modal-header">
			          		<button type="button" class="close" @click="$emit('close')">&times;</button>
			          		<span class="txt-black">Nhắn tin cho</span>
		          			<h4 class="modal-title txt-color-green">{{ comment.from_name }}</h4>
			        	</div>
				        <div class="modal-body">
				        	<div class="form-group">
				        		<h3 class="recent-message">Tin nhắn gần đây</h3>
								<circle-load class="process-recent-message" v-if="isLoading"></circle-load>
				        		<div class="recent-container" id="recent-messages-container" v-else>
				        			<div v-if="messages && messages.data.length">
										<circle-load class="process-recent-message" v-if="isLoadingMore"></circle-load>
				        				<div class="text-center" v-if="messages.next_page_url && !isLoadingMore">
				        					<span class="more-message" @click="loadMore(messages.next_page_url)">Xem thêm</span>
				        				</div>
					        			<!-- UI chatting -->
										<div v-for="message in reverse(messages.data)">
											<div class="item-chat chating-cus" v-if="page.fb_page_id != message.from_id">
												<div class="inner-item-c">
													<div class="box-avar-sb">
														<a href="javascript:;" class="link-cus">
															<img :src="'https://graph.facebook.com/'+ message.from_id + '/picture?height=40&width=40'" class="ava-fb ava-cus_2">
														</a>
													</div>
													<div class="content-chat">
														<div class="inner-cont-c">
															<div class="txt-chat">
																<span v-text="message.message"></span>
																<span v-if="message.attachments">
																	<a href="#" v-for="att in message.attachments">
																		<img class="image-message" v-if="att.type == 'IMAGE'" :src="att.url">
																		<a :href="att.file_url" class="other-file" v-else title="Tệp đính kèm" v-text="att.name">
																		</a>
																	</a>
																</span>
															</div>
														</div>
													</div>
													<div class="time-of-chat">
														<span class="time" v-text="formatTime(message.created_time, 'HH:mm:ss DD/MM/YYYY')"></span>
													</div>
												</div>
											</div>

											<div class="item-chat not-cus" v-else>
												<div class="inner-item-c">
													<div class="content-chat">
														<div class="inner-cont-c">
															<div class="txt-chat">
																<span v-text="message.message" class="txt-black"></span>
																<span v-if="message.attachments">
																	<a href="#" v-for="att in message.attachments">
																		<img class="image-message" v-if="att.type == 'IMAGE'" :src="att.url">
																		<a class="other-file" :href="att.file_url" v-else title="Tệp đính kèm" v-text="att.name">
																		</a>
																	</a>
																</span>
															</div>
														</div>
													</div>
													<div class="time-of-chat">
														<span class="time" v-text="formatTime(message.created_time, 'HH:mm:ss DD/MM/YYYY')"></span>
													</div>
												</div>
											</div>
											<!-- end UI chating -->
										</div>
										<!-- UI Chatting -->
										<div class="item-chat not-cus" v-show="isSending">
											<div class="inner-item-c">
												<div class="content-chat">
													<div class="inner-cont-c">
														<div class="txt-chat">
															<span></span>
															<div class="lds-css ng-scope"> <div style="width:100%;height:100%" class="lds-flickr"> <div></div> <div></div> <div></div> </div> </div>
														</div>
													</div>
												</div>
											</div>
										</div>
				        			</div>
				        			<h3 class="text-center" v-else>Trống</h3>
				        		</div>
				        	</div>
				        	<div class="form-group">
				          		<textarea ref="quickSendMessage" rows="3" v-model="mess" @keyup.enter="reply()" class="in-text-reply form-control" placeholder="Nhập tin nhắn vào đây"></textarea>
				        	</div>
				        </div>
				        <div class="modal-footer">
				          	<button type="button" class="btn btn-success" :disabled="!mess || isSending" @click="reply()">Gửi</button>
				          	<button type="button" class="btn btn-default" @click="$emit('close')">Đóng</button>
				        </div>
		     		</div>
	        	</div>
	      	</div>
	    </div>
  	</transition>
</template>
<script type="text/javascript">
	
	import { EventBus } from '../../helpers/bus'
	import { post } from '../../helpers/send'
	import { get } from '../../helpers/send'
	import { momentLocale } from '../../helpers/momentfix'
	import Circle from '../loading/Circle.vue'
	import AWN from "awesome-notifications"
	import moment from 'moment'
	var notifier = new AWN();
	var env = require('../../env')
	
	export default {
		components: {
			'circle-load': Circle
		},
		props: [
			'messages', 'post', 'comment', 'conversation', 'page'
		],
		data() {
			return {
				isLoading: false,
				isLoadingMore: false,
				type: null,
				mess: '',
				isSending: false,
				autoScroll: true,
				env: env
			}
		},
		created() {
			if (this.comment.can_reply_privately) {
				this.type = 2;
			} else 
			if (this.conversation) {
				this.type = 1;
			}
		},
		mounted() {
			this.scrollToEnd("recent-messages-container");
			this.fucusInpMessage()
		},
		updated() {
			this.scrollToEnd("recent-messages-container");
		},
		methods: {
			fucusInpMessage () {
				this.$nextTick(() => {
					if (this.$refs.quickSendMessage) {
						this.$refs.quickSendMessage.focus()
					}
				})
			},
			reverse(arr) {
				return arr.slice().reverse();
			},
			reply() {
				let message = this.mess;
				this.conversation.updated_time = moment().unix()
		        this.conversation.last_message = message
		        this.conversation.unreply = false
		        EventBus.$emit('updateJustReply', this.conversation)
				if (parseInt(this.type) == 1) {
					this.sendMessage(message);
				} else 
				if (parseInt(this.type) == 2) {
					this.sendMessageWithPost(message);
				}
				this.mess = '';
			},
			scrollToEnd(id) {
		      	let container = document.getElementById(id);
			  	if (container && this.autoScroll) {
			      	container.scrollTop = container.scrollHeight;
			  	}
		    },
			sendMessage(message) {
				this.isSending = true;
				this.autoScroll = true;
				post('/api/conversations/reply/'+this.conversation.id, {
					message: message,
					app: 'WEB'
				})
				.then((res) => {
					if (res.data.id) {
						this.isSending = false;
						if (this.conversation.type == env.MESSAGE_VAR) {this.pushNewMessage(res.data)}
					}
					this.fucusInpMessage()
				})
				.catch((err) => {
					$.notify('Lỗi gửi tin nhắn', 'error');
					this.fucusInpMessage()
					this.isSending = false;
				})
			},
			sendMessageWithPost(message) {
				this.isSending = true;
				post('/api/conversations/messages/private/'+this.comment.fb_comment_id, {
					message: message,
					fb_page_id: this.page.fb_page_id,
					app: 'WEB'
				})
				.then((res) => {
					let response = res.data
					if (response.success) {
						if (response.waitting) {
							$.notify('Gửi tin nhắn thành công! Đang chờ người dùng chấp nhận', 'success')
							this.$emit('close')
						} else {
							this.type = 1;
							this.isSending = false;
							this.pushNewMessage(response.data);
							// emit to Conversation component
							this.$emit('updateConverPrivate', response.conversation);
						}
					}
					this.fucusInpMessage()
				})
				.catch((err) => {
					if (err.response.status == 500) {
						$.notify(err.response.data.message, 'error')
					} else {
						$.notify('Lỗi gửi tin nhắn', 'error');
					}
					this.$emit('close')
					this.isSending = false;
				})
			},
			formatTime(time, formater) {
				return momentLocale(time*1000).format(formater);
			},
			loadMore(url) {
				this.isLoadingMore = true;
				this.autoScroll = false;
				get(url + '&fb_page_id=' + this.page.fb_page_id)
				.then((res) => {
					let messages = res.data.messages.data;
					messages.forEach((item) => {
						this.messages.data.push(item);
					})
					this.messages.next_page_url = res.data.messages.next_page_url;
					this.isLoadingMore = false;
				})
			},
			pushNewMessage (message) {
				let messageHas = this.messages.data
		        if (messageHas) {
		          	let has = messageHas.some(function (el) {
		            	return el.id === message.id
		          	})
		          	if (!has) {
		            	this.messages.data.unshift(message)
		          	}
		        } else {
		          	this.messages.data = []
	          		this.messages.data.push(message)
		        }
			}
		}
	}
</script>
<style type="text/css" scoped>
	.modal-mask {
	  position: fixed;
	  z-index: 9998;
	  top: 0;
	  left: 0;
	  width: 100%;
	  height: 100%;
	  background-color: rgba(0, 0, 0, .5);
	  display: table;
	  transition: opacity .3s ease;
	}

	.modal-wrapper {
	  display: table-cell;
	  vertical-align: middle;
	}

	.modal-container {
	  width: 300px;
	  margin: 0px auto;
	  padding: 20px 30px;
	  background-color: #fff;
	  border-radius: 2px;
	  box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
	  transition: all .3s ease;
	  font-family: Helvetica, Arial, sans-serif;
	}

	.modal-header h3 {
	  margin-top: 0;
	  color: #42b983;
	}

	.modal-body {
	  margin: 20px 0;
	}

	.modal-default-button {
	  float: right;
	}

	/*
	 * The following styles are auto-applied to elements with
	 * transition="modal" when their visibility is toggled
	 * by Vue.js.
	 *
	 * You can easily play with the modal transition by editing
	 * these styles.
	 */

	.modal-enter {
	  opacity: 0;
	}

	.modal-leave-active {
	  opacity: 0;
	}

	.modal-enter .modal-container,
	.modal-leave-active .modal-container {
	  -webkit-transform: scale(1.1);
	  transform: scale(1.1);
	}
</style>