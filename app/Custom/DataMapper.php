<?php
namespace App\Custom;

class DataMapper
{

// ---- org ------------------------

	static function orgHeaders()
	{
		return ['Service',/* 'Organization Name', */'Taxonomy Terms', 'Location Name', 'Phone', 'Address'];
	}
	
	static function orgDetails($data)
	{
		$rr = [];
		$idx = [
			'name' => ['key' => 'organization', 'subkey' => 'name'],
			'descr' => ['key' => 'organization', 'subkey' => 'description'],
			'url' => ['key' => 'organization', 'subkey' => 'url'],
			'email' => ['key' => 'organization', 'subkey' => 'email'],
		];
		foreach ($data as $rec)
		{
			$r = [];
			foreach ($idx as $f=>$keys)
			{
				$rec[$keys['key']] = $rec[$keys['key']] ?? null;
				if (is_string($rec[$keys['key']]))
					$r[$f] = $rec[$keys['key']];
				elseif (is_array($rec[$keys['key']]))
					foreach ($rec[$keys['key']] as $val)
						$r[$f][] = $val[$keys['subkey']];
				else 
					$r[$f] = '';
				if (is_array($r[$f]) && count($r[$f]) == 1)
					$r[$f] = $r[$f][0];
			}
			$r['descr'] = preg_replace('~\s*(\\\n)+\s*~si', ' ', $r['descr']);
			if ($r['name'])
				return $r;
		}
		return [];
	}	

	static function orgGridData($data)
	{
		print_r($data);
		$rr = [];
		$idx = [
			'id' => ['key' => 'id', 'subkey' => ''],
			'Service' => ['key' => 'name', 'subkey' => ''],
			//'Organization Name' => ['key' => 'organization', 'subkey' => 'name'],
			'Taxonomy Terms' => ['key' => 'taxonomy', 'subkey' => 'name'],
			'Location Name' => ['key' => 'location', 'subkey' => 'name'],
			'Phone' => ['key' => 'phones', 'subkey' => 'number'],
			'Address' => ['key' => 'address', 'subkey' => 'address_1'],
			'State' => ['key' => 'address', 'subkey' => 'state_province'],
			'City' => ['key' => 'address', 'subkey' => 'city'],
			'Zip' => ['key' => 'address', 'subkey' => 'postal_code']
		];
		foreach ($data as $rec)
		{
			$r = [];
			foreach ($idx as $f=>$keys)
			{
				$rec[$keys['key']] = $rec[$keys['key']] ?? null;
				if (is_string($rec[$keys['key']]))
					$r[$f] = $rec[$keys['key']];
				elseif (is_array($rec[$keys['key']]))
					foreach ($rec[$keys['key']] as $val)
						$r[$f][] = $val[$keys['subkey']];
				else 
					$r[$f] = '';
				if ($f <> 'Taxonomy Terms' && is_array($r[$f]) && count($r[$f]) == 1)
					$r[$f] = $r[$f][0];
			}
			$rr[] = $r;
		}
		return $rr;
	}	

// ---- tiles ------------------------

	static function tilesData($data)
	{
		$rr = [];
		$idx = [
			'id' => ['key' => 'id', 'subkey' => ''],
			'name' => ['key' => 'name', 'subkey' => ''],
			#'lat' => ['key' => 'location', 'subkey' => 'latitude'],
			#'lon' => ['key' => 'location', 'subkey' => 'longitude'],
			'organization' => ['key' => 'organization', 'subkey' => 'name'],
			'descr' => ['key' => 'description', 'subkey' => ''],
			'phone' => ['key' => 'phone', 'subkey' => 0],
			#'taxonomies' => ['key' => 'taxonomy', 'subkey' => 'name'],
			'categories' => ['key' => 'categories', 'subkey' => 'name'],
			'eligibility' => ['key' => 'eligibility', 'subkey' => 'name'],
		];
		foreach ($data as $rec)
		{
			$r = ['locations' => []];
			foreach ($idx as $f=>$keys)
			{
				$rec[$keys['key']] = $rec[$keys['key']] ?? null;
				if (is_string($rec[$keys['key']]))
					$r[$f] = $rec[$keys['key']];
				elseif (is_array($rec[$keys['key']]))
					foreach ($rec[$keys['key']] as $val)
						$r[$f][] = $val[$keys['subkey']];
				else 
					$r[$f] = '';
				if ($f <> 'Taxonomy Terms' && is_array($r[$f] ?? null) && count($r[$f]) == 1)
					$r[$f] = $r[$f][0];
			}
			$r['descr'] = preg_replace('~\s*(\\\n)+\s*~si', ' ', $r['descr']);
			if (strlen($r['descr']) > 490)
				$r['descr'] = preg_replace('~\W+\w*$~si', '...', substr($r['descr'], 0, 490));
			foreach ($rec['location'] ?? [] as $loc)
			{
				$r['locations'][] = array_merge($loc, [
					'display_pin' => $loc['latitude'] && $loc['longitude'],
					'physical_address' => self::addr($loc['physical_address'] ?? []),
					'phones' => self::phones($loc['phones'] ?? []),
				]);
			}
			$rr[] = $r;
		}
		#echo '<pre>';
		#print_r($rr);
		#echo '</pre>';
		return $rr;
	}	


