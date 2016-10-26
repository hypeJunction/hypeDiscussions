<?php

namespace hypeJunction\Discussions;

use ElggGroup;
use hypeJunction\DiscussionReply;

class Permissions {

	/**
	 * Enable discussion threads
	 *
	 * @param string $hook   "permissions_check:comment"
	 * @param string $type   "object"
	 * @param bool   $return Can comment
	 * @param array  $params Hook params
	 * @return boolean
	 */
	public static function allowRepliesInThreadedDiscussions($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);

		if ($entity instanceof DiscussionReply) {
			$discussion = $entity->getContainerEntity();
			$threads = $discussion->threads;
			if (!$threads) {
				return false;
			}
			return $discussion->canWriteToContainer(0, 'object', 'discussion_reply');
		}
	}

	/**
	 * Check group settings to disallow creation of new discussions
	 *
	 * @param string $hook   "container_permissions_check"
	 * @param string $type   "object"
	 * @param bool   $return Permission
	 * @param array  $params Hook params
	 * @return bool
	 */
	public static function fixDiscussionContainerPermissions($hook, $type, $return, $params) {

		$user = elgg_extract('user', $params);
		$container = elgg_extract('container', $params);
		$subtype = elgg_extract('subtype', $params);

		if ($subtype !== 'discussion') {
			return;
		}

		if (!$container instanceof ElggGroup) {
			if (!elgg_get_plugin_setting('site_wide_discussions', 'hypeDiscussions')) {
				return false;
			}
			return;
		} else {
			if ($container->forum_enable == 'no') {
				return false;
			}

			if ($container->admin_only_discussions_enable == 'yes' && !$container->canEdit($user->guid)) {
				return false;
			}
		}
	}

	/**
	 * Discussion replies should not inherit permissions from discussion but from the parent (group)
	 *
	 * @param string $hook   "container_permissions_check"
	 * @param string $type   "object"
	 * @param bool   $return Permission
	 * @param array  $params Hook params
	 * @return bool
	 */
	public static function fixReplyContainerPermissions($hook, $type, $return, $params) {

		$user = elgg_extract('user', $params);
		$container = elgg_extract('container', $params);
		$subtype = elgg_extract('subtype', $params);

		if (!elgg_instanceof($container, 'object', 'discussion') || $subtype !== 'discussion_reply') {
			return;
		}

		$group = $container->getContainerEntity();
		if ($group instanceof ElggGroup) {
			if ($group->forum_enable == 'no') {
				return false;
			}
			return $group->canWriteToContainer($user->guid);
		}
	}

}
