<?php

$entity = elgg_extract('entity', $vars);
/* @var $entity ElggWidget */

$page_owner = elgg_get_page_owner_entity();
$href = $page_owner instanceof ElggUser ? "discussion/owner/$page_owner->guid" : "discussion/group/$page_owner->guid";

if ($page_owner) {
	echo elgg_view('discussion/listing/owner', array(
		'entity' => $page_owner,
		'limit' => $entity->num_display ? : 5,
		'pagination' => false,
	));
} else {
	echo elgg_view('discussion/listing/all', array(
		'limit' => $entity->num_display ? : 5,
		'pagination' => false,
	));
}

$add_link = elgg_view('output/url', array(
	'text' => elgg_echo('discussion:add'),
	'href' => "discussion/add/$page_owner->guid",
	'class' => 'mrl',
));

$more_link = elgg_view('output/url', array(
	'text' => elgg_echo('link:view:all'),
	'href' => elgg_normalize_url($href),
));

echo elgg_format_element('span', [
	'class' => 'elgg-widget-more',
], $add_link . $more_link);
