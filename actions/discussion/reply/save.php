<?php

/**
 * Save a discussion reply
 */
$entity = \hypeJunction\DiscussionReply::saveAction();
if ($entity) {
	$container = $entity->getContainerEntity();
	if (elgg_is_xhr()) {
		$view = elgg_view('framework/interactions/replies', [
			'topic' => $container,
			'reply' => $entity,
		]);

		$output = array(
			'guid' => $container->guid,
			'view' => $view,
			'stats' => $container->getStats(),
		);
		echo json_encode($output);
	}
	forward($entity->getURL());
}