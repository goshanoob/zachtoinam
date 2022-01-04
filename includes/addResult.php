<?php
include_once 'functions.php';

if (empty($_SESSION['student_fio'])) {
    $_SESSION['error'] = 'Вы не идетифицировались?';
    header('Location: /login.php');
}

$student_name = check_ansvers($_SESSION['student_fio']);
if (!empty($student_name)) {
    $_SESSION['error'] = "Студент $student_name уже прошел тестирование!";
} else if (!empty($_POST['student_ansvers'])) {
    $_SESSION['student_ansvers'] = $_POST['student_ansvers'];
    $save_result = save_student_ansvers($_SESSION['student_fio'], $_POST['student_ansvers']);
    if ($save_result) {
        $_SESSION['success'] = 'Ваши ответы сохранены';
    } else {
        $_SESSION['error'] = 'Что-то пошло не так';
    }
} else {
    $_SESSION['error'] = 'Ответы не были переданы';
}

include_once 'header.php';
include_once 'messages.php';

if (empty($student_name)) {
    $test_result = get_test_result($_SESSION['student_ansvers'], $_SESSION['student_fio']);
    echo "<article><h1>Результат</h1><p>Вы полно и безошибочно ответили на $test_result[0] из 10 вопросов. Ваша оценка $test_result[1]. </p></article>";
}
include_once 'footer.php';
