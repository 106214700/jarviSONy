-- ==========================================================================
-- pedapigogy.sql | COS10026 Applied Web Project Part 2 | team jarviSONy
-- Import this file in phpMyAdmin (Import tab) or:  mysql -u root < pedapigogy.sql
-- It creates the database and ALL tables, then inserts the seed data.
-- Rules: edit only your own marked block. Blank lines between blocks stop merge conflicts.
-- ==========================================================================

CREATE DATABASE IF NOT EXISTS pedapigogy_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pedapigogy_db;
SET NAMES utf8mb4;


-- ===== jobs (Nguyen) =====
-- TODO(Nguyen): DROP TABLE IF EXISTS jobs; CREATE TABLE jobs (...); INSERT the four jobs LL101 to LL104.
-- Columns are in guide Part A5.


-- ===== eoi (Dung) =====
-- TODO(Dung): DROP TABLE IF EXISTS eoi; CREATE TABLE eoi (...); INSERT 5 fictional seed rows.
-- Columns are in guide Part A5.


-- ===== users (Vinh) =====
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(30) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  failed_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
  locked_until DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Marker account required by the brief: username admin, password admin.
-- The value below is a bcrypt HASH made with PHP password_hash(); the real password is not stored.
INSERT INTO users (username, password_hash) VALUES
 ('admin', '$2y$10$hSzgmQ8B0Mf/vBg4dAIpReenfVDMk3d8PkbjxuXX1yKaHwQPP9IKi');


-- ===== about (Quan) =====
-- TODO(Quan): DROP TABLE IF EXISTS about; CREATE TABLE about (...); INSERT the four members.
-- Columns are in guide Part A5.
