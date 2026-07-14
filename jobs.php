<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Job Opportunities | Lumina University</title>
<link rel="stylesheet" href="styles/styles.css">
</head>

<body>

<?php include 'header.inc'; ?>


<main>

<section id="hero-section">

<h2>Current Job Opportunities</h2>

<p>Join our growing Department of Digital Learning & Research.</p>

</section>

<aside>

<h3>Why Join Lumina?</h3>

<ul>

<li>Flexible work arrangements</li>

<li>Professional development</li>

<li>Friendly team environment</li>

<li>Competitive salary</li>

<li>Modern campus facilities</li>

</ul>

</aside>

<?php
require_once("settings.php");

$conn = mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$result = mysqli_query($conn, "SELECT * FROM jobs ORDER BY id ASC");

if ($result && mysqli_num_rows($result) > 0):
    while ($job = mysqli_fetch_assoc($result)):
        $responsibilities = explode('|', $job['key_responsibilities']);
        $essential        = explode('|', $job['essential_requirements']);
        $preferable       = explode('|', $job['preferable_requirements']);
?>
<section class="job-card">
<h2><?php echo htmlspecialchars($job['title']); ?></h2>
<p><strong>Reference:</strong> <?php echo htmlspecialchars($job['reference_code']); ?></p>
<p><strong>Salary:</strong> $<?php echo number_format($job['salary_min']); ?> - $<?php echo number_format($job['salary_max']); ?></p>
<p><strong>Reporting To:</strong> <?php echo htmlspecialchars($job['reporting_to']); ?></p>
<h3>Position Description</h3>
<p>
<?php echo htmlspecialchars($job['position_description']); ?>
</p>
<h3>Key Responsibilities</h3>
<ul>
<?php foreach ($responsibilities as $item): ?>
<li><?php echo htmlspecialchars($item); ?></li>
<?php endforeach; ?>
</ul>
<h3>Essential Requirements</h3>
<ol>
<?php foreach ($essential as $item): ?>
<li><?php echo htmlspecialchars($item); ?></li>
<?php endforeach; ?>
</ol>
<h3>Preferable Requirements</h3>
<ul>
<?php foreach ($preferable as $item): ?>
<li><?php echo htmlspecialchars($item); ?></li>
<?php endforeach; ?>
</ul>
</section>
<?php
    endwhile;
else:
    echo '<p>No job opportunities are currently available.</p>';
endif;

mysqli_close($conn);
?>

</main>

<?php include 'footer.inc'; ?>

</body>
</html>