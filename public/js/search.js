	
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
	
	/* https://stackoverflow.com/a/25040020 */
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

/*
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
*/

	$('.form-control').focus(function()
	{
		$('.form-control').val('');
	});