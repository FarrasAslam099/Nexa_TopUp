<?php
// ============================================================
// includes/header.php — HTML Head
// ============================================================
$pageTitle       = $pageTitle       ?? 'Nexa_Topup — Top Up Mobile Legends';
$pageDescription = $pageDescription ?? 'Platform top up Mobile Legends terpercaya. Diamond, Membership, Bundle, Weekly Pass — harga termurah, proses instan 24/7.';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= sanitize($pageDescription) ?>">
  <title><?= sanitize($pageTitle) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@400;500;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
  <style>
  /* ============================================================
     GLOBAL STYLE (dipindahkan dari assets/style.css)
     Reset, variabel warna, layout dasar, navbar, tombol, flash,
     footer, dan komponen bersama lainnya.
     ============================================================ */

  /* ── Reset & Base ── */
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg:         #0f1117;
    --bg2:        #181c27;
    --bg3:        #1e2333;
    --border:     #2a2f45;
    --text:       #e8ecf4;
    --text2:      #8a93b0;
    --text3:      #555e7a;
    --cyan:       #00d4ff;
    --cyan2:      #0099cc;
    --gold:       #f5a623;
    --gold2:      #e0902a;
    --green:      #22c55e;
    --red:        #ef4444;
    --purple:     #7c3aed;
    --radius:     10px;
    --radius-lg:  16px;
    --shadow:     0 4px 24px rgba(0,0,0,0.4);
    --shadow-sm:  0 2px 8px rgba(0,0,0,0.25);
  }

  html { scroll-behavior: smooth; }

  body {
    font-family: 'Rajdhani', 'Segoe UI', sans-serif;
    font-size: 15px;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    line-height: 1.6;
  }

  a { color: var(--cyan); text-decoration: none; }
  a:hover { text-decoration: underline; }
  img { max-width: 100%; }

  .container {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0 20px;
  }

  /* ── Scrollbar ── */
  ::-webkit-scrollbar { width: 6px; }
  ::-webkit-scrollbar-track { background: var(--bg2); }
  ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

  /* ============================================================
     NAVBAR
     ============================================================ */
  #mainNav {
    position: sticky; top: 0; z-index: 100;
    background: rgba(15,17,23,0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 0;
    padding: 0 24px;
    height: 60px;
  }

  .logo {
    display: flex; align-items: center; gap: 8px;
    font-family: 'Orbitron', sans-serif;
    font-weight: 900; font-size: 16px;
    color: var(--text); text-decoration: none;
    letter-spacing: 1px;
    flex-shrink: 0;
  }
  .logo span { color: var(--cyan); }
  .logo-icon {
    width: 32px; height: 32px;
    background: linear-gradient(135deg, var(--cyan), var(--cyan2));
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
  }

  .nav-links {
    list-style: none;
    display: flex; align-items: center; gap: 4px;
    margin-left: 32px;
    flex: 1;
  }
  .nav-links a {
    display: block; padding: 6px 14px;
    border-radius: 6px;
    color: var(--text2);
    font-size: 14px; font-weight: 600;
    text-decoration: none;
    transition: all .2s;
    white-space: nowrap;
  }
  .nav-links a:hover { color: var(--text); background: var(--bg3); }
  .nav-links a.active { color: var(--cyan); background: rgba(0,212,255,.1); }

  .nav-right {
    display: flex; align-items: center; gap: 8px;
    margin-left: auto;
  }
  .nav-user-wrap { display: flex; align-items: center; gap: 8px; }
  .nav-avatar { font-size: 18px; }
  .nav-username { font-size: 14px; font-weight: 600; color: var(--text2); }

  .btn-sm {
    padding: 6px 14px; border-radius: 6px;
    font-size: 13px; font-weight: 700;
    cursor: pointer; border: none; text-decoration: none;
    transition: all .2s; white-space: nowrap;
  }
  .btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--text2); }
  .btn-ghost:hover { border-color: var(--cyan); color: var(--cyan); text-decoration: none; }
  .btn-gold { background: var(--gold); color: #000; }
  .btn-gold:hover { background: var(--gold2); text-decoration: none; }
  .btn-admin { background: rgba(124,58,237,.2); color: #a78bfa; border: 1px solid rgba(124,58,237,.4); }
  .btn-admin:hover { background: rgba(124,58,237,.35); text-decoration: none; }
  .btn-logout { background: rgba(239,68,68,.15); color: #f87171; border: 1px solid rgba(239,68,68,.3); }
  .btn-logout:hover { background: rgba(239,68,68,.3); text-decoration: none; }

  /* Hamburger */
  .nav-toggle {
    display: none; flex-direction: column; justify-content: center; gap: 5px;
    background: none; border: none; cursor: pointer;
    width: 36px; height: 36px; padding: 6px; margin-left: auto;
  }
  .nav-toggle span {
    display: block; width: 22px; height: 2px;
    background: var(--text2); border-radius: 2px;
    transition: all .3s;
  }

  /* ============================================================
     BUTTONS
     ============================================================ */
  .btn-primary {
    display: inline-block;
    padding: 12px 24px;
    background: linear-gradient(135deg, var(--cyan), var(--cyan2));
    color: #000; font-weight: 700; font-size: 15px;
    border: none; border-radius: var(--radius);
    cursor: pointer; text-decoration: none;
    transition: all .2s; white-space: nowrap;
  }
  .btn-primary:hover { opacity: .9; transform: translateY(-1px); text-decoration: none; }
  .btn-primary:disabled { opacity: .4; cursor: not-allowed; transform: none; }

  .btn-outline {
    display: inline-block;
    padding: 12px 24px;
    background: transparent;
    border: 1.5px solid var(--cyan);
    color: var(--cyan); font-weight: 700; font-size: 15px;
    border-radius: var(--radius); cursor: pointer; text-decoration: none;
    transition: all .2s;
  }
  .btn-outline:hover { background: rgba(0,212,255,.08); text-decoration: none; }
  .btn-block { width: 100%; text-align: center; display: block; }

  /* ============================================================
     FLASH
     ============================================================ */
  .flash {
    padding: 14px 20px;
    border-radius: var(--radius);
    margin: 16px auto; max-width: 1180px;
    font-weight: 600; font-size: 14px;
  }
  .flash-success { background: rgba(34,197,94,.15); border: 1px solid rgba(34,197,94,.3); color: #4ade80; }
  .flash-error   { background: rgba(239,68,68,.15); border: 1px solid rgba(239,68,68,.3); color: #f87171; }
  .flash-info    { background: rgba(0,212,255,.1); border: 1px solid rgba(0,212,255,.25); color: var(--cyan); }

  /* ============================================================
     SECTION & GRID (judul section dipakai di banyak halaman)
     ============================================================ */
  .section { padding: 40px 0; }
  .section-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 20px;
  }
  .section-title {
    font-size: 20px; font-weight: 700;
  }
  .see-all { font-size: 13px; color: var(--cyan); font-weight: 600; }
  .see-all:hover { text-decoration: underline; }

  /* ============================================================
     TABEL DATA (dipakai di history, dashboard, products, orders)
     ============================================================ */
  .table-wrap { overflow-x: auto; border-radius: var(--radius-lg); border: 1px solid var(--border); }
  .data-table { width: 100%; border-collapse: collapse; }
  .data-table th {
    background: var(--bg2); padding: 12px 14px;
    text-align: left; font-size: 12px; font-weight: 700;
    color: var(--text3); letter-spacing: .5px;
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
  }
  .data-table td {
    padding: 12px 14px; font-size: 14px;
    border-bottom: 1px solid var(--border);
    background: var(--bg);
  }
  .data-table tr:last-child td { border-bottom: none; }
  .data-table tr:hover td { background: var(--bg2); }
  .mono { font-family: 'Share Tech Mono', monospace; font-size: 12px; }
  .cell-sub { font-size: 11px; color: var(--text3); margin-top: 2px; }
  .cell-sep { color: var(--text3); margin: 0 3px; }
  .price-cell { font-weight: 700; color: var(--gold); white-space: nowrap; }
  .date-cell { color: var(--text2); white-space: nowrap; }
  .empty-cell { text-align: center; color: var(--text3); padding: 32px; }
  .txt-dim { color: var(--text3); }

  /* Status badges (dipakai di history, dashboard, products, orders) */
  .st-badge {
    display: inline-block; padding: 3px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 700; white-space: nowrap;
  }
  .st-pending    { background: rgba(245,166,35,.2); color: var(--gold); }
  .st-processing { background: rgba(0,212,255,.15); color: var(--cyan); }
  .st-done       { background: rgba(34,197,94,.15); color: var(--green); }
  .st-failed     { background: rgba(239,68,68,.15); color: var(--red); }

  /* Pagination (dipakai di history & admin/orders) */
  .pagination {
    display: flex; gap: 8px; justify-content: center;
    margin-top: 24px; flex-wrap: wrap;
  }
  .pg-btn {
    display: inline-block; padding: 8px 14px;
    background: var(--bg2); border: 1px solid var(--border);
    border-radius: var(--radius); font-size: 14px; font-weight: 600;
    color: var(--text2); text-decoration: none;
    transition: all .2s;
  }
  .pg-btn:hover { border-color: var(--cyan); color: var(--cyan); text-decoration: none; }
  .pg-btn.active { background: var(--cyan); border-color: var(--cyan); color: #000; }

  /* Form group dasar (dipakai di topup, auth, admin) */
  .form-group { margin-bottom: 14px; }
  .form-group label {
    display: block; font-size: 13px; font-weight: 700;
    color: var(--text2); margin-bottom: 6px;
  }
  .form-group input,
  .form-group select {
    width: 100%; padding: 11px 14px;
    background: var(--bg3); border: 1.5px solid var(--border);
    border-radius: var(--radius); color: var(--text);
    font-size: 14px; font-family: inherit;
    transition: border-color .2s;
    outline: none;
  }
  .form-group input:focus,
  .form-group select:focus { border-color: var(--cyan); }
  .form-group input::placeholder { color: var(--text3); }
  .form-group small { font-size: 12px; color: var(--text3); margin-top: 4px; display: block; }
  .form-group select option { background: var(--bg3); }
  .req { color: var(--red); }
  .opt { color: var(--text3); font-size: 11px; font-weight: 400; }

  /* Empty state (dipakai di topup & history) */
  .empty-state { text-align: center; padding: 48px 20px; }
  .empty-icon { font-size: 48px; margin-bottom: 12px; }
  .empty-state p { color: var(--text2); font-size: 15px; margin-bottom: 16px; }

  /* Produk: badge, kartu, grid (dipakai di index & topup) */
  .product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
  }
  .product-card {
    background: var(--bg2);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    padding: 14px;
    cursor: pointer;
    position: relative;
    transition: all .2s;
    text-decoration: none; color: var(--text);
  }
  .product-card:hover {
    border-color: var(--cyan);
    background: rgba(0,212,255,.05);
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
    text-decoration: none; color: var(--text);
  }
  .product-card.selected {
    border-color: var(--cyan);
    background: rgba(0,212,255,.1);
    box-shadow: 0 0 0 2px rgba(0,212,255,.3);
  }

  .p-badge {
    position: absolute; top: 8px; right: 8px;
    font-size: 9px; font-weight: 800; padding: 2px 6px;
    border-radius: 4px; letter-spacing: .5px;
  }
  .badge-hot        { background: #ef4444; color: #fff; }
  .badge-populer    { background: var(--gold); color: #000; }
  .badge-best-value { background: var(--green); color: #000; }
  .badge-sale       { background: #e879f9; color: #000; }
  .badge-promo      { background: var(--cyan); color: #000; }
  .badge-new        { background: var(--purple); color: #fff; }

  .p-icon { font-size: 24px; margin-bottom: 8px; }
  .p-body {}
  .p-name { font-size: 13px; font-weight: 700; margin-bottom: 3px; line-height: 1.3; }
  .p-cat  { font-size: 11px; color: var(--text3); }
  .p-diamonds { font-size: 12px; color: var(--cyan); margin: 4px 0; }
  .p-price { margin-top: 8px; }
  .price-old { font-size: 11px; color: var(--text3); text-decoration: line-through; margin-right: 4px; }
  .price-now { font-size: 15px; font-weight: 800; color: var(--gold); }

  /* ============================================================
     FOOTER
     ============================================================ */
  footer {
    background: var(--bg2);
    border-top: 1px solid var(--border);
    padding: 48px 0 0;
    margin-top: 48px;
  }
  .footer-inner {
    max-width: 1180px; margin: 0 auto; padding: 0 20px;
    display: grid; grid-template-columns: 2fr 1fr; gap: 60px;
  }
  .footer-brand .logo { margin-bottom: 14px; }
  .footer-brand > p { font-size: 13px; color: var(--text3); line-height: 1.7; margin-bottom: 20px; }
  .footer-about { margin-bottom: 20px; }
  .footer-about h4 { font-size: 13px; font-weight: 800; color: var(--text3); letter-spacing: 1px; margin-bottom: 10px; }
  .footer-about p { font-size: 13px; color: var(--text3); line-height: 1.75; }
  .footer-socials { display: flex; gap: 8px; flex-wrap: wrap; }
  .social-btn {
    display: inline-block; padding: 6px 12px;
    background: var(--bg3); border: 1px solid var(--border);
    border-radius: 6px; font-size: 12px; color: var(--text2);
    text-decoration: none; transition: all .2s;
  }
  .social-btn:hover { border-color: var(--cyan); color: var(--cyan); text-decoration: none; }
  .footer-col h4 { font-size: 13px; font-weight: 800; color: var(--text3); letter-spacing: 1px; margin-bottom: 14px; }
  .footer-col ul { list-style: none; }
  .footer-col ul li { margin-bottom: 10px; }
  .footer-col ul a { font-size: 13px; color: var(--text2); text-decoration: none; transition: color .2s; }
  .footer-col ul a:hover { color: var(--cyan); }
  .footer-bottom {
    max-width: 1180px; margin: 0 auto; padding: 20px 20px;
    border-top: 1px solid var(--border); margin-top: 32px;
    text-align: center;
  }
  .footer-bottom p { font-size: 12px; color: var(--text3); margin-bottom: 4px; }
  .footer-disclaimer { font-size: 11px; }

  /* ── Responsive (Navbar, Footer, Form, Produk) ── */
  @media (max-width: 1024px) {
    .footer-inner { grid-template-columns: 1fr 1fr; gap: 28px; }
  }

  @media (max-width: 768px) {
    #mainNav { padding: 0 16px; }
    .nav-links { display: none; flex-direction: column; gap: 4px; position: fixed; top: 60px; left: 0; right: 0; background: var(--bg2); border-bottom: 1px solid var(--border); padding: 16px; z-index: 99; }
    .nav-links.open { display: flex; }
    .nav-right { display: none; }
    .nav-right.open { display: flex; flex-direction: column; }
    .nav-toggle { display: flex; }

    .product-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); }
    .footer-inner { grid-template-columns: 1fr; gap: 24px; }
    .form-row-2, .form-row-3 { grid-template-columns: 1fr; }
  }

  @media (max-width: 480px) {
    .product-grid { grid-template-columns: repeat(2, 1fr); }
  }
  </style>
</head>
<body>
  <!-- Background Orbs -->
  <div class="orb orb1"></div>
  <div class="orb orb2"></div>
  <div class="orb orb3"></div>