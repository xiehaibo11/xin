<template>
    <div class="jc-order-main">
        <mt-header :title="title">
            <mt-button icon="back" slot="left" @click.native="goHome">返回大厅</mt-button>
        </mt-header>
        <div class="tc border-bottom-1px btn-back"  @click="goBack">
            + 继续选择比赛 <em class="c-3 f-sm">(已选{{games}}场)</em>
        </div>
        <!--选号列表-->
        <div class="jc-bet-cont jc-order contentH" :style="{height:contentH + 'px'}">
            <!--无相关赛事-->
            <template v-if="!Object.keys(tempMatchData).length">
                <div class="jc-empty tc">
                    <p class="ft">
                        <svg slot="icon" class="icon" aria-hidden="true">
                            <use xlink:href="#icon-zanwubisai"></use>
                        </svg>
                    </p>
                    <p class="tc c-4">未选择任何赛事</p>
                    <mt-button size="small" @click.native="goBack" class="mt">+ 立即添加</mt-button>
                </div>
            </template>
            <template v-else>
                <!--赛事列表-->
                <div>
                    <template v-for="(match,i) in tempMatchData">
                        <div class="border-bottom-1px" :key="match.infoid">
                            <div class="flex-box jc-item">
                                <div class="jc-item-right flex">
                                    <div class="dang-mteam flex-box">
                                        <div>{{match.matchnum}} {{match.simpleleague}}</div>
                                        <div class="flex tr">
                                            <span class="itm-team-l"><i class="c-4 f-mini">(主)</i>{{match.homename}}</span>
                                            <span class="c-4">VS</span>
                                            <span class="itm-team-r">{{match.awayname}}</span>
                                        </div>
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
                                <div class="jc-item-del" @click="deletMatch(match.mid)"><i class="iconfont icon-chucuo"></i></div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
        <!--选号列表 end-->

        <!--查看全部 弹窗-->
        <base-modal v-model="allVisible"  @hanle-cancel="betCancel" @hanle-sure="betSure">
            <template slot="header">
                <div class="jc-dialog-head">
                    <span class="itm-team-l"><i class="c-4">主<em v-if="Number(curData.homestanding)>0">[{{curData.homestanding}}]</em></i>{{curData.homename}}</span>
                    <span class="itm-vs">VS</span>
                    <span class="itm-team-r">{{curData.awayname}}<i v-if="Number(curData.awaystanding)>0">[{{curData.awaystanding}}]</i></span>
                </div>
            </template>
            <template slot="main" class="jc-dialog-cont">
                <div class="jc-dialog-cont mt-sm">
                    <template v-if="path == 'ht'|| path == 'dg'">
                        <div class="popbox-betsel-tablewrap">
                            <table class="popbox-betsel-table" style="margin-bottom: 0">
                                <tbody>
                                <tr>
                                    <th class="popbox-betsel-blueth">非让球</th>
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
                                        <th class="popbox-betsel-redth ng-binding" style="border-bottom: none;">让球<br/>+{{curData.rangqiu || '--'}}</th>
                                    </template>
                                    <template v-else>
                                        <th class="popbox-betsel-greenth ng-binding" style="border-bottom: none;">让球<br/>{{curData.rangqiu || '--'}}</th>
                                    </template>
                                    <template v-if="!Number(curData.subactive.spfgg)">
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
                            <table class="popbox-betsel-table" >
                                <tbody>
                                <tr>
                                    <th class="popbox-betsel-glight">比分</th>
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
                                    <th class="popbox-betsel-green2th">总进球</th>
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
                                    <th class="popbox-betsel-orgh">半全场</th>
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

        <!--底部-->
        <div class="jc-order-foot">
            <div class="foot-agree flex-box">
                <!--投注协议-->
                <bet-agree-checked v-model="checked"></bet-agree-checked>
                <!--投注协议 end-->
                <div class="flex tr"><i class="iconfont icon-shanchu btn-icon-clear" @click="clear"></i></div>
            </div>
            <div class="foot-set flex-box">
                <div class="flex tl">
                    <button class="mint-button set-type" @click="setTypeShow">
                        <span class="words">
                             <template v-if="!type[0].length">
                                 过关方式 <em class="red">(必选)</em>
                             </template>
                             <template v-else>
                                 {{type[1]}}
                             </template>
                        </span>
                        <i class="iconfont icon-xialajiantou"></i>
                    </button>
                </div>
                <div class="flex tr"><em>投</em> <input-number v-model="multiple" :min="1" :step="1"></input-number> <em>倍</em></div>
            </div>
            <div class="bet-submit flex-box">
                <button @click="toOptimalBonus" class="bet-btn btn-sure" style="background-color: #FF9800; padding: 8px 8px; color: #fff;margin-left: 0;font-size: 14px"><i class="iconfont icon-optimize"></i> 奖金优化</button>
                <button @click="handleJoin" v-if="joinOpen" class="bet-btn btn-sure" style="font-size: 14px;margin-left: 5px;background-color: #607D8B;padding: 8px 8px;">合买</button>
                <div class="acount-award flex tr">
                    <p class="f-sm">{{notes}}注 <b class="red">{{totalMoney}}</b>{{lotteryUnit}}</p>
                    <p class="c-3 f-mini">预计奖金<em :class="{'red' : maxGain > 0}"><em v-if="minGain !== maxGain">{{minGain}}~</em>{{maxGain}}</em>{{lotteryUnit}}</p>
                </div>
                <button class="bet-btn btn-sure" @click="doBuy">提交</button>
            </div>
        </div>
        <!--底部 end-->

        <!--过关方式设置-->
        <mt-popup class="type-set-popup"
            v-model="setVisible"
            position="bottom" style="padding:0">
            <div class="type-set-item">
                <h3>过关方式</h3>
                <ul class="cf">
                    <template v-for="(item,index) in typeOptions">
                        <li class="sel-item" v-if="index == 0 && isAllDgMatch && dgIsOpen" :class="{'active' : item.select}" @click="handleType(index)">
                            {{item.label}}<i class="iconfont icon-xuanzhong" v-show="item.select"></i>
                        </li>
                        <li class="sel-item" v-if="path !== 'dg' && index > 0 && index < games" :class="{'active' : item.select}" @click="handleType(index)">
                            {{item.label}}<i class="iconfont icon-xuanzhong" v-show="item.select"></i>
                        </li>
                    </template>
                </ul>
            </div>
            <div class="flex-box type-set-btn">
                <div class="flex border-right-1px" @click="cancelSetType">取消</div>
                <div class="flex" @click="sureSetType">确定</div>
            </div>
        </mt-popup>
        <!--过关方式设置 end-->

        <!--订单信息确认-->
        <mt-popup
            v-model="orderVisible"
            position="bottom">
            <div class="jc-orders-info" v-if="orderVisible">
                <div class="order-title org border-bottom-1px">订单信息确认</div>
                <div class="order-info">
                    <div>
                        <span class="name">投注彩种:</span>
                        <span class="info">竞彩足球-{{playMap[path]}}</span>
                    </div>
                    <div>
                        <span class="name">过关方式:</span>
                        <span class="info">{{type[1]}}</span>
                    </div>
                    <!--发起合买-->
                    <div v-if="buyType==2">
                        <span class="name">购买方式:</span>
                        <span class="info">发起合买</span>
                    </div>
                    <!--跟单设置-->
                    <div class="gendan-set" v-if="buyType==1">
                        <div class="flex-box">
                            <span class="name">发起跟单:</span>
                            <span class="info gendan-btn">
                                <em :class="{'active': is_gendan}" @click="is_gendan=true">发起</em>
                                <em @click="is_gendan=false" :class="{'active': !is_gendan}">不发起</em>
                            </span>
                        </div>
                        <div class="c-3 f-mini mt-sm mf-sm" style="line-height: 1.2">发起跟单开启，彩民跟单中奖盈利后您可获得一定比例的佣金。</div>
                        <template v-if="is_gendan">
                            <div class="gendan-set-info">
                                <div class="gendan-tips tl mt-sm mf-sm">
                                    <mt-button size="small" class="org">本方案起跟金额：{{notes * 2}}{{lotteryUnit}}</mt-button>
                                </div>
                                <div class="flex-box" style="align-items: flex-start;">
                                    <div class="name">跟单宣言:</div>
                                    <div class="gendan-xy flex">
                                        <textarea placeholder="好的方案宣言能够帮您获得更多的跟单，字数在50字内!" rows="3" v-model="gendan.declaration"></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div>
                        <span class="name">方案金额:</span>
                        <span class="info">
                            <b class="red f-large">{{totalMoney}}</b>{{lotteryUnit}}
                            <span class="join-tip">
                                    ({{notes}}注 <em>{{multiple}}倍</em>)
                            </span>
                        </span>
                    </div>
                    <!--发起合买-->
                    <template v-if="buyType==2">
                        <div>
                            <span class="name">认购金额:</span>
                            <span class="info">
                                <b class="red f-large">{{countShareMoney(join.buy_share+join.bd_share)}}</b> {{lotteryUnit}} <em class="c-3 f-mini">(认购{{countShareMoney(join.buy_share)}}{{lotteryUnit}}+保底{{countShareMoney(join.bd_share)}}{{lotteryUnit}})</em>
                            </span>
                        </div>
                        <div v-if="isGain">
                            <span class="name">盈利佣金:</span>
                            <span class="info">{{join.gain}}%</span>
                        </div>
                    </template>
                    <div>
                        <span class="name">账户余额:</span>
                        <span class="info"><em>{{money}}</em> {{lotteryUnit}}</span>
                        <span class="tips tc" style="font-size: 14px;color: #333" v-if="money < totalMoney"><b class="red">（余额不足，请充值!）</b></span>
                    </div>
                </div>
                <div class="order-footer tr">
                    <mt-button class="cancel" @click="orderVisible = false" size="small">取消</mt-button>
                    <mt-button type="primary"  size="small" v-if="money < totalMoney" @click="goPay">立即充值</mt-button>
                    <mt-button type="primary" v-else @click="payment" size="small">
                        <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                        确定购买
                    </mt-button>
                </div>
            </div>
        </mt-popup>
        <!--订单信息确认 end-->

        <!--合买-->
        <transition name="slide-left">
            <div v-if="joinVisible" class="join-visible">
                <order-join-set :total-money="totalMoney" v-model="joinVisible" :multiple="multiple" @submit-order="getJoinEmit" :total-notes="notes"></order-join-set>
            </div>
        </transition>
        <!--合买 end-->
    </div>
