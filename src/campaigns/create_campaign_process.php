<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
// Ensure $db is defined
if (!isset($db)) {
    $db = get_db();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'orphanage') {
    header('Location: ../../login.php');
    exit;
}

// Validate required fields
$title = sanitizeInput($_POST['title'] ?? '');
$description = sanitizeInput($_POST['description'] ?? '');
$category = sanitizeInput($_POST['category'] ?? '');
$target_amount = (int)($_POST['target_amount'] ?? 0);
$deadline = sanitizeInput($_POST['deadline'] ?? '');

if ($title === '' || $description === '' || $category === '' || $target_amount < 1000 || $deadline === '') {
    echo showAlert('danger', 'Please fill all required fields correctly.');
    exit;
}

// Set image path based on category
$category_safe = !empty($category) ? strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', $category)) : 'default';
$image_path = '../../assets/images/campaigns/' . $category_safe . '.jpg';
if (!file_exists($image_path)) {
    $image_path = '../../assets/images/campaigns/default.jpg';
}

// Insert campaign

// Check orphanage exists and is verified
$stmt = $db->prepare('SELECT orphanage_id, status FROM orphanages WHERE user_id = :user_id');
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$orphanage = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$orphanage) {
    echo showAlert('danger', 'No orphanage found for this user. Please register your orphanage first.');
    exit;
}
if ($orphanage['status'] !== 'verified') {
    echo showAlert('danger', 'Your orphanage must be verified before creating campaigns.');
    exit;
}
$orphanage_id = $orphanage['orphanage_id'];

$stmt = $db->prepare('INSERT INTO campaigns (orphanage_id, title, description, category, target_amount, deadline, image_url, status) VALUES (:orphanage_id, :title, :description, :category, :target_amount, :deadline, :image_url, :status)');
$stmt->bindParam(':orphanage_id', $orphanage_id);
$stmt->bindParam(':title', $title);
$stmt->bindParam(':description', $description);
$stmt->bindParam(':category', $category);
$stmt->bindParam(':target_amount', $target_amount);
$stmt->bindParam(':deadline', $deadline);
$stmt->bindParam(':image_url', $image_path);
$status = 'active';
$stmt->bindParam(':status', $status);
if ($stmt->execute()) {
    echo showAlert('success', 'Campaign created successfully!');
    echo '<script>setTimeout(function(){ window.location.href = "my_campaigns.php"; }, 1500);</script>';
} else {
    echo showAlert('danger', 'Error creating campaign. Please try again or contact support.');
}
