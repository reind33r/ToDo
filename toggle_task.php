<?php
require('ensure_login.inc.php');
require('db.inc.php');

if(!empty($_POST['task_id'])) {
    $update = $db->prepare('UPDATE tasks SET done = !done WHERE id = :id;');
    $update->bindValue('id', (int) $_POST['task_id']);
    $update->execute();

    header('Location: index.php');
}