<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ShopZone</title>
<style>
:root {
  --bg: #1a1a2e;
  --bg-alt: #10101f;
  --surface: rgba(255,255,255,.06);
  --surface-border: rgba(212,165,116,.18);
  --text: #f5f1e8;
  --text-muted: rgba(245,241,232,.72);
  --accent: #d4a574;
  --accent-alt: #6b4e71;
  --success: #2ecc71;
  --danger: #f56565;
}
*{box-sizing:border-box;margin:0;padding:0}
html{font-size:17px}
body{font-family:'Segoe UI',sans-serif;background:linear-gradient(135deg,var(--bg),#2d1b3d);color:var(--text);min-height:100vh;line-height:1.75}
nav{background:linear-gradient(90deg,var(--bg),#2d1b3d);color:var(--text);padding:0 28px;display:flex;align-items:center;justify-content:space-between;height:72px;position:sticky;top:0;z-index:100;box-shadow:0 12px 40px rgba(0,0,0,.35)}
.brand{font-size:1.85rem;font-weight:900;text-decoration:none;color:transparent;background:linear-gradient(90deg,var(--accent),var(--accent-alt));-webkit-background-clip:text;background-clip:text;letter-spacing:2px}
.brand span{color:var(--text)}
.nav-links{display:flex;align-items:center;gap:20px}
.nav-links a{color:var(--text);text-decoration:none;font-size:1rem;font-weight:600;transition:all .25s;position:relative;border:none;background:none;cursor:pointer}
.nav-links a:hover{color:var(--accent)}
.nav-links a.accent{color:var(--accent)}
.nav-links a.danger{color:var(--danger)}
.nav-links a.small{font-size:.92rem;color:var(--text-muted)}
.sidebar-header{padding:18px 20px;color:var(--accent);font-size:1.1rem;font-weight:800;border-bottom:1px solid rgba(255,255,255,.08)}
.sidebar-header span{color:var(--text)}
.sidebar-footer{padding:18px 20px;border-top:1px solid rgba(255,255,255,.08);margin-top:auto;display:flex;flex-direction:column;gap:10px}
.text-success{color:var(--success);font-weight:700}
.text-muted-alt{color:rgba(245,241,232,.72)}
.auth-note{text-align:center;margin-top:12px;font-size:.95rem;color:var(--text-muted)}
.cart-btn{background:linear-gradient(135deg,var(--accent),var(--accent-alt));color:#fff;padding:10px 18px;border-radius:28px;font-weight:700;font-size:0.95rem;letter-spacing:.02em}
.cart-btn:hover{opacity:.95}
.badge-pill{background:var(--accent-alt);color:#fff;border-radius:999px;padding:2px 8px;font-size:11px;font-weight:800;margin-left:6px}
.alert{padding:14px 20px;border-radius:14px;margin:18px auto;max-width:900px;font-size:14px;font-weight:700;display:flex;align-items:center;gap:12px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);color:var(--text)}
.alert.success{background:rgba(46,204,113,.14);border-color:rgba(46,204,113,.28);color:#9ae6b4}
.alert.error{background:rgba(245,101,101,.14);border-color:rgba(245,101,101,.28);color:#feb2b2}
.alert .close-x{margin-left:auto;cursor:pointer;font-size:18px;line-height:1}
.hero{background:linear-gradient(135deg,#1d1a3a,#2d1c47);color:var(--text);padding:72px 28px 64px;text-align:center;border-radius:0 0 38px 38px;box-shadow:0 18px 60px rgba(0,0,0,.4);margin-bottom:28px}
.hero h1{font-size:3.8rem;font-weight:900;margin-bottom:18px;line-height:1.05}
.hero h1 span{color:var(--accent)}
.hero p{color:var(--text-muted);font-size:1.05rem;margin-bottom:30px;max-width:760px;margin-left:auto;margin-right:auto;line-height:1.9}
.hero-btns{display:flex;gap:16px;justify-content:center;flex-wrap:wrap}
.btn{display:inline-block;padding:14px 24px;border-radius:999px;border:none;cursor:pointer;font-size:1rem;font-weight:800;text-decoration:none;transition:all .2s}
.btn:hover{opacity:.95;transform:translateY(-1px)}
.btn.disabled, .btn:disabled{background:#4a5568;color:#e2e8f0;cursor:not-allowed;opacity:.68;transform:none}
.btn-blue{background:linear-gradient(135deg,var(--accent),var(--accent-alt));color:#fff;box-shadow:0 14px 30px rgba(212,165,116,.18)}
.btn-admin{background:#38b2ac;color:#0f1720}
.btn-admin:hover{background:#2c7a7b}
.btn-outline{background:transparent;border:2px solid var(--accent);color:var(--accent)}
.btn-sm{padding:8px 18px;font-size:.92rem}
.btn-red{background:#e53e3e;color:#fff}
.btn-green{background:#38a169;color:#fff}
.btn-gray{background:#475569;color:#fff}
.empty-state{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:20px;padding:40px 30px;text-align:center;color:var(--text);box-shadow:0 18px 60px rgba(0,0,0,.2);margin-bottom:24px}
.empty-state-icon{font-size:3.5rem;margin-bottom:18px}
.empty-state-title{font-size:1.15rem;font-weight:700;margin-bottom:18px;color:var(--text)}
.button-row{display:flex;gap:16px;margin-top:18px;flex-wrap:wrap;align-items:center}
.button-row > *{flex:1;min-width:160px}
.receipt-note{font-size:1rem;font-weight:700;color:var(--text);margin-top:10px}
.receipt-message{text-align:center;margin-top:18px;color:var(--text-muted);font-size:.95rem}
.card-cat-muted{font-size:.92rem;color:var(--text-muted)}
.site-footer{background:linear-gradient(90deg,#1a1a2e,#2d1b3d);color:var(--text-muted);text-align:center;padding:20px;font-size:.95rem;margin-top:36px;border-top:1px solid rgba(255,255,255,.08)}
.section{padding:42px 28px;max-width:1200px;margin:0 auto}
.section-title{font-size:2rem;font-weight:900;color:var(--text);margin-bottom:24px;padding-bottom:10px;border-bottom:2px solid rgba(255,255,255,.12)}
.filter-bar{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:24px}
.filter-btn{padding:10px 18px;border-radius:999px;border:1.5px solid rgba(255,255,255,.12);background:rgba(255,255,255,.05);font-size:.95rem;font-weight:700;cursor:pointer;transition:all .2s;color:var(--text-muted)}
.filter-btn.active,.filter-btn:hover{background:linear-gradient(135deg,var(--accent),var(--accent-alt));color:#fff;border-color:transparent}
.products-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:24px}
.card{background:rgba(255,255,255,.06);border-radius:24px;overflow:hidden;box-shadow:0 22px 60px rgba(0,0,0,.18);transition:transform .25s,box-shadow .25s;position:relative;border:1px solid rgba(255,255,255,.08)}
.card:hover{transform:translateY(-6px);box-shadow:0 30px 80px rgba(0,0,0,.22)}
.card img{width:100%;height:210px;object-fit:cover}
.sale-tag{position:absolute;top:16px;left:16px;background:rgba(229,62,62,.95);color:#fff;font-size:11px;font-weight:800;padding:5px 12px;border-radius:999px;text-transform:uppercase;letter-spacing:.5px}
.card-body{padding:22px}
.card-cat{font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px}
.card-name{font-size:1.05rem;font-weight:800;margin-bottom:10px;color:var(--text)}
.card-desc{font-size:.95rem;color:rgba(245,241,232,.72);margin-bottom:14px;line-height:1.8}
.card-price{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.price-now{font-size:1.25rem;font-weight:800;color:var(--accent)}
.price-old{font-size:.95rem;color:rgba(245,241,232,.64);text-decoration:line-through}
.card form{display:inline}
.card .add-btn{width:100%;padding:12px;background:linear-gradient(135deg,var(--accent),var(--accent-alt));color:#fff;border:none;border-radius:16px;font-size:.98rem;font-weight:800;cursor:pointer;transition:all .25s}
.card .add-btn:hover{transform:translateY(-1px);background:linear-gradient(135deg,#c49d6e,#6f5582)}
.sub-bar{background:linear-gradient(135deg,#131025,#24193d);color:var(--text);padding:42px 28px;text-align:center;border-radius:24px;box-shadow:0 18px 60px rgba(0,0,0,.28);margin:32px 0}
.sub-bar h3{font-size:2rem;font-weight:900;margin-bottom:12px}
.sub-bar p{color:var(--text-muted);font-size:1rem;margin-bottom:18px}
.sub-form{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
.sub-form input{padding:14px 18px;border-radius:14px;border:none;font-size:1rem;width:320px;outline:none;background:rgba(255,255,255,.05);color:var(--text)}
.sub-form button{padding:14px 22px;background:linear-gradient(135deg,var(--accent),var(--accent-alt));color:#fff;border:none;border-radius:14px;font-size:1rem;font-weight:700;cursor:pointer}
.admin-wrap{display:flex;min-height:calc(100vh - 72px);background:linear-gradient(180deg,#0d0f1b,#090b16)}
.sidebar{width:260px;background:rgba(255,255,255,.04);flex-shrink:0;padding:22px 0;border-right:1px solid rgba(255,255,255,.08)}
.sidebar a{display:flex;align-items:center;gap:12px;padding:14px 22px;color:var(--text-muted);text-decoration:none;font-size:1rem;font-weight:600;border-left:4px solid transparent;transition:all .2s}
.sidebar a:hover{color:var(--text);background:rgba(255,255,255,.06)}
.sidebar a.act{color:var(--accent);background:rgba(212,165,116,.08);border-left-color:var(--accent)}
.amain{flex:1;padding:32px;overflow-y:auto;background:transparent;color:var(--text)}
.page-title{font-size:2.4rem;font-weight:900;color:var(--text);margin-bottom:28px}
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:28px}
.stat-card{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:20px;padding:24px;box-shadow:0 18px 50px rgba(0,0,0,.22)}
.stat-card .slabel{font-size:.85rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.15em;margin-bottom:8px}
.stat-card .sval{font-size:2.05rem;font-weight:900;color:var(--text)}
.stat-card .sbadge{font-size:.9rem;color:var(--success);margin-top:4px}
.table-card{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:20px;overflow:hidden;margin-bottom:24px}
table.at{width:100%;border-collapse:collapse}
table.at th{background:rgba(255,255,255,.04);padding:14px 18px;text-align:left;font-size:.95rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.12em}
table.at td{padding:14px 18px;border-bottom:1px solid rgba(255,255,255,.08);font-size:.98rem;color:var(--text)}
table.at tr:hover td{background:rgba(255,255,255,.04)}
table.at tr:last-child td{border:none}
.form-card{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:20px;padding:24px;margin-bottom:24px}
.form-card h3{font-size:1.05rem;font-weight:700;color:var(--text);margin-bottom:18px}
.frow{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:16px}
.frow2{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:16px}
.form-card label{display:block;font-size:.9rem;color:var(--text-muted);font-weight:700;margin-bottom:8px;text-transform:uppercase}
.form-card input,.form-card select,.form-card textarea{width:100%;padding:12px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:var(--text);font-size:.98rem;outline:none}
.form-card input:focus,.form-card select:focus{border-color:var(--accent)}
.form-label-alt{color:var(--text-muted);text-transform:none;font-size:.95rem;font-weight:700}
.dark-select,.dark-input{width:100%;padding:12px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:var(--text);font-size:.98rem;outline:none}
.dark-select:focus,.dark-input:focus{border-color:var(--accent)}
.inline-form{display:inline}
.alert-full{max-width:100%;margin-bottom:18px}
.alert-close{float:right;cursor:pointer}
.table-empty{padding:20px;text-align:center}
.table-empty-lg{padding:22px;text-align:center}
.compact-stats{grid-template-columns:repeat(2,1fr);max-width:420px}
.compact-stats-3{grid-template-columns:repeat(3,1fr);max-width:640px}
.compact-chart{height:180px}
.small-text{font-size:.92rem;color:var(--text-muted)}
.text-danger{color:var(--danger)}
.text-success{color:var(--success);font-weight:700}
.money-positive{color:var(--success);font-weight:700}
.form-card .chk-row{display:flex;align-items:center;gap:10px;margin-bottom:14px;color:var(--text-muted);font-size:.95rem}
.abadge{display:inline-block;padding:4px 10px;border-radius:999px;font-size:.85rem;font-weight:700}
.abadge.active{background:rgba(56,194,172,.18);color:#9ef0db}
.abadge.inactive{background:rgba(243,131,131,.18);color:#fda4af}
.abadge.sale{background:rgba(212,165,116,.18);color:#f7d18c}
.chart-bars{display:flex;align-items:flex-end;gap:8px;height:150px;margin-top:14px}
.bar-wrap{flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;height:100%;justify-content:flex-end}
.bar{width:100%;background:linear-gradient(180deg,#38b2ac,#1f6372);border-radius:6px 6px 0 0;min-height:4px}
.bar-label{font-size:.85rem;color:var(--text-muted);font-weight:600}
.bar-val{font-size:.85rem;color:var(--success);font-weight:700}
.img-thumb{width:52px;height:42px;object-fit:cover;border-radius:8px}
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:999;align-items:center;justify-content:center}
.modal-bg.open{display:flex}
.modal{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:20px;padding:28px;width:520px;max-width:95vw;max-height:90vh;overflow-y:auto}
.modal h3{font-size:1rem;font-weight:700;color:var(--text);margin-bottom:18px}
</style>
</head>
<body>