</template>

<script>
    import BaseModal from 'components/common/BaseModal.vue' //弹窗
    import inputNumber from 'components/common/InputNumber.vue' //购买输入框组件
    import BetAgreeChecked from 'components/lottery/BetAgreeChecked.vue' //投注协议
    import OrderJoinSet from 'components/lottery/OrderJoinSet.vue' //合买
    export default {
        name: 'jcOrder',
        components:{
            BaseModal,
            inputNumber,
            BetAgreeChecked,
            OrderJoinSet
        },
        data () {
            return {
                buyType:1, //购买方式

                loading: false,
                dgIsOpen: true, //单关开关
                plan: this.$store.state.jczq.plan, //投注方案内容
                tempPlan: this.$store.state.jczq.plan || {}, //临时投注方案内容，用于取消后恢复
                multiple : 1, //投注倍数
                minGainArr:[], //单场最小赔率组
                maxGain: 0, //最大奖金

                allVisible: false,//全部玩法弹窗
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
                },//全部玩法弹窗的数据

                setVisible: false, //过关方式设置
                checked : true,//投注协议确认
                orderVisible: false, //订单详情
                joinVisible : false, //合买设置

                typeOptions:[
                    {label: '单关',select : false},
                    {label: '2串1',select : false},
                    {label: '3串1',select : false},
                    {label: '4串1',select : false},
                    {label: '5串1',select : false},
                    {label: '6串1',select : false},
                    {label: '7串1',select : false},
                    {label: '8串1',select : false}
                    ],
                tempTypeOptions:[],//临时数据，取消后恢复原有过关方式

                betMap:{
                    269 : 'spfdg',
                    354 : 'nspfdg',
                    270 : 'jqdg',
                    271 : 'bfdg',
                    272 : 'bqdg',
                    276 : 'sfcdg'
                },

                //合买数据
                join: {
                    total_share:0,//份数
                    buy_share:0,//购买份数
                    bd_share:0,//保底份数
                    infoTitle:'完全公开',
                    infoState: 0, //是否公开
                    gain: '0',
                    declaration: ''
                },

                //跟单数据
                is_gendan : true, //是否可跟单
                gendan:{
                    declaration: ''
                }
            }
        },
        computed:{
            playMap(){
                return this.$store.state.jczq.playMap
            },
            //账户余额
            money(){
                return this.$store.state.userinfo.money
            },
            path(){
                return this.$route.query.type
            },
            //投注选项显示区域宽度
            contentW(){
                return this.$store.state.clientWidth - 60
            },
            //单位
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            title(){
                return this.$route.meta.title
            },
            //所选赛事列表
            tempMatchData(){
                return this.$store.state.jczq.tempMatchData
            },
            //已选场数
            games(){
                return this.$_.size(this.plan)
            },
            //内容高度
            contentH(){
                return this.$store.state.clientHeight - 197
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
            //每场场玩法选项数量
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
            //注数、奖金组计算
            notes(){
                let _arr = []
                let note = 0 //注数
                let maxRex = 0 //最高奖金
                let minArr = []
                let maxArr = []
                for(let mid in this.plan){
                    let n = 0
                    let gainStr = []
                    for(let p in this.plan[mid]){
                        n = n + this.$_.size(this.plan[mid][p]) //单场投注个数组
                        for(let info in this.plan[mid][p]){ //单场投注赔率组
                            gainStr.push(this.plan[mid][p][info])
                        }
                    }
                    _arr.push(n) //单场投注个数组
                    minArr.push(this.$bet.getMaxMin(gainStr,'min')); //单场投注最小赔率
                    maxArr.push(this.$bet.getMaxMin(gainStr,'max')); //单场投注最大赔率
                }
                this.$set(this,'minGainArr',minArr.sort((a, b)=>{return a - b;}))
                for(let t in this.type[0]){
                    //计算注数
                    let notes_res = 0
                    let _selfs = new Array(this.type[0][t]);
                    var _indexs = [0,1,2,3,4,5,6,7,8];
                    var _where = 0;
                    let newArr = [];
                    this.$bet.plzh(_selfs, _arr, _indexs, newArr, _where);
                    for(let m in newArr){
                        let count = 1
                        for(let n in newArr[m]){
                            count = count * newArr[m][n]
                        }
                        notes_res += count
                    }
                    note += notes_res

                    //最大奖金组合
                    let max_gain = 0
                    let _selfs2 = new Array(this.type[0][t]);
                    var _indexs2 = [0,1,2,3,4,5,6,7,8];
                    var _where2 = 0;
                    let maxNewArr = [];
                    this.$bet.plzh(_selfs2, maxArr, _indexs2, maxNewArr, _where2);
                    for(let m in maxNewArr){
                        let count = 1
                        for(let n in maxNewArr[m]){
                            count = this.$bet.accMul(count,maxNewArr[m][n])
                        }
                        max_gain += count
                    }
                    maxRex += max_gain
                }
                this.maxGain = this.$bet.accMul(this.$bet.accMul(maxRex,2),this.multiple,3) //最大奖金计算结果
                return note
            },
            //过关方式 2：2串1 3:3串1...
            type(){
                let arr = [] //提交数据
                let str = '' //显示数据
                for(let i in this.typeOptions){
                    if(this.typeOptions[i].select && Number(i) < this.games){
                        if(this.isAllDgMatch){
                            arr.push(Number(i) + 1)
                            str = str + this.typeOptions[i].label + ','
                        }else {
                            if(i == 0){
                                this.typeOptions[i].select = false
                            }else {
                                arr.push(Number(i) + 1)
                                str = str + this.typeOptions[i].label + ','
                            }
                        }
                    }
                }
                return [arr,str.substring(0, str.lastIndexOf(','))]
            },
            //最小奖金
            minGain(){
                let res = 1
                let len = this.type[0][0]
                for(let i = 0 ; i < len; i++){
                    res = this.$bet.accMul(res,this.minGainArr[i])
                }
                res  = this.$bet.accMul(this.$bet.accMul(res,2),this.multiple,3)
                return this.type[0].length ? Number(res) : 0
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
            //判断是否全为单关投注
            isAllDgMatch(){
                let status = true
                //判断选择的场次中是否有不可投单关的
                if(this.$route.path == '/jczq/bet'){
                    for(let key in this.plan){
                        for(let _key in this.plan[key]){
                            if(this.tempMatchData[key].subactive[this.betMap[_key]] == 0){
                                status = false
                            }
                        }
                    }
                }
                return status
            },
            //总金额
            totalMoney(){
                return this.notes * 2 * this.multiple
            },

            //是否开启佣金
            isGain(){
                return this.$store.state.setting.isGain
            },
            //是否开启合买
            joinOpen(){
                return this.$store.state.setting.join_isOpen == 1 ? true : false
            },
            //每份金额 保留2位去掉
            perMoney(){
                return this.totalMoney ? this.$bet.accDiv(this.totalMoney,this.join.total_share,3) : 0
            },
        },
        methods:{
            //计算金额（每份金额*购买份数，保留2位小数）
            countShareMoney (share) {
                return this.$bet.accMul(this.perMoney,share,3)
            },
            //发起合买
            handleJoin(){
                if(!this.type[0].length){
                    this.$messagebox('提示','请至少选择1种过关方式')
                    return
                }
                if(this.notes < 1){
                    this.$toast('至少选择1注号码才能投注！')
                    return
                }
                if(!this.checked){
                    this.$toast('请仔细阅读并同意相关协议！')
                    return
                }
                this.buyType = 2
                this.joinVisible = true
            },
            //发起自购
            doBuy(){
                this.buyType = 1
                this.submitOrder();
            },
            //合买订单提交
            getJoinEmit(emitVal){
                this.$set(this,'join',emitVal)
                this.submitOrder();
            },
            //重置过关方式
            resetTypeOptions(){
                for(let i in this.typeOptions){
                    this.typeOptions[i].select = false
                }
            },
            //清空投注数据
            clear(){
                this.$store.commit('clearJczqBetData')
                this.resetTypeOptions();
                this.$set(this,'multiple',1)
                this.$set(this,'plan',{})
                this.$set(this,'tempPlan',{})
                this.$set(this,'minGainArr',[])
            },
            //返回大厅
            goHome(){
                if(this.games > 0){
                    this.$messagebox.confirm(
                        '返回大厅将清空所有已选的号码'
                    ).then(()=>{
                        this.$store.commit('delKeepAlivePage','jczq') //清除投注页面缓存
                        this.$store.commit('delKeepAlivePage','jcOrder') //清除当前页面缓存
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
            //返回继续添加比赛
            goBack(){
                this.$store.commit('setJcZqPlan',this.plan)
                this.$router.goBack(-1);//返回上一层
            },
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
                this.$store.commit('setJcZqPlan',this.plan)
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
                this.$store.commit('setJcZqPlan',this.plan)
            },
            //删除所选比赛
            deletMatch(mid){
                this.$delete(this.plan,mid)
                this.$delete(this.tempMatchData,mid)
                this.$store.commit('setJcZqPlan',this.plan)
                this.$store.commit('setJcZqTempMatchData',this.tempMatchData)
                this.resetTypeOptions();//重置过关方式
            },
            //弹出过关方式设置界面
            setTypeShow(){
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
                this.$set(this,'tempTypeOptions',JSON.parse(JSON.stringify(this.typeOptions))) //备份过关方式，用于取消后恢复
                this.setVisible = true
            },
            //选择过关方式
            handleType(i){
                this.typeOptions[i].select = !this.typeOptions[i].select
            },
            //取消设置过关方式
            cancelSetType(){
                this.$set(this,'typeOptions',JSON.parse(JSON.stringify(this.tempTypeOptions))) //还原筛选选项
                this.setVisible = false
            },
            //确认设置过关方式
            sureSetType(){
                this.setVisible = false
            },
            //进入奖金优化
            toOptimalBonus(){
                if(!this.type[0].length){
                    this.$messagebox('提示','请先选择过关方式')
                    return
                }
                if(this.notes > 1000){
                    this.$messagebox('提示','奖金优化最多不能超过1000注')
                    return
                }
                this.$store.commit('setLoadStatus',true);

                let list = {}
                list['type'] = this.type[0]
                list['money'] = this.notes * 2
                sessionStorage.setItem('jczq_gg_type',JSON.stringify(list));
                let setLoad = setTimeout(()=>{
                    this.$router.push({
                        path : '/jczq/optimalBonus'
                    })
                    this.$store.commit('setLoadStatus',false);
                    clearTimeout(setLoad);
                },200)
            },

            //订单信息确认
            submitOrder(){
                if(!this.type[0].length){
                    this.$messagebox('提示','请至少选择1种过关方式')
                    return
                }
                if(this.notes < 1){
                    this.$toast('至少选择1注号码才能投注！')
                    return
                }
                if(!this.checked){
                    this.$toast('请仔细阅读并同意相关协议！')
                    return
                }
                this.$store.commit('setLoadStatus',true)
                this.$axios('/index/moblie/checkLogin').then(({data}) => {
                    this.$store.commit('setBauth', data.status);
                    this.$store.commit('setLoadStatus',false)
                    if(!data.status){
                        this.$router.replace({
                            path:'/login',
                            query:{
                                redirect:this.$router.currentRoute.fullPath
                            }
                        })
                    }else {
                        this.$store.dispatch("getUserInfo"); //更新用户信息
                        this.orderVisible = true
                    }
                });
            },
            //充值
            goPay(){
                this.$router.replace({
                    path:'/pay',
                    query:{
                        redirect:this.$router.currentRoute.fullPath
                    }
                })
            },
            //支付购买
            payment(){
                if(this.buyType==1){
                    if(this.is_gendan){
                        if(!this.gendan.declaration.length){
                            this.$messagebox('提示','请输入跟单宣言！')
                            return
                        }
                    }
                }
                this.loading = true
                let arr =[]
                for(let i in this.type[0]){
                    arr.push(this.type[0][i] + '_1')
                }
                let data = {
                    plan: JSON.stringify(this.plan),
                    multiple: this.multiple,
                    type: this.path,
                    gg_type: arr.join(','),
                    notes : this.notes,
                    total_money: this.totalMoney,
                    is_hemai : this.buyType == 2 ? 1 : 0
                }
                if(this.buyType == 1){
                    data['is_gendan'] = this.is_gendan ? 1 : 0
                    if(this.is_gendan){
                        data['gendan_declaration'] = this.gendan.declaration
                    }
                }
                if(this.buyType == 2){
                    data['total_share'] = this.join.total_share
                    data['buy_share'] = this.join.buy_share
                    data['bd_share'] = this.join.bd_share
                    data['gain'] = this.join.gain
                    data['show'] = this.join.infoState
                    data['declaration'] = this.join.declaration || '这个家伙很懒，只想中大奖！'
                }
                this.$axios.post('/web/ssc/betting',data).then(({data})=>{
                    this.loading = false
                })
            },
            //购买成功后重置
            resetData(){
                this.clear();
                this.buyType = 1
                this.join = {
                    total_share:0,//份数
                    buy_share:0,//购买份数
                    bd_share:0,//保底份数
                    infoTitle:'完全公开',
                    infoState: 0, //是否公开
                    gain: '0',
                    declaration: ''
                }
            },
        },
        created(){
//            setTimeout(()=>{
//                this.setVisible = true
//            },800)
            this.$store.commit('setKeepAlivePage','jcOrder')
        },
        //进入设置页面时缓存页面
        beforeRouteLeave(to, from, next){
            if(to.path == '/jc' && to.path == '/jczq'){
                this.$store.commit('delKeepAlivePage','jcOrder')
            }else {
                this.$store.commit('setKeepAlivePage','jcOrder')
            }
            next();
        }
    }

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .btn-back{
        height: 35px;
        line-height: 35px;
        font-size: 15px;
        background-color: #ffffff;
    }
</style>
