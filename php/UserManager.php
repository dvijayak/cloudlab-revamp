<?php    
    
    // PHP CONSOLE FOR DEBUGGING PURPOSES: ONLY WORKS WITH THE CHROME BROWSER
    require_once("PhpConsole.php");
    PHPConsole::start();	
	
	error_reporting(-1);				
	
	class UserManager {
		private $dbm;		
		
		public function __construct ($dbm) {			
			$this->dbm = $dbm;			
		}
		
		
		/**
		 * Create a new user
		 */
		public function createUser ($user) {
			$idnum = mysql_real_escape_string($user['idnum']);
			$id = mysql_real_escape_string($user['id']);
			$passhash = mysql_real_escape_string($user['passhash']);
			$fname = mysql_real_escape_string($user['fname']);
			$lname = mysql_real_escape_string($user['lname']);
			$email = mysql_real_escape_string($user['email']);
			$role = mysql_real_escape_string($user['role']);
			
			$query = "INSERT INTO Users VALUES " .
			"('" . $idnum . "', '" . $id . "', '" . $passhash . "', '" . $fname . "', '" . $lname . "', '" . $email . "', " . $role . ");";
			
			$success = $this->dbm->query($query);
			return $success;
		}
		
		/**
		 * Update the existing user
		 */
		public function editUser ($user) {
			$idnum = mysql_real_escape_string($user['idnum']);
			$id = mysql_real_escape_string($user['id']);
			$passhash = mysql_real_escape_string($user['passhash']);
			$fname = mysql_real_escape_string($user['fname']);
			$lname = mysql_real_escape_string($user['lname']);
			$email = mysql_real_escape_string($user['email']);
			$role = mysql_real_escape_string($user['role']);
			
			// Note that the idnum and id cannot be changed
			$query = "UPDATE Users SET user_passhash = '" . $passhash . "'," .
			" user_firstname = '" . $fname . "', user_lastname = '" . $lname . "'," .
			" user_email = '" . $email . "', group_id = " . $role .
			" WHERE user_number = '" . $idnum . "';";
			
			$success = $this->dbm->query($query);
			return $success;			
		}
		
        /**
         * Retrieve the list of users (minus admins) that exist in the system
         */
		public function getUsers () {            
            $query = "SELECT user_number AS 'idnum', user_id AS 'id', user_passhash AS 'passhash'," .
			" user_firstname AS 'fname', user_lastname AS 'lname', user_email AS 'email', group_id as 'role' FROM Users" .
			" WHERE group_id != 3;";
            
			$users = $this->dbm->queryFetchAssoc($query);
			return $users;			
		}
		
		/**
         * Retrieve the list of students that exist in the system
         */
		public function getStudents () {
            $query = "SELECT user_number AS 'idnum', user_id AS 'id', user_passhash AS 'passhash'," .
			" user_firstname AS 'fname', user_lastname AS 'lname', user_email AS 'email', group_id as 'role' FROM Users" .
			" WHERE group_id = 1;";
            
            $students = $this->dbm->queryFetchAssoc($query);
			return $students;	 				
		}
		
		/**
         * Retrieve the list of instructors that exist in the system
         */
		public function getInstructors () {
            $query = "SELECT user_number AS 'idnum', user_id AS 'id', user_passhash AS 'passhash'," .
			" user_firstname AS 'fname', user_lastname AS 'lname', user_email AS 'email', group_id as 'role' FROM Users" .
			" WHERE group_id = 2;";
            
            $instructors = $this->dbm->queryFetchAssoc($query);
			return $instructors;					
		}
		
		/*
		 * Delete the specified user
		 */
		public function deleteUser ($user) {
			$idnum = mysql_real_escape_string($user['idnum']);
			$id = mysql_real_escape_string($user['id']);
			
			$queries = array();
			// Delete all files owned by the user			
			$query = "DELETE FROM Files WHERE file_owner = '" . $id . "';";
			array_push($queries, $query);
			// Withdraw the user from all courses enrolled in
			$query = "DELETE FROM Enrollments WHERE user_id = '" . $id . "';";
			array_push($queries, $query);
			// Delete the user
			$query = "DELETE FROM Users WHERE user_number = '" . $idnum . "';";
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
		
	}
	
?>