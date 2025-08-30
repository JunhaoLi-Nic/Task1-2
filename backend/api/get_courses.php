<?php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';

/**
 * get_courses.php
 * ---------------
 * Returns a JSON array of all courses for use in dropdown filters.
 *
 * Output: [{course_id, description}, ...]
 *
 * @author Nick
 */

// Connect to the database
$mysqli = get_db();

// Query all courses
$res = $mysqli->query("SELECT course_id, description FROM courses");
$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);

$mysqli->close();
?> 