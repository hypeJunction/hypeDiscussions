<?php

/**
 * Saves a discussion
 */

elgg_make_sticky_form('topic');
$entity = \hypeJunction\Discussion::saveAction();
if ($entity) {
	elgg_clear_sticky_form('topic');
	if (elgg_is_xhr()) {
		$output = array(
			'guid' => $entity->guid,
			'view' => elgg_view_entity($entity, [
				'full_view' => true,
			]),
		);
		echo json_encode($output);
	}
	forward($entity->getURL());
}
