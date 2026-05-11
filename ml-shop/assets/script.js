// ============================================
// ML Shop — script.js
// ============================================

// ── Promo Countdown Timer ──
(function () {
  const timerEl = document.getElementById('promoTimer');
  if (!timerEl) return;

  // Hitung countdown ke tengah malam
  function getRemainingTime() {
    const now = new Date();
    const midnight = new Date();
    midnight.setHours(23, 59, 59, 0);
    return Math.max(0, midnight - now);
  }

  function pad(n) { return String(n).padStart(2, '0'); }

  function renderTimer() {
    const ms = getRemainingTime();
    const h  = Math.floor(ms / 3_600_000);
    const m  = Math.floor((ms % 3_600_000) / 60_000);
    const s  = Math.floor((ms % 60_000) / 1_000);
    timerEl.innerHTML = `
      <div class="timer-block"><span class="timer-num">${pad(h)}</span><span class="timer-label">Jam</span></div>
      <span class="timer-sep">:</span>
      <div class="timer-block"><span class="timer-num">${pad(m)}</span><span class="timer-label">Menit</span></div>
      <span class="timer-sep">:</span>
      <div class="timer-block"><span class="timer-num">${pad(s)}</span><span class="timer-label">Detik</span></div>
    `;
  }

  renderTimer();
  setInterval(renderTimer, 1000);
})();

// ── Category pill filter (homepage cat grid hover) ──
document.querySelectorAll('.cat-pill').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.cat-pill').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  });
});

// ── Auto-dismiss flash messages ──
const flash = document.querySelector('.flash-banner');
if (flash) {
  setTimeout(() => {
    flash.style.transition = 'opacity 0.5s';
    flash.style.opacity = '0';
    setTimeout(() => flash.remove(), 500);
  }, 4000);
}

// ── Smooth active nav link ──
(function () {
  const path = location.pathname;
  document.querySelectorAll('.nav-links a').forEach(a => {
    if (a.getAttribute('href') && path.includes(a.getAttribute('href').split('?')[0].split('/').pop())) {
      a.classList.add('active');
    }
  });
})();
