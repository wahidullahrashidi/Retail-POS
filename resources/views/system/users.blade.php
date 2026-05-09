@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Spectral:ital,wght@0,400;0,600;0,700;1,400&family=Epilogue:wght@300;400;500;600;700&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════
   TOKENS
══════════════════════════════════════ */
:root {
    --bg:       #f0f1f7;
    --surface:  #ffffff;
    --s2:       #f5f6fb;
    --s3:       #eceef6;
    --border:   #dde0ee;
    --border2:  #c4c9de;
    --ink:      #16192a;
    --ink2:     #3e4268;
    --ink3:     #7c82a0;
    --ink4:     #bbc1d8;
    --blue:     #2e5fe8;
    --blue2:    #1f4ecc;
    --bdim:     rgba(46,95,232,.08);
    --bmid:     rgba(46,95,232,.16);
    --green:    #15803d;
    --gdim:     rgba(21,128,61,.09);
    --red:      #dc2626;
    --rdim:     rgba(220,38,38,.08);
    --amber:    #d97706;
    --adim:     rgba(217,119,6,.09);
    --violet:   #7c3aed;
    --vdim:     rgba(124,58,237,.09);
    --teal:     #0891b2;
    --tdim:     rgba(8,145,178,.09);
    --mono:     'Courier Prime', monospace;
    --body:     'Epilogue', sans-serif;
    --display:  'Spectral', serif;
    --r:        10px;
    --rsm:      6px;
    --rlg:      16px;
    --sh:       0 1px 3px rgba(0,0,0,.05), 0 1px 2px rgba(0,0,0,.03);
    --shmd:     0 4px 18px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.04);
    --shlg:     0 20px 56px rgba(0,0,0,.12), 0 6px 16px rgba(0,0,0,.06);
}

/* ══════════════════════════════════════
   BASE
══════════════════════════════════════ */
.um * { box-sizing: border-box; }
.um { font-family: var(--body); background: var(--bg); min-height: 100vh; color: var(--ink); }
[x-cloak] { display: none !important; }

/* ══════════════════════════════════════
   TOPBAR
══════════════════════════════════════ */
.um-top {
    background: var(--surface); border-bottom: 1px solid var(--border);
    height: 56px; display: flex; align-items: center; justify-content: space-between;
    padding: 0 1.75rem; position: sticky; top: 0; z-index: 80; box-shadow: var(--sh);
}
.um-title { font-family: var(--display); font-size: 22px; color: var(--ink); font-weight: 600; letter-spacing: -.3px; }
.um-title em { color: var(--blue); font-style: italic; }
.top-r { display: flex; align-items: center; gap: 8px; }

/* ══════════════════════════════════════
   BUTTONS
══════════════════════════════════════ */
.btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 15px; border-radius: var(--rsm); font-family: var(--body); font-size: 12.5px; font-weight: 600; border: none; cursor: pointer; transition: all .16s; white-space: nowrap; }
.btn-ghost   { background: var(--s2); border: 1px solid var(--border); color: var(--ink2); }
.btn-ghost:hover { background: var(--s3); color: var(--ink); }
.btn-primary { background: var(--blue); color: #fff; box-shadow: 0 2px 8px rgba(46,95,232,.28); }
.btn-primary:hover { background: var(--blue2); transform: translateY(-1px); box-shadow: 0 5px 16px rgba(46,95,232,.38); }
.btn-danger  { background: var(--rdim); border: 1px solid rgba(220,38,38,.2); color: var(--red); }
.btn-danger:hover { background: var(--red); color: #fff; }
.btn-amber   { background: var(--adim); border: 1px solid rgba(217,119,6,.2); color: var(--amber); }
.btn-amber:hover { background: var(--amber); color: #fff; }
.btn-teal    { background: var(--tdim); border: 1px solid rgba(8,145,178,.2); color: var(--teal); }
.btn-teal:hover { background: var(--teal); color: #fff; }
.btn-sm { padding: 5px 10px; font-size: 11.5px; }
.btn:active { transform: scale(.97); }
.btn:disabled { opacity: .4; cursor: not-allowed; transform: none !important; }

/* ══════════════════════════════════════
   STAT STRIP
══════════════════════════════════════ */
.stat-strip { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; padding: 1.2rem 1.75rem .75rem; }
.stat-tile { background: var(--surface); border: 1px solid var(--border); border-radius: var(--r); padding: 1rem 1.2rem; position: relative; overflow: hidden; transition: all .2s; cursor: default; }
.stat-tile:hover { box-shadow: var(--shmd); transform: translateY(-2px); border-color: var(--border2); }
.stat-tile::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--ac, var(--blue)); transform: scaleX(0); transform-origin: left; transition: transform .3s; }
.stat-tile:hover::before { transform: scaleX(1); }
.st-label { font-size: 10px; font-weight: 700; color: var(--ink3); text-transform: uppercase; letter-spacing: .1em; margin-bottom: 7px; display: flex; align-items: center; justify-content: space-between; }
.st-val   { font-family: var(--mono); font-size: 28px; font-weight: 700; color: var(--ink); line-height: 1; }
.st-sub   { font-size: 11px; color: var(--ink3); margin-top: 5px; }

/* ══════════════════════════════════════
   TOOLBAR
══════════════════════════════════════ */
.um-toolbar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; padding: .75rem 1.75rem .9rem; }
.search-box { position: relative; flex: 1; min-width: 200px; max-width: 320px; }
.search-box i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--ink3); font-size: 13px; pointer-events: none; }
.um-search { width: 100%; padding: 9px 14px 9px 34px; background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--rsm); font-family: var(--body); font-size: 13px; color: var(--ink); outline: none; transition: border .15s, box-shadow .15s; }
.um-search:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--bdim); background: #fff; }
.um-search::placeholder { color: var(--ink4); }
.f-sel { padding: 9px 12px; background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--rsm); font-family: var(--body); font-size: 12.5px; color: var(--ink2); outline: none; cursor: pointer; transition: border .15s; }
.f-sel:focus { border-color: var(--blue); }
.tab-strip { display: flex; gap: 4px; margin-left: auto; }
.tab-btn { padding: 7px 13px; border: 1px solid var(--border); border-radius: var(--rsm); background: var(--surface); font-family: var(--body); font-size: 12px; font-weight: 600; cursor: pointer; color: var(--ink3); transition: all .15s; }
.tab-btn.active { background: var(--blue); color: #fff; border-color: var(--blue); }

/* ══════════════════════════════════════
   GRID + DETAIL PANEL
══════════════════════════════════════ */
.um-main { display: grid; grid-template-columns: 1fr; gap: 0; padding: 0 1.75rem 2rem; transition: grid-template-columns .25s; align-items: start; }
.um-main.panel-open { grid-template-columns: 1fr 400px; gap: 1.25rem; }

/* ══════════════════════════════════════
   USER CARDS GRID
══════════════════════════════════════ */
.users-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1rem; }
.user-card {
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--rlg);
    overflow: hidden; transition: all .22s; cursor: pointer; position: relative;
}
.user-card:hover { border-color: var(--blue); box-shadow: var(--shmd); transform: translateY(-3px); }
.user-card.selected { border-color: var(--blue); box-shadow: 0 0 0 3px var(--bdim), var(--shmd); }
.user-card.inactive { opacity: .6; }

