/* ============================================================
   Nexa_Topup — assets/script.js
   Semua JavaScript ada di sini. Jangan taruh <script> di PHP.
   ============================================================ */

/* ── Auto-select produk jika URL mengandung ?pid= (pages/topup.php) ── */
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('preselectData');
    if (!el) return;
    const pid = el.dataset.pid;
    const card = document.getElementById('pc-' + pid);
    if (card) card.click();
});


/* ── Navbar Mobile Toggle ── */
(function () {
    const toggle = document.getElementById('navToggle');
    const links  = document.getElementById('navLinks');
    const right  = document.getElementById('navRight');
    if (!toggle) return;
    toggle.addEventListener('click', () => {
        const open = links.classList.toggle('open');
        if (right) right.classList.toggle('open', open);
    });
})();


/* ── Flash: hilang otomatis setelah 5 detik ── */
(function () {
    const flash = document.querySelector('.flash');
    if (!flash) return;
    setTimeout(() => {
        flash.style.transition = 'opacity .5s';
        flash.style.opacity    = '0';
        setTimeout(() => flash.remove(), 600);
    }, 5000);
})();


/* ── Countdown Promo (pages/index.php) ── */
(function () {
    const hEl = document.getElementById('cd-h');
    const mEl = document.getElementById('cd-m');
    const sEl = document.getElementById('cd-s');
    if (!hEl) return;

    function pad(n) { return String(n).padStart(2, '0'); }
    function tick() {
        const now      = new Date();
        const midnight = new Date();
        midnight.setHours(23, 59, 59, 0);
        const ms = Math.max(0, midnight - now);
        hEl.textContent = pad(Math.floor(ms / 3_600_000));
        mEl.textContent = pad(Math.floor((ms % 3_600_000) / 60_000));
        sEl.textContent = pad(Math.floor((ms % 60_000) / 1_000));
    }
    tick();
    setInterval(tick, 1000);
})();


/* ── Topup Page: pilih produk, hitung total, validasi form ── */
(function () {
    const productGrid = document.getElementById('productGrid');
    const orderForm   = document.getElementById('orderForm');
    if (!productGrid && !orderForm) return;

    let selectedPrice = 0;

    /* Format rupiah */
    function formatRP(num) {
        return 'Rp ' + num.toLocaleString('id-ID');
    }

    /* Pilih produk */
    window.selectProduct = function (id, name, price, diamonds, icon) {
        selectedPrice = price;

        document.getElementById('productIdInput').value = id;
        document.getElementById('sdName').textContent   = name;
        document.getElementById('sdPrice').textContent  = formatRP(price);
        document.getElementById('sdIcon').textContent   = icon || '💎';

        document.getElementById('noSelection').style.display     = 'none';
        document.getElementById('selectedDisplay').style.display = 'flex';
        document.getElementById('paymentWrap').style.display     = 'block';
        document.getElementById('submitBtn').disabled            = false;

        document.querySelectorAll('.product-card')
            .forEach(c => c.classList.remove('selected'));
        const card = document.getElementById('pc-' + id);
        if (card) card.classList.add('selected');

        updateTotal();

        if (window.innerWidth < 1025) {
            const panel = document.getElementById('orderPanel');
            if (panel) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    /* Hitung total dengan fee pembayaran */
    window.updateTotal = function () {
        const sel    = document.getElementById('paymentMethod');
        if (!sel) return;
        const opt    = sel.options[sel.selectedIndex];
        const feePct = parseFloat(opt?.dataset?.feePct || 0);
        const feeFix = parseFloat(opt?.dataset?.feeFix || 0);
        const fee    = Math.round(selectedPrice * feePct / 100) + feeFix;
        const total  = selectedPrice + fee;

        const totalRow    = document.getElementById('totalRow');
        const totalAmount = document.getElementById('totalAmount');
        if (!totalRow || !totalAmount) return;

        if (selectedPrice > 0 && sel.value) {
            totalAmount.textContent    = formatRP(total);
            totalRow.style.display     = 'flex';
        } else {
            totalRow.style.display     = 'none';
        }
    };

    /* Sinkron hidden fields sebelum submit */
    window.syncHiddenFields = function () {
        const uid = document.getElementById('mlUserId');
        const sid = document.getElementById('mlServerId');
        const pay = document.getElementById('paymentMethod');
        if (uid) document.getElementById('hiddenUserId').value   = uid.value.trim();
        if (sid) document.getElementById('hiddenServerId').value = sid.value.trim();
        if (pay) document.getElementById('hiddenPayment').value  = pay.value;
    };

    /* Verifikasi User ID (simulasi) */
    window.verifyUser = function () {
        const uid = document.getElementById('mlUserId');
        const sid = document.getElementById('mlServerId');
        const msg = document.getElementById('verifyMsg');
        if (!uid || !sid || !msg) return;

        const uidVal = uid.value.trim();
        const sidVal = sid.value.trim();

        if (!uidVal || !sidVal) {
            msg.innerHTML = '<span class="v-warn">⚠️ Isi User ID dan Zone ID dulu.</span>';
            return;
        }
        if (!/^\d{3,20}$/.test(uidVal) || !/^\d{1,8}$/.test(sidVal)) {
            msg.innerHTML = '<span class="v-warn">⚠️ Format ID tidak valid. Gunakan angka saja.</span>';
            return;
        }
        msg.innerHTML = '<span class="v-load">⏳ Memverifikasi akun...</span>';
        setTimeout(() => {
            msg.innerHTML = '<span class="v-ok">✅ Akun ditemukan. Pastikan sudah benar sebelum lanjut!</span>';
        }, 1000);
    };

    /* Validasi form sebelum submit */
    if (orderForm) {
        orderForm.addEventListener('submit', function (e) {
            const uid = document.getElementById('mlUserId')?.value.trim();
            const sid = document.getElementById('mlServerId')?.value.trim();
            const pay = document.getElementById('paymentMethod')?.value;
            const pid = document.getElementById('productIdInput')?.value;

            if (!pid) { e.preventDefault(); alert('Pilih produk terlebih dahulu.'); return; }
            if (!/^\d{3,20}$/.test(uid)) { e.preventDefault(); alert('User ID ML tidak valid (3–20 digit angka).'); return; }
            if (!/^\d{1,8}$/.test(sid))  { e.preventDefault(); alert('Zone ID tidak valid.'); return; }
            if (!pay) { e.preventDefault(); alert('Pilih metode pembayaran.'); return; }

            syncHiddenFields();
        });
    }
})();
