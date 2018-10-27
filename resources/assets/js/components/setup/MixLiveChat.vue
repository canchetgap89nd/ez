<template>
	<div class="cont-tab-st">
    	<div class="row">
	        <div class="form-group col-md-6">
	        	<label class="control-label">Chọn Fanpage</label>
	        	<select class="form-control" v-model="pageActive" @change="getWhiteListSite(pageActive.page_token)">
	        		<option v-for="page in pages" :value="page">{{ page.page_name }}</option>
	        	</select>
	        </div>
	        <div class="form-group col-md-12">
	        	<label style="margin-right: 20px">Chọn background</label>
	        	<div class="pick-color" id="bt-showpick-ch" :style="'position: absolute; top: -5px; background-color: '+color.hex" @click="togglePicker()"></div>
	        	<chrome-picker ref="colorPicker" v-model="color" v-show="displayPicker"></chrome-picker>
	        </div>
	        <div class="form-group col-md-12">
	        	<label>Câu mời chào khi khách đã đăng nhập Facebook</label>
	        	<input type="text" v-model="greet_if_login" class="form-control" placeholder="Nhập vào câu chào">
	        </div>
	        <div class="form-group col-md-12">
	        	<label>Câu mời chào khi khách chưa đăng nhập Facebook</label>
	        	<input type="text" v-model="greet_if_logout" class="form-control" placeholder="Nhập vào câu chào">
	        </div>
	        <div class="form-group col-md-12">
	        	<label class="checkbox-inline"><input v-model="minimized" type="checkbox">Thu nhỏ Icon Chat</label>
	        </div>
			
			<div class="form-group col-md-12">
				<label>Danh sách website</label>
				<v-input-tag :tags.sync="sites" placeholder="Nhập từ khóa"></v-input-tag>
			</div>
			
			<div class="form-group col-md-12">
	        	<button type="button" class="btn-cs" v-show="!isSaving" @click="saveWhitelistedSite()">Cập nhật website</button>
	        	<circle-load v-show="isSaving"></circle-load>
			</div>
    	</div>
	</div>
</template>

<script type="text/javascript">

	import { get } from '../../helpers/send'
	import { post } from '../../helpers/send'
	import Circle from '../loading/Circle.vue'
	import { Chrome } from 'vue-color'
	import InputTag from 'vue-input-tag'

	export default {
		components: {
			'circle-load': Circle,
			'chrome-picker': Chrome,
			'v-input-tag': InputTag,
		},
		props: ['user', 'role'],
		data() {
			return {
				isLoading: false,
				color: {
					hex: '#000'
				},
				displayPicker: false,
				pages: [],
				pageActive: {
					page_id: null
				},
				greet_if_login: null,
				greet_if_logout: null,
				sites: [],
				minimized: false,
				isSaving: false
			}
		},
		created() {
			this.getListFanpage();
		},
		computed: {
			textCode() {
				let html = '<div class="fb-customerchat" page_id="'+this.pageActive.page_id+'" theme_color="'+this.color.hex+'" logged_in_greeting="'+this.greet_if_login+'" logged_out_greeting="'+this.greet_if_logout+'" minimized="'+this.minimized+'"></div>';

				let scriptCode = '\<script lang="javascript"\>window.fbAsyncInit=function(){FB.init({appId:"933735970113789",autoLogAppEvents:!0,xfbml:!0,version:"v2.11"})},function(e,n,t){var o,s=e.getElementsByTagName(n)[0];e.getElementById(t)||((o=e.createElement(n)).id=t,o.src="https://connect.facebook.net/en_US/sdk.js",s.parentNode.insertBefore(o,s))}(document,"script","facebook-jssdk");\<\/script\>';

				if (this.color.hex && this.pageActive.page_id && this.greet_if_login && this.greet_if_logout) {
					return (html + scriptCode).trim();
				}

				return null;
			}
		},
		methods: {
			setColor(color) {
				this.color = color.hex;
			},
			showPicker() {
				document.addEventListener('click', this.documentClick);
				this.displayPicker = true;
			},
			hidePicker() {
				document.removeEventListener('click', this.documentClick);
				this.displayPicker = false;
			},
			togglePicker() {
				this.displayPicker ? this.hidePicker() : this.showPicker();
			},
			documentClick(e) {
				var el = this.$refs.colorPicker,
					bt = $("#bt-showpick-ch"),
					target = e.target;

				if((el.$el !== target) && (target !== bt[0]) && !el.$el.contains(target)) {
					this.hidePicker()
				}
			},
			getListFanpage() {
				this.isLoading = true;
				get('../../api/setting/filter-infomation/pages')
				.then((res) => {
					this.pages = res.data;
					this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải trang lọc thông tin', 'error');
					this.isLoading = false;
				})
			},
			saveWhitelistedSite() {
				this.isSaving = true;
				post('../../api/setting/mix/whitelisted-site', {
					sites: this.sites,
					page_token: this.pageActive.page_token
				})
				.then((res) => {
					if (res.data) {
						$.notify('Cập nhật thành công', 'success');	
					}
					this.isSaving = false;
				})
				.catch((err) => {
					$.notify('Lỗi cập nhật danh sách website', 'error');
					this.isSaving = false;
				})
			},
			getWhiteListSite(token) {
				get('../../api/setting/mix/whitelisted-site/get/'+token)
				.then((res) => {
					if (res.data) {
						this.sites = res.data;
					}
				})
				.catch((err) => {
					$.notify('Lỗi tải danh sách website', 'error');
				})
			}		
		}
	}
</script>