	static function addr($physical_address)
	{
		$matr = array_fill_keys(['address_1', 'address_2', 'city', 'state_province', 'postal_code', 'country'], '');
		$addr = array_merge($matr, array_intersect_key($physical_address[0] ?? [], $matr));
		$addr['state_province'] = trim(implode(' ', [$addr['state_province'], $addr['postal_code']]));
		unset($addr['postal_code']);
		return implode(', ', array_diff($addr, ['']));
	}

	static function phones($raw_phones)
	{
		$pp = [];
		foreach ($raw_phones as $p)
			$pp[] = $p['number'];
		return implode(', ', array_diff($pp, ['']));
	}

	static function serviceCard($rec)
	{
		$idx = [
			'id' => ['key' => 'id', 'subkey' => ''],
			'Service Name' => ['key' => 'name', 'subkey' => ''],
			'Organization Name' => ['key' => 'organization', 'subkey' => 'name'],
			'Description' => ['key' => 'description', 'subkey' => ''],
			'Phones' => ['key' => 'phones', 'subkey' => 'number'],
			'Website' => ['key' => 'url', 'subkey' => ''],
			'Languages' => ['key' => 'languages', 'subkey' => 'language'],
			'SchedulesDays' => ['key' => 'regular_schedule', 'subkey' => 'weekday'],
			'SchedulesOpen' => ['key' => 'regular_schedule', 'subkey' => 'opens_at'],
			'SchedulesClose' => ['key' => 'regular_schedule', 'subkey' => 'closes_at'],
			'Category' => ['key' => 'categories', 'subkey' => 'name'],
			'Eligibility' => ['key' => 'eligibility', 'subkey' => 'name'],
			'DetailsType' => ['key' => 'details', 'subkey' => 'type'],
			'DetailsValue' => ['key' => 'details', 'subkey' => 'value'],
			
		];
		
		$r = ['locations' => []];
		foreach ($idx as $f=>$keys)
		{
			$rec[$keys['key']] = $rec[$keys['key']] ?? null;
			if (is_string($rec[$keys['key']]))
				$r[$f] = $rec[$keys['key']];
			elseif (is_array($rec[$keys['key']]))
				foreach ($rec[$keys['key']] as $val)
					$r[$f][] = $val[$keys['subkey']];
			else 
				$r[$f] = '';
			
			if (is_array($r[$f] ?? null) && count($r[$f]) == 1)
				$r[$f] = $r[$f][0];
		}
		$r['Website'] = preg_match('~^https?://~si', $r['Website']) ? $r['Website'] : 'http://' . $r['Website'];
		
		#$aa = [$r['Address'], $r['Address2'], $r['Address3'], $r['Address4']];
		#$aa = array_diff($aa, ['', null]);
		#$r['Address'] = preg_replace('~[\s ]+,~si', ',', implode(', ', $aa));
		$r['Description'] = preg_replace('~\\\n|\n~si', "<br/>", $r['Description']);
		#$r['Location Description'] = preg_replace('~\\n~si', "<br/>", $r['Location Description']);
		
		$ss = [];
		if ($r['SchedulesDays'])
		{	
			$schedule = [];
			foreach ((array)$r['SchedulesDays'] as $i=>$day)
				$schedule[] = ['day' => $day, 'open' => $r['SchedulesOpen'][$i], 'close' => $r['SchedulesClose'][$i]];
			usort($schedule, 'App\Custom\schedulesort');
			foreach ((array)$schedule as $schRec)
				$ss[] = "<span class='weekday'>{$schRec['day']}</span> {$schRec['open']}-{$schRec['close']}";
		}
		$r['Schedules']	= implode('<br/>', $ss);
		$details = [];
		if ($r['DetailsType'])
		{	
			foreach (['Program', 'Required Document', 'Eligibility', 'Insurance', 'Cultural Competencies', 'Ages Served', 'Transportation'] as $trgType)
				foreach ((array)$r['DetailsType'] as $i=>$type)
					if ($type == $trgType)
						$details[$type][] = ((array)$r['DetailsValue'])[$i];
			foreach ((array)$details as $type=>$detArr)
				$details[$type] = implode(', ', $detArr);
		}
		$r['Details'] = $details;
		
		$r['display_map'] = false;
		foreach ($rec['location'] ?? [] as $loc)
		{
			$r['locations'][] = array_merge($loc, [
				'display_pin' => $loc['latitude'] && $loc['longitude'],
				'physical_address' => self::addr($loc['physical_address'] ?? []),
				'phones' => self::phones($loc['phones'] ?? []),
				'description' => preg_replace('~\\n~si', "<br/>", $loc['description']),
			]);
			if ($loc['latitude'] && $loc['longitude'])
				$r['display_map'] = true;
		}
		
		$r = array_diff_key($r, array_fill_keys(['Address2', 'Address3', 'Address4', 'SchedulesDays', 'SchedulesOpen', 'SchedulesClose', 'DetailsType', 'DetailsValue'], 1));
		
		#echo '<pre>';
		#print_r($r);
		#echo '</pre>';
		return $r;
	}

