<?php

$guid = elgg_extract('guid', $vars);
$reply_guid = elgg_extract('reply_guid', $vars);

elgg_register_rss_link();

elgg_entity_gatekeeper($guid, 'object', 'discussion');

$entity = get_entity($guid);
elgg_group_gatekeeper(true, $entity->container_guid);

$container = $entity->getContainerEntity();
elgg_set_page_owner_guid($container->guid);

if ($container instanceof ElggGroup) {
	$owner_url = "discussion/group/$container->guid";
} else {
	$owner_url = "discussion/owner/$container->guid";
}

elgg_push_breadcrumb($container->getDisplayName(), $owner_url);
elgg_push_breadcrumb($entity->title);

$content = elgg_view_entity($entity, [
	'full_view' => true,
]);

if (!elgg_is_active_plugin('hypeUI')) {
	$content .= elgg_view('discussion/replies', [
		'topic' => $entity,
		'reply' => get_entity($reply_guid),
		'show_add_form' => $entity->canWriteToContainer(0, 'object', 'discussion_reply'),
		'expand_form' => true,
		'full_view' => true,
	]);
}

$params = array(
	'content' => $content,
	'title' => $entity->title,
	'sidebar' => elgg_view('discussion/sidebar'),
	'filter' => '',
	'class' => 'elgg-discussion-layout',
);
$body = elgg_view_layout('content', $params);

echo elgg_view_page($entity->title, $body);
