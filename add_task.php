<?php
require('ensure_login.inc.php');
require('db.inc.php');

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

    $add = $db->prepare('INSERT INTO tasks(title, weekly_category_id, deadline) VALUES(:title, :weekly_category_id, :deadline);');
    $add->bindValue('title', htmlspecialchars($_POST['title']));
    $add->bindValue('weekly_category_id', $weekly_category_id);
    $add->bindValue('deadline', $deadline);

    if($add->execute()) {
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
    <title>ToDo! Ajouter une tâche</title>

    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="todo.css">
</head>
<body>
    <header>
        <h1>ToDo!</h1>
        <h2>Ajouter une tâche</h2>
        <a href="index.php" id="back">Retour</a>
    </header>

    <form class="form" method="POST">
        <?php
        if(isset($has_error) && $has_error) {
        ?>
        <p class="error" title="<?php echo htmlspecialchars($add->errorInfo()[2]); ?>">
            Une erreur a eu lieu lors de l'ajout de la tâche.
        </p>
        <?php
        }
        ?>

        <input type="text" name="title" placeholder="Titre de la tâche" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">

        <select name="weekly_category">
            <option value="">Sans catégorie</option>
            <?php
            $categories = $db->query('SELECT id, name FROM weekly_categories;');
            
            while($category = $categories->fetchObject()) {
                ?>
                <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                <?php
            }
            ?>
        </select>

        <label for="deadline">Date limite</label>

        <input type="date" name="deadline" value="<?php echo htmlspecialchars($_POST['deadline'] ?? ''); ?>">

        <button type="submit">Ajouter</button>
    </form>
</body>
</html>