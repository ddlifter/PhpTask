<?php
// Подключение к базе данных
$dbconn = pg_connect("host=localhost dbname=php user=postgres password=12345")
or die('Could not connect: ' . pg_last_error());

// Получение данных из формы
$surname = $_POST['surname'];
$name = $_POST['name'];
$thirdname = $_POST['thirdname'];
$city = $_POST['city'];
$sport_id = $_POST['sport'];
$age_group_id = $_POST['age_group'];

// Вставка данных в базу данных
$query = "INSERT INTO veterans (surname, name, thirdname, city, id_sport, id_age_group) VALUES ('$surname', '$name', '$thirdname', '$city', $sport_id, $age_group_id)";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

// Проверка на успешное добавление
if ($result) {
    echo "Ветеран успешно добавлен в базу данных.";
} else {
    echo "Произошла ошибка при добавлении ветерана в базу данных.";
}

// Закрытие соединения с базой данных
pg_close($dbconn);
?>
