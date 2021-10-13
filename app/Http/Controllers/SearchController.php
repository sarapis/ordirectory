<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Custom\Model;
use App\Custom\DataMapper;
use Yaml;


class SearchController extends Controller
{

    public function index()
    {
        return view('index', [
					'data' => json_decode(file_get_contents(public_path() . '/resources/taxonomyCatalog.json'), true),
					'design' => Yaml::parse(file_get_contents(base_path() . '/design.yml')),
					'req' => $_GET,
					'title' => 'DC New Social Services Site',
				]);
    }

    public function services()
    {
		$m = Model::engine();
		$data = $m->getServices($_GET);
		if (($data['message'] ?? null) == 'Rate limit exceeded')
			return view('alert', [
					'alert' => 'API rate limit exceeded',
					'design' => Yaml::parse(file_get_contents(base_path() . '/design.yml')),
					'req' => $_GET,
				]);
				
		list($markers, $mapCenter) = DataMapper::markers($data['items'] ?? []);
		file_put_contents(public_path() . '/resources/markers.txt', $markers);
		return view('services', [
				'data' => $data,
				'design' => Yaml::parse(file_get_contents(base_path() . '/design.yml')),
				'req' => $_GET,
				'mapcenter' => $mapCenter,
				'tiles' => DataMapper::tilesData($data['items'] ?? []),
				'title' => 'DC Social Services Search',
			]);
    }

    public function service($id)
    {
		$m = Model::engine();
		$data = $m->getService($id);
		$data = DataMapper::serviceCard($data);
        return view('service', [
					'id' => $id,
					'data' => $data,
					'design' => Yaml::parse(file_get_contents(base_path() . '/design.yml')),
					'title' => $data['Service Name'],
				]);
    }

    public function organization()
    {
		if (!isset($_GET['searchBy']))
			return back();
		$m = Model::engine();
		$data = $m->getServices($_GET);
		if (($data['message'] ?? null) == 'Rate limit exceeded')
			return view('alert', [
					'alert' => 'API rate limit exceeded',
					'design' => Yaml::parse(file_get_contents(base_path() . '/design.yml')),
					'req' => $_GET,
				]);

		list($markers, $mapCenter) = DataMapper::markers($data['items'] ?? []);
		file_put_contents(public_path() . '/resources/markers_org.txt', $markers);

        return view('organization', [
					'data' => $data,
					'design' => Yaml::parse(file_get_contents(base_path() . '/design.yml')),
					'req' => $_GET,
					'mapcenter' => $mapCenter,
					'org' => DataMapper::orgDetails((array)$data['items'] ?? []),
					'tiles' => DataMapper::tilesData((array)$data['items'] ?? []),
					'title' => $data['items'][0]['organization']['name'] ?? '',
				]);
    }

}
