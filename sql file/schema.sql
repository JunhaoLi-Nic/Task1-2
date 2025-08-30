-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    surname VARCHAR(50) NOT NULL
);

-- Courses Table
CREATE TABLE courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255) NOT NULL
);

-- Enrolments Table
CREATE TABLE enrolments (
    enrolment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    completion_status ENUM('not started', 'in progress', 'completed') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (course_id) REFERENCES courses(course_id),
    INDEX(user_id),
    INDEX(course_id)
);

-- Sample Data
INSERT INTO users (first_name, surname) VALUES
('Alice', 'Smith'), ('Bob', 'Jones'), ('Carol', 'White');

INSERT INTO courses (description) VALUES
('PHP Basics'), ('MySQL Essentials'), ('JavaScript Advanced');

INSERT INTO enrolments (user_id, course_id, completion_status) VALUES
(1, 1, 'completed'), (1, 2, 'in progress'), (2, 1, 'not started'), (3, 3, 'completed');
