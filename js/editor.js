var username;
var project;
var type;
var fileArray;

//**********************************
// Code Editor
//**********************************

function editorInit() {

	username = (new String(window.location)).split("?")[1].split("=")[1];
    project  = (new String(window.location)).split("?")[2].split("=")[1];
    project = project.split("#")[0];
	
	/*if (checkValidResource(user) == true) {		
		// alert("Security Breach: Trespasser has attempted to access a private user space!");
		// location.href = "index.html";		
		// return;
	}
	else {*/
		
		window.aceEditor.setTheme("ace/theme/eclipse");
		var DefaultMode = require("ace/mode/c_cpp").Mode;
		window.aceEditor.getSession().setMode(new DefaultMode());
		window.aceEditor.insert("");	
		window.aceEditor.setShowPrintMargin(false);				
			
		/*if (user != undefined) 		
			// document.getElementById("titlebar").childNodes[1].innerHTML += user + ": View Labs";					
			document.getElementById("userlabel").innerHTML = user + ":";	
	
	}	*/
}

function consoleInit() {	
	window.aceConsole.setTheme("ace/theme/vibrant_ink");
	var TextMode = require("ace/mode/text").Mode;
	window.aceConsole.getSession().setMode(new TextMode());	
	var text = username + "@cloud-lab$> ";
	window.aceConsole.insert(text);	
	window.aceConsole.setReadOnly(true);
    window.aceConsole.setShowPrintMargin(false);
}

function getAllFiles() {
    fileManager.get_source_files(username, project, {
        "onFinish": function(response){
            fileArray = jQuery.parseJSON(response);
            var filenames = getKeys(fileArray);
            
            e = document.getElementById("fileList");
            
            ///////////////////
            // This code needs to be changed to correct way of modifying HTML; append elements instead
            ///////////////////
            var file_html = "";
            for (var i = 0; i < filenames.length; i++) {
            
                if ((i == 0) && (filenames[i] != "type.txt")) {
                    file_html += "<li class=\"highlight\">" + filenames[i] + "</li>\n";
                    window.aceEditor.getSession().setValue(fileArray[filenames[i]]);
                }
                
                else if (filenames[i] != "type.txt") {
                    file_html += "<li>" + filenames[i] + "</li>\n";
                }
            }
            
            e.innerHTML = file_html;
            
            type = fileArray["type.txt"];
            var newFiles = document.getElementById("file_types");
            file_html = "";
            
            /////////////////////////
            // This code needs to be changed to correctly modify the HTML; append elements instead
            ////////////////////////
            if (type == "C") {
                file_html += "<li><a href=\"#\" onclick='newFile(\"c\")'>C Source</a></li>";
                file_html += "<li><a href=\"#\" onclick='newFile(\"h\")'>C Header</a></li>";
            }
            else if (type == "CPP") {
                file_html += "<li><a href=\"#\" onclick='newFile(\"cpp\")'>CPP Source</a></li>";
                file_html += "<li><a href=\"#\" onclick='newFile(\"hpp\")'>CPP Header</a></li>";
            }
            else if (type == "BB") {
                file_html += "<li><a href=\"#\" onclick='newFile(\"html\")'>HTML Document</a></li>";
                file_html += "<li><a href=\"#\" onclick='newFile(\"js\")'>Javascript Source</a></li>";
                file_html += "<li><a href=\"#\" onclick='newFile(\"css\")'>CSS Stylesheet</a></li>";
            }
    
            newFiles.innerHTML = file_html;
            
            // get rid of this eventually
            $('.file_list li').click(function() {
                fileArray[$('.highlight').text()] = window.aceEditor.getSession().getValue();
                $('.highlight').removeClass('highlight');
                $(this).addClass('highlight');
                window.aceEditor.getSession().setValue(fileArray[$(this).text()]);
            });
        }
    });
}

function setupEnvironment() {
    fileManager.checkIfEdited(username, project, {});
    getAllFiles();
    //setTimeout(function() {getAllFiles();}, 1000);
    //compileManager.create_compile_dir(username, project, {"onFinish": function(response){alert("done, mf"); alert(response);}});
}

function overlay(element) {
	e = document.getElementById(element);
	e.style.display = (e.style.display == "block") ? "none" : "block";			
}

function newFile(ext) {	
	var newFile = prompt("Enter the name of your new file:", "Untitled");
    
    if (ext == "c") {
        fileManager.create_c_source(username, project, newFile, {"onFinish": function(response){addNewFile(newFile + "." + ext);}});
    }
    else if (ext == "cpp") {
        fileManager.create_cpp_source(username, project, newFile, {"onFinish": function(response){addNewFile(newFile + "." + ext);}});
    }
    else if (ext == "h") {
        fileManager.create_h_source(username, project, newFile, {"onFinish": function(response){addNewFile(newFile + "." + ext);}});
    }
    else if (ext == "hpp") {
        fileManager.create_hpp_source(username, project, newFile, {"onFinish": function(response){addNewFile(newFile + "." + ext);}});
    }
    else if (ext == "html") {
        fileManager.create_html_source(username, project, newFile, {"onFinish": function(response){addNewFile(newFile + "." + ext);}});
    }
    else if (ext == "js") {
        fileManager.create_js_source(username, project, newFile, {"onFinish": function(response){addNewFile(newFile + "." + ext);}});
    }
    else if (ext == "css") {
        fileManager.create_css_source(username, project, newFile, {"onFinish": function(response){addNewFile(newFile + "." + ext);}});
    }
}

