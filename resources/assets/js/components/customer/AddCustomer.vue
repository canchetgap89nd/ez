<template>
	<div>
		<circle-loading v-if="processing"></circle-loading>
		<div v-else class="modal-content modal-add-order">
        	<div class="modal-header">
        		<div class="image-head-modal">
	        		<div class="avatar-modal">
	        			<img src="images/man.png">
	        		</div>
        		</div>
        		<div class="name-cus-modal">
        			<h3>{{ customers.name_cus }}</h3>
        		</div>
        		<div class="interactive-cus-modal">
        			<div class="interactive-item">
        				<div class="interactive-entity interactive-num">
        					0
        				</div>
        				<div class="interactive-entity interactive-icon">  
        					<i class="fa fa-comment"></i>
        				</div>
        			</div>
        			<div class="interactive-item">
        				<div class="interactive-entity interactive-num">
        					0
        				</div>
        				<div class="interactive-entity interactive-icon">  
        					<i class="fa fa-shopping-cart"></i>
        				</div>
        			</div>
        		</div>
        		<div class="label-cus-modal">
        			<div class="label-cus-item" v-for="(group, index) in customers.groups">
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
					        <li v-for="(group, index) in groups"><a href="#" @click="pickGroup(index)">{{ group.group_name }}</a></li>
					    </ul>
        			</div>
        		</div>
			</div>
	        <div class="modal-body modal-body-cus">
	        	<div class="row">
	        		<div class="col-md-6">
	        			<div class="title-order-cl">
		        			<span class="txt-title-orderCl">Thông tin</span>
		        		</div>
	        			<div class="input-group form-group">
						    <span class="input-group-addon"><i class="fa fa-user"></i></span>
						    <input id="text" type="text" class="form-control" placeholder="Nhập tên khách hàng" v-model="customers.name_cus">
					  	</div>
					  	<div class="input-group form-group">
					  		<span class="input-group-addon"><i class="fa fa-mobile-phone"></i></span>
					  		<input type="text" class="form-control" @keyup="searchCustomer()" v-model="customers.phone_cus" placeholder="Nhập số điện thoại">
					  		<span v-if="isSearching" class="icon-searching">
								<i class="fa fa-spinner fa-pulse fa-fw"></i>
								<span class="sr-only">Loading...</span>
							</span>
					  		<ul class="dropdown-menu menu-cus-suggest" v-show="suggestCustomers.length > 0">
					  			<li>
					  				<a href="javascript:;" style="background-color: #ddd;">
					  					Khách hàng đã có
					  					<span @click="removeCus()" style="margin-left: 20px">
					  						<i class="fa fa-close"></i>
					  					</span>
					  				</a>
					  			</li>
					  			<li v-for="cus in suggestCustomers">
					  				<a href="javascript:;">
					  					<span class="inFcus nCus">{{ cus.name_cus }}</span>
					  					<span class="inFcus" v-show="cus.reports.length">{{ cus.reports.length }} lần báo cáo</span>
					  				</a>
					  			</li>
					  		</ul>
					  	</div>
					  	<div class="input-group form-group">
					  		<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
							<input type="email" class="form-control" placeholder="Nhập email" v-model="customers.email_cus">
					  	</div>
					  	<div class="input-group form-group">
					  		<span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
							<input type="text" class="form-control" placeholder="Nhập địa chỉ" v-model="customers.address_cus">
					  	</div>
	        		</div>
	        		<div class="col-md-6">
	        			<div class="title-order-cl">
		        			<span class="txt-title-orderCl">Ghi chú</span>
		        		</div>
		        		<div class="note-order-add">
		        			<div class="item-noteOrder-add" v-for="(note, index) in customers.notes">
		        				<span class="txt-detail-noteAdd">{{ note.note_content }}</span>
		        				<span class="txt-time-noteAdd">{{ note.created_at }}</span>
		        				<span class="txt-extra" @click="removeNote(index)" title="xóa">
		        					<i class="fa fa-trash"></i>
		        				</span>
		        			</div>
		        		</div>
		        		<div class="note-order-add" v-show="showAddNote">
		        			<textarea class="form-control" placeholder="Nhập ghi chú..." v-model="noteNew" @keyup.enter="createNote()"></textarea>
		        		</div>
		        		<div class="noteOrder-add-more">
	        				<span class="btn-addDetail-order" @click="showAddNote = !showAddNote" v-show="!showAddNote">
	        					<i class="fa fa-plus-circle"></i>
	        				</span>
	        			</div>
	        		</div>
	        	</div>
	        </div>
	        <div class="modal-footer modal-footer-cus">
	          	<button type="button" class="btn btn-addNew-order pull-left" @click="createCustomer()" :disabled="isCreating">Thêm khách hàng</button>
				<button type="button" class="btn dismiss-table-orderCus pull-right" @click="resetCustomer()">Hủy</button>
	        </div>
      	</div>
	</div>
