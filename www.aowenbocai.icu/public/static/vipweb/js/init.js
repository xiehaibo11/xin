/**
 * 获取地址？后的参数函数
 * @returns {Array}
 * @constructor
 */
$.GetUrl = function()
{
    var aQuery = window.location.href.split("?");  //取得Get参数
    var aGET = new Array();
    if(aQuery.length > 1)
    {
        var aBuf = aQuery[1].split("&");
        for(var i=0, iLoop = aBuf.length; i<iLoop; i++)
        {
            var aTmp = aBuf[i].split("=");  //分离key与Value
            aGET[aTmp[0]] = aTmp[1];
        }
    }
    return aGET;
}

/**
 * 获取指定Url参数的方法
 * @param paraName 参数
 * @returns {*}
 * @constructor GetUrlParam('id') 获取id值
 */
function GetUrlParam(paraName) {
    var url = document.location.toString();
    var arrObj = url.split("?");

    if (arrObj.length > 1) {
        var arrPara = arrObj[1].split("&");
        var arr;

        for (var i = 0; i < arrPara.length; i++) {
            arr = arrPara[i].split("=");

            if (arr != null && arr[0] == paraName) {
                return arr[1];
            }
        }
        return "";
    }
    else {
        return "";
    }
}

/**
 * 获取当前相对路径的方法
 */
function GetUrlRelativePath()
{
    var url = document.location.toString();
    var arrUrl = url.split("//");

    var start = arrUrl[1].indexOf("/");
    var relUrl = arrUrl[1].substring(start);//stop省略，截取从start开始到结尾的所有字符

    if(relUrl.indexOf("?") != -1){
        relUrl = relUrl.split("?")[0];
    }
    return relUrl;
}

/**
 * 时间戳格式化函数
 * @param  {string} format    格式
 * @param  {int}    timestamp 要格式化的时间 默认为当前时间
 * @return {string}           格式化的时间字符串
 * eg
 * format('Y-m-d','1350052653');//2012-10-11
 * format('Y-m-d H:i:s','1350052653');//2012-10-12 22:37:33
 */
function format(format, timestamp){
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
}

/**
 * 日期时间段值[开始时间，结束时间]
 * @param {int} day 日期间隔天数
 * eg: timeArr(7) //最近一周
 */
function timeArr(day) {
    var time=[];
    var nowDate= new Date();
    var end = nowDate.getTime()/1000;
    var start = (end - 3600 * 1000 * 24 * day/1000);
    time.push(format('Y-m-d',start));
    time.push(format('Y-m-d',end));
    return time;
}

/**
 * 几天的日期
 * @param  AddDayCount   获取AddDayCount天后的日期
 * @param  data   获取data日期
 * eg: getDateStr(1) //明天  getDateStr(-1) 昨天
 */

function Appendzero(obj) //补零
{
    if(obj<10){
        return "0" +""+ obj;
    }
    else{
        return obj;
    }
}
function getDateStr(data,AddDayCount) {
    var dd = new Date(data);
    dd.setDate(dd.getDate() + AddDayCount);   //获取AddDayCount天后的日期
    var year = dd.getFullYear();
    var mon = dd.getMonth()+1;                             //获取当前月份的日期
    var day = dd.getDate();
    return year + '' +  Appendzero(mon) + '' + Appendzero(day);
}
function getDateStr1(data,AddDayCount) {
    var dd = new Date(data);
    dd.setDate(dd.getDate() + AddDayCount);   //获取AddDayCount天后的日期
    var year = dd.getFullYear();
    var mon = dd.getMonth()+1;                             //获取当前月份的日期
    var day = dd.getDate();
    return year + '-' + Appendzero(mon)+ '-' + Appendzero(day);
}

/**
 * 金钱格式化显示
 * @number
 * eg: formatMoney(111111111) // 111,111,111.00
 */
