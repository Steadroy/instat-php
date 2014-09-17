<?php

require_once 'vendor/instagram.class.php';
require_once("vendor/FoursquareAPI.class.php");

ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);

$link=mysqli_connect("localhost","instat","instat2015","instatdb");
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Set Instagram API
$instagram = new Instagram('6ca40952efaf4aea9f74765a59ccfe05');

  // Set FOURSQUARE client key and secret
  $client_key = "NGGVWAPIMRHQJIPHXJUCTU3OVTHMRNIVZMY32OUGX0IRNLVP";
  $client_secret = "3BCPIEII5U4TWJ4MXCMGZPGQGITXK5PPCFT4NFLNJ5B5TU1D";

  // Load the Foursquare API library
  if($client_key=="" or $client_secret=="")
  {
        echo 'Load client key and client secret from <a href="https://developer.foursquare.com/">foursquare</a>';
        exit;
  }

  $foursquare = new FoursquareAPI($client_key,$client_secret);

  $cities = [
    'Moscow' => [],
    'Saint Petersburg' => [],
    'Novosibirsk' => [],
    'Yekaterinburg' => [],
    'Nizhny Novgorod' => [],
  ];

/*
  $id = 0;
  $id_venue = 0;

foreach ($cities as $key => $city) {
  // Prepare parameters
  mysqli_query($link,"INSERT INTO cities (city_id, city_name) VALUES ('".$id."','".$key."')");

 
  $city_venues = [];

  // Perform a request to a public resource
  echo $key;
  $params = array("near"=>$key, "limit"=>10);
  $response = $foursquare->GetPublic("venues/explore",$params);
  $venues = json_decode($response);

  var_dump($venues);

  foreach($venues->response->groups[0]->items as $venue) {

    $temp_venue = [
      'f4v2_id' => 0,
      'name' => "",
      'lat' => 0,
      'lng' => 0
    ];


    $temp_venue['f4v2_id'] = $venue->venue->id;
    if(!empty($venue->venue->name) && is_string($venue->venue->name)) { 
      $temp_venue['name'] = str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $venue->venue->name); 
    } 
    $temp_venue['lat'] = $venue->venue->location->lat;
    $temp_venue['lng'] = $venue->venue->location->lng;

    $city_venues[] = $temp_venue;
    
    // Save data to DB
    $id_venue++;
    $query = "INSERT INTO venues (venue_id, city_id, f4v2_id, name, lat, lng, media_count, media_count_lastweek, media_count_percent ) VALUES ('".$id_venue."','".$id."','".$venue->venue->id."','".$temp_venue['name']."','".$venue->venue->location->lat."','".$venue->venue->location->lng."','0','0','0')";
    mysqli_query($link,$query);
    //echo mysqli_errno($link) . ": " . mysqli_error($link) . "\n";

  }
  $id++;
}
*/


  $cities = mysqli_query($link, "SELECT * FROM cities");
  $error_code = 200;

  $session = mysqli_query($link,"SELECT saved_session FROM sessions WHERE id = 1");
  $session_arr = explode("|", $session);
  $city['city_id'] = $session_arr[0];

  while ($city = mysqli_fetch_array($cities, MYSQL_ASSOC)) {
    if ($error_code != 200) break;

    $venues = mysqli_query($link, "SELECT * FROM venues WHERE city_id = '".$city['city_id']."'");

    $i = 0;
    while ($venue = mysqli_fetch_array($venues, MYSQL_ASSOC)) {

      mysqli_query($link, "UPDATE venues SET media_count = '".rand(10000, 1000000)."' WHERE venue_id = '".$venue['venue_id']."'");


      if ($city['f4v2_id'] = $session_arr[0];
      $city['city_id'] = $session_arr[0];
      $inloc = $instagram->searchLocationF4($venue['f4v2_id'], $distance = 30);

      $media_count = 0;

        $media_loc = $instagram->getLocationMedia($inloc->data[0]->id);
        $media_count += count($media_loc->data);

        while ($media_loc->pagination->next_url) {
            $media_loc = $instagram->pagination($media_loc, $limit = 100);
            if ($media_loc->meta->code != 200) {
              mysqli_query($link, "UPDATE venues SET media_count = '".$media_count."' WHERE venue_id = '".$venue['venue_id']."'");
              $saved_scan = $city['city_id']."|".$venue['f4v2_id']."|".$media_loc->pagination->next_url;
              mysqli_query($link,"UPDATE sessions SET date = '".date('Y/m/d H:i:s')."', saved_session = '".$saved_scan."' WHERE id = 1");
              $error_code = 429;
              break;
            } else {

              $media_count += count($media_loc->data);

            }

        }

      echo $media_count;

      $i++;
      if ($i == 1) {break;}

    }

  }


mysqli_close($link);


?>
