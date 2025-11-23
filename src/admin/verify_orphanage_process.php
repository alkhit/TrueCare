<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Read JSON input
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

if (!is_array($input)) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON body']);
    exit;
}

// Extract values
$orphanage_id = (int) ($input['orphanage_id'] ?? 0);
$action        = trim($input['action'] ?? '');
$notes         = trim($input['notes'] ?? '');
$reason        = trim($input['reason'] ?? '');

// Check auth & role
if (!isLoggedIn() || getUserRole() !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permission denied']);
    exit;
}

// Validate orphanage ID
if ($orphanage_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid orphanage ID']);
    exit;
}

// DB connection
// $db is already set by config.php

try {
    if ($action === 'verify') {

        $stmt = $db->prepare("
            UPDATE orphanages 
            SET status = 'verified',
                description = COALESCE(NULLIF(:notes, ''), description),
                updated_at = NOW()
            WHERE orphanage_id = :id
        ");

        $stmt->execute([
            ':id'    => $orphanage_id,
            ':notes' => $notes
        ]);

        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid orphanage or no changes made']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Orphanage verified']);
        exit;
    }

    elseif ($action === 'reject') {

        $stmt = $db->prepare("
            UPDATE orphanages
            SET status = 'rejected',
                description = COALESCE(NULLIF(CONCAT('REJECTED: ', :reason), ''), description),
                updated_at = NOW()
            WHERE orphanage_id = :id
        ");

        $stmt->execute([
            ':id'     => $orphanage_id,
            ':reason' => $reason
        ]);

        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid orphanage or no changes made']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Orphanage rejected']);
        exit;
    }

    else {
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        exit;
    }

} catch (Exception $e) {
    error_log("Orphanage verify error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
