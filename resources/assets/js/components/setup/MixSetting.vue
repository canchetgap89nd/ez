<template>
	<div v-show="role.name == 'ADMINSTRATOR'">
		<div class="inner-cont" v-if="isLoading">
			<circle-load></circle-load>
		</div>
		<div class="inner-cont" v-else>
			<div class="row">
				<div class="col-md-6">
					<h3 class="title-bl-st">
						Bước 1: Tùy chọn
	                </h3>
					<div class="body-bl-st">
						<ul class="nav nav-tabs">
						    <li class="active"><a data-toggle="tab" href="#chat-fb-type1">Facebook Chat</a></li>
						    <li><a data-toggle="tab" href="#link-refer">Link liên kết</a></li>
						    <li><a data-toggle="tab" href="#bot-chat">Bot Chat</a></li>
						    <li><a data-toggle="tab" href="#popup-mx">Popup</a></li>
						</ul>

						<div class="tab-content">

						    <div id="chat-fb-type1" class="tab-pane fade in active">
						        <mix-chat-type1></mix-chat-type1>
						    </div>
						    <div id="link-refer" class="tab-pane fade">
						        
						    </div>
						    <div id="bot-chat" class="tab-pane fade">
						        
						    </div>
						    <div id="popup-mx" class="tab-pane fade">
						        
						    </div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<h3 class="title-bl-st">Bước 2: Sao chép và dán mã nhúng</h3>
					<p class="help-block">
						Sao chép và dán mã nhúng vào trước thẻ đóng body (phần footer) trong bất kỳ 
						Website nào của bạn.
					</p>
					<div class="form-group">
						<textarea class="form-control" rows="20" v-text="code"></textarea>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</template>

<script type="text/javascript">

	import SlideBar from './SlideBar.vue'
	import { get } from '../../helpers/send'
	import { post } from '../../helpers/send'
	import Circle from '../loading/Circle.vue'
	import { Chrome } from 'vue-color'
	import InputTag from 'vue-input-tag'
	import MixChatType1 from './MixChatType1.vue'
	import { EventBus } from '../../helpers/bus'

	export default {
		components: {
			'circle-load': Circle,
			'slide-bar': SlideBar,
			'mix-chat-type1': MixChatType1
		},
		computed: {
			role() {
				return this.$store.state.role;
			},
			user() {
				return this.$store.state.user;
			}
		},
		mounted() {
			EventBus.$on('show-code-plugin', (code) => {
				this.code = code;
			})
		},
		data() {
			return {
				isLoading: false,
				code: ''
			}
		}
	}
</script>