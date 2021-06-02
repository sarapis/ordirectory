<?php
namespace App\Custom;

class RequestMapper
{
	public static $labels = [
		'Apartment_Number' => 'Apartment',
		'House_Number' => 'House',
		'' => '',
	];
	
	static function getEnc($req, $addField=[], $navClean=false)
	{
		$req = array_merge($req, $addField);
		$req = array_diff($req, ['', null]);
		if ($navClean)
			$req = array_diff_key($req, array_fill_keys(['page', 'per_page'], 0));
		return http_build_query($req);
	}
	
	static function titleEnc($req)
	{
		$f = $req['searchBy'];
		$label = ($req['family'] ?? null) && (($req['searchBy'] ?? null) == 'TaxonomyName')
			? 'taxonomy group'
			: strtolower(preg_replace('~name~si', '', $f));
		$txt = urldecode($req[$f]);
		return "<i><u>{$label}: {$txt}</u></i>";
	}
	
	static function encodeFilename($req)
	{
		$req = array_diff($req, ['', null]);
		$rr = self::encodeApiReq($req);
		$aa = [];
		foreach ((array)$rr['request'] as $f=>$v)
			if (is_array($v))
				$aa[] = "{$f}=" . implode(',', $v);
			else
				$aa[] = "{$f}={$v}";
		return implode(';', $aa);
	}
	
	static function getLabel($id)
	{
		if (self::$labels[$id])
			return self::$labels[$id];
		if ($id == 'Future_Party_Effective_Date')
			return 'Future Party Eff Date';
		return preg_replace('~_~', ' ', $id);
	}

}