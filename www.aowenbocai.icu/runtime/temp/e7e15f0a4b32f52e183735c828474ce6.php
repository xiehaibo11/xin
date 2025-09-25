<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:72:"/www/wwwroot/www.aowenbocai.icu/public/../app/admin/view/index/main.html";i:1758671433;s:63:"/www/wwwroot/www.aowenbocai.icu/app/admin/view/common/head.html";i:1758671433;s:67:"/www/wwwroot/www.aowenbocai.icu/app/admin/view/index/news_show.html";i:1758671433;s:63:"/www/wwwroot/www.aowenbocai.icu/app/admin/view/common/foot.html";i:1758671433;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/static/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="/static/admin/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="/static/admin/base.css"/>
    <script src="/static/js/jquery-3.2.1.min.js"></script>
    <script src="/static/bootstrap/js/bootstrap.min.js"></script>
    <script src="/static/js/bootstrap-alert.js"></script>
    <script type='text/javascript' src="//cdn.bootcss.com/vue/2.3.4/vue.js"></script>
    <title><?php echo $title; ?></title>
</head>
<body>

<script src="/static/admin/echarts.min.js"></script>

<div class="box-padding" style="padding-top: 20px">
    <div class="hello">
        <!--<div class="alert alert-info">-->
        <!--    您好！欢迎使用《彩虹游戏》系统，本系统由-->
        <!--    （<a href="http://www.kurei.cn" target="_blank">彩虹科技有限公司</a>）独立开发，软件制作权登记号：2018SR281034。-->
        <!--    任何个人或组织不得在授权允许的情况下删除、修改、拷贝本软件及其它副本上一切关于版权的信息。-->
        <!--</div>-->
        <div class="alert alert-success">
            当前系统版本号：V <?php echo $version; ?> 最新版本：V <font class="update_version">...</font>
            <a href="<?php echo Url('admin/UpdateVersion/index'); ?>" class="update_version_a" style="display: none;">升级</a>
            <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="panel-group" id="accordion">
            <div class="alert alert-danger">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion"
                       href="#collapseOne" style="font-size: 14px">
                        免责申明:（如果您已经在使用本系统即表示您已经同意以下免责申明条款(<i style="color:#337ab7;font-style: normal">点击查看详情</i>)，否则请立即停止使用本系统）
                    </a>
                </h4>
                <div id="collapseOne" class="panel-collapse collapse">
                    <div class="panel-body" style="color: #333333;padding-bottom: 0;padding-left: 0;">
                        <p>1．使用方在使用本系统时，应当遵守符合国家法律法规和社会公共利益，否则，一切法律责任均由使用方承担。</p>
                        <p>2．使用方在未取得国家相关部门的授权许可之前，对该系统的使用用途仅限于测试、实验、研究为目的，禁止用于一切商业运营。</p>
                        <p>3．使用方利用本系统所构建的任何信息内容，以及导致的任何侵犯第三方的权利纠纷和法律争议及后果，本公司对此不承担任何责任。</p>
                        <p>4．本公司仅作为本系统的提供者，不参与使用方任何方面的合作，本公司不承担使用方在使用本系统过程中的任何违法行为负责。</p>
                        <p>5. 使用方在未经本公司许可情况下，不得将本系统再次以盈利或非盈利方式出租、出售、抵押、转让或授权他人使用。</p>
                        <p>6. 使用方不得以任何获利为目的将本系统整体或在任何基础部分上发展任何衍生产品、或以第三方产品用于重新发布。</p>
                        <p>7. 使用方不得使用任何影响游戏公平、公正和损害玩家利益的第三方软件、工具、外挂等作弊程序用于本系统，因此造成的后果，本公司对此不承担任何责任。</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sum_box row">
        <div class="col-sm-6 col-md-2">
            <div class="panel panel-default sum_panel">
                <div class="panel-body p_box">
                    <div class="sum_title">
                        <div class="icon-svg svg_right" style="background-image: url(/static/admin/svg/user_group.svg);"></div>
                        注册用户
                    </div>
                    <div class="sum_num"><?php echo $regCount; ?> 人</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-2">
            <div class="panel panel-default sum_panel">
                <div class="panel-body p_box">
                    <div class="sum_title">
                        <div class="icon-svg svg_right" style="background-image: url(/static/admin/svg/user_online.svg);"></div>
                        当前在线
                    </div>
                    <div class="sum_num"><?php echo $onlineCount; ?> 人</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-2">
            <div class="panel panel-default sum_panel">
                <div class="panel-body p_box">
                    <div class="sum_title">
                        <div class="icon-svg svg_right" style="background-image: url(/static/admin/svg/use.svg);"></div>
                        今日消费
                    </div>
                    <div class="sum_num"><?php echo $statis['spend']; ?> </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-2">
            <div class="panel panel-default sum_panel">
                <div class="panel-body p_box">
                    <div class="sum_title">
                        <div class="icon-svg svg_right" style="background-image: url(/static/admin/svg/win.svg);"></div>
                        今日中奖
                    </div>
                    <div class="sum_num"><?php echo $statis['award']; ?> </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-2">
            <div class="panel panel-default sum_panel">
                <div class="panel-body p_box">
                    <div class="sum_title">
                        <div class="icon-svg svg_right" style="background-image: url(/static/admin/svg/pay.svg);"></div>
                        今日充值
                    </div>
                    <div class="sum_num"><?php echo $statis['recharge']; ?> </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-2">
            <div class="panel panel-default sum_panel">
                <div class="panel-body p_box">
                    <div class="sum_title">
                        <div class="icon-svg svg_right" style="background-image: url(/static/admin/svg/dh.svg);"></div>
                        今日兑换
                    </div>
                    <div class="sum_num"><?php echo $statis['change']; ?> </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-6">
            <ul id="myTab" class="nav nav-tabs">
                <li class="active"><a href="#tab1" data-toggle="tab">最新会员</a></li>
                <li><a href="#tab2" data-toggle="tab">动态会员</a></li>
            </ul>
            <div id="myTabContent" class="tab-content" style=" height: 420px;border: 1px solid #ddd;border-top: none;margin-bottom: 20px;">
                <div class="tab-pane fade in active" id="tab1">
                    <?php foreach($newUser as $item): ?>
                    <div class="col-md-12">
                        <div class="user_show_info">
                            <div class="user_photo">
                                <img src="<?php echo $item['photo']; ?>" alt="">
                                <span class="user_nickname"> <?php echo $item['nickname']; ?> (<span class="user_time"><?php echo $item['username']; ?></span>)</span>
                                <span class="user_city">四川 德阳</span>
                            </div>
                            <div>
                                <div class="user_city">
                                    <a href="<?php echo url('user/send_message', ['userid' => $item['id']]); ?>" class="btn btn-primary btn-xs"><img height="16" src="/static/admin/svg/send_msg.svg" alt=""> 消息</a>
                                    <a href="<?php echo url('user/info', ['userid' => $item['id']]); ?>" class="btn btn-primary btn-xs"><img height="16" src="/static/admin/svg/look2.svg" alt=""> 资料</a>
                                </div>
                                <div class="user_time">注册时间：<?php echo substr($item['create_time'],5); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="tab-pane fade" id="tab2">
                    <?php foreach($actionUser as $item): ?>
                    <div class="col-md-12">
                        <div class="user_show_info">
                            <div class="user_photo">
                                <img src="<?php echo $item['photo']; ?>" alt="">
                                <span class="user_nickname"> <?php echo $item['nickname']; ?> (<span class="user_time"><?php echo $item['username']; ?></span>)</span>
                                <span class="user_city">四川 德阳</span>
                            </div>
                            <div>
                                <div class="user_city">
                                    <a href="" class="btn btn-primary btn-xs"><img height="16" src="/static/admin/svg/send_msg.svg" alt=""> 消息</a>
                                    <a href="" class="btn btn-primary btn-xs"><img height="16" src="/static/admin/svg/look2.svg" alt=""> 资料</a>
                                </div>
                                <div class="user_time">注册时间：<?php echo substr($item['action_time'],5); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">官方公告</h3>
                </div>
                <div class="gd_news"  style="padding: 15px;height: 420px;">
                    <p class="text-center my_tip">加载中...</p>
                    <table class="table table-hover" style="display: none;"><tbody>
                        <!--<tr>-->
                            <!--<td>Tanmay</td>-->
                            <!--<td align="right">2018-06-25</td>-->
                        <!--</tr>-->
                    </tbody></table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-6">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title">最近6个小时在线人数情况</h3>
                </div>
                <div class="panel-body p_box">
                    <!-- <canvas id="myChart" style="height: 200px"></canvas> -->
                    <div id="main" style="width: 100%;height:400px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">今日消费中奖比例</h3>
                </div>
                <div class="panel-body p_box">
                    <div id="main2" class="col-sm-6" style="height: 400px"></div>
                    <div id="main3" class="col-sm-6" style="height: 400px"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:80%; height: 80%;">
        <div class="modal-content" style="height:100%;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">官方公告</h4>
            </div>
            <div class="modal-body" style=" height: calc(100% - 61px); overflow-y: scroll;">
                <div class="table_box show_gf_article">
                    <h2 style="text-align: center; font-size:22px; font-weight: bold; line-height:30px;">加载中....</h2>
                    <div class="my_bz" style="text-align: center; color:#888; font-size:15px; padding-bottom:10px;"></div>
                    <div class="my_article" style="font-size:15px; line-height:28px; padding:0 30px;">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $("#wx_btn_add").click(function () {

    });
    $(document).on('click', '.gd_news table tbody a', function () {
        var id = $(this).data('id');
        $('#myModal').modal();
        $('.show_gf_article h2').text('加载中....');
        $('.show_gf_article .my_bz').text('');
        $('.show_gf_article .my_article').html('');
        $.post('<?php echo Url("admin/UpdateVersion/get_news"); ?>', {id: id}, function(res) {
            if (res.code) {
                var data =res.data;
                $('.show_gf_article h2').text(data.title);
                $('.show_gf_article .my_bz').text('发布时间：' + data.datetime);
                $('.show_gf_article .my_article').html(data.content);
            } else {
                alert(res.msg);
            }
        }, 'json');
    })
