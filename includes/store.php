<?php

declare(strict_types=1);

function atozee_ensure_storage(): void
{
    foreach ([ATOZEE_DATA, ATOZEE_UPLOADS] as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }

    if (!is_file(ATOZEE_CONTENT_FILE)) {
        copy(ATOZEE_SEED_CONTENT, ATOZEE_CONTENT_FILE);
    }

    if (!is_file(ATOZEE_SETTINGS_FILE)) {
        copy(ATOZEE_SEED_SETTINGS, ATOZEE_SETTINGS_FILE);
    }
}

function atozee_read_json(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $raw = file_get_contents($path);
    $data = json_decode((string) $raw, true);

    return is_array($data) ? $data : [];
}

function atozee_write_json(string $path, array $data): bool
{
    $dir = dirname($path);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        return false;
    }

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    $fp = fopen($path, 'c+');
    if ($fp === false) {
        return false;
    }

    try {
        if (!flock($fp, LOCK_EX)) {
            return false;
        }
        ftruncate($fp, 0);
        rewind($fp);
        $ok = fwrite($fp, $json . PHP_EOL) !== false;
        fflush($fp);
        flock($fp, LOCK_UN);
        return $ok;
    } finally {
        fclose($fp);
    }
}

function atozee_content(): array
{
    $content = atozee_read_json(ATOZEE_CONTENT_FILE);
    $content['site'] = $content['site'] ?? [];
    $content['categories'] = array_values($content['categories'] ?? []);
    $content['agencies'] = array_values($content['agencies'] ?? []);

    usort($content['categories'], static fn($a, $b) => ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0));
    usort($content['agencies'], static fn($a, $b) => ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0));

    if (
        atozee_repair_agency_images($content)
        || atozee_merge_seed_agencies($content)
        || atozee_merge_seed_products($content)
    ) {
        atozee_save_content($content);
    }

    foreach ($content['agencies'] as &$agency) {
        $products = array_values($agency['products'] ?? []);
        usort($products, static fn($a, $b) => ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0));
        $agency['products'] = $products;
    }
    unset($agency);

    return $content;
}

function atozee_repair_agency_images(array &$content): bool
{
    $replacements = [
        'photo-1509042239860-f550ce710b41' => 'https://images.unsplash.com/photo-1459755486867-b55449bb39ff?auto=format&fit=crop&w=1200&q=80',
    ];

    $changed = false;
    foreach ($content['agencies'] as &$agency) {
        $image = (string) ($agency['image'] ?? '');
        foreach ($replacements as $needle => $replacement) {
            if ($image !== '' && str_contains($image, $needle)) {
                $agency['image'] = $replacement;
                $changed = true;
            }
        }
    }
    unset($agency);

    return $changed;
}

function atozee_merge_seed_agencies(array &$content): bool
{
    $seed = atozee_read_json(ATOZEE_SEED_CONTENT);
    $seen = [];
    foreach ($content['agencies'] as $agency) {
        $id = (string) ($agency['id'] ?? '');
        $name = strtolower(trim((string) ($agency['name'] ?? '')));
        if ($id !== '') {
            $seen[$id] = true;
        }
        if ($name !== '') {
            $seen['name:' . $name] = true;
        }
    }

    $changed = false;
    foreach ($seed['agencies'] ?? [] as $agency) {
        $id = (string) ($agency['id'] ?? '');
        $name = strtolower(trim((string) ($agency['name'] ?? '')));
        if ($id === '' || isset($seen[$id]) || ($name !== '' && isset($seen['name:' . $name]))) {
            continue;
        }
        $content['agencies'][] = $agency;
        $seen[$id] = true;
        $changed = true;
    }

    return $changed;
}

function atozee_merge_seed_products(array &$content): bool
{
    $seed = atozee_read_json(ATOZEE_SEED_CONTENT);
    $byId = [];
    foreach ($seed['agencies'] ?? [] as $agency) {
        $id = (string) ($agency['id'] ?? '');
        if ($id !== '') {
            $byId[$id] = $agency;
        }
    }

    $changed = false;
    foreach ($content['agencies'] as &$agency) {
        if (array_key_exists('products', $agency)) {
            continue;
        }
        $id = (string) ($agency['id'] ?? '');
        $products = array_values($byId[$id]['products'] ?? []);
        if ($products === []) {
            $agency['products'] = [];
            $changed = true;
            continue;
        }
        $agency['products'] = $products;
        $changed = true;
    }
    unset($agency);

    return $changed;
}

