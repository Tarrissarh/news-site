'use strict';

$(document).ready(function () {
	$('.preloader').css('display', 'none');

	$(document).on('submit', '.search-form', function (e) {
		e.preventDefault();

		let form = $(this);

		$('.preloader').css('display', 'flex');

		$.ajax({
			url: form.attr('action'),
			method: 'get',
			data: form.serialize(),
			success: function (response) {
				$('#posts').html(response);
				$('.preloader').css('display', 'none');
			}
		})
	});
});