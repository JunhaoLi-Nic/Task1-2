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

    // Retrieve filter parameters from GET request
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    $course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : null;

    // Build SQL query with optional filters
    $sql = "SELECT e.enrolment_id, u.first_name, u.surname, c.description, e.completion_status
            FROM enrolments e
            JOIN users u ON e.user_id = u.user_id
            JOIN courses c ON e.course_id = c.course_id
            WHERE 1=1";
    $params = [];
    $types = '';

    // Add user_id filter if provided
    if (isset($_GET['user_id']) && $_GET['user_id'] !== '') {
        $sql .= " AND e.user_id = ?";
        $params[] = intval($_GET['user_id']);
        $types .= 'i';
    }
    // Add course_id filter if provided
    if (isset($_GET['course_id']) && $_GET['course_id'] !== '') {
        $sql .= " AND e.course_id = ?";
        $params[] = intval($_GET['course_id']);
        $types .= 'i';
    }

    // Debug: log SQL and params
    error_log('SQL: ' . $sql . ' PARAMS: ' . json_encode($params));

    // Prepare SQL statement
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        error_log('SQL prepare failed: ' . $mysqli->error);
        echo json_encode(['error' => 'SQL prepare failed: ' . $mysqli->error]);
        exit;
    }
    // Bind parameters if any
    if ($params) {
        if (!$stmt->bind_param($types, ...$params)) {
            http_response_code(500);
            error_log('Bind param failed: ' . $stmt->error);
            echo json_encode(['error' => 'Bind param failed: ' . $stmt->error]);
            exit;
        }
    }

    // Execute and fetch results
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);

    // Clean up
    $stmt->close();
    $mysqli->close();
} catch (Throwable $e) {
    // Handle unexpected server errors
    http_response_code(500);
    error_log('ENROLMENTS ERROR: ' . $e->getMessage());
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?> 