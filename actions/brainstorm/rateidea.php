<?php
/**
* Idea rate action
*
* @package Brainstorm
*/

gatekeeper();

$idea_guid = (int)get_input('idea');
$value = (int)get_input('value');

$user_guid = elgg_get_logged_in_user_guid();
$page_owner = get_input('page_owner');

$sum = elgg_get_annotations(array(
	'guids' => $idea_guid,
	'annotation_names' => 'point',
	'annotation_owner_guids' => $user_guid,
	'annotation_calculation' => 'sum',
	'limit' => 0
));
$point = $value-$sum;

// Verify if $point = 0 then it's seem user vote for the same rate or rate 0 for the first time (stupid) or POST crack
if ( $point == 0 || $value < 0 || $value > 3 ) {
	register_error(elgg_echo('brainstorm:idea:rate:error:value'));
	$error = true;
} else {
	
	$annotation = new ElggObject($idea_guid);

	if ( create_annotation($annotation->getGUID(),'point',$point,'integer',$user_guid,$annotation->getAccessID()) ) {
		system_message(elgg_echo('brainstorm:idea:rate:submitted'));
	} else {
		register_error(elgg_echo('brainstorm:idea:rate:error'));
	}
		
}

$userVote = elgg_get_annotations(array(
	'container_guid' => $page_owner,
	'annotation_names' => 'point',
	'annotation_calculation' => 'sum',
	'annotation_owner_guids' => $user_guid,
	'limit' => 0
));
$userVote = 10 - $userVote;

if ( $userVote < 0 ) {
	elgg_delete_annotation_by_id($annotation->getGUID());
	register_error(elgg_echo('brainstorm:idea:rate:error:underzero'));
	$error = true;
}

$sum = elgg_get_annotations(array(
	'guids' => $idea_guid,
	'annotation_names' => 'point',
	'annotation_calculation' => 'sum',
	'limit' => 0
));

echo json_encode(array('sum' => $sum, 'userVoteLeft' => $userVote, 'errorRate' => $error));