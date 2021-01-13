<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Itrovert-Training</title>
    <link href="styles/main.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="indexes">
        <div class="indexItem">
            <p>01. ТЗ №1 Сумма по сделкам Клиента за период</p>
            <form action="day-01-test-01.php" method="get" target="_blank">
                <div>
                    <label for="inp_date_from">Начало периода:</label>
                    <input type="date" id="inp_date_from" value="2018-01-01">
                    <!-- Поле для отправки в GET-запросе --!>
                    <input type="hidden" id="date_from" name="date_from" value="0">

                    <label for="inp_date_to">Конец периода:</label>
                    <input type="date" id="inp_date_to" value="2018-12-31">
                    <!-- Поле для отправки в GET-запросе --!>
                    <input type="hidden" id="date_to"  name="date_to" value="0">
                </div>
                <div>
                    <input type="submit" value="Проверка..."  onclick="test01SetParams()">
                </div>
            </form>
        </div>
    </div>
<script type="text/javascript">
    // Заполняет поля для отправки GET-запроса
    function test01SetParams() {
        let dFrom = new Date(document.getElementById('inp_date_from').value).getTime();
        let dTo = new Date(document.getElementById('inp_date_to').value).getTime();
        document.getElementById('date_from').value = dFrom / 1000;
        document.getElementById('date_to').value = dTo / 1000;
    }
</script>
</body>
</html>
