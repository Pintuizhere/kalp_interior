<?php
require 'admin/config/db.php';

$conn->query("CREATE TABLE IF NOT EXISTS `calc_breakdowns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_slug` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `percent_value` decimal(5,2) NOT NULL,
  `position` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$conn->query("TRUNCATE TABLE calc_breakdowns");

$items = [
    ['TV Unit, Crockery, Vanity & Other Furniture', 28.5, 1],
    ['Wardrobes & Storage', 20.4, 2],
    ['Modular Kitchen', 15.5, 3],
    ['False Ceiling', 9.7, 4],
    ['Electrical & Lighting', 8.9, 5],
    ['Paint & Wall Finishes', 7.5, 6],
    ['Decorative Lights & Accessories', 2.5, 7],
    ['Design, Project Management & Site Supervision', 7.0, 8]
];

foreach (['residential', 'commercial'] as $cat) {
    foreach ($items as $item) {
        $name = $conn->real_escape_string($item[0]);
        $pct = $item[1];
        $pos = $item[2];
        $conn->query("INSERT INTO calc_breakdowns (category_slug, name, percent_value, position) VALUES ('$cat', '$name', $pct, $pos)");
    }
}

echo "Database updated successfully.\n";
