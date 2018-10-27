<template>
	<div>
		<div style="display: inline-block; width: 100%; margin-bottom: 15px">
			<div class="select-page-cont item-control-tool no-margin-top">
				<select class="form-control form-control-cus picker-cus select-cus" v-model="pageId">
					<option value="">Chọn Fanpage</option>
					<option v-for="page in pages" :value="page.id">{{ page.page_name }}</option>
				</select>
				<i class="fa fa-caret-down icon-dropdown-sel"></i>
			</div>
		</div>
		<div class="text-center" v-if="isLoading">
			<circle-load></circle-load>
		</div>
		<div v-else class="panel panel-black panel-grid">
			<div class="panel-heading">
				<span class="title-top">
					Top 10 bài viết có tương tác cao nhất
				</span>
			</div>
			<div class="panel-body">
				<div class="col-lg-6 left-cl">
					<div class="entity-post-top" v-for="(post, index) in posts" v-show="index < 5">
						<div class="sort-number">
							<span :class="{'txt-number-sort top1-st': index == 0, 'txt-number-sort top2-st': index == 1, 'txt-number-sort top3-st': index == 2, 'txt-number-sort': index > 2}">{{ index + 1 }}</span>
						</div>
						<div class="detail-post-top"> 
							<div class="title-postTop-box">
								<span class="title-post-top">
									Đăng bởi {{ post.fb_page_name }}
								</span>
								<span class="time-post-top">
									<span class="time">Vào lúc {{ formatTime(post.created_time, 'DD/MM/YYYY') }} </span>
								</span>
							</div>
							<div class="des-post-top">
								{{post.message}}
							</div>
							<div class="more-infoPost-top">
								<span class="interactive-post-top">
									<!-- <span class="type-total">
										<span class="total-interactive">215</span>
										<i class="fa fa-thumbs-o-up"></i>
									</span> -->
									<span class="type-total">
										<span class="total-interactive">{{ post.comments_count }}</span>
										<i class="fa fa-commenting-o"></i>
									</span>
								</span>
								<span class="watch-more">
									<a :href="'https://www.facebook.com/'+post.fb_post_id" target="_blank" class="link-watch-more">Xem thêm</a>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 right-cl">
					<div class="entity-post-top" v-for="(post, index) in posts" v-show="index > 4">
						<div class="sort-number">
							<span class="txt-number-sort">{{ index + 1 }}</span>
						</div>
						<div class="detail-post-top"> 
							<div class="title-postTop-box">
								<span class="title-post-top">
									Đăng bởi {{post.fb_page_name}}
								</span>
								<span class="time-post-top">
									<span class="time">Vào lúc {{formatTime(post.created_time, "DD/MM/YYYY")}} </span>
								</span>
							</div>
							<div class="des-post-top">
								{{post.message}}
							</div>
							<div class="more-infoPost-top">
								<span class="interactive-post-top">
									<span class="type-total">
										<span class="total-interactive">{{ post.comments_count }}</span>
										<i class="fa fa-commenting-o"></i>
									</span>
								</span>
								<span class="watch-more">
									<a :href="'https://www.facebook.com/'+post.fb_post_id" target="_blank" class="link-watch-more">Xem thêm</a>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">

	import { get } from '../../helpers/send'
	import {momentLocale} from '../../helpers/momentfix'
	import Circle from '../loading/Circle.vue'
	import AWN from "awesome-notifications"
	var notifier = new AWN();
	
	export default {
		components: {
			'circle-load': Circle
		},
		data() {
			return {
				posts: [],
				pageId: '',
				isLoading: false
			}
		},
		computed: {
			pages() {
				return this.$store.state.pages;
			}
		},
		mounted() {
			this.getTop10Post();
		},
		watch: {
			pageId() {
				this.getTop10Post();
			}
		},
		methods: {
			formatTime(time, formater) {
				return momentLocale(time * 1000).format(formater);
			},
			getTop10Post() {
				this.isLoading = true;
    			get('../../api/statistic/top10Post?pageId='+this.pageId)
				.then((res) => {
					this.posts = res.data
	    			this.isLoading = false;
				})
				
			}
		}
	}
</script>