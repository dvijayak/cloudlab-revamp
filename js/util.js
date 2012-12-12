function Util () {

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
    
}

Util = new Util();

