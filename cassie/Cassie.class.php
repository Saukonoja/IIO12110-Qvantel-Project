
<?php
class Cassie{

	//Declare array of global variables needed to connect to Cassandra
	public $GLOBALS = array(
		'cluster' => null,
		'keyspace' => null,
		'session' => null
	);

	//Connect to local Cassandra cluster and keuspace 'Drupal'
	public function connect(){
		$GLOBALS['cluster'] = Cassandra::cluster()
                                ->build();
		$GLOBALS['keyspace'] = 'drupal';
                $GLOBALS['session'] = $GLOBALS['cluster']->connect($GLOBALS['keyspace']);
	}

	//Updates table 'popularity' that counts visits in certain page
	public function updateCounter($address){
		$statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
			"UPDATE popularity
			  SET counter = counter + 1
			  WHERE address = '$address'"
		));
	}

	//Gets all products for single category page
	public function getProducts($page_id){
		$result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
			("SELECT product_id, price, description, image_id FROM products WHERE page_id = '$page_id'"));
		return $result;
	}

	//Gets all info needed to show to client of a product
	public function getProductInfo($page_id, $product_id){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
                        ("SELECT product_id, price, description, image_id FROM products WHERE page_id = '$page_id' AND product_id = '$product_id'"));
                return $result;
        }
	//Insert all information of unregistered user to 'users' table
	public function insertUsers($uuid, $language, $ip_address, $time_zone, $country, $region, $city){
		$statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
			"INSERT INTO users (uuid, language, ip_address, time_zone, country, region, city )
				VALUES ($uuid, '$language', '$ip_address', '$time_zone', '$country', '$region', '$city')"));
	}
	//insert with user agent included and HIGH_PRIORITY=false
	//public function insertUsers($uuid, $language, $ip_address, $time_zone, $country, $region, $city, $user_agent){
          //      $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
            //            "INSERT INTO users
		//		(uuid, language, ip_address, time_zone, country, region, city, user_agent, high_priority )
              //                  VALUES
		//	($uuid, '$language', '$ip_address', '$time_zone', '$country', '$region', '$city', $user_agent, false)"));
        //}

	//Promotes user to HIGH_PRIORITY
	public function promoteUser($uuid){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO users (uuid, high_priority )
                                VALUES ($uuid, true)"));
        }


	//Updates visit counter with user identification. Is used to promote users to HIGH_PRIORITY
	public function updateVisits($uuid, $page_id){
		$result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
			"UPDATE visits SET count = count + 1 WHERE uuid = $uuid AND page_id = '$page_id'"));
		return $result;
	}

	//Return information about how many times user has visited different pages. Returns asa many rows the user has visited pages.
	public function selectVisits($uuid){
		$result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "SELECT count FROM visits WHERE uuid = $uuid"));
                return $result;
	}

	//Updates table 'movements' which tracks clients movements between pages
	public function updateMovements($uuid, $source_page_id, $dest_page_id){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "UPDATE movements SET count = count + 1 
				WHERE uuid = $uuid AND source_page_id = '$source_page_id' AND dest_page_id = '$dest_page_id'"));
                return $result;
        }

	//Select count of all movements between pages for single client.
	public function selectMovements($uuid, $source_page_id, $dest_page_id){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "UPDATE movements SET count = count + 1
                                WHERE uuid = $uuid AND source_page_id = '$source_page_id' AND dest_page_id = '$dest_page_id'"));
                return $result;
        }


}
?>
