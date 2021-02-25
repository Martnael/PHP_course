<?php

session_start();
require_once "pdo.php";


if ( isset($_POST["email"]) && isset($_POST["pass"]) ) {
    // clear old session if its set
    unset($_SESSION["name"]);
    unset($_SESSION["user_id"]);

    $email = $_POST["email"];
    $pass = $_POST["pass"];
    $salt = 'XyZzy12*_';
    $check = hash('md5', $salt.$pass);

    // SQL prepare and taking out possible match
    $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :email AND password = :pass');
    $stmt->execute(array( ':email' => $email, ':pass' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // comparing results and declaring 2 sessions if no match from sql then showing failure message
    if ($row !== false) {
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        header("Location: index.php");
        return;
    } else {
        $_SESSION['failure'] = " No such username";
        header("Location: login.php");
        return;
    }
}

// cancel button action
if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mart Nael Login Page - 12bb6b22</title>
    <link type="text/css" rel="stylesheet" href="login.css">
</head>
<body>
<div class="container">
    <h1>Please Log In</h1>
    <?php
    if ( isset($_SESSION["failure"]) ) {
        echo('<p style="color:red">'.htmlentities($_SESSION["failure"])."</p>\n");
        unset($_SESSION["failure"]);
    }
    ?>
    <form method="POST" action="login.php">
        <label for="nam">User Name: </label>
        <input type="text" name="email" id="email" placeholder="Your e-mail"><br/>
        <label for="id_1723">Password:  </label>
        <input type="password" name="pass" id="id_1723" placeholder="Hint: php123"><br/>
        <input type="submit" onclick="return doValidate()" value="Log In">
        <input type="submit" name="cancel" value="Cancel">
    </form>
    <!-- On browser valitading correct format and is all fields filled -->
    <script>
        function doValidate() {
            console.log('Validating...');
            try {
                addr = document.getElementById('email').value;
                pw = document.getElementById('id_1723').value;
                console.log("Validating addr="+addr+" pw="+pw);
                if (addr == null || addr == "" || pw == null || pw == "") {
                    alert("Both fields must be filled out");
                    return false;
                }
                if ( addr.indexOf('@') == -1 ) {
                    alert("Invalid email address");
                    return false;
                }
                return true;
            } catch(e) {
                return false;
            }
            return false;
        }
    </script>
</div>
</body>
