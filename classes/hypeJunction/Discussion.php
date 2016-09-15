<?php

namespace hypeJunction;

/**
 * Discussion object
 */
class Discussion extends \ElggObject {

	const TYPE = 'object';
	const SUBTYPE = 'discussion';

	/**
	 * {@inheritdoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
	}

	/**
	 * Count replies
	 * @return int
	 */
	public function countReplies() {
		return elgg_get_entities(array(
			'type' => 'object',
			'subtype' => DiscussionReply::SUBTYPE,
			'container_guid' => (int) $this->guid,
			'count' => true,
		));
	}

	/**
	 * Can user reply to this discussion
	 *
	 * @param int $user_guid User guid (0 for logged in user)
	 * @return bool
	 */
	public function canReply($user_guid = 0) {
		if ($this->status == 'closed') {
			return false;
		}
		return $this->canWriteToContainer($user_guid, 'object', DiscussionReply::SUBTYPE);
	}

	/**
	 * Returns entity statistics
	 * @return array
	 */
	public function getStats() {
		$stats = array(
			'replies' => array(
				'count' => $this->countReplies()
			),
			'likes' => array(
				'count' => $this->countAnnotations('likes'),
				'state' => (elgg_annotation_exists($entity->guid, 'likes')) ? 'after' : 'before',
			)
		);

		return elgg_trigger_plugin_hook('get_stats', 'interactions', array('entity' => $entity), $stats);
	}

	/**
	 * Saves discussion from form input
	 * @return \hypeJunction\Discussion|false
	 */
	public static function saveAction() {

		$guid = get_input('topic_guid');
		if (!$guid) {
			$action = 'create';
			$entity = new \hypeJunction\Discussion();
			$container_guid = get_input('container_guid');
			$container = get_entity($container_guid);
		} else {
			$action = 'update';
			$entity = get_entity($guid);
			if (!$entity instanceof \hypeJunction\Discussion || !$entity->canEdit()) {
				register_error(elgg_echo('discussion:topic:notfound'));
				return false;
			}
			$container = $entity->getContainerEntity();
		}

		if (!$container || !$container->canWriteToContainer(0, 'object', \hypeJunction\Discussion::SUBTYPE)) {
			register_error(elgg_echo('discussion:error:permissions'));
			return false;
		}

		$title = htmlspecialchars(get_input('title', '', false), ENT_QUOTES, 'UTF-8');
		$description = get_input('description');

		if (!$title || !$description) {
			register_error(elgg_echo('discussion:error:missing'));
			return false;
		}


		$entity->title = $title;
		$entity->description = $description;
		$entity->status = get_input('status', 'open');
		$entity->access_id = get_input('access_id', $container->access_id);
		$entity->container_guid = $container->guid;
		$entity->threads = (bool) get_input('threads');
		$entity->tags = string_to_tag_array(get_input('tags', ''));

		$result = $entity->save();

		if (!$result) {
			register_error(elgg_echo('discussion:error:notsaved'));
			return false;
		}

		if (elgg_is_active_plugin('hypeAttachments')) {
			hypeapps_attach_uploaded_files($entity, 'uploads', [
				'origin' => 'discussion',
				'container_guid' => $entity->guid,
				'access_id' => $entity->access_id,
			]);
		}

		if ($action == 'update') {
			system_message(elgg_echo('discussion:topic:updated'));
		} else {
			system_message(elgg_echo('discussion:topic:created'));
			elgg_create_river_item([
				'view' => 'river/object/discussion/create',
				'action_type' => 'create',
				'subject_guid' => elgg_get_logged_in_user_guid(),
				'object_guid' => $entity->guid,
				'target_guid' => $entity->container_guid,
			]);
		}

		return $entity;
	}

}