	static function markers($data)
	{
		$mm = [];
		foreach ((array)$data as $rec)
		{
			foreach ($rec['location'] ?? [] as $i=>$loc)
				if ((real)trim($loc['latitude'] ?? null) && (real)trim($loc['longitude'] ?? null))
					$mm[] = [
						'id' => $rec['id'],
						'lat' => trim($loc['latitude']),
						'lon' => trim($loc['longitude']),
						'service' => $rec['name'],
						'organization' => $rec['organization']['name'] ?? '',
						'phone' => self::phones($loc['phones'] ?? []),
						'address' => self::addr($loc['physical_address'] ?? []),
					];
		}
		$rr = ['point	title	description	iconSize	iconOffset	icon'];
		$lats = $lons = [];
		foreach ($mm as $rec)
		{
			$rr[] = implode("\t", [
				'point' => "{$rec['lat']},{$rec['lon']}",
				'title' => "<a href=\"service/{$rec['id']}\">{$rec['service']}" .
						($rec['organization'] ? "<br/><small><i>by {$rec['organization']}</i></small>" : '') . '</a>',
				'description' => ($rec['phone'] ? "{$rec['phone']}<br/>" : '') .
							//"{$rec['address']}<br/>{$rec['city']}, {$rec['state']}, {$rec['zip']}",
							preg_replace('~, ~', '<br/>', $rec['address'], 1),
				'iconSize' => '25,25',
				'iconOffset' => '-12,-25',
				'icon' => 'img/markerR.png',
			]);
			$lats[] = (real)trim($rec['lat']);
			$lons[] = (real)trim($rec['lon']);
		}
		if ($lats)
		{
			$minLat = min($lats);
			$minLon = min($lons);
			$maxLat = max($lats);
			$maxLon = max($lons);
			$cLat = ($minLat + $maxLat) / 2;
			$cLon = ($minLon + $maxLon) / 2;
			$dLat = ($maxLat - $minLat) / 2;
			$dLon = ($maxLon - $minLon) / 2;
			$scale = self::mapScale($dLat, $dLon);
		}
		return [implode("\n", $rr) . "\n", compact(['cLat', 'cLon', 'scale'])];
	}	

	static function mapScale($dLat, $dLon)
	{
		if ($dLat === 0.0 && $dLon === 0.0)
			return 18;
		foreach (
			[
				4 => 11,
				5 => 6,
				6 => 3,
				7 => 1.5,
				8 => 0.75,
				9 => 0.5,
				10 => 0.22,
				11 => 0.14,
				12 => 0,
			] as $v=>$lim)
			if ($dLat > $lim)
			{
				$sLat = $v;
				break;
			}	
		foreach (
			[
				7 => 2.5,
				8 => 0.72,
				9 => 0.5,
				10 => 0.27,
				11 => 0.15,
				12 => 0.085,
				13 => 0.05,
				14 => 0,
			] as $v=>$lim)
			if ($dLon > $lim)
			{
				$sLon = $v;
				break;
			}
		return min($sLat, $sLon);
	}
}

function schedulesort($a, $b)
{
	$a = strtotime(preg_replace('~^\W+|\W+$~', '', $a['day']) . " {$a['open']}");
	$b = strtotime(preg_replace('~^\W+|\W+$~', '', $b['day']) . " {$b['open']}");
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;	
}