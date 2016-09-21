<?php

$page_owner_guid = elgg_get_page_owner_guid();

$container_guid = elgg_extract('container_guid', $vars);
elgg_set_page_owner_guid($container_guid);

$entity = elgg_extract('entity', $vars);

echo elgg_view('input/access', array(
	'name' => 'access_id',
	'value' => isset($entity->access_id) ? $entity->access_id : ACCESS_DEFAULT,
	'entity' => $entity,
	'entity_type' => 'object',
	'entity_subtype' => 'discussion',
));

elgg_set_page_owner_guid($page_owner_guid);