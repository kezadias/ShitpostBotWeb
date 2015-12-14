<?php
include('php/autoload.php');
$row = $db->query("SELECT u.userId as submitterId, u.username as submitterName, s.sourceId, s.sourceId || '.' || s.filetype as file
				   FROM SourceImages as s, Users as u
				   WHERE u.userId = s.userId AND s.reviewState = 'p'
				   ORDER BY random()
				   LIMIT 1",
				   array(),
				   array())->fetchArray();

$count = $db->scalar("SELECT count(*) FROM SourceImages WHERE reviewState = 'p'", array(), array());
echo $twig->render('source-review.html', $row === false ? array('msg' => 'No templates found.') : array_merge($row, array('count' => $count)));
$db->close();
?>