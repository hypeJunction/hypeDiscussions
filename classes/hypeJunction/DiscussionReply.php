<?php

namespace hypeJunction;

/**
 * Discussion reply object
 */
class DiscussionReply extends Interactions\Comment {

	const TYPE = 'object';
	const SUBTYPE = 'discussion_reply';

	/**
	 * {@inheritdoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDisplayName() {
		$discussion = $this->getContainerEntity();

		if (!$discussion) {
			return parent::getDisplayName();
		}

		return elgg_echo('discussion:reply:title', [$discussion->getDisplayName()]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function canComment($user_guid = 0, $default = null) {
		$topic = $this->getOriginalContainer();
		if ($topic->status == 'closed') {
			return false;
		}
		return parent::canComment($user_guid, $default);
	}

	/**
	 * Discussion reply save action
	 * @return \hypeJunction\DiscussionReply|false
	 */
	public static function saveAction() {

		$guid = (int) get_input('guid');
		if (!$guid) {
			$action = 'create';
			$entity = new \hypeJunction\DiscussionReply();
			$container_guid = (int) get_input('topic_guid');
			$container = get_entity($container_guid);
		} else {
			$action = 'update';
			$entity = get_entity($guid);
			if (!$entity instanceof \hypeJunction\DiscussionReply) {
				register_error(elgg_echo('discussion:reply:error:notfound'));
				return false;
			}
			if (!$entity->canEdit()) {
				register_error(elgg_echo('discussion:reply:error:cannot_edit'));
				return false;
			}
			$container = $entity->getContainerEntity();
		}

		if (!$container instanceof \hypeJunction\Discussion || !$container->canWriteToContainer(0, 'object', 'discussion_reply')) {
			register_error(elgg_echo('discussion:reply:error:permissions'));
			return false;
		}

		$description = get_input('description');
		if (empty($description)) {
			register_error(elgg_echo('discussion:reply:missing'));
			return false;
		}

		$entity->description = $description;
		$entity->container_guid = $container->guid;
		$entity->access_id = $container->access_id;

		$result = $entity->save();

		if (!$result) {
			if ($action == 'update') {
				register_error(elgg_echo('discussion:reply:error'));
			} else {
				register_error(elgg_echo('discussion:post:failure'));
			}
		}

		if (elgg_is_active_plugin('hypeAttachments')) {
			hypeapps_attach_uploaded_files($entity, 'uploads', [
				'origin' => 'discussion_reply',
				'container_guid' => $entity->guid,
				'access_id' => $entity->access_id,
			]);
		}

		if ($action == 'update') {
			system_message(elgg_echo('discussion:reply:edited'));
		} else {
			system_message(elgg_echo('discussion:post:success'));
			elgg_create_river_item(array(
				'view' => 'river/object/discussion_reply/create',
				'action_type' => 'reply',
				'subject_guid' => elgg_get_logged_in_user_guid(),
				'object_guid' => $entity->guid,
				'target_guid' => $entity->container_guid,
			));
		}

		return $entity;
	}

}
