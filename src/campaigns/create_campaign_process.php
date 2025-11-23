<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

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

// Handle image upload
$image_path = '';
if (isset($_FILES['campaign_image']) && $_FILES['campaign_image']['error'] === UPLOAD_ERR_OK) {
    $img = $_FILES['campaign_image'];
    $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];
    if (!in_array($ext, $allowed)) {
        echo showAlert('danger', 'Invalid image format. Only JPG, JPEG, PNG allowed.');
        exit;
    }
    if ($img['size'] > 5 * 1024 * 1024) {
        echo showAlert('danger', 'Image size exceeds 5MB.');
        exit;
    }
    $image_name = uniqid('cmp_') . '.' . $ext;
    $image_path = '../../assets/images/campaigns/' . $image_name;
    $absolute_path = __DIR__ . '/../../assets/images/campaigns/' . $image_name;
    if (!move_uploaded_file($img['tmp_name'], $absolute_path)) {
        echo showAlert('danger', 'Failed to upload image.');
        exit;
    }
}

// Insert campaign


$stmt = $db->prepare('INSERT INTO campaigns (orphanage_id, title, description, category, target_amount, deadline, image_url, status) VALUES ((SELECT orphanage_id FROM orphanages WHERE user_id = :user_id), :title, :description, :category, :target_amount, :deadline, :image_url, :status)');
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->bindParam(':title', $title);
$stmt->bindParam(':description', $description);
$stmt->bindParam(':category', $category);
$stmt->bindParam(':target_amount', $target_amount);
$stmt->bindParam(':deadline', $deadline);
$stmt->bindParam(':image_url', $image_path);
$status = 'active';
$stmt->bindParam(':status', $status);
$stmt->execute();

echo showAlert('success', 'Campaign created successfully!');
echo '<script>setTimeout(function(){ window.location.href = "my_campaigns.php"; }, 1500);</script>';
