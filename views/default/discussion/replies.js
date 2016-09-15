/**
 * @module discussion/replies
 */
define(function (require) {

	var $ = require('jquery');

	var interactions = require('page/components/interactions');

	var replies = {
		ready: false,
		init: function () {
			if (replies.ready) {
				return;
			}

			$(document).on('click', '.elgg-menu-interactions .elgg-menu-item-replies > a', interactions.triggerTabSwitch);

			$(document).off('click', '.elgg-item-object-discussion_reply .elgg-menu-item-edit > a'); // disable core js events
			$(document).on('click', '.elgg-item-object-discussion_reply .elgg-menu-item-edit > a', interactions.loadEditForm);

			$(document).on('change', '.interactions-replies-list', interactions.listChanged);

			replies.ready = true;
		}
	};

	replies.init();

	return interactions;
});