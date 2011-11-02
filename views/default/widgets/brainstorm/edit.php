<?php
/**
 * Elgg brainstorm widget edit view
 *
 * @package Brainstorm
 */

// set default value
if (!isset($vars['entity']->max_display)) {
	$vars['entity']->max_display = 4;
}

$params = array(
	'name' => 'params[max_display]',
	'value' => $vars['entity']->max_display,
	'options' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10),
);
$dropdown = elgg_view('input/dropdown', $params);

?>
<div>
	<?php echo elgg_echo('brainstorm:numbertodisplay'); ?>:
	<?php echo $dropdown; ?>
</div>
