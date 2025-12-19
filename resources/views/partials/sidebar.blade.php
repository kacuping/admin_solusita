<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ url('/') }}">Admin Solusita</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ url('/') }}">AS</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Utama</li>
            <li class="{{ Request::is('/') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('/') }}"><i class="fas fa-fire"></i> <span>Dashboard</span></a>
            </li>
            <li class="{{ Request::is('transaksi') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('transaksi.index') }}"><i class="fas fa-receipt"></i>
                    <span>Transaksi</span></a>
            </li>

            <li class="menu-header">Management Layanan</li>
            <li class="dropdown {{ Request::is('services/*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-tools"></i>
                    <span>Management Layanan</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('services/kategori') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('services.kategori.index') }}">Kategori</a></li>
                    <li class="{{ Request::is('services/layanan') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('services.layanan.index') }}">Layanan</a></li>
                </ul>
            </li>
            <li class="{{ Request::is('rating') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('rating.index') }}"><i class="fas fa-star"></i> <span>Rating</span></a>
            </li>
            <li class="{{ Request::is('payment-methods*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('payment-methods.index') }}"><i class="fas fa-money-bill-wave"></i> <span>Pembayaran</span></a>
            </li>

            <li class="menu-header">Management</li>
            <li class="dropdown {{ Request::is('users/*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-users"></i>
                    <span>Management User</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('users/admin') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('users.admin.index') }}">Admin</a></li>
                    <li class="{{ Request::is('users/pelanggan') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('users.pelanggan.index') }}">Pelanggan</a></li>
                    <li class="{{ Request::is('users/cleaner') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('users.cleaner.index') }}">Cleaner</a></li>
                </ul>
            </li>
            <li class="{{ Request::is('settings') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('settings.index') }}"><i class="fas fa-cogs"></i> <span>Setting</span></a>
            </li>
        </ul>

        {{-- <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
      <a href="#" class="btn btn-primary btn-lg btn-block btn-icon-split">
        <i class="fas fa-rocket"></i> Documentation
      </a>
    </div> --}}
    </aside>
</div>
