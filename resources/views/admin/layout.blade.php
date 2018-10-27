<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('/images/chotsale_favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex"/>
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('admins/plugins/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('admins/dist/css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,700,700i" rel="stylesheet">
    <script src="{{ asset('admins/plugins/jquery/jquery.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('admins/dist/css/extra.css') }}">
    @yield('styleHead')
</head>
<body class="hold-transition sidebar-mini sidebar-collapse">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="https://chotsale.com.vn/blog" class="brand-link" title="Đi đến Chốt Sale Blog" target="_blank">
                <img src="{{asset('admins/dist/img/AdminLTELogo.png')}}" alt="Chốt Sale Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Chốt Sale</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="/images/demo-product.png" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{Auth::guard('admin')->user()->email}}</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item has-treeview menu-open">
                            <a href="{{ asset('admin/dashboard') }}" class="{{ Request::is('admin/dashboard') ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon fa fa-dashboard"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ asset('admin/conversations') }}" class="{{ Request::is('admin/conversations') ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon fa fa-comments"></i>
                                <p>
                                    Hội thoại
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="{{ Request::is('admin/accounts/*') ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon fa fa-users"></i>
                                <p>
                                    Người dùng
                                    <i class="fa fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.accounts') }}" class="{{ Request::is('admin/accounts/all') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-th"></i>
                                        <p>Thống kê người dùng</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.accounts.activitiesDays') }}" class="{{ Request::is('admin/accounts/activities-every-day') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-book"></i>
                                        <p>Thống kê hoạt động</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="{{ Request::is('admin/blog/*') ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon fa fa-tree"></i>
                                <p>
                                    Blog
                                    <i class="fa fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('blog.all.category') }}" class="{{ Request::is('admin/blog/category') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-th"></i>
                                        <p>Quản lý danh mục</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('blog.add.category') }}" class="{{ Request::is('admin/blog/category/create') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-book"></i>
                                        <p>Thêm danh mục</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                        <a href="{{ route('blog.all.post') }}" class="{{ Request::is('admin/blog/post') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-file"></i>
                                        <p>Quản lý bài viết</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('blog.add.post') }}" class="{{ Request::is('admin/blog/post/create') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-edit"></i>
                                        <p>Thêm bài viết</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="{{ Request::is('admin/redirect-links/*') ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon fa fa-link"></i>
                                <p>
                                    Chuyển hướng
                                    <i class="fa fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('redirect302') }}" class="{{ Request::is('admin/redirect-links/get') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-th"></i>
                                        <p>Danh sách chuyển hướng</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('getCreateRedirect302') }}" class="{{ Request::is('admin/redirect-links/create') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-plus"></i>
                                        <p>Thêm chuyển hướng</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="{{ Request::is('admin/package/*') ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon fa fa-archive"></i>
                                <p>
                                    Gói tài khoản
                                    <i class="fa fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('package.index') }}" class="{{ Request::is('admin/package') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-crop"></i>
                                        <p>Danh sách các gói</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('package.create') }}" class="{{ Request::is('admin/package/create') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-plus"></i>
                                        <p>Thêm gói</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="{{ Request::is('admin/feature/*') ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon fa fa-archive"></i>
                                <p>
                                    Tính năng
                                    <i class="fa fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('feature.index') }}" class="{{ Request::is('admin/feature') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-crop"></i>
                                        <p>Danh sách các tính năng</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('feature.create') }}" class="{{ Request::is('admin/feature/create') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-plus"></i>
                                        <p>Thêm tính năng</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="{{ Request::is('admin/payment/*') ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon fa fa-shopping-bag"></i>
                                <p>
                                    Hóa đơn
                                    <i class="fa fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('payment.index') }}" class="{{ Request::is('admin/payment') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-crop"></i>
                                        <p>Danh sách hóa đơn</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('payment.create') }}" class="{{ Request::is('admin/payment/create') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-plus"></i>
                                        <p>Tạo hóa đơn</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="{{ Request::is('admin/import/*') ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon fa fa-upload"></i>
                                <p>
                                    Nhập liệu
                                    <i class="fa fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.getImport.uid') }}" class="{{ Request::is('admin/import/uid') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa-user"></i>
                                        <p>Nhập UID</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.uid.all') }}" class="{{ Request::is('admin/import/uid/all') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-th"></i>
                                        <p>Thống kê UID</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.scan.phone') }}" class="{{ Request::is('admin/import/scan/phone') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-phone"></i>
                                        <p>Quét số ĐT</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.phone.all') }}" class="{{ Request::is('admin/import/phone/all') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-bars"></i>
                                        <p>Thống kê số ĐT</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.getImport.phone') }}" class="{{ Request::is('admin/import/phone') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-archive"></i>
                                        <p>Nhập UID-Phone</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="{{ Request::is('admin/extractor/*') ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon fa fa-shopping-bag"></i>
                                <p>
                                    Admin Token
                                    <i class="fa fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.extractor.createTokenLord') }}" class="{{ Request::is('admin/extractor/createTokenLord') ? 'nav-link active' : 'nav-link' }}">
                                        <i class="nav-icon fa fa-archive"></i>
                                        <p>Nhập Admin Token</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <div class="content-wrapper">
            <!-- Main content -->
            @yield('content')
        </div>

        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            <div class="float-right d-sm-block-down">
                vChat Team
            </div>
            <!-- Default to the left -->
            <strong>Copyright &copy; 2018 <a href="https://chotsale.com.vn/">Chốt Sale</a>.</strong> All rights reserved.
        </footer>
    </div>
    <script src="{{ asset('plugins/notifyjs/notify.min.js') }}"></script>
    <script src="{{ asset('admins/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @yield('scripts')
    <script src="{{ asset('admins/dist/js/adminlte.js') }}"></script>
</body>

</html>