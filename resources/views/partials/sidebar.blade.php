<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
        @can('read dashboard')
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ url('/dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @endcan

        @can('read categories')
            <li class="nav-item">
                <a class="nav-link collapsed" href="{{ url('/categories/view') }}">
                    <i class="bi bi-tags"></i>
                    <span>Categories</span>
                </a>
            </li>
        @endcan


        @can('read roles')
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ url('/roles/view') }}">
                <i class="bi bi-person-lock"></i>
                <span>User Roles</span>
            </a>
        </li>
        @endcan


        @can('read user')
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ url('/user-management/view') }}">
                <i class="bi bi-people"></i>
                <span>User Managements</span>
            </a>
        </li>
        @endcan


        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#marketingMenu" role="button" aria-expanded="false" aria-controls="marketingMenu">
                <i class="bi bi-megaphone"></i>
                <span>Marketing</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <div class="collapse" id="marketingMenu" data-bs-parent="#sidebar-nav">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">

                    <li>
                        <a href="{{ url('/sms/view') }}" class="nav-link">
                            <i class="bi bi-chat-dots"></i> SMS Sending
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/email/view') }}" class="nav-link">
                            <i class="bi bi-envelope-arrow-up"></i> Email Sending
                        </a>
                    </li>

                    

                </ul>
            </div>
        </li>


        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ url('/settings/view') }}">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
        </li>










    </ul>

</aside><!-- End Sidebar-->