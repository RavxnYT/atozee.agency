<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

function atozee_admin_handle_request(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }

    atozee_verify_csrf();
    $action = (string) ($_POST['action'] ?? '');

    try {
        switch ($action) {
            case 'login':
                $user = trim((string) ($_POST['username'] ?? ''));
                $pass = (string) ($_POST['password'] ?? '');
                if (atozee_attempt_login($user, $pass)) {
                    atozee_flash('Welcome back.');
                    atozee_redirect('admin/');
                }
                atozee_flash('Incorrect username or password.', 'error');
                atozee_redirect('admin/');
                break;

            case 'logout':
                atozee_logout();
                atozee_redirect('admin/');
                break;

            case 'save_category':
                atozee_require_admin();
                atozee_save_category_from_post();
                break;

            case 'delete_category':
                atozee_require_admin();
                atozee_delete_category_from_post();
                break;

            case 'move_category':
                atozee_require_admin();
                atozee_move_category_from_post();
                break;

            case 'save_agency':
                atozee_require_admin();
                atozee_save_agency_from_post();
                break;

            case 'delete_agency':
                atozee_require_admin();
                atozee_delete_agency_from_post();
                break;

            case 'save_product':
                atozee_require_admin();
                atozee_save_product_from_post();
                break;

            case 'delete_product':
                atozee_require_admin();
                atozee_delete_product_from_post();
                break;

            case 'change_password':
                atozee_require_admin();
                $error = atozee_change_password(
                    (string) ($_POST['current_password'] ?? ''),
                    (string) ($_POST['new_password'] ?? '')
                );
                if ($error !== '') {
                    atozee_flash($error, 'error');
                } else {
                    atozee_flash('Password updated.');
                }
                atozee_redirect('admin/?view=settings');
                break;

            default:
                atozee_flash('Unknown action.', 'error');
                atozee_redirect('admin/');
        }
    } catch (Throwable $e) {
        atozee_flash($e->getMessage(), 'error');
        atozee_redirect('admin/?view=' . rawurlencode((string) ($_POST['view'] ?? 'agencies')));
    }
}

function atozee_save_category_from_post(): never
{
    $content = atozee_content();
    $id = trim((string) ($_POST['id'] ?? ''));
    $name = trim((string) ($_POST['name'] ?? ''));
    $nav = trim((string) ($_POST['nav_label'] ?? ''));
    $cta = trim((string) ($_POST['cta_label'] ?? ''));

    if ($name === '') {
        throw new RuntimeException('Category name is required.');
    }

    $nav = $nav !== '' ? $nav : $name;
    $cta = $cta !== '' ? $cta : $name;

    if ($id === '') {
        $category = [
            'id' => atozee_id('cat'),
            'name' => $name,
            'nav_label' => $nav,
            'cta_label' => $cta,
            'slug' => atozee_unique_slug($content, $name),
            'sort' => count($content['categories']) + 1,
        ];
        $content['categories'][] = $category;
        atozee_save_content($content);
        atozee_flash('Category added.');
    } else {
        $found = false;
        foreach ($content['categories'] as &$category) {
            if (($category['id'] ?? '') !== $id) {
                continue;
            }
            $category['name'] = $name;
            $category['nav_label'] = $nav;
            $category['cta_label'] = $cta;
            $category['slug'] = atozee_unique_slug($content, $name, $id);
            $found = true;
            break;
        }
        unset($category);

        if (!$found) {
            throw new RuntimeException('Category not found.');
        }

        atozee_save_content($content);
        atozee_flash('Category updated.');
    }

    atozee_redirect('admin/?view=categories');
}

