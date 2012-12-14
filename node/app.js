var net = require('net');
filePath = __dirname + '/logs.json';
var file = require('fs').createWriteStream(filePath, {flag: 'a'});

var server = net.createServer();

server.on('connection', function (socket) {
  //console.log('got a new connection');
  socket.pipe(file, {end: false});
});

server.listen(8000);