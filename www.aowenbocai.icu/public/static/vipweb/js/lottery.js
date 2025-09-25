/**
 * ---彩票投注相关--
 */

/**
 * 组合算法 C(m,n)
 * @param {int} m  备选数目
 * @param {int} n 选多少个
 * @return
 */
function combination(m , n) {
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
}

/**
 * 随机
 * @param {array} arr  备选数组
 * @param {int} count  随机一次选取数量
 * @return
 */
function getRandomArrayEle(arr, count) {
    var shuffled = arr.slice(0), i = arr.length, min = i - count, temp, index;
    while (i-- > min) {
        index = Math.floor((i + 1) * Math.random());
        temp = shuffled[index];
        shuffled[index] = shuffled[i];
        shuffled[i] = temp;
    }
    return shuffled.slice(min);
}

/**
 * 数组组合 所选不能重复 返回组合个数
 * eg:
 * getCount(Zuhe([1,2,3],[3,4]))
 */
//组合方式
function addNewType(o,t){
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
};
//接受可变长数组参数
function Zuhe(){
    var arr=arguments[0];
    for(var i=1,len=arguments.length ; i<len ; i++){
        arr=addNewType(arr,arguments[i]);
    }
    return arr;
};
//计算组合个数
function getCount(result){
    return result.length
}

/**
 * 获取数组中的最大值和最小值函数
 * */
function getMaxMin(arr,param) {
    var newArr = arr.sort(function(a,b){return a-b})
    var len = arr.length
    if (param == 'max') {
        return newArr[len - 1]
    }else if (param == 'min') {
        return newArr[0]
    }
}
/**
 ** 加法函数，用来得到精确的加法结果
 ** 说明：javascript的加法结果会有误差，在两个浮点数相加的时候会比较明显。这个函数返回较为精确的加法结果。
 ** 参数：arg1：第一个加数；arg2第二个加数；d要保留的小数位数（可以不传此参数，如果不传则不处理小数位数）
 ** 返回值：arg1加上arg2的精确结果
 **/
function accAdd(arg1, arg2) {
    arg1 = arg1.toString(), arg2 = arg2.toString();
    var arg1Arr = arg1.split("."), arg2Arr = arg2.split("."), d1 = arg1Arr.length == 2 ? arg1Arr[1] : "", d2 = arg2Arr.length == 2 ? arg2Arr[1] : "";
    var maxLen = Math.max(d1.length, d2.length);
    var m = Math.pow(10, maxLen);
    var result = Number(((arg1 * m + arg2 * m) / m).toFixed(maxLen));
    var d = arguments[2];
    return typeof d === "number" ? parseFloat(Number((result)).toFixed(d)) : parseFloat(result);
}

//给Number类型增加一个add方法，调用起来更加方便 (0.1).add(0.2)。
Number.prototype.add = function (arg) {
    return parseFloat(accAdd(arg, this));
};

/**
 ** 减法函数，用来得到精确的减法结果
 ** 说明：javascript的减法结果会有误差，在两个浮点数相减的时候会比较明显。这个函数返回较为精确的减法结果。
 ** 调用：accSub(arg1,arg2)
 ** 返回值：arg1减去arg2的精确结果
 **/
function accSub(arg1, arg2) {
    return parseFloat(accAdd(arg1, - Number(arg2), arguments[2]));
}

// 给Number类型增加一个sub方法，调用起来更加方便 (0.2).sub(1)。
Number.prototype.sub = function (arg) {
    return parseFloat(accSub(arg, this));
};

/**
 ** 乘法函数，用来得到精确的乘法结果
 ** 说明：javascript的乘法结果会有误差，在两个浮点数相乘的时候会比较明显。这个函数返回较为精确的乘法结果。
 ** 调用：accMul(arg1,arg2)
 ** 返回值：arg1乘以 arg2的精确结果
 **/
function accMul(arg1, arg2) {
    var r1 = arg1.toString(), r2 = arg2.toString(), m, resultVal, d = arguments[2];
    m = (r1.split(".")[1] ? r1.split(".")[1].length : 0) + (r2.split(".")[1] ? r2.split(".")[1].length : 0);
    resultVal = Number(r1.replace(".", "")) * Number(r2.replace(".", "")) / Math.pow(10, m);
    var num = resultVal.toFixed(d + 1).toString()
    return typeof d !== "number" ? parseFloat(Number(resultVal)) : parseFloat(num.substring(0,num.lastIndexOf('.')+d)) ;
    // return typeof d !== "number" ? Number(resultVal) : Number(resultVal.toFixed(parseInt(d)));
}

// 给Number类型增加一个mul方法，调用起来更加方便。保留4位小数
Number.prototype.mul = function (arg) {
    var num = accMul(arg, this).toFixed(6).toString()
    return parseFloat(num.substring(0,num.lastIndexOf('.')+5));
};

/**
 ** 除法函数，用来得到精确的除法结果
 ** 说明：javascript的除法结果会有误差，在两个浮点数相除的时候会比较明显。这个函数返回较为精确的除法结果。
 ** 调用：accDiv(arg1,arg2)
 ** 返回值：arg1除以arg2的精确结果
 **/
function accDiv(arg1, arg2) {
    var r1 = arg1.toString(), r2 = arg2.toString(), m, resultVal, d = arguments[2];
    m = (r2.split(".")[1] ? r2.split(".")[1].length : 0) - (r1.split(".")[1] ? r1.split(".")[1].length : 0);
    resultVal = Number(r1.replace(".", "")) / Number(r2.replace(".", "")) * Math.pow(10, m);
    var num = resultVal.toFixed(d + 1).toString()
    return typeof d !== "number" ? parseFloat(Number(resultVal)) : parseFloat(num.substring(0,num.lastIndexOf('.')+d));
}

//给Number类型增加一个div方法，调用起来更加方便。
Number.prototype.div = function (arg) {
    var num = accDiv(this, arg).toFixed(6).toString()
    return parseFloat(num.substring(0,num.lastIndexOf('.')+5));
}