function atozee_delete_category_from_post(): never
{
    $id = trim((string) ($_POST['id'] ?? ''));
    $content = atozee_content();

    if (count($content['categories']) <= 1) {
        throw new RuntimeException('Keep at least one category.');
    }

    $remaining = [];
    $found = false;
    foreach ($content['categories'] as $category) {
        if (($category['id'] ?? '') === $id) {
            $found = true;
            continue;
        }
        $remaining[] = $category;
    }

    if (!$found) {
        throw new RuntimeException('Category not found.');
    }

    foreach ($content['agencies'] as $agency) {
        if (($agency['category_id'] ?? '') === $id) {
            atozee_delete_upload_if_local((string) ($agency['image'] ?? ''));
        }
    }

    $content['categories'] = $remaining;
    $content['agencies'] = array_values(array_filter(
        $content['agencies'],
        static fn($agency) => ($agency['category_id'] ?? '') !== $id
    ));

    foreach ($content['categories'] as $index => &$category) {
        $category['sort'] = $index + 1;
    }
    unset($category);

    atozee_save_content($content);
    atozee_flash('Category removed.');
    atozee_redirect('admin/?view=categories');
}

function atozee_move_category_from_post(): never
{
    $id = trim((string) ($_POST['id'] ?? ''));
    $direction = (string) ($_POST['direction'] ?? 'down');
    $content = atozee_content();
    $categories = $content['categories'];
    $index = null;

    foreach ($categories as $i => $category) {
        if (($category['id'] ?? '') === $id) {
            $index = $i;
            break;
        }
    }

    if ($index === null) {
        throw new RuntimeException('Category not found.');
    }

    $swapWith = $direction === 'up' ? $index - 1 : $index + 1;
    if (!isset($categories[$swapWith])) {
        atozee_redirect('admin/?view=categories');
    }

    $tmp = $categories[$index];
    $categories[$index] = $categories[$swapWith];
    $categories[$swapWith] = $tmp;

    foreach ($categories as $i => &$category) {
        $category['sort'] = $i + 1;
    }
    unset($category);

    $content['categories'] = $categories;
    atozee_save_content($content);
    atozee_redirect('admin/?view=categories');
}

