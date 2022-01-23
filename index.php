<?php

include_once 'includes/functions.php';

if (empty($_SESSION['student_fio'])) {
    $_SESSION['error'] = 'Нужно идентифицироваться, чтобы пройти тест';
    header('Location: login.php');
    return;
}

$student_name = check_ansvers($_SESSION['student_fio']);
if (!empty($student_name)) {
    $_SESSION['error'] = "Студент $student_name уже прошел тестирование!";
}

if (empty($_SESSION['student_tasks'])) {
    $tasks = get_tasks();
    $ansvers = get_ansvers($tasks);

    $_SESSION['student_tasks'] = $tasks;
    $_SESSION['ansvers'] = $ansvers;
} else {
    $tasks = $_SESSION['student_tasks'];
    $ansvers = $_SESSION['ansvers'];
}


include_once 'includes/header.php';
?>


<time id="timer" class="timer"></time>
<!-- <p class="welcome">
        <a href="login.php">Выполните</a> вход для прохождения тестирования
    </p> -->

<h1>Тест по основам JS</h1>
<?php include_once 'includes/messages.php'; ?>

<form action="includes/addResult.php" method="post">
    <?php
    foreach ($tasks as $key => $task) : ?>
        <article>
            <h2>Вопрос <?= $key + 1 ?> </h2>
            <p><?= $task[1]['task'] ?></p>
            <?php
            $ans = $ansvers[$key];
            foreach ($ans as $ansver) :
            ?>
                <label>
                    <input type="checkbox" name="student_ansvers[<?= $ansver["task"]; ?>][]" value="<?= $ansver["id"]; ?>">
                    <?= $ansver["ansver"]; ?>
                </label>
                <br>
            <?php
            endforeach; ?>
        </article>
    <?php endforeach; ?>

    <input type="submit" value="Ответить">
</form>
<script src="../scripts/script.js"></script>
<?php include_once 'includes/footer.php'; ?>