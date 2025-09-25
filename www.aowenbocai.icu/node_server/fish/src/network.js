const http = require("http");
const BufferHelper = require('bufferhelper');//缓存
class NetWork {
    constructor(o) {
        //初始化
        if(o) this.site = {
            url: o.url,
            refresh: o.refresh
        };
    }

    static get(url, callback) {
        let backState = {
            startTime: new Date().getTime()
        };

        let req = http.get(url, (res) => {
            let bufferHelper = new BufferHelper();
            res.on('data', (data) => {
                bufferHelper.concat(data);
            });
            res.on('timeout', () => {
                console.log("timeout");
            });
            res.on("end", () => {
                //console.log("访问成功");
                clearTimeout(request_timer);
                var endTime = new Date().getTime();
                backState.endTime = endTime;
                backState.loadTime = backState.endTime - backState.startTime;
                if(callback) callback({data:bufferHelper.toString(),state:backState});
            });
        }).on('error', (e) => {
            console.log("访问出错(超时)");
            let endTime = new Date().getTime();
            backState.endTime = endTime;
            backState.loadTime = backState.endTime - backState.startTime;
            callback(false);
        });
        let request_timer = setTimeout(function () {
            req.abort();
        }, 5000);
        req.on("abort", () => {
            console.log("abort close");
        });
        return req;
    }

    static getJson(url, callback) {
        NetWork.get(url, (res) => {
            try {
                res.data = JSON.parse(res.data);
            } catch(e) {
                res.data = '';
            }

            callback(res);
        });
    }

    start(callback) {
        this.callback = callback;
        this.loadNext()
    }
    load() {
        setTimeout(()=>{
            this.loadNext()
        }, this.site.refresh);
    }
    async loadNext() {
        var getData = await this.get(this.site.url); //实现同步调用async await promise
        this.callback(getData.data,getData.state);
        this.load();
        // this.get(this.site.url, (data, state) => { //异步调用
        //     this.callback(data,state);
        //     //console.log(this.site.url + "延时:" + state.loadTime + "毫秒");
        //     this.load();
        // });
    }
    get(url, callback) {
        let backState = {
            startTime: new Date().getTime()
        };

        return new Promise((resolve, reject) => {
            let req = http.get(url, (res) => {
                let bufferHelper = new BufferHelper();
                res.on('data', (data) => {
                    bufferHelper.concat(data);
                });
                res.on('timeout', () => {
                    console.log("timeout");
                });
                res.on("end", () => {
                    //console.log("访问成功");
                    clearTimeout(request_timer);
                    var endTime = new Date().getTime();
                    backState.endTime = endTime;
                    backState.loadTime = backState.endTime - backState.startTime;
                    resolve({data:bufferHelper,state:backState});
                    if(callback) callback({data:bufferHelper,state:backState});
                });
            }).on('error', (e) => {
                console.log("访问出错(超时)");
                let endTime = new Date().getTime();
                backState.endTime = endTime;
                backState.loadTime = backState.endTime - backState.startTime;
                resolve("", backState);
                //callback("",backState);
                reject();
            });
            let request_timer = setTimeout(function () {
                req.abort();
            }, 5000);
            req.on("abort", () => {
                console.log("abort close");
            });
        })
    }

    // static sync sleep(time){
    //     return new Promise((resolve, reject) => {
    //         setTimeout(() => {
    //             resolve(time);
    //         },time);
    //     });
    // }

    static time() {
        return new Date().getTime();
    };
}
// new NetWork({ url: 'http://caipiao.163.com/t/jclqmixAllWap.html?cache=' + NetWork.time(), refresh: 1 }).start((data,state)=>{
//     console.log(state)
// });
module.exports = NetWork;
