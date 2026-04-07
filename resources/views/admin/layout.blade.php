<!DOCTYPE html>
<html lang="en">
<head>
   <title>Admin Dashboard</title>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link rel="shortcut icon" href="{{ asset('css-admin/images/favicon.png') }}" type="image/x-icon">
   <link href="https://fonts.googleapis.com/css?family=Ubuntu:400,500,700" rel="stylesheet">
   <link rel="stylesheet" type="text/css" href="{{ asset('css-admin/icon/themify-icons/themify-icons.css') }}">
   <link rel="stylesheet" type="text/css" href="{{ asset('css-admin/icon/icofont/css/icofont.css') }}">
   <link rel="stylesheet" type="text/css" href="{{ asset('css-admin/icon/simple-line-icons/css/simple-line-icons.css') }}">
   <link rel="stylesheet" type="text/css" href="{{ asset('css-admin/plugins/bootstrap/css/bootstrap.min.css') }}">
   <link rel="stylesheet" type="text/css" href="{{ asset('css-admin/css/main.css') }}">
   <link rel="stylesheet" type="text/css" href="{{ asset('css-admin/css/responsive.css') }}">
   <style>
      /* Sidebar Toggle Styles */
      .sidebar-toggle { display: block; float: left; padding: 15px; cursor: pointer; }
      .sidebar-toggle:before { content: "\2630"; font-size: 20px; }
      
      /* Prevent body scroll when sidebar is open */
      body.sidebar-open { overflow: hidden; }
      
      @media (min-width: 769px) {
         body.sidebar-mini .main-sidebar { margin-left: 0; }
         body.sidebar-mini.sidebar-collapse .main-sidebar { margin-left: -230px; }
         body.sidebar-mini.sidebar-collapse .content-wrapper { margin-left: 0; }
      }
      
      @media (max-width: 768px) {
         .main-sidebar { position: fixed; left: -230px; top: 0; height: 100%; z-index: 999; transition: left 0.3s; background: #fff; width: 230px; box-shadow: 2px 0 5px rgba(0,0,0,0.1); overflow-y: auto; -webkit-overflow-scrolling: touch; padding-top: 70px; }
         body.sidebar-open .main-sidebar { left: 0; }
         body.sidebar-open .content-wrapper { transform: translateX(230px); transition: transform 0.3s; }
         body.sidebar-open .main-header-top { transform: translateX(230px); transition: transform 0.3s; }
         .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 998; }
         body.sidebar-open .sidebar-overlay { display: block; }
         .wrapper { overflow-x: hidden !important; overflow-y: auto !important; height: 100vh !important; }
         .content-wrapper { padding-top: 70px !important; margin-left: 0 !important; width: 100vw !important; max-width: 100% !important; padding-left: 15px !important; padding-right: 15px !important; box-sizing: border-box !important; position: relative !important; left: 0 !important; height: auto !important; overflow: visible !important; transition: transform 0.3s; }
         .main-header-top { transition: transform 0.3s; }
         .container-fluid { padding-left: 15px !important; padding-right: 15px !important; margin: 0 !important; max-width: 100% !important; }
         .main-header-top .logo { padding: 10px; }
         .main-header-top .logo img { max-width: 120px; }
         .navbar-custom-menu .btn { margin: 5px 10px !important; font-size: 12px; }
         .content-wrapper { padding: 10px 0; }
         .container-fluid { padding: 0 10px; }
         .card { margin-bottom: 15px; }
         .card-block { padding: 15px !important; }
         .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
         .table { font-size: 12px; }
         .table td, .table th { padding: 8px 5px; }
         .btn { font-size: 12px; padding: 5px 10px; }
         .form-control { font-size: 14px; }
         .modal-dialog { margin: 10px; }
         .modal-content { width: 95% !important; max-width: 95% !important; margin: 10px auto !important; flex-direction: column !important; }
         .modal-left, .modal-right { flex: none !important; width: 100% !important; }
         .hero-section { flex-direction: column !important; min-height: auto !important; margin: 10px !important; padding: 20px !important; }
         .hero-content { max-width: 100% !important; padding: 20px !important; }
         .hero-image { position: relative !important; right: 0 !important; transform: none !important; width: 100% !important; height: 200px !important; margin-top: 20px !important; }
         .hero-title { font-size: 24px !important; }
         .edit-btn { padding: 8px 15px !important; font-size: 12px !important; }
         div[style*="grid-template-columns"] { grid-template-columns: 1fr !important; }
      }
      @media (max-width: 480px) {
         .main-header-top .logo img { max-width: 100px; }
         .navbar-custom-menu .btn { padding: 4px 8px; font-size: 11px; }
         .sidebar-menu li a { font-size: 13px; }
         h1, .h1 { font-size: 20px !important; }
         h2, .h2 { font-size: 18px !important; }
         h3, .h3 { font-size: 16px !important; }
         .card-block { padding: 10px !important; }
         .table { font-size: 11px; }
         .btn { font-size: 11px; padding: 4px 8px; }
         .form-group label { font-size: 12px; }
         .form-control { font-size: 13px; padding: 8px; }
         .hero-title { font-size: 20px !important; }
         .hero-description { font-size: 13px !important; }
      }
   </style>
