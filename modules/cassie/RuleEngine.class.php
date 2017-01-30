<?php
//include drupal_get_path('module', 'cassie').'/Cassie.class.php';



	class RuleEngine {
            //check for amount of visits per item category, duration of stay per page, OS, location and language.
            public $local_Language = "";
            public $Location = "Finland";
            public $OS = "";
            public $local_uuid;
            public $VisitsInWebstore;


            public function init($visits, $language) {

                $segmentTargeting = self::SegmentTargeting($visits, $language);
                return $segmentTargeting;
            }

            public function SegmentTargeting($visits, $language) {
	       //target certain items for user viewing based on standalone parameters
                //if parameter1 == smth, target this item1 for presentation
                //if parameter2 == smth, target this item2 for presentation

		$Segments = array(
                	"priorityHigh" => 0,
                	"slavic" => 0,
			"advertTop" => 1,
              	  	"advertSide" => 1
	            );


                if( $visits >= 5){
                    $Segments["priorityHigh"] =1;
                }

                if($language == "fi"){   //Decides which advert to show, 1 for finnish, 2 for ruskiy
                	$Segments["advertSide"] = 1;
			$Segments["advertTop"] = 1;
                }
                else if($language == "ru"){
                    	$Segments["advertSide"] = 2;
			$Segments["advertTop"] = 2;
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


