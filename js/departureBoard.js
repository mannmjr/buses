$(function() {
	// Write default stop to local storage if not already set
	if (localStorage.getItem('atcocode') == null) {
		localStorage.setItem('atcocode', '450011796');
	}
	
	// Refresh on load and every 2 mins
	refresh();
	window.setInterval(refresh, 1000 * 60);
	
	// Clicking anywhere on the page takes you to map.html
	$(document).click(function() {
		location.replace("./map.html");
	});
});

function refresh() {
	$("#container").html("");
	$.get("ajax/buses.php?atcocode=" + localStorage.getItem('atcocode'), function(data) {
		buses = JSON.parse(data);
		for (i in buses) {
			bus = buses[i];
			console.log(buses[i]);
			row = $('<div class="row"></div>');
			row.append('<div class="cell line">' + bus.line + '</div>');
			row.append('<div class="cell direction">' + bus.direction + '</div>');
			row.append('<div class="cell time">' + bus.friendly_time + '</div');
			$("#container").append(row);
		}
	});
}