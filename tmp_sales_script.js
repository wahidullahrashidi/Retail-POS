
const registerSalesPage = () => {
    Alpine.data('salesPage', () => ({

    /* list */
    sales:       [],
    pagination:  {},
    loading:     true,
    search:      '',
    dateFrom:    '',
    dateTo:      '',
    filterMethod:'',
    filterStatus:'',
    filterCashier:'',
    tab:         'today',
    sortCol:     'created_at',
    sortDir:     'desc',
    currentPage: 1,

    /* detail */
    selected:     null,
    detailItems:  [],
    detailLoading:false,

    /* refund */
    showRefundModal: false,
    refundItems:     [],
    refundReason:    '',
    refundError:     '',
    saving:          false,

    /* urls */
    urls: {
        list:   '"X"',
        detail: '"X"',
        refund: '"X"',
        export: '"X"',
        csrf:   document.querySelector('meta[name=csrf-token]')?.content || '"X"',
    },

    /* computed */
    get refundTotal() {
        return this.refundItems
            .filter(i => i.selected)
            .reduce((s, i) => s + i.unit_price * i.refund_qty, 0);
    },

    /* ── Init ── */
    init() {
        const today = new Date().toISOString().split('T')[0];
        this.dateFrom = today;
        this.dateTo   = today;
        this.loadSales();
    },

    /* ── Load sales list ── */
    async loadSales() {
        this.loading = true;
        try {
            const p = new URLSearchParams({
                q:       this.search,
                from:    this.dateFrom,
                to:      this.dateTo,
                method:  this.filterMethod,
                status:  this.filterStatus,
                cashier: this.filterCashier,
                tab:     this.tab,
                sort:    this.sortCol,
                dir:     this.sortDir,
                page:    this.currentPage,
            });
            console.debug('Loading sales', this.urls.list, p.toString());
            const r = await fetch(this.urls.list + '?' + p, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if (!r.ok) {
                const err = await r.text();
                throw new Error(`Failed to load sales: ${r.status} ${err}`);
            }
            const d = await r.json();
            this.sales      = d.data;
            this.pagination = d.meta;
        } catch(e) { console.error(e); this.sales = []; this.pagination = {}; }
        finally { this.loading = false; }
    },

    sort(col) {
        if (this.sortCol === col) this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
        else { this.sortCol = col; this.sortDir = 'desc'; }
        this.loadSales();
    },

    goPage(p) {
        if (p < 1 || p > this.pagination.last_page) return;
        this.currentPage = p;
        this.loadSales();
    },

    /* ── Detail panel ── */
    async openDetail(s) {
        this.selected    = s;
        this.detailItems = [];
        this.detailLoading = true;
        try {
            console.debug('Loading sale detail', s.id, `${this.urls.detail}/${s.id}/items`);
            const r = await fetch(`${this.urls.detail}/${s.id}/items`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if (!r.ok) {
                const err = await r.text();
                throw new Error(`Failed to load sale detail: ${r.status} ${err}`);
            }
            this.detailItems = await r.json();
        } catch(e) { console.error(e); }
        finally { this.detailLoading = false; }
    },

    /* ── Refund ── */
    openRefund(s) {
        this.refundItems  = this.detailItems.map(i => ({
            ...i,
            selected:   false,
            refund_qty: i.quantity,
        }));
        this.refundReason = '';
        this.refundError  = '';
        this.showRefundModal = true;
    },

    async processRefund() {
        const selected = this.refundItems.filter(i => i.selected);
        if (!selected.length) { this.refundError = 'Select at least one item to refund.'; return; }
        if (!this.refundReason.trim()) { this.refundError = 'Reason is required.'; return; }

        // Validate quantities
        for (const item of selected) {
            if (item.refund_qty < 1 || item.refund_qty > item.quantity) {
                this.refundError = `Invalid quantity for "${item.product_name}".`; return;
            }
        }

        this.saving = true; this.refundError = '';
        try {
            const r = await fetch(this.urls.refund, {
                method:  'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.urls.csrf },
                body: JSON.stringify({
                    sale_id: this.selected.id,
                    items:   selected.map(i => ({ sale_item_id: i.id, qty: i.refund_qty })),
                    reason:  this.refundReason,
                })
            });
            const d = await r.json();
            if (d.success) {
                this.showRefundModal = false;
                this.selected = null;
                this.loadSales();
            } else {
                this.refundError = d.message ?? 'Refund failed.';
            }
        } catch(e) { this.refundError = 'Network error.'; }
        finally { this.saving = false; }
    },

    /* ── Receipt reprint ── */
    reprintReceipt(s) {
        if (!s) return;
        document.getElementById('rp-id').textContent      = 'Sale: ' + s.local_id;
        document.getElementById('rp-date').textContent    = s.date + ' ' + s.time;
        document.getElementById('rp-cashier').textContent = 'Cashier: ' + s.cashier;
        document.getElementById('rp-items').innerHTML     = this.detailItems.map(i =>
            `<div style="display:flex;justify-content:space-between">
                <span>${i.product_name} x${i.quantity}</span>
                <span>Af ${this.fmt(i.line_total)}</span>
            </div>`
        ).join('');
        document.getElementById('rp-totals').innerHTML = `
            <div style="display:flex;justify-content:space-between"><span>Total</span><span>Af ${this.fmt(s.total_amount)}</span></div>
            <div style="display:flex;justify-content:space-between"><span>Method</span><span>${s.payment_method}</span></div>
        `;

        const el = document.getElementById('receipt-print');
        el.style.display = 'block';
        window.print();
        el.style.display = 'none';
    },

    /* ── Export ── */
    exportCsv() {
        const p = new URLSearchParams({ from: this.dateFrom, to: this.dateTo, tab: this.tab, method: this.filterMethod, status: this.filterStatus });
        window.location.href = this.urls.export + '?' + p;
    },

    /* ── Helpers ── */
    fmt(n) {
        return Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    },
}));
};
document.addEventListener('alpine:init', registerSalesPage);
if (window.Alpine && typeof window.Alpine.data === 'function') registerSalesPage();
