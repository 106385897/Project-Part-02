<?php
session_start();

// 1. SECURE: Kick out anyone who isn't logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'settings.php';
$conn = new mysqli($host, $user, $pwd, $sql_db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// --- HANDLE ACTIONS (DELETE & UPDATE) ---

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Handle Delete by Job Reference
if (isset($_POST['delete_jobref']) && !empty($_POST['jobref_to_delete'])) {
    $jobref_del = $_POST['jobref_to_delete'];
    $stmt = $conn->prepare("DELETE FROM eoi WHERE jobref = ?");
    $stmt->bind_param("s", $jobref_del);
    $stmt->execute();
    $message = "Deleted " . $stmt->affected_rows . " EOI(s) for Job Reference: " . htmlspecialchars($jobref_del);
    $stmt->close();
}

// Handle Status Update
if (isset($_POST['update_status'])) {
    $eoi_id = $_POST['eoi_to_update'];
    $new_status = $_POST['new_status'];
    $stmt = $conn->prepare("UPDATE eoi SET status = ? WHERE EOInumber = ?");
    $stmt->bind_param("si", $new_status, $eoi_id);
    $stmt->execute();
    $message = "Updated status for EOI Number: " . htmlspecialchars($eoi_id);
    $stmt->close();
}


// --- HANDLE SEARCH & SORT QUERIES ---

// Whitelist allowed sort columns to prevent SQL injection in the ORDER BY clause
$allowed_sorts = ['EOInumber', 'jobref', 'fname', 'lname', 'status'];
$sort_by = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sorts) ? $_GET['sort'] : 'EOInumber';

// Base SQL query
$sql = "SELECT * FROM eoi WHERE 1=1";
$params = [];
$types = "";

// Add conditions based on what the manager searched for
if (!empty($_GET['search_jobref'])) {
    $sql .= " AND jobref = ?";
    $params[] = $_GET['search_jobref'];
    $types .= "s";
}

if (!empty($_GET['search_fname'])) {
    $sql .= " AND fname LIKE ?";
    $params[] = "%" . $_GET['search_fname'] . "%";
    $types .= "s";
}

if (!empty($_GET['search_lname'])) {
    $sql .= " AND lname LIKE ?";
    $params[] = "%" . $_GET['search_lname'] . "%";
    $types .= "s";
}

// Append the sorting order
$sql .= " ORDER BY $sort_by ASC";

// Prepare and execute the search query
$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR Manager Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .panel { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background: #f9f9f9; }
        .msg { color: green; font-weight: bold; }
    </style>
</head>
<body>

    <div style="float: right;">
        Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> | 
        <a href="manage.php?logout=true">Logout</a>
    </div>

    <h1>HR Manager Dashboard</h1>

    <?php if ($message) echo "<p class='msg'>$message</p>"; ?>

    <!-- Control Panel for Searching -->
    <div class="panel">
        <h3>Search EOIs</h3>
        <form method="GET" action="manage.php">
            <label>Job Reference:</label>
            <input type="text" name="search_jobref" placeholder="e.g. IT123">
            
            <label>First Name:</label>
            <input type="text" name="search_fname">
            
            <label>Last Name:</label>
            <input type="text" name="search_lname">
            
            <button type="submit">Search / List All</button>
            <a href="manage.php"><button type="button">Reset</button></a>
        </form>
    </div>

    <!-- Control Panel for Actions -->
    <div class="panel">
        <h3>Manager Actions</h3>
        
        <!-- Delete by Job Ref -->
        <form method="POST" action="manage.php" style="display:inline-block; margin-right: 20px;">
            <label>Delete all by Job Ref:</label>
            <input type="text" name="jobref_to_delete" required>
            <button type="submit" name="delete_jobref" onclick="return confirm('Are you sure you want to delete all EOIs for this job?');">Delete</button>
        </form>

        <!-- Change Status -->
        <form method="POST" action="manage.php" style="display:inline-block;">
            <label>Change Status for EOI #:</label>
            <input type="number" name="eoi_to_update" required style="width: 70px;">
            <select name="new_status">
                <option value="New">New</option>
                <option value="Current">Current</option>
                <option value="Final">Final</option>
            </select>
            <button type="submit" name="update_status">Update Status</button>
        </form>
    </div>

    <!-- Results Table -->
    <h3>EOI Applications</h3>
    <p>Sort by: 
        <a href="manage.php?sort=EOInumber">EOI Number</a> | 
        <a href="manage.php?sort=jobref">Job Ref</a> | 
        <a href="manage.php?sort=fname">First Name</a> | 
        <a href="manage.php?sort=lname">Last Name</a> | 
        <a href="manage.php?sort=status">Status</a>
    </p>

    <table>
        <tr>
            <th>EOI Number</th>
            <th>Job Ref</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Status</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                // SECURE: htmlspecialchars prevents XSS attacks if users submitted malicious script tags
                echo "<td>" . htmlspecialchars($row['EOInumber']) . "</td>";
                echo "<td>" . htmlspecialchars($row['jobref']) . "</td>";
                echo "<td>" . htmlspecialchars($row['fname']) . "</td>";
                echo "<td>" . htmlspecialchars($row['lname']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No records found.</td></tr>";
        }
        $stmt->close();
        $conn->close();
        ?>
    </table>

</body>
</html>