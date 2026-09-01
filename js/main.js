window.addEventListener('load', () => {
    const loading = document.getElementById('loading-screen');
    const main = document.getElementById('main-content');
    window.setTimeout(() => {
        if (loading) loading.style.display = 'none';
        if (main) {
            main.hidden = false;
            main.style.display = 'block';
        }
        initHeroAnimations();
    }, 1200);
});

function initHeroAnimations() {
    if (!window.gsap) return;
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
    tl.from('.nav', { y: -30, opacity: 0, duration: 0.5 })
      .from('.site-name', { y: 20, opacity: 0, filter: 'blur(6px)', duration: 0.7 }, '-=0.3')
      .from('.tagline', { y: 14, opacity: 0, duration: 0.6 }, '-=0.4')
      .from('.cta', { y: 12, opacity: 0, stagger: 0.1, duration: 0.45 }, '-=0.4');
}

function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.style.display = 'block';
    document.body.classList.add('no-scroll');
}

function closeModal(modal) {
    if (!modal) return;
    modal.style.display = 'none';
    document.body.classList.remove('no-scroll');
}

function initNav() {
    const links = document.querySelectorAll('.nav-links a');
    const map = new Map();
    links.forEach((a) => {
        const href = a.getAttribute('href');
        if (!href || !href.startsWith('#')) return;
        const target = document.querySelector(href);
        if (target) map.set(target, a);
    });

    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                const link = map.get(entry.target);
                if (!link || !entry.isIntersecting) return;
                links.forEach((l) => l.classList.remove('active'));
                link.classList.add('active');
            });
        }, { rootMargin: '0px 0px -70% 0px', threshold: 0.1 });
        map.forEach((_, section) => io.observe(section));
    }

    document.querySelectorAll('[data-open-contact]').forEach((el) => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            openModal('contact-modal');
        });
    });
}

function initMobileNav() {
    const toggle = document.getElementById('nav-toggle');
    const drawer = document.getElementById('mobile-drawer');
    const overlay = document.getElementById('mobile-overlay');
    const closeBtn = document.getElementById('nav-close');
    if (!toggle || !drawer || !overlay) return;

    const open = () => {
        drawer.classList.add('open');
        overlay.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        drawer.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    };
    const close = () => {
        drawer.classList.remove('open');
        overlay.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        drawer.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    };

    toggle.addEventListener('click', open);
    overlay.addEventListener('click', close);
    if (closeBtn) closeBtn.addEventListener('click', close);
    drawer.querySelectorAll('a').forEach((a) => {
        a.addEventListener('click', (e) => {
            if (a.hasAttribute('data-open-contact')) {
                e.preventDefault();
                close();
                openModal('contact-modal');
                return;
            }
            close();
        });
    });
    window.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
}

function initToTop() {
    const btn = document.getElementById('to-top');
    if (!btn) return;
    const toggle = () => {
        if (window.scrollY > 400) btn.classList.add('show');
        else btn.classList.remove('show');
    };
    window.addEventListener('scroll', toggle, { passive: true });
    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    toggle();
}

function initCounters() {
    const values = document.querySelectorAll('.stat-value');
    if (!values.length || !('IntersectionObserver' in window)) return;
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            const target = Number(el.dataset.count || 0);
            let cur = 0;
            const step = Math.max(1, Math.floor(target / 60));
            const tick = () => {
                cur += step;
                if (cur >= target) el.textContent = String(target);
                else {
                    el.textContent = String(cur);
                    requestAnimationFrame(tick);
                }
            };
            tick();
            io.unobserve(el);
        });
    }, { threshold: 0.6 });
    values.forEach((v) => io.observe(v));
}

function initMarquee() {
    document.querySelectorAll('.marquee').forEach((mq) => {
        if (mq.dataset.processed) return;
        const track = mq.querySelector('.marquee-track');
        if (!track) return;
        track.innerHTML += track.innerHTML;
        mq.dataset.processed = 'true';
    });
}

function initCarousels() {
    document.querySelectorAll('.arrow').forEach((arrow) => {
        arrow.addEventListener('click', () => {
            const carousel = document.getElementById(arrow.dataset.carousel);
            if (!carousel) return;
            const item = carousel.querySelector('.carousel-item');
            const width = item ? item.offsetWidth + 20 : 320;
            carousel.scrollBy({
                left: arrow.classList.contains('left') ? -width : width,
                behavior: 'smooth'
            });
        });
    });

    document.querySelectorAll('.view-all-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.target;
            const section = document.getElementById(target);
            if (!section) return;
            const carousel = section.querySelector('.carousel-container');
            const grid = document.getElementById(`${target}-grid`);
            const arrows = section.querySelectorAll('.arrow');
            const showingGrid = grid && !grid.hidden;
            if (!grid || !carousel) return;

            if (!showingGrid) {
                grid.hidden = false;
                grid.style.display = 'block';
                carousel.style.display = 'none';
                arrows.forEach((a) => { a.style.display = 'none'; });
                btn.textContent = 'Back to Carousel';
            } else {
                grid.hidden = true;
                grid.style.display = 'none';
                carousel.style.display = 'flex';
                arrows.forEach((a) => { a.style.display = 'block'; });
                btn.textContent = 'View All';
            }
        });
    });
}

function initModals() {
    const contactBtn = document.getElementById('contact-btn');
    if (contactBtn) {
        contactBtn.addEventListener('click', () => openModal('contact-modal'));
    }

    document.querySelectorAll('.close').forEach((closeBtn) => {
        closeBtn.addEventListener('click', () => {
            closeModal(document.getElementById(closeBtn.getAttribute('data-modal')) || closeBtn.closest('.modal'));
        });
    });

    window.addEventListener('click', (event) => {
        if (event.target.classList && event.target.classList.contains('modal')) {
            closeModal(event.target);
        }
    });

    window.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        document.querySelectorAll('.modal').forEach((modal) => {
            if (modal.style.display === 'block') closeModal(modal);
        });
    });
}

function initCardDetails() {
    const cfg = window.ATOZEE || {};
    document.querySelectorAll('.carousel-item').forEach((item) => {
        item.addEventListener('click', () => {
            const modal = document.getElementById('card-details-modal');
            if (!modal) return;
            const name = item.dataset.name || '';
            const code = item.dataset.code || '';
            document.getElementById('details-image').src = item.dataset.image || '';
            document.getElementById('details-image').alt = name;
            document.getElementById('details-title').textContent = name;
            document.getElementById('details-description').textContent = item.dataset.description || '';
            document.getElementById('details-shop-number').textContent = code;
            document.getElementById('details-category').textContent = item.dataset.category || '';

            const subject = `Inquiry about ${name}`;
            const body = `Hi AtoZee Team,\n\nI'm interested in learning more about ${name} (${code}).\n\n`;
            document.getElementById('details-contact-link').href =
                `mailto:${cfg.email || ''}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
            const digits = String(cfg.whatsapp || '').replace(/\D+/g, '');
            document.getElementById('details-whatsapp-link').href =
                `https://wa.me/${digits}?text=${encodeURIComponent(`Hi AtoZee Team, I'm interested in learning more about ${name} (${code})`)}`;

            openModal('card-details-modal');
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initNav();
    initMobileNav();
    initToTop();
    initCounters();
    initMarquee();
    initCarousels();
    initModals();
    initCardDetails();
});
