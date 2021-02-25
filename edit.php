<?php

session_start();
require_once "pdo.php";

if ( ! isset($_SESSION["name"]) && $_SESSION["user_id"]) {
    die('ACCESS DENIED');
}

if ( ! isset($_REQUEST['profile_id']) ) {
    $_SESSION['error'] = "Profile id is missing";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :profile_id");
$stmt->execute(array(":profile_id" => $_REQUEST['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile id';
    header( 'Location: index.php' ) ;
    return;
}

if ( isset($_POST['profile_id']) && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
    if (strlen($_POST['first_name'])<1 || strlen($_POST['last_name'])<1 || strlen($_POST['email'])<1 || strlen($_POST['headline'])<1 || strlen($_POST['summary'])<1) {
        $_SESSION['error'] = 'All information have to be filled';
        header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
        return;
    } else {
        $haystack = "@";
        $pos = strpos($_POST['email'], $haystack);
        if ( $pos == false ) {
            $_SESSION["failure"] = "Email address must contain @";
            header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
            return;
        } else {
            $sql = "UPDATE profile SET first_name = :first_name, last_name = :last_name, email = :email, headline = :headline, summary = :summary WHERE profile_id = :profile_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':first_name' => $_POST['first_name'],
                ':last_name' => $_POST['last_name'],
                ':email' => $_POST['email'],
                ':headline' => $_POST['headline'],
                ':summary' => $_POST['summary'],
                ':profile_id' => $_POST['profile_id']));

            $profile_id = $pdo->lastInsertId();

            $sql = "DELETE FROM position WHERE profile_id = :profile_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(':profile_id' => $_REQUEST['profile_id']));

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
                            ':profile_id' => $_REQUEST['profile_id'],
                            ':rank' => $rank,
                            ':year' => $year,
                            ':description' => $desc)
                    );
                    $rank++;
                } else {
                    $_SESSION["failure"] = "Year have to be numeric value";
                    header('Location: edit.php?profile_id=' . $_REQUEST['profile_id']);
                    return;
                }
            }
            // Deleting Education part
            $sql_del_edu = "DELETE FROM education WHERE profile_id = :profile_id";
            $stmt_del_edu = $pdo->prepare($sql_del_edu);
            $stmt_del_edu->execute(array(':profile_id' => $_REQUEST['profile_id']));

            $rank_edu = 0;

            for ($i = 1; $i <= 9; $i++) {
                if (!isset($_POST['edu_year' . $i])) continue;
                if (!isset($_POST['edu_school' . $i])) continue;
                if (is_numeric($_POST['edu_year' . $i])) {
                    $edu_year = $_POST['edu_year' . $i];
                    $edu_school = $_POST['edu_school' . $i];
                    $sql_school = " SELECT name, institution_id FROM institution WHERE name = :name";
                    $stmt_school = $pdo->prepare($sql_school);
                    $stmt_school->execute(array(":name" => $_POST['edu_school' . $i]));
                    $row_school = $stmt_school->fetch(PDO::FETCH_ASSOC);
                    if (empty($row_school)) {
                        $sql_ins = "INSERT INTO institution (name) VALUES (:name)";
                        $stmt_ins = $pdo->prepare($sql_ins);
                        $stmt_ins->execute(array(
                                ':name' => $_POST['edu_school' . $i])
                        );
                        $institution_id = $pdo->lastInsertId();              // Institution id
                        $sql_edu = "INSERT INTO education (profile_id, institution_id, rank, year) VALUES (:profile_id, :institution_id, :rank, :year)";
                        $stmt_edu = $pdo->prepare($sql_edu);
                        $stmt_edu->execute(array(
                                ':profile_id' => $_REQUEST['$profile_id'],
                                ':institution_id' => $institution_id,
                                ':rank' => $rank_edu,
                                ':year' => $edu_year)
                        );
                    } else {
                        $institution_id = $row_school['institution_id'];
                        $sql_edu = "INSERT INTO education (profile_id, institution_id, rank, year) VALUES (:profile_id, :institution_id, :rank, :year)";
                        $stmt_edu = $pdo->prepare($sql_edu);
                        $stmt_edu->execute(array(
                                ':profile_id' => $_REQUEST['profile_id'],
                                ':institution_id' => $institution_id,
                                ':rank' => $rank_edu,
                                ':year' => $edu_year)
                        );
                    }
                    $rank_edu++;
                } else {
                    $_SESSION["failure"] = "Year have to be numeric value";
                    header('Location: add.php');
                    return;
                }
            }
        $_SESSION["success"] = "Profile modified";
        header('Location: index.php');
        return;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mart Nael edit profile</title>
    <?php
    require_once "head.php";
    ?>
    <link type="text/css" rel="stylesheet" href="index2.css">
</head>
<body>
<div class="sidenav">
    <a href="add.php">Add new entry</a>
    <a href="index.php">List</a>
    <a href="contact.php">Contact</a>
    <a href="logout.php" ID="logout">Logout</a>
