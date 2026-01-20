<?php
// api/precheck.php - API untuk cek perubahan sebelum update
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/config.php';

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = new Database();
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (empty($data['table']) || empty($data['id']) || !isset($data['updates'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Missing required parameters',
            'required' => ['table', 'id', 'updates']
        ]);
        exit();
    }
    
    $table = $data['table'];
    $id = (int)$data['id'];
    $proposed_updates = $data['updates'];
    
    // Get current data
    $current_data = $db->fetchOne("SELECT * FROM $table WHERE id = ?", [$id]);
    
    if (!$current_data) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Record not found']);
        exit();
    }
    
    // Analyze changes
    $changes = [];
    $has_changes = false;
    
    foreach ($proposed_updates as $field => $new_value) {
        $current_value = $current_data[$field] ?? null;
        
        // Skip if field doesn't exist
        if (!array_key_exists($field, $current_data)) {
            continue;
        }
        
        // Compare values
        $is_different = false;
        
        if (is_numeric($current_value) && is_numeric($new_value)) {
            $is_different = (float)$current_value != (float)$new_value;
        } else {
            $is_different = (string)$current_value !== (string)$new_value;
        }
        
        if ($is_different) {
            $changes[$field] = [
                'old' => $current_value,
                'new' => $new_value,
                'type' => gettype($current_value)
            ];
            $has_changes = true;
        }
    }
    
    // Calculate change summary
    $change_summary = [];
    $warning_messages = [];
    
    foreach ($changes as $field => $change) {
        $change_summary[] = "$field: {$change['old']} → {$change['new']}";
        
        // Add warnings for specific conditions
        if ($field === 'stock' && $change['new'] < 0) {
            $warning_messages[] = "Stock cannot be negative";
        }
        
        if ($field === 'price' && $change['new'] < 0) {
            $warning_messages[] = "Price cannot be negative";
        }
        
        if ($field === 'status' && !in_array($change['new'], ['active', 'inactive', 'pending'])) {
            $warning_messages[] = "Invalid status value";
        }
    }
    
    // Check for conflicts (concurrent updates)
    $last_update = $db->fetchOne(
        "SELECT updated_at FROM $table WHERE id = ?",
        [$id]
    );
    
    $conflict_detected = false;
    if (isset($data['last_updated']) && $last_update) {
        $last_updated_client = strtotime($data['last_updated']);
        $last_updated_server = strtotime($last_update['updated_at']);
        
        if ($last_updated_client < $last_updated_server) {
            $conflict_detected = true;
            $warning_messages[] = "Record was modified after you loaded it. Please refresh.";
        }
    }
    
    // Return analysis result
    $result = [
        'success' => true,
        'has_changes' => $has_changes,
        'changes_count' => count($changes),
        'changes' => $changes,
        'change_summary' => $change_summary,
        'conflict_detected' => $conflict_detected,
        'current_data' => $current_data,
        'last_updated' => $last_update['updated_at'] ?? null,
        'warnings' => $warning_messages,
        'should_update' => $has_changes && empty($warning_messages) && !$conflict_detected
    ];
    
    // Add validation messages
    if (!$has_changes) {
        $result['message'] = 'No changes detected. Update not needed.';
    } elseif ($conflict_detected) {
        $result['message'] = 'Conflict detected. Please refresh the data.';
    } elseif (!empty($warning_messages)) {
        $result['message'] = 'Update contains warnings.';
    } else {
        $result['message'] = 'Changes detected. Update can proceed.';
    }
    
    echo json_encode($result);
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>