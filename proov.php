<?php
function past($h, $m, $s) {
    $hours = $h * 3600000;
    $min = $m * 60000;
    $sec = $s * 1000;
    $sum = $hours + $min + $sec;
    return $sum;
}

function getCount($str) {
    $vowelsCount = 0;
    $str_array = count_chars($str);
    if ($str_array[97] > 0) {
        $vowelsCount = $vowelsCount + $str_array[97];
    }
    if ($str_array[101] > 0) {
        $vowelsCount = $vowelsCount + $str_array[101];
    }
    if ($str_array[105] > 0) {
        $vowelsCount = $vowelsCount + $str_array[105];
    }
    if ($str_array[111] > 0) {
        $vowelsCount = $vowelsCount + $str_array[111];
    }
    if ($str_array[117] > 0) {
        $vowelsCount = $vowelsCount + $str_array[117];
    }
    return $vowelsCount;
}

function sum(array $a): float {
    $sum = 0;
    foreach ($a as $value) {
        $sum = $sum + $value;
    }
    return $sum;
}

function nbYear($p0, $percent, $aug, $p) {
    $count = 0;
    while ($p0 <= $p) {
        $p0 = $p0 * ($percent/100 + 1) + $aug;
        $count++;
    }
    return $count;
}

function find_uniq($a) {
    $counted = array_count_values($a);
    foreach ($counted as $value)
        if ($value == 1) {
            //return $value;
        }
    return $counted;
}

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
        <?php echo past(0, 1, 1); ?> <br>
        <?php print_r(getCount("abracadabra")); ?>
        <?php print_r(sum([1, 5.2, 4, 0, -1])); ?>
        <?php print_r(find_uniq([0, 0, 0.55, 0, 0])); ?>
    </p>
