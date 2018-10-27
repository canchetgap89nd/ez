<template>
	<div>
		<circle-loading v-if="processing"></circle-loading>
		<div v-else class="modal-content modal-add-order">
        	<div class="modal-header">
        		<div class="image-head-modal">
	        		<div class="avatar-modal">
	        			<img :src="'https://graph.facebook.com/'+customer.fb_id_cus+'/picture?height=80&width=80'" v-if="customer.fb_id_cus">
	        			<img src="images/man.png" v-else>
	        		</div>
        		</div>
        		<div class="name-cus-modal">
        			<h3>{{ customer.name_cus }}</h3>
        		</div>
        		<div class="interactive-cus-modal">
        			<div class="interactive-item">
        				<div class="interactive-entity interactive-num">
        					{{ customer.count_conversations }}
        				</div>
        				<div class="interactive-entity interactive-icon" title="Số cuộc hội thoại">  
        					<i class="fa fa-comment"></i>
        				</div>
        			</div>
        			<div class="interactive-item">
        				<div class="interactive-entity interactive-num">
        					{{ customer.orders.length }}
        				</div>
        				<div class="interactive-entity interactive-icon" title="Số đơn hàng">  
        					<i class="fa fa-shopping-cart"></i>
        				</div>
        			</div>
        		</div>
        		<div class="label-cus-modal">
        			<div class="label-cus-item" v-for="(group, index) in customer.groups">
        				<div class="label-cus-dismiss" :style="'background: '+ group.group_color">
        					<span class="txt-label-cusDis">
        						{{ group.group_name }}
        					</span>
        					<span class="dismiss-label" @click="removeGroup(index)" title="xóa">
        						<i class="fa fa-close"></i>
        					</span>
        				</div>
        			</div>
        			<div class="label-cus-add dropdown">
        				<span class="btn-add-labelModal" data-toggle="dropdown">
        					<i class="fa fa-tag"></i>
        				</span>
					    <ul class="dropdown-menu">
					        <li v-for="(group, index) in groups"><a href="#" @click="pickGroup(group)">{{ group.group_name }}</a></li>
					    </ul>
        			</div>
        		</div>
			</div>
	        <div class="modal-body modal-body-cus">
	        	<div class="row">
	        		<div class="col-md-6">
	        			<div class="title-order-cl">
		        			<span class="txt-title-orderCl">Thông tin</span>
		        			<span class="edit-info-addOrder" @click="editInfo = !editInfo" title="Chỉnh sửa">
		        				<i v-if="!editInfo" class="fa fa-edit"></i>
		        				<i v-else class="fa fa-close"></i>
		        			</span>
		        		</div>
		        		<div class="info-order-add" v-if="!editInfo">
		        			<div class="info-row-add">
		        				<div class="title-row-order">
		        					<span class="icon-row-order">
		        						<i class="fa fa-user"></i>
		        					</span>
		        				</div>
		        				<div class="detail-row-order">
		        					<span class="txt-detail">
		        						{{ customer.name_cus }}
		        					</span>
		        				</div>
		        			</div>
		        			<div class="info-row-add">
		        				<div class="title-row-order">
		        					<span class="icon-row-order icon-phone-order">
		        						<i class="fa fa-mobile-phone"></i>
		        					</span>
		        				</div>
		        				<div class="detail-row-order">
		        					<span class="txt-detail">
		        						{{ customer.phone_cus }}
		        					</span>
		        				</div>
		        			</div>
		        			<div class="info-row-add">
		        				<div class="title-row-order">
		        					<span class="icon-row-order">
		        						<i class="fa fa-envelope"></i>
		        					</span>
		        				</div>
		        				<div class="detail-row-order">
		        					<span class="txt-detail">
		        						{{ customer.email_cus }}
		        					</span>
		        				</div>
		        			</div>
		        			<div class="info-row-add">
		        				<div class="title-row-order">
		        					<span class="icon-row-order icon-place-order">
		        						<i class="fa fa-map-marker"></i>
		        					</span>
		        				</div>
		        				<div class="detail-row-order">
		        					<span class="txt-detail">
		        						{{ customer.address_cus }}
		        					</span>
		        				</div>
		        			</div>
		        		</div>
		        		<div v-else>
		        			<div class="input-group form-group">
							    <span class="input-group-addon"><i class="fa fa-user"></i></span>
							    <input id="text" type="text" class="form-control" placeholder="Nhập tên khách hàng" v-model="customer.name_cus">
						  	</div>
						  	<div class="input-group form-group">
						  		<span class="input-group-addon"><i class="fa fa-mobile-phone"></i></span>
						  		<input type="text" class="form-control" placeholder="Nhập số điện thoại" v-model="customer.phone_cus">
						  	</div>
						  	<div class="input-group form-group">
						  		<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
								<input type="email" class="form-control" placeholder="Nhập email" v-model="customer.email_cus">
						  	</div>
						  	<div class="input-group form-group">
						  		<span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
								<input type="text" class="form-control" placeholder="Nhập địa chỉ" v-model="customer.address_cus">
						  	</div>
		        		</div>
	        		</div>
	        		<div class="col-md-6">
	        			<div class="title-order-cl">
		        			<span class="txt-title-orderCl">Ghi chú</span>
		        		</div>
		        		<div class="note-order-add">
		        			<div class="item-noteOrder-add" v-for="(note, index) in customer.notes">
		        				<div class="row">
		        					<div class="col-md-4">
		        						{{ note.note_content }}
		        					</div>
			        				<div class="col-md-4 txt-time-noteAdd">{{ formatTime(note.created_at, 'HH:mm DD-MM-YYYY') }}</div>
			        				<div class="col-md-3">
			        					<span class="txt-extra" v-show="note.id" @click="editNote(note.id, note.note_content)" title="Chỉnh sửa">
				        					<i class="fa fa-edit"></i>
				        				</span>
				        				<span class="txt-extra" @click="removeNote(index)" title="Xóa">
				        					<i class="fa fa-trash"></i>
				        				</span>
			        				</div>
		        				</div>
		        			</div>
		        		</div>
		        		<div class="note-order-add" v-show="showAddNote">
		        			<textarea class="form-control" placeholder="Nhập ghi chú..." v-model="detailNote.note_content" @keyup.enter="createNote()"></textarea>
		        		</div>
		        		<div class="noteOrder-add-more">
	        				<span class="btn-addDetail-order" title="Thêm ghi chú" @click="showAddNote = !showAddNote" v-show="!showAddNote">
	        					<i class="fa fa-plus-circle"></i>
	        				</span>
	        			</div>
	        		</div>
	        	</div>
	        	<div class="row" v-if="customer.orders.length">
	        		<div class="col-md-12">
	        			<table class="table table-order-cus">
	        				<thead>
	        					<tr>
	        						<th class="text-center">Mã đơn</th>
	        						<th class="text-center">Ngày tạo</th>
	        						<th class="text-center">Thanh toán</th>
	        						<th class="text-center">Trạng thái</th>
	        					</tr>
	        				</thead>
	        				<tbody>
	        					<tr v-for="(order, index) in customer.orders">
	        						<td class="text-center">{{ order.order_code }}</td>
	        						<td class="text-center">{{ formatTime(order.created_at, 'DD/MM/YYYY') }}</td>
	        						<td class="text-center">{{ formatPrice(order.total_pay) }}</td>
	        						<td class="text-center">
	        							<span v-if="order.status_order == 'NEW'">Đơn mới</span>
	        							<span v-if="order.status_order == 'CONFIRM'">Đã xác nhận</span>
	        							<span v-if="order.status_order == 'SENDING'">Đang giao</span>
	        							<span v-if="order.status_order == 'SENT'">Đã giao</span>
	        							<span v-if="order.status_order == 'REFUNDING'">Hoàn đơn</span>
	        							<span v-if="order.status_order == 'REFUNDED'">Đã hoàn đơn</span>
	        							<span v-if="order.status_order == 'CANCEL'">Hủy</span>
	        						</td>
	        					</tr>
	        				</tbody>
	        			</table>
	        		</div>
	        	</div>
	        </div>
	        <div class="modal-footer modal-footer-cus">
	        	<!-- <button type="button" class="btn btn-addNew-order pull-left">Thêm đơn hàng</button> -->
	        	<button type="button" class="btn btn-update-order pull-left" @click="updateCustomer()" :disabled="isUpdating">Cập nhật</button>
				<button type="button" class="btn dismiss-table-orderCus pull-right" @click="clearDetailCustomer()">Hủy</button>
	        </div>
      	</div>
	</div>
