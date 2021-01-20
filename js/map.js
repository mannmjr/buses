// Set default map centre and zoom level if not already set
if (localStorage.getItem("lat") == null) {
	localStorage.setItem("lat", "53.759930");
}

if (localStorage.getItem("lon") == null) {
	localStorage.setItem("lon", "-1.656748");
}

if (localStorage.getItem("zoomLevel") == null) {
	localStorage.setItem("zoomLevel", "17");
}

// Parse stored defaults
lat = parseFloat(localStorage.getItem("lat"));
lon = parseFloat(localStorage.getItem("lon"));
zoomLevel = parseInt(localStorage.getItem("zoomLevel"));

// Display map
map = L.map("map").setView([lat, lon], zoomLevel);

L.tileLayer("https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}", {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: "mapbox/streets-v11",
    tileSize: 512,
    zoomOffset: -1,
    accessToken: "pk.eyJ1IjoibWFubm1qciIsImEiOiJja2s1ZXlhMnQwNmhwMndsODdsODduajZ1In0.RyZmO56rc-tYfzFclz-PCg"
}).addTo(map);

// Add event listeners
map.on("zoomend, moveend", function(e) {
	refresh();
});

// Perform initial 'refresh'
refresh();


function refresh() {
	console.log(map.getCenter());
	
	// Clear existing markers
	// First layer is the base map
	firstIteration = true;
	map.eachLayer(function(layer) {
		if (firstIteration) {
			firstIteration = false;
		} else {
			layer.remove();
		}
	});
	
	// Only add markers if zoomed in sufficiently
	if (map.getZoom() < 17) {
		return;
	}
	
	// Get bounding box of current view
	bounds = map.getBounds();
	lats = [ bounds.getNorth(), bounds.getSouth() ];
	lons = [ bounds.getEast(), bounds.getWest() ];
	
	// Call back end script to get bus stops
	$.get("ajax/stops.php?lats=" + lats + "&lons=" + lons, function(data) {
		stops = JSON.parse(data);
		// console.log(stops);
		
		// Add markers to map
		for (i in stops) {
			stop = stops[i];
			lat = stop.latitude;
			lon = stop.longitude;
			L.marker([lat, lon], {title: stop.atcocode}).on("click", markerClick).addTo(map);
		}
	});
}

function markerClick(e) {
	// Write selected stop and current map state to local storage
	localStorage.setItem("atcocode", this.options.title);
	localStorage.setItem("lat", map.getCenter().lat);
	localStorage.setItem("lon", map.getCenter().lng);
	localStorage.setItem("zoomLevel", map.getZoom());
	location.href = "departureBoard.html";
}