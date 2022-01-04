<?php
include_once 'functions.php';

$student_tasks = [];
if (!empty($_POST['check_fio'])) {
    $student_info = get_student_ansvers($_POST['check_fio']);
    $student_ansvers = get_object_vars(json_decode($student_info['student_ansvers']));

    $id = array_keys($student_ansvers);

    $student_tasks = get_tasks_by_id($id);
    $right_ansvers = get_tasks_key_by_id($id);

    $ansvers  = get_ansvers_by_id($id);
    $k = 1;
}
include 'header.php';
?>
<form action="" method="post">
    <input type="text" placeholder="ФИО" name="check_fio">
    <input type="submit">
</form>
<?php
$k = 1;
foreach ($student_tasks as $key => $task) {

    echo '<article><h1>Вопрос ' . $k++ . '</h1><p>' . $task['task'] . '</p>';

    foreach ($ansvers[$key] as $key2 => $ansver) {
        if (in_array($ansver['id'], $student_ansvers[$key])) {
            echo '<span>+</span>';
        }
        echo '<label>' . $ansver['ansver'] . '</label>';
        if (in_array($ansver['id'], $right_ansvers[$key])) {
            echo '<span><mark>!</mark></span>';
        }

        echo '<br>';
    }

    echo '</article>';
}

include_once "footer.php";
?>