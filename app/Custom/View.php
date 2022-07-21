<?php
namespace App\Custom;

class View
{
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

	// ===== pages =============================================

	public function searchPage($catData=[])
	{
		$this->drawHeaders();
		$this->drawNavbar();
		$this->drawSearchForm();
		$this->drawTaxonomyCatalog($catData);
		$this->drawDataActualDateNotification();
		$this->drawFooters(true);
	}


	public function servicesPage($req, $data, $mapCenter)
	{
		$this->drawHeaders();
		$this->drawNavbar($req, true);
		$this->drawServicesTiles($data, $req, $mapCenter);
		$this->drawFooters();
	}


	public function orgPage($req, $data, $mapCenter)
	{
		$this->drawHeaders();
		$this->drawNavbar($req, true);
		$this->drawOrgTiles($data, $req, $mapCenter);
		$this->drawFooters();
	}

	public function servicePage($id, $data)
	{
		$this->drawHeaders();
		$this->drawNavbarBack();
		$this->drawServiceCard($data);
		$this->drawDataActualDateNotification();
		$this->drawFooters();
	}

	public function alertPage($req, $msg)
	{
		$this->drawHeaders();
		$this->drawNavbar($req);
		$this->drawAlert($msg);
		$this->drawDataActualDateNotification();
		$this->drawFooters();
	}

	// ===== srv ================================================

	protected function __construct()
	{
	}


