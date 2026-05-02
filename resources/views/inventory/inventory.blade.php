@extends('layouts.app')

@push('styles')
    @vite(['resources/css/pages/inventory.css'])
@endpush

@section('content')
    <div class="inv" x-data="inventoryPage()" x-init="init()">

        {{-- ════ TOPBAR ════ --}}
        <div class="inv-top">
            <div style="display:flex;align-items:center;gap:10px">
                <div class="inv-title">Afghan POS — <em>Inventory</em></div>
            </div>
            <div class="top-actions">
                <button class="btn btn-ghost" @click="showPurchaseDrawer=true">
                    <i class="fas fa-truck"></i> New Purchase Order
                </button>
                <button class="btn btn-ghost" @click="showAdjustModal=true">
                    <i class="fas fa-sliders"></i> Adjust Stock
                </button>
                <button class="btn btn-primary" @click="showProductModal=true; editingProduct=null; resetProductForm()">
                    <i class="fas fa-plus"></i> Add Product
                </button>
            </div>
        </div>

        {{-- ════ STAT STRIP ════ --}}
        <div class="stat-strip">
            <div class="stat-tile" style="--accent:var(--blue)">
                <div class="st-label">Total Products <span class="st-icon" style="color:var(--blue)"><i
                            class="fas fa-boxes-stacked"></i></span></div>
                <div class="st-val">{{ number_format($totalProducts ?? 0) }}</div>
                <div class="st-sub">{{ $activeProducts ?? 0 }} active variants</div>
            </div>
            <div class="stat-tile" style="--accent:var(--red)">
                <div class="st-label">Low Stock <span class="st-icon" style="color:var(--red)"><i
                            class="fas fa-triangle-exclamation"></i></span></div>
                <div class="st-val" style="color:var(--red)">{{ $lowStockCount ?? 0 }}</div>
                <div class="st-sub">below threshold</div>
            </div>
            <div class="stat-tile" style="--accent:var(--amber)">
                <div class="st-label">Expiring Soon <span class="st-icon" style="color:var(--amber)"><i
                            class="fas fa-clock"></i></span></div>
                <div class="st-val" style="color:var(--amber)">{{ $expiringSoon ?? 0 }}</div>
                <div class="st-sub">within 30 days</div>
            </div>
            <div class="stat-tile" style="--accent:var(--green)">
                <div class="st-label">Inventory Value <span class="st-icon" style="color:var(--green)"><i
                            class="fas fa-coins"></i></span></div>
                <div class="st-val" style="font-size:18px">Af {{ number_format($inventoryValue ?? 0) }}</div>
                <div class="st-sub">at cost price</div>
            </div>
            <div class="stat-tile" style="--accent:var(--teal)">
                <div class="st-label">Categories <span class="st-icon" style="color:var(--teal)"><i
                            class="fas fa-tag"></i></span></div>
                <div class="st-val">{{ $categoryCount ?? 0 }}</div>
                <div class="st-sub">active categories</div>
            </div>
        </div>

        {{-- ════ ALERT BARS ════ --}}
        @if (($lowStockCount ?? 0) > 0)
            <div class="alert-bar danger">
                <i class="fas fa-circle-exclamation"></i>
                <span><strong>{{ $lowStockCount }} variants</strong> are below their minimum stock threshold. <a
                        @click="filterTab='low_stock'">View them →</a></span>
            </div>
        @endif
        @if (($expiringSoon ?? 0) > 0)
            <div class="alert-bar warn">
                <i class="fas fa-clock"></i>
                <span><strong>{{ $expiringSoon }} variants</strong> expire within 30 days. <a
                        @click="filterTab='expiring'">View them →</a></span>
            </div>
        @endif

        {{-- ════ TOOLBAR ════ --}}
        <div class="inv-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input class="inv-search" type="text" x-model="search" @input.debounce.350ms="loadProducts()"
                    placeholder="Search name, SKU, barcode…">
            </div>

            <select class="filter-select" x-model="filterCategory" @change="loadProducts()">
                <option value="">All Categories</option>
                @foreach ($categories ?? [] as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <select class="filter-select" x-model="filterSupplier" @change="loadProducts()">
                <option value="">All Suppliers</option>
                @foreach ($suppliers ?? [] as $sup)
                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                @endforeach
            </select>

            <select class="filter-select" x-model="filterStock" @change="loadProducts()">
                <option value="">All Stock</option>
                <option value="ok">In Stock</option>
                <option value="low">Low Stock</option>
                <option value="zero">Out of Stock</option>
            </select>

         
            <div class="view-toggle">
                <button type="button" class="vt-btn" :class="viewMode === 'table' ? 'active' : ''"
                    @click="viewMode='table'">
                    <i class="fas fa-table-list"></i>
                </button>
                <button type="button" class="vt-btn" :class="viewMode === 'grid' ? 'active' : ''"
                    @click="viewMode='grid'">
                    <i class="fas fa-grip"></i>
                </button>
            </div>

            <div class="tab-row">
                <button type="button" class="tab-btn" :class="filterTab === 'all' ? 'active' : ''"
                    @click="filterTab='all';loadProducts()">All</button>
                <button type="button" class="tab-btn" :class="filterTab === 'low_stock' ? 'active' : ''"
                    @click="filterTab='low_stock';loadProducts()">Low Stock</button>
                <button type="button" class="tab-btn" :class="filterTab === 'expiring' ? 'active' : ''"
                    @click="filterTab='expiring';loadProducts()">Expiring</button>
                <button type="button" class="tab-btn" :class="filterTab === 'inactive' ? 'active' : ''"
                    @click="filterTab='inactive';loadProducts()">Inactive</button>
            </div>
        </div>

        {{-- ════ TABLE VIEW ════ --}}
        <div x-show="viewMode==='table'">
            <div class="table-wrap">
                {{-- Loading --}}
                <div x-show="loading" style="padding:3rem;text-align:center;color:var(--ink3)">
                    <i class="fas fa-spinner fa-spin" style="font-size:20px"></i>
                </div>

                <div x-show="!loading">
                    {{-- Empty --}}
                    <div class="empty-state" x-show="products.length===0">
                        <i class="fas fa-box-open"></i>
                        <p>No products found.<br>Try a different search or filter.</p>
                    </div>

                    <table class="inv-table" x-show="products.length>0">
                        <thead>
                            <tr>
                                <th @click="sortBy('name')">Product <i class="fas fa-sort"></i></th>
                                <th @click="sortBy('sku')">SKU <i class="fas fa-sort"></i></th>
                                <th @click="sortBy('category')">Category</th>
                                <th @click="sortBy('price')" class="text-right">Sale Price <i class="fas fa-sort"></i>
                                </th>
                                <th @click="sortBy('cost')" class="text-right">Cost <i class="fas fa-sort"></i></th>
                                <th @click="sortBy('stock')" class="text-center">Stock <i class="fas fa-sort"></i></th>
                                <th class="text-center">Expiry</th>
                                <th class="text-center">Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="p in products" :key="p.variant_id">
                                <tr>
                                    <td>
                                        <div class="cell-product">
                                            <div class="prod-thumb" x-text="p.emoji || '📦'"></div>
                                            <div>
                                                <div class="prod-name" x-text="p.name"></div>
                                                <div class="prod-sku" x-text="p.barcode"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="cell-mono" x-text="p.sku"></td>
                                    <td><span class="pill pill-teal" x-text="p.category"></span></td>
                                    <td class="text-right cell-price">Af <span x-text="fmt(p.price)"></span></td>
                                    <td class="text-right cell-cost">Af <span x-text="fmt(p.cost_price)"></span></td>
                                    <td class="text-center">
                                        <span class="stock-badge"
                                            :class="p.stock_quantity === 0 ? 'stock-zero' : p.stock_quantity <= p.threshold ?
                                                'stock-low' : 'stock-ok'"
                                            x-text="p.stock_quantity + ' ' + (p.unit||'pcs')">
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span x-show="!p.expiry_date" class="expiry-ok">—</span>
                                        <span x-show="p.expiry_date"
                                            :class="p.days_to_expiry < 0 ? 'expiry-due' : p.days_to_expiry <= 30 ?
                                                'expiry-warn' : 'expiry-ok'"
                                            x-text="p.expiry_date"></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="pill" :class="p.is_active ? 'pill-green' : 'pill-red'"
                                            x-text="p.is_active?'Active':'Inactive'"></span>
                                    </td>
                                    <td>
                                        <div class="row-actions">
                                            <button type="button" class="btn btn-ghost btn-sm" title="Edit"
                                                @click="editProduct(p)">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn-amber btn-sm" title="Adjust Stock"
                                                @click="openAdjust(p)">
                                                <i class="fas fa-sliders"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" title="Deactivate"
                                                @click="toggleActive(p)">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="pag-wrap" x-show="pagination.last_page > 1">
                <div class="pag-info">
                    Showing <span x-text="pagination.from"></span>–<span x-text="pagination.to"></span>
                    of <span x-text="pagination.total"></span> variants
                </div>
                <div class="pag-btns">
                    <button class="pag-btn" @click="goPage(pagination.current_page-1)"
                        :disabled="pagination.current_page === 1">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <template x-for="p in pagination.last_page" :key="p">
                        <button class="pag-btn" :class="p === pagination.current_page ? 'active' : ''" @click="goPage(p)"
                            x-text="p"></button>
                    </template>
                    <button class="pag-btn" @click="goPage(pagination.current_page+1)"
                        :disabled="pagination.current_page === pagination.last_page">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ════ GRID VIEW ════ --}}
        <div class="grid-wrap" x-show="viewMode==='grid'" x-cloak>
            <div class="empty-state" x-show="products.length===0" style="grid-column:1/-1">
                <i class="fas fa-box-open"></i>
                <p>No products found.</p>
            </div>
            <template x-for="p in products" :key="p.variant_id">
                <div class="grid-card">
                    <div class="gc-thumb">
                        <span x-text="p.emoji || '📦'"></span>
                        <span class="gc-stock-chip"
                            :class="p.stock_quantity === 0 ? 'stock-zero' : p.stock_quantity <= p.threshold ? 'stock-low' :
                                'stock-ok'"
                            x-text="p.stock_quantity"></span>
                    </div>
                    <div class="gc-body">
                        <div class="gc-name" x-text="p.name"></div>
                        <div class="gc-sku" x-text="p.sku"></div>
                        <div class="gc-row">
                            <span class="gc-label">Price</span>
                            <span class="gc-val" style="color:var(--blue)">Af <span x-text="fmt(p.price)"></span></span>
                        </div>
                        <div class="gc-row">
                            <span class="gc-label">Cost</span>
                            <span class="gc-val">Af <span x-text="fmt(p.cost_price)"></span></span>
                        </div>
                        <div class="gc-row">
                            <span class="gc-label">Category</span>
                            <span class="pill pill-teal" x-text="p.category"></span>
                        </div>
                    </div>
                    <div class="gc-footer">
                        <button type="button" class="btn btn-ghost btn-sm" style="flex:1" @click="editProduct(p)">
                            <i class="fas fa-pen"></i> Edit
                        </button>
                        <button type="button" class="btn btn-amber btn-sm" @click="openAdjust(p)">
                            <i class="fas fa-sliders"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- ═══════════════════════════════════════════
     MODAL: ADD / EDIT PRODUCT
