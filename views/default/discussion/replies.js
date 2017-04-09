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

			$(document).off('click', '.interactions-edit-discussion-reply > a'); // disable core js events
			$(document).on('click', '.interactions-edit-discussion-reply > a', interactions.loadEditForm);

			$(document).on('change', '.interactions-replies-list', interactions.listChanged);

			replies.ready = true;
		}
	};

	replies.init();

	return interactions;
});