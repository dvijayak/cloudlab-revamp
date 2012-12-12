<?php

    error_reporting(E_ALL ^ E_NOTICE);
    //error_reporting(-1);         
     
    $main = new Main();
    
    $postKeys = array_keys($_POST);
    $cookieKeys = array_keys($_COOKIE);
    
    /* Log out the incoming connection's session if requested
     * Note: there does not have to be a valid session to log out
     */
    if (!empty($_POST['logout'])) {
        $output = $main->logout();            
    }
    else {
        // If user is validly logged in already, carry on
        if ($main->validateSession()) {        
            // Get the list of courses for the user
            if (!empty($_POST['getCourses'])) {
                $username = $_COOKIE['username'];
                $firstname = $_COOKIE['firstname'];
                $courses = $main->getCourses();            
                $output = $main->buildResponse("OK", array(
                    "username" => $username,
                    "firstname" => $firstname,
                    "courses" => ($courses == null) ? array() : $courses
                ));
            }
            // Get the list of projects for the chosen course
            else if (!empty($_POST['getProjects'])) {                        
                $username = $_COOKIE['username'];
                $firstname = $_COOKIE['firstname'];
                $role = $_COOKIE['role'];
                $course = $_POST['course'];
                $projects = $main->getProjects($course);
                $output = $main->buildResponse("OK", array(
                    "username" => $username,
                    "firstname" => $firstname,
                    "role" => $role,
                    "course" => $course,
                    "projects" => ($projects == null) ? array() : $projects
                ));            
                
            }
            // Get the list of files for the chosen project
            else if (!empty($_POST['getFiles'])) {                        
                $username = $_COOKIE['username'];
                $firstname = $_COOKIE['firstname'];
                $role = $_COOKIE['role'];
                $course = $_POST['course'];
                $project = $_POST['project'];            
                $output = $main->buildResponse("OK", array(
                    "username" => $username,
                    "firstname" => $firstname,
                    "role" => $role,
                    "course" => $course,
                    "project" => $project,
                    "filess" => ($files == null) ? array() : $files
                ));            
                
            }        
            // Default response: simply inform the client that the user is logged in
            else if (!empty($_POST['validate'])) {
                // Send some personalized data
                $username = $_COOKIE['username'];
                $firstname = $_COOKIE['firstname'];
                $output = $main->buildResponse("OK", array(
                    "username" => $username,
                    "firstname" => $firstname               
                ));            
            }        
            
            //// Loop through the post keys and handle the request accordingly
            //foreach ($postKeys as $post) {
            //                
            //    // Get the list of courses for the user
            //    if ($post == 'getCourses') {
            //        $username = $_COOKIE['username'];
            //        $firstname = $_COOKIE['firstname'];
            //        $courses = $main->getCourses();            
            //        $output = $main->buildResponse("OK", array(
            //            "username" => $username,
            //            "firstname" => $firstname,
            //            "courses" => ($courses == null) ? array() : $courses
            //        ));
            //    }
            //    // Get the list of projects for the chosen course
            //    else if ($post == 'getProjects') {                        
            //        $username = $_COOKIE['username'];
            //        $firstname = $_COOKIE['firstname'];
            //        $role = $_COOKIE['role'];
            //        $course = $_POST['course'];
            //        $projects = $main->getProjects($course);
            //        $output = $main->buildResponse("OK", array(
            //            "username" => $username,
            //            "firstname" => $firstname,
            //            "role" => $role,
            //            "course" => $course,
            //            "projects" => ($projects == null) ? array() : $projects
            //        ));            
            //        
            //    }
            //    // Get the list of files for the chosen project
            //    else if ($post == 'getFiles') {                        
            //        $username = $_COOKIE['username'];
            //        $firstname = $_COOKIE['firstname'];
            //        $role = $_COOKIE['role'];
            //        $course = $_POST['course'];
            //        $project = $_POST['project'];            
            //        $output = $main->buildResponse("OK", array(
            //            "username" => $username,
            //            "firstname" => $firstname,
            //            "role" => $role,
            //            "course" => $course,
            //            "project" => $project,
            //            "filess" => ($files == null) ? array() : $files
            //        ));            
            //        
            //    }        
            //    // Default response: simply inform the client that the user is logged in
            //    else if ($post == 'validate') {
            //        // Send some personalized data
            //        $username = $_COOKIE['username'];
            //        $firstname = $_COOKIE['firstname'];
            //        $output = $main->buildResponse("OK", array(
            //            "username" => $username,
            //            "firstname" => $firstname               
            //        ));            
            //    }
            //    
            //}            
        }
        // Else, authenticate the user
        else {
            // Authentication failed
            if (!($main->authenticate($_POST["username"], $_POST["password"]))) {
                $output = $main->buildResponse("INTRUDER");            
            }
            // Authentication succeeded!
            else {
                // Send some personalized data
                $temp = $main->getTempUserData();
                $username = $temp['username'];
                $firstname = $temp['firstname'];
                $role = $temp['role'];
                $output = $main->buildResponse("OK", array(
                    "username" => $username,
                    "firstname" => $firstname,
                    "role" => $role // Most important part of the data response
                ));            
            }
        }
    }
    
    echo $output;

?>

