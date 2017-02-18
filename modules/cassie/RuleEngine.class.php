<?php
//include drupal_get_path('module', 'cassie').'/Cassie.class.php';



	class RuleEngine {
            //check for amount of visits per item category, duration of stay per page, OS, location and language.
            public $local_Language = "";
            public $Location = "Finland";
            public $local_uuid;
            public $VisitsInWebstore;

            public function init($visits, $os, $location) {

                $segmentTargeting = self::SegmentTargeting(self::getSettings(), $visits, $os, $location);
                return $segmentTargeting;

		}

            public function SegmentTargeting($settings, $visits, $os, $location) {
	       //target certain items for user viewing based on standalone parameters
                //if parameter1 == smth, target this item1 for presentation
                //if parameter2 == smth, target this item2 for presentation


		$Segments = array(
                	"priorityHigh" => 0,
			"advertTop" => 3,
              	  	"advertSide" => 1
	            );

		//-------Visits based----------//		//decides whether the customer is visiting multiple pages and based on number of visits tags high priority for the user

                if ($visits >= 5 && $settings["highPriority"]){
                	$Segments["priorityHigh"] =1;
                }

		//-------OS based--------//		        //decides which advert to show on the side banner based on os, 1 for iPhone, 7 for Windows, 8 for Mac, 6 for iPhone

                if (strpos($os, "iPhone") !== false){
                	$Segments["advertSide"] = 1;
                }else if (strpos($os, "Windows") !== false){
                    	$Segments["advertSide"] = 7;
                }else if (strpos($os, "Mac") !== false){
			$Segments["advertSide"] = 8;
		}else if (strpos($os, "Linux") !== false){
			$Segments["advertSide"] = 6;
		}

		//-------Location based--------//		//decides which advert to show on the top banner based on location, 3 for Finland, 4 for France, 5 for Russia

		if ($location == "Finland" && $settings["country"]){
			$Segments["advertTop"] = 3;
		}
		else if ($location == "France" && $settings["country"]){
			$Segments["advertTop"] = 4;
		}
		else if ($location == "Russia" && $settings["country"]){
			$Segments["advertTop"] = 5;
		}

		//-----------------------------//


		return $Segments;
            }

            public function launchHelper(){
                //result of fast page changing in the webstore
            }

	    public function getSettings(){
		$GLOBALS['cassie'] = new Cassie();
		$GLOBALS['cassie']->connect();

		$results = $GLOBALS['cassie']->getSettings();
		$settings = array(
			"highPriority" => false,
			"country" => false,
			);

		foreach ($results as $row){
			$boolean = $row['enabled'];
			//foreach($settings as $k => $v){
			//	$v = $temp;
			//}
			if($row['rule_name'] == "priority"){
				$settings["highPriority"] = $boolean;
			}
			 if($row['rule_name'] == "country"){
                                $settings["country"] = $boolean;
                        }

		}
		return $settings;
	    }
	}


