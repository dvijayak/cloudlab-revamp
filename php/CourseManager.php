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
            $query = "SELECT DISTINCT Enrollments.course_id AS 'course' FROM Courses, Enrollments WHERE Enrollments.user_id = '" . $user . "';";
			$courses = $this->dbm->queryFetchAssoc($query);
			return $courses;		            
        }		
		
	}
	
?>