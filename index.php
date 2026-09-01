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
$agencyCount = count($content['agencies'] ?? []);

function atozee_partner_code(array $category, int $index): string
{
    $label = (string) (($category['nav_label'] ?? '') ?: ($category['name'] ?? 'CAT'));
    $slug = strtoupper((string) (preg_replace('/[^a-z0-9]+/i', '-', $label) ?? 'CAT'));
    return $slug . '-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#171512">
    <meta name="description" content="AtoZee is a sourcing agency connecting F&amp;B brands with trusted suppliers worldwide — better quality, clearer pricing, and operations from A to Zee.">
    <title><?= e($brand) ?> — Sourcing Agency</title>
    <link rel="stylesheet" href="<?= e(atozee_site_url('assets/style.css')) ?>?v=3.2.0">
    <link rel="shortcut icon" href="<?= e($logo) ?>" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,560;0,9..144,700;1,9..144,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>

    <header class="site-header">
        <div class="wrap header-inner">
            <a class="wordmark" href="#top">
                <img src="<?= e($logo) ?>" alt="">
                <span><?= e($brand) ?></span>
            </a>
            <nav class="primary-nav" aria-label="Primary">
                <a href="#about">Studio</a>
                <a href="#method">Method</a>
                <?php foreach ($categories as $category): ?>
                    <a href="#<?= e($category['slug']) ?>"><?= e($category['nav_label'] ?: $category['name']) ?></a>
                <?php endforeach; ?>
                <a href="#voices">Voices</a>
            </nav>
            <div class="header-actions">
                <a class="btn btn-dark" href="#contact">Start a briefing</a>
                <button class="menu-toggle" id="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu">Menu</button>
            </div>
        </div>
    </header>

    <div class="mobile-menu" id="mobile-menu" hidden>
        <div class="wrap mobile-menu-inner">
            <div class="mobile-menu-top">
                <span>Menu</span>
                <button class="menu-close" id="menu-close" type="button">Close</button>
            </div>
            <a href="#about">Studio</a>
            <a href="#method">Method</a>
            <?php foreach ($categories as $category): ?>
                <a href="#<?= e($category['slug']) ?>"><?= e($category['nav_label'] ?: $category['name']) ?></a>
            <?php endforeach; ?>
            <a href="#voices">Voices</a>
            <a href="#contact">Start a briefing</a>
        </div>
    </div>

    <main id="main">
        <section class="hero" id="top">
            <div class="wrap hero-grid">
                <div class="hero-copy">
                    <p class="eyebrow">Sourcing agency · F&amp;B</p>
                    <h1>We find what your kitchen should be serving next.</h1>
                    <p class="lede"><?= e($tagline) ?>. <?= e($brand) ?> connects coffee shops, restaurants, and retailers with verified suppliers across Europe, Asia, and the Middle East.</p>
                    <div class="hero-cta">
                        <a class="btn btn-dark" href="#contact">Talk to the desk</a>
                        <?php if ($heroCtas): ?>
                            <a class="btn btn-line" href="#<?= e($heroCtas[0]['slug']) ?>"><?= e($heroCtas[0]['cta_label'] ?: $heroCtas[0]['name']) ?></a>
                        <?php endif; ?>
                    </div>
                    <dl class="hero-meta">
                        <div>
                            <dt>Partners on file</dt>
                            <dd><?= e((string) max($agencyCount, 10)) ?>+</dd>
                        </div>
                        <div>
                            <dt>Coverage</dt>
                            <dd>EU · Asia · MENA</dd>
                        </div>
                        <div>
                            <dt>Desk</dt>
                            <dd>Lebanon, worldwide</dd>
                        </div>
                    </dl>
                </div>
                <figure class="hero-media">
                    <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&amp;fit=crop&amp;w=1600&amp;q=80" alt="A composed dining room, the kind of hospitality AtoZee sources for">
                    <figcaption>Hospitality sourcing, handled as a studio practice.</figcaption>
                </figure>
            </div>
        </section>

        <div class="ticker" aria-hidden="true">
            <div class="ticker-track" data-dup="true">
                <span>Artisanal roasters</span>
                <span>Cloud kitchens</span>
                <span>Gourmet suppliers</span>
                <span>Hospitality groups</span>
                <span>Cold chain logistics</span>
                <span>Specialty retail</span>
            </div>
        </div>

        <section class="about" id="about">
            <div class="wrap about-grid">
                <p class="eyebrow">The studio</p>
                <div>
                    <h2>A quieter way to buy well.</h2>
                    <p class="about-text"><?= e($site['about'] ?? '') ?></p>
                    <p class="about-text">You send the brief. We shortlist, negotiate, and see the order through — so your team stays on the floor, not in inboxes.</p>
                </div>
            </div>
        </section>

        <section class="method" id="method">
            <div class="wrap">
                <div class="section-intro">
                    <p class="eyebrow">Method</p>
                    <h2>How a briefing becomes a delivery.</h2>
                </div>
                <ol class="method-list">
                    <li>
                        <span>01</span>
                        <div>
                            <h3>Network</h3>
                            <p>Direct access to verified manufacturers and suppliers matched to the way you actually operate.</p>
                        </div>
                    </li>
                    <li>
                        <span>02</span>
                        <div>
                            <h3>Pace</h3>
                            <p>A tight sourcing workflow: shortlist, sample, price, and confirm without weeks of vendor theatre.</p>
                        </div>
                    </li>
                    <li>
                        <span>03</span>
                        <div>
                            <h3>Standard</h3>
                            <p>We only introduce partners who clear AtoZee’s bar for reliability, consistency, and aftercare.</p>
                        </div>
                    </li>
                    <li>
                        <span>04</span>
                        <div>
                            <h3>Through-line</h3>
                            <p>Discovery, negotiation, shipping, and local delivery sit with us — one desk, one conversation.</p>
                        </div>
                    </li>
                </ol>
            </div>
        </section>

        <section class="figures" id="figures">
            <div class="wrap figures-grid">
                <div>
                    <p class="stat-value" data-count="120">0</p>
                    <p class="stat-label">Curated listings</p>
                </div>
                <div>
                    <p class="stat-value" data-count="38">0</p>
                    <p class="stat-label">Cities served</p>
                </div>
                <div>
                    <p class="stat-value" data-count="24">0</p>
                    <p class="stat-label">Hour desk</p>
                </div>
                <div>
                    <p class="stat-value" data-count="99">0</p>
                    <p class="stat-label">Client satisfaction</p>
                </div>
            </div>
        </section>

        <?php foreach ($categories as $catIndex => $category):
            $agencies = atozee_agencies_in($content, (string) $category['id']);
            $slug = (string) $category['slug'];
            $pad = str_pad((string) ($catIndex + 1), 2, '0', STR_PAD_LEFT);
        ?>
            <section class="catalog" id="<?= e($slug) ?>">
                <div class="wrap">
                    <header class="catalog-head">
                        <div>
                            <p class="eyebrow"><?= e($pad) ?> / <?= e($category['name']) ?></p>
                            <h2><?= e($category['name']) ?></h2>
                        </div>
                        <div class="catalog-tools">
                            <?php if (count($agencies) > 3): ?>
                                <button class="btn btn-line view-all" type="button" data-target="<?= e($slug) ?>">View the full desk</button>
                            <?php endif; ?>
                            <div class="rail-nav">
                                <button type="button" class="rail-btn" data-carousel="<?= e($slug) ?>-rail" data-dir="-1" aria-label="Previous">←</button>
                                <button type="button" class="rail-btn" data-carousel="<?= e($slug) ?>-rail" data-dir="1" aria-label="Next">→</button>
                            </div>
                        </div>
                    </header>
                </div>
                <div class="rail-wrap">
                    <div class="rail" id="<?= e($slug) ?>-rail">
                        <?php if (!$agencies): ?>
                            <p class="empty">This desk is being set. Check back shortly.</p>
                        <?php endif; ?>
                        <?php foreach ($agencies as $index => $agency):
                            $code = atozee_partner_code($category, $index);
                        ?>
                            <article
                                class="partner-card"
                                data-name="<?= e($agency['name']) ?>"
                                data-description="<?= e($agency['description'] ?? '') ?>"
                                data-image="<?= e(atozee_image_src((string) $agency['image'])) ?>"
                                data-category="<?= e($category['name']) ?>"
                                data-code="<?= e($code) ?>"
                                data-products="<?= e(atozee_public_products_json($agency)) ?>"
                            >
                                <div class="partner-photo">
                                    <img src="<?= e(atozee_image_src((string) $agency['image'])) ?>" alt="<?= e($agency['name']) ?>" loading="lazy">
                                </div>
                                <p class="partner-code"><?= e($code) ?></p>
                                <h3><?= e($agency['name']) ?></h3>
                                <p><?= e($agency['description'] ?? '') ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>

        <section class="voices" id="voices">
            <div class="wrap">
                <div class="section-intro">
                    <p class="eyebrow">Voices</p>
                    <h2>Operators, not slogans.</h2>
                </div>
                <div class="voice-grid">
                    <blockquote>
                        <p>AtoZee connected us to suppliers we could not find elsewhere. Seamless.</p>
                        <footer>Operations Lead, Hospitality Group</footer>
                    </blockquote>
                    <blockquote>
                        <p>Their curation saved us weeks of sourcing. Exceptional quality.</p>
                        <footer>Founder, Specialty Coffee Chain</footer>
                    </blockquote>
                    <blockquote>
                        <p>Fast, transparent, and reliable. Exactly what we needed.</p>
                        <footer>Procurement Manager</footer>
                    </blockquote>
                </div>
            </div>
        </section>

        <section class="contact" id="contact">
            <div class="wrap contact-grid">
                <div>
                    <p class="eyebrow">The desk</p>
                    <h2>Send the brief. We will answer the same day.</h2>
                    <p>Share what you need to source — category, volume, and market. <?= e($owner) ?> and the AtoZee desk usually reply within a few hours.</p>
                </div>
                <div class="contact-panel">
                    <a class="contact-row" href="<?= e(atozee_mailto($email, 'Inquiry from AtoZee', "Hi AtoZee Team,\n\n")) ?>">
                        <span>Email</span>
                        <strong><?= e($email) ?></strong>
                    </a>
                    <a class="contact-row" href="<?= e(atozee_whatsapp_url($whatsapp, 'Hi AtoZee Team')) ?>" target="_blank" rel="noopener">
                        <span>WhatsApp</span>
                        <strong><?= e($phoneDisplay) ?></strong>
                    </a>
                    <a class="contact-row" href="tel:<?= e($whatsapp) ?>">
                        <span>Call</span>
                        <strong><?= e($phoneDisplay) ?></strong>
                    </a>
                    <a class="contact-row" href="<?= e(atozee_whatsapp_url($whatsapp)) ?>" target="_blank" rel="noopener">
                        <span>Principal</span>
                        <strong><?= e($owner) ?></strong>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="wrap footer-grid">
            <div>
                <a class="wordmark" href="#top">
                    <img src="<?= e($logo) ?>" alt="">
                    <span><?= e($brand) ?></span>
                </a>
                <p>Sourcing for hospitality, from A to Zee.</p>
            </div>
            <div>
                <p class="footer-label">Visit</p>
                <a href="#about">Studio</a>
                <a href="#method">Method</a>
                <a href="#contact">Briefing</a>
            </div>
            <div>
                <p class="footer-label">Desks</p>
                <?php foreach ($categories as $category): ?>
                    <a href="#<?= e($category['slug']) ?>"><?= e($category['name']) ?></a>
                <?php endforeach; ?>
            </div>
            <div>
                <p class="footer-label">Studio</p>
                <a href="<?= e(atozee_site_url('admin/')) ?>">Admin</a>
                <a href="<?= e(atozee_whatsapp_url($developerWa)) ?>" target="_blank" rel="noopener">Site by <?= e($developer) ?></a>
                <p>© <?= e($year) ?> <?= e($brand) ?></p>
            </div>
        </div>
    </footer>

    <dialog class="sheet" id="partner-sheet">
        <form method="dialog" class="sheet-close-form">
            <button class="sheet-close" value="close" type="submit">Close</button>
        </form>
        <div class="sheet-view" id="sheet-intro">
            <div class="sheet-media">
                <img id="details-image" src="<?= e($logo) ?>" alt="">
            </div>
            <div class="sheet-body">
                <p class="eyebrow" id="details-category"></p>
                <h2 id="details-title"></h2>
                <p class="partner-code" id="details-shop-number"></p>
                <p id="details-description"></p>
                <div class="sheet-actions">
                    <button type="button" class="btn btn-dark" id="explore-products">Explore</button>
                </div>
            </div>
        </div>
        <div class="sheet-view" id="sheet-products" hidden>
            <div class="sheet-body sheet-products-body">
                <button type="button" class="sheet-back" id="products-back">← Back</button>
                <p class="eyebrow">From the desk</p>
                <h2 id="products-heading"></h2>
                <p class="sheet-products-lead" id="products-lead"></p>
                <div class="product-grid" id="product-grid"></div>
            </div>
        </div>
    </dialog>

    <script src="<?= e(atozee_site_url('js/main.js')) ?>?v=3.2.0"></script>
</body>
</html>
