<template>
	<div class="row" v-loading="loading">
		<div class="col-md-5">
			<div class="boxLeft">
				<div class="form-group">
					<label>Chọn kênh đăng bài</label>
				</div>
				<div class="form-group">
					<ul class="nav nav-tabs">
					    <li class="active"><a data-toggle="tab" href="#facebook_channel">Facebook</a></li>
					    <li><a data-toggle="tab" href="#instagram_channel">Instagram</a></li>
					</ul>
					<div class="tab-content">
					    <div id="facebook_channel" class="tab-pane fade in active listSocial">
					    	<div class="checkbox itemChannel" v-for="page in pages">
						  		<label><input type="checkbox" :disabled="isUploadding || (uploadedFiles.length > 0)" v-model="pagesActive" :value="page.id">{{ page.page_name }}</label>
							</div>
					    </div>
					    <div id="instagram_channel" class="tab-pane fade listSocial">

					    </div>
					</div>
				</div>
			</div>
			<div class="actionBox">
				<circle-load v-if="isPushlishing"></circle-load>
				<form class="form-inline" v-else>
					<button type="button" @click="publishPost" :disabled="isUploadding" class="btn-cs btnPub">Đăng bài</button>
				  	<el-date-picker
			  			class="pickerTime"
				      	v-model="timeSchedule"
				      	type="datetime"
				      	placeholder="Chọn thời gian xuất bản"
				      	format="dd-MM-yyyy HH:mm:ss"
				      	value-format="MM-dd-yyyy HH:mm:ss"
			      	>
				    </el-date-picker>
				</form>
			</div>
		</div>
		<div class="col-md-7">
	    	<div class="form-group">
	    		<div class="boxHead">
	        		<textarea rows="15" placeholder="Nhập vào nội dung bài viết" autofocus="" class="form-control inPost" v-model="message"></textarea>
	    		</div>
	    	</div>
	    	<div class="form-group">
	    		<a href="javascript:;" class="itemAction" id="attImage">
	    			<i class="fa fa-camera"></i>
	    			<input type="file" accept="image/png, image/jpeg, image/bmp, image/tiff, image/gif" title="Chọn ảnh có định dạng .png, .jpeg, .bmp, .tiff hoặc .gif" multiple class="attFile" :disabled="isUploadding" id="attFileImage" @change="attFile">
	    		</a>
	    		<a href="javascript:;" class="itemAction">
	    			<i class="fa fa-video-camera"></i>
	    			<input type="file" class="attFile" title="Chọn Video có định dạng 3g2, 3gp, 3gpp, asf, avi, dat, divx, dv, f4v, flv, gif, m2ts, m4v, mkv, mod, mov, mp4, mpe, mpeg, mpeg4, mpg, mts, nsv, ogm, ogv, qt, tod, ts, vob hoặc wmv" accept=".3g2, .3gp, .3gpp, .asf, .avi, .dat, .divx, .dv, .f4v, .flv, .gif, .m2ts, .m4v, .mkv, .mod, .mov, .mp4, .mpe, .mpeg, .mpeg4, .mpg, .mts, .nsv, .ogm, .ogv, .qt, .tod, .ts, .vob, .wmv" :disabled="isUploadding || uploadedFiles.length > 0" id="attFileVideo" @change="attVideo">
	    		</a>
	    	</div>
	    	<div class="form-group">
	    		<div class="containerUp">
	    			<div class="itemUp" v-for="(item, index) in prepareImage">
	    				<div class="headItemUp">
			    			<img :src="item.data" class="imageUp">
			    			<span class="closeUp" title="Gỡ ảnh" v-if="!isUploadding" @click="removeImage(index)">
			    				<i class="fa fa-close"></i>
			    			</span>
	    				</div>
		    		</div>
	    		</div>
	    		<div class="containerUp" v-if="videoUrl">
	    			<div class="itemUp">
	    				<div class="headItemUp">
			    			<img :src="videoUrl" class="imageUp">
			    			<span class="closeUp" title="Gỡ video" v-if="!isUploadding" @click="removeVideo()">
			    				<i class="fa fa-close"></i>
			    			</span>
	    				</div>
		    		</div>
	    		</div>
	    	</div>
	    	<div class="form-group" v-if="isUploadding">
	    		<circle-load></circle-load>
	    	</div>
	    	<div class="noteBoxTime">
			 	<strong>Chú ý:</strong> Vui lòng chọn Fanpage trước khi chọn ảnh/video để tải lên. <br>
			 	Chọn thời gian đăng bài trong khoảng lớn hơn 30 phút và nhỏ hơn 6 tháng so với hiện tại
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">

	import {mapGetters, mapActions} from 'vuex'
	import {post, del, get} from '../../helpers/send'
	import Circle from '../loading/Circle'
	import moment from 'moment'

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
				pagesActive: [],
				message: null,
				isPushlishing: false,
				timeSchedule: '',
				uploadedFiles: [],
				prepareImage: [],
				mediaUpWithPost: {},
				isUploadding: false,
				uploadedPost: [],
				pageIsUploaded: [],
				uploadedPostSuccess: [],
				type: "PHOTO",
				videoUrl: '',
				pages: []
			}
		},
		watch: {
			uploadedFiles () {
				this.uploadDone()
			}
		},
		mounted () {
			this.getPages()
		},
		methods: {
			...mapActions(['doNotify']),
			getPages () {
				this.loading = true
				get('../../../api/pages/hasRole')
				.then((res) => {
					this.pages = res.data.pages
					this.loading = false
				})
			},
			attFile (e) {
				if (this.type == "VIDEO" && (this.uploadedFiles.length > 0)) {
					let message = {
						type: 'error',
						text: 'Bạn đang tạo bài viết Video'
					}
					this.doNotify(message)
					return
				}
				if (!this.pagesActive.length) {
					let message = {
						type: 'error',
						text: 'Vui lòng chọn kênh'
					}
					this.doNotify(message)
					$("#attFileImage").val('')
					return
				}
				this.changeType("PHOTO")
				var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                for (var i = files.length - 1; i >= 0; i--) {
                    this.uploadedFiles.push(files[i])
	            	let reader = new FileReader()
	            	reader.onload = (event) => {
	            		let item = {
	            			data: event.target.result,
	            			uploaded: 0
	            		}
	            		this.prepareImage.push(item)
	            	}
	            	reader.readAsDataURL(files[i])
	            	this.scheduleFile(files[i])
                }
                // Reset the form to avoid copying these files multiple times into this.attachments
               	$("#attFileImage").val('')
			},
			scheduleFile (dataFile) {
				this.pagesActive.forEach((item) => {
					let pageObj = this.getPageFromId(item)
					if (pageObj) {
						this.sendFile(pageObj.fb_page_id, pageObj.page_token, dataFile)
					}
				})
			},
			getPageFromId (id) {
				let result = this.pages.filter((item) => {
					return item.id === id
				})
				return result.length ? result[0] : null
			},
			sendFile (pageId, token, imageData) {
				var fd = new FormData()
			    fd.append("access_token", token)
			    fd.append("source", imageData)
			    fd.append("no_story", true)
			    fd.append("published", false)
			    // Upload image to facebook without story(post to feed)
			    $.ajax({
			        url: "https://graph.facebook.com/" + pageId + "/photos?access_token=" + token,
			        type: "POST",
			        data: fd,
			        processData: false,
			        contentType: false,
			        cache: false,
			        success: (data) => {
			        	if (data.id) {
			        		let itemMedia = '{"media_fbid":"' + data.id + '"}'
				        	if (this.mediaUpWithPost.hasOwnProperty(pageId)) {
				            	this.mediaUpWithPost[pageId].push(itemMedia)
				        	} else {
				        		this.mediaUpWithPost[pageId] = [itemMedia]
				        	}
			        		this.uploadDone()
			        	} else {
			        		let message = {
				        		text: data.error.message,
				        		type: 'error'
				        	}
				            this.doNotify(message)
			        	}
			        },
			        error: (err) => {
			        	let itemMedia = ''
			        	this.mediaUpWithPost[pageId] = [itemMedia]
						this.uploadDone()
			        	let message = {
							text: "Lỗi tải ảnh lên " + this.getNameFromFbIdPage(pageId),
							type: 'error'
						}
						this.doNotify(message)
			        }
			    });
			},
			uploadDone (index) {
				this.isUploadding = true
				let done = true
				let count = this.uploadedFiles.length
				for (var i = this.pagesActive.length - 1; i >= 0; i--) {
					let pageObj = this.getPageFromId(this.pagesActive[i])
					if (!this.mediaUpWithPost.hasOwnProperty(pageObj.fb_page_id) || (this.mediaUpWithPost[pageObj.fb_page_id].length < count)) {
						done = false
						break
					}
				}
				if (done || this.uploadedFiles.length === 0) {
					this.isUploadding = false
					if (this.type == "VIDEO") {
						this.getVideoPreview()
					}
				}
			},
			checkUploadDone () {
				let countPage = this.pagesActive.length
				let countUploaded = this.uploadedPost.length
				if (countPage === countUploaded) {
					this.saveToServe()
				}
			},
			saveToServe () {
				// filter post upload error
				this.uploadedPost.forEach((item, ind) => {
					if (item) {
						this.uploadedPostSuccess.push(item)
					} else {
						this.pageIsUploaded.splice(ind)
					}
				})
				// save post upload success
				if (this.uploadedPostSuccess.length > 0) {
					let timeStamp = this.timeSchedule ? moment(this.timeSchedule, "MM-DD-YYYY HH:mm:ss", "vi", true).unix() : moment().unix()
					let isPublished = this.timeSchedule ? 0 : 1
					let payload = {
						pages: this.pageIsUploaded,
						message: this.message,
						posts: this.uploadedPostSuccess,
						scheduled_publish_time: timeStamp,
						is_published: isPublished,
						type: this.type == 'PHOTO' ? 1 : 2
					}
					post('../../../api/extensions/publish/post', payload)
					.then((res) => {
						if (res.data.success) {
							this.isUploadding = false
							this.reset()
							this.doNotify('Hoàn thành đăng bài viết')
						}
					})
					.catch((err) => {
						let message = {
							text: 'Lỗi lưu bài viết',
							type: 'error'
						}
						this.doNotify(message)
					})
				} else {
					this.isUploadding = false
					this.reset()
				}
			},
			sendPost (pageId, token, message, timeSchedule) {
				let payload = new FormData()
				if (message) {
					payload.append('message', message)
				}
				if (timeSchedule) {
					// Convert to UTC
					let timeStamp = timeSchedule ? moment(timeSchedule, "MM-DD-YYYY HH:mm:ss", "vi", true).unix() : moment().unix()
					payload.append('published', 0)
					payload.append('scheduled_publish_time', timeStamp)
				}
				if (this.mediaUpWithPost.hasOwnProperty(pageId)) {
					payload.append('attached_media', JSON.stringify(this.mediaUpWithPost[pageId]))
				}
				$.ajax({
					url: "https://graph.facebook.com/" + pageId + "/feed?access_token=" + token,
					data: payload,
					dataType: 'json',
					processData: false,
			        contentType: false,
			        cache: false,
					type: 'POST',
					success: (res) => {
						if (res.id) {
							let message = {
								text: "Đã đăng thành công bài viết lên " + this.getNameFromFbIdPage(pageId),
								type: 'success'
							}
							this.pageIsUploaded.push(this.getIdFromFbIdpage(pageId))
							this.uploadedPost.push(res.id)
							this.doNotify(message)
							this.checkUploadDone()
						}
					},
					error: (err) => {
						this.pageIsUploaded.push(this.getIdFromFbIdpage(pageId))
						this.uploadedPost.push(0)
						this.checkUploadDone()
						let message = {
							text: "Lỗi đăng bài viết lên " + this.getNameFromFbIdPage(pageId),
							type: 'error'
						}
						if (err.status == 400) {
							if (err.responseJSON.error.code == 100) {
								message.text = "Thời gian đăng bài phải lớn hơn 30 phút và nhỏ hơn 6 tháng so với hiện tại"
							}
						}
						this.doNotify(message)
					}
				})
			},
			getNameFromFbIdPage (fbId) {
				let result = this.pages.filter((item) => {
					return item.fb_page_id == fbId
				})
				return result.length ? result[0].page_name : null
			},
			getIdFromFbIdpage (fbId) {
				let result = this.pages.filter((item) => {
					return item.fb_page_id == fbId
				})
				return result.length ? result[0].id : null
			},
			reset () {
				this.message = null
				this.isPushlishing = false
				$("#attFileImage").val('')
				$("#attFileVideo").val('')
				this.uploadedFiles = []
				this.mediaUpWithPost = {}
				this.prepareImage = []
				this.uploadedPost = []
				this.timeSchedule = ''
				this.pagesActive = []
				this.pageIsUploaded = []
				this.videoUrl = ''
				this.uploadedPostSuccess = []
			},
			removeImage (index) {
				this.uploadedFiles.splice(index, 1)
				this.prepareImage.splice(index, 1)
				for (let k in this.mediaUpWithPost) {
					this.mediaUpWithPost[k].splice(index, 1)
				}
			},
			publishPost () {
				if (this.pagesActive.length == 0) {
					let message = {
						text: 'Vui lòng chọn Fanpage',
						type: 'error'
					}
					this.doNotify(message);
					return
				}
				this.isUploadding = true
				this.pagesActive.forEach((item) => {
					let pageObj = this.getPageFromId(item)
					setTimeout(() => {
						if (this.type == 'PHOTO') {
							this.sendPost(pageObj.fb_page_id, pageObj.page_token, this.message, this.timeSchedule)
						} else 
						if (this.type == 'VIDEO') {
							this.publishPostWithVideo(pageObj.fb_page_id, pageObj.page_token, this.message, this.timeSchedule)
						}
					}, 300)
				})
			},
			attVideo (e) {
				if (this.type == "PHOTO" && (this.uploadedFiles.length > 0)) {
					let message = {
						type: 'error',
						text: 'Bạn đang tạo bài viết ảnh'
					}
					this.doNotify(message)
					return
				}
				if (!this.pagesActive.length) {
					let message = {
						type: 'error',
						text: 'Vui lòng chọn kênh'
					}
					this.doNotify(message)
					$("#attFileVideo").val('')
					return
				}
				this.changeType("VIDEO")
				var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                for (var i = files.length - 1; i >= 0; i--) {
                    this.uploadedFiles.push(files[i])
                	this.scheduleVideo(files[i])
                }
               	$("#attFileVideo").val('')
			},
			scheduleVideo (videoData) {
				this.pagesActive.forEach((item) => {
					let pageObj = this.getPageFromId(item)
					if (pageObj) {
						this.uploadVideo(pageObj.fb_page_id, pageObj.page_token, videoData)
					}
				})
			},
			uploadVideo (pageId, token, videoData) {
				var fd = new FormData()
			    fd.append("access_token", token)
			    fd.append("source", videoData)
			    fd.append("no_story", true)
			    fd.append("published", false)
			    // Upload image to facebook without story(post to feed)
			    $.ajax({
			        url: "https://graph.facebook.com/" + pageId + "/videos?access_token=" + token,
			        type: "POST",
			        data: fd,
			        processData: false,
			        contentType: false,
			        cache: false,
			        success: (data) => {
			        	if (data.id) {
			        		let itemMedia = data.id
				        	if (this.mediaUpWithPost.hasOwnProperty(pageId)) {
				            	this.mediaUpWithPost[pageId].push(itemMedia)
				        	} else {
				        		this.mediaUpWithPost[pageId] = [itemMedia]
				        	}
			        		this.uploadDone()
			        	}
			        },
			        error: (err) => {
			        	let itemMedia = ''
			        	this.mediaUpWithPost[pageId] = [itemMedia]
						this.uploadDone()
			        	let message = {
							text: "Lỗi tải video lên " + this.getNameFromFbIdPage(pageId),
							type: 'error'
						}
						this.doNotify(message)
			        }
			    });
			},
			publishPostWithVideo (pageId, token, message, timeSchedule) {
				let payload = new FormData()
				if (message) {
					payload.append('description', message)
				}
				if (timeSchedule) {
					// convert to UTC
					let timeStamp = timeSchedule ? moment(timeSchedule, "MM-DD-YYYY HH:mm:ss", "vi", true).unix() : moment().unix()
					payload.append('published', 0)
					payload.append('scheduled_publish_time', timeStamp)
				}
				if (this.mediaUpWithPost.hasOwnProperty(pageId)) {
					payload.append('crossposted_video_id', this.mediaUpWithPost[pageId][0])
				}
				$.ajax({
					url: "https://graph.facebook.com/" + pageId + "/videos?access_token=" + token,
					data: payload,
					dataType: 'json',
					processData: false,
			        contentType: false,
			        cache: false,
					type: 'POST',
					success: (res) => {
						if (res.id) {
							let message = {
								text: "Đã đăng thành công bài viết lên " + this.getNameFromFbIdPage(pageId),
								type: 'success'
							}
							this.doNotify(message)
							this.uploadedPost.push(res.id)
							this.pageIsUploaded.push(this.getIdFromFbIdpage(pageId))
							this.checkUploadDone()
						}
					},
					error: (err) => {
						this.pageIsUploaded.push(this.getIdFromFbIdpage(pageId))
						this.uploadedPost.push(0)
						this.checkUploadDone()
						let message = {
							text: "Lỗi đăng bài viết video lên " + this.getNameFromFbIdPage(pageId),
							type: 'error'
						}
						this.doNotify(message)
					}
				})
			},
			changeType (type) {
				this.type = type
			},
			getVideoPreview () {
				if (this.pagesActive.length > 0) {
					let pageId = this.pagesActive[0]
					let pageObj = this.getPageFromId(pageId)
					let videoId = this.mediaUpWithPost[pageObj.fb_page_id][0].toString()
					let token = pageObj.page_token
					if (!this.videoUrl) {
						$.ajax({
							url: "https://graph.facebook.com/" + videoId + "?fields=source,picture&access_token=" + token,
							type: "get",
							dataType: "json",
							success: (res) => {
								if (res.id) {
									this.videoUrl = res.picture
								}
							}
						})
					}
				}
			},
			removeVideo () {
				$("#attFileVideo").val('')
				this.uploadedFiles = []
				this.mediaUpWithPost = {}
				this.videoUrl = ''
				this.changeType('PHOTO')
			}
		}
	}
</script>
<style type="text/css">
	.pickerTime .el-input__inner {
		background-color: #151516 !important;
		color: #fff !important;
	}
</style>
<style type="text/css" scoped>
	.pickerTime {
		margin-top: 7px;
		float: right;
	}
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
		margin: 0;
		float: left;
	}
	.btnPub:first-child {
		margin-right: 10px;
	}
	.actionBox {
	    margin-top: 35px;
    	float: right;
    	text-align: left;
    	display: inline-block;
    	width: 100%;
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
	.noteBoxTime {
		margin-top: 5px;
		font-size: 13px;
		font-style: italic;
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