</head>

<body class="sidebar-mini fixed">
   <div class="loader-bg">
      <div class="loader-bar"></div>
   </div>
   <div class="wrapper">
      <!-- Navbar-->
      <header class="main-header-top hidden-print">
         <a href="{{ url('/admin') }}" class="logo" style="display: flex; align-items: center; padding: 15px 20px; text-decoration: none;">
            <span style="color: #fff; font-size: 14px; font-weight: bold; letter-spacing: 0.5px;">ADMIN TASTY FOOD</span>
         </a>
         <nav class="navbar navbar-static-top">
            <a href="#!" data-toggle="offcanvas" class="sidebar-toggle"></a>
            <div class="navbar-custom-menu f-right">
               <a href="{{ route('admin.logout') }}" class="btn btn-sm btn-danger" style="margin-top: 10px; margin-right: 15px;">Logout</a>
            </div>
         </nav>
      </header>
      
      <!-- Side-Nav-->
      <aside class="main-sidebar hidden-print ">
         <section class="sidebar" id="sidebar-scroll">
            <ul class="sidebar-menu">
                <li class="nav-level">--- Database Tables</li>
                <li class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">
                    <a class="waves-effect waves-dark" href="{{ route('admin.index') }}">
                        <i class="icon-speedometer"></i><span> Dashboard</span>
                    </a>                
                </li>
             
                <li class="{{ request()->routeIs('admin.contents') ? 'active' : '' }}">
                    <a class="waves-effect waves-dark" href="{{ route('admin.contents') }}">
                        <i class="icon-docs"></i><span> Contents</span>
                    </a>                
                </li>
                <li class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <a class="waves-effect waves-dark" href="{{ route('admin.settings') }}">
                        <i class="icon-settings"></i><span> Settings</span>
                    </a>                
                </li>
                <li class="{{ request()->routeIs('admin.news') ? 'active' : '' }}">
                    <a class="waves-effect waves-dark" href="{{ route('admin.news') }}">
                        <i class="icon-book-open"></i><span> News</span>
                    </a>                
                </li>
                <li class="{{ request()->routeIs('admin.galleries') ? 'active' : '' }}">
                    <a class="waves-effect waves-dark" href="{{ route('admin.galleries') }}">
                        <i class="icon-picture"></i><span> Galleries</span>
                    </a>                
                </li>
                <li class="{{ request()->routeIs('admin.contacts') ? 'active' : '' }}">
                    <a class="waves-effect waves-dark" href="{{ route('admin.contacts') }}">
                        <i class="icon-envelope"></i><span> Contacts</span>
                    </a>                
                </li>
                <li class="{{ request()->routeIs('admin.tentang') ? 'active' : '' }}">
                    <a class="waves-effect waves-dark" href="{{ route('admin.tentang') }}">
                        <i class="icon-info"></i><span> Tentang</span>
                    </a>                
                </li>
                <li>
                    <a class="waves-effect waves-dark" href="/" target="website_preview" onclick="window.open('/', 'website_preview'); return false;">
                        <i class="icon-globe"></i><span> Lihat Website</span>
                    </a>                
                </li>
            </ul>
         </section>
      </aside>
      
      <div class="content-wrapper">
         <div class="container-fluid">
            @yield('content')
         </div>
      </div>
   </div>
   
   <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

   <script src="{{ asset('css-admin/plugins/Jquery/dist/jquery.min.js') }}"></script>
   <script src="{{ asset('css-admin/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
   <script src="{{ asset('css-admin/plugins/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
   <script src="{{ asset('css-admin/js/main.min.js') }}"></script>
   <script src="{{ asset('css-admin/js/menu.min.js') }}"></script>
   <script>
      function toggleSidebar() {
         document.body.classList.toggle('sidebar-open');
      }
      
      document.addEventListener('DOMContentLoaded', function() {
         const sidebarToggle = document.querySelector('.sidebar-toggle');
         if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function(e) {
               e.preventDefault();
               toggleSidebar();
            });
         }
         
         // Auto hide alerts after 5 seconds
         setTimeout(function() {
            $('.alert').fadeOut('slow');
         }, 5000);
      });
   </script>
   @yield('scripts')
</body>
</html>