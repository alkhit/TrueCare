<?php
include 'includes/config.php';

echo "<h2>Complete Database Structure Test</h2>";

try {
    // Test each table
    $tables = ['users', 'orphanages', 'campaigns', 'donations', 'verifications'];
    
    foreach ($tables as $table) {
        echo "<h3>Table: $table</h3>";
        
        $stmt = $db->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show sample data
        $sample = $db->query("SELECT COUNT(*) as count FROM $table")->fetch();
        echo "<p>Records: {$sample['count']}</p>";
    }
    
    echo "<p style='color: green;'>✅ All tables checked successfully</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>