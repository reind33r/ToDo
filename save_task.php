<?php
require('ensure_login.inc.php');
require('db.inc.php');

if(isset($_GET['id'])) {
    $get = $db->prepare('SELECT title, weekly_category_id, deadline FROM tasks WHERE id = :id;');
    $get->bindValue('id', (int) $_GET['id']);
    $get->execute();

    if(!$task = $get->fetchObject()) {
        header('Location: index.php');
    }

    $update = true;
}

if(!empty($_POST['title']) && isset($_POST['weekly_category']) && isset($_POST['deadline'])) {
    if(!empty($_POST['deadline'])) {
        $deadline = (new DateTime($_POST['deadline']))->format('Y-m-d H:i:s');
    } else {
        $deadline = null;
    }

    if(!empty($_POST['weekly_category'])) {
        $weekly_category_id = (int) $_POST['weekly_category'];
    } else {
        $weekly_category_id = null;
    }

    if(isset($update)) {
        $query = 'UPDATE tasks SET title = :title, weekly_category_id = :weekly_category_id, deadline = :deadline WHERE id = :id;';
    } else {
        $query = 'INSERT INTO tasks(title, weekly_category_id, deadline) VALUES(:title, :weekly_category_id, :deadline);';
    }

    $save = $db->prepare($query);
    $save->bindValue('title', htmlspecialchars($_POST['title']));
    $save->bindValue('weekly_category_id', $weekly_category_id);
    $save->bindValue('deadline', $deadline);
    if(isset($update)) {
        $save->bindValue('id', (int) $_GET['id']);
    }

    if($save->execute()) {
        header('Location: index.php');
    } else {
        $has_error = true;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ToDo! Enregistrement d'une tâche</title>

    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="todo.css">
</head>
<body>
    <header>
        <h1>ToDo!</h1>
        <h2>Enregistrement d'une tâche</h2>
        <a href="index.php" id="back">Retour</a>
    </header>

    <form class="form" method="POST">
        <?php
        if(isset($has_error) && $has_error) {
        ?>
        <p class="error" title="<?php echo htmlspecialchars($add->errorInfo()[2]); ?>">
            Une erreur a eu lieu lors de l'enregistrement de la tâche.
        </p>
        <?php
        }
        ?>

        <input type="text" name="title" placeholder="Titre de la tâche" value="<?php echo htmlspecialchars($_POST['title'] ?? $task->title ?? ''); ?>">

        <select name="weekly_category">
            <option value="">Sans catégorie</option>
            <?php
            $categories = $db->query('SELECT id, name FROM weekly_categories;');
            
            $defaultSelected = $_POST['weekly_category'] ?? $task->weekly_category_id ?? '';

            while($category = $categories->fetchObject()) {
                ?>
                <option value="<?php echo $category->id; ?>"<?php if($defaultSelected == $category->id) { echo ' selected'; } ?>>
                    <?php echo $category->name; ?>
                </option>
                <?php
            }
            ?>
        </select>

        <label for="deadline">Date limite</label>

        <input type="date" name="deadline" value="<?php echo htmlspecialchars($_POST['deadline'] ?? $task->deadline ?? ''); ?>">

        <button type="submit">Ajouter</button>
    </form>
</body>
</html>