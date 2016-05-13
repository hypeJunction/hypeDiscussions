/**
 * @module discussion/replies
 */
define(function (require) {

	var elgg = require('elgg');
	var $ = require('jquery');

	var interactions = require('page/components/interactions');

	var replies = {
		ready: false,
		init: function () {
			if (replies.ready) {
				return;
			}

			$(document).on('click', '.elgg-menu-interactions .elgg-menu-item-replies > a', replies.triggerTabSwitch);

			$(document).off('click', '.elgg-item-object-discussion_reply .elgg-menu-item-edit > a'); // disable core js events
			$(document).on('click', '.elgg-item-object-discussion_reply .elgg-menu-item-edit > a', interactions.loadEditForm);

			$(document).on('change', '.interactions-replies-list', interactions.listChanged);

			replies.ready = true;
		},
		triggerTabSwitch: function (e) {
			e.preventDefault();

			var $elem = $(this);

			if ($elem.is('.elgg-menu-item-replies > a')) {
				$elem = $elem.closest('.interactions-controls').find('.elgg-menu-interactions-tabs').find('a[data-trait="replies"]');
			}

			var trait = $elem.data('trait') || 'replies';

			$elem.parent().addClass('elgg-state-selected').siblings().removeClass('elgg-state-selected');

			var $controls = $(this).closest('.interactions-controls');
			$controls.parent().addClass('interactions-has-active-tab');

			var $components = $controls.nextAll('.interactions-component');
			$components.removeClass('elgg-state-selected');

			var $traitComponent = $components.filter(interactions.buildSelector('.interactions-component', {
				'data-trait': trait
			}));

			if ($traitComponent.length) {
				$traitComponent.addClass('elgg-state-selected');
				if ($(e.target).parents().andSelf().is('.elgg-menu-item-replies > a')) {
					$traitComponent.children('.interactions-form').show().find('[name="description"]').focus().trigger('click');
				}
			} else {
				$traitComponent = $('<div></div>').addClass('interactions-component elgg-state-selected elgg-ajax-loader').data('trait', trait).attr('data-trait', trait);
				$controls.after($traitComponent);
				elgg.ajax($elem.attr('href'), {
					success: function (data) {
						$traitComponent.removeClass('elgg-ajax-loader').html(data);
						$traitComponent.find('.elgg-list').trigger('refresh');
						if ($(e.target).parents().andSelf().is('.elgg-menu-item-replies > a')) {
							$traitComponent.children('.interactions-form').show().find('[name="description"]').focus().trigger('click');
						}
					}
				});
			}
		}
	};

	replies.init();

	return interactions;
});