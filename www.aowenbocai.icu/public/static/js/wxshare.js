function setConfig(data)
{
    wx.config({  
        debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。  
        appId: data.appId, // 必填，公众号的唯一标识  
        timestamp: data.timestamp, // 必填，生成签名的时间戳  
        nonceStr: data.nonceStr, // 必填，生成签名的随机串  
        signature: data.signature,// 必填，签名，见附录1  
        jsApiList: [  
            'checkJsApi',  
            'onMenuShareTimeline',  
            'onMenuShareAppMessage',  
            'onMenuShareQQ',  
            'onMenuShareWeibo',  
            'chooseWXPay'  
        ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2  
    });  
}
$.post('/index/index/getConfig','',function(res){
    var data = res;
wx.config({ 
    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。  
    appId: data.appId, // 必填，公众号的唯一标识  
    timestamp: data.timestamp, // 必填，生成签名的时间戳  
    nonceStr: data.nonceStr, // 必填，生成签名的随机串  
    signature: data.signature,// 必填，签名，见附录1  
    jsApiList: [  
        'checkJsApi',  
        'onMenuShareTimeline',  
        'onMenuShareAppMessage',  
        'onMenuShareQQ',  
        'onMenuShareWeibo',  
        'chooseWXPay'  
    ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2  
});
});
wx.ready(function(){
    wx.onMenuShareTimeline({
        title: '4444', // 分享标题
        link: 'www.aowenbocai.icu', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: '/uploads/logo.png', // 分享图标
        success: function () {
            $.alert('sucess');
        },
        cancel: function () {
            $.alert('error');
        }
    });
})
function shareToCircle(s_title, s_link, s_imgUrl) 
{
    $.post('/index/index/getConfig','',function(res){
        alert('kjjj');
        setConfig(res);
        wx.onMenuShareTimeline({
            title: '4444', // 分享标题
            link: 'www.aowenbocai.icu', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: '/uploads/logo.png', // 分享图标
            success: function () {
                $.alert('sucess');
            },
            cancel: function () {
                $.alert('error');
            }
        });
    })
}

function shareToFriend(){
    wx.onMenuShareAppMessage({
        title: '', // 分享标题
        desc: '', // 分享描述
        link: '', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: '', // 分享图标
        type: '', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        success: function () {
        // 用户确认分享后执行的回调函数
        },
        cancel: function () {
        // 用户取消分享后执行的回调函数
        }
    });
}
function shareToQq(){
    wx.onMenuShareQQ({
        title: '', // 分享标题
        desc: '', // 分享描述
        link: '', // 分享链接
        imgUrl: '', // 分享图标
        success: function () {
        // 用户确认分享后执行的回调函数
        },
        cancel: function () {
        // 用户取消分享后执行的回调函数
        }
    });
}
function shareToQZone()
{
    wx.onMenuShareQZone({
        title: '', // 分享标题
        desc: '', // 分享描述
        link: '', // 分享链接
        imgUrl: '', // 分享图标
        success: function () {
        // 用户确认分享后执行的回调函数
        },
        cancel: function () {
        // 用户取消分享后执行的回调函数
        }
    });
}