export default{
    /**
     * 时间戳格式化函数
     * @param  {string} format    格式
     * @param  {int}    timestamp 要格式化的时间 默认为当前时间
     * @return {string}           格式化的时间字符串
     * eg
     * format('Y-m-d','1350052653');//2012-10-11
     * format('Y-m-d H:i:s','1350052653');//2012-10-12 22:37:33
     */
    formatTime:function (format, timestamp){
        var a, jsdate=((timestamp) ? new Date(timestamp*1000) : new Date());
        var pad = function(n, c){
            if((n = n + "").length < c){
                return new Array(++c - n.length).join("0") + n;
            } else {
                return n;
            }
        };
        var txt_weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        var txt_ordin = {1:"st", 2:"nd", 3:"rd", 21:"st", 22:"nd", 23:"rd", 31:"st"};
        var txt_months = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var f = {
            // Day
            d: function(){return pad(f.j(), 2)},
            D: function(){return f.l().substr(0,3)},
            j: function(){return jsdate.getDate()},
            l: function(){return txt_weekdays[f.w()]},
            N: function(){return f.w() + 1},
            S: function(){return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th'},
            w: function(){return jsdate.getDay()},
            z: function(){return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0},

            // Week
            W: function(){
                var a = f.z(), b = 364 + f.L() - a;
                var nd2, nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1;
                if(b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b){
                    return 1;
                } else{
                    if(a <= 2 && nd >= 4 && a >= (6 - nd)){
                        nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31");
                        return date("W", Math.round(nd2.getTime()/1000));
                    } else{
                        return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
                    }
                }
            },

            // Month
            F: function(){return txt_months[f.n()]},
            m: function(){return pad(f.n(), 2)},
            M: function(){return f.F().substr(0,3)},
            n: function(){return jsdate.getMonth() + 1},
            t: function(){
                var n;
                if( (n = jsdate.getMonth() + 1) == 2 ){
                    return 28 + f.L();
                } else{
                    if( n & 1 && n < 8 || !(n & 1) && n > 7 ){
                        return 31;
                    } else{
                        return 30;
                    }
                }
            },

            // Year
            L: function(){var y = f.Y();return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0},
            //o not supported yet
            Y: function(){return jsdate.getFullYear()},
            y: function(){return (jsdate.getFullYear() + "").slice(2)},

            // Time
            a: function(){return jsdate.getHours() > 11 ? "pm" : "am"},
            A: function(){return f.a().toUpperCase()},
            B: function(){
                // peter paul koch:
                var off = (jsdate.getTimezoneOffset() + 60)*60;
                var theSeconds = (jsdate.getHours() * 3600) + (jsdate.getMinutes() * 60) + jsdate.getSeconds() + off;
                var beat = Math.floor(theSeconds/86.4);
                if (beat > 1000) beat -= 1000;
                if (beat < 0) beat += 1000;
                if ((String(beat)).length == 1) beat = "00"+beat;
                if ((String(beat)).length == 2) beat = "0"+beat;
                return beat;
            },
            g: function(){return jsdate.getHours() % 12 || 12},
            G: function(){return jsdate.getHours()},
            h: function(){return pad(f.g(), 2)},
            H: function(){return pad(jsdate.getHours(), 2)},
            i: function(){return pad(jsdate.getMinutes(), 2)},
            s: function(){return pad(jsdate.getSeconds(), 2)},
            //u not supported yet

            // Timezone
            //e not supported yet
            //I not supported yet
            O: function(){
                var t = pad(Math.abs(jsdate.getTimezoneOffset()/60*100), 4);
                if (jsdate.getTimezoneOffset() > 0) t = "-" + t; else t = "+" + t;
                return t;
            },
            P: function(){var O = f.O();return (O.substr(0, 3) + ":" + O.substr(3, 2))},
            //T not supported yet
            //Z not supported yet

            // Full Date/Time
            c: function(){return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P()},
            //r not supported yet
            U: function(){return Math.round(jsdate.getTime()/1000)}
        };
        let ret;
        return format.replace(/[\\]?([a-zA-Z])/g, function(t, s){
            if( t!=s ){
                // escaped
                ret = s;
            } else if( f[s] ){
                // a date function exists
                ret = f[s]();
            } else{
                // nothing special
                ret = s;
            }
            return ret;
        });
    },

    /**
     * 倒计时格式化
     * @param value
     * @returns {string}
     */
    formatDownTime:function(s) {
        var t;
        if(s > -1){
            var hour = Math.floor(s/3600);
            var min = Math.floor(s/60) % 60;
            var sec = s % 60;
            if(hour < 10) {
                t = '0'+ hour + ":";
            } else {
                t = hour + ":";
            }
            if(min < 10){t += "0";}
            t += min + ":";
            if(sec < 10){t += "0";}
            t += sec;
        }
        return t;
    },
    /**
     * 组合算法 C(m,n)
     * @param {int} m  备选数目
     * @param {int} n 选多少个
     * @return
     */
    combination:function(m , n) {
        if(m >= n){
            var mArr = [];
            var nArr = [];
            var mRes = 1;
            var nRes = 1;
            for(var i = m ;i >= m-n+1;i--) {
                mArr.push(i)
            }
            for(var i = n ;i > 1;i--) {
                nArr.push(i)
            }
            for(var j = 0;j < mArr.length; j++){
                mRes =mRes * mArr[j]
            }
            for(var j = 0;j < nArr.length; j++){
                nRes =nRes * nArr[j]
            }
            return parseInt(mRes) / parseInt(nRes);
        }else {
            return 0
        }
    },
    /**
     * 随机
     * @param {array} arr  备选数组
     * @param {int} count  随机一次选取数量
     * @return
     */
    getRandomArrayEle:function(arr, count) {
    var shuffled = arr.slice(0), i = arr.length, min = i - count, temp, index;
    while (i-- > min) {
        index = Math.floor((i + 1) * Math.random());
        temp = shuffled[index];
        shuffled[index] = shuffled[i];
        shuffled[i] = temp;
    }
        return shuffled.slice(min);
    },

    /**
     * 数组组合 所选不能重复 返回组合个数
     * eg:
     * getCount(Zuhe([1,2,3],[3,4]))
     */
    //组合方式
    addNewType:function(o,t){
        var result=[];
        for(var k=0,len=o.length;k<len;k++){
            for(var j=0,lenj=t.length;j<lenj;j++){
                var r = '"'+o[k]+ ',"'
                if(r.indexOf(t[j]+',')<0){ //添加逗号判断区分1和10
                    result.push(o[k]+','+t[j]);
                }
            }
        }
        return result;
    },
    //接受可变长数组参数
    Zuhe:function(){
        var arr=arguments[0];
        for(var i=1,len=arguments.length ; i<len ; i++){
            arr=this.addNewType(arr,arguments[i]);
        }
        return arr;
    },
    //计算组合个数
    getCount:function(result){
        return result.length
    },

    /**
     * 几天的日期
     * @param  AddDayCount   获取AddDayCount天后的日期
     * @param  data   获取data日期
     * eg: getDateStr(1) //明天  getDateStr(-1) 昨天
     */
    Appendzero:function (obj) //补零
    {
        if(obj<10) return "0" +""+ obj;
        else return obj;
    },
    getDateStr:function(data,AddDayCount) {
        var dd = new Date(data);
        dd.setDate(dd.getDate() + AddDayCount);   //获取AddDayCount天后的日期
        var year = dd.getFullYear();
        var mon = dd.getMonth()+1;                             //获取当前月份的日期
        var day = dd.getDate();
        return year + '' + this.Appendzero(mon) + '' + this.Appendzero(day);
    },

    /**
     * 获取数组中的最大值和最小值函数
     * */
    getMaxMin:function(arr,param) {
        var newArr = arr.sort(function(a,b){return a-b})
        var len = arr.length
        if (param == 'max') {
            return newArr[len - 1]
        }else if (param == 'min') {
            return newArr[0]
        }
    },

    /**
     ** 加法函数，用来得到精确的加法结果
     ** 说明：javascript的加法结果会有误差，在两个浮点数相加的时候会比较明显。这个函数返回较为精确的加法结果。
     ** 参数：arg1：第一个加数；arg2第二个加数；d要保留的小数位数（可以不传此参数，如果不传则不处理小数位数）
     ** 返回值：arg1加上arg2的精确结果
     **/
    accAdd:function(arg1, arg2) {
        arg1 = arg1.toString(), arg2 = arg2.toString();
        var arg1Arr = arg1.split("."), arg2Arr = arg2.split("."), d1 = arg1Arr.length == 2 ? arg1Arr[1] : "", d2 = arg2Arr.length == 2 ? arg2Arr[1] : "";
        var maxLen = Math.max(d1.length, d2.length);
        var m = Math.pow(10, maxLen);
        var result = Number(((arg1 * m + arg2 * m) / m).toFixed(maxLen));
        var d = arguments[2];
        return typeof d === "number" ? parseFloat(Number((result)).toFixed(d)) : parseFloat(result);
    },

    /**
     ** 减法函数，用来得到精确的减法结果
     ** 说明：javascript的减法结果会有误差，在两个浮点数相减的时候会比较明显。这个函数返回较为精确的减法结果。
     ** 调用：accSub(arg1,arg2)
     ** 返回值：arg1减去arg2的精确结果
     **/
    accSub:function(arg1, arg2) {
        return parseFloat(this.accAdd(arg1, - Number(arg2), arguments[2]));
    },

    /**
     ** 乘法函数，用来得到精确的乘法结果
     ** 说明：javascript的乘法结果会有误差，在两个浮点数相乘的时候会比较明显。这个函数返回较为精确的乘法结果。
     ** 调用：accMul(arg1,arg2)
     ** 返回值：arg1乘以 arg2的精确结果
     **/
    accMul:function(arg1, arg2) {
        var r1 = arg1.toString(), r2 = arg2.toString(), m, resultVal, d = arguments[2];
        m = (r1.split(".")[1] ? r1.split(".")[1].length : 0) + (r2.split(".")[1] ? r2.split(".")[1].length : 0);
        resultVal = Number(r1.replace(".", "")) * Number(r2.replace(".", "")) / Math.pow(10, m);
        var num = resultVal.toFixed(d + 1).toString()
        return typeof d !== "number" ? parseFloat(Number(resultVal)) : parseFloat(num.substring(0,num.lastIndexOf('.')+d)) ;
        // return typeof d !== "number" ? Number(resultVal) : Number(resultVal.toFixed(parseInt(d)));
    },

    /**
     ** 除法函数，用来得到精确的除法结果
     ** 说明：javascript的除法结果会有误差，在两个浮点数相除的时候会比较明显。这个函数返回较为精确的除法结果。
     ** 调用：accDiv(arg1,arg2)
     ** 返回值：arg1除以arg2的精确结果
     **/
    accDiv:function(arg1, arg2) {
        var r1 = arg1.toString(), r2 = arg2.toString(), m, resultVal, d = arguments[2];
        m = (r2.split(".")[1] ? r2.split(".")[1].length : 0) - (r1.split(".")[1] ? r1.split(".")[1].length : 0);
        resultVal = Number(r1.replace(".", "")) / Number(r2.replace(".", "")) * Math.pow(10, m);
        var num = resultVal.toFixed(d + 1).toString()
        return typeof d !== "number" ? parseFloat(Number(resultVal)) : parseFloat(num.substring(0,num.lastIndexOf('.')+d));
    },

    /**
     * @param obj   要clone的对象
     * @return      clone对象
     */
    deepCoby: function (obj) {
        // Handle the 3 simple types, and null or undefined
        if (null == obj || "object" != typeof obj)
            return obj;
        // Handle Date
        if (obj instanceof Date) {
            var copy = new Date();
            copy.setTime(obj.getTime());
            return copy;
        }

        // Handle Array
        if (obj instanceof Array) {
            var copy = [];
            for (var i = 0, len = obj.length; i < len; ++i) {
                copy[i] = this.deepCoby(obj[i]);
            }
            return copy;
        }
        // Handle Object
        if (obj instanceof Object) {
            var copy = {};
            for (var attr in obj) {
                if (obj.hasOwnProperty(attr)) copy[attr] = this.deepCoby(obj[attr]);
            }
            return copy;
        }
        throw new Error("Unable to copy obj! Its type isn't supported.");
    },
    /**
     * @param  var _selfs = new Array(8); //新数组每位的个数
     var _arr = [1,2,3,4,5,6,7,8,9,10]; //备选数组
     var _indexs = [0,1,2,3,4,5,6,7,8]; //新组合数组下标0,1,2
     var _where = 0; //起始
     var _total = []; //新组合结果
     eg: plzh(_selfs, _arr, _indexs, _total, _where);
     * @return _total //新组合结果
     */
    plzh : function(_selfs, _arr, _indexs, _total, _where) {
    if (_selfs != null && _arr != null && _arr.length >= _selfs.length) {
        if (_where < _selfs.length - 1) { //非末位
            var index = _indexs[_where];
            if (index == _arr.length) { //非末位末尾
                --_where;
                if (_where == -1) { //首位超出
                    return;
                } else {
                    _indexs[_where] = _indexs[_where] + 1;
                    for (var i = _where + 1; i < _selfs.length; i++) {
                        _indexs[i] = _indexs[i - 1] + 1;
                    }
                    this.plzh(_selfs, _arr, _indexs, _total, _where);
                }
            } else {
                _selfs[_where] = _arr[index];
                this.plzh(_selfs, _arr, _indexs, _total, ++_where);
            }
        } else { //末位
            var index = _indexs[_where];
            if (index == _arr.length) {  //直接末位末尾
                --_where;
                if (_where == -1) { //末位超出即 单关
                    return;
                }
                _indexs[_where] = _indexs[_where] + 1;
                for (var i = _where + 1; i < _selfs.length; i++) {
                    _indexs[i] = _indexs[i - 1] + 1;
                }
                this.plzh(_selfs, _arr, _indexs, _total, _where);
            } else {
                _selfs[_where] = _arr[index];
                _total.push(this.deepCoby(_selfs));
                var _nextIndex = _indexs[_where] + 1;
                if (_nextIndex < _arr.length) {
                    _indexs[_where] = _nextIndex;
                    this.plzh(_selfs, _arr, _indexs, _total, _where);
                } else { //下一个末位末尾
                    --_where;
                    if (_where == -1) {
                        return;
                    }
                    _indexs[_where] = _indexs[_where] + 1;
                    for (var i = _where + 1; i < _selfs.length; i++) {
                        _indexs[i] = _indexs[i - 1] + 1;
                    }
                    this.plzh(_selfs, _arr, _indexs, _total, _where);
                }
            }
        }
    }
},
    /**
     * 递归算法，m选n所有组合方式
     * @param {any} headIndex 当前所要添加字符串在原始字符串中的index
     * @param {any} length 当前字符串长度
     * @param {arr} inArr 原始数组
     * @param {str} res 当前字符串
     * @param {str} n 所要组合的数组长度
     * @param {arr} list 结果集合
     */
    zucaiPlzh:function (headIndex, length, inArr, res, n, list) {
        var s = res;
        for (var i = headIndex; i < inArr.length + length - n; i++) {
            if (length <= n) {
                res += inArr[i] + '|';
                this.zucaiPlzh(i + 1, length + 1, inArr, res, n, list);
                if (length == n) {
                    list.push(res.substring(0, res.length - 1));
                }
            } else {
                return
            }
            res = s;
        }
    },

    /**
     * 二维数组排列组合（足球投注赛事拆分）
     * @param arr
     * @returns {Array}
     */
    serialArray:function(arr){
        var lengthArr = [];
        var productArr = [];
        var result = [];
        var length = 1;
        for(var i = 0; i < arr.length; i++){
            var len = arr[i].length;
            lengthArr.push(len);
            var product = i === 0 ? 1 : arr[i - 1].length * productArr[i - 1];
            productArr.push(product);
            length *= len;
        }
        for(var i = 0; i < length; i++){
            var resultItem = '';
            for(var j = 0; j < arr.length ; j ++){
                resultItem += arr[j][Math.floor(i / productArr[j]) % lengthArr[j]] + '&';
            }
            result.push(resultItem.substring(0, resultItem.lastIndexOf('&')));
        }
        return result
    },
}
