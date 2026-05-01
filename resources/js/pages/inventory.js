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
                urls: {
                    products: '{{ route('pos.inventory.products') }}',
                    saveProduct: '{{ route('pos.inventory.products.store') }}',
                    adjust: '{{ route('pos.inventory.adjust') }}',
                    purchase: '{{ route('pos.inventory.purchase.store') }}',
                    search: '{{ route('pos.products.search') }}',
                    csrf: '{{ csrf_token() }}'
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
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                ...this.pf,
                                _method: this.editingProduct ? 'PUT' : 'POST',
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
                    if (!confirm(`${p.is_active?'Deactivate':'Activate'} ${p.name}?`)) return;
                    await fetch(`/pos/inventory/products/${p.variant_id}/toggle`, {
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