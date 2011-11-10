<?php
/**
 * Elgg brainstorm plugin group page
 *
 * @package Brainstorm
 */
$page_owner = elgg_get_page_owner_entity();

elgg_push_breadcrumb(elgg_echo('brainstorm'));

$offset = (int)get_input('offset', 0);
$order_by = get_input('order', 'desc');

$content = elgg_list_entities_from_annotation_calculation(array(
	'type' => 'object',
	'subtype' => 'idea',
	'container_guid' => $page_owner->guid,
	'limit' => 0,
	'pagination' => false,
	'annotation_names' => 'point',
	'order_by' => 'annotation_calculation ' . $order_by,
	'full_view' => false,
	'view_toggle_type' => false,
	'list_class' => 'brainstorm-list',
	'item_class' => 'elgg-item-idea'
));

if (!$content) {
	$content = elgg_echo('brainstorm:none');
}

$title = elgg_echo('brainstorm:owner', array($page_owner->name));

$filter_context = 'top';

$vars = array(
	'filter_context' => $filter_context,
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('brainstorm/sidebar'),
);

// don't show filter if out of filter context
if ($page_owner instanceof ElggGroup) {
	//$vars['filter'] = false;
}

$body = elgg_view_layout('brainstorm', $vars);

echo elgg_view_page($title, $body);