const http = require("http");
http.globalAgent.maxSockets = Infinity;
const BufferHelper = require('bufferhelper');//缓存

class NetWork {
    constructor(o) {
        //初始化
        if(o) this.site = {
            url: o.url,
            refresh: o.refresh
        };
    }
    static get(url, callback, is_err = 0) {
        let backState = {
            startTime: new Date().getTime()
        };
        let req = http.get(url, (res) => {
            let bufferHelper = new BufferHelper();
            res.on('data', (data) => {
                bufferHelper.concat(data);
            });
            res.on('timeout', () => {
                clearTimeout(request_timer);
                let chunk = '{"err":2,"msg":"访问出错(超时)<01>"}';
                if (callback) callback({"data":chunk});
            });
            res.on("end", () => {
                clearTimeout(request_timer);
                var chunk = bufferHelper.toString();
                if (is_err && (!(typeof(chunk) != "undefined" && chunk.length < 100) || !chunk)) {
                    chunk = '{"err":1,"msg":"代码错误"}';
                }
                var endTime = new Date().getTime();
                backState.endTime = endTime;
                backState.loadTime = backState.endTime - backState.startTime;
                if (callback) callback({"data":chunk, "time": backState.loadTime});
            });
        }).on('error', (e) => {
            //console.log("访问出错(超时)");
            clearTimeout(request_timer);
            let chunk = '{"err":2,"msg":"访问出错(超时)<02>"}';
            console.log('错误代码');
            console.log(e.message);
            if (callback) callback({"data":chunk});
        });
        let request_timer = setTimeout(function () {
            req.abort();
        }, 150000);
        req.on("abort", () => {
            let chunk = '{"err":2,"msg":"(超时)"}';
            if (callback) callback({"data":chunk});
        });
        return req;
    }

}
module.exports = NetWork;