═══════════════════════════════════════════ --}}
        <div class="modal-overlay" x-show="showProductModal" x-cloak @click.self="showProductModal=false">
            <div class="modal-card modal-lg">
                <div class="modal-head">
                    <div class="modal-title" x-text="editingProduct ? 'Edit Product Variant' : 'Add New Product'"></div>
                    <button class="modal-close" @click="showProductModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">

                    {{-- Basic Info --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-info-circle"></i> Basic Information</div>
                        <div class="form-grid form-grid-2">
                            <div>
                                <label class="field-label">Product Name (English) <span class="field-req">*</span></label>
                                <input type="text" class="field-input" x-model="pf.name"
                                    placeholder="e.g. Premium Saffron">
                            </div>
                            <div>
                                <label class="field-label">Category <span class="field-req">*</span></label>
                                <select class="field-input" x-model="pf.category_id">
                                    <option value="">Select category</option>
                                    @foreach ($categories ?? [] as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Name (Pashto / پښتو)</label>
                                <input type="text" class="field-input" x-model="pf.name_ps" placeholder="د محصول نوم"
                                    dir="rtl">
                            </div>
                            <div>
                                <label class="field-label">Name (Dari / دری)</label>
                                <input type="text" class="field-input" x-model="pf.name_dr" placeholder="نام محصول"
                                    dir="rtl">
                            </div>
                            <div style="grid-column:span 2">
                                <label class="field-label">Description</label>
                                <textarea class="field-input" x-model="pf.description" placeholder="Optional product description…"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Variant Info --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-barcode"></i> Variant & Pricing</div>
                        <div class="form-grid form-grid-3">
                            <div>
                                <label class="field-label">SKU <span class="field-req">*</span></label>
                                <input type="text" class="field-input" x-model="pf.sku" placeholder="AUTO-001">
                                <div class="field-hint">Unique stock keeping unit</div>
                            </div>
                            <div>
                                <label class="field-label">Barcode <span class="field-req">*</span></label>
                                <input type="text" class="field-input" x-model="pf.barcode"
                                    placeholder="EAN-13 / QR">
                            </div>
                            <div>
                                <label class="field-label">Unit</label>
                                <select class="field-input" x-model="pf.unit">
                                    <option value="piece">Piece</option>
                                    <option value="kg">Kilogram</option>
                                    <option value="gram">Gram</option>
                                    <option value="liter">Liter</option>
                                    <option value="box">Box</option>
                                    <option value="pack">Pack</option>
                                    <option value="dozen">Dozen</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Sale Price (Af) <span class="field-req">*</span></label>
                                <input type="number" class="field-input" x-model.number="pf.price" placeholder="0.00"
                                    min="0" step="0.01">
                            </div>
                            <div>
                                <label class="field-label">Cost Price (Af)</label>
                                <input type="number" class="field-input" x-model.number="pf.cost_price"
                                    placeholder="0.00" min="0" step="0.01">
                            </div>
                            <div>
                                <label class="field-label">Opening Stock</label>
                                <input type="number" class="field-input" x-model.number="pf.stock_quantity"
                                    placeholder="0" min="0">
                            </div>
                            <div>
                                <label class="field-label">Low Stock Threshold</label>
                                <input type="number" class="field-input" x-model.number="pf.low_stock_threshold"
                                    placeholder="10" min="0">
                                <div class="field-hint">Alert when stock falls below this</div>
                            </div>
                            <div>
                                <label class="field-label">Expiry Date</label>
                                <input type="date" class="field-input" x-model="pf.expiry_date">
                            </div>
                            <div>
                                <label class="field-label">Batch Number</label>
                                <input type="text" class="field-input" x-model="pf.batch_number"
                                    placeholder="e.g. BATCH-2024-01">
                            </div>
                        </div>
                    </div>

                    {{-- Profit preview --}}
                    <div x-show="pf.price > 0 && pf.cost_price > 0"
                        style="padding:10px 14px;background:var(--gdim);border:1px solid rgba(21,128,61,.2);border-radius:var(--rsm);margin-bottom:1rem;display:flex;gap:2rem">
                        <div>
                            <div style="font-size:10px;color:var(--ink3);text-transform:uppercase;letter-spacing:.06em">
                                Margin</div>
                            <div style="font-family:var(--mono);font-size:16px;color:var(--green);font-weight:500"
                                x-text="profitMargin + '%'"></div>
                        </div>
                        <div>
                            <div style="font-size:10px;color:var(--ink3);text-transform:uppercase;letter-spacing:.06em">
                                Gross Profit / Unit</div>
                            <div style="font-family:var(--mono);font-size:16px;color:var(--green);font-weight:500"
                                x-text="'Af ' + fmt(pf.price - pf.cost_price)"></div>
                        </div>
                    </div>

                    {{-- Error --}}
                    <div x-show="formError" x-cloak
                        style="padding:10px 14px;background:var(--rdim);border:1px solid rgba(220,38,38,.2);border-radius:var(--rsm);font-size:12px;color:var(--red)"
                        x-text="formError"></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" @click="showProductModal=false">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="saveProduct()" :disabled="saving">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="saving ? 'Saving…' : (editingProduct ? 'Update Product' : 'Add Product')"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
     MODAL: ADJUST STOCK
═══════════════════════════════════════════ --}}
        <div class="modal-overlay" x-show="showAdjustModal" x-cloak @click.self="showAdjustModal=false">
            <div class="modal-card modal-md">
                <div class="modal-head">
                    <div class="modal-title">Stock Adjustment</div>
                    <button class="modal-close" @click="showAdjustModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">

                    {{-- Product selector (if opened from toolbar not row) --}}
                    <div x-show="!adjustTarget" class="form-grid" style="margin-bottom:1rem">
                        <div>
                            <label class="field-label">Select Variant <span class="field-req">*</span></label>
                            <input type="text" class="field-input" x-model="adjustSearch"
                                @input.debounce.300ms="searchForAdjust()" placeholder="Search product or SKU…">
                        </div>
                        <div x-show="adjustSearchResults.length > 0"
                            style="border:1px solid var(--border);border-radius:var(--rsm);overflow:hidden;max-height:160px;overflow-y:auto">
                            <template x-for="r in adjustSearchResults" :key="r.variant_id">
                                <div class="sr-item"
                                    style="padding:9px 12px;display:flex;justify-content:space-between;cursor:pointer;border-bottom:1px solid var(--border)"
                                    @click="adjustTarget=r;adjustSearch='';adjustSearchResults=[]">
                                    <span style="font-size:13px;font-weight:500" x-text="r.name"></span>
                                    <span style="font-family:var(--mono);font-size:12px;color:var(--ink3)"
                                        x-text="r.sku + ' · ' + r.stock_quantity + ' in stock'"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Selected product info --}}
                    <div x-show="adjustTarget" x-cloak
                        style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--bdim);border:1px solid var(--bmid);border-radius:var(--rsm);margin-bottom:1rem">
                        <div>
                            <div style="font-weight:600;font-size:13px" x-text="adjustTarget?.name"></div>
                            <div style="font-family:var(--mono);font-size:11px;color:var(--ink3)"
                                x-text="adjustTarget?.sku"></div>
                        </div>
                        <div style="text-align:right">
                            <div style="font-size:10px;color:var(--ink3);text-transform:uppercase;letter-spacing:.06em">
                                Current Stock</div>
                            <div style="font-family:var(--mono);font-size:18px;font-weight:500;color:var(--blue)"
                                x-text="adjustTarget?.stock_quantity + ' ' + (adjustTarget?.unit||'pcs')"></div>
                        </div>
                        <button type="button" @click="adjustTarget=null"
                            style="background:none;border:none;cursor:pointer;color:var(--ink3);font-size:14px"><i
                                class="fas fa-times"></i></button>
                    </div>

                    {{-- Adjustment type --}}
                    <label class="field-label" style="margin-bottom:.5rem">Adjustment Type <span
                            class="field-req">*</span></label>
                    <div class="adj-type-grid">
                        <button type="button" class="adj-type-btn"
                            :class="af.type === 'increase' ? 'active-increase' : ''" @click="af.type='increase'">
                            <span class="adj-icon">📈</span><span class="adj-lbl">Increase</span>
                        </button>
                        <button type="button" class="adj-type-btn"
                            :class="af.type === 'decrease' ? 'active-decrease' : ''" @click="af.type='decrease'">
                            <span class="adj-icon">📉</span><span class="adj-lbl">Decrease</span>
                        </button>
                        <button type="button" class="adj-type-btn"
                            :class="af.type === 'correction' ? 'active-correction' : ''" @click="af.type='correction'">
                            <span class="adj-icon">🔧</span><span class="adj-lbl">Correction</span>
                        </button>
                        <button type="button" class="adj-type-btn"
                            :class="af.type === 'damage' ? 'active-damage' : ''" @click="af.type='damage'">
                            <span class="adj-icon">⚠️</span><span class="adj-lbl">Damage</span>
                        </button>
                        <button type="button" class="adj-type-btn"
                            :class="af.type === 'expiry' ? 'active-expiry' : ''" @click="af.type='expiry'">
                            <span class="adj-icon">🗓️</span><span class="adj-lbl">Expiry</span>
                        </button>
                        <button type="button" class="adj-type-btn"
                            :class="af.type === 'return_to_supplier' ? 'active-return_to_supplier' : ''"
                            @click="af.type='return_to_supplier'">
                            <span class="adj-icon">↩️</span><span class="adj-lbl">Return</span>
                        </button>
                    </div>

                    <div class="form-grid form-grid-2" style="margin-top:.75rem">
                        <div>
                            <label class="field-label">Quantity <span class="field-req">*</span></label>
                            <input type="number" class="field-input" x-model.number="af.quantity" min="1"
                                placeholder="0">
                        </div>
                        <div x-show="af.type==='correction'">
                            <label class="field-label">New Stock Count</label>
                            <input type="number" class="field-input" x-model.number="af.new_count" min="0"
                                placeholder="Actual count">
                        </div>
                    </div>

                    <div style="margin-top:.75rem">
                        <label class="field-label">Reason <span class="field-req">*</span></label>
                        <textarea class="field-input" x-model="af.reason" placeholder="Explain why this adjustment is being made…"></textarea>
                    </div>

                    {{-- Preview --}}
                    <div class="adj-preview" x-show="adjustTarget && af.quantity > 0"
                        :class="af.type === 'increase' ? 'increase' : af.type === 'decrease' || af.type === 'damage' || af
                            .type === 'expiry' || af.type === 'return_to_supplier' ? 'decrease' : 'neutral'">
                        <span
                            x-text="adjustTarget?.stock_quantity + ' → ' + previewStock + ' ' + (adjustTarget?.unit||'pcs')"></span>
                        <span style="font-weight:700" x-text="(af.type==='increase'?'+':'-') + af.quantity"></span>
                    </div>

                    <div x-show="formError" x-cloak
                        style="margin-top:.75rem;padding:10px 14px;background:var(--rdim);border:1px solid rgba(220,38,38,.2);border-radius:var(--rsm);font-size:12px;color:var(--red)"
                        x-text="formError"></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost"
                        @click="showAdjustModal=false;adjustTarget=null">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="saveAdjustment()" :disabled="saving">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="saving?'Saving…':'Save Adjustment'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
     DRAWER: NEW PURCHASE ORDER
