<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config/db.php';

// Fetch all leads
$query = "SELECT * FROM leads ORDER BY created_at DESC";
$result = $conn->query($query);

if ($result) {
    $filename = "leads_export_" . date('Ymd_His') . ".csv";
    
    // Set headers to force download as CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');
    
    // Output the column headings
    fputcsv($output, array('ID', 'Name', 'Email', 'Service', 'Message', 'Status', 'Date Submitted'));
    
    // Output data rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array(
            $row['id'],
            $row['name'],
            $row['email'],
            $row['service'],
            $row['message'],
            $row['status'],
            $row['created_at']
        ));
    }
    
    fclose($output);
    exit;
} else {
    echo "Error fetching leads from the database.";
}
?>