/* Card top accent by role */
.user-card-accent { height: 4px; }
.accent-admin    { background: linear-gradient(90deg, var(--violet), #9b4ef5); }
.accent-manager  { background: linear-gradient(90deg, var(--blue), #4f8ef7); }
.accent-cashier  { background: linear-gradient(90deg, var(--teal), #22b8d4); }

.uc-body { padding: 1.25rem; }
.uc-top { display: flex; align-items: center; gap: 12px; margin-bottom: 1rem; }

/* Avatar */
.uc-avatar {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-family: var(--display); font-size: 20px; font-weight: 700; color: #fff;
    flex-shrink: 0; position: relative;
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
}
.uc-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 14px; }
.uc-online { position: absolute; bottom: -2px; right: -2px; width: 12px; height: 12px; border-radius: 50%; border: 2px solid var(--surface); }
.uc-online.online  { background: var(--green); }
.uc-online.offline { background: var(--ink4); }

.uc-info { flex: 1; min-width: 0; }
.uc-name { font-weight: 700; font-size: 14px; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.uc-email { font-size: 11px; color: var(--ink3); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Role badge */
.role-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 99px; font-size: 10px; font-weight: 700; letter-spacing: .04em; margin-top: 5px; }
.role-admin   { background: var(--vdim); color: var(--violet); border: 1px solid rgba(124,58,237,.2); }
.role-manager { background: var(--bdim); color: var(--blue);   border: 1px solid var(--bmid); }
.role-cashier { background: var(--tdim); color: var(--teal);   border: 1px solid rgba(8,145,178,.2); }

/* Stats row */
.uc-stats { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; margin-top: .75rem; }
.uc-stat { background: var(--s2); border: 1px solid var(--border); border-radius: var(--rsm); padding: 7px 8px; text-align: center; }
.uc-stat-val { font-family: var(--mono); font-size: 14px; font-weight: 700; color: var(--ink); }
.uc-stat-label { font-size: 9px; color: var(--ink3); text-transform: uppercase; letter-spacing: .06em; margin-top: 2px; }

/* Card footer */
.uc-footer { padding: .75rem 1.25rem; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.uc-last-login { font-size: 11px; color: var(--ink3); display: flex; align-items: center; gap: 5px; }

/* pill */
.pill { display: inline-block; padding: 3px 9px; border-radius: 99px; font-size: 10px; font-weight: 700; letter-spacing: .04em; }
.pill-green  { background: var(--gdim); color: var(--green); border: 1px solid rgba(21,128,61,.2); }
.pill-red    { background: var(--rdim); color: var(--red);   border: 1px solid rgba(220,38,38,.2); }
.pill-amber  { background: var(--adim); color: var(--amber); border: 1px solid rgba(217,119,6,.2); }
.pill-blue   { background: var(--bdim); color: var(--blue);  border: 1px solid var(--bmid); }
.pill-violet { background: var(--vdim); color: var(--violet);border: 1px solid rgba(124,58,237,.2); }
.pill-gray   { background: var(--s3);   color: var(--ink3);  border: 1px solid var(--border); }
.pill-teal   { background: var(--tdim); color: var(--teal);  border: 1px solid rgba(8,145,178,.2); }

/* empty / loading */
.empty-state { text-align: center; padding: 4rem 2rem; color: var(--ink3); grid-column: 1/-1; }
.empty-state i { font-size: 36px; margin-bottom: 12px; display: block; color: var(--ink4); }
.empty-state p { font-size: 13px; line-height: 1.7; }
.loading-state { text-align: center; padding: 3rem; color: var(--ink3); grid-column: 1/-1; }

/* ══════════════════════════════════════
   DETAIL PANEL
══════════════════════════════════════ */
.detail-panel { background: var(--surface); border: 1px solid var(--border); border-radius: var(--r); box-shadow: var(--sh); display: flex; flex-direction: column; max-height: calc(100vh - 200px); position: sticky; top: 72px; overflow: hidden; animation: panelIn .2s cubic-bezier(.2,.8,.36,1); }
@keyframes panelIn { from { opacity: 0; transform: translateX(14px); } to { opacity: 1; transform: none; } }
.dp-head { padding: .9rem 1.25rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
.dp-head-label { font-size: 11px; font-weight: 700; color: var(--ink3); text-transform: uppercase; letter-spacing: .09em; }
.dp-close { background: none; border: none; cursor: pointer; color: var(--ink3); font-size: 16px; transition: color .15s; }
.dp-close:hover { color: var(--ink); }
.dp-body { flex: 1; overflow-y: auto; }
.dp-body::-webkit-scrollbar { width: 4px; }
.dp-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }
.dp-foot { padding: .9rem 1.25rem; border-top: 1px solid var(--border); display: flex; gap: 7px; flex-shrink: 0; flex-wrap: wrap; }

/* dp hero */
.dp-hero { padding: 1.5rem 1.25rem; border-bottom: 1px solid var(--border); text-align: center; }
.dp-av-wrap { position: relative; display: inline-block; margin-bottom: 10px; }
.dp-avatar { width: 72px; height: 72px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-family: var(--display); font-size: 26px; font-weight: 700; color: #fff; box-shadow: 0 4px 14px rgba(0,0,0,.15); }
.dp-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 18px; }
.dp-online { position: absolute; bottom: -2px; right: -2px; width: 14px; height: 14px; border-radius: 50%; border: 2px solid var(--surface); }
.dp-online.online  { background: var(--green); animation: pulse-dot 2.5s infinite; }
.dp-online.offline { background: var(--ink4); }
@keyframes pulse-dot { 0%,100%{box-shadow:0 0 0 0 rgba(21,128,61,.4)} 50%{box-shadow:0 0 0 5px rgba(21,128,61,0)} }
.dp-name { font-family: var(--display); font-size: 20px; color: var(--ink); font-weight: 600; margin-bottom: 3px; }
.dp-email { font-size: 12px; color: var(--ink3); margin-bottom: 8px; }

/* dp kpi strip */
.dp-kpi { display: grid; grid-template-columns: repeat(3,1fr); gap: 6px; padding: .85rem 1.25rem; border-bottom: 1px solid var(--border); }
.dp-kpi-item { background: var(--s2); border: 1px solid var(--border); border-radius: var(--rsm); padding: 8px; text-align: center; }
.dp-kpi-val { font-family: var(--mono); font-size: 15px; font-weight: 700; color: var(--ink); }
.dp-kpi-label { font-size: 9px; color: var(--ink3); text-transform: uppercase; letter-spacing: .06em; margin-top: 3px; }

/* dp section */
.dp-section { padding: .85rem 1.25rem; border-bottom: 1px solid var(--border); }
.dp-section:last-child { border-bottom: none; }
.dp-section-title { font-size: 10px; font-weight: 700; color: var(--ink3); text-transform: uppercase; letter-spacing: .1em; margin-bottom: .6rem; display: flex; align-items: center; gap: 6px; }
.dp-section-title i { color: var(--blue); }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 7px; }
.info-field { background: var(--s2); border: 1px solid var(--border); border-radius: var(--rsm); padding: 7px 10px; }
.info-field.full { grid-column: span 2; }
.if-label { font-size: 10px; color: var(--ink3); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 3px; }
.if-val { font-size: 12.5px; font-weight: 500; color: var(--ink); }
.if-val.mono { font-family: var(--mono); font-size: 12px; }

/* shift mini list */
.mini-shift { display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; background: var(--s2); border: 1px solid var(--border); border-radius: var(--rsm); margin-bottom: 5px; font-size: 12px; }
.ms-date { color: var(--ink2); font-family: var(--mono); font-size: 11px; }
.ms-sales { font-family: var(--mono); font-weight: 600; color: var(--blue); }

/* PIN display */
.pin-display { display: flex; justify-content: center; gap: 8px; padding: .5rem 0; }
.pin-dot { width: 14px; height: 14px; border-radius: 50%; background: var(--blue); }

/* ══════════════════════════════════════
   MODALS
══════════════════════════════════════ */
.modal-overlay { position: fixed; inset: 0; background: rgba(22,25,42,.45); backdrop-filter: blur(5px); z-index: 200; display: flex; align-items: center; justify-content: center; padding: 1rem; animation: fadeIn .15s ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.modal-card { background: var(--surface); border-radius: var(--rlg); box-shadow: var(--shlg); width: 100%; max-height: 92vh; display: flex; flex-direction: column; animation: slideUp .18s cubic-bezier(.2,.8,.36,1); }
@keyframes slideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }
.modal-sm { max-width: 440px; }
.modal-md { max-width: 600px; }
.modal-head { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
.modal-title { font-family: var(--display); font-size: 20px; color: var(--ink); font-weight: 600; }
.modal-close { background: none; border: none; cursor: pointer; color: var(--ink3); font-size: 18px; }
.modal-body { padding: 1.25rem 1.5rem; overflow-y: auto; flex: 1; }
.modal-foot { padding: 1rem 1.5rem; border-top: 1px solid var(--border); display: flex; gap: 8px; justify-content: flex-end; flex-shrink: 0; }

/* form */
.form-grid { display: grid; gap: .9rem; }
.form-2 { grid-template-columns: 1fr 1fr; }
.field-label { display: block; font-size: 11px; font-weight: 700; color: var(--ink2); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 5px; }
.field-req { color: var(--red); }
.field-input { width: 100%; padding: 9px 12px; background: var(--s2); border: 1.5px solid var(--border); border-radius: var(--rsm); font-family: var(--body); font-size: 13px; color: var(--ink); outline: none; transition: border .15s, box-shadow .15s; }
.field-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--bdim); background: #fff; }
.field-input::placeholder { color: var(--ink4); }
textarea.field-input { resize: vertical; min-height: 68px; }
select.field-input { cursor: pointer; }
.field-hint { font-size: 11px; color: var(--ink3); margin-top: 3px; }
.form-err { padding: 9px 12px; background: var(--rdim); border: 1px solid rgba(220,38,38,.2); border-radius: var(--rsm); font-size: 12px; color: var(--red); margin-top: .75rem; }
.form-section-title { font-size: 11px; font-weight: 700; color: var(--blue); text-transform: uppercase; letter-spacing: .08em; margin-bottom: .7rem; padding-bottom: 6px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 6px; }

/* photo upload */
.photo-upload { display: flex; align-items: center; gap: 1rem; margin-bottom: .5rem; }
.photo-preview { width: 64px; height: 64px; border-radius: 14px; background: var(--s3); border: 2px dashed var(--border2); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; font-size: 22px; cursor: pointer; transition: border-color .15s; }
.photo-preview:hover { border-color: var(--blue); }
.photo-preview img { width: 100%; height: 100%; object-fit: cover; }

/* PIN input */
.pin-input-row { display: flex; gap: 8px; justify-content: center; }
.pin-box { width: 52px; height: 52px; text-align: center; font-family: var(--mono); font-size: 20px; font-weight: 700; background: var(--s2); border: 1.5px solid var(--border); border-radius: var(--rsm); color: var(--ink); outline: none; transition: border .15s, box-shadow .15s; }
.pin-box:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--bdim); background: #fff; }

/* password strength */
.pw-strength { margin-top: 6px; }
.pw-bar { height: 4px; background: var(--s3); border-radius: 2px; overflow: hidden; margin-bottom: 4px; }
.pw-fill { height: 100%; border-radius: 2px; transition: width .3s, background .3s; }
.pw-label { font-size: 11px; }

/* warn box */
.warn-box { padding: 10px 14px; background: var(--adim); border: 1px solid rgba(217,119,6,.22); border-radius: var(--rsm); font-size: 12px; color: var(--amber); display: flex; gap: 8px; margin-bottom: 1rem; line-height: 1.5; }

/* permissions grid */
.perm-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
.perm-item { display: flex; align-items: center; gap: 8px; padding: 7px 10px; background: var(--s2); border: 1px solid var(--border); border-radius: var(--rsm); font-size: 12px; color: var(--ink2); cursor: pointer; transition: all .12s; }
.perm-item:hover { background: var(--bdim); border-color: var(--blue); }
.perm-item.active { background: var(--bdim); border-color: var(--blue); color: var(--blue); font-weight: 600; }
.perm-check { width: 16px; height: 16px; border-radius: 4px; border: 1.5px solid var(--border2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all .12s; }
.perm-item.active .perm-check { background: var(--blue); border-color: var(--blue); color: #fff; font-size: 9px; }
</style>
@endpush

@section('content')
<div class="um" x-data="usersPage()" x-init="init()">

{{-- ════ TOPBAR ════ --}}
<div class="um-top">
    <div class="um-title">Afghan <em>POS</em> — User Management</div>
    <div class="top-r">
        <button class="btn btn-primary" @click="openUserModal(null)">
            <i class="fas fa-user-plus"></i> Add User
        </button>
    </div>
</div>

{{-- ════ STATS ════ --}}
<div class="stat-strip">
    <div class="stat-tile" style="--ac:var(--blue)">
        <div class="st-label">Total Users <span style="color:var(--blue)"><i class="fas fa-users"></i></span></div>
        <div class="st-val">{{ $stats['total'] ?? 0 }}</div>
        <div class="st-sub">{{ $stats['active'] ?? 0 }} active accounts</div>
    </div>
    <div class="stat-tile" style="--ac:var(--violet)">
        <div class="st-label">Admins <span style="color:var(--violet)"><i class="fas fa-user-shield"></i></span></div>
        <div class="st-val" style="color:var(--violet)">{{ $stats['admins'] ?? 0 }}</div>
        <div class="st-sub">full system access</div>
    </div>
    <div class="stat-tile" style="--ac:var(--teal)">
        <div class="st-label">Managers <span style="color:var(--teal)"><i class="fas fa-user-tie"></i></span></div>
        <div class="st-val" style="color:var(--teal)">{{ $stats['managers'] ?? 0 }}</div>
        <div class="st-sub">inventory + reports</div>
    </div>
    <div class="stat-tile" style="--ac:var(--green)">
        <div class="st-label">Cashiers <span style="color:var(--green)"><i class="fas fa-cash-register"></i></span></div>
        <div class="st-val" style="color:var(--green)">{{ $stats['cashiers'] ?? 0 }}</div>
        <div class="st-sub">POS operations</div>
    </div>
</div>

{{-- ════ TOOLBAR ════ --}}
<div class="um-toolbar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input class="um-search" type="text" x-model="search"
               @input.debounce.350ms="loadUsers()"
               placeholder="Name, email…">
    </div>
    <select class="f-sel" x-model="filterRole" @change="loadUsers()">
        <option value="">All Roles</option>
        @foreach($roles ?? [] as $role)
            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
        @endforeach
    </select>
    <div class="tab-strip">
        <button type="button" class="tab-btn" :class="tab==='all'?'active':''"      @click="tab='all';loadUsers()">All</button>
        <button type="button" class="tab-btn" :class="tab==='active'?'active':''"   @click="tab='active';loadUsers()">Active</button>
        <button type="button" class="tab-btn" :class="tab==='inactive'?'active':''" @click="tab='inactive';loadUsers()">Inactive</button>
    </div>
</div>

{{-- ════ MAIN ════ --}}
<div class="um-main" :class="selected ? 'panel-open' : ''">

    {{-- USERS GRID --}}
    <div>
        {{-- Loading --}}
        <div class="users-grid" x-show="loading">
            <div class="loading-state"><i class="fas fa-spinner fa-spin" style="font-size:20px"></i></div>
        </div>

        <div class="users-grid" x-show="!loading">
            <div class="empty-state" x-show="users.length===0">
                <i class="fas fa-users-slash"></i>
                <p>No users found.<br>Add your first user to get started.</p>
            </div>

            <template x-for="u in users" :key="u.id">
                <div class="user-card" :class="[selected?.id===u.id?'selected':'', u.is_active?'':'inactive']"
                     @click="openDetail(u)">

                    {{-- Role color accent --}}
                    <div class="user-card-accent"
                         :class="u.role_name==='admin'?'accent-admin':u.role_name==='manager'?'accent-manager':'accent-cashier'">
                    </div>

                    <div class="uc-body">
                        <div class="uc-top">
                            <div class="uc-avatar" :style="`background:${roleColor(u.role_name)}`">
                                <template x-if="u.photo">
                                    <img :src="'/storage/' + u.photo" :alt="u.name">
                                </template>
                                <template x-if="!u.photo">
                                    <span x-text="initials(u.name)"></span>
                                </template>
                                <span class="uc-online" :class="u.is_active?'online':'offline'"></span>
                            </div>
                            <div class="uc-info">
                                <div class="uc-name" x-text="u.name"></div>
                                <div class="uc-email" x-text="u.email"></div>
                                <div>
                                    <span class="role-badge"
                                          :class="u.role_name==='admin'?'role-admin':u.role_name==='manager'?'role-manager':'role-cashier'">
                                        <i :class="u.role_name==='admin'?'fas fa-shield-halved':u.role_name==='manager'?'fas fa-user-tie':'fas fa-cash-register'"></i>
                                        <span x-text="u.role_display"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="uc-stats">
                            <div class="uc-stat">
                                <div class="uc-stat-val" x-text="u.sale_count || 0"></div>
                                <div class="uc-stat-label">Sales</div>
                            </div>
                            <div class="uc-stat">
                                <div class="uc-stat-val" x-text="u.shift_count || 0"></div>
                                <div class="uc-stat-label">Shifts</div>
                            </div>
                            <div class="uc-stat">
                                <div class="uc-stat-val" style="font-size:11px" x-text="u.total_sales ? 'Af ' + fmtK(u.total_sales) : '—'"></div>
                                <div class="uc-stat-label">Revenue</div>
                            </div>
                        </div>
                    </div>

                    <div class="uc-footer">
                        <div class="uc-last-login">
                            <i class="fas fa-clock"></i>
                            <span x-text="u.last_login ? u.last_login : 'Never logged in'"></span>
                        </div>
                        <span class="pill" :class="u.is_active?'pill-green':'pill-gray'"
                              x-text="u.is_active?'Active':'Inactive'"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ════ DETAIL PANEL ════ --}}
    <div class="detail-panel" x-show="selected" x-cloak>
        <div class="dp-head">
            <span class="dp-head-label">User Detail</span>
            <button class="dp-close" @click="selected=null"><i class="fas fa-times"></i></button>
        </div>
        <div class="dp-body">

            {{-- Hero --}}
            <div class="dp-hero">
                <div class="dp-av-wrap">
                    <div class="dp-avatar" :style="`background:${roleColor(selected?.role_name)}`">
                        <template x-if="selected?.photo">
                            <img :src="'/storage/' + selected.photo" :alt="selected.name">
                        </template>
                        <template x-if="!selected?.photo">
                            <span x-text="initials(selected?.name)"></span>
                        </template>
                    </div>
                    <span class="dp-online" :class="selected?.is_active?'online':'offline'"></span>
                </div>
                <div class="dp-name" x-text="selected?.name"></div>
                <div class="dp-email" x-text="selected?.email"></div>
                <div>
                    <span class="role-badge"
                          :class="selected?.role_name==='admin'?'role-admin':selected?.role_name==='manager'?'role-manager':'role-cashier'">
                        <i :class="selected?.role_name==='admin'?'fas fa-shield-halved':selected?.role_name==='manager'?'fas fa-user-tie':'fas fa-cash-register'"></i>
                        <span x-text="selected?.role_display"></span>
                    </span>
                    <span class="pill" :class="selected?.is_active?'pill-green':'pill-gray'" style="margin-left:6px"
                          x-text="selected?.is_active?'Active':'Inactive'"></span>
                </div>
            </div>

            {{-- KPIs --}}
            <div class="dp-kpi">
                <div class="dp-kpi-item">
                    <div class="dp-kpi-val" x-text="selected?.sale_count || 0"></div>
                    <div class="dp-kpi-label">Total Sales</div>
                </div>
                <div class="dp-kpi-item">
                    <div class="dp-kpi-val" x-text="selected?.shift_count || 0"></div>
                    <div class="dp-kpi-label">Shifts</div>
                </div>
                <div class="dp-kpi-item">
                    <div class="dp-kpi-val" style="font-size:12px" x-text="selected?.total_sales ? 'Af ' + fmtK(selected.total_sales) : '—'"></div>
                    <div class="dp-kpi-label">Revenue</div>
                </div>
            </div>

            {{-- Info --}}
            <div class="dp-section">
                <div class="dp-section-title"><i class="fas fa-id-card"></i> Account Info</div>
                <div class="info-grid">
                    <div class="info-field">
                        <div class="if-label">Full Name</div>
                        <div class="if-val" x-text="selected?.name"></div>
                    </div>
                    <div class="info-field">
                        <div class="if-label">Role</div>
                        <div class="if-val" x-text="selected?.role_display"></div>
                    </div>
                    <div class="info-field full">
                        <div class="if-label">Email</div>
                        <div class="if-val mono" x-text="selected?.email"></div>
                    </div>
                    <div class="info-field">
                        <div class="if-label">PIN Code</div>
                        <div class="pin-display" x-show="selected?.has_pin">
                            <template x-for="i in 4" :key="i">
                                <div class="pin-dot"></div>
                            </template>
                        </div>
                        <div class="if-val" x-show="!selected?.has_pin" style="color:var(--ink3)">Not set</div>
                    </div>
                    <div class="info-field">
                        <div class="if-label">Last Login</div>
                        <div class="if-val mono" x-text="selected?.last_login || 'Never'"></div>
                    </div>
                    <div class="info-field">
                        <div class="if-label">Member Since</div>
                        <div class="if-val mono" x-text="selected?.created_at"></div>
                    </div>
                </div>
            </div>

            {{-- Permissions --}}
            <div class="dp-section">
                <div class="dp-section-title"><i class="fas fa-lock"></i> Permissions</div>
                <div class="perm-grid">
                    <template x-for="perm in (selected?.permissions || [])" :key="perm">
                        <div class="perm-item active">
                            <div class="perm-check"><i class="fas fa-check"></i></div>
                            <span x-text="perm"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Recent shifts --}}
            <div class="dp-section">
                <div class="dp-section-title"><i class="fas fa-clock-rotate-left"></i> Recent Shifts</div>
                <div x-show="detailLoading" style="text-align:center;padding:1rem;color:var(--ink3);font-size:12px">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div x-show="!detailLoading">
                    <div x-show="recentShifts.length===0" style="text-align:center;padding:.75rem;color:var(--ink4);font-size:12px">
                        No shift history yet.
                    </div>
                    <template x-for="s in recentShifts" :key="s.id">
                        <div class="mini-shift">
                            <div>
                                <div class="ms-date" x-text="s.opened_at"></div>
                                <div style="font-size:10px;color:var(--ink3)" x-text="s.duration"></div>
                            </div>
                            <div style="text-align:right">
                                <div class="ms-sales" x-text="'Af ' + fmt(s.total_sales)"></div>
                                <span class="pill" :class="s.is_closed?'pill-blue':'pill-green'"
                                      x-text="s.is_closed?'Closed':'Active'" style="font-size:9px"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>
        <div class="dp-foot">
            <button type="button" class="btn btn-ghost" style="flex:1" @click="openUserModal(selected)">
                <i class="fas fa-pen"></i> Edit
            </button>
            <button type="button" class="btn btn-amber" @click="openPasswordModal(selected)">
                <i class="fas fa-key"></i> Reset PW
            </button>
            <button type="button" class="btn btn-danger" @click="toggleUser(selected)">
                <i class="fas fa-power-off"></i>
                <span x-text="selected?.is_active?'Deactivate':'Activate'"></span>
            </button>
        </div>
    </div>

</div>{{-- /um-main --}}

{{-- ════════════════════════════════════════
     MODAL: ADD / EDIT USER
════════════════════════════════════════ --}}
<div class="modal-overlay" x-show="showUserModal" x-cloak @click.self="showUserModal=false">
    <div class="modal-card modal-md">
        <div class="modal-head">
            <div class="modal-title" x-text="editingUser ? 'Edit User' : 'New User'"></div>
            <button class="modal-close" @click="showUserModal=false"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">

            {{-- Photo --}}
            <div class="form-section-title"><i class="fas fa-image"></i> Profile Photo</div>
            <div class="photo-upload" style="margin-bottom:1rem">
                <div class="photo-preview" @click="$refs.photoInput.click()">
                    <template x-if="photoPreview">
                        <img :src="photoPreview" alt="preview">
                    </template>
                    <template x-if="!photoPreview && uf.photo">
                        <img :src="'/storage/' + uf.photo" alt="photo">
                    </template>
                    <template x-if="!photoPreview && !uf.photo">
                        <span>📷</span>
                    </template>
                </div>
                <div>
                    <button type="button" class="btn btn-ghost btn-sm" @click="$refs.photoInput.click()">
                        <i class="fas fa-upload"></i> Upload Photo
                    </button>
                    <input type="file" x-ref="photoInput" accept="image/*" class="hidden"
                           style="display:none" @change="previewPhoto($event)">
                    <div style="font-size:11px;color:var(--ink3);margin-top:5px">JPG, PNG up to 2MB</div>
                </div>
            </div>

            {{-- Basic info --}}
            <div class="form-section-title"><i class="fas fa-user"></i> Basic Info</div>
            <div class="form-grid form-2" style="margin-bottom:1rem">
                <div>
                    <label class="field-label">Full Name <span class="field-req">*</span></label>
                    <input type="text" class="field-input" x-model="uf.name" placeholder="Full name">
                </div>
                <div>
                    <label class="field-label">Email <span class="field-req">*</span></label>
                    <input type="email" class="field-input" x-model="uf.email" placeholder="email@example.com">
                </div>
                <div>
                    <label class="field-label">Role <span class="field-req">*</span></label>
                    <select class="field-input" x-model="uf.role_id" @change="updatePermissionsFromRole()">
                        <option value="">Select role</option>
                        @foreach($roles ?? [] as $role)
                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-show="!editingUser">
                    <label class="field-label">Password <span class="field-req">*</span></label>
                    <input type="password" class="field-input" x-model="uf.password"
                           @input="checkPwStrength()"
                           placeholder="Min 8 characters">
                    <div class="pw-strength" x-show="uf.password">
                        <div class="pw-bar">
                            <div class="pw-fill" :style="`width:${pwStrength.pct}%;background:${pwStrength.color}`"></div>
                        </div>
                        <span class="pw-label" :style="`color:${pwStrength.color}`" x-text="pwStrength.label"></span>
                    </div>
                </div>
            </div>

            {{-- PIN --}}
            <div class="form-section-title"><i class="fas fa-hashtag"></i> Cashier PIN Code</div>
            <div style="margin-bottom:1rem">
                <div style="font-size:12px;color:var(--ink3);margin-bottom:.75rem">4-digit PIN for quick login at POS terminal</div>
                <div class="pin-input-row">
                    <input class="pin-box" type="password" x-model="pinDigits[0]" maxlength="1" inputmode="numeric"
                           @input="pinNext($event, 0)" @keydown.backspace="pinBack($event, 0)">
                    <input class="pin-box" type="password" x-model="pinDigits[1]" maxlength="1" inputmode="numeric"
                           @input="pinNext($event, 1)" @keydown.backspace="pinBack($event, 1)" id="pin-1">
                    <input class="pin-box" type="password" x-model="pinDigits[2]" maxlength="1" inputmode="numeric"
                           @input="pinNext($event, 2)" @keydown.backspace="pinBack($event, 2)" id="pin-2">
                    <input class="pin-box" type="password" x-model="pinDigits[3]" maxlength="1" inputmode="numeric"
                           @keydown.backspace="pinBack($event, 3)" id="pin-3">
                </div>
                <div style="font-size:11px;color:var(--ink3);text-align:center;margin-top:6px">Leave blank to keep existing PIN</div>
            </div>

            {{-- Permissions --}}
            <div class="form-section-title"><i class="fas fa-lock"></i> Permissions</div>
            <div class="perm-grid" style="margin-bottom:.5rem">
                <template x-for="perm in allPermissions" :key="perm.key">
                    <div class="perm-item" :class="uf.permissions.includes(perm.key)?'active':''"
                         @click="togglePerm(perm.key)">
                        <div class="perm-check">
                            <i class="fas fa-check" x-show="uf.permissions.includes(perm.key)"></i>
                        </div>
                        <span x-text="perm.label"></span>
                    </div>
                </template>
            </div>

            <div class="form-err" x-show="formError" x-text="formError" x-cloak></div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-ghost" @click="showUserModal=false">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveUser()" :disabled="saving">
                <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                <span x-text="saving?'Saving…':(editingUser?'Update User':'Create User')"></span>
            </button>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     MODAL: RESET PASSWORD
════════════════════════════════════════ --}}
<div class="modal-overlay" x-show="showPasswordModal" x-cloak @click.self="showPasswordModal=false">
    <div class="modal-card modal-sm">
        <div class="modal-head">
            <div class="modal-title">Reset Password</div>
            <button class="modal-close" @click="showPasswordModal=false"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="warn-box">
                <i class="fas fa-triangle-exclamation" style="flex-shrink:0;margin-top:1px"></i>
                <div>You are resetting the password for <strong x-text="passwordTarget?.name"></strong>. They will need to use this new password on their next login.</div>
            </div>
            <div class="form-grid">
                <div>
                    <label class="field-label">New Password <span class="field-req">*</span></label>
                    <input type="password" class="field-input" x-model="newPassword"
                           @input="checkNewPwStrength()"
                           placeholder="Min 8 characters">
                    <div class="pw-strength" x-show="newPassword">
                        <div class="pw-bar">
                            <div class="pw-fill" :style="`width:${newPwStrength.pct}%;background:${newPwStrength.color}`"></div>
                        </div>
                        <span class="pw-label" :style="`color:${newPwStrength.color}`" x-text="newPwStrength.label"></span>
                    </div>
                </div>
                <div>
                    <label class="field-label">Confirm Password <span class="field-req">*</span></label>
                    <input type="password" class="field-input" x-model="confirmPassword" placeholder="Re-enter password">
                </div>
            </div>
            <div class="form-err" x-show="pwError" x-text="pwError" x-cloak></div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-ghost" @click="showPasswordModal=false">Cancel</button>
            <button type="button" class="btn btn-primary" @click="savePassword()" :disabled="saving">
                <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                <span x-text="saving?'Saving…':'Reset Password'"></span>
            </button>
        </div>
    </div>
</div>

</div>{{-- /um --}}
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
Alpine.data('usersPage', () => ({

    /* list */
    users:       [],
    loading:     true,
    search:      '',
    filterRole:  '',
    tab:         'all',

    /* detail */
    selected:      null,
    recentShifts:  [],
    detailLoading: false,

    /* user modal */
    showUserModal: false,
    editingUser:   null,
    uf: { name:'', email:'', role_id:'', password:'', permissions:[], photo:'' },
    pinDigits:    ['','','',''],
    photoPreview: null,
    photoFile:    null,
    pwStrength:   { pct:0, color:'var(--ink4)', label:'' },
    formError:    '',
    saving:       false,

    /* password modal */
    showPasswordModal: false,
    passwordTarget:    null,
    newPassword:       '',
    confirmPassword:   '',
    newPwStrength:     { pct:0, color:'var(--ink4)', label:'' },
    pwError:           '',

    /* roles from server */
    rolesData: @json($roles ?? []),

    /* all permissions */
    allPermissions: [
        { key: 'pos.sale',        label: 'Process Sales' },
        { key: 'pos.return',      label: 'Process Returns' },
        { key: 'pos.hold',        label: 'Hold Sales' },
        { key: 'inventory.view',  label: 'View Inventory' },
        { key: 'inventory.edit',  label: 'Edit Inventory' },
        { key: 'customers.view',  label: 'View Customers' },
        { key: 'customers.edit',  label: 'Edit Customers' },
        { key: 'reports.view',    label: 'View Reports' },
        { key: 'loans.manage',    label: 'Manage Loans' },
        { key: 'suppliers.view',  label: 'View Suppliers' },
        { key: 'users.manage',    label: 'Manage Users' },
        { key: 'settings.access', label: 'System Settings' },
    ],

    /* urls */
    urls: {
        base: '{{ request()->getBaseUrl() }}',
        list: '{{ request()->getBaseUrl() }}/pos/users',
        store: '{{ request()->getBaseUrl() }}/pos/users/store',
        detail: '{{ request()->getBaseUrl() }}/pos/users',
        toggle: '{{ request()->getBaseUrl() }}/pos/users',
        password: '{{ request()->getBaseUrl() }}/pos/users/password',
        csrf: document.querySelector('meta[name=csrf-token]').content,
    },

    /* ── Init ── */
    async init() {
        await this.loadUsers();
    },

    /* ── Load users ── */
    async loadUsers() {
        this.loading = true;
        try {
            const p = new URLSearchParams({ q: this.search, role: this.filterRole, tab: this.tab });
            const r = await fetch(this.urls.list + '?' + p, {
                headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if (!r.ok) {
                const err = await r.text();
                throw new Error(`Failed to load users: ${r.status} ${err}`);
            }
            this.users = await r.json();
        } catch(e) { console.error(e); this.users = []; }
        finally { this.loading = false; }
    },

    /* ── Detail panel ── */
    async openDetail(u) {
        this.selected      = u;
        this.recentShifts  = [];
        this.detailLoading = true;
        try {
            const r = await fetch(`${this.urls.detail}/${u.id}/detail`, {
                headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            const d = await r.json();
            this.recentShifts = d.shifts;
            this.selected     = { ...this.selected, ...d.user };
        } catch(e) { console.error(e); }
        finally { this.detailLoading = false; }
    },

    /* ── User modal ── */
    openUserModal(u) {
        this.editingUser  = u;
        this.photoPreview = null;
        this.photoFile    = null;
        this.pinDigits    = ['','','',''];
        this.formError    = '';
        this.pwStrength   = { pct:0, color:'var(--ink4)', label:'' };

        if (u) {
            this.uf = {
                name:        u.name,
                email:       u.email,
                role_id:     u.role_id,
                password:    '',
                permissions: u.permissions || [],
                photo:       u.photo || '',
            };
        } else {
            this.uf = { name:'', email:'', role_id:'', password:'', permissions:[], photo:'' };
        }
        this.showUserModal = true;
    },

    previewPhoto(e) {
        const file = e.target.files[0];
        if (!file) return;
        this.photoFile    = file;
        this.photoPreview = URL.createObjectURL(file);
    },

    updatePermissionsFromRole() {
        const role = this.rolesData.find(r => r.id == this.uf.role_id);
        if (role?.permissions) {
            try {
                this.uf.permissions = typeof role.permissions === 'string'
                    ? JSON.parse(role.permissions)
                    : role.permissions;
            } catch(e) { this.uf.permissions = []; }
        }
    },

    togglePerm(key) {
        const idx = this.uf.permissions.indexOf(key);
        if (idx === -1) this.uf.permissions.push(key);
        else            this.uf.permissions.splice(idx, 1);
    },

    checkPwStrength() { this.pwStrength    = this.calcStrength(this.uf.password); },
    checkNewPwStrength() { this.newPwStrength = this.calcStrength(this.newPassword); },

    calcStrength(pw) {
        if (!pw) return { pct:0, color:'var(--ink4)', label:'' };
        let score = 0;
        if (pw.length >= 8)  score++;
        if (pw.length >= 12) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[0-9]/.test(pw)) score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;
        const map = [
            { pct:20, color:'var(--red)',   label:'Very Weak' },
            { pct:40, color:'var(--red)',   label:'Weak' },
            { pct:60, color:'var(--amber)', label:'Fair' },
            { pct:80, color:'var(--green)', label:'Strong' },
            { pct:100,color:'var(--green)', label:'Very Strong' },
        ];
        return map[score - 1] || map[0];
    },

    pinNext(e, idx) {
        if (e.target.value.length === 1 && idx < 3) {
            document.getElementById(`pin-${idx + 1}`)?.focus();
        }
    },
    pinBack(e, idx) {
        if (e.key === 'Backspace' && !this.pinDigits[idx] && idx > 0) {
            document.getElementById(`pin-${idx - 1}`)?.focus();
        }
    },

    async saveUser() {
        if (!this.uf.name.trim())  { this.formError = 'Name is required.';  return; }
        if (!this.uf.email.trim()) { this.formError = 'Email is required.'; return; }
        if (!this.uf.role_id)      { this.formError = 'Role is required.';  return; }
        if (!this.editingUser && !this.uf.password) { this.formError = 'Password is required for new users.'; return; }

        this.saving = true; this.formError = '';

        try {
            const formData = new FormData();
            formData.append('name',        this.uf.name);
            formData.append('email',       this.uf.email);
            formData.append('role_id',     this.uf.role_id);
            formData.append('permissions', JSON.stringify(this.uf.permissions));
            if (this.uf.password)  formData.append('password', this.uf.password);
            if (this.editingUser)  formData.append('user_id', this.editingUser.id);
            if (this.photoFile)    formData.append('photo', this.photoFile);

            // PIN
            const pin = this.pinDigits.join('');
            if (pin.length === 4) formData.append('pin_code', pin);

            formData.append('_token', this.urls.csrf);

            const r = await fetch(this.urls.store, { method: 'POST', body: formData, credentials: 'same-origin' });
            const d = await r.json();

            if (d.success) {
                this.showUserModal = false;
                this.loadUsers();
                if (this.selected?.id === this.editingUser?.id) {
                    this.selected = { ...this.selected, ...d.user };
                }
            } else {
                this.formError = d.message ?? 'Failed to save.';
            }
        } catch(e) { this.formError = 'Network error.'; }
        finally { this.saving = false; }
    },

    /* ── Password reset ── */
    openPasswordModal(u) {
        this.passwordTarget  = u;
        this.newPassword     = '';
        this.confirmPassword = '';
        this.pwError         = '';
        this.newPwStrength   = { pct:0, color:'var(--ink4)', label:'' };
        this.showPasswordModal = true;
    },

    async savePassword() {
        if (!this.newPassword)                        { this.pwError = 'New password is required.'; return; }
        if (this.newPassword.length < 8)              { this.pwError = 'Password must be at least 8 characters.'; return; }
        if (this.newPassword !== this.confirmPassword){ this.pwError = 'Passwords do not match.'; return; }

        this.saving = true; this.pwError = '';
        try {
            const r = await fetch(this.urls.password, {
                method:  'POST',
                headers: { 'Content-Type':'application/json','X-CSRF-TOKEN': this.urls.csrf },
                body:    JSON.stringify({ user_id: this.passwordTarget.id, password: this.newPassword }),
                credentials: 'same-origin'
            });
            const d = await r.json();
            if (d.success) { this.showPasswordModal = false; }
            else            { this.pwError = d.message ?? 'Failed.'; }
        } catch(e) { this.pwError = 'Network error.'; }
        finally { this.saving = false; }
    },

    /* ── Toggle active ── */
    async toggleUser(u) {
        if (!confirm(`${u.is_active?'Deactivate':'Activate'} ${u.name}?`)) return;
        await fetch(`${this.urls.toggle}/${u.id}/toggle`, {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': this.urls.csrf },
            credentials: 'same-origin'
        });
        this.loadUsers();
        if (this.selected?.id === u.id) this.selected = null;
    },

    /* ── Helpers ── */
    initials(name) {
        if (!name) return '?';
        return name.trim().split(' ').map(w => w[0]).join('').toUpperCase().slice(0,2);
    },
    roleColor(role) {
        const c = { admin:'#7c3aed', manager:'#2e5fe8', cashier:'#0891b2' };
        return c[role] ?? '#6b7280';
    },
    fmt(n)  { return Number(n||0).toLocaleString('en-US',{minimumFractionDigits:0,maximumFractionDigits:0}); },
    fmtK(n) { return n >= 1000 ? (n/1000).toFixed(1)+'k' : this.fmt(n); },
}));
});
</script>
@endpush