═══════════════════════════════════════════ --}}
        <div class="drawer-overlay" x-show="showPurchaseDrawer" x-cloak @click="showPurchaseDrawer=false"></div>
        <div class="drawer" x-show="showPurchaseDrawer" x-cloak>
            <div class="drawer-head">
                <div style="font-family:var(--display);font-size:17px;font-weight:500;color:var(--ink)">New Purchase Order
                </div>
                <button class="modal-close" @click="showPurchaseDrawer=false"><i class="fas fa-times"></i></button>
            </div>
            <div class="drawer-body">

                <div class="form-section">
                    <div class="form-section-title"><i class="fas fa-truck"></i> Supplier & Reference</div>
                    <div class="form-grid">
                        <div>
                            <label class="field-label">Supplier <span class="field-req">*</span></label>
                            <select class="field-input" x-model="po.supplier_id">
                                <option value="">Select supplier</option>
                                @foreach ($suppliers ?? [] as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-grid" style="grid-template-columns:1fr 1fr;gap:8px">
                            <div>
                                <label class="field-label">Purchase Date <span class="field-req">*</span></label>
                                <input type="date" class="field-input" x-model="po.purchase_date">
                            </div>
                            <div>
                                <label class="field-label">Expected Delivery</label>
                                <input type="date" class="field-input" x-model="po.delivery_date">
                            </div>
                        </div>
                        <div>
                            <label class="field-label">Reference / Invoice #</label>
                            <input type="text" class="field-input" x-model="po.reference_number"
                                placeholder="Supplier invoice number">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title"><i class="fas fa-list"></i> Order Items</div>

                    {{-- Item search --}}
                    <div style="position:relative;margin-bottom:.75rem">
                        <input type="text" class="field-input" x-model="poItemSearch"
                            @input.debounce.300ms="searchForPO()" placeholder="Search product to add…">
                        <div x-show="poSearchResults.length > 0" x-cloak
                            style="position:absolute;left:0;right:0;top:100%;background:var(--surface);border:1px solid var(--border);border-radius:var(--rsm);box-shadow:var(--shmd);z-index:10;max-height:180px;overflow-y:auto">
                            <template x-for="r in poSearchResults" :key="r.variant_id">
                                <div style="padding:9px 12px;cursor:pointer;display:flex;justify-content:space-between;border-bottom:1px solid var(--border);font-size:13px"
                                    @mousedown.prevent="addPOItem(r)">
                                    <span x-text="r.name"></span>
                                    <span style="font-family:var(--mono);font-size:11px;color:var(--ink3)"
                                        x-text="r.sku"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <table class="po-items" x-show="po.items.length > 0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Unit Cost</th>
                                <th class="text-right">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item,idx) in po.items" :key="idx">
                                <tr>
                                    <td>
                                        <div style="font-size:12px;font-weight:500" x-text="item.name"></div>
                                        <div style="font-family:var(--mono);font-size:10px;color:var(--ink3)"
                                            x-text="item.sku"></div>
                                    </td>
                                    <td class="text-right">
                                        <input type="number" x-model.number="item.quantity_ordered" min="1"
                                            style="width:60px;padding:4px 6px;border:1px solid var(--border);border-radius:4px;font-family:var(--mono);font-size:12px;text-align:right">
                                    </td>
                                    <td class="text-right">
                                        <input type="number" x-model.number="item.unit_cost" min="0"
                                            step="0.01"
                                            style="width:80px;padding:4px 6px;border:1px solid var(--border);border-radius:4px;font-family:var(--mono);font-size:12px;text-align:right">
                                    </td>
                                    <td class="text-right"
                                        style="font-family:var(--mono);font-size:12px;font-weight:500;color:var(--blue)"
                                        x-text="'Af ' + fmt(item.quantity_ordered * item.unit_cost)"></td>
                                    <td>
                                        <button type="button" @click="po.items.splice(idx,1)"
                                            style="background:none;border:none;cursor:pointer;color:var(--ink4);font-size:12px">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <div x-show="po.items.length === 0" class="empty-state" style="padding:1.5rem">
                        <i class="fas fa-cart-plus" style="font-size:24px"></i>
                        <p>Search and add products above</p>
                    </div>

                    {{-- PO Total --}}
                    <div x-show="po.items.length > 0" x-cloak
                        style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:var(--s2);border-radius:var(--rsm);margin-top:8px;border:1px solid var(--border)">
                        <span
                            style="font-size:12px;color:var(--ink3);font-weight:600;text-transform:uppercase;letter-spacing:.06em">Order
                            Total</span>
                        <span style="font-family:var(--mono);font-size:18px;font-weight:600;color:var(--blue)"
                            x-text="'Af ' + fmt(poTotal)"></span>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title"><i class="fas fa-pen"></i> Notes</div>
                    <textarea class="field-input" x-model="po.notes" placeholder="Optional notes for this purchase order…"
                        rows="2"></textarea>
                </div>

                <div x-show="formError" x-cloak
                    style="padding:10px 14px;background:var(--rdim);border:1px solid rgba(220,38,38,.2);border-radius:var(--rsm);font-size:12px;color:var(--red)"
                    x-text="formError"></div>
            </div>
            <div class="drawer-foot">
                <button type="button" class="btn btn-ghost" @click="showPurchaseDrawer=false">Cancel</button>
                <button type="button" class="btn btn-primary" @click="savePurchaseOrder()" :disabled="saving">
                    <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                    <span x-text="saving?'Saving…':'Create Purchase Order'"></span>
                </button>
            </div>
        </div>

    </div>{{-- /inv --}}
