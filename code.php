-- Create the database
CREATE DATABASE IF NOT EXISTS student_networking_hub;

-- Use the database
USE student_networking_hub;

-- Create the users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,       -- Unique user ID
    username VARCHAR(100) NOT NULL,          -- User's name
    email VARCHAR(150) NOT NULL UNIQUE,      -- User's email
    password VARCHAR(255) NOT NULL,          -- Encrypted password
    age INT NOT NULL,                        -- User's age
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Registration time
    location VARCHAR(255) DEFAULT NULL,      -- User's location
    phone VARCHAR(15) DEFAULT NULL,          -- Phone number
    university VARCHAR(255) DEFAULT NULL,    -- User's university name
    education_duration VARCHAR(50) DEFAULT NULL, -- Education duration
    skills TEXT DEFAULT NULL,                -- Skills as comma-separated values
    profile_photo VARCHAR(255) DEFAULT NULL  -- Path to profile photo
);


CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    profile_picture VARCHAR(255),
    content TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

<!-- post table  -->
ALTER TABLE posts ADD likes INT DEFAULT 0;
ALTER TABLE posts ADD email VARCHAR(150) NOT NULL;

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT,
    username VARCHAR(50),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);


