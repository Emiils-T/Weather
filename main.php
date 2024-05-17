<?php

$personalAPIKey = "a1a93a673bc6fc9e9dfbe41514f1ac1d";

$metricUnits = new stdClass();
$metricUnits->name = 'metric';
$metricUnits->degrees = '°C';
$metricUnits->windSpeed = 'm/s';

$imperialUnits = new stdClass();
$imperialUnits->name = 'imperial';
$imperialUnits->windSpeed = 'mph';
$imperialUnits->degrees = 'f';

$standardUnits = new stdClass();
$standardUnits->name = 'standard';
$standardUnits->windSpeed = 'm/s';
$standardUnits->degrees = 'k';

$geoCode = null;
while ($geoCode == null) {
    $city = (string)readline("Enter city name for weather details: ");
    if ($city == "" || is_numeric($city)) {
        while (true) {
            $city = readline("Please enter a valid city name: ");
            if (!$city == '' && !is_numeric($city)) {
                break;
            }
        }
    }

    $country = (string)readline("Enter country code for weather details: ");
    if (!strlen($country) == 2 || $country == '' || is_numeric($country)) {
        while (true) {
            $country = readline("Please enter a two letter country code, e.g., 'US' : ");
            if (strlen($country) == 2 && !$country == '' && !is_numeric($country)) {
                break;
            }
        }
    }

    $geoCode = "http://api.openweathermap.org/geo/1.0/direct?q=$city,$country&limit=5&appid=$personalAPIKey";
    $geoCode = file_get_contents($geoCode);
    $geoCode = json_decode($geoCode);
    if ($geoCode == null) {
        echo "Cant find $city in $country \n";
    }
}

$lat = $geoCode[0]->lat;
$lon = $geoCode[0]->lon;

$units = ['metric', 'imperial', 'kelvin'];
foreach ($units as $index => $unit) {
    echo $index . ". " . $unit . "\n";
}

$selectedUnit = new stdClass();
$selection = (string)readline("Select unit for weather details: ");
switch ($selection) {
    case 0:
        $selectedUnit = $metricUnits;
        break;
    case 1:
        $selectedUnit = $imperialUnits;
        break;
    case 2:
        $selectedUnit = $standardUnits;
        break;
    default:
        echo "Invalid unit selection \n";
        break;
}

$weatherAPI = "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=$personalAPIKey&units=$selectedUnit->name";
$weatherData = file_get_contents($weatherAPI);
$weatherData = json_decode($weatherData);
$windSpeed = $weatherData->wind->speed;

echo PHP_EOL;

echo "Weather currently is " . $weatherData->weather[0]->description . "\n";
echo "Temperature is: " . $weatherData->main->temp . " $selectedUnit->degrees\n";
echo "Feels like: " . $weatherData->main->feels_like . " $selectedUnit->degrees\n";
echo "Wind speed is: " . $windSpeed . " $selectedUnit->windSpeed\n";
echo "Humidity is: " . $weatherData->main->humidity . "%\n";