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
            $this->result = mysql_query($query, $this->link);            
            return $this->result;
        }
        
        /*
         * Query the DB and read the results into an associative array
         */
        public function queryFetchAssoc ($query) {                                
			$result = $this->query($query);
            return $this->fetchAssoc($result);
        }
        
        /*
         * Read the specified result items into an array of associative arrays
         */
        public function fetchAssoc ($result) {
            // Faulty query or some other error
            if (!$result) {                
                return NULL;
            }
            // No results
            else if (mysql_num_rows($result) == 0) {                    
                return 0;
            }
            else {                            
                // Iterate as long as we have rows in the result set  
                $items = array();
                while ($row = mysql_fetch_assoc($result)) {
                    array_push($items, $row);                    
                }                
                return $items;
            }              
        }
        
        /*
         * Process the latest query result into an array of associative arrays
         */
        public function fetchLatestAssoc () {
            return $this->fetchAssoc($this->result);
        }
        
        /*
         * Chain an array of queries together
         */
        public function queryChain ($queries) {
            $results = array();
            foreach ($queries as $query) {
                $result = $this->query($query);
                array_push($results, $result);
            }
            return $results;
        }
        
        /*
         * Chain multiple queries together and process into associative arrays
         */
        public function queryChainFetchAssoc ($queries) {
            $results = $this->queryChain($queries);
            $items = array();
            foreach ($results as $result) {
                array_push($items, $this->fetchAssoc($result));
            }
            return $items;
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