<?php
//include drupal_get_path('module', 'cassie').'/Cassie.class.php';



	class RuleEngine {
            //check for amount of visits per item category, duration of stay per page, OS, location and language.
            public $local_Language = "";
            public $Location = "Finland";
            public $OS = "";
            public $local_uuid;
            public $VisitsInWebstore;
            /**global $Segments = [
                "priorityLow" => 1,
                "priorityHigh" => 0,
                "slavic" => 0,
                "advert" => 0
            ];*/




	    public function getProducts($os){

	    }

            public function init($visits, $language) {
		//contact Cassandra db, read user profile and behaviour data and store it
                //call cassie
                //set local params

//                $GLOBALS['cassie'] = new Cassie();
  //              $GLOBALS['cassie']->connect();

       //         global $local_uuid, $local_Language, $VisitsInWebstore;

           //     $local_uuid = $uuid;
//                $local_Language = $Language;
    //            $VisitsInWebstore = $GLOBALS['cassie']->checkForPromotion($uuid);

                $segmentTargeting = self::SegmentTargeting($visits, $language);
                return $segmentTargeting;
            }

            public function SegmentTargeting($visits, $language) {
	       //target certain items for user viewing based on standalone parameters
                //if parameter1 == smth, target this item1 for presentation
                //if parameter2 == smth, target this item2 for presentation
		$Segments = array(
        	        "priorityLow" => 1,
                	"priorityHigh" => 0,
                	"slavic" => 0,
              	  	"advert" => 1
	            );

     //           global $VisitsInWebstore, $Segments, $local_Language;

                if( $visits >= 5){
                    $Segments["priorityHigh"] =1;
                } else {
                    //do nothing
                }

                if($language == "fi"){   //Decides which advert to show, 1 for finnish, 2 for ruskiy
                    $Segments["advert"] = 1;
                }
                else if($language == "ru"){
                    $Segments["advert"] = 2;
                }
		return $Segments;
            }

            public function trackBrowsingSpeed(){
                //options for tracking browsing times on a certain page
                //and multiple quick changes of page
            }

            public function launchHelper(){
                //result of fast page changing in the webstore
            }
	}


