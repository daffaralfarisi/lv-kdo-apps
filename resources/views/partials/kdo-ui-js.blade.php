{{-- Partial JS UI KDO. Dropdown kustom & popup rincian memakai JS murni; helper tabel/CRUD memakai jQuery. --}}
@once
<script id="kdo-ui-js">
(function () {
    'use strict';
    if (window.KDO) { return; } // guard: jangan terpasang dua kali

    var BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    var KOSONG = '<span class="kdo-kosong">—</span>';
    var KDO = {};

    KDO.esc = function (nilai) {
        if (nilai === null || nilai === undefined) { return ''; }
        return String(nilai).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    };
    KDO.kosong = KOSONG;
    KDO.atauKosong = function (nilai) { return nilai === null || nilai === undefined || nilai === '' ? KOSONG : KDO.esc(nilai); };
    KDO.tanggal = function (nilai) { return nilai ? moment(nilai).format('DD/MM/YYYY') : KOSONG; };
    KDO.tanggalJam = function (nilai) { return nilai ? moment(nilai).format('DD/MM/YYYY HH:mm') : KOSONG; };
    KDO.tanggalKalimat = function (nilai) {
        if (!nilai) { return ''; }
        var m = moment(nilai);
        return m.date() + ' ' + BULAN[m.month()] + ' ' + m.year();
    };
    KDO.rupiah = function (nilai) { return 'Rp ' + Number(nilai || 0).toLocaleString('id-ID'); };
    KDO.angka = function (nilai) { return Number(nilai || 0).toLocaleString('id-ID'); };
    KDO.chip = function (teks, varian) {
        return '<span class="kdo-chip' + (varian ? ' kdo-chip-' + varian : '') + '">' + KDO.esc(teks) + '</span>';
    };
    var WARNA = {
        baik: 'hijau', rusak_ringan: 'amber', rusak_berat: 'merah',
        aktif: 'hijau', dalam_service: 'amber', tidak_aktif: 'merah',
        dijadwalkan: '', berlangsung: 'amber', selesai: 'hijau',
        admin: 'ungu', user: '', umur: 'amber', dev: 'ungu', prod: 'hijau'
    };
    KDO.chipKode = function (kode, label) { return KDO.chip(label, WARNA[kode]); };
    KDO.orang = function (nama, sub) {
        if (!nama) { return KOSONG; }
        return '<span class="d-block font-weight-bold">' + KDO.esc(nama) + '</span>' + (sub ? '<span class="kdo-sub">' + KDO.esc(sub) + '</span>' : '');
    };

    /* ---------- Dropdown kustom pengganti <select> (JS murni) ---------- */
    var KdoSelect = {
        pasang: function (select) {
            if (select.dataset.kdoSelect) { return; }
            select.dataset.kdoSelect = '1';
            select.classList.add('kdo-select-native');
            var field = select.closest('.kdo-field');
            var tombol = document.createElement('button');
            tombol.type = 'button';
            tombol.className = 'kdo-select-btn';
            tombol.innerHTML = '<span></span><i class="fas fa-chevron-down kdo-field-chevron"></i>';
            var panel = document.createElement('div');
            panel.className = 'kdo-select-panel';
            panel.setAttribute('role', 'listbox');
            select.insertAdjacentElement('afterend', tombol);
            field.appendChild(panel);
            select._kdo = { tombol: tombol, panel: panel };

            tombol.addEventListener('click', function (e) {
                e.stopPropagation();
                var terbuka = panel.classList.contains('show');
                KdoSelect.tutupSemua();
                if (!terbuka) { KdoSelect.render(select); panel.classList.add('show'); }
            });
            panel.addEventListener('click', function (e) {
                var opsi = e.target.closest('.kdo-select-opt');
                if (!opsi) { return; }
                e.stopPropagation();
                select.value = opsi.dataset.value;
                panel.classList.remove('show');
                KdoSelect.label(select);
                select.dispatchEvent(new Event('change', { bubbles: true }));
            });
            select.addEventListener('change', function () { KdoSelect.label(select); });
            KdoSelect.label(select);
        },
        render: function (select) {
            var html = '';
            Array.prototype.forEach.call(select.options, function (opt) {
                html += '<div class="kdo-select-opt' + (opt.value === select.value ? ' aktif' : '') + '" data-value="' + KDO.esc(opt.value) + '">'
                    + '<span>' + KDO.esc(opt.text) + '</span><i class="fas fa-check"></i></div>';
            });
            select._kdo.panel.innerHTML = html;
        },
        label: function (select) {
            if (!select._kdo) { return; }
            var opt = select.options[select.selectedIndex];
            select._kdo.tombol.querySelector('span').textContent = opt ? opt.text : '';
        },
        refresh: function (select) { if (select._kdo) { KdoSelect.render(select); KdoSelect.label(select); } },
        tutupSemua: function () {
            document.querySelectorAll('.kdo-select-panel.show').forEach(function (p) { p.classList.remove('show'); });
        },
        pasangSemua: function (root) {
            (root || document).querySelectorAll('.kdo-field select').forEach(KdoSelect.pasang);
        }
    };
    document.addEventListener('click', KdoSelect.tutupSemua);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { KdoSelect.tutupSemua(); } });
    KDO.select = KdoSelect;

    /* ---------- Popup tabel rincian (JS murni) ---------- */
    KDO.popup = function (o) {
        var pemicu = document.activeElement;
        var kolom = o.kolom || [];
        var baris = o.baris || [];
        var sel = function (k, b) { return k.html ? k.html(b) : KDO.atauKosong(b[k.key]); };
        var isi = baris.length ? baris.map(function (b) {
            return '<tr>' + kolom.map(function (k) { return '<td class="' + (k.kanan ? 'text-right' : '') + '">' + sel(k, b) + '</td>'; }).join('') + '</tr>';
        }).join('') : '<tr><td colspan="' + kolom.length + '" class="text-center kdo-kosong">Tidak ada data</td></tr>';
        var overlay = document.createElement('div');
        overlay.className = 'kdo-popup-overlay';
        overlay.innerHTML = '<div class="kdo-popup" role="dialog" aria-modal="true">'
            + '<div class="kdo-popup-head"><div class="kdo-popup-icon"><i class="fas ' + (o.ikon || 'fa-list') + '"></i></div>'
            + '<div><div class="kdo-popup-title">' + KDO.esc(o.judul) + '</div><div class="kdo-popup-sub">' + KDO.esc(o.subjudul || '') + '</div></div>'
            + '<button type="button" class="kdo-popup-close" aria-label="Tutup">&times;</button></div>'
            + (o.htmlAtas || '')
            + '<div class="kdo-popup-body"><table class="kdo-popup-table"><thead><tr>'
            + kolom.map(function (k) { return '<th class="' + (k.kanan ? 'text-right' : '') + '">' + KDO.esc(k.label) + '</th>'; }).join('')
            + '</tr></thead><tbody>' + isi + '</tbody>' + (o.tfoot || '') + '</table></div>'
            + (o.kaki ? '<div class="kdo-popup-foot">' + KDO.esc(o.kaki) + '</div>' : '') + '</div>';
        document.body.appendChild(overlay);
        function tutup() {
            document.removeEventListener('keydown', tombolEsc, true);
            overlay.remove();
            if (pemicu && pemicu.focus) { pemicu.focus(); }
        }
        function tombolEsc(e) {
            if (e.key === 'Escape') { e.stopPropagation(); e.preventDefault(); tutup(); }
        }
        document.addEventListener('keydown', tombolEsc, true); // capture agar modal di bawahnya tidak ikut tertutup
        overlay.addEventListener('click', function (e) { if (e.target === overlay) { tutup(); } });
        overlay.querySelector('.kdo-popup-close').addEventListener('click', tutup);
        overlay.querySelector('.kdo-popup-close').focus();
    };

    /* ---------- SweetAlert2 preset ---------- */
    KDO.swalModern = function (gaya, opsi) {
        return Swal.fire(Object.assign({
            buttonsStyling: false,
            confirmButtonText: 'OK',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'swal-modern',
                confirmButton: 'swal-modern-btn swal-modern-confirm' + (gaya === 'tolak' ? ' swal-modern-tolak' : ''),
                cancelButton: 'swal-modern-btn swal-modern-cancel',
                denyButton: 'swal-modern-btn swal-modern-cancel'
            }
        }, opsi || {}));
    };
    KDO.teksPanjang = function (o) {
        return KDO.swalModern(o.gaya || 'info', Object.assign({
            width: 760,
            showCloseButton: true,
            title: o.judul,
            html: '<div class="kdo-teks-panjang">' + KDO.esc(o.isi) + '</div>' + (o.meta ? '<div class="kdo-teks-meta">' + KDO.esc(o.meta) + '</div>' : '')
        }, o.opsi || {}));
    };
    KDO.sukses = function (pesan) {
        return KDO.swalModern('info', { icon: 'success', title: 'Berhasil', text: pesan, timer: 1600, showConfirmButton: false });
    };
    KDO.gagal = function (xhr) {
        var pesan = (xhr && xhr.responseJSON && xhr.responseJSON.message) || 'Terjadi kesalahan. Silakan coba lagi.';
        return KDO.swalModern('info', { icon: 'error', title: 'Gagal', text: pesan });
    };
    KDO.konfirmasiHapus = function (nama, catatan) {
        return KDO.swalModern('tolak', {
            icon: 'warning', title: 'Hapus data?', showCancelButton: true, confirmButtonText: 'Ya, hapus', reverseButtons: true,
            html: 'Data <b>' + KDO.esc(nama) + '</b> akan dihapus permanen.' + (catatan ? '<br><small>' + KDO.esc(catatan) + '</small>' : '')
        });
    };

    /* ---------- API (session + CSRF Sanctum) ---------- */
    KDO.api = function (method, url, data) {
        return $.ajax({ url: url, method: method, dataType: 'json', contentType: 'application/json', data: data ? JSON.stringify(data) : undefined });
    };
    KDO.siapkanAjax = function () {
        $.ajaxSetup({ headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $(document).ajaxError(function (e, xhr) {
            if ((xhr.status === 401 || xhr.status === 419) && !window.KDO_HALAMAN_LOGIN) {
                KDO.swalModern('info', { icon: 'warning', title: 'Sesi berakhir', text: 'Silakan masuk kembali.', allowOutsideClick: false })
                    .then(function () { window.location.href = '/login'; });
            }
        });
    };
    KDO.isiSelect = function (select, url, label, placeholder) {
        return $.getJSON(url, { per_page: 500 }).done(function (res) {
            var nilai = select.value;
            select.innerHTML = (placeholder !== null ? '<option value="">' + KDO.esc(placeholder || 'Pilih') + '</option>' : '')
                + res.data.map(function (r) { return '<option value="' + r.id + '">' + KDO.esc(label(r)) + '</option>'; }).join('');
            select.value = nilai;
            KdoSelect.refresh(select);
        });
    };

    /* ---------- Form ---------- */
    KDO.dataForm = function ($form) {
        var data = {};
        $form.serializeArray().forEach(function (f) { data[f.name] = f.value === '' ? null : f.value; });
        return data;
    };
    KDO.bersihkanError = function ($form) {
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback.kdo-err').remove();
    };
    KDO.tampilkanError = function ($form, errors) {
        KDO.bersihkanError($form);
        Object.keys(errors).forEach(function (nama) {
            var $el = $form.find('[name="' + nama + '"]');
            $el.addClass('is-invalid').after('<div class="invalid-feedback kdo-err">' + KDO.esc(errors[nama][0]) + '</div>');
        });
        $form.find('.is-invalid').first().trigger('focus');
    };

    /* ---------- Filter bar ---------- */
    KDO.filter = function ($root, onUbah) {
        var reset = false;
        var timer = null;
        $root.find('input[type=search]').on('input', function () {
            clearTimeout(timer);
            timer = setTimeout(onUbah, 400); // debounce 400ms
        });
        $root.find('select').on('change', function () { if (!reset) { onUbah(); } });
        $root.find('[data-aksi=reset]').on('click', function () {
            reset = true;
            $root.find('input').val('').removeData('dari').removeData('sampai');
            $root.find('select').each(function () {
                this.value = '';
                this.dispatchEvent(new Event('change', { bubbles: true })); // native agar label dropdown kustom ikut berubah
            });
            reset = false;
            onUbah();
        });
        return function () {
            var p = {};
            $root.find('[data-param]').each(function () {
                var $el = $(this);
                if ($el.data('rentang')) {
                    if ($el.data('dari')) { p.tanggal_dari = $el.data('dari'); p.tanggal_sampai = $el.data('sampai'); }
                } else if ($el.val()) {
                    p[$el.data('param')] = $el.val();
                }
            });
            return p;
        };
    };
    KDO.rentangTanggal = function (input, onUbah) {
        var $i = $(input);
        $i.daterangepicker({
            autoUpdateInput: false,
            showDropdowns: true,
            opens: 'left',
            locale: {
                format: 'DD/MM/YYYY', separator: ' – ', applyLabel: 'Terapkan', cancelLabel: 'Hapus', customRangeLabel: 'Pilih rentang',
                daysOfWeek: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                firstDay: 1
            },
            ranges: {
                'Hari ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 hari terakhir': [moment().subtract(6, 'days'), moment()],
                '30 hari terakhir': [moment().subtract(29, 'days'), moment()],
                'Bulan ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Tahun ini': [moment().startOf('year'), moment().endOf('year')]
            }
        });
        $i.on('apply.daterangepicker', function (e, picker) {
            $i.val(picker.startDate.format('DD/MM/YYYY') + ' – ' + picker.endDate.format('DD/MM/YYYY'));
            $i.data('dari', picker.startDate.format('YYYY-MM-DD')).data('sampai', picker.endDate.format('YYYY-MM-DD'));
            onUbah();
        });
        $i.on('cancel.daterangepicker', function () {
            $i.val('').removeData('dari').removeData('sampai');
            onUbah();
        });
    };

    /* ---------- DataTables server-side -> API Laravel (page/per_page/sort/direction) ---------- */
    KDO.tabel = function (selector, o) {
        return $(selector).DataTable({
            serverSide: true,
            processing: true,
            searching: false,
            lengthChange: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            pagingType: 'full_numbers',
            order: o.order || [],
            autoWidth: false,
            dom: 'rt<"d-flex flex-wrap justify-content-between align-items-center"<"d-flex align-items-center"il>p>',
            columns: o.columns,
            language: {
                processing: 'Memuat...', loadingRecords: 'Memuat...',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ baris', infoEmpty: 'Menampilkan 0–0 dari 0 baris', infoFiltered: '',
                emptyTable: 'Tidak ada data', zeroRecords: 'Tidak ada data', lengthMenu: '_MENU_',
                paginate: { first: 'Awal', last: 'Akhir', next: 'Berikutnya', previous: 'Sebelumnya' }
            },
            ajax: function (dt, callback) {
                var urut = dt.order && dt.order.length ? dt.order[0] : null;
                var params = Object.assign({ page: Math.floor(dt.start / dt.length) + 1, per_page: dt.length }, o.params ? o.params() : {});
                if (urut && o.columns[urut.column].name) { params.sort = o.columns[urut.column].name; params.direction = urut.dir; }
                $.getJSON(o.url, params).done(function (res) {
                    callback({ draw: dt.draw, recordsTotal: res.meta.total, recordsFiltered: res.meta.total, data: res.data });
                    if (o.setelahMuat) { o.setelahMuat(res); }
                }).fail(function (xhr) {
                    if (xhr.status !== 401 && xhr.status !== 419) { KDO.gagal(xhr); }
                    callback({ draw: dt.draw, recordsTotal: 0, recordsFiltered: 0, data: [] });
                });
            }
        });
    };
    KDO.tombolAksi = function (lihat) {
        return '<div class="kdo-aksi">'
            + (lihat ? '<button type="button" class="kdo-btn-lihat" data-aksi="lihat"><i class="fas fa-file-alt"></i>Lihat</button>' : '')
            + '<button type="button" class="kdo-btn-aksi" data-aksi="edit" title="Ubah"><i class="fas fa-pen"></i></button>'
            + '<button type="button" class="kdo-btn-aksi hapus" data-aksi="hapus" title="Hapus"><i class="fas fa-trash-alt"></i></button></div>';
    };

    /* ---------- CRUD generik: tabel + modal form + hapus ---------- */
    KDO.crud = function (o) {
        var $modal = $(o.modal);
        var $form = $(o.form);
        var idEdit = null;
        var ambilFilter = KDO.filter($(o.filterBar), function () { tabel.ajax.reload(); });
        var tabel = KDO.tabel(o.tabel, { url: o.url, columns: o.columns, order: o.order, params: ambilFilter });

        function bukaForm(baris) {
            idEdit = baris ? baris.id : null;
            $form[0].reset();
            KDO.bersihkanError($form);
            $modal.find('.modal-title').text(baris ? o.judulEdit : o.judulTambah);
            if (baris) {
                Array.prototype.forEach.call($form[0].elements, function (el) {
                    if (el.name && el.type !== 'password' && Object.prototype.hasOwnProperty.call(baris, el.name)) {
                        el.value = baris[el.name] === null ? '' : baris[el.name];
                    }
                });
            }
            if (o.isiForm) { o.isiForm(baris, $form); }
            $modal.modal('show');
        }
        function barisDari(el) { return tabel.row($(el).closest('tr')).data(); }

        $(o.tabel).on('click', '[data-aksi=edit]', function () { bukaForm(barisDari(this)); });
        $(o.tabel).on('click', '[data-aksi=lihat]', function () { if (o.lihat) { o.lihat(barisDari(this)); } });
        $(o.tabel).on('click', '[data-aksi=hapus]', function () {
            var baris = barisDari(this);
            KDO.konfirmasiHapus(o.namaBaris(baris), o.catatanHapus).then(function (hasil) {
                if (!hasil.isConfirmed) { return; }
                KDO.api('DELETE', o.url + '/' + baris.id).done(function (res) {
                    KDO.sukses(res.message);
                    tabel.ajax.reload(null, false);
                }).fail(KDO.gagal);
            });
        });
        $(o.filterBar).find('[data-aksi=tambah]').on('click', function () { bukaForm(null); });
        $form.on('submit', function (e) {
            e.preventDefault();
            var $tombol = $form.find('[type=submit]').prop('disabled', true);
            var data = o.dataForm ? o.dataForm($form) : KDO.dataForm($form);
            KDO.api(idEdit ? 'PUT' : 'POST', idEdit ? o.url + '/' + idEdit : o.url, data).done(function (res) {
                $modal.modal('hide');
                KDO.sukses(res.message);
                tabel.ajax.reload(null, false);
            }).fail(function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    KDO.tampilkanError($form, xhr.responseJSON.errors);
                } else {
                    KDO.gagal(xhr);
                }
            }).always(function () { $tombol.prop('disabled', false); });
        });
        return { tabel: tabel, bukaForm: bukaForm };
    };

    window.KDO = KDO;
})();
</script>
@endonce
