<?php

$username = elgg_extract('username', $vars);

$user = get_user_by_username($username);
if (!$user || !$user->canEdit()) {
	forward('', '404');
}

$content = elgg_view('discussion/listing/friends', [
	'entity' => $user,
]);

if (elgg_is_xhr()) {
	echo $content;
	return;
}

$title = elgg_echo('discussion:friends');

$crumbs_title = $user->name;
elgg_push_breadcrumb($crumbs_title, "discussion/owner/{$user->username}");
elgg_push_breadcrumb(elgg_echo('friends'));

elgg_register_title_button();

$site_wide_discussions = elgg_get_plugin_setting('site_wide_discussions', 'hypeDiscussions');
$filter = $site_wide_discussions ? null : '';

$params = array(
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('discussion/sidebar'),
	'filter' => $filter,
	'filter_context' => $user->guid == elgg_get_logged_in_user_guid() ? 'friends' : false,
);

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);