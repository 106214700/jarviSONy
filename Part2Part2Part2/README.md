Pedapigogy Learn | COS10026 Applied Web Project Part 2
Team: jarviSONy (Vinh, Nguyen, Dung, Quan)

Dynamic PHP + MySQL site. No JavaScript.

HOW TO RUN (XAMPP)
  1. Copy this folder to C:\xampp\htdocs\project2 (the folder must be called project2).
  2. Start Apache and MySQL in the XAMPP control panel.
  3. Open http://localhost/phpmyadmin, click Import, choose pedapigogy.sql, click Import.
     (This creates the database pedapigogy_db and every table.)
  4. Open http://localhost/project2/index.php
  Do not move or rename any file.

MANAGER LOGIN (for the marker)
  Page: http://localhost/project2/login.php  (footer link "Manager login")
  Username: admin    Password: admin
  Three wrong passwords lock the account for 5 minutes.

FILES
  index.php  jobs.php  search.php  apply.php  process_eoi.php  about.php
  login.php  logout.php  manage.php
  header.inc  nav.inc  footer.inc          shared HTML (header, navigation, footer)
  settings.php                             database settings (host, user, password, database)
  functions.php  auth.php  job_functions.php   PHP helpers (not pages)
  pedapigogy.sql                           database and seed data
  styles/style.css                         one stylesheet (images for CSS go in styles/images/)
  images/                                  page images
  AI-LOG.md  SECURITY-TESTS.md             AI use and security test records

OWNERS
  Vinh   index, includes, settings, functions, auth, login, logout, SQL users block, integration
  Nguyen jobs, search, job_functions, SQL jobs block, responsive CSS (Section 11), security test log
  Dung   apply, process_eoi, SQL eoi block
  Quan   manage, about, SQL about block

LINKS
  Jira: https://cos10026appliedwebproject.atlassian.net/jira/software/projects/SCRUM/boards/1
  GitHub: https://github.com/106214700/jarviSONy

(c) 2026 Pedapigogy Learn. Fictional student project.
