<?php
require_once(__DIR__ . '/../vendor/autoload.php');

// Проверка GET-запроса
if (!isset($_GET['date_from']) || !isset($_GET['date_to']))
    die('В запросе не задан период');

$beginDate = (int)$_GET['date_from'];
$endDate = (int)$_GET['date_to'];

// Перевод Timestamp в DateTime формат для отображения в заголовке
$dateFrom = new DateTime(); $dateFrom->setTimestamp($beginDate);
$dateTo = new DateTime(); $dateTo->setTimestamp($endDate);

// Заголовки таблицы
echo "<table border='0' cellspacing='1' cellpadding='10' style='background-color: #000000; text-align: center;'>";
echo "<tr style='font-weight: bold; background-color: #9FD2DF'>";
echo "<td colspan='3'>Отчёт по закрытым сделкам (со статусом 142) за период с {$dateFrom->format('d.m.Y')} по {$dateTo->format('d.m.Y')}</td>";
echo "</tr>";
echo "<tr style='font-weight: bold; background-color: #9FD2DF'>";
echo "<td width='150px'>ID Клиента (id)</td>";
echo "<td width='300px'>Название Клиента (name)</td>";
echo "<td width='300px'>Сумма сделок</td>";
echo "</tr>";

// Итого по всем Клиентам
$sumTotal = 0;
// Получение массива Клиентов
$clients = getClients();
foreach ($clients as $client) {
    // Итоги по Клиенту
    $total = 0;
    // Создание экземпляра API для Клиента
    Introvert\Configuration::getDefaultConfiguration()->setApiKey('key', $client['api']);
    $api = new Introvert\ApiClient();
    // Новая строка в таблице
    echo "<tr style='text-align: right; background-color: #F0F0F0'>";
    echo "<td>{$client['id']}</td><td>{$client['name']}</td>";
    // Попытка получить итоги по Клиенту
    try {
        $total =  totalBudgetPeriodByClient($beginDate, $endDate, $api);
        $sumTotal += $total;
        // ...если всё хорошо, в последней колонке итог по Клиенту
        echo "<td>"
            . number_format($total, 2, '.', ' ')
            . "</td>";
    }
    catch (Exception $e) {
        // ...если ошибка, вывод как итог по Клиенту
        echo "<td style='text-align: center; color: #CC0000'>{$e->getMessage()}</td>";
    }
    echo "</tr>";
}
// Итоговая строка таблицы
echo "<tr style='text-align: right; font-weight: bold; background-color: #F0F0F0'>";
echo "<td colspan='2'>Итого:</td><td>"
    . number_format($sumTotal, 2, '.', ' ')
    . "</td>";
echo "</tr>";
echo "</table>";


/**
 * @param int $startPeriod - Начало периода
 * @param int $endPeriod - Конец периода
 * @param \Introvert\ApiClient $apiClient - Объект API для Клиента
 * @return int - Итоговая сумма по всем закрытым сделкам (статус 142)
 */
function totalBudgetPeriodByClient($startPeriod, $endPeriod, $apiClient)
{
    // Итоги по Клиенту
    $totalPrices = 0;
    // Кол-во записей в запросе
    $count = 250;
    $offset = 0;
    // Цикл запросов (выход по break;)
    while (true) {
        // Получение Сделок на статусе 142 по Клиенту
        $leads = $apiClient->lead->getAll(null, [142], null, null, $count, $offset);
        foreach ($leads['result'] as $lead) {
            // Если попадает в период, добавить в бюджет
            if ($lead['date_close'] >= $startPeriod
                && $lead['date_close'] <= $endPeriod) {
                $totalPrices += (int)$lead['price'];
            }
        }
        // Расчёт смещения
        $offset += $count;
        // Если кол-во записей в выдаче меньше запроса, выход из цикла запросов
        if ($leads['count'] < $count) break;
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
