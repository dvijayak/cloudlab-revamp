<?php

    require_once("../lib/aws/sdk.class.php");
    
	error_reporting(-1);		
	
	// ATTENTION: MAKE SURE TO CREATE THE PRIVATE CLASSES FOR COURSE, FILE, PROJECT, USER AND GROUP OBJECTS
	
	class CourseManager {
	
		private $ec2;	
		public $instance;		
		
		function __construct ($id = "i-57d8d32b") {
			$this->ec2 = new AmazonEC2();			
			$this->instance = $this->getInstanceDetails($id);
		}
		
		/**
		 * Obtain useful details of the ec2 instance running this server
		 */
		private function getInstanceDetails ($id) {
			// Describe the running instance
			$response = $this->ec2->describe_instances(array(
				"InstanceId" => $id
			));
			
			if ($response->isOK()) {
				$instance = array(
					"id" => $id,
					// Always cast the desired CFSimpleXML object to string in order to receive a workable object
					"availabilityZone" => (string)$response->body->reservationSet->item->instancesSet->item->placement->availabilityZone
				);
				
				return $instance;			
			}
			
			return null;
		}			
		
		/**
		 * Map of all possible combinations of device names permitted by AWS EBS
		 */
		private static $deviceNames = array(
			"f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p"
		);
		//private static $deviceNames = array(
		//	"f" => array("", "1", "2", "3", "4", "5", "6"),
		//	"g" => array("", "1", "2", "3", "4", "5", "6"),
		//	"h" => array("", "1", "2", "3", "4", "5", "6"),
		//	"i" => array("", "1", "2", "3", "4", "5", "6"),
		//	"j" => array("", "1", "2", "3", "4", "5", "6"),
		//	"k" => array("", "1", "2", "3", "4", "5", "6"),
		//	"l" => array("", "1", "2", "3", "4", "5", "6"),
		//	"m" => array("", "1", "2", "3", "4", "5", "6"),
		//	"n" => array("", "1", "2", "3", "4", "5", "6"),
		//	"o" => array("", "1", "2", "3", "4", "5", "6"),
		//	"p" => array("", "1", "2", "3", "4", "5", "6")
		//);		
		
		/**
		 * Create a new course
		 */
		function createCourse ($courseID, $courseName, $courseDesc) {
			echo "Creating a new volume...\n";
			// Create a new volume
			$response = $this->ec2->create_volume($this->instance["availabilityZone"], array(
				"Size" => 1
			));			
			if ($response->isOK()) {
				echo "Volume created.\n\n";
				
				// Attach volume to ec2 instance
				echo "Attaching volume to instance...\n";
				$volumeId = (string)$response->body->volumeId;				
				$countI = count(CourseManager::$deviceNames);				
				$countJ = count(CourseManager::$deviceNames[0]);
				//$keys = array_keys(CourseManager::$deviceNames);
				//$countJ = count(CourseManager::$deviceNames[$keys[0]]);				
				// Find an available device name through trial and error
				// TODO: HIGHLY inefficient approach
				for ($i = 0; $i < $countI; $i++) {
					$device = "/dev/sd" . CourseManager::$deviceNames[$i];
					$response = $this->ec2->attach_volume($volumeId, $this->instance["id"], $device);
					if ($response->isOK()) {
						echo "Volume attached.\n\n";
						break; // PHP construct for breaking out of multiple nested structures (neat)
					}
					// If the device is already in use, try another device name
					else if (stripos((string)$response->body->Errors->Error->Message, $device . " is already in use") !== false) {
						echo $device . " is already in use. Trying another device...\n";
						continue;
					}
					else {
						echo "Request failed: Volume was not attached!\n\n";
						print_r($response->body);
						break;
					}					
				}				
				//for ($i = 0; $i < $countI; $i++) {
				//	for ($j = 0; $j < $countJ; $j++) {
				//		$device = "/dev/sd" . $keys[$i] . CourseManager::$deviceNames[$keys[$i]][$j];
				//		$response = $this->ec2->attach_volume($volumeId, $this->instance["id"], $device);
				//		if ($response->isOK()) {
				//			echo "Volume attached.\n\n";
				//			break 2; // PHP construct for breaking out of multiple nested structures (neat)
				//		}
				//		// If the device is already in use, try another device name
				//		else if (stripos((string)$response->body->Errors->Error->Message, $device . " is already in use") !== false) {
				//			echo $device . " is already in use. Trying another device...\n";
				//			continue;
				//		}
				//		else {
				//			echo "Request failed: Volume was not attached!\n\n";
				//			print_r($response->body);
				//			break 2;
				//		}
				//	}
				//}
													
				
				
				// Create a course object
				$course = array(
					"id" => $courseID,
					"name" => $courseName,
					"description" => $courseDesc
				);								
				
				// Add course to DB							
				
				//$response = $this->ec2->attach_volume
			}
			else
				echo "Request failed: Volume was not created!\n\n";
		}
		
		/**
		 * List the ids of all volumes attached to the instance
		 */
		function getAllAttachedVolumes () {			
			$response = $this->ec2->describe_volumes(array(
				"Filter" => array(
					array("Name" => "attachment.instance-id", "Value" => $this->instance["id"])
				)
			));			
			if ($response->isOK()) {				
				$volumeIds = $response->body->volumeId()->map_string();
				return $volumeIds;				
			}			
			return null;
		}
		
		/**
		 * List the ids of all volumes in the instance's availability zone		 
		 */
		function getAllZoneVolumes () {			
			$response = $this->ec2->describe_volumes(array(
				"Filter" => array(
					array("Name" => "availability-zone", "Value" => $this->instance["availabilityZone"])
				)
			));			
			if ($response->isOK()) {								
				$volumeIds = $response->body->volumeId()->map_string();
				return $volumeIds;								
			}			
			return null;
		}
		
		/**
		 * List the ids of all volumes in the instance's availability zone that are available		 
		 */
		function getAllAvailableVolumes () {			
			$response = $this->ec2->describe_volumes(array(
				"Filter" => array(
					array("Name" => "status", "Value" => "available")
				)
			));			
			if ($response->isOK()) {								
				$volumeIds = $response->body->volumeId()->map_string();
				return $volumeIds;								
			}			
			return null;
		}		
		
		/**
		 * Detach all volumes specified by the input ids
		 */
		function detachVolumes ($volumeIds) {
			echo "Eventually detaching all specified volumes...\n";
			foreach ($volumeIds as $volume) {				
				$response = $this->ec2->detach_volume($volume);
				if ($response->isOK()) {
					echo "\tEventually detaching this particular volume...\n";
				}
				else
					echo "\tRequest failed: Volume(s) was(were) not detached!\n";
			}
			echo PHP_EOL;
		}
		
		/**
		 * Delete all volumes specified by the input ids
		 */
		function deleteVolumes ($volumeIds) {
			echo "Eventually deleting all specified volumes...\n";
			foreach ($volumeIds as $volume) {				
				$response = $this->ec2->delete_volume($volume);
				if ($response->isOK()) {
					echo "\tEventually deleting this particular volume...\n";
				}
				else
					echo "\tRequest failed: Volume(s) was(were) not deleted!\n";
			}
			echo PHP_EOL;
		}
		
	}

	/////////////////
	// TEST SCRIPT //
	/////////////////
	
	$cm = new CourseManager();
	
	// Create volumes
	//$cm->createCourse("cis3760", "Software Engineering", "A course on Software Engineering principles.");
	//$cm->createCourse("cis4410", "Software Engineering", "A course on Software Engineering principles.");
	//$cm->createCourse("cis2750", "Software Engineering", "A course on Software Engineering principles.");
	//$cm->createCourse("cis3110", "Software Engineering", "A course on Software Engineering principles.");
	//$cm->createCourse("cis3750", "Software Engineering", "A course on Software Engineering principles.");
	
	// List volumes
	print_r($cm->getAllAttachedVolumes());
	//print_r($cm->getAllZoneVolumes());
	//print_r($cm->getAllAvailableVolumes());
	
	// Detach + delete volumes 
	//$cm->detachVolumes($cm->getAllAttachedVolumes());	
	//$cm->deleteVolumes($cm->getAllAvailableVolumes());
	
?>