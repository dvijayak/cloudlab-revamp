<?php

    /* error reporting must be set at -1 during testing */
    
    //error_reporting(E_ALL ^ E_NOTICE);
    error_reporting(-1);        
    
    // PHP CONSOLE FOR DEBUGGING PURPOSES: ONLY WORKS WITH THE CHROME BROWSER
    require_once("PhpConsole.php");
    PHPConsole::start();
    /////////////////////////////////////////////////////////////////////////
     
    $main = new Main();    
    
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
                $user = $_COOKIE['username'];
                $courses = $main->getCourseManager()->getCourses($user);
                $output = $main->buildResponse(array(
                    "courses" => $courses
                ));                
            }
            // Get the list of projects for the chosen course
            else if (!empty($_POST['getProjects'])) {                                        
                $course = $_POST['course'];                
                $projects = $main->getProjectManager()->getProjects($course);
                $output = $main->buildResponse(array(
                    "projects" => $projects
                ));
            }
            // Create the specified project
            else if (!empty($_POST['newProject'])) {                
                $course = $_COOKIE['course'];                
                $project = array(
                    "name" => $_POST['name'],
                    "description" => $_POST['description']
                );  
                $success = $main->getProjectManager()->createProject($course, $project);                
                if ($success) {                    
                    $output = $main->buildResponse();
                }
                else {
                    $output = $main->buildResponse(array(), "FAIL");
                }
            }            
            // Rename the specified project
            else if (!empty($_POST['renameProject'])) {
                $course = $_COOKIE['course'];                                
                $old = array(
                    "name" => $_POST['oldname']                    
                );
                $new = array(
                    "name" => $_POST['newname']                    
                );
                $success = $main->getProjectManager()->renameProject($course, $old, $new);                
                if ($success) {                    
                    $output = $main->buildResponse();                
                }
                else {
                    $output = $main->buildResponse(array(), "FAIL");
                }
            }
            // Delete the specified project
            else if (!empty($_POST['deleteProject'])) {
                $course = $_COOKIE['course'];                
                $project = array(
                    "name" => $_POST['name']                    
                );  
                $success = $main->getProjectManager()->deleteProject($course, $project);
                if ($success) {                    
                    $output = $main->buildResponse();
                }
                else {
                    $output = $main->buildResponse(array(), "FAIL");
                }
            }            
            // Get the list of files for the chosen project
            else if (!empty($_POST['getFiles'])) {
                $user = $_COOKIE['username'];
                $course = $_COOKIE['course'];
                $project = $_POST['project'];
                $files = $main->getFileManager()->getFiles($user, $course, $project);
                $output = $main->buildResponse(array(
                    "files" => $files
                ));
            }
            // Create the specified file
            else if (!empty($_POST['newFile'])) {
                $user = $_COOKIE['username'];
                $course = $_COOKIE['course'];
                $project = $_COOKIE['project'];
                $file = array(
                    "name" => $_POST['name'],
                    "ext" => $_POST['ext']/*,
                    "contents" => $_POST['contents']*/
                );  
                $success = $main->getFileManager()->createFile($user, $course, $project, $file);                
                if ($success) {                    
                    $output = $main->buildResponse();
                }
                else {
                    $output = $main->buildResponse(array(), "FAIL");
                }
            }            
            // Open (retrieve the contents of) the specified file
            else if (!empty($_POST['openFile'])) {
                $user = $_COOKIE['username'];
                $course = $_COOKIE['course'];
                $project = $_COOKIE['project'];
                $file = array(
                    "name" => $_POST['name'],
                    "ext" => $_POST['ext']                    
                );
                $contents = $main->getFileManager()->openFile($user, $course, $project, $file);                
                $output = $main->buildResponse(array(
                    "contents" => $contents
                ));                
            }
            // Save (overwrite the contents of) the specified file
            else if (!empty($_POST['saveFile'])) {
                $user = $_COOKIE['username'];
                $course = $_COOKIE['course'];
                $project = $_COOKIE['project'];
                $file = array(
                    "name" => $_POST['name'],
                    "ext" => $_POST['ext'],
                    "contents" => $_POST['contents']
                );                
                $success = $main->getFileManager()->saveFile($user, $course, $project, $file);                
                if ($success) {
                    $output = $main->buildResponse();
                }
                else {
                    $output = $main->buildResponse(array(), "FAIL");
                }
            }
            // Rename the specified file
            else if (!empty($_POST['renameFile'])) {
                $user = $_COOKIE['username'];
                $course = $_COOKIE['course'];
                $project = $_COOKIE['project'];
                $old = array(
                    "name" => $_POST['oldname'],
                    "ext" => $_POST['oldext']
                );
                $new = array(
                    "name" => $_POST['newname'],
                    "ext" => $_POST['newext']
                );
                $success = $main->getFileManager()->renameFile($user, $course, $project, $old, $new);
                if ($success) {                    
                    $output = $main->buildResponse();                
                }
                else {
                    $output = $main->buildResponse(array(), "FAIL");
                }
            }
            // Delete the specified file
            else if (!empty($_POST['deleteFile'])) {
                $user = $_COOKIE['username'];
                $course = $_COOKIE['course'];
                $project = $_COOKIE['project'];
                $file = array(
                    "name" => $_POST['name'],
                    "ext" => $_POST['ext']
                );  
                $success = $main->getFileManager()->deleteFile($user, $course, $project, $file);                
                if ($success) {                    
                    $output = $main->buildResponse();
                }
                else {
                    $output = $main->buildResponse(array(), "FAIL");
                }
            }
            // Store the user's selected course in a cookie (overwrite)
            else if (!empty($_POST['course'])) {
                setcookie('course', $_POST['course'], time()+3600, $main->getPaths()->root, $main->getPaths()->domain);
                $output = $main->buildResponse();
            }
            // Store the user's selected project in a cookie (overwrite)
            else if (!empty($_POST['project'])) {
                setcookie('project', $_POST['project'], time()+3600, $main->getPaths()->root, $main->getPaths()->domain);
                $output = $main->buildResponse();
            }
            // Store the user's selected file in a cookie (overwrite)
            else if (!empty($_POST['file'])) {
                setcookie('file', $_POST['file'], time()+3600, $main->getPaths()->root, $main->getPaths()->domain);
                $output = $main->buildResponse();
            }
            // Retrieve all users that exist in the system
            else if (!empty($_POST['getAllUsers'])) {
                $users = $main->getUserManager()->getUsers();
                $output = $main->buildResponse(array(
                    "users" => $users
                ));                
            }
            // Create the specified user
            else if (!empty($_POST['newUser'])) {
                $user = array(
                    "idnum" => $_POST['idnum'],
                    "id" => $_POST['id'],
                    "passhash" => $_POST['passhash'],
                    "fname" => $_POST['fname'],
                    "lname" => $_POST['lname'],
                    "email" => $_POST['email'],
                    "role" => $_POST['role']
                );
                $success = $main->getUserManager()->createUser($user);
                if ($success) {
                    $output = $main->buildResponse();
                }
                else {
                    $output = $main->buildResponse(array(), "FAIL");
                }
            }
            // Edit the specified user
            else if (!empty($_POST['editUser'])) {
                $user = array(
                    "idnum" => $_POST['idnum'],
                    "id" => $_POST['id'],
                    "passhash" => $_POST['passhash'],
                    "fname" => $_POST['fname'],
                    "lname" => $_POST['lname'],
                    "email" => $_POST['email'],
                    "role" => $_POST['role']
                );
                $success = $main->getUserManager()->editUser($user);
                if ($success) {
                    $output = $main->buildResponse();
                }
                else {
                    $output = $main->buildResponse(array(), "FAIL");
                }               
            }
            // Delete the specified user
            else if (!empty($_POST['deleteUser'])) {
                $user = array(
                    "idnum" => $_POST['idnum'],
                    "id" => $_POST['id']
                );                
                $success = $main->getUserManager()->deleteUser($user);
                if ($success) {
                    $output = $main->buildResponse();
                }
                else {
                    $output = $main->buildResponse(array(), "FAIL");
                }
            }
            // Retrieve all courses that exist in the system
            else if (!empty($_POST['getAllCourses'])) {
                $courses = $main->getCourseManager()->getCourses();
                $output = $main->buildResponse(array(
                    "courses" => $courses
                ));                
            }
            // Create the specified course
            else if (!empty($_POST['newCourse'])) {
                $course = array(                    
                    "id" => $_POST['id'],                    
                    "name" => $_POST['name'],
                    "description" => $_POST['description']
                );
                $success = $main->getCourseManager()->createCourse($course);
                if ($success) {
                    $output = $main->buildResponse();
                }
                else {
                    $output = $main->buildResponse(array(), "FAIL");
                }
            }
            // Edit the specified course
            else if (!empty($_POST['editCourse'])) {
                $course = array(
                    "id" => $_POST['id'],                    
                    "name" => $_POST['name'],
                    "description" => $_POST['description']
                );
                $success = $main->getCourseManager()->editCourse($course);
                if ($success) {
                    $output = $main->buildResponse();
                }
                else {
                    $output = $main->buildResponse(array(), "FAIL");
                }               
            }
            // Delete the specified course
            else if (!empty($_POST['deleteCourse'])) {
                $course = array(                    
                    "id" => $_POST['id']
                );                
                $success = $main->getCourseManager()->deleteCourse($course);
                if ($success) {
                    $output = $main->buildResponse();
                }
                else {
                    $output = $main->buildResponse(array(), "FAIL");
                }
            }            
            // Default response: simply inform the client that the user is logged in
            else if (!empty($_POST['validate'])) {                
                $output = $main->buildResponse(array(), "VALIDATED");
            }                
        }
        // Else, authenticate the user
        else {            
            // Authentication failed
            $success = $main->authenticate($_POST['username'], $_POST['password']);
            if ((empty($_POST['username']) || empty($_POST['password']))
                || $success === 0 || !$success) {
                $output = $main->buildResponse(array(), "INTRUDER");            
            }
            // Authentication succeeded!
            else {                                
                $output = $main->buildResponse();
            }            
        }
    }
    
    echo $output;

