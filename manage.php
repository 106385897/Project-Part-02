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

// Small helper to turn a status into a colour-coded badge class
function statusClass($status) {
    switch ($status) {
        case 'New': return 'status-new';
        case 'Current': return 'status-current';
        case 'Final': return 'status-final';
        default: return '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Manager Dashboard</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>

    <header class="dashboard-topbar">
        <h1>HR Manager Dashboard</h1>
        <div class="dashboard-user">
            Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            <a href="manage.php?logout=true" class="logout-link">Logout</a>
        </div>
    </header>

    <main class="dashboard-main">

        <?php if ($message) : ?>
            <p class="dashboard-msg"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Control Panel for Searching -->
        <div class="dashboard-panel">
            <h3>Search EOIs</h3>
            <form method="GET" action="manage.php" class="dashboard-form">
                <div class="form-field">
                    <label>Job Reference:</label>
                    <input type="text" name="search_jobref" placeholder="e.g. IT123">
                </div>

                <div class="form-field">
                    <label>First Name:</label>
                    <input type="text" name="search_fname">
                </div>

                <div class="form-field">
                    <label>Last Name:</label>
                    <input type="text" name="search_lname">
                </div>

                <div class="form-field form-field-buttons">
                    <button type="submit" class="btn-primary">Search / List All</button>
                    <a href="manage.php"><button type="button" class="btn-secondary">Reset</button></a>
                </div>
            </form>
        </div>

        <!-- Control Panel for Actions -->
        <div class="dashboard-panel">
            <h3>Manager Actions</h3>

            <div class="dashboard-actions">
                <!-- Delete by Job Ref -->
                <form method="POST" action="manage.php" class="dashboard-form dashboard-form-inline">
                    <label>Delete all by Job Ref:</label>
                    <input type="text" name="jobref_to_delete" required>
                    <button type="submit" name="delete_jobref" class="btn-danger" onclick="return confirm('Are you sure you want to delete all EOIs for this job?');">Delete</button>
                </form>

                <!-- Change Status -->
                <form method="POST" action="manage.php" class="dashboard-form dashboard-form-inline">
                    <label>Change Status for EOI #:</label>
                    <input type="number" name="eoi_to_update" required class="input-small">
                    <select name="new_status">
                        <option value="New">New</option>
                        <option value="Current">Current</option>
                        <option value="Final">Final</option>
                    </select>
                    <button type="submit" name="update_status" class="btn-primary">Update Status</button>
                </form>
            </div>
        </div>

        <!-- Results Table -->
        <div class="dashboard-panel">
            <h3>EOI Applications</h3>
            <p class="sort-links">
                Sort by:
                <a href="manage.php?sort=EOInumber">EOI Number</a> |
                <a href="manage.php?sort=jobref">Job Ref</a> |
                <a href="manage.php?sort=fname">First Name</a> |
                <a href="manage.php?sort=lname">Last Name</a> |
                <a href="manage.php?sort=status">Status</a>
            </p>

            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>EOI Number</th>
                        <th>Job Ref</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            // SECURE: htmlspecialchars prevents XSS attacks if users submitted malicious script tags
                            echo "<td data-label='EOI Number'>" . htmlspecialchars($row['EOInumber']) . "</td>";
                            echo "<td data-label='Job Ref'>" . htmlspecialchars($row['jobref']) . "</td>";
                            echo "<td data-label='First Name'>" . htmlspecialchars($row['fname']) . "</td>";
                            echo "<td data-label='Last Name'>" . htmlspecialchars($row['lname']) . "</td>";
                            echo "<td data-label='Status'><span class='status-badge " . statusClass($row['status']) . "'>" . htmlspecialchars($row['status']) . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='no-records'>No records found.</td></tr>";
                    }
                    $stmt->close();
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>

    </main>

</body>
</html>