	
/* https://stackoverflow.com/a/25040020 */
/*
	var services = new Bloodhound({
	  datumTokenizer: Bloodhound.tokenizers.whitespace,
	  queryTokenizer: Bloodhound.tokenizers.whitespace,
	  prefetch: './resources/servicesAutocomplete.json'
	});
	$('[name="ServiceName"]').typeahead(null, {
	  name: 'services',
	  limit: 8,
	  source: services
	});
	
	services.clearPrefetchCache();
	services.initialize(true);
	
	var organizations = new Bloodhound({
	  datumTokenizer: Bloodhound.tokenizers.whitespace,
	  queryTokenizer: Bloodhound.tokenizers.whitespace,
	  prefetch: './resources/organizationsAutocomplete.json'
	});
	$('[name="OrganizationName"]').typeahead(null, {
	  name: 'organizations',
	  limit: 8,
	  source: organizations
	});
	organizations.clearPrefetchCache();
	organizations.initialize(true);

	var taxonomy = new Bloodhound({
	  datumTokenizer: Bloodhound.tokenizers.whitespace,
	  queryTokenizer: Bloodhound.tokenizers.whitespace,
	  prefetch: './resources/taxonomyAutocomplete.json'
	});
	$('#TaxonomyName .form-control').typeahead(null, {
	  name: 'taxonomy',
	  limit: 8,
	  source: taxonomy
	});

	$('.form-control').focus(function()
	{
		$('.form-control').val('');
	});
*/
	function val_reset() {
		$('#namesearch').val('');
		$('.reset-button button').addClass('inactive');
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
	
