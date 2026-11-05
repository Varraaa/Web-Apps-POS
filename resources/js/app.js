// Modern High-Fashion POS Storefront Engine & Micro-interactions
document.addEventListener('DOMContentLoaded', () => {
    initScrollReveal();
    initStatCounters();
    initCouponCopy();
    initMobileNav();
    initBackToTop();
    initToasts();
    initPosRegister();
});

/**
 * Scroll Reveal Animation via IntersectionObserver
 */
function initScrollReveal() {
    const revealElements = document.querySelectorAll('.reveal-on-scroll, .reveal-fade, .reveal-scale, .reveal-stagger');
    
    if (!revealElements.length) return;

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const delay = el.dataset.delay || 0;
                
                setTimeout(() => {
                    el.classList.add('is-revealed');
                }, delay);

                obs.unobserve(el);
            }
        });
    }, {
        root: null,
        rootMargin: '0px 0px -60px 0px',
        threshold: 0.12
    });

    revealElements.forEach(el => observer.observe(el));
}

/**
 * Animated Numerical Counters for Brand Milestones
 */
function initStatCounters() {
    const counters = document.querySelectorAll('.counter-value[data-target]');
    if (!counters.length) return;

    const counterObserver = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.getAttribute('data-target'), 10);
                const prefix = el.getAttribute('data-prefix') || '';
                const suffix = el.getAttribute('data-suffix') || '';
                const duration = 1800;
                const start = performance.now();

                function updateCounter(currentTime) {
                    const elapsed = currentTime - start;
                    const progress = Math.min(elapsed / duration, 1);
                    // easeOutExpo
                    const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                    const currentVal = Math.floor(easeProgress * target);

                    el.textContent = `${prefix}${currentVal.toLocaleString('id-ID')}${suffix}`;

                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    } else {
                        el.textContent = `${prefix}${target.toLocaleString('id-ID')}${suffix}`;
                    }
                }

                requestAnimationFrame(updateCounter);
                obs.unobserve(el);
            }
        });
    }, { threshold: 0.3 });

    counters.forEach(counter => counterObserver.observe(counter));
}

/**
 * Interactive Coupon Voucher Copying and 1-Click Application
 */
function initCouponCopy() {
    document.querySelectorAll('[data-copy-coupon]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const code = btn.getAttribute('data-copy-coupon');
            if (!code) return;

            navigator.clipboard.writeText(code).then(() => {
                showToast(`Kode promo "${code}" berhasil disalin! Silakan pasang di tas belanja.`, 'success');
                
                const origText = btn.innerHTML;
                btn.innerHTML = '<span>✓ Tersalin</span>';
                btn.classList.add('copied');
                
                // If coupon input exists on current page (e.g. Cart page), auto-fill it!
                const couponInput = document.querySelector('#coupon-input');
                if (couponInput) {
                    couponInput.value = code;
                    couponInput.focus();
                }

                setTimeout(() => {
                    btn.innerHTML = origText;
                    btn.classList.remove('copied');
                }, 2500);
            }).catch(() => {
                showToast(`Gunakan kode promo: ${code}`, 'info');
            });
        });
    });
}

/**
 * Mobile Navigation Drawer
 */
function initMobileNav() {
    const menuBtn = document.querySelector('.mobile-menu-button');
    const mobileMenu = document.querySelector('#mobile-menu');
    
    if (!menuBtn || !mobileMenu) return;

    menuBtn.addEventListener('click', () => {
        const isExpanded = menuBtn.getAttribute('aria-expanded') === 'true';
        menuBtn.setAttribute('aria-expanded', String(!isExpanded));
        mobileMenu.hidden = isExpanded;
        document.body.classList.toggle('overflow-hidden', !isExpanded);
    });

    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            menuBtn.setAttribute('aria-expanded', 'false');
            mobileMenu.hidden = true;
            document.body.classList.remove('overflow-hidden');
        });
    });
}

/**
 * Back to Top Floating Button
 */
function initBackToTop() {
    const bttBtn = document.querySelector('.back-to-top-btn');
    if (!bttBtn) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 400) {
            bttBtn.classList.add('visible');
        } else {
            bttBtn.classList.remove('visible');
        }
    }, { passive: true });

    bttBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

/**
 * Toast Notification System
 */
function initToasts() {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
}

