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
                    <span class="chose" @click="show = !show">竞足-{{selected}}<i class="iconfont icon-jiantou yellow" :class="show ? 'is-active':'no-active'"></i></span>
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
                                        <div class="border-bottom-1px" :key="match.infoid">
                                            <div class="flex-box jc-item">
                                                <div class="jc-item-left" :class="{'fx-mt-sm':path !== 'ht' && path !== 'jqs' && path !== 'dg'}"  @click="infoSlide(match.infoid)">
                                                    <p>{{match.matchnum}}</p>
                                                    <p>{{match.simpleleague}}</p>
                                                    <p>{{match.ptggendtime | timeStr}}截止</p>
                                                    <p class="c-3">分析<i class="iconfont icon-xialajiantou rotateIcon" :class="infoState(match.infoid) ? 'is-active' : 'no-active'"></i></p>
                                                </div>
                                                <div class="jc-item-right flex">
                                                    <div class="dang-mteam">
                                                        <span class="itm-team-l"><i class="c-4">主<em v-if="Number(match.homestanding)>0">[{{match.homestanding}}]</em></i>{{match.homename}}</span>
                                                        <span class="itm-vs">VS</span>
                                                        <span class="itm-team-r">{{match.awayname}}<i v-if="Number(match.awaystanding)>0">[{{match.awaystanding}}]</i></span>
                                                    </div>
                                                    <!--混合、单关投注-->
                                                    <template v-if="path == 'ht' || path == 'dg'">
                                                        <div class="betbtns-box flex-box">
                                                            <div class="betbtns-wrap flex">
                                                                <table class="betbtns-wrap-table">
                                                                    <tbody>
                                                                    <tr>
                                                                        <th class="th-zq-col1">非<br/>让<br/>球</th>
                                                                        <td v-if="Number(match.subactive.nspfgg)">
                                                                            <table width="100%" class="betbtns" :class="{'border-1px-red': Number(match.subactive.nspfdg) && dgIsOpen && path !== 'dg'}">
                                                                                <tr>
                                                                                    <td :class="{'active' : curOpt(match.mid,'354','3')}" @click="selectDone(match.mid,'354','3',match.nspfpl.win)">主胜<i class="jc-binding">{{match.nspfpl.win}}</i></td>
                                                                                    <td :class="{'active' : curOpt(match.mid,'354','1')}" @click="selectDone(match.mid,'354','1',match.nspfpl.draw)">平<i class="jc-binding">{{match.nspfpl.draw}}</i></td>
                                                                                    <td :class="{'active' : curOpt(match.mid,'354','0')}" @click="selectDone(match.mid,'354','0',match.nspfpl.lost)">主负<i class="jc-binding">{{match.nspfpl.lost}}</i></td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                        <td v-else class="no-sale">
                                                                            <span class="flex c-4" v-if="path == 'ht'">该玩法未开售</span>
                                                                            <span class="flex c-4" v-if="path == 'dg'">该玩法未开单关</span>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                                <table class="betbtns-wrap-table" style="margin-top : -1px">
                                                                    <tbody>
                                                                    <tr>
                                                                        <th v-if="match.rangqiu > 0" class="th-zq-col2">
                                                                            主<br/>+{{match.rangqiu || '--'}}
                                                                        </th>
                                                                        <th v-else class="th-zq-col3">
                                                                            主<br/>{{match.rangqiu || '--'}}
                                                                        </th>
                                                                        <td v-if="Number(match.subactive.spfgg)">
                                                                            <table width="100%" class="betbtns" :class="{'border-1px-red': Number(match.subactive.spfdg) && dgIsOpen && path !== 'dg'}">
                                                                                <tr>
                                                                                    <td :class="{'active' : curOpt(match.mid,'269','3')}" @click="selectDone(match.mid,'269','3',match.spfpl.win)">主胜<i class="jc-binding">{{match.spfpl.win}}</i></td>
                                                                                    <td :class="{'active' : curOpt(match.mid,'269','1')}" @click="selectDone(match.mid,'269','1',match.spfpl.draw)">平<i class="jc-binding">{{match.spfpl.draw}}</i></td>
                                                                                    <td :class="{'active' : curOpt(match.mid,'269','0')}" @click="selectDone(match.mid,'269','0',match.spfpl.lost)">主负<i class="jc-binding">{{match.spfpl.lost}}</i></td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                        <td v-else class="no-sale">
                                                                            <span class="flex c-4">该玩法未开售</span>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="betbtns-wrap-more" @click="showAll(match)" v-if="gameNum(match.mid) < 1">全部<br>玩法</div>
                                                            <div class="betbtns-wrap-more red" @click="showAll(match)" v-else>已选<br>{{gameNum(match.mid)}}项</div>
                                                        </div>
                                                    </template>
                                                    <!--混合、单关投注 end-->
                                                    <!--让球胜平负-->
                                                    <template v-if="path == 'spf'">
                                                        <div class="betbtns-box flex-box">
                                                            <div class="betbtns-wrap flex">
                                                                <table class="betbtns-wrap-table">
                                                                    <tbody>
                                                                    <tr>
                                                                        <th v-if="match.rangqiu > 0" class="th-zq-col2">
                                                                            主<br/>+{{match.rangqiu || '--'}}
                                                                        </th>
                                                                        <th v-else class="th-zq-col3">
                                                                            主<br/>{{match.rangqiu || '--'}}
                                                                        </th>
                                                                        <td v-if="Number(match.subactive.spfgg)">
                                                                            <table width="100%" class="betbtns" :class="{'border-1px-red': Number(match.subactive.spfdg) && dgIsOpen && path !== 'dg'}">
                                                                                <tr>
                                                                                    <td :class="{'active' : curOpt(match.mid,'269','3')}" @click="selectDone(match.mid,'269','3',match.spfpl.win)">主胜<i class="jc-binding">{{match.spfpl.win}}</i></td>
                                                                                    <td :class="{'active' : curOpt(match.mid,'269','1')}" @click="selectDone(match.mid,'269','1',match.spfpl.draw)">平<i class="jc-binding">{{match.spfpl.draw}}</i></td>
                                                                                    <td :class="{'active' : curOpt(match.mid,'269','0')}" @click="selectDone(match.mid,'269','0',match.spfpl.lost)">主负<i class="jc-binding">{{match.spfpl.lost}}</i></td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                        <td v-else class="no-sale">
                                                                            <span class="flex c-4">该玩法未开售</span>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <!--让球胜平负 end-->
                                                    <!--胜平负-->
                                                    <template v-if="path == 'nspf'">
                                                        <div class="betsel-list-wrap flex-box">
                                                            <div class="betbtns-wrap flex">
                                                                <div class="betbtns flex-box" :class="{'border-1px-red': Number(match.subactive.nspfdg) && dgIsOpen}">
                                                                    <template v-if="Number(match.subactive.nspfgg)">
                                                                        <div class="betbtn-pl-item flex-box">
                                                                            <span class="flex" :class="{'active' : curOpt(match.mid,'354','3')}" @click="selectDone(match.mid,'354','3',match.nspfpl.win)">主胜<i class="jc-binding">{{match.nspfpl.win}}</i></span>
                                                                            <span class="flex" :class="{'active' : curOpt(match.mid,'354','1')}" @click="selectDone(match.mid,'354','1',match.nspfpl.draw)">平<i class="jc-binding">{{match.nspfpl.draw}}</i></span>
                                                                            <span class="flex" :class="{'active' : curOpt(match.mid,'354','0')}" @click="selectDone(match.mid,'354','0',match.nspfpl.lost)">主负<i class="jc-binding">{{match.nspfpl.lost}}</i></span>
                                                                        </div>
                                                                    </template>
                                                                    <template v-else>
                                                                        <div class="betbtn-pl-item flex-box">
                                                                            <span class="flex c-4">该玩法未开售</span>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <!--胜平负 end-->
                                                    <!--比分-->
                                                    <template v-if="path == 'bf'">
                                                        <div class="betsel-list-wrap flex-box">
                                                            <div class="betbtns-wrap flex">
                                                                <div class="betbtns" :class="{'border-1px-red': Number(match.subactive.bfdg) && dgIsOpen}">
                                                                    <div class="betbtn-pl-item" @click="showAll(match)" v-if="gameNum(match.mid) < 1">
                                                                        <span style="display: block" class="c-2">点击展开比分投注区</span>
                                                                    </div>
                                                                    <div class="red betbtn-pl-item" :style="{'width': contentW + 'px'}" @click="showAll(match)" v-else>
                                                                    <span class="text-ellipsis" style="display: block">
                                                                        <em v-for="(_item,_index) in gameBetCont(match.mid)">{{_item}}&nbsp;</em>
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
                                                                <div class="betbtns" :class="{'border-1px-red': Number(match.subactive.bqdg) && dgIsOpen}">
                                                                    <div class="betbtn-pl-item" @click="showAll(match)" v-if="gameNum(match.mid) < 1">
                                                                        <span style="display: block" class="c-2">点击展开半全场投注区</span>
                                                                    </div>
                                                                    <div class="red betbtn-pl-item" :style="{'width': contentW + 'px'}" @click="showAll(match)" v-else>
                                                                    <span class="text-ellipsis" style="display: block">
                                                                       <em v-for="(_item,_index) in gameBetCont(match.mid)">{{_item}}&nbsp;</em>
                                                                    </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <!--半全场 end-->
                                                    <!--进球数-->
                                                    <template v-if="path == 'jqs'">
                                                        <div class="betsel-list-wrap flex-box">
                                                            <div class="betbtns-wrap flex">
                                                                <div class="betbtns" :class="{'border-1px-red': Number(match.subactive.jqdg) && dgIsOpen}">
                                                                    <div class="betbtn-pl-item flex-box">
                                                                        <span class="flex border-r border-b" :class="{'active' : curOpt(match.mid,'270','0')}" @click="selectDone(match.mid,'270','0',match.jqspl.s0)">0<i class="jc-binding">{{match.jqspl.s0}}</i></span>
                                                                        <span class="flex border-r border-b" :class="{'active' : curOpt(match.mid,'270','1')}" @click="selectDone(match.mid,'270','1',match.jqspl.s1)">1<i class="jc-binding">{{match.jqspl.s1}}</i></span>
                                                                        <span class="flex border-r border-b" :class="{'active' : curOpt(match.mid,'270','2')}" @click="selectDone(match.mid,'270','2',match.jqspl.s2)">2<i class="jc-binding">{{match.jqspl.s2}}</i></span>
                                                                        <span class="flex border-r border-b" :class="{'active' : curOpt(match.mid,'270','3')}" @click="selectDone(match.mid,'270','3',match.jqspl.s3)">3<i class="jc-binding">{{match.jqspl.s3}}</i></span>
                                                                    </div>
                                                                    <div class="betbtn-pl-item flex-box" style="margin-top: -1px;">
                                                                        <span class="flex border-r" :class="{'active' : curOpt(match.mid,'270','4')}" @click="selectDone(match.mid,'270','4',match.jqspl.s4)">4<i class="jc-binding">{{match.jqspl.s4}}</i></span>
                                                                        <span class="flex border-r" :class="{'active' : curOpt(match.mid,'270','5')}" @click="selectDone(match.mid,'270','5',match.jqspl.s5)">5<i class="jc-binding">{{match.jqspl.s5}}</i></span>
                                                                        <span class="flex border-r" :class="{'active' : curOpt(match.mid,'270','6')}" @click="selectDone(match.mid,'270','6',match.jqspl.s6)">6<i class="jc-binding">{{match.jqspl.s6}}</i></span>
                                                                        <span class="flex border-r" :class="{'active' : curOpt(match.mid,'270','7')}" @click="selectDone(match.mid,'270','7',match.jqspl.s7)">7+<i class="jc-binding">{{match.jqspl.s7}}</i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <!--进球数 end-->
                                                </div>
                                            </div>
                                            <!--分析-->
                                            <div class="fxinfo-box"  :class="infoState(match.infoid) && openState(index) ? 'fxinfo-box-on' : ''">
                                                <div class="jc-info-arrow"><i class="ico-arrow2"></i></div>
                                                <div class="jc-item-fxinfo">
                                                    <ul>
                                                        <li>历史交锋：{{fxInfo.against || '-'}}</li>
                                                        <li>近战战绩：<em v-for="fx in fxInfo.history" class="mr-sm">{{fx}}</em></li>
                                                        <li>平均赔率：<span class="mr-sm">主胜{{fxInfo.averagepl[0]}}</span><span class="mr-sm">平{{fxInfo.averagepl[1]}}</span><span class="mr-sm">主负{{fxInfo.averagepl[2]}}</span></li>
                                                    </ul>
                                                    <div class="more tc">
                                                        <a>
                                                            详细赛事分析 >>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--分析 end-->
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
                            <span class="itm-team-l"><i class="c-4">主<em v-if="Number(curData.homestanding)>0">[{{curData.homestanding}}]</em></i>{{curData.homename}}</span>
                            <span class="itm-vs">VS</span>
                            <span class="itm-team-r">{{curData.awayname}}<i v-if="Number(curData.awaystanding)>0">[{{curData.awaystanding}}]</i></span>
                        </div>
                    </template>
                    <template slot="main">
                        <div class="jc-dialog-cont mt-sm">
                            <template v-if="path == 'ht'|| path == 'dg'">
                                <div class="popbox-betsel-tablewrap">
                                    <table class="popbox-betsel-table" style="margin-bottom: 0">
                                        <tbody>
                                        <tr>
                                            <th class="th-zq-col1">非<br/>让<br/>球</th>
                                            <template v-if="Number(curData.subactive.nspfgg)">
                                                <td>
                                                    <table class="all-table" width="100%" :class="{'border-1px-red': Number(curData.subactive.nspfdg) && dgIsOpen && path !== 'dg'}">
                                                        <tr>
                                                            <td :class="{'active':curOpt('354','3')}" @click="selectDoneTemp('354','3',curData.nspfpl.win)">胜<i class="ng-binding">{{curData.nspfpl.win}}</i></td>
                                                            <td :class="{'active':curOpt('354','1')}" @click="selectDoneTemp('354','1',curData.nspfpl.win)">平<i class="ng-binding">{{curData.nspfpl.draw}}</i></td>
                                                            <td :class="{'active':curOpt('354','0')}" @click="selectDoneTemp('354','0',curData.nspfpl.win)">负<i class="ng-binding">{{curData.nspfpl.lost}}</i></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </template>
                                            <template v-else>
                                                <td>
                                                    <table class="all-table" width="100%">
                                                        <tr>
                                                            <td class="c-4" v-if="path == 'ht'">该玩法未开售</td>
                                                            <td class="c-4" v-if="path == 'dg'">该玩法未开单关</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </template>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table class="popbox-betsel-table" style="margin-top: -1px">
                                        <tbody>
                                        <tr>
                                            <template v-if="curData.rangqiu > 0">
                                                <th class="th-zq-col2"  style="border-bottom: none;">让<br/>球<br/>+{{curData.rangqiu || '--'}}</th>
                                            </template>
                                            <template v-else>
                                                <th class="th-zq-col3" style="border-bottom: none;">让<br/>球<br/>{{curData.rangqiu || '--'}}</th>
                                            </template>
                                            <template v-if="Number(curData.subactive.spfgg)">
                                                <table class="all-table" width="100%" :class="{'border-1px-red': Number(curData.subactive.spfdg) && dgIsOpen && path !== 'dg'}">
                                                    <tr>
                                                        <td :class="{'active':curOpt('269','3')}" @click="selectDoneTemp('269','3',curData.spfpl.win)">胜<i class="ng-binding">{{curData.spfpl.win}}</i></td>
                                                        <td :class="{'active':curOpt('269','1')}" @click="selectDoneTemp('269','1',curData.spfpl.win)">平<i class="ng-binding">{{curData.spfpl.draw}}</i></td>
                                                        <td :class="{'active':curOpt('269','0')}" @click="selectDoneTemp('269','0',curData.spfpl.win)">负<i class="ng-binding">{{curData.spfpl.lost}}</i></td>
                                                    </tr>
                                                </table>
                                            </template>
                                            <template v-else>
                                                <td>
                                                    <table class="all-table" width="100%">
                                                        <tr>
                                                            <td class="c-4" v-if="path == 'ht'">该玩法未开售</td>
                                                            <td class="c-4" v-if="path == 'dg'">该玩法未开单关</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </template>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                            <template v-if="path == 'ht' || path == 'bf' || path == 'dg'">
                                <div class="popbox-betsel-tablewrap" v-if="curData.subactive.bfgg">
                                    <table class="popbox-betsel-table">
                                        <tbody>
                                        <tr>
                                            <th class="th-zq-col4">比分</th>
                                            <td>
                                                <table class="all-table" width="100%" :class="{'border-1px-red': Number(curData.subactive.bfdg) && dgIsOpen && path !== 'dg'}">
                                                    <tr>
                                                        <td :class="{'active':curOpt('271','1:0')}" @click="selectDoneTemp('271','1:0',curData.bfpl.a10)">1:0<i class="ng-binding">{{curData.bfpl.a10}}</i></td>
                                                        <td :class="{'active':curOpt('271','2:0')}" @click="selectDoneTemp('271','2:0',curData.bfpl.a20)">2:0<i class="ng-binding">{{curData.bfpl.a20}}</i></td>
                                                        <td :class="{'active':curOpt('271','2:1')}" @click="selectDoneTemp('271','2:1',curData.bfpl.a21)">2:1<i class="ng-binding">{{curData.bfpl.a21}}</i></td>
                                                        <td :class="{'active':curOpt('271','3:0')}" @click="selectDoneTemp('271','3:0',curData.bfpl.a30)">3:0<i class="ng-binding">{{curData.bfpl.a30}}</i></td>
                                                        <td :class="{'active':curOpt('271','3:1')}" @click="selectDoneTemp('271','3:1',curData.bfpl.a31)">3:1<i class="ng-binding">{{curData.bfpl.a31}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active':curOpt('271','3:2')}" @click="selectDoneTemp('271','3:2',curData.bfpl.a32)">3:2<i class="ng-binding">{{curData.bfpl.a32}}</i></td>
                                                        <td :class="{'active':curOpt('271','4:0')}" @click="selectDoneTemp('271','4:0',curData.bfpl.a40)">4:0<i class="ng-binding">{{curData.bfpl.a40}}</i></td>
                                                        <td :class="{'active':curOpt('271','4:1')}" @click="selectDoneTemp('271','4:1',curData.bfpl.a41)">4:1<i class="ng-binding">{{curData.bfpl.a41}}</i></td>
                                                        <td :class="{'active':curOpt('271','4:2')}" @click="selectDoneTemp('271','4:2',curData.bfpl.a42)">4:2<i class="ng-binding">{{curData.bfpl.a42}}</i></td>
                                                        <td :class="{'active':curOpt('271','5:0')}" @click="selectDoneTemp('271','5:0',curData.bfpl.a50)">5:0<i class="ng-binding">{{curData.bfpl.a50}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active':curOpt('271','5:1')}" @click="selectDoneTemp('271','5:1',curData.bfpl.a51)">5:1<i class="ng-binding">{{curData.bfpl.a51}}</i></td>
                                                        <td :class="{'active':curOpt('271','5:2')}" @click="selectDoneTemp('271','5:2',curData.bfpl.a52)">5:2<i class="ng-binding">{{curData.bfpl.a52}}</i></td>
                                                        <td :class="{'active':curOpt('271','胜其它')}" @click="selectDoneTemp('271','胜其它',curData.bfpl.aother)" colspan="3">胜其它<i class="ng-binding">{{curData.bfpl.aother}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active':curOpt('271','0:0')}" @click="selectDoneTemp('271','0:0',curData.bfpl.c00)">0:0<i class="ng-binding">{{curData.bfpl.c00}}</i></td>
                                                        <td :class="{'active':curOpt('271','1:1')}" @click="selectDoneTemp('271','1:1',curData.bfpl.c11)">1:1<i class="ng-binding">{{curData.bfpl.c11}}</i></td>
                                                        <td :class="{'active':curOpt('271','2:2')}" @click="selectDoneTemp('271','2:2',curData.bfpl.c22)">2:2<i class="ng-binding">{{curData.bfpl.c22}}</i></td>
                                                        <td :class="{'active':curOpt('271','3:3')}" @click="selectDoneTemp('271','3:3',curData.bfpl.c33)">3:3<i class="ng-binding">{{curData.bfpl.c33}}</i></td>
                                                        <td :class="{'active':curOpt('271','平其它')}" @click="selectDoneTemp('271','平其它',curData.bfpl.cother)">平其它<i class="ng-binding">{{curData.bfpl.cother}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active':curOpt('271','0:1')}" @click="selectDoneTemp('271','0:1',curData.bfpl.b10)">0:1<i class="ng-binding">{{curData.bfpl.b10}}</i></td>
                                                        <td :class="{'active':curOpt('271','0:2')}" @click="selectDoneTemp('271','0:2',curData.bfpl.b20)">0:2<i class="ng-binding">{{curData.bfpl.b20}}</i></td>
                                                        <td :class="{'active':curOpt('271','1:2')}" @click="selectDoneTemp('271','1:2',curData.bfpl.b21)">1:2<i class="ng-binding">{{curData.bfpl.b21}}</i></td>
                                                        <td :class="{'active':curOpt('271','0:3')}" @click="selectDoneTemp('271','0:3',curData.bfpl.b30)">0:3<i class="ng-binding">{{curData.bfpl.b30}}</i></td>
                                                        <td :class="{'active':curOpt('271','1:3')}" @click="selectDoneTemp('271','1:3',curData.bfpl.b31)">1:3<i class="ng-binding">{{curData.bfpl.b31}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active':curOpt('271','2:3')}" @click="selectDoneTemp('271','2:3',curData.bfpl.b32)">2:3<i class="ng-binding">{{curData.bfpl.b32}}</i></td>
                                                        <td :class="{'active':curOpt('271','0:4')}" @click="selectDoneTemp('271','0:4',curData.bfpl.b40)">0:4<i class="ng-binding">{{curData.bfpl.b40}}</i></td>
                                                        <td :class="{'active':curOpt('271','1:4')}" @click="selectDoneTemp('271','1:4',curData.bfpl.b41)">1:4<i class="ng-binding">{{curData.bfpl.b41}}</i></td>
                                                        <td :class="{'active':curOpt('271','2:4')}" @click="selectDoneTemp('271','2:4',curData.bfpl.b42)">2:4<i class="ng-binding">{{curData.bfpl.b42}}</i></td>
                                                        <td :class="{'active':curOpt('271','0:5')}" @click="selectDoneTemp('271','0:5',curData.bfpl.b50)">0:5<i class="ng-binding">{{curData.bfpl.b50}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active':curOpt('271','1:5')}" @click="selectDoneTemp('271','1:5',curData.bfpl.b51)">1:5<i class="ng-binding">{{curData.bfpl.b51}}</i></td>
                                                        <td :class="{'active':curOpt('271','2:5')}" @click="selectDoneTemp('271','2:5',curData.bfpl.b52)">2:5<i class="ng-binding">{{curData.bfpl.b52}}</i></td>
                                                        <td :class="{'active':curOpt('271','负其它')}" @click="selectDoneTemp('271','负其它',curData.bfpl.bother)" colspan="3">负其它<i class="ng-binding">{{curData.bfpl.bother}}</i></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                            <template v-if="path == 'ht'|| path == 'dg'">
                                <div class="popbox-betsel-tablewrap" v-if="curData.subactive.jqgg==1">
                                    <table class="popbox-betsel-table">
                                        <tbody>
                                        <tr>
                                            <th class="th-zq-col5">总进球</th>
                                            <td>
                                                <table class="all-table" width="100%" :class="{'border-1px-red': Number(curData.subactive.jqdg) && dgIsOpen && path !== 'dg'}">
                                                    <tr>
                                                        <td :class="{'active':curOpt('270','0')}" @click="selectDoneTemp('270','0',curData.jqspl.s0)">0球<i class="ng-binding">{{curData.jqspl.s0}}</i></td>
                                                        <td :class="{'active':curOpt('270','1')}" @click="selectDoneTemp('270','1',curData.jqspl.s1)">1球<i class="ng-binding">{{curData.jqspl.s1}}</i></td>
                                                        <td :class="{'active':curOpt('270','2')}" @click="selectDoneTemp('270','2',curData.jqspl.s2)">2球<i class="ng-binding">{{curData.jqspl.s2}}</i></td>
                                                        <td :class="{'active':curOpt('270','3')}" @click="selectDoneTemp('270','3',curData.jqspl.s3)">3球<i class="ng-binding">{{curData.jqspl.s3}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active':curOpt('270','4')}" @click="selectDoneTemp('270','4',curData.jqspl.s4)">4球<i class="ng-binding">{{curData.jqspl.s4}}</i></td>
                                                        <td :class="{'active':curOpt('270','5')}" @click="selectDoneTemp('270','5',curData.jqspl.s5)">5球<i class="ng-binding">{{curData.jqspl.s5}}</i></td>
                                                        <td :class="{'active':curOpt('270','6')}" @click="selectDoneTemp('270','6',curData.jqspl.s6)">6球<i class="ng-binding">{{curData.jqspl.s6}}</i></td>
                                                        <td :class="{'active':curOpt('270','7')}" @click="selectDoneTemp('270','7',curData.jqspl.s7)">7+球<i class="ng-binding">{{curData.jqspl.s7}}</i></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                            <template v-if="path == 'ht' || path == 'bqc'|| path == 'dg'">
                                <div class="popbox-betsel-tablewrap" v-if="curData.subactive.bqgg==1">
                                    <table class="popbox-betsel-table">
                                        <tbody>
                                        <tr>
                                            <th class="th-zq-col6">半全场</th>
                                            <td>
                                                <table class="all-table" width="100%" :class="{'border-1px-red': Number(curData.subactive.bqdg) && dgIsOpen && path !== 'dg'}">
                                                    <tr>
                                                        <td :class="{'active':curOpt('272','3-3')}" @click="selectDoneTemp('272','3-3',curData.bqcpl.aa)">胜-胜<i class="ng-binding">{{curData.bqcpl.aa}}</i></td>
                                                        <td :class="{'active':curOpt('272','3-1')}" @click="selectDoneTemp('272','3-1',curData.bqcpl.ac)">胜-平<i class="ng-binding">{{curData.bqcpl.ac}}</i></td>
                                                        <td :class="{'active':curOpt('272','3-0')}" @click="selectDoneTemp('272','3-0',curData.bqcpl.ab)">胜-负<i class="ng-binding">{{curData.bqcpl.ab}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active':curOpt('272','1-3')}" @click="selectDoneTemp('272','1-3',curData.bqcpl.ca)">平-胜<i class="ng-binding">{{curData.bqcpl.ca}}</i></td>
                                                        <td :class="{'active':curOpt('272','1-1')}" @click="selectDoneTemp('272','1-1',curData.bqcpl.cc)">平-平<i class="ng-binding">{{curData.bqcpl.cc}}</i></td>
                                                        <td :class="{'active':curOpt('272','1-0')}" @click="selectDoneTemp('272','1-0',curData.bqcpl.cb)">平-负<i class="ng-binding">{{curData.bqcpl.cb}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active':curOpt('272','0-3')}" @click="selectDoneTemp('272','0-3',curData.bqcpl.ba)">负-胜<i class="ng-binding">{{curData.bqcpl.ba}}</i></td>
                                                        <td :class="{'active':curOpt('272','0-1')}" @click="selectDoneTemp('272','0-1',curData.bqcpl.bc)">负-平<i class="ng-binding">{{curData.bqcpl.bc}}</i></td>
                                                        <td :class="{'active':curOpt('272','0-0')}" @click="selectDoneTemp('272','0-0',curData.bqcpl.bb)">负-负<i class="ng-binding">{{curData.bqcpl.bb}}</i></td>
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
                        <template v-if="path == 'dg'">
                            <p v-if="!games" class="f-mini">请至少选择1场比赛</p>
                            <p v-else>已选择<em class="red"> {{games}} </em>场</p>
                        </template>
                        <template v-else>
                            <template v-if="!games && dgIsOpen">
                                <p class="red f-mini">红色区域可投单关</p>
                            </template>
                            <template v-else>
                                <p>已选择<em class="red"> {{games}} </em>场</p>
                                <p class="c-3 f-mini" v-if="!isAllDgMatch">至少选择2场</p>
                            </template>
                        </template>
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
                                    {{item.simpleleague}}
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
        name: 'jczq',
        components:{
            StopSaleTips,
            BaseModal
        },
        data () {
            return {
                barFixed : -1,
                betMap:{
                    269 : 'spfdg',
                    354 : 'nspfdg',
                    270 : 'jqdg',
                    271 : 'bfdg',
                    272 : 'bqdg'
                },

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
                    infoid : 0,
                    fixtureid : 0,
                    matchnum : "",
                    simpleleague : "",
                    homesxname : "",
                    homestanding : 0,
                    awaysxname : "",
                    awaystanding : 0,
                    rangqiu : -1,
                    matchgroup : "",
                    seasonid : 0,
                    homename : "",
                    awayname : "",
                    matchdate : "",
                    matchtime : "",
                    stagegbname : "",
                    homeid : 0,
                    awayid : 0,
                    subactive : {
                        "bfdg" : "1",
                        "bfgg" : "1",
                        "bqdg" : "1",
                        "bqgg" : "1",
                        "hcgg" : "1",
                        "jqdg" : "1",
                        "jqgg" : "1",
                        "nspfdg" : "0", //非让球单关
                        "nspfgg" : "1", //非让球
                        "spfdg" : "0",
                        "spfgg" : "1"
                    },
                    mid : 142157,
                    processname : 3001,
                    processdate : "2018-12-19",
                    ptggendtime : "2018-12-19 23:55:00",
                    nspfpl : {
                        win: "2.08",
                        draw: "3.08",
                        lost: "3.10"
                    },
                    nspfpl_add : {
                        win : "",
                        draw : "",
                        lost : ""
                    },
                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                    hcount_nspf_percent : [ "-", "-", "-" ],
                    spfpl : {
                        win : "4.45",
                        draw : "3.65",
                        lost : "1.59"
                    },
                    spfpl_add : {
                        win : "",
                        draw : "",
                        lost : ""
                    },
                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                    hcount_spf_percent : [ "-", "-", "-" ],
                    bfpl : {
                        "a10" : "6.50",
                        "a20" : "9.50",
                        "a21" : "7.50",
                        "a30" : "18.00",
                        "a31" : "16.00",
                        "a32" : "25.00",
                        "a40" : "50.00",
                        "a41" : "39.00",
                        "a42" : "70.00",
                        "a50" : "120.00",
                        "a51" : "120.00",
                        "a52" : "200.00",
                        "aother" : "70.00",
                        "c00" : "8.00",
                        "c11" : "5.50",
                        "c22" : "13.00",
                        "c33" : "60.00",
                        "cother" : "400.00",
                        "b10" : "8.00",
                        "b20" : "15.00",
                        "b21" : "9.50",
                        "b30" : "38.00",
                        "b31" : "25.00",
                        "b32" : "29.00",
                        "b40" : "100.00",
                        "b41" : "80.00",
                        "b42" : "100.00",
                        "b50" : "400.00",
                        "b51" : "250.00",
                        "b52" : "400.00",
                        "bother" : "120.00"
                    },
                    bqcpl : {
                        "aa" : "3.20",
                        "ac" : "13.00",
                        "ab" : "34.00",
                        "ca" : "4.60",
                        "cc" : "4.80",
                        "cb" : "6.50",
                        "ba" : "28.00",
                        "bc" : "13.00",
                        "bb" : "5.00"
                    },
                    jqspl : {
                        "s0" : "8.00",
                        "s1" : "3.95",
                        "s2" : "3.10",
                        "s3" : "3.70",
                        "s4" : "6.00",
                        "s5" : "11.00",
                        "s6" : "20.00",
                        "s7" : "29.00"
                    }
                },//显示全部的数据

                matchInfo:{}, //赛事列表数据
                fxInfo:{  //分析数组
                    against: "", //历史交锋
                    averagepl: [3.33, 3.39, 2.08],
                    history: ["7胜1平2负", "1胜4平5负"]
                },
                //投注
                plan:this.$store.state.jczq.plan, //投注方案内容
                tempPlan:{}, //临时投注方案内容，用于取消后恢复
                tempMatchData:{},//投注数组，投注页数据显示相关

                popupVisible: true,
                dgIsOpen:true,//单关是否开启
            }
        },
        computed:{
            //玩法
            play(){
                let arr = [
                    {"name":"混合过关","type":"1",'path':'ht'},
                    {"name":"让球胜平负","type":"2",'path':'spf'},
                    {"name":"胜平负","type":"3",'path':'nspf'},
                    {"name":"比分","type":"4",'path':'bf'},
                    {"name":"进球数","type":"5",'path':'jqs'},
                    {"name":"半全场","type":"6",'path':'bqc'},
                    {"name":"冠军/冠亚军","type":"7",'path':'gy'}
                ]
                if(this.dgIsOpen){
                    arr.push({"name":"单关","type":"8",'path':'dg'})
                }
                return arr
            },
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
                                if(this.matchInfo[j].matchdata[m].simpleleague == this.filterList[i].simpleleague){
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
                        return this.checkStatus(this.tempPlan,this.curData.mid,play,sign)
                    }
                }
            },
            //已选场数
            games(){
                return this.$_.size(this.plan)
            },
            //可购买单关的场次数量
            dgMatchNum(){
                let n = 0
                //判断选择的场次中是否有可投的单关
                if(this.$route.path !== '/jczq/bet' && this.$route.path !== '/jczq/optimalBonus'){
                    for(let key in this.plan){
                        for(let _key in this.plan[key]){
                            for(let p in this.matchInfo){
                                for(let _p in this.matchInfo[p].matchdata){
                                    if(this.matchInfo[p].matchdata[_p].mid == key){
                                        if(this.matchInfo[p].matchdata[_p].subactive[this.betMap[_key]] == 1){
                                            n +=1
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                return n
            },
            //判断是否全为单关投注
            isAllDgMatch(){
                let status = true
                //判断选择的场次中是否有不可投单关的
                if(this.$route.path !== '/jczq/bet' && this.$route.path !== '/jczq/optimalBonus'){
                    for(let key in this.plan){
                        for(let _key in this.plan[key]){
                            for(let p in this.matchInfo){
                                for(let _p in this.matchInfo[p].matchdata){
                                    if(this.matchInfo[p].matchdata[_p].mid == key){
                                        if(this.matchInfo[p].matchdata[_p].subactive[this.betMap[_key]] == 0){
                                            status = false
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                return status
            },
            //计算每场场玩法选项数量
            gameNum(){
                return (mid)=>{
                    let n = 0
                    if(this.plan.hasOwnProperty(mid)){
                        for(let key in this.plan[mid]){
                            n = n + this.$_.size(this.plan[mid][key])
                        }
                    }
                    return n
                }
            },
            //比分投注，半全场玩法选项显示
            gameBetCont(){
                return (mid)=>{
                    let arr = []
                    if(this.plan.hasOwnProperty(mid)){
                        for(let key in this.plan[mid]){
                            for(let _key in this.plan[mid][key]){
                                arr.push(_key)
                            }
                        }
                    }
                    let resArr = []
                    if(this.path == 'bf'){
                        const initArr= ['1:0','2:0','2:1','3:0','3:1','3:2','4:0','4:1','4:2','5:0','5:1','5:2','胜其它','0:0','1:1','2:2','3:3','平其它',
                            '0:1' ,'0:2','1:2','0:3','1:3','2:3','0:4','1:4','2:4','0:5','1:5','2:5','负其它']
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
                let arr =  this.$route.query.type || 'ht'
                return arr
            },
            playObj(){
                return {
                    'ht' : {"name":"混合过关","type":"1"},
                    'spf' : {"name":"让球胜平负","type":"2"},
                    'nspf' : {"name":"胜平负","type":"3"},
                    'bf' : {"name":"比分","type":"4"},
                    'jqs' : {"name":"进球数","type":"5"},
                    'bqc' : {"name":"半全场","type":"6"},
                    'gy' : {"name":"冠军/冠亚军","type":"7"},
                    'dg' : {"name":"单关","type":"8"},
                }
            },
            //选中的玩法
            selected(){
                return this.$route.path !== '/jczq/bet' && this.$route.path !== '/jczq/optimalBonus' ? this.playObj[this.path].name : ''
            },
            //选中的玩法对应的type
            type(){
                return this.$route.path !== '/jczq/bet' && this.$route.path !== '/jczq/optimalBonus' ? this.playObj[this.path].type : ''
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
                    return this.filterArr.indexOf(item.simpleleague) > -1
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
                        this.filterArr.push(this.filterList[i].simpleleague)
                    }
                }
                //更新临时列表
                this.$set(this,'tempfilterList',JSON.parse(JSON.stringify(this.filterList)))
                //若已选有场次，删除未筛选场次数据
                if(JSON.stringify(this.plan) !== '{}'){
                    for(let j in this.matchInfo){
                        for(let m in this.matchInfo[j].matchdata){
                            if(!this.$base.isExit(this.filterArr,this.matchInfo[j].matchdata[m].simpleleague)){
                                if(this.plan.hasOwnProperty(this.matchInfo[j].matchdata[m].mid)){
                                    this.$delete(this.plan,this.matchInfo[j].matchdata[m].mid)
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
                        if(this.filterList[i].simpleleague == '英超' || this.filterList[i].simpleleague == '意甲' || this.filterList[i].simpleleague == '德甲' ||
                            this.filterList[i].simpleleague == '西甲' || this.filterList[i].simpleleague == '法甲'){
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
                this.addPlan(this.tempPlan,this.curData.mid,play,sign,pl);
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
                this.$store.commit('clearJczqBetData')
            },
            //选择玩法
            chosePlay(type){
                this.$refs.wrapper.scrollTop = 0
                this.$router.push({
                    path: '/jczq',
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
                if(!this.dgIsOpen){
                    if(this.games < 2){
                        this.$toast('请至少选择2场比赛');
                        return
                    }
                }else {
                    if(!this.games){
                        this.$toast('请至少选择1场比赛');
                        return
                    }else {
                        if(this.path !== 'dg' && !this.isAllDgMatch){
                            if(this.games < 2){
                                this.$toast('非单关场次请至少选择2场比赛');
                                return
                            }
                        }
                    }
                }
                this.tempMatchData = {}
                //提取已选赛事相关数据
                for(let i in this.matchInfo){
                    for(let j in this.matchInfo[i].matchdata){
                        if(this.plan.hasOwnProperty(this.matchInfo[i].matchdata[j].mid)){
                            this.tempMatchData[this.matchInfo[i].matchdata[j].mid] = this.matchInfo[i].matchdata[j]
                        }
                    }
                }
                this.$store.commit('setJcZqPlan',this.plan)
                this.$store.commit('setJcZqTempMatchData',this.tempMatchData)
                this.$router.push({
                    path : '/jczq/bet',
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
                        this.$store.commit('delKeepAlivePage','jczq') //清除投注页面缓存
                        this.$store.commit('delKeepAlivePage','jcOrder') //清除订单页面缓存
                        this.$store.commit('clearJczqBetData')
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
                        'matchtime': '2018-12-17', //投注日期
                        'matchday': '周三', //投注星期
                        'matchdata' :
                            [
                                {
                                    infoid : 128067,
                                    fixtureid : 768689,
                                    matchnum : "周三001",
                                    simpleleague : "葡杯",
                                    homesxname : "艾维斯",
                                    homestanding : 0,
                                    awaysxname : "查维斯",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5033,
                                    homename : "阿维斯",
                                    awayname : "沙维什",
                                    matchdate : "12-20",
                                    matchtime : "00:00",
                                    stagegbname : "第五圈",
                                    homeid : 223,
                                    awayid : 239,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142157,
                                    processname : 3001,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win: "2.08",
                                        draw: "3.08",
                                        lost: "3.10"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "4.45",
                                        draw : "3.65",
                                        lost : "1.59"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "6.50",
                                        "a20" : "9.50",
                                        "a21" : "7.50",
                                        "a30" : "18.00",
                                        "a31" : "16.00",
                                        "a32" : "25.00",
                                        "a40" : "50.00",
                                        "a41" : "39.00",
                                        "a42" : "70.00",
                                        "a50" : "120.00",
                                        "a51" : "120.00",
                                        "a52" : "200.00",
                                        "aother" : "70.00",
                                        "c00" : "8.00",
                                        "c11" : "5.50",
                                        "c22" : "13.00",
                                        "c33" : "60.00",
                                        "cother" : "400.00",
                                        "b10" : "8.00",
                                        "b20" : "15.00",
                                        "b21" : "9.50",
                                        "b30" : "38.00",
                                        "b31" : "25.00",
                                        "b32" : "29.00",
                                        "b40" : "100.00",
                                        "b41" : "80.00",
                                        "b42" : "100.00",
                                        "b50" : "400.00",
                                        "b51" : "250.00",
                                        "b52" : "400.00",
                                        "bother" : "120.00"
                                    },
                                    bqcpl : {
                                        "aa" : "3.20",
                                        "ac" : "13.00",
                                        "ab" : "34.00",
                                        "ca" : "4.60",
                                        "cc" : "4.80",
                                        "cb" : "6.50",
                                        "ba" : "28.00",
                                        "bc" : "13.00",
                                        "bb" : "5.00"
                                    },
                                    jqspl : {
                                        "s0" : "8.00",
                                        "s1" : "3.95",
                                        "s2" : "3.10",
                                        "s3" : "3.70",
                                        "s4" : "6.00",
                                        "s5" : "11.00",
                                        "s6" : "20.00",
                                        "s7" : "29.00"
                                    }
                                },
                                {
                                    infoid : 128068,
                                    fixtureid : 768733,
                                    matchnum : "周三002",
                                    simpleleague : "世俱杯",
                                    homesxname : "鹿  岛",
                                    homestanding : 0,
                                    awaysxname : "皇  马",
                                    awaystanding : 0,
                                    rangqiu : 2,
                                    matchgroup : "",
                                    seasonid : 5045,
                                    homename : "鹿岛鹿角",
                                    awayname : "皇马",
                                    matchdate : "12-20",
                                    matchtime : "00:30",
                                    stagegbname : "半决赛",
                                    homeid : 1029,
                                    awayid : 883,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "1",
                                        "spfgg" : "1"
                                    },
                                    mid : 142158,
                                    processname : 3002,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "12.00",
                                        draw : "6.25",
                                        lost : "1.13"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.12",
                                        draw : "4.00",
                                        lost : "2.45"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "34.00",
                                        "a20" : "70.00",
                                        "a21" : "27.00",
                                        "a30" : "250.00",
                                        "a31" : "100.00",
                                        "a32" : "70.00",
                                        "a40" : "700.00",
                                        "a41" : "400.00",
                                        "a42" : "300.00",
                                        "a50" : "1000.00",
                                        "a51" : "1000.00",
                                        "a52" : "900.00",
                                        "aother" : "250.00",
                                        "c00" : "21.00",
                                        "c11" : "10.00",
                                        "c22" : "19.00",
                                        "c33" : "70.00",
                                        "cother" : "300.00",
                                        "b10" : "8.50",
                                        "b20" : "6.50",
                                        "b21" : "8.00",
                                        "b30" : "7.20",
                                        "b31" : "8.50",
                                        "b32" : "19.00",
                                        "b40" : "9.50",
                                        "b41" : "13.00",
                                        "b42" : "29.00",
                                        "b50" : "16.00",
                                        "b51" : "21.00",
                                        "b52" : "50.00",
                                        "bother" : "9.50"
                                    },
                                    bqcpl : {
                                        "aa" : "20.00",
                                        "ac" : "23.00",
                                        "ab" : "22.00",
                                        "ca" : "25.00",
                                        "cc" : "10.00",
                                        "cb" : "3.80",
                                        "ba" : "80.00",
                                        "bc" : "23.00",
                                        "bb" : "1.40"
                                    },
                                    jqspl : {
                                        "s0" : "21.00",
                                        "s1" : "8.00",
                                        "s2" : "4.30",
                                        "s3" : "3.50",
                                        "s4" : "3.95",
                                        "s5" : "5.80",
                                        "s6" : "9.50",
                                        "s7" : "11.00"
                                    }
                                },
                                {
                                    infoid : 128069,
                                    fixtureid : 737692,
                                    matchnum : "周三003",
                                    simpleleague : "德甲",
                                    homesxname : "沙尔克",
                                    homestanding : 14,
                                    awaysxname : "勒  沃",
                                    awaystanding : 11,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 4852,
                                    homename : "沙尔克04",
                                    awayname : "勒沃库森",
                                    matchdate : "12-20",
                                    matchtime : "01:30",
                                    stagegbname : "联赛赛程",
                                    homeid : 478,
                                    awayid : 986,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142159,
                                    processname : 3003,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "2.30",
                                        draw : "3.25",
                                        lost : "2.60"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "5.00",
                                        draw : "3.95",
                                        lost : "1.48"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.00",
                                        "a20" : "11.00",
                                        "a21" : "7.50",
                                        "a30" : "21.00",
                                        "a31" : "17.00",
                                        "a32" : "21.00",
                                        "a40" : "60.00",
                                        "a41" : "39.00",
                                        "a42" : "60.00",
                                        "a50" : "150.00",
                                        "a51" : "120.00",
                                        "a52" : "200.00",
                                        "aother" : "70.00",
                                        "c00" : "10.00",
                                        "c11" : "6.00",
                                        "c22" : "12.00",
                                        "c33" : "50.00",
                                        "cother" : "250.00",
                                        "b10" : "8.25",
                                        "b20" : "13.00",
                                        "b21" : "8.00",
                                        "b30" : "27.00",
                                        "b31" : "20.00",
                                        "b32" : "24.00",
                                        "b40" : "70.00",
                                        "b41" : "50.00",
                                        "b42" : "70.00",
                                        "b50" : "250.00",
                                        "b51" : "150.00",
                                        "b52" : "250.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "3.80",
                                        "ac" : "11.00",
                                        "ab" : "24.00",
                                        "ca" : "5.40",
                                        "cc" : "5.10",
                                        "cb" : "5.70",
                                        "ba" : "23.00",
                                        "bc" : "11.00",
                                        "bb" : "4.40"
                                    },
                                    jqspl : {
                                        "s0" : "10.00",
                                        "s1" : "4.30",
                                        "s2" : "3.40",
                                        "s3" : "3.40",
                                        "s4" : "5.20",
                                        "s5" : "10.00",
                                        "s6" : "17.00",
                                        "s7" : "26.00"
                                    }
                                },
                                {
                                    infoid : 128070,
                                    fixtureid : 767900,
                                    matchnum : "周三004",
                                    simpleleague : "法联杯",
                                    homesxname : "亚  眠",
                                    homestanding : 0,
                                    awaysxname : "里  昂",
                                    awaystanding : 0,
                                    rangqiu : 1,
                                    matchgroup : "",
                                    seasonid : 4955,
                                    homename : "亚眠",
                                    awayname : "里昂",
                                    matchdate : "12-20",
                                    matchtime : "01:45",
                                    stagegbname : "十六强",
                                    homeid : 1307,
                                    awayid : 997,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142160,
                                    processname : 3004,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "4.35",
                                        draw : "3.65",
                                        lost : "1.60"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.05",
                                        draw : "3.45",
                                        lost : "2.85"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "10.50",
                                        "a20" : "21.00",
                                        "a21" : "11.50",
                                        "a30" : "60.00",
                                        "a31" : "39.00",
                                        "a32" : "34.00",
                                        "a40" : "200.00",
                                        "a41" : "120.00",
                                        "a42" : "120.00",
                                        "a50" : "600.00",
                                        "a51" : "400.00",
                                        "a52" : "400.00",
                                        "aother" : "150.00",
                                        "c00" : "10.50",
                                        "c11" : "7.00",
                                        "c22" : "12.00",
                                        "c33" : "50.00",
                                        "cother" : "300.00",
                                        "b10" : "6.50",
                                        "b20" : "7.50",
                                        "b21" : "7.00",
                                        "b30" : "12.00",
                                        "b31" : "12.00",
                                        "b32" : "20.00",
                                        "b40" : "24.00",
                                        "b41" : "25.00",
                                        "b42" : "50.00",
                                        "b50" : "60.00",
                                        "b51" : "60.00",
                                        "b52" : "100.00",
                                        "bother" : "39.00"
                                    },
                                    bqcpl : {
                                        "aa" : "7.00",
                                        "ac" : "14.00",
                                        "ab" : "24.00",
                                        "ca" : "9.50",
                                        "cc" : "5.60",
                                        "cb" : "4.00",
                                        "ba" : "40.00",
                                        "bc" : "14.00",
                                        "bb" : "2.35"
                                    },
                                    jqspl : {
                                        "s0" : "10.50",
                                        "s1" : "4.60",
                                        "s2" : "3.45",
                                        "s3" : "3.45",
                                        "s4" : "4.90",
                                        "s5" : "9.00",
                                        "s6" : "16.00",
                                        "s7" : "24.00"
                                    }
                                },
                                {
                                    infoid : 128071,
                                    fixtureid : 768688,
                                    matchnum : "周三005",
                                    simpleleague : "葡杯",
                                    homesxname : "博维斯",
                                    homestanding : 0,
                                    awaysxname : "吉马良",
                                    awaystanding : 0,
                                    rangqiu : 1,
                                    matchgroup : "",
                                    seasonid : 5033,
                                    homename : "博阿维斯",
                                    awayname : "吉马良斯",
                                    matchdate : "12-20",
                                    matchtime : "02:00",
                                    stagegbname : "第五圈",
                                    homeid : 709,
                                    awayid : 893,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142161,
                                    processname : 3005,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "3.10",
                                        draw : "2.95",
                                        lost : "2.15"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "1.55",
                                        draw : "3.60",
                                        lost : "4.85"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "7.00",
                                        "a20" : "14.00",
                                        "a21" : "9.50",
                                        "a30" : "39.00",
                                        "a31" : "29.00",
                                        "a32" : "39.00",
                                        "a40" : "120.00",
                                        "a41" : "80.00",
                                        "a42" : "150.00",
                                        "a50" : "400.00",
                                        "a51" : "300.00",
                                        "a52" : "500.00",
                                        "aother" : "200.00",
                                        "c00" : "7.00",
                                        "c11" : "5.40",
                                        "c22" : "15.00",
                                        "c33" : "80.00",
                                        "cother" : "500.00",
                                        "b10" : "6.00",
                                        "b20" : "8.50",
                                        "b21" : "7.50",
                                        "b30" : "18.00",
                                        "b31" : "18.00",
                                        "b32" : "29.00",
                                        "b40" : "50.00",
                                        "b41" : "50.00",
                                        "b42" : "80.00",
                                        "b50" : "150.00",
                                        "b51" : "150.00",
                                        "b52" : "250.00",
                                        "bother" : "100.00"
                                    },
                                    bqcpl : {
                                        "aa" : "5.50",
                                        "ac" : "13.00",
                                        "ab" : "30.00",
                                        "ca" : "7.00",
                                        "cc" : "4.40",
                                        "cb" : "4.30",
                                        "ba" : "40.00",
                                        "bc" : "13.00",
                                        "bb" : "3.15"
                                    },
                                    jqspl : {
                                        "s0" : "7.00",
                                        "s1" : "3.45",
                                        "s2" : "3.00",
                                        "s3" : "3.85",
                                        "s4" : "6.50",
                                        "s5" : "14.50",
                                        "s6" : "26.00",
                                        "s7" : "38.00"
                                    }
                                },
                                {
                                    infoid : 128072,
                                    fixtureid : 767749,
                                    matchnum : "周三006",
                                    simpleleague : "荷杯",
                                    homesxname : "福图纳",
                                    homestanding : 0,
                                    awaysxname : "坎布尔",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "福图纳",
                                    awayname : "坎布尔",
                                    matchdate : "12-20",
                                    matchtime : "02:45",
                                    stagegbname : "第三圈",
                                    homeid : 1303,
                                    awayid : 235,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142162,
                                    processname : 3006,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "1.35",
                                        draw : "4.50",
                                        lost : "6.00"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.05",
                                        draw : "3.65",
                                        lost : "2.72"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "9.00",
                                        "a20" : "8.50",
                                        "a21" : "7.50",
                                        "a30" : "10.00",
                                        "a31" : "9.00",
                                        "a32" : "15.00",
                                        "a40" : "16.00",
                                        "a41" : "15.00",
                                        "a42" : "28.00",
                                        "a50" : "29.00",
                                        "a51" : "29.00",
                                        "a52" : "50.00",
                                        "aother" : "15.00",
                                        "c00" : "19.00",
                                        "c11" : "8.00",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "19.00",
                                        "b20" : "39.00",
                                        "b21" : "15.00",
                                        "b30" : "80.00",
                                        "b31" : "39.00",
                                        "b32" : "34.00",
                                        "b40" : "250.00",
                                        "b41" : "150.00",
                                        "b42" : "120.00",
                                        "b50" : "700.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "100.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.85",
                                        "ac" : "16.00",
                                        "ab" : "40.00",
                                        "ca" : "4.10",
                                        "cc" : "7.50",
                                        "cb" : "12.50",
                                        "ba" : "20.00",
                                        "bc" : "16.00",
                                        "bb" : "8.75"
                                    },
                                    jqspl : {
                                        "s0" : "19.00",
                                        "s1" : "7.00",
                                        "s2" : "4.20",
                                        "s3" : "3.50",
                                        "s4" : "4.10",
                                        "s5" : "6.00",
                                        "s6" : "10.00",
                                        "s7" : "12.00"
                                    }
                                },
                                {
                                    infoid : 128073,
                                    fixtureid : 768680,
                                    matchnum : "周三007",
                                    simpleleague : "比杯",
                                    homesxname : "欧  本",
                                    homestanding : 0,
                                    awaysxname : "奥斯坦",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5021,
                                    homename : "欧本",
                                    awayname : "奥斯坦德",
                                    matchdate : "12-20",
                                    matchtime : "03:00",
                                    stagegbname : "半准决赛",
                                    homeid : 2100,
                                    awayid : 625,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142163,
                                    processname : 3007,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "2.14",
                                        draw : "3.30",
                                        lost : "2.80"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "4.35",
                                        draw : "3.95",
                                        lost : "1.55"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "11.00",
                                        "a21" : "7.50",
                                        "a30" : "21.00",
                                        "a31" : "15.00",
                                        "a32" : "19.00",
                                        "a40" : "50.00",
                                        "a41" : "34.00",
                                        "a42" : "50.00",
                                        "a50" : "120.00",
                                        "a51" : "80.00",
                                        "a52" : "120.00",
                                        "aother" : "50.00",
                                        "c00" : "12.00",
                                        "c11" : "6.50",
                                        "c22" : "10.50",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "9.50",
                                        "b20" : "15.00",
                                        "b21" : "8.50",
                                        "b30" : "29.00",
                                        "b31" : "20.00",
                                        "b32" : "21.00",
                                        "b40" : "70.00",
                                        "b41" : "50.00",
                                        "b42" : "60.00",
                                        "b50" : "250.00",
                                        "b51" : "150.00",
                                        "b52" : "200.00",
                                        "bother" : "60.00"
                                    },
                                    bqcpl : {
                                        "aa" : "3.20",
                                        "ac" : "12.00",
                                        "ab" : "28.00",
                                        "ca" : "4.90",
                                        "cc" : "5.80",
                                        "cb" : "6.50",
                                        "ba" : "24.00",
                                        "bc" : "12.00",
                                        "bb" : "4.40"
                                    },
                                    jqspl : {
                                        "s0" : "12.00",
                                        "s1" : "5.40",
                                        "s2" : "3.65",
                                        "s3" : "3.40",
                                        "s4" : "4.50",
                                        "s5" : "8.00",
                                        "s6" : "13.00",
                                        "s7" : "20.00"
                                    }
                                },
                                {
                                    infoid : 128074,
                                    fixtureid : 737687,
                                    matchnum : "周三008",
                                    simpleleague : "德甲",
                                    homesxname : "拜  仁",
                                    homestanding : 3,
                                    awaysxname : "莱比锡",
                                    awaystanding : 4,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 4852,
                                    homename : "拜仁",
                                    awayname : "莱红牛",
                                    matchdate : "12-20",
                                    matchtime : "03:30",
                                    stagegbname : "联赛赛程",
                                    homeid : 664,
                                    awayid : 970,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142164,
                                    processname : 3008,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "1.25",
                                        draw : "5.20",
                                        lost : "7.40"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "1.78",
                                        draw : "3.90",
                                        lost : "3.22"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "9.50",
                                        "a20" : "7.50",
                                        "a21" : "7.50",
                                        "a30" : "8.50",
                                        "a31" : "8.50",
                                        "a32" : "16.00",
                                        "a40" : "13.00",
                                        "a41" : "14.00",
                                        "a42" : "26.00",
                                        "a50" : "23.00",
                                        "a51" : "24.00",
                                        "a52" : "50.00",
                                        "aother" : "12.00",
                                        "c00" : "23.00",
                                        "c11" : "9.00",
                                        "c22" : "14.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "23.00",
                                        "b20" : "50.00",
                                        "b21" : "18.00",
                                        "b30" : "120.00",
                                        "b31" : "60.00",
                                        "b32" : "39.00",
                                        "b40" : "400.00",
                                        "b41" : "200.00",
                                        "b42" : "150.00",
                                        "b50" : "900.00",
                                        "b51" : "600.00",
                                        "b52" : "500.00",
                                        "bother" : "120.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.65",
                                        "ac" : "17.00",
                                        "ab" : "50.00",
                                        "ca" : "3.95",
                                        "cc" : "8.75",
                                        "cb" : "15.00",
                                        "ba" : "20.00",
                                        "bc" : "17.00",
                                        "bb" : "12.00"
                                    },
                                    jqspl : {
                                        "s0" : "23.00",
                                        "s1" : "8.00",
                                        "s2" : "4.50",
                                        "s3" : "3.50",
                                        "s4" : "3.90",
                                        "s5" : "6.00",
                                        "s6" : "8.50",
                                        "s7" : "10.50"
                                    }
                                },
                                {
                                    infoid : 128075,
                                    fixtureid : 737689,
                                    matchnum : "周三009",
                                    simpleleague : "德甲",
                                    homesxname : "弗赖堡",
                                    homestanding : 12,
                                    awaysxname : "汉诺威",
                                    awaystanding : 18,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 4852,
                                    homename : "弗赖堡",
                                    awayname : "汉诺威96",
                                    matchdate : "12-20",
                                    matchtime : "03:30",
                                    stagegbname : "联赛赛程",
                                    homeid : 804,
                                    awayid : 862,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142165,
                                    processname : 3009,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "1.75",
                                        draw : "3.40",
                                        lost : "3.80"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "3.30",
                                        draw : "3.50",
                                        lost : "1.85"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "7.00",
                                        "a20" : "8.00",
                                        "a21" : "7.00",
                                        "a30" : "14.00",
                                        "a31" : "13.00",
                                        "a32" : "20.00",
                                        "a40" : "29.00",
                                        "a41" : "29.00",
                                        "a42" : "50.00",
                                        "a50" : "70.00",
                                        "a51" : "70.00",
                                        "a52" : "120.00",
                                        "aother" : "39.00",
                                        "c00" : "10.50",
                                        "c11" : "6.50",
                                        "c22" : "12.00",
                                        "c33" : "50.00",
                                        "cother" : "300.00",
                                        "b10" : "10.00",
                                        "b20" : "19.00",
                                        "b21" : "10.50",
                                        "b30" : "50.00",
                                        "b31" : "29.00",
                                        "b32" : "29.00",
                                        "b40" : "150.00",
                                        "b41" : "80.00",
                                        "b42" : "100.00",
                                        "b50" : "500.00",
                                        "b51" : "300.00",
                                        "b52" : "400.00",
                                        "bother" : "120.00"
                                    },
                                    bqcpl : {
                                        "aa" : "2.60",
                                        "ac" : "13.00",
                                        "ab" : "34.00",
                                        "ca" : "4.20",
                                        "cc" : "5.60",
                                        "cb" : "8.50",
                                        "ba" : "24.00",
                                        "bc" : "13.00",
                                        "bb" : "6.00"
                                    },
                                    jqspl : {
                                        "s0" : "10.50",
                                        "s1" : "4.50",
                                        "s2" : "3.40",
                                        "s3" : "3.45",
                                        "s4" : "4.90",
                                        "s5" : "9.50",
                                        "s6" : "17.00",
                                        "s7" : "24.00"
                                    }
                                },
                                {
                                    infoid : 128076,
                                    fixtureid : 737693,
                                    matchnum : "周三010",
                                    simpleleague : "德甲",
                                    homesxname : "不来梅",
                                    homestanding : 9,
                                    awaysxname : "霍芬海",
                                    awaystanding : 8,
                                    rangqiu : 1,
                                    matchgroup : "",
                                    seasonid : 4852,
                                    homename : "不来梅",
                                    awayname : "霍芬海姆",
                                    matchdate : "12-20",
                                    matchtime : "03:30",
                                    stagegbname : "联赛赛程",
                                    homeid : 1334,
                                    awayid : 1869,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142166,
                                    processname : 3010,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "3.00",
                                        draw : "3.90",
                                        lost : "1.86"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "1.75",
                                        draw : "3.90",
                                        lost : "3.32"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "17.00",
                                        "a20" : "21.00",
                                        "a21" : "11.00",
                                        "a30" : "39.00",
                                        "a31" : "20.00",
                                        "a32" : "17.00",
                                        "a40" : "80.00",
                                        "a41" : "39.00",
                                        "a42" : "39.00",
                                        "a50" : "200.00",
                                        "a51" : "80.00",
                                        "a52" : "80.00",
                                        "aother" : "29.00",
                                        "c00" : "26.00",
                                        "c11" : "8.00",
                                        "c22" : "9.50",
                                        "c33" : "22.00",
                                        "cother" : "80.00",
                                        "b10" : "13.00",
                                        "b20" : "13.00",
                                        "b21" : "8.00",
                                        "b30" : "20.00",
                                        "b31" : "12.00",
                                        "b32" : "13.00",
                                        "b40" : "34.00",
                                        "b41" : "21.00",
                                        "b42" : "27.00",
                                        "b50" : "70.00",
                                        "b51" : "50.00",
                                        "b52" : "60.00",
                                        "bother" : "17.00"
                                    },
                                    bqcpl : {
                                        "aa" : "4.80",
                                        "ac" : "12.00",
                                        "ab" : "19.00",
                                        "ca" : "7.25",
                                        "cc" : "7.00",
                                        "cb" : "4.90",
                                        "ba" : "24.00",
                                        "bc" : "12.00",
                                        "bb" : "2.80"
                                    },
                                    jqspl : {
                                        "s0" : "26.00",
                                        "s1" : "8.50",
                                        "s2" : "4.80",
                                        "s3" : "3.50",
                                        "s4" : "3.85",
                                        "s5" : "5.50",
                                        "s6" : "8.50",
                                        "s7" : "9.50"
                                    }
                                },
                                {
                                    infoid : 128077,
                                    fixtureid : 737691,
                                    matchnum : "周三011",
                                    simpleleague : "德甲",
                                    homesxname : "美因茨",
                                    homestanding : 10,
                                    awaysxname : "法兰克",
                                    awaystanding : 5,
                                    rangqiu : 1,
                                    matchgroup : "",
                                    seasonid : 4852,
                                    homename : "美因茨",
                                    awayname : "法兰克福",
                                    matchdate : "12-20",
                                    matchtime : "03:30",
                                    stagegbname : "联赛赛程",
                                    homeid : 1088,
                                    awayid : 791,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142167,
                                    processname : 3011,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "2.80",
                                        draw : "3.40",
                                        lost : "2.10"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "1.58",
                                        draw : "3.88",
                                        lost : "4.20"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "10.00",
                                        "a20" : "16.00",
                                        "a21" : "8.50",
                                        "a30" : "29.00",
                                        "a31" : "21.00",
                                        "a32" : "21.00",
                                        "a40" : "80.00",
                                        "a41" : "50.00",
                                        "a42" : "60.00",
                                        "a50" : "250.00",
                                        "a51" : "150.00",
                                        "a52" : "200.00",
                                        "aother" : "60.00",
                                        "c00" : "13.00",
                                        "c11" : "6.60",
                                        "c22" : "10.50",
                                        "c33" : "33.00",
                                        "cother" : "200.00",
                                        "b10" : "8.50",
                                        "b20" : "11.00",
                                        "b21" : "7.50",
                                        "b30" : "20.00",
                                        "b31" : "14.00",
                                        "b32" : "19.00",
                                        "b40" : "39.00",
                                        "b41" : "34.00",
                                        "b42" : "50.00",
                                        "b50" : "100.00",
                                        "b51" : "80.00",
                                        "b52" : "120.00",
                                        "bother" : "39.00"
                                    },
                                    bqcpl : {
                                        "aa" : "4.50",
                                        "ac" : "12.00",
                                        "ab" : "24.00",
                                        "ca" : "6.40",
                                        "cc" : "5.90",
                                        "cb" : "4.80",
                                        "ba" : "29.00",
                                        "bc" : "12.00",
                                        "bb" : "3.20"
                                    },
                                    jqspl : {
                                        "s0" : "13.00",
                                        "s1" : "5.30",
                                        "s2" : "3.60",
                                        "s3" : "3.35",
                                        "s4" : "4.50",
                                        "s5" : "8.00",
                                        "s6" : "14.00",
                                        "s7" : "19.00"
                                    }
                                },
                                {
                                    infoid : 128078,
                                    fixtureid : 768687,
                                    matchnum : "周三012",
                                    simpleleague : "葡杯",
                                    homesxname : "里斯本",
                                    homestanding : 0,
                                    awaysxname : "里奥阿",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5033,
                                    homename : "里斯本",
                                    awayname : "里奥阿维",
                                    matchdate : "12-20",
                                    matchtime : "03:30",
                                    stagegbname : "第五圈",
                                    homeid : 1000,
                                    awayid : 1002,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142168,
                                    processname : 3012,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "1.30",
                                        draw : "4.45",
                                        lost : "7.50"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "1.98",
                                        draw : "3.55",
                                        lost : "2.92"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "7.00",
                                        "a20" : "6.50",
                                        "a21" : "7.00",
                                        "a30" : "8.00",
                                        "a31" : "9.50",
                                        "a32" : "19.00",
                                        "a40" : "14.50",
                                        "a41" : "16.00",
                                        "a42" : "34.00",
                                        "a50" : "29.00",
                                        "a51" : "34.00",
                                        "a52" : "70.00",
                                        "aother" : "19.00",
                                        "c00" : "15.00",
                                        "c11" : "8.00",
                                        "c22" : "15.00",
                                        "c33" : "60.00",
                                        "cother" : "300.00",
                                        "b10" : "18.00",
                                        "b20" : "39.00",
                                        "b21" : "17.00",
                                        "b30" : "120.00",
                                        "b31" : "60.00",
                                        "b32" : "50.00",
                                        "b40" : "400.00",
                                        "b41" : "250.00",
                                        "b42" : "200.00",
                                        "b50" : "900.00",
                                        "b51" : "700.00",
                                        "b52" : "600.00",
                                        "bother" : "200.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.72",
                                        "ac" : "18.00",
                                        "ab" : "50.00",
                                        "ca" : "3.90",
                                        "cc" : "7.00",
                                        "cb" : "15.00",
                                        "ba" : "23.00",
                                        "bc" : "18.00",
                                        "bb" : "11.00"
                                    },
                                    jqspl : {
                                        "s0" : "15.00",
                                        "s1" : "6.00",
                                        "s2" : "3.75",
                                        "s3" : "3.40",
                                        "s4" : "4.30",
                                        "s5" : "7.00",
                                        "s6" : "12.00",
                                        "s7" : "17.00"
                                    }
                                },
                                {
                                    infoid : 128079,
                                    fixtureid : 768684,
                                    matchnum : "周三013",
                                    simpleleague : "英联杯",
                                    homesxname : "阿森纳",
                                    homestanding : 0,
                                    awaysxname : "热  刺",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 4926,
                                    homename : "阿森纳",
                                    awayname : "热刺",
                                    matchdate : "12-20",
                                    matchtime : "03:45",
                                    stagegbname : "半准决赛",
                                    homeid : 554,
                                    awayid : 1238,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "1",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142169,
                                    processname : 3013,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "2.30",
                                        draw : "3.40",
                                        lost : "2.50"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "4.80",
                                        draw : "4.10",
                                        lost : "1.48"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "10.00",
                                        "a20" : "14.00",
                                        "a21" : "7.50",
                                        "a30" : "24.00",
                                        "a31" : "15.00",
                                        "a32" : "16.00",
                                        "a40" : "50.00",
                                        "a41" : "34.00",
                                        "a42" : "39.00",
                                        "a50" : "120.00",
                                        "a51" : "80.00",
                                        "a52" : "100.00",
                                        "aother" : "39.00",
                                        "c00" : "15.00",
                                        "c11" : "7.50",
                                        "c22" : "10.00",
                                        "c33" : "27.00",
                                        "cother" : "120.00",
                                        "b10" : "11.00",
                                        "b20" : "15.00",
                                        "b21" : "8.00",
                                        "b30" : "27.00",
                                        "b31" : "17.00",
                                        "b32" : "18.00",
                                        "b40" : "60.00",
                                        "b41" : "39.00",
                                        "b42" : "50.00",
                                        "b50" : "150.00",
                                        "b51" : "100.00",
                                        "b52" : "120.00",
                                        "bother" : "39.00"
                                    },
                                    bqcpl : {
                                        "aa" : "3.80",
                                        "ac" : "11.00",
                                        "ab" : "21.00",
                                        "ca" : "5.70",
                                        "cc" : "5.80",
                                        "cb" : "5.80",
                                        "ba" : "19.00",
                                        "bc" : "11.00",
                                        "bb" : "4.05"
                                    },
                                    jqspl : {
                                        "s0" : "15.00",
                                        "s1" : "5.80",
                                        "s2" : "3.90",
                                        "s3" : "3.30",
                                        "s4" : "4.35",
                                        "s5" : "7.00",
                                        "s6" : "12.00",
                                        "s7" : "17.00"
                                    }
                                },
                                {
                                    infoid : 128080,
                                    fixtureid : 768685,
                                    matchnum : "周三014",
                                    simpleleague : "英联杯",
                                    homesxname : "切尔西",
                                    homestanding : 0,
                                    awaysxname : "伯恩茅",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 4926,
                                    homename : "切尔西",
                                    awayname : "伯恩茅斯",
                                    matchdate : "12-20",
                                    matchtime : "03:45",
                                    stagegbname : "半准决赛",
                                    homeid : 1173,
                                    awayid : 667,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142170,
                                    processname : 3014,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "1.22",
                                        draw : "5.10",
                                        lost : "8.80"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "1.75",
                                        draw : "3.80",
                                        lost : "3.40"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "7.50",
                                        "a20" : "6.75",
                                        "a21" : "7.00",
                                        "a30" : "7.50",
                                        "a31" : "9.00",
                                        "a32" : "19.00",
                                        "a40" : "12.50",
                                        "a41" : "15.00",
                                        "a42" : "34.00",
                                        "a50" : "22.00",
                                        "a51" : "28.00",
                                        "a52" : "60.00",
                                        "aother" : "15.00",
                                        "c00" : "16.00",
                                        "c11" : "9.00",
                                        "c22" : "16.00",
                                        "c33" : "60.00",
                                        "cother" : "300.00",
                                        "b10" : "21.00",
                                        "b20" : "50.00",
                                        "b21" : "19.00",
                                        "b30" : "150.00",
                                        "b31" : "70.00",
                                        "b32" : "50.00",
                                        "b40" : "500.00",
                                        "b41" : "250.00",
                                        "b42" : "250.00",
                                        "b50" : "1000.00",
                                        "b51" : "800.00",
                                        "b52" : "700.00",
                                        "bother" : "250.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.60",
                                        "ac" : "19.00",
                                        "ab" : "60.00",
                                        "ca" : "3.80",
                                        "cc" : "8.00",
                                        "cb" : "18.00",
                                        "ba" : "20.00",
                                        "bc" : "19.00",
                                        "bb" : "14.00"
                                    },
                                    jqspl : {
                                        "s0" : "16.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 128081,
                                    fixtureid : 767748,
                                    matchnum : "周三015",
                                    simpleleague : "荷杯",
                                    homesxname : "罗达JC",
                                    homestanding : 0,
                                    awaysxname : "阿贾克",
                                    awaystanding : 0,
                                    rangqiu : 3,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "罗达JC",
                                    awayname : "阿贾克斯",
                                    matchdate : "12-20",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 1030,
                                    awayid : 540,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "0",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142171,
                                    processname : 3015,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    spfpl : {
                                        win : "1.90",
                                        draw : "4.60",
                                        lost : "2.60"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "70.00",
                                        "a20" : "200.00",
                                        "a21" : "50.00",
                                        "a30" : "500.00",
                                        "a31" : "200.00",
                                        "a32" : "100.00",
                                        "a40" : "1000.00",
                                        "a41" : "600.00",
                                        "a42" : "500.00",
                                        "a50" : "1000.00",
                                        "a51" : "1000.00",
                                        "a52" : "1000.00",
                                        "aother" : "400.00",
                                        "c00" : "39.00",
                                        "c11" : "14.00",
                                        "c22" : "22.00",
                                        "c33" : "70.00",
                                        "cother" : "300.00",
                                        "b10" : "13.50",
                                        "b20" : "8.00",
                                        "b21" : "10.00",
                                        "b30" : "7.00",
                                        "b31" : "9.00",
                                        "b32" : "21.00",
                                        "b40" : "7.50",
                                        "b41" : "10.00",
                                        "b42" : "27.00",
                                        "b50" : "10.00",
                                        "b51" : "15.00",
                                        "b52" : "39.00",
                                        "bother" : "4.80"
                                    },
                                    bqcpl : {
                                        "aa" : "25.00",
                                        "ac" : "26.00",
                                        "ab" : "20.00",
                                        "ca" : "32.00",
                                        "cc" : "15.00",
                                        "cb" : "4.30",
                                        "ba" : "100.00",
                                        "bc" : "26.00",
                                        "bb" : "1.24"
                                    },
                                    jqspl : {
                                        "s0" : "39.00",
                                        "s1" : "13.00",
                                        "s2" : "6.50",
                                        "s3" : "4.10",
                                        "s4" : "3.80",
                                        "s5" : "4.70",
                                        "s6" : "6.30",
                                        "s7" : "5.60"
                                    }
                                },
                                {
                                    infoid : 128082,
                                    fixtureid : 731664,
                                    matchnum : "周三016",
                                    simpleleague : "苏超",
                                    homesxname : "凯尔特",
                                    homestanding : 3,
                                    awaysxname : "马瑟韦",
                                    awaystanding : 9,
                                    rangqiu : -2,
                                    matchgroup : "",
                                    seasonid : 4828,
                                    homename : "凯尔特人",
                                    awayname : "马瑟韦尔",
                                    matchdate : "12-20",
                                    matchtime : "03:45",
                                    stagegbname : "联赛赛程",
                                    homeid : 926,
                                    awayid : 1065,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142172,
                                    processname : 3016,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "1.11",
                                        draw : "6.10",
                                        lost : "16.00"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.45",
                                        draw : "3.90",
                                        lost : "2.15"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "6.00",
                                        "a20" : "4.80",
                                        "a21" : "8.50",
                                        "a30" : "5.70",
                                        "a31" : "10.50",
                                        "a32" : "37.00",
                                        "a40" : "8.00",
                                        "a41" : "15.00",
                                        "a42" : "50.00",
                                        "a50" : "15.00",
                                        "a51" : "29.00",
                                        "a52" : "80.00",
                                        "aother" : "14.00",
                                        "c00" : "14.00",
                                        "c11" : "9.50",
                                        "c22" : "29.00",
                                        "c33" : "150.00",
                                        "cother" : "700.00",
                                        "b10" : "29.00",
                                        "b20" : "80.00",
                                        "b21" : "45.00",
                                        "b30" : "400.00",
                                        "b31" : "200.00",
                                        "b32" : "150.00",
                                        "b40" : "1000.00",
                                        "b41" : "700.00",
                                        "b42" : "600.00",
                                        "b50" : "1000.00",
                                        "b51" : "1000.00",
                                        "b52" : "1000.00",
                                        "bother" : "600.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.40",
                                        "ac" : "26.00",
                                        "ab" : "125.00",
                                        "ca" : "3.50",
                                        "cc" : "8.50",
                                        "cb" : "29.00",
                                        "ba" : "29.00",
                                        "bc" : "26.00",
                                        "bb" : "24.00"
                                    },
                                    jqspl : {
                                        "s0" : "14.00",
                                        "s1" : "5.70",
                                        "s2" : "3.60",
                                        "s3" : "3.45",
                                        "s4" : "4.40",
                                        "s5" : "7.50",
                                        "s6" : "13.00",
                                        "s7" : "17.00"
                                    }
                                },
                                {
                                    infoid : 128083,
                                    fixtureid : 731661,
                                    matchnum : "周三017",
                                    simpleleague : "苏超",
                                    homesxname : "希伯尼",
                                    homestanding : 8,
                                    awaysxname : "流浪者",
                                    awaystanding : 1,
                                    rangqiu : 1,
                                    matchgroup : "",
                                    seasonid : 4828,
                                    homename : "希伯尼安",
                                    awayname : "流浪者",
                                    matchdate : "12-20",
                                    matchtime : "03:45",
                                    stagegbname : "联赛赛程",
                                    homeid : 1393,
                                    awayid : 827,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142173,
                                    processname : 3017,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "3.82",
                                        draw : "3.50",
                                        lost : "1.72"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "1.90",
                                        draw : "3.50",
                                        lost : "3.15"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "11.00",
                                        "a20" : "18.00",
                                        "a21" : "11.00",
                                        "a30" : "50.00",
                                        "a31" : "29.00",
                                        "a32" : "29.00",
                                        "a40" : "150.00",
                                        "a41" : "80.00",
                                        "a42" : "100.00",
                                        "a50" : "500.00",
                                        "a51" : "300.00",
                                        "a52" : "400.00",
                                        "aother" : "100.00",
                                        "c00" : "11.00",
                                        "c11" : "6.50",
                                        "c22" : "12.00",
                                        "c33" : "50.00",
                                        "cother" : "250.00",
                                        "b10" : "7.00",
                                        "b20" : "8.00",
                                        "b21" : "7.00",
                                        "b30" : "13.00",
                                        "b31" : "13.00",
                                        "b32" : "19.00",
                                        "b40" : "29.00",
                                        "b41" : "27.00",
                                        "b42" : "50.00",
                                        "b50" : "70.00",
                                        "b51" : "70.00",
                                        "b52" : "100.00",
                                        "bother" : "39.00"
                                    },
                                    bqcpl : {
                                        "aa" : "5.90",
                                        "ac" : "13.00",
                                        "ab" : "23.00",
                                        "ca" : "9.00",
                                        "cc" : "5.70",
                                        "cb" : "4.20",
                                        "ba" : "35.00",
                                        "bc" : "13.00",
                                        "bb" : "2.55"
                                    },
                                    jqspl : {
                                        "s0" : "11.00",
                                        "s1" : "4.70",
                                        "s2" : "3.35",
                                        "s3" : "3.45",
                                        "s4" : "4.90",
                                        "s5" : "9.00",
                                        "s6" : "16.00",
                                        "s7" : "24.00"
                                    }
                                },
                                {
                                    infoid : 128084,
                                    fixtureid : 768695,
                                    matchnum : "周三018",
                                    simpleleague : "法联杯",
                                    homesxname : "阿弗尔",
                                    homestanding : 0,
                                    awaysxname : "尼  姆",
                                    awaystanding : 0,
                                    rangqiu : 1,
                                    matchgroup : "",
                                    seasonid : 4955,
                                    homename : "勒阿弗尔",
                                    awayname : "尼姆",
                                    matchdate : "12-20",
                                    matchtime : "04:05",
                                    stagegbname : "十六强",
                                    homeid : 982,
                                    awayid : 1132,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142174,
                                    processname : 3018,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "2.60",
                                        draw : "3.05",
                                        lost : "2.40"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "1.44",
                                        draw : "4.00",
                                        lost : "5.40"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "7.50",
                                        "a20" : "12.00",
                                        "a21" : "8.00",
                                        "a30" : "28.00",
                                        "a31" : "20.00",
                                        "a32" : "27.00",
                                        "a40" : "70.00",
                                        "a41" : "60.00",
                                        "a42" : "80.00",
                                        "a50" : "250.00",
                                        "a51" : "200.00",
                                        "a52" : "250.00",
                                        "aother" : "100.00",
                                        "c00" : "8.50",
                                        "c11" : "5.90",
                                        "c22" : "12.00",
                                        "c33" : "60.00",
                                        "cother" : "300.00",
                                        "b10" : "7.25",
                                        "b20" : "10.50",
                                        "b21" : "7.50",
                                        "b30" : "24.00",
                                        "b31" : "19.00",
                                        "b32" : "26.00",
                                        "b40" : "60.00",
                                        "b41" : "50.00",
                                        "b42" : "80.00",
                                        "b50" : "200.00",
                                        "b51" : "150.00",
                                        "b52" : "250.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "4.10",
                                        "ac" : "12.00",
                                        "ab" : "24.00",
                                        "ca" : "5.80",
                                        "cc" : "4.80",
                                        "cb" : "5.50",
                                        "ba" : "25.00",
                                        "bc" : "12.00",
                                        "bb" : "3.90"
                                    },
                                    jqspl : {
                                        "s0" : "8.50",
                                        "s1" : "3.95",
                                        "s2" : "3.15",
                                        "s3" : "3.60",
                                        "s4" : "6.00",
                                        "s5" : "11.00",
                                        "s6" : "19.00",
                                        "s7" : "27.00"
                                    }
                                },
                                {
                                    infoid : 128085,
                                    fixtureid : 767901,
                                    matchnum : "周三019",
                                    simpleleague : "法联杯",
                                    homesxname : "摩纳哥",
                                    homestanding : 0,
                                    awaysxname : "洛里昂",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 4955,
                                    homename : "摩纳哥",
                                    awayname : "洛里昂",
                                    matchdate : "12-20",
                                    matchtime : "04:05",
                                    stagegbname : "十六强",
                                    homeid : 1105,
                                    awayid : 980,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142175,
                                    processname : 3019,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "1.80",
                                        draw : "3.25",
                                        lost : "3.75"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "3.60",
                                        draw : "3.40",
                                        lost : "1.80"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "6.00",
                                        "a20" : "7.50",
                                        "a21" : "7.00",
                                        "a30" : "13.00",
                                        "a31" : "13.00",
                                        "a32" : "23.00",
                                        "a40" : "29.00",
                                        "a41" : "29.00",
                                        "a42" : "60.00",
                                        "a50" : "80.00",
                                        "a51" : "80.00",
                                        "a52" : "150.00",
                                        "aother" : "50.00",
                                        "c00" : "8.50",
                                        "c11" : "6.50",
                                        "c22" : "13.00",
                                        "c33" : "60.00",
                                        "cother" : "400.00",
                                        "b10" : "10.00",
                                        "b20" : "20.00",
                                        "b21" : "10.00",
                                        "b30" : "50.00",
                                        "b31" : "39.00",
                                        "b32" : "39.00",
                                        "b40" : "200.00",
                                        "b41" : "120.00",
                                        "b42" : "150.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "500.00",
                                        "bother" : "150.00"
                                    },
                                    bqcpl : {
                                        "aa" : "2.70",
                                        "ac" : "14.00",
                                        "ab" : "30.00",
                                        "ca" : "4.30",
                                        "cc" : "5.00",
                                        "cb" : "8.00",
                                        "ba" : "23.00",
                                        "bc" : "14.00",
                                        "bb" : "6.00"
                                    },
                                    jqspl : {
                                        "s0" : "8.50",
                                        "s1" : "4.10",
                                        "s2" : "3.20",
                                        "s3" : "3.60",
                                        "s4" : "5.60",
                                        "s5" : "10.50",
                                        "s6" : "19.00",
                                        "s7" : "29.00"
                                    }
                                },
                                {
                                    infoid : 128086,
                                    fixtureid : 767904,
                                    matchnum : "周三020",
                                    simpleleague : "法联杯",
                                    homesxname : "第  戎",
                                    homestanding : 0,
                                    awaysxname : "波尔多",
                                    awaystanding : 0,
                                    rangqiu : 1,
                                    matchgroup : "",
                                    seasonid : 4955,
                                    homename : "第戎",
                                    awayname : "波尔多",
                                    matchdate : "12-20",
                                    matchtime : "04:05",
                                    stagegbname : "十六强",
                                    homeid : 774,
                                    awayid : 693,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142176,
                                    processname : 3020,
                                    processdate : "2018-12-19",
                                    ptggendtime : "2018-12-19 23:55:00",
                                    nspfpl : {
                                        win : "2.90",
                                        draw : "3.00",
                                        lost : "2.22"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "1.52",
                                        draw : "3.80",
                                        lost : "4.80"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "7.50",
                                        "a20" : "14.00",
                                        "a21" : "8.50",
                                        "a30" : "29.00",
                                        "a31" : "25.00",
                                        "a32" : "29.00",
                                        "a40" : "80.00",
                                        "a41" : "70.00",
                                        "a42" : "100.00",
                                        "a50" : "300.00",
                                        "a51" : "250.00",
                                        "a52" : "300.00",
                                        "aother" : "120.00",
                                        "c00" : "7.50",
                                        "c11" : "5.80",
                                        "c22" : "13.00",
                                        "c33" : "60.00",
                                        "cother" : "400.00",
                                        "b10" : "6.50",
                                        "b20" : "10.50",
                                        "b21" : "7.50",
                                        "b30" : "20.00",
                                        "b31" : "18.00",
                                        "b32" : "26.00",
                                        "b40" : "50.00",
                                        "b41" : "50.00",
                                        "b42" : "70.00",
                                        "b50" : "150.00",
                                        "b51" : "150.00",
                                        "b52" : "250.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "4.70",
                                        "ac" : "12.00",
                                        "ab" : "24.00",
                                        "ca" : "6.00",
                                        "cc" : "4.60",
                                        "cb" : "5.10",
                                        "ba" : "28.00",
                                        "bc" : "12.00",
                                        "bb" : "3.60"
                                    },
                                    jqspl : {
                                        "s0" : "7.50",
                                        "s1" : "3.80",
                                        "s2" : "3.15",
                                        "s3" : "3.75",
                                        "s4" : "6.10",
                                        "s5" : "11.00",
                                        "s6" : "21.00",
                                        "s7" : "34.00"
                                    }
                                }
                            ]
                    },{
                        'matchtime' : '2018-12-18',
                        'matchday' : '周四',
                        'matchdata' :
                            [
                                {
                                    infoid : 128103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 138103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 148103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 158103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 168103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 178103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 188103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 198103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 228103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 328103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 428103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 528103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 628103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                            ]
                    },
                    {
                        'matchtime' : '2018-12-19',
                        'matchday' : '周五',
                        'matchdata' :
                            [
                                {
                                    infoid : 128103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 138103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 148103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 158103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 168103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 178103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 188103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 198103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 228103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 328103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 428103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 528103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
                                {
                                    infoid : 628103,
                                    fixtureid : 767747,
                                    matchnum : "周四001",
                                    simpleleague : "荷杯",
                                    homesxname : "费耶诺",
                                    homestanding : 0,
                                    awaysxname : "乌德勒",
                                    awaystanding : 0,
                                    rangqiu : -1,
                                    matchgroup : "",
                                    seasonid : 5019,
                                    homename : "费耶诺德",
                                    awayname : "乌德勒支",
                                    matchdate : "12-21",
                                    matchtime : "03:45",
                                    stagegbname : "第三圈",
                                    homeid : 801,
                                    awayid : 1275,
                                    subactive : {
                                        "bfdg" : "1",
                                        "bfgg" : "1",
                                        "bqdg" : "1",
                                        "bqgg" : "1",
                                        "hcgg" : "1",
                                        "jqdg" : "1",
                                        "jqgg" : "1",
                                        "nspfdg" : "0",
                                        "nspfgg" : "1",
                                        "spfdg" : "0",
                                        "spfgg" : "1"
                                    },
                                    mid : 142193,
                                    processname : 4001,
                                    processdate : "2018-12-20",
                                    ptggendtime : "2018-12-20 23:55:00",
                                    nspfpl : {
                                        win : "1.42",
                                        draw : "4.30",
                                        lost : "5.20"
                                    },
                                    nspfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_nspf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_nspf_percent : [ "-", "-", "-" ],
                                    spfpl : {
                                        win : "2.24",
                                        draw : "3.60",
                                        lost : "2.47"
                                    },
                                    spfpl_add : {
                                        win : "",
                                        draw : "",
                                        lost : ""
                                    },
                                    hcount_spf_tzdxcs : [ 0, 0, 0 ],
                                    hcount_spf_percent : [ "-", "-", "-" ],
                                    bfpl : {
                                        "a10" : "8.50",
                                        "a20" : "8.00",
                                        "a21" : "7.25",
                                        "a30" : "11.00",
                                        "a31" : "10.50",
                                        "a32" : "15.00",
                                        "a40" : "19.00",
                                        "a41" : "17.00",
                                        "a42" : "29.00",
                                        "a50" : "39.00",
                                        "a51" : "39.00",
                                        "a52" : "60.00",
                                        "aother" : "19.00",
                                        "c00" : "17.00",
                                        "c11" : "7.50",
                                        "c22" : "12.00",
                                        "c33" : "39.00",
                                        "cother" : "200.00",
                                        "b10" : "16.00",
                                        "b20" : "29.00",
                                        "b21" : "13.00",
                                        "b30" : "70.00",
                                        "b31" : "39.00",
                                        "b32" : "29.00",
                                        "b40" : "250.00",
                                        "b41" : "100.00",
                                        "b42" : "100.00",
                                        "b50" : "600.00",
                                        "b51" : "400.00",
                                        "b52" : "400.00",
                                        "bother" : "80.00"
                                    },
                                    bqcpl : {
                                        "aa" : "1.90",
                                        "ac" : "14.00",
                                        "ab" : "40.00",
                                        "ca" : "4.50",
                                        "cc" : "7.00",
                                        "cb" : "12.00",
                                        "ba" : "19.00",
                                        "bc" : "14.00",
                                        "bb" : "8.50"
                                    },
                                    jqspl : {
                                        "s0" : "17.00",
                                        "s1" : "6.50",
                                        "s2" : "4.00",
                                        "s3" : "3.40",
                                        "s4" : "4.20",
                                        "s5" : "6.50",
                                        "s6" : "11.00",
                                        "s7" : "14.00"
                                    }
                                },
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
                    let simpleleague= data.data[m].matchdata[j].simpleleague
                    if(!this.$base.isExit(filterArr,simpleleague)){
                        filterArr.push(simpleleague)
                    }
                }
            }
            this.$set(this,'filterArr',filterArr)

            let resArr = []
            for(let i in filterArr){
                let list = {}
                list['select'] = true //添加选择状态
                list['simpleleague'] = filterArr[i] //添加比赛名称
                resArr.push(list)
            }
            this.$set(this,'filterList',resArr)
            this.$set(this,'tempfilterList',JSON.parse(JSON.stringify(resArr))) //临时数组，用于筛选取消后恢复

            this.loading = false
        },
        //进入设置页面时缓存页面
        beforeRouteLeave(to, from, next){
            if(to.path == '/jczq/bet'){
                this.$store.commit('setKeepAlivePage','jczq')
            }
            next();
        },
        activated(){
            this.$set(this,'plan',JSON.parse(sessionStorage.getItem("m_jczq_plan")) || {})
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">

</style>