function atozee_save_agency_from_post(): never
{
    $content = atozee_content();
    $id = trim((string) ($_POST['id'] ?? ''));
    $name = trim((string) ($_POST['name'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $categoryId = trim((string) ($_POST['category_id'] ?? ''));
    $imageUrl = trim((string) ($_POST['image_url'] ?? ''));

    if ($name === '') {
        throw new RuntimeException('Agency name is required.');
    }

    if (atozee_find_category($content, $categoryId) === null) {
        throw new RuntimeException('Please choose a valid category.');
    }

    $uploaded = atozee_handle_upload($_FILES['image'] ?? null);
    $image = $uploaded ?: $imageUrl;

    if ($id === '') {
        if ($image === '') {
            throw new RuntimeException('Add an image file or an image URL.');
        }

        $inCategory = atozee_agencies_in($content, $categoryId);
        $newId = atozee_id('ag');
        $content['agencies'][] = [
            'id' => $newId,
            'category_id' => $categoryId,
            'name' => $name,
            'description' => $description,
            'image' => $image,
            'sort' => count($inCategory) + 1,
            'products' => [],
        ];
        atozee_save_content($content);
        atozee_flash('Agency added. Add the products visitors will see under Explore.');
        atozee_redirect('admin/?view=agencies&edit_agency=' . rawurlencode($newId) . '#explore-products');
    } else {
        $found = false;
        foreach ($content['agencies'] as &$agency) {
            if (($agency['id'] ?? '') !== $id) {
                continue;
            }

            $previous = (string) ($agency['image'] ?? '');
            if ($uploaded) {
                atozee_delete_upload_if_local($previous);
                $agency['image'] = $uploaded;
            } elseif ($imageUrl !== '') {
                if ($imageUrl !== $previous) {
                    atozee_delete_upload_if_local($previous);
                }
                $agency['image'] = $imageUrl;
            }

            $agency['name'] = $name;
            $agency['description'] = $description;
            $agency['category_id'] = $categoryId;
            $found = true;
            break;
        }
        unset($agency);

        if (!$found) {
            throw new RuntimeException('Agency not found.');
        }

        atozee_save_content($content);
        atozee_flash('Agency updated.');
        atozee_redirect('admin/?view=agencies&edit_agency=' . rawurlencode($id));
    }
}

function atozee_delete_agency_from_post(): never
{
    $id = trim((string) ($_POST['id'] ?? ''));
    $content = atozee_content();
    $categoryId = '';
    $remaining = [];
    $found = false;

    foreach ($content['agencies'] as $agency) {
        if (($agency['id'] ?? '') === $id) {
            $found = true;
            $categoryId = (string) ($agency['category_id'] ?? '');
            atozee_delete_upload_if_local((string) ($agency['image'] ?? ''));
            foreach ($agency['products'] ?? [] as $product) {
                atozee_delete_upload_if_local((string) ($product['image'] ?? ''));
            }
            continue;
        }
        $remaining[] = $agency;
    }

    if (!$found) {
        throw new RuntimeException('Agency not found.');
    }

    $content['agencies'] = $remaining;
    atozee_save_content($content);
    atozee_flash('Agency removed.');
    atozee_redirect('admin/?view=agencies&category=' . rawurlencode($categoryId));
}

function atozee_save_product_from_post(): never
{
    $content = atozee_content();
    $agencyId = trim((string) ($_POST['agency_id'] ?? ''));
    $productId = trim((string) ($_POST['id'] ?? ''));
    $name = trim((string) ($_POST['name'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $imageUrl = trim((string) ($_POST['image_url'] ?? ''));

    if ($name === '') {
        throw new RuntimeException('Product name is required.');
    }

    $agency = atozee_find_agency($content, $agencyId);
    if ($agency === null) {
        throw new RuntimeException('Agency not found.');
    }

    $uploaded = atozee_handle_upload($_FILES['image'] ?? null);
    $image = $uploaded ?: $imageUrl;
    $foundAgency = false;

    foreach ($content['agencies'] as &$item) {
        if (($item['id'] ?? '') !== $agencyId) {
            continue;
        }
        $foundAgency = true;
        $item['products'] = array_values($item['products'] ?? []);

        if ($productId === '') {
            if ($image === '') {
                throw new RuntimeException('Add a product image or image URL.');
            }
            $item['products'][] = [
                'id' => atozee_id('pr'),
                'name' => $name,
                'description' => $description,
                'image' => $image,
                'sort' => count($item['products']) + 1,
            ];
            break;
        }

        $foundProduct = false;
        foreach ($item['products'] as &$product) {
            if (($product['id'] ?? '') !== $productId) {
                continue;
            }
            $previous = (string) ($product['image'] ?? '');
            if ($uploaded) {
                atozee_delete_upload_if_local($previous);
                $product['image'] = $uploaded;
            } elseif ($imageUrl !== '') {
                if ($imageUrl !== $previous) {
                    atozee_delete_upload_if_local($previous);
                }
                $product['image'] = $imageUrl;
            }
            $product['name'] = $name;
            $product['description'] = $description;
            $foundProduct = true;
            break;
        }
        unset($product);

        if (!$foundProduct) {
            throw new RuntimeException('Product not found.');
        }
        break;
    }
    unset($item);

    if (!$foundAgency) {
        throw new RuntimeException('Agency not found.');
    }

    atozee_save_content($content);
    atozee_flash($productId === '' ? 'Product added.' : 'Product updated.');
    atozee_redirect('admin/?view=agencies&edit_agency=' . rawurlencode($agencyId) . '#explore-products');
}

function atozee_delete_product_from_post(): never
{
    $agencyId = trim((string) ($_POST['agency_id'] ?? ''));
    $productId = trim((string) ($_POST['id'] ?? ''));
    $content = atozee_content();
    $found = false;

    foreach ($content['agencies'] as &$agency) {
        if (($agency['id'] ?? '') !== $agencyId) {
            continue;
        }
        $remaining = [];
        foreach ($agency['products'] ?? [] as $product) {
            if (($product['id'] ?? '') === $productId) {
                $found = true;
                atozee_delete_upload_if_local((string) ($product['image'] ?? ''));
                continue;
            }
            $remaining[] = $product;
        }
        $agency['products'] = array_values($remaining);
        break;
    }
    unset($agency);

    if (!$found) {
        throw new RuntimeException('Product not found.');
    }

    atozee_save_content($content);
    atozee_flash('Product removed.');
    atozee_redirect('admin/?view=agencies&edit_agency=' . rawurlencode($agencyId) . '#explore-products');
}
