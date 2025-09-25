/*用export把方法暴露出来*/
export default{
    /*设置cookie*/
    setCookie:function(c_name,value,expire){
        var date=new Date()
        date.setSeconds(date.getSeconds()+expire)
        document.cookie=c_name+ "="+escape(value)+"; expires="+date.toGMTString() + "; path=/;";
    },
    /*获取cookie*/
    getCookie:function(c_name){
        if (document.cookie.length>0){
            let c_start=document.cookie.indexOf(c_name + "=")
            if (c_start!=-1){
                c_start=c_start + c_name.length+1
                let c_end=document.cookie.indexOf(";",c_start)
                if (c_end==-1) c_end=document.cookie.length
                return unescape(document.cookie.substring(c_start,c_end))
            }
        }
        return ""
    },
    /*删除cookie*/
    delCookie:function(c_name) {
        this.setCookie(c_name, "", -1)
    },
    //*手机正则验证*/
    testTel:function (tel) {
        var reg = /^1[3-9]{1}[0-9]{9}$/;
        return reg.test(tel) ? true : false;
    },
    //*邮箱正则验证*/
    testEmail:function (email) {
        let reg = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/;
        return reg.test(email) ? true : false;
    },
    //*身份证正则验证*/
    testIdNum:function (IdNum) {
        let reg = /^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|[X|x])$/;
        return reg.test(IdNum) ? true : false;
    },
    //*QQ号码正则验证*/
    testQQnum:function (qq) {
        let reg = /^[1-9][0-9]{4,14}$/;
        return reg.test(qq) ? true : false;
    },
    //验证输入值是否为正整数
    isValueNumber : function(value) {
        var reg =  /^[1-9]+[0-9]*]*$/ ;
        return reg.test(value) ? true : false;
    },

    //检测电话号码所属
    checkMobile : function(telphone) {
        let isChinaMobile = /^134[0-8]\d{7}$|^(?:13[5-9]|147|15[0-27-9]|178|1703|1705|1706|18[2-478])\d{7,8}$/; //移动
        let isChinaUnion = /^(?:13[0-2]|145|15[56]|176|1704|1707|1708|1709|171|18[56])\d{7,8}$/; //联通
        let isChinaTelcom = /^(?:133|153|1700|1701|1702|177|173|18[019])\d{7,8}$/; // 电信
        if (telphone.toString().length !== 11) {
            return '未检测到正确的手机号码';
        } else {
            if (isChinaMobile.test(telphone)) {
                return '移动';
            } else if (isChinaUnion.test(telphone)) {
                return '联通';
            } else if (isChinaTelcom.test(telphone)) {
                return '电信';
            } else {
                return '未检测到相关运营商';
            }
        }
    },

    //判断是否是微信浏览器
    isWeiXin:function(){
        //window.navigator.userAgent属性包含了浏览器类型、版本、操作系统类型、浏览器引擎类型等信息，这个属性可以用来判断浏览器类型
        let ua = window.navigator.userAgent.toLowerCase();
        //通过正则表达式匹配ua中是否含有MicroMessenger字符串
        if(ua.match(/MicroMessenger/i) == 'micromessenger'){
            return true;
        }else{
            return false;
        }
    },

    //判断是否存在
    isExit:function (arr,string) {
        if(arr.indexOf(string) > -1){
            return true
        }else {
            return false
        }
    },

    //格式化输入值
    /**
     * 验证修改输入的小数值
     * @param item 输入的值
     * @param n 保留小数位数
     * @returns item
     * eg formateSmallNumber(4.25652,3) 4.256
     */
    formateSmallNumber : function(item,n) {
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
    },

    /**
     * 金钱格式化显示
     * @number
     * eg: formatMoney(111111111) // 111,111,111.00
     */
    formatMoney: function(number) {
        //格式化金额
        function outputdollars(number) {
            if (number.length <= 3)
                return (number == '' ? '0' : number);
            else {
                var mod = number.length % 3;
                var output = (mod == 0 ? '' : (number.substring(0, mod)));
                for (var i = 0; i < Math.floor(number.length / 3); i++) {
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
        number = number.toString().replace(/\,/g, "");
        if(isNaN(number) || number == "")return "";
        number = Math.round(number * 100) / 100;
        if (number < 0)
            return '-' + outputdollars(Math.floor(Math.abs(number) - 0) + '') + outputcents(Math.abs(number) - 0);
        else
            return outputdollars(Math.floor(number - 0) + '') + outputcents(number - 0);
    },
}



