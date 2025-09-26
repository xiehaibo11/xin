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
    static get(url, callback, is_err = 0, options = {}) {
        const { timeout = 30000, retries = 0 } = options;
        let attempt = 0;
        
        const makeRequest = () => {
            attempt++;
            let backState = {
                startTime: new Date().getTime()
            };
            
            let req = http.get(url, (res) => {
                let bufferHelper = new BufferHelper();
                let responseTimeout;
                
                // 设置响应超时
                if (timeout > 0) {
                    responseTimeout = setTimeout(() => {
                        req.abort();
                    }, timeout);
                }
                
                res.on('data', (data) => {
                    bufferHelper.concat(data);
                });
                
                res.on('timeout', () => {
                    if (responseTimeout) clearTimeout(responseTimeout);
                    let chunk = '{"err":2,"msg":"响应超时"}';
                    if (callback) callback({"data":chunk, "attempt": attempt});
                });
                
                res.on("end", () => {
                    if (responseTimeout) clearTimeout(responseTimeout);
                    var chunk = bufferHelper.toString();
                    
                    if (is_err && (!(typeof(chunk) != "undefined" && chunk.length < 100) || !chunk)) {
                        chunk = '{"err":1,"msg":"响应数据错误"}';
                    }
                    
                    var endTime = new Date().getTime();
                    backState.endTime = endTime;
                    backState.loadTime = backState.endTime - backState.startTime;
                    
                    // 检查是否需要重试
                    try {
                        const data = JSON.parse(chunk);
                        if (data.err && attempt <= retries) {
                            console.log(`请求失败，尝试重试 (${attempt}/${retries + 1}): ${url}`);
                            setTimeout(makeRequest, 1000 * attempt); // 递增延迟重试
                            return;
                        }
                    } catch (e) {
                        // 非JSON响应，继续处理
                    }
                    
                    if (callback) callback({
                        "data": chunk, 
                        "time": backState.loadTime,
                        "attempt": attempt
                    });
                });
                
                res.on('error', (e) => {
                    if (responseTimeout) clearTimeout(responseTimeout);
                    handleError(e);
                });
                
            }).on('error', (e) => {
                handleError(e);
            });
            
            // 请求超时处理
            let request_timer = setTimeout(() => {
                req.abort();
            }, timeout);
            
            req.on("abort", () => {
                clearTimeout(request_timer);
                let chunk = '{"err":2,"msg":"请求被中止"}';
                if (attempt <= retries) {
                    console.log(`请求被中止，尝试重试 (${attempt}/${retries + 1}): ${url}`);
                    setTimeout(makeRequest, 2000 * attempt);
                    return;
                }
                if (callback) callback({"data":chunk, "attempt": attempt});
            });
            
            const handleError = (e) => {
                clearTimeout(request_timer);
                console.error(`网络请求错误 (attempt ${attempt}):`, e.message);
                
                if (attempt <= retries) {
                    console.log(`网络错误，尝试重试 (${attempt}/${retries + 1}): ${url}`);
                    setTimeout(makeRequest, 2000 * attempt);
                    return;
                }
                
                let chunk = `{"err":2,"msg":"网络错误: ${e.message}"}`;
                if (callback) callback({"data":chunk, "attempt": attempt});
            };
            
            return req;
        };
        
        return makeRequest();
    }

}
module.exports = NetWork;
