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
		public function createCourse ($course) {
			$id = mysql_real_escape_string($course['id']);
			$name = mysql_real_escape_string($course['name']);
			$description = mysql_real_escape_string($course['description']);
			
			$query = "INSERT INTO Courses VALUES " .
			"('" . $id . "', '" . $name . "', '" . $description . "');";
			
			$success = $this->dbm->query($query);
			return $success;
		}
		
		/**
		 * Update the details of the specified course
		 */
		public function editCourse ($course) {
			$id = mysql_real_escape_string($course['id']);
			$name = mysql_real_escape_string($course['name']);
			$description = mysql_real_escape_string($course['description']);
			
			// Note that the id cannot be changed						
			$query = "UPDATE Courses SET course_name = '" . $name . "', course_desc = '" . $description . "';'";			
			
			$success = $this->dbm->query($query);
			return $success;			
		}		

		/**
		 * Delete the specified course
		 */
		public function deleteCourse ($course) {
			$id = mysql_real_escape_string($course['id']);			
			
			$queries = array();
			// Delete all files belonging to projects under the course
			$query = "DELETE Files.* FROM Files LEFT JOIN Projects ON Projects.project_id = Files.project_id WHERE Projects.course_id = '" . $id . "';";
			array_push($queries, $query);			
			// Delete all projects under the course
			$query = "DELETE FROM Projects WHERE course_id = '" . $id . "';";
			array_push($queries, $query);			
			// Withdraw all users from the course
			$query = "DELETE FROM Enrollments WHERE course_id = '" . $id . "';";
			array_push($queries, $query);			
			// Delete the course
			$query = "DELETE FROM Courses WHERE course_id = '" . $course . "';";
			array_push($queries, $query);
			
			$results = $this->dbm->queryChain($queries);
			$success = true;
			foreach ($results as $result) {				
				$success = $result;
				if (!$success) {
					break;
				}
			}									
			return $success;			
		}
		
        /**
         * Retrieve the list of courses based on the specified parameters
         * If a username is provided, list courses for which the user is enrolled in,
         * else, list all courses that exist in the system
         */
        public function getCourses ($username=NULL) {
			if (!empty($username)) {
				$user = mysql_real_escape_string($username);
				$query = "SELECT DISTINCT Enrollments.course_id AS 'course' FROM Courses, Enrollments WHERE Enrollments.user_id = '" . $user . "';";				
			}
			else {
				$query = "SELECT Courses.course_id AS 'id', Courses.course_name AS 'name', COUNT(Enrollments.user_id) AS 'heads'," .
				" Courses.course_desc AS 'description' FROM Enrollments, Courses" .
				" WHERE Enrollments.course_id = Courses.course_id GROUP BY Courses.course_id;";
			}
			$courses = $this->dbm->queryFetchAssoc($query);	
			return $courses;		            
        }
		
		
	}
	
?>