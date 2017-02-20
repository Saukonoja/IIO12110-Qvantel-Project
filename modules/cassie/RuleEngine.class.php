
<?php
//include drupal_get_path('module', 'cassie').'/Cassie.class.php';



	class RuleEngine {
            //check for amount of visits per item category, duration of stay per page, OS, location and language.
            public $local_Language = "";
            public $Location = "Finland";
            public $local_uuid;
            public $VisitsInWebstore;

            public function init($visits, $os, $location, $language) {

                $segmentTargeting = self::SegmentTargeting(self::getSettings(), $visits, $os, $location, $language);
                return $segmentTargeting;

		}

            public function SegmentTargeting($settings, $visits, $os, $location, $language) {
	       							//target certain items or adverts for user viewing based on customer and ui-end parameters


		$Segments = array(				//default values before parameter based targeting
                	"priorityHigh" => 0,
			"advertTop" => 3,
              	  	"advertSide" => 1,
			"highPrioByCountry" => 0,
			"languageAndCountry" => 0
	            );

		//-------Visits based----------//		//decides whether the customer is visiting multiple pages and based on number of visits tags high priority for the user

                if ($visits >= 5 && $settings["highPriority"]){
                	$Segments["priorityHigh"] = 1;
                }

		//-------OS based--------//		        //decides which advert to show on the side banner based on os, 1 for iPhone, 7 for Windows, 8 for Mac, 6 for iPhone

		if($settings["os"]){
                	if (strpos($os, "iPhone") !== false){
                		$Segments["advertSide"] = 1;
                	}else if (strpos($os, "Windows") !== false){
                    		$Segments["advertSide"] = 7;
                	}else if (strpos($os, "Mac") !== false){
				$Segments["advertSide"] = 8;
			}else if (strpos($os, "Linux") !== false){
				$Segments["advertSide"] = 6;
			}
		}

		//-------Location based--------//		//decides which advert to show on the top banner based on location, 3 for Finland, 4 for France, 5 for Russia

		if($settings["location"]){
			if ($location == "Finland"){
				$Segments["advertTop"] = 3;
			}
			else if ($location == "France"){
				$Segments["advertTop"] = 4;
			}
			else if ($location == "Russia"){
				$Segments["advertTop"] = 5;
			}
		}

		//-------HighPrio and Location based--------//	//combination segment for high priority and location

		if($settings["priorityAndLocation"]){
			if($location == "Finland" && $Segments["priorityHigh"] == 1){
				$Segments["highPrioByCountry"] = 1;
			}
			else if($location == "Russia" && $Segments["priorityHigh"] == 1){
				$Segments["highPrioByCountry"] = 2;
			}
			else {
				$Segments["highPrioByCountry"] = 0;
			}
		}

		//-------Language and Location based--------//	//combination segment for location and language

		if($settings["languageAndLocation"]){
			if($location == "Finland" && $language == "fi"){
				$Segments["languageAndCountry"] = 1;
			}
			else if($location == "Russia" && $language == "ru"){
				$Segments["languageAndCountry"] = 2;
			}
			else {
				$Segments["languageAndCountry"] = 0;
			}
		}

		//------------------------------------------//

		return $Segments;
            }

            public function launchHelper(){
                //result of fast page changing in the webstore
		//javascript and php functions done and working on normal test environment, found unsuitable for use on drupal.
            }

	//-------Get Settings--------//		//get rules from cassandra, to determine which rules are enabled and which disabled from the rule-engine interface on admin panel.

	public function getSettings(){
		$GLOBALS['cassie'] = new Cassie();
		$GLOBALS['cassie']->connect();

		$results = $GLOBALS['cassie']->getSettings();
		$settings = array(
			"highPriority" => false,
			"location" => false,
			"os" => false,
			"priorityAndLocation" => false,
			"languageAndLocation" => false
			);

		foreach ($results as $row){
			$boolean = $row['enabled'];

			if($row['rule_name'] == "priority"){
				$settings["highPriority"] = $boolean;
			}
			if($row['rule_name'] == "country"){
                		$settings["location"] = $boolean;
            		}
			if($row['rule_name'] == "os"){
				$settings["os"] = $boolean;
			}
			if($row['rule_name'] == "priorityAndLocation"){
				$settings["priorityAndLocation"] = $boolean;
			}
			if($row['rule_name'] == "languageAndLocation"){
				$settings["priorityAndLocation"] = $boolean;
			}

		}
		return $settings;
	    }
	}


