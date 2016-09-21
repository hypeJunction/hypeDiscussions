<?php
$container_guid = (int) elgg_extract('container_guid', $vars);
if ($container_guid) {
	return;
}

$dbprefix = elgg_get_config('dbprefix');
$user = elgg_get_logged_in_user_entity();
$groups = new ElggBatch('elgg_get_entities_from_relationship', array(
	'relationship' => 'member',
	'relationship_guid' => (int) $user->guid,
	'inverse_relationship' => false,
	'limit' => 0,
));

$options_values = array();
foreach ($groups as $group) {
	if ($group->canWriteToContainer(0, 'group', 'discussion')) {
		$options_values["$group->guid"] = $group->name;
	}
}

$options_values = elgg_trigger_plugin_hook('allowed_containers', 'object:discussion', $vars, $options_values);
if (empty($options_values)) {
	// @todo: do we need to terminate form rendering all together?
}

asort($options_values);

$placeholder = array('' => elgg_echo('discussion:group:container:select'));
$options_values = $placeholder + $options_values;

echo elgg_view_input('select', array(
	'options_values' => $options_values,
	'class' => 'select-discussion-container',
	'required' => true,
	'label' => elgg_echo('discussion:group:container'),
));
?>
<script>
	require(['input/discussions/container']);
</script>

