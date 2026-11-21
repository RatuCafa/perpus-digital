(function () {
    'use strict';

    var rupiah = function (n) { return 'Rp ' + n.toLocaleString('id-ID'); };

    // Menu responsif
    var toggle = document.querySelector('.nav-toggle');
    var nav = document.querySelector('.main-nav');
    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            var open = nav.classList.toggle('open');
            toggle.setAttribute('aria-expanded', String(open));
        });
    }

    // Validasi form sisi klien
    document.querySelectorAll('form.validate').forEach(function (form) {
        var showError = function (field, msg) {
            var box = form.querySelector('.error[data-for="' + field.name + '"]');
            if (box) { box.textContent = msg; }
            field.classList.toggle('invalid', msg !== '');
        };
        var check = function (field) {
            var v = field.value.trim(), msg = '';
            if (field.required && v === '') { msg = 'Wajib diisi.'; }
            else if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) { msg = 'Format email tidak valid.'; }
            else if (field.minLength > 0 && v.length < field.minLength && v !== '') { msg = 'Minimal ' + field.minLength + ' karakter.'; }
            else if (field.type === 'number' && v !== '') {
                var n = Number(v);
                if (field.min !== '' && n < Number(field.min)) { msg = 'Minimal ' + field.min + '.'; }
                if (field.max !== '' && n > Number(field.max)) { msg = 'Maksimal ' + field.max + '.'; }
            }
            if (field.name === 'confirm') {
                var pw = form.querySelector('[name="password"]');
                if (pw && v !== pw.value) { msg = 'Konfirmasi tidak cocok.'; }
            }
            showError(field, msg);
            return msg === '';
        };
        var fields = form.querySelectorAll('input:not([type=hidden]), textarea');
        fields.forEach(function (f) {
            f.addEventListener('blur', function () { check(f); });
            f.addEventListener('input', function () { if (f.classList.contains('invalid')) { check(f); } });
        });
        form.addEventListener('submit', function (e) {
            var ok = true;
            fields.forEach(function (f) { if (!check(f)) { ok = false; } });
            if (!ok) {
                e.preventDefault();
                var first = form.querySelector('.invalid');
                if (first) { first.focus(); }
            }
        });
    });

    // Indikator kekuatan password
    var pw = document.querySelector('#strength');
    var pwInput = document.querySelector('form.validate #password');
    if (pw && pwInput) {
        pwInput.addEventListener('input', function () {
            var v = pwInput.value, score = 0;
            if (v.length >= 6) { score++; }
            if (v.length >= 10) { score++; }
            if (/[A-Z]/.test(v) && /[a-z]/.test(v)) { score++; }
            if (/\d/.test(v) && /[^A-Za-z0-9]/.test(v)) { score++; }
            pw.textContent = v ? 'Kekuatan: ' + ['lemah', 'lemah', 'cukup', 'baik', 'kuat'][score] : '';
        });
    }

    // Pencarian katalog langsung (tanpa reload)
    var q = document.querySelector('#q');
    var cards = document.querySelectorAll('#catalog-grid .card');
    var info = document.querySelector('#result-info');
    if (q && cards.length) {
        q.addEventListener('input', function () {
            var term = q.value.trim().toLowerCase(), shown = 0;
            cards.forEach(function (c) {
                var match = c.dataset.search.indexOf(term) !== -1;
                c.hidden = !match;
                if (match) { shown++; }
            });
            if (info) { info.textContent = shown + ' media ditemukan.'; }
        });
    }

    // Dialog pinjam + hitung biaya
    var dialog = document.querySelector('#borrow-dialog');
    if (dialog && typeof dialog.showModal === 'function') {
        var days = document.querySelector('#borrow-days');
        var total = document.querySelector('#borrow-total');
        var fee = 0;
        var update = function () {
            var d = parseInt(days.value, 10);
            var max = parseInt(days.max, 10);
            total.textContent = (d >= 1 && d <= max) ? rupiah(fee * d) : 'Isi 1 - ' + max + ' hari';
        };
        document.querySelectorAll('.btn-borrow').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelector('#borrow-id').value = btn.dataset.id;
                document.querySelector('#borrow-title').textContent = btn.dataset.title;
                days.max = btn.dataset.max;
                days.value = Math.min(3, parseInt(btn.dataset.max, 10));
                fee = parseInt(btn.dataset.fee, 10);
                update();
                dialog.showModal();
            });
        });
        days.addEventListener('input', update);
        document.querySelector('#borrow-cancel').addEventListener('click', function () { dialog.close(); });
        document.querySelector('#borrow-form').addEventListener('submit', function (e) {
            var d = parseInt(days.value, 10);
            if (!(d >= 1 && d <= parseInt(days.max, 10))) { e.preventDefault(); days.focus(); }
        });
    }

    // Konfirmasi hapus
    document.querySelectorAll('form.confirm-delete').forEach(function (f) {
        f.addEventListener('submit', function (e) {
            if (!window.confirm('Hapus "' + f.dataset.title + '"?')) { e.preventDefault(); }
        });
    });

    // Form media: tampilkan field sesuai jenis
    var typeSel = document.querySelector('select#type');
    var sets = document.querySelectorAll('fieldset[data-show]');
    if (typeSel && sets.length) {
        var sync = function () {
            sets.forEach(function (s) { s.hidden = s.dataset.show !== typeSel.value; });
        };
        typeSel.addEventListener('change', sync);
        sync();
    }
})();
