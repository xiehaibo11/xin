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
                    <span class="chose" @click="show = !show">竞篮-{{selected}}<i class="iconfont icon-jiantou yellow" :class="show ? 'is-active':'no-active'"></i></span>
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
                                                <div class="jc-item-left" :class="{'fx-mt-sm':path !== 'ht' && path !== 'dg'}"  @click="infoSlide(match.infoid)">
                                                    <p>{{match.matchnum}}</p>
                                                    <p>{{match.simpleleague}}</p>
                                                    <p>{{match.ptggendtime | timeStr}}截止</p>
                                                    <p class="c-3">分析<i class="iconfont icon-xialajiantou rotateIcon" :class="infoState(match.infoid) ? 'is-active' : 'no-active'"></i></p>
                                                </div>
                                                <div class="jc-item-right flex">
                                                    <div class="dang-mteam" v-if="path == 'ht' || path == 'dg' || path == 'dxf' || path == 'sfc'">
                                                        <span class="itm-team-l"><i class="c-4">客<em v-if="Number(match.awaystanding)>0">[{{match.awaystanding}}]</em></i>{{match.awayname}}</span>
                                                        <span class="itm-vs">VS</span>
                                                        <span class="itm-team-r">{{match.homename}}<i v-if="Number(match.homestanding)>0">[{{match.homestanding}}]主</i></span>
                                                    </div>
                                                    <!--混合投注 单关投注-->
                                                    <template v-if="path == 'ht' || path == 'dg'">
                                                        <div class="betbtns-box flex-box">
                                                            <div class="betbtns-wrap flex">
                                                                <table class="betbtns-wrap-table">
                                                                    <tbody>
                                                                    <tr>
                                                                        <th class="th-lq-col2">让<br>分</th>
                                                                        <td v-if="Number(match.subactive.rfsfgg)">
                                                                            <table width="100%" class="betbtns" :class="{'border-1px-red' : Number(match.subactive.rfsfdg) && dgIsOpen && path !== 'dg'}">
                                                                                <tr>
                                                                                    <td class="betbtn-pl" :class="{'active' : curOpt(match.mid,'275','3')}" @click="selectDone(match.mid,'275','3',match.rfsf_pl.lost)">客胜<i class="jc-binding">{{match.rfsf_pl.lost}}</i></td>
                                                                                    <td class="betbtn-pl" :class="{'active' : curOpt(match.mid,'275','1')}" @click="selectDone(match.mid,'275','1',match.rfsf_pl.win)">主胜 <em :class="match.rangfen > 0 ? 'red' : 'suc'"><em v-if="match.rangfen>0">+</em>{{match.rangfen}}</em> <i class="jc-binding">{{match.rfsf_pl.win}}</i></td>
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
                                                                        <th class="th-lq-col3">大<br>小<br>分</th>
                                                                        <td v-if="Number(match.subactive.dxfgg)">
                                                                            <table width="100%" class="betbtns" :class="{'border-1px-red' : Number(match.subactive.dxfdg) && dgIsOpen && path !== 'dg'}">
                                                                                <tr>
                                                                                    <td :class="{'active' : curOpt(match.mid,'278','1')}" @click="selectDone(match.mid,'278','1',match.dxf_pl.big)">大于{{match.dxf_pl.yszf}}<i class="jc-binding">{{match.dxf_pl.big}}</i></td>
                                                                                    <td :class="{'active' : curOpt(match.mid,'278','2')}" @click="selectDone(match.mid,'278','2',match.dxf_pl.small)">小于{{match.dxf_pl.yszf}}<i class="jc-binding">{{match.dxf_pl.small}}</i></td>
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
                                                            </div>
                                                            <div class="betbtns-wrap-more" @click="showAll(match)" v-if="gameNum(match.mid) < 1">全部<br>玩法</div>
                                                            <div class="betbtns-wrap-more red" @click="showAll(match)" v-else>已选<br>{{gameNum(match.mid)}}项</div>
                                                        </div>
                                                    </template>
                                                    <!--胜负-->
                                                    <template v-if="path == 'sf'">
                                                        <div class="betbtns-box flex-box">
                                                            <div class="betbtns-wrap flex">
                                                                <table class="betbtns-wrap-table">
                                                                    <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <table width="100%" class="betbtns betbtns-sf">
                                                                                <tr>
                                                                                    <td style="height: 60px" class="betbtn-pl" :class="{'active' : curOpt(match.mid,'274','3'),'border-1px-red' : Number(match.subactive.sfdg) && dgIsOpen && path !== 'dg'}" @click="selectDone(match.mid,'274','3',match.sf_pl.lost)">
                                                                                        <p>{{match.awayname}}(客)</p>
                                                                                        <i class="jc-binding">胜 {{match.sf_pl.win}}</i>
                                                                                    </td>
                                                                                    <td width="35" style="background-color: #f1f1f1;border: none;font-size: 18px" class="c-3">vs</td>
                                                                                    <td class="betbtn-pl" :class="{'active' : curOpt(match.mid,'274','1'),'border-1px-red' : Number(match.subactive.sfdg) && dgIsOpen && path !== 'dg'}" @click="selectDone(match.mid,'274','1',match.sf_pl.win)">
                                                                                        <p>{{match.homename}}</p>
                                                                                        <i class="jc-binding">胜 {{match.sf_pl.lost}}</i>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <!--让分胜负-->
                                                    <template v-if="path == 'rfsf'">
                                                        <div class="betbtns-box flex-box">
                                                            <div class="betbtns-wrap flex">
                                                                <table class="betbtns-wrap-table">
                                                                    <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <table width="100%" class="betbtns betbtns-sf">
                                                                                <tr>
                                                                                    <td style="height: 60px" class="betbtn-pl" :class="{'active' : curOpt(match.mid,'275','3'),'border-1px-red' : Number(match.subactive.rfsfdg) && dgIsOpen && path !== 'dg'}" @click="selectDone(match.mid,'275','3',match.rfsf_pl.lost)">
                                                                                        <p>{{match.awayname}}(客)</p>
                                                                                        <i class="jc-binding">胜 {{match.sf_pl.win}}</i>
                                                                                    </td>
                                                                                    <td width="35" style="background-color: #f1f1f1;border: none;font-size: 18px" class="c-3">vs</td>
                                                                                    <td class="betbtn-pl" :class="{'active' : curOpt(match.mid,'275','1'),'border-1px-red' : Number(match.subactive.rfsfdg) && dgIsOpen && path !== 'dg'}" @click="selectDone(match.mid,'275','1',match.rfsf_pl.win)">
                                                                                        <p>{{match.homename}} <em :class="match.rangfen > 0 ? 'red' : 'suc'"><em v-if="match.rangfen>0">+</em>{{match.rangfen}}</em> </p>
                                                                                        <i class="jc-binding">胜 {{match.sf_pl.lost}}</i>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <!--大小分-->
                                                    <template v-if="path == 'dxf'">
                                                        <div class="betbtns-box flex-box">
                                                            <div class="betbtns-wrap flex">
                                                                <table class="betbtns-wrap-table" style="margin-top : -1px">
                                                                    <tbody>
                                                                    <tr>
                                                                        <td v-if="Number(match.subactive.dxfgg)">
                                                                            <table width="100%" class="betbtns" :class="{'border-1px-red' : Number(match.subactive.dxfdg) && dgIsOpen && path !== 'dg'}">
                                                                                <tr>
                                                                                    <td :class="{'active' : curOpt(match.mid,'278','1')}" @click="selectDone(match.mid,'278','1',match.dxf_pl.big)">大于{{match.dxf_pl.yszf}}<i class="jc-binding">{{match.dxf_pl.big}}</i></td>
                                                                                    <td :class="{'active' : curOpt(match.mid,'278','2')}" @click="selectDone(match.mid,'278','2',match.dxf_pl.small)">小于{{match.dxf_pl.yszf}}<i class="jc-binding">{{match.dxf_pl.small}}</i></td>
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
                                                    <!--胜分差-->
                                                    <template v-if="path == 'sfc'">
                                                        <div class="betsel-list-wrap flex-box">
                                                            <div class="betbtns-wrap flex">
                                                                <div class="betbtns" :class="{'border-1px-red': Number(match.subactive.sfcdg) && dgIsOpen}">
                                                                    <div class="betbtn-pl-item" @click="showAll(match)" v-if="gameNum(match.mid) < 1">
                                                                        <span style="display: block" class="c-2">点击展开投注选项</span>
                                                                    </div>
                                                                    <div class="red betbtn-pl-item" :style="{'width': contentW + 'px'}" @click="showAll(match)" v-else>
                                                                        <span class="text-ellipsis" style="display: block">{{gameBetCont(match.mid,match.homename,match.awayname)}}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
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
                            <span class="itm-team-l"><i class="c-4">客<em v-if="Number(curData.awaystanding)>0">[{{curData.awaystanding}}]</em></i>{{curData.awayname}}</span>
                            <span class="itm-vs">VS</span>
                            <span class="itm-team-r">{{curData.homename}}<i v-if="Number(curData.homestanding)>0">[{{curData.homestanding}}]主</i></span>
                        </div>
                    </template>
                    <template slot="main">
                        <div class="jc-dialog-cont mt-sm">
                            <div class="popbox-betsel-tablewrap">
                                <template v-if="path == 'ht'|| path == 'dg'">
                                    <!--非让分-->
                                    <table class="popbox-betsel-table">
                                        <tbody>
                                        <tr>
                                            <th class="th-lq-col1">非让分</th>
                                            <template v-if="Number(curData.subactive.sfgg)">
                                                <td>
                                                    <table class="all-table" width="100%" :class="{'border-1px-red': Number(curData.subactive.sfdg) && dgIsOpen && path !== 'dg'}">
                                                        <tr>
                                                            <td :class="{'active':curOpt('274','3')}" @click="selectDoneTemp('274','3',curData.sf_pl.lost)">客胜<i class="jc-binding">{{curData.sf_pl.lost}}</i></td>
                                                            <td :class="{'active':curOpt('274','1')}" @click="selectDoneTemp('274','1',curData.sf_pl.win)">主胜<i class="jc-binding">{{curData.sf_pl.win}}</i></td>
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
                                    <!--让分-->
                                    <table class="popbox-betsel-table">
                                        <tbody>
                                        <tr>
                                            <th class="th-lq-col2">让分</th>
                                            <template v-if="Number(curData.subactive.rfsfgg)">
                                                <td>
                                                    <table class="all-table" width="100%" :class="{'border-1px-red': Number(curData.subactive.rfsfdg) && dgIsOpen && path !== 'dg'}">
                                                        <tr>
                                                            <td :class="{'active':curOpt('275','3')}" @click="selectDoneTemp('275','3',curData.rfsf_pl.lost)">客胜<i class="jc-binding">{{curData.rfsf_pl.lost}}</i></td>
                                                            <td :class="{'active':curOpt('275','1')}" @click="selectDoneTemp('275','1',curData.rfsf_pl.win)">主胜 <em :class="curData.rangfen > 0 ? 'red' : 'suc'"><em v-if="curData.rangfen>0">+</em>{{curData.rangfen}}</em><i class="jc-binding">{{curData.rfsf_pl.win}}</i></td>
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
                                    <!--大小分-->
                                    <table class="popbox-betsel-table">
                                        <tbody>
                                        <tr>
                                            <th class="th-lq-col3" style="border-bottom: none;">大小分</th>
                                            <template v-if="Number(curData.subactive.dxfgg)">
                                                <table class="all-table" width="100%" :class="{'border-1px-red': Number(curData.subactive.dxfdg) && dgIsOpen && path !== 'dg'}">
                                                    <tr>
                                                        <td :class="{'active' : curOpt('278','1')}" @click="selectDoneTemp('278','1',curData.dxf_pl.big)">大于{{curData.dxf_pl.yszf}}<i class="jc-binding">{{curData.dxf_pl.big}}</i></td>
                                                        <td :class="{'active' : curOpt('278','2')}" @click="selectDoneTemp('278','2',curData.dxf_pl.small)">小于{{curData.dxf_pl.yszf}}<i class="jc-binding">{{curData.dxf_pl.small}}</i></td>
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
                                </template>
                                <!--胜分差-->
                                <template v-if="path == 'sfc' || path == 'ht'|| path == 'dg'">
                                    <div class="c-2 f-sm">胜分差</div>
                                    <table class="popbox-betsel-table">
                                        <tbody>
                                        <tr>
                                            <th class="th-lq-col4" style="height: 86px">客胜</th>
                                            <td rowspan="2">
                                                <table class="all-table" width="100%" :class="{'border-1px-red': Number(curData.subactive.sfcdg) && dgIsOpen && path !== 'dg'}">
                                                    <tr>
                                                        <td :class="{'active' : curOpt('276','201')}" @click="selectDoneTemp('276','201',curData.sfc_pl.lost[0])">1-5<i class="jc-binding">{{curData.sfc_pl.lost[0]}}</i></td>
                                                        <td :class="{'active' : curOpt('276','202')}" @click="selectDoneTemp('276','202',curData.sfc_pl.lost[1])">6-10<i class="jc-binding">{{curData.sfc_pl.lost[1]}}</i></td>
                                                        <td :class="{'active' : curOpt('276','203')}" @click="selectDoneTemp('276','203',curData.sfc_pl.lost[2])">11-15<i class="jc-binding">{{curData.sfc_pl.lost[2]}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active' : curOpt('276','204')}" @click="selectDoneTemp('276','204',curData.sfc_pl.lost[3])">16-20<i class="jc-binding">{{curData.sfc_pl.lost[3]}}</i></td>
                                                        <td :class="{'active' : curOpt('276','205')}" @click="selectDoneTemp('276','205',curData.sfc_pl.lost[4])">21-25<i class="jc-binding">{{curData.sfc_pl.lost[4]}}</i></td>
                                                        <td :class="{'active' : curOpt('276','206')}" @click="selectDoneTemp('276','206',curData.sfc_pl.lost[5])">26+<i class="jc-binding">{{curData.sfc_pl.lost[5]}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active' : curOpt('276','101')}" @click="selectDoneTemp('276','101',curData.sfc_pl.win[0])">1-5<i class="jc-binding">{{curData.sfc_pl.win[0]}}</i></td>
                                                        <td :class="{'active' : curOpt('276','102')}" @click="selectDoneTemp('276','102',curData.sfc_pl.win[1])">6-10<i class="jc-binding">{{curData.sfc_pl.win[1]}}</i></td>
                                                        <td :class="{'active' : curOpt('276','103')}" @click="selectDoneTemp('276','103',curData.sfc_pl.win[2])">11-15<i class="jc-binding">{{curData.sfc_pl.win[2]}}</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td :class="{'active' : curOpt('276','104')}" @click="selectDoneTemp('276','104',curData.sfc_pl.win[3])">16-20<i class="jc-binding">{{curData.sfc_pl.win[3]}}</i></td>
                                                        <td :class="{'active' : curOpt('276','105')}" @click="selectDoneTemp('276','105',curData.sfc_pl.win[4])">21-25<i class="jc-binding">{{curData.sfc_pl.win[4]}}</i></td>
                                                        <td :class="{'active' : curOpt('276','106')}" @click="selectDoneTemp('276','106',curData.sfc_pl.win[5])">26+<i class="jc-binding">{{curData.sfc_pl.win[5]}}</i></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="th-lq-col5" style="height: 86px">主胜</th>
                                        </tr>
                                        </tbody>
                                    </table>
                                </template>
                            </div>
                        </div>
                    </template>
                </base-modal>
                <!--查看全部 end-->
                <!--底部-->
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
                <!--底部 end-->
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
        name: 'jclq',
        components:{
            StopSaleTips,
            BaseModal
        },
        data () {
            return {
                barFixed : -1,
                betMap:{
                    274 : 'sfdg',
                    275 : 'rfsfdg',
                    276 : 'sfcdg',
                    278 : 'dxfdg'
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
                    averagepl: [2.01, 1.81],
                    awayid: 12,
                    awayname: "快船",
                    awaystanding: 8,
                    awaysxname: "快船",
                    bgcolor: "#df3737",
                    dxf_pl: {big: 1.75, small: 1.75, yszf: 215.5},
                    fixtureid: 136085,
                    homeid: 26,
                    homename: "活塞",
                    homestanding: 9,
                    homesxname: "活塞",
                    infoid: 18047,
                    isright: "1",
                    matchdate: "02-03",
                    matchnum: "周六301",
                    matchtime: "06:00",
                    mid: 26197,
                    processdate: "2019-02-02",
                    processname: 6301,
                    ptggendtime: "2019-02-03 00:55:00",
                    rangfen: -1.5,
                    rfsf_bl: ["-", "-"],
                    rfsf_pl: {win: 1.75, lost: 1.75},
                    seasonid: 472,
                    sf_bl: ["-", "-"],
                    sf_pl: {win: 1.63, lost: 1.87},
                    sfc_pl: {lost: [4.25, 4.45, 8.6, 16.5, 31, 39], win: [4.05, 4.15, 7.45, 13.5, 27, 32]},
                    simpleleague: "NBA",
                    stagegbname: "",
                    subactive: {dxfdg: "0",
                        dxfgg: "1",
                        hcgg: "1",
                        rfsfdg: "0",
                        rfsfgg: "1",
                        sfcdg: "1",
                        sfcgg: "1",
                        sfdg: "0",
                        sfgg: "1"},
                },//显示全部的数据

                matchInfo:[], //赛事列表数据
                fxInfo:{  //分析数组
                    against: "", //历史交锋
                    averagepl: [3.33, 3.39, 2.08],
                    history: ["7胜1平2负", "1胜4平5负"]
                },
                //投注
                plan:this.$store.state.jclq.plan, //投注方案内容
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
                    {"name":"胜负","type":"1",'path':'sf'},
                    {"name":"让分胜负","type":"2",'path':'rfsf'},
                    {"name":"胜分差","type":"3",'path':'sfc'},
                    {"name":"大小分","type":"4",'path':'dxf'},
                    {"name":"混合过关","type":"5",'path':'ht'}
                ]
                if(this.dgIsOpen){
                    arr.push({"name":"单关","type":"6",'path':'dg'})
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
                if(this.$route.path !== '/jclq/bet' && this.$route.path !== '/optimalBonus'){
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
            //比分差玩法选项显示
            gameBetCont(){
                return (mid,homename,awayname)=>{
                    let arr = []
                    if(this.plan.hasOwnProperty(mid)){
                        for(let key in this.plan[mid]){
                            for(let _key in this.plan[mid][key]){
                                arr.push(_key)
                            }
                        }
                    }
                    let resArr1 = []
                    let resArr2 = []
                    let str = ''
                    if(this.path == 'sfc'){
                        const initArr = [[{sign: '201',bet:'1-5'},{sign: '202',bet:'6-10'},{sign: '203',bet:'11-15'},{sign: '204',bet:'16-21'},{sign: '205',bet:'21-25'},{sign: '206',bet:'26+'}],
                            [{sign: '101',bet:'1-5'},{sign: '102',bet:'6-10'},{sign: '103',bet:'11-15'},{sign: '104',bet:'16-21'},{sign: '105',bet:'21-25'},{sign: '106',bet:'26+'}]]
                        for(let i in initArr[0]){
                            if(arr.indexOf(initArr[0][i].sign) > -1){
                                resArr1.push(initArr[0][i].bet)
                            }
                        }
                        for(let j in initArr[1]){
                            if(arr.indexOf(initArr[1][j].sign) > -1){
                                resArr2.push(initArr[1][j].bet)
                            }
                        }
                        let str1 = resArr1.length ? awayname + '(客)胜:'+  resArr1.join(',') : ''
                        let str2 = resArr2.length ? homename + '(主)胜:'+  resArr2.join(',') : ''
                        str = str1 + ' ' + str2
                    }
                    return str;
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
                    'sf' : {"name":"胜负","type":"1"},
                    'rfsf' : {"name":"让分胜负","type":"2"},
                    'sfc' : {"name":"胜分差","type":"3"},
                    'dxf' : {"name":"大小分","type":"4"},
                    'ht' : {"name":"混合过关","type":"5"},
                    'dg' : {"name":"单关","type":"6"}
                }
            },
            //选中的玩法
            selected(){
                return this.$route.path !== '/jclq/bet' && this.$route.path !== '/optimalBonus' ? this.playObj[this.path].name : ''
            },
            //选中的玩法对应的type
            type(){
                return this.$route.path !== '/jclq/bet' && this.$route.path !== '/optimalBonus' ? this.playObj[this.path].type : ''
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
                this.$store.commit('clearJclqBetData')
            },
            //选择玩法
            chosePlay(type){
                this.$refs.wrapper.scrollTop = 0
                this.$router.push({
                    path: '/jclq',
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
                this.$store.commit('setJcLqPlan',this.plan)
                this.$store.commit('setJcLqTempMatchData',this.tempMatchData)
                this.$router.push({
                    path : '/jclq/bet',
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
                        this.$store.commit('delKeepAlivePage','jclq') //清除投注页面缓存
                        this.$store.commit('delKeepAlivePage','jcOrder') //清除订单页面缓存
                        this.$store.commit('clearJclqBetData')
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
                        'matchtime': '2019-02-02', //投注日期
                        'matchday': '周六', //投注星期
                        'matchdata' :
                            [
                                {
                                    averagepl: [3.15, 1.37],
                                    awayid: 22,
                                    awayname: "公牛",
                                    awaystanding: 13,
                                    awaysxname: "公牛",
                                    bgcolor: "#df3737",
                                    dxf_pl: {big: 1.8, small: 1.69, yszf: 217.5},
                                    fixtureid: 136086,
                                    homeid: 23,
                                    homename: "黄蜂",
                                    homestanding: 7,
                                    homesxname: "黄蜂",
                                    infoid: 18048,
                                    isright: "1",
                                    matchdate: "02-03",
                                    matchnum: "周六302",
                                    matchtime: "08:00",
                                    mid: 26186,
                                    processdate: "2019-02-02",
                                    processname: 6302,
                                    ptggendtime: "2019-02-03 00:55:00",
                                    rangfen: -6.5,
                                    rfsf_bl: ["-", "-"],
                                    rfsf_pl: {win: 1.75, lost: 1.75},
                                    seasonid: 472,
                                    sf_bl: ["-", "-"],
                                    sf_pl: {win: 1.22, lost: 3.05},
                                    sfc_pl: {lost: [5.9, 7.45, 13.5, 32, 60, 70], win: [4.3, 3.35, 4.75, 7.7, 13, 14]},
                                    simpleleague: "NBA",
                                    stagegbname: "",
                                    subactive: {dxfdg: "0",
                                        dxfgg: "1",
                                        hcgg: "1",
                                        rfsfdg: "0",
                                        rfsfgg: "1",
                                        sfcdg: "1",
                                        sfcgg: "1",
                                        sfdg: "0",
                                        sfgg: "1"},
                                },
                                {
                                    averagepl: [2.08, 1.75],
                                    awayid: 30,
                                    awayname: "篮网",
                                    awaystanding: 6,
                                    awaysxname: "篮网",
                                    bgcolor: "#df3737",
                                    dxf_pl: {big: 1.75, small: 1.75, yszf: 219.5},
                                    fixtureid: 136088,
                                    homeid: 18,
                                    homename: "魔术",
                                    homestanding: 11,
                                    homesxname: "魔术",
                                    infoid: 18049,
                                    isright: "1",
                                    matchdate: "02-03",
                                    matchnum: "周六303",
                                    matchtime: "08:00",
                                    mid: 26187,
                                    processdate: "2019-02-02",
                                    processname: 6303,
                                    ptggendtime: "2019-02-03 00:55:00",
                                    rangfen: 1.5,
                                    rfsf_bl: ["-", "-"],
                                    rfsf_pl: {win: 1.68, lost: 1.81},
                                    seasonid: 472,
                                    sf_bl: ["-", "-"],
                                    sf_pl: {win: 1.59, lost: 1.92},
                                    sfc_pl: {lost: [4.35, 4.55, 8.9, 18, 32, 41], win: [4, 4.05, 7.1, 13, 26, 30]},
                                    simpleleague: "NBA",
                                    stagegbname: "",
                                    subactive: {dxfdg: "0",
                                        dxfgg: "1",
                                        hcgg: "1",
                                        rfsfdg: "0",
                                        rfsfgg: "1",
                                        sfcdg: "1",
                                        sfcgg: "1",
                                        sfdg: "0",
                                        sfgg: "1"}
                                }
                            ]
                    },
                    {
                        'matchtime' : '2019-02-11',
                        'matchday' : '周一',
                        'matchdata' :
                            [
                                {
                                "infoid" : 18059,
                                "fixtureid" : 136151,
                                "matchnum" : "周一301",
                                "simpleleague" : "NBA",
                                "homesxname" : "活塞",
                                "homestanding" : 9,
                                "awaysxname" : "奇才",
                                "awaystanding" : 11,
                                "rangfen" : -4.5,
                                "seasonid" : 472,
                                "homename" : "活塞",
                                "awayname" : "奇才",
                                "matchdate" : "02-12",
                                "matchtime" : "08:00",
                                "stagegbname" : "",
                                "homeid" : 26,
                                "awayid" : 27,
                                "mid" : 26198,
                                "bgcolor" : "#df3737",
                                "processname" : 1301,
                                "processdate" : "2019-02-11",
                                "ptggendtime" : "2019-02-11 23:55:00",
                                "isright" : "1",
                                "averagepl" : [ 2.45, 1.56 ],
                                "sf_bl" : [ "-", "-" ],
                                "sf_pl" : {
                                    "win" : 1.39,
                                    "lost" : 2.18
                                },
                                "rfsf_bl" : [ "-", "-" ],
                                "rfsf_pl" : {
                                    "win" : 1.76,
                                    "lost" : 1.64
                                },
                                "dxf_pl" : {
                                    "big" : 1.75,
                                    "small" : 1.65,
                                    "yszf" : 220.5
                                },
                                "sfc_pl" : {
                                    "lost" : [ 4.75, 5.5, 10.5, 22.0, 37.0, 52.0 ],
                                    "win" : [ 4.1, 3.75, 5.75, 10.0, 20.0, 22.0 ]
                                },
                                "subactive" : {
                                    "dxfdg" : "0",
                                    "dxfgg" : "1",
                                    "hcgg" : "1",
                                    "rfsfdg" : "0",
                                    "rfsfgg" : "1",
                                    "sfcdg" : "1",
                                    "sfcgg" : "1",
                                    "sfdg" : "0",
                                    "sfgg" : "1"
                                }
                            }, {
                                "infoid" : 18060,
                                "fixtureid" : 136150,
                                "matchnum" : "周一302",
                                "simpleleague" : "NBA",
                                "homesxname" : "步行者",
                                "homestanding" : 3,
                                "awaysxname" : "黄蜂",
                                "awaystanding" : 7,
                                "rangfen" : -5.5,
                                "seasonid" : 472,
                                "homename" : "步行者",
                                "awayname" : "黄蜂",
                                "matchdate" : "02-12",
                                "matchtime" : "08:00",
                                "stagegbname" : "",
                                "homeid" : 28,
                                "awayid" : 23,
                                "mid" : 26199,
                                "bgcolor" : "#df3737",
                                "processname" : 1302,
                                "processdate" : "2019-02-11",
                                "ptggendtime" : "2019-02-11 23:55:00",
                                "isright" : "1",
                                "averagepl" : [ 2.74, 1.45 ],
                                "sf_bl" : [ "-", "-" ],
                                "sf_pl" : {
                                    "win" : 1.28,
                                    "lost" : 2.52
                                },
                                "rfsf_bl" : [ "-", "-" ],
                                "rfsf_pl" : {
                                    "win" : 1.64,
                                    "lost" : 1.76
                                },
                                "dxf_pl" : {
                                    "big" : 1.7,
                                    "small" : 1.7,
                                    "yszf" : 217.5
                                },
                                "sfc_pl" : {
                                    "lost" : [ 5.25, 6.45, 12.0, 28.0, 50.0, 60.0 ],
                                    "win" : [ 4.2, 3.5, 5.15, 8.4, 16.0, 17.0 ]
                                },
                                "subactive" : {
                                    "dxfdg" : "0",
                                    "dxfgg" : "1",
                                    "hcgg" : "1",
                                    "rfsfdg" : "0",
                                    "rfsfgg" : "1",
                                    "sfcdg" : "1",
                                    "sfcgg" : "1",
                                    "sfdg" : "0",
                                    "sfgg" : "1"
                                }
                            }, {
                                "infoid" : 18061,
                                "fixtureid" : 136156,
                                "matchnum" : "周一303",
                                "simpleleague" : "NBA",
                                "homesxname" : "火箭",
                                "homestanding" : 5,
                                "awaysxname" : "独行侠",
                                "awaystanding" : 11,
                                "rangfen" : -8.5,
                                "seasonid" : 472,
                                "homename" : "火箭",
                                "awayname" : "独行侠",
                                "matchdate" : "02-12",
                                "matchtime" : "09:00",
                                "stagegbname" : "",
                                "homeid" : 7,
                                "awayid" : 2,
                                "mid" : 26200,
                                "bgcolor" : "#df3737",
                                "processname" : 1303,
                                "processdate" : "2019-02-11",
                                "ptggendtime" : "2019-02-11 23:55:00",
                                "isright" : "1",
                                "averagepl" : [ 0.0, 0.0 ],
                                "sf_bl" : [ "-", "-" ],
                                "sf_pl" : {
                                    "win" : 1.17,
                                    "lost" : 3.08
                                },
                                "rfsf_bl" : [ "-", "-" ],
                                "rfsf_pl" : {
                                    "win" : 1.7,
                                    "lost" : 1.7
                                },
                                "dxf_pl" : {
                                    "big" : 1.65,
                                    "small" : 1.75,
                                    "yszf" : 223.5
                                },
                                "sfc_pl" : {
                                    "lost" : [ 6.2, 7.8, 14.5, 34.0, 64.0, 75.0 ],
                                    "win" : [ 4.4, 3.35, 4.6, 7.2, 12.0, 13.0 ]
                                },
                                "subactive" : {
                                    "dxfdg" : "0",
                                    "dxfgg" : "1",
                                    "hcgg" : "1",
                                    "rfsfdg" : "0",
                                    "rfsfgg" : "1",
                                    "sfcdg" : "1",
                                    "sfcgg" : "1",
                                    "sfdg" : "0",
                                    "sfgg" : "1"
                                }
                            }, {
                                "infoid" : 18062,
                                "fixtureid" : 136155,
                                "matchnum" : "周一304",
                                "simpleleague" : "NBA",
                                "homesxname" : "森林狼",
                                "homestanding" : 12,
                                "awaysxname" : "快船",
                                "awaystanding" : 8,
                                "rangfen" : -2.5,
                                "seasonid" : 472,
                                "homename" : "森林狼",
                                "awayname" : "快船",
                                "matchdate" : "02-12",
                                "matchtime" : "09:00",
                                "stagegbname" : "",
                                "homeid" : 15,
                                "awayid" : 12,
                                "mid" : 26201,
                                "bgcolor" : "#df3737",
                                "processname" : 1304,
                                "processdate" : "2019-02-11",
                                "ptggendtime" : "2019-02-11 23:55:00",
                                "isright" : "1",
                                "averagepl" : [ 2.16, 1.66 ],
                                "sf_bl" : [ "-", "-" ],
                                "sf_pl" : {
                                    "win" : 1.47,
                                    "lost" : 2.01
                                },
                                "rfsf_bl" : [ "-", "-" ],
                                "rfsf_pl" : {
                                    "win" : 1.64,
                                    "lost" : 1.76
                                },
                                "dxf_pl" : {
                                    "big" : 1.65,
                                    "small" : 1.75,
                                    "yszf" : 227.5
                                },
                                "sfc_pl" : {
                                    "lost" : [ 4.5, 5.0, 9.6, 20.0, 34.0, 47.0 ],
                                    "win" : [ 4.0, 3.95, 6.4, 11.0, 23.0, 26.0 ]
                                },
                                "subactive" : {
                                    "dxfdg" : "0",
                                    "dxfgg" : "1",
                                    "hcgg" : "1",
                                    "rfsfdg" : "0",
                                    "rfsfgg" : "1",
                                    "sfcdg" : "1",
                                    "sfcgg" : "1",
                                    "sfdg" : "0",
                                    "sfgg" : "1"
                                }
                            } ]
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
            if(to.path == '/jclq/bet'){
                this.$store.commit('setKeepAlivePage','jclq')
            }
            next();
        },
        activated(){
            this.$set(this,'plan',JSON.parse(sessionStorage.getItem("m_jclq_plan")) || {})
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .betbtns-wrap{

    }
</style>
