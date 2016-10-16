<?php

/**
 * Renders a list of discussion topics by an owner
 *
 * @uses $vars['entity'] Owner/target entity
 */

$entity = elgg_extract('entity', $vars);

$options = array(
	'type' => 'object',
	'subtype' => 'discussion',
	'limit' => elgg_extract('limit', $vars, max(20, elgg_get_config('default_limit'))),
	'order_by' => 'e.last_action desc',
	'full_view' => false,
	'no_results' => elgg_echo('discussion:none'),
	'preload_owners' => true,
	'list_id' => "discussions-owner-$entity->guid",
	'base_url' => elgg_normalize_url("discussion/owner/$entity->guid"),
	'pagination_type' => 'infinite',
	'pagination' => elgg_extract('pagination', $vars),
);

if ($entity instanceof ElggUser) {
	// Display all discussions started by the user regardless of
	// the entity that is working as a container. See #4878.
	$options['owner_guid'] = (int) $entity->guid;
} else {
	$options['container_guid'] = (int) $entity->guid;
}

$enable_sort = !elgg_in_context('widgets') && elgg_get_plugin_setting('enable_sort', 'hypeDiscussions');
$sort = elgg_get_plugin_user_setting('sort_discussions', 0, 'hypeDiscussions', 'last_action::desc');

if ($enable_sort) {
	$sort = get_input('sort', $sort);
}

echo elgg_view('lists/objects', [
	'show_filter' => false,
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

