
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
				->withContactPoints('172.31.19.110', '172.31.48.120')
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

	public function testProduct(){
		$newProduct = new Product("test", "test", 10.5, "test", "test", 10);
	//	$newProduct = __construct("test", "test", 10.5, "test", "test", 10);
		$statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO products
                           (category_id, product_id, price, amount, description, image_link, high_priority_segment)
                             VALUES ('$newProduct->category_id', '$newProduct->product_id', $newProduct->price, $newProduct->amount, '$newProduct->description', '$newProduct->image_link', false)"));

	}
	 //Gets all distinct categories
        public function getCategories(){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
                        ("SELECT DISTINCT category_id FROM products"));
                return $result;
        }


	//Gets all products for single category page with defined priority
	public function getProducts($page_id){
		$result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
			("SELECT category_id, product_id, price, amount, description, image_link FROM products 
				WHERE category_id = '$page_id' AND high_priority_segment = false ALLOW FILTERING"));
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
        public function getProductInfoTest($product_id){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
                        ("SELECT product_id, price, description, image_link FROM products_by_product
                                WHERE product_id = '$product_id'"));
                return $result;
        }

	//Gets all info needed to show to client of a product
        public function getProductInfo($product_id){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
                        ("SELECT product_id, price, description, image_link FROM products
                                WHERE product_id = '$product_id' ALLOW FILTERING"));
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
	//insert values from registration form
	public function registerUser($username, $password, $firstname, $lastname, $email, $age){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO registered_segment_users 
				(username, password, firstname, lastname, email, age_segment)
				VALUES ('$username', '$password', '$firstname', '$lastname', '$email', $age)"));
        }
	//insert values from user_agent to registered user
	public function insertRegisteredInfo($username, $language, $ip_address, $time_zone, $country, $region, $city){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO registered_segment_users
                                (username, language, ip_address, time_zone, country, region, city)
                                VALUES ('$username', '$language', '$ip_address', '$time_zone', '$country', '$region', '$city')"));
        }
	//update registered user to high_priority
	 public function updateRegisteredPriority($username, $high_priority){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO registered_segment_users
                                (username, high_priority_segment)
                                VALUES ('$username', $high_priority)"));
        }
	//update registered user wealth_segment
	 public function insertRegisteredWealth($username, $wealth){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO registered_segment_users
                                (username, wealth_segment)
                                VALUES ('$username', '$wealth')"));
        }
	//insert registered climate_segments
	public function insertRegisteredClimate($username, $climate){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO registered_segment_users
                                (username, climate_segment)
                                VALUES ('$username', '$climate')"));
        }

	 //insert values from registration form
        public function testRegister($username, $password){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO registered_segment_users (username, password) VALUES ('$username', '$password')"));
        }
	//Check username password correct
	 public function testLogin($username, $password){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "SELECT username FROM registered_segment_users 
				WHERE username = '$username' AND password = '$password' ALLOW FILTERING"));
		$boo = 0;
		if($result->first() !== null){
			$boo = 1;
		}
		return $boo;
        }
/*	//Insert product into users cart
	 public function cartProduct($uuid, $product_id, $price){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "UPDATE users_cart SET amount = amount + 1 WHERE uuid = $uuid AND product_id = '$product_id' AND price = $price"));
        }
	//Select all contents of single users cart
	public function getCartContents($uuid){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "SELECT * FROM users_cart WHERE uuid = $uuid"));
		return $result;
        }
	//Insert product into registered users cart
        public function cartRegisteredProduct($username, $product_id, $price){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "UPDATE reg_users_cart SET amount = amount + 1 WHERE username = '$username' AND product_id = '$product_id' AND price = $price"));
        }
	 //Select all contents of single users cart
        public function getRegisteredCartContents($username){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "SELECT * FROM reg_users_cart WHERE username = $username"));
                return $result;
        }
*/
	 //insert product to users shopping cart
        public function cartProduct($uuid, $product_id, $price, $image_link){
		 $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "SELECT amount FROM users_cart WHERE uuid = $uuid AND product_id = '$product_id'"));

		$amount = 1;
                if($result->first() !== null){
                        $row = $result->first();
			$amount = $row['amount'];
			$amount = $amount + 1;
                }

                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO users_cart (uuid, product_id, amount, price, image_link) VALUES ($uuid, '$product_id', $amount, $price, '$image_link' )"));
        }

	//select all products user has in cart
	public function getCartProducts($uuid){
		$result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
			"SELECT product_id, amount, price, image_link FROM users_cart WHERE uuid = $uuid"));
		return $result;
	}
	/*//select all old purchases
	public function getCartPdsaaroducts($uuid){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "SELECT product_id, amount, price FROM users_cart WHERE uuid = $uuid AND checkout = true"));
                return $result;
        }*/
	//update products checkout when paid
	 public function updateCheckout($uuid, $product_id, $amount, $price, $image_link){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO users_purchases (uuid, timeuuid, product_id, amount, price, image_link) VALUES ($uuid, now(), '$product_id', $amount, $price, '$image_link')"));
		self::deleteCheckout($uuid, $product_id);
        }
	//remove product from cart
	public function removeProduct($uuid, $product_id){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "SELECT amount FROM users_cart WHERE uuid = $uuid AND product_id = '$product_id'"));
		$row = $result->first();
		$amount = $row['amount'];
                if($amount > 1){
			$amount = $amount - 1;
			$statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO users_cart (uuid, product_id, amount) VALUES ($uuid, '$product_id', $amount)"));
		}else{
			$statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
			"DELETE FROM users_cart WHERE uuid = $uuid AND product_id = '$product_id'"));
		}
        }
	 //update products checkout when paid
         public function deleteCheckout($uuid, $product_id){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "DELETE FROM users_cart WHERE uuid = $uuid AND product_id = '$product_id'"));
        }


	 //add new product to database
         public function addProduct($category_id, $product_id, $price, $amount, $description, $image_link){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO products 
			   (category_id, product_id, price, amount, description, image_link, high_priority_segment)
			     VALUES ('$category_id', '$product_id', $price, $amount, '$description', '$image_link', false)"));
        }


	//delete product from database
         public function deleteProduct($category_id, $product_id){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "DELETE FROM products WHERE category_id = '$category_id' AND product_id = '$product_id'"));
        }


	 //update product in database
         /*public function deleteProduct($category_id, $product_id){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "U FROM products WHERE category_id = '$category_id' AND product_id = '$product_id$
        }*/

	public function updateProduct($category_id1, $product_id1, $category_id, $product_id, $price, $amount, $description, $image_link){
		self::deleteProduct($category_id1, $product_id1);
		self::addProduct($category_id, $product_id, $price, $amount, $description, $image_link);
	}

}
?>
