<?php
namespace App\Custom;

class Model
{
	protected $verbose = false;
	public $db;
	
	// ===== singleton =============================================
	
	protected static $instance;

	public static function engine()
	{
        if (!isset(self::$instance)) 
		{
            $c = get_called_class();
            self::$instance = new $c;
        }
        return self::$instance;
	}
		
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

	// ===== data request =============================================

	static function getServices(&$params)
	{
		foreach (['ServiceName','OrganizationName','NameSearch','TaxonomyName'] as $f)
			if ($params[$f] ?? null)
				$params['searchBy'] = $f;
		if (!preg_match('~^(ServiceName|OrganizationName|NameSearch|TaxonomyName)$~si', $params['searchBy'] ?? ''))
			return [];
		$params = array_merge([
				'page' => 1,
				'per_page' => 20,
			],
			$params,
			[
				'sort_by' => 'name',
				'order' => 'asc',
			]
		);
		$strict = $params['strict'] ?? null ? 'strictSearchMode:true' : 'strictSearchMode:false';
		$taxonomyFamily = $params['family'] ?? null ? 'taxonomyFamily:true' : 'taxonomyFamily:false';

		$field = $params['searchBy'];
		$value = urlencode($params[$field]);

		$pp = [];
		foreach (['page', 'per_page', 'sort_by', 'order'] as $param)
			$pp[] = "{$param}={$params[$param]}";
		$ppp = implode('&', $pp);

		$reqs = [
			'ServiceName' => "?queries=name:{$value}|{$strict}&{$ppp}",
			'OrganizationName' => "?queries=organization:{$value}|{$strict}&{$ppp}",
			'NameSearch' => "?queries=namesearch:{$value}|{$strict}&{$ppp}",
			'TaxonomyName' => sprintf('/%s?queries=%s|%s&%s', preg_replace('~%2f~si', '/', $value), $strict, $taxonomyFamily, $ppp)
		];
		$req = $reqs[$field];
		#echo config('conf.APIENTRY') . '/services/completeext' . $req;
		$data = self::req($q = config('conf.APIENTRY') . '/services/completeext' . $req);
		foreach ($data['items'] ?? [] as $k=>$item)
		{
			#foreach ($item['location'] ?? [] as $i=>$loc)
			#	$data['items'][$k]['location'][$i] = array_merge($loc, self::getServiceLocationDetails($loc['id'], $item['id']));
				
			$data['items'][$k]['categories'] = $data['items'][$k]['eligibility'] = []; 
			foreach ($item['taxonomy'] ?? [] as $taxonomy)
			{
				if ($taxonomy['taxonomy_facet'] == 'Service Eligibility')
					$data['items'][$k]['eligibility'][] = $taxonomy;
				elseif ($taxonomy['taxonomy_facet'] == 'Service Category')
					$data['items'][$k]['categories'][] = $taxonomy;
			}
		}
		#echo '<pre>';
		#print_r($data);
		return $data;
	}

	static function getService($id)
	{
		$item = self::req(config('conf.APIENTRY') . '/services/complete/' . $id);
		foreach ($item['location'] ?? [] as $i=>$loc)
			$item['location'][$i] = array_merge($loc, self::getServiceLocationDetails($loc['id'], $item['id']));
		$item['categories'] = $item['eligibility'] = []; 
		foreach ($item['taxonomy'] ?? [] as $taxonomy)
		{
			if ($taxonomy['taxonomy_facet'] == 'Service Eligibility')
				$item['eligibility'][] = $taxonomy;
			elseif ($taxonomy['taxonomy_facet'] == 'Service Category')
				$item['categories'][] = $taxonomy;
		}
		#echo '<pre>';
		#print_r($item);
		return $item;
	}

	static function getServiceLocationDetails($lid, $sid)
	{
		static $lcache = [];
		$lcache[$lid] = $lcache[$lid] ?? (self::req(config('conf.APIENTRY') . "/locations/complete/{$lid}") ?? []);
		$rr = array_intersect_key($lcache[$lid], ['regular_schedule' => [], 'physical_address' => [], 'phones' => []]);
		
		$has_custom_schedule = false;
		foreach ($rr['regular_schedule'] ?? [] as $i=>$s)
			if ($s['service_id'] ?? null)
				unset($rr['regular_schedule'][$i]);
		return $rr;
	}


	static function req($url)
	{
		#print_r($url);
		$res = Curl::exec($url);
		#file_put_contents(__DIR__ . '/../dcmodel.req', print_r([$url, $res], 1));
		#print_r($res);
		$jj = json_decode($res, true);
		return $jj;
	}
	
	
	static function url($uri)
	{
		return config('conf.APIENTRY') . $uri;
	}


	static function getTaxonomies()
	{
		return self::req(config('conf.APIENTRY') . '/taxonomy');
	}

// ======= autocomplete ===================================================
	static function autocomplete($pn, $entry)
	{
		return self::req(config('conf.APIENTRY') . "/{$entry}?page={$pn}&per_page=50&sort_by=name&order=asc");
	}

}