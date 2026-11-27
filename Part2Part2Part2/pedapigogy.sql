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
-- Column names = the name="" attributes in apply.php (contract A5).
-- All seed data below is fictional (made-up names, example.com emails, invented phones).

DROP TABLE IF EXISTS eoi;
CREATE TABLE eoi (
  EOInumber INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  job_ref VARCHAR(5) NOT NULL,
  first_name VARCHAR(20) NOT NULL, last_name VARCHAR(20) NOT NULL,
  dob DATE NOT NULL,
  gender ENUM('female','male','non-binary','not-stated') NOT NULL,
  street VARCHAR(255) NOT NULL, suburb VARCHAR(255) NOT NULL,
  state ENUM('VIC','NSW','QLD','NT','WA','SA','TAS','ACT') NOT NULL,
  postcode CHAR(4) NOT NULL,
  email VARCHAR(320) NOT NULL, phone VARCHAR(12) NOT NULL,
  skill_html TINYINT(1) NOT NULL DEFAULT 0, skill_css TINYINT(1) NOT NULL DEFAULT 0,
  skill_accessibility TINYINT(1) NOT NULL DEFAULT 0, skill_responsive TINYINT(1) NOT NULL DEFAULT 0,
  skill_git TINYINT(1) NOT NULL DEFAULT 0, skill_wireframing TINYINT(1) NOT NULL DEFAULT 0,
  skill_research TINYINT(1) NOT NULL DEFAULT 0, skill_writing TINYINT(1) NOT NULL DEFAULT 0,
  other_skills TEXT NULL,
  status ENUM('New','Current','Final') NOT NULL DEFAULT 'New',
  submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5 seed rows so Quan can test manage.php (list all, by job, by name, delete by job, change status, sort).
INSERT INTO eoi
  (job_ref, first_name, last_name, dob, gender, street, suburb, state, postcode, email, phone,
   skill_html, skill_css, skill_accessibility, skill_responsive, skill_git, skill_wireframing, skill_research, skill_writing,
   other_skills, status)
VALUES
  -- 1: normal case, several skills ticked
  ('LL101', 'Mai',  'Tran',   '2001-04-12', 'female',     '12 Example Street', 'Hawthorn',   'VIC', '3122', 'mai.tran@example.com',   '0400000001',
   1, 1, 1, 1, 0, 0, 0, 0, 'Built a small accessible quiz site for a uni club.', 'New'),
  -- 2: same job as row 1 -> tests "list by job" and "delete by job"
  ('LL101', 'An',   'Le',     '1998-09-30', 'male',       '5 Sample Road',     'Parramatta', 'NSW', '2150', 'an.le@example.com',      '0400000002',
   1, 1, 0, 1, 1, 0, 0, 0, 'Git and responsive layouts.', 'Current'),
  -- 3: same first name as row 1 -> tests name search
  ('LL102', 'Mai',  'Pham',   '1995-01-20', 'female',     '88 Demo Avenue',    'Carlton',    'VIC', '3053', 'mai.pham@example.com',   '0400000003',
   0, 0, 1, 0, 0, 1, 1, 1, 'User research interviews and plain-language writing.', 'Final'),
  -- 4: no other skills (NULL)
  ('LL103', 'Linh', 'Do',     '2003-07-07', 'non-binary', '3 Test Lane',       'Adelaide',   'SA',  '5000', 'linh.do@example.com',    '0400000004',
   1, 0, 0, 0, 0, 0, 1, 0, NULL, 'New'),
  -- 5: different state and a long other_skills text
  ('LL104', 'Bao',  'Nguyen', '1990-11-02', 'not-stated', '41 Placeholder Pde', 'Brisbane',  'QLD', '4000', 'bao.nguyen@example.com', '0400000005',
   1, 1, 1, 1, 1, 1, 1, 1,
   'Five years running accessibility audits for learning platforms, mentoring junior developers, writing style guides, running usability tests with screen reader users, and presenting findings to non-technical stakeholders.',
   'Current');


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
