// Including libraries

var fs = require('fs'),
	app = require('http').createServer(handler),
	io = require('socket.io').listen(app),
	exec = require('child_process').exec;
	
// Predefined

var COMPILE_PATH = "compile";

// This is the port for our web server.
// you will need to go to http://localhost:8000 to see it
app.listen(8000);

var nconnections = 0;

// If the URL of the socket server is opened in a browser
function handler (request, response) {
	
	console.log("HTTP Connection: " + (++nconnections));

	request.addListener('end', function () {
        fileServer.serve(request, response); // this will return the correct file
    });
}

function removeCompiledFiles (fullname) {
	if (fullname !== undefined) {		
		console.log("Deleting compiled files...");
		var command = "rm -r " + fullname + "*"
		var child = exec(command, function (error, stdout, stderr) {
			var stdoutput = ((stdout) ? stdout : "No output"),
				stderror = ((stderr) ? stderr : "No errors!");
			console.log("STDOUT: " + stdoutput);
			console.log("STDERR: " + stderror);
			console.log("Successfully deleted compiled files.");
		});
	}
}

// Delete this row if you want to see debug messages
io.set('log level', 1);

// Listen for incoming connections from clients
io.sockets.on('connection', function (socket) {

	console.log("Socket.io Connection: " + (++nconnections));
		
	socket.on('compile', function(input) {
		compile(input);
	});
			
	socket.on('run', function (input) {
		run(input);		
	});		
	
	socket.on('disconnect', function () {
		console.log("Client has disconnected");
	})
	
	socket.on('compilerun', function (input) {
		compile(input, run);
	});
	
	/* Inner functions */
	
	// Compile the input file. Accepts an optional function for running the compiled file
	function compile (input, execute) {
		console.log("\nReceived compile request....");
		/* Parse the JSON output */
		console.log("Parsing filename from JSON...");
		var data = JSON.parse(input);
		var file = data.user + "-" + data.name + "." + data.ext;
		console.log("Parsed filename successfully.");
								
		
		/* Create a temporary unique file that is ready to be compiled */
		console.log("Writing to file...");
		// Check if the compile directory/path exists; if not, create it (must be synchronous)
		if (!fs.existsSync(COMPILE_PATH)) {
			console.log("Compilation path: '" + COMPILE_PATH + "' does not exist. Creating it...");
			fs.mkdirSync(COMPILE_PATH);
			console.log("Now, writing to file...");
		}
		fs.writeFile(COMPILE_PATH + "/" + file, data.contents, "utf-8", function (err) {
			if (err) {
				throw err;
			}
			console.log("Wrote source code to file: " + file);
			
			/* Compile the file (and optionally pipe output to a log file) */
			// TODO: Must add support for different compile commands, i.e. arguments, flags, etc.
			var command;
			if (data.ext == "c" || data.ext == "C") {
				command = "/usr/bin/gcc";
			}
			else if (data.ext == "cpp" || data.ext == "CPP") {
				command = "/usr/bin/g++";
			}
			else command = "/usr/bin/gcc"
			console.log("Compiling " + file + "...");
			// I could have set file = COMPILE_PATH + .... in the first place, but I want to avoid any potential future parsing
			var child = exec(command + " " + (COMPILE_PATH + "/" + file) + " -o " + (COMPILE_PATH + "/" + file) + ".out -Wall -pedantic", function (error, stdout, stderr) {
				var stdoutput = ((stdout) ? stdout : "No output"),
					stderror = ((stderr) ? stderr : "No compilation issues!");
				console.log("STDOUT: " + stdoutput);
				console.log("STDERR: " + stderror);
				console.log("Compilation complete.");
				
				// Emit the compilation log to the client							
				socket.emit('output', stderror);
				
				// If a function is provided, execute the file
				if (execute !== undefined) {
					run(input);
				}
			});
						
		});			
	}
	
	// Execute a compiled file
	function run (input) {
		/* Parse the JSON output */
		console.log("Parsing filename from JSON...");
		var data = JSON.parse(input);
		var file = data.user + "-" + data.name + "." + data.ext;
		console.log("Parsed filename successfully.");			
		
		// If the output file was generated, execute the file (and optionally pipe output to a log file)
		fs.exists(COMPILE_PATH + "/" + file + ".out", function (exists) {
			if (exists) {					
				console.log("Executing " + file + "...");					
				var child = exec("./" + COMPILE_PATH + "/" + file + ".out", function (error, stdout, stderr) {
					var stdoutput = ((stdout) ? stdout : "No output"),
						stderror = ((stderr) ? stderr : "No runtime issues!");
					console.log("STDOUT: " + stdoutput);
					console.log("STDERR: " + stderror);
					console.log("Execution complete.");
					
					// Emit the program's runtime output to the client
					var output = {
						"stdout" : stdoutput,
						"stderr" : stderror
					};					
					socket.emit('output', JSON.stringify(output));
					
					// Remove all created compilation files
					// TODO: Uncomment if desired
					//removeCompiledFiles(COMPILE_PATH + "/" + file);
				});
			}
			else {				
				socket.emit('output', "Error: attempting to execute a file that does not exist. Did you forget to first compile it?");
				console.log("Error: file does not exist!");
			}
		});		
	}	
});