?>

<?php

    class Main {
        
        private $paths;        
        private $dbm; // Database Manager
        private $um;  // User Manager
        private $cm;  // Course Manager
        private $pm;  // Project Manager
        private $fm;  // File Manager
        
        private $userData = null;
        
        public function __construct ($root = "/") { /* TODO: Change on server */
            $this->paths = new stdClass();            
            $this->paths->root = $root; /* TODO: Change on server */            
            $this->paths->domain = null/*$_SERVER['SERVER_NAME']*/; /* Do not explicitly state "localhost"; use null instead.
                                                                     * TODO: When scripts are finally hosted on a different server,
                                                                     * uncomment this expression
                                                                     */
            
            /* Attempt to allocate all managers */
                        
            require_once ("DBManager.php");
            $this->dbm = new DBManager();
            
            require_once ("UserManager.php");
            $this->um = new UserManager($this->dbm);
                        
            require_once ("CourseManager.php");
            $this->cm = new CourseManager($this->dbm);            
                        
            require_once ("ProjectManager.php");
            $this->pm = new ProjectManager($this->dbm);
                                    
            require_once ("FileManager.php");
            $this->fm = new FileManager($this->dbm);
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
            $query = "SELECT * FROM Users WHERE user_id='" . $user . "' AND user_passhash='" . $pass . "' LIMIT 1;";
            $result = $this->dbm->query($query);
            if (!$result) {
                return false;
            }
            else if (mysql_num_rows($result) == 0) {
                return 0;
            }
            else {
                $result = mysql_fetch_assoc($result);
                // Create the session
                $details = array(
                  "username" => $user,
                  "password" => $pass,
                  "idnumber" => $result['user_number'],
                  "firstname" => $result['user_firstname'],
                  "lastname" => $result['user_lastname'],
                  "email" => $result['user_email'],                  
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
                setcookie($key, $details[$key], time()+3600, $this->paths->root, $this->paths->domain);
                //$this->userData[$key] = $details[$key];
            }
        }
        
        /**
         * Log-out of the system; release the session
         */
        public function logout () {            
            $this->destroySession();
            return $this->buildResponse(array(), "LOGOUT");                    
        }
        
        /**
         * Destroy/Delete session data upon logging out
         */        
        private function destroySession () {
            $keys = array_keys($_COOKIE);
            foreach ($keys as $key) {
                setcookie($key, $_COOKIE[$key], time()-3600, $this->paths->root, $this->paths->domain);
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
                if (!$this->dbm->queryFetchAssoc($query)) {
                    $isLoggedIn = false;            
                }                                                            
            }
                                                        
            return $isLoggedIn;
        }    
        
        /**
         * This method constructs a JSON response based on the input params and returns
         * it to the caller.This method is crucial for message passing to the client application.
         */
        public function buildResponse ($data=array(), $status=NULL) {
            // Default: status = OK, no response data
            $output = array(
                "status" => "OK",
                "data" => array()
            );
            
            // Handle the provided data
            if (!empty($data)) {
                //print_r($data);
                $keys = array_keys($data);
                $nKeys = count($keys);                
                $compiledData = array();
                for ($i = 0; $i < $nKeys; $i++) {
                    $datum = $data[$keys[$i]];
                    // Ensure to do STRICT equality tests
                    if ($datum === NULL) {
                        $output['status'] = "FAIL";
                        break;
                    }
                    else if ($datum === 0) { 
                        $output['status'] = "ZERO_RESULTS";                        
                        break;
                    }
                    else {                        
                        $compiledData[$keys[$i]] = $datum;                        
                    }
                }
                $output['data'] = $compiledData;
            }
            
            // Prefer the provided status
            if (!empty($status)) {
                $output['status'] = $status;
            }
            return json_encode($output);
        }
        
        /* Accessors */
        
        public function getUserManager () {
            return $this->um;
        }
        
        public function getCourseManager () {
            return $this->cm;
        }
        
        public function getProjectManager () {
            return $this->pm;
        }
        
        public function getFileManager () {
            return $this->fm;
        }                
        
        public function getPaths () {
            return $this->paths;
        }
        
        public function getTempUserData () {
            return $this->userData;
        }        
        
    }  

?>
