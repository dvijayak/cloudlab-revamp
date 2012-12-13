var express = require('express');
var server = express();

server.get('/', function (req, res) {
	
	res.json(200, {
		host: req.host,
		status: "All tings irie"
	});	
});

server.post('/', function (req, res) {	
	username = req.body.username || 'Anonymous';
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
