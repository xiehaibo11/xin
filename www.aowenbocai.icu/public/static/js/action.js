
(function($) {
    /*
     @config.method 		  	 string   	表单提交的方式，默认POST
     @config.calback  		 function 	ajax提交成功回调 ，写了常用的回调，在常用方法里面，默认为 callback0
     @config.error    		 function  	ajax提交失败回调 ，写了常用的错误处理，在常用方法里面，默认为 error0
     return void
     */
    $.fn.formAjax = function(config) {
        var _this = $(this);
        // 初始值
        var _config = {
            method: "POST",
            callback:function () {
                alert("success");
            },
            error:function (data) {
                alert("错误："+data.error);
            }
        };
        _config = $.extend({},_config,config);
        _this.on('submit',function(){
            var __ajax_url = $(this).attr("action")
                ,__ajax_data = $(this).serialize();
            $.ajax({
                url:__ajax_url,
                type:_config.method,
                data:__ajax_data,
                dataType:"json",
                cache:false,
                success:_config.callback,
                error:_config.error
            });
            return false;
        });
    }
})((typeof Zepto == 'undefined' ? jQuery : Zepto));

// jQuery url get parameters function [获取URL的GET参数值]
// <code>
//     var GET = $.urlGet(); //获取URL的Get参数
//     var id = GET['id']; //取得id的值
// </code>
//  url get parameters
//  public
//  return array()
(function($) {
    $.urlGet = function()
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
})((typeof Zepto == 'undefined' ? jQuery : Zepto));
;
(function() {
    /**
     * 动态加载js文件
     * @param  {string}   url      js文件的url地址
     * @param  {Function} callback 加载完成后的回调函数
     */
    var _getScript = function(url, callback) {
        var head = document.getElementsByTagName('head')[0],
            js = document.createElement('script');

        js.setAttribute('type', 'text/javascript');
        js.setAttribute('src', url);

        head.appendChild(js);

        //执行回调
        var callbackFn = function(){
            if(typeof callback === 'function'){
                callback();
            }
        };

        if (document.all) { //IE
            js.onreadystatechange = function() {
                if (js.readyState == 'loaded' || js.readyState == 'complete') {
                    callbackFn();
                }
            }
        } else {
            js.onload = function() {
                callbackFn();
            }
        }
    }

    //如果使用的是zepto，就添加扩展函数
    if(typeof Zepto != 'undefined'){
        $.getScript = _getScript;
    }

})();
$(function() {
    $('a:not([data-no-cache])').attr('data-no-cache', 'true');
});
