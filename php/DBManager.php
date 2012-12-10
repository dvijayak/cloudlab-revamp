<?php

    /**
     * TODO: Currently using only the MySQL extensions, which is discouraged (and possibly deprecated from PHP 5.1.x)
     *       In the future, to improve security (such as protecting from SQL injection) and increase productivity,
     *       use PDO throughout instead.
     */

    class DBManager {
                
        private $link;
        private $result;
        
        private function errorDump ($message) {
            $dump = "Error: " . mysql_errno() . " - " . mysql_error() . "\n\n";
            if ($message != null) {
                $dump .= "More info: " . $message . "\n";
            }
            return $dump;
        }        
        
        public function connect ($host, $user, $pass, $db) {
            if (($this->link = mysql_connect($host, $user, $pass)) === false) {
                die($this->errorDump());
            }
            if (mysql_select_db($db) === false)
                die($this->errorDump());            
        }        
        
        public function __construct ($host="dvijayak-db-instance.cvnivdipnyk6.us-east-1.rds.amazonaws.com",
                                     $user="cloudlab", $pass="cloudlabone234", $db="cloudlabdb") {
            if ($user == null || $pass == null) {
                die("Error: a username/password was not provided.");
            }
            else {
                $this->connect($host, $user, $pass, $db);
            }
        }        
        
        public function query ($query) {
            if (!($result = mysql_query($query))) {
                $this->result = null;                
            }
            else {
                $this->result = mysql_query($query, $this->link);
            }            
            return $this->result;
        }
        
        public function queryfetchAssoc ($query) {            
            $array = mysql_fetch_assoc($this->query($query));
            if (mysql_num_rows($this->result) == 0) {
                $array = null;
            }            
            return $array;
        }
        
        /**
         * If one might ever need the resource to the database connection for some odd reason...
         */
        public function getLink () {
            return $this->link;
        }
            
        /**
         * Get the latest result resurce
         */
        public function getResult () {
            return $this->result;
                
        }
        
        public function close () {
            mysql_close($this->link);
        }
        
        public function changeConnection ($host, $user, $pass, $db) {
            $this->close();
            $this->connect($host, $user, $pass, $db);
        }
        
    }        
    
?>