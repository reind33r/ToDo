<?php
require('ensure_login.inc.php');
require('db.inc.php');
require('helpers.inc.php');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ToDo!</title>

    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="todo.css">
    <link rel="icon" href="favicon.png">
</head>
<body>
    <?php
    if(file_exists('countdown.inc.php')) {
        include 'countdown.inc.php';
    } else {
    ?>
    <header>
        <h1>ToDo!</h1>
    </header>
    <?php
    }
    ?>

    <div class="row">
        <div id="weekly_planner">
            <?php
            $class_today = [
                0 => '',
                1 => '',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
            ];
            $class_today[((int) date('N')) - 1] = ' class="today"';
            ?>

            <table>
                <tr>
                    <th></th>
                    <th<?php echo $class_today[0]; ?>>Lundi</th>
                    <th<?php echo $class_today[1]; ?>>Mardi</th>
                    <th<?php echo $class_today[2]; ?>>Mercredi</th>
                    <th<?php echo $class_today[3]; ?>>Jeudi</th>
                    <th<?php echo $class_today[4]; ?>>Vendredi</th>
                    <th<?php echo $class_today[5]; ?>>Samedi</th>
                    <th<?php echo $class_today[6]; ?>>Dimanche</th>
                </tr>
    
                <?php
                $class_special = ' class="special"';
    
                $categories = $db->query('SELECT * FROM weekly_categories ORDER BY priority DESC, name ASC;');
                
    
                while($category = $categories->fetchObject()) {
                    $tasks = $db->prepare('SELECT id, title, done, WEEKDAY(deadline) AS day
                                           FROM tasks
                                           WHERE weekly_category_id = :id
                                           AND deadline > NOW() - INTERVAL 1 DAY
                                           AND deadline <= NOW() + INTERVAL 6 DAY
                                           ORDER BY id;');
                    $tasks->bindValue('id', $category->id);
                    $tasks->execute();
    
                    $tasks_array = [
                        0 => [],
                        1 => [],
                        2 => [],
                        3 => [],
                        4 => [],
                        5 => [],
                        6 => [],
                    ];
    
                    while($task = $tasks->fetchObject()) {
                        $tasks_array[$task->day][] = $task;
                    }
                    ?>
                    <tr>
                        <th><?php echo $category->name; ?></th>
                        <td<?php if($category->special_monday) { echo $class_special; } ?>>
                            <?php weekly_planner_echo_tasks($tasks_array[0]); ?>
                        </td>
                        <td<?php if($category->special_tuesday) { echo $class_special; } ?>>
                            <?php weekly_planner_echo_tasks($tasks_array[1]); ?>
                        </td>
                        <td<?php if($category->special_wednesday) { echo $class_special; } ?>>
                            <?php weekly_planner_echo_tasks($tasks_array[2]); ?>
                        </td>
                        <td<?php if($category->special_thursday) { echo $class_special; } ?>>
                            <?php weekly_planner_echo_tasks($tasks_array[3]); ?>
                        </td>
                        <td<?php if($category->special_friday) { echo $class_special; } ?>>
                            <?php weekly_planner_echo_tasks($tasks_array[4]); ?>
                        </td>
                        <td<?php if($category->special_saturday) { echo $class_special; } ?>>
                            <?php weekly_planner_echo_tasks($tasks_array[5]); ?>
                        </td>
                        <td<?php if($category->special_sunday) { echo $class_special; } ?>>
                            <?php weekly_planner_echo_tasks($tasks_array[6]); ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <?php
                $tasks = $db->query('SELECT id, title, done, WEEKDAY(deadline) AS day
                                       FROM tasks
                                       WHERE weekly_category_id IS NULL
                                       AND deadline > NOW() - INTERVAL 1 DAY
                                       AND deadline <= NOW() + INTERVAL 6 DAY
                                       ORDER BY id;');

                $tasks_array = [
                    0 => [],
                    1 => [],
                    2 => [],
                    3 => [],
                    4 => [],
                    5 => [],
                    6 => [],
                ];

                while($task = $tasks->fetchObject()) {
                    $has_one_task = true;
                    $tasks_array[$task->day][] = $task;
                }
                if(isset($has_one_task)) {
                ?>
                <tr>
                    <th class="italic">Autres</th>
                    <td<?php echo $class_today[0]; ?>>
                        <?php weekly_planner_echo_tasks($tasks_array[0]); ?>
                    </td>
                    <td<?php echo $class_today[1]; ?>>
                        <?php weekly_planner_echo_tasks($tasks_array[1]); ?>
                    </td>
                    <td<?php echo $class_today[2]; ?>>
                        <?php weekly_planner_echo_tasks($tasks_array[2]); ?>
                    </td>
                    <td<?php echo $class_today[3]; ?>>
                        <?php weekly_planner_echo_tasks($tasks_array[3]); ?>
                    </td>
                    <td<?php echo $class_today[4]; ?>>
                        <?php weekly_planner_echo_tasks($tasks_array[4]); ?>
                    </td>
                    <td<?php echo $class_today[5]; ?>>
                        <?php weekly_planner_echo_tasks($tasks_array[5]); ?>
                    </td>
                    <td<?php echo $class_today[6]; ?>>
                        <?php weekly_planner_echo_tasks($tasks_array[6]); ?>
                    </td>
                </tr>
                <?php
                }
                ?>
            </table>
        </div>

        <div id="coming">
            <h3>À procrastiner, mais pas trop...</h3>

            <?php
            $coming_tasks = $db->query('SELECT t.id, title, DATEDIFF(deadline, NOW()) AS days_left, c.name AS category
                                FROM tasks t
                                LEFT JOIN weekly_categories c ON t.weekly_category_id = c.id
                                WHERE (deadline > NOW() + INTERVAL 6 DAY OR deadline IS NULL) AND done = FALSE
                                ORDER BY deadline, id;')
                        ->fetchAll(PDO::FETCH_OBJ);

            echo_coming_tasks($coming_tasks);
            
            if(count($coming_tasks) == 0) {
                echo '<p>Rien à faire... pour l\'instant !</p>';
            }
            ?>
            
            <div class="sticky dark">
                <h5>Quote of the day</h5>
                <?php echo get_qod(); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="sticky dark">
            <form class="form" method="POST" action="save_task.php">
                <input type="text" name="title" placeholder="Titre de la tâche">
    
                <select name="weekly_category">
                    <option value="">Sans catégorie</option>
                    <?php
                    $categories = $db->query('SELECT id, name FROM weekly_categories ORDER BY priority DESC, name ASC;');
                    
                    while($category = $categories->fetchObject()) {
                        ?>
                        <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                        <?php
                    }
                    ?>
                </select>
    
                <label for="deadline">Date limite</label>
    
                <input type="date" name="deadline">
    
                <button type="submit">Ajouter une tâche</button>
            </form>
        </div>
        <div id="undone_past" class="sticky red">
            <h3>Tâches passées</h3>
    
            <?php
            $undone_past_tasks = $db->query('SELECT t.id, title, DATEDIFF(NOW(), deadline) AS days_behind, c.name AS category
                                 FROM tasks t
                                 LEFT JOIN weekly_categories c ON t.weekly_category_id = c.id
                                 WHERE deadline <= NOW() - INTERVAL 1 DAY AND done = FALSE
                                 ORDER BY deadline, id;')
                        ->fetchAll(PDO::FETCH_OBJ);
    
            echo_past_tasks($undone_past_tasks);
    
            if(count($undone_past_tasks) == 0) {
                echo '<p>Bravo, tu n\'es pas en retard !</p>';
            }
            ?>
        </div>
    </div>

    <p>
        <a href="weekly_categories.php">Gérer les catégories</a>
    </p>

    <!--
    <div class="sticky">
        <h3>Sticky</h3>

        <p>
            En v2 : créer une table "stickies" avec task_id et additionnal_data et afficher un post-it.
        </p>
    </div>

    <div id="tips">
        <h2>Tips</h2>

        <p>
            Pour <strong>ajouter une tâche dans le calendrier hebdomadaire</strong>, double-clique sur une case.
        </p>

        <p>
            Pour <strong>déplacer un élément</strong>, double-clic dessus et déplace le curseur tout en maintenant le bouton appuyé (pour le calendrier hebdomadaire, clique sur la case en haut à gauche).
        </p>
    </div>-->


    <script type="text/javascript">
    var checkbox_toggles = document.getElementsByClassName('checkbox_toggle');
    for(var i=0; i < checkbox_toggles.length; i++) {
        var checkbox_toggle = checkbox_toggles[i];

        checkbox_toggle.addEventListener('change', function() {
            var checkbox = this;

            checkbox.closest('form').submit();
        });
    }
    </script>
</body>
</html>