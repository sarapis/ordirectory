<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Custom\Model;

class CronJobCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:localtaxonomies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates and saves locally Taxonomy cataglogue, Organizations autocomplete, Services autocomplete .json files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$rootDir = dirname(dirname(dirname(__DIR__)));
		$localDataPath = $rootDir . '/public/resources';
		$m = Model::engine();

		$data = $m->getTaxonomies();
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
		#file_put_contents($localDataPath . '/servicesAutocomplete.json', json_encode($services));
		#echo 'services processed: ' . count($services) . "\n"; flush();


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
		#file_put_contents($localDataPath . '/organizationsAutocomplete.json', json_encode($orgs));
		#echo 'orgs processed: ' . count($orgs) . "\n"; flush();

		$namesearch = array_merge($services, $orgs);
		sort($namesearch);
		file_put_contents($localDataPath . '/namesearchAutocomplete.json', json_encode($namesearch));
		echo 'namesearch processed: ' . count($namesearch) . "\n"; flush();

        return 0;
    }
	
	public static function taxonomyAutocomplete($tt)
		{
			$rr = [];
			foreach ($tt as $t)
				$rr[] = trim($t['name']);
			sort($rr);
			return $rr;
		}

	public static function taxonomyCatalog($tt)
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

	public static function taxonomyNode($tt, $level=1, $parentId='')
	{
		$node = [];
		foreach ($tt as $i=>$t)
			foreach (explode(',', $t['parent_id']) as $pid)
			{
				if ($pid == $parentId)
				{
					#unset($tt[$i]);
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
		return $node;
	}

}
