<?php
$entity = elgg_extract('entity', $vars);
?>
<div class="elgg-field">
	<label class="elgg-field-label"><?php echo elgg_echo('widget:numbertodisplay'); ?></label>
	<?php
	echo elgg_view('input/select', array(
		'name' => 'params[num_display]',
		'value' => $entity->num_display,
		'options' => array(5, 10, 15, 20),
	));
	?>
</div>