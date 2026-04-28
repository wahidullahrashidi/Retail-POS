
/* â”€â”€ Live clock â”€â”€ */
(function tick() {
    const el = document.getElementById('liveClock');
    if (el) {
        const now = new Date();
        el.textContent = now.toLocaleTimeString('en-GB');
    }
    setTimeout(tick, 1000);
})();

/* â”€â”€ Cart System â”€â”€ */
function cartSystem() {
    return {
        query: '',
        searchResults: [],
        trendingProducts: [],
        trendingLoading: true,
        cart: [],
        searching: false,

        urls: {
            search: '{{ route('pos.products.search') }}',
            trending: '{{ route('pos.products.trending') }}',
            checkout: '{{ route('pos.checkout') }}',
            csrf: '{{ csrf_token() }}'
        },

        get cartTotal() {
            return this.cart.reduce((s, i) => s + i.price * i.qty, 0);
        },

        async init() {
            await this.loadTrending();
        },

        async loadTrending() {
            this.trendingLoading = true;
            try {
                const r = await fetch(this.urls.trending, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                this.trendingProducts = await r.json();
            } catch (e) {
                console.error('Trending failed:', e);
            } finally {
                this.trendingLoading = false;
            }
        },

        async search() {
            const q = this.query.trim();
            if (!q) {
                this.searchResults = [];
                return;
            }
            this.searching = true;
            this.searchResults = [];
            try {
                const r = await fetch(this.urls.search + '?q=' + encodeURIComponent(q), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                this.searchResults = await r.json();
            } catch (e) {
                console.error('Search failed:', e);
            } finally {
                this.searching = false;
            }
        },

        clearSearch() {
            this.query = '';
            this.searchResults = [];
            this.searching = false;
        },

        addToCart(p) {
            const ex = this.cart.find(i => i.variant_id === p.variant_id);
            if (ex) {
                if (ex.qty < ex.stock_quantity) ex.qty++;
            } else {
                this.cart.push({
                    ...p,
                    qty: 1
                });
            }
            this.clearSearch();
        },

        incQty(idx) {
            const i = this.cart[idx];
            if (i.qty < i.stock_quantity) i.qty++;
        },

        decQty(idx) {
            if (this.cart[idx].qty > 1) this.cart[idx].qty--;
            else this.removeFromCart(idx);
        },

        removeFromCart(idx) {
            this.cart.splice(idx, 1);
        },

        clearCart() {
            if (confirm('Clear entire cart?')) this.cart = [];
        },

        checkout() {
            if (!this.cart.length) return;
            const f = document.createElement('form');
            f.method = 'POST';
            f.action = this.urls.checkout;
            f.innerHTML = `
    <input type="hidden" name="_token" value="${this.urls.csrf}">
        <input type="hidden" name="cart" value='${JSON.stringify(this.cart)}'>
            `;
            document.body.appendChild(f);
            f.submit();
        },

        fmt(n) {
            return Number(n || 0).toLocaleString('en-US', {
                minimumFractionDigits: 0
            });
        }
    }
}