function addNewFile(name) {
    var fileListContainer = document.getElementById("filePane");

    for (var i = 0; i < fileListContainer.childNodes.length; i++) {
		if (fileListContainer.childNodes[i].nodeName == "UL") {
			fileArray[$('.highlight').text()] = window.aceEditor.getSession().getValue();
            $('.highlight').removeClass('highlight');
            
			var ulist = fileListContainer.childNodes[i];
			var listitem = document.createElement("LI");			
			listitem.setAttribute("class", "highlight");
			listitem.innerHTML = name;
			ulist.appendChild(listitem);
            getFile(name);
			break;
		}
	}    

    // get rid of this eventually; use onclick if possible
    $('.file_list li').click(function() {
        fileArray[$('.highlight').text()] = window.aceEditor.getSession().getValue();
        $('.highlight').removeClass('highlight');
        $(this).addClass('highlight');
        window.aceEditor.getSession().setValue(fileArray[$(this).text()]);
    });
}

function getFile(name) {
    fileManager.get_source_file(username, project, name, {
        "onFinish": function(response){
            fileArray[name] = response;
            window.aceEditor.getSession().setValue(fileArray[name]);
        }
    });
}

function saveFile() {
    fileArray[$('.highlight').text()] = window.aceEditor.getSession().getValue();
    data = fileArray[$('.highlight').text()];
    fileManager.save_file(project, username + "/" + $('.highlight').text(), data, {          
        "onFinish": function(response){}});
}

function saveAllFiles() {
    var filenames = getKeys(fileArray);
    fileArray[$('.highlight').text()] = window.aceEditor.getSession().getValue();
    for (var i = 0; i < filenames.length; i++) {
        fileManager.save_file(project, username + "/" + filenames[i], fileArray[filenames[i]], {          
            "onFinish": function(response){}}); 
    }
}

function deleteFile() {
    fileManager.delete_file(username, project, $('.highlight').text(), {
        "onFinish": function(response){
            var filenames = getKeys(fileArray);
            var flist = document.getElementById("fileList");
            var listitem = flist.childNodes[0];
            
            delete fileArray[$('.highlight').text()];
            for (var j = 0; j < flist.childNodes.length; j++) {											
				if (flist.childNodes[j].nodeName == "LI") {
					if (flist.childNodes[j].innerHTML == $('.highlight').text()) {
                        flist.removeChild(flist.childNodes[j]);
                        break;
                    }
				}
			}

            listitem.setAttribute("class", "highlight");
            window.aceEditor.getSession().setValue(fileArray[$('.highlight').text()]);
        }
    });
}

function compileSource() {
    var results = "";
    var filenames = getKeys(fileArray);
    var sourcecode = "";
    
    for (var i = 0; i < filenames.length; i++) {
    
        if (filenames[i] != "type.txt") {
            sourcecode += filenames[i] + " ";
        }
    }
    
    if (type == "C") {
        window.aceConsole.setReadOnly(false);
        window.aceConsole.insert("gcc " + sourcecode + "-o main -Wall\n");
        window.aceConsole.setReadOnly(true);
        //results = setTimeout(function() {compileManager.compile_c_source(username, project);}, 3000);
        window.aceConsole.setReadOnly(false);
        window.aceConsole.insert(results + "\n" + username + "@cloud-lab$> ");
        window.aceConsole.setReadOnly(true);
    }
    else if (type == "CPP") {
        window.aceConsole.setReadOnly(false);
        window.aceConsole.insert("g++ " + sourcecode + "-o main -Wall\n");
        window.aceConsole.setReadOnly(true);
        //results = setTimeout(function() {compileManager.compile_cpp_source(username, project);}, 3000);
        window.aceConsole.setReadOnly(false);
        window.aceConsole.insert(results + "\n" + username + "@cloud-lab$> ");
        window.aceConsole.setReadOnly(true);
    }
    else if (type == "BB") {
        //setTimeout(function() {getAllFiles(username project);}, 3000);
        alert("Blackberry compilation not implemented.");
    }
}

function charCount() {
	var text = window.aceEditor.getSession().getValue();
	alert("The total number of characters in your code is " + text.length + " chars.");
}

function applyChanges() {

 	var mode = (require(document.getElementById("mode").value)).Mode;	
	window.aceEditor.getSession().setMode(new mode()); 
	
	var theme = document.getElementById("theme").value;
	window.aceEditor.setTheme(theme);
	
	var fontSize = document.getElementById("fontsize").value;	
	document.getElementById("editor").style.fontSize = fontSize;

	var toggle = document.getElementById("highlight_active").checked;		
	window.aceEditor.setHighlightActiveLine(toggle);	
	
	toggle = document.getElementById("show_gutter").checked;
	window.aceEditor.renderer.setShowGutter(toggle);
}
