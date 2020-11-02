<?php

const ROWS_LIMIT = 10000;

if (sizeof($argv) != 3) {
    echo "Неверно переданы аргументы" . PHP_EOL;
    echo "арг1 -- название цвета. Например: red" . PHP_EOL;
    echo "арг2 -- процент, как целочисленное значение. Например: 10 или -5" . PHP_EOL;
    exit;
}

$color = (string)$argv[1];
$percent = (int)$argv[2];

$mysql = new mysqli('localhost', 'admin', '123', 'boxberry');

if ($mysql->connect_errno) {
    echo "Ошибка соединения к MySQL: " . $mysql->cconnect_error . PHP_EOL;
    exit;
}

$recordsCnt = getRecordsCount($mysql, $color);
$iterateCnt = ceil($recordsCnt/10000);
var_dump($iterateCnt);
$updateParams['color'] = $color;
$updateParams['percent'] = $percent;
$updateParams['iterateCnt'] = (int)$iterateCnt;

doUpdatePrice($mysql, $updateParams);

$mysql->close();

function getRecordsCount($mysql, $color)
{
    $prepareQuery = $mysql->prepare("SELECT count(*) AS cnt FROM products WHERE color = ?");
    if (!$prepareQuery) {
        echo "Не удалось подготовить запрос: " . $mysql->error . PHP_EOL;
        exit;
    }

    if (!$prepareQuery->bind_param("s", $color)) {
        echo "Не удалось привязать запрос: " . $prepareQuery->error . PHP_EOL;
        exit;
    }

    if (!$prepareQuery->execute()) {
        echo "Ошибка выполнения запроса: " . $prepareQuery->error . PHP_EOL;
        exit;
    }

    $queryResult = $prepareQuery->get_result();
    $recordsCnt = $queryResult->fetch_assoc();

    $prepareQuery->close();

    return (int)$recordsCnt['cnt'];
}

function doUpdatePrice($mysql, $updateParams)
{   
    $prepareUpdate = $mysql->prepare("UPDATE products SET price=price + (price * (?)/100) WHERE color=? LIMIT 1000");
    $prepareUpdate->bind_param("is", $updateParams['percent'], $updateParams['color']);

    for ($i = 0; $i < $updateParams['iterateCnt']; $i++) {
        $prepareUpdate->execute();
        echo "Итерация $i" . PHP_EOL;
    }

    $prepareUpdate->close();

    return;
}


