<template>
	<div class="main-chat" id="container_chat">
		<div class="inner-main-c">

			<!-- UI info post -->
			<div class="post-info-cont" v-if="post">
				<div class="inner-info-post" @click="changeTarget(post.fb_page_id, 'Bài viết')">
					<div class="post-by">
						<span class="title-post-by">Được đăng bởi</span>
						<span class="author-by" v-text="post.fb_page_name"></span>
					</div>
					<div class="time-post">
						<span class="txt-time-post" v-text="formatTime(post.created_time, 'DD/MM/YYYY')"></span>
					</div>
					<div class="content-post">
						<p class="txt-content-post" v-text="post.message"></p>
					</div>
					<div class="cont-images-post">
						<a href="javascript:;" class="zoom-thumb-post">
							<img v-viewer :src="post.picture" class="thumb-post">
						</a>
					</div>
				</div>
				<div class="more-info-post">
					<a v-if="post" :href="'https://www.facebook.com/' + post.fb_post_id" target="_blank" class="link-more-post">Xem thêm</a>
				</div>
			</div>
			<!-- UI info post -->

			<div class="text-center" v-if="messages.next_page_url">
				<span v-if="isLoadingMore">
					<i class="fa fa-spinner fa-pulse fa-fw"></i>
					<span class="sr-only">Loading...</span>
				</span>
				<span v-else class="more-message" @click="loadMore(messages.next_page_url)">Xem thêm</span>
			</div>
		
			<!-- UI chatting -->
			<div v-for="message in reverse(messages.data)" v-if="conversation.type == env.MESSAGE_VAR">
				<div class="item-chat chating-cus" v-if="conversation.fb_page_id != message.from_id">
					<div class="inner-item-c">
						<div class="box-avar-sb">
							<a href="javascript:;" class="link-cus" @click="getLink(message.from_id)">
								<img :src="'https://graph.facebook.com/'+ message.from_id + '/picture?height=40&width=40'" class="ava-fb ava-cus_2">
							</a>
						</div>
						<div class="content-chat">
							<div class="inner-cont-c">
								<div class="txt-chat">
									<span v-text="message.message"></span>
									<span v-if="message.attachments">
										<a href="#" v-for="att in message.attachments">
											<img v-viewer class="image-message" v-if="att.type == 'IMAGE' || 'STICKER'" :src="att.url">
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
											<img v-viewer class="image-message" v-if="att.type == 'IMAGE' || 'STICKER'" :src="att.url">
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

			<!-- UI Commenting -->
			<div v-for="(comment, index) in reverse(messages.data)" v-if="conversation.type == env.COMMENT_VAR">
				<div v-if="comment.from_id == conversation.fb_page_id" class="item-chat not-cus">
					<div class="inner-item-c">
						<div :class="{'content-chat markup': comment.is_remove, 'content-chat': !comment.is_remove}">
							<div class="inner-cont-c">
								<div class="txt-chat">
									<span v-html="comment.message" class="txt-black"></span>
									<a href="#" v-if="comment.attachment">
										<img v-viewer v-if="comment.attachment.type == 'IMAGE' || 'STICKER'" class="image-message" :src="comment.attachment.url">
									</a>
								</div>
							</div>
							<span v-show="comment.unread || comment.just_reply" class="badge badge-alert badge-chat">New</span>
						</div>
						<div class="time-of-chat">
							<div class="cont-time">
								<span class="time">{{ formatTime(comment.created_time, 'HH:mm:ss DD/MM/YYYY') }}</span>
							</div>
							<ul class="action-of-c" v-show="!comment.is_remove">
								<li v-show="comment.can_remove">
									<a href="#" title="Xóa bình luận" class="item-ac-c" @click="destroyComment(comment, index)" data-toggle="modal" data-target="#modal-del-comment">
										<i class="fa fa-trash"></i>
									</a>
								</li>
								<li>
									<a class="item-ac-c" :href="'https://www.facebook.com/'+comment.fb_comment_id" title="Xem trên facebook" target="_blank">
										<i class="fa fa-facebook-official"></i>
									</a>
								</li>
								<li v-show="comment.can_hide">
									<a href="#" @click="hideComment(comment)" :class="{ 'item-ac-c active': comment.is_hidden, 'item-ac-c': ! comment.is_hidden }">
										<i class="fa fa-eye-slash"></i>
									</a>
								</li>
								<li v-show="comment.can_like">
									<a href="#" :class="{ 'item-ac-c active': comment.user_likes, 'item-ac-c': ! comment.user_likes }" @click="likeComment(comment)">
										<i class="fa fa-thumbs-o-up"></i>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>

				<div v-else class="item-chat">
					<div class="inner-item-c">
						<div :class="{'content-chat markup': comment.is_remove, 'content-chat': !comment.is_remove}">
							<div class="inner-cont-c">
								<h3 class="name-of name-of-cus" @click="getLink(comment.from_id)" v-text="comment.from_name"></h3>
								<div class="txt-chat">
									<span v-html="comment.message"></span>
									<a href="#" v-if="comment.attachment">
										<img v-viewer v-if="comment.attachment.type == 'IMAGE' || 'STICKER'" class="image-message" :src="comment.attachment.url">
									</a>
								</div>
							</div>
							<span v-show="comment.unread || comment.just_reply" class="badge badge-alert badge-chat">New</span>
						</div>
						<div class="time-of-chat">
							<span class="time">{{ formatTime(comment.created_time, 'HH:mm:ss DD/MM/YYYY') }}</span>
							<a href="#" @click="changeTarget(comment.fb_comment_id, comment.from_name, comment.message)" class="click-rep" v-show="!comment.is_remove">Trả lời</a>
							<ul class="action-of-c" v-show="!comment.is_remove">
								<li v-show="comment.can_like">
									<a href="#" :class="{ 'item-ac-c active': comment.user_likes, 'item-ac-c': !comment.user_likes }" @click="likeComment(comment)">
										<i class="fa fa-thumbs-o-up"></i>
									</a>
								</li>
								<li>
									<a class="item-ac-c" :href="'https://www.facebook.com/'+comment.fb_comment_id" title="Xem trên facebook" target="_blank">
										<i class="fa fa-facebook-official"></i>
									</a>
								</li>
								<li>
									<a href="#" title="Nhắn tin riêng" class="item-ac-c" @click="replyPrivate(comment)">
										<i class="fa fa-commenting"></i>
									</a>
								</li>
								<li v-show="comment.can_hide">
									<a href="#" :class="{ 'item-ac-c': !comment.is_hidden, 'item-ac-c active': comment.is_hidden }" @click="hideComment(comment)">
										<i class="fa fa-eye-slash"></i>
									</a>
								</li>
								<li v-show="comment.can_remove">
									<a href="#" title="Xóa bình luận" class="item-ac-c"  @click="destroyComment(comment, index)" data-toggle="modal" data-target="#modal-del-comment">
										<i class="fa fa-trash"></i>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				
				<div v-if="comment.childs" v-for="(child, index) in reverse(comment.childs)">
					<div v-if="child.from_id == conversation.fb_page_id" class="item-chat not-cus">
						<div class="inner-item-c">
							<div :class="{'content-chat markup': child.is_remove, 'content-chat': !child.is_remove}">
								<div class="inner-cont-c">
									<div class="txt-chat">
										<span v-text="child.message" class="txt-black"></span>
										<a href="#" v-if="child.attachment">
											<img v-viewer v-if="child.attachment.type == 'IMAGE' || 'STICKER'" class="image-message" :src="child.attachment.url">
										</a>
									</div>
								</div>
								<span v-show="child.unread || child.just_reply" class="badge badge-alert badge-chat">New</span>
							</div>
							<div class="time-of-chat">
								<div class="cont-time">
									<span class="time">{{ formatTime(child.created_time, 'HH:mm:ss DD/MM/YYYY') }}</span>
								</div>
								<ul class="action-of-c" v-show="!child.is_remove">
									<li v-show="child.can_remove">
										<a href="#" title="Xóa bình luận" class="item-ac-c" @click="destroyComment(child, index, 2)" data-toggle="modal" data-target="#modal-del-comment">
											<i class="fa fa-trash"></i>
										</a>
									</li>
									<li>
										<a class="item-ac-c" :href="'https://www.facebook.com/'+child.fb_comment_id" title="Xem trên facebook" target="_blank">
											<i class="fa fa-facebook-official"></i>
										</a>
									</li>
									<li v-show="child.can_hide">
										<a href="#" :class="{ 'item-ac-c': !child.is_hidden, 'item-ac-c active': child.is_hidden }" @click="hideComment(child)">
											<i class="fa fa-eye-slash"></i>
										</a>
									</li>
									<li v-show="child.can_like">
										<a href="#" :class="{ 'item-ac-c active': child.user_likes, 'item-ac-c': !child.user_likes }" @click="likeComment(child)">
											<i class="fa fa-thumbs-o-up"></i>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>

					<div v-else class="item-chat cus-sub">
						<div class="inner-item-c">
							<div class="box-avar-sb">
								<a href="javascript:;" @click="getLink(child.from_id)">
									<img :src="'https://graph.facebook.com/'+ child.from_id + '/picture?height=40&width=40'" class="ava-fb ava-cus_2">
								</a>
							</div>
							<div :class="{'content-chat markup': child.is_remove, 'content-chat': !child.is_remove}">
								<div class="inner-cont-c">
									<h3 class="name-of title-black" v-text="child.from_name"></h3>
									<div class="txt-chat">
										<span v-text="child.message"></span>
										<a href="#" v-if="child.attachment">
											<img v-viewer v-if="child.attachment.type == 'IMAGE' || 'STICKER'" class="image-message" :src="child.attachment.url">
										</a>
									</div>
								</div>
								<span v-show="child.unread || child.just_reply" class="badge badge-alert badge-chat">New</span>
							</div>
							<div class="time-of-chat">
								<span class="time">{{ formatTime(child.created_time, 'HH:mm:ss DD/MM/YYYY') }}</span>
								<a href="#" @click="changeTarget(comment.fb_comment_id, child.from_name, child.message)" class="click-rep" v-show="!child.is_remove">Trả lời</a>
								<ul class="action-of-c" v-show="!child.is_remove">
									<li v-show="child.can_like">
										<a href="#" :class="{ 'item-ac-c active': child.user_likes, 'item-ac-c': !child.user_likes }" @click="likeComment(child)">
											<i class="fa fa-thumbs-o-up"></i>
										</a>
									</li>
									<li>
										<a class="item-ac-c" :href="'https://www.facebook.com/'+child.fb_comment_id" title="Xem trên facebook" target="_blank">
											<i class="fa fa-facebook-official"></i>
										</a>
									</li>
									<li>
										<a href="#" title="Nhắn tin riêng" class="item-ac-c" @click="replyPrivate(child)">
											<i class="fa fa-commenting"></i>
										</a>
									</li>
									<li v-show="child.can_hide">
										<a href="#" :class="{ 'item-ac-c': !child.is_hidden, 'item-ac-c active': child.is_hidden }" @click="hideComment(child)">
											<i class="fa fa-eye-slash"></i>
										</a>
									</li>
									<li v-show="child.can_remove">
										<a href="#" class="item-ac-c"  @click="destroyComment(child, index, 2)" title="Xóa bình luận" data-toggle="modal" data-target="#modal-del-comment">
											<i class="fa fa-trash"></i>
										</a>
									</li>
									<li>
										<a href="javascript:;" class="item-get-phone" @click="getPhone(child.from_id)">
											Tìm số điện thoại
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- end UI commenting -->
			<div class="item-chat not-cus" v-show="isSaving">
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
			<div class="item-chat not-cus" v-show="isFailed">
				<div class="inner-item-c">
					<div class="content-chat">
						<div class="inner-cont-c">
							<div class="txt-chat">
								<span>
									<a href="#" class="text-danger">Gửi lỗi!</a>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<reply-private v-if="showReplyPrivate" :post="post" :messages="messagesFromTarget" :comment="commentReplyPrivate" :conversation="conversationFromTarget" :page="pageOfTarget" @close="showReplyPrivate = false" @updateConverPrivate="updateConverPrivate"></reply-private>
	</div>