// function format_number(n){
//     var b=parseFloat(n).toString();
//     var len=b.length;
//     if(len<=3){return b;}
//     var r=len%3;
//     return r>0?b.slice(0,r)+","+b.slice(r,len).match(/\d{3}/g).join(","):b.slice(r,len).match(/\d{3}/g).join(",");
// }
function formatMoney(number) {
    number = number.toString().replace(/\,/g, "");
    if(isNaN(number) || number == "")return "";
    number = Math.round(number * 100) / 100;
    if (number < 0)
        return '-' + outputdollars(Math.floor(Math.abs(number) - 0) + '') + outputcents(Math.abs(number) - 0);
    else
        return outputdollars(Math.floor(number - 0) + '') + outputcents(number - 0);
}
//格式化金额
function outputdollars(number) {
    if (number.length <= 3)
        return (number == '' ? '0' : number);
    else {
        var mod = number.length % 3;
        var output = (mod == 0 ? '' : (number.substring(0, mod)));
        for (i = 0; i < Math.floor(number.length / 3); i++) {
            if ((mod == 0) && (i == 0))
                output += number.substring(mod + 3 * i, mod + 3 * i + 3);
            else
                output += ',' + number.substring(mod + 3 * i, mod + 3 * i + 3);
        }
        return (output);
    }
}
function outputcents(amount) {
    amount = Math.round(((amount) - Math.floor(amount)) * 100);
    return (amount < 10 ? '.0' + amount : '.' + amount);
}

/**
 * 判断是否存在返回ture or false
 * @number
 * eg: isExist([1,2,3],2) // true
 */
function isExist(arr,string) {
    if(arr.indexOf(string) > -1){
        return true
    }else {
        return false
    }
}

function in_array(stringToSearch, arrayToSearch) {
    for (s = 0; s < arrayToSearch.length; s++) {
        thisEntry = arrayToSearch[s].toString();
        if (thisEntry == stringToSearch) {
            return true;
        }
    }
    return false;
}

/**
 * 验证修改输入的小数值
 * @param item 输入的值
 * @param n 保留小数位数
 * @returns item
 * eg formateSmallNumber(4.25652,3) 4.256
 */
function formateSmallNumber(item,n) {
    if (item!=null && item!=undefined) {
        //先把非数字的都替换掉，除了数字和.
        item = item.toString().replace(/[^\d.]/g, "");
        //必须保证第一个为数字而不是.
        item = item.toString().replace(/^\./g, "");
        //保证只有出现一个.而没有多个.
        item = item.toString().replace(/\.{2,}/g, "");
        //保证.只出现一次，而不能出现两次以上
        item = item.toString().replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");
        //最多保留小数点后n位
        var arr = item.split(".");
        if (arr.length > 1) item = arr[0] + '.' + (arr[1].length > 1 ? arr[1].substr(0, n) : arr[1]);
    }
    return item;
}

var cookie = {
    "setCookie": function(name, value) {
        var curDate = new Date();
        //当前时间戳
        var curTamp = curDate.getTime();
        //当前日期
        var curDay = curDate.toLocaleDateString();
        var curWeeHours = 0;
        curWeeHours = new Date(curDay).getTime() - 1;
        //当日已经过去的时间（毫秒）
        var passedTamp = curTamp - curWeeHours;
        //当日剩余时间
        var leftTamp = 24 * 60 * 60 * 1000 - passedTamp;
        var leftTime = new Date();
        leftTime.setTime(leftTamp + curTamp);
        //创建cookie
        document.cookie = name + "=" + value + ";expires=" + leftTime.toGMTString() + ";path=/";
    },
    "getCookie": function(name) {
        //name 为想要取到的键值的键名
        var reg = /\s/g;
        var result = document.cookie.replace(reg, "");
        var resultArr = result.split(";");
        for (var i = 0; i < resultArr.length; i++) {
            var nameArr = resultArr[i].split("=");
            if (nameArr[0] == name) {
                return nameArr[1];
            }
        }
    },
    "removeCookie": function(name) {
        //name为想要删除的Cookie的键名
        var oDate = new Date(); //时间对象
        oDate.setDate(new Date().getDate() - 1);
        document.cookie = name + "='';expires=" + oDate + ";path=/";
    }
    //购买
};
