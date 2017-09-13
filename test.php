<?php
require_once 'functions.php';
echo "<link rel='stylesheet' href='style.css'>";
echo "<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css\" integrity=\"sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M\" crossorigin=\"anonymous\">";
session_start();
?>
<html>
<head>
    <title>test</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

</head>
<body>
<div class="border_sell"></div>
<div id="balance_fix">
        <div id="balance"></div>
    <br><br>
</div>
<button class="btn logs" id="log"><a href="history.php" target="_blank">Логи</a></button>
        <div class="container">
    <form action="test.php" name="min_max_order" method="get">
        <select name="currencyPair" class="selectpicker" id="currencyPair" data-live-search="true">
            <option value="DOT_BTC">DOT_BTC</option>
            <option value="BTC_USDT">BTC_USDT</option>
            <option value="DOGE_BTC">DOGE_BTC</option>
            <option value="LTC_BTC">LTC_BTC</option>
            <option value="ETH_BTC">ETH_BTC</option>
            <option value="DGC_BTC">DGC_BTC</option>
        </select>
        <button type="submit" class="btn">Выбрать</button>
        Вывести стенку: <input type="text" name="wall1" class="input_field">
        <button type="submit" class="btn">Выбрать</button>
    </form>
    <form  name="eat_order" id="form_eat_order" method="get">
        <h3>Сожрать офер</h3>
        Цена: <input type="text" class="input_field" name="fixed_price">
        Интервал: <input type="text" class="input_field" name="interval_time">
        Количество запросов: <input type="text" class="input_field" name="count_quer">
        <select name="buy_sell" id="buy">
            <option value="Buy">Buy</option>
            <option value="Sell">Sell</option>
        </select>
        <button type="submit" class="btn" id="btn_eat_order">Выбрать</button>
        <div id="result_eat_order"></div>
    </form>
        </div>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
    <form class="forms" method="get" id="form_wall_orders">
        <h3>Покупка/продажа в стенку</h3>
        От: <input type="text" class="input_field" name="min_order" placeholder="0.00000000">
        <br><br> До: <input type="text" class="input_field" name="max_order" placeholder="0.00000000"><br><br>
        Кол-во валюты: <input type="text" class="input_field" name="count_order"><br><br>
        Кол-во запросов: <input type="text" class="input_field" name="count_query"><br><br>
        <select name="buy_sell" id="buy">
            <option value="buy">Buy</option>
            <option value="sell">Sell</option>
        </select>
        <br>
        <button type="submit" name="buyOrSell" class="btn" id="btn_wall_orders">Выбрать</button>
        <div id="result_wall_orders"></div>
    </form>
         </div>
    <form name="min_max_order" id="form_rand" method="get" class="forms col-md-3" action="less_sell.php">
        <h3>Рандомная покупка</h3>
        От: <input type="text" class="input_field" name="min_order_rand" placeholder="0.00000000">
        <br><br> До: <input type="text" class="input_field" name="max_order_rand" placeholder="0.00000000"><br><br>
        Кол-во: <input type="text" class="input_field" name="count_order_rand"><br>
        <select name="buy_sel_rand" id="buy">
            <option value="buy_rand">Buy</option>
            <option value="sell_rand">Sell</option>
        </select><br>
        <button type="submit" class="btn" id="btn1">Рандом</button>

        <button type="submit" name="min_sell" class="btn" id="btn_less_sell">Меньше цены продажи</button>
        <div id="result_form"></div>
    </form>

    <form name="min_max_percent" method="get" id="form_percent" class="forms col-md-3">
        <h3>Покупка за шаг</h3>
        Шаг покупки: <input type="text" class="input_field" name="step_orders" placeholder="0.00000000"><br><br>
        Кол-во покупок: <input type="text" name="count_prices" class="input_field"><br><br>
        Кол-во валюты: <input type="text" name="count_coins" class="input_field"><br><br>
        Разница в %: <input type="text" name="percents" class="input_field"><br><br>
        <button type="submit" class="btn" id="btn_percent">Выбрать</button>
        <div id="result_percent"></div>
        <div id="result_form_error"></div>
    </form>

    <form name="set_interval" method="get" class="forms col-md-3" id="form_auto">
        <h3>Авто-режим</h3>
        Интервал времени <br>
        От: <input type="text" name="interval_time_start" class="input_field"><br><br>
        До: <input type="text" name="interval_time_end" class="input_field"><br><br>
        Кол-во валюты: <input type="text" name="set_count" class="input_field"><br><br>
        <input type="hidden" value="0" name="hide_false">
        <button type="submit" class="btn" id="btn_auto">Выбрать</button>
        <button type="submit" class="btn" id="btn_auto" onclick="var xhr = $.get('set_auto_buy.php'); xhr.abort();">Остановить</button>
        <div id="auto_result"></div>
    </form>
    </div>
