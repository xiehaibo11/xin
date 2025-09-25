// 公用js
//选项卡
var setTab = function(name,cur,n){
    for(var i=1;i<=n;i++){
        var menu=$('#'+ name+i);
        var con=$("#con_"+name+"_"+i);
        var more=$("#more_"+name+"_"+i);
        if(i == cur){
            menu.addClass('active')
            con.css({ display:'block'})
        }else {
            menu.removeClass('active')
            con.css({ display:'none'})
        }
        more.css({ display:i == cur ? "block" : "none" })
    }
}
//玩游戏链接
var urlLink = function (name) {
    var nameA = name.replace("/","");
    if(nameA.indexOf('11')>-1){
        window.open('/web/Syxw/index/name/'+ name)
    }else if(nameA.indexOf('ssc')>-1){
        window.open('/web/Ssc/index/name/'+ name)
    }else if(nameA.indexOf('pk')>-1){
        window.open('/web/'+ name)
    }
}

/**
 * 获取指定Url参数的方法
 * @param paraName 参数
 * @returns {*}
 * @constructor GetUrlParam('id') 获取id值
 */
function GetUrlParam(paraName) {
    var url = decodeURI(document.location.toString());
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
