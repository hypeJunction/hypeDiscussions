<?php

/**
 * Discussion topic reply form body
 *
 * @uses $vars['topic']  A discussion topic object
 * @uses $vars['entity'] A discussion reply object
 * @uses $vars['inline'] Display a shortened form?
 */
$topic = elgg_extract('topic', $vars);
$reply = elgg_extract('entity', $vars);
$inline = elgg_extract('inline', $vars, false);

$fields[] = [
	'#type' => 'hidden',
	'name' => 'topic_guid',
	'value' => $topic ? $topic->guid : '',
];

$fields[] = [
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $reply ? $reply->guid : '',
];

if ($reply) {
	$label = elgg_echo('discussion:reply:edit');
	$value = $reply->description;
	$action = elgg_echo('save');
} else {
	$label = elgg_echo('reply:this');
	$value = '';
	$action = elgg_echo('reply');
}

$fields[] = [
	'#type' => 'interactions/comment',
	'name' => 'description',
	'value' => $entity->description,
	'placeholder' => $entity ? elgg_echo('discussion:reply:edit') : elgg_echo('reply:this'),
];

if (elgg_get_plugin_setting('enable_attachments', 'hypeInteractions')) {
	$fields[] = [
		'#type' => 'attachments',
		'expand' => false,
	];
}

$buttons = [
		[
		'#type' => 'submit',
		'value' => $action,
	]
];

if ($inline) {
	$buttons[] = [
		'#type' => 'button',
		'type' => 'reset',
		'text' => elgg_echo('cancel'),
		'class' => 'elgg-button-cancel',
	];
}


$fields[] = [
	'#type' => 'fieldset',
	'fields' => $buttons,
	'align' => 'horizontal',
	'class' => 'elgg-foot',
];

$extensions = (array) elgg_extract('fields', $vars, []);

$fields = array_merge($fields, $extensions);
usort($fields, function($fa, $fb) {
	$a = elgg_extract('priority', $fa, 500);
	$b = elgg_extract('priority', $fb, 500);
	if ($a == $b) {
		return 0;
	}
	return ($a < $b) ? -1 : 1;
});

$body = '';
foreach ($fields as $field) {
	$body .= elgg_view_field($field);
}

$owner = $entity->guid ? $entity->getOwnerEntity() : elgg_get_logged_in_user_entity();

$icon = elgg_view_entity_icon($owner, 'small', array(
	'use_hover' => false,
	'use_link' => false,
		));

echo elgg_view_image_block($icon, $body, array(
	'class' => 'interactions-image-block',
));
