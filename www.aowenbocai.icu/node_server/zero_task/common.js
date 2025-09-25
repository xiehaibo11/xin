var common = {};

//元素是否在数组中
common.isInArray = function(arr,value) {
    for(var i = 0; i < arr.length; i++){
        if(value === arr[i]){
            return true;
        }
    }
    return false;
};

//对象转数组
common.objToArray = function(data) {
    let _data = [];
    for (let i in data) {
        _data.push(data[i]); //属性
    }
    return _data;
};

common.deepCopy = function(source) {
    var result={}
    for (var key in source) {
        result[key] = typeof source[key]==="object"? deepCopy(source[key]): source[key]
    }
    return result;
}

common.formatDate = function(time, format){
    var t = new Date(time); 
    var tf = function(i){return (i < 10 ? '0' : '') + i}; 
    return format.replace(/yyyy|MM|dd|HH|mm|ss/g, function(a){ 
        switch(a){ 
            case 'yyyy': 
            return tf(t.getFullYear()); 
            break; 
            case 'MM': 
            return tf(t.getMonth() + 1); 
            break; 
            case 'mm': 
            return tf(t.getMinutes()); 
            break; 
            case 'dd': 
            return tf(t.getDate()); 
            break; 
            case 'HH': 
            return tf(t.getHours()); 
            break; 
            case 'ss': 
            return tf(t.getSeconds()); 
            break; 
        }; 
    }); 
};


module.exports = common;