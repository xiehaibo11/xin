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
                                            <span class="itm-team-l"><i class="c-4 f-mini">(客)</i>{{match.awayname}}</span>
                                            <span class="c-4">VS</span>
                                            <span class="itm-team-r">{{match.homename}}<i class="c-4 f-mini">(主)</i></span>
                                        </div>
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
                                                            <table width="100%" class="betbtns">
                                                                <tr>
                                                                    <td class="betbtn-pl" :class="{'active' : curOpt(match.mid,'274','3'),'border-1px-red' : Number(match.subactive.sfdg) && dgIsOpen && path !== 'dg'}" @click="selectDone(match.mid,'274','3',match.sf_pl.lost)">
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
                                                            <table width="100%" class="betbtns">
                                                                <tr>
                                                                    <td class="betbtn-pl" :class="{'active' : curOpt(match.mid,'275','3'),'border-1px-red' : Number(match.subactive.rfsfdg) && dgIsOpen && path !== 'dg'}" @click="selectDone(match.mid,'275','3',match.rfsf_pl.lost)">
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
                <button v-if="joinOpen" @click="handleJoin" class="bet-btn btn-sure" style="font-size: 14px;margin-left: 0px;background-color: #607D8B;padding: 8px 8px;">合买</button>
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
                        <span class="info">竞彩篮球-{{playMap[path]}}</span>
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
        name: 'jclqOrder',
        components:{
            BaseModal,
            inputNumber,
            BetAgreeChecked,
            OrderJoinSet
        },
        data () {
            return {
                buyType:1, //购买方式 1自购 2发起合买

                loading: false,
                dgIsOpen: true, //单关开关
                plan: this.$store.state.jclq.plan, //投注方案内容
                tempPlan: this.$store.state.jclq.plan || {}, //临时投注方案内容，用于取消后恢复
                multiple : 1, //投注倍数
                minGainArr:[], //单场最小赔率组
                maxGain: 0, //最大奖金

                allVisible: false,//全部玩法弹窗
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
                    274 : 'sfdg',
                    275 : 'rfsfdg',
                    276 : 'sfcdg',
                    278 : 'dxfdg'
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
                return this.$store.state.jclq.playMap
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
                return this.$store.state.jclq.tempMatchData
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
            //过关方式1：单关 2：2串1 3:3串1...
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
            //判断是否全为单关投注
            isAllDgMatch(){
                let status = true
                //判断选择的场次中是否有不可投单关的
                if(this.$route.path == '/jclq/bet'){
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
                this.$store.commit('clearJclqBetData')
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
                        this.$store.commit('delKeepAlivePage','jclq') //清除投注页面缓存
                        this.$store.commit('delKeepAlivePage','jclqOrder') //清除当前页面缓存
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
            //返回继续添加比赛
            goBack(){
                this.$store.commit('setJcLqPlan',this.plan)
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
                this.$store.commit('setJcLqPlan',this.plan)
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
                this.$store.commit('setJcLqPlan',this.plan)
            },
            //删除所选比赛
            deletMatch(mid){
                this.$delete(this.plan,mid)
                this.$delete(this.tempMatchData,mid)
                this.$store.commit('setJcLqPlan',this.plan)
                this.$store.commit('setJcLqTempMatchData',this.tempMatchData)
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
                    console.log(data)
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
            this.$store.commit('setKeepAlivePage','jclqOrder')
        },
        //进入设置页面时缓存页面
        beforeRouteLeave(to, from, next){
            if(to.path == '/jc' && to.path == '/jclq'){
                this.$store.commit('delKeepAlivePage','jclqOrder')
            }else {
                this.$store.commit('setKeepAlivePage','jclqOrder')
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
