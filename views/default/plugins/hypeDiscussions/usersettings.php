<?php

$entity = elgg_extract('entity', $vars);
/* @var $entity ElggPlugin */

$user = elgg_extract('user', $vars);

echo elgg_view_input('select', [
	'name' => 'params[sort_discussions]',
	'value' => $entity->getUserSetting('sort_discussions', $user->guid, 'last_action::desc'),
	'options_values' => [
		'last_action::desc'=> elgg_echo('sort:object:last_action::desc'),
		'last_action::asc'=> elgg_echo('sort:object:last_action::asc'),
		'time_created::desc' => elgg_echo('sort:object:time_created::desc'),
		'time_created::asc' => elgg_echo('sort:object:time_created::asc'),
		'responses_count::desc'=> elgg_echo('sort:object:responses_count::desc'),
		'likes_count::desc' => elgg_echo('sort:object:likes_count::desc'),
	],
	'label' => elgg_echo('discussion:setting:sort_discussions'),
]);


