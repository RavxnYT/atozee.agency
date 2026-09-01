<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$content = atozee_content();
$site = $content['site'];
$categories = $content['categories'];
$brand = (string) ($site['name'] ?? 'AtoZee');
$tagline = (string) ($site['tagline'] ?? 'Your Global F&B Partner');
$email = (string) ($site['email'] ?? 'zeina@atozee.agency');
$whatsapp = (string) ($site['whatsapp'] ?? '+96176858441');
$phoneDisplay = (string) ($site['phone_display'] ?? $whatsapp);
$owner = (string) ($site['owner'] ?? 'Zeina Slim');
$developer = (string) ($site['developer_name'] ?? 'Joe Boulos');
$developerWa = (string) ($site['developer_whatsapp'] ?? '+96176403131');
$logo = atozee_site_url('assets/logo.png');
$heroCtas = array_slice($categories, 0, 2);
$year = date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0b1d36">
    <meta name="description" content="AtoZee connects F&amp;B brands with trusted suppliers worldwide. Better quality, better prices, smoother operations from A to Zee.">
    <title><?= e($brand) ?> — Sourcing Platform</title>
    <link rel="stylesheet" href="<?= e(atozee_site_url('assets/style.css')) ?>?v=2.0.0">
    <link rel="shortcut icon" href="<?= e($logo) ?>" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>
