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


function get_qod() {
    try {
        $file = file('qod.txt');
        
        if(date('Y-m-d') != trim($file[0])) {
            throw new Exception('QoD is not from today -> getting a new one.');
        }

        $qod = $file[1];
    } catch(Exception $e) {
        $api_url = 'http://quotes.rest/qod';
        $json = file_get_contents($api_url);
        $data = json_decode($json, true);
        
        $qod = $data['contents']['quotes'][0]['quote'];
        
        file_put_contents('qod.txt', date('Y-m-d'));
        file_put_contents('qod.txt', "\r\n" . $qod, FILE_APPEND | LOCK_EX);
    }
    
    return $qod;
}