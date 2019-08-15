<?php

namespace hypeJunction\Discussions;

use ElggMenuItem;

class Menus {

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
	public static function setupInteractionsMenu($hook, $type, $menu, $params) {
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
						'priority' => 200,
						'data-trait' => 'replies',
						'item_class' => 'interactions-action',
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
						'priority' => 100,
						'data-trait' => 'replies',
						'item_class' => 'interactions-tab',
			));
		}

		$remove = ['comments', 'comments:badge'];

		foreach ($menu as $key => $item) {
			if ($item instanceof ElggMenuItem && in_array($item->getName(), $remove)) {
				unset($menu[$key]);
			}
		}

		return $menu;
	}

	/**
	 * Setup entity menu
	 *
	 * @param string $hook   "register"
	 * @param string $type   "menu:entity"
	 * @param array  $menu   Menu
	 * @param array  $params Hook parameters
	 * @return array
	 */
	public static function setupEntityMenu($hook, $type, $menu, $params) {

		$entity = elgg_extract('entity', $params);

		if (!$entity instanceof \hypeJunction\DiscussionReply) {
			return;
		}

		foreach ($menu as &$item) {
			if ($item->getName() == 'edit') {
				$item->addItemClass('interactions-edit-discussion-reply');
			}
		}

		return $menu;
	}

	/**
	 * Setup owner block menu
	 *
	 * @param string $hook   "register"
	 * @param string $type   "menu:owner_block"
	 * @param array  $menu   Menu
	 * @param array  $params Hook parameters
	 * @return array
	 */
	public static function setupOwnerBlock($hook, $type, $menu, $params) {

		$entity = elgg_extract('entity', $params);

		if (!$entity instanceof \ElggUser) {
			return;
		}

		if (!elgg_get_plugin_setting('site_wide_discussions', 'hypeDiscussions')) {
			return;
		}

		$menu[] = \ElggMenuItem::factory([
			'name' => 'discussions',
			'href' => "discussion/owner/$entity->username",
			'text' => elgg_echo('discussion'),
		]);

		return $menu;
	}

}
