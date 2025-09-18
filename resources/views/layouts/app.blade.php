<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <title>timeQuote</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>

        
        
        
        .btn-pink {
            --bs-btn-color: #fff;
            --bs-btn-bg: #FF82E6;
            --bs-btn-border-color: #FF82E6;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #e76ccf;
            --bs-btn-hover-border-color: #e76ccf;
            --bs-btn-focus-shadow-rgb: 231, 108, 207;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #e76ccf;
            --bs-btn-active-border-color: #e76ccf;
            --bs-btn-disabled-color: #fff;
            --bs-btn-disabled-bg: #FF82E6;
            --bs-btn-disabled-border-color: #FF82E6;
        }
    </style>

    <!-- load Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
        }

        #sidebar {
            width: 250px;
            background-color: #1e1e1e;
            color: white;
            min-height: 100vh;
            transition: transform 0.3s ease;
        }

        #sidebar.collapsed {
            transform: translateX(-100%);
            position: absolute;
            z-index: 1000;
        }

        .sidebar-link {
            padding: 12px 20px;
            display: block;
            color: white;
            text-decoration: none;
        }

        .sidebar-link.active,
        .sidebar-link:hover {
            background-color: #FF82E6;
            color: white;
        }

        .sidebar-dropdown {
    position: relative;
}

.sidebar-submenu {
    display: none;
    padding-left: 20px;
    background-color: #2a2a2a;
}

.sidebar-dropdown:hover .sidebar-submenu {
    display: block;
}

.sidebar-submenu .sidebar-link {
    padding: 8px 20px;
    font-size: 0.9rem;
}

        #main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: white;
        }

        .app-bar {
            background-color: rgb(0, 0, 0);
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }

        .hamburger {
            font-size: 24px;
            margin-right: 15px;
            cursor: pointer;
            color: #ffffff;
        }

        @media (max-width: 768px) {
            #sidebar {
                position: absolute;
                height: 100%;
                z-index: 1000;
            }
        }
        select.form-select {
             position: relative;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    padding-right: 2rem;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg width='14' height='10' viewBox='0 0 14 10' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M7 10L0.0718 0.25H13.9282L7 10Z' fill='%23696969'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 0.65rem auto;
    /*appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: none !important;
    padding-right: 2rem;*/
}

        
    </style>
     @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<div id="sidebar">
    <div class="p-3 fw-bold fs-5"></div>
    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->is('presale.dashboard') ? 'active' : '' }}">
        <i class="bi bi-house-door"></i> Dashboard
    </a>
    <!-- Customers with sub-menu -->
<div class="sidebar-dropdown">
    <a href="{{ route('customers.index') }}" class="sidebar-link {{ request()->is('customers*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Customers
    </a>
    <div class="sidebar-submenu">
        <a href="{{ route('projects.index') }}" class="sidebar-link {{ request()->is('projects*') ? 'active' : '' }}">
            <i></i> Project
        </a>
        <a href="{{ route('projects.index', ['project' => 1]) }}" class="sidebar-link {{ request()->is('projects/*/versions*') ? 'active' : '' }}"  style="padding-left: 30px;">
            <i></i> Versions
        </a>
      
            </div>
    
    </div>
     
      <a href="{{ route('solutions.index') }}" class="sidebar-link {{ request()->is('solutions*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Committed Solution
            </a>
   

@auth
       @if(in_array(Auth::user()->role, ['admin', 'product']))

     <!-- Products with sub-menu -->

    <div class="sidebar-dropdown">
        <a href="{{ route('products.index') }}" class="sidebar-link {{ request()->is('product*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Products
        </a>
        <div class="sidebar-submenu">
               
                <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->is('categories*') ? 'active' : '' }}">
                <i></i> Category
                <a href="{{ route('services.index') }}" class="sidebar-link {{ request()->is('services*') ? 'active' : '' }}">
                    <i></i> Service 
                <a href="{{ route('ecs-flavours.index') }}" class="sidebar-link {{ request()->is('ecs-flavours*') ? 'active' : '' }}">
                <i></i> ECS Flavour
                <a href="{{ route('network-mappings.index') }}" class="sidebar-link {{ request()->is('network-mappings*') ? 'active' : '' }}">
                <i></i> Network Mapping
                </a>

                </div>



      <div class="sidebar-dropdown">
           <a href="{{ route('vm-mapping.index') }}" class="sidebar-link {{ request()->is('vm-mapping*') ? 'active' : '' }}">
        <i class="bi bi-server"></i> VM Mapping
    </a>
      <div class="sidebar-submenu">
          <a href="{{ route('flavour.index') }}" class="sidebar-link {{ request()->is('flavour*') ? 'active' : '' }}">
    <i></i> P.Flavour Map
</a>

        </div>

        
    </div>

      @endif
@endauth

@auth
    @if(Auth::user()->role === 'admin')
        <div class="sidebar-dropdown">
            <a href="{{ route('register') }}" class="sidebar-link {{ request()->is('register*') ? 'active' : '' }}">
                <i class="bi bi-person"></i> Users
            </a>
        </div>
    @endif
@endauth


               
  


                   <a href="{{ route('logout') }}" class="sidebar-link text-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
        </div>
</div>

   
 
{{-- Main Content --}}
<div id="main">
    {{-- Top App Bar --}}
    <div class="app-bar">
        <i class="bi bi-list hamburger" onclick="toggleSidebar()"></i>
        <div class="fs-5 text-white fw-normal">timeQuote</div>
    </div>

    <div class="p-4">
        @yield('content')
    </div>
</div>

<!-- Bootstrap Bundle JS (modal, dropdown, collapse, etc.) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
    }
</script>
@stack('scripts')
</body>
</html>




