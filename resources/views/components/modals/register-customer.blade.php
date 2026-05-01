{{-- ── GLOBAL: Quick Register Customer Modal ── --}}
<div x-data="globalCustomerModal()" x-show="$store.customerModal.show" x-cloak class="modal-overlay"
    @click.self="$store.customerModal.close()">

    <div class="modal-card modal-sm" style="max-width:380px">
        <div class="modal-head">
            <span class="modal-title">
                <i class="fas fa-user-plus" style="margin-right:6px;color:#2563eb"></i>
                Register New Customer
            </span>
            <button class="modal-close" @click="$store.customerModal.close()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body">
            <div style="display:flex;flex-direction:column;gap:10px">
                <div>
                    <label class="field-label">Full Name <span style="color:red">*</span></label>
                    <input type="text" class="field-input" x-model="form.name" placeholder="Customer full name">
                </div>
                <div>
                    <label class="field-label">Phone <span style="color:red">*</span></label>
                    <input type="text" class="field-input" x-model="form.phone" placeholder="07XX XXX XXXX">
                </div>
                <div>
                    <label class="field-label">City</label>
                    <input type="text" class="field-input" x-model="form.city" placeholder="Kabul...">
                </div>
                <div>
                    <label class="field-label">Notes</label>
                    <textarea class="field-input" x-model="form.notes" rows="2" placeholder="Optional..."></textarea>
                </div>
                <div x-show="error" x-cloak
                    style="padding:8px 10px;background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.2);border-radius:6px;font-size:12px;color:#dc2626"
                    x-text="error"></div>
            </div>
        </div>

        <div class="modal-foot">
            <button type="button" class="btn btn-ghost" @click="$store.customerModal.close()">Cancel</button>
            <button type="button" class="btn btn-primary" @click="submit()" :disabled="saving">
                <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                <span x-text="saving ? 'Saving...' : 'Register & Select'"></span>
            </button>
        </div>
    </div>
</div>


<script>
    document.addEventListener('alpine:init', () => {

        // ── Global Customer Store ──
        Alpine.store('customerModal', {
            show: false,
            onSuccess: null, // callback fn — set by whoever opens it

            open(callback = null) {
                this.onSuccess = callback;
                this.show = true;
            },

            close() {
                this.show = false;
                this.onSuccess = null;
            },

            // Called after successful registration
            registered(customer) {
                if (typeof this.onSuccess === 'function') {
                    this.onSuccess(customer);
                }
                this.close();
            }
        });

    });

    function globalCustomerModal() {
        return {
            form: {
                name: '',
                phone: '',
                city: '',
                notes: ''
            },
            error: '',
            saving: false,

            // Reset form whenever modal opens
            init() {
                this.$watch('$store.customerModal.show', val => {
                    if (val) {
                        this.form = {
                            name: '',
                            phone: '',
                            city: '',
                            notes: ''
                        };
                        this.error = '';
                        this.saving = false;
                    }
                });
            },

            async submit() {
                if (!this.form.name.trim()) {
                    this.error = 'Name is required.';
                    return;
                }
                if (!this.form.phone.trim()) {
                    this.error = 'Phone is required.';
                    return;
                }

                this.saving = true;
                this.error = '';

                try {
                    const r = await fetch('{{ route('pos.customers.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.form)
                    });

                    const data = await r.json();

                    if (data.success) {
                        this.$store.customerModal.registered(data.customer);
                    } else {
                        this.error = data.message ?? 'Registration failed.';
                    }
                } catch (e) {
                    this.error = 'Network error. Try again.';
                } finally {
                    this.saving = false;
                }
            }
        }
    }
</script>
