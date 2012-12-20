<?php    
    
	error_reporting(-1);				
	
	class CourseManager {
		private $dbm;		
		
		function __construct ($dbm) {			
			$this->dbm = $dbm;			
		}
		
		
		/**
		 * Create a new course
		 */
		function createCourse ($id, $name, $description) {
			
		}	
		
        /**
         * Retrieve the list of courses that the user is enrolled in
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