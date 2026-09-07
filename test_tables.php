<?php
require 'admin/config/db.php';

$updates = [
    "MODERN 4 BHK APARTMENT" => ["category" => "Residential Design", "property_type" => "Apartment"],
    "LUXURY 6 BHK BUNGALOW" => ["category" => "Residential Design", "property_type" => "Bungalow"],
    "CORPORATE OFFICE SPACE" => ["category" => "Commercial Design", "property_type" => "Offices"],
    "MINIMALIST KITCHEN DESIGN" => ["category" => "Interior Design", "property_type" => "Kitchen"],
    "CONTEMPORARY LIVING ROOM" => ["category" => "Interior Design", "property_type" => "Living Room"],
    "ELEGANT MASTER BEDROOM" => ["category" => "Interior Design", "property_type" => "Bed Room"]
];

foreach ($updates as $title => $data) {
    $stmt = $conn->prepare("UPDATE projects SET category = ?, property_type = ? WHERE title LIKE ?");
    $searchTitle = "%" . $title . "%";
    $stmt->bind_param("sss", $data['category'], $data['property_type'], $searchTitle);
    $stmt->execute();
}
echo "Projects updated.";
?>
