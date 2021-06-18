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
		foreach (['ServiceName','OrganizationName','TaxonomyName'] as $f)
			if ($params[$f] ?? null)
				$params['searchBy'] = $f;
		if (!preg_match('~^(ServiceName|OrganizationName|TaxonomyName)$~si', $params['searchBy']))
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
			'TaxonomyName' => sprintf('/%s?queries=%s|%s&%s', preg_replace('~%2f~si', '/', $value), $strict, $taxonomyFamily, $ppp)
		];
		$req = $reqs[$field];
		#echo $req;
		$data = self::req($q = config('conf.APIENTRY') . '/services/complete' . $req);
		foreach ($data['items'] ?? [] as $k=>$item)
			if (isset($item['location'][0]))
				$data['items'][$k]['address'] = (self::req(config('conf.APIENTRY') . "/locations/{$item['location'][0]['id']}/physical-address"))['items'] ?? null;
		//echo '<pre>';
		#print_r($data);
		return $data;
	}

	static function getService($id)
	{
		$item = self::req(config('conf.APIENTRY') . '/services/complete/' . $id);
		if (isset($item['location'][0]))
			$item['address'] = (self::req(config('conf.APIENTRY') . "/locations/{$item['location'][0]['id']}/physical-address"))['items'] ?? null;
		return $item;
	}

	static function req($url)
	{
		#print_r($url);
		$res = Curl::exec($url);
		//file_put_contents(__DIR__ . '/../dcmodel.req', print_r([$url, $res], 1));
		#print_r($res);
		$jj = json_decode($res, true);
		return $jj;
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