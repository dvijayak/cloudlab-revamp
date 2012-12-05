var response;
var username;

//**********************************
// View Labs Page
//**********************************

function viewLabsInit() {	
	var loggedUser = (new String(window.location)).split("?")[1].split("=")[1];
    username = loggedUser.split("#")[0];
	var loggedIsInstructor = (new String(window.location).split("?")[2] != undefined) ? new String(window.location).split("?")[2].split("=")[1] : undefined; 			
	
	if (checkValidResource(username) == true) {		
		alert("Security Breach: Trespasser has attempted to access a private user space!");
		// location.href = "index.html";		
		location.href = "index.html";		
		return;
	}
	else {
		if (username != undefined)	
			// document.getElementById("titlebar").childNodes[1].innerHTML += user + ": View Labs";					
			document.getElementById("userlabel").innerHTML = username + ":";					
		
		if (loggedIsInstructor) {
		
			forceElementDisplay(document.getElementById("instructorControlsToolbar"), "ON");
		
		}
		else {
			forceElementDisplay(document.getElementById("studentControlsToolbar"), "ON");
        }

		//------------------------
		// Code for loading all the user's labs 
		//------------------------
        
        projectManager.list_projects({
            "onFinish": function(response) {
                var labs = getKeys(jQuery.parseJSON(response));
                var fileListContainer = document.getElementById("fileListContainer");
                
                for (var i = 0; i < fileListContainer.childNodes.length; i++) {
                    if (fileListContainer.childNodes[i].nodeName == "UL") {
                        var ulist = fileListContainer.childNodes[i];
                        
                        for (var j = 0; j < labs.length; j++) {
                            var listitem = document.createElement("LI");			
                            var anchor = document.createElement("a");
                            anchor.setAttribute("href", "#");
                            anchor.setAttribute("onclick", "editLab(\"" + labs[j] + "\")");
                            anchor.setAttribute("value", labs[j]);
                            anchor.innerHTML = labs[j];
                            listitem.appendChild(anchor);
                            ulist.appendChild(listitem);
                        }
                        break;
                    }
                }
            }
        });
	}		
		
}

function viewFilePaneInit() {	
	
	var TextMode = require("ace/mode/text").Mode;
	window.aceViewFilePane.getSession().setMode(new TextMode());
	window.aceViewFilePane.setReadOnly(true);
    window.aceViewFilePane.setShowPrintMargin(false);
	window.aceViewFilePane.renderer.setShowGutter(false);
}

function createLab(name, type) {
    if (type == "C") {
        projectManager.create_c_project(name, {
            "onFinish": function(response) {
                return response;
            }
        });        
    }
    else if (type == "C++") {
        projectManager.create_cpp_project(name, {
            "onFinish": function(response) {
                return response;
            }
        });    
    }
    else if (type == "BB") {
        projectManager.create_bb_project(name, {
            "onFinish": function(response) {                
                return response;
            }
        });
    }
    else {
        alert("Error: invalid lab type entered. Lab will not be created.");
        return false;
    }
}

function newLab() {
	
	var newLab = {
		"name"  : prompt("Enter the name of the new lab:", "defaultLab"),
		"type"  : prompt("Enter the type of the lab (C [C], C++ [C++] or BlackBerry Web App [BB]")
	};
	
    if (createLab(newLab.name, newLab.type) != false) {
        alert("Lab " + newLab.name + " successfully created!");
    }
    else {
        alert("Error: failed to create lab " + newLab.name);
        return;
    }
    
	var fileListContainer = document.getElementById("fileListContainer");
	for (var i = 0; i < fileListContainer.childNodes.length; i++) {
		
		if (fileListContainer.childNodes[i].nodeName == "UL") {
			
			var ulist = fileListContainer.childNodes[i];
			
			// Append a new LI element that represents a new file called <FilePath> (modify code as needed for correct file name)
			var listitem = document.createElement("LI");			
			var anchor = document.createElement("a");
			anchor.setAttribute("href", "#");
			anchor.setAttribute("onclick", "editLab(\"" + newLab.name + "\")");
			anchor.setAttribute("value", newLab.name);
			anchor.innerHTML = newLab.name;
			listitem.appendChild(anchor);

			ulist.appendChild(listitem);
			break;
		}
	}
}

function deleteLab() {

	var delLab = {
		"name"  : prompt("Enter the name of the lab you would like to delete", "defaultLab")/* ,
		"users" : prompt("Enter the usernames of the students (separated by spaces only) that were assigned this lab:" */
	};
	
	var fileListContainer = document.getElementById("fileListContainer");
	
	for (var i = 0; i < fileListContainer.childNodes.length; i++) {
		
		if (fileListContainer.childNodes[i].nodeName == "UL") {
			
			var ulist = fileListContainer.childNodes[i];
			
			for (var j = 0; j < ulist.childNodes.length; j++) {												
				if (ulist.childNodes[j].nodeName == "LI" && ulist.childNodes[j].firstChild.nodeName == "A") {
					for (var k = 0; k < ulist.childNodes[j].firstChild.attributes.length; k++) {
						if (delLab.name == ulist.childNodes[j].firstChild.attributes[k].value) {						
							ulist.removeChild(ulist.childNodes[j]);
							break;						
						}
					}
				}
			}																
		}		
	}
}

function openLab(fileName) {

	// Read file contents and store in buffer (not implemented yet)
	var buffer = fileName;
		
	window.aceViewFilePane.getSession().setValue(buffer);	

}

function editLab(fileName) {
	if (fileName != null && fileName != undefined) {
        location.href = "editor.php?user=" + username + "?lab=" + fileName;
		//location.href = "editor.html?lab=" + fileName;
    }
	else
		alert("Invalid Lab!");
}
