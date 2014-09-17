<?php

	$link=mysqli_connect("localhost","instat","instat2015","instatdb");
	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram - Популярные места по городам (Москва)</title>
    <link href="assets/style.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <header class="clearfix">
        <h1>Instagram <span>Популярные места России по городам</span></h1>
      </header>
      <div class="main">
		<?php // Get info from DB
		  $cities = mysqli_query($link, "SELECT * FROM cities");

		  while ($city = mysqli_fetch_array($cities, MYSQL_ASSOC)) {
		  	echo '<div class="city"><div class="header">'.$city['city_name'].'</div>';

		    $venues = mysqli_query($link, "SELECT * FROM venues WHERE city_id = '".$city['city_id']."'");
		    $venue_sorted = [];
		    $count = [];
		    while ($venue = mysqli_fetch_array($venues, MYSQL_ASSOC)) {
		    	$venue_sorted[] = $venue;
		    	$count[] = $venue['media_count'];
		    }

			array_multisort($count, SORT_DESC, $venue_sorted);

			for ($i = 0; $i < 10; $i++) {
				
				echo '<div class="venue">
          				<div class="venue-name">';
            	echo $venue_sorted[$i]['name']; 
            	echo '</div>
            		<div class="insta-info-all">';
             	echo ($venue_sorted[$i]['media_count']) ? $venue_sorted[$i]['media_count'] : 0;
            	echo '</div>
      					</div>';

			   
		    }
		    echo "</div>";
		  }
		?>

        <footer>
          <p>Сделано Appigram
        </footer>
      </div>
    </div>

  </body>
</html>