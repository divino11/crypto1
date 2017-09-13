function show() {
    $.ajax({
        url: "sellandbuy.php",
        cache: false,
        complete:  function Start(){ setTimeout(show,1000);},
        success: function (html) {
            $("#sell_and_buy").html(html);
        }
    });
}

$(document).ready(function () {
    show();
    return false;
});



function balance_show() {
    $.ajax({
        url: "balance.php",
        cache: false,
        complete:  function Start(){ setTimeout(balance_show,2000);},
        success: function (html) {
            $("#balance").html(html);
        }
    });
}

$(document).ready(function () {
    balance_show();
return false;
});

function wall_show() {
    $.ajax({
        url: "wall.php",
        cache: false,
        complete:  function Start(){ setTimeout(wall_show,2000);},
        success: function (html) {
            $("#wall").html(html);
        }
    });
}

$(document).ready(function () {
    wall_show();
    return false;
});

$(document).ready(function () {
    $("#btn1").click(
        function () {
            sendAjaxFormBuySell('result_form', 'form_rand', 'buysell.php');
            return false;
        }
    );
});

function sendAjaxFormBuySell(result_form, ajax_form, url) {
    jQuery.ajax({
        url: url, //url страницы (action_ajax_form.php)
        type: "GET", //метод отправки
        dataType: "html", //формат данных
        data: jQuery("#" + ajax_form).serialize(),  // Сеарилизуем объект
        success: function (response) { //Данные отправлены успешно
            document.getElementById(result_form).innerHTML = "Запрос отправлен";
        },
        error: function (response) { // Данные не отправлены
            document.getElementById(result_form).innerHTML = "Ошибка. Данные не отправленны.";
        }
    });
}

$(document).ready(function () {
    $("#btn_percent").click(
        function () {
            sendAjaxFormPercent('result_percent', 'form_percent', 'percent.php');
return false;
        }
    );
});

function sendAjaxFormPercent(result_percent, form_percent, url1) {
    jQuery.ajax({
        url: url1, //url страницы (action_ajax_form.php)
        type: "GET", //метод отправки
        dataType: "html", //формат данных
        data: jQuery("#" + form_percent).serialize(),  // Сеарилизуем объект
        complete:  function Start(){ sendAjaxFormPercent('result_percent', 'form_percent', 'percent.php');},
        success: function (response) { //Данные отправлены успешно
            document.getElementById(result_percent).innerHTML = "Запрос отправлен";
        },
        error: function (response) { // Данные не отправлены
            document.getElementById(result_percent).innerHTML = "Ошибка. Данные не отправленны.";
        }
    });
}

$(document).ready(function () {
    $("#btn_auto").click(
        function () {
            sendAjaxFormAuto('auto_result', 'form_auto', 'set_auto_buy.php');
            return false;
        }
    );
});

function sendAjaxFormAuto(auto_result, auto_form, url2) {
    jQuery.ajax({
        url: url2, //url страницы (action_ajax_form.php)
        type: "GET", //метод отправки
        dataType: "html", //формат данных
        data: jQuery("#" + auto_form).serialize(),  // Сеарилизуем объект
        //complete:  function Start(){ sendAjaxFormAuto('auto_result', 'form_auto', 'set_auto_buy.php');},
        //success: function (response) { //Данные отправлены успешно
         //   document.getElementById(auto_result).innerHTML = "Запрос отправлен";
        //},
        error: function (response) { // Данные не отправлены
            document.getElementById(auto_result).innerHTML = "Ошибка. Данные не отправленны.";
        }
    });
}


$(document).ready(function () {
    $("#btn_wall_orders").click(
        function () {
            sendAjaxFormWall('result_wall_orders', 'form_wall_orders', 'wall_orders.php');
            return false;
        }
    );
});

function sendAjaxFormWall(auto_result, auto_form, url2) {
    jQuery.ajax({
        url: url2, //url страницы (action_ajax_form.php)
        type: "GET", //метод отправки
        dataType: "html", //формат данных
        data: jQuery("#" + auto_form).serialize(),  // Сеарилизуем объект
        success: function (response) { //Данные отправлены успешно
            document.getElementById(auto_result).innerHTML = "Запрос отправлен";
        },
        error: function (response) { // Данные не отправлены
            document.getElementById(auto_result).innerHTML = "Ошибка. Данные не отправленны.";
        }
    });
}
$(document).ready(function () {
    $("#btn_eat_order").click(
        function  () {
            sendAjaxFormEat('result_eat_order', 'form_eat_order', 'eat_order.php');
            return false;
        }
    );
});

function sendAjaxFormEat(auto_result, auto_form, url2) {
    jQuery.ajax({
        url: url2, //url страницы (action_ajax_form.php)
        type: "GET", //метод отправки
        dataType: "html", //формат данных
        complete:  function StartEAT(){ sendAjaxFormEat('result_eat_order', 'form_eat_order', 'eat_order.php');},
        data: jQuery("#" + auto_form).serialize(),  // Сеарилизуем объект
        success: function (response) { //Данные отправлены успешно
            document.getElementById(auto_result).innerHTML = "Запрос отправлен";
        },
        error: function (response) { // Данные не отправлены
            document.getElementById(auto_result).innerHTML = "Ошибка. Данные не отправленны.";
        }
    });
}

$(document).ready(function () {
    $("#btn_less_sell").click(
        function  () {
            sendAjaxFormSell('result_form', 'form_rand', 'less_sell.php');
            return false;
        }
    );
});

function sendAjaxFormSell(auto_result, auto_form, url2) {
    jQuery.ajax({
        url: url2, //url страницы (action_ajax_form.php)
        type: "GET", //метод отправки
        dataType: "html", //формат данных
        data: jQuery("#" + auto_form).serialize(),  // Сеарилизуем объект
        success: function (response) { //Данные отправлены успешно
            document.getElementById(auto_result).innerHTML = "Запрос отправлен";
        },
        error: function (response) { // Данные не отправлены
            document.getElementById(auto_result).innerHTML = "Ошибка. Данные не отправленны.";
        }
    });
}

$(document).ready(function () {
    $("#btn_fake_wall").click(
        function () {
            sendAjaxFormFake('result_fake_wall', 'form_fake_wall', 'fake_wall.php');
            return false;
        }
    );
});

function sendAjaxFormFake(result_fake_wall, form_fake_wall, url2) {
    jQuery.ajax({
        url: url2, //url страницы (action_ajax_form.php)
        type: "GET", //метод отправки
        dataType: "html", //формат данных
        data: jQuery("#" + form_fake_wall).serialize(),  // Сеарилизуем объект
        complete:  function Start(){ sendAjaxFormFake('result_fake_wall', 'form_fake_wall', 'fake_wall.php');},
        success: function (response) { //Данные отправлены успешно
            document.getElementById(result_fake_wall).innerHTML = "Запрос отправлен";
        },
        error: function (response) { // Данные не отправлены
            document.getElementById(result_fake_wall).innerHTML = "Ошибка. Данные не отправленны.";
        }
    });
}