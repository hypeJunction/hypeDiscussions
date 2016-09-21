<?php

/**
 * Discussion topic form
 *
 * @uses $vars['guid']     GUID of the reply
 * @uses $vars['fields']   Custom fields array
 * @uses $vars['controls'] Custom controls array
 */
$guid = elgg_extract('guid', $vars, null);
$entity = get_entity($guid);

$fields = [
	[
		'input' => 'text',
		'name' => 'title',
		'value' => elgg_extract('title', $vars, $entity->title),
		'label' => elgg_echo('title'),
		'required' => true,
	],
	[
		'input' => 'longtext',
		'name' => 'description',
		'value' => elgg_extract('description', $vars, $entity->description),
		'label' => elgg_echo('discussion:topic:description'),
		'required' => true,
	],
];

if (elgg_is_active_plugin('hypeAttachments') && hypeapps_allow_attachments('object', 'discussion')) {
	$fields[] = [
		'input' => 'attachments',
		'name' => 'uploads',
		'expand' => true,
		'label' => elgg_echo('discussion:topic:attachments'),
	];
}

$fields[] = [
	'input' => 'tags',
	'name' => 'tags',
	'value' => elgg_extract('tags', $vars, $entity->tags),
	'label' => elgg_echo('tags'),
];

$fields[] = [
	'input' => 'select',
	'name' => 'status',
	'value' => elgg_extract('status', $vars, $entity->status),
	'options_values' => array(
		'open' => elgg_echo('status:open'),
		'closed' => elgg_echo('status:closed'),
	),
	'label' => elgg_echo('discussion:topic:status'),
];

if (elgg_get_plugin_setting('max_comment_depth', 'hypeInteractions') > 1) {
	$fields[] = [
		'input' => 'select',
		'name' => 'threads',
		'value' => $entity->threads,
		'options_values' => array(
			0 => elgg_echo('option:no'),
			1 => elgg_echo('option:yes'),
		),
		'label' => elgg_echo('discussion:topic:enable_threads'),
	];
}

$fields[] = [
	'input' => 'access',
	'name' => 'access_id',
	'value' => elgg_extract('access_id', $vars, $entity ? $entity->access_id : ACCESS_DEFAULT),
	'entity' => $entity,
	'entity_type' => 'object',
	'entity_subtype' => 'discussion',
	'label' => elgg_echo('access'),
];

$fields[] = [
	'input' => 'hidden',
	'name' => 'container_guid',
	'value' => elgg_extract('container_guid', $vars, $entity->container_guid),
];

$fields[] = [
	'input' => 'hidden',
	'name' => 'topic_guid',
	'value' => $guid,
];

$fields = (array) elgg_extract('fields', $vars, $fields);

foreach ($fields as $field) {
	$type = elgg_extract('input', $field, 'text');
	unset($field['input']);
	echo elgg_view_input($type, $field);
}

$controls = [
	[
		'input' => 'submit',
		'value' => elgg_echo('save'),
	]
];

$controls = (array) elgg_extract('controls', $vars, $controls);

$footer = '';
foreach ($controls as $control) {
	$type = elgg_extract('input', $control, 'text');
	unset($control['input']);
	$footer .= elgg_view_input($type, $control);
}

echo elgg_format_element('div', ['class' => 'elgg-foot'], $footer);