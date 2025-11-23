<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

include '../../includes/config.php';
require_once '../../includes/functions.php';

// Ensure $db is defined
if (!isset($db)) {
    $db = get_db();
}

// This would process the actual payment
// For now, we'll just show a success message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campaign_id = $_POST['campaign_id'] ?? null;
    $amount = $_POST['amount'] ?? 0;
    $payment_method = $_POST['payment_method'] ?? 'mpesa';
    
    // 1. Validate input
    $user_id = $_SESSION['user_id'];
    $amount = floatval($amount);
    if (!$campaign_id || $amount < 100) {
        header("Location: donate.php?error=invalid");
        exit;
    }

    // 2. Record the donation in the database
    $transaction_id = 'TXN_' . uniqid();
    $message = $_POST['message'] ?? '';
    $is_anonymous = isset($_POST['anonymous']) ? 1 : 0;
    $status = 'completed';

    $stmt = $db->prepare("INSERT INTO donations (user_id, campaign_id, amount, payment_method, transaction_id, status, message, is_anonymous) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $campaign_id, $amount, $payment_method, $transaction_id, $status, $message, $is_anonymous]);

    // 3. Update the campaign's current_amount
    $stmt2 = $db->prepare("UPDATE campaigns SET current_amount = current_amount + ? WHERE campaign_id = ?");
    $stmt2->execute([$amount, $campaign_id]);

    // 4. Store donation info for success page
    $_SESSION['donation_success'] = [
        'amount' => $amount,
        'campaign_id' => $campaign_id,
        'payment_method' => $payment_method,
        'transaction_id' => $transaction_id,
        'message' => $message,
        'is_anonymous' => $is_anonymous
    ];

    header("Location: donation_success.php");
    exit;
} else {
    header("Location: donate.php");
    exit;
}
?>