</template>
<script type="text/javascript">

	import { EventBus } from '../../helpers/bus'
	import { post } from '../../helpers/send'
	import { get } from '../../helpers/send'
	import { del } from '../../helpers/send'
	import { momentLocale } from '../../helpers/momentfix'
	import ReplyPrivate from './ReplyPrivate.vue'
	import {mapActions} from 'vuex'

	import Viewer from 'v-viewer'
  	import Vue from 'vue'
  	Vue.use(Viewer)
	import AWN from "awesome-notifications"
  	var notifier = new AWN();
  	var env = require('../../env')

	const STATUS_INITIAL = 0, STATUS_SAVING = 1, STATUS_SUCCESS = 2, STATUS_FAILED = 3;

	export default {
		components: {
			'reply-private': ReplyPrivate
		},
		props: ['conversation', 'messages', 'customer', 'currentStatus', 'target', 'post'],
		data() {
			return {
				autoScroll: true,
				isLoadingMore: false,
				showReplyPrivate: false,
				commentReplyPrivate: null,
				messagesFromTarget: null,
				conversationFromTarget: null,
				pageOfTarget: null,
				env: env
			}
		},
		computed: {
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
	      	}
	    },
		mounted() {
			this.scrollToEnd("container_chat");
			EventBus.$on('autoScroll', (type) => {
				this.autoScroll = type;
			})
			EventBus.$on('updateMessageNew', (conversation) => {
				if (this.commentReplyPrivate && parseInt(conversation.from_id) === parseInt(this.commentReplyPrivate.from_id) && parseInt(this.pageOfTarget.fb_page_id) === parseInt(conversation.fb_page_id) && conversation.type == env.MESSAGE_VAR) {
					this.updateMessage(conversation.just_message);
				}
			})
		},
		updated() {
			this.scrollToEnd("container_chat");
		},
		methods: {
			...mapActions('conversation', ['getPhone', 'getLink']),
			reverse(arr) {
				if (arr) {
					return arr.slice().reverse();
				}
				return [];
			},
			formatTime(time, formater) {
				return momentLocale(time*1000).format(formater);
			},
			scrollToEnd(id) {
		      	let container = document.getElementById(id);
			  	if (container && this.autoScroll) {
			      	container.scrollTop = container.scrollHeight;
			  	}
		    },
	    	loadMore(url) {
	    		this.autoScroll = false;
	    		this.isLoadingMore = true;
    			get(url)
    			.then((res) => {
    				this.messages.next_page_url = res.data.messages.next_page_url;
    				let messages = res.data.messages.data;
    				messages.forEach((el) => {
    					this.messages.data.push(el);
    				});
    				this.isLoadingMore = false;
    			})
	    	},
	    	changeTarget(id, name, message) {
	    		this.target.id = id;
	    		this.target.name = name;
	    		this.target.message = message;
	    		this.$emit('focusInput');
	    	},
	    	destroyComment(comment, index, type = 1) {
	    		notifier.confirm('Xóa bình luận', () => {
	    			this.yesDeleteComment(comment, index, type);
	    		})
	    	},
	    	yesDeleteComment(comment, index, type) {
	    		post('/api/conversations/delete/comment/'+this.conversation.id+'/'+ comment.id)
	    		.then((res) => {
	    			if (res.data.success) {
	    				if (comment.childs) {
		    				let childs = comment.childs;
		    				childs.forEach((el) => {
		    					el.is_remove = true;
			    				el.unread = false;
			    				el.has_key = false;
			    				el.has_phone = false;
		    				})
	    				}
	    				comment.is_remove = true;
	    				comment.unread = false;
	    				comment.has_key = false;
	    				comment.has_phone = false;
	    				$.notify('Xóa thành công', 'success');
	    			}
	    		})
	    		.catch((err) => {
	    			$.notify('Lỗi xóa bình luận', 'error');
	    		})
	    	},
	    	replyPrivate(comment) {
	    		this.commentReplyPrivate = comment;
	    		new Promise((resolve, reject) => {
	    			this.loadMessagePrivate(comment);
	    		})
	    		notifier.asyncBlock(Promise.resolve("all done"));
	    	},
	    	loadMessagePrivate(comment) {
	    		let fromId = comment.from_id;
	    		get('/api/conversations/messages/private/'+fromId+'?fb_page_id='+this.conversation.fb_page_id)
				.then((res) => {
					let messageBox = {
						data: []
					}
					let converBox = {
						id: null,
						type: ''
					}
					this.messagesFromTarget = res.data.messages ? res.data.messages : messageBox;
					this.conversationFromTarget = res.data.conversation ? res.data.conversation : converBox;
					this.pageOfTarget = res.data.page;
					if (res.data.conversation || comment.can_reply_privately) {
						this.showReplyPrivate = true;
					} else {
						$.notify('Không thể nhắn tin tới đối tượng này', 'warn');
					}
				})
				.catch((err) => {
					$.notify('Lỗi tải tin nhắn', 'error');
				})
	    	},
	    	hideComment(comment) {
	    		let commentId = comment.id;
	    		let fbPageId = this.conversation.fb_page_id;
	    		let converId = this.conversation.id;
	    		let isHide = comment.is_hidden ? 0 : 1;
	    		comment.is_hidden = isHide;
    			post('/api/conversations/comments/hide/'+ commentId, {
	    			fb_page_id: fbPageId,
	    			converId: converId,
	    			isHide: isHide
	    		})
	    		.then((res) => {
	    			if (res.data.success) {
	    				let cmt = res.data.data;
	    				comment.user_likes = cmt.user_likes;
	    				comment.is_hidden = cmt.is_hidden;
	    				comment.can_hide = cmt.can_hide;
	    				comment.can_like = cmt.can_like;
	    				comment.can_reply_privately = cmt.can_reply_privately;
	    				comment.can_remove = cmt.can_remove;
	    			}
	    		})
	    		.catch((err) => {
	    			$.notify('Lỗi ẩn bình luận', 'error');
	    		})
	    	},
	    	likeComment(comment) {
	    		let commentId = comment.id;
	    		let fbPageId = this.conversation.fb_page_id;
	    		let converId = this.conversation.id;
	    		let isLike = comment.user_likes ? false : true;
	    		comment.user_likes = isLike;
    			post('/api/conversations/comments/like/'+ commentId, {
	    			fb_page_id: fbPageId,
	    			converId: converId,
	    			isLike: isLike
	    		})
	    		.then((res) => {
	    			if (res.data.success) {
	    				let cmt = res.data.data;
	    				comment.user_likes = cmt.user_likes;
	    				comment.is_hidden = cmt.is_hidden;
	    				comment.can_hide = cmt.can_hide;
	    				comment.can_like = cmt.can_like;
	    				comment.can_reply_privately = cmt.can_reply_privately;
	    				comment.can_remove = cmt.can_remove;
	    			}
	    		})
	    		.catch((err) => {
	    			$.notify('Lỗi thích bình luận', 'error');
	    		})
	    	},
	    	updateMessage(message) {
				let has = this.messagesFromTarget.data.some(function(el) {
					return el.id == message.id;
				})
				if (!has) {this.messagesFromTarget.data.unshift(message)};
			},
			updateConverPrivate(conversation) {
				this.conversationFromTarget = conversation;
			}
		},
		beforeDestroy () {
            EventBus.$off('autoScroll')
            EventBus.$off('updateMessageNew')
        }
	}
</script>