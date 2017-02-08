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

                $segmentTargeting = self::SegmentTargeting(self::getSettings(), $visits, $language);
                return $segmentTargeting;
		
		}

            public function SegmentTargeting($settings, $visits, $language) {
	       //target certain items for user viewing based on standalone parameters
                //if parameter1 == smth, target this item1 for presentation
                //if parameter2 == smth, target this item2 for presentation

		$Segments = array(
                	"priorityHigh" => 0,
			"advertTop" => 1,
              	  	"advertSide" => 1
	            );


                if( $visits >= 5 && $settings["highPriority"]){
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

            public function launchHelper(){
                //result of fast page changing in the webstore
            }
	    
	    public function getSettings(){
		$GLOBALS['cassie'] = new Cassie();
		$GLOBALS['cassie']->connect();
		
		$results = $GLOBALS['cassie']->getSettings();
		$settings = array(
			"highPriority" => false,
			"climate" => false,
			);

		foreach ($results as $row){
			$temp = $row['enabled'];
			//foreach($settings as $k => $v){
			//	$v = $temp;
			//}
			if($row['rule_name'] == "priority"){
				$settings["highPriority"] = $temp;
			}
		}
		return $settings;
	    }
	}