</div>
<div class="container-fluid">
    <form name="fake_wall" method="get" id="form_fake_wall">
        <h3>Фейк стенка</h3>
        Проценты: <input type="text" name="percents" class="input_field">
        Кол-во валюты: <input type="text" name="coins_count" class="input_field">
        Кол-во запросов: <input type="text" name="query_count1" class="input_field">
        <select name="buy_sell" id="buy">
            <option value="Buy">Buy</option>
            <option value="Sell">Sell</option>
        </select>
        <button type="submit" class="btn" id="btn_fake_wall">Выбрать</button>
        <div id="result_fake_wall"></div>
    </form>
</div>

    <?php
    $btc = $_GET['currencyPair'];
    $wall_btc = $_GET['wall1'];
    if (!is_array($_SESSION['rez'])) {
        $_SESSION['rez'] = $btc;
        $_SESSION['query_count'] = $_GET['count_query'];
        $_SESSION['wall'] = $wall_btc;
    }
    $val = $_SESSION['rez'];
    $curren_pair = $_SESSION['curr_pair'];
    $_SESSION['rez'];
    echo "<script type='text/javascript'>
     document.getElementById('currencyPair').value = \"{$_SESSION['rez']}\";
     </script>";
    ?>

<!-- Вывод таблиц из ajax-->
<div id="sell_and_buy"></div>
<div id="wall">
</div>
<div id="percent"></div>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script  src="https://code.highcharts.com/modules/exporting.js"></script>

<!-- Вывод графика-->
<?php
$last_buy_price = sprintf('%.8F', $_SESSION['x1']);
$last_sell_price = sprintf('%.8F', $_SESSION['x2']);
?>
<div id="container"></div>
<script>var A = <?php echo $last_buy_price; ?>; var B = <?php echo $last_sell_price; ?>;</script>
<script type="text/javascript">
    Highcharts.chart('container', {
        chart: {
            type: 'spline',
            animation: Highcharts.svg, // don't animate in old IE
            marginRight: 10,
            events: {
                load: function () {
                    // set up the updating of the chart each second
                    var seriesA = this.series[0];
                    var seriesB = this.series[1];
                    setInterval(function () {
                        A = <?php echo $last_buy_price = sprintf('%.8F', $last_buy_price); ?>;
                        B = <?php echo $last_sell_price = sprintf('%.8F', $last_sell_price); ?>;
                        var x = (new Date()).getTime(); // current time
                        if (A) {
                            y1 = A;
                            seriesA.addPoint([x, y1], false, true);
                        }

                        if (B) {
                            y2 = B;
                            seriesB.addPoint([x, y2], true, true);
                        }
                    }, 1000);
                }
            }
        },
        title: {
            text: 'Live random data'
        },
        xAxis: {
            type: 'datetime',
            tickPixelInterval: 150
        },
        yAxis: {
            title: {
                text: 'Value'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueDecimals: 9
            /* formatter: function () {
                 return '<b>' + this.series.name + '</b><br/>' +
                     Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) + '<br/>' +
                     Highcharts.numberFormat(this.y, 2);
             }*/
        },
        legend: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
        series: [{
            name: 'Sell orders',
            data: (function () {
                // generate an array of random data
                var data = [],
                    time = (new Date()).getTime(),
                    i;

                for (i = -100; i <= 0; i += 1) {
                    data.push({
                        x: time + i * 1000,
                        y: A
                    });
                }
                return data;
            }())
        },
            {
                name: 'Buy orders',
                data: (function () {
                    // generate an array of random data
                    var data = [],
                        time = (new Date()).getTime(),
                        i;

                    for (i = -100; i <= 0; i += 1) {
                        data.push({
                            x: time + i * 1000,
                            y: B
                        });
                    }
                    return data;
                }())
            }
        ]
    });
    </script>
<script async src='script.js'></script>
</body>
</html>