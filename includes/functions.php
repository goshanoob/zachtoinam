<?php

include_once 'config.php';

// Получить объект для работы с БД.
function get_db()
{
    try {
        $host = 'mysql:host=' . DB_HOST . '; dbname=' . DB_NAME . '; charset=utf8';
        $settings = [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        return new PDO($host, DB_LOGIN, DB_PASSWORD, $settings);
    } catch (PDOException $ex) {
        die($ex->getMessage());
    }
}


function get_tasks_count()
{
    $query = get_db()->prepare("SELECT COUNT(`id`) FROM `tasks`;");
    $query->execute([]);
    return $query->fetchColumn();
}

// Получить вопросы из БД. Возвращает $max_count вопросов в случайном порядке из БД.
function get_tasks()
{
    $tasks_count = get_tasks_count();
    $numbers = range(1, $tasks_count);
    shuffle($numbers);
    $max_count = 10;
    foreach ($numbers as $key => $number) {
        $query = get_db()->prepare("SELECT * FROM `tasks` WHERE `id` = ?;");
        $query->execute([$number]);
        $tasks[] = [$number, $query->fetch()];
        if ($key >= $max_count - 1) {
            break;
        }
    }
    return $tasks;
}

// Выполнить запрос к БД. Второй параметр true означает, что метод 
function make_db_query($sql = '', $exec = false)
{
    if (empty($sql)) {
        return false;
    }
    if ($exec) {
        return get_db()->exec($sql);
    }

    return get_db()->query($sql);
}


function get_ansvers($tasks)
{
    foreach ($tasks as $task) {
        $query = get_db()->prepare("SELECT * FROM `ansvers` WHERE `task` = ?;");
        $query->execute([$task[0]]);
        $ansvers[] =  $query->fetchAll();
    }
    return $ansvers;
}


// Сохранение ответов студента в БД.
function save_student_ansvers($fio, $ansvers)
{
    if (empty($fio) || empty($ansvers)) {
        return false;
    }

    $query = get_db()->prepare("INSERT INTO `results`(`student_fio`,`student_ansvers`) values (?,?);");
    return $query->execute([$fio, json_encode($ansvers)]);
}


// Проверка наличия резульатов студента в БД.
function check_ansvers($fio)
{
    if (empty($fio)) {
        return false;
    }

    $query = get_db()->prepare("SELECT `student_fio` FROM `results` WHERE `student_fio` = ?;");
    $query->execute([$fio]);
    return $query->fetchColumn();
}


function set_tasks_to_db($tasks)
{
    $ansver_id = 0;
    foreach ($tasks as $task_key => $task) {
        foreach ($task as $ansver_key => $ansver) {
            if ($ansver_key === 0) {
                $query = get_db()->prepare("INSERT INTO `tasks`(`task`) VALUES (?);");
                $query->execute([$ansver]);
            } else {
                $query = get_db()->prepare("INSERT INTO `ansvers`(`id`, `task`, `ansver`) VALUES (?,?,?);");
                $query->execute([++$ansver_id, $task_key + 1,  substr($ansver, 1)]);
                if ($ansver[0] === '*') {
                    $query = get_db()->prepare("INSERT INTO `tasks_keys`(`task_id`, `right_ansver`) VALUES (?,?);");
                    $query->execute([$task_key + 1, $ansver_id]);
                }
            }
        }
    }
}

// Читать файл.
function reade_file($file_name)
{
    $data = file_get_contents($file_name);

    $tasks = explode('%', $data);
    foreach ($tasks as $key => $task) {
        $task = trim($task);

        // $varenty1[] = preg_split('/^[~*]/', $task);
        $varenty1[] = preg_split('/(?=[~*])/', $task);
    }

    set_tasks_to_db($varenty1);

    return $varenty1;
}


// Определить результаты теста.
function get_test_result($ansvers, $fio)
{
    $counter = 0;
    // $ids = [];
    // foreach ($ansvers as $key => $ansver) {
    //     $ids[] = $key;
    // }
    $query = get_db()->prepare("SELECT task_id, right_ansver from `tasks_keys`;");
    $query->execute();
    $tasks_key = $query->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_GROUP);
    foreach ($ansvers as $key => $ansver) {

        if (count($ansver) === count($tasks_key[$key]) && !array_diff($ansver, $tasks_key[$key])) {
            $counter++;
        }
    }

    $mark = get_mark($counter);
    set_mark($mark, $fio);
    return [$counter, $mark];
}

function get_mark($count)
{
    $res = $count / 10;
    $mark = 0;
    if ($res <=   0.3) {
        $mark = 2;
    } else if ($res <= 0.6) {
        $mark = 3;
    } else if ($res < 0.9) {
        $mark = 4;
    } else {
        $mark = 5;
    }
    return $mark;
}

function set_mark($mark, $fio)
{
    $query = get_db()->prepare("UPDATE `results` SET `mark` = ? WHERE `student_fio` = ?;");
    $query->execute([$mark, $fio]);
}


// Вернуть массив вопросов, на которые ответил студент.
function get_student_ansvers($fio)
{
    $query = get_db()->prepare("SELECT * FROM `results` WHERE `student_fio` = ?;");
    $query->execute([$fio]);
    return $query->fetch();
}

// Вернуть массив вопросов по идентификаторам в БД.
function get_tasks_by_id($id)
{
    $query = get_db()->prepare("SELECT * FROM `tasks` WHERE `id` IN (?,?,?,?,?,?,?,?,?,?);");
    $query->execute([$id[0], $id[1], $id[2], $id[3], $id[4], $id[5], $id[6], $id[7], $id[8], $id[9]]);
    return $query->fetchAll(PDO::FETCH_UNIQUE);
}


// Вернуть массив верных ответов.
function get_tasks_key_by_id($id)
{
    $query = get_db()->prepare("SELECT `task_id`, `right_ansver` FROM `tasks_keys` WHERE `task_id` IN (?,?,?,?,?,?,?,?,?,?);");
    $query->execute([$id[0], $id[1], $id[2], $id[3], $id[4], $id[5], $id[6], $id[7], $id[8], $id[9]]);
    return $query->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_GROUP);
}


// Вернуть массив ответов на по id вопроса.
function get_ansvers_by_id($id)
{
    $query = get_db()->prepare("SELECT task, id, ansver FROM `ansvers` WHERE `task` IN (?,?,?,?,?,?,?,?,?,?);");
    $query->execute([$id[0], $id[1], $id[2], $id[3], $id[4], $id[5], $id[6], $id[7], $id[8], $id[9]]);
    return $query->fetchAll(PDO::FETCH_GROUP);
}
