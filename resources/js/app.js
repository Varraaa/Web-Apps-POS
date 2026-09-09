// Modern High-Fashion POS Storefront Engine & Micro-interactions
document.addEventListener('DOMContentLoaded', () => {
    initScrollReveal();
    initStatCounters();
    initCouponCopy();
    initMobileNav();
    initBackToTop();
    initToasts();
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