</template>
<script type="text/javascript">

	import {momentLocale} from '../../helpers/momentfix'
	import {formatPrice} from '../../helpers/numeralfix'
	import {functionHelper} from '../../helpers/myfunction'
	import {get} from '../../helpers/send'
	import {post} from '../../helpers/send'
	import { EventBus } from '../../helpers/bus'
	import Circle from '../loading/Circle.vue'

	export default {
		components: {
			'circle-loading': Circle
		},
		data() {
			return {
				customer: {
					id: null,
					name_cus: null,
					email_cus: null,
					phone_cus: null,
					address_cus: null,
					fb_id_cus: null,
					count_conversations: 0,
					count_orders: 0,
					groups: [],
					notes: [],
					orders: [],
					groups_del: [],
					notes_del: []
				},
				editInfo: false,
				showAddNote: false,
				processing: false,
				isUpdating: false,
				detailNote: {
					id: null,
					note_content: null
				}
			}
		},
		computed: {
			groups() {
				return this.$store.state.groups;
			}
		},
		mounted() {
			EventBus.$on('detail-customer', (id) => {
				this.detailCustomer(id);
			})
		},
		methods: {
			detailCustomer(id) {
				this.processing = true;
				$("#detail-customer-modal").modal('show');
				get('api/customers/detail/'+id)
				.then((res) => {
					let customer = res.data.customer; 
					if (customer) {
						this.customer.id = customer.id;
						this.customer.name_cus = customer.name_cus;
						this.customer.phone_cus = customer.phone_cus;
						this.customer.email_cus = customer.email_cus;
						this.customer.address_cus = customer.address_cus;
						this.customer.fb_id_cus = customer.fb_id_cus;
						this.customer.groups = customer.groups;
						this.customer.notes = customer.notes;
						this.customer.orders = customer.orders;
						this.customer.count_conversations = customer.count_conversations;
					}
					this.processing = false;
				})
				.catch((err) => {
					if (err.response.status === 302) {
						$.notify(err.response.message, 'error');
					}
					this.processing = false;
				})
			},
			formatTime(time, formater) {
				return momentLocale(time).format(formater);
			},
			formatPrice(price) {
				return formatPrice(price);
			},
			clearDetailCustomer() {
				$('#detail-customer-modal').modal('hide');
				this.customer.groups = [];
				this.customer.notes = [];
				this.customer.name_cus = null;
				this.customer.phone_cus = null;
				this.customer.email_cus = null;
				this.customer.address_cus = null;
				this.customer.fb_id_cus = null;
				this.updating = false;
				this.editInfo = false;
				this.showAddNote = false;
			},
			removeNote(index) {
				if (this.customer.notes[index].id) {
					let id = this.customer.notes[index].id;
					this.customer.notes_del.push(id);
				}
				this.customer.notes.splice(index, 1);
			},
			removeGroup(index) {
				let group = this.customer.groups[index];
				this.groups.push(group);
				this.customer.groups_del.push(group.id);
				this.customer.groups.splice(index, 1);
			},
			createNote() {
				if (this.detailNote.id != null) {
					for (var i = 0; i < this.customer.notes.length; i++) {
						if (this.customer.notes[i].id == this.detailNote.id) {
							this.customer.notes[i].note_content = this.detailNote.note_content;
							break;
						}
					}
				} else {
					let note = {
						note_content: this.detailNote.note_content,
						created_at: momentLocale().format('YYYY-MM-DD HH:mm')
					}
					this.customer.notes.unshift(note);
				}
				this.resetDetailNote();
			},
			resetDetailNote() {
				this.detailNote.note_content = null;
				this.detailNote.id = null;
			},
			pickGroup(group) {
				let has = this.customer.groups.filter((val) => {
					return val.id == group.id;
				});
				if (!has.length) {this.customer.groups.push(group)};
			},
			editNote(id, content) {
				this.detailNote.note_content = content;
				this.detailNote.id = id;
				if (!this.showAddNote) {this.showAddNote = true};
			},
			updateCustomer() {
				this.isUpdating = true;
				post('api/customers/update/'+this.customer.id, this.customer)
				.then((res) => {
					if (res.data.updated) {
						$.notify('Cập nhật khách hàng thành công', 'success');
						this.clearDetailCustomer();
					}
					this.isUpdating = false;
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
					}
					this.isUpdating = false;
				})
			}
		},
		beforeDestroy () {
			EventBus.$off('detail-customer')
		}
	}
</script>