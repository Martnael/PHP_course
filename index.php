<?php
session_start();
if ( ! isset($_SESSION["name"]) || ! isset($_SESSION["user_id"])) {
    include ("index1.php");
} else {
    include ("index2.php");
}
?>