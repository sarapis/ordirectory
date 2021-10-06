<pre><?php
require_once __DIR__ . '/include/loader.php';

$localDataPath = __DIR__ . '/resources';
$m = Model::engine();

$data = $m->getTaxonomies();
file_put_contents($localDataPath . '/taxonomyAutocomplete.json', json_encode(taxonomyAutocomplete($data['items'])));
$taxCat = taxonomyCatalog($data['items']);
file_put_contents($localDataPath . '/taxonomyCatalog.json', json_encode($taxCat));
echo 'taxonomies processed: ' . count($data['items']) . "\n"; flush();

$services = [];
$pn = 1;
do {
	$ss = $m->autocomplete($pn, 'services');
	if ($ss['items'])
	{	
		foreach ($ss['items'] as $item)
			$services[] = trim($item['name']);
		$pn++;
	}
	else
		continue;
} while($pn <= $ss['total_pages']);
sort($services);
file_put_contents($localDataPath . '/servicesAutocomplete.json', json_encode($services));
echo 'services processed: ' . count($services) . "\n"; flush();


$orgs = [];
$pn = 1;
do {
	$oo = $m->autocomplete($pn, 'organizations');
	if ($oo['items'])
	{	
		foreach ($oo['items'] as $item)
			$orgs[] = trim($item['name']);
		$pn++;
	}
	else
		continue;
} while($pn <= $oo['total_pages']);
sort($orgs);
file_put_contents($localDataPath . '/organizationsAutocomplete.json', json_encode($orgs));
echo 'orgs processed: ' . count($orgs) . "\n"; flush();


function taxonomyAutocomplete($tt)
{
	$rr = [];
	foreach ($tt as $t)
		$rr[] = trim($t['name']);
	sort($rr);
	return $rr;
}

function taxonomyCatalog($tt)
{
	foreach ($tt as $i=>$t)
		$tt[$i]['code'] = $i;
	$cat = taxonomyNode($tt);
	$perColumn = ceil(count($cat) / 2);
	$menu = [];
	foreach (array_chunk($cat, $perColumn, true) as $i => $col)
		$menu[] = [
				'col' => $i,
				'items' => $col
			];
	return $menu;
}

function taxonomyNode($tt, $level=1, $parentId='')
{
	$node = [];
	foreach ($tt as $i=>$t)
	{
		if ($t['parent_id'] == $parentId)
		{
			unset($tt[$i]);
			$node[$t['name']] = [
						'lev' => $level,
						'name' => $t['name'],
						'code' => $t['code'],
						'url' => "services.php?searchBy=TaxonomyName&strict=true&family=true&TaxonomyName=" . urlencode($t['name']),
						'items' => taxonomyNode($tt, $level+1, $t['id']),
					];
		}
	}
	ksort($node);
	return $node;
}