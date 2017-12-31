<?php
function pluriel($variable) {
    $count = (is_array($variable)) ? count($variable) : (int) $variable;

    return ($count == 1) ? '' : 's';
}

function echo_form_toggle($task_id, $done, $id_prepend) {
    echo '<form class="toggle" method="POST" action="toggle_task.php">';
        echo '<input class="checkbox_toggle" type="checkbox" id="'.$id_prepend.$task_id.'"'. (($done) ? ' checked' : '') .'>&nbsp;';
        echo '<input type="hidden" name="task_id" value="'.$task_id.'">';
    echo '</form>';
}

function echo_past_tasks($tasks) {
    echo '<ul>';

    foreach($tasks as $task) {
        echo '<li>';
            echo_form_toggle($task->id, false, 'undone_past_task_');
            
            echo '&nbsp;<label for="undone_past_task_'.$task->id.'">';
                if($task->category) {
                    echo '<strong>'. $task->category .'</strong> ';
                }
                echo $task->title;
            echo '</label>';
            echo ' <span class="days_behind">'.$task->days_behind.' jour'. pluriel($task->days_behind) .' de retard</span>';
        echo '</li>';
    }

    echo '</ul>';
}

function echo_coming_tasks($tasks) {
    echo '<ul>';

    foreach($tasks as $task) {
        echo '<li>';
            echo_form_toggle($task->id, false, 'coming_task_');
            
            echo '&nbsp;<label for="coming_task_'.$task->id.'">';
                if($task->category) {
                    echo '<strong>'. $task->category .'</strong> ';
                }
                echo $task->title;
            echo '</label>';
            if($task->days_left) {
                echo ' <span class="days_left">dans '.$task->days_left.' jour'. pluriel($task->days_left) .'</span>';
            }
        echo '</li>';
    }

    echo '</ul>';
}



function weekly_planner_echo_tasks($tasks) {
    echo '<ul>';

    foreach($tasks as $task) {
        echo '<li>';
            echo_form_toggle($task->id, $task->done, 'planner_task_');
            echo '<label'. (($task->done) ? ' class="strike"' : '') .' for="planner_task_'.$task->id.'">'.$task->title.'</label>';
        echo '</li>';
    }

    echo '</ul>';
}