<?php
require('ensure_login.inc.php');
require('db.inc.php');

if(!empty($_POST['delete_weekly_category'])) {
    $remove = $db->prepare('DELETE FROM weekly_categories WHERE id = :id;');
    $remove->bindValue('id', (int) $_POST['delete_weekly_category']);

    if($remove->execute()) {
        header('Location: weekly_categories.php');
    } else {
        $remove_has_error = true;
    }
}
if(!empty($_POST['name']) && !empty($_POST['priority'])) {
    $add = $db->prepare('INSERT INTO weekly_categories(name, priority, special_monday, special_tuesday, special_wednesday, special_thursday, special_friday, special_saturday, special_sunday)
                         VALUES(:name, :priority, :special_monday, :special_tuesday, :special_wednesday, :special_thursday, :special_friday, :special_saturday, :special_sunday)');
    
    $add->bindValue('name', htmlspecialchars($_POST['name']));
    $add->bindValue('priority', (int) $_POST['priority']);
    $add->bindValue('special_monday', ($_POST['special_monday'] ?? false) ? true : false);
    $add->bindValue('special_tuesday', ($_POST['special_tuesday'] ?? false) ? true : false);
    $add->bindValue('special_wednesday', ($_POST['special_wednesday'] ?? false) ? true : false);
    $add->bindValue('special_thursday', ($_POST['special_thursday'] ?? false) ? true : false);
    $add->bindValue('special_friday', ($_POST['special_friday'] ?? false) ? true : false);
    $add->bindValue('special_saturday', ($_POST['special_saturday'] ?? false) ? true : false);
    $add->bindValue('special_sunday', ($_POST['special_sunday'] ?? false) ? true : false);

    if($add->execute()) {
        header('Location: weekly_categories.php');
    } else {
        $add_has_error = true;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ToDo! Gérer les catégories</title>

    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="todo.css">
</head>
<body>
    <header>
        <h1>ToDo!</h1>
        <h2>Gérer les catégories</h2>
        <a href="index.php" id="back">Retour</a>
    </header>

    <p>
        Les catégories sont affichées d'abord selon leur priorité, puis par ordre alphabétique.
    </p>

    <?php
    if(isset($remove_has_error) && $remove_has_error) {
    ?>
    <p class="error" title="<?php echo htmlspecialchars($remove->errorInfo()[2]); ?>">
        Une erreur a eu lieu lors de la suppression.
    </p>
    <?php
    }
    ?>

    <div class="row">
        <div id="categories_list">
            <table>
                <tr>
                    <th>Priorité</th>
                    <th>Nom</th>
                    <th>Jours</th>
                    <th>Actions</th>
                </tr>
    
                <?php
                $class_special = ' class="special"';
    
                $categories = $db->query('SELECT * FROM weekly_categories ORDER BY priority DESC, name ASC;');
    
                while($category = $categories->fetchObject()) {
                    ?>
                    <tr>
                        <td><?php echo $category->priority; ?></td>
                        <td><?php echo $category->name; ?></td>
                        <td>
                            <?php if($category->special_monday) { echo 'Lundi |'; } ?>
                            <?php if($category->special_tuesday) { echo 'Mardi |'; } ?>
                            <?php if($category->special_wednesday) { echo 'Mercredi |'; } ?>
                            <?php if($category->special_thursday) { echo 'Jeudi |'; } ?>
                            <?php if($category->special_friday) { echo 'Vendredi |'; } ?>
                            <?php if($category->special_saturday) { echo 'Samedi |'; } ?>
                            <?php if($category->special_sunday) { echo 'Dimanche |'; } ?>
                        </td>
                        <td>
                            <a href="edit_category.php?id=<?php echo $category->id; ?>">Modifier</a>
                            <form class="toggle" method="POST">
                                <input type="hidden" name="delete_weekly_category" value="<?php echo $category->id; ?>">
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        <div class="sticky dark">
            <form class="form" method="POST">
                <h3>Ajout d'une catégorie</h3>
                <?php
                if(isset($add_has_error) && $add_has_error) {
                ?>
                <p class="error" title="<?php echo htmlspecialchars($add->errorInfo()[2]); ?>">
                    Une erreur a eu lieu lors de l'ajout.
                </p>
                <?php
                }
                ?>
        
                <input type="text" name="name" placeholder="Nom de la catégorie" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                <label for="priority">Priorité</label>
                <input type="number" id="priority" name="priority" placeholder="Priorité" value="<?php echo htmlspecialchars($_POST['priority'] ?? 1); ?>">
        
                <label for="special_monday">Lundi</label> <input type="checkbox" name="special_monday" id="special_monday"<?php echo (($_POST['special_monday'] ?? false) ? ' checked' : '') ?>>
                <label for="special_tuesday">Mardi</label> <input type="checkbox" name="special_tuesday" id="special_tuesday"<?php echo (($_POST['special_tuesday'] ?? false) ? ' checked' : '') ?>>
                <label for="special_wednesday">Mercredi</label> <input type="checkbox" name="special_wednesday" id="special_wednesday"<?php echo (($_POST['special_wednesday'] ?? false) ? ' checked' : '') ?>>
                <label for="special_thursday">Jeudi</label> <input type="checkbox" name="special_thursday" id="special_thursday"<?php echo (($_POST['special_thursday'] ?? false) ? ' checked' : '') ?>>
                <label for="special_friday">Vendredi</label> <input type="checkbox" name="special_friday" id="special_friday"<?php echo (($_POST['special_friday'] ?? false) ? ' checked' : '') ?>>
                <label for="special_saturday">Samedi</label> <input type="checkbox" name="special_saturday" id="special_saturday"<?php echo (($_POST['special_saturday'] ?? false) ? ' checked' : '') ?>>
                <label for="special_sunday">Dimanche</label> <input type="checkbox" name="special_sunday" id="special_sunday"<?php echo (($_POST['special_sunday'] ?? false) ? ' checked' : '') ?>>
        
                <br>
        
                <button type="submit">Ajouter</button>
            </form>
        </div>
    </div>
</body>
</html>