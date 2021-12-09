/* mapbox */

var map = null
var domMarkers = [];
var mapMarkers = [];
var popup = null
var d = 0.06

function newMap() {
	mapboxgl.accessToken = 'pk.eyJ1Ijoic291bmRwcmVzcyIsImEiOiJjazY1OTF3cXIwbjZyM3BtcGt3Y3F2NjZwIn0.3hmCJsl0_oBUpoVsNJKZjQ';
	
	var center = (typeof center == 'undefined') ? [-77.037, 38.902] : center;
	var zoom = (typeof zoom == 'undefined') ? 11 : zoom;
	
	map = new mapboxgl.Map({
		container: 'map',
		//style: 'mapbox://styles/mapbox/light-v10',
		//style: 'mapbox://styles/mapbox/navigation-day-v1',
		style: 'mapbox://styles/mapbox/streets-v11',
		//style: 'mapbox://styles/mapbox/dark-v10',
		center: center,
		zoom: zoom
	});
	map.addControl(new mapboxgl.NavigationControl());
}

function drawMarkers(geojson) {
	if ($.isEmptyObject(geojson.features))
		return;
	
	var bounds = [[360, 180], [-360, -180]];

    geojson.features.forEach(function(marker) {
        // create a DOM element for the marker
        var el = document.createElement('div');
        el.className = 'marker';
        el.style.backgroundImage = 'url(/img/markerR.png)';

        el.addEventListener('click', function(e) {
			mapPopup(marker.properties);
			e.stopPropagation();
        });

        // add marker to map
		domMarkers.push(el);
        mapMarkers.push(new mapboxgl.Marker(el)
				.setLngLat(marker.geometry.coordinates)
				.addTo(map));

		bounds[0][0] = Math.min(bounds[0][0], marker.geometry.coordinates[0] - d);
		bounds[0][1] = Math.min(bounds[0][1], marker.geometry.coordinates[1] - d);
		bounds[1][0] = Math.max(bounds[1][0], marker.geometry.coordinates[0] + d);
		bounds[1][1] = Math.max(bounds[1][1], marker.geometry.coordinates[1] + d);
    });

	if (bounds[0][0] == 360)
		bounds = [[-76.956, 38.940], [ -77.141, 38.838]]
	if (popup)
		popup.remove();
	map.fitBounds(bounds);
}

function mapPopup(pr) {
	var description = `
<table><tbody>
	<tr><td>${pr.title}</td></tr>
	<tr><td>${pr.description}</td></tr>
</tbody></table>`;
	if (popup)
		popup.remove();
	popup = new mapboxgl.Popup()
		.setLngLat([pr.lon,pr.lat])
		.setHTML(description)
		.addTo(map);
		
	map.fitBounds([
		[pr.lon - d,pr.lat - d],
		[pr.lon + d,pr.lat + d]
	]);
	
}


