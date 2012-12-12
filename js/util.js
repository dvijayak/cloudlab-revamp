function Util () {
    
    console.log("Created the Util object");

    /* Fields */
    
    // URIs/URLs to important internal and external resources    
    this.paths = new Array();
    this.paths.protocol = "http://";
    this.paths.domain = "localhost";
    this.paths.root = "/cloudlab_revamp/";
    this.paths.img = "img/";
    this.paths.js = "js/";
    this.paths.php = "php/";
    
    this.urls = new Array();    
    this.urls.server = this.paths.protocol + this.paths.domain + this.paths.root;
    this.urls.php = new Array();
    this.urls.php.main = this.urls.server + this.paths.php + "main.php";
    
    // Pre-configured AJAX settings
    this.ajax = new Array();
    this.ajax.url = this.urls.php.main;
    this.ajax.dataType = "json";    

    /* Methods */
    
    this.cookiesToArray = function () {
        var array = new Array();
        // key/value pairs in document.cookie are delimited by "; " instead of ";"
        var cookies = document.cookie.split("; ");
        var n = cookies.length;
        for (var i = 0; i < n; i++) {
            var cookie = cookies[i].split("=");        
            array[cookie[0]] = cookie[1];
        }							
        return array;
    }
    
    // Validates the session and then optionally performs a given AJAX task (with optional input data) to the same server
    this.validateSession = function (task, data) {        
        $.post(this.ajax.url,
               function (output) {
                    // Ensure that the user is logged in
                    if (output.status == "OK") {                    
                        var cookies = this.cookiesToArray();
                        
                        // Performs the AJAX task only if it was specified
                        if (task !== undefined) {
                            if (data !== undefined) {
                                $.post(this.ajax.url, data, task, this.ajax.dataType);   
                            }
                            else {
                                $.post(this.ajax.url, task, this.ajax.dataType);
                            }
                        }                    
                    }
                    // Incorrect login credentials
                    else if (output.status == "INTRUDER") {
                        alert("Back off, intruder!");
                        window.location.href = "index.html";							
                    }
                },
               this.ajax.dataType);
    }
    
}

Util = new Util();

