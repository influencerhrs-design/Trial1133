<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $file = 'jobs.json';
    
    $newJob = [
        "company" => $_POST['company'],
        "title" => $_POST['title'],
        "location" => $_POST['location'],
        "company_email" => $_POST['email'],
        "whatsapp" => $_POST['whatsapp'],
        "deadline" => $_POST['deadline'],
        "description" => $_POST['description'],
        "approved" => false 
    ];

    $currentData = json_decode(file_get_contents($file), true) ?: [];
    $currentData[] = $newJob;
    file_put_contents($file, json_encode($currentData, JSON_PRETTY_PRINT));

    // Admin Notification
    $to = "hrs.team.a@gmail.com";
    $subject = "New Job Review Needed";
    $body = "A new job has been submitted for " . $_POST['title'] . ". Check jobs.json to approve.";
    mail($to, $subject, $body);

    header("Location: success.html");
    exit();
}
?>
