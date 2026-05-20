@extends('layouts.app')

@push('styles')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/pages/settings.css'])
    @endif
@endpush

@section('content')
    <div class="st" x-data="settingsPage()" x-init="init()">

        {{-- ════ TOPBAR ════ --}}
        <div class="st-top">
            <div class="st-title">Afghan <em>POS</em> — {{ __('messages.settings') }}</div>
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
                <div class="rail-section-label">{{ __('messages.configuration') }}</div>
                <button type="button" class="rail-item" :class="tab === 'general' ? 'active' : ''" @click="tab='general'">
                    <i class="fas fa-store"></i> {{ __('messages.general') }}
                </button>
                <button type="button" class="rail-item" :class="tab === 'calendar' ? 'active' : ''" @click="tab='calendar'">
                    <i class="fas fa-calendar-alt"></i> {{ __('messages.calendar') }}
                </button>
                <div class="rail-section-label" style="margin-top:.5rem">{{ __('messages.catalog') }}</div>
                <button type="button" class="rail-item" :class="tab === 'categories' ? 'active' : ''" @click="tab='categories'">
                    <i class="fas fa-tag"></i> {{ __('messages.categories') }}
                </button>
                <button type="button" class="rail-item" :class="tab === 'attributes' ? 'active' : ''" @click="tab='attributes'">
                    <i class="fas fa-list-check"></i> {{ __('messages.attributes') }}
                </button>
                <div class="rail-section-label" style="margin-top:.5rem">{{ __('messages.system') }}</div>
                <button type="button" class="rail-item" :class="tab === 'hardware' ? 'active' : ''" @click="tab='hardware'">
                    <i class="fas fa-microchip"></i> {{ __('messages.hardware') }}
                </button>
                <button type="button" class="rail-item" :class="tab === 'security' ? 'active' : ''" @click="tab='security'">
                    <i class="fas fa-shield-halved"></i> {{ __('messages.security') }}
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
                                <div class="card-title"><i class="fas fa-store"></i> {{ __('messages.store_information') }}</div>
                                <div class="card-sub">{{ __('messages.store_information_sub') }}</div>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" @click="saveGroup('general')"
                                :disabled="saving">
                                <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                                <span x-text="saving?'{{ __('messages.saving') }}…':'{{ __('messages.save_changes') }}'"></span>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="form-grid form-2">
                                <div>
                                    <label class="field-label">{{ __('messages.store_name') }} <span class="field-req">*</span></label>
                                    <input type="text" class="field-input" x-model="s.store_name"
                                        placeholder="Afghan POS">
                                </div>
                                <div>
                                    <label class="field-label">{{ __('messages.phone') }}</label>
                                    <input type="text" class="field-input" x-model="s.store_phone"
                                        placeholder="+93 XXX XXX XXXX">
                                </div>
                                <div>
                                    <label class="field-label">{{ __('messages.email') }}</label>
                                    <input type="email" class="field-input" x-model="s.store_email"
                                        placeholder="store@example.com">
                                </div>
                                <div>
                                    <label class="field-label">{{ __('messages.timezone') }}</label>
                                    <select class="field-input" x-model="s.timezone">
                                        <option value="Asia/Kabul">Asia/Kabul (UTC+4:30)</option>
                                        <option value="UTC">UTC</option>
                                        <option value="Asia/Tehran">Asia/Tehran (UTC+3:30)</option>
                                    </select>
                                </div>
                                <div style="grid-column:span 2">
                                    <label class="field-label">{{ __('messages.address') }}</label>
                                    <textarea class="field-input" x-model="s.store_address" placeholder="{{ __('messages.address_placeholder') }}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <div>
                                <div class="card-title"><i class="fas fa-coins"></i> {{ __('messages.currency') }}</div>
                                <div class="card-sub">{{ __('messages.currency_sub') }}</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-grid form-3">
                                <div>
                                    <label class="field-label">{{ __('messages.currency_code') }}</label>
                                    <select class="field-input" x-model="s.currency">
                                        <option value="AFN">AFN — Afghan Afghani</option>
                                        <option value="USD">USD — US Dollar</option>
                                        <option value="EUR">EUR — Euro</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="field-label">{{ __('messages.currency_symbol') }}</label>
                                    <input type="text" class="field-input" x-model="s.currency_symbol" placeholder="Af">
                                    <div class="field-hint">{{ __('messages.currency_symbol_hint') }}</div>
                                </div>
                                <div>
                                    <label class="field-label">{{ __('messages.preview') }}</label>
                                    <div
                                        style="padding:9px 12px;background:var(--s2);border:1px solid var(--border);border-radius:var(--rsm);font-family:var(--mono);font-size:14px;font-weight:600;color:var(--blue)">
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
                                <div class="card-title"><i class="fas fa-calendar-alt"></i> {{ __('messages.calendar_language') }}</div>
                                <div class="card-sub">{{ __('messages.calendar_language_sub') }}</div>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" @click="saveGroup('calendar')"
                                :disabled="saving">
                                <span x-text="saving?'{{ __('messages.saving') }}…':'{{ __('messages.save_changes') }}'"></span>
                            </button>
                        </div>
                        <div class="card-body">

                            <div class="setting-row">
                                <div>
                                    <div class="sr-label"><i class="fas fa-calendar"></i> {{ __('messages.default_calendar') }}</div>
                                    <div class="sr-sub">{{ __('messages.default_calendar_sub') }}</div>
                                </div>
                                <div class="sr-right">
                                    <select class="field-input" x-model="s.default_calendar" style="width:200px">
                                        <option value="hijri">{{ __('messages.solar_hijri') }}</option>
                                        <option value="gregorian">{{ __('messages.gregorian') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="setting-row">
                                <div>
                                    <div class="sr-label"><i class="fas fa-language"></i> {{ __('messages.default_language') }}</div>
                                    <div class="sr-sub">{{ __('messages.default_language_sub') }}</div>
                                </div>
                                <div class="sr-right">
                                    <select class="field-input" x-model="s.default_language" style="width:200px">
                                        <option value="en">🇬🇧 English</option>
                                        <option value="ps">🇦🇫 {{ __('messages.pashto') }}</option>
                                        <option value="dr">🇦🇫 {{ __('messages.dari') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="setting-row">
                                <div>
                                    <div class="sr-label"><i class="fas fa-text-height"></i> {{ __('messages.date_format') }}</div>
                                    <div class="sr-sub">{{ __('messages.date_format_sub') }}</div>
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
                                <div class="card-title"><i class="fas fa-tag"></i> {{ __('messages.product_categories') }}</div>
                                <div class="card-sub">{{ __('messages.product_categories_sub') }}</div>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" @click="openCatModal(null, null)">
                                <i class="fas fa-plus"></i> {{ __('messages.add_category') }}
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
                                <p>{{ __('messages.no_categories') }}<br>{{ __('messages.add_first_category') }}</p>
                            </div>

                            {{-- Tree --}}
                            <div class="cat-tree" x-show="!catsLoading">
                                <template x-for="cat in categories" :key="cat.id">
                                    <div class="cat-parent">
                                        <div class="cat-parent-row" @click="cat.open = !cat.open">
                                            <i class="fas fa-chevron-right cat-toggle-icon"
                                                :class="cat.open ? 'open' : ''"></i>
                                            <div class="cat-icon"
                                                :style="`background:${cat.color || 'var(--bdim)'};color:var(--blue)`">
                                                <i :class="cat.icon || 'fas fa-tag'"></i>
                                            </div>
                                            <span class="cat-name" x-text="cat.name"></span>
                                            <span class="cat-code" x-text="cat.code ? '· ' + cat.code : ''"></span>
                                            <span class="pill pill-gray" style="font-size:9px"
                                                x-text="(cat.children?.length || 0) + ' {{ __('messages.sub') }}'"></span>
                                            <div class="cat-actions" @click.stop>
                                                <button type="button" class="btn btn-ghost btn-sm"
                                                    @click="openCatModal(null, cat.id)" title="{{ __('messages.add_subcategory') }}">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <button type="button" class="btn btn-ghost btn-sm"
                                                    @click="openCatModal(cat, null)" title="{{ __('messages.edit') }}">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    @click="deleteCategory(cat)" title="{{ __('messages.delete') }}"
                                                    x-show="!cat.children?.length">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        {{-- Children --}}
                                        <div class="cat-children" x-show="cat.open" x-cloak>
                                            <div x-show="!cat.children?.length"
                                                style="padding:.5rem 8px;font-size:12px;color:var(--ink4)">
                                                {{ __('messages.no_subcategories') }} — <a href="#"
                                                    @click.prevent="openCatModal(null, cat.id)"
                                                    style="color:var(--blue);text-decoration:none;font-weight:600">{{ __('messages.add_one') }}</a>
                                            </div>
                                            <template x-for="child in cat.children" :key="child.id">
                                                <div class="cat-child-row">
                                                    <div class="cat-child-indicator"></div>
                                                    <div class="cat-icon"
                                                        style="width:24px;height:24px;border-radius:6px;font-size:11px"
                                                        :style="`background:${child.color || 'var(--s3)'};color:var(--ink3)`">
                                                        <i :class="child.icon || 'fas fa-circle-dot'"></i>
                                                    </div>
                                                    <span class="cat-child-name" x-text="child.name"></span>
                                                    <span class="cat-code"
                                                        x-text="child.code ? '· ' + child.code : ''"></span>
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
                                <i class="fas fa-plus"></i> {{ __('messages.add_parent_category') }}
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
                                <div class="card-title"><i class="fas fa-list-check"></i> {{ __('messages.product_attributes') }}</div>
                                <div class="card-sub">{{ __('messages.product_attributes_sub') }}</div>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" @click="openAttrModal(null)">
                                <i class="fas fa-plus"></i> {{ __('messages.add_attribute') }}
                            </button>
                        </div>
                        <div class="card-body">

                            <div x-show="attrsLoading" style="text-align:center;padding:2rem;color:var(--ink3)">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>

                            <div class="empty-state" x-show="!attrsLoading && attributes.length===0">
                                <i class="fas fa-list-check"></i>
                                <p>{{ __('messages.no_attributes') }}<br>{{ __('messages.add_first_attribute') }}</p>
                            </div>

                            <template x-for="attr in attributes" :key="attr.id">
                                <div class="attr-card">
                                    <div class="attr-card-head" @click="attr.open = !attr.open">
                                        <div class="attr-name">
                                            <i class="fas fa-chevron-right"
                                                style="font-size:10px;color:var(--ink4);transition:transform .2s"
                                                :style="attr.open ? 'transform:rotate(90deg)' : ''"></i>
                                            <span x-text="attr.name"></span>
                                            <span x-show="attr.name_ps" x-cloak style="font-size:11px;color:var(--ink3)"
                                                x-text="'/ ' + attr.name_ps"></span>
                                            <span class="attr-type-badge"
                                                :class="attr.data_type === 'color' ? 'attr-type-color' : attr.data_type ===
                                                    'number' ? 'attr-type-number' : 'attr-type-string'"
                                                x-text="attr.data_type"></span>
                                        </div>
                                        <div style="display:flex;gap:6px" @click.stop>
                                            <span class="pill pill-gray" style="font-size:9px"
                                                x-text="(attr.values?.length || 0) + ' {{ __('messages.values') }}'"></span>
                                            <button type="button" class="btn btn-ghost btn-sm"
                                                @click="openAttrModal(attr)">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                @click="deleteAttribute(attr)" x-show="!attr.values?.length">
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
                                        <button type="button" class="btn-add-val" @click="openValueModal(attr)">
                                            <i class="fas fa-plus"></i> {{ __('messages.add_value') }}
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
                            <div class="card-title"><i class="fas fa-microchip"></i> {{ __('messages.device_status') }}</div>
                            <button type="button" class="btn btn-ghost btn-sm" @click="testAllDevices()"
                                :disabled="hwTesting">
                                <i class="fas fa-rotate" :class="hwTesting ? 'fa-spin' : ''"></i>
                                <span x-text="hwTesting?'{{ __('messages.testing') }}…':'{{ __('messages.test_all') }}'"></span>
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
                                        <input type="text" class="field-input"
                                            style="flex:1;min-width:160px;font-size:12px" x-model="device.port"
                                            :placeholder="device.port_placeholder || '{{ __('messages.port_connection') }}'">
                                        <button type="button" class="hw-test-btn" @click="testDevice(device)"
                                            :disabled="device.testing">
                                            <i class="fas fa-plug" x-show="!device.testing"></i>
                                            <i class="fas fa-spinner fa-spin" x-show="device.testing"></i>
                                            <span x-text="device.testing?'{{ __('messages.testing') }}…':'{{ __('messages.test') }}'"></span>
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
                            <div class="card-title"><i class="fas fa-receipt"></i> {{ __('messages.receipt_settings') }}</div>
                            <button type="button" class="btn btn-primary btn-sm" @click="saveGroup('hardware')"
                                :disabled="saving">
                                <span x-text="saving?'{{ __('messages.saving') }}…':'{{ __('messages.save') }}'"></span>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="form-grid">
                                <div>
                                    <label class="field-label">{{ __('messages.receipt_footer_text') }}</label>
                                    <textarea class="field-input" x-model="s.receipt_footer" rows="2"
                                        placeholder="{{ __('messages.receipt_footer_placeholder') }}"></textarea>
                                    <div class="field-hint">{{ __('messages.receipt_footer_hint') }}</div>
                                </div>
                                <div class="setting-row">
                                    <div>
                                        <div class="sr-label"><i class="fas fa-print"></i> {{ __('messages.auto_print_receipt') }}</div>
                                        <div class="sr-sub">{{ __('messages.auto_print_receipt_sub') }}</div>
                                    </div>
                                    <label class="toggle">
                                        <input type="checkbox" x-model="s.auto_print">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="setting-row">
                                    <div>
                                        <div class="sr-label"><i class="fas fa-cash-register"></i> {{ __('messages.open_cash_drawer') }}</div>
                                        <div class="sr-sub">{{ __('messages.open_cash_drawer_sub') }}</div>
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
                            <div class="card-title"><i class="fas fa-shield-halved"></i> {{ __('messages.security_settings') }}</div>
                            <button type="button" class="btn btn-primary btn-sm" @click="saveGroup('security')"
                                :disabled="saving">
                                <span x-text="saving?'{{ __('messages.saving') }}…':'{{ __('messages.save') }}'"></span>
                            </button>
                        </div>
                        <div class="card-body">

                            <div class="setting-row">
                                <div>
                                    <div class="sr-label"><i class="fas fa-clock"></i> {{ __('messages.auto_logout') }}</div>
                                    <div class="sr-sub">{{ __('messages.auto_logout_sub') }}</div>
                                </div>
                                <div class="sr-right">
                                    <select class="field-input" x-model="s.auto_logout" style="width:150px">
                                        <option value="15">{{ __('messages.minutes', ['minutes' => 15]) }}</option>
                                        <option value="30">{{ __('messages.minutes', ['minutes' => 30]) }}</option>
                                        <option value="60">{{ __('messages.hour', ['hour' => 1]) }}</option>
                                        <option value="120">{{ __('messages.hours', ['hours' => 2]) }}</option>
                                        <option value="0">{{ __('messages.never') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="setting-row">
                                <div>
                                    <div class="sr-label"><i class="fas fa-hashtag"></i> {{ __('messages.require_pin_login') }}</div>
                                    <div class="sr-sub">{{ __('messages.require_pin_login_sub') }}</div>
                                </div>
                                <label class="toggle">
                                    <input type="checkbox" x-model="s.require_pin">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="setting-row">
                                <div>
                                    <div class="sr-label"><i class="fas fa-file-lines"></i> {{ __('messages.audit_logging') }}</div>
                                    <div class="sr-sub">{{ __('messages.audit_logging_sub') }}</div>
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
                            <div class="card-title"><i class="fas fa-scroll"></i> {{ __('messages.recent_audit_log') }}</div>
                            <span style="font-size:11px;color:var(--ink3)" x-text="auditLog.length + ' {{ __('messages.entries') }}'"></span>
                        </div>
                        <div class="card-body" style="padding:.75rem 1.25rem">
                            <div x-show="auditLoading" style="text-align:center;padding:1.5rem;color:var(--ink3)">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                            <div x-show="!auditLoading && auditLog.length===0" class="empty-state"
                                style="padding:1.5rem">
                                <i class="fas fa-scroll" style="font-size:24px"></i>
                                <p>{{ __('messages.no_audit_entries') }}</p>
                            </div>
                            <template x-for="entry in auditLog" :key="entry.id">
                                <div class="audit-row">
                                    <div class="audit-icon" :class="entry.type">
                                        <i
                                            :class="{
                                                'fas fa-pen': entry.type==='edit',
                                                'fas fa-trash': entry.type==='delete',
                                                'fas fa-plus': entry.type==='create',
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
                    <div class="modal-title" x-text="editingCat ? '{{ __('messages.edit_category') }}' : '{{ __('messages.new_category') }}'"></div>
                    <button class="modal-close" @click="showCatModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-grid">
                        <div>
                            <label class="field-label">{{ __('messages.name_english') }} <span class="field-req">*</span></label>
                            <input type="text" class="field-input" x-model="cf.name" placeholder="{{ __('messages.name_english_placeholder') }}">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.name_pashto') }}</label>
                            <input type="text" class="field-input" x-model="cf.name_ps" placeholder="{{ __('messages.name_pashto_placeholder') }}"
                                dir="rtl">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.name_dari') }}</label>
                            <input type="text" class="field-input" x-model="cf.name_dr" placeholder="{{ __('messages.name_dari_placeholder') }}"
                                dir="rtl">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.code') }}</label>
                            <input type="text" class="field-input" x-model="cf.code" placeholder="ELEC"
                                style="text-transform:uppercase">
                            <div class="field-hint">{{ __('messages.code_hint') }}</div>
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.parent_category') }}</label>
                            <select class="field-input" x-model="cf.parent_id">
                                <option value="">{{ __('messages.none_top_level') }}</option>
                                <template x-for="cat in categories" :key="cat.id">
                                    <option :value="cat.id" x-text="cat.name"
                                        :disabled="editingCat && editingCat.id === cat.id"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.sort_order') }}</label>
                            <input type="number" class="field-input" x-model.number="cf.sort_order" min="0"
                                placeholder="0">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.low_stock_threshold') }}</label>
                            <input type="number" class="field-input" x-model.number="cf.low_stock_threshold"
                                min="0" placeholder="10">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.active') }}</label>
                            <label class="toggle" style="margin-top:6px;display:block">
                                <input type="checkbox" x-model="cf.is_active">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-err" x-show="catError" x-text="catError" x-cloak></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" @click="showCatModal=false">{{ __('messages.cancel') }}</button>
                    <button type="button" class="btn btn-primary" @click="saveCategory()" :disabled="saving">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="saving?'{{ __('messages.saving') }}…':(editingCat?'{{ __('messages.update') }}':'{{ __('messages.create') }}')"></span>
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
                    <div class="modal-title" x-text="editingAttr ? '{{ __('messages.edit_attribute') }}' : '{{ __('messages.new_attribute') }}'"></div>
                    <button class="modal-close" @click="showAttrModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-grid">
                        <div>
                            <label class="field-label">{{ __('messages.name_english') }} <span class="field-req">*</span></label>
                            <input type="text" class="field-input" x-model="af.name" placeholder="e.g. Color">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.name_pashto') }}</label>
                            <input type="text" class="field-input" x-model="af.name_ps" dir="rtl"
                                placeholder="رنګ">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.name_dari') }}</label>
                            <input type="text" class="field-input" x-model="af.name_dr" dir="rtl"
                                placeholder="رنگ">
                        </div>
                        <div>
                            <label class="field-label">{{ __('messages.data_type') }} <span class="field-req">*</span></label>
                            <select class="field-input" x-model="af.data_type">
                                <option value="string">{{ __('messages.data_type_string') }}</option>
                                <option value="number">{{ __('messages.data_type_number') }}</option>
                                <option value="color">{{ __('messages.data_type_color') }}</option>
                            </select>
                            <div class="field-hint">{{ __('messages.data_type_hint') }}</div>
                        </div>
                    </div>
                    <div class="form-err" x-show="attrError" x-text="attrError" x-cloak></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" @click="showAttrModal=false">{{ __('messages.cancel') }}</button>
                    <button type="button" class="btn btn-primary" @click="saveAttribute()" :disabled="saving">
                        <span x-text="saving?'{{ __('messages.saving') }}…':(editingAttr?'{{ __('messages.update') }}':'{{ __('messages.create') }}')"></span>
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
                    <div class="modal-title">Add Value — <em style="font-style:italic;color:var(--blue)"
                            x-text="valueAttr?.name"></em></div>
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
                            <input type="text" class="field-input" x-model="vf.value_ps" dir="rtl"
                                placeholder="سور">
                        </div>
                        <div>
                            <label class="field-label">Value (Dari)</label>
                            <input type="text" class="field-input" x-model="vf.value_dr" dir="rtl"
                                placeholder="قرمز">
                        </div>
                        <div x-show="valueAttr?.data_type==='color'">
                            <label class="field-label">Color Code</label>
                            <input type="text" class="field-input" x-model="vf.color_code" placeholder="#dc2626"
                                style="font-family:var(--mono)">
                            <div class="swatch-grid">
                                <template x-for="c in swatchColors" :key="c">
                                    <div class="swatch" :class="vf.color_code === c ? 'active' : ''" :style="`background:${c}`"
                                        @click="vf.color_code = c"></div>
                                </template>
                            </div>
                        </div>
                        <div>
                            <label class="field-label">Sort Order</label>
                            <input type="number" class="field-input" x-model.number="vf.sort_order" min="0"
                                placeholder="0">
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
                    store_name: '',
                    store_address: '',
                    store_phone: '',
                    store_email: '',
                    currency: 'AFN',
                    currency_symbol: 'Af',
                    timezone: 'Asia/Kabul',
                    default_calendar: 'hijri',
                    default_language: 'en',
                    date_format: 'd M Y',
                    auto_logout: '30',
                    require_pin: true,
                    audit_log: true,
                    printer_type: 'thermal',
                    printer_port: 'USB001',
                    drawer_enabled: true,
                    scanner_enabled: true,
                    receipt_footer: 'شکریه — Thank you',
                    auto_print: true,
                },

                saving: false,
                saveMsg: '',

                /* categories */
                categories: [],
                catsLoading: true,
                showCatModal: false,
                editingCat: null,
                cf: {},
                catError: '',

                /* attributes */
                attributes: [],
                attrsLoading: true,
                showAttrModal: false,
                editingAttr: null,
                af: {},
                attrError: '',

                /* attribute values */
                showValueModal: false,
                valueAttr: null,
                vf: {},
                valueError: '',
                swatchColors: [
                    '#dc2626', '#ea580c', '#d97706', '#65a30d', '#16a34a',
                    '#0891b2', '#2563eb', '#7c3aed', '#db2777', '#64748b',
                    '#ffffff', '#1a1d2e',
                ],

                /* hardware */
                hwTesting: false,
                devices: [{
                        key: 'printer',
                        name: 'Receipt Printer',
                        icon: 'fas fa-print',
                        status: 'idle',
                        port: 'USB001',
                        port_placeholder: 'USB001 or COM3',
                        enabled: true,
                        testing: false,
                        message: ''
                    },
                    {
                        key: 'scanner',
                        name: 'Barcode Scanner',
                        icon: 'fas fa-barcode',
                        status: 'idle',
                        port: 'USB',
                        port_placeholder: 'USB / COM port',
                        enabled: true,
                        testing: false,
                        message: ''
                    },
                    {
                        key: 'drawer',
                        name: 'Cash Drawer',
                        icon: 'fas fa-cash-register',
                        status: 'idle',
                        port: 'COM3',
                        port_placeholder: 'COM port',
                        enabled: true,
                        testing: false,
                        message: ''
                    },
                    {
                        key: 'terminal',
                        name: 'Card Terminal',
                        icon: 'fas fa-credit-card',
                        status: 'idle',
                        port: '',
                        port_placeholder: 'IP address or port',
                        enabled: false,
                        testing: false,
                        message: ''
                    },
                ],

                /* audit log */
                auditLog: [],
                auditLoading: false,

                /* urls */
                urls: {
                    settings: '{{ route('pos.settings.index') }}',
                    save: '{{ route('pos.settings.save') }}',
                    categories: '{{ route('pos.settings.categories.index') }}',
                    saveCat: '{{ route('pos.settings.categories.store') }}',
                    deleteCat: '{{ url('pos/settings/categories') }}',
                    attributes: '{{ route('pos.settings.attributes.index') }}',
                    saveAttr: '{{ route('pos.settings.attributes.store') }}',
                    deleteAttr: '{{ url('pos/settings/attributes') }}',
                    saveValue: '{{ route('pos.settings.attributes.values.store') }}',
                    deleteValue: '{{ url('pos/settings/attributes/values') }}',
                    hwTest: '{{ route('pos.settings.hardware.test') }}',
                    audit: '{{ route('pos.settings.audit') }}',
                    csrf: document.querySelector('meta[name=csrf-token]').content,
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
                        const r = await fetch(this.urls.settings, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const d = await r.json();
                        this.s = {
                            ...this.s,
                            ...d
                        };
                    } catch (e) {
                        console.error(e);
                    }
                },

                /* ── Save settings by group ── */
                async saveGroup(group) {
                    this.saving = true;
                    this.saveMsg = '';
                    try {
                        const r = await fetch(this.urls.save, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                group,
                                settings: this.s
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.saveMsg = '{{ __('messages.saved_successfully') }}';
                            setTimeout(() => this.saveMsg = '', 3000);
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.saving = false;
                    }
                },

                /* ══════════════════════════════
                   CATEGORIES
                ══════════════════════════════ */
                async loadCategories() {
                    this.catsLoading = true;
                    try {
                        const r = await fetch(this.urls.categories, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const raw = await r.json();
                        // Build tree
                        const map = {};
                        raw.forEach(c => {
                            map[c.id] = {
                                ...c,
                                children: [],
                                open: false
                            };
                        });
                        const tree = [];
                        raw.forEach(c => {
                            if (c.parent_id && map[c.parent_id]) map[c.parent_id].children
                                .push(map[c.id]);
                            else if (!c.parent_id) tree.push(map[c.id]);
                        });
                        this.categories = tree;
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.catsLoading = false;
                    }
                },

                openCatModal(cat, parentId) {
                    this.editingCat = cat;
                    this.catError = '';
                    this.cf = cat ?
                        {
                            ...cat
                        } :
                        {
                            name: '',
                            name_ps: '',
                            name_dr: '',
                            code: '',
                            parent_id: parentId || '',
                            sort_order: 0,
                            low_stock_threshold: 10,
                            is_active: true
                        };
                    this.showCatModal = true;
                },

                async saveCategory() {
                    if (!this.cf.name.trim()) {
                        this.catError = '{{ __('messages.name_required') }}';
                        return;
                    }
                    this.saving = true;
                    this.catError = '';
                    try {
                        const r = await fetch(this.urls.saveCat, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                ...this.cf,
                                category_id: this.editingCat?.id
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showCatModal = false;
                            this.loadCategories();
                        } else this.catError = d.message ?? '{{ __('messages.failed') }}.';
                    } catch (e) {
                        this.catError = '{{ __('messages.network_error') }}.';
                    } finally {
                        this.saving = false;
                    }
                },

                async deleteCategory(cat) {
                    if (!confirm(`Delete "${cat.name}"?`)) return;
                    await fetch(`${this.urls.deleteCat}/${cat.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': this.urls.csrf
                        }
                    });
                    this.loadCategories();
                },

                /* ══════════════════════════════
                   ATTRIBUTES
                ══════════════════════════════ */
                async loadAttributes() {
                    this.attrsLoading = true;
                    try {
                        const r = await fetch(this.urls.attributes, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await r.json();
                        this.attributes = data.map(a => ({
                            ...a,
                            open: false
                        }));
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.attrsLoading = false;
                    }
                },

                openAttrModal(attr) {
                    this.editingAttr = attr;
                    this.attrError = '';
                    this.af = attr ? {
                        ...attr
                    } : {
                        name: '',
                        name_ps: '',
                        name_dr: '',
                        data_type: 'string'
                    };
                    this.showAttrModal = true;
                },

                async saveAttribute() {
                    if (!this.af.name.trim()) {
                        this.attrError = '{{ __('messages.name_required') }}.';
                        return;
                    }
                    this.saving = true;
                    this.attrError = '';
                    try {
                        const r = await fetch(this.urls.saveAttr, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                ...this.af,
                                attribute_id: this.editingAttr?.id
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showAttrModal = false;
                            this.loadAttributes();
                        } else this.attrError = d.message ?? '{{ __('messages.failed') }}.';
                    } catch (e) {
                        this.attrError = '{{ __('messages.network_error') }}.';
                    } finally {
                        this.saving = false;
                    }
                },

                async deleteAttribute(attr) {
                    if (!confirm(`Delete attribute "${attr.name}"?`)) return;
                    await fetch(`${this.urls.deleteAttr}/${attr.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': this.urls.csrf
                        }
                    });
                    this.loadAttributes();
                },

                /* Attribute values */
                openValueModal(attr) {
                    this.valueAttr = attr;
                    this.valueError = '';
                    this.vf = {
                        value: '',
                        value_ps: '',
                        value_dr: '',
                        color_code: '',
                        sort_order: 0
                    };
                    this.showValueModal = true;
                },

                async saveValue() {
                    if (!this.vf.value.trim()) {
                        this.valueError = '{{ __('messages.value_required') }}.';
                        return;
                    }
                    this.saving = true;
                    this.valueError = '';
                    try {
                        const r = await fetch(this.urls.saveValue, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                ...this.vf,
                                attribute_id: this.valueAttr.id
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showValueModal = false;
                            this.loadAttributes();
                        } else this.valueError = d.message ?? '{{ __('messages.failed') }}.';
                    } catch (e) {
                        this.valueError = '{{ __('messages.network_error') }}.';
                    } finally {
                        this.saving = false;
                    }
                },

                async deleteAttrValue(attr, val) {
                    if (!confirm(`Delete value "${val.value}"?`)) return;
                    await fetch(`${this.urls.deleteValue}/${val.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': this.urls.csrf
                        }
                    });
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
                    device.testing = true;
                    device.status = 'idle';
                    device.message = '';
                    try {
                        const r = await fetch(this.urls.hwTest, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                device: device.key,
                                port: device.port
                            })
                        });
                        const d = await r.json();
                        device.status = d.success ? 'ok' : 'err';
                        device.message = d.message;
                    } catch (e) {
                        device.status = 'err';
                        device.message = '{{ __('messages.connection_failed') }}';
                    } finally {
                        device.testing = false;
                    }
                },

                statusLabel(s) {
                    return {
                        ok: 'Connected',
                        warn: 'Warning',
                        err: 'Not Connected',
                        idle: 'Not tested'
                    } [s] || '—';
                },

                /* ══════════════════════════════
                   AUDIT LOG
                ══════════════════════════════ */
                async loadAuditLog() {
                    this.auditLoading = true;
                    try {
                        const r = await fetch(this.urls.audit, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        this.auditLog = await r.json();
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.auditLoading = false;
                    }
                },

            }));
        });
    </script>
@endpush
