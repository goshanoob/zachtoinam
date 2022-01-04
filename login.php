<?php
include_once 'includes/functions.php';


if (!empty($_SESSION['student_fio'])) {
    $_SESSION['success'] = 'Я Вас запомнил';
    header('Location: /');
} else if (empty($_POST['student_fio'])) {
    $_SESSION['error'] = 'Введите фамилию, имя и отчество!';
} else {
    $_SESSION['student_fio'] = $_POST['student_fio'];
    $_SESSION['success'] = 'Вы идентифицировались';
    header('Location: /');
}

include_once 'includes/header.php';

?>

<article>
    <h1>Тест по основам JS</h1>

    <?php include_once 'includes/messages.php'; ?>

    <p>Тест по темам: операторы, функции, массивы, объектная модель документа, простые объекты.</p>
    <p>Тест состоит из 10 вопросов со множественным выбором ответов. Каждый вопрос может иметь произвольное количество верных ответов (кроме нуля).</p>
    <p>Отметив верные варианты на все 10 вопросов, нажмите кнопку "Ответить".</p>
    <p>Только одна попытка.</p>
    <p>Но сначала идентифицируйтесь:</p>
</article>
<form action="" method="post">
    <input type="text" placeholder="Фамилия Имя Отчество" name="student_fio">
    <input type="submit" value="Приступить!">
</form>

<?php include_once 'includes/footer.php'; ?>