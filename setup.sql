CREATE DATABASE IF NOT EXISTS citystatus_db;

-- Use the database citystatus_db

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE 
        CHECK (email LIKE '%_@_%._%'),
    phone CHAR(11) NOT NULL UNIQUE 
        CHECK (phone REGEXP '^[0-9]{11}$'),
    password VARCHAR(255) NOT NULL,
    sex ENUM('Male', 'Female', 'Other') NOT NULL,
    DOB DATE NOT NULL,
    district VARCHAR(100) NOT NULL,
    acc_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_type ENUM('admin', 'member', 'guest') DEFAULT 'member'
);

ALTER TABLE users
ADD COLUMN security_q VARCHAR(100),
ADD COLUMN security_a VARCHAR(100);

INSERT INTO users 
(name, email, phone, password, sex, DOB, district, user_type, security_q, security_a)
VALUES 
(
    'John',
    'john@gmail.com',
    '01333333333',
    '$2y$10$PciWWkUZsCHG04P1K7sSB.r2.t1g44DkowD7Yv1UyB1dZQs/Ne1n.',
    'Male',
    '2001-08-12',
    'Dhaka',
    'admin',
    'What was the name of your first pet?',
    'tom'
);


--MEMBER password: 123456
INSERT INTO users 
(name, email, phone, password, sex, DOB, district, security_q, security_a)
VALUES 
(
    'Doe',
    'doe@gmail.com',
    '01333333334',
    '$2y$10$PciWWkUZsCHG04P1K7sSB.r2.t1g44DkowD7Yv1UyB1dZQs/Ne1n.',
    'Male',
    '2001-08-12',
    'Dhaka',
    'In what city were you born?',
    'Dhaka'
);


CREATE TABLE posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    text TEXT NOT NULL,
    division VARCHAR(100) NOT NULL,
    city VARCHAR(100),
    upvote INT DEFAULT 0 CHECK (upvote >= 0),
    downvote INT DEFAULT 0 CHECK (downvote >= 0),
    report_count INT DEFAULT 0 CHECK (report_count >= 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);



INSERT INTO posts 
(user_id, text, image_link, post_status, division, city, upvote, downvote)
VALUES 
(
    1, 
    'This is my first post!', 
    'active', 
    'Dhaka', 
    'Gulshan', 
    10, 
    2,
    0
);

