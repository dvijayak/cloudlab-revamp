// Including libraries

var app = require('http').createServer(handler),
	io = require('socket.io').listen(app),
	exec = require('child_process').exec,
	nstatic = require('node-static'); // for serving files

// This will make all the files in the current folder
// accessible from the web
var fileServer = new  nstatic.Server('./');

// This is the port for our web server.
// you will need to go to http://localhost:8080 to see it
app.listen(8000);

var nconnections = 0;

// If the URL of the socket server is opened in a browser
function handler (request, response) {
	
	console.log("HTTP Connection: " + (++nconnections));

	request.addListener('end', function () {
        fileServer.serve(request, response); // this will return the correct file
    });
}

// Delete this row if you want to see debug messages
io.set('log level', 1);

// Listen for incoming connections from clients
io.sockets.on('connection', function (socket) {

	console.log("Socket.io Connection: " + (++nconnections));

	// Start listening for mouse move events
	socket.on('mousemove', function (data) {

		console.log(data);
		// This line sends the event (broadcasts it)
		// to everyone except the originating client.
		socket.broadcast.emit('moving', data);
	});
	
	socket.on('disconnect', function () {
		console.log("Client has disconnected");
	})
});
