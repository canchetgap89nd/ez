<template>
	<div class="row">
		<div class="col-xs-3">
			<div class="form-group">
				<label>Chọn gói tài khoản</label>
				<select class="form-control" v-model="packInd">
					<option v-for="(pack, index) in packages" :value="index">{{ pack.display_name }}</option>
				</select>
			</div>
			<div class="form-group" v-if="packages.length > 0">
				<label>Đơn giá: {{ priceText(this.packages[this.packInd].price) }} VNĐ/tháng</label>
			</div>
			<div class="form-group">
				<label>Chọn thời hạn (tháng)</label>
				<input type="number" v-model="timeExpire" class="form-control" min="3">
			</div>
			<button class="btn btn-success" type="button" @click="upgrade()">Tạo hóa đơn</button>
		</div>
		<div class="col-xs-3">
			<div class="form-group">
				<label>Thành tiên: {{ priceText(total) }}</label>
			</div>
			<div class="form-group">
				<a href="/detail/prices" target="_blank" title="Bảng giá">
					<span class="text-success">Xem bảng giá</span>
				</a>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="form-group" v-show="unpaids.length > 0">
				<label>Bạn đang có các hóa đơn cần thanh toán:</label>
				<ol class="list-bank">
					<li v-for="item in unpaids"><strong>{{ item.pay_code }}:</strong> {{ priceText(item.total_after_discount) }} VNĐ - Thời hạn: {{ item.duration }} tháng</li>
				</ol>
				<p class="text-danger">
					Anh/chị vui lòng chuyển khoản để nâng cấp hoặc gia hạn. 
					Nội dung chuyển khoản "Thanh toán chotsale_[mã hóa đơn]"
				</p>
			</div>
			<div class="form-group">
				<label>Số tài khoản:</label>
				<ol class="list-bank">
					<li>
						Tài khoản Ngân hàng TMCP Ngoại thương Việt Nam - Vietcombank <br>
						Số tài khoản: 0011 0018 13907 <br>
						Tên người hưởng: Công ty Cổ phần Vật giá Việt Nam <br>
						Chi nhánh: Sở giao dịch Hà Nội 
					</li>
					<li>
						Tài khoản Ngân hàng TMCP Kỹ thương Việt Nam - Techcombank <br>
					 	Số tài khoản: 190 2530 334 1017 <br>
					 	Tại ngân hàng: Techcombank <br>
					 	Chi nhánh: Sở giao dịch <br>
					 	Tên người hưởng: Công ty Cổ phần Vật giá Việt Nam
					</li>
					<li>
						Tài khoản Ngân hàng Nông nghiệp và Phát triển nông thôn Việt Nam - Agribank 
				 		Số tài khoản: 1303 201 038 145
					 	Tại ngân hàng: Agribank 
					 	Chi nhánh: Hà Thành, Hà Nội
					 	Tên người hưởng: Công ty Cổ phần Vật giá Việt Nam
					</li>
				</ol>
				<p class="text-danger">
					Sau khi chuyển khoản anh/chị vui lòng gọi đến số hotline (024) 7305 0105 / 01658 299 244 để được kích hoạt dịch vụ
				</p>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	import { get } from '../../../helpers/send'
	import { post } from '../../../helpers/send'
	import {formatPrice} from '../../../helpers/numeralfix'
	export default {
		computed: {
			total () {
				let total = 0
				if (this.packages.length > 0) {
					let price = this.packages[this.packInd].price
					total = price * parseInt(this.timeExpire)
				}
				return total
			}
		},
		data () {
			return {
				packInd: 0,
				timeExpire: 3,
				packages: [],
				unpaids: []
			}
		},
		mounted () {
			this.getPackages()
			this.getUnpaid()
		},
		methods: {
			priceText (price) {
				return formatPrice(price)
			},
			getPackages () {
				get('../../../api/account/upgrade')
					.then((res) => {
						this.packages = res.data.data
					})
					.catch((err) => {
						if (err) {
							$.notify('Lỗi tải danh sách các gói Chốt Sale', 'error')
						}
					})
			},
			upgrade() {
				if (this.timeExpire >= 3) {
					post('../../../api/account/upgrade', {
						package: this.packages[this.packInd].id,
						time_expire: this.timeExpire
					})
						.then((res) => {
							if (res.data.success) {
								$.notify('Bạn vừa tạo hóa đơn thành công', 'success')
								this.unpaids.push(res.data.data)
							}
						})
						.catch((err) => {
							if (err) {
								$.notify(err.response.data.message, 'error')
							}
						})
				} else {
					$.notify('Vui lòng chọn thời hạn 3 tháng trở lên', 'error')
				}
			},
			getUnpaid() {
				get('../../../api/account/upgrade/unpaid')
					.then((res) => {
						this.unpaids = res.data.data
					})
					.catch((err) => {
						$.notify('Lỗi tải trang', 'error')
					})
			}
		}
	}
</script>
<style type="text/css" scoped>
	.list-bank {
		list-style: decimal;
		margin: unset;
		padding: unset;
	}
</style>