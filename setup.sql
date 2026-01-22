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

--MEMBER password: 123456

INSERT INTO users 
(name, email, phone, password, sex, DOB, district, user_type, security_q, security_a)
VALUES 
(
    'Mahfuz Ahamed',
    'mahfuz@gmail.com',
    '01711374792',
    '$2y$10$PciWWkUZsCHG04P1K7sSB.r2.t1g44DkowD7Yv1UyB1dZQs/Ne1n.',
    'Male',
    '2001-08-12',
    'Dhaka',
    'admin',
    'What was the name of your first pet?',
    'tom'
);

INSERT INTO users 
(name, email, phone, password, sex, DOB, district, security_q, security_a)
VALUES 
(
    'Nadman Alvee',
    'nadmanalvee@gmail.com',
    '01763914143',
    '$2y$10$PciWWkUZsCHG04P1K7sSB.r2.t1g44DkowD7Yv1UyB1dZQs/Ne1n.',
    'Male',
    '2004-07-31',
    'Dhaka',
    'In what city were you born?',
    'Dhaka'
);

INSERT INTO users (name, email, phone, password, sex, DOB, district, user_type, security_q, security_a) VALUES
('Amina Khan', 'amina@example.com', '01711111111', '$2y$10$PciWWkUZsCHG04P1K7sSB.r2.t1g44DkowD7Yv1UyB1dZQs/Ne1n.', 'Female', '1995-03-15', 'Dhaka', 'member', 'Your high school?', 'Viqarunnisa'),
('Samiul Islam', 'samiul@example.com', '01822222222', '$2y$10$PciWWkUZsCHG04P1K7sSB.r2.t1g44DkowD7Yv1UyB1dZQs/Ne1n.', 'Male', '1992-11-20', 'Chittagong', 'member', 'First pet?', 'Sheru'),
('Fatima Zahra', 'fatima@example.com', '01933333333', '$2y$10$PciWWkUZsCHG04P1K7sSB.r2.t1g44DkowD7Yv1UyB1dZQs/Ne1n.', 'Female', '1998-05-10', 'Sylhet', 'member', 'Favorite color?', 'Blue'),
('Tanvir Ahmed', 'tanvir@example.com', '01544444444', '$2y$10$PciWWkUZsCHG04P1K7sSB.r2.t1g44DkowD7Yv1UyB1dZQs/Ne1n.', 'Male', '1990-01-01', 'Rajshahi', 'member', 'Birth city?', 'Rajshahi'),
('Nusrat Jahan', 'nusrat@example.com', '01655555555', '$2y$10$PciWWkUZsCHG04P1K7sSB.r2.t1g44DkowD7Yv1UyB1dZQs/Ne1n.', 'Female', '2000-12-25', 'Khulna', 'member', 'Mother\'s name?', 'Mariam'),
('Arifur Rahman', 'arif@example.com', '01366666666', '$2y$10$PciWWkUZsCHG04P1K7sSB.r2.t1g44DkowD7Yv1UyB1dZQs/Ne1n.', 'Male', '1988-07-04', 'Barisal', 'member', 'Favorite food?', 'Biryani'),
('Sadia Afrin', 'sadia@example.com', '01477777777', '$2y$10$PciWWkUZsCHG04P1K7sSB.r2.t1g44DkowD7Yv1UyB1dZQs/Ne1n.', 'Female', '1996-09-12', 'Rangpur', 'member', 'First car?', 'Toyota'),
('Rakibul Hasan', 'rakib@example.com', '01788888888', '$2y$10$PciWWkUZsCHG04P1K7sSB.r2.t1g44DkowD7Yv1UyB1dZQs/Ne1n.', 'Male', '1994-02-28', 'Mymensingh', 'member', 'Best friend?', 'Kamal'),
('Mehedi Hasan', 'mehedi@example.com', '01899999999', '$2y$10$PciWWkUZsCHG04P1K7sSB.r2.t1g44DkowD7Yv1UyB1dZQs/Ne1n.', 'Male', '1991-06-18', 'Comilla', 'member', 'Favorite book?', 'Hamlet'),
('Tahmina Akter', 'tahmina@example.com', '01900000000', '$2y$10$PciWWkUZsCHG04P1K7sSB.r2.t1g44DkowD7Yv1UyB1dZQs/Ne1n.', 'Female', '1999-10-30', 'Dhaka', 'member', 'Favorite movie?', 'Inception');


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
(user_id, text, division, city, upvote, downvote, report_count)
VALUES 
(
    1, 
    'This is my first post!', 
    'Dhaka', 
    'Gulshan', 
    10, 
    2,
    0
);



CREATE TABLE IF NOT EXISTS areas (
    area_id INT AUTO_INCREMENT PRIMARY KEY,
    division VARCHAR(100) NOT NULL,
    city VARCHAR(100) DEFAULT NULL
);


INSERT INTO areas (division, city) VALUES
('Dhaka', 'Gulshan'),
('Dhaka', 'Dhanmondi'),
('Chittagong', 'Pahartali'),
('Khulna', 'Sonadanga'),
('Rajshahi', 'Boalia');


-- seed posts
DELIMITER $$

CREATE PROCEDURE PopulatePostsSafe()
BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE random_user_id INT;
    DECLARE div_name VARCHAR(100);
    DECLARE city_name VARCHAR(100);
    
    WHILE i <= 100 DO
        -- Dynamically pick a random user_id that actually exists in the table
        SELECT user_id INTO random_user_id FROM users ORDER BY RAND() LIMIT 1;
        
        -- Cycle through area logic
        CASE (i % 5)
            WHEN 0 THEN SELECT 'Dhaka', 'Gulshan' INTO div_name, city_name;
            WHEN 1 THEN SELECT 'Dhaka', 'Dhanmondi' INTO div_name, city_name;
            WHEN 2 THEN SELECT 'Chittagong', 'Pahartali' INTO div_name, city_name;
            WHEN 3 THEN SELECT 'Khulna', 'Sonadanga' INTO div_name, city_name;
            WHEN 4 THEN SELECT 'Rajshahi', 'Boalia' INTO div_name, city_name;
        END CASE;

        INSERT INTO posts (user_id, text, division, city, upvote, downvote, report_count)
        VALUES (
            random_user_id, 
            CONCAT('Community update #', i, ': Checking in from ', city_name, '. Everything looks good!'), 
            div_name, 
            city_name, 
            FLOOR(RAND() * 100), 
            FLOOR(RAND() * 20), 
            FLOOR(RAND() * 5)
        );
        SET i = i + 1;
    END WHILE;
END$$

DELIMITER ;

-- Run it
CALL PopulatePostsSafe();
DROP PROCEDURE PopulatePosts;
