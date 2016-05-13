<?php

/**
 * Save a discussion reply
 */
$entity = \hypeJunction\DiscussionReply::saveAction();
if ($entity) {
	$container = $entity->getContainerEntity();
	if (elgg_is_xhr()) {
		$output = array(
			'guid' => $container->guid,
			'view' => elgg_view_entity($entity, [
				'full_view' => true,
			]),
			'stats' => $container->getStats(),
		);
		echo json_encode($output);
	}
	forward($entity->getURL());
}