</div>
<div class="main">
    <h2>Edit record</h2>
    <p>
        <?php
        if (isset ($_SESSION["failure"])) {
        echo('<p style="color: red;">' . htmlentities($_SESSION["error"]) . "</p>\n");
        unset($_SESSION["error"]);
        }
        ?>
    </p>
    <?php
    $stmt = $pdo->prepare("SELECT first_name, last_name, email, headline, summary FROM profile WHERE profile_id = :profile_id");
    $stmt->execute(array(":profile_id" => $_REQUEST['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $f_name = htmlentities($row['first_name']);
    $l_name = htmlentities($row['last_name']);
    $email = htmlentities($row['email']);
    $headline = htmlentities($row['headline']);
    $summary = htmlentities($row['summary']);

    $stmt_pos = $pdo->prepare("SELECT year, description FROM position WHERE profile_id = :profile_id ORDER BY rank");
    $stmt_pos->execute(array(":profile_id" => $_REQUEST['profile_id']));
    $positions = $stmt_pos->fetchall(PDO::FETCH_ASSOC);

    $sql_edu = "SELECT education.rank, education.year, institution.name FROM education JOIN institution ON institution.institution_id = education.institution_id WHERE profile_id = :profile_id ORDER BY rank";
    $stmt_edu = $pdo->prepare($sql_edu);
    $stmt_edu->execute(array(":profile_id" => $_REQUEST['profile_id']));
    $edu = $stmt_edu->fetchall(PDO::FETCH_ASSOC);

    ?>
    <p>
        <form method="post" action="edit.php">
        <input type="hidden" name="profile_id" id="profile_id" value="<?= htmlentities($_GET['profile_id']); ?>"><br/>
    <p>
        First name:
        <input type="text" name="first_name" id="first_name" value="<?= $f_name ?>"><br/>
    </p>
    <p>
        Last name:
        <input type="text" name="last_name" id="last_name" value="<?= $l_name ?>"><br/>
    </p>
    <p>
        Email:
        <input type="text" name="email" id="email" value="<?= $email ?>"><br/>
    </p>
    <p>
        Headline:
        <input type="text" name="headline" id="headline" value="<?= $headline ?>"><br/>
    </p>
    <p>
        Summary:<br/>
        <textarea name="summary" rows="8" cols="80"><?= $summary ?></textarea><br/>
    </p>
    <p>
        Education:
        <input type="submit" id="addEdu" value="+">
    </p>
    <div id = "education_fields">
        <?php
        $edu_pos = 0;
        if (!empty($edu)){
            foreach ($edu as $posit) {
                $edu_pos++;
                echo ('<div id = "education_field'.$edu_pos.'">');
                echo ("<p>");
                echo ("Year: ");
                echo ('<input type="text" name="edu_year'.$edu_pos.'" value="'.$posit["year"].'">');
                echo ('<input type="button" value="-" onclick="$(\'#education_field'.$edu_pos.'\').remove(); return false;">');
                echo ("</p>");
                echo ("<p>");
                echo ('<input type="text" size="70" name="edu_school'.$edu_pos.'" class="school" value="'.$posit['name'].'">');
                echo ("</p>");
                echo ('</div>');
            }
        }
        ?>
    </div>
    <p>
        Position:
        <input type="submit" id="addPos" value="+">
    </p>
    <div id = "position_fields">
        <?php
        $pos = 0;
        if (!empty($positions)){
            foreach ($positions as $posit) {
                $pos++;
                echo ('<div id = "position'.$pos.'">');
                echo ("<p>");
                echo ("Year: ");
                echo ('<input type="text" name="year'.$pos.'" value="'.$posit["year"].'">');
                echo ('<input type="button" value="-" onclick="$(\'#position'.$pos.'\').remove(); return false;">');
                echo ("</p>");
                echo ('<textarea name = "desc'.$pos.'" rows="8" cols = "80">');
                echo (htmlentities($posit['description']));
                echo ('</textarea>');
                echo ('</div>');
            }
        }
        ?>
    </div>
    <p>
        <input type="submit" value="Save">
        <input type="submit" name="cancel" value="Cancel">
    </p>
    </form>
    <script>
        countPos = <?= $pos?>;
        countEdu = <?= $edu_pos?>;

        $(document).ready(function(){
            $('#addPos').click(function(event){
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

            $('#addEdu').click(function(event){
                event.preventDefault();
                if ( countEdu >= 9 ) {
                    alert("Maximum of nine education entries exceeded");
                    return;
                }
                countEdu++;
                var source  = $("#edu-template").html();
                $('#education_fields').append(source.replace(/@COUNT@/g,countEdu));
                $('.school').autocomplete({
                    source: "school.php"
                });

            });

            $('.school').autocomplete({
                source: "school.php"
            });

        });
    </script>
    <script id="edu-template" type="text">
        <div id="education_field@COUNT@">
        <p>Year: <input type="text" name="edu_year@COUNT@" value="" />
        <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;"><br>
        <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value="" />
        </p>
        </div>
    </script>
    </p>
</div>
</body>
</html>