<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Custom\Model;
use Yaml;


class SrvController extends Controller
{
    public function autocompleteFiles()
    {

		$localDataPath = public_path() . '/resources';
		$m = Model::engine();

		$data = $m->getTaxonomies();
		#echo $data . "\n";
		file_put_contents($localDataPath . '/taxonomyAutocomplete.json', json_encode(self::taxonomyAutocomplete($data['items'])));
		$taxCat = self::taxonomyCatalog($data['items']);
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


    }


	private static function taxonomyAutocomplete($tt)
	{
		$rr = [];
		foreach ($tt as $t)
			$rr[] = trim($t['name']);
		sort($rr);
		return $rr;
	}

	private static function taxonomyCatalog($tt)
	{
		foreach ($tt as $i=>$t)
			$tt[$i]['code'] = $i;
		$cat = self::taxonomyNode($tt);
		$perColumn = ceil(count($cat) / 2);
		$menu = [];
		foreach (array_chunk($cat, $perColumn, true) as $i => $col)
			$menu[] = [
					'col' => $i,
					'items' => $col
				];
		return $menu;
	}

	private static function taxonomyNode($tt, $level=1, $parentId='')
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
							'items' => self::taxonomyNode($tt, $level+1, $t['id']),
						];
			}
		}
		ksort($node);
		//echo $node; exit;
		return $node;
	}		

    public function stats()
    {
		$m = Model::engine();
		$design = Yaml::parse(file_get_contents(base_path() . '/design.yml'));
		$data = $m->getStats();
		$tz = new \DateTimeZone("Etc/{$design['stats']['timezone']}");
		$dt = new \DateTime();
		$dt = $dt->setTimestamp($data['last_updated'])->setTimezone($tz);
		$data['last_updated_fmt'] = $dt->format('Y-m-d H:i:s') . " {$design['stats']['timezone']}";
        return view('stats', [
					'data' => $data,
					'design' => $design,
					'req' => $_GET,
				]);
    }
}
