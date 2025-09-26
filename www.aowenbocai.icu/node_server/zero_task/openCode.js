require('./time.js');
var network = require("./network.js");
var config = require("./config.js");

module.exports.createOpenCode = function(base_config) {
    this.name = base_config.name;//标识
    this.start_time = base_config. startTime;//每天开始时间
    this.space_time = base_config.timelong;//间隔时间
    this.run = function () {
        const startTime = Date.now();
        network.get(config.url + 'index/api/pushSystemCode?name=' + this.name, (res) => {
            const responseTime = Date.now() - startTime;
            console.log(`[${this.name}] 响应时间: ${responseTime}ms, 数据: ${res.data}`);
            
            var data = res.data;
            try {
                data = eval('(' + data + ')');
            } catch (error) {
                console.error(`[${this.name}] 数据解析失败:`, error);
                data = "";
            }
            
            if (data != "" && !data.err) {
                // 开奖成功，计算下次执行时间
                const nextTime = this.getNextTime(data.next_time, res.time);
                console.log(`[${this.name}] 开奖成功，${nextTime}ms后执行下次采集`);
                
                setTimeout(() => {
                    this.run();
                }, nextTime);
            } else {
                // 处理错误情况，使用指数退避策略
                var next_time = 3000; // 基础重试时间3秒
                if (data != "" && data.hasOwnProperty('next_time') && data.next_time) {
                    next_time = Math.max((data.next_time + 1) * 1000, 3000);
                } else if (data != "" && data.err) {
                    // 如果有错误信息，适当增加重试间隔
                    next_time = 5000;
                }
                
                console.log(`[${this.name}] 采集失败，${next_time}ms后重试`);
                setTimeout(() => {
                    this.run();
                }, next_time);
            }
        }, 1, {
            timeout: 15000, // 15秒超时
            retries: 2      // 重试2次
        });
    }

    //获取下次采集时间
    this.getNextTime = function (next_time = 0, time=0) {
        var my_time = (next_time + 2) * 1000 - time;
        return my_time > 0 ? my_time : 0;
        //判断今天是否采集完
        // var delay = 5 * 1000;//设置时差
        // var last_expect = String(this.last_expect);
        // var sort_day = last_expect.substr(0, 8);
        // var day =  sort_day.substr(0, 4) + "-" + sort_day.substr(4, 2) + "-" + sort_day.substr(6, 2);
        // var time = new Date();
        // var startDayTime = new Date(time.Format(day + " 00:00:00")).getTime();
        // var nowTime = time.getTime();
        //
        // var over = 0;//今天是否采集完
        // var timeOne = this.start_time.split(":");
        // var start_time = timeOne[0] * 60 * 60 * 1000 + timeOne[1] * 60 * 1000;
        //
        // if ( last_expect.substr(8) == this.total_issue && nowTime < (startDayTime + 8640000 + start_time)) {
        //     startDayTime += 24 * 60 * 60 * 1000 + start_time;
        //     over = 1;
        // }
        // if (over) {
        //     return new Date(startDayTime).getTime() + delay - time.getTime();
        // } else {
        //     return  this.space_time * 60 * 1000;
        // }
    }
}
