function Util () {
    
    console.log("Created the Util object");

    /* Fields */    
    
    // URIs/URLs to important internal and external resources    
    this.paths = new Array();
    this.paths.protocol = "http://";
    this.paths.domain = "23.21.85.72"; // TODO: Change on server
    this.paths.root = ""; // TODO: Change on server
    this.paths.img = "img";
    this.paths.js = "js";
    this.paths.php = "php";
    this.paths.node = new Array();
    this.paths.node.server = "compile";
    this.paths.node.port = "8000";
    
    this.urls = new Array();    
    //this.urls.server = this.paths.protocol + this.paths.domain + "/" + this.paths.root;
    this.urls.php = new Array();    
    this.urls.php.main = this.paths.php + "/" + "main.php";
    this.urls.node = this.paths.protocol + this.paths.domain + ":" + this.paths.node.port;    
    
    // Pre-configured AJAX settings
    this.ajax = new Array();
    this.ajax.url = this.urls.php.main;
    this.ajax.dataType = "json";    
    
    // The back function works only if HTML5 local/sessionStorage is supported (see the log message in your respective developer console)
    if (window.sessionStorage) {
        /* Stack-based implementation (using array) of the back function */        
        if (!sessionStorage.stack) {
            sessionStorage.stack = JSON.stringify(new Array());
        }
        this.back = new Array();
        // Push the currently landed page on to the stack
        this.back.add = function () {
            var current = window.location.pathname;
            var stack = JSON.parse(sessionStorage.stack); // Note that you need to wrap Web Storage objects as JSON strings to work with them
            // Prevents repetitions of the same page
            if (stack[stack.length - 1] != current) {
                stack.push(current);
            }            
            sessionStorage.stack = JSON.stringify(stack);
            // Debugging purposes
            console.log(sessionStorage.stack);
        };
        // Go to the previous page by popping the current page from the stack
        this.back.go = function () {
            var stack = JSON.parse(sessionStorage.stack);
            var current = stack.pop();            
            var prev = stack.pop();                
            stack.push(prev);            
            sessionStorage.stack = JSON.stringify(stack);            
            window.location.href = prev;            
        };            
    }
    else {
        console.log("Warning: Back function is not available: HTML5 Local Web Storage is not supported on your device.");
    }
    
    /* Methods */
    
    this.cookiesToArray = function () {
        var array = new Array();
        // key/value pairs in document.cookie are sometimes delimited by "; " instead of ";"
        var cookies = document.cookie.split("; ");
        var n = cookies.length;
        for (var i = 0; i < n; i++) {
            var cookie = cookies[i].split("=");        
            array[cookie[0]] = cookie[1];
        }							
        return array;
    };
    
    // Validates the session and then optionally performs a given AJAX task (with optional input data) to the same server
    /* Note: Sometimes the server responds to the request so fast that it reaches the 
     * browser before all elements are even created. Always ensure that the document is ready
     * when manipulating the DOM in a callback function that is hooked to an AJAX request
     */
    this.validateSession = function (task, taskData) {        
        $.post(Util.ajax.url,
               "validate=true",
               function (output) {                    
                    // Ensure that the user is logged in
                    if (output.status == "VALIDATED") {
			console.log("Util: Validated the user");
                        /* Perform default operations */

                        $( document ).ready(function () {							
                        	// Bind onclick events to the Back and Logout buttons
	                        $( "#header #back" ).click(function () {
	                            //window.location.href=document.referrer;
	                            Util.back.go();                            
	                        });
                        
	                        $( "#header #logout" ).click(function () {
	                            Util.logout();
	                        });
                        
				// Personalize page			
				var cookies = Util.cookiesToArray();
				$( "#header #user" ).text(cookies['firstname']);
                        
                       		// Perform the additional AJAX task only if it was specified
	                        if (task !== undefined) {
					console.log("Performing the AJAX Task");                                                    
	                            $.post(Util.ajax.url, taskData, task, Util.ajax.dataType);                                                           
	                        } else console.log("Task is undefined for some reason");                    

			});
                    }
                    // Incorrect login credentials
                    else if (output.status == "INTRUDER") {
                        alert("Back off, intruder!");
                        window.location.href = "index.html";							
                    }
                },
               Util.ajax.dataType);
    };
    
    // Log out of the current session    
    this.logout = function () {
        var data = "logout=true",
            success = function (output) {                                    
                if (output.status == "LOGOUT") {
                    // Avoid an infinite loop by ensuring the user is not redirected to the same page
                    if (window.location.pathname.search("index.html") == -1) {
                        window.location.href = "index.html";
                    }
                }
            };  
        $.post(Util.ajax.url, data, success, Util.ajax.dataType);
        // Clear the back stack
        if (window.sessionStorage) {
            delete sessionStorage.stack;
        }
    };
    
    // A mapping between human-readable status levels and their respective classes
    this.StatusBarLevel = {
        NORMAL : "alert",
        SUCCESS : "alert alert-success",
        INTERIM : "alert alert-info",
        WARNING : "alert alert-warning",
        ERROR : "alert alert-error"
    };
    
    // Changes the visual indication of the status as specified and displays the specified message in the status bar
    this.changeStatus = function (status, message) {
        // Get all the classes that are currently attached to the element
        var oldClasses = $( "#statusbar #status" ).attr('class');
        // Remove the old classes and attach the new class
        $( "#statusbar #status" ).toggleClass(oldClasses + " " + status);
        // If a message is provided, display it
        if (message !== undefined) {
            $( "#statusbar #status" ).text(message);
            // Return the status bar back to normal after 3 seconds
            if (message != "Ready.") { // Avoids an endless recursive loop
                setTimeout(function () {                            
                            Util.changeStatus(Util.StatusBarLevel.NORMAL, "Ready.")
                        }, 3500);
            }
        }				
    }    
    
};

Util = new Util();

