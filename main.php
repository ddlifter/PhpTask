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

</head>
<body>

<h2>Список ветеранов спорта с группировкой по спорту</h2>
<form method="post">
    <input type="submit" name="viewData" value="Показать данные">
</form>



<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['viewData'])) {
    // Подключение к базе данных
    $host = 'localhost';
    $dbname = 'php'; // Замените на имя вашей базы данных
    $user = 'postgres'; // Замените на ваше имя пользователя
    $password = '12345'; // Замените на ваш пароль

    try {
        // Создание подключения к базе данных
        $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

        // Определение SQL-запроса с использованием CTE для подсчета ветеранов
        $sql = <<<SQL
WITH veteran_counts AS (
    SELECT 
        s.name AS sport_name, 
        COUNT(*) AS veterans_count
    FROM 
        veterans v
    JOIN 
        sports s ON v.id_sport = s.id_sport
    JOIN 
        age_groups ag ON v.id_age_group = ag.id_age_group
    GROUP BY 
        s.name
)
SELECT 
    vc.sport_name, 
    v.surname, 
    v.name, 
    v.thirdname, 
    v.city, 
    ag.age_group,
    vc.veterans_count
FROM 
    veterans v
JOIN 
    sports s ON v.id_sport = s.id_sport
JOIN 
    age_groups ag ON v.id_age_group = ag.id_age_group
JOIN 
    veteran_counts vc ON s.name = vc.sport_name
ORDER BY 
    vc.sport_name;
SQL;

        // Выполнение запроса
        $statement = $pdo->prepare($sql);
        $statement->execute();

        // Вывод результатов
        echo "<table border='1'>";
        echo "<tr><th>Спорт</th><th>Фамилия</th><th>Имя</th><th>Отчество</th><th>Город</th><th>Возрастная группа</th><th>Количество ветеранов</th></tr>";
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>". htmlspecialchars($row['sport_name']). "</td>";
            echo "<td>". htmlspecialchars($row['surname']). "</td>";
            echo "<td>". htmlspecialchars($row['name']). "</td>";
            echo "<td>". htmlspecialchars($row['thirdname']). "</td>";
            echo "<td>". htmlspecialchars($row['city']). "</td>";
            echo "<td>". htmlspecialchars($row['age_group']). "</td>";
            echo "<td>". htmlspecialchars($row['veterans_count']). "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (PDOException $e) {
        echo "Ошибка подключения к базе данных: ". $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<h2>Список ветеранов с ограничением</h2>
<form action="" method="POST">
    <label for="limit">Limit:</label>
    <input type="number" id="limit" name="limit" min="1">
    <input type="submit" value="Show">
</form>
<br>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение значения LIMIT из формы
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 0;

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

    pg_free_result($result);

// Закрываем соединение с базой данных
    pg_close($dbconn);
}
?>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterans Database</title>
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
<h2>Поиск по фамилии</h2>

<form method="POST">
    <label for="surname">Введите фамилию для поиска:</label>
    <input type="text" id="surname" name="surname">
    <button type="submit">Search</button>
</form>

<?php
try {
    $pdo = new PDO('pgsql:host=localhost;dbname=php', 'postgres', '12345');
} catch (PDOException $e) {
    die("Error: Could not connect. " . $e->getMessage());
}

function searchBySurname($pdo, $surname) {
    $stmt = $pdo->prepare("SELECT * FROM veterans WHERE surname = :surname");
    $stmt->execute(['surname' => $surname]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Если форма отправлена, выполнить поиск по введенной фамилии
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $surname = $_POST['surname'];
    $searchResults = searchBySurname($pdo, $surname);
}
?>

<?php if (!empty($searchResults)): ?>
    <h2>Результаты поиска по фамилии</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Фамилия</th>
            <th>Имя</th>
            <th>Отчество</th>
            <th>Город</th>
            <th>Sport ID</th>
            <th>Age Group ID</th>
        </tr>
        <?php foreach ($searchResults as $row): ?>
            <tr>
                <td><?= $row['id_veteran'] ?></td>
                <td><?= $row['surname'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['thirdname'] ?></td>
                <td><?= $row['city'] ?></td>
                <td><?= $row['id_sport'] ?></td>
                <td><?= $row['id_age_group'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>


<?php endif; ?>

</body>
</html>



<?php
// Код для соединения с базой данных и запросов к ней

// Функция для получения данных ветеранов с сортировкой по фамилии и данными о виде спорта и возрастной группе
function getVeteransWithDetails($pdo) {
    $stmt = $pdo->query("SELECT veterans.id_veteran, veterans.surname, veterans.name, veterans.thirdname, veterans.city, sports.name AS sport_name, age_groups.age_group
                         FROM veterans
                         LEFT JOIN sports ON veterans.id_sport = sports.id_sport
                         LEFT JOIN age_groups ON veterans.id_age_group = age_groups.id_age_group
                         ORDER BY veterans.surname");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получаем данные ветеранов с сортировкой по фамилии и данными о виде спорта и возрастной группе
$veterans = [];

if (isset($_POST['getVeterans'])) {
    $veterans = getVeteransWithDetails($pdo);
}
?>

<form method="post">
    <button type="submit" name="getVeterans">Сортировка</button>
</form>
<?php if (!empty($veterans)): ?>
    <h2>Сортировка по фамилии</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Фамилия</th>
            <th>Имя</th>
            <th>Отчество</th>
            <th>Город</th>
            <th>Вид спорта</th>
            <th>Возрастная группа</th>
        </tr>
        <?php foreach ($veterans as $row): ?>
            <tr>
                <td><?= $row['id_veteran'] ?></td>
                <td><?= $row['surname'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['thirdname'] ?></td>
                <td><?= $row['city'] ?></td>
                <td><?= $row['sport_name'] ?></td>
                <td><?= $row['age_group'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>



<?php
// Код для соединения с базой данных и запросов к ней

// Функция для получения данных ветеранов с сортировкой по фамилии и данными о виде спорта и возрастной группе
function getVeteransWithDetails2($pdo) {
    $stmt = $pdo->query("SELECT veterans.id_veteran, veterans.surname, veterans.name, veterans.thirdname, veterans.city, sports.name AS sport_name, age_groups.age_group
                         FROM veterans
                         LEFT JOIN sports ON veterans.id_sport = sports.id_sport
                         LEFT JOIN age_groups ON veterans.id_age_group = age_groups.id_age_group
                         ORDER BY age_groups.age_group");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получаем данные ветеранов с сортировкой по фамилии и данными о виде спорта и возрастной группе
$veterans = [];

if (isset($_POST['getVeterans'])) {
    $veterans = getVeteransWithDetails2($pdo);
}
?>



<?php if (!empty($veterans)): ?>
    <h2>Сортировка по возрасту</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Фамилия</th>
            <th>Имя</th>
            <th>Отчество</th>
            <th>Город</th>
            <th>Вид спорта</th>
            <th>Возрастная группа</th>
        </tr>
        <?php foreach ($veterans as $row): ?>
            <tr>
                <td><?= $row['id_veteran'] ?></td>
                <td><?= $row['surname'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['thirdname'] ?></td>
                <td><?= $row['city'] ?></td>
                <td><?= $row['sport_name'] ?></td>
                <td><?= $row['age_group'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>



<?php

// Подключение к базе данных
$host = 'localhost'; // адрес сервера базы данных
$dbname = 'php'; // имя базы данных
$username = 'postgres'; // имя пользователя базы данных
$password = '12345'; // пароль пользователя базы данных

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Получение названия города из поля ввода
if(isset($_POST['city'])) {
    $city = $_POST['city'];
    // Подготовка запроса
    $sql = "SELECT v.surname, v.name, v.thirdname, s.name as sport_name, ag.age_group 
            FROM veterans v 
            JOIN sports s ON v.id_sport = s.id_sport 
            JOIN age_groups ag ON v.id_age_group = ag.id_age_group 
            WHERE v.city = :city";

    try {
        // Подготовка и выполнение запроса
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':city', $city, PDO::PARAM_STR);
        $stmt->execute();

        // Вывод результатов запроса
        echo "<h2>Список ветеранов спорта из города $city:</h2>";
        echo "<ul>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>{$row['surname']} {$row['name']} {$row['thirdname']} - {$row['sport_name']} ({$row['age_group']})</li>";
        }
        echo "</ul>";
    } catch (PDOException $e) {
        die("Ошибка выполнения запроса: " . $e->getMessage());
    }
}

?>

<!-- Форма для ввода названия города -->
<form method="POST">
    <label for="city">Введите название города:</label>
    <input type="text" id="city" name="city">
    <button type="submit">Поиск</button>
</form>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Hotels</title>
</head>
<body>

<h2>Нажмите кнопку, чтобы посмотреть список гостиниц:</h2>

<form method="POST">
    <button type="submit" name="showHotelsButton">Показать гостиницы</button>
</form>

<div id="hotelList">
    <?php
    // Проверка, была ли нажата кнопка
    if (isset($_POST['showHotelsButton'])) {
        // Подключение к базе данных
        $host = 'localhost'; // адрес сервера базы данных
        $dbname = 'php'; // имя базы данных
        $username = 'postgres'; // имя пользователя базы данных
        $password = '12345'; // пароль пользователя базы данных

        try {
            $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }

        // Подготовка запроса
        $sql = "SELECT * FROM hostels";

        try {
            // Выполнение запроса
            $stmt = $pdo->query($sql);

            // Вывод результатов запроса
            echo "<h3>Список гостиниц:</h3>";
            echo "<ul>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<li>{$row['name']} - {$row['address']}</li>";
            }
            echo "</ul>";
        } catch (PDOException $e) {
            die("Ошибка выполнения запроса: " . $e->getMessage());
        }
    }
    ?>
</div>

</body>
</html>


<!DOCTYPE html>
<html>
<body>
<h2>Справочная информация</h2>
<table>
    <!-- Здесь должна быть ваша таблица -->
    <tr>
        <th>Таблица в начале</th>
        <th>Добавление ветерана</th>
        <th>Число ветеранов по каждому виду спорта</th>
        <th>Список ветеранов спорта с группировкой по спорту</th>
        <th>Список ветеранов с ограничением по количеству строк</th>
        <th>Поиск по фамилии</th>
        <th>Поиск по городу</th>
        <th>Сортировка</th>
        <th>Список гостиниц</th>

    </tr>
    <tr>
        <td>Данные о всех ветеранах</td>
        <td>Введите данные в форму и нажмите добавить ветерана, после чего обновите страницу</td>
        <td>Выводит таблицу в виде: вид спорта - количество участников</td>
        <td>Выводит ветеранов с группировкой по виду спорта</td>
        <td>Выводит только указанное число ветеранов</td>
        <td>Выводит только ветеранов с указанной фамилией</td>
        <td>Выводит только ветеранов из указанного города</td>
        <td>Выводит ветеранов с сортировкой по фамилиям и по возрасту</td>
        <td>Выводит список гостиниц</td>
    </tr>
</table>

<form method="post" action="">
    <input type="submit" name="showInfo" value="Показать справочную информацию">
</form>

<?php
if (isset($_POST['showInfo'])) {
    echo '<h2>Справочная информация</h2>';
    echo '<p>Буторин БСБО-01-22.</p>';
    // Здесь можно добавить больше деталей о структуре таблицы, её назначении и т.д.
}
?>
</body>
</html>






