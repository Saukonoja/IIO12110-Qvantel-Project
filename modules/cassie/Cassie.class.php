
<?php
class Cassie{

//Declare array of global variables needed for Cassandra connection
	public $GLOBALS = array(
		'cluster' => null,
		'keyspace' => null,
		'session' => null
	);

//Connect to local Cassandra cluster and keyspace 'Drupal'
	public function connect(){
		$GLOBALS['cluster'] = Cassandra::cluster()
				->withContactPoints('172.31.19.110')
                                ->build();
		$GLOBALS['keyspace'] = 'drupal';
                $GLOBALS['session'] = $GLOBALS['cluster']->connect($GLOBALS['keyspace']);
	}

//Updates table 'popularity' that counts visits on specific page
	public function updateCounter($address){
		$statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
			"UPDATE popularity
			  SET counter = counter + 1
			  WHERE address = '$address'"
		));
	}

//get all products for update page
	 public function getProducts($page_id){
                        $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
                                ("SELECT category_id, product_id, country_segment, price, amount, description_fi, description_ru, image_link, high_priority_segment 
                                    FROM products
                                        WHERE category_id = '$page_id'"));
                return $result;
        }

//get all product with category and ru language (returns an array of objects which is easier to sort than result array)
	public function getProductsRu($page_id, $prio){
                $arrayObj = array();
                if ($prio == 1){
                        $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
                                ("SELECT category_id, product_id, country_segment, price, amount, description_ru, image_link, high_priority_segment FROM products
                                        WHERE category_id = '$page_id'"));
                        foreach($result as $row){
                                $product = new Product($row['category_id'], $row['product_id'], $row['price'], $row['image_link'], $row['description_ru'], $row['amount'], $row['high_priority_segment'], $row['country_segment']);
                                $arrayObj[] = $product;
                        }
                }else {
                        $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
                                ("SELECT category_id, product_id, country_segment, price, amount, description_ru, image_link, high_priority_segment FROM products
                                        WHERE category_id = '$page_id' AND high_priority_segment = false"));
                        foreach($result as $row){
                                $product = new Product($row['category_id'], $row['product_id'], $row['price'], $row['image_link'], $row['description_ru'], $row['amount'], $row['high_priority_segment'], $row['country_segment']);
                                $arrayObj[] = $product;
                        }
                }
                return $arrayObj;
        }

//get all product with category and fi language (returns an array of objects which is easier to sort than result array)
    public function getProductsFi($page_id, $prio){
            $arrayObj = array();
            if ($prio == 1){
                    $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
                            ("SELECT category_id, product_id, country_segment, price, amount, description_fi, image_link, high_priority_segment FROM products
                                    WHERE category_id = '$page_id'"));
                    foreach($result as $row){
                            $product = new Product($row['category_id'], $row['product_id'], $row['price'], $row['image_link'], $row['description_fi'], $row['amount'], $row['high_priority_segment'], $row['country_segment']);
                            $arrayObj[] = $product;
                    }
            }else {
                    $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
                            ("SELECT category_id, product_id, country_segment, price, amount, description_fi, image_link, high_priority_segment FROM products
                                    WHERE category_id = '$page_id' AND high_priority_segment = false"));
                    foreach($result as $row){
                            $product = new Product($row['category_id'], $row['product_id'], $row['price'], $row['image_link'], $row['description_fi'], $row['amount'], $row['high_priority_segment'], $row['country_segment']);
                            $arrayObj[] = $product;
                    }
            }
            return $arrayObj;
    }

//Gets all distinct categories
    public function getCategories(){
            $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
                    ("SELECT DISTINCT category_id FROM products"));
            return $result;
    }

//insert with user agent included and HIGH_PRIORITY=false
    public function insertUsersSegment($uuid, $language, $ip_address, $time_zone, $country, $region, $city, $os){
            $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                    "INSERT INTO segment_users
                          (uuid, language, ip_address, time_zone, country, region, city, high_priority_segment, os_segment )
                            VALUES
                  ($uuid, '$language', '$ip_address', '$time_zone', '$country', '$region', '$city', false, '$os')"));
    }

//Gets all info needed to show to client of a product
    public function getProductInfo($category_id, $product_id){
            $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement
                    ("SELECT product_id, price, description_fi, description_ru, image_link FROM products
                            WHERE category_id = '$category_id' AND product_id = '$product_id'"));
            return $result;
    }


//Promotes user to HIGH_PRIORITY
	public function promoteUser($uuid){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO segment_users (uuid, high_priority_segment )
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

//update language for single user
	public function updateLanguage($uuid, $language){
		$result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
			"INSERT INTO segment_users (uuid, language) VALUES ($uuid, '$language')"));
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

//insert product to registered_users shopping cart
    public function cartRegisteredProduct($username, $product_id, $price, $image_link){
         $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                    "SELECT amount FROM registered_users_cart WHERE username = '$username' AND product_id = '$product_id'"));

        $amount = 1;
            if($result->first() !== null){
                    $row = $result->first();
                $amount = $row['amount'];
                $amount = $amount + 1;
            }

            $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                    "INSERT INTO registered_users_cart (username, product_id, amount, price, image_link) VALUES ('$username', '$product_id', $amount, $price, '$image_link' )"));
    }

//select all products registered user has in cart
        public function getRegisteredCartProducts($username){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "SELECT product_id, amount, price, image_link FROM registered_users_cart WHERE username = '$username'"));
                return $result;
        }

