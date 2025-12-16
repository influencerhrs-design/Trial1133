<?php
// --- ADMIN CONFIGURATION ---
$admin_password = "7797"; 

session_start();
$job_file = 'jobs.json';
$blog_file = 'blog.json';

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

// Security Check
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    
    // --- JOB LOGIC ---
    $jobs = json_decode(file_get_contents($job_file), true) ?: [];
    if (isset($_GET['approve_job'])) {
        $jobs[$_GET['approve_job']]['approved'] = true;
        file_put_contents($job_file, json_encode($jobs, JSON_PRETTY_PRINT));
        header("Location: admin.php"); exit();
    }
    if (isset($_GET['delete_job'])) {
        array_splice($jobs, $_GET['delete_job'], 1);
        file_put_contents($job_file, json_encode($jobs, JSON_PRETTY_PRINT));
        header("Location: admin.php"); exit();
    }

    // --- BLOG LOGIC ---
    $blogs = json_decode(file_get_contents($blog_file), true) ?: [];
    if (isset($_POST['add_blog'])) {
        $new_post = [
            "title" => $_POST['blog_title'],
            "date" => date("M d, Y"),
            "author" => "Admin",
            "excerpt" => $_POST['blog_excerpt'],
            "content" => $_POST['blog_content']
        ];
        $blogs[] = $new_post;
        file_put_contents($blog_file, json_encode($blogs, JSON_PRETTY_PRINT));
        header("Location: admin.php#blog-manager"); exit();
    }
    if (isset($_GET['delete_blog'])) {
        array_splice($blogs, $_GET['delete_blog'], 1);
        file_put_contents($blog_file, json_encode($blogs, JSON_PRETTY_PRINT));
        header("Location: admin.php#blog-manager"); exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Master Admin | JobsPortal</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
    <style>
        body { background-color: #121212; color: white; font-family: "Poppins", sans-serif; }
        .bg-yellow { background-color: #FFD833 !important; color: black !important; }
        input, textarea { background: #1e1e1e !important; color: white !important; border: 1px solid #333 !important; }
        .tab-btn { cursor: pointer; padding: 10px 20px; display: inline-block; border-bottom: 2px solid transparent; }
        .tab-btn.active { border-color: #FFD833; color: #FFD833; }
    </style>
</head>
<body class="w3-container w3-padding-32">

<?php if (!isset($_SESSION['loggedin'])): ?>
    <div class="w3-modal" style="display:block">
        <div class="w3-modal-content w3-dark-grey w3-padding-32 w3-center" style="max-width:400px; margin-top:100px; border: 2px solid #FFD833;">
            <h2 class="theme-yellow">Admin Unlock</h2>
            <form method="POST">
                <input class="w3-input w3-border w3-margin-bottom w3-center" type="password" name="password" placeholder="Passcode" required autofocus>
                <button type="submit" name="login" class="w3-button bg-yellow w3-block">Access Dashboard</button>
            </form>
        </div>
    </div>
<?php else: ?>

    <header class="w3-padding-16 w3-border-bottom w3-border-dark-grey">
        <a href="admin.php?logout=1" class="w3-right w3-button w3-red w3-round">Logout</a>
        <h1 class="w3-xlarge">Master <span class="w3-text-yellow">Dashboard</span></h1>
    </header>

    <div class="w3-margin-top">
        <div class="tab-btn active" onclick="openTab('jobs')">Manage Jobs</div>
        <div class="tab-btn" onclick="openTab('blog')">Manage Blog</div>
    </div>

    <div id="jobs" class="tab-content w3-margin-top">
        <h3>Pending & Active Jobs</h3>
        <table class="w3-table w3-bordered w3-border-dark-grey">
            <tr class="w3-dark-grey"><th>Job/Company</th><th>Status</th><th>Actions</th></tr>
            <?php foreach($jobs as $idx => $j): ?>
            <tr>
                <td><b><?= $j['title'] ?></b><br><small><?= $j['company'] ?></small></td>
                <td><?= $j['approved'] ? '<span class="w3-text-green">LIVE</span>' : '<span class="w3-text-yellow">PENDING</span>' ?></td>
                <td>
                    <?php if(!$j['approved']): ?> <a href="admin.php?approve_job=<?= $idx ?>" class="w3-button w3-green w3-tiny">Approve</a> <?php endif; ?>
                    <a href="admin.php?delete_job=<?= $idx ?>" class="w3-button w3-red w3-tiny">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div id="blog" class="tab-content w3-margin-top" style="display:none">
        <h3>Post New Blog Article</h3>
        <form method="POST" class="w3-container w3-padding w3-card-4 w3-dark-grey">
            <label>Title</label><input class="w3-input" name="blog_title" required>
            <label>Short Excerpt (Intro)</label><input class="w3-input" name="blog_excerpt" required>
            <label>Full Content</label><textarea class="w3-input" name="blog_content" rows="5" required></textarea>
            <button type="submit" name="add_blog" class="w3-button bg-yellow w3-margin-top">Publish Article</button>
        </form>

        <h3 class="w3-margin-top">Existing Posts</h3>
        <table class="w3-table w3-bordered w3-border-dark-grey">
            <?php foreach($blogs as $idx => $b): ?>
            <tr>
                <td><b><?= $b['title'] ?></b><br><small><?= $b['date'] ?></small></td>
                <td><a href="admin.php?delete_blog=<?= $idx ?>" class="w3-button w3-red w3-tiny">Remove</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

<?php endif; ?>

<script>
function openTab(name) {
    var i;
    var x = document.getElementsByClassName("tab-content");
    for (i = 0; i < x.length; i++) { x[i].style.display = "none"; }
    document.getElementById(name).style.display = "block";
    
    var buttons = document.getElementsByClassName("tab-btn");
    for (i = 0; i < buttons.length; i++) { buttons[i].className = buttons[i].className.replace(" active", ""); }
    event.currentTarget.className += " active";
}
</script>
</body>
</html>
