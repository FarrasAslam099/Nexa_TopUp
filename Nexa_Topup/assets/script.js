// ============================================================
// Nexa_Topup — script.js
// ============================================================

// ── Navbar Toggle (Mobile) ──────────────────────────────────
(function () {
  const toggle = document.getElementById('navToggle');
  const links  = document.getElementById('navLinks');
  const right  = document.querySelector('.nav-right');
  if (!toggle) return;
  toggle.addEventListener('click', () => {
    const open = links.classList.toggle('open');
    if (right) right.classList.toggle('open', open);
  });
})();

// ── Flash auto-dismiss ──────────────────────────────────────
(function () {
  const flash = document.querySelector('.flash');
  if (!flash) return;
  setTimeout(() => {
    flash.style.transition = 'opacity .5s';
    flash.style.opacity = '0';
    setTimeout(() => flash.remove(), 600);
  }, 5000);
})();

// ── Countdown (Promo Banner) ────────────────────────────────
(function () {
  const hEl = document.getElementById('cd-h');
  const mEl = document.getElementById('cd-m');
  const sEl = document.getElementById('cd-s');
  if (!hEl) return;

  function pad(n) { return String(n).padStart(2, '0'); }
  function tick() {
    const now      = new Date();
    const midnight = new Date(); midnight.setHours(23, 59, 59, 0);
    const ms = Math.max(0, midnight - now);
    hEl.textContent = pad(Math.floor(ms / 3_600_000));
    mEl.textContent = pad(Math.floor((ms % 3_600_000) / 60_000));
    sEl.textContent = pad(Math.floor((ms % 60_000) / 1_000));
  }
  tick();
  setInterval(tick, 1000);
})();
