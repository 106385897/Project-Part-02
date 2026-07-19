<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'settings.php';
$conn = new mysqli($host, $user, $pwd, $sql_db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = isset($_GET['msg']) ? $_GET['msg'] : "";

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (isset($_POST['delete_jobref']) && !empty($_POST['jobref_to_delete'])) {
    $jobref_del = $_POST['jobref_to_delete'];
    $stmt = $conn->prepare("DELETE FROM eoi WHERE jobref = ?");
    if ($stmt === false) {
        die("Query prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $jobref_del);
    $stmt->execute();
    $message = "Deleted " . $stmt->affected_rows . " EOI(s) for Job Reference: " . htmlspecialchars($jobref_del);
    $stmt->close();
    header("Location: manage.php?msg=" . urlencode($message));
    exit;
}

if (isset($_POST['update_status'])) {
    $eoi_id = $_POST['eoi_to_update'];
    $new_status = $_POST['new_status'];

    $allowed_statuses = ['New', 'Current', 'Final'];
    if (!in_array($new_status, $allowed_statuses, true)) {
        $new_status = 'New';
    }

    $stmt = $conn->prepare("UPDATE eoi SET status = ? WHERE EOInumber = ?");
    if ($stmt === false) {
        die("Query prepare failed: " . $conn->error);
    }
    $stmt->bind_param("si", $new_status, $eoi_id);
    $stmt->execute();
    $message = "Updated status for EOI Number: " . htmlspecialchars($eoi_id);
    $stmt->close();
    header("Location: manage.php?msg=" . urlencode($message));
    exit;
}

$allowed_sorts = ['EOInumber', 'jobref', 'fname', 'lname', 'status'];
$sort_by = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sorts) ? $_GET['sort'] : 'EOInumber';

$search_jobref = isset($_GET['search_jobref']) ? $_GET['search_jobref'] : '';
$search_fname  = isset($_GET['search_fname'])  ? $_GET['search_fname']  : '';
$search_lname  = isset($_GET['search_lname'])  ? $_GET['search_lname']  : '';

$sql = "SELECT * FROM eoi WHERE 1=1";
$params = [];
$types = "";


if (!empty($search_jobref)) {
    $sql .= " AND jobref = ?";
    $params[] = $search_jobref;
    $types .= "s";
}

if (!empty($search_fname)) {
    $sql .= " AND fname LIKE ?";
    $params[] = "%" . $search_fname . "%";
    $types .= "s";
}

if (!empty($search_lname)) {
    $sql .= " AND lname LIKE ?";
    $params[] = "%" . $search_lname . "%";
    $types .= "s";
}

$sql .= " ORDER BY $sort_by ASC";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    $result = false;
    $message = $message ?: "No EOI records yet (the eoi table may not exist).";
} else {
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
}


function statusClass($status) {
    switch ($status) {
        case 'New': return 'status-new';
        case 'Current': return 'status-current';
        case 'Final': return 'status-final';
        default: return '';
    }
}

function sortLink($column, $label, $sort_by, $search_jobref, $search_fname, $search_lname) {
    $qs = http_build_query([
        'sort' => $column,
        'search_jobref' => $search_jobref,
        'search_fname' => $search_fname,
        'search_lname' => $search_lname,
    ]);
    $activeClass = ($column === $sort_by) ? ' class="active-sort"' : '';
    return "<a href=\"manage.php?{$qs}\"{$activeClass}>{$label}</a>";
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
            <p class="dashboard-msg"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <div class="dashboard-panel">
            <h3>Search EOIs</h3>
            <form method="GET" action="manage.php" class="dashboard-form">
                <div class="form-field">
                    <label>Job Reference:</label>
                    <input type="text" name="search_jobref" value="<?php echo htmlspecialchars($search_jobref); ?>" placeholder="e.g. IT123">
                </div>

                <div class="form-field">
                    <label>First Name:</label>
                    <input type="text" name="search_fname" value="<?php echo htmlspecialchars($search_fname); ?>">
                </div>

                <div class="form-field">
                    <label>Last Name:</label>
                    <input type="text" name="search_lname" value="<?php echo htmlspecialchars($search_lname); ?>">
                </div>

                <div class="form-field form-field-buttons">
                    <button type="submit" class="btn-primary">Search / List All</button>
                    <a href="manage.php"><button type="button" class="btn-secondary">Reset</button></a>
                </div>
            </form>
        </div>

        
        <div class="dashboard-panel">
            <h3>Manager Actions</h3>

            <div class="dashboard-actions">
            
                <form method="POST" action="manage.php" class="dashboard-form dashboard-form-inline">
                    <label>Delete all by Job Ref:</label>
                    <input type="text" name="jobref_to_delete" required>
                    <button type="submit" name="delete_jobref" class="btn-danger" onclick="return confirm('Are you sure you want to delete all EOIs for this job?');">Delete</button>
                </form>

                
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

     
        <div class="dashboard-panel">
            <h3>EOI Applications</h3>
            <p class="sort-links">
                Sort by:
                <?php echo sortLink('EOInumber', 'EOI Number', $sort_by, $search_jobref, $search_fname, $search_lname); ?> |
                <?php echo sortLink('jobref', 'Job Ref', $sort_by, $search_jobref, $search_fname, $search_lname); ?> |
                <?php echo sortLink('fname', 'First Name', $sort_by, $search_jobref, $search_fname, $search_lname); ?> |
                <?php echo sortLink('lname', 'Last Name', $sort_by, $search_jobref, $search_fname, $search_lname); ?> |
                <?php echo sortLink('status', 'Status', $sort_by, $search_jobref, $search_fname, $search_lname); ?>
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
                    if ($result !== false && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
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
                    if ($stmt !== false) { $stmt->close(); }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>

    </main>

</body>
</html>