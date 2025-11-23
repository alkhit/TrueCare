<?php
include '../../includes/config.php';
require_once '../../includes/functions.php';

// Ensure $db is defined
if (!isset($db)) {
    $db = get_db();
}

header('Content-Type: application/json');

try {
    $query = "SELECT c.*, o.name as orphanage_name, o.location 
              FROM campaigns c 
              JOIN orphanages o ON c.orphanage_id = o.orphanage_id 
              WHERE c.status = 'active' 
              ORDER BY c.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($campaigns);
} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Failed to fetch campaigns: ' . $e->getMessage()
    ]);
}
?>