function atozee_save_content(array $content): bool
{
    $content['categories'] = array_values($content['categories'] ?? []);
    $content['agencies'] = array_values($content['agencies'] ?? []);
    return atozee_write_json(ATOZEE_CONTENT_FILE, $content);
}

function atozee_settings(): array
{
    return atozee_read_json(ATOZEE_SETTINGS_FILE);
}

function atozee_save_settings(array $settings): bool
{
    return atozee_write_json(ATOZEE_SETTINGS_FILE, $settings);
}

function atozee_find_category(array $content, string $id): ?array
{
    foreach ($content['categories'] as $category) {
        if (($category['id'] ?? '') === $id) {
            return $category;
        }
    }

    return null;
}

function atozee_find_agency(array $content, string $id): ?array
{
    foreach ($content['agencies'] as $agency) {
        if (($agency['id'] ?? '') === $id) {
            return $agency;
        }
    }

    return null;
}

function atozee_agencies_in(array $content, string $categoryId): array
{
    return array_values(array_filter(
        $content['agencies'],
        static fn($agency) => ($agency['category_id'] ?? '') === $categoryId
    ));
}

function atozee_public_categories(array $content): array
{
    $out = [];
    foreach ($content['categories'] ?? [] as $category) {
        if (($category['public'] ?? true) === false) {
            continue;
        }

        $haystack = strtolower(implode(' ', [
            (string) ($category['slug'] ?? ''),
            (string) ($category['nav_label'] ?? ''),
            (string) ($category['name'] ?? ''),
        ]));
        if (str_contains($haystack, 'hotel')) {
            continue;
        }
        if (str_contains($haystack, 'coffee')) {
            $category['nav_label'] = 'Coffee shop';
        }

        $out[] = $category;
    }

    return $out;
}

function atozee_public_products_json(array $agency): string
{
    $out = [];
    foreach (array_values($agency['products'] ?? []) as $product) {
        $out[] = [
            'name' => (string) ($product['name'] ?? ''),
            'description' => (string) ($product['description'] ?? ''),
            'image' => atozee_image_src((string) ($product['image'] ?? '')),
        ];
    }

    return json_encode($out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '[]';
}

function atozee_unique_slug(array $content, string $name, ?string $ignoreId = null): string
{
    $base = atozee_slug($name);
    $slug = $base;
    $i = 2;

    $exists = static function (string $candidate) use ($content, $ignoreId): bool {
        foreach ($content['categories'] as $category) {
            if (($category['id'] ?? '') === $ignoreId) {
                continue;
            }
            if (($category['slug'] ?? '') === $candidate) {
                return true;
            }
        }
        return false;
    };

    while ($exists($slug)) {
        $slug = $base . '-' . $i;
        $i++;
    }

    return $slug;
}

function atozee_delete_upload_if_local(string $image): void
{
    if ($image === '' || preg_match('#^https?://#i', $image)) {
        return;
    }

    $normalized = ltrim($image, '/');
    if (!str_starts_with($normalized, 'uploads/agencies/')) {
        return;
    }

    $full = ATOZEE_ROOT . '/' . $normalized;
    $realRoot = realpath(ATOZEE_UPLOADS);
    $realFile = realpath($full);

    if ($realRoot && $realFile && str_starts_with($realFile, $realRoot) && is_file($realFile)) {
        unlink($realFile);
    }
}

function atozee_handle_upload(?array $file): ?string
{
    if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed. Please try a smaller JPG, PNG, WEBP, or GIF file.');
    }

    if (($file['size'] ?? 0) > 8 * 1024 * 1024) {
        throw new RuntimeException('Image is too large. Maximum size is 8 MB.');
    }

    $tmp = (string) ($file['tmp_name'] ?? '');
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        throw new RuntimeException('Invalid upload.');
    }

    $info = @getimagesize($tmp);
    if ($info === false) {
        throw new RuntimeException('Please upload a valid image file.');
    }

    $mime = $info['mime'] ?? '';
    $map = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    if (!isset($map[$mime])) {
        throw new RuntimeException('Allowed image types: JPG, PNG, WEBP, GIF.');
    }

    if (!is_dir(ATOZEE_UPLOADS) && !mkdir(ATOZEE_UPLOADS, 0775, true) && !is_dir(ATOZEE_UPLOADS)) {
        throw new RuntimeException('Upload folder is not writable. Set permissions on uploads/agencies to 775.');
    }

    $filename = 'agency-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $map[$mime];
    $destination = ATOZEE_UPLOADS . '/' . $filename;

    if (!move_uploaded_file($tmp, $destination)) {
        throw new RuntimeException('Could not save the uploaded image. Check folder permissions.');
    }

    return 'uploads/agencies/' . $filename;
}
