<?php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';

/**
 * get_users.php
 * --------------
 * Returns a JSON array of all users for use in dropdown filters.
 *
 * Output: [{user_id, first_name, surname}, ...]
 *
 * @author Nick
 */

// Connect to the database
$mysqli = get_db();

// Query all users
$res = $mysqli->query("SELECT user_id, first_name, surname FROM users");
$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);

$mysqli->close();
?> 