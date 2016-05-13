<?php

$entity_guid = elgg_extract('guid', $vars);
$reply_guid = elgg_extract('reply_guid', $vars);

$entity = get_entity($entity_guid);
if (!$entity instanceof \hypeJunction\Discussion) {
	forward('', '404');
}

$reply = get_entity($reply_guid);
/* @var $reply \hypeJunction\DiscussionReply */

if (elgg_is_xhr()) {
	echo elgg_view('framework/interactions/replies', array(
		'topic' => $entity,
		'reply' => $reply,
		'active_tab' => ($reply_guid) ? 'replies' : false,
	));
} else {
	echo elgg_view('resources/discussion/view', [
		'guid' => $entity->guid,
		'reply_guid' => $reply->guid,
	]);
}

