<template>
	<div class="cont-tab-st">
    	<div class="row">
	        <div class="form-group col-md-6">
	        	<label class="control-label">Chọn Fanpage</label>
	        	<input type="text" required="" placeholder="Nhập vào địa chỉ Fanpage" v-model="fanpage" class="form-control">
	        </div>
	        <!-- <div class="form-group col-md-12">
	        	<label style="margin-right: 20px">Chọn màu khung</label>
	        	<div class="pick-color" id="bt-showpick-ch1" :style="'position: absolute; top: -5px; background-color: '+color1.hex" @click="togglePicker(1)"></div>
	        	<chrome-picker ref="colorPicker1" v-model="color1" v-show="displayPicker1"></chrome-picker>
	        </div>
	        <div class="form-group col-md-12">
	        	<label style="margin-right: 35px">Chọn màu chữ</label>
	        	<div class="pick-color" id="bt-showpick-ch2" :style="'position: absolute; top: -5px; background-color: '+color2.hex" @click="togglePicker(2)"></div>
	        	<chrome-picker ref="colorPicker2" v-model="color2" v-show="displayPicker2"></chrome-picker>
	        </div>
	        <div class="form-group col-md-12">
	        	<label>Tiêu đề thu nhỏ</label>
	        	<input type="text" v-model="greetCollep" class="form-control" placeholder="Nhập vào câu chào">
	        </div>
	        <div class="form-group col-md-12">
	        	<div class="checkbox">
				  	<label><input type="checkbox" v-model="minimized">Hiện thu nhỏ</label>
				</div>
	        </div>
	        <div class="form-group col-md-12">
	        	<div class="radio">
				  	<label><input v-model="place" type="radio" value="1">Vị trí góc bên dưới phải</label>
				</div>
	        </div>
	        <div class="form-group col-md-12">
	        	<div class="radio">
				  	<label><input v-model="place" type="radio" value="2">Vị trí góc bên dưới trái</label>
				</div>
	        </div>
         	<div class="form-group col-md-12">
	        	<label>Cách lề (Trái/Phải)</label>
	        	<input type="number" placeholder="Nhập vào số pixel" class="form-control">
	        </div> -->
														
			<div class="form-group col-md-12">
	        	<button type="button" class="btn-cs" v-show="!isSaving" @click="getCode()">Lấy mã nhúng</button>
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
	import { EventBus } from '../../helpers/bus'

	export default {
		components: {
			'circle-load': Circle,
			'chrome-picker': Chrome
		},
		data() {
			return {
				isLoading: false,
				fanpage: null,
				color1: {
					hex: '#000'
				},
				color2: {
					hex: '#000'
				},
				displayPicker1: false,
				displayPicker2: false,
				greetCollep: null,
				minimized: false,
				place: 1,
				isSaving: false
			}
		},
		methods: {
			setColor(color) {
				this.color = color.hex;
			},
			showPicker(type) {
				document.addEventListener('click', this.documentClick);
				if (type == 1) {
					this.displayPicker1 = true;
				} else 
				if (type == 2) {
					this.displayPicker2 = true;
				}
			},
			hidePicker(type) {
				if (type == 1) {
					this.displayPicker1 = false;
				} else 
				if (type == 2) {
					this.displayPicker2 = false;
				}
			},
			togglePicker(type) {
				switch (type) {
					case 1: 
						this.displayPicker1 ? this.hidePicker(1) : this.showPicker(1);
						break;
					case 2:
						this.displayPicker2 ? this.hidePicker(2) : this.showPicker(2);
						break;
					default: break;
				}
			},
			getCode() {
				if (this.fanpage) {
					let scriptCode = '\<script lang="javascript"\>window.fbAsyncInit=function(){FB.init({appId:"933735970113789",autoLogAppEvents:!0,xfbml:!0,version:"v2.11"})},function(e,n,t){var o,s=e.getElementsByTagName(n)[0];e.getElementById(t)||((o=e.createElement(n)).id=t,o.src="https://connect.facebook.net/en_US/sdk.js",s.parentNode.insertBefore(o,s))}(document,"script","facebook-jssdk");\<\/script\>';
					let html = '<div class="fb-page" data-href="'+ this.fanpage +'" data-hide-cover="false" data-show-facepile="false" data-tabs="messages" hide_cover="false"></div>';
					let code = scriptCode + html;
					EventBus.$emit('show-code-plugin', code);
				} else {
					$.notify('Vui lòng nhập vào địa chỉ Fanpage', 'error');
				}
			}
		}
	}
</script>