
@auth
@php
    $user = Auth::user();
    $dashboardRoute = $user->role === 'admin' ? route('admin.dashboard') : route('presale.dashboard');
@endphp

<div class="sidebar bg-dark text-white vh-100 p-3">
    <h4 class="text-white mb-4"></h4>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-white {{ request()->is('customers*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                <i class="bi bi-people me-2"></i> Customers
            </a>
            <ul class="nav flex-column ms-4">
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->is('projects*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                        <i class="bi bi-folder me-2"></i> Project
                    </a>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('versions*') ? 'active' : '' }}"  style="padding-left: 30px;" href="{{ route('versions.index') }}">
                            <i class="bi bi-lightbulb me-2"></i> Version
                        </a>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->is('solutions*') ? 'active' : '' }}" href="{{ route('solutions.index') }}">
                        <i class="bi bi-lightbulb me-2"></i> Solution
                    </a>
                </li>
            </ul>
        </li>

<li class="nav-item">
    <a href="{{ route('vm-mapping.index') }}" class="nav-link">
        <p>VM Mapping</p>
    </a>
</li>



        
        <li class="nav-item">
            <a class="nav-link text-white {{ request()->is('product*') ? 'active' : '' }}" href="#">
                <i class="bi bi-box-seam" ></i> Product
            </a>
            <ul class="nav flex-column ms-4">
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->is('categories*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                        <i></i> Category
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->is('services*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                        <i></i> Service
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->is('ecs-flavours*') ? 'active' : '' }}" href="{{ route('ecs-flavours.index') }}">
                        <i></i> ECS Flavour
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->is('network-mappings*') ? 'active' : '' }}" href="{{ route('network-mappings.index') }}">
                        <i></i> Network Mapping
                    </a>
                </li>

                   <a href="{{ route('logout') }}" class="sidebar-link text-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
            </ul>
        </li>
    </ul>
</div>
@endauth