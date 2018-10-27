<template>
	<div :class="'ss-main ss-' + mainId ">
        <div @click="openSearch()" class="ss-single-selected" :title="this.adActive">
        	<span class="placeholder">
        		<span>{{ this.adActive }}</span>
        	</span>
        	<span :class="'ss-arrow ss-arrow-'+ mainId">
        		<span class="arrow-down"></span>
        	</span>
        </div>
        <div :class="'ss-content ss-content-' + mainId ">
            <div class="ss-search">
                <input type="search" placeholder="Tìm kiếm" v-model.lazy="keyword" v-debounce="500" tabindex="0">
            </div>
            <div class="ss-list">
                <div class="ss-option text-center" v-show="searching">
                	<i class="fa fa-spinner fa-pulse fa-fw"></i>
					<span class="sr-only">Loading...</span>
                </div>
                <!-- <div class="ss-option ss-disabled" data-id="14758305">Hà Nội</div> -->
                <div v-for="ad in address" class="ss-option" @click="pickAddress(ad)">
                	{{ad.name_ward}}, {{ ad.name_district }}, {{ ad.name }}
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
	
	import { EventBus } from '../helpers/bus'
	import { get } from '../helpers/send'
	import {momentLocale} from '../helpers/momentfix'
	import debounce from '../helpers/directive'

	export default {
		data() {
			return {
				address: [],
				keyword: '',
				searching: false,
				adActive: 'Chọn địa chỉ'
			}
		},
		directives: {debounce},
		created() {
			this.mainId = Math.floor((Math.random() * 1000000) + 1);
		},
		mounted() {
			let thisIns = this;
			$(document).click(function(e) {
			    if(($(e.target).attr('class') != 'ss-main ss-'+thisIns.mainId) && !$(".ss-main.ss-"+thisIns.mainId).find(e.target).length) {
		        	$(".ss-arrow-" + thisIns.mainId + " span").removeClass('arrow-up');
					$(".ss-arrow-" + thisIns.mainId + " span").addClass('arrow-down');
			        $(".ss-content-"+thisIns.mainId).removeClass('ss-open');
			    } 
			});
		},
		watch: {
			keyword() {
				this.searchAddress();
			}
		},
		methods: {
			openSearch() {
				let content = $(".ss-content.ss-content-"+this.mainId);
				let arrow = $(".ss-arrow-" + this.mainId + " span");
				if (content.attr('class') == 'ss-content ss-content-'+this.mainId) {
					content.addClass('ss-open');
					arrow.removeClass('arrow-down');
					arrow.addClass('arrow-up');
				} else {
					content.removeClass('ss-open');
					arrow.removeClass('arrow-up');
					arrow.addClass('arrow-down');
				}
			},
			closeSearch() {
				let content = $(".ss-content.ss-content-"+this.mainId);
				let arrow = $(".ss-arrow-" + this.mainId + " span");
				if (content.attr('class') == 'ss-content ss-content-'+this.mainId+ ' ss-open') {
					content.removeClass('ss-open');
					arrow.removeClass('arrow-up');
					arrow.addClass('arrow-down');
				}
			},
			searchAddress: function() {
				this.searching = true;
				get('/api/address/all?keyword='+this.keyword)
				.then((res) => {
					this.searching = false;
					if (res.data) {
						this.address = res.data;
					}
				})
				.catch((err) => {
					$.notify('Lỗi tải địa chỉ', 'error');
					this.searching = false;
				})
			},
			pickAddress(ad) {
				this.adActive = ad.name_ward + ', ' + ad.name_district + ', ' + ad.name;
				EventBus.$emit('address-pick', ad);
				this.closeSearch();
			}
		}
	}
</script>