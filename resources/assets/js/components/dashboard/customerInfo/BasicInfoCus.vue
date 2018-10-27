<template>
	<div>
		<div class="box-info-cus">
			<div class="inner-binfo">
				<div class="cont-info-or form-horizontal">
					<div class="form-group">
						<label for="name-receive" class="title-info control-label col-lg-2">
							<i class="fa fa-user"></i>
						</label>
						<div class="col-lg-10">
							<input type="text" id="name-receive" placeholder="Nhập tên khách hàng" class="form-control input-sm" v-model="customer.name_cus">
						</div>
					</div>
					<div class="form-group">
						<label class="title-info control-label col-lg-2">
							<i class="fa fa-phone"></i>
						</label>
						<div class="col-lg-10">
							<input type="text" placeholder="Nhập số điện thoại" class="form-control input-sm" v-model="customer.phone_cus">
						</div>
					</div>
					<div class="form-group">
						<label class="title-info control-label col-lg-2">
							<i class="fa fa-map-marker"></i>
						</label>
						<div class="col-lg-10">
							<input type="text" placeholder="Nhập địa chỉ" class="form-control input-sm" v-model="customer.address_cus">
						</div>
					</div>
					<div class="form-group">
						<label class="title-info control-label col-lg-2">
							<i class="fa fa-envelope"></i>
						</label>
						<div class="col-lg-10">
							<input type="text" placeholder="Nhập email" class="form-control input-sm" v-model="customer.email_cus">
						</div>
					</div>
				</div>
				<div class="save-info-cus">
					<a href="#" class="bt-save">
						<div class="lds-css ng-scope" v-if="isSaving"> <div style="width:100%;height:100%" class="lds-flickr"> <div></div> <div></div> <div></div> </div> </div>
						<span v-else>
							<span class="ic-save">
								<i class="fa fa-save"></i>
							</span>
							<span class="txt-save" @click="saveInfoCus()">Lưu thông tin</span>
						</span>
					</a>
				</div>
			</div>
		</div>
		<div class="box-history">
			<div class="inner-his">
				<h3 class="title-box-cus">Lịch sử truy cập</h3>
				<ul class="ls-history">
					<li>
						<a href="javascript:;" class="item-his-cus his-chat">
							<i class="material-icons">textsms</i>
						</a>
						<div class="info-his">
							<span class="txt-info-his">{{ customerOnChat && (customerOnChat.comments_count > 0) ? customerOnChat.comments_count : '' }}</span>
						</div>
					</li>
					<li>
						<a href="javascript:;" class="item-his-cus his-shopping">
							<i class="fa fa-cart-arrow-down"></i>
						</a>
						<div class="info-his">
							<span class="txt-info-his">{{ customerOnChat && (customerOnChat.orders_count > 0) ? customerOnChat.orders_count : '' }}</span>
						</div>
					</li>
					<li>
						<a href="javascript:;" class="item-his-cus his-buy">
							<span class="bg-dollar">
								<i class="fa fa-dollar"></i>
							</span>
						</a>
						<div class="info-his">
							<span class="txt-info-his">{{ totalPay > 0 ? totalPay : '' }}</span>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<div class="box-note">
			<div class="inner-box-note">
				 <h3 class="title-box-cus">Ghi chú</h3>
				 <div class="box-cont-note">
				 	<textarea class="txt-note" ref="inpNote" v-model="note.note_content" @keyup.enter="saveNote()" placeholder="Nhập ghi chú vào đây" :disabled="isSavingNote"></textarea>
				 	<div class="his-note">
				 		<ul class="list-note-cus" v-if="customerOnChat">
				 			<li v-for="(note, index) in customerOnChat.notes">
				 				<div class="time-of-note">
				 					<span class="date-note" v-text="formatTime(note.created_at, 'DD/MM/YYYY')"></span>
				 				</div>
				 				<div class="content-note">
				 					<span class="txt-ct-note" :title="note.note_content" v-text="note.note_content"></span>
				 				</div>
				 				<div class="act-note">
				 					<a href="javascript:;" @click="editNote(note)" class="edit-note" title="Chỉnh sửa">
				 						<i class="fa fa-edit"></i>
				 					</a>
				 					<a href="javascript:;" @click="destroyNote(note.id)" class="del-note" title="Xóa">
				 						<i class="fa fa-close"></i>
				 					</a>
				 				</div>
				 			</li>
				 		</ul>
				 	</div>
				 </div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import { EventBus } from '../../../helpers/bus'
	import { post, get, del } from '../../../helpers/send'
	import {momentLocale} from '../../../helpers/momentfix'
	import {formatPrice} from '../../../helpers/numeralfix'
	import {mapGetters, mapActions} from 'vuex'
	import AWN from "awesome-notifications"
	var notifier = new AWN();

	export default {
		data() {
			return {
				note: {
					id: null,
					note_content: null
				},
				isSaving: false,
				isSavingNote: false,
				customer: {
					name_cus: '',
					email_cus: '',
					phone_cus: '',
					address_cus: ''
				}
			}
		},
		watch: {
			customerOnChat () {
				this.customer.name_cus = this.customerOnChat.name_cus
				this.customer.email_cus = this.customerOnChat.email_cus
				this.customer.phone_cus = this.customerOnChat.phone_cus
				this.customer.address_cus = this.customerOnChat.address_cus
				this.customer.fb_page_id = this.customerOnChat.fb_page_id
				this.customer.fb_id_cus = this.customerOnChat.fb_id_cus
			}
		},
		computed: {
			...mapGetters(
				'conversation', ['customerOnChat']
			),
			totalPay() {
				let total = 0;
				let payments = this.customer.payments;
				if (payments) {
					payments.forEach((val) => {
						if (val.refund) {
							total -= val.amount_pay;
						} else {
							total += val.amount_pay;
						}
					})
				}
				return formatPrice(total);
			}
		},
		methods: {
			...mapActions(
				'conversation', ['setCustomerOnChat']
			),
			formatTime(time, formater) {
				return momentLocale(time).format(formater);
			},
			formatPrice(price) {
				return formatPrice(price);
			},
			saveInfoCus() {
				this.isSaving = true;
				post('/api/customers/quick/add', this.customer)
				.then((res) => {
					if (res.data.created) {
						$.notify('Tạo khách hàng mới thành công', 'success');
					} else {
						$.notify('Cập nhật khách hàng thành công', 'success');
					}
					this.updateInfoCustomer(res.data.data.id)
					EventBus.$emit("getListConversations");
					this.isSaving = false;
				})
				.catch((err) => {
					let errors = err.response.data;
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
					}
					this.isSaving = false;
				})
			},
			saveNote() {
				this.isSavingNote = true;
				let customerId = this.customerOnChat ? this.customerOnChat.id : null;
				post('/api/customers/notes/quick/add', {
					customer_id: customerId,
					note_content: this.note.note_content,
					note_id: this.note.id,
					fb_id_cus: this.customerOnChat.fb_id_cus,
					name_cus: this.customerOnChat.name_cus,
					fb_page_id: this.customerOnChat.fb_page_id
				})
				.then((res) => {
					if (res.data.saved) {
						this.updateInfoCustomer(res.data.customer.id);
						this.resetNote();
						$.notify('Đã lưu ghi chú', 'success');
					} else if (res.data.updated) {
						let note = res.data.note;
						this.updateNote(note);
						$.notify('Đã cập nhật ghi chú', 'success')
						this.resetNote();
					} else {
						$.notify('Không lưu được ghi chú', 'warn')
					}
					EventBus.$emit("getListConversations");
					this.isSavingNote = false;
					this.$nextTick(() => {
						if (this.$refs.inpNote) {
	              			this.$refs.inpNote.focus()
						}
		            })
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
					}
					this.isSavingNote = false;
				})
			},
			editNote(note) {
				this.note.id = note.id;
				this.note.note_content = note.note_content;
			},
			updateNote(note) {
				this.customerOnChat.notes.forEach((val, idx) => {
					if (val.id == note.id) {
						this.customerOnChat.notes[idx] = note;
					}
				})
			},
			destroyNote(id) {
				notifier.confirm('Bạn có muốn xóa ghi chú', () => {
					this.yesDeleteNote(id);
				});
			},
			yesDeleteNote(id) {
				del('/api/customers/notes/destroy/'+this.customerOnChat.id+'/'+id)
				.then((res) => {
					if (res.data.success) {
						this.customerOnChat.notes.forEach((val, idx) => {
							if (val.id == id) {
								this.customerOnChat.notes.splice(idx, 1);
							}
						})
						this.noteDelete = null
						$.notify('Đã xóa ghi chú', 'success');
					}
				})
				.catch((err) => {
					$.notify('Lỗi xóa ghi chú', 'error');
				})
			},
			resetNote() {
				this.note.note_content = '';
				this.note.id = null;
			},
			updateInfoCustomer (customerId) {
                get('api/customers/infomation/'+customerId)
                    .then((res) => {
                        this.setCustomerOnChat(res.data)
                    })
            }
		}
	}
</script>