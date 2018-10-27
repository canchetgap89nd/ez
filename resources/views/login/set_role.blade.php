@extends('login.layout')
@section('titleHead', 'Thêm thành viên để cùng quản lý Fanpage')
@section('title', "Phân quyền nhân viên")
@section('content')
<div class="row">
	<div class="col-lg-4 cl-mid cl-gray">
		<div class="cl-ls">
			<h3 class="title-cl-r">Click vào Fanpage để thêm nhân viên hỗ trợ</h3>
			<ul class="list-item list-style-none list-far">

				@isset($pages)
				@foreach($pages as $page)
				<li onclick="getPageAdmins('{{ $page['fb_page_id'] }}'); boldRow($(this));">
					<a href="#" class="pages-manager">
						<img src="https://graph.facebook.com/{{ $page['fb_page_id'] }}/picture?height=60&width=60"> 
						<span class="txtNameStaff">{{ $page['page_name'] }}</span>
					</a>
				</li>
				@endforeach
				@endisset
				
			</ul>
		</div>
	</div>
	<div class="col-lg-4 cl-mid cl-white">
		<div class="box-loading" id="loadding-staff"> 
			<div class="icon-loading">
				<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
				<span class="sr-only">Loading...</span> 
			</div>
		</div>
		<div class="cl-ls hidden" id="mems_panel">
			<h3 class="title-cl-r">Click vào nhân viên bạn muốn thêm</h3>
			<ul class="list-item list-style-none list-t1 list-far" id="list-page-admins">
		        {{-- list members --}}
		    </ul>
		</div>
	</div>
	<div class="col-lg-4 cl-mid cl-bor-lef cl-white">
		<div class="box-loading" id="loadding-roles">
			<div class="icon-loading">
				<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
				<span class="sr-only">Loading...</span>
			</div>
		</div>
		<div class="cl-ls">
			<div class="box_role_ov hidden" id="roles-panel">
				<h3 class="title-cl-r">Lựa chọn chức vụ cho nhân viên rồi xác nhận</h3>
				<div class="container-radio list-far">
					<form id="form-set-perms">
						<input type="hidden" name="fb_page_id" id="id_page_set">
						<input type="hidden" name="fb_id_staff" id="fb_staff_set">
						<div class="radio">
						  	<label>
						    	<input type="radio" name="role_staff" id="saleRole" value="3">
						   		Kinh doanh
						  	</label>
						  	<p class="help-block">Quản lý comment/inbox, tạo và quản lý đơn hàng của mình. Thêm và quản lý khách hàng. Xem các sản phẩm đang khuyến mãi.</p>
						</div>
						<div class="radio">
						  	<label>
						    	<input type="radio" name="role_staff" id="warehouseRole" value="2">
						   		Quản lý kho
						  	</label>
						  	<p class="help-block">Tạo và quản lý sản phẩm, quản lý nhập/xuất kho, tạo chiến dịch khuyến mãi.</p>
						</div>
						<div class="radio">
						  	<label>
						    	<input type="radio" name="role_staff" id="managerRole" value="1">
						   		Kiểm soát viên
						  	</label>
						  	<p class="help-block">Hỗ trợ Admin và các thành viên. Có tất cả các quyền trừ phần quyền nhân viên, kích hoạt Fanpage, xuất file dữ liệu.</p>
						</div>
						<a href="{{ route('info.roles') }}" target="_blank" class="more-info">Xem chi tiết</a>
						<div id="loadding_setup_role" class="hidden">
							<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
							<span class="sr-only">Loading...</span>
						</div>
						<div class="bt-group-t" id="btn_setup_role">
							<button type="button" onclick="setRolesStaff()" class="btn btn-success sbProfile sbRole">Xác nhận</button>
						</div>
					</form>
				</div>
			</div>
			<div class="bt-group-t pull-right">
				<a href="{{ route('set.info') }}">
					<button type="button" class="btn btn-default">Quay lại chọn Fanpage</button>
				</a>
				<a href="{{ route('conversations') }}">
					<button type="button" class="btn btn-default">Vào trang quản trị</button>
				</a>
			</div>
			<div class="boxHelper">
				<p><strong>Lưu ý:</strong></p>
				<ol>
					<li>Sau khi thêm nhân viên, hãy thông báo với nhân viên của bạn đăng nhập vào chốt sale để chấp nhân quyền và sử dụng.</li>
					<li>Tài khoản nhân viên phải là tài khoản mới, chưa sử dụng chốt sale.</li>
					<li>Hotline:  Liên hệ 0165 299 244 để nhận hỗ trợ.</li>
				</ol>
			</div>
		</div>
	</div>
</div>
@endsection