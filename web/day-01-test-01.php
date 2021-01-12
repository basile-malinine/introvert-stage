<?php
require_once(__DIR__ . '/../vendor/autoload.php');

// Проверка GET-запроса
if (!isset($_GET['date_from']) || !isset($_GET['date_to']))
    die('В запросе не задан период');

// Заголовки таблицы
echo "<table border='0' cellspacing='1' cellpadding='10' style='background-color: #000000; text-align: center;'>";
echo "<tr>";
echo "<td width='150px' style='font-weight: bold; background-color: #9FD2DF'>ID Клиента (id)</td>";
echo "<td width='300px' style='font-weight: bold; background-color: #9FD2DF'>Название Клиента (name)</td>";
echo "<td width='300px' style='font-weight: bold; background-color: #9FD2DF'>Сумма сделок</td>";
echo "</tr>";

// Итого по всем Клиентам
$sumTotal = 0;
// Получение массива Клиентов
$clients = getClients();
foreach ($clients as $client) {
    $total = 0;
    // Создание api для Клиента
    Introvert\Configuration::getDefaultConfiguration()
        ->setApiKey('key', $client['api']);
    $api = new Introvert\ApiClient();
    // Новая строка в таблице
    echo "<tr style='text-align: right; background-color: #F0F0F0'>";
    echo "<td>{$client['id']}</td><td>{$client['name']}</td>";
    // Попытка получить данные
    try {
        $total =  totalBudgetPeriodByClient($_GET['date_from'], $_GET['date_to'], $api);
        $sumTotal += $total;
        // ...если всё хорошо, в последней колонке итог по Клиенту
        echo "<td>{$total}</td>";
    }
    catch (Exception $e) {
        // ...если ошибка, вывод как итог по Клиенту
        echo "<td style='text-align: center; color: #CC0000'>{$e->getMessage()}</td>";
    }
    echo "</tr>";
}
// Итоговая строка таблицы
echo "<tr style='text-align: right; font-weight: bold; background-color: #F0F0F0'>";
echo "<td colspan='2'>Итого:</td><td>{$sumTotal}</td>";
echo "</tr>";
echo "</table>";


/**
 * @param int $startPeriod - начало периода
 * @param int $endPeriod - конец периода
 * @param \Introvert\ApiClient $apiClient
 * @return int
 */
function totalBudgetPeriodByClient($startPeriod, $endPeriod, $apiClient)
{
    // Итог по клиенту
    $totalPrices = 0;
    // Кол-во записей в запросе
    $count = 250;
    $offset = 0;
    // Цикл запросов (выход по break;)
    while (true) {
//        try {
            $leads = $apiClient->lead->getAll(null, [142], null, null, $count, $offset);
            foreach ($leads['result'] as $lead) {
                // Если попадает в период, добавить в бюджет
                if ($lead['date_close'] >= $startPeriod
                    && $lead['date_close'] <= $endPeriod) {
                    $totalPrices += (integer) $lead['price'];
                }
            }
            // Расчёт смещения
            $offset += $count;
            // Если кол-во записей в выдаче меньше запроса, выход из цикла запросов
            if ($leads['count'] < $count) break;
//        } catch (Exception $e) {
//            die("\nError {$e->getCode()}: {$e->getMessage()}");
//        }
    }
    return $totalPrices;
}

function getClients() {
    return [
        [
            'id' => 1,
            'name' => 'intrdev',
            'api' => '23bc075b710da43f0ffb50ff9e889aed'
        ],
        [
            'id' => 2,
            'name' => 'artedegrass0',
            'api' => '',
        ],
    ];
}
?>