<<?php
session_start();
require_once "pdo.php";

if ( ! isset($_SESSION["name"]) || ! isset($_SESSION["user_id"])) {
    die('ACCESS DENIED');
}

if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM profile WHERE profile_id = :profile_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':profile_id' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
}

$stmt = $pdo->prepare("SELECT profile_id, first_name, last_name FROM profile where profile_id = :profile_id");
$stmt->execute(array(":profile_id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mart Nael delete profile</title>
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
    <h1>Deleting Profile</h1>
    <p>
        First Name: <br>
        <?= htmlentities($row['first_name']) ?>
        <br>
        Last Name: <br>
        <?= htmlentities($row['last_name']) ?>
    </p>
    <p>
    <form method="post">
        <input type="hidden" name="profile_id" value=" <?= $row['profile_id'] ?>">
        <input type="submit" name="delete" value="Delete">
        <input type="submit" name="cancel" value="Cancel">
    </form>
    </p>

</div>
</body>
</html>