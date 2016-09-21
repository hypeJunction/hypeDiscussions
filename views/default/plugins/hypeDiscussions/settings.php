<?php

$entity = elgg_extract('entity', $vars);

echo elgg_view_input('select', [
	'name' => 'params[site_wide_discussions]',
	'value' => $entity->site_wide_discussions,
	'options_values' => [
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	],
	'label' => elgg_echo('discussion:settings:site_wide_discussions'),
	'help' => elgg_echo('discussion:settings:site_wide_discussions:help'),
]);

echo elgg_view_input('select', [
	'name' => 'params[enable_sort]',
	'value' => $entity->enable_sort,
	'options_values' => [
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	],
	'label' => elgg_echo('discussion:settings:enable_sort'),
	'help' => elgg_echo('discussion:settings:enable_sort:help'),
]);