<?php
/**
 * POST /api/add_item.php
 * Report a lost or found item (requires authentication)
 * Supports multipart/form-data for image upload
 */
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);
}

// Require login
$user = requireAuth();

// Read input (supports both JSON and form-data)
$type       = sanitize($_POST['type'] ?? '');
$name       = sanitize($_POST['item_name'] ?? '');
$category   = sanitize($_POST['category'] ?? '');
$location   = sanitize($_POST['location'] ?? '');
$item_date  = sanitize($_POST['item_date'] ?? '');
$desc       = sanitize($_POST['description'] ?? '');
$holding    = sanitize($_POST['holding_location'] ?? '');

// Validation
$errors = [];

if (!in_array($type, ['lost', 'found'])) {
    $errors[] = 'Invalid item type.';
}
if (strlen($name) < 2) {
    $errors[] = 'Item name must be at least 2 characters.';
}
if (empty($category)) {
    $errors[] = 'Please select a category.';
}
if (empty($location)) {
    $errors[] = 'Please select a location.';
}
if (empty($item_date) || $item_date > date('Y-m-d')) {
    $errors[] = 'Please enter a valid date (cannot be in the future).';
}
if (strlen($desc) < 10) {
    $errors[] = 'Description must be at least 10 characters.';
}

if (!empty($errors)) {
    jsonResponse(['success' => false, 'error' => implode(' ', $errors)], 400);
}

// Handle image upload (optional)
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $maxSize = 5 * 1024 * 1024; // 5MB
    $fileTmp = $_FILES['image']['tmp_name'];
    $fileSize = $_FILES['image']['size'];

    if (!is_uploaded_file($fileTmp)) {
        jsonResponse(['success' => false, 'error' => 'Invalid file upload.'], 400);
    }

    if ($fileSize > $maxSize) {
        jsonResponse(['success' => false, 'error' => 'Image must be smaller than 5MB.'], 400);
    }

    // Server-side MIME type detection (do not trust client $_FILES['image']['type'])
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $realMime = finfo_file($finfo, $fileTmp);
    finfo_close($finfo);

    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    if (!array_key_exists($realMime, $allowedMimes)) {
        jsonResponse(['success' => false, 'error' => 'Only JPG, PNG, GIF, and WebP images are allowed.'], 400);
    }

    // Verify file content is a valid image
    $imageInfo = @getimagesize($fileTmp);
    if ($imageInfo === false) {
        jsonResponse(['success' => false, 'error' => 'Uploaded file is not a valid image.'], 400);
    }

    // Derive extension strictly from server-detected MIME type (prevents PHP file execution)
    $ext = $allowedMimes[$realMime];
    $filename = 'item_' . bin2hex(random_bytes(16)) . '.' . $ext;
    $uploadDir = __DIR__ . '/../uploads/';

    // Create uploads directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $destination = $uploadDir . $filename;
    if (move_uploaded_file($fileTmp, $destination)) {
        $imagePath = 'uploads/' . $filename;
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to save uploaded image.'], 500);
    }
}

$db = getDB();

$stmt = $db->prepare("
    INSERT INTO items (user_id, type, status, name, category, location, item_date, description, contact_email, posted_by, holding_location, image_path)
    VALUES (?, ?, 'active', ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $user['id'],
    $type,
    $name,
    $category,
    $location,
    $item_date,
    $desc,
    $user['email'],
    $user['name'],
    $holding ?: null,
    $imagePath,
]);

$itemId = $db->lastInsertId();

// Emoji mapping
$emojiMap = [
    'Electronics'        => '📱',
    'Documents & ID'     => '🪪',
    'Bags & Accessories' => '👜',
    'Clothing'           => '👕',
    'Books & Notes'      => '📚',
    'Keys & Cards'       => '🔑',
    'Other'              => '📦',
];

jsonResponse([
    'success' => true,
    'message' => ucfirst($type) . ' report submitted successfully!',
    'item'    => [
        'id'         => (int)$itemId,
        'type'       => $type,
        'status'     => 'active',
        'name'       => $name,
        'category'   => $category,
        'location'   => $location,
        'date'       => $item_date,
        'desc'       => $desc,
        'contact'    => $user['email'],
        'postedBy'   => $user['name'],
        'emoji'      => $emojiMap[$category] ?? '📦',
        'image_path' => $imagePath,
    ]
], 201);
