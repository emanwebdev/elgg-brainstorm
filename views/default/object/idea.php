<?php
/**
 * Idea view
 *
 * @package Brainstorm
 */

$full = elgg_extract('full_view', $vars, FALSE);
$idea = elgg_extract('entity', $vars, FALSE);

if (!$idea) {
	return;
}

$owner = $idea->getOwnerEntity();
$owner_icon = elgg_view_entity_icon($owner, 'small');
$container = $idea->getContainerEntity();
$categories = elgg_view('output/categories', $vars);

$description = elgg_view('output/longtext', array('value' => $idea->description, 'class' => 'pbl'));

$owner_link = elgg_view('output/url', array(
	'href' => "brainstorm/owner/$owner->username",
	'text' => $owner->name,
));
$author_text = elgg_echo('byline', array($owner_link));

$tags = elgg_view('output/tags', array('tags' => $idea->tags));
$date = elgg_view_friendly_time($idea->time_created);

$comments_count = $idea->countComments();
//only display if there are commments
if ($comments_count != 0) {
	$text = elgg_echo("comments") . " ($comments_count)";
	$comments_link = elgg_view('output/url', array(
		'href' => $idea->getURL() . '#comments',
		'text' => $text,
	));
} else {
	$comments_link = '';
}

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'brainstorm',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$subtitle = "$author_text $date $categories $comments_link";

$params = array(
	'entity' => $idea,
	'title' => false,
	'metadata' => $metadata,
	'subtitle' => $subtitle,
	'tags' => $tags,
);
$params = $params + $vars;
$list_body = elgg_view('object/elements/summary', $params);

$sum = elgg_get_annotations(array(
	'guids' => $idea->guid,
	'annotation_names' => 'point',
	'annotation_calculation' => 'sum'
));
if ( $sum == '' ) $sum = 0;

$userVote = elgg_get_annotations(array(
	'guids' => $idea->guid,
	'annotation_names' => 'point',
	'annotation_calculation' => 'sum',
	'annotation_owner_guids' => elgg_get_logged_in_user_guid()
));
if ( $userVote == '' || $userVote == '0') $userVote = 'vote';

$vars['idea'] = array('userVote'  => $userVote);

$vote = "<div class='idea-points mbs'>$sum</div>" .
	"<a class='idea-rate-button value-$userVote' rel='popup' href='#vote-popup-{$idea->guid}'>$userVote</a>" .
	"<div id='vote-popup-{$idea->guid}' class='elgg-module-popup brainstorm-vote-popup'>" .
		"<div class='triangle gris'></div><div class='triangle blanc'></div>" .
		elgg_view_form('brainstorm/vote_popup','', $vars) .
	"</div>";

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}

if ($full && !elgg_in_context('gallery')) {
	$header = elgg_view_title($idea->title);

	$idea_info = elgg_view_image_block($owner_icon, $list_body);

	echo <<<HTML
<div id="elgg-object-{$idea->guid}">
	<div class="idea-left-column mts">$vote</div>
	<div class="idea-content">
		$header
		$idea_info
		$description
	</div>
</div>
HTML;

} elseif (elgg_in_context('gallery')) {
	echo <<<HTML
<div class="idea-gallery-item">
	<h3>$idea->title</h3>
	<p class='subtitle'>$owner_link $date</p>
</div>
HTML;
} else {
	// brief view
	$header = elgg_view_title($idea->title);

	$content = elgg_get_excerpt($idea->description, '300');

	$params = array(
		'text' => $idea->title,
		'href' => $idea->getURL(),
	);
	$title_link = elgg_view('output/url', $params);
	
	echo <<<HTML

<div class="idea-left-column mts mbs">$vote</div>
<div class="idea-content mts">
	<h3>$title_link</h3>
	$content
	$list_body
</div>
HTML;

}