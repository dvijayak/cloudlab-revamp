var http = require("http");

var server = http.createServer(function(request, response) {
	response.writeHead(200, {"Content-Type": "text/plain"});
	response.write("Hello World");
	response.end();
});
server.listen(8000);

//var net = require('net');
//var port = 8000;
//
//var s = net.createServer(
//	function (c)
//	{
//		console.log("Connection Established!");				
//
//		c.on('end',
//			function ()
//			{
//				console.log("Disconnecting Server...");
//				c.end("goodbye\r\n");
//			}
//		);
//
//		c.write('hello\r\n');
//		c.pipe(c);
//	}
//);
//
//s.listen(port,		
//    function ()
//	{
//		console.log("Listening to clients at port " + port);
//	}
//);
