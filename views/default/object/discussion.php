<?php

/**
 * Forum topic entity view
 */
$full = elgg_extract('full_view', $vars, FALSE);
$entity = elgg_extract('entity', $vars, FALSE);

if (!$entity) {
	return;
}

if (!$entity->getOwnerEntity() || !$entity->getContainerEntity()) {
	if ($full) {
		forward(REFERRER);
	}
	return;
}

if (elgg_is_active_plugin('hypeUI')) {
	$vars['responses'] = function($entity, $full) {

		if ($full) {
			$content = elgg_view('discussion/replies', [
				'topic' => $entity,
				'show_add_form' => $entity->canWriteToContainer(0, 'object', 'discussion_reply') && $entity->status != 'closed',
			]);

			if ($entity->status == 'closed') {
				$content .= elgg_view('discussion/closed');
			}

			return $content;
		} else {
			$last_reply = elgg_get_entities([
				'type' => 'object',
				'subtype' => 'discussion_reply',
				'container_guid' => $entity->guid,
				'limit' => 1,
				'distinct' => false,
			]);

			if ($last_reply) {
				$last_reply = array_shift($last_reply);
				/* @var ElggDiscussionReply $last_reply */

				$poster = $last_reply->getOwnerEntity();
				$reply_time = elgg_view_friendly_time($last_reply->time_created);

				return elgg_view('output/url', [
					'text' => elgg_echo('discussion:updated', [$poster->name, $reply_time]),
					'href' => $last_reply->getURL(),
					'is_trusted' => true,
				]);
			}
		}
	};

	if ($entity->status == 'closed') {
		$vars['badges'] = elgg_format_element('span', [
			'class' => 'tag is-danger',
		], elgg_view_icon('lock'));
	}

	if ($full) {
		echo elgg_view('object/elements/full', $vars);
	} elseif (elgg_in_context('gallery')) {
		echo elgg_view('object/elements/card', $vars);
	} else {
		echo elgg_view('object/elements/summary', $vars);
	}
} else {
	$poster = $entity->getOwnerEntity();
	if (!$poster) {
		elgg_log("User {$entity->owner_guid} could not be loaded, and is needed to display entity {$entity->guid}", 'WARNING');
		if ($full) {
			forward('', '404');
		}

		return;
	}

	$poster_icon = elgg_view_entity_icon($poster, 'tiny');

	$by_line = elgg_view('page/elements/by_line', $vars);

	$tags = elgg_view('output/tags', ['tags' => $entity->tags]);

	$replies_link = '';
	$reply_text = '';

	$num_replies = elgg_get_entities([
		'type' => 'object',
		'subtype' => 'discussion_reply',
		'container_guid' => $entity->getGUID(),
		'count' => true,
		'distinct' => false,
	]);

	if ($num_replies != 0) {
		$last_reply = elgg_get_entities([
			'type' => 'object',
			'subtype' => 'discussion_reply',
			'container_guid' => $entity->getGUID(),
			'limit' => 1,
			'distinct' => false,
		]);
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

		$replies_link = elgg_view('output/url', [
			'href' => $entity->getURL() . '#group-replies',
			'text' => elgg_echo('discussion:replies') . " ($num_replies)",
			'is_trusted' => true,
		]);
	}

	// do not show the metadata and controls in widget view
	$metadata = '';
	if (!elgg_in_context('widgets')) {
		// only show entity menu outside of widgets
		$metadata = elgg_view_menu('entity', [
			'entity' => $vars['entity'],
			'handler' => 'discussion',
			'sort_by' => 'priority',
			'class' => 'elgg-menu-hz',
		]);
	}

	$status_indicator = '';
	if ($entity->status == 'closed') {
		$status_indicator = elgg_format_element('span', [
			'class' => 'discussion-closed-indicator',
			'title' => elgg_echo('discussion:topic:closed'),
		], elgg_view_icon('lock'));
	}

	if ($full) {

		$title = elgg_view('output/url', [
			'href' => $entity->getURL(),
			'text' => $entity->getDisplayName() . $status_indicator,
		]);

		$subtitle = "$by_line $replies_link";

		$params = [
			'entity' => $entity,
			'title' => false,
			'metadata' => $metadata,
			'subtitle' => $subtitle,
			'tags' => $tags,
		];
		$params = $params + $vars;
		$summary = elgg_view('object/elements/summary', $params);

		$body = elgg_view('output/longtext', [
			'value' => $entity->description,
			'class' => 'elgg-discussion-body clearfix',
		]);

		echo elgg_view('object/elements/full', [
			'entity' => $entity,
			'summary' => $summary,
			'icon' => $poster_icon,
			'body' => $body,
		]);
	} else {

		if (elgg_is_active_plugin('search') && get_input('query')) {

			if ($entity->getVolatileData('search_matched_title')) {
				$title = $entity->getVolatileData('search_matched_title');
			} else {
				$title = search_get_highlighted_relevant_substrings($entity->getDisplayName(), get_input('query'), 5, 5000);
			}

			if ($entity->getVolatileData('search_matched_description')) {
				$excerpt = $entity->getVolatileData('search_matched_description');
			} else {
				$excerpt = search_get_highlighted_relevant_substrings($entity->description, get_input('query'), 5, 5000);
			}
		} else {
			$title = $entity->getDisplayName();
			$excerpt = elgg_get_excerpt($entity->description);
		}

		$title = elgg_view('output/url', [
			'href' => $entity->getURL(),
			'text' => $title . $status_indicator,
		]);

		// brief view
		$subtitle = "$by_line $replies_link <span class=\"float-alt\">$reply_text</span>";

		$params = [
			'entity' => $entity,
			'title' => $title,
			'metadata' => $metadata,
			'subtitle' => $subtitle,
			'tags' => $tags,
			'content' => $excerpt,
		];
		$params = $params + $vars;
		$list_body = elgg_view('object/elements/summary', $params);

		echo elgg_view_image_block($poster_icon, $list_body);
	}
}