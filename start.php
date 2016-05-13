<?php

/**
 * Discussions Threads
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2016, Ismayil Khayredinov
 */
require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', 'hypediscussions_init');

/**
 * Initialize the plugin
 * @return void
 */
function hypediscussions_init() {

	elgg_register_action('discussion/save', __DIR__ . '/actions/discussion/save.php');
	elgg_register_action('discussion/reply/save', __DIR__ . '/actions/discussion/reply/save.php');

	elgg_unregister_plugin_hook_handler('register', 'menu:river', 'discussion_add_to_river_menu');
	elgg_register_plugin_hook_handler('register', 'menu:interactions', 'hypediscussions_interactions_menu_setup');

	elgg_register_plugin_hook_handler('route', 'stream', 'hypediscussions_route_stream');
	elgg_register_plugin_hook_handler('route', 'discussion', 'hypediscussions_route_discussion');

	elgg_register_plugin_hook_handler('permissions_check:comment', 'object', 'hypediscussions_discussion_reply_comments');

	elgg_extend_view('elgg.css', 'forms/discussion/reply/save.css');
}

/**
 * Setups entity interactions menu
 *
 * @param string $hook   "register"
 * @param string $type   "menu:interactions"
 * @param array  $menu   Menu
 * @param array  $params Hook parameters
 * @uses $params['entity'] An entity that we are interacting with
 * @uses $params['active_tab'] Currently active tab, default to 'replies'
 * @return array
 */
function hypediscussions_interactions_menu_setup($hook, $type, $menu, $params) {

	$entity = elgg_extract('entity', $params, false);
	/* @var \hypeJunction\Discussion $entity */

	if (!elgg_instanceof($entity, 'object', 'discussion')) {
		return $menu;
	}

	$active_tab = elgg_extract('active_tab', $params);

// Replies
	$replies_count = $entity->countReplies();
	$can_reply = $entity->canReply();

	if ($can_reply) {
		$menu[] = ElggMenuItem::factory(array(
					'name' => 'replies',
					'text' => elgg_echo('interactions:reply:create'),
					'href' => "stream/replies/$entity->guid",
					'priority' => 100,
					'data-trait' => 'replies',
					'section' => 'actions',
		));
	}

	if ($can_reply || $replies_count) {
		$menu[] = ElggMenuItem::factory(array(
					'name' => 'replies:badge',
					'text' => elgg_view('framework/interactions/elements/badge', array(
						'entity' => $entity,
						'icon' => 'comments',
						'type' => 'replies',
						'count' => $replies_count,
					)),
					'href' => "stream/replies/$entity->guid",
					'selected' => ($active_tab == 'replies'),
					'priority' => 500,
					'data-trait' => 'replies',
					'section' => 'tabs',
		));
	}

	return $menu;
}

/**
 * Route stream page
 * 
 * @param string $hook   "route"
 * @param string $type   "stream"
 * @param array  $return Identifier and segments
 * @param array  $params Hook params
 * @return array
 */
function hypediscussions_route_stream($hook, $type, $return, $params) {

	$segments = elgg_extract('segments', $return);

	switch ($segments[0]) {
		case 'replies' :
			echo elgg_view_resource('interactions/replies', [
				'guid' => $segments[1],
				'reply_guid' => $segments[2],
			]);
			return false;
	}
}

/**
 * Route discussion page
 *
 * @param string $hook   "route"
 * @param string $type   "discussion"
 * @param array  $return Identifier and segments
 * @param array  $params Hook params
 * @return array
 */
function hypediscussions_route_discussion($hook, $type, $return, $params) {

	if (empty($return)) {
		return;
	}

	$segments = elgg_extract('segments', $return);

	switch ($segments[0]) {
		case 'view' :
			$reply_guid = get_entity($segments[1]);
			if ($reply_guid) {
				$reply = get_entity($reply_guid);
				if (!$reply) {
					return;
				}
			}
			echo elgg_view_resource('interactions/replies', [
				'guid' => $reply->container_guid,
				'reply_guid' => $reply->guid,
			]);
			return false;
	}
}

/**
 * Enable discussion threads
 *
 * @param string $hook   "permissions_check:comment"
 * @param string $type   "object"
 * @param bool   $return Can comment
 * @param array  $params Hook params
 * @return boolean
 */
function hypediscussions_discussion_reply_comments($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);

	if ($entity instanceof \hypeJunction\DiscussionReply) {
		$discussion = $entity->getContainerEntity();
		$threads = $discussion->threads;
		if (!$threads) {
			return false;
		}
		return $discussion->canWriteToContainer(0, 'object', 'discussion_reply');
	}
}
