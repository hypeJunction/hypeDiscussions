<?php

/**
 * Renders a list of discussions, optionally filtered by container type
 *
 * @uses $vars['container_type'] Container type filter to apply
 */

$options = array(
	'type' => 'object',
	'subtype' => 'discussion',
	'order_by' => 'e.last_action desc',
	'limit' => elgg_extract('limit', $vars, max(20, elgg_get_config('default_limit'))),
	'full_view' => false,
	'no_results' => elgg_echo('discussion:none'),
	'preload_owners' => true,
	'preload_containers' => true,
	'list_id' => 'discussions-all',
	'base_url' => elgg_normalize_url('discussion/all'),
	'pagination_type' => 'infinite',
	'pagination' => elgg_extract('pagination', $vars),
);

$container_type = elgg_extract('container_type', $vars);
if ($container_type) {
	$dbprefix = elgg_get_config('dbprefix');
	$container_type = sanitize_string($container_type);
	$options['joins'][] = "JOIN {$dbprefix}entities ce ON ce.guid = e.container_guid";
	$options['wheres'][] = "ce.type = '$container_type'";
}

$enable_sort = elgg_get_plugin_setting('enable_sort', 'hypeDiscussions');
$sort = elgg_get_plugin_user_setting('sort_discussions', 0, 'hypeDiscussions', 'last_action::desc');

if ($enable_sort) {
	$sort = get_input('sort', $sort);
}

echo elgg_view('lists/objects', [
	'show_filter' => $enable_sort,
	'filter_target' => elgg_get_logged_in_user_entity(),
	'show_search' => $enable_sort,
	'show_sort' => $enable_sort,
	'sort_options' => [
		'last_action::desc',
		'last_action::asc',
		'time_created::desc',
		'time_created::asc',
		'responses_count::desc',
		'likes_count::desc',
	],
	'sort' => $sort,
	'options' => $options,
]);

