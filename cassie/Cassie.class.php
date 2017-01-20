
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

	//Gets all products for single category page with defined priority
	public function getProducts($page_id){
		$result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
			("SELECT product_id, price, description, image_id FROM products 
				WHERE page_id = '$page_id' AND high_priority = true"));
		return $result;
	}

	//Gets all products for single category page with defined priority
        public function getTest($page_id, $os, $priority){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
                        ("SELECT product_id, price, description, image_link FROM products_by_os
                                WHERE category_id = '$page_id' AND os_segment = '$os' AND high_priority_segment = true ALLOW FILTERING"));
                return $result;
        }


	//Gets all info needed to show to client of a product
//	public function getProductInfo($page_id, $product_id){
  //              $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
    //                    ("SELECT product_id, price, description, image_id FROM products 
//				WHERE page_id = '$page_id' AND high_priority = true AND product_id = '$product_id'"));
  //              return $result;
    //    }
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

	//insert with user agent included and HIGH_PRIORITY=false
        public function insertUsersSegment($uuid, $language, $ip_address, $time_zone, $country, $region, $city, $os){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO segment_users
                              (uuid, language, ip_address, time_zone, country, region, city, high_priority_segment, os_segment )
                                VALUES
                      ($uuid, '$language', '$ip_address', '$time_zone', '$country', '$region', '$city', true, '$os')"));
        }

	//Gets all info needed to show to client of a product
        public function getProductInfo($product_id){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
                        ("SELECT product_id, price, description, image_link FROM products_by_product
                                WHERE product_id = '$product_id'"));
                return $result;
        }

	

	//Promotes user to HIGH_PRIORITY
	public function promoteUser($uuid){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO users (uuid, high_priority )
                                VALUES ($uuid, true)"));
        }

	public function getProductByOs($page_id, $os, $product_id){
		$statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "SELECT FROM products_by_os WHERE category_id = '$page_id' AND os_segment = '$os', AND product_id = '$product_id' AND high_priority_segment = false ALLOW FILTERING"));
		return $result;
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

	//Select specific add. Test with $add_id = 'jackets' or $add_id = 'pants'
        public function selectAd($ad_id){
            $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                "SELECT image_link FROM ads WHERE ad_id = '$ad_id'"));
		return $result;
        }
	//Returns sum of visit by a unregistered user.
	public function checkForPromotion($uuid){
            $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                "SELECT count FROM visits WHERE uuid = $uuid"));
            $sum = 0;
            foreach ($result as $row) {
                $count_value = $row['count'];
                $sum = $sum + $count_value;
            }

            return $sum;
        }
	//Select priority value for single user
	public function selectUserPriority($uuid){
		$result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
			"SELECT high_priority WHERE uuid = $uuid"));
		$row = $result->first();
		$boo = $row['high_priority'];
		return $boo;
	}

	public function updateLanguage($uuid, $language){
		$result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
			"INSERT INTO segment_users (uuid, language) VALUES ($uuid, '$language')"));
		return $result;
	}

	public function getProductsByOs($page_id, $os){
		$result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
			"SELECT category_id, os_segment, product_id, description, image_link, price
				FROM products_by_os WHERE category_id = '$page_id' AND os_segment = '$os' AND high_priority_segment = false ALLOW FILTERING"));
		return $result;
	}

}
?>
