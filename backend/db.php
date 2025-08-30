<?php
declare(strict_types=1);
// db.php - Database connection handler
//
// Provides a function to establish a connection to the MySQL database.
// Intended to be included in other PHP scripts that require database access.
// If the connection fails, an HTTP 500 error is returned and a JSON error message is sent.
//
/**
 * Establishes and returns a MySQLi database connection.
 *
 * @author Nick
 * @return mysqli The MySQLi connection object.
 */
function get_db(): mysqli {
    $host = 'localhost'; // Database host
    $user = 'root';      // Database username
    $pass = 'root';      // Database password
    $db   = 'enrolment'; // Database name

    // Create a new MySQLi connection
    $mysqli = new mysqli($host, $user, $pass, $db);
    if ($mysqli->connect_errno) {
        // If connection fails, send HTTP 500 and output error as JSON
        http_response_code(500);
        die(json_encode(['error' => 'Database connection failed']));
    }
    return $mysqli;
}