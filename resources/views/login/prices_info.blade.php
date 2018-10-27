@extends('login.layout')
@section('title', 'Thông tin chi tiết bảng giá Chốt Sale')
@section('titleHead', "Chi tiết bảng giá Chốt Sale")
@section('head')
<link rel="stylesheet" type="text/css" href="{{ asset('client/css/roleinfo.css') }}">
@endsection
@section('content')
<div class="body-div">
    <div class="container">
        <table class="table table-bordered table-popup ">
            <thead>
                <tr class="active">
                    <th class="col-md-2 th-head">Tính năng</th>
                    <th class="col-md-1 text-center th-head">MIỄN PHÍ</th>
                    <th class="col-md-1 text-center th-head">CƠ BẢN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="title th-head">Mức phí</td>
                    <td class="text-center">0 đồng</td>
                    <td class="text-center">99K/1 tháng</td>
                </tr>
                <tr>
                    <td class="title th-head">Số page quản lý</td>
                    <td class="text-center">3 Fanpage</td>
                    <td class="text-center">50 Fanpage</td>
                </tr>
                <tr>
                    <td class="title th-head">Số nhân viên</td>
                    <td class="text-center">Không giới hạn</td>
                    <td class="text-center">Không giới hạn</td>
                </tr>
                <tr>
                    <th colspan="3" class="th-head text-center">SIÊU TÍNH NĂNG</th>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Đăng bài/lên lịch đăng bài lên nhiều Fanpage cùng lúc </td>
                    <td class="tr-no-border text-center">
                        <label>
                            <input type="checkbox" dischecked="" disabled="">
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
                    <td class="tr-no-border title-normal">Tự động ẩn comment chứa số điện thoại/email/từ khóa</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Tự động inbox khi khách hàng comment vào bài viết (100% vào mục inbox)</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th colspan="3" class="th-head text-center">HỘI THOẠI</th>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Quản lý comment/inbox tập trung</td>
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
                    <td class="tr-no-border title-normal">Chat realtime</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Tự động like comment</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Gắn nhãn</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Trả lời nhanh</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Báo xấu</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Báo tin nhắn, comment chưa từ khóa **</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Lọc email, sđt theo id bài viết **</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th colspan="3" class="th-head text-center">QUẢN LÝ KHÁCH HÀNG</th>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Lưu khách hàng</td>
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
                    <td class="tr-no-border title-normal">Tải, Xuất file excel</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th colspan="3" class="th-head text-center">QUẢN LÝ BÁN HÀNG</th>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Gửi đơn hàng cho khách hàng qua messenger</td>
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
                    <td class="tr-no-border title-normal">Theo dõi tình trạng đơn hàng</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Quản lý tình trạng giao hàng</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Tích hợp vận chuyển </td>
                    <td class="tr-no-border text-center" colspan="2">
                        Tính năng đang phát triển
                    </td>
                </tr>

                <tr>
                    <th colspan="3" class="th-head text-center">QUẢN LÝ TỒN KHO</th>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Quản lý danh mục sản phẩm</td>
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
                    <td class="tr-no-border title-normal">Quản lý sản phẩm</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Quản lý xuất-nhập kho</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Quản lý khuyến mãi</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th colspan="3" class="th-head text-center">THỐNG KÊ HOẠT ĐỘNG BÁN HÀNG</th>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Thống kê hội thoại (Số tin nhắn, bình luận)</td>
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
                    <td class="tr-no-border title-normal">Thống kê theo danh mục</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Thống kê theo sản phẩm</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">Thống kê đơn hàng (Doanh thu, số đơn hàng)</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tr-no-border title-normal">- Thông kê chi tiết (tiến trình đơn hàng)</td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                    <td class="tr-no-border">
                        <label>
                            <input type="checkbox" checked="" dischecked="" disabled="">
                            <span class="label-text"></span>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th colspan="3" class="th-head text-center">Thanh toán</th>
                </tr>
                <tr>
                    <td class="title th-head">3 tháng</td>
                    <td class="text-center">0 đồng</td>
                    <td class="text-center">297.000 đồng</td>
                </tr>
                <tr>
                    <td class="title th-head">6 tháng</td>
                    <td class="text-center">0 đồng</td>
                    <td class="text-center">594.000 đồng (Tặng thêm 1 tháng sử dụng)</td>
                </tr>
                <tr>
                    <td class="title th-head">12 tháng</td>
                    <td class="text-center">0 đồng</td>
                    <td class="text-center">1188.000 đồng (Tặng thêm 3 tháng sử dụng)</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">
                        <strong>Thông tin chuyển khoản:</strong> <br>
                        Tài khoản Ngân hàng TMCP Ngoại thương Việt Nam - Vietcombank <br>
                        Số tài khoản: 0011 0018 13907 <br>
                        Tên người hưởng: Công ty Cổ phần Vật giá Việt Nam <br>
                        Chi nhánh: Sở giao dịch Hà Nội <br>
                        Sau khi chuyển khoản anh/chị vui lòng gọi đến số hotline (024) 7305 0105 / 01658 299 244 để được kích hoạt dịch vụ
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection