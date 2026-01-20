<?php
// backup.php - Script backup database (akses via browser)
// Password: backup123
require_once 'config/config.php';

if (!isset($_GET['token']) || $_GET['token'] !== 'backup123') {
    die('Access denied');
}

$backup_file = 'backup/database_' . date('Y-m-d_H-i-s') . '.sql';

// Get database connection
$conn = $db->connect();

// Get all tables
$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch(PDO::FETCH_NUM)) {
    $tables[] = $row[0];
}

$sql_script = "-- Database Backup\n";
$sql_script .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
$sql_script .= "-- Database: sistem_pemesanan_barang\n\n";

foreach ($tables as $table) {
    // Drop table if exists
    $sql_script .= "DROP TABLE IF EXISTS `$table`;\n\n";
    
    // Create table
    $result = $conn->query("SHOW CREATE TABLE `$table`");
    $row = $result->fetch(PDO::FETCH_NUM);
    $sql_script .= $row[1] . ";\n\n";
    
    // Insert data
    $result = $conn->query("SELECT * FROM `$table`");
    $row_count = $result->rowCount();
    
    if ($row_count > 0) {
        $sql_script .= "-- Dumping data for table `$table`\n";
        $sql_script .= "LOCK TABLES `$table` WRITE;\n";
        $sql_script .= "/*!40000 ALTER TABLE `$table` DISABLE KEYS */;\n";
        
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $keys = array_map(fn($k) => "`$k`", array_keys($row));
            $values = array_map(fn($v) => "'" . addslashes($v) . "'", array_values($row));
            
            $sql_script .= "INSERT INTO `$table` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n";
        }
        
        $sql_script .= "/*!40000 ALTER TABLE `$table` ENABLE KEYS */;\n";
        $sql_script .= "UNLOCK TABLES;\n\n";
    }
}

// Save to file
if (!is_dir('backup')) {
    mkdir('backup', 0777, true);
}

file_put_contents($backup_file, $sql_script);

// Compress
if (class_exists('ZipArchive')) {
    $zip = new ZipArchive();
    $zip_file = str_replace('.sql', '.zip', $backup_file);
    
    if ($zip->open($zip_file, ZipArchive::CREATE) === TRUE) {
        $zip->addFile($backup_file, basename($backup_file));
        $zip->close();
        unlink($backup_file);
        $backup_file = $zip_file;
    }
}

// Download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($backup_file) . '"');
readfile($backup_file);

// Delete after download
unlink($backup_file);
?>