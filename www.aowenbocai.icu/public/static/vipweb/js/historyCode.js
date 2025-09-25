/**
 * 判断数组中重复值的个数countRepeat(arr)
 * @param {arr} arr  数组
 * @return {number} 最高重复个数
 */
function countRepeat(_arr) {
    var _res = []; //
    _arr.sort();
    for (var i = 0; i < _arr.length;) {
        var count = 0;
        for (var j = i; j < _arr.length; j++) {
            if (_arr[i] == _arr[j]) {
                count++;
            }
        }
        _res.push([_arr[i], count]);
        i += count;
    }
    var _newArr = [];
    for (var i = 0; i < _res.length; i++) {
        // console.log(_res[i][0] + "重复次数:" + _res[i][1]);
        _newArr.push(_res[i][1]);
    }

    return _newArr.sort()[_newArr.length-1]
}

//时时彩大小单双判断
function sscCodeXt(num) {
    var dx = '-'
    var ds = '-'
    if(num){
        dx = num >= 5 ? '大' : '小'
        ds = num % 2 == 1 ? '单' : '双'
    }
    return dx + ds
}
//时时彩个位、十位、后三形态
function sscStatus(codeStr) {
    var shi = ''
    var ge = ''
    var hs = ''
    var bg_color = ''
    var patrn= /.*[\u4e00-\u9fa5]+.*$/;
    if(codeStr && !patrn.test(codeStr)){
        var codeArr = codeStr.split(',')
        shi = sscCodeXt(codeArr[3])
        ge = sscCodeXt(codeArr[4])
        if(countRepeat(codeArr.slice(-3)) == 1){
            hs = '组六'}
        if(countRepeat(codeArr.slice(-3)) == 2){
            hs = '组三';
            bg_color = 'org'}
        if(countRepeat(codeArr.slice(-3)) == 3){
            hs = '豹子';
            bg_color = 'green'}
    }
    return {'shi':shi,'ge': ge, 'hs' : hs, 'bg_color' : bg_color}
}

//11选5大小比、奇偶比
function syxwStatus(codeStr) {
    var patrn= /.*[\u4e00-\u9fa5]+.*$/;
    if(codeStr && !patrn.test(codeStr)){
        var codeArr = codeStr.split(',')
        var da = 0 , xiao = 0 ,js = 0 , os = 0
        for(var i=0 ; i< codeArr.length ; i++){
            if(Number(codeArr[i]) > 5){
                da += 1
            }else {
                xiao += 1
            }
            if(Number(codeArr[i]) % 2 == 1){
                js += 1
            }else {
                os += 1
            }
        }
        return {'dx': da + ':' + xiao, 'jo' : js + ':' + os}
    }else {
        return {'dx': '', 'jo' : ''}
    }
}

//快3和值、形态、类型
function ksStatus(codeStr) {
    var patrn= /.*[\u4e00-\u9fa5]+.*$/;
    if(codeStr && !patrn.test(codeStr)){
        var codeArr = codeStr.split(',')
        var xt = '' , he = 0 , lx = ''
        for(var i=0 ; i< codeArr.length ; i++){
            he += Number(codeArr[i])
        }
        var dx = he >= 11 ? '大' : '小'
        var ds = he%2 == 1 ? '单' : '双'
        if(countRepeat(codeArr) == 1){
            lx = '三不同号'
        }
        if(countRepeat(codeArr) == 2){
            lx = '二同号'
        }
        if(countRepeat(codeArr) == 3){
            lx = '三同号'
        }
        return {'he': he, 'xt' : dx+''+ds, 'lx': lx}
    }else {
        return {'he': '', 'xt' : '', 'lx': ''}
    }
}

//计算pc28【和、形态、颜色，色波】
function pcStatus(codeStr){
    var patrn= /.*[\u4e00-\u9fa5]+.*$/;
    if(codeStr && !patrn.test(codeStr)){
        var codeArr = codeStr.split(',')
        var green_w = [1,4,7,10,16,19,22,25]
        var bule_w = [2,5,8,11,17,20,23,26]
        var red_w = [3,6,9,12,15,18,21,24]
        var gray_w = [0,13,14,27]
        var color = ''
        var text_color = ''
        var he = 0
        for(var i=0 ; i< codeArr.length ; i++){
            he += Number(codeArr[i])
        }
        if(red_w.indexOf(he) > -1){
            color = '#ff0000'
            text_color = '红波'
        }
        if(bule_w.indexOf(he) > -1){
            color = '#2388f5'
            text_color = '蓝波'
        }
        if(green_w.indexOf(he) > -1){
            color = '#12c231'
            text_color = '绿波'
        }
        if(gray_w.indexOf(he) > -1){
            color = '#999999'
            text_color = '灰波'
        }
        var dxObj = he >= 14 ? '大' : '小'
        var dsObj = he%2 == 0 ? '双' : '单'
        var jz = ''
        if(he >= 22 && he<=27 ){jz = '极大'}
        if(he >= 0 && he<=5 ){jz = '极小'}
        var bz = countRepeat(codeArr) == 3 ? '豹子' : ''
        return {'he' : he , 'xt':dxObj + ',' + dsObj,'bg_color': color,'text_color' : text_color,'jz':jz ,'bz': bz}
    }else {
        return {'he' : '' , 'xt': '','bg_color': '','text_color' : '','jz':'' ,'bz': ''}
    }
}
//pc28开奖号码转换成数组
function getCodeArr(codeStr) {
    var patrn= /.*[\u4e00-\u9fa5]+.*$/;
    if(codeStr && !patrn.test(codeStr)) {
        return codeStr.split(',')
    }else {
        return []
    }
}