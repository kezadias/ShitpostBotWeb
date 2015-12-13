<?php
include('php/autoload.php');
$row = $db->query("SELECT u.userId as submitterId, u.username as submitterName, t.templateId, t.templateId || '.' || t.filetype as file, CASE WHEN t.overlayFiletype = null THEN null ELSE t.templateId || '.' || t.overlayFiletype END as overlayFile
				   FROM Templates as t, Users as u
				   WHERE u.userId = t.userId AND t.reviewState = 'p'
				   ORDER BY random()
				   LIMIT 1",
				   array(),
				   array())->fetchArray();

echo $twig->render('templatereview.html', $row === false ? array('msg' => 'No templates found.') : $row);
$db->close();
?>