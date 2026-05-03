@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Literata:ital,wght@0,300;0,400;0,500;1,300&family=Azeret+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════
   TOKENS
══════════════════════════════════════ */
:root {
    --bg:      #f0f2f8;
    --surface: #ffffff;
    --s2:      #f5f6fb;
    --s3:      #eceff6;
    --border:  #dde0ed;
    --border2: #c3c8dc;
    --ink:     #15182a;
    --ink2:    #3d4168;
    --ink3:    #7b82a0;
    --ink4:    #bac0d6;
    --blue:    #2f5de8;
    --blue2:   #1f4ccc;
    --bdim:    rgba(47,93,232,.08);
    --bmid:    rgba(47,93,232,.16);
    --green:   #15803d;
    --gdim:    rgba(21,128,61,.09);
    --red:     #dc2626;
    --rdim:    rgba(220,38,38,.08);
    --amber:   #d97706;
    --adim:    rgba(217,119,6,.09);
    --teal:    #0891b2;
    --tdim:    rgba(8,145,178,.09);
    --violet:  #7c3aed;
    --vdim:    rgba(124,58,237,.09);
    --mono:    'Azeret Mono', monospace;
    --body:    'Syne', sans-serif;
    --serif:   'Literata', serif;
    --r:       10px;
    --rsm:     6px;
    --rlg:     16px;
    --sh:      0 1px 3px rgba(0,0,0,.05), 0 1px 2px rgba(0,0,0,.03);
    --shmd:    0 4px 18px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.04);
    --shlg:    0 20px 56px rgba(0,0,0,.12), 0 6px 16px rgba(0,0,0,.06);
}

/* ══════════════════════════════════════
   BASE
══════════════════════════════════════ */
.st * { box-sizing: border-box; }
.st { font-family: var(--body); background: var(--bg); min-height: 100vh; color: var(--ink); }
[x-cloak] { display: none !important; }

/* ══════════════════════════════════════
   TOPBAR
══════════════════════════════════════ */
.st-top {
    background: var(--surface); border-bottom: 1px solid var(--border);
    height: 56px; display: flex; align-items: center; justify-content: space-between;
    padding: 0 1.75rem; position: sticky; top: 0; z-index: 80; box-shadow: var(--sh);
}
.st-title { font-size: 20px; font-weight: 700; color: var(--ink); letter-spacing: -.3px; }
.st-title em { color: var(--blue); font-style: italic; font-family: var(--serif); }

