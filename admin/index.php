<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/admin-actions.php';

atozee_admin_handle_request();

$loggedIn = atozee_logged_in();
$view = (string) ($_GET['view'] ?? 'agencies');
$allowed = ['agencies', 'categories', 'settings'];
if (!in_array($view, $allowed, true)) {
    $view = 'agencies';
}

$content = atozee_content();
$categories = $content['categories'];
$flash = atozee_flash();
$logo = atozee_site_url('assets/logo.png');
$editCategoryId = (string) ($_GET['edit_category'] ?? '');
$editAgencyId = (string) ($_GET['edit_agency'] ?? '');
$filterCategory = (string) ($_GET['category'] ?? ($categories[0]['id'] ?? ''));
$editCategory = $editCategoryId !== '' ? atozee_find_category($content, $editCategoryId) : null;
$editAgency = $editAgencyId !== '' ? atozee_find_agency($content, $editAgencyId) : null;
$editProductId = (string) ($_GET['edit_product'] ?? '');
$editProduct = null;

if ($editAgency) {
    $filterCategory = (string) $editAgency['category_id'];
    foreach ($editAgency['products'] ?? [] as $product) {
        if (($product['id'] ?? '') === $editProductId) {
            $editProduct = $product;
            break;
        }
    }
}

$agencies = $filterCategory !== '' ? atozee_agencies_in($content, $filterCategory) : $content['agencies'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AtoZee Admin</title>
    <link rel="shortcut icon" href="<?= e($logo) ?>" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,560;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(atozee_site_url('assets/admin.css')) ?>?v=3.2.0">
</head>
<body class="admin-body">
<?php if (!$loggedIn): ?>
    <main class="admin-login">
        <form class="login-card" method="post">
            <?= atozee_csrf_field() ?>
            <input type="hidden" name="action" value="login">
            <div class="login-brand">
                <img src="<?= e($logo) ?>" alt="AtoZee">
                <span>AtoZee</span>
            </div>
            <h1>Admin panel</h1>
            <p>Sign in to update categories, agencies, and the products shown under Explore.</p>
            <?php if ($flash): ?>
                <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
            <?php endif; ?>
            <div class="form-group" style="margin-bottom:12px">
                <label for="username">Username</label>
                <input id="username" name="username" autocomplete="username" required>
            </div>
            <div class="form-group" style="margin-bottom:18px">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <button class="btn btn-primary" type="submit" style="width:100%">Sign in</button>
        </form>
    </main>
<?php else: ?>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="admin-brand">
                <img src="<?= e($logo) ?>" alt="AtoZee">
                <div>
                    <strong>AtoZee</strong>
                    <small>Content admin</small>
                </div>
            </div>
            <nav class="admin-nav">
                <a class="<?= $view === 'agencies' ? 'active' : '' ?>" href="<?= e(atozee_site_url('admin/?view=agencies')) ?>">Agencies</a>
                <a href="<?= e(atozee_site_url('admin/?view=agencies#explore-products')) ?>">Explore products</a>
                <a class="<?= $view === 'categories' ? 'active' : '' ?>" href="<?= e(atozee_site_url('admin/?view=categories')) ?>">Categories</a>
                <a class="<?= $view === 'settings' ? 'active' : '' ?>" href="<?= e(atozee_site_url('admin/?view=settings')) ?>">Settings</a>
                <a href="<?= e(atozee_site_url('')) ?>">View website</a>
            </nav>
            <div class="spacer"></div>
            <form method="post">
                <?= atozee_csrf_field() ?>
                <input type="hidden" name="action" value="logout">
                <button class="btn btn-ghost" type="submit" style="width:100%">Log out</button>
            </form>
        </aside>

        <main class="admin-main">
            <?php if ($flash): ?>
                <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
            <?php endif; ?>

            <?php if ($view === 'categories'): ?>
                <div class="admin-header">
                    <div>
                        <h1>Categories</h1>
                        <p>These sections appear on the public site. Add, rename, or reorder them at any time.</p>
                    </div>
                </div>

                <section class="panel">
                    <h2><?= $editCategory ? 'Edit category' : 'Add category' ?></h2>
                    <form method="post" class="form-grid">
                        <?= atozee_csrf_field() ?>
                        <input type="hidden" name="action" value="save_category">
                        <input type="hidden" name="id" value="<?= e($editCategory['id'] ?? '') ?>">
                        <div class="form-group">
                            <label for="cat-name">Name</label>
                            <input id="cat-name" name="name" required value="<?= e($editCategory['name'] ?? '') ?>" placeholder="Coffee Shops">
                        </div>
                        <div class="form-group">
                            <label for="cat-nav">Navigation label</label>
                            <input id="cat-nav" name="nav_label" value="<?= e($editCategory['nav_label'] ?? '') ?>" placeholder="Coffee">
                        </div>
                        <div class="form-group full">
                            <label for="cat-cta">Button label</label>
                            <input id="cat-cta" name="cta_label" value="<?= e($editCategory['cta_label'] ?? '') ?>" placeholder="Explore Coffee">
                        </div>
                        <div class="form-group full">
                            <button class="btn btn-primary" type="submit"><?= $editCategory ? 'Save category' : 'Add category' ?></button>
                            <?php if ($editCategory): ?>
                                <a class="btn" href="<?= e(atozee_site_url('admin/?view=categories')) ?>">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </section>

                <section class="panel">
                    <h2>Existing categories</h2>
                    <div class="table-list">
                        <?php foreach ($categories as $category): ?>
                            <article class="row-card category-row">
                                <div>
                                    <h3><?= e($category['name']) ?></h3>
                                    <p><?= count(atozee_agencies_in($content, (string) $category['id'])) ?> agencies · /#<?= e($category['slug']) ?></p>
                                </div>
                                <div class="row-actions">
                                    <form method="post" class="inline-forms">
                                        <?= atozee_csrf_field() ?>
                                        <input type="hidden" name="action" value="move_category">
                                        <input type="hidden" name="id" value="<?= e($category['id']) ?>">
                                        <input type="hidden" name="direction" value="up">
                                        <button class="btn btn-small" type="submit">Up</button>
                                    </form>
                                    <form method="post" class="inline-forms">
                                        <?= atozee_csrf_field() ?>
                                        <input type="hidden" name="action" value="move_category">
                                        <input type="hidden" name="id" value="<?= e($category['id']) ?>">
                                        <input type="hidden" name="direction" value="down">
                                        <button class="btn btn-small" type="submit">Down</button>
                                    </form>
                                    <a class="btn btn-small" href="<?= e(atozee_site_url('admin/?view=categories&edit_category=' . rawurlencode((string) $category['id']))) ?>">Edit</a>
                                    <form method="post" class="inline-forms" data-confirm="Delete this category and all of its agencies?">
                                        <?= atozee_csrf_field() ?>
                                        <input type="hidden" name="action" value="delete_category">
                                        <input type="hidden" name="id" value="<?= e($category['id']) ?>">
                                        <button class="btn btn-small btn-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

            <?php elseif ($view === 'settings'): ?>
                <div class="admin-header">
                    <div>
                        <h1>Settings</h1>
                        <p>Change the admin password. Keep it private — this panel can update the live website.</p>
                    </div>
                </div>
                <section class="panel">
                    <h2>Password</h2>
                    <form method="post" class="form-grid">
                        <?= atozee_csrf_field() ?>
                        <input type="hidden" name="action" value="change_password">
                        <div class="form-group">
                            <label for="current_password">Current password</label>
                            <input id="current_password" name="current_password" type="password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New password</label>
                            <input id="new_password" name="new_password" type="password" minlength="8" required>
                            <small>At least 8 characters.</small>
                        </div>
                        <div class="form-group full">
                            <button class="btn btn-primary" type="submit">Update password</button>
                        </div>
                    </form>
                </section>

            <?php else: ?>
                <div class="admin-header">
                    <div>
                        <h1>Agencies</h1>
                        <p>Change names and images, then add Explore products (name, description, and image) for each agency.</p>
                    </div>
                </div>

                <div class="chips">
                    <?php foreach ($categories as $category): ?>
                        <a class="chip <?= $filterCategory === $category['id'] ? 'active' : '' ?>" href="<?= e(atozee_site_url('admin/?view=agencies&category=' . rawurlencode((string) $category['id']))) ?>">
                            <?= e($category['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <section class="panel" id="explore-products" style="margin-top:16px">
                    <h2><?= $editProduct ? 'Edit Explore product' : 'Add Explore product' ?></h2>
                    <p class="muted" style="margin:-4px 0 16px">This is what visitors see after they tap Explore on an agency. Pick the agency, then add name, description, and image.</p>
                    <?php if (!$content['agencies']): ?>
                        <p class="muted">Add an agency first, then come back here to add its products.</p>
                    <?php else: ?>
                        <form method="post" enctype="multipart/form-data" class="form-grid">
                            <?= atozee_csrf_field() ?>
                            <input type="hidden" name="action" value="save_product">
                            <input type="hidden" name="id" value="<?= e($editProduct['id'] ?? '') ?>">
                            <div class="form-group">
                                <label for="product-agency">Agency</label>
                                <select id="product-agency" name="agency_id" required>
                                    <option value="">Choose an agency</option>
                                    <?php foreach ($content['agencies'] as $agencyOption): ?>
                                        <option value="<?= e($agencyOption['id']) ?>" <?= (($editAgency['id'] ?? '') === $agencyOption['id']) ? 'selected' : '' ?>>
                                            <?= e($agencyOption['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="product-name">Product name</label>
                                <input id="product-name" name="name" required value="<?= e($editProduct['name'] ?? '') ?>" placeholder="Coconut milk">
                            </div>
                            <div class="form-group full">
                                <label for="product-description">Description</label>
                                <textarea id="product-description" name="description" placeholder="What this product is"><?= e($editProduct['description'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="product-image">Upload image</label>
                                <input id="product-image" name="image" type="file" accept="image/jpeg,image/png,image/webp,image/gif">
                                <small>JPG, PNG, WEBP, or GIF. Max 8 MB.</small>
                                <img id="product-preview" class="preview" alt="Preview">
                            </div>
                            <div class="form-group">
                                <label for="product-image-url">Or image URL</label>
                                <input id="product-image-url" name="image_url" type="text" value="<?= e($editProduct['image'] ?? '') ?>" placeholder="https://... or keep the current path">
                            </div>
                            <div class="form-group full">
                                <button class="btn btn-primary" type="submit"><?= $editProduct ? 'Save product' : 'Add product' ?></button>
                                <?php if ($editProduct && $editAgency): ?>
                                    <a class="btn" href="<?= e(atozee_site_url('admin/?view=agencies&edit_agency=' . rawurlencode((string) $editAgency['id']) . '#explore-products')) ?>">Cancel product</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    <?php endif; ?>
                </section>

                <?php if ($editAgency): ?>
                    <section class="panel">
                        <h2>Products for <?= e($editAgency['name']) ?></h2>
                        <div class="table-list">
                            <?php if (empty($editAgency['products'])): ?>
                                <p class="muted">No products yet for this agency. Use the form above to add the first one.</p>
                            <?php endif; ?>
                            <?php foreach ($editAgency['products'] ?? [] as $product): ?>
                                <article class="row-card">
                                    <img src="<?= e(atozee_image_src((string) ($product['image'] ?? ''))) ?>" alt="">
                                    <div>
                                        <h3><?= e($product['name'] ?? '') ?></h3>
                                        <p><?= e($product['description'] ?? '') ?></p>
                                    </div>
                                    <div class="row-actions">
                                        <a class="btn btn-small" href="<?= e(atozee_site_url('admin/?view=agencies&edit_agency=' . rawurlencode((string) $editAgency['id']) . '&edit_product=' . rawurlencode((string) $product['id']) . '#explore-products')) ?>">Edit</a>
                                        <form method="post" data-confirm="Delete this product?">
                                            <?= atozee_csrf_field() ?>
                                            <input type="hidden" name="action" value="delete_product">
                                            <input type="hidden" name="agency_id" value="<?= e($editAgency['id']) ?>">
                                            <input type="hidden" name="id" value="<?= e($product['id']) ?>">
                                            <button class="btn btn-small btn-danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <section class="panel">
                    <h2><?= $editAgency ? 'Edit agency' : 'Add agency' ?></h2>
                    <form method="post" enctype="multipart/form-data" class="form-grid">
                        <?= atozee_csrf_field() ?>
                        <input type="hidden" name="action" value="save_agency">
                        <input type="hidden" name="id" value="<?= e($editAgency['id'] ?? '') ?>">
                        <div class="form-group">
                            <label for="agency-name">Name</label>
                            <input id="agency-name" name="name" required value="<?= e($editAgency['name'] ?? '') ?>" placeholder="Urban Brew">
                        </div>
                        <div class="form-group">
                            <label for="agency-category">Category</label>
                            <select id="agency-category" name="category_id" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= e($category['id']) ?>" <?= (($editAgency['category_id'] ?? $filterCategory) === $category['id']) ? 'selected' : '' ?>>
                                        <?= e($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group full">
                            <label for="agency-description">Short description</label>
                            <textarea id="agency-description" name="description" placeholder="What this partner offers"><?= e($editAgency['description'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="agency-image">Upload image</label>
                            <input id="agency-image" name="image" type="file" accept="image/jpeg,image/png,image/webp,image/gif">
                            <small>JPG, PNG, WEBP, or GIF. Max 8 MB.</small>
                            <img id="image-preview" class="preview" alt="Preview">
                        </div>
                        <div class="form-group">
                            <label for="agency-image-url">Or image URL</label>
                            <input id="agency-image-url" name="image_url" type="text" value="<?= e($editAgency['image'] ?? '') ?>" placeholder="https://... or keep the current path">
                            <?php if ($editAgency && !empty($editAgency['image'])): ?>
                                <small>Leave as-is to keep the current image, or upload a new file to replace it.</small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group full">
                            <button class="btn btn-primary" type="submit"><?= $editAgency ? 'Save agency' : 'Add agency' ?></button>
                            <?php if ($editAgency): ?>
                                <a class="btn" href="<?= e(atozee_site_url('admin/?view=agencies&category=' . rawurlencode($filterCategory))) ?>">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </section>

                <section class="panel">
                    <h2>Listings</h2>
                    <div class="table-list">
                        <?php if (!$agencies): ?>
                            <p style="color:#cbd5e1">No agencies in this category yet.</p>
                        <?php endif; ?>
                        <?php foreach ($agencies as $agency): ?>
                            <article class="row-card">
                                <img src="<?= e(atozee_image_src((string) $agency['image'])) ?>" alt="">
                                <div>
                                    <h3><?= e($agency['name']) ?></h3>
                                    <p><?= e($agency['description'] ?? '') ?></p>
                                    <p class="muted"><?= count($agency['products'] ?? []) ?> Explore products</p>
                                </div>
                                <div class="row-actions">
                                    <a class="btn btn-small btn-primary" href="<?= e(atozee_site_url('admin/?view=agencies&edit_agency=' . rawurlencode((string) $agency['id']) . '#explore-products')) ?>">Products</a>
                                    <a class="btn btn-small" href="<?= e(atozee_site_url('admin/?view=agencies&edit_agency=' . rawurlencode((string) $agency['id']))) ?>">Edit</a>
                                    <form method="post" data-confirm="Delete this agency?">
                                        <?= atozee_csrf_field() ?>
                                        <input type="hidden" name="action" value="delete_agency">
                                        <input type="hidden" name="id" value="<?= e($agency['id']) ?>">
                                        <button class="btn btn-small btn-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </main>
    </div>
    <script src="<?= e(atozee_site_url('js/admin.js')) ?>?v=3.2.0"></script>
<?php endif; ?>
</body>
</html>
