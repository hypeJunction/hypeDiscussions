<?php

namespace hypeJunction\Discussions;

use hypeJunction\Discussion;
use hypeJunction\DiscussionReply;
use hypeJunction\Interactions\InteractionsService;

class Router {

	/**
	 * Rewrite /discussions route for convenience
	 *
	 * @param string $hook   "route:rewrite"
	 * @param string $type   "discussions"
	 * @param array  $return Identifier and segments
	 * @param array  $params Hook params
	 * @return array
	 */
	public static function routeDiscussions($hook, $type, $return, $params) {
		$identifier = elgg_extract('identifier', $return);
		if ($identifier == 'discussions') {
			$return['identifier'] = 'discussion';
		}
		return $return;
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
	public static function routeStream($hook, $type, $return, $params) {

		$segments = elgg_extract('segments', $return);

		$page = array_shift($segments);

		switch ($page) {
			case 'replies' :
				echo elgg_view_resource('interactions/replies', [
					'guid' => array_shift($segments),
					'reply_guid' => array_shift($segments),
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
	public static function routeDiscussion($hook, $type, $return, $params) {

		if (empty($return)) {
			return;
		}

		$segments = elgg_extract('segments', $return);

		$page = array_shift($segments);

		switch ($page) {
			case 'friends' :
				$username = array_shift($segments);
				echo elgg_view_resource('discussion/friends', [
					'username' => $username,
				]);
				return false;

			case 'reply' :
				$subpage = array_shift($segments);
				if ($subpage !== 'view') {
					return;
				}

				$guid = array_shift($segments);

				elgg_entity_gatekeeper($guid, 'object', DiscussionReply::SUBTYPE);

				$reply = get_entity($guid);

				elgg_entity_gatekeeper($reply->container_guid, 'object', Discussion::SUBTYPE);

				$topic = $reply->getContainerEntity();

				$offset_key = "replies_{$topic->guid}";

				$count = $topic->countReplies();

				$offset = InteractionsService::calculateOffset($count, $limit, $reply);
				$limit = get_input('limit', InteractionsService::getLimit(!$full_view));

				$topic_url = elgg_http_add_url_query_elements($topic->getURL());
				$forward_url = elgg_http_add_url_query_elements($topic_url, [
					$offset_key => $offset,
					'limit' => $limit,
				]) . "#elgg-object-$reply->guid";

				forward($forward_url);
				break;

		}
	}

	/**
	 * Handler for /groups/all?filter=discussion
	 *
	 * @param string $hook   "route"
	 * @param string $type   "groups"
	 * @param mixed  $return Route
	 * @param array  $params Hook params
	 * @return mixed
	 */
	public function routeGroups($hook, $type, $return, $params) {

		if (!is_array($return)) {
			return;
		}

		$identifier = elgg_extract('identifier', $return);
		$segments = (array) elgg_extract('segments', $return);

		if ($identifier !== 'groups') {
			return;
		}

		if ($segments[0] != 'all') {
			return;
		}

		if (get_input('filter') !== 'discussion') {
			return;
		}

		echo elgg_view_resource('discussion/all');

		return false;
	}

	/**
	 * Handles entity URLs
	 *
	 * @param string $hook   "entity:url"
	 * @param string $type   "object"
	 * @param string $url    Current URL
	 * @param array  $params Hook params
	 * @return string Filtered URL
	 */
	public static function urlHandler($hook, $type, $url, $params) {

		$entity = elgg_extract('entity', $params);
		
		if (!$entity instanceof DiscussionReply) {
			return;
		}

		return "discussion/reply/view/$entity->guid/$entity->container_guid";
	}
}
