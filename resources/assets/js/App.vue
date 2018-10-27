<template>
	<div class="full-page" 
	v-loading="loading"
    element-loading-text="Đang tải..."
    element-loading-spinner="el-icon-loading"
    style="width: 100%"
	>
		<!-- Begin header -->
		<div class="head">
			<div class="head-tab">
				<div class="left-tab">
					<ul class="tab-container">
						<li>
							<router-link tag="a" :to="{ name: 'conversations'}" class="tab-item" v-if="role.name != 'STORAGER'">
								Hội thoại
							</router-link>
						</li>
						<li>
							<router-link tag="a" :to="{ name: 'customer'}" class="tab-item">
								Khách hàng
							</router-link>
						</li>
						<li>
							<router-link tag="a" :to="{ name: 'order'}" class="tab-item">
								Đơn hàng
							</router-link>
						</li>
						<li>
							<router-link tag="a" :to="{ name: 'product'}" class="tab-item">
								Sản phẩm
							</router-link>
						</li>
						<li v-if="role.name == 'ADMINSTRATOR' || role.name == 'MANAGER'">
							<router-link tag="a" :to="{ name: 'statistic'}" class="tab-item">
								Thống kê
							</router-link>
						</li>
						<li v-if="role.name == 'ADMINSTRATOR' || role.name == 'MANAGER' || role.name == 'SALER'">
							<router-link tag="a" :to="{ name: 'setting'}" class="tab-item tab-set">
								<i class="fa fa-cog"></i>
							</router-link>
						</li>
					</ul>
				</div>

				<div class="right-tab">
					<div class="package-active-container">
						<span class="pack-txt" v-if="package">
							Bạn đang sử dụng {{ package.display_name }}
						</span>
						<span class="pack-txt" v-else>
							Bạn đang sử dụng gói miễn phí
						</span>
					</div>
					<div class="dropdown pull-right dropdown-avatar" id="drHeadAva">
				    	<a href="javascript:;" class="item-fb active" data-toggle="dropdown">
							<img :src="'https://graph.facebook.com/'+ user.user_fb_id +'/picture?height=60&width=60'" class="ava-fb">
						</a>
						<ul class="dropdown-menu" id="dropDownAvatar">
					      	<li class="text-center"><a href="/setup/pages">Chọn thêm Fanpage</a></li>
					      	<li class="text-center"><a href="javascript:;" @click="showInfoUser()">Thông tin tài khoản</a></li>
					     	<li class="text-center"><a href="/setup/accounts" v-show="role.name == 'ADMINSTRATOR'">Phân quyền cho thành viên</a></li>
					      	<li class="text-center"><a href="/logout?app=WEB">Đăng xuất</a></li>
					      	<li class="divider"></li>
					      	<li>
					      		<a href="javascript:;" class="ls-ent-ava">
						      		<ul>
								      	<li v-for="page in pages">
								      		<a href="#">
									      		<div class="md-checkbox">
													<input checked="" name="pagesShow" :value="page.id" :id="'pageAva_'+page.fb_page_id" type="checkbox">
													<label :for="'pageAva_'+page.fb_page_id">
														<img :src="'https://graph.facebook.com/'+ page.fb_page_id + '/picture?height=40&width=40'">
														<span v-text="page.page_name"></span>
													</label>
												</div>
								      		</a>
								      	</li>
						      		</ul>
					      		</a>
					      	</li>
					      	<li>
					      		<div class="cont-button">
						      		<button class="btn btn-sm btn-success" @click="filterConversation()">OK</button>
						      		<button class="btn btn-sm btn-danger" @click="closeDropDown()">Hủy</button>
					      		</div>
					      	</li>
					    </ul>
				  	</div>
				  	<div class="notify pull-right">
						<a href="javascript:;" class="bell-notify">
							<i class="fa fa-bell"></i>
							<!-- <span class="badge badge-alert badge-bell">3</span> -->
						</a>
					</div>
				</div>

				<div class="humbergar">
					<div class="inner-humbergar">
						<div class="menu-mobile">
							<a href="javascript:;" class="icon-hum" onclick="">
								<i class="fa fa-align-justify"></i>
							</a>
							<ul class="menu-ls">
								
							</ul>
						</div>
					</div>
				</div>
			</div>
			<!-- <div class="notificationFromAdmin" v-show="showNotify">
				<a href="#" class="linkNotify">
					THÔNG BÁO: Chốt sale vừa nâng cấp server, việc tải những tin nhắn cũ sẽ update trong ngày 19/10/2018. Rất mong các bạn thông cảm!
				</a>
				<span class="closeNotify" @click="showNotify = false">
					<i class="el-icon-close"></i>
				</span>
			</div> -->
		</div>
		<!-- End header -->
		<div class="main">
			<div class="main-container">
				<router-view></router-view>
			</div>

			<!-- modal infomation customer -->
		  	<div class="modal fade" id="info_account_modal" tabindex="-1" role="dialog" aria-hidden="true">
			    <div class="modal-dialog">
			    	
			      	<div class="modal-content modal-add-order">
			        	<div class="modal-header">
			        		<div class="image-head-modal">
				        		<div class="avatar-modal">
				        			<img :src="'https://graph.facebook.com/'+ user.user_fb_id +'/picture?height=160&width=160'">
				        		</div>
			        		</div>
			        		<div class="name-cus-modal">
			        			<h3>{{ user.name }}</h3>
			        		</div>
		    			</div>
				        <div class="modal-body modal-body-cus">
				        	<div class="modal-order-cl">
				        		<div class="title-order-cl">
				        			<span class="txt-title-orderCl">Thông tin tài khoản</span>
				        		</div>
				        		<div class="info-order-add">
				        			<div class="info-row-add">
				        				<div class="title-row-order">
				        					<span class="icon-row-order icon-phone-order">
				        						<i class="fa fa-mobile-phone"></i>
				        					</span>
				        				</div>
				        				<div class="detail-row-order">
				        					<span class="txt-detail">
				        						{{ user.user_phone }}
				        					</span>
				        				</div>
				        			</div>
				        			<div class="info-row-add">
				        				<div class="title-row-order">
				        					<span class="icon-row-order">
				        						<i class="fa fa-envelope"></i>
				        					</span>
				        				</div>
				        				<div class="detail-row-order">
				        					<span class="txt-detail">
				        						{{ user.user_email }}
				        					</span>
				        				</div>
				        			</div>
				        		</div>
				        	</div>
				        	<div class="modal-order-cl">
				        		<div class="title-order-cl">
				        			<span class="txt-title-orderCl">Loại tài khoản</span>
				        		</div>
				        		<div class="note-order-add">
				        			{{ role.display_name }}
				        		</div>
				        	</div>
				        	<div class="cont-list-orderCus" v-if="role.name == 'ADMINSTRATOR' && mems.length > 0">
				        		<div class="title-order-cl">
				        			<span class="txt-title-orderCl">Danh sách tài khoản nhân viên</span>
				        		</div>
			        			<div class="cont-table-orderCus">
					        		<table class="table table-order-cus">
					        			<thead>
					        				<tr>
					        					<th class="text-center">Thành viên</th>
					        					<th class="text-center">Chức vụ</th>
					        					<th class="text-center">Fanpage</th>
					        				</tr>
				        				</thead>
					        			<tbody>
					        				<tr v-for="mem in mems">
					        					<td class="text-center">{{ mem.name }}</td>
					        					<td class="text-center">{{ mem.roles[0].display_name }}</td>
					        					<td class="text-center">
					        						<span v-for="page in mem.pages">
					        							{{ page.page_name }}, 
					        						</span>
					        					</td>
					        				</tr>
					        			</tbody>
					        		</table>
			        			</div>
				        	</div>
				        </div>
				        <div class="modal-footer modal-footer-cus">
				        	<button type="button" data-dismiss="modal" class="btn dismiss-table-orderCus pull-right">Đóng</button>
				        </div>
			      	</div>
			      
			    </div>
		  	</div>
		  	<!-- end modal infomation customer -->
		</div>

		<!-- guide helper -->
		<div class="guideTool" v-show="showGuide">
			<el-dropdown placement="top" trigger="click" :hide-on-click="true">
			  	<span class="el-dropdown-link">
				  	<div class="wrapGuide">
				  		<div class="gzGuide">
				  			<span class="ttSp">Hỗ trợ</span>
				  		</div>
					    <div class="guideControll">
					    	
					    </div>
				  	</div>
			  	</span>
			  	<el-dropdown-menu slot="dropdown">
				    <el-tooltip class="item" effect="dark" content="Chat hỗ trợ trực tuyến" placement="left-start">
						<a href="/chat-support" target="_blank" class="callSupport itemGuide"></a>
				    </el-tooltip>
					<el-tooltip class="item" effect="dark" content="Điện thoại hỗ trợ (024)73 050 105" placement="left-start">
						<a href="javascript:;" class="teleSupport itemGuide"></a>
				    </el-tooltip>
				    <el-tooltip class="item" effect="dark" content="Xem bài viết hướng dẫn" placement="left-start">
						<a href="https://chotsale.com.vn/blog/huong-dan-dang-ky-su-dung-phan-mem-chot-sale-12-08-2018.htm" target="_blank" class="bookSupport itemGuide"></a>
				    </el-tooltip>
			  	</el-dropdown-menu>
			</el-dropdown>
			<span class="closeGuide" @click="showGuide = false">
		    	<i class="el-icon-close"></i>
		    </span>
		</div>
		
		<!-- modal phone scan -->
		<el-dialog title="Số điện thoại tìm được" :visible.sync="phoneBoard" :show-close="false">
		    <div class="text-center">
		    	<span class="txtPhone">
		    		{{ phoneNumber }}
		    	</span>
		    </div>
		    <span slot="footer" class="dialog-footer">
			    <el-button type="success" @click="setPhoneBoard(false)">OK</el-button>
		  	</span>
		</el-dialog>
	</div>
