$(function() {
	refresh();
	window.setInterval(refresh, 1000 * 120);
});

function refresh() {
	$("table").html("");
	$.get("buses.php", function(data) {
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