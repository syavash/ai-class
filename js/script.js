$(function() {
	$('span').each(function() {
		if ($(this).attr('title')) $(this).tipsy({gravity: 's'});
	});
});