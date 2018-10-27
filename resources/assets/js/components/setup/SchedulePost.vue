<template>
	<div class="publishPost" v-loading="loading">
		<div class="wrapBox">
			<div class="panel-body no-padding-top">
				<table class="table table-condensed table-head-black">
					<thead>
						<tr>
							<th class="cs-ellipsis-clm">Tiêu đề</th>
							<th class="cs-ellipsis-clm">Kênh đăng</th>
							<th class="ellipsis-column">Thời gian đăng</th>
							<th class="text-center">Tác vụ</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(article, index) in articles.data">
							<td class="cs-ellipsis-clm" style="width: 400px;" :title="article.message">
								{{ article.message }}
							</td>
							<td>
								<ol>
									<li v-for="page in article.pages">
										{{ page.page_name }}
									</li>
								</ol>
							</td>
							<td class="cs-ellipsis-clm">
								{{ formatTime(article.scheduled_publish_time * 1000) }}
							</td>
							<td class="text-center">
								<div>
									<a :href="'https://www.facebook.com/' + article.pages[0].pivot.article_id" target="_blank">
										<span class="ac-row-tb edit-row-table" title="Xem trên Facebook">
											<i class="fa fa-eye"></i>
										</span>
									</a>
									<span class="ac-row-tb edit-row-table" title="Chỉnh sửa" @click="editPost(index)">
										<i class="fa fa-edit"></i>
									</span>
									<span class="ac-row-tb edit-row-table" title="Công khai ngay" @click="confirmPublish(index)">
										<i class="fa fa-globe"></i>
									</span>
									<span class="ac-row-tb del-row-table" title="Xóa" @click="confirmDestroy(index)">
										<i class="fa fa-close"></i>
									</span>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="under-table">
				<div class="count-total" v-if="articles.total > 0">
					Tổng {{ articles.total }} bài viết
				</div>
				<paginate
				  	:page-count="articles.last_page"
				  	:click-handler="turnPage"
			  		:prev-text="'Prev'"
			  		:next-text="'Next'"
			  		:container-class="'paginate-cont'"
			  		:page-class="'page-item'"
			  		:prev-link-class="'per-page previous'"
			  		:next-link-class="'per-page next'"
			  		:prev-class="'per-page previous'"
			  		:next-class="'per-page next'" v-if="articles.last_page > 1">

			  		<span slot="prevContent">
			  			<i class="fa fa-arrow-left"></i>
			  		</span>
			  		<span slot="nextContent">
			  			<i class="fa fa-arrow-right"></i>
			  		</span>
				</paginate>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import {get, del, post} from '../../helpers/send'
	import {momentLocale} from '../../helpers/momentfix'
	import { EventBus } from '../../helpers/bus'
	import { mapActions} from 'vuex'
	import Paginate from 'vuejs-paginate'
	import AWN from "awesome-notifications"
	var notifier = new AWN()

	export default {
		name: 'publish-posted',
		components: {
			'paginate': Paginate
		},
		data () {
			return {
				loading: false,
				articles: []
			}
		},
		mounted () {
			this.getListSchedulePost()
		},
		methods: {
			...mapActions(['doNotify']),
			getListSchedulePost () {
				this.loading = true
				get('../../../api/extensions/publish/list-schedule-post')
				.then((res) => {
					this.loading = false
					this.articles = res.data.data
				})
			},
			turnPage(num) {
				this.loading = true
				let url = this.articles.path + '?page=' + num
				get(url)
				.then((res) => {
					this.articles = res.data.data
					this.loading = false
				})
				.catch((err) => {
					$.notify('Lỗi tải trang!', 'error')
					this.loading = false
				})
			},
			formatTime (time) {
				return momentLocale(time).format('DD-MM-YYYY HH:mm')
			},
			editPost (index) {
				let articleId = this.articles.data[index].id
				let newTab = {
					name: 'editPost',
					id: articleId
				}
				EventBus.$emit('addTabEdit', newTab)
				this.$router.push({name: 'editPost', params: { id: articleId }})
			},
			confirmDestroy (index) {
				notifier.confirm('Xóa bài viết', () => {
    			this.deletePost(index);
    		})
			},
			deletePost (index) {
				this.loading = true
				let articleId = this.articles.data[index].id
				del('../../../api/extensions/publish/destroy/' + articleId)
				.then((res) => {
					if (res.data.success) {
						this.doNotify('Xóa thành công bài viết')
						this.articles.data.splice(index, 1)
						let errors = res.data.errors
						if (errors) {
							for (let k in errors) {
								let message = {
									text: errors[k],
									type: 'error'
								}
								this.doNotify(message)
							}
						}
					} else {
						let message = {
							text: res.data.message,
							type: 'error'
						}
						this.doNotify(message)
					}
					this.loading = false
				})
				.catch((err) => {
					let message = {
						text: 'Lỗi xóa bài viết',
						type: 'error'
					}
					this.doNotify(message)
					this.loading = false
				})
			},
			confirmPublish (index) {
				notifier.confirm('Bạn có chắc muốn công khai bài viết này trên các Fanpage đã chọn', () => {
	    			this.publishNow(index);
	    		})
			},
			publishNow (index) {
				this.loading = true
				let is_published = this.articles.data[index].is_published
				let articleId = this.articles.data[index].id
				post('../../../api/extensions/publish/publish-now/' + articleId, {is_published: !is_published})
				.then((res) => {
					if (res.data.success) {
						this.doNotify('Công khai thành công bài viết')
						this.articles.data.splice(index, 1)
						let errors = res.data.errors
						if (errors) {
							for (let k in errors) {
								let message = {
									text: errors[k],
									type: 'error'
								}
								this.doNotify(message)
							}
						}
					} else {
						let message = {
							text: 'Lỗi công khai bài viết',
							type: 'error'
						}
						this.doNotify(message)
					}
					this.loading = false
				})
			}
		}
	}
</script>
<style type="text/css" scoped>
	.wrapBox .panel-body {
		padding: 0;
	}
</style>