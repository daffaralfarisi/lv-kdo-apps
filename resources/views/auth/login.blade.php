<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk · {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @include('partials.vendor-css')
    @include('partials.kdo-ui-css')
    <style>
        body.kdo-login-page{min-height:100vh;margin:0;display:flex;align-items:center;justify-content:center;padding:32px 16px;position:relative;overflow-x:hidden;background:linear-gradient(115deg,#f3f8fe 0%,#f1eefb 48%,#eef3fc 100%)}
        .kdo-login-bg{position:fixed;inset:0;overflow:hidden;pointer-events:none;z-index:0}
        .kdo-login-bg span{position:absolute;border-radius:50%}
        .kdo-login-bg .b1{width:640px;height:640px;left:-200px;bottom:-330px;background:linear-gradient(160deg,#e4eefb,#d8e7f8)}
        .kdo-login-bg .b2{width:760px;height:760px;right:-260px;top:10%;background:linear-gradient(200deg,#e8eafb,#dee5f8)}
        .kdo-login-bg .b3{width:600px;height:600px;left:38%;top:-320px;background:radial-gradient(circle,rgba(226,214,248,.95) 0%,rgba(226,214,248,0) 70%)}
        .kdo-login-card{position:relative;z-index:1;display:flex;width:100%;max-width:900px;min-height:480px;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 24px 48px rgba(15,23,42,.10)}
        .kdo-login-brand{flex:1 1 50%;display:flex;align-items:center;justify-content:center;background:#f3f4f6;padding:40px}
        .kdo-login-brand img{width:100%;max-width:250px;height:auto}
        .kdo-login-panel{flex:1 1 50%;display:flex;flex-direction:column;justify-content:center;padding:44px 40px}
        .kdo-login-logo-mobile{display:none;width:72px;height:72px;margin:0 auto 14px}
        .kdo-login-sparkle{display:block;width:20px;height:20px;margin:0 auto 14px;color:#0f172a}
        .kdo-login-judul{font-size:22px;font-weight:800;line-height:1.3;color:#0f172a;text-align:center;margin:0 0 6px}
        .kdo-login-sub{font-size:14px;color:#667085;text-align:center;margin:0 0 28px}
        .kdo-login-grup{margin-bottom:16px}
        .kdo-login-label{display:block;font-size:12.5px;font-weight:700;color:#344054;margin-bottom:6px}
        .kdo-input{display:flex;align-items:center;gap:10px;height:48px;padding:0 6px 0 14px;background:#f8fafc;border:1.5px solid #e3e8ee;border-radius:12px;transition:border-color .15s,box-shadow .15s,background .15s}
        .kdo-input:hover{border-color:#ccd7e3}
        .kdo-input:focus-within{background:#fff;border-color:#7ea9d8;box-shadow:0 0 0 3px rgba(40,123,181,.13)}
        .kdo-input>i{flex:none;width:16px;text-align:center;font-size:14px;color:#9aa3b2;transition:color .15s}
        .kdo-input:focus-within>i{color:#287bb5}
        .kdo-input input{flex:1;min-width:0;height:100%;border:0;outline:0;background:transparent;font-size:14px;font-weight:600;color:#3d4657}
        .kdo-input input::placeholder{color:#a8b0bd;font-weight:500}
        .kdo-input-mata{flex:none;width:34px;height:34px;border:0;border-radius:9px;background:transparent;color:#9aa3b2;cursor:pointer;transition:color .15s,background .15s}
        .kdo-input-mata:hover{color:#287bb5;background:#eef4fb}
        .kdo-input-mata:focus{outline:0}.kdo-input-mata:focus-visible{box-shadow:0 0 0 3px rgba(40,123,181,.13)}
        .kdo-input.is-invalid{border-color:#d92d20;background:#fff}
        .kdo-input.is-invalid:focus-within{box-shadow:0 0 0 3px rgba(217,45,32,.12)}
        .kdo-input.is-invalid>i{color:#d92d20}
        .kdo-login-error{display:none;align-items:center;gap:6px;margin-top:6px;font-size:12px;font-weight:600;color:#b42318}
        .kdo-login-error.tampil{display:flex}
        .kdo-login-ingat{font-size:12.5px;font-weight:600;color:#475467;cursor:pointer}
        .kdo-login-btn{width:100%;height:48px;border:0;border-radius:12px;background:#287bb5;color:#fff;font-size:14.5px;font-weight:800;letter-spacing:.2px;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 8px 18px rgba(40,123,181,.28);cursor:pointer;transition:background .15s,box-shadow .15s,transform .1s}
        .kdo-login-btn:hover{background:#20699b}
        .kdo-login-btn:active{transform:translateY(1px)}
        .kdo-login-btn:disabled{background:#9cc3e6;box-shadow:none;cursor:progress}
        .kdo-login-btn:focus{outline:0}.kdo-login-btn:focus-visible{box-shadow:0 0 0 3px rgba(40,123,181,.3)}
        .kdo-note.bahaya{background:#fef3f2;border-color:#fecdca;color:#b42318}
        .kdo-note.bahaya>i{color:#d92d20}
        .kdo-login-alert{display:none;margin-bottom:16px}
        @media (max-width:767.98px){
            .kdo-login-brand{display:none}
            .kdo-login-logo-mobile{display:block}
            .kdo-login-panel{padding:32px 22px}
            .kdo-login-judul{font-size:19px}
        }
    </style>
</head>
<body class="kdo-login-page">
<div class="kdo-login-bg" aria-hidden="true"><span class="b1"></span><span class="b2"></span><span class="b3"></span></div>

<main class="kdo-login-card">
    <section class="kdo-login-brand">
        <img src="{{ asset('favicon.ico') }}" alt="Logo BPAD — Badan Pengelolaan Aset Daerah">
    </section>

    <section class="kdo-login-panel">
        <img src="{{ asset('favicon.ico') }}" alt="" class="kdo-login-logo-mobile">
        <h1 class="kdo-login-judul">Selamat datang di aplikasi Monitoring KDO</h1>
        <p class="kdo-login-sub">Kendaraan Dinas Operasional</p>

        <div class="kdo-note bahaya kdo-login-alert" id="loginAlert" role="alert"><i class="fas fa-exclamation-circle"></i><div id="loginAlertTeks"></div></div>

        <form id="formLogin" novalidate>
            <div class="kdo-login-grup">
                <label class="kdo-login-label" for="kdo_email">Email</label>
                <div class="kdo-input" data-field="kdo_email">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="kdo_email" name="kdo_email" placeholder="Masukkan email Anda" autocomplete="username" required autofocus>
                </div>
                <div class="kdo-login-error" data-error-for="kdo_email"></div>
            </div>

            <div class="kdo-login-grup">
                <label class="kdo-login-label" for="kdo_password">Password</label>
                <div class="kdo-input" data-field="kdo_password">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="kdo_password" name="kdo_password" placeholder="Masukkan password Anda" autocomplete="current-password" required>
                    <button type="button" class="kdo-input-mata" id="tombolMata" aria-label="Tampilkan password" aria-pressed="false"><i class="far fa-eye"></i></button>
                </div>
                <div class="kdo-login-error" data-error-for="kdo_password"></div>
            </div>

            <div class="custom-control custom-checkbox mb-4">
                <input type="checkbox" class="custom-control-input" id="ingat_saya">
                <label class="custom-control-label kdo-login-ingat" for="ingat_saya">Ingat saya</label>
            </div>

            <button type="submit" class="kdo-login-btn" id="tombolMasuk"><span>Masuk</span></button>
        </form>
    </section>
</main>

@include('partials.vendor-js')
@include('partials.kdo-ui-js')
<script>
(function () {
    window.KDO_HALAMAN_LOGIN = true;
    KDO.siapkanAjax();

    var $form = $('#formLogin');
    var $email = $('#kdo_email');
    var $password = $('#kdo_password');
    var $tombol = $('#tombolMasuk');
    var $alert = $('#loginAlert');

    function tampilkanError(nama, pesan) {
        $form.find('[data-field="' + nama + '"]').addClass('is-invalid');
        $form.find('[data-error-for="' + nama + '"]').html('<i class="fas fa-exclamation-circle"></i>' + KDO.esc(pesan)).addClass('tampil');
    }
    function bersihkanError(nama) {
        $form.find('[data-field="' + nama + '"]').removeClass('is-invalid');
        $form.find('[data-error-for="' + nama + '"]').removeClass('tampil').empty();
    }
    function aturTombol(memproses) {
        $tombol.prop('disabled', memproses).html(memproses
            ? '<i class="fas fa-circle-notch fa-spin"></i><span>Memproses...</span>'
            : '<span>Masuk</span>');
    }

    $email.add($password).on('input', function () { bersihkanError(this.name); $alert.hide(); });

    $('#tombolMata').on('click', function () {
        var tampil = $password.attr('type') === 'password';
        $password.attr('type', tampil ? 'text' : 'password');
        $(this).attr({ 'aria-pressed': String(tampil), 'aria-label': tampil ? 'Sembunyikan password' : 'Tampilkan password' })
            .find('i').toggleClass('fa-eye', !tampil).toggleClass('fa-eye-slash', tampil);
        $password.trigger('focus');
    });

    $form.on('submit', function (e) {
        e.preventDefault();
        $alert.hide();
        var email = $email.val().trim();
        var password = $password.val();
        var valid = true;
        if (!email) { tampilkanError('kdo_email', 'Email wajib diisi.'); valid = false; }
        if (!password) { tampilkanError('kdo_password', 'Password wajib diisi.'); valid = false; }
        if (!valid) { $form.find('.is-invalid input').first().trigger('focus'); return; }

        aturTombol(true);
        KDO.api('POST', '/api/v1/auth/login', { kdo_email: email, kdo_password: password, ingat_saya: $('#ingat_saya').is(':checked') })
            .done(function () {
                $tombol.html('<i class="fas fa-check"></i><span>Berhasil, mengalihkan...</span>');
                window.location.href = '/dashboard';
            })
            .fail(function (xhr) {
                var res = xhr.responseJSON || {};
                if (xhr.status === 422 && res.errors) {
                    Object.keys(res.errors).forEach(function (nama) { tampilkanError(nama, res.errors[nama][0]); });
                } else {
                    $('#loginAlertTeks').text(res.message || 'Terjadi kesalahan. Silakan coba lagi.');
                    $alert.css('display', 'flex');
                }
                aturTombol(false);
            });
    });
})();
</script>
</body>
</html>
