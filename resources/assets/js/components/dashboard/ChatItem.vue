<template>
    <div>
        <div v-show="!conversation.hide" class="conver-item" v-for="conversation in conversations" :class="{ 'conver-item active': active_el == conversation.id, 'conver-item': active_el != conversation.id }" @click="openChat(conversation)">
            <div class="inner-conver-item" v-if="conversation.type == env.COMMENT_VAR">
                <div class="pic-avar">
                    <span :class="{ 'badge badge-gray badge-ava': active_el == conversation.id, 'badge badge-alert badge-ava': active_el != conversation.id }" v-show="conversation.unread_count">
                    	{{ conversation.unread_count }}
                    </span>
                    <img :src="'https://graph.facebook.com/'+ conversation.from_id + '/picture?height=40&width=40'" class="ava-fb pics-of">
                </div>
                <div class="info-of">
                    <h3 class="name-of" v-text="conversation.from_name"></h3>
                    <span class="has-key">
                    	<span class="is-chat item-ext" v-if="!conversation.unreply">
                            <i class="fa fa-reply" aria-hidden="true" title="Đã trả lời"></i>
                        </span>
                        {{ conversation.last_message }}
                    </span>
                    <span class="time-up">{{ formatTime(conversation.updated_time) }}</span>
                </div>
                <div class="more-info">
                    <div class="labels-of">
                    	<span v-if="conversation.customer">
        	                <span class="label-box" v-for="group in conversation.customer.groups">
        	                    <span class="label-card label-small" :style="{ 'background-color': group.group_color }"></span>
        	                </span>
                    	</span>
                    </div>
                    <div class="cart-of" v-if="conversation.customer">
                        <span class="carted item-cart" v-if="conversation.has_order" title="Có đơn hàng">
                            <i class="material-icons">shopping_cart</i>
                        </span>
                        <span class="receipt item-cart" v-if="conversation.has_note" title="Có ghi chú khách hàng">
                            <i class="material-icons">receipt</i>
                        </span>
                    </div>
                    <div class="info-extend">
                    	<span class="has-phone item-ext" title="Chứa từ khóa" v-show="conversation.has_key">
                            <i class="fa fa-key"></i>
                        </span>
                        <span class="has-phone item-ext" title="Chứa số điện thoại" v-show="conversation.has_phone">
                            <i class="fa fa-phone"></i>
                        </span>
                        <span class="is-chat item-ext">
                        	<i class="fa fa-comments-o" v-if="conversation.is_multiple_chat" title="Nhiều bình luận cấp 1"></i>
                        	<i class="fa fa-comment-o" v-else title="Một bình luận cấp 1"></i>
                        </span>
                        <span class="pic-box item-ext">
                            <img :src="'https://graph.facebook.com/'+ conversation.fb_page_id + '/picture?height=40&width=40'" class="pic-page-m">
                        </span>
                    </div>
                </div>
            </div>

           	<!-- type message -->
            <div class="inner-conver-item" v-else>
            	<div class="pic-avar">
                    <span :class="{ 'badge badge-gray badge-ava': active_el == conversation.id, 'badge badge-alert badge-ava': active_el != conversation.id }" v-show="conversation.unread_count">
                    	{{ conversation.unread_count }}
                    </span>
                    <img :src="'https://graph.facebook.com/'+ conversation.from_id + '/picture?height=40&width=40'" class="ava-fb pics-of">
                </div>
                <div class="info-of">
                    <h3 class="name-of" v-text="conversation.from_name"></h3>
                    <span class="has-key">
                    	<span class="is-chat item-ext" v-if="!conversation.unreply">
                            <i class="fa fa-reply" aria-hidden="true" title="Đã trả lời"></i>
                        </span>
                        {{ conversation.last_message }}
                    </span>
                    <span class="time-up">{{ formatTime(conversation.updated_time) }}</span>
                </div>
                <div class="more-info">
                    <div class="labels-of">
                    	<span v-if="conversation.customer">
        	                <span class="label-box" v-for="group in conversation.customer.groups">
        	                    <span class="label-card label-small" :style="{ 'background-color': group.group_color }">
        	                    </span>
        	                </span>
                    	</span>
                    </div>
                    <div class="cart-of" v-if="conversation.customer">
                        <span class="carted item-cart" v-if="conversation.has_order" title="Có đơn hàng">
                            <i class="material-icons">shopping_cart</i>
                        </span>
                        <span class="receipt item-cart" v-if="conversation.has_note" title="Có ghi chú khách hàng">
                            <i class="material-icons">receipt</i>
                        </span>
                    </div>
                    <div class="info-extend">
                    	<span class="has-phone item-ext" title="Chứa từ khóa" v-show="conversation.has_key">
                            <i class="fa fa-key"></i>
                        </span>
                        <span class="has-phone item-ext" title="Chứa số điện thoại" v-show="conversation.has_phone">
                            <i class="fa fa-phone"></i>
                        </span>
                        <span class="is-chat item-ext">
                            <i class="fa fa-envelope" title="Tin nhắn riêng"></i>
                        </span>
                        <span class="pic-box item-ext">
                            <img :src="'https://graph.facebook.com/'+ conversation.fb_page_id + '/picture?height=40&width=40'" class="pic-page-m">
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">

	import moment from 'moment'
	import { momentLocale } from '../../helpers/momentfix'
    import { EventBus } from '../../helpers/bus'
    import { get } from '../../helpers/send'
    import { post } from '../../helpers/send'
    import {mapActions} from 'vuex'
    var env = require('../../env')

	export default {
		props: ['conversations'],
        data() {
            return {
                active_el: '',
                env: env
            }
        },
        mounted() {
            EventBus.$on('loadConversation', (conversation) => {
                this.openChat(conversation);
            })
        },
		methods: {
            ...mapActions(
                'conversation', ['setCustomerOnChat']
            ),
			formatTime(time) {
				return momentLocale(time*1000).fromNow();
			},
            openChat(data) {
                if (this.active_el !== data.id) {
                    this.markRead(this.active_el);
                    this.active_el = data.id;
                    EventBus.$emit('load-conversation', data);
                    this.getInfoCustomer(data);
                }
            },
            getInfoCustomer(data) {
                if (data.customer) {
                    let customerId = data.customer.id;
                    this.updateInfoCustomer(customerId)
                } else {
                    let customer = {
                        name_cus: data.from_name,
                        fb_id_cus: data.from_id,
                        fb_page_id: data.fb_page_id,
                        notes: [],
                        reports: [],
                        comments_count: 0,
                        orders_count: 0,
                        payments: [],
                        id: ''
                    }
                    this.setCustomerOnChat(customer)
                }
            },
            updateInfoCustomer (customerId) {
                get('api/customers/infomation/'+customerId)
                    .then((res) => {
                        this.setCustomerOnChat(res.data)
                    })
            },
            markRead(converId) {
                if (converId) {
                    post('/api/conversations/markRead/'+ converId)
                    .then((res) => {
                        if (res.data.success) {
                            EventBus.$emit('markRead', converId);
                            document.title = 'Hội thoại';
                        }
                    })
                }
            }
		},
        beforeDestroy () {
            EventBus.$off('loadConversation')
        }
	}
</script>