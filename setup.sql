-- SQL script to set up the citystatus database and users table
--CREATE DATABASE IF NOT EXISTS citystatus;
--USE citystatus;

--CREATE TABLE IF NOT EXISTS users (
    --id INT AUTO_INCREMENT PRIMARY KEY,
    --name VARCHAR(100) NOT NULL,
    -- VARCHAR(100) NOT NULL,
    -- VARCHAR(20),
    --created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
--);

--INSERT INTO users (name, email, phone) VALUES
--('Alice Smith', 'alice@example.com', '123-456-7890'),
--('Bob Johnson', 'bob@example.com', '234-567-8901'),
--('Charlie Lee', 'charlie@example.com', '345-678-9012');



--ours





--part1
CREATE DATABASE IF NOT EXISTS citystatus_db;


--part 2
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE 
        CHECK (email LIKE '%_@_%._%'),
    phone CHAR(11) NOT NULL UNIQUE 
        CHECK (phone REGEXP '^[0-9]{11}$'),
    password CHAR(6) NOT NULL 
        CHECK (CHAR_LENGTH(password) = 6),
    sex ENUM('Male', 'Female', 'Other') NOT NULL,
    DOB DATE NOT NULL,
    district VARCHAR(100) NOT NULL,
    acc_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_type ENUM('admin', 'member', 'guest') DEFAULT 'member'
);


--part 3
ALTER TABLE users
ADD COLUMN security_q VARCHAR(100),
ADD COLUMN security_a VARCHAR(100);

--part 4 ADMIN
INSERT INTO users 
(name, email, phone, password, sex, DOB, district, user_type, security_q, security_a)
VALUES 
(
    'John',
    'john@gmail.com',
    '01333333333',
    '123456',
    'Male',
    '2001-08-12',
    'Dhaka',
    'admin',
    'What was the name of your first pet?',
    'tom'
);


--MEMBER
INSERT INTO users 
(name, email, phone, password, sex, DOB, district, security_q, security_a)
VALUES 
(
    'Doe',
    'doe@gmail.com',
    '01333333334',
    '123456',
    'Male',
    '2001-08-12',
    'Dhaka',
    'In what city were you born?',
    'Dhaka'
);

