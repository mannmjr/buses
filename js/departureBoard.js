$(function() {
	// Write default stop to local storage if not already set
	if (localStorage.getItem('atcocode') == null) {
		localStorage.setItem('atcocode', '450011796');
	}
	
	// Refresh on load and every 2 mins
	refresh();
	window.setInterval(refresh, 1000 * 120);
	
	// Clicking anywhere on the page takes you to map.html
	$(document).click(function() {
		location.replace("./map.html");
	});
});

function refresh() {
	$("table").html("");
	$.get("ajax/buses.php?atcocode=" + localStorage.getItem('atcocode'), function(data) {
		// console.log(data);
		buses = JSON.parse(data);
		for (i in buses) {
			bus = buses[i];
			console.log(buses[i]);
			row = $("<tr></tr>");
			row.append("<td>" + bus.line + "</td>");
			row.append("<td>" + bus.direction + "</td>");
			row.append("<td>" + bus.friendly_time + "</td>");
			$("table").append(row);
		}
	});
}