/* ══════════════════════════════════════
   BUTTONS
══════════════════════════════════════ */
.btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 15px; border-radius: var(--rsm); font-family: var(--body); font-size: 12.5px; font-weight: 600; border: none; cursor: pointer; transition: all .16s; white-space: nowrap; }
.btn-ghost   { background: var(--s2); border: 1px solid var(--border); color: var(--ink2); }
.btn-ghost:hover { background: var(--s3); color: var(--ink); }
.btn-primary { background: var(--blue); color: #fff; box-shadow: 0 2px 8px rgba(47,93,232,.28); }
.btn-primary:hover { background: var(--blue2); transform: translateY(-1px); }
.btn-danger  { background: var(--rdim); border: 1px solid rgba(220,38,38,.2); color: var(--red); }
.btn-danger:hover { background: var(--red); color: #fff; }
.btn-green   { background: var(--gdim); border: 1px solid rgba(21,128,61,.2); color: var(--green); }
.btn-green:hover { background: var(--green); color: #fff; }
.btn-teal    { background: var(--tdim); border: 1px solid rgba(8,145,178,.2); color: var(--teal); }
.btn-teal:hover { background: var(--teal); color: #fff; }
.btn-sm { padding: 5px 10px; font-size: 11.5px; }
.btn:active { transform: scale(.97); }
.btn:disabled { opacity: .4; cursor: not-allowed; transform: none !important; }

/* ══════════════════════════════════════
   LAYOUT (tabs left + content right)
══════════════════════════════════════ */
.st-body { display: flex; gap: 0; min-height: calc(100vh - 56px); }

/* ── LEFT TAB RAIL ── */
.st-rail {
    width: 220px; flex-shrink: 0;
    background: var(--surface); border-right: 1px solid var(--border);
    padding: 1.25rem 0;
    position: sticky; top: 56px; height: calc(100vh - 56px);
    overflow-y: auto;
}
.st-rail::-webkit-scrollbar { width: 3px; }
.st-rail::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }
.rail-section-label { font-size: 9px; font-weight: 700; color: var(--ink4); text-transform: uppercase; letter-spacing: .12em; padding: .5rem 1.25rem .3rem; }
.rail-item {
    display: flex; align-items: center; gap: 9px;
    padding: 9px 1.25rem; margin: 1px 8px;
    border-radius: 8px; cursor: pointer;
    font-size: 13px; font-weight: 500;
    color: var(--ink3); transition: all .15s; border: none; background: none;
    width: calc(100% - 16px); text-align: left;
}
.rail-item i { width: 16px; text-align: center; font-size: 13px; }
.rail-item:hover { background: var(--s3); color: var(--ink); }
.rail-item.active {
    background: var(--bdim); color: var(--blue); font-weight: 700;
    border-left: 3px solid var(--blue); border-radius: 0 8px 8px 0; padding-left: calc(1.25rem - 3px);
}

/* ── CONTENT AREA ── */
.st-content { flex: 1; padding: 1.75rem; max-width: 860px; }

/* ══════════════════════════════════════
   CARDS
══════════════════════════════════════ */
.card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--rlg); overflow: hidden; box-shadow: var(--sh); margin-bottom: 1.25rem; }
.card-head { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.card-title { font-size: 14px; font-weight: 700; color: var(--ink); display: flex; align-items: center; gap: 8px; }
.card-title i { color: var(--blue); }
.card-sub { font-size: 12px; color: var(--ink3); margin-top: 2px; }
.card-body { padding: 1.25rem 1.5rem; }

/* ══════════════════════════════════════
   FORM ELEMENTS
══════════════════════════════════════ */
.form-grid { display: grid; gap: 1rem; }
.form-2 { grid-template-columns: 1fr 1fr; }
.form-3 { grid-template-columns: 1fr 1fr 1fr; }
.field-label { display: block; font-size: 11px; font-weight: 700; color: var(--ink2); text-transform: uppercase; letter-spacing: .07em; margin-bottom: 5px; }
.field-req { color: var(--red); }
.field-hint { font-size: 11px; color: var(--ink3); margin-top: 3px; }
.field-input {
    width: 100%; padding: 9px 12px;
    background: var(--s2); border: 1.5px solid var(--border);
    border-radius: var(--rsm); font-family: var(--body);
    font-size: 13px; color: var(--ink); outline: none;
    transition: border .15s, box-shadow .15s;
}
.field-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--bdim); background: #fff; }
.field-input::placeholder { color: var(--ink4); }
select.field-input { cursor: pointer; }
textarea.field-input { resize: vertical; min-height: 72px; }
.form-err { padding: 9px 12px; background: var(--rdim); border: 1px solid rgba(220,38,38,.2); border-radius: var(--rsm); font-size: 12px; color: var(--red); margin-top: .75rem; }
.form-ok  { padding: 9px 12px; background: var(--gdim); border: 1px solid rgba(21,128,61,.2);  border-radius: var(--rsm); font-size: 12px; color: var(--green); margin-top: .75rem; }

/* Toggle switch */
.toggle { position: relative; width: 40px; height: 22px; flex-shrink: 0; }
.toggle input { opacity: 0; width: 0; height: 0; }
.toggle-slider { position: absolute; inset: 0; background: var(--border2); border-radius: 11px; cursor: pointer; transition: background .2s; }
.toggle-slider::before { content: ''; position: absolute; width: 16px; height: 16px; background: #fff; border-radius: 50%; top: 3px; left: 3px; transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
.toggle input:checked + .toggle-slider { background: var(--blue); }
.toggle input:checked + .toggle-slider::before { transform: translateX(18px); }

/* Setting row */
.setting-row { display: flex; align-items: center; justify-content: space-between; padding: .85rem 1rem; background: var(--s2); border: 1px solid var(--border); border-radius: var(--rsm); margin-bottom: 8px; transition: border-color .15s; }
.setting-row:last-child { margin-bottom: 0; }
.setting-row:hover { border-color: var(--border2); }
.sr-label { font-size: 13px; font-weight: 500; color: var(--ink); display: flex; align-items: center; gap: 8px; }
.sr-label i { color: var(--blue); width: 16px; text-align: center; }
.sr-sub { font-size: 11px; color: var(--ink3); margin-top: 2px; }
.sr-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

/* Pills */
.pill { display: inline-block; padding: 3px 9px; border-radius: 99px; font-size: 10px; font-weight: 700; letter-spacing: .04em; }
.pill-green  { background: var(--gdim); color: var(--green); border: 1px solid rgba(21,128,61,.2); }
.pill-red    { background: var(--rdim); color: var(--red);   border: 1px solid rgba(220,38,38,.2); }
.pill-amber  { background: var(--adim); color: var(--amber); border: 1px solid rgba(217,119,6,.2); }
.pill-blue   { background: var(--bdim); color: var(--blue);  border: 1px solid var(--bmid); }
.pill-gray   { background: var(--s3);   color: var(--ink3);  border: 1px solid var(--border); }

/* ══════════════════════════════════════
   CATEGORY TREE
══════════════════════════════════════ */
.cat-tree { display: flex; flex-direction: column; gap: 4px; }
.cat-parent {
    background: var(--s2); border: 1px solid var(--border);
    border-radius: var(--r); overflow: hidden;
    transition: border-color .15s;
}
.cat-parent:hover { border-color: var(--border2); }
.cat-parent-row {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 12px; cursor: pointer; transition: background .12s;
}
.cat-parent-row:hover { background: var(--bdim); }
.cat-toggle-icon { color: var(--ink3); font-size: 10px; width: 14px; transition: transform .2s; flex-shrink: 0; }
.cat-toggle-icon.open { transform: rotate(90deg); }
.cat-icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
.cat-name { font-size: 13px; font-weight: 600; color: var(--ink); flex: 1; }
.cat-code { font-family: var(--mono); font-size: 10px; color: var(--ink3); margin-left: 4px; }
.cat-actions { display: flex; gap: 4px; opacity: 0; transition: opacity .15s; }
.cat-parent-row:hover .cat-actions { opacity: 1; }

/* Children */
.cat-children { padding: 0 8px 8px 8px; border-top: 1px solid var(--border); background: var(--surface); }
.cat-child-row {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 10px; border-radius: var(--rsm);
    margin-top: 4px; transition: background .12s;
}
.cat-child-row:hover { background: var(--bdim); }
.cat-child-indicator { width: 20px; flex-shrink: 0; display: flex; align-items: center; justify-content: flex-end; }
.cat-child-indicator::before { content: ''; width: 12px; height: 1px; background: var(--ink4); display: block; }
.cat-child-name { font-size: 12.5px; font-weight: 500; color: var(--ink2); flex: 1; }
.cat-child-actions { display: flex; gap: 4px; opacity: 0; transition: opacity .15s; }
.cat-child-row:hover .cat-child-actions { opacity: 1; }

/* Add category button */
.btn-add-cat {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 14px; border: 1.5px dashed var(--border2);
    background: var(--s2); border-radius: var(--r);
    font-family: var(--body); font-size: 13px; font-weight: 500;
    color: var(--ink3); cursor: pointer; width: 100%;
    transition: all .15s; margin-top: 6px;
}
.btn-add-cat:hover { border-color: var(--blue); color: var(--blue); background: var(--bdim); }

/* ══════════════════════════════════════
   ATTRIBUTES
══════════════════════════════════════ */
.attr-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--r); overflow: hidden; margin-bottom: 10px;
    transition: border-color .15s;
}
.attr-card:hover { border-color: var(--border2); }
.attr-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 14px; cursor: pointer;
    background: var(--s2); transition: background .12s;
}
.attr-card-head:hover { background: var(--s3); }
.attr-name { font-size: 13px; font-weight: 700; color: var(--ink); display: flex; align-items: center; gap: 8px; }
.attr-type-badge { font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 4px; }
.attr-type-string { background: var(--bdim); color: var(--blue); }
.attr-type-color  { background: var(--vdim); color: var(--violet); }
.attr-type-number { background: var(--tdim); color: var(--teal); }
.attr-values-wrap { padding: 10px 14px; display: flex; flex-wrap: wrap; gap: 6px; border-top: 1px solid var(--border); }
.attr-value-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px; border-radius: 99px;
    border: 1px solid var(--border); background: var(--s2);
    font-size: 12px; font-weight: 500; color: var(--ink2);
    cursor: default; transition: all .12s;
}
.attr-value-chip:hover { border-color: var(--border2); }
.color-dot { width: 12px; height: 12px; border-radius: 50%; border: 1px solid rgba(0,0,0,.1); flex-shrink: 0; }
.chip-del {
    width: 14px; height: 14px; border-radius: 50%;
    border: none; background: none; cursor: pointer;
    color: var(--ink4); font-size: 10px; display: flex;
    align-items: center; justify-content: center;
    transition: color .12s;
}
.chip-del:hover { color: var(--red); }
.btn-add-val {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border: 1px dashed var(--border2);
    background: transparent; border-radius: 99px;
    font-size: 12px; color: var(--ink3); cursor: pointer;
    font-family: var(--body); transition: all .12s;
}
.btn-add-val:hover { border-color: var(--blue); color: var(--blue); background: var(--bdim); }

