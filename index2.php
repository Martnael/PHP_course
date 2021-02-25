<?php

if ( ! isset($_SESSION["name"]) && ! isset($_SESSION["user_id"]) ) {
    die('ACCESS DENIED');
}

require_once "pdo.php";

$stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM profile");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mart Nael registery</title>
    <link type="text/css" rel="stylesheet" href="index2.css">
</head>
<body>
<div class="sidenav">
    <a href="add.php">Add New Entry</a>
    <a href="index.php">List</a>
    <a href="contact.php">Contact</a>
    <a href="logout.php" ID="logout">Logout</a>
</div>
<div class="main">
    <h2>Registery</h2>
    <p>
        <?php
        if ( isset($_SESSION['error']) ) {
            echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
            unset($_SESSION['error']);
        }

        if ( isset($_SESSION['success']) ) {
            echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
            unset($_SESSION['success']);
        }
        ?>
    </p>
    <p class = "table">
        <?php
        if (!empty($rows)) {
            echo "<table>";
            echo "<tr id = 'top_row'><td>";
            echo("Name");
            echo("</td><td>");
            echo("Headline");
            echo("</td><td>");
            echo("Action");
            echo("</td></tr>");
            foreach ($rows as $row) {
                echo "<tr><td>";
                echo(htmlentities($row["first_name"]." ".$row["last_name"]));
                echo("</td><td>");
                echo(htmlentities($row['headline']));
                echo("</td><td>");
                echo('<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> / ');
                echo('<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>');
                echo("</td></tr>\n");
            }
            echo "</table>";
        } else {
            echo("No rows found");
        }
        ?>
    </p>
</div>
</body>
</html>