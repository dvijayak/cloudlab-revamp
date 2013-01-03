function Util () {
    
    console.log("Created the Util object");

    /* Fields */    
    
    // URIs/URLs to important internal and external resources    
    this.paths = new Array();
    this.paths.protocol = "http://";
    this.paths.domain = "localhost"; // TODO: Change on server
    this.paths.root = "/cloudlab_revamp"; // TODO: Change on server
    this.paths.img = "/img";
    this.paths.js = "/js";
    this.paths.php = "/php";
    this.paths.node = new Array();
    this.paths.node.server = "/compile";
    this.paths.node.port = "8000";
    
    this.urls = new Array();    
    this.urls.server = this.paths.protocol + this.paths.domain + this.paths.root;
    this.urls.php = new Array();
    this.urls.php.main = this.urls.server + this.paths.php + "/main.php";
    this.urls.node = this.paths.protocol + this.paths.domain + ":" + this.paths.node.port;    
    
    // Pre-configured AJAX settings
    this.ajax = new Array();
    this.ajax.url = this.urls.php.main;
    this.ajax.dataType = "json";    
        
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
    }
    
    // Validates the session and then optionally performs a given AJAX task (with optional input data) to the same server
    this.validateSession = function (task, taskData) {        
        $.post(Util.ajax.url,
               "validate=true",
               function (output) {                    
                    // Ensure that the user is logged in
                    if (output.status == "OK") {
                        /* Perform default operations */
                        
                        // Bind onclick events to the Back and Logout buttons
                        $( "#header #back" ).click(function () {
                            window.location.href=document.referrer;
                        });
                        
                        $( "#header #logout" ).click(function () {
                            Util.logout();
                        });
                        
						// Personalize page
						var cookies = Util.cookiesToArray();
						$( "#header #user" ).text(cookies['firstname']);
                        
                        // Perform the additional AJAX task only if it was specified
                        if (task !== undefined) {                                                    
                            $.post(Util.ajax.url, taskData, task, Util.ajax.dataType);                                                           
                        }                    
                    }
                    // Incorrect login credentials
                    else if (output.status == "INTRUDER") {
                        alert("Back off, intruder!");
                        window.location.href = "index.html";							
                    }
                },
               Util.ajax.dataType);
    }
    
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
    }        
    
}

Util = new Util();