/* ══════════════════════════════════════
   HARDWARE STATUS
══════════════════════════════════════ */
.hw-device {
    background: var(--s2); border: 1px solid var(--border);
    border-radius: var(--r); padding: 1rem 1.25rem;
    margin-bottom: 10px; transition: border-color .15s;
}
.hw-device:hover { border-color: var(--border2); }
.hw-device-head { display: flex; align-items: center; gap: 12px; margin-bottom: .75rem; }
.hw-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
.hw-icon.ok     { background: var(--gdim); color: var(--green); }
.hw-icon.warn   { background: var(--adim); color: var(--amber); }
.hw-icon.err    { background: var(--rdim); color: var(--red); }
.hw-icon.idle   { background: var(--s3);   color: var(--ink3); }
.hw-name   { font-size: 13px; font-weight: 700; color: var(--ink); }
.hw-status { font-size: 11px; margin-top: 2px; }
.hw-status.ok   { color: var(--green); }
.hw-status.warn { color: var(--amber); }
.hw-status.err  { color: var(--red); }
.hw-status.idle { color: var(--ink3); }
.hw-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-left: auto; }
.hw-dot.ok   { background: var(--green); animation: hwPulse 2.5s infinite; box-shadow: 0 0 5px var(--green); }
.hw-dot.warn { background: var(--amber); }
.hw-dot.err  { background: var(--red); }
.hw-dot.idle { background: var(--ink4); }
@keyframes hwPulse { 0%,100%{opacity:1} 50%{opacity:.4} }

/* test btn */
.hw-test-btn {
    padding: 6px 14px; border-radius: var(--rsm);
    border: 1px solid var(--border); background: var(--surface);
    font-family: var(--body); font-size: 12px; font-weight: 600;
    color: var(--ink2); cursor: pointer; transition: all .15s;
    display: flex; align-items: center; gap: 5px;
}
.hw-test-btn:hover { background: var(--bdim); border-color: var(--blue); color: var(--blue); }
.hw-test-btn:disabled { opacity: .4; cursor: not-allowed; }

/* ══════════════════════════════════════
   SECURITY — audit log
══════════════════════════════════════ */
.audit-row { display: flex; align-items: flex-start; gap: 10px; padding: 9px 12px; background: var(--s2); border: 1px solid var(--border); border-radius: var(--rsm); margin-bottom: 5px; font-size: 12px; transition: background .12s; }
.audit-row:hover { background: var(--bdim); }
.audit-icon { flex-shrink: 0; width: 26px; height: 26px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 11px; }
.audit-icon.edit   { background: var(--bdim); color: var(--blue); }
.audit-icon.delete { background: var(--rdim); color: var(--red); }
.audit-icon.create { background: var(--gdim); color: var(--green); }
.audit-icon.login  { background: var(--tdim); color: var(--teal); }
.audit-text { flex: 1; color: var(--ink2); line-height: 1.5; }
.audit-time { font-family: var(--mono); font-size: 10px; color: var(--ink4); flex-shrink: 0; }

