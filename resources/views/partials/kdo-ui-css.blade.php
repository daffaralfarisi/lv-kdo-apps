{{-- Partial gaya UI KDO (REFERENSI_STYLING_FRONTEND.md). Di-include sekali per halaman; @once + id guard. --}}
@once
<style id="kdo-ui-css">
:root{--kdo-navy:#082556;--kdo-primary:#287bb5;--kdo-primary-hover:#20699b;--kdo-text:#3d4657;--kdo-muted:#9aa3b2;--kdo-muted2:#a8b0bd;--kdo-th:#94a3b8;--kdo-line-field:#e3e8ee;--kdo-line-table:#eef1f5;--kdo-line-row:#f2f5f8;--kdo-ring:0 0 0 3px rgba(40,123,181,.13)}
body{font-family:'Nunito',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:var(--kdo-text);background:#f4f6fa;font-size:14px}
a{color:var(--kdo-primary)}a:hover{color:var(--kdo-primary-hover)}
.kdo-navy{color:var(--kdo-navy)}
.kdo-card{background:#fff;border:0;border-radius:14px;box-shadow:0 .125rem .25rem rgba(0,0,0,.075)}
.kdo-page-title{font-size:18px;font-weight:800;color:var(--kdo-navy);margin:0}
.kdo-page-sub{font-size:12.5px;color:var(--kdo-muted)}
/* 1. Filter bar */
.kdo-filter-card{padding:12px 14px;margin-bottom:14px}
.kdo-filter-bar{display:flex;flex-wrap:wrap;align-items:center;gap:9px}
.kdo-field{position:relative;display:inline-flex;align-items:center;gap:7px;height:36px;padding:0 11px;border:1px solid var(--kdo-line-field);border-radius:10px;background:#fff;transition:border-color .15s,box-shadow .15s}
.kdo-field:hover{border-color:#ccd7e3}
.kdo-field:focus-within{border-color:#7ea9d8;box-shadow:var(--kdo-ring)}
.kdo-field>i.kdo-field-icon{font-size:11.5px;color:var(--kdo-muted)}
.kdo-field input,.kdo-field select{border:0;outline:0;background:transparent;font-size:12px;font-weight:600;color:var(--kdo-text);height:34px;padding:0;min-width:0}
.kdo-field input::placeholder{color:var(--kdo-muted);font-weight:600}
.kdo-field input[type=search]{width:190px}
.kdo-field input[readonly]{cursor:pointer;width:170px}
.kdo-field .kdo-field-chevron{font-size:8px;color:#b0b8c4;pointer-events:none}
.kdo-sep{width:1px;height:24px;background:#e8ecf1}
.kdo-btn{display:inline-flex;align-items:center;gap:6px;height:36px;padding:0 13px;border-radius:10px;font-size:11.5px;font-weight:700;border:1px solid var(--kdo-line-field);background:#fff;color:#6b7688;cursor:pointer;white-space:nowrap;transition:all .15s}
.kdo-btn:focus{outline:0}.kdo-btn:focus-visible{box-shadow:var(--kdo-ring)}
.kdo-btn-reset{margin-left:auto}
.kdo-btn-reset:hover{background:#fdecea;border-color:#f5c2c7;color:#b42318}
.kdo-btn-primary{background:var(--kdo-primary);border-color:var(--kdo-primary);color:#fff}
.kdo-btn-primary:hover{background:var(--kdo-primary-hover);border-color:var(--kdo-primary-hover);color:#fff}
.kdo-btn-primary:disabled{opacity:.6;cursor:not-allowed}
/* Dropdown kustom (pengganti <select> di filter) */
.kdo-select-native{position:absolute!important;opacity:0;pointer-events:none;width:1px!important;height:1px!important}
.kdo-select-btn{border:0;background:transparent;font-size:12px;font-weight:600;color:var(--kdo-text);padding:0;display:inline-flex;align-items:center;gap:7px;height:34px;cursor:pointer;max-width:220px}
.kdo-select-btn:focus{outline:0}
.kdo-select-btn span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.kdo-select-panel{position:absolute;top:calc(100% + 10px);left:0;z-index:1040;min-width:180px;max-height:280px;overflow:auto;background:#fff;border-radius:10px;padding:5px;box-shadow:0 12px 30px rgba(15,23,42,.15);display:none}
.kdo-select-panel.show{display:block}
.kdo-select-opt{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:7px 10px;border-radius:7px;font-size:12px;font-weight:600;color:var(--kdo-text);cursor:pointer;white-space:nowrap}
.kdo-select-opt:hover{background:#e6f1f9;color:var(--kdo-navy)}
.kdo-select-opt.aktif{background:var(--kdo-primary);color:#fff}
.kdo-select-opt .fa-check{font-size:10px;visibility:hidden}.kdo-select-opt.aktif .fa-check{visibility:visible}
/* Tema daterangepicker */
.daterangepicker{border:0;border-radius:12px;box-shadow:0 12px 30px rgba(15,23,42,.15);font-family:inherit;font-size:12px}
.daterangepicker:before,.daterangepicker:after{display:none}
.daterangepicker td.active,.daterangepicker td.active:hover,.daterangepicker .ranges li.active{background:var(--kdo-primary);color:#fff}
.daterangepicker td.in-range{background:#e6f1f9;color:var(--kdo-navy)}
.daterangepicker td.start-date,.daterangepicker td.end-date{background:var(--kdo-primary);color:#fff}
.daterangepicker .ranges li{font-size:12px;font-weight:600;border-radius:7px;margin:2px 5px}
.daterangepicker .drp-buttons .btn{border-radius:8px;font-weight:700;font-size:11.5px}
.daterangepicker .drp-buttons .btn-primary{background:var(--kdo-primary);border-color:var(--kdo-primary)}
/* 2. Tabel striped */
.kdo-table-card{padding:6px 14px 12px}
table.kdo-table{font-size:12.5px;width:100%!important;margin-bottom:0!important;border-collapse:collapse!important}
table.kdo-table thead th{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--kdo-th);border-top:0!important;border-bottom:1px solid var(--kdo-line-table)!important;padding:.6rem;white-space:nowrap}
table.kdo-table tbody td{padding:.6rem;vertical-align:middle;border-top:1px solid var(--kdo-line-row);color:var(--kdo-text)}
table.kdo-table tbody tr:nth-of-type(odd){background:#f6f8fb}
table.kdo-table tbody tr:hover{background:#eef4fb}
.kdo-kode{font-weight:700;color:var(--kdo-navy);display:block}
.kdo-sub{font-size:10.5px;color:var(--kdo-muted);display:block;margin-top:2px}
.kdo-kosong{color:var(--kdo-muted2)}
.kdo-nominal{text-align:right;white-space:nowrap}
.dataTables_wrapper .dataTables_info{font-size:12px;color:#6b7688;padding-top:14px!important}
.dataTables_wrapper .dataTables_paginate{padding-top:10px}
.dataTables_wrapper .page-link{font-size:12px;font-weight:600;color:var(--kdo-navy);border-color:var(--kdo-line-field)}
.dataTables_wrapper .page-item.active .page-link{background:var(--kdo-primary);border-color:var(--kdo-primary);color:#fff}
.dataTables_wrapper .page-item:first-child .page-link{border-radius:8px 0 0 8px}.dataTables_wrapper .page-item:last-child .page-link{border-radius:0 8px 8px 0}
.dataTables_processing{border:0!important;border-radius:10px;box-shadow:0 12px 30px rgba(15,23,42,.15);font-size:12px;font-weight:700;color:var(--kdo-navy)}
/* 3. Badge & indikator */
.kdo-chip{display:inline-block;padding:2px 9px;border-radius:7px;font-size:10px;font-weight:700;letter-spacing:.3px;background:#eef4fb;color:#1f5f99;white-space:nowrap}
.kdo-chip+.kdo-chip{margin-left:3px}
.kdo-chip-ungu{background:#f1ecfb;color:#5b3ea6}
.kdo-chip-amber{background:#fff6e0;color:#9a6700}
.kdo-chip-hijau{background:#ecfdf3;color:#067647}
.kdo-chip-merah{background:#fef3f2;color:#b42318}
.kdo-pill{display:inline-block;font-size:10px;font-weight:700;color:#667085;background:#f2f4f7;border-radius:999px;padding:0 7px}
.kdo-status-line{font-size:11px;color:#667085}
.kdo-status-line .kritis{color:#b42318;font-weight:700}
.kdo-note{display:flex;gap:8px;padding:9px 12px;border-radius:10px;font-size:12.5px;line-height:1.5;background:#fffaeb;border:1px solid #fedf89;color:#7a2e0e}
.kdo-note>i{color:#dc6803;margin-top:3px}
.kdo-note.aman{background:#f6fef9;border-color:#abefc6;color:#067647}
.kdo-note.aman>i{color:#079455}
.kdo-note b{font-weight:700}
.kdo-note-link{font-weight:700;color:var(--kdo-navy);cursor:pointer;white-space:nowrap}
.kdo-note-link:hover{color:var(--kdo-primary);text-decoration:underline}
.kdo-note-small{display:block;font-size:11px;color:#98a2b3}
.kdo-btn-lihat,.kdo-btn-aksi{display:inline-flex;align-items:center;justify-content:center;gap:5px;height:30px;padding:0 12px;border-radius:8px;border:1px solid var(--kdo-line-field);background:#fff;font-size:11px;font-weight:700;color:var(--kdo-navy);cursor:pointer;white-space:nowrap;transition:all .15s}
.kdo-btn-lihat:hover{background:var(--kdo-navy);border-color:var(--kdo-navy);color:#fff}
.kdo-btn-aksi{padding:0 10px}
.kdo-btn-aksi:hover{background:#eef4fb;border-color:#cfe0f1}
.kdo-btn-aksi.hapus:hover{background:#fdecea;border-color:#f5c2c7;color:#b42318}
.kdo-btn-aksi.kirim{background:var(--kdo-primary);border-color:var(--kdo-primary);color:#fff}
.kdo-btn-aksi.kirim:hover{background:var(--kdo-primary-hover)}
.kdo-aksi{display:flex;gap:5px;justify-content:flex-end}
/* Kartu statistik */
.kdo-stat{padding:16px 18px;display:flex;align-items:center;gap:14px;height:100%}
.kdo-stat-icon{width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:16px;background:#eef4fb;color:#1f5f99;flex:none}
.kdo-stat-icon.amber{background:#fff6e0;color:#9a6700}.kdo-stat-icon.merah{background:#fef3f2;color:#b42318}.kdo-stat-icon.ungu{background:#f1ecfb;color:#5b3ea6}.kdo-stat-icon.hijau{background:#ecfdf3;color:#067647}
.kdo-stat-val{font-size:22px;font-weight:800;color:var(--kdo-navy);line-height:1.1}
.kdo-stat-label{font-size:11.5px;font-weight:600;color:#6b7688}
/* Form modal */
.modal-content{border:0;border-radius:16px;box-shadow:0 24px 48px rgba(15,23,42,.18)}
.modal-header{border-bottom:1px solid var(--kdo-line-table);padding:16px 20px}
.modal-title{font-size:15px;font-weight:800;color:var(--kdo-navy)}
.modal-footer{border-top:1px solid var(--kdo-line-table)}
.modal label{font-size:12px;font-weight:700;color:#475467;margin-bottom:4px}
.modal .form-control{border-radius:10px;border-color:var(--kdo-line-field);font-size:13px;color:var(--kdo-text)}
.modal .form-control:focus{border-color:#7ea9d8;box-shadow:var(--kdo-ring)}
.modal .invalid-feedback{font-size:11.5px}
/* 4. Preset SweetAlert2 (swalModern) */
.swal2-popup.swal-modern{border-radius:18px;border:0;box-shadow:0 24px 48px rgba(15,23,42,.18);padding:26px 26px 20px}
.swal-modern .swal2-title{font-size:17px;font-weight:800;color:#0f172a;padding:0}
.swal-modern .swal2-html-container{font-size:13px;color:#475467;line-height:1.6;margin:10px 0 0}
.swal-modern .swal2-icon{font-size:10.8px;margin:0 auto 14px;border-width:3px!important}
.swal-modern .swal2-icon.swal2-info{border-color:#cfe4fb;color:#175cd3}
.swal-modern .swal2-icon.swal2-question{border-color:#cfe4fb;color:#175cd3}
.swal-modern .swal2-icon.swal2-warning{border-color:#fedf89;color:#b54708}
.swal-modern .swal2-icon.swal2-error{border-color:#fecdca;color:#d92d20}
.swal-modern .swal2-icon.swal2-error [class^=swal2-x-mark-line]{background-color:#d92d20}
.swal-modern .swal2-icon.swal2-success{border-color:#c6f0d8;color:#067647}
.swal-modern .swal2-icon.swal2-success .swal2-success-ring{border-color:#c6f0d8}
.swal-modern .swal2-icon.swal2-success [class^=swal2-success-line]{background-color:#067647}
.swal-modern .swal2-actions{margin-top:20px;gap:8px}
.swal-modern-btn{border:0;border-radius:10px;padding:11px 20px;font-size:13.5px;font-weight:700;box-shadow:none;cursor:pointer}
.swal-modern-btn:focus{outline:0}.swal-modern-btn:focus-visible{box-shadow:var(--kdo-ring)}
.swal-modern-confirm{background:var(--kdo-primary);color:#fff}.swal-modern-confirm:hover{background:var(--kdo-primary-hover)}
.swal-modern-tolak{background:#d92d20}.swal-modern-tolak:hover{background:#b42318}
.swal-modern-cancel{background:#f2f4f7;color:#475467}.swal-modern-cancel:hover{background:#e7eaee}
.swal-modern .swal2-input,.swal-modern .swal2-textarea{border:1px solid #d0d5dd;border-radius:10px;box-shadow:none;font-size:13px}
.swal-modern .swal2-input:focus,.swal-modern .swal2-textarea:focus{border-color:#7ea9d8;box-shadow:var(--kdo-ring)}
.kdo-teks-panjang{white-space:pre-wrap;text-align:left;font-size:12.5px;background:#f8fafc;border:1px solid var(--kdo-line-table);border-radius:10px;padding:12px 14px;max-height:55vh;overflow:auto;color:var(--kdo-text)}
.kdo-teks-meta{margin-top:8px;font-size:11px;color:#6b7688;text-align:left;word-break:break-all}
/* Popup tabel rincian */
.kdo-popup-overlay{position:fixed;inset:0;z-index:20000;background:rgba(15,23,42,.45);display:flex;align-items:center;justify-content:center;padding:24px}
.kdo-popup{background:#fff;width:100%;max-width:760px;max-height:84vh;border-radius:16px;box-shadow:0 24px 48px rgba(15,23,42,.28);display:flex;flex-direction:column;overflow:hidden}
.kdo-popup-head{display:flex;align-items:center;gap:11px;padding:13px 16px;background:#f3f7fd;border-bottom:1px solid #e4ecf8}
.kdo-popup-icon{width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#082556,#287bb5);color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;flex:none}
.kdo-popup-title{font-size:13.5px;font-weight:700;color:var(--kdo-navy)}
.kdo-popup-sub{font-size:11.5px;color:#7089ab}
.kdo-popup-close{margin-left:auto;border:0;background:transparent;font-size:22px;line-height:1;color:#7089ab;cursor:pointer;padding:0 4px}
.kdo-popup-close:hover{color:var(--kdo-navy)}
.kdo-popup-body{overflow:auto;flex:1}
.kdo-popup-table{width:100%;font-size:12.5px;border-collapse:collapse}
.kdo-popup-table thead th{position:sticky;top:0;background:#f8fafc;font-size:10.5px;text-transform:uppercase;letter-spacing:.4px;color:#667085;font-weight:700;padding:8px 14px;border-bottom:1px solid var(--kdo-line-table);white-space:nowrap}
.kdo-popup-table td{padding:8px 14px;border-bottom:1px solid var(--kdo-line-table);color:var(--kdo-text);vertical-align:middle}
.kdo-popup-table tfoot td{font-weight:800;color:var(--kdo-navy);background:#f8fafc}
.kdo-popup-foot{font-size:11px;color:#98a2b3;padding:8px 16px;border-top:1px solid var(--kdo-line-table)}
@media (max-width:575.98px){.kdo-popup-overlay{padding:10px}.kdo-popup-table td,.kdo-popup-table thead th{padding:7px 9px}}
/* Layout — palet sidebar mengikuti warna brand: biru #006cb8 (latar) & oranye #ff9e00 (menu aktif) */
:root{--kdo-sb-lebar:236px;--kdo-sb-ciut:78px;--kdo-sb-ease:cubic-bezier(.4,0,.2,1);--kdo-sb-durasi:.3s}
.kdo-sidebar{--kdo-sb-biru:#006cb8;--kdo-sb-oren:#ff9e00;position:fixed;top:0;left:0;bottom:0;width:var(--kdo-sb-lebar);background:var(--kdo-sb-biru);color:#eaf3fb;padding:18px 12px;overflow-x:hidden;overflow-y:auto;z-index:1035;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.25) transparent;transition:width var(--kdo-sb-durasi) var(--kdo-sb-ease),transform var(--kdo-sb-durasi) var(--kdo-sb-ease),box-shadow var(--kdo-sb-durasi)}
/* Brand bertumpuk: logo, nama, kepanjangan. Logo BPAD transparan & birunya = latar sidebar, jadi diberi alas putih */
.kdo-brand{display:flex;flex-direction:column;align-items:center;text-align:center;color:#fff;padding:6px 0 16px;margin-bottom:4px;border-bottom:1px solid rgba(255,255,255,.16);transition:padding var(--kdo-sb-durasi) var(--kdo-sb-ease)}
.kdo-brand-logo{width:80px;height:80px;object-fit:contain;background:#fff;border-radius:14px;padding:7px;box-shadow:0 6px 16px rgba(0,0,0,.16);margin-bottom:10px;transition:width var(--kdo-sb-durasi) var(--kdo-sb-ease),height var(--kdo-sb-durasi) var(--kdo-sb-ease),padding var(--kdo-sb-durasi) var(--kdo-sb-ease),margin var(--kdo-sb-durasi) var(--kdo-sb-ease),border-radius var(--kdo-sb-durasi)}
.kdo-brand-teks{max-height:60px;overflow:hidden;white-space:nowrap;transition:opacity .2s,max-height var(--kdo-sb-durasi) var(--kdo-sb-ease)}
.kdo-brand-nama{font-size:16px;font-weight:800;line-height:1.2}
.kdo-brand small{display:block;font-size:10.5px;font-weight:600;color:#dcecf8;margin-top:2px}
.kdo-menu-label{position:relative;font-size:10px;font-weight:800;letter-spacing:.6px;text-transform:uppercase;color:#dcecf8;padding:14px 10px 6px;white-space:nowrap;transition:color .2s}
.kdo-menu-label::after{content:'';position:absolute;left:50%;top:62%;width:22px;height:2px;margin-left:-11px;border-radius:2px;background:rgba(255,255,255,.3);opacity:0;transition:opacity .2s}
.kdo-menu a{position:relative;display:flex;align-items:center;padding:9px 12px;border-radius:10px;color:#eaf3fb;font-size:13px;font-weight:600;text-decoration:none;margin-bottom:3px;white-space:nowrap;overflow:hidden;transition:background .2s,color .2s,box-shadow .2s,padding var(--kdo-sb-durasi) var(--kdo-sb-ease)}
.kdo-menu a i{flex:none;width:18px;text-align:center;font-size:14px;transition:transform .2s}
.kdo-menu-teks{display:inline-block;margin-left:11px;max-width:170px;overflow:hidden;text-overflow:ellipsis;transition:opacity .2s,max-width var(--kdo-sb-durasi) var(--kdo-sb-ease),margin var(--kdo-sb-durasi) var(--kdo-sb-ease)}
.kdo-menu a:hover{background:rgba(255,255,255,.12);color:#fff}
.kdo-menu a:hover i{transform:scale(1.12)}
.kdo-menu a.aktif{background:var(--kdo-sb-oren);color:#fff;font-weight:700;text-shadow:0 1px 2px rgba(0,0,0,.18);box-shadow:0 6px 14px rgba(255,158,0,.35)}
.kdo-menu a.aktif:hover{background:#f29600;color:#fff}
/* Tombol ciutkan sidebar (desktop), menempel di tepi kanan sidebar */
.kdo-sb-toggle{position:fixed;top:50px;left:calc(var(--kdo-sb-lebar) - 13px);z-index:1036;width:26px;height:26px;padding:0;border:0;border-radius:50%;background:#fff;color:#006cb8;box-shadow:0 2px 10px rgba(15,23,42,.22);display:flex;align-items:center;justify-content:center;font-size:11px;cursor:pointer;transition:left var(--kdo-sb-durasi) var(--kdo-sb-ease),background .2s,color .2s,transform .2s}
.kdo-sb-toggle:hover{background:#ff9e00;color:#fff;transform:scale(1.1)}
.kdo-sb-toggle:focus{outline:0}.kdo-sb-toggle:focus-visible{box-shadow:0 0 0 3px rgba(255,158,0,.45)}
.kdo-sb-toggle i{transition:transform var(--kdo-sb-durasi) var(--kdo-sb-ease)}
.kdo-sb-backdrop{position:fixed;inset:0;z-index:1034;background:rgba(15,23,42,.45);opacity:0;visibility:hidden;transition:opacity var(--kdo-sb-durasi),visibility var(--kdo-sb-durasi)}
.kdo-main{margin-left:var(--kdo-sb-lebar);min-height:100vh;transition:margin-left var(--kdo-sb-durasi) var(--kdo-sb-ease)}
.kdo-topbar{background:#fff;padding:12px 24px;display:flex;align-items:center;gap:12px;box-shadow:0 1px 0 #eef1f5;position:sticky;top:0;z-index:1020}
.kdo-content{padding:20px 24px 32px}
.kdo-user{margin-left:auto;display:flex;align-items:center;gap:10px;font-size:12.5px}
.kdo-user-name{font-weight:700;color:var(--kdo-navy)}
.tooltip{font-family:inherit}
.tooltip-inner{background:var(--kdo-navy);font-size:11.5px;font-weight:700;border-radius:8px;padding:6px 10px}
.bs-tooltip-right .arrow::before,.bs-tooltip-auto[x-placement^=right] .arrow::before{border-right-color:var(--kdo-navy)}
/* Mode ciut (desktop): rail ikon; status disimpan di localStorage */
@media (min-width:992px){
html.kdo-ciut .kdo-sidebar{width:var(--kdo-sb-ciut)}
html.kdo-ciut .kdo-main{margin-left:var(--kdo-sb-ciut)}
html.kdo-ciut .kdo-sb-toggle{left:calc(var(--kdo-sb-ciut) - 13px)}
html.kdo-ciut .kdo-sb-toggle i{transform:rotate(180deg)}
html.kdo-ciut .kdo-brand{padding-bottom:12px}
html.kdo-ciut .kdo-brand-logo{width:46px;height:46px;padding:4px;border-radius:12px;margin-bottom:0}
html.kdo-ciut .kdo-brand-teks{opacity:0;max-height:0}
html.kdo-ciut .kdo-menu-label{color:transparent}
html.kdo-ciut .kdo-menu-label::after{opacity:1}
html.kdo-ciut .kdo-menu a{padding-left:18px;padding-right:18px}
html.kdo-ciut .kdo-menu-teks{opacity:0;max-width:0;margin-left:0}
}
/* Layar kecil: sidebar menjadi drawer dengan backdrop */
@media (max-width:991.98px){
.kdo-sidebar{transform:translateX(-100%)}
html.kdo-sb-buka .kdo-sidebar{transform:none;box-shadow:12px 0 32px rgba(15,23,42,.28)}
html.kdo-sb-buka .kdo-sb-backdrop{opacity:1;visibility:visible}
.kdo-main{margin-left:0}
.kdo-sb-toggle{display:none}
}
@media (prefers-reduced-motion:reduce){.kdo-sidebar,.kdo-main,.kdo-sb-toggle,.kdo-menu a,.kdo-menu-teks,.kdo-brand-logo,.kdo-brand-teks{transition:none!important}}
</style>
@endonce
