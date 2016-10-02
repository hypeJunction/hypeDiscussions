<?php
$entity = elgg_extract('topic', $vars);
if (!$entity instanceof \hypeJunction\Discussion) {
	return;
}

$vars['active_tab'] = $entity->countReplies() ? 'replies' : false;
$vars['entity'] = $vars['topic'];
$vars['comment'] = $vars['reply'];
$vars['expand_form'] = !elgg_in_context('activity') && !elgg_in_context('widgets');
?>
<div id="group-replies">
	<?php
	echo elgg_view('page/components/interactions', $vars);
	?>
	<script>
		require(['page/components/interactions', 'discussion/replies']);
	</script>
</div>