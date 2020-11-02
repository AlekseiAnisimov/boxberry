<?php

const FIND_DAY_NAME = "Tue";

if (sizeof($argv) != 3) {
    echo "Необходимо передать аргументы: начало и конец периода в формате 'yyyy-mm-dd'" . PHP_EOL;
    echo "Проверьте передавамые аргументы и попробуйте снова" . PHP_EOL;
    exit;
}

try {
    $start = new DateTime($argv[1]);
    $end = new DateTime($argv[2]);
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

$interval = new DateInterval('P1D');
$days = new DatePeriod($start, $interval, $end);

$findDayCnt = 0;
foreach ($days as $day) {
    $dayName = $day->format('D');
    if ($dayName == FIND_DAY_NAME) {
        $findDayCnt++;
    }
}

print($findDayCnt);