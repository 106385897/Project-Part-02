<?php
// Figure out which page we're currently on, e.g. "jobs.php"
$current_page = basename($_SERVER['PHP_SELF']);

// Map each nav link to its label
$nav_links = [
    'index.php' => 'Home',
    'jobs.php'  => 'Job Opportunities',
    'apply.php' => 'Apply Now',
    'about.php' => 'About Our Team',
    'login.php' => 'Login',
];
?>
<nav aria-label="Main Navigation">
    <ul class="nav-menu">
        <?php foreach ($nav_links as $href => $label) : ?>
            <?php $is_active = ($current_page === $href); ?>
            <li>
                <a href="<?php echo $href; ?>"
                   <?php if ($is_active) : ?>class="active" aria-current="page"<?php endif; ?>>
                    <?php echo $label; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
