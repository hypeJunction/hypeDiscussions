<?php

namespace hypeJunction\Discussions;

use ElggGroup;

class Views {

	/**
	 * Filter form variables
	 *
	 * @param string $hook   "view_vars"
	 * @param string $type   "forms/discussions/save"
	 * @param array  $return View vars
	 * @param array  $params Hook params
	 * @return array
	 */
	public static function filterDiscussionFormVars($hook, $type, $return, $params) {

		$guid = elgg_extract('guid', $return);
		$container_guid = elgg_extract('container_guid', $return);
		if ($container_guid) {
			return;
		}

		$entity = null;
		if ($guid) {
			$entity = get_entity($guid);
		}

		if ($entity) {
			$return['entity'] = $entity;
			$container_guid = $entity->getContainerGUID();
		} else {
			$page_owner = elgg_get_page_owner_entity();
			if ($page_owner instanceof ElggGroup) {
				$container_guid = $page_owner->guid;
			}
		}

		$return['container_guid'] = $container_guid;
		return $return;
	}

	/**
	 * Remove discussions widget from group context if discussions are not enabled
	 *
	 * @param string $hook   "view_vars"
	 * @param string $type   "page/layouts/widgets"
	 * @param array  $return View vars
	 * @param array  $params Hook params
	 * @return void
	 */
	public static function filterWidgetLayoutVars($hook, $type, $return, $params) {

		//$owner_guid = elgg_extract('owner_guid', $return, elgg_get_page_owner_guid()); // not yet supported
		$owner = elgg_get_page_owner_entity();
		if ($owner instanceof ElggGroup && $owner->forum_enable != 'yes') {
			elgg_unregister_widget_type('group_discussions');
		}
	}

}
