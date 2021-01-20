<?php

class TransportAPI {
	private array $credentials;
	private int $credIndex;
	private string $baseURL = "https://transportapi.com/v3";
	
	public function __construct(array $credentials) {
		$this->credentials = $credentials;
		$this->credIndex = (int) trim(file_get_contents(dirname(__FILE__) . "/../data/credIndex.txt"));
	}
	
	/**
	 *  @brief Changes which credentials to use when limit exceeded
	 *  
	 */
	private function incrementCredIndex() : void {
		if ($this->credIndex == count($this->credentials) - 1) {
			$this->credIndex = 0;
		} else {
			$this->credIndex++;
		}
		file_put_contents(dirname(__FILE__) . "/../data/credIndex.txt", $this->credIndex);
	}
	
	/**
	 *  @brief Builds the relevant API URL and adds API credentials to the query string
	 *  
	 *  @param $resource is a "Resource" as defined at
	 *  	 https://developer.transportapi.com/docs?raml=https://transportapi.com/v3/raml/transportapi.raml
	 *  @param $query is an array of key-value pairs to come after "?" in the URL. Do not include credentials
	 *  @return Return URL
	 *  
	 */
	private function getURL(string $resource, array $query=[]) : string {
		$query = array_merge($query, $this->credentials[$this->credIndex]);
		$queryString = "";
		foreach ($query as $key => $value) {
			$queryString .= "$key=$value&";
		}
		// Remove trailing "&"
		$queryString = substr($queryString, 0, -1);
		
		return $this->baseURL . $resource . "?" . $queryString;
	}
	
	/**
	 *  @brief Makes the request to the API. Cycles through credentials until the request is successful
	 *  
	 *  @param $resource is a "Resource" as defined at
	 *  	 https://developer.transportapi.com/docs?raml=https://transportapi.com/v3/raml/transportapi.raml
	 *  @param $query is an array of key-value pairs to come after "?" in the URL. Do not include credentials
	 *  @return Return the API response as a JSON object
	 */
	private function request(string $resource, array $query=[]) : object {
		while (true) {
			$url = $this->getURL($resource, $query);
			$handle = curl_init($url);
			curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
			$response = curl_exec($handle);
			$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
			if($httpCode == 200) {
				curl_close($handle);
				return json_decode($response);
			} else {
				$this->incrementCredIndex();
			}
		}
	}
	
	/**
	 *  @brief Get an array of bus stops near a given latitude and longitude
	 *  
	 *  @param $lat Latitude
	 *  @param $lon Longitude
	 *  @return Array of bus stops
	 */
	public function getNearbyStops(float $lat, float $lon) : array {
		$resource = "/uk/places.json";
		$query = [
			"lat" => $lat,
			"lon" => $lon,
			"type" => "bus_stop",
		];
		return $this->request($resource, $query)->member;
	}
	
	/**
	 *  @brief Get an array of bus stops inside a bounding box
	 *  
	 *  @param $min_lat Smallestlatitude
	 *  @param $min_lon Smallest longitude
	 *  @param $max_lat Largest latitude
	 *  @param $max_lon Largest longitude
	 *  @return Array of bus stops
	 */
	public function getBoundingBoxStops(array $lats, array $lons) : array {
		$min_lat = min($lats);
		$max_lat = max($lats);
		$min_lon = min($lons);
		$max_lon = max($lons);
		
		$resource = "/uk/places.json";
		$query = [
			"min_lat" => $min_lat,
			"min_lon" => $min_lon,
			"max_lat" => $max_lat,
			"max_lon" => $max_lon,
			"type" => "bus_stop",
		];
		return $this->request($resource, $query)->member;
	}
	
	/**
	 *  @brief Get up-coming departures from selected bus stop
	 *  
	 *  @param $atcocode ATCO code of a bus stop
	 *  
	 *  @return Array of departures, soonest first
	 */
	public function getDepartures(string $atcocode) : array {
		// Query API
		$resource = "/uk/bus/stop/$atcocode/live.json";
		$departures = $this->request($resource)->departures;
		
		// Pick out relevant information
		$buses = [];
		foreach ($departures as $service) {
			foreach ($service as $bus) {
				if ((bool) $bus->status->cancellation->value) {
					continue;
				}
				$details = [
					"line" => $bus->line,
					"direction" => $bus->direction,
					"time" => strtotime($bus->expected_departure_date . "T" . $bus->best_departure_estimate),
				];
				$buses[] = $details;
			}
		}
		
		// Sort soonest first
		usort($buses, function($a, $b) {
			return $a["time"] - $b["time"];
		});
		
		// Give each bus a 'friendly_time':
		// If the next bus is due in the next 10 mins, friendly time = X mins
		// If the next bus is due in the next minute or past due, friendly time = 'Due'
		// All other buses, friendly time = "HH:MM"
		$next_bus = true;
		foreach ($buses as $key => $value) {
			if ($next_bus) {
				$minutesUntilDue = floor(($buses[$key]["time"] - time()) / 60);
				if ($minutesUntilDue <= 1) {
					$buses[$key]["friendly_time"] =  "Due";
				} else if ($minutesUntilDue >= 10) {
					$buses[$key]["friendly_time"] =  date("H:i", $buses[$key]["time"]);
				}
				else {
					$buses[$key]["friendly_time"] = $minutesUntilDue . " min";
				}
				$next_bus = false;
			} else {
				$buses[$key]["friendly_time"] = date("H:i", $buses[$key]["time"]);
			}
		}
		
		return $buses;
	}
}






