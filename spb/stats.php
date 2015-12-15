<?php
include('php/autoload.php');

$acceptedT6e = $db->scalar("SELECT count(*) FROM Templates WHERE reviewState = 'a'");
$pendingT6e = $db->scalar("SELECT count(*) FROM Templates WHERE reviewState = 'p'");
$deniedT6e = $db->scalar("SELECT count(*) FROM Templates WHERE reviewState = 'd'");
$mostCommonT6eSubmitter = $db->getUsers("SELECT u.* FROM Users as u, Templates as t WHERE t.userId = u.userId AND t.reviewState = 'a' GROUP BY t.userId ORDER BY count(t.userId) DESC LIMIT 1")[0];
$mostCommonT6eSubmitterCount = $db->scalar("SELECT count(t.userId) FROM Users as u, Templates as t WHERE t.userId = u.userId AND t.reviewState = 'a' GROUP BY t.userId ORDER BY count(t.userId) DESC LIMIT 1");

$acceptedSrc = $db->scalar("SELECT count(*) FROM SourceImages WHERE reviewState = 'a'");
$pendingSrc = $db->scalar("SELECT count(*) FROM SourceImages WHERE reviewState = 'p'");
$deniedSrc = $db->scalar("SELECT count(*) FROM SourceImages WHERE reviewState = 'd'");
$mostCommonSrcSubmitter = $db->getUsers("SELECT u.* FROM Users as u, SourceImages as s WHERE s.userId = u.userId AND s.reviewState = 'a' GROUP BY s.userId ORDER BY count(s.userId) DESC LIMIT 1")[0];
$mostCommonSrcSubmitterCount = $db->scalar("SELECT count(s.userId) FROM Users as u, SourceImages as s WHERE s.userId = u.userId AND s.reviewState = 'a' GROUP BY s.userId ORDER BY count(s.userId) DESC LIMIT 1");

echo $twig->render('stats.html', 
	array(
		'acceptedT6e' => $acceptedT6e, 
		'pendingT6e' => $pendingT6e, 
		'deniedT6e' => $deniedT6e, 
		'mostCommonT6eSubmitter' => $mostCommonT6eSubmitter, 
		'mostCommonT6eSubmitterCount' => $mostCommonT6eSubmitterCount,
		'acceptedSrc' => $acceptedSrc,
		'pendingSrc' => $pendingSrc,
		'deniedSrc' => $deniedSrc,
		'mostCommonSrcSubmitter' => $mostCommonSrcSubmitter,
		'mostCommonSrcSubmitterCount' => $mostCommonSrcSubmitterCount
	)
);
$db->close();
?>