//update products checkout when paid
	 public function updateCheckout($uuid, $product_id, $amount, $price, $image_link){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO users_purchases (uuid, timeuuid, product_id, amount, price, image_link) VALUES ($uuid, now(), '$product_id', $amount, $price, '$image_link')"));
		self::deleteCheckout($uuid, $product_id);
        }

//update products checkout when paid
         public function updateRegisteredCheckout($username, $product_id, $amount, $price, $image_link){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO registered_users_purchases (username, timeuuid, product_id, amount, price, image_link) VALUES ('$username', now(), '$product_id', $amount, $price, '$image_link')"));
                self::deleteRegisteredCheckout($username, $product_id);
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
//remove product from registeredcart
    public function removeRegisteredProduct($username, $product_id){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "SELECT amount FROM registered_users_cart WHERE username = '$username' AND product_id = '$product_id'"));
                $row = $result->first();
                $amount = $row['amount'];
                if($amount > 1){
                        $amount = $amount - 1;
                        $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO registered_users_cart (username, product_id, amount) VALUES ('$username', '$product_id', $amount)"));
                }else{
                        $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "DELETE FROM registered_users_cart WHERE username = '$username' AND product_id = '$product_id'"));
                }
        }


//update products checkout when paid
    public function deleteCheckout($uuid, $product_id){
            $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                    "DELETE FROM users_cart WHERE uuid = $uuid AND product_id = '$product_id'"));
    }

//update products checkout when paid
    public function deleteRegisteredCheckout($username, $product_id){
            $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                    "DELETE FROM registered_users_cart WHERE username = '$username' AND product_id = '$product_id'"));
    }


//add new product to database
    public function addProduct($category_id, $product_id, $price, $amount, $description_fi, $description_ru, $image_link, $prio, $country_segment){
            if ($prio == 1){
                    $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                            "INSERT INTO products
                               (category_id, product_id, country_segment, price, amount, description_fi, description_ru, image_link, high_priority_segment)
                                 VALUES ('$category_id', '$product_id', '$country_segment', $price, $amount, '$description_fi', '$description_ru', '$image_link', true)"));
            }else{
                     $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                            "INSERT INTO products
                               (category_id, product_id, country_segment, price, amount, description_fi, description_ru, image_link, high_priority_segment)
                                 VALUES ('$category_id', '$product_id', '$country_segment', $price, $amount, '$description_fi', '$description_ru', '$image_link', false)"));
            }
    }

//delete product from database
     public function deleteProduct($category_id, $product_id){
            $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                    "DELETE FROM products WHERE category_id = '$category_id' AND product_id = '$product_id'"));
    }

//shows as update to user but in fact removes old product and creates new one with new info
	public function updateProduct($category_id1, $product_id1, $category_id, $product_id, $price, $amount, $description_fi, $description_ru, $image_link, $prio, $country){
		self::deleteProduct($category_id1, $product_id1);
		self::addProduct($category_id, $product_id, $price, $amount, $description_fi, $description_ru, $image_link, $prio, $country);
	}

//Enable specific rule
	public function enableRule($rule_name){
                $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "INSERT INTO rule_settings (rule_name, enabled) VALUES ('$rule_name', true)"));
    }

//Disable specific rule
    public function disableRule($rule_name){
            $statement = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                    "INSERT INTO rule_settings (rule_name, enabled) VALUES ('$rule_name', false)"));
    }

//Get state of specific rule
    public function getSettings(){
            $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                    "SELECT * FROM rule_settings"));
            return $result;
    }
//Returns true if rule is enabled
	public function getRule($rule_name){
                $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                        "SELECT * FROM rule_settings WHERE rule_name = '$rule_name'"));
		$row = $result->first();
                $boo = $row['enabled'];
		if ($boo == true){
			return 1;
		}else{
			return 0;
		}

    }

//Get country of specific user
    public function getUserCountry($uuid){
            $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                    "SELECT country FROM segment_users WHERE uuid = $uuid"));
            $row = $result->first();
	$country = $row['country'];
	return $country;
    }

//Get contetns of specific users user agent
    public function getUserAgent($uuid){
            $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                    "SELECT os_segment FROM segment_users WHERE uuid = $uuid"));
            $row = $result->first();
            $country = $row['os_segment'];
            return $country;
    }

//Get contetns of specific users user agent
    public function getAmount($uuid){
            $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                    "SELECT COUNT(*) FROM users_cart WHERE uuid = $uuid"));
            $row = $result->first();
            $count = $row['count'];
            return $count;
    }

//Get contents of specific users user agent
   public function getRegisteredAmount($username){
            $result = $GLOBALS['session']->execute(new Cassandra\SimpleStatement(
                    "SELECT COUNT(*) FROM registered_users_cart WHERE username = '$username'"));
            $row = $result->first();
            $count = $row['count'];
            return $count;
    }
}
?>
