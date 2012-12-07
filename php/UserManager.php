<?php

    class UserManager {

        function createGroup ($groupname) {
            
        }
    
        function deleteGroup ($groupname) {
            $command = "";
            
            exec($command);
        }
    
        function createUser ($username, $password, $groupname, $expiry) {                                    
            //$command = "useradd -p " . crypt($password) . " -g " . $groupname . " -e " . $expiry . " -M " . $username;
            $command = "./createuser.sh " . $username . " " . crypt($password) . " " . $groupname . " " . $expiry;
            
            exec($command);
        }
        
        function deleteUser ($username) {
            $command = "userdel " . $username;
            
            exec($command);
        }
        
    }

?>