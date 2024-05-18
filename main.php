<!DOCTYPE html>
<html>
<head>
    <title>Управление ветеранами</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<h2>Данные о ветеранах</h2>
<?php
// Подключение к базе данных
$dbconn = pg_connect("host=localhost dbname=php user=postgres password=12345")
or die('Could not connect: ' . pg_last_error());

// Запрос для получения данных о ветеранах
$query = "SELECT * FROM veterans";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

// Вывод данных в виде таблицы
echo "<table>";
echo "<tr><th>ID</th><th>Фамилия</th><th>Имя</th><th>Отчество</th><th>Город</th><th>Вид спорта</th><th>Возрастная группа</th></tr>";
while ($row = pg_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['id_veteran'] . "</td>";
    echo "<td>" . $row['surname'] . "</td>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td>" . $row['thirdname'] . "</td>";
    echo "<td>" . $row['city'] . "</td>";

    // Запрос для получения названия вида спорта
    $sport_query = "SELECT name FROM sports WHERE id_sport = " . $row['id_sport'];
    $sport_result = pg_query($sport_query);
    $sport_row = pg_fetch_assoc($sport_result);
    echo "<td>" . $sport_row['name'] . "</td>";

    // Запрос для получения названия возрастной группы
    $age_group_query = "SELECT age_group FROM age_groups WHERE id_age_group = " . $row['id_age_group'];
    $age_group_result = pg_query($age_group_query);
    $age_group_row = pg_fetch_assoc($age_group_result);
    echo "<td>" . $age_group_row['age_group'] . "</td>";

    echo "</tr>";
}
echo "</table>";

// Освобождаем результат запроса
pg_free_result($result);

// Закрываем соединение с базой данных
pg_close($dbconn);
?>

<h2>Добавление нового ветерана</h2>
<form method="post" action="add_veteran.php">
    Фамилия: <input type="text" name="surname"><br>
    Имя: <input type="text" name="name"><br>
    Отчество: <input type="text" name="thirdname"><br>
    Город: <input type="text" name="city"><br>
    Вид спорта:
    <select name="sport">
        <?php
        // Подключение к базе данных
        $dbconn = pg_connect("host=localhost dbname=php user=postgres password=12345")
        or die('Could not connect: ' . pg_last_error());

        // Запрос для получения всех видов спорта
        $sport_query = "SELECT * FROM sports";
        $sport_result = pg_query($sport_query) or die('Query failed: ' . pg_last_error());

        // Вывод вариантов в выпадающий список
        while ($row = pg_fetch_assoc($sport_result)) {
            echo "<option value='" . $row['id_sport'] . "'>" . $row['name'] . "</option>";
        }

        // Освобождаем результат запроса
        pg_free_result($sport_result);

        // Закрываем соединение с базой данных
        pg_close($dbconn);
        ?>
    </select><br>
    Возрастная группа:
    <select name="age_group">
        <?php
        // Подключение к базе данных
        $dbconn = pg_connect("host=localhost dbname=php user=postgres password=12345")
        or die('Could not connect: ' . pg_last_error());

        // Запрос для получения всех возрастных групп
        $age_group_query = "SELECT * FROM age_groups";
        $age_group_result = pg_query($age_group_query) or die('Query failed: ' . pg_last_error());

        // Вывод вариантов в выпадающий список
        while ($row = pg_fetch_assoc($age_group_result)) {
            echo "<option value='" . $row['id_age_group'] . "'>" . $row['age_group'] . "</option>";
        }

        // Освобождаем результат запроса
        pg_free_result($age_group_result);

        // Закрываем соединение с базой данных
        pg_close($dbconn);
        ?>
    </select><br>
    <input type="submit" value="Добавить ветерана">
</form>
</body>
</html>
