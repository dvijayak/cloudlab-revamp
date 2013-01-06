<?php    
    
	error_reporting(-1);				
	
	class CourseManager {
		private $dbm;		
		
		public function __construct ($dbm) {			
			$this->dbm = $dbm;			
		}
		
		
		/**
		 * Create a new course
		 */
		public function createCourse ($id, $name, $description) {
			
		}
		
        /**
         * Retrieve the list of courses based on the specified parameters
         * If a username is provided, list courses for which the user is enrolled in,
         * else, list all courses that exist in the system
         */
        public function getCourses ($username) {
			$user = mysql_real_escape_string($username);
            $query = "SELECT DISTINCT Enrollments.course_id FROM Courses, Enrollments WHERE Enrollments.user_id = '" . $user . "';";                        
            if (($result = $this->dbm->query($query)) == null) {                
                return null;
            }
            else {                
                if (mysql_num_rows($result) == 0) {                    
                    $courses = null;
                }
                // Iterate as long as we have rows in the result set  
                $courses = array();
                while ($row = mysql_fetch_assoc($result)) {                    
                    array_push($courses, $row['course_id']);
                }                
                return $courses;
            }
        }		
		
	}
	
?>