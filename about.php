<?php
require_once("settings.php");

$conn = mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create the contributions table if it doesn't exist
$table = "CREATE TABLE IF NOT EXISTS contributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_name VARCHAR(50) NOT NULL,
    project_name VARCHAR(20) NOT NULL,
    contribution TEXT NOT NULL
)";

mysqli_query($conn, $table);

// Seed the table once, only if it's empty
$check = mysqli_query($conn, "SELECT COUNT(*) AS total FROM contributions");
$row = mysqli_fetch_assoc($check);

if ($row['total'] == 0) {

    $seedData = [
        ["Khaled", "Project 1", "Built the Home page and wrote the CSS styling for the Home and Jobs pages."],
        ["Khaled", "Project 2", "Configured the database settings (Task 2), secured the HR Manager login system (Task 6), and created this Contributions table with dynamic data loading on the About page (Task 7)."],
        ["Nahan", "Project 1", "Built the Apply Now page and its CSS styling, and collaborated on the Jobs page HTML."],
        ["Nahan", "Project 2", "Created the Expression of Interest (EOI) database table (Task 3) and implemented validated record processing via process_eoi.php (Task 4)."],
        ["Mohammed Malki", "Project 1", "Built the About Us page and its CSS styling, and collaborated on the Jobs page HTML."],
        ["Mohammed Malki", "Project 2", "Reused common UI elements with PHP includes (Task 1) and built the dynamic Jobs table and jobs.php page (Task 5)."],
    ];

    $insertStmt = mysqli_prepare($conn, "INSERT INTO contributions (member_name, project_name, contribution) VALUES (?, ?, ?)");

    foreach ($seedData as $entry) {
        mysqli_stmt_bind_param($insertStmt, "sss", $entry[0], $entry[1], $entry[2]);
        mysqli_stmt_execute($insertStmt);
    }

    mysqli_stmt_close($insertStmt);
}

// Load all contributions, grouped by member
$contributions = [];

$result = mysqli_query($conn, "SELECT member_name, project_name, contribution FROM contributions ORDER BY member_name, project_name");

while ($row = mysqli_fetch_assoc($result)) {
    $name = $row['member_name'];

    if (!isset($contributions[$name])) {
        $contributions[$name] = [];
    }

    $contributions[$name][$row['project_name']] = $row['contribution'];
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us | Lumina University</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>


    <?php include 'header.inc'; ?>

    

    <main class="content-section">
        <h2>About Lumina University & Our Team</h2>
        
        <p style="margin-bottom: 20px; font-size: 1.1rem; color: #555;">
            Founded with a vision to transform higher education, Lumina University stands at the forefront of academic excellence and digital innovation. The Department of Digital Learning & Research is driven by dedicated student creators and developers who shape the university's online identity.
        </p>

        <hr class="section-divider">

        <section>
            <h3>Project Development Team</h3>
            <ul>
                <li><strong>Official Team:</strong> Lumina Campus Innovators</li>
                <li><strong>Academic Session:</strong>
                    <ul>
                        <li>Course: Web Development Fundamentals</li>
                        <li>Academic Year: 2025 - 2026</li>
                        <li>Semester: Semester 2</li>
                        <li>Class Day: Thursday</li>
                        <li>Lab Time: 4:00 PM - 6:00 PM</li>
                        <li>Instructor: Department of Digital Learning & Research</li>
                    </ul>
                </li>
            </ul>
        </section>

        <hr class="section-divider">

        
        <section>
            <h3>Our Developers & Campus Contributions</h3>
            <dl>
                <dt><strong>Mohammed Malki</strong></dt>
                <dd>Front-End Architect: Structured the core HTML design, integrated the campus branding elements, and optimized layout assets. <em>"Code is the paint, the browser is our canvas."</em></dd>
                <dd>Favorite Language: HTML5 (HyperText Markup Language)</dd>
                
                <dt><strong>Nahan</strong></dt>
                <dd>Backend Logic & Quality Assurance: Ensured code validity, managed file structures, and tested the site for bugs. <em>"Logic is the foundation of innovation."</em></dd>
                <dd>Favorite Language: Python</dd>

                <dt><strong>Khaled</strong></dt>
                <dd>Style & UX Lead: Developed the CSS styles, chose the color palettes, and guaranteed responsive layouts. <em>"Great design is invisible."</em></dd>
                <dd>Favorite Language: CSS3 (Cascading Style Sheets)</dd>
            </dl>
        </section>

        <hr class="section-divider">

        <section>
            <h3>Project Contributions</h3>
            <p style="margin-bottom: 15px; color: #555;">Loaded live from the database — a breakdown of what each member completed across Project 1 and Project 2.</p>

            <table class="contributions-table">
                <thead>
                    <tr>
                        <th>Developer</th>
                        <th>Project 1</th>
                        <th>Project 2</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contributions as $memberName => $projects) : ?>
                        <tr>
                            <td class="contrib-name" data-label="Developer"><?php echo htmlspecialchars($memberName); ?></td>
                            <td data-label="Project 1"><?php echo htmlspecialchars($projects['Project 1'] ?? '—'); ?></td>
                            <td data-label="Project 2"><?php echo htmlspecialchars($projects['Project 2'] ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <hr class="section-divider">

        <section>
            <h3>The Lumina Lab Creators</h3>
            <figure style="text-align: center; margin: 20px 0;">
                <img src="images/team.jpeg" alt="Lumina University Web Development Team" class="responsive-img" style="width: 100%; max-width: 600px; height: auto; border-radius: 8px; display: block; margin: 0 auto;">
                <figcaption style="font-style: italic; color: #666; margin-top: 8px;">Mohammed, Nahan, and Khaled collaborating inside the Lumina University Digital Innovation Library.</figcaption>
            </figure>
        </section>

        <hr class="section-divider">

        <section>
            <h3>Team Profiles & Campus Fun Facts</h3>
            <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse; margin-top: 15px; border: 1px solid #ddd;">
                <caption style="font-style: italic; margin-bottom: 10px; color: #081935; font-weight: bold;">A quick look at the student minds behind the Lumina platform.</caption>
                <thead>
                    <tr style="background-color: #081935; color: white;">
                        <th>Developer</th>
                        <th>Campus Target Career</th>
                        <th>Coding Snack</th>
                        <th>Hometown Campus</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Mohammed</td>
                        <td>Full-Stack Engineer</td>
                        <td>Chicken, Eggs & Laban</td>
                        <td>Melbourne Campus</td>
                    </tr>
                    <tr>
                        <td>Nahan</td>
                        <td>Cybersecurity Analyst</td>
                        <td>Coffee & Biscuits</td>
                        <td>Doha Campus</td>
                    </tr>
                    <tr>
                        <td>Khaled</td>
                        <td>Cloud Architect</td>
                        <td>Karak Tea</td>
                        <td>Al Wakrah Campus</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>

  <?php include 'footer.inc'; ?>

</body>
</html>