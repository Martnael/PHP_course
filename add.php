<?php
session_start();

if ( ! isset($_SESSION["name"]) && $_SESSION["user_id"]) {
    die('ACCESS DENIED');
}

if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

require_once "pdo.php";

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION["failure"] = "All fields are required";
        header('Location: add.php');
        return;
    } else {
        $needle = "@";
        $pos = strpos($_POST['email'], $needle);
        if ($pos == false) {
            $_SESSION["failure"] = "Email address must contain @";
            header('Location: add.php');
            return;
        } else {
            $sql = "INSERT INTO profile (user_id, first_name, last_name, email, headline, summary) VALUES (:user_id, :first_name, :last_name, :email, :headline, :summary)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':user_id' => $_SESSION['user_id'],
                ':first_name' => $_POST['first_name'],
                ':last_name' => $_POST['last_name'],
                ':email' => $_POST['email'],
                ':headline' => $_POST['headline'],
                ':summary' => $_POST['summary']));
            $_SESSION["success"] = "added";
        }
    }

    $profile_id = $pdo->lastInsertId();
    $rank = 1;

    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i])) continue;
        if (!isset($_POST['desc' . $i])) continue;
        if (is_numeric($_POST['year' . $i])) {
            $year = $_POST['year' . $i];
            $desc = $_POST['desc' . $i];
            $sql = "INSERT INTO position ( profile_id, rank, year, description) VALUES (:profile_id, :rank, :year, :description)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                    ':profile_id' => $profile_id,
                    ':rank' => $rank,
                    ':year' => $year,
                    ':description' => $desc)
            );
            $rank++;
        } else {
            $_SESSION["failure"] = "Year have to be numeric value";
            header('Location: add.php');
            return;
        }
    }
    $rank = 0;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['edu_year' . $i])) continue;
        if (!isset($_POST['edu_school' . $i])) continue;
        if (is_numeric($_POST['edu_year' . $i])) {
            $edu_year = $_POST['edu_year' . $i];
            $edu_school = ['edu_school' . $i];
            $sql_school = " SELECT name, institution_id FROM institution WHERE name = :name";
            $stmt_school = $pdo->prepare($sql_school);
            $stmt_school->execute(array(":name" => $_POST['edu_school' . $i]));
            $row_school = $stmt_school->fetch(PDO::FETCH_ASSOC);
            if (empty($row_school)) {
                $sql_ins = "INSERT INTO institution (name) VALUES (:name)";
                $stmt_ins = $pdo ->prepare($sql_ins);
                $stmt_ins -> execute (array(
                        ':name' => $_POST['edu_school' . $i])
                );
                $institution_id = $pdo->lastInsertId();              // Institution id
                $sql_edu = "INSERT INTO education (profile_id, institution_id, rank, year) VALUES (:profile_id, :institution_id, :rank, :year)";
                $stmt_edu = $pdo ->prepare($sql_edu);
                $stmt_edu -> execute (array(
                        ':profile_id' => $profile_id,
                        ':institution_id' => $institution_id,
                        ':rank' => $rank,
                        ':year' => $edu_year)
                );
            } else {
                $institution_id = $row_school['institution_id'];
                $sql_edu = "INSERT INTO education (profile_id, institution_id, rank, year) VALUES (:profile_id, :institution_id, :rank, :year)";
                $stmt_edu = $pdo->prepare($sql_edu);
                $stmt_edu->execute(array(
                        ':profile_id' => $profile_id,
                        ':institution_id' => $institution_id,
                        ':rank' => $rank,
                        ':year' => $edu_year)
                );
            }
            $rank++;
        } else {
            $_SESSION["failure"] = "Year have to be numeric value";
            header('Location: add.php');
            return;
        }
    }
    $_SESSION["success"] = "added";
    header('Location: index.php');
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Mart Nael</title>
    <?php
        require_once "head.php";
    ?>
    <link type="text/css" rel="stylesheet" href="index2.css">
</head>
<body>
<?php
$_SESSION["failure"] = isset($_SESSION["failure"]) ? $_SESSION["failure"] : "";
?>
<div class="sidenav">
    <a href="add.php">Add profile</a>
    <a href="index.php">List</a>
    <a href="contact.php">Contact</a>
    <a href="logout.php" ID="logout">Logout</a>
</div>
<div class="main">
    <h1>
        <?php
        echo("Adding Profile for ".$_SESSION["name"]);
        ?>
    </h1>
    <p>
        <?php
        if (isset ($_SESSION["failure"])) {
            echo('<p style="color: red;">' . htmlentities($_SESSION["failure"]) . "</p>\n");
            unset($_SESSION["failure"]);
        }
        ?>
    </p>
    <p>
        <form method="post">
    <p>
        First name:
        <input type="text" name="first_name" id="first_name" placeholder="First Name"><br/>
    </p>
    <p>
        Last name:
        <input type="text" name="last_name" id="last_name" placeholder="Last Name"><br/>
    </p>
    <p>
        E-Mail:
        <input type="text" name="email" id="email" placeholder="E-Mail"><br/>
    </p>
    <p>
        Headline:
        <input type="text" name="headline" id="headline" placeholder="Headline"><br/>
    </p>
    <p>
        Summary:<br/>
        <textarea name="summary" id="summary" rows="8" cols="80"></textarea><br/>
    </p>
    <p>
        Education:
        <input type="submit" id = "add_edu" value = "+">
        <div id = "edu_fields" class = "autocomplete">
        </div>
    </p>
    <p>
        Position:
        <input type="submit" id = "add_position" value = "+">
        <div id = "position_fields">
        </div>
    </p>
    <p>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">
    </p>
    </form>
    <script>
        countPos = 0;
        countedu = 0;

        $(document).ready(function(){
            $('#add_position').click(function(event){
                event.preventDefault();
                if ( countPos >= 9 ) {
                    alert("Maximum of nine position entries exceeded");
                    return;
                }
                countPos++;
                $('#position_fields').append(
                    '<div id="position'+countPos+'"> \
                    <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                    <input type="button" value="-" \
                    onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                    <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                    </div>');
            });
        });

        $(document).ready(function(){
            $('#add_edu').click(function(event){
                event.preventDefault();
                if ( countedu >= 9 ) {
                    alert("Maximum of nine education entries exceeded");
                    return;
                }
                countedu++;
                $('#edu_fields').append(
                    '<div id="edu'+countedu+'"> \
                    <p>Year: <input type="text" name="edu_year'+countedu+'" value="" /> \
                    <input type="button" value="-" \
                    onclick="$(\'#edu'+countedu+'\').remove();return false;"></p> \
                    <p> School: <input type="text" size="80" name="edu_school'+countedu+'" class="school" value=""/>\
                    </p> </div>');
                $('.school').autocomplete({
                    source: "school.php"
                });
            });
        });

    </script>
    </p>

</div>
</body>
</html>