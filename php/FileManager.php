<?php

    class FileManager
    {
        //private $s3;
        private $dbm;
        private $defaults;
        
        public function __construct($dbm) {
            //$this->s3 = new AmazonS3();
            $this->dbm = $dbm;
            
            $this->defaults = new stdClass();
            $this->defaults->files = new stdClass();
            $this->defaults->files->c = "#include <stdio.h>\n\nint main(void) {\n\tprintf(\"Hello world\\n\");\n\treturn 0;\n}\n";
            $this->defaults->files->cpp = "#include <iostream>\n\nint main(void) {\n\tstd::cout << \"Hello world\" << std::endl;\n\treturn 0;\n}\n";
        }                
        
        public function openFile ($username, $course, $project, $file) {
            $username = mysql_real_escape_string($username);
            $course = mysql_real_escape_string($course);
            $project = mysql_real_escape_string($project);
            $filename = mysql_real_escape_string($file['name']);
            $ext = mysql_real_escape_string($file['ext']);
            // Retrieve the project id for the given pair of course id and project name
            $subquery = "SELECT project_id FROM Projects WHERE course_id = '" . $course . "' AND project_name = '" . $project . "'";
            // Retrieve the contents of the specified file
            $query = "SELECT file_data FROM Files" .
            " WHERE project_id = (" . $subquery . ") AND file_name = '" .$filename.
            "' AND file_ext='" . $ext . "' AND file_owner = '" . $username . "';";
                        
            
            $result = $this->dbm->query($query);
            if (!$result) {                
                return false;
            }
            else if (mysql_num_rows($result) == 0) {
                return "";
            }
            else {                
                $result = mysql_fetch_assoc($result);                
                $contents = $result['file_data'];                
                return $contents;
            }  
        }        
        
        public function createFile ($username, $course, $project, $file) {
            $username = mysql_real_escape_string($username);
            $course = mysql_real_escape_string($course);
            $project = mysql_real_escape_string($project);
            $filename = mysql_real_escape_string($file['name']);
            $ext = mysql_real_escape_string($file['ext']);
            //$contents = mysql_real_escape_string(($file['contents']) ? $file['contents'] : "");            
            $contents = "// New file: " . $filename.".".$ext . "\n\n";
            if ($ext == "c") {
                $contents = "/* New file: " . $filename.".".$ext . " */\n\n" . $this->defaults->files->c;    
            }
            else if ($ext == "cpp") {
                $contents .= $this->defaults->files->cpp;
            }            
            $contents = mysql_real_escape_string($contents);
            // Retrieve the project id for the given pair of course id and project name
            $subquery = "SELECT project_id FROM Projects WHERE course_id = '" . $course . "' AND project_name = '" . $project . "'";
            // Insert the file into the table
            $query = "INSERT INTO Files (file_name, file_ext, project_id, file_owner, file_data)" .
            " VALUES ('" . $filename . "', '" . $ext . "', (" . $subquery . "), '" . $username . "', '" . $contents . "');";
                                
            $success = $this->dbm->query($query);            
            return $success;           
        }
        
        public function renameFile ($username, $course, $project, $old, $new) {
            $username = mysql_real_escape_string($username);
            $course = mysql_real_escape_string($course);
            $project = mysql_real_escape_string($project);
            $oldname = mysql_real_escape_string($old['name']);
            $newname = mysql_real_escape_string($new['name']);
            // For now, the old and new file names must have the same extension/file type
            $ext = mysql_real_escape_string($old['ext']);
            // Retrieve the project id for the given pair of course id and project name
            $subquery = "SELECT project_id FROM Projects WHERE course_id = '" . $course . "' AND project_name = '" . $project . "'";
            // Set the contents of the specified file
            $query = "UPDATE Files SET file_name = '" . $newname .
            "' WHERE project_id = (" . $subquery . ") AND file_name = '" .$oldname.
            "' AND file_ext='" . $ext . "' AND file_owner = '" . $username . "';";
            
            $success = $this->dbm->query($query);
            return $success;
        }        
        
        public function deleteFile ($username, $course, $project, $file) {
            $username = mysql_real_escape_string($username);
            $course = mysql_real_escape_string($course);
            $project = mysql_real_escape_string($project);
            $filename = mysql_real_escape_string($file['name']);
            $ext = mysql_real_escape_string($file['ext']);            
            // Retrieve the project id for the given pair of course id and project name
            $subquery = "SELECT project_id FROM Projects WHERE course_id = '" . $course . "' AND project_name = '" . $project . "'";
            // Delete the file from the table
            $query = "DELETE FROM Files" .
            " WHERE project_id = (" . $subquery . ") AND file_name = '" .$filename.
            "' AND file_ext='" . $ext . "' AND file_owner = '" . $username . "';";
            
            $success = $this->dbm->query($query);            
            return $success;            
        }
        
        public function saveFile ($username, $course, $project, $file) {
            $username = mysql_real_escape_string($username);
            $course = mysql_real_escape_string($course);
            $project = mysql_real_escape_string($project);
            $filename = mysql_real_escape_string($file['name']);
            $ext = mysql_real_escape_string($file['ext']);
            $contents = mysql_real_escape_string($file['contents']);
            // Retrieve the project id for the given pair of course id and project name
            $subquery = "SELECT project_id FROM Projects WHERE course_id = '" . $course . "' AND project_name = '" . $project . "'";
            // Set the contents of the specified file
            $query = "UPDATE Files SET file_data = '" . $contents .
            "' WHERE project_id = (" . $subquery . ") AND file_name = '" .$filename.
            "' AND file_ext='" . $ext . "' AND file_owner = '" . $username . "';";
            
            $success = $this->dbm->query($query);
            return $success;
        }        
        
        /**
         * Retrieve the list of files for the chosen project
         */
        public function getFiles ($username, $course, $project) {
            $username = mysql_real_escape_string($username);
            $course = mysql_real_escape_string($course);
            $project = mysql_real_escape_string($project);
            // Retrieve the project id for the given pair of course id and project name
            $subquery = "SELECT project_id FROM Projects WHERE course_id = '" . $course . "' AND project_name = '" . $project . "'";
            // Retrieve the list of files for the given project id which is owned by the given user
            $query = "SELECT Enrollments.course_id AS 'course', Projects.project_name AS 'project'," .
            " Files.file_id AS 'id', Files.file_name AS 'name', Files.file_ext AS 'ext', Files.project_id, Files.file_owner AS 'owner', Files.file_data AS 'contents'" .
            " FROM Enrollments, Projects, Files WHERE Files.file_owner = '" . $username . // Note that each new line continuation begins with a space
            "' AND Projects.project_id = (" . $subquery . ") AND Enrollments.course_id = Projects.course_id" .
            " AND Projects.project_id = Files.project_id AND Enrollments.user_id = Files.file_owner;";
            
            $files = $this->dbm->queryFetchAssoc($query);            
			return $files;		       
        }
        
    }
	  
?>
