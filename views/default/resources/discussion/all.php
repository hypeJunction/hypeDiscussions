<?php

$content = elgg_view('discussion/listing/all');

if (elgg_is_xhr()) {
	echo $content;
	return;
}

elgg_pop_breadcrumb();
elgg_push_breadcrumb(elgg_echo('discussion'));

elgg_register_title_button();

$title = elgg_echo('discussion:latest');

if (elgg_in_context('groups')) {
	if (elgg_get_plugin_setting('limited_groups', 'groups') != 'yes' || elgg_is_admin_logged_in()) {
		elgg_register_title_button('groups', 'add', 'group');
	}
	$filter = elgg_view('groups/group_sort_menu', array('selected' => $selected_tab));

	$sidebar = elgg_view('groups/sidebar/find');
	$sidebar .= elgg_view('groups/sidebar/featured');
	$title = null;
	$filter_context = 'discussions';
} else {
	$title = elgg_echo('discussion:latest');
	$sidebar = elgg_view('discussion/sidebar');
	$site_wide_discussions = elgg_get_plugin_setting('site_wide_discussions', 'hypeDiscussions');
	$filter = $site_wide_discussions ? null : '';
	$filter_context = 'all';
}

$params = array(
	'content' => $content,
	'title' => $title,
	'sidebar' => $sidebar,
	'filter' => $filter,
	'filter_context' => $filter_context,
);

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