</template>
<script type="text/javascript">
	
	import { get } from './helpers/send';
	import Auth from './store/auth';
	import { getCookie } from './helpers/cookies'
	import { EventBus } from './helpers/bus'
	import {mapGetters, mapActions} from 'vuex'

	export default {
		data () {
			return {
				showGuide: true,
				showNotify: true
			}
		},
		created () {
			Auth.initialize();
			this.$nextTick(() => {
				this.getInfomation();
			})
		},
		computed: {
			...mapGetters(['user', 'mems', 'role', 'setting', 'pages', 'package', 'loading']),
			...mapGetters('conversation', ['phoneBoard', 'phoneNumber'])
		},
		mounted() {
            var uid = getCookie('uid');
            var token = getCookie('sk_token');
            var socket = io(window.location.hostname, {
            	query: {token: token}
            });
            socket.on('chat.' + uid + ':message', (data) => {
            	console.log(data)
				let conversation = JSON.parse(data.message);
				// emit event to Interactive.vue and ChatBoard.vue
	            EventBus.$emit('updateConversation', conversation);
	            this.notifyWin(conversation)
            })
            $('#dropDownAvatar').on({
				"click":function(e){
		      		e.stopPropagation();
			    }
			});
        },
		methods: {
			...mapActions(['upUser', 'upSetting', 'upPages', 'upRole', 'upMems', 'upGroups', 'upQuickAns', 'setPack']),
			...mapActions('conversation', ['setPhoneBoard']),
			getInfomation() {
				get('/api/infomation/sumary')
				.then((res) => {
					let response = res.data;
					var pages = {};
					for (var i = 0; i < response.pages.length; i++) {
						var dataPage = response.pages[i];
	                    pages[dataPage.fb_page_id] = dataPage;
	                }
					this.upUser(response.user);
					this.upSetting(response.setting);
					this.upPages(pages);
					this.upRole(response.role);
					this.upMems(response.mems);
					this.upGroups(response.groups);
					this.upQuickAns(response.quickAns);
					this.setPack(response.package);
				})
			},
            closeDropDown() {
            	$("#drHeadAva").removeClass('open');
            },
            showInfoUser() {
            	this.closeDropDown();
            	$("#info_account_modal").modal('show');
            },
            filterConversation() {
            	let pagesShow = [];
            	$('input[name="pagesShow"]:checked').each(function(ind) {
            		pagesShow.push($(this).val());
            	});
            	let payload = {
            		status: "PAGE",
            		value: pagesShow
            	}
            	EventBus.$emit('filter-conversation', payload);
            	this.closeDropDown();
            },
            notifyWin(conversation) {
            	let fromId = conversation.just_message.from_id
            	let ofMe = false
            	for (let k in this.pages) {
            		if (parseInt(this.pages[k].fb_page_id) == parseInt(fromId)) {
            			ofMe = true
            			break
            		}
            	}
            	// notify if not me
    			if (!ofMe) {
    				let theBody = conversation.last_message,
	                	theTitle = conversation.just_message.from_name,
	                	options = {
					      	body: theBody,
					      	icon: 'images/logo-512x512.png'
					  	};
					Notification.requestPermission((permission) => {
				      	if (permission === "granted") {
							var notify = new Notification(theTitle, options);
							notify.onclick = (event) => {
								event.preventDefault();
									EventBus.$emit('loadConversation', conversation);
									window.focus();
							}
							setTimeout(notify.close.bind(notify), 4000);
			     	 	}
				    });
	                if (this.setting.ping_notify) {
	                    let sound = document.createElement("audio");
	                    sound.src = '/sound/Notification-02.mp3';
	                    sound.setAttribute("preload", "auto");
	                    sound.setAttribute("controls", "none");
	                    sound.style.display = "none";
	                    sound.play();
	                }
	                let newTitle = conversation.type == "MESSAGE" ? "Bạn đang có tin nhắn chưa đọc" : "Bạn đang có bình luận chưa đọc";
            		document.title = newTitle;
    			}
            }
		}
	}
</script>
<style type="text/css">
	ul.tab-container .tab-item.router-link-active {
		background: #009f3a;
	}
	ul.tab-container .tab-item.router-link-exact-active {
		background: #009f3a;
	}
</style>
<style type="text/css" scoped>
	.package-active-container {
		margin-right: 10px;
	}
	.pack-txt {
		font-size: 15px;
		color: #fff;
	}
	.txtPhone {
		font-size: 20px;
	}
</style>