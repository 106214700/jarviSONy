            <nav class="site-nav" aria-label="Main navigation">
                <ul class="site-nav__list">
                    <li><a href="index.php"<?php echo nav_current('home'); ?>>Home</a></li>
                    <li><a href="jobs.php"<?php echo nav_current('jobs'); ?>>Jobs</a></li>
                    <li><a href="apply.php"<?php echo nav_current('apply'); ?>>Apply</a></li>
                    <li><a href="about.php"<?php echo nav_current('about'); ?>>About</a></li>
<?php if (is_logged_in()) { ?>
                    <li><a href="manage.php"<?php echo nav_current('manage'); ?>>Manage</a></li>
                    <li><a href="logout.php">Logout</a></li>
<?php } ?>
                </ul>
            </nav>
