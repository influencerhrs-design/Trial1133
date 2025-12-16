<?php
// --- ADMIN CONFIGURATION ---
$admin_password = "7797"; 

session_start();
$file = 'jobs.json';

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Handle Login
if (isset($_POST['login'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['loggedin'] = true;
    } else {
        $error = "Incorrect Password!";
    }
}

// Handle Approval or Deletion (Only if logged in)
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Read current jobs
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }
    $data = json_decode(file_get_contents($file), true) ?: [];

    // Action: Approve
    if (isset($_GET['approve'])) {
        $id = $_GET['approve'];
        if (isset($data[$id])) {
            $data[$id]['approved'] = true;
            file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
        }
        header("Location: admin.php");
        exit();
    }

    // Action: Delete
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        if (isset($data[$id])) {
            array_splice($data, $id, 1);
            file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
        }
        header("Location: admin.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard | JobsPortal</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
    <style>
        body { background-color: #121212; color: white; font-family: "Poppins", sans-serif; }
        .bg-yellow { background-color: #FFD833 !important; color: black !important; }
        .w3-modal-content { border-radius: 8px; border: 2px solid #FFD833; }
        .status-pending { color: #FFD833; font-weight: bold; font-size: 0.8em; border: 1px solid #FFD833; padding: 2px 6px; border-radius: 4px; }
        .status-live { color: #25D366; font-weight: bold; font-size: 0.8em; border: 1px solid #25D366; padding: 2px 6px; border-radius: 4px; }
        tr:hover { background-color: #1e1e1e; }
        .w3-table td { vertical-align: middle; }
    </style>
</head>
<body class="w3-container">

<?php if (!isset($_SESSION['loggedin'])): ?>
    <div class="w3-modal" style="display:block; background-color: rgba(0,0,0,0.9);">
        <div class="w3-modal-content w3-dark-grey w3-padding-32" style="max-width:400px; margin-top:150px;">
            <div class="w3-container w3-center">
                <h2 style="color:#FFD833"><b>Admin Panel</b></h2>
                <p>Enter access code to manage jobs</p>
                <form method="POST">
                    <input class="w3-input w3-border w3-margin-bottom w3-center" type="password" name="password" placeholder="****" style="background:#000; color:#FFD833; font-size:24px; letter-spacing: 5px;" required autofocus>
                    <button type="submit" name="login" class="w3-button w3-block bg-yellow w3-round w3-bold">UNLOCK</button>
                    <?php if(isset($error)): ?>
                        <p class="w3-text-red w3-margin-top"><?php echo $error; ?></p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="w3-main w3-content" style="max-width:1000px">
        <header class="w3-padding-48 w3-border-bottom w3-border-dark-grey">
            <div class="w3-right">
                <a href="admin.php?logout=1" class="w3-button w3-red w3-small w3-round">Sign Out</a>
            </div>
            <h1 class="w3-xxlarge">Jobs<span class="w3-text-yellow">Moderator</span></h1>
            <p class="w3-opacity">Manage pending and active listings from `jobs.json`</p>
        </header>

        <div class="w3-responsive w3-margin-top">
            <table class="w3-table w3-bordered w3-border-dark-grey">
                <tr style="background:#1e1e1e">
                    <th>Job Info</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th class="w3-center">Actions</th>
                </tr>
                <?php if (empty($data)): ?>
                    <tr><td colspan="4" class="w3-center w3-padding-64 w3-opacity">No data found in jobs.json</td></tr>
                <?php else: ?>
                    <?php foreach ($data as $index => $job): ?>
                    <tr>
                        <td>
                            <b><?php echo htmlspecialchars($job['title']); ?></b><br>
                            <small class="w3-text-grey"><?php echo htmlspecialchars($job['company']); ?> | <?php echo htmlspecialchars($job['location']); ?></small>
                        </td>
                        <td>
                            <small><?php echo htmlspecialchars($job['company_email']); ?><br>
                            WA: <?php echo htmlspecialchars($job['whatsapp']); ?></small>
                        </td>
                        <td>
                            <?php if($job['approved']): ?>
                                <span class="status-live">LIVE</span>
                            <?php else: ?>
                                <span class="status-pending">PENDING</span>
                            <?php endif; ?>
                        </td>
                        <td class="w3-center">
                            <div class="w3-bar">
                                <?php if(!$job['approved']): ?>
                                    <a href="admin.php?approve=<?php echo $index; ?>" class="w3-button w3-green w3-tiny w3-round">Approve</a>
                                <?php endif; ?>
                                <a href="admin.php?delete=<?php echo $index; ?>" class="w3-button w3-red w3-tiny w3-round" onclick="return confirm('Permanently delete this job?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>
        
        <footer class="w3-padding-32 w3-center w3-opacity">
            <p>Direct JSON Editor v1.0</p>
            <a href="index.html" class="w3-text-yellow">View Live Site</a>
        </footer>
    </div>
<?php endif; ?>

</body>
</html>
