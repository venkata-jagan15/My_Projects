-- Create database
CREATE DATABASE IF NOT EXISTS section_allocation;
USE section_allocation;

-- Create courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT
);

-- Create course_registrations table
CREATE TABLE IF NOT EXISTS course_registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    section ENUM('A', 'B') NOT NULL,
    registration_time DATETIME NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    jntuno VARCHAR(50) NOT NULL UNIQUE,
    section ENUM('A', 'B') NOT NULL,
    registration_time DATETIME NOT NULL
);

-- Create admin table
CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user with password 'admin123'
-- The password is hashed using PHP's password_hash function
INSERT INTO admin (username, password, name) VALUES
('admin', '$2y$10$YourHashedPasswordHere', 'Administrator')
ON DUPLICATE KEY UPDATE password = '$2y$10$YourHashedPasswordHere';

-- Insert course data
INSERT INTO courses (name, description) VALUES
('Innovation and Entrepreneurship track', 'This course focuses on developing entrepreneurial skills and innovative thinking in students.'),
('Introduction to smart cities and urban planning', 'This course covers the fundamentals of smart city development and urban planning strategies.'); 