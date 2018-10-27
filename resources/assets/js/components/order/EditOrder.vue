<template>
	<div>
		<div class="modal-content modal-detail-order" v-if="isProcessing">
			<div class="modal-body text-center">
				<circle-load></circle-load>
			</div>
		</div>

		<div class="modal-content modal-detail-order" v-else>
			<div class="modal-body">
				<div class="text-overview-order">
					<div class="col-md-2">
						<span class="txt-overview active">Mã đơn {{ infoOrder.order_code }}</span>
					</div>
					<div class="col-md-2">
						<span class="txt-overview active">{{ textConfirm }}</span>
					</div>
					<div class="col-md-3">
						<span class="txt-overview active">Từ trang: </span>
					</div>
					<div class="col-md-2">
						<a href="javascript:;" class="txt-overview active" @click="showMap = !showMap">{{ showMap ? 'Đóng lịch sử đơn' : 'Lịch sử đơn' }}</a>
					</div>
					<div class="col-md-3">
						<span class="txt-overview active">Người tạo: {{ creater.name }}</span>
					</div>
				</div>
				<div class="map-process-order" v-show="showMap">
					<div class="step-item">
						<div :class="{ 'div-circle circle-green': infoOrder.created_at != null, 'div-circle': infoOrder.created_at == null }" :title="infoOrder.created_at">
							<i class="fa fa-circle"></i>
							<span :class="{ 'line-circle line-green': infoOrder.time_confirmed != null, 'line-circle':  infoOrder.time_confirmed == null }"></span>
						</div>
						<div class="text-under-cirlce">
							Mới
						</div>
					</div>
					<div class="step-item">
						<div :class="{ 'div-circle circle-green': infoOrder.time_confirmed != null, 'div-circle': infoOrder.time_confirmed == null }" :title="formatTime(infoOrder.time_confirmed, 'LLL')">
							<i class="fa fa-circle"></i>
							<span :class="{ 'line-circle line-green': infoOrder.time_sending != null, 'line-circle': infoOrder.time_sending == null }"></span>
						</div>
						<div class="text-under-cirlce">
							Chờ giao hàng
						</div>
					</div>
					<div class="step-item">
						<div :class="{ 'div-circle circle-green': infoOrder.time_sending != null, 'div-circle': infoOrder.time_sending == null }" :title="formatTime(infoOrder.time_sending, 'LLL')">
							<i class="fa fa-circle"></i>
							<span :class="{ 'line-circle line-green': infoOrder.time_sent != null, 'line-circle': infoOrder.time_sent == null }"></span>
						</div>
						<div class="text-under-cirlce">
							Đang giao hàng
						</div>
					</div>
					<div class="step-item">
						<div :class="{ 'div-circle circle-green': infoOrder.time_sent != null, 'div-circle': infoOrder.time_sent == null }" :title="formatTime(infoOrder.time_sent, 'LLL')">
							<i class="fa fa-circle"></i>
							<span :class="{ 'line-circle line-green': infoOrder.time_refunding != null, 'line-circle': infoOrder.time_refunding == null }"></span>
						</div>
						<div class="text-under-cirlce">
							Khách đã nhận
						</div>
					</div>
					<div class="step-item">
						<div :class="{ 'div-circle circle-green': infoOrder.time_refunding != null, 'div-circle': infoOrder.time_refunding == null }" :title="formatTime(infoOrder.time_refunding, 'LLL')">
							<i class="fa fa-circle"></i>
							<span :class="{ 'line-circle line-green': infoOrder.time_refunded != null, 'line-circle': infoOrder.time_refunded == null }"></span>
						</div>
						<div class="text-under-cirlce">
							Hoàn đơn
						</div>
					</div>
					<div class="step-item">
						<div :class="{ 'div-circle circle-green': infoOrder.time_refunded != null, 'div-circle': infoOrder.time_refunded == null }" :title="formatTime(infoOrder.time_refunded, 'LLL')">
							<i class="fa fa-circle"></i>
							<span :class="{ 'line-circle line-green': infoOrder.time_canceled != null, 'line-circle': infoOrder.time_canceled == null }"></span>
						</div>
						<div class="text-under-cirlce">
							Đã hoàn đơn 
						</div>
					</div>
					<div class="step-item">
						<div :class="{ 'div-circle circle-green': infoOrder.time_canceled != null, 'div-circle': infoOrder.time_canceled == null }" :title="formatTime(infoOrder.time_canceled, 'LLL')">
							<i class="fa fa-circle"></i>
						</div>
						<div class="text-under-cirlce">
							Hủy
						</div>
					</div>
				</div>
				<div class="cont-send-receive">
					<div class="col-md-6">
						<div class="send-cus-info">
							<h2 class="title-block-info">
								<span class="txt-title-blockInfo">Thông tin khách hàng</span>
								<span class="btn-edit-blockInfo" v-show="infoOrder.status_order == 'NEW' || infoOrder.status_order == 'CONFIRM'" @click="editInfoCus = !editInfoCus">
									<i class="fa fa-close" v-if="editInfoCus"></i>
									<i class="fa fa-edit" v-else></i>
								</span>
							</h2>
							<div class="body-block-info">
								<div class="avatar-left-info">
									<div class="avatar-detail-order">
										<img :src="'https://graph.facebook.com/'+ infoCus.fb_id_cus +'/picture?height=70&width=70'" v-if="infoCus.fb_id_cus">
										<img v-else src="images/man.png">
									</div>
									<div class="name-under-avatar">
										{{ infoCus.name_cus }}
									</div>
								</div>
								<div class="info-order-add info-block-left">
			        			<div class="info-row-add">
			        				<div class="title-row-order">
			        					<span class="icon-row-order">
			        						<i class="fa fa-user"></i>
			        					</span>
			        				</div>
			        				<div class="detail-row-order">
		        						<input type="text" placeholder="Nhập họ tên" v-model="infoCus.name_cus" class="form-control input-sm" v-if="editInfoCus">
		        						<span v-else>
			        						{{ infoCus.name_cus }}
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
		        						<input type="text" placeholder="Nhập số điện thoại" class="form-control input-sm" v-model="infoCus.phone_cus"  v-if="editInfoCus">
			        					<span v-else>{{ infoCus.phone_cus }}</span>
			        				</div>
			        			</div>
			        			<div class="info-row-add">
			        				<div class="title-row-order">
			        					<span class="icon-row-order">
			        						<i class="fa fa-envelope"></i>
			        					</span>
			        				</div>
			        				<div class="detail-row-order">
			        					<input type="email" placeholder="Nhập địa chỉ email" class="form-control input-sm" v-model="infoCus.email_cus"  v-if="editInfoCus">
			        					<span v-else>{{ infoCus.email_cus }}</span>
			        				</div>
			        			</div>
			        			<div class="info-row-add">
			        				<div class="title-row-order">
			        					<span class="icon-row-order icon-place-order">
			        						<i class="fa fa-map-marker"></i>
			        					</span>
			        				</div>
			        				<div class="detail-row-order">
			        					<input type="email" placeholder="Nhập địa chỉ" class="form-control input-sm" v-model="infoCus.address_cus"  v-if="editInfoCus">
			        					<span v-else>{{ infoCus.address_cus }}</span>
			        				</div>
			        			</div>
			        		</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="receive-cus-info">
							<h2 class="title-block-info">
								<span class="txt-title-blockInfo">Thông tin nhận hàng</span>
								<span class="btn-edit-blockInfo" v-show="infoOrder.status_order == 'NEW' || infoOrder.status_order == 'CONFIRM'" @click="editInfoReceive = !editInfoReceive">
									<i class="fa fa-close" v-if="editInfoReceive"></i>
									<i class="fa fa-edit" v-else></i>
								</span>
							</h2>
							<div class="body-block-info">
								<div class="avatar-left-info">
									<div class="avatar-receive-order">
										<img src="images/receive-none.png">
									</div>
								</div>
								<div class="info-order-add info-block-left">
				        			<div class="info-row-add">
				        				<div class="title-row-order">
				        					<span class="icon-row-order">
				        						<i class="fa fa-user"></i>
				        					</span>
				        				</div>
				        				<div class="detail-row-order">
				        					<input type="text" placeholder="Nhập họ tên" v-model="infoOrder.name_receive" class="form-control input-sm" v-if="editInfoReceive">
			        						<span v-else>
				        						{{ infoOrder.name_receive }}
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
				        					<input type="text" placeholder="Nhập số điện thoại" v-model="infoOrder.phone_receive" class="form-control input-sm" v-if="editInfoReceive">
			        						<span v-else>
				        						{{ infoOrder.phone_receive }}
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
				        					<input type="email" placeholder="Nhập địa chỉ email" v-model="infoOrder.email_receive" class="form-control input-sm" v-if="editInfoReceive">
			        						<span v-else>
				        						{{ infoOrder.email_receive }}
			        						</span>
				        				</div>
				        			</div>
				        			<div class="info-row-add">
				        				<div class="title-row-order">
				        					<span class="icon-row-order icon-place-order" style="margin-left: -4px">
				        						<i class="fa fa-home"></i>
				        					</span>
				        				</div>
				        				<div class="detail-row-order">
			        						<input type="text" placeholder="Nhập thêm địa chỉ VD: số nhà 15" class="form-control input-sm" v-model="infoOrder.ad_receive" v-if="editInfoReceive">
			        						<span v-else>
			        							{{ infoOrder.ad_receive }}
			        						</span>
				        				</div>
				        			</div>
				        			<div class="info-row-add">
				        				<div class="title-row-order">
				        					<span class="icon-row-order icon-place-order">
				        						<i class="fa fa-map-marker"></i>
				        					</span>
				        				</div>
				        				<div class="detail-row-order" v-if="editInfoReceive">
				        					<search-address></search-address>
				        				</div>
				        				<div class="detail-row-order" v-else>
				        					{{this.wardActive.name}}, {{this.districtActive.name}}, {{this.provinceActive.name}}
				        				</div>
				        			</div>
			        			</div>
							</div>
						</div>
					</div>
				</div>
				<div class="cont-order-listCus">
					<h2 class="title-block-info">
					<span class="txt-title-blockInfo">Thông tin sản phẩm</span>
				</h2>
				<div class="body-block-info">
					<div class="cont-table-custom">
						<div class="inner-cont-tableCustom">
							<table class="table-custom">
								<thead>
									<tr>
										<th></th>
										<th class="cs-ellipsis-clm">
											Sản phẩm
										</th>
										<th class="cs-ellipsis-clm text-right">
											Số lượng
										</th>
										<th class="cs-ellipsis-clm text-right">
											Đơn giá
										</th>
										<th class="cs-ellipsis-clm text-right">
											KM (%)
										</th>
										<th class="cs-ellipsis-clm text-right">
											Giá sau KM
										</th>
										<th class="cs-ellipsis-clm text-right">
											Thành tiền
										</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(product, index) in products">
										<td class="cs-ellipsis-clm thumb-product">
											<img :src="product.prod_thumb" v-if="product.prod_thumb">
										</td>
										<td class="cs-ellipsis-clm">
											<div>
												<span class="txt-name-productOrder" :title="product.prod_name">
													{{ product.prod_name }}
												</span>
											</div>
											<div>
												<span class="text-blur" v-if="product.properties">( {{ product.properties }} )</span>
											</div>
										</td>
										<td class="cs-ellipsis-clm text-right quantity-numberOrder">
											<input v-if="infoOrder.status_order == 'NEW' || infoOrder.status_order == 'CONFIRM'" type="number" class="form-control" min="1" v-model="product.quantity">
											<span v-else>
												{{ product.quantity }}
											</span>
										</td>
										<td class="cs-ellipsis-clm text-right">{{ parseFloat(product.price).toLocaleString() }}</td>
										<td class="cs-ellipsis-clm text-right">
											{{ product.perc_disc }}
										</td>
										<td class="cs-ellipsis-clm text-right">{{ parseFloat(product.price_sale).toLocaleString() }}</td>
										<td class="cs-ellipsis-clm text-right">{{ parseFloat(product.price_sale * product.quantity).toLocaleString() }}</td>
										<td class="cs-ellipsis-clm">
											<span class="del-orderCus" v-show="infoOrder.status_order == 'NEW' || infoOrder.status_order == 'CONFIRM'" title="Xóa SP khỏi danh sách" @click="removeProduct(index)">
												<i class="fa fa-minus-square"></i>
											</span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="contUnder-table-total">
						<table class="table-total-under">
							<thead>
								<tr>
									<th class="thumb-product"></th>
									<th>
										<search-product v-show="infoOrder.status_order == 'NEW' ||infoOrder.status_order == 'CONFIRM'"></search-product>
									</th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th class="total-cell"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<th>Tổng tiền</th>
									<td class="text-center"></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="total-cell">
										{{ parseFloat(totalValue).toLocaleString() }}
									</td>
								</tr>
								<tr>
									<td></td>
									<th>Tổng tiền sau KM</th>
									<td class="text-center"></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="total-cell">
										{{ parseFloat(valueHasSale).toLocaleString() }}
									</td>
								</tr>
								<tr>
									<td></td>
									<th>Giảm giá</th>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="total-cell">
										<input v-if="infoOrder.status_order == 'NEW' || infoOrder.status_order == 'CONFIRM'" type="text" v-model="infoOrder.discount" @input="fixPrice($event, infoOrder, 'discount')">
										<span v-else>
											{{ infoOrder.discount }}
										</span>
									</td>
								</tr>
								<tr>
									<td></td>
									<th>Vận chuyển</th>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="total-cell">
										<input v-if="infoOrder.status_order == 'NEW' || infoOrder.status_order == 'CONFIRM'" type="text" v-model="infoOrder.ship_fee" @input="fixPrice($event, infoOrder, 'ship_fee')">
										<span v-else>{{ infoOrder.ship_fee }}</span>
									</td>
								</tr>
								<tr class="row-bold">
									<td></td>
									<th class="txt-color-green text-bold">Số tiền cần thanh toán</th>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="total-cell">
										{{ parseFloat(totalAmount).toLocaleString() }}
									</td>
								</tr>
								<tr v-for="(pay, index) in payments">
									<td></td>
									<td>
										<span class="txt-name-productOrder">
											{{ pay.text }}
											<span class="del-orderCus" v-if="(infoOrder.status_order != 'CANCELED') && (pay.id === null)" title="Xóa" @click="removeRowPay(index)">
												<i class="fa fa-minus-square"></i>
											</span>
										</span>
									</td>
									<td></td>
									<td></td>
									<td class="text-danger"></td>
									<td class="text-center">{{ pay.created_at }}</td>
									<td class="total-cell">
										<input v-if="(infoOrder.status_order != 'CANCELED') && (pay.id === null)" type="text" v-model="pay.amount_pay" @input="fixPrice($event, pay, 'amount_pay')">
										<span v-else>{{ pay.amount_pay }}</span>
									</td>
								</tr>
								<tr class="row-payment">
									<td></td>
									<td>
										<div title="Click vào đây để thêm dòng thanh toán" class="btn-payment-order" @click="addRowPay(false)" v-show="infoOrder.status_order != 'NEW' && infoOrder.status_order != 'CANCELED'">
											<i class="fa fa-plus-circle text-orange"></i>
											<span>Thêm thanh toán</span>
										</div>
									</td>
									<td>
										<div title="Click vào đây để thêm dòng hoàn lại tiền" class="btn-payment-order refund-btn" @click="addRowPay(true)" v-show="infoOrder.status_order != 'NEW' && infoOrder.status_order != 'CANCELED'">
											<i class="fa fa-minus-circle text-orange"></i>
											<span>Hoàn lại thanh toán</span>
										</div>
									</td>
									<td></td>
									<td></td>
									<td></td>
									<td class="total-cell"></td>
								</tr>
								<tr class="row-bold">
									<td></td>
									<td class="text-orange text-bold">Số tiền còn lại</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="text-orange text-bold total-cell">
										{{ (parseFloat(totalAmount) - parseFloat(totalPay)).toLocaleString() }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="note-underTable-order">
						<div class="inner-note">
							<span>Ghi chú:</span>
							<textarea class="form-control" v-model="infoOrder.note_order" placeholder="Đây là ghi chú của đơn hàng ..." :disabled="infoOrder.status_order != 'NEW' && infoOrder.status_order != 'CONFIRM'"></textarea>
						</div>
					</div>
					<div class="infoTransport-order">
						<h2 class="title-block-info">
								<span class="txt-title-blockInfo">Thông tin vận chuyển</span>
							</h2>
							<div class="inner-infoTransport-order">
							<div class="note-transport">
								<label>Ghi chú: </label>
								<textarea class="form-control" rows="1" placeholder="Ghi chú vận chuyển..." v-model="transport.note" :disabled="infoOrder.status_order != 'NEW' && infoOrder.status_order != 'CONFIRM'"></textarea>
							</div>
							<div class="price-transport">
								<input type="text" :disabled="infoOrder.status_order != 'NEW' && infoOrder.status_order != 'CONFIRM'" class="form-control" placeholder="Nhập số tiền..." v-model="transport.amount" @input="fixPrice($event, transport, 'amount')">
								<label>Số tiền</label>
							</div>
							</div>
					</div>
				</div>
				</div>
			</div>
			<div v-if="isSubmiting" class="modal-footer modal-footer-order text-center">
				<span class="processing-icon">
					<i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
					<span class="sr-only">Loading...</span>
	        	</span>
			</div>
			<div v-else class="modal-footer modal-footer-order">
				<button class="btn btn-success pull-left" v-show="infoOrder.status_order !== 'CANCELED' && (role.name == 'ADMINSTRATOR' || role.name == 'MANAGER' || role.name == 'SALER')" @click="saveInfo()">Lưu</button>
				<div class="dropup" style="display: inline-block">
					<button class="btn btn-confirm-order dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" :style="'background-color:' + colorConfirm">{{ textConfirm }}</button>
					<ul class="dropdown-menu" v-show="role.name == 'ADMINSTRATOR' || role.name == 'MANAGER' || role.name == 'SALER'">
				    	<li v-show="infoOrder.status_order == 'NEW'"> 
				    		<a role="button" @click="updateOrder('CONFIRM')">Xác nhận đơn</a>
				    	</li>
				    	<li v-show="infoOrder.status_order == 'NEW' || infoOrder.status_order == 'CONFIRM'">
				    		<a role="button" @click="updateOrder('SENDING')">Đang giao hàng</a>
				    	</li>
				    	<li v-show="infoOrder.status_order == 'NEW' || infoOrder.status_order == 'CONFIRM' || infoOrder.status_order == 'SENDING'">
				    		<a role="button" @click="updateOrder('SENT')">Khách đã nhận</a>
				    	</li>
				    	<li v-show="infoOrder.status_order == 'SENT' || infoOrder.status_order == 'SENDING'">
				    		<a role="button" @click="updateOrder('REFUNDING')">Hoàn đơn</a>
				    	</li>
				    	<li v-show="infoOrder.status_order == 'SENDING' || infoOrder.status_order == 'SENT' || infoOrder.status_order == 'REFUNDING'">
				    		<a role="button" @click="updateOrder('REFUNDED')">Đã hoàn đơn</a>
				    	</li>
				    	<li v-show="infoOrder.status_order === 'NEW' || infoOrder.status_order === 'CONFIRM'">
				    		<a role="button" @click="processDestroyOrder()">Hủy đơn</a>
				    	</li>
			  		</ul>
				</div>
				<button class="btn-dismiss-order" data-dismiss="modal" @click="resetDetailOrder()">Đóng</button>
			</div>
		</div>

		<!-- Modal -->
		<div class="modal fade" id="modal-disable-order" data-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true">
		   	<div class="modal-dialog">
		      	<!-- Modal content-->
		      	<div class="modal-content">
		         	<div class="modal-body">
		            	<p>Khách hàng đã thanh toán tổng cộng {{ parseFloat(infoDisablePay.amount_pay).toLocaleString() }} VNĐ. Bạn có muốn hủy thanh toán này?</p>
		            	<div class="form-group">
		            		<input type="text" class="form-control" v-model="infoDisablePay.amount_dis" placeholder="Nhập vào số tiền hủy" @input="fixPrice($event, infoDisablePay, 'amount_dis')">
		            		<p class="help-block">Số tiền này sẽ bị trừ vào doanh thu</p>
		            	</div>
		         	</div>
		         	<div class="modal-footer">
		         		<span class="processing-icon" v-show="isDestroying">
							<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
							<span class="sr-only">Loading...</span>
			        	</span>
		            	<button type="button" :disabled="isDestroying" class="btn btn-danger" @click="destroyOrder()">Hủy đơn</button>
		            	<button type="button" class="btn btn-default" @click="cancelDestroyOrder()">Bỏ qua</button>
		         	</div>
		      	</div>
		   	</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	
	import {get} from "../../helpers/send"
	import {post} from "../../helpers/send"
	import SlimSelect from 'slim-select'
	import SearchProduct from '../../views/SearchProduct.vue'
	import SearchAddress from '../../views/SearchAddress.vue'
	import { EventBus } from '../../helpers/bus'
	import {formatPrice} from '../../helpers/numeralfix'
	import {valuePrice} from '../../helpers/numeralfix'
	import {numeral} from '../../helpers/numeralfix'
	import {functionHelper} from '../../helpers/myfunction'
	import {momentLocale} from "../../helpers/momentfix"
	import Circle from '../loading/Circle.vue'

	export default {
		components: {
			'search-product': SearchProduct,
			'search-address': SearchAddress,
			'circle-load': Circle,
		},
		props: ['user', 'role'],
		data() {
			return {
				provinceActive: {},
				districtActive: {},
				wardActive: {},
				showMap: false,
				products: [],
				payments: [],
				infoOrder: {
					id: null,
					order_code: null,
					created_at: null,
					time_confirmed: null,
					time_sending: null,
					time_sent: null,
					total_value: 0,
					total_amount: 0,
					value_has_sale: 0,
					total_pay: 0,
					discount: 0,
					ship_fee: 0,
					ad_receive: null
				},
				infoCus: {
					name_cus: null,
					phone_cus: null,
					email_cus: null,
					address_cus: null,
				},
				creater: {
					name: null
				},
				transport: {
					note: null,
					amount: 0
				},
				infoDisablePay: {
					amount_dis: 0,
					amount_pay: 0,
					order_id: null,
					pay_id: null
				},
				editInfoCus: false,
				editInfoReceive: false,
				productsDel: [],
				isSubmiting: false,
				isProcessing: false,
				isDestroying: false,
				textConfirm: "Xác nhận đơn",
				colorConfirm: "#000"
			}
		},
		watch: {
			totalDebt () {
				if (!this.transport.id) {
					this.transport.amount = formatPrice(this.totalDebt)
				}
			}
		},
		computed: {
			totalValue() {
				let total = 0;
				for (var i = 0; i < this.products.length; i++) {
					total += this.products[i].price * this.products[i].quantity;
				}
				return total;
			},
			valueHasSale() {
				let total = 0;
				for (var i = 0; i < this.products.length; i++) {
					total += this.products[i].price_sale * this.products[i].quantity;
				}
				return total;
			},
			totalAmount() {
				return this.valueHasSale - valuePrice(this.infoOrder.discount) + valuePrice(this.infoOrder.ship_fee);
			},
			totalPay() {
				let pay = 0;
				for (var i = 0; i < this.payments.length; i++) {
					if (this.payments[i].refund) {
						pay -= valuePrice(this.payments[i].amount_pay);
					} else {
						pay += valuePrice(this.payments[i].amount_pay);
					}
				}
				return pay;
			},
			totalDebt() {
				return this.totalAmount - this.totalPay;
			}
		},
		mounted() {
			EventBus.$on('process-edit-order', (id) => {
				this.getEditOrder(id);
			})
			EventBus.$on('choose-product', (product) => {
				if (!this.hasProdInOrder(product.id)) {
					let priceSale = product.hasSale ? product.prod_price - (product.prod_price * product.perc_disc/100) : product.prod_price;
					let prod = {
						prod_name: product.prod_name,
						camp_id: product.camp_id,
						prod_thumb: product.prod_thumb,
						quantity: 1,
						price: product.prod_price,
						price_sale: priceSale,
						perc_disc: product.perc_disc,
						hasSale: product.hasSale,
						prod_id: product.id,
						order_id: this.infoOrder.id,
						properties: product.properties
					}
					this.products.push(prod);
				}
			})
			EventBus.$on('address-pick', (ad) => {
				this.provinceActive.id = ad.province_id;
				this.districtActive.id = ad.district_id;
				this.wardActive.id = ad.ward_id;
			});
		},
		methods: {
			fixPrice(e, o, prop) {
			    e.target.value = numeral(e.target.value).format('0,0');
			    this.$set(o, prop, e.target.value);
		  	},
		  	formatTime(time, formater) {
		  		return momentLocale(time).format(formater);
		  	},
		  	formatPrice(price) {
		  		return formatPrice(price);
		  	},
			getEditOrder(id) {
				this.isProcessing = true;
				get('api/orders/edit/'+id)
				.then((res) => {
					if (res.data.infoOrder) {
						res.data.infoOrder.discount = formatPrice(res.data.infoOrder.discount);
						res.data.infoOrder.ship_fee = formatPrice(res.data.infoOrder.ship_fee);
						for (var i = 0; i < res.data.payments.length; i++) {
							res.data.payments[i].amount_pay = formatPrice(res.data.payments[i].amount_pay);
							res.data.payments[i].created_at = momentLocale(res.data.payments[i].created_at).format('HH:mm DD/MM/YYYY');
							res.data.payments[i].text = res.data.payments[i].refund ? "Hoàn lại khách hàng" : "Khách hàng đã thanh toán";
						}
						if (res.data.transport) {
							res.data.transport.amount = formatPrice(res.data.transport.amount);
							this.transport = res.data.transport;
						}
						this.provinceActive.id = res.data.infoOrder.province_id;
						this.provinceActive.name = res.data.infoOrder.name_province;
						this.districtActive.id = res.data.infoOrder.district_id;
						this.districtActive.name = res.data.infoOrder.name_district;
						this.wardActive.id = res.data.infoOrder.ward_id;
						this.wardActive.name = res.data.infoOrder.name_ward;
						this.infoOrder = res.data.infoOrder;
						this.infoCus = res.data.infoCus;
						this.payments = res.data.payments;
						this.creater = res.data.creater;
						this.changeTextColorConfirmButton(res.data.infoOrder.status_order);
						this.products = res.data.products;
						for (var i = 0; i < this.products.length; i++) {
							let tg = this.products[i].price - this.products[i].price_sale;
							this.products[i].perc_disc = Math.round((tg/this.products[i].price)*100);
						}
					}
					this.isProcessing = false;
				})
				.catch((err) => {
					if (err.response.status == 302) {
						$.notify(err.response.data.message, 'error');
					}
					this.isProcessing = false;
				})
			},
			hasProdInOrder(id) {
				let check = false;
				let prodOrder = this.products;
				for (var i = 0; i < prodOrder.length; i++) {
		  			if (prodOrder[i].prod_id == id) {
		  				check = true;
		  				break;
		  			}
		  		}
		  		return check;
			},
			hasProdInTrash(id) {
				let index = null;
				for (var i = 0; i < this.productsDel.length; i++) {
		  			if (this.productsDel[i].prod_id == id) {
		  				index = i;
		  				break;
		  			}
		  		}
		  		return index;
			},
			addRowPay(refund) {
				let text = refund ? "Hoàn lại khách hàng" : "Khách hàng đã thanh toán";
				let newPay = {
					text: text,
					id: null,
					amount_pay: 0,
					created_at: momentLocale().format('HH:mm DD/MM/YYYY'),
					refund: refund,
					order_id: this.infoOrder.id
				}
				this.payments.push(newPay);
			},
			removeProduct(index) {
				this.productsDel.push(this.products[index]);
				this.products.splice(index, 1);
			},
			removeRowPay(index) {
				this.payments.splice(index, 1);
			},
			resetDisPay() {
				$("#modal-disable-pay").modal("hide");
				this.infoDisablePay.order_id = null;
				this.infoDisablePay.pay_id = null;
				this.infoDisablePay.amount_pay = 0;
				this.infoDisablePay.amount_dis = 0;
			},
			processUpdate() {
				let res = {};
				res.infoCus = this.infoCus;
				res.infoOrder = this.infoOrder;
				res.products = this.products;
				res.productsDel = this.productsDel;
				res.payments = this.payments;
				res.transport = this.transport;
				res.infoOrder.province_id = this.provinceActive.id;
				res.infoOrder.district_id = this.districtActive.id;
				res.infoOrder.ward_id = this.wardActive.id;
				res.infoOrder.discount = valuePrice(res.infoOrder.discount);
				res.infoOrder.ship_fee = valuePrice(res.infoOrder.ship_fee);
				res.infoOrder.total_pay = this.totalPay;
				res.infoOrder.total_amount = this.totalAmount;
				res.infoOrder.value_has_sale = this.valueHasSale;
				res.infoOrder.total_value = this.totalValue;
				for (var i = 0; i < res.payments.length; i++) {
					res.payments[i].amount_pay = valuePrice(res.payments[i].amount_pay);
				}
				res.transport.amount = valuePrice(res.transport.amount);
				return res;
			},
			changeTextColorConfirmButton(type) {
				switch (type) {
					case 'NEW':
						this.textConfirm = "Đơn mới";
						this.colorConfirm = "#4CAF50";
						break;
					case 'CONFIRM':
						this.textConfirm = "Xác nhận đơn";
						this.colorConfirm = "#9a9a9a";
						break;
					case 'SENDING':
						this.textConfirm = "Đang giao hàng";
						this.colorConfirm = "#f7b26c";
						break;
					case 'SENT':
						this.textConfirm = "Đã giao hàng";
						this.colorConfirm = "#00b140";
						break;
					case 'REFUNDING':
						this.textConfirm = "Hoàn đơn";
						this.colorConfirm = "#c6a910";
						break;
					case 'REFUNDED':
						this.textConfirm = "Đã hoàn đơn";
						this.colorConfirm = "#996b58";
						break;
					case 'CANCELED':
						this.textConfirm = "Đã hủy đơn";
						this.colorConfirm = "#ab0202";
						break;
					default: break; 
				}
			},
			updateOrder(type) {
				this.isSubmiting = true;
				let form = this.processUpdate();
				form.status = type;
				post('api/orders/update', form)
				.then((res) => {
					this.isSubmiting = false;
					if (res.data.updated) {
						$.notify("Cập nhật trạng thái thành công", "success");
						EventBus.$emit('close-edit-order');
						EventBus.$emit('list-orders');
						EventBus.$emit('get-count-order-new');
						this.resetDetailOrder();
					}
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
					} else 
					if (err.response.status == 302) {
						$.notify(err.response.data.message, 'error');
					}
					this.isSubmiting = false;
				})
			},
			processDestroyOrder() {
				this.infoDisablePay.amount_pay = this.infoOrder.total_pay;
				this.infoDisablePay.order_id = this.infoOrder.id;
				this.infoDisablePay.amount_dis = 0;
				$("#modal-disable-order").modal('show');
				$('#modal-disable-order.modal').on('hidden.bs.modal', function (e) {
				    if($('#modal-disable-order.modal').hasClass('in')) {
				    	$('body').addClass('modal-open');
				    }    
				});
			},	
			destroyOrder() {
				this.isDestroying = true;
				let form = 	{
								order_id: this.infoDisablePay.order_id,
								amount_dis: valuePrice(this.infoDisablePay.amount_dis)
							};
				post('api/orders/destroy', form)
				.then((res) => {
					this.isDestroying = false;
					if (res.data.destroyed) {
						$.notify("Hủy đơn thành công", "success");
						EventBus.$emit('close-edit-order');
						EventBus.$emit('list-orders');
						if($('#modal-disable-order.modal').hasClass('in')) {
					    	$('body').removeClass('modal-open');
					    }
					    $("#modal-disable-order").modal('hide');
					}
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
					}
					this.isDestroying = false;
				})
			},
			cancelDestroyOrder() {
				$("#modal-disable-order").modal('hide');
				this.infoDisablePay.order_id = null;
				this.infoDisablePay.pay_id = null;
				this.infoDisablePay.amount_pay = 0;
				this.infoDisablePay.amount_dis = 0;
			},
			saveInfo() {
				this.isSubmiting = true;
				let form = this.processUpdate();
				form.status = this.infoOrder.status_order;
				post('api/orders/save', form)
				.then((res) => {
					this.isSubmiting = false;
					if (res.data.saved) {
						$.notify("Lưu đơn hàng thành công", "success");
						EventBus.$emit('close-edit-order');
						EventBus.$emit('list-orders');
						if (res.data.message) {
							$.notify(res.data.message, "warn");
						}
						this.resetDetailOrder();
					}
				})
				.catch((err) => {
					if (err.response.status == 422) {
						let errors = err.response.data.errors;
						for (let k in errors) {
							$.notify(errors[k], 'error');
						}
					} else 
					if (err.response.status == 302) {
						$.notify(err.response.data.message, 'error');
					}
					this.isSubmiting = false;
				})
			},
			resetDetailOrder() {
				this.provinceActive = {};
				this.districtActive = {};
				this.wardActive = {};
				this.infoCus.name_cus = null;
				this.infoCus.phone_cus = null;
				this.infoCus.email_cus = null;
				this.infoCus.address_cus = null;
				this.infoOrder.total_value = 0;
				this.infoOrder.value_has_sale = 0;
				this.infoOrder.total_amount = 0;
				this.infoOrder.discount = 0;
				this.infoOrder.ship_fee = 0;
				this.infoOrder.note = null;
				this.infoOrder.name_receive = null;
				this.infoOrder.phone_receive = null;
				this.infoOrder.email_receive = null;
				this.infoOrder.ad_receive = null;
				this.products = [];
				this.payments = [];
				this.transport.note = null;
				this.transport.amount = 0;
				this.transport.id = null;
				this.editInfoCus = false;
				this.editInfoReceive = false;
			}
		},
		beforeDestroy () {
			EventBus.$off('choose-product')
			EventBus.$off('address-pick')
			EventBus.$off('process-edit-order')
		}
	}
</script>
<style type="text/css">
	#modal-disable-pay.modal.in .modal-dialog {
	    transform: translateY(700px);
	}
	#modal-disable-order.modal.in .modal-dialog {
	    transform: translateY(700px);
	}
</style>