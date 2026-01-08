<?php

require_once __DIR__ . '/../src/Config/autoload.php';

use Config\Database;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Database Connection Test</h1>
    
    <?php
    try {
        $pdo = Database::getInstance();
        echo '<p class="success">✅ Database connection successful!</p>';
        
        // Test query
        $stmt = $pdo->query("SELECT DATABASE() as current_db");
        $result = $stmt->fetch();
        echo '<p><strong>Current Database:</strong> ' . htmlspecialchars($result['current_db']) . '</p>';
        
        // Show tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            echo '<p class="error">⚠️ No tables found. Run migrations first!</p>';
        } else {
            echo '<p><strong>Tables found:</strong></p>';
            echo '<ul>';
            foreach ($tables as $table) {
                echo '<li>' . htmlspecialchars($table) . '</li>';
            }
            echo '</ul>';
        }
        
    } catch (PDOException $e) {
        echo '<p class="error">❌ Database connection failed!</p>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    }
    ?>
</body>
</html>