<?php

/**
 * Discussion reply form
 *
 * @uses $vars['topic']    Discussion topic
 * @uses $vars['entity']   Discussion reply
 * @uses $vars['fields']   Custom fields array
 * @uses $vars['controls'] Custom controls array
 */
$entity = elgg_extract('entity', $vars);
$container = elgg_extract('topic', $vars);

$fields = [
	[
		'input' => 'hidden',
		'name' => 'topic_guid',
		'value' => $container->guid,
	],
	[
		'input' => 'hidden',
		'name' => 'guid',
		'value' => $entity->guid,
	],
	[
		'input' => 'interactions/comment',
		'name' => 'description',
		'value' => $entity->description,
		'placeholder' => $entity ? elgg_echo('discussion:reply:edit') : elgg_echo('reply:this'),
	],
];

if (elgg_get_plugin_setting('enable_attachments', 'hypeInteractions')) {
	$fields[] = [
		'input' => 'attachments',
		'expand' => false,
	];
}

$fields = elgg_extract('fields', $vars, $fields);

$body = '';

foreach ($fields as $field) {
	$type = elgg_extract('input', $field, 'text');
	unset($field['input']);
	$body .= elgg_view_input($type, $field);
}

$controls = [
	'cancel' => [
		'input' => 'button',
		'value' => elgg_echo('cancel'),
		'class' => 'elgg-button-cancel',
	],
	'submit' => [
		'input' => 'submit',
		'value' => $entity ? elgg_echo('save') : elgg_echo('reply'),
	],
];

if (!elgg_is_xhr() || !$entity instanceof hypeJunction\DiscussionReply) {
	unset($controls['cancel']);
}

$controls = (array) elgg_extract('controls', $vars, $controls);

$footer = '';
foreach ($controls as $control) {
	$type = elgg_extract('input', $control, 'text');
	unset($control['input']);
	$footer .= elgg_view_input($type, $control);
}

$body .= elgg_format_element('div', ['class' => 'elgg-foot'], $footer);

$owner = $entity->guid ? $entity->getOwnerEntity() : elgg_get_logged_in_user_entity();

$icon = elgg_view_entity_icon($owner, 'small', array(
	'use_hover' => false,
	'use_link' => false,
		));

echo elgg_view_image_block($icon, $body, array(
	'class' => 'interactions-image-block',
));
