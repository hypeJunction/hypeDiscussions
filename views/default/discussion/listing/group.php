<?php

/**
 * Renders a list of discussion topics in a group
 *
 * @uses $vars['entity'] Group entity
 */

$entity = elgg_extract('entity', $vars);

$options = array(
	'type' => 'object',
	'subtype' => 'discussion',
	'limit' => max(20, elgg_get_config('default_limit')),
	'order_by' => 'e.last_action desc',
	'container_guid' => (int) $entity->guid,
	'full_view' => false,
	'no_results' => elgg_echo('discussion:none'),
	'preload_owners' => true,
	'list_id' => "discussions-group-$entity->guid",
	'base_url' => elgg_normalize_url("discussion/group/$entity->guid"),
	'pagination_type' => 'infinite',
);

$enable_sort = elgg_get_plugin_setting('enable_sort', 'hypeDiscussions');
$sort = elgg_get_plugin_user_setting('sort_discussions', 0, 'hypeDiscussions', 'last_action::desc');

if ($enable_sort) {
	$sort = get_input('sort', $sort);
}

echo elgg_view('lists/objects', [
	'show_filter' => false,
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

