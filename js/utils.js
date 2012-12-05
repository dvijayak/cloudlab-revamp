var loginElements = document.getElementsByClassName('loginElements');	
var loadingElements = document.getElementsByClassName('loadingElements');

var response;
var loggedUser; 
var loggedIsInstructor;

var validUsers = [
	{
		"user": "mdelong",
		"pass": "M",
		"role": 1
	},
	{
		"user": "dvijayak",
		"pass": "D",
		"role": 1
	},
	{
		"user": "qmahmoud",
		"pass": "Q",
		"role": 0
	}
];	

var projectTypes = ["C", "C++", "BB"];

//**********************************
// General Utilities
//**********************************
function validate(){  
    val = document.getElementById("userText").value;  
    myClass.validateEmail(val, {          
        "onFinish": function(response){              
            if(response) alert("Valid Email Address!");  
            else alert("Invalid Email Address!");  
        }  
    });  
}

/*
* Conceals or reveals the specified element depending on the request
*/
function forceElementDisplay(element, state) {

	if (state == "ON")
		element.style.display = "block";
	else if (state == "OFF")
		element.style.display = "none";	
		
}

/*
* Conceals or reveals a list of elements depending on the request
*/
function forceElementSDisplay(elements, state) {
	
	for (var i = 0; i < elements.length; i++) {		
		forceElementDisplay(elements[i], state);
	}
	
}

function checkValidResource(user) {
	
	var invalidURL = true;
	
	for (var i = 0; i < validUsers.length; i++) {
		if (user == validUsers[i].user || user == validUsers[i].user + "#") {
			invalidURL = false;
			break;
		}
	}
	
	return invalidURL;
}

function logOut() {
	
	// Routines for saving, clearing memory of current user, etc.
	// ----------	
	// --------- -
	// End
	
	// location.href = "index.html";
	location.href = "index.html";
	
}

function getKeys(dictionary) {
    if (!Object.keys) {
        Object.keys = (function () {  
            var hasOwnProperty = Object.prototype.hasOwnProperty,  
            hasDontEnumBug = !({toString: null}).propertyIsEnumerable('toString'),  
            dontEnums = [  
            'toString',  
            'toLocaleString',  
            'valueOf',  
            'hasOwnProperty',  
            'isPrototypeOf',  
            'propertyIsEnumerable',  
            'constructor'  
            ],  
            dontEnumsLength = dontEnums.length  

            return function (obj) {  
                if (typeof obj !== 'object' && typeof obj !== 'function' || obj === null) throw new TypeError('Object.keys called on non-object')  

                var result = []  

                for (var prop in obj) {  
                    if (hasOwnProperty.call(obj, prop)) result.push(prop)  
                }  

                if (hasDontEnumBug) {  
                    for (var i=0; i < dontEnumsLength; i++) {  
                        if (hasOwnProperty.call(obj, dontEnums[i])) result.push(dontEnums[i])  
                    }  
                }  
                return result  
            }  
        })()  
    }
    
    return Object.keys(dictionary);
}
