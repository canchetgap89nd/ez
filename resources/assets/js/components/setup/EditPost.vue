<template>
	<div class="row" v-loading="loading">
		<div class="col-md-7">
	    	<div class="form-group">
	    		<div class="boxHead">
	        		<textarea rows="15" class="form-control inPost" v-model="message"></textarea>
	    		</div>
	    	</div>
	    	<div class="form-group" v-if="type == 'PHOTO'">
	    		<div class="containerUp" v-if="previewMedia">
	    			<div class="itemUp">
	    				<div class="headItemUp">
			    			<img :src="previewMedia" class="imageUp">
	    				</div>
		    		</div>
	    		</div>
	    	</div>
	    	<div class="form-group" v-else>
	    		<div class="containerUp" v-if="previewMedia">
	    			<div class="itemUp">
	    				<div class="headItemUp">
			    			<video width="320" height="240" controls>
								  <source :src="previewMedia" type="video/mp4">
								</video>
	    				</div>
		    		</div>
	    		</div>
	    	</div>
		</div>
		<div class="col-md-5">
			<div class="boxLeft">
				<div class="form-group">
					<label>Kênh đăng bài</label>
				</div>
				<div class="form-group">
					<ul class="nav nav-tabs">
					    <li class="active"><a data-toggle="tab" href="#facebook_channel">Facebook</a></li>
					    <li><a data-toggle="tab" href="#instagram_channel">Instagram</a></li>
					</ul>
					<div class="tab-content">
					    <div id="facebook_channel" class="tab-pane fade in active listSocial">
					    	<div class="checkbox itemChannel" v-for="page in pages">
						  		<label><input type="checkbox" checked disabled :value="page.id">{{ page.page_name }}</label>
							</div>
					    </div>
					    <div id="instagram_channel" class="tab-pane fade listSocial">
					        <div class="checkbox itemChannel">
						  		<label><input type="checkbox" value="">intagram</label>
							</div>
							<div class="checkbox itemChannel">
						  		<label><input type="checkbox" value="">intagram</label>
							</div>
							<div class="checkbox itemChannel">
						  		<label><input type="checkbox" value="">intagram</label>
							</div>
					    </div>
					</div>
				</div>
			</div>
			<div class="actionBox">
				<circle-load v-if="isPushlishing"></circle-load>
				<form class="form-inline" v-else>
			  		<button style="margin-right: 0;" type="button" @click="update(article.id)" :disabled="isPushlishing" class="btn-cs btnPub">Cập nhật</button>
				</form>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">

	import {mapGetters, mapActions} from 'vuex'
	import {post, del, get} from '../../helpers/send'
	import Circle from '../loading/Circle'
	import moment from 'moment'
	import { EventBus } from '../../helpers/bus'

	export default {
		name: 'create-post',
		components: {
			'circle-load': Circle
		},
		computed: {
			...mapGetters([
				'role',
				'user'
			])
		},
		data () {
			return {
				loading: false,
				message: null,
				isPushlishing: false,
				timeSchedule: null,
				pages: [],
				article: null,
				previewMedia: '',
				type: 'PHOTO'
			}
		},
		watch: {
			uploadedFiles () {
				this.uploadDone()
			}
		},
		mounted () {
			this.getEdit(this.$route.params.id)
		},
		methods: {
			...mapActions(['doNotify']),
			getEdit (id) {
				this.loading = true
				get('../../../../api/extensions/publish/edit/' + id)
				.then((res) => {
					if (res.data.success) {
						let response = res.data
						if (parseInt(response.article.type) == 1) {
							this.message = response.data.message
							this.type = 'PHOTO'
							this.previewMedia = response.data.full_picture
						} else {
							this.message = response.data.description
							this.type = 'VIDEO'
							this.previewMedia = response.data.source
						}
						this.pages = response.article.pages
						this.article = response.article
						this.loading = false
					}
				})
			},
			update (id) {
				this.isPushlishing = true
				post('../../../../api/extensions/publish/update/' + id, {message: this.message})
				.then((res) => {
					if (res.data.success) {
						this.reset()
						this.doNotify('Cập nhật bài viết thành công')
					} else {
						let message = {
							text: res.data.message,
							type: 'error'
						}
						this.doNotify(message)
					}
					let errors = res.data.errors
					if (errors) {
						for (let k in errors) {
							let message = {
								text: errors[k],
								type: 'warn'
							}
							this.doNotify(message)
						}
					}
					this.isPushlishing = false
				})
				.catch((err) => {
					let message = {
						text: "Lỗi cập nhật bài viết",
						type: 'error'
					}
					this.doNotify(message)
					this.isPushlishing = false
				})
			},
			reset () {
				this.message = ''
				this.pages = []
				EventBus.$emit('clearTabEdit')
				this.$router.push({name: 'publishedPost'})
			}
		}
	}
</script>
<style type="text/css" scoped>
	.inPost, .inPost:focus, .inPost:active {
		border: none;
		outline: none !important;
		box-shadow: unset;
		resize: none;
		height: 100%;
	}
	.boxHead {
		border: 1px solid #ddd;
		height: 342px;
	}
	.boxLeft {
		padding: 10px 20px;
    	border: 1px solid #ddd;
    	height: 342px;
	}
	.listSocial {
		overflow-y: auto;
		overflow-x: hidden;
		max-height: 230px;
	}
	.itemChannel {
		width: 100%;
		display: inline-block;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	.btnPub {
		margin: 0 0 0 10px;
	}
	.btnPub:first-child {
		margin-right: 10px;
	}
	.actionBox {
	    margin-top: 35px;
    	float: right;
	}
	.itemAction {
	    font-size: 20px;
    	margin: 0 5px;
    	position: relative;
    	z-index: 5;
	}
	.itemAction:first-child {
		margin-left: 0;
	}
	.attFile {
		position: absolute;
		top: 0;
		left: 0;
		width: 23px;
		height: 30px;
		overflow: hidden;
		opacity: 0;
		z-index: 9;
	}
	.containerUp {
		display: flex;
		justify-content: flex-start;
		max-width: 100%;
		flex-wrap: wrap;
		margin: 0 -5px;
	}
	.itemUp {
		position: relative;
		display: inline-block;
		margin: 5px;
		border: 1px solid #989494;
		text-align: center;
	}
	.closeUp {
		position: absolute;
		top: 0px;
		right: 5px;
		color: #a0a0a0;
		transition: all 0.3s;
		cursor: pointer;
	}
	.closeUp:hover {
		color: #ddd;
	}
	.imageUp {
	    width: 110px;
    	height: 100px;
	}
	.progressItemUp {
		display: inline-block;
		width: 100%;
	}
	.videoContainer {
		display: inline-block;
		position: relative;
	}
	::-webkit-scrollbar-track
	{
		-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
		border-radius: 3px;
		background-color: #F5F5F5;
	}

	::-webkit-scrollbar
	{
		width: 5px;
		background-color: #F5F5F5;
	}

	::-webkit-scrollbar-thumb
	{
		border-radius: 3px;
		-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
		background-color: #555;
	}
</style>