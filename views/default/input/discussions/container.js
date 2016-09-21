define(function(require) {
	var elgg = require('elgg');
	var $ = require('jquery');

	$(document).on('change init', '.select-discussion-container', function() {
		var $elem = $(this);
		var $form = $elem.closest('form');
		var container_guid = $(this).val();
		$form.find('[name="container_guid"]').last().val(container_guid);

		elgg.ajax('ajax/view/input/discussions/access', {
			data: {
				container_guid: container_guid,
			},
			success: function(output) {
				var $access = $form.find('[name="access_id"]');
				$(output).val($access.val());
				$form.find('[name="access_id"]').replaceWith($(output));
			}
		})
	});
	$('.select-discussion-container').trigger('init');

});