</script>
<script>
    //获取版本更新
    $.post('<?php echo Url("admin/UpdateVersion/getVersion"); ?>', function(res) {
        if (res.code) {
            $('.update_version').text(res.version);
            if (res.version != '<?php echo $version; ?>') {
                $('.update_version_a').show();
                $.modal.confirm('<font style="color:red;">有新的版本<' + res.version + '>可升级</font>，是否立刻升级?', function () {
                    location.href = "<?php echo Url('admin/UpdateVersion/index'); ?>";
                });
            }
        }
    }, 'json');

    //获取新闻公告
    $.post('<?php echo Url("admin/UpdateVersion/get_news"); ?>', function(res) {
        if (res.code) {
            var data =res.data;
            if (data.length) {
                var html = '';
                for (let k in data) {
                    let my_v = data[k];
                    html += '<tr>\n' +
                        '        <td><a href="javascript:;" data-id="' + my_v.id + '">' + my_v.title + '</a></td>\n' +
                        '        <td align="right">' + my_v.datetime + '</td>\n' +
                        '   </tr>';
                }
                $('.gd_news table').show();
                $('.gd_news .my_tip').hide();
                $('.gd_news table tbody').html(html);
            }
        } else {
            $('.gd_news').html('<p style="text-align: center;">未授权，请联系我们授权</p> ');
        }
    }, 'json');

    var onlineECharts = function (labels, data, data2) {
        var myChart = echarts.init(document.getElementById('main'));
        // 指定图表的配置项和数据
        var option = {
            "color": [
                "rgba(75, 192, 192, .6)",
                "rgba(255, 92, 92, .6)"
            ],
            title: {
                text: '实时人数变化'
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'cross',
                    label: {
                        backgroundColor: '#6a7985'
                    }
                }
            },
            legend: {
                data: ['在线人数', '注册人数']
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: [
                {
                    type: 'category',
                    boundaryGap: false,
                    data: labels
                }
            ],
            yAxis: [
                {
                    type: 'value'
                }
            ],
            series: [
                {
                    name: '在线人数',
                    type: 'line',
                    areaStyle: { normal: {} },
                    lineStyle: {
                        normal: {
                            width: 1
                        }
                    },
                    data: data
                },
                {
                    name: '注册人数',
                    type: 'line',
                    areaStyle: { normal: {} },
                    lineStyle: {
                        normal: {
                            width: 1
                        }
                    },
                    data: data2
                }
            ]
        };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    }

    var loadCountData = function (type, callback) {
        var url = '../index/get' + type + 'Count';
        $.get('<?php echo url("'+url+'"); ?>', function (res) {
            var labels = [];
            var data = [];
            for (var i = res.length - 1; i >= 0; i--) {
                var value = res[i];
                labels.push(value.create_time.substr(11, 5));
                data.push(value.value);
            }
            callback(labels, data);
        });
    }

    loadCountData('Online', function (labels, data) {
        loadCountData('Reg', function (labels2, data2) {
            onlineECharts(labels, data, data2);
        });
    });

    var option2 = {
         "color": [
                "rgba(75, 192, 192, .6)",
                "rgba(255, 92, 92, .6)"
            ],
        backgroundColor: '#2c343c',

        title: {
            text: '消费中奖比',
            left: 'center',
            top: 20,
            textStyle: {
                color: '#ccc'
            }
        },

        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },

        visualMap: {
            show: false,
            min: 80,
            max: 600,
            inRange: {
                colorLightness: [0, 1]
            }
        },
        series: [
            {
                name: '消费中奖比',
                type: 'pie',
                radius: '50%',
                center: ['50%', '50%'],
                data: [
                    { value: "<?php echo $statis['spend']; ?>", name: '消费' },
                    { value: "<?php echo $statis['award']; ?>", name: '中奖' },
                ].sort(function (a, b) { return a.value - b.value; }),
                roseType: 'radius',
                label: {
                    normal: {
                        textStyle: {
                            color: 'rgba(255, 255, 255, 0.3)'
                        }
                    }
                },
                labelLine: {
                    normal: {
                        lineStyle: {
                            color: 'rgba(255, 255, 255, 0.3)'
                        },
                        smooth: 0.2,
                        length: 10,
                        length2: 20
                    }
                },
                itemStyle: {
                    normal: {
                        color: '#c23531',
                        shadowBlur: 200,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                },

                animationType: 'scale',
                animationEasing: 'elasticOut',
                animationDelay: function (idx) {
                    return Math.random() * 200;
                }
            }
        ]
    };
    var myChart2 = echarts.init(document.getElementById('main2'));
    myChart2.setOption(option2);
    option2.title.text = '充值兑换比';
    option2.series[0].name = '充值兑换比';
    option2.series[0].data =  [
                    { value: "<?php echo $statis['change']; ?>", name: '兑换' },
                    { value: "<?php echo $statis['recharge']; ?>", name: '充值' },
                ].sort(function (a, b) { return a.value - b.value; });
    option2.series[0].itemStyle.normal.color = 'rgba(75, 192, 192, .6)';
    var myChart3 = echarts.init(document.getElementById('main3'));
    myChart3.setOption(option2);

</script>
</body>
</html>
