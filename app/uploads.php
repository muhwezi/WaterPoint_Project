<?php

declare(strict_types=1);

/** @return array{0:?string,1:?string,2:?string} */
function inspectOptionalPhoto(mixed $file, int $limit, array $mimeTypes): array
{
    if ($file === null || (is_array($file) && ($file['error'] ?? null) === UPLOAD_ERR_NO_FILE)) {
        return [null, null, null];
    }
    if (!is_array($file) || is_array($file['name'] ?? null)
        || ($file['error'] ?? null) !== UPLOAD_ERR_OK) {
        return [null, null, 'The photo could not be uploaded.'];
    }
    $temp = $file['tmp_name'] ?? null;
    if (!is_string($temp) || !is_uploaded_file($temp)) {
        return [null, null, 'The uploaded photo could not be verified.'];
    }
    if (($file['size'] ?? 0) < 1 || $file['size'] > $limit) {
        return [null, null, 'The photo must be smaller than 5 MB.'];
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($temp);
    if (!isset($mimeTypes[$mime]) || @getimagesize($temp) === false) {
        return [null, null, 'Use a valid JPG, PNG or WebP image.'];
    }
    return [$temp, $mimeTypes[$mime], null];
}

/** Validated optional repair photo. Returns a public-relative path or null. */
function storeRepairPhoto(string $field = 'repair_photo'): ?string
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$field];
    if (!is_array($file) || is_array($file['name'] ?? null)
        || ($file['error'] ?? null) !== UPLOAD_ERR_OK
        || !is_uploaded_file((string)($file['tmp_name'] ?? ''))
        || (int)($file['size'] ?? 0) < 1
        || (int)$file['size'] > 5 * 1024 * 1024) {
        throw new DomainException('Choose a JPG, PNG, or WebP photo under 5 MB.');
    }
    $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($types[$mime]) || @getimagesize($file['tmp_name']) === false) {
        throw new DomainException('The repair photo is not a valid image.');
    }
    $folder = dirname(__DIR__) . '/public/uploads/repairs';
    if (!is_dir($folder) && !mkdir($folder, 0755, true) && !is_dir($folder)) {
        throw new RuntimeException('The repair photo folder is unavailable.');
    }
    $name = 'repair_' . bin2hex(random_bytes(16)) . '.' . $types[$mime];
    if (!move_uploaded_file($file['tmp_name'], $folder . '/' . $name)) {
        throw new RuntimeException('The repair photo could not be saved.');
    }
    return 'uploads/repairs/' . $name;
}
