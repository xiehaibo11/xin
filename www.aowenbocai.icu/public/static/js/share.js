(function() {
    $("#share").click(function(){
       $(".screenW").show();
        $(".subW").slideToggle();
    });
    /*�ٶȷ���js*/
    window._bd_share_config = {
        "common": {
            "bdSnsKey": {},
            "bdText": "",
            "bdMini": "2",
            "bdMiniList": false,
            "bdPic": "",
            "bdStyle": "0",
            "bdSize": "16"
        },
        "share": {},
        "image": {
            "viewList": ["qzone", "tsina", "sqq", "tqq", "weixin","douban"],
            "viewText": "������",
            "viewSize": "16"
        },
        "selectShare": {
            "bdContainerClass": null,
            "bdSelectMiniList": ["qzone", "tsina", "sqq", "tqq", "weixin","douban"]
        }
    };

    with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion='+~(-new Date()/36e5)];

})();