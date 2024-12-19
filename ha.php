<?php
// Увімкнення відображення помилок для діагностики
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Завантаження жартів з текстового файлу
$jokesFile = __DIR__ . '/jokes.txt';
$jokes = [];
if (file_exists($jokesFile)) {
    $jokes = file($jokesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}
$randomJoke = $jokes ? $jokes[array_rand($jokes)] : "Жарти закінчились! Додайте більше :)";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');

    // Обробка форми
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $jokeResponse = htmlspecialchars($_POST['jokeResponse']);
    $question1 = htmlspecialchars($_POST['question1']);
    $question2 = htmlspecialchars($_POST['question2']);
    $question3 = htmlspecialchars($_POST['question3']);

    // Створення папки для збереження
    $folder = __DIR__ . '/survey';
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    // Ім'я файлу
    $timestamp = date('Y-m-d_H-i-s');
    $filename = $folder . "/response_{$timestamp}.txt";

    // Форматування даних для запису
    $data = "Ім'я: $name\nEmail: $email\nРеакція на жарт: $jokeResponse\nПитання 1: $question1\nПитання 2: $question2\nПитання 3: $question3\nДата: " . date('Y-m-d H:i:s');

    // Запис у файл
    if (file_put_contents($filename, $data)) {
        echo json_encode(['success' => true, 'message' => 'Форма успішно надіслана!', 'timestamp' => date('Y-m-d H:i:s')]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Помилка збереження файлу.']);
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Анкета з жартом</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Опитування</h1>
    <p><strong>Жарт дня:</strong> <?= $randomJoke ?></p>
    <form id="surveyForm">
        <label>Ім'я респондента:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Email респондента:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Чи сподобався жарт?</label><br>
        <input type="radio" name="jokeResponse" value="Так" required> Так<br>
        <input type="radio" name="jokeResponse" value="Ні" required> Ні<br><br>

        <label>Питання 1: Який ваш улюблений колір?</label><br>
        <input type="text" name="question1" required><br><br>

        <label>Питання 2: Виберіть улюблений вид техніки:</label><br>
        <select name="question2">
            <option value="Ноутбук">Ноутбук</option>
            <option value="Смартфон">Смартфон</option>
            <option value="Телевізор">Телевізор</option>
        </select><br><br>

        <label>Питання 3: Опишіть ваш ідеальний гаджет:</label><br>
        <textarea name="question3" rows="4" cols="50"></textarea><br><br>

        <button type="submit">Надіслати</button>
    </form>

    <div id="responseMessage" style="margin-top: 20px;"></div>

    <script>
        $(document).ready(function () {
            $('#surveyForm').on('submit', function (event) {
                event.preventDefault();

                const formData = $(this).serialize(); // Збираємо дані форми
                
                $.ajax({
                    url: '', // Форма обробляється цим самим файлом
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('#responseMessage').html(
                                `<h2>${response.message}</h2><p>Дата та час: ${response.timestamp}</p>`
                            );
                            $('#surveyForm')[0].reset(); // Очищаємо форму
                        } else {
                            $('#responseMessage').html(`<p style="color:red;">${response.message}</p>`);
                        }
                    },
                    error: function () {
                        $('#responseMessage').html('<p style="color:red;">Сталася помилка. Спробуйте пізніше.</p>');
                    }
                });
            });
        });
    </script>
</body>
</html>






