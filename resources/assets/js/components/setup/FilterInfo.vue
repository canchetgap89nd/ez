<template>
	<div v-show="role.name == 'ADMINSTRATOR'">
		<div class="inner-cont" v-if="isLoading">
			<circle-load></circle-load>
		</div>
		<div class="inner-cont" v-else>
			<div class="title">
				<span>Lọc thông tin</span>
			</div>

			<div class="clear-fix"></div>

            <div class="body-bl-st">
                <table class="table table-head-black">
                    <thead>
                        <tr>
                            <th class="text-center">Tên File</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-left">Email khách hàng tiềm năng</td>
                            <td class="text-center">{{ count_email }}</td>
                            <td class="text-center">
                                <a class="ac-item-tb" href="/filter-infomation/export/email/xlsx" target="_blank" title="Tải xuống">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left">Số điện thoại khách hàng tiềm năng</td>
                            <td class="text-center">{{ count_phone }}</td>
                            <td class="text-center">
                                <a class="ac-item-tb" title="Tải xuống" href="/filter-infomation/export/phone/xlsx" target="_blank">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

		</div>
	</div>
</template>
<script type="text/javascript">
	
	import SlideBar from './SlideBar.vue'
	import { get } from '../../helpers/send'
	import { post } from '../../helpers/send'
	import { del } from '../../helpers/send'
	import Circle from '../loading/Circle.vue'
	import {functionHelper} from '../../helpers/myfunction'
	import {momentLocale} from "../../helpers/momentfix"

	export default {
		components: {
			'circle-load': Circle,
			'slide-bar':SlideBar
		},
		computed: {
			role() {
				return this.$store.state.role;
			},
			user() {
				return this.$store.state.user;
			}
		},
		data() {
			return {
                isLoading: false,
				isSaving: false,
                filter_email: false,
				filter_phone: false,
                count_email: 0,
                count_phone: 0
			}
		},
		created() {
			this.getInfoMarketing();
		},
		methods: {
			getInfoMarketing() {
				this.isLoading = true;
				get('../../api/setting/filter-infomation/infomation')
				.then((res) => {
                    this.count_email = res.data.count_email;
                    this.count_phone = res.data.count_phone;
                    this.filter_email = res.data.setting.filter_email;
					this.filter_phone = res.data.setting.filter_phone;
                    this.isLoading = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải trang lọc thông tin', 'error');
					this.isLoading = false;
				})
			}
		}
	}
</script>