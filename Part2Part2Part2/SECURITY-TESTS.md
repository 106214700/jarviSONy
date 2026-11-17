# Security and accessibility test log (Part 2)

Coordinator: Nguyen. Each member adds rows for their own pages. Result = Pass / Fail (with the fix).

| Test | Page | Input / action | Expected | Result | Tester | Date |
|------|------|----------------|----------|--------|--------|------|
| Not logged in | manage.php | open URL in a private window | redirect to login.php | | Vinh | |
| Wrong password x3 | login.php | admin + wrong password, 3 times | generic message; 4th attempt blocked for 5 min | | Vinh | |
| Session after logout | manage.php | log out, press Back | redirect to login.php | | Vinh | |
| CSRF | login.php | post without csrf_token | 403 message | | Vinh | |
| XSS in username | login.php | `<script>alert(1)</script>` | no alert, generic message | | Vinh | |
| SQL injection | login.php | `admin' OR '1'='1` | rejected, generic message | | Vinh | |
| Helper files | functions.php, auth.php, settings.php | open URL directly | redirect or blank page, no source | | Vinh | |
| Direct access | process_eoi.php | open URL directly | redirect to apply.php | | Dung | |
| SQL injection / XSS | search.php | see Nguyen guide B8 | no injection, text escaped | | Nguyen | |
| Sort tampering | manage.php | ?sort=1;DROP TABLE eoi | default sort, no error | | Quan | |
