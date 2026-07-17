<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Opportunities | Lumina University</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>

<body>
    <?php require_once("header.inc"); ?>
    <?php require_once("nav.inc"); ?>

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

        // 1. Automatically create the 'jobs' table if it doesn't exist
        $create_table_sql = "CREATE TABLE IF NOT EXISTS jobs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            reference_code VARCHAR(10) NOT NULL UNIQUE,
            title VARCHAR(100) NOT NULL,
            salary_min INT NOT NULL,
            salary_max INT NOT NULL,
            reporting_to VARCHAR(100) NOT NULL,
            position_description TEXT NOT NULL,
            key_responsibilities TEXT NOT NULL,
            essential_requirements TEXT NOT NULL,
            preferable_requirements TEXT NOT NULL
        ) ENGINE=InnoDB;";

        mysqli_query($conn, $create_table_sql);

        // 2. Seed the table with default data if it is empty
        $check_rows = mysqli_query($conn, "SELECT COUNT(*) AS total FROM jobs");
        $row_count = mysqli_fetch_assoc($check_rows)['total'];

        if ($row_count == 0) {
            $seed_sql = "INSERT INTO jobs (reference_code, title, salary_min, salary_max, reporting_to, position_description, key_responsibilities, essential_requirements, preferable_requirements) VALUES
            ('DL101', 'Senior Learning Designer', 85000, 110000, 'Head of Digital Learning', 
             'Lead the design and development of innovative digital courseware across undergraduate and postgraduate units.', 
             'Collaborate with academic staff to design courses|Develop engaging online learning materials|Evaluate learning tool effectiveness', 
             'Degree in Education or related field|3+ years experience in instructional design|Proficiency with LMS platforms like Canvas or Moodle', 
             'Master degree in Educational Technology|Experience with H5P and SCORM compliance'),
            
            ('DL102', 'Educational Technologist', 70000, 90000, 'Senior Learning Designer', 
             'Provide technical support and training for learning technologies across campus.', 
             'Train faculty on digital teaching tools|Troubleshoot learning platform issues|Develop user guides and video tutorials', 
             'Bachelor degree in IT or related discipline|Strong communication skills|Experience supporting LMS systems', 
             'Knowledge of HTML/CSS/JavaScript|Experience in a higher education environment')";

            mysqli_query($conn, $seed_sql);
        }

        // 3. Fetch and render all jobs
        $result = mysqli_query($conn, "SELECT * FROM jobs ORDER BY id ASC");

        if ($result && mysqli_num_rows($result) > 0):
            while ($job = mysqli_fetch_assoc($result)):
                // Filter out any blank items caused by trailing pipes '|'
                $responsibilities = array_filter(explode('|', $job['key_responsibilities']));
                $essential        = array_filter(explode('|', $job['essential_requirements']));
                $preferable       = array_filter(explode('|', $job['preferable_requirements']));
        ?>
                <section class="job-card">
                    <h2><?php echo htmlspecialchars($job['title']); ?></h2>
                    <p><strong>Reference:</strong> <?php echo htmlspecialchars($job['reference_code']); ?></p>
                    <p><strong>Salary:</strong> $<?php echo number_format($job['salary_min']); ?> - $<?php echo number_format($job['salary_max']); ?></p>
                    <p><strong>Reporting To:</strong> <?php echo htmlspecialchars($job['reporting_to']); ?></p>

                    <h3>Position Description</h3>
                    <p><?php echo htmlspecialchars($job['position_description']); ?></p>

                    <h3>Key Responsibilities</h3>
                    <ul>
                        <?php foreach ($responsibilities as $item): ?>
                            <li><?php echo htmlspecialchars(trim($item)); ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <h3>Essential Requirements</h3>
                    <ol>
                        <?php foreach ($essential as $item): ?>
                            <li><?php echo htmlspecialchars(trim($item)); ?></li>
                        <?php endforeach; ?>
                    </ol>

                    <h3>Preferable Requirements</h3>
                    <ul>
                        <?php foreach ($preferable as $item): ?>
                            <li><?php echo htmlspecialchars(trim($item)); ?></li>
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

    <?php require_once("footer.inc"); ?>

</body>

</html>