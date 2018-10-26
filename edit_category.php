<?php
require('ensure_login.inc.php');
require('db.inc.php');

if(!empty($_POST['id']) && !empty($_POST['name']) && !empty($_POST['priority'])) {
    $update = $db->prepare('UPDATE weekly_categories
                         SET name = :name, priority = :priority, special_monday = :special_monday, special_tuesday = :special_tuesday, special_wednesday = :special_wednesday, special_thursday = :special_thursday, special_friday = :special_friday, special_saturday = :special_saturday, special_sunday = :special_sunday
                         WHERE id = :id;');
    
    $update->bindValue('id', (int) $_POST['id']);
    $update->bindValue('name', htmlspecialchars($_POST['name']));
    $update->bindValue('priority', (int) $_POST['priority']);
    $update->bindValue('special_monday', ($_POST['special_monday'] ?? false) ? true : false);
    $update->bindValue('special_tuesday', ($_POST['special_tuesday'] ?? false) ? true : false);
    $update->bindValue('special_wednesday', ($_POST['special_wednesday'] ?? false) ? true : false);
    $update->bindValue('special_thursday', ($_POST['special_thursday'] ?? false) ? true : false);
    $update->bindValue('special_friday', ($_POST['special_friday'] ?? false) ? true : false);
    $update->bindValue('special_saturday', ($_POST['special_saturday'] ?? false) ? true : false);
    $update->bindValue('special_sunday', ($_POST['special_sunday'] ?? false) ? true : false);

    if($update->execute()) {
        header('Location: weekly_categories.php');
    } else {
        $has_error = true;
    }
}

$category = $db->prepare('SELECT * FROM weekly_categories WHERE id = :id;');
$category->bindValue('id', (int) $_GET['id']);
$category->execute();

if(!$category = $category->fetchObject()) {
    exit('La catégorie n\'existe pas.');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ToDo! Modifier une catégorie</title>

    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="todo.css">
</head>
<body>
    <header>
        <h1>ToDo!</h1>
        <h2>Modifier une catégorie</h2>
        <a href="weekly_categories.php" id="back">Retour</a>
    </header>

    <form class="form" method="POST">
        <h3>Modifier une catégorie</h3>
        <?php
        if(isset($has_error) && $has_error) {
        ?>
        <p class="error" title="<?php echo htmlspecialchars($update->errorInfo()[2]); ?>">
            Une erreur a eu lieu lors de la mise à jour.
        </p>
        <?php
        }
        ?>

        <input type="hidden" name="id" value="<?php echo htmlspecialchars($_POST['id'] ?? $_GET['id']); ?>">
        <input type="text" name="name" placeholder="Nom de la catégorie" value="<?php echo htmlspecialchars($_POST['name'] ?? $category->name, null, null, false); ?>">
        <label for="priority">Priorité</label>
        <input type="number" id="priority" name="priority" placeholder="Priorité" value="<?php echo htmlspecialchars($_POST['priority'] ?? $category->priority); ?>">

        <label for="special_monday">Lundi</label> <input type="checkbox" name="special_monday" id="special_monday"<?php echo (($_POST['special_monday'] ?? $category->special_monday) ? ' checked' : '') ?>>
        <label for="special_tuesday">Mardi</label> <input type="checkbox" name="special_tuesday" id="special_tuesday"<?php echo (($_POST['special_tuesday'] ?? $category->special_tuesday) ? ' checked' : '') ?>>
        <label for="special_wednesday">Mercredi</label> <input type="checkbox" name="special_wednesday" id="special_wednesday"<?php echo (($_POST['special_wednesday'] ?? $category->special_wednesday) ? ' checked' : '') ?>>
        <label for="special_thursday">Jeudi</label> <input type="checkbox" name="special_thursday" id="special_thursday"<?php echo (($_POST['special_thursday'] ?? $category->special_thursday) ? ' checked' : '') ?>>
        <label for="special_friday">Vendredi</label> <input type="checkbox" name="special_friday" id="special_friday"<?php echo (($_POST['special_friday'] ?? $category->special_friday) ? ' checked' : '') ?>>
        <label for="special_saturday">Samedi</label> <input type="checkbox" name="special_saturday" id="special_saturday"<?php echo (($_POST['special_saturday'] ?? $category->special_saturday) ? ' checked' : '') ?>>
        <label for="special_sunday">Dimanche</label> <input type="checkbox" name="special_sunday" id="special_sunday"<?php echo (($_POST['special_sunday'] ?? $category->special_sunday) ? ' checked' : '') ?>>

        <br>

        <button type="submit">Mettre à jour</button>
    </form>
</body>
</html>