<?php
header('Content-Type: application/json');
include('php/autoload.php');

$type = isset($_GET['type']) ? $_GET['type'] : '7day';
$table = isset($_GET['table']) ? $_GET['table'] : 'templates';
$table = $table == 'templates' ? 'Templates' : 'SourceImages';

switch($type){
    case '7day':
        $query = "
            SELECT  strftime('%s', timeAdded, 'unixepoch', 'start of day') AS dateAdded, count(*) as amount
            FROM    $table
            WHERE	timeAdded > (SELECT strftime('%s', 'now', 'start of day', '-7 day')) AND
                    timeAdded < (SELECT strftime('%s', 'now', 'start of day'))
            GROUP BY dateAdded
        ";
        break;
    default:
        echo json_encode(array('error' => 'type not found'));
        exit;
}

$result = $db->queryAsAssoc($query);
echo json_encode($result);

?>