<?php
require_once("settings.php");

// Prevent direct access
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: apply.php");
    exit();
}

// Connect to database
$conn = mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create table if it doesn't exist
$table = "CREATE TABLE IF NOT EXISTS eoi (
    EOInumber INT AUTO_INCREMENT PRIMARY KEY,
    jobref VARCHAR(10) NOT NULL,
    fname VARCHAR(20) NOT NULL,
    lname VARCHAR(20) NOT NULL,
    dob DATE NOT NULL,
    gender VARCHAR(10) NOT NULL,
    address VARCHAR(40) NOT NULL,
    state VARCHAR(5) NOT NULL,
    postcode CHAR(4) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(12) NOT NULL,
    skills TEXT,
    otherskills TEXT,
    resume VARCHAR(255),
    status ENUM('New','Current','Final') DEFAULT 'New'
)";

mysqli_query($conn, $table);

// Function to sanitise data
function sanitise($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

// Read form data
$jobref = sanitise($_POST['jobref'] ?? '');
$fname = sanitise($_POST['fname'] ?? '');
$lname = sanitise($_POST['lname'] ?? '');
$dob = sanitise($_POST['dob'] ?? '');
$gender = sanitise($_POST['gender'] ?? '');
$address = sanitise($_POST['address'] ?? '');
$state = sanitise($_POST['state'] ?? '');
$postcode = sanitise($_POST['postcode'] ?? '');
$email = sanitise($_POST['email'] ?? '');
$phone = sanitise($_POST['phone'] ?? '');
$otherskills = sanitise($_POST['otherskills'] ?? '');

$skills = "";
if (isset($_POST['skills'])) {
    $skills = implode(", ", $_POST['skills']);
}

// Validation
$errors = [];

if ($jobref == "")
    $errors[] = "Please select a job.";

if (!preg_match("/^[A-Za-z]{1,20}$/", $fname))
    $errors[] = "Invalid first name.";

if (!preg_match("/^[A-Za-z]{1,20}$/", $lname))
    $errors[] = "Invalid last name.";

if ($dob == "")
    $errors[] = "Please enter your date of birth.";

if ($gender == "")
    $errors[] = "Please select your gender.";

if ($address == "")
    $errors[] = "Please enter your address.";

if ($state == "")
    $errors[] = "Please select your state.";

if (!preg_match("/^[0-9]{4}$/", $postcode))
    $errors[] = "Postcode must contain exactly 4 digits.";

if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = "Invalid email address.";

if (!preg_match("/^[0-9]{8,12}$/", $phone))
    $errors[] = "Phone number must contain 8-12 digits.";

if (!isset($_POST['declaration']))
    $errors[] = "You must accept the declaration.";

// Resume Upload
$resumeName = "";

if (isset($_FILES["resume"]) && $_FILES["resume"]["error"] == 0) {

    $uploadDir = "uploads/";

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $resumeName = basename($_FILES["resume"]["name"]);

    $targetFile = $uploadDir . $resumeName;

    $extension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $allowed = array("pdf", "doc", "docx");

    if (!in_array($extension, $allowed)) {
        $errors[] = "Resume must be PDF, DOC or DOCX.";
    }

    if ($_FILES["resume"]["size"] > 5 * 1024 * 1024) {
        $errors[] = "Resume must be smaller than 5MB.";
    }

    if (empty($errors)) {
        move_uploaded_file($_FILES["resume"]["tmp_name"], $targetFile);
    }
}

// Display validation errors
if (!empty($errors)) {

    echo "<h2>Validation Errors</h2>";
    echo "<ul>";

    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }

    echo "</ul>";
    echo "<p><a href='apply.php'>Return to Application Form</a></p>";

    mysqli_close($conn);
    exit();
}

// Insert into database
$sql = "INSERT INTO eoi
(jobref, fname, lname, dob, gender, address, state, postcode, email, phone, skills, otherskills, resume)
VALUES
('$jobref',
'$fname',
'$lname',
'$dob',
'$gender',
'$address',
'$state',
'$postcode',
'$email',
'$phone',
'$skills',
'$otherskills',
'$resumeName')";

if (mysqli_query($conn, $sql)) {

    $eoiNumber = mysqli_insert_id($conn);

    echo "<h1>Application Submitted Successfully</h1>";
    echo "<p>Your EOI Number is <strong>$eoiNumber</strong></p>";

} else {

    echo "<h2>Error</h2>";
    echo mysqli_error($conn);

}

mysqli_close($conn);
?>