function val_reset() {
	$('#namesearch').val('');
	$('.reset-button button').addClass('inactive');
}

function check_empty_input() {
	return $('#namesearch').val() ? true : false;
}

var namesearch = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.whitespace,
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  prefetch: './resources/namesearchAutocomplete.json'
});
$('[name="NameSearch"]').typeahead(null, {
  name: 'namesearch',
  limit: 1000,
  source: namesearch
});

namesearch.clearPrefetchCache();
namesearch.initialize(true);


$('#namesearch').on('input propertychange change', function () {
	if ($('#namesearch').val())
		$('.reset-button button').removeClass('inactive');
	else
		$('.reset-button button').addClass('inactive');
});
	