</head>
<body>
    <div id="loading-screen">
        <div class="loader">
            <div class="orb"></div>
            <div class="logo"><?= e($brand) ?></div>
        </div>
    </div>

    <div id="main-content" hidden>
        <nav id="nav" class="nav">
            <button id="nav-toggle" class="nav-toggle" aria-label="Open menu" aria-controls="mobile-drawer" aria-expanded="false">☰</button>
            <a class="nav-logo" href="#hero">
                <img src="<?= e($logo) ?>" alt="<?= e($brand) ?>">
                <span><?= e($brand) ?></span>
            </a>
            <ul class="nav-links">
                <li><a href="#about">About</a></li>
                <?php foreach ($categories as $category): ?>
                    <li><a href="#<?= e($category['slug']) ?>"><?= e($category['nav_label'] ?: $category['name']) ?></a></li>
                <?php endforeach; ?>
                <li><a href="#features">Features</a></li>
                <li><a href="#testimonials">Testimonials</a></li>
            </ul>
            <a class="nav-cta" href="#contact-modal" data-open-contact>Contact</a>
        </nav>

        <div id="mobile-overlay" class="mobile-overlay" hidden></div>
        <aside id="mobile-drawer" class="mobile-drawer" aria-hidden="true">
            <div class="drawer-header">
                <span>Menu</span>
                <button id="nav-close" class="nav-close" aria-label="Close menu">×</button>
            </div>
            <ul class="drawer-links">
                <li><a href="#about">About</a></li>
                <?php foreach ($categories as $category): ?>
                    <li><a href="#<?= e($category['slug']) ?>"><?= e($category['nav_label'] ?: $category['name']) ?></a></li>
                <?php endforeach; ?>
                <li><a href="#features">Features</a></li>
                <li><a href="#testimonials">Testimonials</a></li>
                <li><a href="#contact-modal" data-open-contact>Contact</a></li>
            </ul>
        </aside>

        <header id="hero">
            <div id="particles"></div>
            <div id="stars"></div>
            <div class="hero-content">
                <h1 class="site-name"><?= e($brand) ?></h1>
                <p class="tagline"><?= e($tagline) ?></p>
                <div class="cta-row">
                    <?php foreach ($heroCtas as $index => $cta): ?>
                        <a class="cta <?= $index === 0 ? 'primary' : 'secondary' ?>" href="#<?= e($cta['slug']) ?>">
                            <?= e($cta['cta_label'] ?: $cta['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </header>

        <section id="about">
            <div class="glass-card">
                <h2>About <?= e($brand) ?></h2>
                <p><?= e($site['about'] ?? '') ?></p>
            </div>
        </section>

        <section id="features" class="features">
            <div class="section-header">
                <h2>Why <?= e($brand) ?></h2>
            </div>
            <div class="feature-grid">
                <article class="feature-card">
                    <div class="feature-icon">🌐</div>
                    <h3>Global Network</h3>
                    <p>AtoZee connects you directly to verified suppliers and manufacturers across Europe, Asia, and the Middle East that match your business needs.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Rapid Sourcing</h3>
                    <p>Our efficient workflow makes sourcing simple, easy, and time-saving for our clients.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3>Quality Assured</h3>
                    <p>We only partner with manufacturers and brands that meet AtoZee's standards for reliability and performance.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon">🤝</div>
                    <h3>End-to-End Support</h3>
                    <p>From product discovery and negotiation to shipping and local delivery — we handle the full process.</p>
                </article>
            </div>
        </section>

        <section id="partners" class="partners">
            <div class="marquee" data-dup="true">
                <div class="marquee-track">
                    <span>Partnered Brands</span>
                    <span>Artisanal Roasters</span>
                    <span>Cloud Kitchens</span>
                    <span>Gourmet Suppliers</span>
                    <span>Hospitality Groups</span>
                    <span>Cold Chain Logistics</span>
                </div>
            </div>
        </section>

        <section id="stats" class="stats">
            <div class="stat">
                <div class="stat-value" data-count="120">0</div>
                <div class="stat-label">Curated Listings</div>
            </div>
            <div class="stat">
                <div class="stat-value" data-count="38">0</div>
                <div class="stat-label">Cities</div>
            </div>
            <div class="stat">
                <div class="stat-value" data-count="24">0</div>
                <div class="stat-label">Hour Support</div>
            </div>
            <div class="stat">
                <div class="stat-value" data-count="99">0</div>
                <div class="stat-label">Satisfaction (%)</div>
            </div>
        </section>

        <section id="testimonials" class="testimonials">
            <div class="section-header">
                <h2>What Partners Say</h2>
            </div>
            <div class="testimonial-carousel">
                <article class="t-card">
                    <p>“AtoZee connected us to suppliers we couldn't find elsewhere. Seamless.”</p>
                    <h4>— Operations Lead, Hospitality Group</h4>
                </article>
                <article class="t-card">
                    <p>“Their curation saved us weeks of sourcing. Exceptional quality.”</p>
                    <h4>— Founder, Specialty Coffee Chain</h4>
                </article>
                <article class="t-card">
                    <p>“Fast, transparent, and reliable. Exactly what we needed.”</p>
                    <h4>— Procurement Manager</h4>
                </article>
            </div>
        </section>

        <?php foreach ($categories as $category):
            $agencies = atozee_agencies_in($content, (string) $category['id']);
            $slug = (string) $category['slug'];
        ?>
            <section id="<?= e($slug) ?>" class="category-section" data-category="<?= e($slug) ?>">
                <div class="section-header">
                    <h2><?= e($category['name']) ?></h2>
                    <?php if (count($agencies) > 3): ?>
                        <button class="view-all-btn" type="button" data-target="<?= e($slug) ?>">View All</button>
                    <?php endif; ?>
                </div>
                <div class="carousel-container">
                    <button class="arrow left" type="button" data-carousel="<?= e($slug) ?>-carousel" aria-label="Previous">←</button>
                    <div class="carousel" id="<?= e($slug) ?>-carousel">
                        <?php if (!$agencies): ?>
                            <div class="carousel-empty">Partners in this category will appear here shortly.</div>
                        <?php endif; ?>
                        <?php foreach ($agencies as $index => $agency):
                            $code = strtoupper(preg_replace('/[^a-z0-9]+/i', '-', (string) $category['nav_label'] ?: $category['name']) ?? 'CAT') . '-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
                        ?>
                            <article
                                class="carousel-item"
                                data-name="<?= e($agency['name']) ?>"
                                data-description="<?= e($agency['description'] ?? '') ?>"
                                data-image="<?= e(atozee_image_src((string) $agency['image'])) ?>"
                                data-category="<?= e($category['name']) ?>"
                                data-code="<?= e($code) ?>"
                            >
                                <img src="<?= e(atozee_image_src((string) $agency['image'])) ?>" alt="<?= e($agency['name']) ?>" loading="lazy">
                                <h3><?= e($agency['name']) ?></h3>
                                <p><?= e($agency['description'] ?? '') ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <button class="arrow right" type="button" data-carousel="<?= e($slug) ?>-carousel" aria-label="Next">→</button>
                </div>
                <div class="grid-view" id="<?= e($slug) ?>-grid" hidden>
                    <div class="grid-container">
                        <?php foreach ($agencies as $index => $agency):
                            $code = strtoupper(preg_replace('/[^a-z0-9]+/i', '-', (string) $category['nav_label'] ?: $category['name']) ?? 'CAT') . '-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
                        ?>
                            <article
                                class="carousel-item"
                                data-name="<?= e($agency['name']) ?>"
                                data-description="<?= e($agency['description'] ?? '') ?>"
                                data-image="<?= e(atozee_image_src((string) $agency['image'])) ?>"
                                data-category="<?= e($category['name']) ?>"
                                data-code="<?= e($code) ?>"
                            >
                                <img src="<?= e(atozee_image_src((string) $agency['image'])) ?>" alt="<?= e($agency['name']) ?>" loading="lazy">
                                <h3><?= e($agency['name']) ?></h3>
                                <p><?= e($agency['description'] ?? '') ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>

        <button id="contact-btn" class="floating-btn" type="button" aria-label="Contact">
            <span>📧</span>
        </button>
        <a id="admin-btn" class="floating-btn admin-btn" href="<?= e(atozee_site_url('admin/')) ?>" title="Admin panel">
            <span>🔐</span>
        </a>
        <button id="to-top" class="to-top" type="button" aria-label="Back to top">↑</button>

        <div id="contact-modal" class="modal">
            <div class="modal-content contact-modal">
                <span class="close" data-modal="contact-modal">&times;</span>
                <div class="contact-header">
                    <div class="contact-avatar"><img src="<?= e($logo) ?>" alt="<?= e($brand) ?> logo"></div>
                    <div class="contact-titles">
                        <h3>Get in touch</h3>
                        <p>We usually reply within a few hours.</p>
                    </div>
                </div>
                <div class="contact-actions">
                    <a class="contact-action primary" href="<?= e(atozee_mailto($email, 'Inquiry from AtoZee', "Hi AtoZee Team,\n\n")) ?>">📧 Email Us</a>
                    <a class="contact-action" href="<?= e(atozee_whatsapp_url($whatsapp, 'Hi AtoZee Team')) ?>" target="_blank" rel="noopener">💬 WhatsApp</a>
                    <a class="contact-action" href="tel:<?= e($whatsapp) ?>">📞 Call</a>
                </div>
                <div class="contact-divider"><span>or</span></div>
                <div class="contact-info">
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <a class="info-value" href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
                    </div>
                    <div class="info-row">
                        <span class="info-label">WhatsApp</span>
                        <a class="info-value" href="<?= e(atozee_whatsapp_url($whatsapp)) ?>" target="_blank" rel="noopener"><?= e($phoneDisplay) ?></a>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Owner</span>
                        <a class="info-value" href="<?= e(atozee_whatsapp_url($whatsapp)) ?>" target="_blank" rel="noopener"><?= e($owner) ?></a>
                    </div>
                </div>
            </div>
        </div>

        <div id="card-details-modal" class="modal">
            <div class="modal-content card-details-modal">
                <span class="close" data-modal="card-details-modal">&times;</span>
                <div class="card-details-content">
                    <div class="card-details-image">
                        <img id="details-image" src="<?= e($logo) ?>" alt="">
                    </div>
                    <div class="card-details-info">
                        <h2 id="details-title"></h2>
                        <div class="card-details-meta">
                            <div class="detail-item">
                                <span class="detail-label">Partner code</span>
                                <span class="detail-value" id="details-shop-number"></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Category</span>
                                <span class="detail-value" id="details-category"></span>
                            </div>
                        </div>
                        <div class="card-details-description">
                            <h3>Description</h3>
                            <p id="details-description"></p>
                        </div>
                        <div class="card-details-actions">
                            <a id="details-contact-link" href="#" class="contact-action primary">📧 Contact Us</a>
                            <a id="details-whatsapp-link" href="#" class="contact-action" target="_blank" rel="noopener">💬 WhatsApp</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer>
            <p>&copy; <?= e($year) ?> <?= e($brand) ?>. All rights reserved.</p>
            <a href="<?= e(atozee_whatsapp_url($developerWa)) ?>" target="_blank" rel="noopener">Developed and Designed by <?= e($developer) ?>.</a>
        </footer>
    </div>

    <script>
        window.ATOZEE = {
            email: <?= json_encode($email) ?>,
            whatsapp: <?= json_encode($whatsapp) ?>
        };
    </script>
    <script src="<?= e(atozee_site_url('js/main.js')) ?>?v=2.0.0"></script>
</body>
</html>
