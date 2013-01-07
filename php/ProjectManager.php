<?php    
    
    class ProjectManager
    {
        //private $s3;
        private $dbm;        
        
        public function __construct($dbm) {
            //$this->s3 = new AmazonS3();
            $this->dbm = $dbm;            
        }                
        
        /**
         * Create the specified project for the provided course
         */
        public function createProject ($course, $project) {
            $course = mysql_real_escape_string($course);
            $name = mysql_real_escape_string($project['name']);
            $description = mysql_real_escape_string($project['description']);
            $query = "INSERT INTO Projects (course_id, project_name, project_desc)" .
            " VALUES ('" . $course . "', '" . $name . "', '" . $description . "');";
                                
            $success = $this->dbm->query($query);            
            return $success;              
        }
        
        /**
         * Rename the specified project for the provided course
         */
        public function renameProject ($course, $old, $new) {
            $course = mysql_real_escape_string($course);            
            $oldname = mysql_real_escape_string($old['name']);
            $newname = mysql_real_escape_string($new['name']);
            // Rename the project
            $query = "UPDATE Projects SET project_name = '" . $newname .
            "' WHERE course_id = '" . $course . "' AND project_name = '" . $oldname . "';";
            
            $success = $this->dbm->query($query);
            return $success;                    
        }        
        
        /**
         * Delete the specified project for the provided course
         */
        public function deleteProject ($course, $project) {
            $course = mysql_real_escape_string($course);
            $name = mysql_real_escape_string($project['name']);            
            $query = "DELETE FROM Projects WHERE course_id = '" . $course . "'" .
            " AND project_name = '" . $name . "'";
                                
            $success = $this->dbm->query($query);            
            return $success;              
        }        
        
        /**
         * Retrieve the list of projects of the chosen course
         */
        public function getProjects ($course) {        
            $course = mysql_real_escape_string($course);
            $query = "SELECT project_name AS project FROM Projects WHERE course_id='" . $course . "'";
            $projects = $this->dbm->queryFetchAssoc($query);
			return $projects;		       
        }
                
    //    function create_bb_project($project_name) {
    //        $bucket = $this->create_project($project_name);
    //        if ($bucket) {
    //            $fm = new FileManager();
    //            $fm->create_type_file("default", $project_name, "BB");
    //            $fm->create_html_source("default", $project_name, "index");
    //            $fm->create_js_source("default", $project_name, "script");
    //            $fm->create_css_source("default", $project_name, "style");
    //            return True;
    //        }
    //        else {
    //            return False;
    //        }
    //    }
    //    
    //    function create_c_project($project_name) {
    //        $bucket = $this->create_project($project_name);
    //        if ($bucket) {
    //            $fm = new FileManager();
    //            $fm->create_type_file("default", $project_name, "C");
    //            $fm->create_c_source("default", $project_name, "main");
    //            $fm->create_h_source("default", $project_name, "main");
    //            return True;
    //        }
    //        else {
    //            return False;
    //        }
    //    }
    //    
    //    function create_cpp_project($project_name) {
    //        $bucket = $this->create_project($project_name);
    //        if ($bucket) {
    //            $fm = new FileManager();
    //            $fm->create_type_file("default", $project_name, "CPP");
    //            $fm->create_cpp_source("default", $project_name, "main");
    //            $fm->create_hpp_source("default", $project_name, "main");
    //            return True;
    //        }
    //        else {
    //            return False;
    //        }
    //    }
    //    
    //    function delete_project($project_name) {
    //        $bucket = strtolower($project_name);
    //        $response = $this->s3->delete_bucket($bucket, true);
    //        return $response->isOk();
    //    }
    //
    //    private function create_project($project_name) {
    //        $bucket = strtolower($project_name);
    //        
    //        if (!($this->s3->if_bucket_exists($bucket))) {
    //            $create_bucket_response = $this->s3->create_bucket($bucket, AmazonS3::REGION_US_W1);
    //            
    //            if ($create_bucket_response->isOk()) {
    //                $exists = $this->s3->if_bucket_exists($bucket);
    //                 while (!$exists)
    //                 {
    //                    sleep(1);
    //                    $exists = $this->s3->if_bucket_exists($bucket);
    //                 }
    //                
    //                return $bucket;
    //            }
    //            
    //            else {
    //                echo "Error: failed to create project\n";
    //                return False;
    //            }
    //        }
    //
    //        else {
    //            echo "Error: project named " . $project_name . " already exists.\n";
    //            return False;
    //        }
    //    }
    }

?>