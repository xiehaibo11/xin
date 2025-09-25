var sendPrize = require("./sendPrize.js").createSendPrize;
var redis = require('redis');

var client = redis.createClient(6379,'localhost');


var send_data = {};

var start = function() {
    client.subscribe('prize');
    client.on('message', function(channel, msg){
        if  (!msg) return ;
        let data = msg.split('&');
        let name = data[2].split('=')[1];
        if (send_data.hasOwnProperty(name)) {
            send_data[name].init(msg);
        } else {
            send_data[name] = new sendPrize();
            send_data[name].init(msg);
        }
    });
};
start();