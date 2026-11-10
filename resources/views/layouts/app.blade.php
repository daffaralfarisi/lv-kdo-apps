<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('judul') · {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @include('partials.vendor-css')
    @include('partials.kdo-ui-css')
    <script>
        // Terapkan status sidebar ciut sebelum render agar tidak berkedip.
        try { if (localStorage.getItem('kdo.sidebar.ciut') === '1') { document.documentElement.classList.add('kdo-ciut'); } } catch (e) {}
    </script>
</head>
<body>
@php
    $menu = [
        ['Utama', [['dashboard', 'Dashboard Monitoring', 'fa-tachometer-alt', false]]],
        ['Master Data', [
            ['kendaraan.index', 'Kendaraan Dinas', 'fa-car', false],
            ['pegawai.index', 'Pegawai', 'fa-id-badge', false],
            ['perangkat-daerah.index', 'Perangkat Daerah', 'fa-building', false],
        ]],
        ['Service', [['penjadwalan-service.index', 'Penjadwalan Service', 'fa-tools', false]]],
        ['Pengaturan', [['users.index', 'Master User', 'fa-users-cog', true]]],
    ];
@endphp
<aside class="kdo-sidebar" id="kdoSidebar">
    <div class="kdo-brand">
        <img src="{{ asset('favicon.ico') }}" alt="Logo BPAD" class="kdo-brand-logo" width="80" height="80">
        <div class="kdo-brand-teks">
            <div class="kdo-brand-nama">KDO Apps</div>
            <small>Kendaraan Dinas Operasional</small>
        </div>
    </div>
    <nav class="kdo-menu">
        @foreach ($menu as [$grup, $items])
            @php $items = array_filter($items, fn ($item) => ! $item[3] || auth()->user()->isAdmin()); @endphp
            @if ($items)
                <div class="kdo-menu-label">{{ $grup }}</div>
                @foreach ($items as [$route, $label, $ikon])
                    <a href="{{ route($route) }}" class="{{ request()->routeIs($route) ? 'aktif' : '' }}" title="{{ $label }}" @if (request()->routeIs($route)) aria-current="page" @endif><i class="fas {{ $ikon }}"></i><span class="kdo-menu-teks">{{ $label }}</span></a>
                @endforeach
            @endif
        @endforeach
    </nav>
</aside>
<button type="button" class="kdo-sb-toggle" id="kdoSbToggle" aria-controls="kdoSidebar" aria-expanded="true" aria-label="Ciutkan menu"><i class="fas fa-chevron-left"></i></button>
<div class="kdo-sb-backdrop" id="kdoSbBackdrop"></div>
<div class="kdo-main">
    <header class="kdo-topbar">
        <button type="button" class="kdo-btn d-lg-none" id="kdoToggleMenu" aria-label="Menu"><i class="fas fa-bars"></i></button>
        <div>
            <h1 class="kdo-page-title">@yield('judul')</h1>
            <div class="kdo-page-sub">@yield('subjudul')</div>
        </div>
        <div class="kdo-user">
            <div class="text-right">
                <div class="kdo-user-name">{{ auth()->user()->kdo_nama_user }}</div>
                <span class="kdo-chip {{ auth()->user()->isAdmin() ? 'kdo-chip-ungu' : '' }}">{{ auth()->user()->kdo_role->label() }}</span>
            </div>
            <button type="button" class="kdo-btn" id="kdoKeluar"><i class="fas fa-sign-out-alt"></i>Keluar</button>
        </div>
    </header>
    <main class="kdo-content">
        @yield('konten')
    </main>
</div>
@stack('modal')
@include('partials.vendor-js')
@include('partials.kdo-ui-js')
<script>
    KDO.siapkanAjax();
    KDO.select.pasangSemua();
    window.KDO_USER_ID = {{ Js::from(auth()->id()) }};
    (function () {
        var html = document.documentElement;
        var $menu = $('.kdo-menu a');
        var $toggle = $('#kdoSbToggle');
        $menu.tooltip({ placement: 'right', container: 'body', trigger: 'hover', boundary: 'window' });
        function sinkron() {
            var ciut = html.classList.contains('kdo-ciut');
            $menu.tooltip(ciut && window.innerWidth >= 992 ? 'enable' : 'disable');
            $toggle.attr({ 'aria-expanded': String(!ciut), 'aria-label': ciut ? 'Perluas menu' : 'Ciutkan menu', 'title': ciut ? 'Perluas menu' : 'Ciutkan menu' });
        }
        $toggle.on('click', function () {
            var ciut = html.classList.toggle('kdo-ciut');
            try { localStorage.setItem('kdo.sidebar.ciut', ciut ? '1' : '0'); } catch (e) {}
            $menu.tooltip('hide');
            sinkron();
            // Lebar konten berubah: sesuaikan ulang kolom DataTables setelah animasi selesai.
            setTimeout(function () { $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust(); }, 320);
        });
        $('#kdoToggleMenu').on('click', function () { html.classList.add('kdo-sb-buka'); });
        $('#kdoSbBackdrop').on('click', function () { html.classList.remove('kdo-sb-buka'); });
        $(document).on('keydown', function (e) { if (e.key === 'Escape') { html.classList.remove('kdo-sb-buka'); } });
        $(window).on('resize', sinkron);
        sinkron();
    })();
    $('#kdoKeluar').on('click', function () {
        KDO.swalModern('info', { icon: 'question', title: 'Keluar dari aplikasi?', showCancelButton: true, confirmButtonText: 'Keluar', reverseButtons: true })
            .then(function (hasil) {
                if (!hasil.isConfirmed) { return; }
                KDO.api('POST', '/api/v1/auth/logout').always(function () { window.location.href = '/login'; });
            });
    });
</script>
@stack('scripts')
</body>
</html>
