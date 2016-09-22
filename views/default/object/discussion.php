<?php

/**
 * Forum topic entity view
 */
$full = elgg_extract('full_view', $vars, FALSE);
$topic = elgg_extract('entity', $vars, FALSE);

if (!$topic) {
	return;
}

$poster = $topic->getOwnerEntity();
if (!$poster) {
	elgg_log("User {$topic->owner_guid} could not be loaded, and is needed to display entity {$topic->guid}", 'WARNING');
	if ($full) {
		forward('', '404');
	}
	return;
}

$poster_icon = elgg_view_entity_icon($poster, 'tiny');

$by_line = elgg_view('page/elements/by_line', $vars);

$tags = elgg_view('output/tags', array('tags' => $topic->tags));

$replies_link = '';
$reply_text = '';

$num_replies = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'discussion_reply',
	'container_guid' => $topic->getGUID(),
	'count' => true,
	'distinct' => false,
));

if ($num_replies != 0) {
	$last_reply = elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'discussion_reply',
		'container_guid' => $topic->getGUID(),
		'limit' => 1,
		'distinct' => false,
	));
	if (isset($last_reply[0])) {
		$last_reply = $last_reply[0];
	}
	/* @var ElggDiscussionReply $last_reply */

	$poster = $last_reply->getOwnerEntity();
	$reply_time = elgg_view_friendly_time($last_reply->time_created);

	$reply_text = elgg_view('output/url', [
		'text' => elgg_echo('discussion:updated', [$poster->name, $reply_time]),
		'href' => $last_reply->getURL(),
		'is_trusted' => true,
	]);

	$replies_link = elgg_view('output/url', array(
		'href' => $topic->getURL() . '#group-replies',
		'text' => elgg_echo('discussion:replies') . " ($num_replies)",
		'is_trusted' => true,
	));
}

// do not show the metadata and controls in widget view
$metadata = '';
if (!elgg_in_context('widgets')) {
	// only show entity menu outside of widgets
	$metadata = elgg_view_menu('entity', array(
		'entity' => $vars['entity'],
		'handler' => 'discussion',
		'sort_by' => 'priority',
		'class' => 'elgg-menu-hz',
	));
}

$status_indicator = '';
if ($topic->status == 'closed') {
	$status_indicator = elgg_format_element('span', [
		'class' => 'discussion-closed-indicator',
		'title' => elgg_echo('discussion:topic:closed'),
	], elgg_view_icon('lock'));
}

if ($full) {

	$title = elgg_view('output/url', array(
		'href' => $topic->getURL(),
		'text' => $topic->getDisplayName() . $status_indicator,
	));

	$subtitle = "$by_line $replies_link";

	$params = array(
		'entity' => $topic,
		'title' => false,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
	);
	$params = $params + $vars;
	$summary = elgg_view('object/elements/summary', $params);

	$body = elgg_view('output/longtext', array(
		'value' => $topic->description,
		'class' => 'elgg-discussion-body clearfix',
	));

	echo elgg_view('object/elements/full', array(
		'entity' => $topic,
		'summary' => $summary,
		'icon' => $poster_icon,
		'body' => $body,
	));
} else {

	if (elgg_is_active_plugin('search') && get_input('query')) {

		if ($topic->getVolatileData('search_matched_title')) {
			$title = $topic->getVolatileData('search_matched_title');
		} else {
			$title = search_get_highlighted_relevant_substrings($topic->getDisplayName(), get_input('query'), 5, 5000);
		}

		if ($topic->getVolatileData('search_matched_description')) {
			$excerpt = $topic->getVolatileData('search_matched_description');
		} else {
			$excerpt = search_get_highlighted_relevant_substrings($topic->description, get_input('query'), 5, 5000);
		}
	} else {
		$title = $topic->getDisplayName();
		$excerpt = elgg_get_excerpt($topic->description);
	}

	$title = elgg_view('output/url', array(
		'href' => $topic->getURL(),
		'text' => $title . $status_indicator,
	));

	// brief view
	$subtitle = "$by_line $replies_link <span class=\"float-alt\">$reply_text</span>";

	$params = array(
		'entity' => $topic,
		'title' => $title,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
		'content' => $excerpt,
	);
	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($poster_icon, $list_body);
}
