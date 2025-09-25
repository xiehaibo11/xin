var schedule = require('node-schedule');
var async = require('async');
var network = require("./network.js");
var common = require("./common.js");
var config = require("./config.js");
var openCode = require("./openCode.js").createOpenCode;

var sendPrize = require("./sendPrize.js").createSendPrize;
var redis = require('redis');

var client = redis.createClient(6379,'localhost');

var config_url = [];
var err_url = [];


var tast = function () {
    let count = 0;
    let stop_for =  () => {
        return count < config_url.length;
    };
    async.whilst(stop_for, (callback) => {
        let  url = config_url[count];
        count++;
        if (common.isInArray(err_url, url)) {
            network.get(url, (res) => {
                var data = res.data;
                try {
                    data = eval('(' + data + ')');
                } catch (error) {
                    data = "";
                }
                if (data != "" && !data.err) {
                    var index = err_url.indexOf(url);
                    if (index > -1) {
                        err_url.splice(index, 1);
                    }
                    callback();
                } else {
                    callback(true);
                }
            })
        } else {
            callback();
        };
    },(err) => {
        if (err) {
            tast();
        }
    });
}

function scheduleCronstyle(){
    tast();
    schedule.scheduleJob('0 0 0 * * *', function(){
        console.log('零点开始执行任务');
        err_url = common.objToArray(common.deepCopy(config_url));
        tast();
    })
}
var send_data = {};
var start = function() {
    // let filepath = path.resolve(__dirname, '../../.env')
    // fs.readFile(filepath, 'utf8', function(err, data){
    //     var index = data.indexOf('hostname');
    //     data = data.substring(0,index)
    //     data = data.replace(/\s+/g,"");
    //     data = data.split("=");
    //     if (data[0] == 'url') {
    //         config.url = data[1];
    //     }

        var base_url = config.url;
        config_url = [
            base_url + 'index/api/openCode',//系统开奖
            base_url + 'index/api/index'//统计
        ];
        err_url = common.objToArray(common.deepCopy(config_url));
        network.get(base_url + 'index/api/getSystemLotteryConfig',function (data) {
            var res = JSON.parse(data.data);
            res = common.objToArray(res);
            for (let k in res) {
                var value = res[k];
                if (!value) continue;
                new openCode(value).run();
            }
        })
        client.subscribe('prize');
        client.on('message', function(channel, msg){
            if  (!msg) return ;
            let data = msg.split('&');
            let name = data[2].split('=')[1];
            if (send_data.hasOwnProperty(name)) {
                send_data[name].init(msg);
            } else {
                send_data[name] = new sendPrize();
                send_data[name].init(msg)
            }
        });
        scheduleCronstyle();
   // });
};
start();