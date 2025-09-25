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
                                        <div>{{match.ordernum}} {{match.league}}</div>
                                        <div class="flex tr">
                                            <span class="itm-team-l"><i class="c-4 f-mini">(主)</i>{{match.homesxname}}</span>
                                            <span class="c-4">VS</span>
                                            <span class="itm-team-r">{{match.guestsxname}}</span>
                                        </div>
                                    </div>
                                    <!--胜平负-->
                                    <div class="cf zucai-bet-item" style="padding: 0px 0 8px" v-if="path == 'spf'">
                                        <div class="fl bet-win item f-sm" :class="{'active' : curOpt(match.id,'101','3')}" @click="selectDone(match.id,'101','3',match.pl[0])">
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
                                    <!--胜平负 end-->
                                    <!--胜负-->
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
                                    <!--胜负 end-->
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
                                </div>
                                <div class="jc-item-del" @click="deletMatch(match.id)"><i class="iconfont icon-chucuo"></i></div>
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
                    <span class="itm-team-l"><i class="c-4">主</i>{{curData.homesxname}}</span>
                    <span class="itm-vs">VS</span>
                    <span class="itm-team-r">{{curData.guestsxname}}</span>
                </div>
            </template>
            <template slot="main" class="jc-dialog-cont">
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
                <!--<button @click="toOptimalBonus" class="bet-btn btn-sure" style="background-color: #FF9800; padding: 8px 8px; color: #fff;margin-left: 0;font-size: 14px"><i class="iconfont icon-optimize"></i> 奖金优化</button>-->
                <button @click="handleJoin" v-if="joinOpen" class="bet-btn btn-sure" style="font-size: 14px;margin-left: 0px;background-color: #607D8B;padding: 8px 8px;">合买</button>
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
                        <li class="sel-item" v-if="index < games" :class="{'active' : item.select}" @click="handleType(index)">
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
                        <span class="info">北京单场-{{playMap[path]}}</span>
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
        name: 'bjdcOrder',
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
                plan: this.$store.state.bjdc.plan, //投注方案内容
                tempPlan: this.$store.state.bjdc.plan || {}, //临时投注方案内容，用于取消后恢复
                multiple : 1, //投注倍数
                minGainArr:[], //单场最小赔率组
                maxGain: 0, //最大奖金

                allVisible: false,//全部玩法弹窗
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
                return this.$store.state.bjdc.playMap
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
                return this.$store.state.clientWidth - 58
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
                return this.$store.state.bjdc.tempMatchData
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
                        return this.checkStatus(this.tempPlan,this.curData.id,play,sign)
                    }
                }
            },
            //每场场玩法选项数量
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
            //注数、奖金组计算
            notes(){
                let _arr = []
                let note = 0 //注数
                let maxRex = 0 //最高奖金
                let minArr = []
                let maxArr = []
                for(let id in this.plan){
                    let n = 0
                    let gainStr = []
                    for(let p in this.plan[id]){
                        n = n + this.$_.size(this.plan[id][p]) //单场投注个数组
                        for(let info in this.plan[id][p]){ //单场投注赔率组
                            gainStr.push(this.plan[id][p][info])
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
                        arr.push(Number(i) + 1)
                        str = str + this.typeOptions[i].label + ','
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
            }
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
                this.$store.commit('clearBjdcBetData')
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
                        this.$store.commit('delKeepAlivePage','bjdc') //清除投注页面缓存
                        this.$store.commit('delKeepAlivePage','jcOrder') //清除当前页面缓存
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
            //返回继续添加比赛
            goBack(){
                this.$store.commit('setBjdcPlan',this.plan)
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
                this.$store.commit('setBjdcPlan',this.plan)
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
                this.$store.commit('setBjdcPlan',this.plan)
            },
            //删除所选比赛
            deletMatch(id){
                this.$delete(this.plan,id)
                this.$delete(this.tempMatchData,id)
                this.$store.commit('setBjdcPlan',this.plan)
                this.$store.commit('setBjdcTempMatchData',this.tempMatchData)
                this.resetTypeOptions();//重置过关方式
            },
            //弹出过关方式设置界面
            setTypeShow(){
                if(!this.games){
                    this.$toast('请至少选择1场比赛');
                    return
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
                sessionStorage.setItem('bjdc_gg_type',JSON.stringify(list));
                let setLoad = setTimeout(()=>{
                    this.$router.push({
                        path : '/bjdc/optimalBonus'
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
            if(to.path == '/jc' && to.path == '/bjdc'){
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