</template>
<script type="text/javascript">

	import {get} from '../../helpers/send'
	import {post} from '../../helpers/send'
	import { EventBus } from '../../helpers/bus'
	import Circle from '../loading/Circle.vue'
	import {momentLocale} from '../../helpers/momentfix'
	import {debounce} from 'lodash'

	export default {

		components: {
			'circle-loading': Circle
		},
		data() {
			return {
				groups: [],
				customers: {
					groups: [],
					name_cus: null,
					email_cus: null,
					phone_cus: null,
					address_cus: null,
					notes: [],
				},
				noteNew: null,
				showAddNote: false,
				processing: false,
				isCreating: false,
				suggestCustomers: [],
				isSearching: false
			}
		},
		mounted() {
			EventBus.$on('get-add-customer', () => {
				this.getAddCreate();
				$("#create-customer-modal").modal('show');
			});
		},
		methods: {
			getAddCreate() {
				this.processing = true;
				get('api/customers/add')
				.then((res) => {
					if (res.data) {
						this.groups = res.data.groups;
					}
					this.processing = false;
				})
				.catch((err) => {
					$.notify('Lỗi tải trang tạo khách hàng mới', 'error');
					this.processing = false;
				})
			},
			createNote() {
				let note = {
					note_content: this.noteNew,
					created_at: momentLocale().format('HH:mm DD-MM-YYYY')
				}
				this.customers.notes.push(note);
				this.resetNoteNew();
			},
			resetNoteNew() {
				this.noteNew = null;
			},
			createCustomer() {
				this.isCreating = true;
				post('api/customers/add', this.customers)
				.then((res) => {
					if (res.data.created) {
						$.notify('Tạo khách hàng mới thành công', 'success');
						this.resetCustomer();
						EventBus.$emit('get-list-customers');
					}
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
					} else 
					if (err.response.status === 302) {
						$.notify(err.response.data.message, 'error');
					}
					this.isCreating = false;
				})
			},
			pickGroup(index) {
				this.customers.groups.push(this.groups[index]);
				this.groups.splice(index, 1);
			},
			removeNote(index) {
				this.customers.notes.splice(index, 1);
			},
			removeGroup(index) {
				this.groups.push(this.customers.groups[index]);
				this.customers.groups.splice(index, 1);
			},
			resetCustomer() {
				$("#create-customer-modal").modal("hide");
				this.customers.groups = [];
				this.customers.notes = [];
				this.customers.name_cus = null;
				this.customers.phone_cus = null;
				this.customers.email_cus = null;
				this.customers.address_cus = null;
				this.isCreating = false;
			},
			searchCustomer: debounce(function () {
				this.isSearching = true;
				if (this.customers.phone_cus) {
					get('api/customers/search?phone='+this.customers.phone_cus)
					.then((res) => {
						this.isSearching = false;
						this.suggestCustomers = res.data;
					})
				} else {
					this.removeCus();
				}
			}, 500),
			removeCus() {
				this.suggestCustomers = [];
			}
		},
		beforeDestroy() {
			EventBus.$off('get-add-customer')
		}
	}
</script>
<style type="text/css">
	.menu-cus-suggest {
		display: block;
		max-height: 200px;
		overflow-x: hidden;
		overflow-y: auto;
	}
</style>