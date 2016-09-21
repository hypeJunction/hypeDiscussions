<?php

$guid = elgg_extract('guid', $vars);

if (!is_numeric($guid)) {
	$user = get_user_by_username($guid);
	if ($user) {
		$guid = $user->guid;
	}
}

elgg_entity_gatekeeper($guid);

$target = get_entity($guid);

elgg_set_page_owner_guid($guid);

$content = elgg_view('discussion/listing/owner', [
	'entity' => $target,
]);

if (elgg_is_xhr()) {
	echo $content;
	return;
}

if ($target instanceof ElggGroup) {
	// Before Elgg 2.0 only groups could work as containers for discussions.
	// Back then the URL that listed all discussions within a group was
	// "discussion/owner/<guid>". Now that any entity can be used as a
	// container, we use the standard "<content type>/group/<guid>" URL
	// also with discussions.
	forward("discussion/group/$guid", '301');
}

elgg_push_breadcrumb(elgg_echo('item:object:discussion'));

elgg_register_title_button();

$title = elgg_echo('item:object:discussion');

$site_wide_discussions = elgg_get_plugin_setting('site_wide_discussions', 'hypeDiscussions');
$filter = $site_wide_discussions ? null : '';

$params = array(
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('discussion/sidebar'),
	'filter' => $filter,
	'filter_context' => $target->guid == elgg_get_logged_in_user_guid() ? 'mine' : false,
);

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