	protected function log($msg)
	{
		if (!$this->verbose)
			return;
		echo "{$msg}\n";
		flush();
	}



// ===== html ================================================

public function redirect($trg)
{
	header("Location: {$trg}");
}


public function back()
{

?>	<script>
		function goBack() {
		  window.history.back();
		}
		document.onload(goBack());
	</script>
<?php
die();
}


public function sendCSV($dd, $fn)
{
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename="' . $fn . '";');
	echo $dd;
}


public function drawSearchForm()
{
?>	<div class="container-fluid ">
	  <div class="row mt-3 mb-5">
		<div class="col-8 mx-auto">
		<h1>Social Service Directory</h1>
		  <form action="services.php" method="GET">
			  <div class="row mt-5">
				<div class="col-6">
				    <input type="text" class="form-control" aria-label="Text input with checkbox" placeholder="Service Name" name="ServiceName">
				</div>
				<div class="col-6">
					<input type="text" class="form-control" aria-label="Text input with radio button" placeholder="... or Organization Name" name="OrganizationName">
				</div>
				<div class="col-6">
					<div class="input-group my-4 ">
						<button style="min-width:8em;" type="submit" class="btn btn-primary">Submit</button>
						<button style="min-width:8em;"  type="reset" class="btn btn-light ml-2" onclick="document.location.assign('search.php');">Reset</button>
					</div>
				</div>
			  </div>
		  </form>
	    </div>
	  </div>
	</div>
<?php
}

// ----- taxonomy catalog ----------


public function drawTaxonomyCatalog($data)
{
?>
	<div class="container-fluid">
	  <div class="row justify-content-center">
		<div class="col-8">
		  <div class="row justify-content-center">
		    <div class="col mx-auto">
			  <h4 class="pt-5 mb-5" style="border-top: 1px solid #ccc; text-align:center;">Browse by Category</h4>
		    </div>
		  </div>
	    <div id="accordion">
		  <div class="row">
			  <?php foreach ($data as $col) : ?>
				<div class="col-6">
				  <?php foreach ($col['items'] as $name=>$card) : ?>
					<?php echo $this->drawTaxonomyAcCard($card, 32); ?>
				  <?php endforeach; ?>
				</div>
			  <?php endforeach; ?>
		  </div>
		</div>
	  </div>
	</div>
	</div>
<?php
}

public function drawTaxonomyAcCard($dd, $offs=0)
{
	$ll = [];
	foreach ($dd['items'] as $item)
		$ll[] = $item['items']
					? $this->drawTaxonomyInnerCard($item)
					: "<p>—&nbsp;<a href=\"{$item['url']}\" class=\"pl-1\">{$item['name']}</a></p>";
	$code = self::offs(implode("\r\n", $ll), 12);
	$html = <<<EOD
		<div class="card mb-2">
		<div class="card-header" id="card-{$dd['code']}">
		  <h5 class="mb-0">
			<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-{$dd['code']}" aria-expanded="false" aria-controls="collapse-{$dd['code']}">
			  +
			</button>
			<a href="{$dd['url']}" class="btn btn-link collapsed">{$dd['name']}</a>
		  </h5>
		</div>

		<div id="collapse-{$dd['code']}" class="collapse" aria-labelledby="card-{$dd['code']}" data-parent="#accordion">
		  <div class="card-body">

			{$code}

		  </div>
		</div>
		</div>
EOD;
	return self::offs($html, $offs - 8);
}

public function drawTaxonomyInnerCard($dd)
{
	$ll = [];
	foreach ($dd['items'] as $item)
		$ll[] = $item['items']
					? $this->drawTaxonomyInnerCard($item)
					: "<p>—&nbsp;<a href=\"{$item['url']}\" class=\"pl-1\">{$item['name']}</a></p>";
	$code = self::offs(implode("\r\n", $ll), 4);
	$html = <<<EOD
		<p>
		  <a data-toggle="collapse" href="#collapse-{$dd['code']}" role="button" aria-expanded="false" aria-controls="collapse-{$dd['code']}">
			<b class="fa"></b>&nbsp;
		  </a>
		  <a href="{$dd['url']}" class="btn btn-link collapsed">{$dd['name']}</a>
		</p>
		<div class="collapse" id="collapse-{$dd['code']}">
		  <div class="card card-body">

			{$code}

		  </div>
		</div>
EOD;
	return $html;
}


public static function offs($html, $offs)
{
	return preg_replace('~[\r\n]+~si', str_repeat(' ', $offs) . "\r\n", $html);
}




// ----- grids/tiles ----------

public function drawServicesTiles(array $data, $req=[], $mapCenter)
{
	if (!$data['items'])
	{
		$this->drawNothingFound();
		return;
	}

?>	<div class="container-fluid mt-3" id="sGrid">
		<div class="row">
			<div class="col-7">
				<?php $this->drawPagination($data['page'], $data['per_page'], $data['total_items'], $req); ?>

				<?php foreach (DataMapper::tilesData((array)$data['items']) as $row) : ?>
				  <a href="service.php?id=<?php echo $row['id']; ?>" class="cardlink">
					<div class="card mb-3">
					  <div class="card-body">
						<h5 class="title">
							<?php echo $row['name']; ?>
							<?php if ($row['organization']) : ?>
								<small>by <?php echo $row['organization']; ?></small>
							<?php endif; ?>
						</h5>
						<p class="descr"><?php echo $row['descr']; ?></p>
						<p class="address">
							<?php if ($row['lat'] && $row['lon']) : ?>
							  <img src="resources/markerR.png" height="16" width="16">
							<?php endif; ?>
							<?php echo $row['address']; ?>, <?php echo $row['city']; ?>, <?php echo $row['state']; ?>, <?php echo $row['zip']; ?></p>
						<p class="badges">
							<?php foreach ((array)$row['taxonomies'] as $taxonomy) : ?>
								<span class="badge badge-info mr-1" title="<?php echo $taxonomy; ?>"><?php echo $taxonomy; ?></span>
							<?php endforeach; ?>
						</p>
					  </div>
					</div>
				  </a>
				<?php endforeach; ?>
				<?php $this->drawPagination($data['page'], $data['per_page'], $data['total_items'], $req);
					  $this->drawDataActualDateNotification(); ?>
			</div>


			<div class="col-5">
			  <?php if ($mapCenter['scale']) : ?>
				<div class="sticky-top" style="height:99vh;" id="map">
					<style>
						.olPopup {border-radius:15px;}
						.olPopupContent {padding:15px; overflow: hidden !important;}
						.olPopupContent a {text-decoration:initial; color:#002b80;}
						.olPopup h2 {font-size:1em;}
						.olPopup p {font-size:0.75em;}
					</style>
					<script src="./OpenLayers/OpenLayers.min.js"></script>

					<script type="text/javascript">
						var map, layer;

						map = new OpenLayers.Map("map");
						var mapnik = new OpenLayers.Layer.OSM();
						map.addLayer(mapnik);
						var fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
						var toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection

						var newl = new OpenLayers.Layer.Text( "text", { location:"./resources/markers.txt"} );
						map.addLayer(newl);

						map.setCenter(new OpenLayers.LonLat(<?php echo $mapCenter['cLon']; ?>,<?php echo $mapCenter['cLat']; ?>).transform(fromProjection, toProjection), <?php echo $mapCenter['scale']; ?>);
					</script>
				</div>
			  <?php else : ?>
				<div class="sticky-top" style="height:99vh;" id="basicMapPlaceholder">
					<p>Geocoordinates not available</p>
				</div>
			  <?php endif; ?>
			</div>
		</div>
	</div>
<?php
}


public function drawOrgTiles(array $data, $req=[], $mapCenter)
{
	if (!$data['items'])
	{
		$this->drawNothingFound();
		return;
	}

?>	<div class="container-fluid mt-3" id="sGrid">
		<div class="row">
			<div class="col-7">
				<?php $this->drawOrgDetails($data); ?>

				<?php $this->drawPagination($data['page'], $data['per_page'], $data['total_items'], $req); ?>

				<?php foreach (DataMapper::tilesData((array)$data['items']) as $row) : ?>
				  <a href="service.php?id=<?php echo $row['id']; ?>" class="cardlink">
					<div class="card mb-3">
					  <div class="card-body">
						<h5 class="title">
							<?php echo $row['name']; ?>
							<?php if ($row['organization']) : ?>
								<small>by <?php echo $row['organization']; ?></small>
							<?php endif; ?>
						</h5>
						<p class="descr"><?php echo $row['descr']; ?></p>
						<p class="address">
							<?php if ($row['lat'] && $row['lon']) : ?>
							  <img src="resources/markerR.png" height="16" width="16">
							<?php endif; ?>
							<?php echo $row['address']; ?>, <?php echo $row['city']; ?>, <?php echo $row['state']; ?>, <?php echo $row['zip']; ?></p>
						<p class="badges">
							<?php foreach ((array)$row['taxonomies'] as $taxonomy) : ?>
								<span class="badge badge-info mr-1" title="<?php echo $taxonomy; ?>"><?php echo $taxonomy; ?></span>
							<?php endforeach; ?>
						</p>
					  </div>
					</div>
				  </a>
				<?php endforeach; ?>
				<?php $this->drawPagination($data['page'], $data['per_page'], $data['total_items'], $req);
					  $this->drawDataActualDateNotification(); ?>
			</div>


			<div class="col-5">
			  <?php if ($mapCenter['scale']) : ?>
				<div class="sticky-top" style="height:99vh;" id="map">

					<style>
						.olPopup {border-radius:15px;}
						.olPopupContent {padding:15px; overflow: hidden !important;}
						.olPopupContent a {text-decoration:initial; color:#002b80;}
						.olPopup h2 {font-size:1em;}
						.olPopup p {font-size:0.75em;}
					</style>
					<script src="./OpenLayers/OpenLayers.min.js"></script>

					<script type="text/javascript">
						var map, layer;

						map = new OpenLayers.Map("map");
						var mapnik = new OpenLayers.Layer.OSM();
						map.addLayer(mapnik);
						var fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
						var toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection

						var newl = new OpenLayers.Layer.Text( "text", { location:"./resources/markers_org.txt"} );
						map.addLayer(newl);

						map.setCenter(new OpenLayers.LonLat(<?php echo $mapCenter['cLon']; ?>,<?php echo $mapCenter['cLat']; ?>).transform(fromProjection, toProjection), <?php echo $mapCenter['scale']; ?>);
					</script>
				</div>
			  <?php else : ?>
				<div class="sticky-top" style="height:99vh;" id="basicMapPlaceholder">
					<p>Geocoordinates not available</p>
				</div>
			  <?php endif; ?>
			</div>
		</div>
	</div>
<?php
}


public function drawOrgDetails(array $data)
{
	$org = DataMapper::orgDetails((array)$data['items']);
?>
			<div class="orgDetails">
				<h2><?php echo $org['name']; ?></h2>
				<p class="description85"><?php echo $org['descr']; ?></p>
				<?php if ($org['url']) : ?>
					<p class="mb-0">url:&nbsp;&nbsp;<a href="<?php echo $org['url']; ?>"><?php echo $org['url']; ?></a></p>
				<?php endif; ?>

				<?php if ($org['email']) : ?>
					<p class="mb-0">email:&nbsp;&nbsp;<a href="mailto:<?php echo $org['email']; ?>"><?php echo $org['email']; ?></a></p>
				<?php endif; ?>
			</div>
<?php
}


public function drawDataGrid(array $data, $req=[])
{
	if (!$data)
	{
		$this->drawNothingFound();
		return;
	}

	$hh = DataMapper::gridHeaders($data);
?>	<div class="container">
	  <table class="table">
		<thead>
		  <tr>
			<?php foreach ($hh as $h) : ?>
			  <th scope="col"><?php echo $h; ?></th>
			<?php endforeach; ?>
		  </tr>
		</thead>
		<tbody>
		  <?php foreach (DataMapper::gridData($data) as $row) : ?>
		    <tr>
			  <?php foreach ($hh as $h)
				{
					$cell = $row[$h] ?? '';
					switch ($h)
					{
						case '#':
							echo "<th>{$cell}</th>";
							break;

						case 'Taxonomy Terms':
							echo '<td>';
							foreach ((array)$cell as $taxonomyItem)
							{
								$txt = strlen($taxonomyItem) > 25 ? substr($taxonomyItem, 0, 22) . '...' : $taxonomyItem;
								$href = 'services.php?searchBy=TaxonomyName&strict=true&family=true&TaxonomyName=' . urlencode($taxonomyItem);
								echo "<a href=\"{$href}\" class=\"badge badge-info mr-1\" title=\"{$taxonomyItem}\">{$txt}</a>";
							}
							echo '</td>';
							break;
						case 'Service Name':
							$href = 'service.php?id=' . $row['id'];
							echo "<td><a href=\"{$href}\">{$cell}</a></td>";
							break;
						case 'Organization Name':
							$href = 'services.php?searchBy=OrganizationName&OrganizationName=' . urlencode($cell);
							echo "<td><a href=\"{$href}\">{$cell}</a></td>";
							break;
						default:
							echo "<td>{$cell}</td>";
					}
				}
			  ?>
		    </tr>
		  <?php endforeach; ?>
		</tbody>
	  </table>
	</div>
<?php
}


public function drawNothingFound()
{
?>	<div class="container mt-4">
	  <div class="alert alert-warning" role="alert">
		0 records found
	  </div>
	</div>
<?php
}


public function drawAlert($msg)
{
?>	<div class="container mt-4">
	  <div class="alert alert-danger" role="alert">
		<?php echo $msg; ?>
	  </div>
	</div>
<?php
}



// ----- pagination ----------

public function drawPagination($num, $size, $totalItems, $req)
{
	$isFirst = $num == 1;
	$total = $size ? ceil($totalItems / $size) : 0;
	if ($total < 2)
		return;
	$isLast = $num == $total;
	$shorten = $total >= 10;

	$min = 1 + $size * ($num - 1);
	$max = $size * $num;

?>
	  <div class="row">
		<div class="col-3">
			Results <?php echo $min; ?> - <?php echo $max; ?> of <?php echo $totalItems; ?>
		</div>
		<div class="col-9">
			<nav aria-label="Page navigation">
			  <ul class="pagination justify-content-end">
				<?php $this->drawPaginationLink('Previous', $isFirst, $req, $num - 1) ?>
				<?php for ($i=1; $i<=$total; $i++) : ?>
				  <?php if ($shorten && $i>2 && $i<$num-2) : ?>
					<?php $this->drawPaginationLink('...', true, $req, $num) ?>
					<?php $i = $num-3; ?>
				  <?php elseif ($shorten && $i>$num+2 && $i<$total-1) : ?>
					<?php $this->drawPaginationLink('...', true, $req, $num) ?>
					<?php $i = $total-2; ?>
				  <?php elseif ($num == $i) : ?>
					<li class="page-item active"><a class="page-link" href="#"><?php echo $i; ?></a></li>
				  <?php else : ?>
					<?php $this->drawPaginationLink($i, false, $req, $i) ?>
				  <?php endif; ?>
				<?php endfor; ?>
				<?php $this->drawPaginationLink('Next', $isLast, $req, $num + 1) ?>
			  </ul>
			</nav>
	    </div>
	  </div>
<?php
}

public function drawPaginationLink($label, $disabled, $req, $n)
{
	$link = $disabled ? '#' : ('services.php?' . RequestMapper::getEnc($req, ['page' => $n]));
?>
	<li class="page-item<?php if ($disabled) echo ' disabled'; ?>">
	  <?php echo "<a class=\"page-link\" href=\"{$link}\"" . ($disabled ? ' tabindex="-1" aria-disabled="true">' : '>'); ?>
	    <?php echo $label; ?>
      </a>
	</li>
<?php
}



// ----- / grids/tiles ----------



// ----- service page ----------

public function drawServiceCard($dd)
{
?>	<div class="container pl-4">
	  <div class="row">
		<div class="col-7 mt-2">
		  <div class="row">
			<div class="col">
				<h2><?php echo $dd['Service Name']; ?></h2>
				<h6>Organization: <a href="organization.php?searchBy=OrganizationName&OrganizationName=<?php echo urlencode($dd['Organization Name']); ?>"><?php echo $dd['Organization Name']; ?></a></h6>
				<p class="description85"><?php echo $dd['Description']; ?></p>
			</div>
		  </div>
		  <div class="row">
			<div class="col-<?php echo $dd['Schedules'] ? 6 : 12; ?> ml-0">
				<?php if ($dd['Phones']) : ?>
					<p class="mb-0">tel:&nbsp;&nbsp;<?php echo implode(', ', (array)$dd['Phones']); ?></p>
				<?php endif; ?>

				<?php if ($dd['Website']) : ?>
					<p class="mb-0">url:&nbsp;&nbsp;<a href="<?php echo $dd['Website']; ?>"><?php echo $dd['Website']; ?></a></p>
				<?php endif; ?>

				<?php if ($dd['Languages']) : ?>
					<p class="mb-3">languages:&nbsp;&nbsp;<?php echo implode(', ', (array)$dd['Languages']); ?></p>
				<?php endif; ?>

				<?php if ($dd['Taxonomy']) : ?>
					<h5 class="mt-3 mb-3">Taxonomy</h5>
					<p>
					<?php foreach ((array)$dd['Taxonomy'] as $taxonomyItem)
						{
							$txt = strlen($taxonomyItem) > 25 ? substr($taxonomyItem, 0, 22) . '...' : $taxonomyItem;
							$href = 'services.php?searchBy=TaxonomyName&strict=true&family=true&TaxonomyName=' . urlencode($taxonomyItem);
							echo "<a href=\"{$href}\" class=\"badge badge-info mr-1\" title=\"{$taxonomyItem}\">{$txt}</a>";
						}
					?>
					</p>
				<?php endif; ?>
			</div>
			<?php if ($dd['Schedules']) : ?>
				<div class="col-6">
					<div class="card bg-light mb-3" style="max-width: 18rem;">
					  <div class="card-body">
						<p class="card-text"><?php echo $dd['Schedules']; ?></p>
					  </div>
					</div>
				</div>
			<?php endif; ?>
		  </div>

		  <?php if ($dd['Details']) : ?>
			<div class="row">
			<div class="col-12">
			<h5 class="mt-3 mb-3">Details</h5>
				<?php foreach ((array)$dd['Details'] as $detType=>$detText) : ?>
				  <p>
					<h6 style="display: inline-block;"><?php echo $detType; ?>:</h6>&nbsp;&nbsp;<?php echo $detText; ?>
				  </p>
				<?php endforeach; ?>

			</div>
			</div>
		  <?php endif; ?>

		</div>

		<div class="col-5">
		  <div class="row mt-3">
			<div class="col">
				<?php if ($dd['Lng'] && $dd['Lat']) : ?>
					<script src="./OpenLayers/OpenLayers.min.js"></script>
					<div id="basicMap"></div>
					<script>
						map = new OpenLayers.Map("basicMap");
						var mapnik         = new OpenLayers.Layer.OSM();
						var fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
						var toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
						var position       = new OpenLayers.LonLat(<?php echo $dd['Lng']; ?>,<?php echo $dd['Lat']; ?>).transform( fromProjection, toProjection);
						var zoom           = 15;

						map.addLayer(mapnik);

						var size = new OpenLayers.Size(25,25);
						var offset = new OpenLayers.Pixel(-12,-25);
						var icon = new OpenLayers.Icon('resources/markerR.png',size,offset);
						var markers = new OpenLayers.Layer.Markers("Markers");
						map.addLayer(markers);
						markers.addMarker(new OpenLayers.Marker(position, icon));

						map.setCenter(position, zoom);
					</script>
				<?php else : ?>
					<div id="basicMapPlaceholder">
						<p class="mb-0">Geocoordinates not available<br/>
							<small><a href="https://www.google.com.ua/maps/place/<?php echo urlencode(implode(', ', [$dd['Address'],$dd['City'],$dd['State'],$dd['Zip']])); ?>">See on Google Maps</a></small>
						</p>
					</div>
				<?php endif; ?>
			</div>
		  </div>


		  <div class="row mt-3">
			<div class="col">
				<h6>Location</h6>
				<h5><?php echo $dd['Location Name']; ?></h5>
				<p><?php echo implode(', ', [$dd['Address'], $dd['City'], $dd['State'], $dd['Zip']]); ?></p>
			</div>
		  </div>


		</div>


	  </div>



	</div>
<?php
}

public function drawCardGroupOpen($header, $width)
{
?>		 <div class="col-<?php echo $width; ?>">
			<div class="card">
				<div class="card-header"><?php echo "<u>{$header}</u>"; ?></div>
				<div class="card-body row">
<?php
}


public function drawCardGroupClose()
{
?>				</div>
		    </div>
		 </div>
<?php
}


public function drawCardField($dd, $f, $width=null)
{
	$label = $dd[$f];
	$label = $label == '' || $label == '0000-00-00' ? '-' : $label;
	echo $width
		? "<div class=\"col-{$width}\">"
		: "<div class=\"col\">"
	?><small class="text-muted"><?php echo RequestMapper::getLabel($f); ?></small><br /><h6><?php echo $label; ?></h6></div><?php
}




// --------- srv -------------------------------


public function drawNavbar($req=[], $fullwidth=false)
{
?>	<div class="container<?php echo $fullwidth ? '-fluid language_link' : ''; ?>">
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
			<?php if ($req) : ?>
			  <?php echo '<a class="nav-link" href="search.php">'; ?>&laquo; Back to search form</a>
			<?php endif; ?>
			<?php if ($req) : ?>
				<a class="nav-link"><?php echo RequestMapper::titleEnc($req, true); ?></a>
			<?php endif; ?>
			<a class="nav-link white_space">&nbsp;</a>
		    <a class="nav-link " id="google_translate_element"></a>
		</nav>
	</div>
<?php
}

public function drawNavbarBack()
{
?>	<div class="container language_link">
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
		  <a class="nav-link" onclick="window.history.back();" href="#">&laquo; Back to search results</a>
		  <a class="nav-link" id="google_translate_element"></a>
		</nav>
	</div>
<?php
}

public function drawHeaders()
{
?><!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title></title>
    <style>
	  body{
		top: 0 !important;
	  }
	  #st-1 .st-btn[data-network='sharethis'] {
		background: transparent !important;
		padding-left: 0;
	  }
	  #google_translate_element{
		width: 140px;
		position: absolute;
		right: 32px;
	  }
	  .goog-te-banner-frame.skiptranslate{
		display: none;
	  }
	  .goog-te-gadget img {
		display: none;
	  }
	  .goog-te-gadget-simple {
		background-color: transparent !important;
		border: 0 !important;
	  }
	  .goog-te-gadget-simple .goog-te-menu-value span {
		color: white;
		font-size: 14px;
		font-weight: 500;
	  }
	  .goog-te-menu-value span{
		font-family: 'Poppins', sans-serif !important;
	  }

	  .goog-te-menu-value span:nth-child(3){
		display: none;
	  }
	  .goog-te-menu-value span:nth-child(5){
		display: none;
	  }
	  .goog-te-menu-value span:nth-child(1){

	  }
	  .goog-te-gadget-simple .goog-te-menu-value span:nth-of-type(1) {
		font-family: 'Font Awesome' !important;
		font-weight: normal;
		font-style: normal;
		font-size: 22px !important;
		position: relative;
		display: inline-block;
		-webkit-transform: translate(0, 0);
		-ms-transform: translate(0, 0);
		-o-transform: translate(0, 0);
		transform: translate(0, 0);
		text-rendering: auto;
		speak: none;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
		visibility: hidden;
	  }
	  .goog-te-gadget-simple .goog-te-menu-value span:before {
		content: "Select Language";
		visibility: visible;
		font-family: Roboto,sans-serif;
		font-size: 1rem;
		color: #007bff;
	  }
	  .goog-te-menu-value {
		max-width: 22px;
		display: inline-block;
	  }
    </style>

    <link rel="stylesheet" href="./resources/styles.css">
    <link rel="stylesheet" href="./resources/responsive.css">
  </head>
  <body>
<?php
}

public function drawDataActualDateNotification()
{
?>  	<div class="container">
		  <div class="row mt-4 mb-5 justify-content-center">
			<div class="col-11 py-4 text-muted" style="border-top: 1px solid #ccc; text-align:center;">
				<?php echo 'Data last updated <i>' . DATA_ACTUAL_DATE . '</i>'; ?>
			</div>
		  </div>
		</div>
<?php
}

public function drawFooters($autocopmlete=null)
{
?>    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity=
		"sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<!-- Google translate -->
	<script type="text/javascript">
		function googleTranslateElementInit() {
			new google.translate.TranslateElement(
				{
					pageLanguage: 'en',
					includedLanguages: 'en,es,ht',
					layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
					multilanguagePage: true
				},
				'google_translate_element'
			);
		}
	</script>
	<script type="text/javascript" src=
		"https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
	</script>

	<?php if ($autocopmlete)
		$this->autocompleteScripts();
	?>
  </body>
</html><?php
}

public function autocompleteScripts()
{
?>    <script src="https://typeahead.js.org/releases/latest/typeahead.bundle.js"></script>
	<script src="./resources/search.js"></script>

<?php
}

}
