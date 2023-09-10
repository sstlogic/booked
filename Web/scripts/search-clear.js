/**
 Copyright 2017-2023 Twinkle Toes Software, LLC
 */

$('.searchclear').each((index, element) => {
	var ref = $(element).attr('data-ref');
	var refs = ref.split(',');
	refs.forEach((ref) => {
		$('#' + ref).on('keyup', e => {
			if (e.target.value !== "") {
				$(element).show();
			}
			else {
				$(element).hide();
			}
		});
	});
});

$('.searchclear').click(function(e) {
	e.preventDefault();
	e.stopPropagation();

	var ref = $(e.target).attr('data-ref');
	var refs = ref.split(',');
	refs.forEach((ref) => {
		$('#' + ref).val('');
		$(e.target).hide();
	});
});