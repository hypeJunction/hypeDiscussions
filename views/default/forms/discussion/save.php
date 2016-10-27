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

if ($entity->container_guid) {
	$fields[] = [
		'#type' => 'hidden',
		'name' => 'container_guid',
		'value' => elgg_extract('container_guid', $vars, $entity->container_guid),
	];
} else {
	$fields[] = [
		'#type' => 'discussions/container',
		'#label' => elgg_echo('discussion:group:container'),
		'name' => 'container_guid',
		'required' => true,
		'value' => elgg_extract('container_guid'),
	];
}

$fields[] = [
	'#type' => 'text',
	'#label' => elgg_echo('title'),
	'name' => 'title',
	'value' => elgg_extract('title', $vars, $entity->title),
	'required' => true,
];

$fields[] = [
	'#type' => 'longtext',
	'#label' => elgg_echo('discussion:topic:description'),
	'name' => 'description',
	'value' => elgg_extract('description', $vars, $entity->description),
	'required' => true,
];

if (elgg_is_active_plugin('hypeAttachments') && hypeapps_allow_attachments('object', 'discussion')) {
	$fields[] = [
		'#type' => 'attachments',
		'#label' => elgg_echo('discussion:topic:attachments'),
		'name' => 'uploads',
		'expand' => true,
	];
}

$fields[] = [
	'#type' => 'tags',
	'#label' => elgg_echo('tags'),
	'name' => 'tags',
	'value' => elgg_extract('tags', $vars, $entity->tags),
];

$fields[] = [
	'#type' => 'select',
	'#label' => elgg_echo('discussion:topic:status'),
	'name' => 'status',
	'value' => elgg_extract('status', $vars, $entity->status),
	'options_values' => array(
		'open' => elgg_echo('status:open'),
		'closed' => elgg_echo('status:closed'),
	),
];

if (elgg_get_plugin_setting('max_comment_depth', 'hypeInteractions') > 1) {
	$fields[] = [
		'#type' => 'select',
		'#label' => elgg_echo('discussion:topic:enable_threads'),
		'name' => 'threads',
		'value' => $entity->threads,
		'options_values' => array(
			0 => elgg_echo('option:no'),
			1 => elgg_echo('option:yes'),
		),
	];
}

$fields[] = [
	'#type' => 'access',
	'#label' => elgg_echo('access'),
	'name' => 'access_id',
	'value' => elgg_extract('access_id', $vars, $entity ? $entity->access_id : ACCESS_DEFAULT),
	'entity' => $entity,
	'entity_type' => 'object',
	'entity_subtype' => 'discussion',
];

$fields[] = [
	'#type' => 'hidden',
	'name' => 'topic_guid',
	'value' => $guid,
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

foreach ($fields as $field) {
	echo elgg_view_field($field);
}

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
		]);

elgg_set_form_footer($footer);
