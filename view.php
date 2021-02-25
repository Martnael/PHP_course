<?php

require_once "pdo.php";

// $sql = "SELECT profile.first_name, profile.last_name, profile.email, profile.headline, profile.summary, position.year, position.description FROM profile JOIN position ON profile.profile_id = position.profile_id WHERE profile.profile_id = :profile_id";
$sql = "SELECT first_name, last_name, email, headline, summary FROM profile WHERE profile_id = :profile_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":profile_id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$first_name = $row['first_name'];
$last_name = $row['last_name'];
$email = $row['email'];
$headline = $row['headline'];
$summary = $row['summary'];

$sql_pos = "SELECT * FROM position WHERE profile_id = :profile_id ORDER BY rank";
$stmt_pos = $pdo->prepare($sql_pos);
$stmt_pos->execute(array(":profile_id" => $_GET['profile_id']));
$rows = $stmt_pos->fetchall(PDO::FETCH_ASSOC);

$sql_edu = "SELECT education.rank, education.year, institution.name FROM education JOIN institution ON institution.institution_id = education.institution_id WHERE profile_id = :profile_id ORDER BY rank";
$stmt_edu = $pdo->prepare($sql_edu);
$stmt_edu->execute(array(":profile_id" => $_GET['profile_id']));
$rows_edu = $stmt_edu->fetchall(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Mart Nael Registery</title>
    <link type="text/css" rel="stylesheet" href="view.css">
</head>
<body>
<div class="main">
    <h1 id="header">
        Profile information
    </h1>
    <p>
        <?php
        echo('First name: '.$first_name);
        echo('<br>');
        echo('Last name:  '.$last_name);
        echo('<br>');
        echo('Email:      '.$email);
        echo('<br>');
        echo('Headline:');
        echo('<br>');
        echo($headline);
        echo('<br>');
        echo('Summary');
        echo('<br>');
        echo($summary);
        echo('</p><p>');
        if (!empty($rows)) {
            echo('Position:');
            echo('</p><ul>');
            foreach ($rows as $row) {
                echo('<li>');
                echo($row['year'] . ': ' . $row['description'] . '<br>');
                echo('</li>');
            }
        }
        echo('</ul>');
        echo('<p>');
        if (!empty($rows_edu)) {
            echo('Education:');
            echo('</p><ul>');
            foreach ($rows_edu as $row_edu) {
                echo('<li>');
                echo($row_edu['year'] . ': ' . $row_edu['name'] . '<br>');
                echo('</li>');
            }
        }
        echo('</ul>');
        ?>
    </p>
    <p>
        <a href="index.php">Done</a>
    </p>
</div>
</body>
</html>