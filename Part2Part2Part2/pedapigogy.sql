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
DROP TABLE IF EXISTS jobs;
CREATE TABLE jobs (
  job_ref          CHAR(5)       NOT NULL,
  title            VARCHAR(100)  NOT NULL,
  summary          TEXT          NOT NULL,
  salary           VARCHAR(100)  NOT NULL,
  reports_to       VARCHAR(100)  NOT NULL,
  responsibilities TEXT          NOT NULL,
  essential_reqs   TEXT          NOT NULL,
  preferable_reqs  TEXT          NOT NULL,
  PRIMARY KEY (job_ref)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO jobs (job_ref, title, summary, salary, reports_to, responsibilities, essential_reqs, preferable_reqs) VALUES
(
  'LL101',
  'Front-End Developer, Accessible Learning Tools',
  'Build the pages that students and teachers use every day, from lesson viewers to quiz screens, with accessibility checked at every step.',
  'AUD $78,000 to $92,000 per year plus superannuation',
  'Head of Product Engineering',
  'Build and maintain accessible lesson, quiz and dashboard pages in HTML5 and CSS3.\nTurn design files into layouts that work on phones, tablets and desktops.\nTest every page with a keyboard and a screen reader before release.\nReview teammates'' code and fix issues found in accessibility audits.\nDocument reusable components so other teams can use them.',
  'HTML5 and semantic markup: write correct landmarks, headings, lists and forms.\nCSS3 layout (Flexbox and Grid): build page layouts without frameworks.\nAccessibility (WCAG 2.2): know how to meet WCAG 2.2 level AA.\nResponsive design: design layouts that adapt to any screen size.',
  'Git and GitHub: use branches and pull requests in a team.\nClear written communication: explain technical choices to non-technical colleagues.'
),
(
  'LL102',
  'UX and Accessibility Designer',
  'Design clear, welcoming online learning experiences and make sure every design works for people with different abilities and devices.',
  'AUD $74,000 to $88,000 per year plus superannuation',
  'Head of Design',
  'Design wireframes and prototypes for courses, assessments and progress screens.\nRun usability tests with learners, including people who use assistive technology.\nWrite plain-language interface text and instructions.\nCheck colour contrast, focus order and reading order in every design.\nWork with developers so the finished product matches the design.',
  'Accessibility (WCAG 2.2): apply WCAG 2.2 level AA to designs.\nWireframing and prototyping: turn ideas into wireframes and clickable prototypes.\nUser research and usability testing: plan sessions, observe learners and report findings.\nClear written communication: write short, clear instructions.',
  'HTML5 and semantic markup: understand how a design becomes a web page.\nCSS3 layout (Flexbox and Grid): know what is easy and hard to build.'
),
(
  'LL103',
  'Personal English Language Tutor',
  'Support learners in building confidence, fluency and academic English skills through personalised, encouraging one-to-one sessions.',
  'AUD $82,000 to $98,000 per year plus superannuation',
  'Head of Academic Support',
  'Plan and deliver personalised English lessons for students with different levels, goals and learning styles.\nTeach speaking, listening, reading and writing in a clear and encouraging way.\nTrack learner progress, set achievable goals and provide constructive feedback.\nCreate engaging lesson materials, activities and homework to reinforce learning.\nSupport students in preparing for interviews, assessments, everyday conversation and academic communication.',
  'IELTS overall score of 6.5 or equivalent English proficiency qualification.\nStrong spoken and written English communication skills.\nAbility to design tailored lesson plans for individual learners.\nPatience, empathy and the ability to motivate students of different ages and backgrounds.\nExperience supporting learners with grammar, vocabulary, pronunciation or conversation skills.',
  'TESOL, TEFL, ESL or equivalent teaching qualification.\nExperience teaching online or in a one-to-one tutoring environment.'
),
(
  'LL104',
  'Mathematics Teacher (Primary to Lower Secondary)',
  'Guide students from primary through lower secondary in building strong mathematical foundations, problem-solving confidence and consistent academic progress.',
  'AUD $86,000 to $102,000 per year plus superannuation',
  'Head of Curriculum and Learning',
  'Teach mathematics to students across primary and lower secondary levels using clear explanations and structured lesson plans.\nBuild confidence in arithmetic, algebra, geometry, problem-solving and mathematical reasoning.\nPrepare lessons, assessments and revision activities tailored to different learning levels.\nMonitor learner progress, identify gaps in understanding and provide actionable support.\nWork closely with families and academic staff to maintain consistent student development.',
  'Strong knowledge of mathematics curriculum from primary to lower secondary levels.\nAbility to explain concepts clearly and adapt teaching methods for different learning needs.\nExperience teaching or tutoring students in foundational and intermediate mathematics.\nExcellent classroom communication, organisation and student engagement skills.',
  'Teaching qualification or relevant education training.\nExperience teaching in online, hybrid or small-group learning environments.'
);


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