window.showToast = function(message, type = 'info') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast-item toast-${type}`;
    
    const icon = type === 'success' ? '✦' : (type === 'error' ? '✕' : 'ℹ');
    
    toast.innerHTML = `
        <span class="toast-icon">${icon}</span>
        <span class="toast-text">${message}</span>
        <button class="toast-close" aria-label="Tutup">&times;</button>
    `;

    container.appendChild(toast);

    // Trigger enter animation
    requestAnimationFrame(() => {
        toast.classList.add('toast-show');
    });

    const removeToast = () => {
        toast.classList.remove('toast-show');
        toast.classList.add('toast-hide');
        setTimeout(() => toast.remove(), 400);
    };

    toast.querySelector('.toast-close').addEventListener('click', removeToast);
    setTimeout(removeToast, 4500);
};

/**
 * Modern POS Register Terminal Controller
 */
function initPosRegister() {
    const posApp = document.getElementById('pos-app');
    if (!posApp) return;

    let ticket = [];
    let appliedCoupon = null;
    let paymentMethod = 'cash';
    let activeCustomer = null;

    // Elements
    const searchInput = document.getElementById('pos-search-input');
    const clearSearchBtn = document.getElementById('pos-clear-search');
    const categoryPills = document.querySelectorAll('.pos-cat-pill');
    const productCards = document.querySelectorAll('.pos-product-card');
    const ticketItemsContainer = document.getElementById('pos-ticket-items-container');
    const ticketEmptyNotice = document.getElementById('pos-ticket-empty');
    const ticketCountLabel = document.getElementById('pos-ticket-count');
    const clearCartBtn = document.getElementById('pos-btn-clear-cart');

    // Customer & Loyalty Elements
    const memberSearchInput = document.getElementById('pos-member-search');
    const memberResultsDropdown = document.getElementById('pos-member-results');
    const selectedMemberCard = document.getElementById('pos-selected-member-card');
    const selectedMemberName = document.getElementById('selected-member-name');
    const selectedMemberTier = document.getElementById('selected-member-tier');
    const selectedMemberPoints = document.getElementById('selected-member-points');
    const removeMemberBtn = document.getElementById('pos-remove-member');
    const redeemPointsCheck = document.getElementById('pos-redeem-points-check');
    const labelRedeemPoints = document.getElementById('label-redeem-points');
    const walkinInputs = document.getElementById('pos-walkin-inputs');

    const customerModal = document.getElementById('pos-customer-modal');
    const openCustomerModalBtn = document.getElementById('pos-btn-new-customer');
    const closeCustomerModalBtn = document.getElementById('pos-close-customer-modal');
    const cancelCustomerModalBtn = document.getElementById('pos-cancel-customer-modal');
    const quickCustomerForm = document.getElementById('pos-quick-customer-form');

    const couponSelect = document.getElementById('pos-coupon-select');
    const applyCouponBtn = document.getElementById('pos-apply-coupon');

    const subtotalDisplay = document.getElementById('pos-val-subtotal');
    const discountRow = document.getElementById('pos-row-discount');
    const discountLabel = document.getElementById('pos-discount-label');
    const discountDisplay = document.getElementById('pos-val-discount');
    const grandtotalDisplay = document.getElementById('pos-val-grandtotal');
    const payBtn = document.getElementById('pos-btn-open-payment');

    // Modals
    const paymentModal = document.getElementById('pos-payment-modal');
    const modalCloseBtn = document.getElementById('pos-modal-close');
    const modalDueAmount = document.getElementById('pos-modal-due');
    const payTabs = document.querySelectorAll('.pos-pay-tab');
    const payPanes = {
        cash: document.getElementById('pane-cash'),
        qris: document.getElementById('pane-qris'),
        transfer: document.getElementById('pane-transfer'),
    };
    const cashInput = document.getElementById('pos-cash-input');
    const cashChangeDisplay = document.getElementById('pos-cash-change');
    const cancelPayBtn = document.getElementById('pos-cancel-pay-btn');
    const submitTransactionBtn = document.getElementById('pos-submit-transaction-btn');

    const receiptModal = document.getElementById('pos-receipt-modal');
    const receiptFrame = document.getElementById('receipt-frame');
    const printReceiptBtn = document.getElementById('btn-print-receipt');
    const closeReceiptBtn = document.getElementById('btn-close-receipt-modal');

    // Live Clock
    const clockElement = document.getElementById('pos-live-clock');
    if (clockElement) {
        const updateClock = () => {
            const now = new Date();
            clockElement.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        };
        updateClock();
        setInterval(updateClock, 1000);
    }

    // Number formatter
    const formatRp = (num) => 'Rp ' + Number(num).toLocaleString('id-ID');

    // Calculations
    const calculateTotals = () => {
        const subtotal = ticket.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        let discount = 0;

        if (appliedCoupon) {
            if (subtotal < appliedCoupon.min_spend) {
                appliedCoupon = null;
                if (window.showToast) {
                    window.showToast('Voucher dibatalkan karena subtotal di bawah minimum belanja.', 'error');
                }
            } else {
                if (appliedCoupon.type === 'percent') {
                    discount = Math.round((appliedCoupon.value / 100) * subtotal);
                    if (appliedCoupon.max_discount && discount > appliedCoupon.max_discount) {
                        discount = appliedCoupon.max_discount;
                    }
                } else {
                    discount = Math.min(appliedCoupon.value, subtotal);
                }
            }
        }

        let pointsDiscount = 0;
        const totalAfterCoupon = Math.max(0, subtotal - discount);

        if (activeCustomer && activeCustomer.points > 0) {
            const maxPoints = Math.min(activeCustomer.points, Math.floor(totalAfterCoupon / 100));
            if (redeemPointsCheck && redeemPointsCheck.checked) {
                pointsDiscount = maxPoints * 100;
                labelRedeemPoints.textContent = `Tukar ${maxPoints} Poin (Hemat ${formatRp(pointsDiscount)})`;
            } else if (labelRedeemPoints) {
                labelRedeemPoints.textContent = `Tukar Poin (${activeCustomer.points} Poin = Hemat s/d ${formatRp(maxPoints * 100)})`;
            }
        }

        const grandTotal = Math.max(0, totalAfterCoupon - pointsDiscount);

        // Update UI
        subtotalDisplay.textContent = formatRp(subtotal);
        if ((discount + pointsDiscount) > 0) {
            discountRow.style.display = 'flex';
            let label = appliedCoupon ? appliedCoupon.code : '';
            if (pointsDiscount > 0) {
                label += (label ? ' + ' : '') + 'Poin VIP';
            }
            discountLabel.textContent = label;
            discountDisplay.textContent = '- ' + formatRp(discount + pointsDiscount);
        } else {
            discountRow.style.display = 'none';
        }

        grandtotalDisplay.textContent = formatRp(grandTotal);

        const totalQty = ticket.reduce((sum, item) => sum + item.quantity, 0);
        ticketCountLabel.textContent = `${totalQty} item dipilih`;
        payBtn.disabled = ticket.length === 0;

        return { subtotal, discount, grandTotal };
    };

    // Render Ticket items
    const renderTicket = () => {
        if (ticket.length === 0) {
            ticketEmptyNotice.classList.remove('hidden');
            ticketItemsContainer.innerHTML = '';
            ticketItemsContainer.appendChild(ticketEmptyNotice);
        } else {
            ticketEmptyNotice.classList.add('hidden');
            ticketItemsContainer.innerHTML = '';

            ticket.forEach(item => {
                const row = document.createElement('div');
                row.className = 'ticket-item-row';
                row.innerHTML = `
                    <img src="${item.image}" alt="${item.name}" class="ticket-item-thumb">
                    <div class="ticket-item-info">
                        <h4 class="ticket-item-title" title="${item.name}">${item.name}</h4>
                        <span class="ticket-item-meta">${item.color} · ${formatRp(item.price)}</span>
                    </div>
                    <div class="ticket-qty-control">
                        <button type="button" class="ticket-qty-btn btn-dec" data-id="${item.id}">−</button>
                        <span class="ticket-qty-val">${item.quantity}</span>
                        <button type="button" class="ticket-qty-btn btn-inc" data-id="${item.id}" ${item.quantity >= item.stock ? 'disabled' : ''}>+</button>
                    </div>
                    <span class="ticket-item-subtotal">${formatRp(item.price * item.quantity)}</span>
                    <button type="button" class="ticket-del-btn" data-id="${item.id}" title="Hapus">✕</button>
                `;

                // Events
                row.querySelector('.btn-dec').addEventListener('click', () => {
                    if (item.quantity > 1) {
                        item.quantity--;
                    } else {
                        ticket = ticket.filter(t => t.id !== item.id);
                    }
                    renderTicket();
                    calculateTotals();
                });

                row.querySelector('.btn-inc').addEventListener('click', () => {
                    if (item.quantity < item.stock) {
                        item.quantity++;
                        renderTicket();
                        calculateTotals();
                    } else if (window.showToast) {
                        window.showToast(`Batas stok tercapai (${item.stock} item).`, 'info');
                    }
                });

                row.querySelector('.ticket-del-btn').addEventListener('click', () => {
                    ticket = ticket.filter(t => t.id !== item.id);
                    renderTicket();
                    calculateTotals();
                });

                ticketItemsContainer.appendChild(row);
            });
        }

        calculateTotals();
    };

    // Add product to ticket
    productCards.forEach(card => {
        card.addEventListener('click', () => {
            const id = parseInt(card.dataset.id, 10);
            const stock = parseInt(card.dataset.stock, 10);
            if (stock <= 0) return;

            const existing = ticket.find(t => t.id === id);
            if (existing) {
                if (existing.quantity < stock) {
                    existing.quantity++;
                } else if (window.showToast) {
                    window.showToast(`Stok produk "${card.dataset.name}" maksimal ${stock} item.`, 'info');
                    return;
                }
            } else {
                ticket.push({
                    id: id,
                    name: card.dataset.name,
                    category: card.dataset.category,
                    price: parseFloat(card.dataset.price),
                    originalPrice: parseFloat(card.dataset.originalPrice),
                    stock: stock,
                    image: card.dataset.image,
                    color: card.dataset.color,
                    size: card.dataset.size,
                    quantity: 1
                });
            }

            renderTicket();

            // Subtle click animation
            card.classList.add('ring-2', 'ring-slate-900');
            setTimeout(() => card.classList.remove('ring-2', 'ring-slate-900'), 200);
        });
    });

    // Clear Ticket
    clearCartBtn?.addEventListener('click', () => {
        if (ticket.length === 0) return;
        if (confirm('Kosongkan semua item di tiket kasir saat ini?')) {
            ticket = [];
            appliedCoupon = null;
            renderTicket();
        }
    });

    // Apply Coupon
    applyCouponBtn?.addEventListener('click', () => {
        const selectedCode = couponSelect.value;
        if (!selectedCode) {
            appliedCoupon = null;
            calculateTotals();
            if (window.showToast) window.showToast('Voucher dihapus.', 'info');
            return;
        }

        const opt = couponSelect.selectedOptions[0];
        const minSpend = parseFloat(opt.dataset.min || 0);
        const subtotal = ticket.reduce((sum, item) => sum + (item.price * item.quantity), 0);

        if (subtotal < minSpend) {
            if (window.showToast) {
                window.showToast(`Minimal belanja untuk voucher ini adalah ${formatRp(minSpend)}.`, 'error');
            }
            return;
        }

        appliedCoupon = {
            code: selectedCode,
            type: opt.dataset.type,
            value: parseFloat(opt.dataset.value),
            min_spend: minSpend,
            max_discount: parseFloat(opt.dataset.max || 0)
        };

        calculateTotals();
        if (window.showToast) {
            window.showToast(`Voucher ${selectedCode} berhasil dipasang!`, 'success');
        }
    });

    // Customer & Loyalty Member Functions
    const selectMember = (cust) => {
        activeCustomer = cust;
        memberResultsDropdown.classList.add('hidden');
        memberSearchInput.value = '';

        selectedMemberName.textContent = `${cust.name} (${cust.member_code})`;
        selectedMemberTier.textContent = cust.tier_label || cust.tier.toUpperCase();
        selectedMemberPoints.textContent = `★ ${cust.points} Poin`;
        selectedMemberCard.classList.remove('hidden');
        walkinInputs.classList.add('hidden');

        if (redeemPointsCheck) {
            redeemPointsCheck.checked = false;
        }

        calculateTotals();
        if (window.showToast) {
            window.showToast(`Member VIP ${cust.name} dipilih!`, 'info');
        }
    };

    let memberSearchTimeout = null;
    memberSearchInput?.addEventListener('input', () => {
        clearTimeout(memberSearchTimeout);
        const q = memberSearchInput.value.trim();

        if (q.length < 2) {
            memberResultsDropdown.classList.add('hidden');
            memberResultsDropdown.innerHTML = '';
            return;
        }

        memberSearchTimeout = setTimeout(async () => {
            try {
                const res = await fetch(`/admin/customers/search?q=${encodeURIComponent(q)}`);
                const data = await res.json();
                memberResultsDropdown.innerHTML = '';

                if (data.customers && data.customers.length > 0) {
                    data.customers.forEach(cust => {
                        const div = document.createElement('div');
                        div.className = 'p-2.5 hover:bg-slate-50 cursor-pointer border-b border-slate-100 flex items-center justify-between text-xs';
                        div.innerHTML = `
                            <div>
                                <strong class="text-slate-900 block">${cust.name}</strong>
                                <span class="text-[10px] text-slate-500 font-mono">${cust.member_code} · ${cust.phone || '-'}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 uppercase">${cust.tier}</span>
                                <small class="text-[10px] text-emerald-600 font-bold block mt-0.5">★ ${cust.points} pt</small>
                            </div>
                        `;
                        div.addEventListener('click', () => selectMember(cust));
                        memberResultsDropdown.appendChild(div);
                    });
                    memberResultsDropdown.classList.remove('hidden');
                } else {
                    memberResultsDropdown.innerHTML = '<div class="p-3 text-xs text-slate-400 text-center">Member tidak ditemukan.</div>';
                    memberResultsDropdown.classList.remove('hidden');
                }
            } catch (e) {
                console.error(e);
            }
        }, 300);
    });

    removeMemberBtn?.addEventListener('click', () => {
        activeCustomer = null;
        selectedMemberCard.classList.add('hidden');
        walkinInputs.classList.remove('hidden');
        if (redeemPointsCheck) redeemPointsCheck.checked = false;
        calculateTotals();
        if (window.showToast) window.showToast('Member dilepas dari transaksi.', 'info');
    });

    redeemPointsCheck?.addEventListener('change', () => {
        calculateTotals();
        updateChangeCalculation();
    });

    // Quick Member Register Modal Handlers
    openCustomerModalBtn?.addEventListener('click', () => {
        customerModal?.classList.remove('hidden');
        document.getElementById('cust-name')?.focus();
    });

    const closeCustModal = () => customerModal?.classList.add('hidden');
    closeCustomerModalBtn?.addEventListener('click', closeCustModal);
    cancelCustomerModalBtn?.addEventListener('click', closeCustModal);

    quickCustomerForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const name = document.getElementById('cust-name').value.trim();
        const phone = document.getElementById('cust-phone').value.trim();
        const email = document.getElementById('cust-email')?.value.trim() || null;
        const address = document.getElementById('cust-address')?.value.trim() || null;

        try {
            const res = await fetch('/admin/customers/quick-create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({ name, phone, email, address })
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Gagal mendaftarkan member.');
            }

            closeCustModal();
            quickCustomerForm.reset();
            selectMember(data.customer);

            if (window.showToast) {
                window.showToast(`Member baru ${data.customer.name} berhasil didaftarkan!`, 'success');
            }
        } catch (err) {
            alert(err.message || 'Terjadi kesalahan saat mendaftarkan member.');
        }
    });

    // POS Held Bills (Parkir Transaksi) Engine
    const btnHoldBill = document.getElementById('pos-btn-hold-bill');
    const btnViewHeld = document.getElementById('pos-btn-view-held');
    const heldCountBadge = document.getElementById('pos-held-count-badge');
    const heldModal = document.getElementById('pos-held-modal');
    const closeHeldModal = document.getElementById('pos-close-held-modal');
    const dismissHeldModal = document.getElementById('pos-dismiss-held-modal');
    const heldList = document.getElementById('pos-held-list');

    const refreshHeldBillsCount = async () => {
        try {
            const res = await fetch('/admin/pos/held-bills');
            const data = await res.json();
            if (heldCountBadge) heldCountBadge.textContent = data.count || 0;
            return data;
        } catch (e) {
            console.error(e);
        }
    };
    refreshHeldBillsCount();

    btnHoldBill?.addEventListener('click', async () => {
        if (ticket.length === 0) {
            alert('Tiket kasir kosong. Tambahkan produk sebelum menyimpan tagihan.');
            return;
        }

        const note = prompt('Beri catatan/nama untuk tagihan yang diparkir (opsional):', activeCustomer ? activeCustomer.name : 'Antrean Pelanggan');
        if (note === null) return; // User cancelled

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const customerName = activeCustomer ? activeCustomer.name : (document.getElementById('pos-customer-name')?.value || 'Pelanggan Walk-In');

        try {
            const res = await fetch('/admin/pos/hold-bill', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({
                    customer_name: customerName,
                    customer_id: activeCustomer ? activeCustomer.id : null,
                    reference_note: note,
                    coupon_code: appliedCoupon ? appliedCoupon.code : null,
                    items: ticket
                })
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Gagal menyimpan tagihan.');
            }

            // Clear current register
            ticket = [];
            appliedCoupon = null;
            activeCustomer = null;
            selectedMemberCard?.classList.add('hidden');
            walkinInputs?.classList.remove('hidden');
            renderTicket();
            refreshHeldBillsCount();

            if (window.showToast) {
                window.showToast('Tagihan berhasil diparkir! Register siap untuk pelanggan berikutnya.', 'success');
            }
        } catch (err) {
            alert(err.message || 'Terjadi kesalahan sistem.');
        }
    });

    const openHeldModal = async () => {
        const data = await refreshHeldBillsCount();
        if (!heldList) return;

        heldList.innerHTML = '';
        if (data.bills && data.bills.length > 0) {
            data.bills.forEach(bill => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'py-3 flex items-center justify-between gap-3 text-xs';
                itemDiv.innerHTML = `
                    <div>
                        <strong class="text-slate-900 block font-bold">${bill.customer_name}</strong>
                        <span class="text-[11px] text-slate-500">${bill.items_count} item · ${bill.time_ago}</span>
                        ${bill.reference_note ? `<small class="block text-[10px] text-amber-700 font-semibold italic mt-0.5">"${bill.reference_note}"</small>` : ''}
                    </div>
                    <div class="text-right">
                        <strong class="text-slate-900 block">${bill.formatted_subtotal}</strong>
                        <div class="flex items-center gap-1.5 mt-1">
                            <button type="button" class="btn-recall-bill pos-btn-pay text-[10px] py-1 px-2" data-id="${bill.id}">
                                Buka ↗
                            </button>
                            <button type="button" class="btn-del-held pos-btn-danger-ghost text-[10px] py-1 px-2" data-id="${bill.id}">
                                ✕
                            </button>
                        </div>
                    </div>
                `;

                // Events
                itemDiv.querySelector('.btn-recall-bill').addEventListener('click', async () => {
                    if (ticket.length > 0 && !confirm('Tiket saat ini masih berisi item. Ganti dengan tagihan yang diparkir?')) {
                        return;
                    }
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    try {
                        const recallRes = await fetch(`/admin/pos/held-bills/${bill.id}/recall`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken || '' }
                        });
                        const recalled = await recallRes.json();
                        if (recalled.success && recalled.bill) {
                            ticket = recalled.bill.items;
                            if (recalled.bill.customer) {
                                selectMember(recalled.bill.customer);
                            }
                            heldModal.classList.add('hidden');
                            renderTicket();
                            refreshHeldBillsCount();
                            if (window.showToast) window.showToast('Tagihan berhasil dibuka kembali!', 'success');
                        }
                    } catch (e) {
                        alert('Gagal memulihkan tagihan.');
                    }
                });

                itemDiv.querySelector('.btn-del-held').addEventListener('click', async () => {
                    if (!confirm('Hapus tagihan yang diparkir ini?')) return;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    try {
                        await fetch(`/admin/pos/held-bills/${bill.id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrfToken || '' }
                        });
                        openHeldModal();
                    } catch (e) {
                        alert('Gagal menghapus tagihan.');
                    }
                });

                heldList.appendChild(itemDiv);
            });
        } else {
            heldList.innerHTML = '<div class="py-8 text-center text-slate-400 text-xs">Tidak ada tagihan yang sedang diparkir.</div>';
        }

        heldModal?.classList.remove('hidden');
    };

    btnViewHeld?.addEventListener('click', openHeldModal);
    closeHeldModal?.addEventListener('click', () => heldModal?.classList.add('hidden'));
    dismissHeldModal?.addEventListener('click', () => heldModal?.classList.add('hidden'));

    // Search and Category Filter
    let activeCategory = '';
    const filterProducts = () => {
        const query = searchInput.value.toLowerCase().trim();
        clearSearchBtn.classList.toggle('hidden', query === '');

        productCards.forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const color = card.dataset.color.toLowerCase();
            const cat = card.dataset.category;

            const matchesQuery = query === '' || name.includes(query) || color.includes(query);
            const matchesCat = activeCategory === '' || cat === activeCategory;

            card.style.display = (matchesQuery && matchesCat) ? 'flex' : 'none';
        });
    };

    searchInput?.addEventListener('input', filterProducts);
    clearSearchBtn?.addEventListener('click', () => {
        searchInput.value = '';
        filterProducts();
        searchInput.focus();
    });

    categoryPills.forEach(pill => {
        pill.addEventListener('click', () => {
            categoryPills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            activeCategory = pill.dataset.category;
            filterProducts();
        });
    });

    // Payment Modal & Change Calculation
    const updateChangeCalculation = () => {
        const { grandTotal } = calculateTotals();
        const paid = parseFloat(cashInput.value) || 0;
        const change = Math.max(0, paid - grandTotal);

        cashChangeDisplay.textContent = formatRp(change);
        if (paid < grandTotal && paymentMethod === 'cash') {
            cashChangeDisplay.classList.remove('text-emerald-600');
            cashChangeDisplay.classList.add('text-rose-600');
            submitTransactionBtn.disabled = true;
            submitTransactionBtn.innerHTML = `<span>Uang Masih Kurang (${formatRp(grandTotal - paid)})</span>`;
        } else {
            cashChangeDisplay.classList.remove('text-rose-600');
            cashChangeDisplay.classList.add('text-emerald-600');
            submitTransactionBtn.disabled = false;
            submitTransactionBtn.innerHTML = `<span>Konfirmasi Pembayaran Selesai ✓</span>`;
        }
    };

    cashInput?.addEventListener('input', updateChangeCalculation);

    // Quick cash presets
    document.querySelectorAll('.btn-cash-preset').forEach(btn => {
        btn.addEventListener('click', () => {
            const { grandTotal } = calculateTotals();
            if (btn.id === 'preset-exact') {
                cashInput.value = grandTotal;
            } else {
                cashInput.value = btn.dataset.amount;
            }
            updateChangeCalculation();
        });
    });

    // Payment method tabs
    payTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            payTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            paymentMethod = tab.dataset.method;

            Object.keys(payPanes).forEach(k => {
                if (payPanes[k]) {
                    payPanes[k].classList.toggle('hidden', k !== paymentMethod);
                }
            });

            if (paymentMethod !== 'cash') {
                const { grandTotal } = calculateTotals();
                cashInput.value = grandTotal;
                submitTransactionBtn.disabled = false;
                submitTransactionBtn.innerHTML = `<span>Konfirmasi Pembayaran Selesai ✓</span>`;
            } else {
                updateChangeCalculation();
            }
        });
    });

    // Open Payment Modal
    payBtn?.addEventListener('click', () => {
        if (ticket.length === 0) return;
        const { grandTotal } = calculateTotals();
        modalDueAmount.textContent = formatRp(grandTotal);
        cashInput.value = grandTotal;
        paymentModal.classList.remove('hidden');
        updateChangeCalculation();
        setTimeout(() => cashInput.focus(), 100);
    });

    const closePaymentModal = () => {
        paymentModal.classList.add('hidden');
    };

    modalCloseBtn?.addEventListener('click', closePaymentModal);
    cancelPayBtn?.addEventListener('click', closePaymentModal);

    // Submit Transaction
    submitTransactionBtn?.addEventListener('click', async () => {
        if (ticket.length === 0) return;
        const { grandTotal } = calculateTotals();
        const paid = parseFloat(cashInput.value) || grandTotal;

        if (paymentMethod === 'cash' && paid < grandTotal) {
            alert('Uang yang dibayarkan kurang dari total tagihan.');
            return;
        }

        const customerName = document.getElementById('pos-customer-name')?.value || 'Pelanggan Walk-In';
        const customerPhone = document.getElementById('pos-customer-phone')?.value || '-';
        const notes = document.getElementById('pos-notes-input')?.value || '';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                          document.querySelector('input[name="_token"]')?.value;

        submitTransactionBtn.disabled = true;
        submitTransactionBtn.innerHTML = `<span>Memproses Transaksi...</span>`;

        try {
            const response = await fetch('/admin/pos/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({
                    customer_id: activeCustomer ? activeCustomer.id : null,
                    redeem_points: redeemPointsCheck?.checked ? true : false,
                    customer_name: activeCustomer ? activeCustomer.name : customerName,
                    customer_phone: activeCustomer ? (activeCustomer.phone || '-') : customerPhone,
                    payment_method: paymentMethod,
                    amount_paid: paid,
                    coupon_code: appliedCoupon?.code || null,
                    notes: notes,
                    items: ticket.map(t => ({
                        product_id: t.id,
                        quantity: t.quantity
                    }))
                })
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Gagal memproses transaksi.');
            }

            // Close payment modal
            closePaymentModal();

            // Open Receipt Modal
            receiptFrame.src = result.receipt_url;
            document.getElementById('receipt-modal-sub').textContent = `No. Struk: ${result.invoice_number}`;
            receiptModal.classList.remove('hidden');

            if (window.showToast) {
                window.showToast(`Transaksi POS #${result.invoice_number} Sukses!`, 'success');
            }

            // Reset Ticket
            ticket = [];
            appliedCoupon = null;
            activeCustomer = null;
            selectedMemberCard?.classList.add('hidden');
            walkinInputs?.classList.remove('hidden');
            if (redeemPointsCheck) redeemPointsCheck.checked = false;
            document.getElementById('pos-notes-input').value = '';
            renderTicket();

        } catch (err) {
            alert(err.message || 'Terjadi kesalahan sistem saat memproses transaksi.');
            submitTransactionBtn.disabled = false;
            submitTransactionBtn.innerHTML = `<span>Konfirmasi Pembayaran Selesai ✓</span>`;
        }
    });

    // Close Receipt Modal
    closeReceiptBtn?.addEventListener('click', () => {
        receiptModal.classList.add('hidden');
        receiptFrame.src = '';
    });

    // Print Receipt
    printReceiptBtn?.addEventListener('click', () => {
        if (receiptFrame && receiptFrame.contentWindow) {
            receiptFrame.contentWindow.focus();
            receiptFrame.contentWindow.print();
        }
    });

    // Global Keyboard Shortcuts (F2 search, F4 pay, ESC close)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'F2') {
            e.preventDefault();
            searchInput?.focus();
        } else if (e.key === 'F4') {
            e.preventDefault();
            if (!payBtn.disabled && paymentModal.classList.contains('hidden')) {
                payBtn.click();
            }
        } else if (e.key === 'Escape') {
            if (!paymentModal.classList.contains('hidden')) {
                closePaymentModal();
            } else if (!receiptModal.classList.contains('hidden')) {
                receiptModal.classList.add('hidden');
            }
        }
    });

    // ==========================================
    // Hardware Barcode Scanner Listener
    // ==========================================
    let barcodeBuffer = '';
    let lastKeyTime = Date.now();

    window.addEventListener('keydown', (e) => {
        const isInputField = ['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName);
        const currentTime = Date.now();
        const diff = currentTime - lastKeyTime;
        lastKeyTime = currentTime;

        // Scanners typically send characters rapidly (< 75ms between characters)
        if (diff > 80 && e.key !== 'Enter') {
            barcodeBuffer = '';
        }

        if (e.key === 'Enter') {
            if (barcodeBuffer.length >= 3) {
                const scannedCode = barcodeBuffer.trim().toLowerCase();
                barcodeBuffer = '';

                // Try to find product by barcode, sku, or ID
                const matchedCard = Array.from(productCards).find(card => {
                    const barcode = (card.dataset.barcode || '').toLowerCase();
                    const sku = (card.dataset.sku || '').toLowerCase();
                    const id = String(card.dataset.id || '');
                    return barcode === scannedCode || sku === scannedCode || id === scannedCode;
                });

                if (matchedCard) {
                    e.preventDefault();
                    if (isInputField) {
                        document.activeElement.blur();
                    }
                    matchedCard.click();
                    if (window.showToast) {
                        window.showToast(`[Barcode Scan] Menambahkan: ${matchedCard.dataset.name}`, 'success');
                    }
                } else if (barcodeBuffer.length > 0) {
                    if (window.showToast) {
                        window.showToast(`Barcode/SKU "${scannedCode}" tidak ditemukan.`, 'info');
                    }
                }
            }
        } else if (e.key.length === 1) {
            barcodeBuffer += e.key;
        }
    });
}


