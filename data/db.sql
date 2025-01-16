CREATE DATABASE courselab;
USE courselab;

-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('student', 'teacher', 'admin') NOT NULL,
    profile_image TEXT,
    banner_image TEXT, 
    bio TEXT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
);

-- Courses Table
CREATE TABLE courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    teacher_id INT,
    category VARCHAR(100),
    banner_image VARCHAR(255) NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    content TEXT, -- stores HTML content
    content_type ENUM('video', 'text') DEFAULT 'text',
    FOREIGN KEY (teacher_id) REFERENCES Users(user_id)
);

-- Enrollments Table
CREATE TABLE enrollments (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    course_id INT,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_status BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (student_id) REFERENCES Users(user_id),
    FOREIGN KEY (course_id) REFERENCES Courses(course_id)
);

-- Certificates Table
CREATE TABLE certificates (
    certificate_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    course_id INT,
    certificate_data LONGBLOB, -- Stores PDF data
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES Users(user_id),
    FOREIGN KEY (course_id) REFERENCES Courses(course_id)
);

-- Tags Table
CREATE TABLE tags (
    tag_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- CourseTags Table
CREATE TABLE coursetags (
    course_id INT,
    tag_id INT,
    PRIMARY KEY (course_id, tag_id),
    FOREIGN KEY (course_id) REFERENCES Courses(course_id),
    FOREIGN KEY (tag_id) REFERENCES Tags(tag_id)
);

-- Reviews Table
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    course_id INT,
    comment TEXT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    reviewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES Users(user_id),
    FOREIGN KEY (course_id) REFERENCES Courses(course_id)
);



-- Course detail view 

CREATE VIEW course_details AS
SELECT 
    c.course_id,
    c.title,
    c.description,
    c.category,
    c.banner_image,
    c.created_at AS course_created_at,
    c.content,
    c.content_type,
    u.user_id AS teacher_id,
    u.first_name AS teacher_first_name,
    u.last_name AS teacher_last_name,
    u.profile_image AS teacher_profile_image,
    e.student_id,
    u_student.first_name AS student_first_name,
    u_student.last_name AS student_last_name,
    u_student.profile_image AS student_profile_image,
    e.enrollment_date,
    e.completion_status,
    r.review_id,
    r.comment,
    r.rating,
    r.reviewed_at
FROM 
    courses c
JOIN 
    users u ON c.teacher_id = u.user_id
LEFT JOIN 
    enrollments e ON c.course_id = e.course_id
LEFT JOIN 
    users u_student ON e.student_id = u_student.user_id
LEFT JOIN 
    reviews r ON c.course_id = r.course_id AND e.student_id = r.student_id;