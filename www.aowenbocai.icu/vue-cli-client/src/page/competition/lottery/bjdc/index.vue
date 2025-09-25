<template>
    <div>
        <div class="lottery-loading" v-if="loading">
            <mt-spinner :type="3"></mt-spinner>
        </div>
        <div v-if="!loading" class="jc-lottery-box">
            <stop-sale-tips :is-stop="isStop"></stop-sale-tips>
            <!--顶部-->
            <div class="head">
                <mt-header>
                    <mt-button icon="back" slot="left" @click.native="goHome"></mt-button>
                    <div class="tr" slot="right">
                        <span class="filter-icon" @click="filterShow">
                            <i class="iconfont icon-shaixuan f-large"></i>
                            <i class="dian" v-show="isFilter"></i>
                        </span>
                        <span><mt-button icon="more" @click.native = "showMore = !showMore"></mt-button></span>
                    </div>
                </mt-header>
                <!--玩法选择-->
                <div class="select flex-box">
                    <span class="f-mini label">玩法</span>
                    <span class="chose" @click="show = !show">{{selected}}<i class="iconfont icon-jiantou yellow" :class="show ? 'is-active':'no-active'"></i></span>
                </div>
                <div class="layout z-2 contentH top-1-select" @click.self="show = false" v-show="show" :style="{height:selctHeight + 'px'}">
                    <div class="select-grounp">
                        <ul class="clearfloat">
                            <template v-for="(item,index) in play">
                                <li @click="chosePlay(item.path)">
                                    <a :class="{'active':type == item.type}">{{item.name}}</a>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <!--玩法选择 end-->
                <!--右侧下拉-->
                <transition name="fade">
                    <div class="layout z-1" @click.self="showMore = false" v-show="showMore">
                        <div class="head-list border-1px">
                            <ul >
                                <li class="border-bottom-1px">返回首页</li>
                                <li class="border-bottom-1px" @click="toPlayNews">玩法介绍</li>
                            </ul>
                        </div>
                    </div>
                </transition>
                <!--右侧下拉 end-->
            </div>
            <!--顶部 end-->

            <!--无相关赛事-->
            <template v-if="!matchInfo.length">
                <div class="jc-empty tc" ref="wrapper">
                    <p class="ft">
                        <svg slot="icon" class="icon" aria-hidden="true">
                            <use xlink:href="#icon-zanwubisai"></use>
                        </svg>
                    </p>
                    <p class="tc c-4">暂无相关赛事</p>
                </div>
            </template>
            <!--有相关赛事-->
            <template v-else>
                <!--选号区-->
                <div class="bet-cont contentH" :style="{'height': betHeight + 'px'}" ref="wrapper" @scroll="handleScroll()">
                    <div class="jc-bet-cont">
                        <!--filterData(val)筛选赛事-->
                        <template v-for="(item,index) in matchInfo">
                            <!--赛事日期头部-->
                            <div class="jc-head tc f-sm border-bottom-1px card" :class="{isFixed : barFixed == index}"
                                 :key="index"
                                 @click="handleSlide(index)"
                                 v-show="filterData(item.matchdata).length">
                                <span>{{item.matchtime}} {{item.matchday}} {{filterData(item.matchdata).length}}场比赛可投</span>
                                <i class="iconfont icon-xialajiantou" :class="openState(index) ? 'is-active' : 'no-active'"></i>
                            </div>
                            <!--赛事列表-->
                            <div ref="matchWrapper">
                                <div v-show="openState(index)">
                                    <template v-for="(match,i) in filterData(item.matchdata)">
                                        <div class="border-bottom-1px" :key="match.id">
                                            <!--让球胜平负-->
                                            <template>
                                                <div class="border-bottom-1px">
                                                    <div class="flex-box jc-item">
                                                        <div class="jc-item-left" @click="infoSlide(match.id)" :class="{'fx-mt-sm':path=='bf'||path=='bqc'||path=='sf'||path=='spf'}">
                                                            <p>{{match.ordernum}}</p>
                                                            <p>{{match.league}}</p>
                                                            <p>{{match.zyggendtime | timeStr}}截止</p>
                                                            <p class="c-3">分析<i class="iconfont icon-xialajiantou rotateIcon" :class="infoState(match.id) ? 'is-active' : 'no-active'"></i></p>
                                                        </div>
                                                        <div class="jc-item-right flex">
                                                            <template v-if="path == 'spf'|| path == 'sf'">
                                                                <div class="cf zucai-bet-item" style="padding: 10px 0" v-if="path == 'spf'">
                                                                    <div class="fl bet-win item" :class="{'active' : curOpt(match.id,'101','3')}" @click="selectDone(match.id,'101','3',match.pl[0])">
                                                                        <p>{{match.homesxname}} <em class="f-sm" :class="match.rangqiu > 0 ? 'red' : match.rangqiu == 0 ? 'c-3': 'suc'">(<em v-if="match.rangqiu>0">+</em>{{match.rangqiu}})</em></p>
                                                                        <p><em class="pl">主胜{{match.pl[0]}}</em></p>
                                                                    </div>
                                                                    <div class="fl bet-draw item" :class="{'active' : curOpt(match.id,'101','0')}" @click="selectDone(match.id,'101','0',match.pl[1])">
                                                                        <p>vs</p>
                                                                        <p><em class="pl">平{{match.pl[1]}}</em></p>
                                                                    </div>
                                                                    <div class="fl bet-lost item" :class="{'active' : curOpt(match.id,'101','1')}" @click="selectDone(match.id,'101','1',match.pl[2])">
                                                                        <p>{{match.guestsxname}}</p>
                                                                        <p><em class="pl">客胜{{match.pl[2]}}</em></p>
                                                                    </div>
                                                                </div>
                                                                <div class="cf zucai-bet-item" style="padding: 10px 0" v-if="path == 'sf'">
                                                                    <div class="fl bet-win item" style="width: 43%" :class="{'active' : curOpt(match.id,'102','3')}" @click="selectDone(match.id,'102','3',match.pl[0])">
                                                                        <p>{{match.homesxname}}</p>
                                                                        <p><em class="pl">主胜{{match.pl[0]}}</em></p>
                                                                    </div>
                                                                    <div class="fl bet-draw" style="width: 11%;text-align: center;line-height: 54px">vs</div>
                                                                    <div class="fl bet-lost item" style="width: 43%"  :class="{'active' : curOpt(match.id,'102','1')}" @click="selectDone(match.id,'102','1',match.pl[2])">
                                                                        <p>{{match.guestsxname}}</p>
                                                                        <p><em class="pl">客胜{{match.pl[2]}}</em></p>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                            <template v-else>
                                                                <div class="dang-mteam">
                                                                    <span class="itm-team-l"><i class="c-4">主</i>{{match.homesxname}}</span>
                                                                    <span class="itm-vs">VS</span>
                                                                    <span class="itm-team-r">{{match.guestsxname}}</span>
                                                                </div>
                                                                <!--进球数-->
                                                                <template v-if="path == 'jqs'">
                                                                    <div class="betsel-list-wrap flex-box">
                                                                        <div class="betbtns-wrap flex">
                                                                            <div class="betbtns">
                                                                                <div class="betbtn-pl-item flex-box">
                                                                                    <span class="flex border-r border-b" :class="{'active' : curOpt(match.id,'103','0')}" @click="selectDone(match.id,'103','0',match.pl[0])">0<i class="jc-binding">{{match.pl[0]}}</i></span>
                                                                                    <span class="flex border-r border-b" :class="{'active' : curOpt(match.id,'103','1')}" @click="selectDone(match.id,'103','1',match.pl[1])">1<i class="jc-binding">{{match.pl[1]}}</i></span>
                                                                                    <span class="flex border-r border-b" :class="{'active' : curOpt(match.id,'103','2')}" @click="selectDone(match.id,'103','2',match.pl[2])">2<i class="jc-binding">{{match.pl[2]}}</i></span>
                                                                                    <span class="flex border-r border-b" :class="{'active' : curOpt(match.id,'103','3')}" @click="selectDone(match.id,'103','3',match.pl[3])">3<i class="jc-binding">{{match.pl[3]}}</i></span>
                                                                                </div>
                                                                                <div class="betbtn-pl-item flex-box" style="margin-top: -1px;">
                                                                                    <span class="flex border-r" :class="{'active' : curOpt(match.id,'103','4')}" @click="selectDone(match.id,'103','4',match.pl[4])">4<i class="jc-binding">{{match.pl[4]}}</i></span>
                                                                                    <span class="flex border-r" :class="{'active' : curOpt(match.id,'103','5')}" @click="selectDone(match.id,'103','5',match.pl[5])">5<i class="jc-binding">{{match.pl[5]}}</i></span>
                                                                                    <span class="flex border-r" :class="{'active' : curOpt(match.id,'103','6')}" @click="selectDone(match.id,'103','6',match.pl[6])">6<i class="jc-binding">{{match.pl[6]}}</i></span>
                                                                                    <span class="flex border-r" :class="{'active' : curOpt(match.id,'103','7')}" @click="selectDone(match.id,'103','7',match.pl[7])">7+<i class="jc-binding">{{match.pl[7]}}</i></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                                <!--进球数 end-->
                                                                <!--上下单双-->
                                                                <template v-if="path == 'sxds'">
                                                                    <div class="betsel-list-wrap flex-box">
                                                                        <div class="betbtns-wrap flex">
                                                                            <div class="betbtns">
                                                                                <div class="betbtn-pl-item flex-box">
                                                                                    <span style="line-height: 1.8;padding: 5px 0" class="flex border-r border-b" :class="{'active' : curOpt(match.id,'104','s+d')}" @click="selectDone(match.id,'104','s+d',match.pl[0])">上+单<i class="jc-binding block">{{match.pl[0]}}</i></span>
                                                                                    <span style="line-height: 1.8;padding: 5px 0" class="flex border-r border-b" :class="{'active' : curOpt(match.id,'104','s+s')}" @click="selectDone(match.id,'104','s+s',match.pl[1])">上+双<i class="jc-binding block">{{match.pl[1]}}</i></span>
                                                                                    <span style="line-height: 1.8;padding: 5px 0" class="flex border-r border-b" :class="{'active' : curOpt(match.id,'104','x+d')}" @click="selectDone(match.id,'104','x+d',match.pl[2])">下+单<i class="jc-binding block">{{match.pl[2]}}</i></span>
                                                                                    <span style="line-height: 1.8;padding: 5px 0" class="flex border-r border-b" :class="{'active' : curOpt(match.id,'104','x+s')}" @click="selectDone(match.id,'104','x+s',match.pl[3])">下+双<i class="jc-binding block">{{match.pl[3]}}</i></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                                <!--上下单双 end-->
                                                                <!--比分-->
                                                                <template v-if="path == 'bf'">
                                                                    <div class="betsel-list-wrap flex-box">
                                                                        <div class="betbtns-wrap flex">
                                                                            <div class="betbtns">
                                                                                <div class="betbtn-pl-item" @click="showAll(match)" v-if="gameNum(match.id) < 1">
                                                                                    <span style="display: block" class="c-2">点击展开比分投注区</span>
                                                                                </div>
                                                                                <div class="red betbtn-pl-item" :style="{'width': contentW + 'px'}" @click="showAll(match)" v-else>
                                                                                    <span class="text-ellipsis" style="display: block">
                                                                                        <em v-for="(_item,_index) in gameBetCont(match.id)">{{_item}}&nbsp;</em>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                                <!--比分 end-->
                                                                <!--半全场-->
                                                                <template v-if="path == 'bqc'">
                                                                    <div class="betsel-list-wrap flex-box">
                                                                        <div class="betbtns-wrap flex">
                                                                            <div class="betbtns">
                                                                                <div class="betbtn-pl-item" @click="showAll(match)" v-if="gameNum(match.id) < 1">
                                                                                    <span style="display: block" class="c-2">点击展开半全场投注区</span>
                                                                                </div>
                                                                                <div class="red betbtn-pl-item" :style="{'width': contentW + 'px'}" @click="showAll(match)" v-else>
                                                                                    <span class="text-ellipsis" style="display: block">
                                                                                       <em v-for="(_item,_index) in gameBetCont(match.id)">{{_item}}&nbsp;</em>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                                <!--半全场 end-->
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <!--分析-->
                                                    <div class="fxinfo-box"  :class="infoState(match.id) ? 'fxinfo-box-on' : ''">
                                                        <div class="jc-info-arrow"><i class="ico-arrow2"></i></div>
                                                        <div class="jc-item-fxinfo">
                                                            <ul>
                                                                <li>历史交锋：--</li>
                                                                <li>近战战绩：--</li>
                                                                <li>平均赔率：<span class="mr-sm">主胜{{match.win}}</span><span class="mr-sm">平{{match.draw}}</span><span class="mr-sm">主负{{match.lost}}</span></li>
                                                                <li>投注比例：<span class="mr-sm">{{match.hcount_pct[0]}}</span><span class="mr-sm">{{match.hcount_pct[1]}}</span><span class="mr-sm">{{match.hcount_pct[2]}}</span></li>
                                                            </ul>
                                                            <!--<div class="more tc">-->
                                                                <!--<a>-->
                                                                    <!--详细赛事分析 >>-->
                                                                <!--</a>-->
                                                            <!--</div>-->
                                                        </div>
                                                    </div>
                                                    <!--分析 end-->
                                                </div>
                                            </template>
                                            <!--让球胜平负 end-->
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <!--选号区 end-->
                <!--查看全部 弹窗-->
                <base-modal v-model="allVisible"  @hanle-cancel="betCancel" @hanle-sure="betSure">
                    <template slot="header">
                        <div class="jc-dialog-head">
                            <span class="itm-team-l"><i class="c-4">主</i>{{curData.homesxname}}</span>
                            <span class="itm-vs">VS</span>
                            <span class="itm-team-r">{{curData.guestsxname}}</span>
                        </div>
                    </template>
                    <template slot="main">
                        <div class="jc-dialog-cont mt-sm">
                            <template v-if="path == 'bf'">
                                <div class="popbox-betsel-tablewrap">
                                    <table class="popbox-betsel-table">
                                        <tbody>
                                        <tr>
                                            <th class="th-zq-col4">比分</th>
                                            <td>
                                                <table class="all-table" width="100%">
                                                    <tr>
                                                        <td :class="{'active':curOpt('105','1:0')}" @click="selectDoneTemp('105','1:0',curData.pl[0])">1:0<i class="ng-binding">{{curData.pl[0]}}</i></td>
                                                        <td :class="{'active':curOpt('105','2:0')}" @click="selectDoneTemp('105','2:0',curData.pl[1])">2:0<i class="ng-binding">{{curData.pl[1]}}</i></td>
                                                        <td :class="{'active':curOpt('105','2:1')}" @click="selectDoneTemp('105','2:1',curData.pl[2])">2:1<i class="ng-binding">{{curData.pl[2]}}</i></td>
                                                        <td :class="{'active':curOpt('105','3:0')}" @click="selectDoneTemp('105','3:0',curData.pl[3])">3:0<i class="ng-binding">{{curData.pl[3]}}</i></td>
                                                        <td :class="{'active':curOpt('105','3:1')}" @click="selectDoneTemp('105','3:1',curData.pl[4])">3:1<i class="ng-binding">{{curData.pl[4]}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active':curOpt('105','3:2')}" @click="selectDoneTemp('105','3:2',curData.pl[5])">3:2<i class="ng-binding">{{curData.pl[5]}}</i></td>
                                                        <td :class="{'active':curOpt('105','4:0')}" @click="selectDoneTemp('105','4:0',curData.pl[6])">4:0<i class="ng-binding">{{curData.pl[6]}}</i></td>
                                                        <td :class="{'active':curOpt('105','4:1')}" @click="selectDoneTemp('105','4:1',curData.pl[7])">4:1<i class="ng-binding">{{curData.pl[7]}}</i></td>
                                                        <td :class="{'active':curOpt('105','4:2')}" @click="selectDoneTemp('105','4:2',curData.pl[8])">4:2<i class="ng-binding">{{curData.pl[8]}}</i></td>
                                                        <td :class="{'active':curOpt('105','胜其它')}" @click="selectDoneTemp('105','胜其它',curData.pl[9])">胜其它<i class="ng-binding">{{curData.pl[9]}}</i></td>
                                                    <tr>
                                                        <td :class="{'active':curOpt('105','0:0')}" @click="selectDoneTemp('105','0:0',curData.pl[10])">0:0<i class="ng-binding">{{curData.pl[10]}}</i></td>
                                                        <td :class="{'active':curOpt('105','1:1')}" @click="selectDoneTemp('105','1:1',curData.pl[11])">1:1<i class="ng-binding">{{curData.pl[11]}}</i></td>
                                                        <td :class="{'active':curOpt('105','2:2')}" @click="selectDoneTemp('105','2:2',curData.pl[12])">2:2<i class="ng-binding">{{curData.pl[12]}}</i></td>
                                                        <td :class="{'active':curOpt('105','3:3')}" @click="selectDoneTemp('105','3:3',curData.pl[13])">3:3<i class="ng-binding">{{curData.pl[13]}}</i></td>
                                                        <td :class="{'active':curOpt('105','平其它')}" @click="selectDoneTemp('105','平其它',curData.pl[14])">平其它<i class="ng-binding">{{curData.pl[14]}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active':curOpt('105','0:1')}" @click="selectDoneTemp('105','0:1',curData.pl[15])">0:1<i class="ng-binding">{{curData.pl[15]}}</i></td>
                                                        <td :class="{'active':curOpt('105','0:2')}" @click="selectDoneTemp('105','0:2',curData.pl[16])">0:2<i class="ng-binding">{{curData.pl[16]}}</i></td>
                                                        <td :class="{'active':curOpt('105','1:2')}" @click="selectDoneTemp('105','1:2',curData.pl[17])">1:2<i class="ng-binding">{{curData.pl[17]}}</i></td>
                                                        <td :class="{'active':curOpt('105','0:3')}" @click="selectDoneTemp('105','0:3',curData.pl[18])">0:3<i class="ng-binding">{{curData.pl[18]}}</i></td>
                                                        <td :class="{'active':curOpt('105','1:3')}" @click="selectDoneTemp('105','1:3',curData.pl[19])">1:3<i class="ng-binding">{{curData.pl[19]}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active':curOpt('105','2:3')}" @click="selectDoneTemp('105','2:3',curData.pl[20])">2:3<i class="ng-binding">{{curData.pl[20]}}</i></td>
                                                        <td :class="{'active':curOpt('105','0:4')}" @click="selectDoneTemp('105','0:4',curData.pl[21])">0:4<i class="ng-binding">{{curData.pl[21]}}</i></td>
                                                        <td :class="{'active':curOpt('105','1:4')}" @click="selectDoneTemp('105','1:4',curData.pl[22])">1:4<i class="ng-binding">{{curData.pl[22]}}</i></td>
                                                        <td :class="{'active':curOpt('105','2:4')}" @click="selectDoneTemp('105','2:4',curData.pl[23])">2:4<i class="ng-binding">{{curData.pl[23]}}</i></td>
                                                        <td :class="{'active':curOpt('105','负其它')}" @click="selectDoneTemp('105','负其它',curData.pl[24])">负其它<i class="ng-binding">{{curData.pl[24]}}</i></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                            <template v-if="path == 'bqc'">
                                <div class="popbox-betsel-tablewrap">
                                    <table class="popbox-betsel-table">
                                        <tbody>
                                        <tr>
                                            <th class="th-zq-col6">半全场</th>
                                            <td>
                                                <table class="all-table" width="100%">
                                                    <tr>
                                                        <td :class="{'active':curOpt('106','3-3')}" @click="selectDoneTemp('106','3-3',curData.pl[0])">胜-胜<i class="ng-binding">{{curData.pl[0]}}</i></td>
                                                        <td :class="{'active':curOpt('106','3-1')}" @click="selectDoneTemp('106','3-1',curData.pl[1])">胜-平<i class="ng-binding">{{curData.pl[1]}}</i></td>
                                                        <td :class="{'active':curOpt('106','3-0')}" @click="selectDoneTemp('106','3-0',curData.pl[2])">胜-负<i class="ng-binding">{{curData.pl[2]}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active':curOpt('106','1-3')}" @click="selectDoneTemp('106','1-3',curData.pl[3])">平-胜<i class="ng-binding">{{curData.pl[3]}}</i></td>
                                                        <td :class="{'active':curOpt('106','1-1')}" @click="selectDoneTemp('106','1-1',curData.pl[4])">平-平<i class="ng-binding">{{curData.pl[4]}}</i></td>
                                                        <td :class="{'active':curOpt('106','1-0')}" @click="selectDoneTemp('106','1-0',curData.pl[5])">平-负<i class="ng-binding">{{curData.pl[5]}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active':curOpt('106','0-3')}" @click="selectDoneTemp('106','0-3',curData.pl[6])">负-胜<i class="ng-binding">{{curData.pl[6]}}</i></td>
                                                        <td :class="{'active':curOpt('106','0-1')}" @click="selectDoneTemp('106','0-1',curData.pl[7])">负-平<i class="ng-binding">{{curData.pl[7]}}</i></td>
                                                        <td :class="{'active':curOpt('106','0-0')}" @click="selectDoneTemp('106','0-0',curData.pl[8])">负-负<i class="ng-binding">{{curData.pl[8]}}</i></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                        </div>
                    </template>
                </base-modal>
                <!--查看全部 end-->
                <!--底部选号区-->
                <div class="jc-bet-foot flex-box border-top-1px">
                    <div class="bet-del tc border-right-1px" @click="clear">
                        <p><i class="iconfont icon-shanchu btn-icon-clear"></i></p>
                        <p class="f-mini">清空</p>
                    </div>
                    <div class="bet-plan flex tc">
                        <p>已选择<em class="red"> {{games}} </em>场</p>
                        <p class="c-3 f-mini">请选择您要投注的比赛</p>
                    </div>
                    <button class="bet-btn btn-sure" @click="submitOrder">选好了</button>
                </div>
                <!--底部选号区 end-->
            </template>
            <!--筛选-->
            <base-modal v-model="filterVisible" @hanle-cancel="filterCancel" @hanle-sure="filterSure">
                <template slot="header">共有<b class="red">{{totalMatch}}</b>场比赛可选</template>
                <template slot="main">
                    <div class="filter-match-cont">
                        <div class="ui-filter-stit c-2 f-sm mf-sm">赛事选择</div>
                        <div class="tc flex-box sel-btn">
                            <span class="btn flex border-right-1px" @click="filterChose(1)" :class="{'selected':filterSelected == 1}">全选</span>
                            <span class="btn flex border-right-1px" @click="filterChose(2)" :class="{'selected':filterSelected == 2}">反选</span>
                            <span class="btn flex" @click="filterChose(3)" :class="{'selected':filterSelected == 3}">仅5大联赛</span>
                        </div>
                        <div class="sel-group">
                            <ul class="cf">
                                <li v-for="(item,index) in filterList" class="sel-item" :class="{'active' : item.select}" @click="handleMatchFilter(index)">
                                    {{item.league}}
                                    <i class="iconfont icon-xuanzhong" v-show="item.select"></i>
                                </li>
                            </ul>
                        </div>
                    </div>
                </template>
            </base-modal>
            <!--筛选 end-->
        </div>
    </div>
</template>

<script>
    import StopSaleTips from 'components/lottery/StopSaleTips.vue' //暂停销售
    import BaseModal from 'components/common/BaseModal.vue' //弹窗
    export default {
        name: 'bjdc',
        components:{
            StopSaleTips,
            BaseModal
        },
        data () {
            return {
                barFixed : -1,
                betMap:{
                    101 : 'spf',
                    102 : 'sf',
                    103 : 'jqs',
                    104 : 'sxds',
                    105 : 'bf',
                    106 : 'bqc'
                },
                play:[
                    {"name":"让球胜平负","type":"1",'path':'spf'},
                    {"name":"胜负过关","type":"2",'path':'sf'},
                    {"name":"总进球数","type":"3",'path':'jqs'},
                    {"name":"上下单双","type":"4",'path':'sxds'},
                    {"name":"比分","type":"5",'path':'bf'},
                    {"name":"半全场","type":"6",'path':'bqc'}
                ],
                isStop : 0, //是否暂停
                loading: true,
                openArr:[], //按天展开数组
                infoArr:[], //分析展开数组
                filterArr:[],//赛事筛选
                filterList:[],//赛事筛选列表
                tempfilterList:[],//临时赛事筛选列表

                show:false, //选择玩法
                showMore:false, //右侧列表
                allVisible:false, //展开全部
                filterVisible:false,//筛选
                filterSelected : 1, //筛选快捷选择

                curData: {
                    "ordernum" : 1,
                    "id" : "273135",
                    "fixtureid" : "785097",
                    "seasonid" : "5050",
                    "matchid" : "170",
                    "homesxname" : "山东鲁能",
                    "guestsxname" : "鹿  岛",
                    "rangqiu" : "0",
                    "league" : "亚冠联赛",
                    "schedulesdate" : "2019-03-12",
                    "matchdate" : "2019-03-12",
                    "matchtime" : "15:30",
                    "matchweek" : "星期二",
                    "win" : "2.38",
                    "draw" : "3.35",
                    "lost" : "2.74",
                    "isright" : "1",
                    "zyggendtime" : "2019-03-12 15:20:00",
                    "pl" : [ "19.91", "6.47", "14.24", "7.49", "33.53", "18.24", "36.19", "66.18", "40.93", "78.41", "803.76", "17.03", "8.05", "16.41", "99.69", "200.94", "20.88", "50.16", "19.35", "114.83", "68.10", "71.91", "357.22", "273.62", "252.16" ],
                    "hcount_pct" : [ "0.00%", "0.00%", "0.00%" ]
                },//显示全部的数据

                matchInfo:{}, //赛事列表数据

                //投注
                plan:this.$store.state.bjdc.plan, //投注方案内容
                tempPlan:{}, //临时投注方案内容，用于取消后恢复
                tempMatchData:{},//投注数组，投注页数据显示相关

                popupVisible: true
            }
        },
        computed:{
            //玩法下拉高度
            selctHeight(){
                return this.$store.state.clientHeight - 40
            },
            //选号区高度
            betHeight(){
                return this.$store.state.clientHeight - 92
            },
            //投注选项显示区域宽度
            contentW(){
                return this.$store.state.clientWidth - 86
            },
            //筛选--可选赛事总场数
            totalMatch(){
                let num = 0
                for(let i in this.filterList){
                    if(this.filterList[i].select){
                        for(let j in this.matchInfo){
                            for(let m in this.matchInfo[j].matchdata){
                                if(this.matchInfo[j].matchdata[m].league == this.filterList[i].league){
                                    num +=1
                                }
                            }
                        }
                    }
                }
                return num
            },
            //当前选中的显示状态
            curOpt(){
                if(!this.allVisible){
                    return (id,play,sign)=>{
                        return this.checkStatus(this.plan,id,play,sign)
                    }
                }else {
                    return (play,sign)=>{
                        return this.checkStatus(this.tempPlan,this.curData.id,play,sign)
                    }
                }
            },
            //已选场数
            games(){
                return this.$_.size(this.plan)
            },
            //计算每场场玩法选项数量
            gameNum(){
                return (id)=>{
                    let n = 0
                    if(this.plan.hasOwnProperty(id)){
                        for(let key in this.plan[id]){
                            n = n + this.$_.size(this.plan[id][key])
                        }
                    }
                    return n
                }
            },
            //比分投注，半全场玩法选项显示
            gameBetCont(){
                return (id)=>{
                    let arr = []
                    if(this.plan.hasOwnProperty(id)){
                        for(let key in this.plan[id]){
                            for(let _key in this.plan[id][key]){
                                arr.push(_key)
                            }
                        }
                    }
                    let resArr = []
                    if(this.path == 'bf'){
                        const initArr= ['1:0','2:0','2:1','3:0','3:1','3:2','4:0','4:1','4:2','胜其它','0:0','1:1','2:2','3:3','平其它',
                            '0:1' ,'0:2','1:2','0:3','1:3','2:3','0:4','1:4','2:4','负其它']
                        for(let i in initArr){
                            if(arr.indexOf(initArr[i]) > -1){
                                resArr.push(initArr[i])
                            }
                        }
                    }
                    if(this.path == 'bqc'){
                        const bqcArr = ['3-3','3-1','3-0','1-3','1-1','1-0','0-3','0-1','0-0']
                        const textObj = { 3 :'胜',1 :'平',0 :'负',}
                        for(let i in bqcArr){
                            if(arr.indexOf(bqcArr[i]) > -1){
                                let _arr = bqcArr[i].split('-')
                                resArr.push(textObj[_arr[0]] + '' + textObj[_arr[1]])
                            }
                        }
                    }
                    return resArr;
                }
            },
            //是否筛选（控制状态图标显示状态）
            isFilter(){
                let state = false
                for(let i in this.filterList){
                    if(!this.filterList[i].select){
                        state = true
                    }
                }
                return state
            },
            //当前路径
            path(){
                let arr =  this.$route.query.type || 'spf'
                return arr
            },
            playObj(){
                return {
                    'spf' : {"name":"让球胜平负","type":"1"},
                    'sf' : {"name":"胜负过关","type":"2"},
                    'jqs' : {"name":"总进球数","type":"3"},
                    'sxds' : {"name":"上下单双","type":"4"},
                    'bf' : {"name":"比分","type":"5"},
                    'bqc' : {"name":"半全场","type":"6"}
                }
            },
            //选中的玩法
            selected(){
                return this.$route.path !== '/bjdc/bet' ? this.playObj[this.path].name : ''
            },
            //选中的玩法对应的type
            type(){
                return this.$route.path !== '/bjdc/bet' ? this.playObj[this.path].type : ''
            }
        },
        filters:{
            //截止时间截取
            timeStr(val){
                return val.slice(10,16)
            }
        },
        methods:{
            //按天展开or收起状态
            openState(val){
                return this.openArr.indexOf(val) > - 1 ? true : false
            },
            //按天展开or收起
            handleSlide(val){
                let a = this.openArr.indexOf(val)
                if(a > -1){
                    this.openArr.splice(a,1)
                }else {
                    this.openArr.push(val)
                }
            },
            //赛事分析展开or收起状态
            infoState(val){
                return this.infoArr.indexOf(val) > - 1 ? true : false
            },
            //赛事分析展开or收起
            infoSlide(val){
                let a = this.infoArr.indexOf(val)
                if(a > -1){
                    this.infoArr.splice(a,1)
                }else {
                    this.infoArr.push(val)
                }
            },

            //<!--赛事筛选****************-->
            //赛事列表过滤
            filterData(list){
                return list.filter((item)=>{
                    return this.filterArr.indexOf(item.league) > -1
                })
            },
            //筛选弹窗
            filterShow(){
                this.filterVisible = true
            },
            //选择
            handleMatchFilter(index){
                this.filterList[index].select = !this.filterList[index].select
            },
            //取消筛选
            filterCancel(){
                this.$set(this,'filterList',JSON.parse(JSON.stringify(this.tempfilterList))) //还原筛选选项
            },
            //确认筛选
            filterSure(){
                if(this.totalMatch < 1){
                    this.$toast('筛选结果为0条数据，请重新设定筛选条件')
                    return
                }
                //更新筛选数组
                this.filterArr = []
                for(let i in this.filterList){
                    if(this.filterList[i].select){
                        this.filterArr.push(this.filterList[i].league)
                    }
                }
                //更新临时列表
                this.$set(this,'tempfilterList',JSON.parse(JSON.stringify(this.filterList)))
                //若已选有场次，删除未筛选场次数据
                if(JSON.stringify(this.plan) !== '{}'){
                    for(let j in this.matchInfo){
                        for(let m in this.matchInfo[j].matchdata){
                            if(!this.$base.isExit(this.filterArr,this.matchInfo[j].matchdata[m].league)){
                                if(this.plan.hasOwnProperty(this.matchInfo[j].matchdata[m].id)){
                                    this.$delete(this.plan,this.matchInfo[j].matchdata[m].id)
                                }
                            }
                        }
                    }
                }
                this.filterVisible = false
            },
            //快捷筛选
            filterChose(val){
                this.filterSelected = val
                if(val == 1){ //全选
                    for(let i in this.filterList){
                        this.filterList[i].select = true
                    }
                }
                if(val == 2){ //反选
                    for(let i in this.filterList){
                        this.filterList[i].select = !this.filterList[i].select
                    }
                }
                if(val == 3){ //五大联赛
                    for(let i in this.filterList){
                        if(this.filterList[i].league == '英超' || this.filterList[i].league == '意甲' || this.filterList[i].league == '德甲' ||
                            this.filterList[i].league == '西甲' || this.filterList[i].league == '法甲'){
                            this.filterList[i].select = true
                        }else {
                            this.filterList[i].select = false
                        }
                    }
                }
            },
            //<!--赛事筛选**************** end-->

            //<!--投注方案选择***********-->
            //全部玩法显示
            showAll(item){
                this.allVisible = true;
                this.curData = item
                this.$set(this,'tempPlan',JSON.parse(JSON.stringify(this.plan)))
            },
            //检测选中状态
            checkStatus(obj,id,play,sign) {
                let res = false
                if(obj.hasOwnProperty(id)){
                    if(obj[id].hasOwnProperty(play)){
                        if(obj[id][play].hasOwnProperty(sign)){
                            res = true
                        }else {
                            res = false
                        }
                    }else {
                        res = false
                    }
                }else {
                    res = false
                }
                return res
            },
            //选择方案增减
            addPlan(obj,id,play,sign,pl){
                if(obj.hasOwnProperty(id)){
                    if(obj[id].hasOwnProperty(play)){
                        if(obj[id][play].hasOwnProperty(sign)){
                            this.$delete(obj[id][play],sign)
                            if(JSON.stringify(obj[id][play]) == '{}'){
                                this.$delete(obj[id],[play])
                                if(JSON.stringify(obj[id]) == '{}'){
                                    this.$delete(obj,[id])
                                }
                            }
                        }else {
                            this.$set(obj[id][play],[sign], pl)
                        }
                    }else {
                        this.$set(obj[id],[play], {})
                        this.$set(obj[id][play],[sign], pl)
                    }
                }else{
                    this.$set(obj,[id], {})
                    this.$set(obj[id],[play], {})
                    this.$set(obj[id][play],[sign], pl)
                }
            },
            //方案选择(场次id,play玩法标识,sign选号标识3主胜、1平、0主负,pl赔率)
            selectDone(id,play,sign,pl){
                this.addPlan(this.plan,id,play,sign,pl);
                console.log(JSON.stringify(this.plan))
            },
            //全部玩法弹窗玩法选择
            selectDoneTemp(play,sign,pl){
                this.addPlan(this.tempPlan,this.curData.id,play,sign,pl);
            },
            //全部玩法 取消选择
            betCancel(){
                this.allVisible = false
            },
            //全部玩法 确认选择
            betSure(){
                this.allVisible = false
                this.$set(this,'plan',JSON.parse(JSON.stringify(this.tempPlan)))
            },
            //<!--投注方案选择*********** end-->
            clear(){
                this.$set(this,'plan',{})
                this.$set(this,'tempPlan',{})
                this.$set(this,'tempMatchData',{})
                this.$store.commit('clearBjdcBetData')
            },
            //选择玩法
            chosePlay(type){
                this.$refs.wrapper.scrollTop = 0
                this.$router.push({
                    path: '/bjdc',
                    query:{
                        type : type
                    }
                })
                this.clear(); //清空投注选项
                this.show = false
            },
            //玩法介绍
            toPlayNews(){
                this.$store.commit('setLoadStatus',true)
                this.$axios.get('/index/news/getAppBetNew',{
                    params:{
                        name:this.name
                    }
                }).then(({data})=>{
                    this.$store.commit('setLoadStatus',false)
                    this.showMore = false
                    if(!data.err){
                        this.$router.push({
                            path:'/news/detail',
                            query:{
                                cz:this.cz,
                                id:data.data.id,
                                navid:data.data.nav_id
                            }
                        })
                    }else {
                        this.$toast('暂无相关玩法介绍!')
                    }
                }).catch(({error})=>{
                    console.log(error)
                })
            },
            //进入投注设置页面
            submitOrder(){
                if(!this.games){
                    this.$toast('请至少选择1场比赛');
                    return
                }
                this.tempMatchData = {}
                //提取已选赛事相关数据
                for(let i in this.matchInfo){
                    for(let j in this.matchInfo[i].matchdata){
                        if(this.plan.hasOwnProperty(this.matchInfo[i].matchdata[j].id)){
                            this.tempMatchData[this.matchInfo[i].matchdata[j].id] = this.matchInfo[i].matchdata[j]
                        }
                    }
                }
                this.$store.commit('setBjdcPlan',this.plan)
                this.$store.commit('setBjdcTempMatchData',this.tempMatchData)
                this.$router.push({
                    path : '/bjdc/bet',
                    query:{
                        type : this.path
                    }
                })
            },
            //返回大厅
            goHome(){
                if(this.games > 0){
                    this.$messagebox.confirm(
                        '返回大厅将清空所有已选的号码'
                    ).then(()=>{
                        this.$store.commit('delKeepAlivePage','bjdc') //清除投注页面缓存
                        this.$store.commit('delKeepAlivePage','bjdcOrder') //清除订单页面缓存
                        this.$store.commit('clearBjdcBetData')
                        this.$router.isBack = true
                        this.$router.push({
                            path:'/jc'
                        })
                    }).catch((err)=>{

                    });
                }else {
                    this.$router.isBack = true
                    this.$router.push({
                        path:'/jc'
                    })
                }
            },
            //监听元素到顶部的距离  并判断滚动的距离如果大于了元素到顶部的距离时，为元素添加 isFixed的class
            handleScroll(){
                var scrollTop = this.$refs.wrapper.scrollTop
                var wrapperArr = this.$refs.matchWrapper
                if(scrollTop <= 35){
                    this.barFixed = -1
                }else {
                    this.barFixed = 0
                    for(let i in wrapperArr){
                        if(scrollTop + 74 >= wrapperArr[i].offsetTop){
                            this.barFixed = i
                        }
                    }
                }
            }
        },
        created(){
            let data = {
                'pause' : 0, //0开启，1停
                'data' :  [
                    {
                        'matchtime': '2019-03-12', //投注日期
                        'matchday': '周三', //投注星期
                        'matchdata' :
                            [
                                {
                                    "ordernum" : 1,
                                    "id" : "273135",
                                    "fixtureid" : "785097",
                                    "seasonid" : "5050",
                                    "matchid" : "170",
                                    "homesxname" : "山东鲁能",
                                    "guestsxname" : "鹿  岛",
                                    "rangqiu" : "0",
                                    "league" : "亚冠联赛",
                                    "schedulesdate" : "2019-03-12",
                                    "matchdate" : "2019-03-12",
                                    "matchtime" : "15:30",
                                    "matchweek" : "星期二",
                                    "win" : "2.38",
                                    "draw" : "3.35",
                                    "lost" : "2.74",
                                    "isright" : "1",
                                    "zyggendtime" : "2019-03-12 15:20:00",
                                    "pl" : [ "19.91", "6.47", "14.24", "7.49", "33.53", "18.24", "36.19", "66.18", "40.93", "78.41", "803.76", "17.03", "8.05", "16.41", "99.69", "200.94", "20.88", "50.16", "19.35", "114.83", "68.10", "71.91", "357.22", "273.62", "252.16" ],
                                    "hcount_pct" : [ "0.00%", "0.00%", "0.00%" ]
                                }, {
                                "ordernum" : 2,
                                "id" : "273136",
                                "fixtureid" : "785121",
                                "seasonid" : "5050",
                                "matchid" : "170",
                                "homesxname" : "广  岛",
                                "guestsxname" : "墨尔本",
                                "rangqiu" : "0",
                                "league" : "亚冠联赛",
                                "schedulesdate" : "2019-03-12",
                                "matchdate" : "2019-03-12",
                                "matchtime" : "18:00",
                                "matchweek" : "星期二",
                                "win" : "1.76",
                                "draw" : "3.60",
                                "lost" : "4.21",
                                "isright" : "1",
                                "zyggendtime" : "2019-03-12 17:50:00",
                                "pl" : [ "182.21", "17.53", "37.91", "11.07", "86.58", "80.84", "41.07", "290.67", "239.37", "200.13", "813.88", "14.99", "9.17", "12.24", "50.86", "21.93", "11.93", "17.08", "8.34", "29.75", "15.14", "31.20", "89.01", "50.23", "90.43" ],
                                "hcount_pct" : [ "0.00%", "0.00%", "0.00%" ]
                            }, {
                                "ordernum" : 3,
                                "id" : "273137",
                                "fixtureid" : "785123",
                                "seasonid" : "5050",
                                "matchid" : "170",
                                "homesxname" : "大邱FC",
                                "guestsxname" : "恒  大",
                                "rangqiu" : "0",
                                "league" : "亚冠联赛",
                                "schedulesdate" : "2019-03-12",
                                "matchdate" : "2019-03-12",
                                "matchtime" : "18:30",
                                "matchweek" : "星期二",
                                "win" : "3.26",
                                "draw" : "3.33",
                                "lost" : "2.09",
                                "isright" : "1",
                                "zyggendtime" : "2019-03-12 18:20:00",
                                "pl" : [ "156.35", "9.69", "26.21", "9.56", "71.33", "43.39", "54.09", "237.78", "150.17", "184.08", "380.45", "11.47", "8.64", "18.00", "70.02", "51.82", "9.89", "18.07", "11.05", "24.44", "27.77", "41.77", "79.26", "89.87", "101.00" ],
                                "hcount_pct" : [ "0.00%", "0.00%", "0.00%" ]
                            }, {
                                "ordernum" : 4,
                                "id" : "273138",
                                "fixtureid" : "785099",
                                "seasonid" : "5050",
                                "matchid" : "170",
                                "homesxname" : "达罗塔",
                                "guestsxname" : "庆南FC",
                                "rangqiu" : "1",
                                "league" : "亚冠联赛",
                                "schedulesdate" : "2019-03-12",
                                "matchdate" : "2019-03-12",
                                "matchtime" : "20:45",
                                "matchweek" : "星期二",
                                "win" : "3.97",
                                "draw" : "3.53",
                                "lost" : "1.82",
                                "isright" : "1",
                                "zyggendtime" : "2019-03-12 20:35:00",
                                "pl" : [ "24.50", "8.21", "10.18", "8.04", "20.30", "12.38", "56.02", "152.81", "26.24", "139.09", "433.99", "14.83", "9.22", "18.17", "132.31", "132.31", "18.83", "63.88", "19.93", "144.98", "73.47", "67.46", "328.78", "193.74", "212.74" ],
                                "hcount_pct" : [ "0.00%", "0.00%", "0.00%" ]
                            }, {
                                "ordernum" : 5,
                                "id" : "273139",
                                "fixtureid" : "785073",
                                "seasonid" : "5050",
                                "matchid" : "170",
                                "homesxname" : "棉  农",
                                "guestsxname" : "阿赫利",
                                "rangqiu" : "0",
                                "league" : "亚冠联赛",
                                "schedulesdate" : "2019-03-12",
                                "matchdate" : "2019-03-12",
                                "matchtime" : "21:00",
                                "matchweek" : "星期二",
                                "win" : "1.98",
                                "draw" : "3.29",
                                "lost" : "3.63",
                                "isright" : "1",
                                "zyggendtime" : "2019-03-12 20:50:00",
                                "pl" : [ "24.50", "8.21", "10.18", "8.04", "20.30", "12.38", "56.02", "152.81", "26.24", "139.09", "433.99", "14.83", "9.22", "18.17", "132.31", "132.31", "18.83", "63.88", "19.93", "144.98", "73.47", "67.46", "328.78", "193.74", "212.74" ],
                                "hcount_pct" : [ "0.00%", "0.00%", "0.00%" ]
                            }, {
                                "ordernum" : 6,
                                "id" : "273140",
                                "fixtureid" : "785049",
                                "seasonid" : "5050",
                                "matchid" : "170",
                                "homesxname" : "德独立",
                                "guestsxname" : "艾  因",
                                "rangqiu" : "0",
                                "league" : "亚冠联赛",
                                "schedulesdate" : "2019-03-12",
                                "matchdate" : "2019-03-12",
                                "matchtime" : "23:30",
                                "matchweek" : "星期二",
                                "win" : "2.01",
                                "draw" : "3.16",
                                "lost" : "3.65",
                                "isright" : "1",
                                "zyggendtime" : "2019-03-12 23:20:00",
                                "pl" : [ "24.50", "8.21", "10.18", "8.04", "20.30", "12.38", "56.02", "152.81", "26.24", "139.09", "433.99", "14.83", "9.22", "18.17", "132.31", "132.31", "18.83", "63.88", "19.93", "144.98", "73.47", "67.46", "328.78", "193.74", "212.74" ],
                                "hcount_pct" : [ "0.00%", "0.00%", "0.00%" ]
                            }, {
                                "ordernum" : 7,
                                "id" : "273141",
                                "fixtureid" : "785051",
                                "seasonid" : "5050",
                                "matchid" : "170",
                                "homesxname" : "希拉尔",
                                "guestsxname" : "杜海勒",
                                "rangqiu" : "0",
                                "league" : "亚冠联赛",
                                "schedulesdate" : "2019-03-12",
                                "matchdate" : "2019-03-12",
                                "matchtime" : "23:45",
                                "matchweek" : "星期二",
                                "win" : "2.12",
                                "draw" : "3.37",
                                "lost" : "3.15",
                                "isright" : "1",
                                "zyggendtime" : "2019-03-12 23:35:00",
                                "pl" : [ "24.50", "8.21", "10.18", "8.04", "20.30", "12.38", "56.02", "152.81", "26.24", "139.09", "433.99", "14.83", "9.22", "18.17", "132.31", "132.31", "18.83", "63.88", "19.93", "144.98", "73.47", "67.46", "328.78", "193.74", "212.74" ],
                                "hcount_pct" : [ "0.00%", "0.00%", "0.00%" ]
                            }, {
                                "ordernum" : 8,
                                "id" : "273142",
                                "fixtureid" : "785075",
                                "seasonid" : "5050",
                                "matchid" : "170",
                                "homesxname" : "萨  德",
                                "guestsxname" : "波斯波",
                                "rangqiu" : "-1",
                                "league" : "亚冠联赛",
                                "schedulesdate" : "2019-03-12",
                                "matchdate" : "2019-03-12",
                                "matchtime" : "23:45",
                                "matchweek" : "星期二",
                                "win" : "1.59",
                                "draw" : "3.78",
                                "lost" : "5.15",
                                "isright" : "1",
                                "zyggendtime" : "2019-03-12 23:35:00",
                                "pl" : [ "24.50", "8.21", "10.18", "8.04", "20.30", "12.38", "56.02", "152.81", "26.24", "139.09", "433.99", "14.83", "9.22", "18.17", "132.31", "132.31", "18.83", "63.88", "19.93", "144.98", "73.47", "67.46", "328.78", "193.74", "212.74" ],
                                "hcount_pct" : [ "0.00%", "0.00%", "0.00%" ]
                            }, {
                                "ordernum" : 9,
                                "id" : "273143",
                                "fixtureid" : "729375",
                                "seasonid" : "4820",
                                "matchid" : "107",
                                "homesxname" : "第  戎",
                                "guestsxname" : "日尔曼",
                                "rangqiu" : "2",
                                "league" : "法甲",
                                "schedulesdate" : "2019-03-12",
                                "matchdate" : "2019-03-13",
                                "matchtime" : "02:00",
                                "matchweek" : "星期二",
                                "win" : "12.99",
                                "draw" : "7.11",
                                "lost" : "1.20",
                                "isright" : "1",
                                "zyggendtime" : "2019-03-13 01:50:00",
                                "pl" : [ "24.50", "8.21", "10.18", "8.04", "20.30", "12.38", "56.02", "152.81", "26.24", "139.09", "433.99", "14.83", "9.22", "18.17", "132.31", "132.31", "18.83", "63.88", "19.93", "144.98", "73.47", "67.46", "328.78", "193.74", "212.74" ],
                                "hcount_pct" : [ "0.00%", "0.00%", "0.00%" ]
                            }, {
                                "ordernum" : 10,
                                "id" : "273144",
                                "fixtureid" : "731921",
                                "seasonid" : "4829",
                                "matchid" : "163",
                                "homesxname" : "罗斯郡",
                                "guestsxname" : "法基克",
                                "rangqiu" : "-1",
                                "league" : "苏冠",
                                "schedulesdate" : "2019-03-12",
                                "matchdate" : "2019-03-13",
                                "matchtime" : "03:45",
                                "matchweek" : "星期二",
                                "win" : "1.46",
                                "draw" : "4.18",
                                "lost" : "6.07",
                                "isright" : "1",
                                "zyggendtime" : "2019-03-13 03:35:00",
                                "pl" : [ "24.50", "8.21", "10.18", "8.04", "20.30", "12.38", "56.02", "152.81", "26.24", "139.09", "433.99", "14.83", "9.22", "18.17", "132.31", "132.31", "18.83", "63.88", "19.93", "144.98", "73.47", "67.46", "328.78", "193.74", "212.74" ],
                                "hcount_pct" : [ "0.00%", "0.00%", "0.00%" ]
                            }
                            ]
                    }
                ]
            } //初始数据
            this.$set(this,'isStop',data.pause)
            this.$set(this,'matchInfo',JSON.parse(JSON.stringify(data.data)))

            //初始化按照日期展开or收起数组
            let arr = []
            for(let i in data.data){
                arr.push(Number(i))
            }
            this.$set(this,'openArr',arr)

            //初始化赛事筛选数据数组
            let filterArr = []
            for(let m in data.data){
                for (let j in data.data[m].matchdata){
                    let league= data.data[m].matchdata[j].league
                    if(!this.$base.isExit(filterArr,league)){
                        filterArr.push(league)
                    }
                }
            }
            this.$set(this,'filterArr',filterArr)

            let resArr = []
            for(let i in filterArr){
                let list = {}
                list['select'] = true //添加选择状态
                list['league'] = filterArr[i] //添加比赛名称
                resArr.push(list)
            }
            this.$set(this,'filterList',resArr)
            this.$set(this,'tempfilterList',JSON.parse(JSON.stringify(resArr))) //临时数组，用于筛选取消后恢复

            this.loading = false
        },
        //进入设置页面时缓存页面
        beforeRouteLeave(to, from, next){
            if(to.path == '/bjdc/bet'){
                this.$store.commit('setKeepAlivePage','bjdc')
            }else {
                this.$store.commit('delKeepAlivePage','bjdc')
                this.$store.commit('delKeepAlivePage','bjdcOrder')
            }
            next();
        },
        activated(){
            this.$set(this,'plan',JSON.parse(sessionStorage.getItem("m_bjdc_plan")) || {})
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">

</style>
