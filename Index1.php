<?php

require_once "pdo.php";
$stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM profile");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Mart Nael Registery</title>
    <link type="text/css" rel="stylesheet" href="index1.css">
</head>
<body>
<div class="main">
    <h1 id="header">
        Registry v.1
    </h1>
    <p>
        <a href="login.php">Please log in</a>
    </p>
    <p class = "table">
        <?php
        if (!empty($rows)) {
            echo("<table>");
            echo("<tr id = 'top_row'><td>");
            echo("Name");
            echo("</td><td>");
            echo("Headline");
            echo("</td></tr>");
            foreach ($rows as $row) {
                echo("<tr><td>");
                echo('<a href="view.php?profile_id=' . $row['profile_id'] . '">'. $row['first_name'] . ' ' . $row['last_name'] .'</a>');
                echo("</td><td>");
                echo(htmlentities($row['headline']));
                echo("</td></tr>\n");
            }
            echo "</table>";
        } else {
            echo(" ");
        }
        ?>
    </p>
</div>
</body>
</html>