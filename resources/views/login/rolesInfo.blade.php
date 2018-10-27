@extends('login.layout')
@section('title', 'Thông tin vai trò trang')
@section('titleHead', "Quyền hạn các vai trò trên Chốt Sale")
@section('head')
<link rel="stylesheet" type="text/css" href="{{ asset('client/css/roleinfo.css') }}">
@endsection
@section('content')
<div class="body-div">
    <div class="container">
        <table class="table table-bordered table-popup ">
            <thead>
                <tr class="active">
                    <th class="col-md-2 th-head">Chức danh</th>
                    <th class="col-md-1 text-center th-head">Admin</th>
                    <th class="col-md-1 text-center th-head">Kinh doanh</th>
                    <th class="col-md-1 text-center th-head">Quản lý kho</th>
                    <th class="col-md-1 text-center th-head">Kiểm soát</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="title">Quản lý bình luận/tin nhắn</td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="title">Quản lý khách hàng</td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="title">Quản lý đơn hàng</td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">Chỉ những đơn hàng của nhân viên đó tạo hoặc được ủy quyền từ nhân viên khác</td>
                    <td class="text-center">Chỉ xem</td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="title">Quản lý giao hàng</td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">Chỉ xem</td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title">Quản lý sản phẩm</td>
                    <td class="tr-no-border"></td>
                    <td class="tr-no-border"></td>
                    <td class="tr-no-border"></td>
                    <td class="tr-no-border"></td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal"> - Quản lý danh sách sản phẩm</td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal"> - Quản lý nhập/xuất kho</td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-top-no-border title-normal"> - Quản lý khuyến mãi</td>
                    <td class="tr-top-no-border text-center" >
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-top-no-border text-center" >Chỉ xem</td>
                    <td class="tr-top-no-border text-center" >
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-top-no-border text-center" >
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title">Cài đặt hệ thống</td>
                    <td class="tr-no-border"></td>
                    <td class="tr-no-border"></td>
                    <td class="tr-no-border"></td>
                    <td class="tr-no-border"></td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal"> - Cài đặt chung</td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal"> - Cài đặt nhãn khách hàng</td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal"> - Cài đặt trả lời nhanh</td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal"> - Danh sách đen</td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal"> - Lọc thông tin</td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal"> - Tích hợp</td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                <tr>
                    <td class="title">Xem thống kê</td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="title">Xuất File dữ liệu </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="title">Phân quyền nhân viên</td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" checked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label>
                            <input type="checkbox" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection