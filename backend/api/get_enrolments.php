<?php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';

/**
 * get_enrolments.php
 * ------------------
 * Returns a JSON array of enrolment records, including user and course details.
 * Supports optional filtering by user_id and/or course_id via GET parameters.
 *
 * Query Parameters:
 *   user_id (optional): Filter by user ID
 *   course_id (optional): Filter by course ID
 *   completion_status (optional): Filter by completion status
 *
 * Example usage:
 *   get_enrolments.php?user_id=1&course_id=2
 *
 * Error Handling:
 *   - Returns HTTP 500 and a JSON error message on server/database errors.
 *
 * @author Nick
 */

try {
    $mysqli = get_db();

    // By adding limit and offset, we can get the data in pages.
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    if ($limit < 1) $limit = 20;
    if ($limit > 1000) $limit = 1000;
    if ($offset < 0) $offset = 0;

    $sql = "SELECT e.enrolment_id, u.first_name, u.surname, c.description, e.completion_status
            FROM enrolments_100000 e
            JOIN users u ON e.user_id = u.user_id
            JOIN courses c ON e.course_id = c.course_id
            WHERE 1=1";
    $params = [];
    $types = '';
    if (isset($_GET['user_id']) && $_GET['user_id'] !== '') {
        $sql .= " AND e.user_id = ?";
        $params[] = intval($_GET['user_id']);
        $types .= 'i';
    }
    if (isset($_GET['course_id']) && $_GET['course_id'] !== '') {
        $sql .= " AND e.course_id = ?";
        $params[] = intval($_GET['course_id']);
        $types .= 'i';
    }
    if (isset($_GET['completion_status']) && $_GET['completion_status'] !== '') {
        $sql .= " AND e.completion_status = ?";
        $params[] = $_GET['completion_status'];
        $types .= 's';
    }
    // Limit is showing how many records to return and offset is sjowing from which record to start
    $sql .= " ORDER BY e.enrolment_id ASC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    // For total count
    $count_sql = "SELECT COUNT(*) as total FROM enrolments_100000 e WHERE 1=1";
    $count_params = [];
    $count_types = '';
    if (isset($_GET['user_id']) && $_GET['user_id'] !== '') {
        $count_sql .= " AND e.user_id = ?";
        $count_params[] = intval($_GET['user_id']);
        $count_types .= 'i';
    }
    if (isset($_GET['course_id']) && $_GET['course_id'] !== '') {
        $count_sql .= " AND e.course_id = ?";
        $count_params[] = intval($_GET['course_id']);
        $count_types .= 'i';
    }
    if (isset($_GET['completion_status']) && $_GET['completion_status'] !== '') {
        $count_sql .= " AND e.completion_status = ?";
        $count_params[] = $_GET['completion_status'];
        $count_types .= 's';
    }
    $count_stmt = $mysqli->prepare($count_sql);
    if ($count_params) {
        $count_stmt->bind_param($count_types, ...$count_params);
    }
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total = $count_result->fetch_assoc()['total'];
    $count_stmt->close();

    // Prepare SQL statement for data
    $stmt = $mysqli->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(['data' => $data, 'total' => $total]);
    $stmt->close();
    $mysqli->close();
} catch (Throwable $e) {
    // Handle unexpected server errors
    http_response_code(500);
    error_log('ENROLMENTS ERROR: ' . $e->getMessage());
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?> 