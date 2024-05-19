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
$dbconn = pg_connect("host=localhost dbname=php user=postgres password=12345") or die('Could not connect: '. pg_last_error());

// Запрос для получения данных о ветеранах
$query = "SELECT * FROM veterans";
$result = pg_query($query) or die('Query failed: '. pg_last_error());

// Вывод данных в виде таблицы
echo "<table>";
echo "<tr><th>ID</th><th>Фамилия</th><th>Имя</th><th>Отчество</th><th>Город</th><th>Вид спорта</th><th>Возрастная группа</th></tr>";
while ($row = pg_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>". $row['id_veteran']. "</td>";
    echo "<td>". $row['surname']. "</td>";
    echo "<td>". $row['name']. "</td>";
    echo "<td>". $row['thirdname']. "</td>";
    echo "<td>". $row['city']. "</td>";

    // Запрос для получения названия вида спорта
    $sport_query = "SELECT name FROM sports WHERE id_sport = ". $row['id_sport'];
    $sport_result = pg_query($sport_query);
    $sport_row = pg_fetch_assoc($sport_result);
    echo "<td>". $sport_row['name']. "</td>";

    // Запрос для получения названия возрастной группы
    $age_group_query = "SELECT age_group FROM age_groups WHERE id_age_group = ". $row['id_age_group'];
    $age_group_result = pg_query($age_group_query);
    $age_group_row = pg_fetch_assoc($age_group_result);
    echo "<td>". $age_group_row['age_group']. "</td>";

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
        $dbconn = pg_connect("host=localhost dbname=php user=postgres password=12345") or die('Could not connect: '. pg_last_error());

        // Запрос для получения всех видов спорта
        $sport_query = "SELECT * FROM sports";
        $sport_result = pg_query($sport_query) or die('Query failed: '. pg_last_error());

        // Вывод вариантов в выпадающий список
        while ($row = pg_fetch_assoc($sport_result)) {
            echo "<option value='". $row['id_sport']. "'>". $row['name']. "</option>";
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
        $dbconn = pg_connect("host=localhost dbname=php user=postgres password=12345") or die('Could not connect: '. pg_last_error());

        // Запрос для получения всех возрастных групп
        $age_group_query = "SELECT * FROM age_groups";
        $age_group_result = pg_query($age_group_query) or die('Query failed: '. pg_last_error());

        // Вывод вариантов в выпадающий список
        while ($row = pg_fetch_assoc($age_group_result)) {
            echo "<option value='". $row['id_age_group']. "'>". $row['age_group']. "</option>";
        }

        // Освобождаем результат запроса
        pg_free_result($age_group_result);

        // Закрываем соединение с базой данных
        pg_close($dbconn);
        ?>
    </select><br>
    <input type="submit" value="Добавить ветерана">
</form>

<h2>Число ветеранов по каждому виду спорта</h2>
<form method="post" action="">
    <input type="hidden" name="action" value="getSportVeteransCount">
    <button type="submit">Получить данные</button>
</form>

<?php
if (isset($_POST['action']) && $_POST['action'] == 'getSportVeteransCount') {
    // Подключение к базе данных
    $dbconn = pg_connect("host=localhost dbname=php user=postgres password=12345") or die('Could not connect: '. pg_last_error());

    // SQL-запрос для получения количества ветеранов по каждому виду спорта
    $query = "SELECT s.name AS sport, COUNT(v.id_veteran) AS veteran_count FROM sports s LEFT JOIN veterans v ON s.id_sport = v.id_sport GROUP BY s.name;";
    $result = pg_query($query) or die('Query failed: '. pg_last_error());

    // Вывод результата в виде таблицы
    echo "<table>";
    echo "<tr><th>Спорт</th><th>Количество ветеранов</th></tr>";
    while ($row = pg_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>". $row['sport']. "</td>";
        echo "<td>". $row['veteran_count']. "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Освобождаем результат запроса
    pg_free_result($result);

    // Закрываем соединение с базой данных
    pg_close($dbconn);
}
?>

</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Управление ветеранами</title>
    <style>
        /* Стили */
    </style>
</head>
<body>
<!-- Ваши существующие разделы -->

<h2>Список ветеранов спорта</h2>
<form method="post" action="">
    <input type="hidden" name="action" value="listVeteransBySport">
    <button type="submit">Получить список</button>
</form>

<?php
if (isset($_POST['action']) && $_POST['action'] == 'listVeteransBySport') {
    // Подключение к базе данных
    $dbconn = pg_connect("host=localhost dbname=php user=postgres password=12345") or die('Could not connect: '. pg_last_error());

    // SQL-запрос для получения списка ветеранов, сгруппированных по виду спорта
    $query = "SELECT 
            s.name AS sport_name,
            v.surname || ' ' || v.name || ' ' || v.thirdname AS full_name,
            v.city,
            ag.age_group,
            COUNT(*) AS veterans_count
          FROM 
            veterans v
          JOIN 
            sports s ON v.id_sport = s.id_sport
          JOIN 
            age_groups ag ON v.id_age_group = ag.id_age_group
          GROUP BY 
            s.name, v.surname, v.name, v.thirdname, v.city, ag.age_group
          ORDER BY
            s.name";

    $result = pg_query($query) or die('Query failed: ' . pg_last_error());

// Вывод результата
    echo "<table>";
    echo "<tr><th>Спорт</th><th>ФИО спортсмена</th><th>Город</th><th>Возрастная группа</th><th>Количество ветеранов</th></tr>";
    while ($row = pg_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['sport_name'] . "</td>";
        echo "<td>" . $row['full_name'] . "</td>";
        echo "<td>" . $row['city'] . "</td>";
        echo "<td>" . $row['age_group'] . "</td>";
        echo "<td>" . $row['veterans_count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";


    // Освобождаем результат запроса
    pg_free_result($result);

    // Закрываем соединение с базой данных
    pg_close($dbconn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterans List</title>
</head>
<body>
<h2>Veterans List</h2>
<form action="" method="POST">
    <label for="limit">Limit:</label>
    <input type="number" id="limit" name="limit" value="10" min="1">
    <input type="submit" value="Show">
</form>
<br>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение значения LIMIT из формы
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;

    // Установка соединения с базой данных
    $dbconn = pg_connect("host=localhost dbname=php user=postgres password=12345")
    or die('Could not connect: ' . pg_last_error());

    // Запрос к базе данных с использованием LIMIT
    $query = "SELECT 
                    v.surname,
                    ag.age_group,
                    v.city,
                    s.name AS sport_name
                  FROM 
                    veterans v
                  JOIN 
                    sports s ON v.id_sport = s.id_sport
                  JOIN 
                    age_groups ag ON v.id_age_group = ag.id_age_group
                  ORDER BY 
                    v.surname
                  LIMIT 
                    $limit";

    // Вывод результата
    echo "<table>";
    echo "<tr><th>Surname</th><th>Age Group</th><th>City</th><th>Sport</th></tr>";

    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    while ($row = pg_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['surname'] . "</td>";
        echo "<td>" . $row['age_group'] . "</td>";
        echo "<td>" . $row['city'] . "</td>";
        echo "<td>" . $row['sport_name'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Закрытие соединения с базой данных
    pg_close($dbconn);
}
?>
</body>
</html>