@endsection

@push('scripts')
    <script>
        function inventoryPage() {
            return {
                /* state */
                products: [],
                pagination: {},
                loading: true,
                search: '',
                filterCategory: '',
                filterSupplier: '',
                filterStock: '',
                filterTab: 'all',
                viewMode: 'table',
                sortCol: 'name',
                sortDir: 'asc',
                currentPage: 1,

                /* modals */
                showProductModal: false,
                showAdjustModal: false,
                showPurchaseDrawer: false,
                saving: false,
                formError: '',

                /* product form */
                editingProduct: null,
                pf: {},

                /* adjust form */
                adjustTarget: null,
                adjustSearch: '',
                adjustSearchResults: [],
                af: {
                    type: 'increase',
                    quantity: 0,
                    reason: '',
                    new_count: 0
                },

                /* purchase order */
                po: {
                    supplier_id: '',
                    purchase_date: '',
                    delivery_date: '',
                    reference_number: '',
                    notes: '',
                    items: []
                },
                poItemSearch: '',
                poSearchResults: [],

                /* urls */
                get urls() {
                    return {
                        products: '{{ route('pos.inventory.products') }}',
                        saveProduct: '{{ route('pos.inventory.products.store') }}',
                        adjust: '{{ route('pos.inventory.adjust') }}',
                        purchase: '{{ route('pos.inventory.purchase.store') }}',
                        search: '{{ route('pos.products.search') }}',
                        toggle: '{{ url('pos/inventory/products') }}',
                        csrf: document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}'
                    };
                },

                /* ── computed ── */
                get profitMargin() {
                    if (!this.pf.price || !this.pf.cost_price) return 0;
                    return (((this.pf.price - this.pf.cost_price) / this.pf.price) * 100).toFixed(1);
                },
                get previewStock() {
                    if (!this.adjustTarget) return 0;
                    const curr = this.adjustTarget.stock_quantity;
                    const qty = this.af.quantity || 0;
                    if (this.af.type === 'correction') return this.af.new_count;
                    if (['increase'].includes(this.af.type)) return curr + qty;
                    return Math.max(0, curr - qty);
                },
                get poTotal() {
                    return this.po.items.reduce((s, i) => s + (i.quantity_ordered * i.unit_cost), 0);
                },

                /* ── init ── */
                async init() {
                    await this.loadProducts();
                },

                /* ── load products ── */
                async loadProducts() {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams({
                            q: this.search,
                            category: this.filterCategory,
                            supplier: this.filterSupplier,
                            stock: this.filterStock,
                            tab: this.filterTab,
                            sort: this.sortCol,
                            dir: this.sortDir,
                            page: this.currentPage,
                        });
                        const r = await fetch(this.urls.products + '?' + params, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const d = await r.json();
                        this.products = d.data;
                        this.pagination = d.meta;
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.loading = false;
                    }
                },

                sortBy(col) {
                    if (this.sortCol === col) this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                    else {
                        this.sortCol = col;
                        this.sortDir = 'asc';
                    }
                    this.loadProducts();
                },

                goPage(p) {
                    if (p < 1 || p > this.pagination.last_page) return;
                    this.currentPage = p;
                    this.loadProducts();
                },

                /* ── product form ── */
                resetProductForm() {
                    this.pf = {
                        name: '',
                        name_ps: '',
                        name_dr: '',
                        description: '',
                        category_id: '',
                        sku: '',
                        barcode: '',
                        unit: 'piece',
                        price: 0,
                        cost_price: 0,
                        stock_quantity: 0,
                        low_stock_threshold: 10,
                        expiry_date: '',
                        batch_number: ''
                    };
                    this.formError = '';
                },

                editProduct(p) {
                    this.editingProduct = p;
                    this.pf = {
                        ...p
                    };
                    this.showProductModal = true;
                    this.formError = '';
                },

                async saveProduct() {
                    if (!this.pf.name || !this.pf.sku || !this.pf.barcode) {
                        this.formError = 'Name, SKU and Barcode are required.';
                        return;
                    }
                    this.saving = true;
                    this.formError = '';
                    try {
                        const r = await fetch(this.urls.saveProduct, {
                            method: 'POST', // ← always POST, never PUT
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                ...this.pf,
                                variant_id: this.editingProduct?.variant_id
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showProductModal = false;
                            this.loadProducts();
                        } else this.formError = d.message ?? 'Failed to save.';
                    } catch (e) {
                        this.formError = 'Network error.';
                    } finally {
                        this.saving = false;
                    }
                },

                async toggleActive(p) {
                    if (!confirm(`${p.is_active ? 'Deactivate' : 'Activate'} ${p.name}?`)) return;
                    await fetch(`${this.urls.toggle}/${p.variant_id}/toggle`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.urls.csrf
                        }
                    });
                    this.loadProducts();
                },

                /* ── adjust ── */
                openAdjust(p) {
                    this.adjustTarget = p;
                    this.af = {
                        type: 'increase',
                        quantity: 0,
                        reason: '',
                        new_count: 0
                    };
                    this.showAdjustModal = true;
                    this.formError = '';
                },

                async searchForAdjust() {
                    if (!this.adjustSearch.trim()) {
                        this.adjustSearchResults = [];
                        return;
                    }
                    const r = await fetch(this.urls.search + '?q=' + encodeURIComponent(this.adjustSearch), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    this.adjustSearchResults = await r.json();
                },

                async saveAdjustment() {
                    if (!this.adjustTarget) {
                        this.formError = 'Select a product.';
                        return;
                    }
                    if (!this.af.quantity && this.af.type !== 'correction') {
                        this.formError = 'Enter a quantity.';
                        return;
                    }
                    if (!this.af.reason.trim()) {
                        this.formError = 'Reason is required.';
                        return;
                    }
                    this.saving = true;
                    this.formError = '';
                    try {
                        const r = await fetch(this.urls.adjust, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                variant_id: this.adjustTarget.variant_id,
                                ...this.af
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showAdjustModal = false;
                            this.adjustTarget = null;
                            this.loadProducts();
                        } else this.formError = d.message ?? 'Adjustment failed.';
                    } catch (e) {
                        this.formError = 'Network error.';
                    } finally {
                        this.saving = false;
                    }
                },

                /* ── purchase order ── */
                async searchForPO() {
                    if (!this.poItemSearch.trim()) {
                        this.poSearchResults = [];
                        return;
                    }
                    const r = await fetch(this.urls.search + '?q=' + encodeURIComponent(this.poItemSearch), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    this.poSearchResults = await r.json();
                },

                addPOItem(p) {
                    const exists = this.po.items.find(i => i.variant_id === p.variant_id);
                    if (exists) {
                        exists.quantity_ordered++;
                    } else this.po.items.push({
                        variant_id: p.variant_id,
                        name: p.name,
                        sku: p.sku,
                        quantity_ordered: 1,
                        unit_cost: p.cost_price || 0
                    });
                    this.poItemSearch = '';
                    this.poSearchResults = [];
                },

                async savePurchaseOrder() {
                    if (!this.po.supplier_id) {
                        this.formError = 'Select a supplier.';
                        return;
                    }
                    if (!this.po.purchase_date) {
                        this.formError = 'Purchase date is required.';
                        return;
                    }
                    if (!this.po.items.length) {
                        this.formError = 'Add at least one item.';
                        return;
                    }
                    this.saving = true;
                    this.formError = '';
                    try {
                        const r = await fetch(this.urls.purchase, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                ...this.po,
                                total_cost: this.poTotal
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showPurchaseDrawer = false;
                            this.po = {
                                supplier_id: '',
                                purchase_date: '',
                                delivery_date: '',
                                reference_number: '',
                                notes: '',
                                items: []
                            };
                            alert(`Purchase Order ${d.local_id} created.`);
                        } else this.formError = d.message ?? 'Failed.';
                    } catch (e) {
                        this.formError = 'Network error.';
                    } finally {
                        this.saving = false;
                    }
                },

                fmt(n) {
                    return Number(n || 0).toLocaleString('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                }
            };
        }
    </script>
@endpush
