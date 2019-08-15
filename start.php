<?php

/**
 * Discussion Threads
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2016, Ismayil Khayredinov
 */
use hypeJunction\Discussions\Menus;
use hypeJunction\Discussions\Permissions;
use hypeJunction\Discussions\Router;
use hypeJunction\Discussions\Views;
use hypeJunction\Interactions\Notifications;

require_once __DIR__ . '/autoloader.php';

// Rewrite /discussions to /discussion, otherwise it's a constant hassle
elgg_register_plugin_hook_handler('route:rewrite', 'discussions', [Router::class, 'routeDiscussions']);

elgg_register_event_handler('init', 'system', function() {

	elgg_extend_view('elgg.css', 'forms/discussion/reply/save.css');

	// Add a group picker before the discussions edit form
	elgg_extend_view('forms/discussion/save', 'input/discussions/container', 100);

	// Handle reply URLs
	elgg_register_plugin_hook_handler('entity:url', 'object', [Router::class, 'urlHandler'], 600);
	
	// Register actions
	elgg_register_action('discussion/save', __DIR__ . '/actions/discussion/save.php');
	elgg_register_action('discussion/reply/save', __DIR__ . '/actions/discussion/reply/save.php');

	// Cleanup river menu
	elgg_unregister_plugin_hook_handler('register', 'menu:river', 'discussion_add_to_river_menu');

	// Setup interactions menu for discussions
	elgg_register_plugin_hook_handler('register', 'menu:interactions', [Menus::class, 'setupInteractionsMenu'], 700);
	elgg_register_plugin_hook_handler('register', 'menu:entity', [Menus::class, 'setupEntityMenu']);
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', [Menus::class, 'setupOwnerBlock']);

	// Route pages
	elgg_register_plugin_hook_handler('route', 'stream', [Router::class, 'routeStream']);
	elgg_register_plugin_hook_handler('route', 'discussion', [Router::class, 'routeDiscussion']);
	elgg_register_plugin_hook_handler('route', 'groups', [Router::class, 'routeGroups']);

	// Configure permissions
	elgg_register_plugin_hook_handler('permissions_check:comment', 'object', [Permissions::class, 'allowRepliesInThreadedDiscussions']);
	elgg_register_plugin_hook_handler('container_permissions_check', 'object', [Permissions::class, 'fixDiscussionContainerPermissions']);
	elgg_register_plugin_hook_handler('container_permissions_check', 'object', [Permissions::class, 'fixReplyContainerPermissions']);

	elgg_register_plugin_hook_handler('view_vars', 'forms/discussion/save', [Views::class, 'filterDiscussionFormVars']);

	// Cleanup group_tools registrations
	elgg_unregister_widget_type('start_discussion');
	elgg_unregister_widget_type('group_forum_topics');

	// Add a new discussion widget
	elgg_register_widget_type('discussion', elgg_echo('discussion:widget'), elgg_echo('discussion:widget:desc'), ['dashboard', 'profile', 'groups']);

	// Unregister discussion widget type if group forum is disabled
	elgg_register_plugin_hook_handler('view_vars', 'page/layouts/widgets', [Views::class, 'filterWidgetLayoutVars']);

	// Update access picker based on group selection in the discussion form
	elgg_register_ajax_view('input/discussions/access');

	// Allow admin only discussion creation in groups
	add_group_tool_option('admin_only_discussions', elgg_echo('group:discussion:admin_only'), false);

	// Register site menu item
	elgg_register_menu_item('site', [
		'name' => 'discussion',
		'href' => 'discussions',
		'text' => elgg_echo('discussion'),
	]);

	elgg_register_notification_event('object', 'discussion_reply', ['create']);
	elgg_unregister_plugin_hook_handler('prepare', 'notification:create:object:discussion_reply', 'discussion_prepare_reply_notification');
	elgg_register_plugin_hook_handler('prepare', 'notification:create:object:discussion_reply', [Notifications::class, 'format']);
});


