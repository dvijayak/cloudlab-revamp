<?php

    /* error reporting must be set at -1 during testing */
    
    //error_reporting(E_ALL ^ E_NOTICE);
    error_reporting(-1);
    
    // PHP CONSOLE FOR DEBUGGING PURPOSES: ONLY WORKS WITH THE CHROME BROWSER
    //require_once("PhpConsole.php");
    //PHPConsole::start();
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
                if ($courses == null) {
                    $output = $main->buildResponse("ZERO_RESULTS");    
                }
                else {                    
                    $output = $main->buildResponse("OK", array(                                    
                        "courses" => $courses
                    ));
                }
            }
            // Get the list of projects for the chosen course
            else if (!empty($_POST['getProjects'])) {                                        
                $course = $_POST['course'];                
                $projects = $main->getProjectManager()->getProjects($course);
                if ($projects == null) {
                    $output = $main->buildResponse("ZERO_RESULTS");    
                }
                else {                    
                    $output = $main->buildResponse("OK", array(                                    
                        "projects" => $projects
                    ));
                }
                
            }
            // Get the list of files for the chosen project
            else if (!empty($_POST['getFiles'])) {
                $user = $_COOKIE['username'];
                $course = $_COOKIE['course'];
                $project = $_POST['project'];
                $files = $main->getFileManager()->getFiles($user, $course, $project);
                if ($files == null) {
                    $output = $main->buildResponse("ZERO_RESULTS");    
                }
                else {                    
                    $output = $main->buildResponse("OK", array(                                    
                        "files" => $files
                    ));
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
                if ($contents == "") {
                    $output = $main->buildResponse("OK", array(
                        "contents" => ""
                    ));
                }
                else if ($contents == null) {
                    $output = $main->buildResponse("ZERO_RESULTS");
                }
                else {
                    $output = $main->buildResponse("OK", array(
                       "contents" => $contents 
                    ));
                }
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
                    $output = $main->buildResponse("OK");
                }
                else {
                    $output = $main->buildResponse("FAIL");
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
                    // Return the updated list of files
                    $files = $main->getFileManager()->getFiles($user, $course, $project);
                    if ($files == null) {
                        $output = $main->buildResponse("ZERO_RESULTS");    
                    }
                    else {                    
                        $output = $main->buildResponse("OK", array(                                    
                            "files" => $files
                        ));
                    }
                }
                else {
                    $output = $main->buildResponse("FAIL");
                }
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
                    // Return the updated list of files
                    $files = $main->getFileManager()->getFiles($user, $course, $project);
                    if ($files == null) {
                        $output = $main->buildResponse("ZERO_RESULTS");    
                    }
                    else {                    
                        $output = $main->buildResponse("OK", array(                                    
                            "files" => $files
                        ));
                    }
                }
                else {
                    $output = $main->buildResponse("FAIL");
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
                    // Return the updated list of files
                    $files = $main->getFileManager()->getFiles($user, $course, $project);
                    if ($files == null) {
                        $output = $main->buildResponse("ZERO_RESULTS");    
                    }
                    else {                    
                        $output = $main->buildResponse("OK", array(                                    
                            "files" => $files
                        ));
                    }
                }
                else {
                    $output = $main->buildResponse("FAIL");
                }
            }
            // Store the user's selected course in a cookie (overwrite)
            else if (!empty($_POST['course'])) {
                setcookie('course', $_POST['course'], time()+3600, $main->getPaths()->serverRoot, $main->getPaths()->domain);
                $output = $main->buildResponse("OK");
            }
            // Store the user's selected project in a cookie (overwrite)
            else if (!empty($_POST['project'])) {
                setcookie('project', $_POST['project'], time()+3600, $main->getPaths()->serverRoot, $main->getPaths()->domain);
                $output = $main->buildResponse("OK");
            }
            // Store the user's selected file in a cookie (overwrite)
            else if (!empty($_POST['file'])) {
                setcookie('file', $_POST['file'], time()+3600, $main->getPaths()->serverRoot, $main->getPaths()->domain);
                $output = $main->buildResponse("OK");
            }
            // Default response: simply inform the client that the user is logged in
            else if (!empty($_POST['validate'])) {                 
                $output = $main->buildResponse("OK", $_COOKIE);
            }                
        }
        // Else, authenticate the user
        else {
            // Authentication failed
            if ((empty($_POST['username']) || empty($_POST['password'])) ||
                !($main->authenticate($_POST['username'], $_POST['password']))) {
                $output = $main->buildResponse("INTRUDER");            
            }
            // Authentication succeeded!
            else {                                
                $output = $main->buildResponse("OK");
            }
        }
    }
    
    echo $output;

?>

<?php

    class Main {
        
        private $paths;        
        private $dbm; // Database Manager
        private $cm;  // Course Manager
        private $pm;  // Project Manager
        private $fm;  // File Manager
        
        private $userData = null;
        
        public function __construct ($root = "/home/cloudlab/public_html/") { /* TODO: Change on server */
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
            $this->paths->serverRoot = "/"; /* TODO: Change on server */
            
            /* Attempt to allocate all managers */
            
            require_once ($this->paths->php . "DBManager.php");
            $this->dbm = new DBManager();
            
            require_once ($this->paths->php . "CourseManager.php");                    
            $this->cm = new CourseManager($this->dbm);            
            
            require_once ($this->paths->php . "ProjectManager.php");            
            $this->pm = new ProjectManager($this->dbm);
                        
            require_once ($this->paths->php . "FileManager.php");
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
                //$this->userData[$key] = $details[$key];
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
        
        /**
         * This method constructs a JSON response based on the input params and returns
         * it to the caller.This method is crucial for message passing to the client application.
         */
        public function buildResponse ($status="OK", $params = array()) {            
            $output = array(
                "status" => $status,
                "data" => $params
            );

            return json_encode($output);
        }
        
        /* Accessors */
        
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
