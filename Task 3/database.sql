-- Database: internship3
-- Run these statements in MySQL to create the database and table.

CREATE DATABASE IF NOT EXISTS internship3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE internship3;

-- Posts table
CREATE TABLE IF NOT EXISTS posts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FULLTEXT KEY ft_title_content (title, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