/* ══════════════════════════════════════
   MODALS
══════════════════════════════════════ */
.modal-overlay { position: fixed; inset: 0; background: rgba(21,24,42,.45); backdrop-filter: blur(5px); z-index: 200; display: flex; align-items: center; justify-content: center; padding: 1rem; animation: fadeIn .15s ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.modal-card { background: var(--surface); border-radius: var(--rlg); box-shadow: var(--shlg); width: 100%; max-height: 90vh; display: flex; flex-direction: column; animation: slideUp .18s cubic-bezier(.2,.8,.36,1); }
@keyframes slideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }
.modal-sm { max-width: 460px; }
.modal-md { max-width: 580px; }
.modal-head { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
.modal-title { font-size: 17px; font-weight: 700; color: var(--ink); }
.modal-close { background: none; border: none; cursor: pointer; color: var(--ink3); font-size: 18px; }
.modal-body { padding: 1.25rem 1.5rem; overflow-y: auto; flex: 1; }
.modal-foot { padding: 1rem 1.5rem; border-top: 1px solid var(--border); display: flex; gap: 8px; justify-content: flex-end; flex-shrink: 0; }

/* color swatch picker */
.swatch-grid { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
.swatch { width: 28px; height: 28px; border-radius: 6px; cursor: pointer; border: 2px solid transparent; transition: transform .12s, border-color .12s; }
.swatch:hover  { transform: scale(1.15); }
.swatch.active { border-color: var(--ink); }

/* empty state */
.empty-state { text-align: center; padding: 3rem 2rem; color: var(--ink3); }
.empty-state i { font-size: 32px; margin-bottom: 10px; display: block; color: var(--ink4); }
.empty-state p { font-size: 13px; line-height: 1.6; }
</style>
@endpush

@section('content')
<div class="st" x-data="settingsPage()" x-init="init()">

{{-- ════ TOPBAR ════ --}}
<div class="st-top">
    <div class="st-title">Afghan <em>POS</em> — Settings</div>
    <div style="display:flex;align-items:center;gap:8px">
        <span x-show="saveMsg" x-cloak
              style="font-size:12px;color:var(--green);display:flex;align-items:center;gap:5px">
            <i class="fas fa-check-circle"></i> <span x-text="saveMsg"></span>
        </span>
    </div>
</div>

<div class="st-body">

    {{-- ════ LEFT RAIL ════ --}}
    <div class="st-rail">
        <div class="rail-section-label">Configuration</div>
        <button type="button" class="rail-item" :class="tab==='general'?'active':''"    @click="tab='general'">
            <i class="fas fa-store"></i> General
        </button>
        <button type="button" class="rail-item" :class="tab==='calendar'?'active':''"  @click="tab='calendar'">
            <i class="fas fa-calendar-alt"></i> Calendar
        </button>
        <div class="rail-section-label" style="margin-top:.5rem">Catalog</div>
        <button type="button" class="rail-item" :class="tab==='categories'?'active':''" @click="tab='categories'">
            <i class="fas fa-tag"></i> Categories
        </button>
        <button type="button" class="rail-item" :class="tab==='attributes'?'active':''" @click="tab='attributes'">
            <i class="fas fa-list-check"></i> Attributes
        </button>
        <div class="rail-section-label" style="margin-top:.5rem">System</div>
        <button type="button" class="rail-item" :class="tab==='hardware'?'active':''"  @click="tab='hardware'">
            <i class="fas fa-microchip"></i> Hardware
        </button>
        <button type="button" class="rail-item" :class="tab==='security'?'active':''"  @click="tab='security'">
            <i class="fas fa-shield-halved"></i> Security
        </button>
    </div>

    {{-- ════ CONTENT ════ --}}
    <div class="st-content">

        {{-- ══════════════════════════
             TAB: GENERAL
        ══════════════════════════ --}}
        <div x-show="tab==='general'" x-cloak>

            <div class="card">
                <div class="card-head">
                    <div>
                        <div class="card-title"><i class="fas fa-store"></i> Store Information</div>
                        <div class="card-sub">Basic details about your retail store</div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm"
                            @click="saveGroup('general')" :disabled="saving">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="saving?'Saving…':'Save Changes'"></span>
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-grid form-2">
                        <div>
                            <label class="field-label">Store Name <span class="field-req">*</span></label>
                            <input type="text" class="field-input" x-model="s.store_name" placeholder="Afghan POS">
                        </div>
                        <div>
                            <label class="field-label">Phone</label>
                            <input type="text" class="field-input" x-model="s.store_phone" placeholder="+93 XXX XXX XXXX">
                        </div>
                        <div>
                            <label class="field-label">Email</label>
                            <input type="email" class="field-input" x-model="s.store_email" placeholder="store@example.com">
                        </div>
                        <div>
                            <label class="field-label">Timezone</label>
                            <select class="field-input" x-model="s.timezone">
                                <option value="Asia/Kabul">Asia/Kabul (UTC+4:30)</option>
                                <option value="UTC">UTC</option>
                                <option value="Asia/Tehran">Asia/Tehran (UTC+3:30)</option>
                            </select>
                        </div>
                        <div style="grid-column:span 2">
                            <label class="field-label">Address</label>
                            <textarea class="field-input" x-model="s.store_address" placeholder="Full store address…"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div>
                        <div class="card-title"><i class="fas fa-coins"></i> Currency</div>
                        <div class="card-sub">How prices are displayed throughout the system</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-grid form-3">
                        <div>
                            <label class="field-label">Currency Code</label>
                            <select class="field-input" x-model="s.currency">
                                <option value="AFN">AFN — Afghan Afghani</option>
                                <option value="USD">USD — US Dollar</option>
                                <option value="EUR">EUR — Euro</option>
                            </select>
                        </div>
                        <div>
                            <label class="field-label">Currency Symbol</label>
                            <input type="text" class="field-input" x-model="s.currency_symbol" placeholder="Af">
                            <div class="field-hint">Displayed before amounts</div>
                        </div>
                        <div>
                            <label class="field-label">Preview</label>
                            <div style="padding:9px 12px;background:var(--s2);border:1px solid var(--border);border-radius:var(--rsm);font-family:var(--mono);font-size:14px;font-weight:600;color:var(--blue)">
                                <span x-text="s.currency_symbol"></span> 1,250
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ══════════════════════════
             TAB: CALENDAR
        ══════════════════════════ --}}
        <div x-show="tab==='calendar'" x-cloak>
            <div class="card">
                <div class="card-head">
                    <div>
                        <div class="card-title"><i class="fas fa-calendar-alt"></i> Calendar & Language</div>
                        <div class="card-sub">Date format and language preferences for the system</div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm"
                            @click="saveGroup('calendar')" :disabled="saving">
                        <span x-text="saving?'Saving…':'Save Changes'"></span>
                    </button>
                </div>
                <div class="card-body">

                    <div class="setting-row">
                        <div>
                            <div class="sr-label"><i class="fas fa-calendar"></i> Default Calendar</div>
                            <div class="sr-sub">Used across all date displays in the system</div>
                        </div>
                        <div class="sr-right">
                            <select class="field-input" x-model="s.default_calendar" style="width:200px">
                                <option value="hijri">Solar Hijri (شمسی)</option>
                                <option value="gregorian">Gregorian</option>
                            </select>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div>
                            <div class="sr-label"><i class="fas fa-language"></i> Default Language</div>
                            <div class="sr-sub">UI language for new sessions</div>
                        </div>
                        <div class="sr-right">
                            <select class="field-input" x-model="s.default_language" style="width:200px">
                                <option value="en">🇬🇧 English</option>
                                <option value="ps">🇦🇫 پښتو (Pashto)</option>
                                <option value="dr">🇦🇫 دری (Dari)</option>
                            </select>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div>
                            <div class="sr-label"><i class="fas fa-text-height"></i> Date Format</div>
                            <div class="sr-sub">How dates appear on receipts and reports</div>
                        </div>
                        <div class="sr-right">
                            <select class="field-input" x-model="s.date_format" style="width:200px">
                                <option value="d M Y">15 Jan 2024</option>
                                <option value="Y-m-d">2024-01-15</option>
                                <option value="d/m/Y">15/01/2024</option>
                                <option value="m/d/Y">01/15/2024</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ══════════════════════════
             TAB: CATEGORIES
        ══════════════════════════ --}}
        <div x-show="tab==='categories'" x-cloak>
            <div class="card">
                <div class="card-head">
                    <div>
                        <div class="card-title"><i class="fas fa-tag"></i> Product Categories</div>
                        <div class="card-sub">Organize products into a hierarchy</div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm"
                            @click="openCatModal(null, null)">
                        <i class="fas fa-plus"></i> Add Category
                    </button>
                </div>
                <div class="card-body">

                    {{-- Loading --}}
                    <div x-show="catsLoading" style="text-align:center;padding:2rem;color:var(--ink3)">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>

                    {{-- Empty --}}
                    <div class="empty-state" x-show="!catsLoading && categories.length===0">
                        <i class="fas fa-tag"></i>
                        <p>No categories yet.<br>Add your first category above.</p>
                    </div>

                    {{-- Tree --}}
                    <div class="cat-tree" x-show="!catsLoading">
                        <template x-for="cat in categories" :key="cat.id">
                            <div class="cat-parent">
                                <div class="cat-parent-row" @click="cat.open = !cat.open">
                                    <i class="fas fa-chevron-right cat-toggle-icon" :class="cat.open?'open':''"></i>
                                    <div class="cat-icon"
                                         :style="`background:${cat.color || 'var(--bdim)'};color:var(--blue)`">
                                        <i :class="cat.icon || 'fas fa-tag'"></i>
                                    </div>
                                    <span class="cat-name" x-text="cat.name"></span>
                                    <span class="cat-code" x-text="cat.code ? '· ' + cat.code : ''"></span>
                                    <span class="pill pill-gray" style="font-size:9px"
                                          x-text="(cat.children?.length || 0) + ' sub'"></span>
                                    <div class="cat-actions" @click.stop>
                                        <button type="button" class="btn btn-ghost btn-sm"
                                                @click="openCatModal(null, cat.id)" title="Add sub-category">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-ghost btn-sm"
                                                @click="openCatModal(cat, null)" title="Edit">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm"
                                                @click="deleteCategory(cat)" title="Delete"
                                                x-show="!cat.children?.length">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                {{-- Children --}}
                                <div class="cat-children" x-show="cat.open" x-cloak>
                                    <div x-show="!cat.children?.length"
                                         style="padding:.5rem 8px;font-size:12px;color:var(--ink4)">
                                        No sub-categories — <a href="#" @click.prevent="openCatModal(null, cat.id)"
                                           style="color:var(--blue);text-decoration:none;font-weight:600">add one</a>
                                    </div>
                                    <template x-for="child in cat.children" :key="child.id">
                                        <div class="cat-child-row">
                                            <div class="cat-child-indicator"></div>
                                            <div class="cat-icon" style="width:24px;height:24px;border-radius:6px;font-size:11px"
                                                 :style="`background:${child.color || 'var(--s3)'};color:var(--ink3)`">
                                                <i :class="child.icon || 'fas fa-circle-dot'"></i>
                                            </div>
                                            <span class="cat-child-name" x-text="child.name"></span>
                                            <span class="cat-code" x-text="child.code ? '· ' + child.code : ''"></span>
                                            <div class="cat-child-actions">
                                                <button type="button" class="btn btn-ghost btn-sm"
                                                        @click="openCatModal(child, null)">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        @click="deleteCategory(child)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" class="btn-add-cat" @click="openCatModal(null, null)">
                        <i class="fas fa-plus"></i> Add Parent Category
                    </button>

                </div>
            </div>
        </div>

        {{-- ══════════════════════════
             TAB: ATTRIBUTES
        ══════════════════════════ --}}
        <div x-show="tab==='attributes'" x-cloak>
            <div class="card">
                <div class="card-head">
                    <div>
                        <div class="card-title"><i class="fas fa-list-check"></i> Product Attributes</div>
                        <div class="card-sub">Manage variant attributes like Size, Color, Weight</div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm"
                            @click="openAttrModal(null)">
                        <i class="fas fa-plus"></i> Add Attribute
                    </button>
                </div>
                <div class="card-body">

                    <div x-show="attrsLoading" style="text-align:center;padding:2rem;color:var(--ink3)">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>

                    <div class="empty-state" x-show="!attrsLoading && attributes.length===0">
                        <i class="fas fa-list-check"></i>
                        <p>No attributes yet.<br>Add attributes to create product variants.</p>
                    </div>

                    <template x-for="attr in attributes" :key="attr.id">
                        <div class="attr-card">
                            <div class="attr-card-head" @click="attr.open = !attr.open">
                                <div class="attr-name">
                                    <i class="fas fa-chevron-right" style="font-size:10px;color:var(--ink4);transition:transform .2s"
                                       :style="attr.open?'transform:rotate(90deg)':''"></i>
                                    <span x-text="attr.name"></span>
                                    <span x-show="attr.name_ps" x-cloak
                                          style="font-size:11px;color:var(--ink3)" x-text="'/ ' + attr.name_ps"></span>
                                    <span class="attr-type-badge"
                                          :class="attr.data_type==='color'?'attr-type-color':attr.data_type==='number'?'attr-type-number':'attr-type-string'"
                                          x-text="attr.data_type"></span>
                                </div>
                                <div style="display:flex;gap:6px" @click.stop>
                                    <span class="pill pill-gray" style="font-size:9px"
                                          x-text="(attr.values?.length || 0) + ' values'"></span>
                                    <button type="button" class="btn btn-ghost btn-sm"
                                            @click="openAttrModal(attr)">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                            @click="deleteAttribute(attr)"
                                            x-show="!attr.values?.length">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Attribute values --}}
                            <div class="attr-values-wrap" x-show="attr.open" x-cloak>
                                <template x-for="val in attr.values" :key="val.id">
                                    <div class="attr-value-chip">
                                        <div class="color-dot" x-show="attr.data_type==='color'"
                                             :style="`background:${val.color_code || '#ccc'}`"></div>
                                        <span x-text="val.value"></span>
                                        <span x-show="val.value_ps" style="color:var(--ink4)"
                                              x-text="'/ ' + val.value_ps"></span>
                                        <button type="button" class="chip-del"
                                                @click="deleteAttrValue(attr, val)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" class="btn-add-val"
                                        @click="openValueModal(attr)">
                                    <i class="fas fa-plus"></i> Add value
                                </button>
                            </div>
                        </div>
                    </template>

                </div>
            </div>
        </div>

        {{-- ══════════════════════════
             TAB: HARDWARE
        ══════════════════════════ --}}
        <div x-show="tab==='hardware'" x-cloak>

            {{-- Devices status --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title"><i class="fas fa-microchip"></i> Device Status</div>
                    <button type="button" class="btn btn-ghost btn-sm"
                            @click="testAllDevices()" :disabled="hwTesting">
                        <i class="fas fa-rotate" :class="hwTesting?'fa-spin':''"></i>
                        <span x-text="hwTesting?'Testing…':'Test All'"></span>
                    </button>
                </div>
                <div class="card-body">

                    <template x-for="device in devices" :key="device.key">
                        <div class="hw-device">
                            <div class="hw-device-head">
                                <div class="hw-icon" :class="device.status">
                                    <i :class="device.icon"></i>
                                </div>
                                <div>
                                    <div class="hw-name" x-text="device.name"></div>
                                    <div class="hw-status" :class="device.status"
                                         x-text="device.message || statusLabel(device.status)"></div>
                                </div>
                                <div class="hw-dot" :class="device.status" style="margin-left:auto"></div>
                            </div>
                            {{-- Settings fields for each device --}}
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                <input type="text" class="field-input" style="flex:1;min-width:160px;font-size:12px"
                                       x-model="device.port"
                                       :placeholder="device.port_placeholder || 'Port / Connection'">
                                <button type="button" class="hw-test-btn"
                                        @click="testDevice(device)" :disabled="device.testing">
                                    <i class="fas fa-plug" x-show="!device.testing"></i>
                                    <i class="fas fa-spinner fa-spin" x-show="device.testing"></i>
                                    <span x-text="device.testing?'Testing…':'Test'"></span>
                                </button>
                                <label class="toggle">
                                    <input type="checkbox" x-model="device.enabled">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </template>

                </div>
            </div>

            {{-- Receipt settings --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title"><i class="fas fa-receipt"></i> Receipt Settings</div>
                    <button type="button" class="btn btn-primary btn-sm"
                            @click="saveGroup('hardware')" :disabled="saving">
                        <span x-text="saving?'Saving…':'Save'"></span>
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-grid">
                        <div>
                            <label class="field-label">Receipt Footer Text</label>
                            <textarea class="field-input" x-model="s.receipt_footer" rows="2"
                                      placeholder="شکریه — Thank you for shopping with us"></textarea>
                            <div class="field-hint">Printed at the bottom of every receipt</div>
                        </div>
                        <div class="setting-row">
                            <div>
                                <div class="sr-label"><i class="fas fa-print"></i> Auto Print Receipt</div>
                                <div class="sr-sub">Print receipt automatically after each sale</div>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" x-model="s.auto_print">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-row">
                            <div>
                                <div class="sr-label"><i class="fas fa-cash-register"></i> Open Cash Drawer</div>
                                <div class="sr-sub">Automatically open drawer after cash sale</div>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" x-model="s.drawer_enabled">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ══════════════════════════
             TAB: SECURITY
        ══════════════════════════ --}}
        <div x-show="tab==='security'" x-cloak>

            <div class="card">
                <div class="card-head">
                    <div class="card-title"><i class="fas fa-shield-halved"></i> Security Settings</div>
                    <button type="button" class="btn btn-primary btn-sm"
                            @click="saveGroup('security')" :disabled="saving">
                        <span x-text="saving?'Saving…':'Save'"></span>
                    </button>
                </div>
                <div class="card-body">

                    <div class="setting-row">
                        <div>
                            <div class="sr-label"><i class="fas fa-clock"></i> Auto Logout</div>
                            <div class="sr-sub">Automatically log out after inactivity</div>
                        </div>
                        <div class="sr-right">
                            <select class="field-input" x-model="s.auto_logout" style="width:150px">
                                <option value="15">15 minutes</option>
                                <option value="30">30 minutes</option>
                                <option value="60">1 hour</option>
                                <option value="120">2 hours</option>
                                <option value="0">Never</option>
                            </select>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div>
                            <div class="sr-label"><i class="fas fa-hashtag"></i> Require PIN Login</div>
                            <div class="sr-sub">Allow cashiers to log in with 4-digit PIN</div>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" x-model="s.require_pin">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div>
                            <div class="sr-label"><i class="fas fa-file-lines"></i> Audit Logging</div>
                            <div class="sr-sub">Log all important actions (price changes, refunds, etc.)</div>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" x-model="s.audit_log">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                </div>
            </div>

            {{-- Audit log viewer --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title"><i class="fas fa-scroll"></i> Recent Audit Log</div>
                    <span style="font-size:11px;color:var(--ink3)" x-text="auditLog.length + ' entries'"></span>
                </div>
                <div class="card-body" style="padding:.75rem 1.25rem">
                    <div x-show="auditLoading" style="text-align:center;padding:1.5rem;color:var(--ink3)">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <div x-show="!auditLoading && auditLog.length===0" class="empty-state" style="padding:1.5rem">
                        <i class="fas fa-scroll" style="font-size:24px"></i>
                        <p>No audit entries yet.</p>
                    </div>
                    <template x-for="entry in auditLog" :key="entry.id">
                        <div class="audit-row">
                            <div class="audit-icon" :class="entry.type">
                                <i :class="{
                                    'fas fa-pen':         entry.type==='edit',
                                    'fas fa-trash':       entry.type==='delete',
                                    'fas fa-plus':        entry.type==='create',
                                    'fas fa-right-to-bracket': entry.type==='login',
                                }"></i>
                            </div>
                            <div class="audit-text">
                                <strong x-text="entry.user"></strong>
                                <span x-text="' ' + entry.action"></span>
                            </div>
                            <span class="audit-time" x-text="entry.time"></span>
                        </div>
                    </template>
                </div>
            </div>

        </div>

    </div>{{-- /st-content --}}
</div>{{-- /st-body --}}

{{-- ═════════════════════════════════════
     MODAL: ADD / EDIT CATEGORY
═════════════════════════════════════ --}}
<div class="modal-overlay" x-show="showCatModal" x-cloak @click.self="showCatModal=false">
    <div class="modal-card modal-sm">
        <div class="modal-head">
            <div class="modal-title" x-text="editingCat ? 'Edit Category' : 'New Category'"></div>
            <button class="modal-close" @click="showCatModal=false"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="form-grid">
                <div>
                    <label class="field-label">Name (English) <span class="field-req">*</span></label>
                    <input type="text" class="field-input" x-model="cf.name" placeholder="e.g. Electronics">
                </div>
                <div>
                    <label class="field-label">Name (Pashto)</label>
                    <input type="text" class="field-input" x-model="cf.name_ps" placeholder="د محصول کټګوري" dir="rtl">
                </div>
                <div>
                    <label class="field-label">Name (Dari)</label>
                    <input type="text" class="field-input" x-model="cf.name_dr" placeholder="کتگوری محصول" dir="rtl">
                </div>
                <div>
                    <label class="field-label">Code</label>
                    <input type="text" class="field-input" x-model="cf.code" placeholder="ELEC" style="text-transform:uppercase">
                    <div class="field-hint">Short unique identifier</div>
                </div>
                <div>
                    <label class="field-label">Parent Category</label>
                    <select class="field-input" x-model="cf.parent_id">
                        <option value="">— None (top level) —</option>
                        <template x-for="cat in categories" :key="cat.id">
                            <option :value="cat.id" x-text="cat.name"
                                    :disabled="editingCat && editingCat.id === cat.id"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="field-label">Sort Order</label>
                    <input type="number" class="field-input" x-model.number="cf.sort_order" min="0" placeholder="0">
                </div>
                <div>
                    <label class="field-label">Low Stock Threshold</label>
                    <input type="number" class="field-input" x-model.number="cf.low_stock_threshold" min="0" placeholder="10">
                </div>
                <div>
                    <label class="field-label">Active</label>
                    <label class="toggle" style="margin-top:6px;display:block">
                        <input type="checkbox" x-model="cf.is_active">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="form-err" x-show="catError" x-text="catError" x-cloak></div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-ghost" @click="showCatModal=false">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveCategory()" :disabled="saving">
                <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                <span x-text="saving?'Saving…':(editingCat?'Update':'Create')"></span>
            </button>
        </div>
    </div>
</div>

{{-- ═════════════════════════════════════
     MODAL: ADD / EDIT ATTRIBUTE
═════════════════════════════════════ --}}
<div class="modal-overlay" x-show="showAttrModal" x-cloak @click.self="showAttrModal=false">
    <div class="modal-card modal-sm">
        <div class="modal-head">
            <div class="modal-title" x-text="editingAttr ? 'Edit Attribute' : 'New Attribute'"></div>
            <button class="modal-close" @click="showAttrModal=false"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="form-grid">
                <div>
                    <label class="field-label">Name (English) <span class="field-req">*</span></label>
                    <input type="text" class="field-input" x-model="af.name" placeholder="e.g. Color">
                </div>
                <div>
                    <label class="field-label">Name (Pashto)</label>
                    <input type="text" class="field-input" x-model="af.name_ps" dir="rtl" placeholder="رنګ">
                </div>
                <div>
                    <label class="field-label">Name (Dari)</label>
                    <input type="text" class="field-input" x-model="af.name_dr" dir="rtl" placeholder="رنگ">
                </div>
                <div>
                    <label class="field-label">Data Type <span class="field-req">*</span></label>
                    <select class="field-input" x-model="af.data_type">
                        <option value="string">String (text)</option>
                        <option value="number">Number</option>
                        <option value="color">Color (with swatch)</option>
                    </select>
                    <div class="field-hint">Determines how values are displayed and validated</div>
                </div>
            </div>
            <div class="form-err" x-show="attrError" x-text="attrError" x-cloak></div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-ghost" @click="showAttrModal=false">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveAttribute()" :disabled="saving">
                <span x-text="saving?'Saving…':(editingAttr?'Update':'Create')"></span>
            </button>
        </div>
    </div>
</div>

{{-- ═════════════════════════════════════
     MODAL: ADD ATTRIBUTE VALUE
═════════════════════════════════════ --}}
<div class="modal-overlay" x-show="showValueModal" x-cloak @click.self="showValueModal=false">
    <div class="modal-card modal-sm">
        <div class="modal-head">
            <div class="modal-title">Add Value — <em style="font-style:italic;color:var(--blue)" x-text="valueAttr?.name"></em></div>
            <button class="modal-close" @click="showValueModal=false"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="form-grid">
                <div>
                    <label class="field-label">Value (English) <span class="field-req">*</span></label>
                    <input type="text" class="field-input" x-model="vf.value" placeholder="e.g. Red">
                </div>
                <div>
                    <label class="field-label">Value (Pashto)</label>
                    <input type="text" class="field-input" x-model="vf.value_ps" dir="rtl" placeholder="سور">
                </div>
                <div>
                    <label class="field-label">Value (Dari)</label>
                    <input type="text" class="field-input" x-model="vf.value_dr" dir="rtl" placeholder="قرمز">
                </div>
                <div x-show="valueAttr?.data_type==='color'">
                    <label class="field-label">Color Code</label>
                    <input type="text" class="field-input" x-model="vf.color_code" placeholder="#dc2626" style="font-family:var(--mono)">
                    <div class="swatch-grid">
                        <template x-for="c in swatchColors" :key="c">
                            <div class="swatch" :class="vf.color_code===c?'active':''"
                                 :style="`background:${c}`"
                                 @click="vf.color_code = c"></div>
                        </template>
                    </div>
                </div>
                <div>
                    <label class="field-label">Sort Order</label>
                    <input type="number" class="field-input" x-model.number="vf.sort_order" min="0" placeholder="0">
                </div>
            </div>
            <div class="form-err" x-show="valueError" x-text="valueError" x-cloak></div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-ghost" @click="showValueModal=false">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveValue()" :disabled="saving">
                <span x-text="saving?'Saving…':'Add Value'"></span>
            </button>
        </div>
    </div>
</div>

</div>{{-- /st --}}
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
Alpine.data('settingsPage', () => ({

    tab: 'general',

    /* settings values */
    s: {
        store_name:'', store_address:'', store_phone:'', store_email:'',
        currency:'AFN', currency_symbol:'Af', timezone:'Asia/Kabul',
        default_calendar:'hijri', default_language:'en', date_format:'d M Y',
        auto_logout:'30', require_pin:true, audit_log:true,
        printer_type:'thermal', printer_port:'USB001',
        drawer_enabled:true, scanner_enabled:true,
        receipt_footer:'شکریه — Thank you', auto_print:true,
    },

    saving:  false,
    saveMsg: '',

    /* categories */
    categories:   [],
    catsLoading:  true,
    showCatModal: false,
    editingCat:   null,
    cf: {},
    catError: '',

    /* attributes */
    attributes:   [],
    attrsLoading: true,
    showAttrModal:false,
    editingAttr:  null,
    af: {},
    attrError: '',

    /* attribute values */
    showValueModal: false,
    valueAttr:      null,
    vf: {},
    valueError: '',
    swatchColors: [
        '#dc2626','#ea580c','#d97706','#65a30d','#16a34a',
        '#0891b2','#2563eb','#7c3aed','#db2777','#64748b',
        '#ffffff','#1a1d2e',
    ],

    /* hardware */
    hwTesting: false,
    devices: [
        { key:'printer',  name:'Receipt Printer',  icon:'fas fa-print',       status:'idle', port:'USB001', port_placeholder:'USB001 or COM3', enabled:true,  testing:false, message:'' },
        { key:'scanner',  name:'Barcode Scanner',  icon:'fas fa-barcode',      status:'idle', port:'USB',    port_placeholder:'USB / COM port',  enabled:true,  testing:false, message:'' },
        { key:'drawer',   name:'Cash Drawer',      icon:'fas fa-cash-register',status:'idle', port:'COM3',   port_placeholder:'COM port',        enabled:true,  testing:false, message:'' },
        { key:'terminal', name:'Card Terminal',    icon:'fas fa-credit-card',  status:'idle', port:'',       port_placeholder:'IP address or port', enabled:false, testing:false, message:'' },
    ],

    /* audit log */
    auditLog:     [],
    auditLoading: false,

    /* urls */
    urls: {
        settings:    '{{ route("pos.settings.index") }}',
        save:        '{{ route("pos.settings.save") }}',
        categories:  '{{ route("pos.settings.categories.index") }}',
        saveCat:     '{{ route("pos.settings.categories.store") }}',
        deleteCat:   '{{ url("pos/settings/categories") }}',
        attributes:  '{{ route("pos.settings.attributes.index") }}',
        saveAttr:    '{{ route("pos.settings.attributes.store") }}',
        deleteAttr:  '{{ url("pos/settings/attributes") }}',
        saveValue:   '{{ route("pos.settings.attributes.values.store") }}',
        deleteValue: '{{ url("pos/settings/attributes/values") }}',
        hwTest:      '{{ route("pos.settings.hardware.test") }}',
        audit:       '{{ route("pos.settings.audit") }}',
        csrf:        document.querySelector('meta[name=csrf-token]').content,
    },

    /* ── Init ── */
    async init() {
        await this.loadSettings();
        await this.loadCategories();
        await this.loadAttributes();
        this.loadAuditLog();
    },

    /* ── Load settings ── */
    async loadSettings() {
        try {
            const r = await fetch(this.urls.settings, { headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' } });
            const d = await r.json();
            this.s = { ...this.s, ...d };
        } catch(e) { console.error(e); }
    },

    /* ── Save settings by group ── */
    async saveGroup(group) {
        this.saving = true; this.saveMsg = '';
        try {
            const r = await fetch(this.urls.save, {
                method:  'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': this.urls.csrf },
                body:    JSON.stringify({ group, settings: this.s })
            });
            const d = await r.json();
            if (d.success) {
                this.saveMsg = 'Saved successfully';
                setTimeout(() => this.saveMsg = '', 3000);
            }
        } catch(e) { console.error(e); }
        finally { this.saving = false; }
    },

    /* ══════════════════════════════
       CATEGORIES
    ══════════════════════════════ */
    async loadCategories() {
        this.catsLoading = true;
        try {
            const r = await fetch(this.urls.categories, { headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' } });
            const raw = await r.json();
            // Build tree
            const map = {};
            raw.forEach(c => { map[c.id] = { ...c, children: [], open: false }; });
            const tree = [];
            raw.forEach(c => {
                if (c.parent_id && map[c.parent_id]) map[c.parent_id].children.push(map[c.id]);
                else if (!c.parent_id) tree.push(map[c.id]);
            });
            this.categories = tree;
        } catch(e) { console.error(e); }
        finally { this.catsLoading = false; }
    },

    openCatModal(cat, parentId) {
        this.editingCat = cat;
        this.catError   = '';
        this.cf = cat
            ? { ...cat }
            : { name:'', name_ps:'', name_dr:'', code:'', parent_id: parentId || '', sort_order:0, low_stock_threshold:10, is_active:true };
        this.showCatModal = true;
    },

    async saveCategory() {
        if (!this.cf.name.trim()) { this.catError = 'Name is required.'; return; }
        this.saving = true; this.catError = '';
        try {
            const r = await fetch(this.urls.saveCat, {
                method:  'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': this.urls.csrf },
                body:    JSON.stringify({ ...this.cf, category_id: this.editingCat?.id })
            });
            const d = await r.json();
            if (d.success) { this.showCatModal = false; this.loadCategories(); }
            else this.catError = d.message ?? 'Failed.';
        } catch(e) { this.catError = 'Network error.'; }
        finally { this.saving = false; }
    },

    async deleteCategory(cat) {
        if (!confirm(`Delete "${cat.name}"?`)) return;
        await fetch(`${this.urls.deleteCat}/${cat.id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':this.urls.csrf} });
        this.loadCategories();
    },

    /* ══════════════════════════════
       ATTRIBUTES
    ══════════════════════════════ */
    async loadAttributes() {
        this.attrsLoading = true;
        try {
            const r = await fetch(this.urls.attributes, { headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' } });
            const data = await r.json();
            this.attributes = data.map(a => ({ ...a, open: false }));
        } catch(e) { console.error(e); }
        finally { this.attrsLoading = false; }
    },

    openAttrModal(attr) {
        this.editingAttr = attr;
        this.attrError   = '';
        this.af = attr ? { ...attr } : { name:'', name_ps:'', name_dr:'', data_type:'string' };
        this.showAttrModal = true;
    },

    async saveAttribute() {
        if (!this.af.name.trim()) { this.attrError = 'Name is required.'; return; }
        this.saving = true; this.attrError = '';
        try {
            const r = await fetch(this.urls.saveAttr, {
                method:  'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': this.urls.csrf },
                body:    JSON.stringify({ ...this.af, attribute_id: this.editingAttr?.id })
            });
            const d = await r.json();
            if (d.success) { this.showAttrModal = false; this.loadAttributes(); }
            else this.attrError = d.message ?? 'Failed.';
        } catch(e) { this.attrError = 'Network error.'; }
        finally { this.saving = false; }
    },

    async deleteAttribute(attr) {
        if (!confirm(`Delete attribute "${attr.name}"?`)) return;
        await fetch(`${this.urls.deleteAttr}/${attr.id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':this.urls.csrf} });
        this.loadAttributes();
    },

    /* Attribute values */
    openValueModal(attr) {
        this.valueAttr  = attr;
        this.valueError = '';
        this.vf = { value:'', value_ps:'', value_dr:'', color_code:'', sort_order:0 };
        this.showValueModal = true;
    },

    async saveValue() {
        if (!this.vf.value.trim()) { this.valueError = 'Value is required.'; return; }
        this.saving = true; this.valueError = '';
        try {
            const r = await fetch(this.urls.saveValue, {
                method:  'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': this.urls.csrf },
                body:    JSON.stringify({ ...this.vf, attribute_id: this.valueAttr.id })
            });
            const d = await r.json();
            if (d.success) { this.showValueModal = false; this.loadAttributes(); }
            else this.valueError = d.message ?? 'Failed.';
        } catch(e) { this.valueError = 'Network error.'; }
        finally { this.saving = false; }
    },

    async deleteAttrValue(attr, val) {
        if (!confirm(`Delete value "${val.value}"?`)) return;
        await fetch(`${this.urls.deleteValue}/${val.id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':this.urls.csrf} });
        this.loadAttributes();
    },

    /* ══════════════════════════════
       HARDWARE
    ══════════════════════════════ */
    async testAllDevices() {
        this.hwTesting = true;
        for (const device of this.devices) {
            if (device.enabled) await this.testDevice(device);
        }
        this.hwTesting = false;
    },

    async testDevice(device) {
        device.testing = true; device.status = 'idle'; device.message = '';
        try {
            const r = await fetch(this.urls.hwTest, {
                method:  'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': this.urls.csrf },
                body:    JSON.stringify({ device: device.key, port: device.port })
            });
            const d = await r.json();
            device.status  = d.success ? 'ok' : 'err';
            device.message = d.message;
        } catch(e) {
            device.status  = 'err';
            device.message = 'Connection failed';
        } finally { device.testing = false; }
    },

    statusLabel(s) {
        return { ok:'Connected', warn:'Warning', err:'Not Connected', idle:'Not tested' }[s] || '—';
    },

    /* ══════════════════════════════
       AUDIT LOG
    ══════════════════════════════ */
    async loadAuditLog() {
        this.auditLoading = true;
        try {
            const r = await fetch(this.urls.audit, { headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' } });
            this.auditLog = await r.json();
        } catch(e) { console.error(e); }
        finally { this.auditLoading = false; }
    },

}));
});
</script>
@endpush
