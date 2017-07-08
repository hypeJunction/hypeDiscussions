<?php

use hypeJunction\Discussion;
use hypeJunction\DiscussionReply;
use hypeJunction\Interactions\Comment;
use hypeJunction\Interactions\InteractionsService;

$entity = elgg_extract('topic', $vars, false);
if (!$entity instanceof Discussion) {
	return;
}

$reply = elgg_extract('reply', $vars, false);
/* @var $reply Comment */

if (!$entity instanceof Discussion) {
	return true;
}

if (!elgg_is_active_plugin('hypeUI')) {
	if ($entity->status == 'closed') {
		echo elgg_view('discussion/closed');
	}
}

$full_view = elgg_extract('full_view', $vars, true);
$show_form = elgg_extract('show_add_form', $vars, true) && $entity->canReply();
$expand_form = elgg_extract('expand_form', $vars, !elgg_in_context('widgets'));

$sort = InteractionsService::getCommentsSort();
if ($reply && !in_array($sort, ['time_created::asc', 'time_created::desc'])) {
	$sort = 'time_created::desc';
}

$style = InteractionsService::getLoadStyle();
$form_position = InteractionsService::getCommentsFormPosition();
$limit = elgg_extract('limit', $vars, InteractionsService::getLimit(!$full_view));

$offset_key = "replies_$entity->guid";
$offset = get_input($offset_key, null);

$count = $entity->countReplies();

if (!isset($offset)) {
	$offset = InteractionsService::calculateOffset($count, $limit, $reply);
}

$level = elgg_extract('level', $vars) ? : 1;

$options = array(
	'types' => 'object',
	'subtypes' => array(DiscussionReply::SUBTYPE),
	'container_guid' => $entity->guid,
	'list_id' => "interactions-replies-{$entity->guid}",
	'list_class' => 'interactions-comments-list elgg-comments',
	'base_url' => elgg_normalize_url("stream/replies/$entity->guid"),
	'limit' => $limit,
	'offset' => $offset,
	'offset_key' => $offset_key,
	'full_view' => true,
	'pagination' => true,
	'pagination_type' => 'infinite',
	'lazy_load' => 0,
	'reversed' => $sort == 'time_created::asc',
	'auto_refresh' => 90,
	'no_results' => elgg_echo('discussion:replies:no_results'),
	'data-guid' => $entity->guid,
	'data-trait' => 'replies',
	'level' => $level,
);

elgg_push_context('replies');
$allow_sort = $level == 1 && (bool) elgg_get_plugin_setting('comment_sort', 'hypeInteractions');
$list = elgg_view('lists/objects', [
	'options' => $options,
	'show_filter' => $allow_sort,
	'show_sort' => $allow_sort,
	'show_search' => $allow_sort,
	'expand_form' => false,
	'sort_options' => [
		'time_created::desc',
		'time_created::asc',
		'likes_count::desc',
	],
	'sort' => get_input('sort', $sort),
]);
elgg_pop_context();

$form = '';
if ($show_form) {
	$form_class = [
		'interactions-form',
		'interactions-add-reply-form',
	];
	if (!$expand_form) {
		$form_class[] = 'hidden';
	}
	$form = elgg_view_form('discussion/reply/save', array(
		'class' => implode(' ', $form_class),
		'data-guid' => $entity->guid,
		'enctype' => 'multipart/form-data',
			), array(
		'topic' => $entity,
	));
}

if ($form_position == 'before') {
	echo $form . $list;
} else {
	echo $list . $form;
}