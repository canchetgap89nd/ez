<template>
	<div class="inner-column-chat">
		<h3 v-if="!beginChat && !isLoading" class="trick-title"><i class="fa fa-comments"></i> Hãy chọn một cuộc hội thoại</h3>
		<span v-else-if="isLoading && beginChat" class="process-chat-board">
			<circle-load></circle-load>
		</span>
		<div v-else class="inner-column-chat">

			<div class="head-chat">
				<div class="inner-head-c">
					<div class="avar-cus">
						<a href="javascript:;">
							<img v-if="customerOnChat && customerOnChat.fb_id_cus" :src="'https://graph.facebook.com/'+ customerOnChat.fb_id_cus + '/picture?height=160&width=160'" class="ava-fb ava-boxinfo" @click="getLink(customerOnChat.fb_id_cus)">
							<img v-else :src="'/images/logo.png'" class="ava-fb ava-boxinfo">
						</a>
					</div>
					<div class="info-cus">
						<h3 v-if="customerOnChat && customerOnChat.name_cus" class="name-of title-black" v-text="customerOnChat.name_cus"></h3>
						<h3 v-else class="name-of title-black">Khách hàng</h3>
						<div class="phone-cus">
							<span class="txt-tel" v-if="customerOnChat && customerOnChat.phone_cus" v-text="'Tel: ' + customerOnChat.phone_cus"></span>
							<span class="txt-tel" v-else>Tel: ...</span>
							<a href="javascript:;" class="txt-getphone item-get-phone" @click="getPhone(customerOnChat.fb_id_cus)">
								Tìm số điện thoại
							</a>
						</div>
						<div class="address-cus">
							<span v-if="customerOnChat && customerOnChat.address_cus" v-text="'Ad: ' + customerOnChat.address_cus"></span>
							<span v-else>Ad: ...</span>
						</div>
					</div>
					<div class="action-cus">
						<a id="ic_edithead_chat" class="edit-info item-ac collapse-cl-cus" @click="toggleCustomerPanel()">
							<i class="fa fa-edit"></i>
						</a>
						<div class="dropdown">
							<a class="set-cus item-ac tooltip-cs" data-toggle="dropdown">
								<i class="fa fa-bug" v-if="reports && reports.length" style="color: red"></i>
								<i class="fa fa-bug" v-else></i>
								<span class="tooltiptext">
									Khách hàng này có {{ reports ? reports.length : 0 }} lần báo xấu
								</span>
							</a>
						  	<ul class="dropdown-menu dropdown-menu-right">
						    	<li><a href="#" @click="reportCustomer()">Báo xấu</a></li>
						  	</ul>
						</div>
						<div class="modal fade" id="modal-report" tabindex="-1" role="dialog" data-backdrop="false" aria-hidden="true">
						    <div class="modal-dialog modal-lg" role="document">
								<report-board :customer="customerOnChat"></report-board>
						    </div>
						</div>
					</div>
				</div>
			</div>

			<conversations :conversation="conversation" :messages="messages" :customer="customerOnChat" :post="infoPost" :currentStatus="currentStatus" :target="target" @focusInput="focusInpMessage"></conversations>

			<div class="chat-typing">
				<div class="action-top-chating">
					<!-- list groups of customer -->
					<ul class="ls-labels" v-if="customerOnChat && customerOnChat.groups">
						<li v-for="group in customerOnChat.groups">
							<span class="it-label" :style="{ 'background': group.group_color }">
								<span class="label" v-text="group.group_name"></span>
								<span class="del-label" @click="outGroup(group)">
									<i class="fa fa-close"></i>
								</span>
							</span>
						</li>
					</ul>
					<!-- list groups of customer -->

					<!-- create order -->
					<div class="shop-cart">
				 		<div class="dropdown menu-top-left">
						  	<a href="javascript:;" class="bn-cart" data-toggle="dropdown" title="Tạo đơn hàng">
								<i class="fa fa-shopping-cart"></i>
							</a>
						  	<ul class="dropdown-menu">
							    <li v-for="part in participants">
							    	<a href="javascript:;" @click="getInfoCusrWithIdFb(part)" class="item-cus"><i class="fa fa-cart-plus"></i>  {{ part.from_name }}</a>
							    </li>
						  	</ul>
						</div> 
					</div>
					<!-- create order -->

				</div>
				<span v-if="conversation.type == env.COMMENT_VAR" class="reply-to">@Trả lời 
					<span class="txt-color-green">{{ target.name }}</span>
					<span class="txt-black">{{ target.message }}</span>
				</span>
				<!-- image upload preview -->
				<div class="prepare-image" v-if="prepareImage">
					<span class="preview-img-chat">
						<img :src="prepareImage">
					</span>
					<span v-show="!isSaving" class="remove-image" @click="resetFiles()" title="Xóa ảnh">
						<i class="fa fa-close"></i>
					</span>
				</div>
				<!-- image upload preview -->
				
				<div class="inp-mess-container">
				  	<ul class="dropdown-menu list-quick-ans" v-show="showQuickTxtPanel">
					    <li v-for="(ans, index) in quickAnsSearch" :class="{ 'active':index == indQuickAns }">
					    	<a href="#" @click="quickReply(ans)">
					    		<span class="txt-color-green">{{ ans.quick_text }}</span>
					    		<br>
					    		{{ ans.answer_text }}
					    	</a>
						</li>
					    <li class="divider"></li>
				  		<li>
				  			<a href="javascript:;" @click="$router.push({ name: 'quickReplySet' })">
				  				<span class="icon-add-pr txt-color-green">
									<i class="fa fa-plus-circle"></i>
								</span>
								Thêm mới
				  			</a>
				  		</li>
				  	</ul>
					<!-- input message -->
					<textarea ref="inpMessage" v-model="mess" placeholder="Nhập vào lời nhắn..." @keyup="reply" class="txt-chating"></textarea>
					<!-- input message -->
				</div>

				<ul class="action-chating">
					<li class="item-bt-on">
						<a href="javascript:;" @click="if (!isSaving) {reply}" :class="{'item-send item-oval-outside active': mess, 'item-send item-oval-outside': !mess}">
							<i class="material-icons">send</i>
						</a>
					</li>
					<li class="item-bt-on">
						<a href="javascript:;" class="item-ac-chating item-sphoto" title="Gửi ảnh">
							<i class="material-icons">insert_photo</i>
							<input type="file" class="choose-photo" @change="filesChange" :disabled="isSaving" id="attachments">
						</a>
					</li>
					<li class="item-bt-on">
						<div class="dropdown menu-top-left">
							<a href="#" data-toggle="dropdown" class="item-oval-outside item-fchat" title="Gửi tin nhắn mẫu">
								<i class="material-icons">textsms</i>
							</a>
						  	<ul class="dropdown-menu list-quick-ans">
							    <li v-for="ans in quickAns">
							    	<a href="#" @click="quickReply(ans)">
							    		<span class="txt-color-green">{{ ans.quick_text }}</span>
							    		<br>
							    		{{ ans.answer_text }}
							    	</a>
								</li>
							    <li class="divider"></li>
						  		<li>
						  			<a href="javascript:;" @click="$router.push({ name: 'quickReplySet' })">
						  				<span class="icon-add-pr txt-color-green">
											<i class="fa fa-plus-circle"></i>
										</span>
										Thêm mới
						  			</a>
						  		</li>
						  	</ul>
						</div>
					</li>
					<li class="item-bt-on">
				 		<div class="dropdown menu-top-left">
						  	<a href="javascript:;" data-toggle="dropdown" class="item-ac-chating item-tag" title="Gắn nhãn khách hàng">
								<i class="fa fa-tag"></i>
							</a>
						  	<ul class="dropdown-menu list-op-label">
							    <li v-for="group in groups">
							    	<a href="#" @click="pinGroup(group)">{{ group.group_name }}</a>
							    </li>
							    <li class="divider"></li>
						  		<li>
						  			<a href="javascript:;" @click="$router.push({ name: 'groupSet' })">
						  				<span class="icon-add-pr txt-color-green">
											<i class="fa fa-plus-circle"></i>
										</span>
										Thêm mới
						  			</a>
						  		</li>
						  	</ul>
						</div> 
					</li>
				</ul>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	import {get,del,post} from '../../helpers/send'
	import {addName} from '../../helpers/replaceNameInString'
	import { EventBus } from '../../helpers/bus'
	import Conversation from './Conversation.vue'
	import ReportBoard from './ReportBoard.vue'
	import Circle from '../loading/Circle.vue'
	import {mapGetters, mapActions} from 'vuex'
	import moment from 'moment'
	import Viewer from 'v-viewer'
  	import Vue from 'vue'
  	Vue.use(Viewer)

	const STATUS_INITIAL = 0, STATUS_SAVING = 1, STATUS_SUCCESS = 2, STATUS_FAILED = 3;
	var env = require('../../env')

	export default {
		components: {
			'conversations': Conversation,
			'report-board': ReportBoard,
			'circle-load': Circle
		},
		data() {
			return {
				beginChat: false,
				isLoading: false,
				messages: {
					data: []
				},
				reports: [],
				conversation: {},
				infoPost: null,
				currentStatus: null,
				mess: '',
				prepareImage: '',
				dataFile: new FormData(),
				uploadedFiles: [],
				target: {
					id: null,
					name: null,
					message: null
				},
				showQuickTxtPanel: false,
				indQuickAns: 0,
				quickAnsSearch: [],
				env: env
			}
		},
		computed: {
			...mapGetters(
				'conversation', ['customerOnChat']
			),
	      	isInitial() {
	        	return this.currentStatus === STATUS_INITIAL;
	      	},
	      	isSaving() {
	        	return this.currentStatus === STATUS_SAVING;
	      	},
	      	isSuccess() {
	        	return this.currentStatus === STATUS_SUCCESS;
	      	},
	      	isFailed() {
	        	return this.currentStatus === STATUS_FAILED;
	      	},
	      	participants() {
	      		let mess = this.messages.data;
	      		let parts = [];

	      		let tg = mess.filter((el) => {
	      			return el.from_id != this.conversation.fb_page_id
	      		})

	      		tg.forEach(function(el1) {
	      			let has = parts.some(function(el2) {
	      				return el2.from_id == el1.from_id;
	      			})
	      			if (!has) {
	      				parts.push(el1);
	      			}
	      		})

	      		if (this.conversation.type == env.COMMENT_VAR) {
	      			tg.forEach(function(el1) {
	      				if (el1.childs) {
		  					let childs = el1.childs;
		  					childs.forEach(function(el2) {
		  						let has = parts.some(function(el3) {
				      				return el2.from_id == el3.from_id;
				      			})
				      			if (!has) {
				      				parts.push(el2);
				      			}
		  					})
	      				}
	  				})
	      		}

	      		return parts;
	      	},
	      	groups() {
	      		return this.$store.state.groups;
	      	},
	      	quickAns() {
	      		return this.$store.state.quickAns;
	      	},
	      	setting() {
				return this.$store.state.setting;
			}
	    },
	    mounted() {
	    	EventBus.$on('load-conversation', (data) => {
	    		this.loadConversation(data);
	    	})
	    	EventBus.$on('updateConversation', (conversation) => {
	    		EventBus.$emit('updateMessageNew', conversation);
                this.updateMessageOfConversation(conversation);
            })
            EventBus.$on('sendOrderMessage', (info) => {
            	this.sendOrderMessage(info);
            })
            EventBus.$on('addReports', (data) => {
            	this.addReports(data);
            })
	    },
	    methods: {
	    	...mapActions(
				'conversation', ['setCustomerOnChat', 'getPhone', 'getLink']
			),
	    	focusInpMessage () {
		        this.$nextTick(() => {
		        	if (this.$refs.inpMessage) {
	          			this.$refs.inpMessage.focus()
		        	}
		        })
	      	},
	    	toggleCustomerPanel(openPanel = false) {
				if ($("#checkboxToggle").is(':checked')) {
					if (openPanel) {return}
					$('#icShowCusPanel').removeClass('fa-rotate-180');
					$('#ic_edithead_chat').removeClass('fa-rotate-180');
					$('#checkboxToggle').prop('checked', false);
					$("#innerClCusPanel").hide()
				}else{
					$('#icShowCusPanel').addClass('fa-rotate-180');
					$('#ic_edithead_chat').addClass('fa-rotate-180');
					$('#checkboxToggle').prop('checked', true);
					$("#innerClCusPanel").show()
				}
			},
	    	reset() {
	    		this.conversation = {};
	    		this.currentStatus = null;
	    		this.infoPost = null;
	    		this.dataFile = new FormData();
	    		this.uploadedFiles = [];
	    	},
	    	loadConversation: function(data) {
	    		this.reset();
            	this.isLoading = true;
            	if (!this.beginChat) {this.beginChat = true};
                get('/api/conversations/'+ data.id)
                .then((res) => {
                	this.conversation = res.data.conversation;
                	this.messages = res.data.messages;
                	this.reports = res.data.reports;
                	if (res.data.conversation.type == env.COMMENT_VAR) {
                		this.infoPost = res.data.infoPost;
                		for (var i = this.messages.data.length - 1; i >= 0; i--) {
                			let mess = this.messages.data[i];
                			if (!mess.is_remove) {
                				this.target.id = mess.fb_comment_id;
		                		this.target.name = mess.from_name;
		                		this.target.message = mess.message;
		                		this.target.type = env.COMMENT_VAR;
                			} else 
                			if (!this.messages.data[i - 1]) {
                				this.target.id = this.infoPost.id;
                				this.target.name = "Bài viết";
                			}
                		}
                	} else {
                		this.infoPost = null;
                	}
                	this.isLoading = false;
                	this.focusInpMessage()
                })
                .catch((err) => {
                    $.notify('Có lỗi xảy ra', 'error');
                })
            },
            resetFiles() {
		        // reset form to initial state
		        // this.currentStatus = STATUS_INITIAL;
		        this.dataFile = new FormData();
		        this.uploadedFiles = [];
		        this.prepareImage = '';
		        this.mess = '';
		        $('.txt-chating').css({'width': '100%'});
	      	},
			saveFiles() {
		        // upload data to the server
		        this.currentStatus = STATUS_SAVING;
		        this.prepareFields();
		        let fbPageId = this.conversation.fb_page_id;
		        let access_token = this.$store.state.pages[fbPageId].page_token;

		        this.conversation.updated_time = moment().unix();
		        this.conversation.last_message = '@Tệp tin';
		        this.conversation.unreply = false;
		        EventBus.$emit('updateJustReply', this.conversation);
                if (this.conversation.type == env.COMMENT_VAR) {
	                this.uploadToComment(this.target.id, access_token);
                } else 
                if (this.conversation.type == env.MESSAGE_VAR) {
                	this.uploadToMessage(this.conversation.thread_id, access_token);
                }
	      	},
			filesChange(e) {
		        var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                for (var i = files.length - 1; i >= 0; i--) {
                    this.uploadedFiles.push(files[i]);
                }
                //if send image to comment
                if (this.conversation.type == env.COMMENT_VAR) {
                	let reader = new FileReader();
                	$('.txt-chating').css({'width': '89%'});
                	reader.onload = (event) => {
                		this.prepareImage = event.target.result;
                	}
                	reader.readAsDataURL(files[0]);
                } else 
                if (this.conversation.type == env.MESSAGE_VAR) {
		     		this.saveFiles();
                }
                this.focusInpMessage()
                // Reset the form to avoid copying these files multiple times into this.attachments
                $("#attachments").val('');
		    },
		    prepareFields() {
                if (this.uploadedFiles.length > 0) {
                	this.dataFile.append('file', this.uploadedFiles[0]);
                    this.dataFile.append('upload_file', true);
                    if (this.conversation.type == env.COMMENT_VAR) {
                    	let message = this.mess.trim();
                    	if (message.trim()) {
                    		this.dataFile.append('message', message);
                    	};
                    };
                }
            },
            uploadToComment(targetId, token) {
            	let fileSend = this.dataFile
            	this.resetFiles();
            	let url = 'https://graph.facebook.com/v2.7/'+targetId+'/comments?access_token='+token;
            	post(url, fileSend)
                .then((response) => {
                    if (response.data.id) {
                    	this.currentStatus = STATUS_SUCCESS;
                       	this.createCommentWithId(response.data.id);
                    }
                })
                .catch((error) => {
                	this.currentStatus = STATUS_FAILED;
                });
            },
            uploadToMessage(conversationId, token) {
            	let fileSend = this.dataFile
            	this.resetFiles();
            	let url = 'https://graph.facebook.com/v2.7/'+conversationId+'/messages?access_token='+token;
            	post(url, fileSend)
                .then((response) => {
                    if (response.data.id) {
                        this.currentStatus = STATUS_SUCCESS;
                        this.createMessageWithId(response.data.id);
                    }
                })
                .catch((error) => {
                	this.currentStatus = STATUS_FAILED;
                });
            },
			createCommentWithId(id) {
				let targetId = this.target.type == env.COMMENT_VAR ? this.target.id : '';
				post('/api/conversations/create/comment', {
					converId: this.conversation.id,
					fbCommentId: id,
					targetId: targetId,
					app: 'WEB'
				})
				.then((res) => {
					if (res.data.id) {
						this.currentStatus = STATUS_SUCCESS;
						this.pushNewComment(res.data);
					}
				})
				.catch((err) => {
					this.currentStatus = STATUS_FAILED;
				})
			},
			createMessageWithId(id) {
				post('/api/conversations/create/message', {
					converId: this.conversation.id,
					fbMessageId: id,
					app: 'WEB'
				})
				.then((res) => {
					if (res.data.id) {
						this.currentStatus = STATUS_SUCCESS;
						this.pushNewMessage(res.data);
					}
				})
				.catch((err) => {
					this.currentStatus = STATUS_FAILED;
				})
			},
			pushNewMessage(message) {
				let messageHas = this.messages.data;
				let has = messageHas.some(function(el) {
					return el.id == message.id;
				})
				if (!has) {this.messages.data.unshift(message)};
			},
			pushNewComment(comment) {
				comment.just_reply = true;
				let commentsHas = this.messages.data;
				let has = commentsHas.some(function(el) {
					return (el.id == comment.id);
				})
				if (!has) {
					this.messages.data.forEach((val) => {
						if (val.childs) {
							val.childs.forEach((val2) => {
								val2.just_reply = false;
							})
						}
						val.just_reply = false;
					})
					if (comment.parent_id) {
						commentsHas.forEach((el, idx) => {
							if (el.id == comment.parent_id) {
								if (el.childs) {
									let hasChild = el.childs.some((el2) => {
										return el2.id == comment.id;
									})
									if (!hasChild) {this.messages.data[idx].childs.unshift(comment)}
								} else {
									this.messages.data[idx].childs = [comment];
								}
							}
						});
					} else {
						this.messages.data.unshift(comment);
					}
				};
			},
			reply(event) {
				let keyCode = event.which || event.keyCode;
				switch (keyCode) {
					case 13:
						if (!this.showQuickTxtPanel) {
							EventBus.$emit('autoScroll', true);
							this.markRead(this.conversation.id);
							if (this.uploadedFiles.length > 0) {
								this.saveFiles()
							} else {
								this.sendMessage()
							}
						} else {
							this.mess = this.quickAnsSearch[this.indQuickAns].answer_text;
							this.showQuickTxtPanel = false;
							this.sendMessage()
						}
						break;
					case 51:
						this.showQuickTxtPanel = true;
						break;
					case 27:
						this.showQuickTxtPanel = false;
						break;
					case 8:
						if (!this.mess) {this.showQuickTxtPanel = false}
						break;
					case 40:
						let index = this.indQuickAns + 1;
						if (this.quickAnsSearch[index]) {
							this.indQuickAns = index;
						}
						break;
					case 38:
						let ind = this.indQuickAns - 1;
						if (this.quickAnsSearch[ind]) {
							this.indQuickAns = ind;
						}
						break;

					default: 
						if (this.showQuickTxtPanel) {
							let word = /#(\w+)/ig;
							let arr = [];
							this.quickAns.forEach((val) => {
								let name = this.mess.match(word);
								if (name) {
									let strtags = name[0];
	        						if (strtags != '') strtags = strtags.replace('#', '');
	        						let item = val.quick_text.search(strtags);
	        						if (item != -1) {
	        							arr.push(val);
	        						}
								}
							})
							this.quickAnsSearch = arr;
						}
						break;
				}
			},
			sendMessage () {
				this.currentStatus = STATUS_SAVING;
				let message = this.mess
        		this.mess = ''
        		this.conversation.updated_time = moment().unix()
		        this.conversation.last_message = message
		        this.conversation.unreply = false
		        EventBus.$emit('updateJustReply', this.conversation)
				post('/api/conversations/reply/'+this.conversation.id, {
					message: message,
					target: this.target.id,
					app: 'WEB'
				})
				.then((res) => {
					if (res.data.id) {
						this.currentStatus = STATUS_SUCCESS;
						if (this.conversation.type == env.MESSAGE_VAR) {this.pushNewMessage(res.data)}
						else {this.pushNewComment(res.data)}
						this.resetFiles();
					}
					this.focusInpMessage()
				})
				.catch((err) => {
					this.currentStatus = STATUS_FAILED;
				})
			},
			updateMessageOfConversation(conversation) {
				if (conversation.id == this.conversation.id) {
					let message = conversation.just_message;
					if (this.conversation.type == env.MESSAGE_VAR) {
						this.pushNewMessage(message);
					} else 
					if (this.conversation.type == env.COMMENT_VAR) {
						this.pushNewComment(message);
					}
				}
			},
			pinGroup(group) {
				let groupId = group.id,
					fb_page_id = this.conversation.fb_page_id,
					name = this.conversation.from_name,
					fbId = this.conversation.from_id,
					converId = this.conversation.id;
				let customerId = this.customerOnChat ? (this.customerOnChat.id) : '';
				let url = customerId ? 'api/customers/pinGroup/'+customerId : 'api/customers/pinGroup';
				post(url, {
					groupId: groupId,
					fb_page_id: fb_page_id,
					name: name,
					fbId: fbId,
					converId: converId
				})
					.then((res) => {
						if (res.data.success) {
							EventBus.$emit('getListConversations');
							this.updateInfoCustomer(res.data.customer.id);
							$.notify('Gắn nhãn khách hàng thành công', 'success');
						}
					})
					.catch((err) => {
						if (err.response.status == 500) {
							$.notify(err.response.data.message, 'warn');
						} else {
							$.notify('Lỗi gắn nhãn khách hàng', 'error');
						}
					})
			},
			updateInfoCustomer (customerId) {
                get('api/customers/infomation/'+customerId)
                    .then((res) => {
                        this.setCustomerOnChat(res.data)
                        this.conversation.customer_id = res.data.id
                    })
            },
			outGroup(group) {
				let groupId = group.id;
				post('api/customers/outGroup/'+this.customerOnChat.id+'/'+groupId)
				.then((res) => {
					if (res.data.success) {
						EventBus.$emit('getListConversations');
						this.updateInfoCustomer(res.data.customer.id);
						$.notify('Bỏ nhãn khách hàng thành công', 'success');
					}
				})
				.catch((err) => {
					$.notify('Lỗi bỏ nhãn khách hàng', 'error');
				})
			},
			quickReply(ans) {
				this.mess = addName(this.customerOnChat.name_cus, ans.answer_text);
				this.showQuickTxtPanel = false;
				this.sendMessage()
			},
			sendOrderMessage(info) {
				this.mess = info;
			},
			reportCustomer() {
				EventBus.$emit('get-create-report');
			},
			getInfoCusrWithIdFb(part) {
				let fbId = part.from_id;
				let name = part.from_name;
				get('api/customers/infomation/fb/'+fbId)
				.then((res) => {
					let customer = res.data;
					if (customer.id) {
                		this.updateInfoCustomer(customer.id)
					} else {
						let customer = {
	                        name_cus: part.from_name,
	                        fb_id_cus: part.from_id,
	                        fb_page_id: this.conversation.fb_page_id,
	                        notes: [],
	                        reports: [],
	                        comments_count: 0,
	                        orders_count: 0,
	                        payments: [],
	                        id: ''
	                    }
                		this.setCustomerOnChat(customer)
                        this.conversation.customer_id = null
					}
					this.toggleCustomerPanel(true)
					$('.tabs-cus a[href="#board-order"]').tab('show')
				})
			},
			markRead(converId) {
                post('/api/conversations/markRead/'+ converId)
                .then((res) => {
                    if (res.data.success) {
                        EventBus.$emit('markRead', converId);
                    }
                })
            },
            addReports(data) {
            	this.reports.push(data);
            }
	    },
	    beforeDestroy () {
			EventBus.$off('load-conversation')
			EventBus.$off('updateConversation')
			EventBus.$off('sendOrderMessage')
			EventBus.$off('addReports')
		}
	}
</script>
<style type="text/css" scoped>
	.txt-tel {
		display: inline-block;
		width: 200px;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}
	.txt-getphone {
		display: inline-block;
	}
</style>