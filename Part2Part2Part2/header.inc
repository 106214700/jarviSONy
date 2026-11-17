<?php
/* header.inc | owner: Vinh | HTML head, skip link, site header and navigation; opens <main>.
   Expects: $page_title, $body_class, $active_page (all optional), $meta_description, $extra_head (optional). */
$page_title       = $page_title       ?? 'Pedapigogy Learn';
$body_class       = $body_class       ?? '';
$meta_description = $meta_description ?? 'Pedapigogy Learn: accessible online education. Careers, jobs and applications.';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($page_title); ?> | Pedapigogy Learn</title>
    <meta name="description" content="<?php echo e($meta_description); ?>">
    <meta name="keywords" content="Pedapigogy Learn, EdTech, accessible education, careers, web developer jobs">
    <meta name="author" content="jarviSONy: Bui The Vinh, Nguyen Khoa Nguyen, Pham Tran Quoc Dung, Bui Minh Quan">
    <link rel="stylesheet" href="styles/style.css">
<?php if (!empty($extra_head)) { echo $extra_head, "\n"; } ?>
</head>

<body class="<?php echo e($body_class); ?>">
    <!-- Skip link: first interactive element to improve keyboard accessibility -->
    <a class="skip-link" href="#main">Skip to main content</a>

    <!-- Shared header and navigation (Part 2: one file for every page) -->
    <header class="site-header">
        <div class="container site-header__inner">
            <div class="site-logo">
                <img src="images/logo.svg" alt="" width="48" height="48">
                <span class="site-logo__name">Pedapigogy Learn</span>
            </div>
<?php include __DIR__ . '/nav.inc'; ?>
        </div>
    </header>

    <main id="main">
