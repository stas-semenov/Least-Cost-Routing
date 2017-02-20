<?php
// Search for the cheapest route
function cheapest_route($city_from, $city_to, &$cost_of_travel) {
	$city_index = 0;
	$city_code = array();
	$cities_graph = array();

	foreach ($cost_of_travel as $val) {
		if (!isset($city_code[$val[0]]))
			$city_code[$val[0]] = $city_index++;
		if (!isset($city_code[$val[1]]))
			$city_code[$val[1]] = $city_index++;
		// symmetrically
		$cities_graph[$city_code[$val[0]]][$city_code[$val[1]]] = $val[2];
		$cities_graph[$city_code[$val[1]]][$city_code[$val[0]]] = $val[2];
	}

	if (!isset($city_code[$city_from]))
		throw new Exception('Not found.');
	$city_from_code = $city_code[$city_from];
	if (!isset($city_code[$city_to]))
		throw new Exception('Not found.');
	$city_to_code = $city_code[$city_to];
	if ($city_from_code == $city_to_code)
		throw new Exception('Same cities.');

	$cities = array();
	$opt_path = array();
	foreach (array_keys($cities_graph) as $key => $val)
		$cities[$val] = INF;

	$cities[$city_from_code] = 0;
	while (!empty($cities)) {
		$min = array_keys($cities, min($cities));
		if (array_search($city_to_code, $min) !== FALSE)
			break;
		foreach ($cities_graph[$min[0]] as $key => $val) {
			if (isset($cities[$key]) && $cities[$key] > $cities[$min[0]] + $val) {
				$cities[$key] = $cities[$min[0]] + $val;
				$opt_path[$key] = array($min[0], $cities[$key]);
			}
		}
		unset($cities[$min[0]]);
	}
	if (!array_key_exists($city_to_code, $opt_path)) {
		return array('path' => array(), 'cost' => -1);
	}

	$cheap_path = array();
	$pos = $city_to_code;
	while ($pos != $city_from_code) {
		$cheap_path[] = array_keys($city_code, $pos)[0];
		$pos = $opt_path[$pos][0];
	}

	$cheap_path[] = array_keys($city_code, $city_from_code)[0];
	$cheap_path = array_reverse($cheap_path);

	return array('path' => $cheap_path, 'cost' => $opt_path[$city_to_code][1]);
}

$cost_of_travel = array();
if (($handle = fopen("table.russia.csv", "r")) !== FALSE) {
	$cities = fgetcsv($handle);
	$num = count($cities);
	$line = 0;
	while (($data = fgetcsv($handle)) !== FALSE) {
		$line++;
		for ($i = $line + 1; $i < $num; $i++) {
			$cost = 0 + $data[$i];
			if ($cost > 0)
				$cost_of_travel[] = array($cities[$line], $cities[$i], floor($data[$i] / 25) * 25);
		}
	}
	fclose($handle);
}
$cheap_path = cheapest_route("Санкт-Петербург", "Анапа", $cost_of_travel);
print_r($cheap_path);
$cheap_path = cheapest_route("Москва", "Комсомольск-на-Амуре", $cost_of_travel);
print_r($cheap_path);
?>
