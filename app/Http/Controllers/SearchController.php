<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Custom\Model;
use App\Custom\DataMapper;
use App\Custom\Csv;
use App\Custom\PDF;
use App\Custom\RequestMapper;
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
				'csvLink' => route('servicescsv') . '?' . http_build_query($_GET),
				'pdfLink' => route('servicespdf') . '?' . http_build_query($_GET),
			]);
    }

    public function servicescsv()
    {
		$m = Model::engine();
		$data = $m->getServices($_GET);
		$fn = date('Ymd-His') . '_' . preg_replace('~<[^>]+>~', '', RequestMapper::titleEnc($_GET, true)) . '.csv';
		$dd = DataMapper::tilesExportData($data['items'] ?? []);
		$this->exportcsv($dd, $fn);
    }

    public function servicespdf()
    {
		$m = Model::engine();
		$data = $m->getServices($_GET);
		$title = preg_replace('~<[^>]+>~', '', RequestMapper::titleEnc($_GET, true));
		$hh = ['Service Name' => 30, 'Category' => 30, 'Eligibility' => 30, 'Organization' => 30, 'Phone' => 30, 'Address' => 30, 'Service Description' => 100];
		#$fn = date('Ymd-His') . "_{$title}.pdf";
		$this->exportpdf($title, $hh, DataMapper::tilesExportData($data['items'] ?? [], false));
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
					'csvLink' => route('servicecsv', $id),
					'pdfLink' => route('servicepdf', $id),
				]);
    }

    public function servicecsv($id)
    {
		$m = Model::engine();
		$item = $m->getService($id);
		$fn = date('Ymd-His') . '_' . $item['name'] . '.csv';
		$dd = DataMapper::tilesExportData([$item]);
		$this->exportcsv($dd, $fn);
    }

    public function servicepdf($id)
    {
		$m = Model::engine();
		$item = $m->getService($id);
		$title = $item['name'];
		$hh = ['Service Name' => 30, 'Category' => 30, 'Eligibility' => 30, 'Organization' => 30, 'Phone' => 30, 'Address' => 30, 'Service Description' => 100];
		#$fn = date('Ymd-His') . "_{$title}.pdf";
		$this->exportpdf($title, $hh, DataMapper::tilesExportData([$item], false));
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
					'csvLink' => route('organizationcsv') . '?' . http_build_query($_GET),
					'pdfLink' => route('organizationpdf') . '?' . http_build_query($_GET),
				]);
    }

    public function organizationcsv()
    {
		$m = Model::engine();
		$data = $m->getServices($_GET);
		$dd = DataMapper::orgExpDetails((array)$data['items'] ?? []);
		#print_r($dd);
		#die();
		$fn = date('Ymd-His') . "_{$dd['Name']}.csv";
		$this->exportcsv([$dd], $fn);
    }

    public function organizationpdf()
    {
		$m = Model::engine();
		$data = $m->getServices($_GET);
		$dd = DataMapper::orgExpDetails((array)$data['items'] ?? []);
		$title = $dd['Name'];
		$hh = ['Name' => 60, 'Description' => 120, 'Url' => 50, 'Email' => 50];
		#$fn = date('Ymd-His') . "_{$title}.pdf";
		$this->exportpdf($title, $hh, [$dd]);
    }


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function exportcsv($dd, $fn)
	{
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="' . $fn . '";');
		echo Csv::encodeCSV($dd, ",", '"');
	}

	public function exportpdf($title, $hh, $dd)
	{
		#header('Content-Type: application/pdf');
		#header('Content-Disposition: attachment; filename="' . $fn . '";');

		$pdf = new PDF($title, $hh, Yaml::parse(file_get_contents(base_path() . '/design.yml'))['pdf']['logo_url']);
		$pdf->AddPage('L');
		$pdf->AliasNbPages();
		$pdf->SetFont('Arial','B',10);
		
		foreach($hh as $h=>$w)
			$pdf->Cell($w,12,$h,1);
		$pdf->Ln();
		
		$pdf->SetFont('Arial','',8);

		foreach($dd as $row)
			$pdf->Row($row);
			
		$pdf->Output();
	}
}