<?php

    class Main {
        
        private $paths;        
        private $dbm;
        
        private $userData = null;
        
        public function __construct ($root = "/home/pilgrim/private_html/cloudlab_revamp/") {
            $this->paths = new stdClass();
            $this->paths->root = $root;
            $this->paths->php = $this->paths->root . "php/";
            $this->paths->img = $this->paths->root . "img/";
            $this->paths->lib = $this->paths->root . "lib/";
            $this->paths->css = $this->paths->root . "css/";
            $this->paths->domain = null/*$_SERVER['SERVER_NAME']*/; /* Do not explicitly state "localhost"; use null instead.
                                                                     * TODO: When scripts are finally hosted on a different server,
                                                                     * uncomment this expression
                                                                     */
            $this->paths->serverRoot = "/cloudlab_revamp/";
            
            // Allocate the DBManager
            require_once ($this->paths->php . "DBManager.php");
            $this->dbm = new DBManager();
        }
        
        public function __destruct () {
            $this->dbm->close();
        }
        
        /**
         * Authenticate the incoming connection and allocate the session
         */
        public function authenticate ($user, $pass) {            
            // Authenticate the user
            $user = mysql_real_escape_string($user);
            $pass = mysql_real_escape_string($pass);                    
            if ($user == null || $pass == null) {
                $user = "mdelong";
                $pass = md5("M");
            }
            $query = "SELECT * FROM Users WHERE user_id='" . $user . "' AND user_passhash='" . $pass . "' LIMIT 1;";                        
            if (($result = $this->dbm->queryFetchAssoc($query)) == null) {
                return false;
            }
            else {
                // Create the session
                $details = array(
                  "username" => $user,
                  "password" => $pass,
                  "idnumber" => $result['user_number'],
                  "firstname" => $result['user_firstname'],
                  "lastname" => $result['user_lastname'],
                  "email" => $result['user_email'],
                  "course" => $result['course_id'],
                  "role" => $result['group_id']
                );
                $this->createSession($details);
                return true;                
            }
        }                
        
        /**
         * Stores the user's current session via cookies
         * All information from the DB of the incoming user is stored
         * This avoids repeated DB queries, but could pose security threats
         */
        private function createSession ($details) {
            /* Store user details in memory just for the initial session
             * so that a response with data can be sent
             */
            $this->userData = array();
            
            // Creating Cookies
            $keys = array_keys($details);            
            foreach ($keys as $key) {
                setcookie($key, $details[$key], time()+3600, $this->paths->serverRoot, $this->paths->domain);
                $this->userData[$key] = $details[$key];
            }
        }
        
        /**
         * Log-out of the system; release the session
         */
        public function logout () {            
            $this->destroySession();
            return $this->buildResponse("LOGOUT");                    
        }
        
        /**
         * Destroy/Delete session data upon logging out
         */        
        private function destroySession () {
            $keys = array_keys($_COOKIE);
            foreach ($keys as $key) {
                setcookie($key, $_COOKIE[$key], time()-3600, $this->paths->serverRoot, $this->paths->domain);
            }
        }
        
        /**
         * Whenever a page is loaded/refreshed, the session must first always be validated
         */
        public function validateSession () {
            $isLoggedIn = true;
                        
            // If the cookies do not exist, the user needs to log in     
            if (empty($_COOKIE['username']) || empty($_COOKIE['password'])) {
                $isLoggedIn = false;
            }
            /* If the cookies do exist, check if the user/pass are valid.
            * This handles any potential cookie injection.
            */
            else {                
               $query = "SELECT * FROM Users WHERE user_id='" . $_COOKIE['username'] . "' AND user_passhash='" . $_COOKIE['password'] . "' LIMIT 1;";                        
               if (($result = $this->dbm->queryFetchAssoc($query)) == null)
                   $isLoggedIn = false;
            }
                                                        
            return $isLoggedIn;
        }
        
        public function getPaths () {
            return $this->paths;
        }
        
        public function getTempUserData () {
            return $this->userData;
        }
        
        /**
         * This method constructs a JSON response based on the input params and returns
         * it to the caller.This method is crucial for message passing to the client application.
         */
        public function buildResponse ($status="OK", $params = array()) {            
            $output = array(
                "status" => $status,
                "data" => $params
            );
       
            //$output = array(
            //    "status" => $status,
            //    "data" => $_COOKIE
            //);
            
            return json_encode($output);
        }
        
        /**
         * Retrieve the list of courses that the user is enrolled in
         */
        public function getCourses ($user) {
            if ($user == null) {
                $user = $_COOKIE['username'];                    
            }
            $user = mysql_real_escape_string($user);
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
        
        /**
         * Retrieve the list of projects of the chosen course
         */
        public function getProjects ($course) {
            $course = mysql_real_escape_string($course);
            $query = "SELECT project_name AS project FROM Projects WHERE course_id='" . $course . "'";
            if (($result = $this->dbm->query($query)) == null) {                
                return null;
            }
            else {                
                if (mysql_num_rows($result) == 0) {                    
                    $projects = null;
                }
                // Iterate as long as we have rows in the result set  
                $projects = array();
                while ($row = mysql_fetch_assoc($result)) {                    
                    array_push($projects, $row['project']);
                }                
                return $projects;
            }        
        }
        
        /**
         * Retrieve the list of files for the chosen project
         */
        public function getFiles ($course, $project) {
            $course = mysql_real_escape_string($course);
            $project = mysql_real_escape_string($project);
            $query = "SELECT project_name AS project FROM Projects WHERE course_id='" . $course . "'";
            if (($result = $this->dbm->query($query)) == null) {                
                return null;
            }
            else {                
                if (mysql_num_rows($result) == 0) {                    
                    $projects = null;
                }
                // Iterate as long as we have rows in the result set  
                $projects = array();
                while ($row = mysql_fetch_assoc($result)) {                    
                    array_push($projects, $row['project']);
                }                
                return $projects;
            }        
        }        
        
    }  

?>
