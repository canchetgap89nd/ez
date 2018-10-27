<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Chốt Sale - Phần mềm quản lý Fanpage miễn phí</title>
        <link href="https://fonts.googleapis.com/css?family=Roboto:100i,400,700" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{ asset('/plugins/font-awesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/bootstrap/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('home_client/css/style.css') }}">
        <meta name="description" content="Phần mềm quản lý Fanpage Facebook miễn phí, không giới hạn số Fanpage, giúp bạn quản lý comment, inbox, thông tin khách hàng, quản lý đơn hàng, quản lý kho, tất cả đều có trong Chốt Sale.">
        <meta name="keywords" content="Chốt sale, Chốt Sale, chot sale, quản lý Fanpage miễn phí, quản lý Fanpage, phần mềm, quản lý không giới hạn fanpage, tự động ẩn bình luận, tự động ẩn comment">
        <meta name="robots" content="all" />
        <link rel="icon" href="{{ asset('/favicon.ico') }}">
        <meta property="og:url" content="https://chotsale.com.vn/" />
        <meta property="og:type" content="article" />
        <meta property="og:title" content="Chốt Sale - Phần mềm quản lý Fanpage miễn phí" />
        <meta property="og:description" content="Phần mềm quản lý Fanpage Facebook miễn phí, không giới hạn số Fanpage, giúp bạn quản lý comment, inbox, thông tin khách hàng, quản lý đơn hàng, quản lý kho, tất cả đều có trong Chốt Sale." />
        <meta property="og:image" content="{{ asset('home_client/images/banner.png') }}" />
        <meta property="fb:app_id" content="933735970113789" />
        <meta property="og:image:alt" content="Chốt Sale - Phần mềm quản lý Fanpage miễn phí" />
        <style type="text/css">
            .background-loading {
                background-color: #FFFFFF;
                position: fixed;
                width: 100%;
                height: 100%;
                z-index: 9999999;
                display: none;
            }
            .icon-loading {
                position: fixed;
                top: 45%;
                color: #01531d;
                left: 50%;
            }
        </style>
    </head>
    <body>
        <!-- login loadding -->
        <div class="background-loading">
            <div class="icon-loading">
                <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw" style="color: #00b140;"></i>
                <span class="sr-only">Loading...</span> 
            </div>
        </div>

        <div id="header">
            <div class="container">
                <div id="header-inner" class="row">
                    <div id="logo" class="col-md-3 col-sm-12 col-xs-12 text-center">
                        <h1><a href="#"><img src="{{ asset('home_client/images/logo.png') }}" alt="Logo"></a>
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#menu">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </h1>
                    </div>
                    <div id="menu" class="collapse navbar-collapse col-md-9 col-sm-12 col-xs-12">
                        <ul>
                            <li><a href="https://chotsale.com.vn/blog/huong-dan-dang-ky-su-dung-phan-mem-chot-sale-12-08-2018.htm" target="_blank">Hướng dẫn</a></li>
                            <li><a href="#main">Tính năng</a></li>
                            <li><a href="#priceBoard">Bảng giá</a></li>
                            <li><a target="_blank" href="https://chotsale.com.vn/blog">Blog</a></li>
                            <li><a target="_blank" href="https://www.facebook.com/groups/chotsale/">Group</a></li>
                            <li><a href="{{ $loginUrl }}">Đăng ký / Đăng nhập</a></li>
                        </ul>
                    </div>
                </div>
                <!--End Header-Inner-->

                <div id="header-section" class="row">
                    <div id="slogan" class="col-md-6 col-sm-12 col-xs-12">
                        <h3>Quản lý tất cả fanpage của bạn tại cùng 1 nơi</h3>
                        <h2>miễn phí trọn đời</h2>
                        <a class="btn-facebook btn-1" href="{{ $loginUrl }}">
                            <i class="fa fa-facebook facebook-custom" ></i>
                            <button type="button" class="btnRegis">
                                Đăng ký ngay
                            </button>
                        </a>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <img class="img-responsive"  src="{{ asset('home_client/images/imac.png') }}" alt="iMac">
                    </div>
                </div>
                <!--End Header-Section-->

            </div>
        </div>
        <!--End Header-->

        <div id="main">
            <div class="container">
                <div id="one-title" class="row title-area">
                    <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                        <h4>Tính năng nổi bật <strong>CHỐT SALE</strong></h4>
                    </div>
                </div>
                <div class="article row">
                    <div class="col-md-4 col-sm-6 col-xs-12 article-left">
                        <div id="article-left-one">
                            <div id="icon-title-1">
                                <div class="article-left-img">
                                    <img class="img-responsive" src="{{ asset('home_client/images/icon-1.png') }}" alt="Icon-1" id="icon-1">
                                </div>
                                <div class="article-left-txt">
                                    <div id="description-title-one">
                                        <h5>Không giới hạn</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="article-left-txt">
                                <div id="description-block-one">
                                    <p>Không giới hạn Fanpage quản lý</p>
                                    <p>Không giới hạn tài khoản nhân viên</p>
                                    <p>Không giới hạn thiết bị đăng nhập</p>
                                </div>
                            </div>
                        </div>
                        <div class="clear-fix"></div>

                        <div id="article-left-two">
                            <div id="icon-title-2">
                                <div class="article-left-img">
                                    <img class="img-responsive" src="{{ asset('home_client/images/icon-2.png') }}" alt="Icon-2" id="icon-2">
                                </div>
                                <div class="article-left-txt">
                                    <div id="description-title-two">
                                        <h5>Quản lý comment/inbox</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="article-left-txt">
                                <div id="description-block-two">
                                    <p>Ẩn comment chứa SĐT, ẩn comment chứa từ khóa</p>
                                    <p>Gắn tag, phân loại khách hàng dễ dàng</p>
                                    <p>Sử dụng câu trả lời nhanh để KH không phải chờ đợi</p>
                                </div>
                            </div>
                        </div>
                        <div class="clear-fix"></div>

                        <div id="article-left-three">
                            <div id="icon-title-3">
                                <div class="article-left-img">
                                    <img class="img-responsive" src="{{ asset('home_client/images/icon-3.png') }}" alt="Icon-3" id="icon-3">
                                </div>
                                <div class="article-left-txt">
                                    <div id="description-title-three">
                                        <h5>Tạo đơn khi đang chat với khách hàng</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="article-left-txt" style="margin-left: 75px;">
                                <div id="description-block-three">
                                    <p>Tiết kiệm thời gian</p>
                                    <p>Dễ dàng trong việc tổng hợp đơn chốt cuối ngày</p>
                                    <p>Gửi thông tin đơn hàng qua inbox để khách hàng xác nhận ngay</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="article-img col-md-4">
                        <div class="article-img-three">
                            <img src="{{ asset('home_client/images/anh-1.png') }}" alt="Anh-3" id="artice-slide" width="463px" height="254px">
                        </div>
                    </div>


                    <div class="col-md-4 col-sm-6 col-xs-12 article-right text-right">
                        <div id="article-right-one">
                            <div id="icon-title-4">
                                <div class="article-right-txt four">
                                    <div id="description-title-four">
                                        <h5>Quản lý kho hàng</h5>
                                    </div>
                                    <div id="description-block-four">
                                        <p>Tạo và quản lý sản phẩm</p>
                                        <p>Quản lý tồn kho</p>
                                        <p>Thông báo khi sắp hết hàng</p>
                                    </div>
                                </div>
                                <div class="article-right-img">
                                    <img class="img-responsive" src="{{ asset('home_client/images/icon-4.png') }}" alt="Icon-4" width="54px"
                                         height="54px" id="icon-4">
                                </div>
                            </div>
                        </div>
                        <div class="clear-fix"></div>

                        <div id="article-right-two">
                            <div id="icon-title-5">
                                <div class="article-right-txt">
                                    <div id="description-title-five">
                                        <h5>Báo cáo</h5>
                                    </div>
                                    <div id="description-block-five">
                                        <p>Báo cáo số lượng comment/inbox</p>
                                        <p>Báo cáo số đơn hàng từ fanpage</p>
                                        <p>Báo cáo doanh thu bán hàng từ Facebook</p>
                                    </div>
                                </div>
                                <div class="article-right-img">
                                    <img class="img-responsive" src="{{ asset('home_client/images/icon-5.png') }}" alt="Icon-5" width="54px"
                                         height="54px" id="icon-5">
                                </div>
                            </div>
                        </div>
                        <div class="clear-fix"></div>

                        <div id="article-right-three">
                            <div id="icon-title-6" style="margin-top: 42px;">
                                <div class="article-right-txt">
                                    <div id="description-title-six">
                                         <h5>Tiện ích khác</h5>
                                    </div>
                                    <div id="description-block-six">
                                        <p>Lên lịch đăng bài trên nhiều Fanpage cùng lúc</p>
                                        <p>Đã có app cho điện thoại</p>
                                        <p>Quản lý Instagram chat <br> (Đang phát triển)</p>
                                        <p>Tạo popup quảng cáo trên website (Đang phát triển)</p>
                                    </div>
                                </div>
                                <div class="article-right-img">
                                    <img class="img-responsive" src="{{ asset('home_client/images/icon-6.png') }}" alt="Icon-6" width="54px"
                                         height="54px" id="icon-6">
                                </div>
                            </div>
                        </div>
                        <div class="clear-fix"></div>
                    </div>

                </div>
                <div id="priceBoard" class="row title-area">
                    <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                        <h4>Bảng giá <strong>CHỐT SALE</strong></h4>
                    </div>
                </div>
                <div class="article row">
                    <div class="col-sm-6 col-xs-12 text-center">
                        <div class="plan">
                            <div class="plan-inner">
                                <div class="entry-title">
                                    <h3>Miễn phí</h3>
                                    <div class="price">0<span>/VNĐ</span>
                                    </div>
                                </div>
                                <div class="entry-content">
                                    <ul>
                                        <li><strong>3 Fanpage </strong> </li>
                                        <li><strong>Không giới hạn TK nhân viên</strong> </li>
                                        <li><strong>X</strong><br></li>
                                        <li><strong>X <br>   </strong></li>
                                        <li><strong>X</strong></li>
                                        <li><strong>X</strong></li>
                                        <li><strong>Quản lý đơn hàng</strong></li>
                                        <li><strong>Quản lý tồn kho</strong></li>
                                        <li><strong>Thống kê kết quả bán hàng</strong></li>
                                        <li>
                                            <a href="{{ route('info.prices') }}">Xem chi tiết</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="btn">
                                    <a href="{{ $loginUrl }}">Đăng ký ngay</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end of price tab-->
                    <!--price tab-->
                    <div class="col-sm-6 col-xs-12 text-center">
                        <div class="plan basic">
                            <div class="plan-inner">
                                <div class="hot">hot</div>
                                <div class="entry-title">
                                    <h3>Cơ bản</h3>
                                    <div class="price">99K<span>/Tháng</span>
                                    </div>
                                </div>
                                <div class="entry-content">
                                    <ul>
                                        <li><strong>50 Fanpage</strong></li>
                                        <li><strong>Không giới hạn TK nhân viên</strong></li>
                                        <li><strong>Tự động ẩn comment chứa SĐT/KEYWORD</strong> </li>
                                        <li><strong>Lên lịch/đăng bài viết lên nhiều Fanpage cùng lúc</strong> </li>
                                        <li><strong>Tự động trả lời comment</strong></li>
                                        <li><strong>Tự động inbox cho khách hàng comment</strong></li>
                                        <li><strong>Quản lý đơn hàng</strong></li>
                                        <li><strong>Quản lý tồn kho</strong></li>
                                        <li><strong>Thống kê kết quả bán hàng</strong></li>
                                        <li>
                                            <a href="{{ route('info.prices') }}">Xem chi tiết</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="btn">
                                    <a href="{{ $loginUrl }}">Đăng ký ngay</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End Main-->

        <div id="statistics">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-md-offset-1 col-sm-4  col-xs-12">
                        <h4>1000+</h4>
                        <p>Fanpage</p>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <h4>100+</h4>
                        <p>Đơn hàng được hoàn thành<br> trên hệ thống mỗi ngày</p>
                    </div>
                    <div class="col-md-3 col-md-offset-1 col-sm-4 col-xs-12">
                        <h4>0 ĐỒNG</h4>
                        <p>Miễn phí trọn đời quản lý<br> các fanpage</p>
                    </div>
                </div>
            </div>
        </div>
        <!--End statistics-->

        <div id="register">
            <div class="container">
                <div class="row">
                    <div class="col-md-5 col-md-offset-2 col-sm-12 col-xs-12">
                        <h2>Sale is simple</h2>
                    </div>
                    <div id="btn-register" class="col-md-3 col-sm-12 col-xs-12">
                        <a href="{{ $loginUrl }}" class="btn-1 btn-facebook">
                            <i class="fa fa-facebook facebook-custom"></i>
                            <button class="btnRegis" type="button">
                                Đăng ký ngay
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!--End register-->

        <div id="footer">
            <div class="logoFooter">
                <img src="{{ asset('images/logo_vnp.png') }}">
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                        <span class="rowInfo">
                            Công ty Cổ phần Vật Giá Việt Nam. Số GCNDT: 011032001615, cấp ngày 21/06/2012, nơi cấp: UBND thành phố Hà Nội.
                        </span>
                        <span class="rowInfo">
                            Trụ sở chính: Số 102, phố Thái Thịnh, phường Trung Liệt, quận Đống Đa, Hà Nội
                        </span>
                        <span class="rowInfo">Chi nhánh Hồ Chí Minh: Đường Lữ Gia, Phường 15, Quận 11, Hồ Chí Minh</span>
                        <span class="rowInfo">Hotline: (024)73 050 105 - 0169 2250 821</span>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.min.js') }}"></script>
        <script type="text/javascript">
            $("a[href^='#']").click(function(e) {
                e.preventDefault();

                var position = $($(this).attr("href")).offset().top;

                $("body, html").animate({
                    scrollTop: position
                } ,1000 );
            });
        </script>
        <script type="text/javascript" src="{{ url('/client/js/login.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#article-left-one").mouseenter(function () {
                    $("#artice-slide").addClass("img-display").attr("src", "{{ asset('home_client/images/anh-1.png') }}");
                    $(".blur-images").addClass("img-blur").attr("src", "{{ asset('home_client/images/anh-1.png') }}");
                });
                $("#article-left-one").mouseleave(function () {
                    $("#artice-slide").removeClass("img-display");
                });
                $("#article-left-two").mouseenter(function () {
                    $("#artice-slide").addClass("img-display").attr("src", "{{ asset('home_client/images/2.jpg') }}");
                    $(".blur-images").addClass("img-blur").attr("src", "{{ asset('home_client/images/2.jpg') }}");
                });
                $("#article-left-two").mouseleave(function () {
                    $("#artice-slide").removeClass("img-display");
                });
                $("#article-left-three").mouseenter(function () {
                    $("#artice-slide").addClass("img-display").attr("src", "{{ asset('home_client/images/3.jpg') }}");
                    $(".blur-images").addClass("img-blur").attr("src", "{{ asset('home_client/images/3.jpg') }}");
                });
                $("#article-left-three").mouseleave(function () {
                    $("#description-block-three").stop().removeClass("description-display");
                    $("#artice-slide").removeClass("img-display");
                });

                $("#article-right-one").mouseenter(function () {
                    $("#artice-slide").addClass("img-display").attr("src", "{{ asset('home_client/images/4.jpg') }}");
                    $(".blur-images").addClass("img-blur").attr("src", "{{ asset('home_client/images/4.jpg') }}");
                });
                $("#article-right-one").mouseleave(function () {
                    $("#artice-slide").removeClass("img-display");
                });
                $("#article-right-two").mouseenter(function () {
                    $("#artice-slide").addClass("img-display").attr("src", "{{ asset('home_client/images/5.jpg') }}");
                    $(".blur-images").addClass("img-blur").attr("src", "{{ asset('home_client/images/5.jpg') }}");
                });
                $("#article-right-two").mouseleave(function () {
                    $("#artice-slide").removeClass("img-display");
                });
                $("#article-right-three").mouseenter(function () {
                    $("#artice-slide").addClass("img-display").attr("src", "{{ asset('home_client/images/6.jpg') }}");
                    $(".blur-images").addClass("img-blur").attr("src", "{{ asset('home_client/images/6.jpg') }}");
                });
                $("#article-right-three").mouseleave(function () {
                    $("#artice-slide").removeClass("img-display");
                });
            });
        </script>
        <!-- vchat -->
        <script lang="javascript">(function() {var pname = ( (document.title !='')? document.title : ((document.querySelector('h1') != null)? document.querySelector('h1').innerHTML : '') );var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async=1; ga.src = '//live.vnpgroup.net/js/web_client_box.php?hash=f2e746dc7471f4784de4c16042d19bf8&data=eyJzc29faWQiOjUzNDEyMjUsImhhc2giOiJmNjk0MDY5MDJlYTQwMjdkOGEwZjc2OGMxMjA2NzYzYiJ9&pname='+pname;var s = document.getElementsByTagName('script');s[0].parentNode.insertBefore(ga, s[0]);})();</script>
        <!-- end vchat -->
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-115626442-1"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'UA-115626442-1');
        </script>

    </body>
</html>