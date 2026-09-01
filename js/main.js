function openMenu() {
    const menu = document.getElementById('mobile-menu');
    const toggle = document.getElementById('menu-toggle');
    if (!menu) return;
    menu.hidden = false;
    document.body.classList.add('no-scroll');
    if (toggle) toggle.setAttribute('aria-expanded', 'true');
}

function closeMenu() {
    const menu = document.getElementById('mobile-menu');
    const toggle = document.getElementById('menu-toggle');
    if (!menu) return;
    menu.hidden = true;
    document.body.classList.remove('no-scroll');
    if (toggle) toggle.setAttribute('aria-expanded', 'false');
}

function initNav() {
    const links = document.querySelectorAll('.primary-nav a');
    const map = new Map();
    links.forEach((a) => {
        const href = a.getAttribute('href');
        const target = href ? document.querySelector(href) : null;
        if (target) map.set(target, a);
    });
    if (!('IntersectionObserver' in window)) return;
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            const link = map.get(entry.target);
            if (!link || !entry.isIntersecting) return;
            links.forEach((l) => l.classList.remove('active'));
            link.classList.add('active');
        });
    }, { rootMargin: '0px 0px -65% 0px', threshold: 0.12 });
    map.forEach((_, section) => io.observe(section));
}

function initMobileMenu() {
    const toggle = document.getElementById('menu-toggle');
    const closeBtn = document.getElementById('menu-close');
    const menu = document.getElementById('mobile-menu');
    if (toggle) toggle.addEventListener('click', openMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (!menu) return;
    menu.querySelectorAll('a').forEach((a) => a.addEventListener('click', closeMenu));
    window.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMenu(); });
}

function initTicker() {
    document.querySelectorAll('.ticker-track').forEach((track) => {
        if (track.dataset.ready) return;
        track.innerHTML += track.innerHTML;
        track.dataset.ready = 'true';
    });
}

function initCounters() {
    const values = document.querySelectorAll('.stat-value');
    if (!values.length || !('IntersectionObserver' in window)) return;
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            const target = Number(el.dataset.count || 0);
            const suffix = target === 99 ? '%' : '';
            let cur = 0;
            const step = Math.max(1, Math.floor(target / 48));
            const tick = () => {
                cur += step;
                if (cur >= target) el.textContent = String(target) + suffix;
                else {
                    el.textContent = String(cur);
                    requestAnimationFrame(tick);
                }
            };
            tick();
            io.unobserve(el);
        });
    }, { threshold: 0.5 });
    values.forEach((v) => io.observe(v));
}

function initRails() {
    document.querySelectorAll('.rail-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            const rail = document.getElementById(btn.dataset.carousel);
            if (!rail) return;
            const card = rail.querySelector('.partner-card');
            const amount = (card ? card.getBoundingClientRect().width + 22 : 360) * Number(btn.dataset.dir || 1);
            rail.scrollBy({ left: amount, behavior: 'smooth' });
        });
    });

    document.querySelectorAll('.view-all').forEach((btn) => {
        btn.addEventListener('click', () => {
            const section = document.getElementById(btn.dataset.target);
            if (!section) return;
            const wrap = section.querySelector('.rail-wrap');
            const nav = section.querySelector('.rail-nav');
            if (!wrap) return;
            const grid = wrap.classList.toggle('is-grid');
            if (nav) nav.hidden = grid;
            btn.textContent = grid ? 'Back to the rail' : 'View the full desk';
        });
    });
}

function parseProducts(raw) {
    if (!raw) return [];
    try {
        const data = JSON.parse(raw);
        return Array.isArray(data) ? data : [];
    } catch {
        return [];
    }
}

function initPartnerSheet() {
    const sheet = document.getElementById('partner-sheet');
    if (!sheet) return;

    const intro = document.getElementById('sheet-intro');
    const productsView = document.getElementById('sheet-products');
    const exploreBtn = document.getElementById('explore-products');
    const backBtn = document.getElementById('products-back');
    const grid = document.getElementById('product-grid');
    const heading = document.getElementById('products-heading');
    const lead = document.getElementById('products-lead');
    let products = [];
    let partnerName = '';

    const showIntro = () => {
        if (intro) intro.hidden = false;
        if (productsView) productsView.hidden = true;
        sheet.classList.remove('is-exploring');
        sheet.scrollTop = 0;
    };

    const renderProducts = () => {
        if (!grid) return;
        grid.innerHTML = '';
        if (heading) heading.textContent = partnerName;
        if (lead) {
            lead.textContent = products.length
                ? 'What this partner stocks and sources.'
                : 'No products listed yet.';
        }
        if (!products.length) {
            const empty = document.createElement('p');
            empty.className = 'product-empty';
            empty.textContent = 'Ask the AtoZee desk if you need this range added.';
            grid.appendChild(empty);
            return;
        }
        products.forEach((product) => {
            const tile = document.createElement('article');
            tile.className = 'product-tile';
            const photo = document.createElement('div');
            photo.className = 'product-photo';
            const img = document.createElement('img');
            img.src = product.image || '';
            img.alt = product.name || '';
            img.loading = 'lazy';
            photo.appendChild(img);
            const title = document.createElement('h3');
            title.textContent = product.name || '';
            const note = document.createElement('p');
            note.textContent = product.description || '';
            tile.appendChild(photo);
            tile.appendChild(title);
            tile.appendChild(note);
            grid.appendChild(tile);
        });
    };

    const showProducts = () => {
        renderProducts();
        if (intro) intro.hidden = true;
        if (productsView) productsView.hidden = false;
        sheet.classList.add('is-exploring');
        sheet.scrollTop = 0;
    };

    document.querySelectorAll('.partner-card').forEach((card) => {
        card.addEventListener('click', () => {
            partnerName = card.dataset.name || '';
            const code = card.dataset.code || '';
            products = parseProducts(card.dataset.products);
            document.getElementById('details-image').src = card.dataset.image || '';
            document.getElementById('details-image').alt = partnerName;
            document.getElementById('details-title').textContent = partnerName;
            document.getElementById('details-description').textContent = card.dataset.description || '';
            document.getElementById('details-shop-number').textContent = code;
            document.getElementById('details-category').textContent = card.dataset.category || '';
            const wa = document.getElementById('details-whatsapp-link');
            if (wa) {
                const digits = String((window.ATOZEE || {}).whatsapp || '').replace(/\D+/g, '');
                wa.href = `https://wa.me/${digits}?text=${encodeURIComponent(`Hi AtoZee Team, I'm interested in learning more about ${partnerName} (${code})`)}`;
            }
            showIntro();
            if (typeof sheet.showModal === 'function') sheet.showModal();
        });
    });

    if (exploreBtn) exploreBtn.addEventListener('click', showProducts);
    if (backBtn) backBtn.addEventListener('click', showIntro);

    sheet.addEventListener('click', (e) => {
        if (e.target === sheet) sheet.close();
    });
    sheet.addEventListener('close', showIntro);
}

document.addEventListener('DOMContentLoaded', () => {
    initNav();
    initMobileMenu();
    initTicker();
    initCounters();
    initRails();
    initPartnerSheet();
});
