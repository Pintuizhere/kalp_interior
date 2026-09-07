<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $location = $conn->real_escape_string($_POST['location'] ?? '');
    $property_category = $conn->real_escape_string($_POST['property_category'] ?? '');
    $property_type = $conn->real_escape_string($_POST['property_type'] ?? '');
    $design_style = $conn->real_escape_string($_POST['design_style'] ?? '');
    $package = $conn->real_escape_string($_POST['package'] ?? '');
    $estimated_cost = $conn->real_escape_string($_POST['estimated_cost'] ?? '');
    
    $sql = "INSERT INTO estimate_requests (name, phone, location, property_category, property_type, design_style, package, estimated_cost) 
            VALUES ('$name', '$phone', '$location', '$property_category', '$property_type', '$design_style', '$package', '$estimated_cost')";
            
    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Estimate request saved.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
    }
}
?>
