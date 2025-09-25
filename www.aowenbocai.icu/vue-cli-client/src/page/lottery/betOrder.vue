<template>
    <div class="lottery-bg" :class="{'lottery-ks' : cz == 'ks'}">
        <mt-header :title="title">
            <mt-button icon="back" slot="left" @click.native="goHome">返回大厅</mt-button>
        </mt-header>
        <ks-top-info @change-time="doChase" v-if="cz == 'ks'"></ks-top-info><!--快3 头部倒计时信息组件-->
        <down-time @change-time="doChase" v-else></down-time><!--头部倒计时信息组件-->
        <div class="fixed-cont">
            <!--操作按钮组-->
            <div class="bet-order-cont">
                <div class="flex-box bd-v" style="padding: 10px 10px">
                    <div>
                        <mt-button @click.native="$router.goBack(-1)" size="small" class="flex-box"><i slot="icon" class="iconfont icon-tianjia f-mini"></i> 继续选号</mt-button>
                    </div>
                    <div class="flex tc">
                        <template v-if="cz=='syxw'"><!--11选5机选-->
                            <mt-button @click.native="syxwRandom(1)" size="small" class="flex-box"><i slot="icon" class="iconfont icon-tianjia f-mini"></i> 机选一注</mt-button>
                        </template>
                        <template v-if="cz=='ssc'"><!--时时彩机选-->
                            <mt-button @click.native="sscRandom(1)" size="small" class="flex-box"><i slot="icon" class="iconfont icon-tianjia f-mini"></i> 机选一注</mt-button>
                        </template>
                        <template v-if="cz=='pk10'"><!--pk10机选-->
                            <mt-button @click.native="pkRandom(1)" size="small" class="flex-box"><i slot="icon" class="iconfont icon-tianjia f-mini"></i> 机选一注</mt-button>
                        </template>
                    </div>
                    <div>
                        <mt-button @click.native="clear" size="small" class="flex-box"><i slot="icon" class="iconfont icon-shanchu f-mini"></i> 清空列表</mt-button>
                    </div>
                </div>
            </div>
            <!--操作按钮组 end-->
        </div>
        <div class="contentH" :style="{height:contentH + 'px'}" ref="wrapper">
            <!--选号列表-->
            <div class="bet-order-cont">
                <div class="num-box">
                    <ul>
                        <template v-if="!betArr.length">
                            <li class="tc f-small c-4">您还未添加任何方案！</li>
                        </template>
                        <template v-else>
                            <li v-for="(item,index) in betArr" :key="index" class="flex-box" :class="{'border-bottom-1px': index !== betArr.length-1}">
                                <span class="del" @click="deleteBet(index)"><i class="iconfont icon-shanchu1"></i></span>
                                <div class="flex">
                                    <!--模式 1-->
                                    <template v-if="betModel == 1">
                                        <p class="num-list">
                                            <em>{{item.num}}</em>
                                        </p>
                                        <p class="f-mini c-3 flex-box">
                                            <span>{{item.type_text}}</span>
                                            <span class="ml" v-if="unitIsOpen">{{item.unit}}</span>  <!--模式1 元角分厘模式-->
                                            <span class="ml" v-if="rebateIsOpen">返{{item.rebate}}%</span>  <!--返点-->
                                            <span class="ml">{{item.notes}}注</span>
                                            <!--模式1 默认模式 投注列表-->
                                            <template v-if="!unitIsOpen">
                                                <span class="ml">{{multiple}}倍</span>
                                                <span class="ml"><em class="red">{{item.money * multiple}}</em>{{lotteryUnit}}</span>
                                                <span class="flex tr" v-if="cz == 'ks' || cz == 'pc28'">可中<em class="red">{{item.gain * 1000000 * multiple / 1000000}}</em>{{lotteryUnit}}</span>
                                            </template>
                                            <!--模式1 元角分厘模式 投注列表-->
                                            <template v-if="unitIsOpen">
                                                <span class="ml">{{item.multiple}}倍</span>
                                                <span class="ml"><em class="red">{{item.money}}</em>{{lotteryUnit}}</span>
                                                <span class="flex tr" v-if="cz == 'ks' || cz == 'pc28'">可中<em class="red">{{item.gain}}</em>{{lotteryUnit}}</span>
                                            </template>
                                        </p>
                                    </template>
                                    <!--模式 2-->
                                    <template v-if="betModel == 2">
                                        <div class="flex-box">
                                            <p style="line-height: 1.2;word-break: break-all; width: 40%;">
                                                {{item.num}}
                                            </p>
                                            <div class="flex tr f-mini">每注 <input-number v-model="item.money" :min="minMoney" size="small"></input-number> {{lotteryUnit}}</div>
                                        </div>
                                        <div class="f-mini c-3 flex-box">
                                            <span>{{item.type_text}}</span>
                                            <span class="ml">{{item.notes}}注</span>
                                            <span class="ml" v-if="rebateIsOpen">返{{item.rebate}}%</span>  <!--返点-->
                                            <span class="flex tr" v-if="cz == 'ks' || cz == 'pc28'">可中<em class="red">{{item.gain * 1000000 * item.money / 1000000}}</em>{{lotteryUnit}}</span>
                                        </div>
                                    </template>
                                </div>
                            </li>
                        </template>
                    </ul>
                    <div class="box-wave"></div>
                </div>
            </div>
            <!--选号列表 end-->
            <!--投注协议-->
            <div class="tc flex-box mf mt-sm" :class="cz == 'ks' ? 'ks-agree' : 'c-3'" style="justify-content: center;font-size: 13px">
                <label class="mint-radiolist-label" style="padding:0px">
                    <span class="mint-radio">
                        <input type="checkbox" class="mint-radio-input" v-model="checked">
                        <span class="mint-radio-core"></span>
                    </span>
                    <span class="mint-radio-label" style="margin-left: 0">我已阅读并同意</span>
                </label>
                <em @click="agreeVisible = true">《用户服务协议》</em><em @click="gameVisible = true">《游戏服务协议》</em>
            </div>
            <!--投注协议 end-->
        </div>
        <!--底部 start-->
        <div class="bet-foot">
            <div class="multiple f-small" :class="{'border-top-1px ' : cz !== 'ks'}">
                <div class="chase-box" v-if="betModel == 1 && !unitIsOpen"><em>投</em> <input-number v-model="multiple" :min="1" :step="1" size="small"></input-number> <em>倍</em></div>
            </div>
            <div class="flex-box notes-box">
                <div class="tc">
                    <button class="bet-btn btn-basket" @click="toJoin(true)" v-if="betModel == 1 && joinOpen && !unitIsOpen || betModel == 2 && joinOpen">合买</button>
                    <button class="bet-btn btn-random" @click="toChase(true)" v-if="betModel == 1">追号</button>
                </div>
                <div class="flex bet-basket" :class="!joinOpen && betModel == 2 ? 'tl' : 'tc'">
                    <p>共 {{totalMoney}} {{lotteryUnit}}</p>
                    <p class="f-mini c-4">自购 {{totalNotes}}注 <em v-if="betModel == 1 && !unitIsOpen">{{multiple}}倍</em></p>
                </div>
                <button class="bet-btn btn-sure" @click="submitOrder">确认提交</button>
            </div>
        </div>
        <!--底部 end-->
        <!--订单信息确认-->
        <mt-popup
            v-model="orderVisible"
            position="bottom">
            <div class="orders-info" v-if="orderVisible">
                <div class="order-title org border-bottom-1px">订单信息确认</div>
                <div class="order-info">
                    <div>
                        <span class="name">投注彩种:</span>
                        <span class="info">{{title}}</span>
                    </div>
                    <div>
                        <span class="name">投注方式:</span>
                        <span class="info" v-if="buyType==1">自购</span>
                        <span class="info" v-if="buyType==2">发起合买</span>
                        <span class="info" v-if="buyType==3">追号</span>
                    </div>
                    <div>
                        <span class="name">投注期号:</span>
                        <span class="info">{{expect}} <em v-if="isChase">（共 {{chase.chaseNum}} 期）</em></span>
                    </div>
                    <div>
                        <span class="name">方案金额:</span>
                        <span class="info">
                            <b class="red f-large" v-if="isChase">{{chaseTotalMoney}}</b>
                            <b class="red f-large" v-else>{{totalMoney}}</b>
                            {{lotteryUnit}}
                        </span>
                    </div>
                    <template  v-if="buyType==2">
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
                    <div v-if="betModel == 1 && !unitIsOpen">
                        <span class="name">投注倍数:</span>
                        <span class="info"> <b class="red">{{multiple}}</b> 倍</span>
                    </div>
                    <div>
                        <span class="name">账户余额:</span>
                        <span class="info"><em>{{money}}</em> {{lotteryUnit}}</span>
                        <span class="tips tc" style="font-size: 14px;color: #333" v-if="money < resultMoney"><b class="red">（余额不足，请充值!）</b></span>
                    </div>
                </div>
                <div class="order-footer tr">
                    <mt-button class="cancel" @click="orderVisible = false" size="small">取消</mt-button>
                    <mt-button type="primary"  size="small" v-if="money < resultMoney" @click="goPay">立即充值</mt-button>
                    <mt-button type="primary" v-else @click="payment" size="small">
                        <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                        确定购买
                    </mt-button>
                </div>
            </div>
        </mt-popup>
        <!--用户服务协议-->
        <mt-popup
            class="agree-poput"
            v-model="agreeVisible"
            position="right">
            <div class="visible-cont">
                <mt-header title="用户服务协议">
                    <mt-button icon="back" slot="left" @click.native="agreeVisible = false">关闭</mt-button>
                </mt-header>
                <div v-html="user_web" class="service-cont"></div>
            </div>
        </mt-popup>
        <!--游戏服务协议-->
        <mt-popup
            class="agree-poput"
            v-model="gameVisible"
            position="right">
            <div class="visible-cont">
                <mt-header title="游戏服务协议">
                    <mt-button icon="back" slot="left" @click.native="gameVisible = false">关闭</mt-button>
                </mt-header>
                <div v-html="web_service" class="service-cont"></div>
            </div>
        </mt-popup>
        <!--合买-->
        <transition name="slide-left">
            <div v-if="joinVisible" class="join-visible" :class="{'ks-join-visible' : cz == 'ks'}">
                <mt-header title="合买详情">
                    <mt-button icon="back" slot="left" @click.native="toJoin(false)">取消合买</mt-button>
                </mt-header>
                <div class="bet-join contentH" :style="{height:joinContentH + 'px'}">
                    <div class="info flex-box border-bottom-1px">
                        <div class="label">方案金额</div>
                        <div class="cont">
                            <em class="red">{{totalMoney}}</em> {{lotteryUnit}}
                            <span class="join-tip">
                                    {{totalNotes}}注 <em v-if="betModel == 1 && !unitIsOpen">{{multiple}}倍</em>
                                </span>
                        </div>
                    </div>
                    <div class="info flex-box border-bottom-1px">
                        <div class="label">我要分成</div>
                        <div class="cont">
                            <input-number v-model="join.total_share" :min="minTotalShare" :max="maxShare" size="small"></input-number> 份
                            <span class="join-tip">每份<em class="red">{{perMoney}}</em>{{lotteryUnit}}</span>
                            <span style="font-size: 10px">(每份至少1{{lotteryUnit}})</span>
                        </div>
                    </div>
                    <div class="info flex-box border-bottom-1px">
                        <div class="label">我要认购</div>
                        <div class="cont">
                            <input-number v-model="join.buy_share" :min="minJoinMoney" :max="join.total_share" size="small"></input-number> 份
                            <span class="join-tip">至少认购<em class="red">5</em>%</span>
                            <span style="font-size: 10px">(已认购<em class="red">{{isbuy}}</em>%)</span>
                        </div>
                    </div>
                    <div class="info flex-box border-bottom-1px">
                        <div class="label">我要保底</div>
                        <div class="cont">
                            <input-number v-model="join.bd_share" :min="0" :max="bdMax" size="small"></input-number> 份
                            <span class="join-tip">最多可保底<em class="red">{{bdMax}}</em>份</span>
                            <span style="font-size: 10px">(已保底<em class="red">{{bdPercent}}</em>%)</span>
                        </div>
                    </div>
                    <div v-if="isGain" class="info flex-box border-bottom-1px">
                        <div class="label">盈利佣金</div>
                        <div class="cont">
                            <span class="select-type border-1px" @click="gainVisible = !gainVisible">{{join.gain}}% <i class="iconfont icon-jiantou c-4 f-mini"></i></span>
                            <span class="join-tip">盈利佣金=奖金*佣金比例</span>
                        </div>
                    </div>
                    <div class="info flex-box border-bottom-1px">
                        <div class="label">保密设置</div>
                        <div class="cont">
                            <span class="select-type border-1px" @click="openVisible = !openVisible">{{join.infoTitle}} <i class="iconfont icon-jiantou c-4 f-mini"></i></span>
                        </div>
                    </div>
                    <div class="info flex-box border-bottom-1px">
                        <div class="label">合买宣言</div>
                        <div class="cont">
                            <input class="input" type="text" placeholder="说点什么吧!" v-model="join.declaration">
                        </div>
                    </div>
                </div>
                <!--底部 start-->
                <div class="bet-foot">
                    <div class="flex-box notes-box">
                        <div class="flex tl bet-basket">
                            <p>共 {{countShareMoney(join.buy_share+join.bd_share)}} {{lotteryUnit}}</p>
                            <p class="f-mini c-4">认购{{countShareMoney(join.buy_share)}}{{lotteryUnit}}+保底{{countShareMoney(join.bd_share)}}{{lotteryUnit}}</p>
                        </div>
                        <button class="bet-btn btn-sure" @click="submitOrder">确认合买</button>
                    </div>
                </div>
                <!--底部 end-->
                <!--盈利佣金-->
                <mt-popup
                    v-model="gainVisible"
                    position="bottom"
                >
                    <mt-picker :slots="slots1" @change="onGainChange"></mt-picker>
                </mt-popup>
                <!--保密设置-->
                <mt-popup
                    v-model="openVisible"
                    position="bottom"
                >
                    <mt-picker :slots="slots" @change="onValuesChange" valueKey="title"></mt-picker>
                </mt-popup>
            </div>
        </transition>
        <!--追号-->
        <transition name="slide-left">
            <div v-if="chaseVisible" class="chase-visible" :class="{'ks-chase-visible' : cz == 'ks'}">
                <div>
                    <mt-header title="追号详情">
                        <mt-button icon="back" slot="left" @click.native="toChase(false)">取消追号</mt-button>
                    </mt-header>
                    <div class="bet-join contentH" :style="{height:joinContentH + 'px'}">
                        <div class="info flex-box border-bottom-1px">
                            <div class="label">追号期数</div>
                            <div class="cont">
                                <input-number v-model="chase.chaseNum" :min="2" :max="maxChase" size="small"></input-number> 期
                                <span class="join-tip">最多可追{{maxChase}}期</span>
                            </div>
                        </div>
                        <div class="border-bottom-1px" style="background-color: #ffffff;">
                            <div class="label f-small c-2 mt-sm mf-sm tl">
                                <div class="tc" style="padding-top: 10px">
                                    期号列表
                                </div>
                            </div>
                            <div class="cont">
                                <table cellpadding="0" cellspacing="0" class="table-list">
                                    <tr>
                                        <th width="10%">序号</th>
                                        <th width="27%">期号</th>
                                        <th width="38%">倍数<em class="c-3" style="font-weight: normal"></em></th>
                                        <th width="25%">金额</th>
                                    </tr>
                                    <tr v-for="(item,index) in chase.chaseData" :key="index">
                                        <td>{{index + 1}}</td>
                                        <td>{{item.expect}}</td>
                                        <td><input-number v-model="item.multiple" :min="1" size="small"></input-number> 倍</td>
                                        <td>{{oneMoney * 1000000 * item.multiple / 1000000}} {{lotteryUnit}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--底部 start-->
                    <div class="bet-foot">
                        <div class="multiple f-small" :class="{'border-top-1px ' : cz !== 'ks'}">
                            <div class="tr f-sm c-2 chase-box">
                                <div class="flex-box" style="padding:0 10px">
                                    <div>
                                        <em>投</em> <input-number v-model="multiple" :min="1" :step="1" size="small"></input-number> <em>倍</em>
                                    </div>
                                    <div class="flex-box" style="justify-content: flex-end;">
                                        <label class="mint-radiolist-label" style="padding-right: 5px">
                                        <span class="mint-radio">
                                            <input type="checkbox" class="mint-radio-input" v-model="chase.isStop">
                                            <span class="mint-radio-core"></span>
                                        </span>
                                            <em class="mint-radio-label" style="margin-left: 0">中奖后停止追号</em>
                                        </label>
                                        <i @click="showStop" class="iconfont icon-yiwen1 f-sm c-3"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-box notes-box">
                            <div class="flex tl bet-basket">
                                <p>共 {{chaseTotalMoney}} {{lotteryUnit}}</p>
                                <p class="f-mini c-4">追{{chase.chaseNum}}期</p>
                            </div>
                            <button class="bet-btn btn-sure" @click="submitOrder">追号提交</button>
                        </div>
                    </div>
                    <!--底部 end-->
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
    import DownTime from 'components/lottery/DownTime.vue' //顶部期号+ 倒计时组件
    import inputNumber from 'components/common/InputNumber.vue' //购买输入框组件
    import KsTopInfo from 'components/lottery/Ks/KsTopInfo.vue' //快3顶部期号 + 倒计时
    export default {
        name:'betOrder',
        components:{
            DownTime,
            KsTopInfo,
            inputNumber
        },
        data () {
            return {
                slideName:'',
                loading:false,
                multiple: 1, //投注倍数
                checked: true, //投注协议
                orderVisible:false, //订单详情
                openVisible:false,//保密设置
                gainVisible:false,//保密设置
                agreeVisible:false ,
                gameVisible:false ,
                buyType: "1", //购买方式 1 自购 2 合买
                expectArr: [], //期号、倍数

                joinVisible:false,//合买
                chaseVisible:false,//追号

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
                //追号数据
                chase:{
                    chaseNum: 4, //默认追号期数
                    chaseMul: 1, //追号倍数设置
                    isStop: false, //中奖是否停止追号
                    chaseData: [], //追号数据内容
                },

                slots: [
                    {
                        flex: 1,
                        values: [
                            {title:"完全公开",value:0},
                            {title:"截止后公开",value:1},
                            {title:"仅跟单人可见",value:2},
                            {title:"完全保密",value:3}
                        ],
                        className: 'slot1',
                        textAlign: 'center'
                    }
                ],
                slots1: [
                    {
                        flex: 1,
                        values: ['0','1','2','3','4','5','6','7','8','9','10'],
                        className: 'slot1',
                        textAlign: 'center'
                    }
                ]
            }
        },
        computed:{
            //返点设置是否开启
            rebateIsOpen(){
                return this.$store.state.setting.rebate_isOpen == 1 ? true : false
            },
            //用户返点为零时不显示
            isRebate(){
                return this.$store.state.lottery.userRebate == 0 ? false : true
            },
            //是否开启合买
            joinOpen(){
                return this.$store.state.setting.join_isOpen == 1 ? true : false
            },
            //模式1下 元角分模式是否开启
            unitIsOpen(){
                return this.$store.state.setting.unit_isOpen == 1 ? true : false
            },
            //模式2下每注的最低金额
            minMoney(){
                return Number(this.$store.state.setting.mode2_min_money)
            },
            betModel(){
                return this.$route.query.betModel
            },
            //单位
            label(){
                return this.$store.state.lottery.label
            },
            //单位值
            value(){
                return this.$store.state.lottery.value
            },
            //比例
            scale(){
                return this.$store.getters.getScale
            },
            //倍数
            multipleC(){
                return this.$store.state.lottery.multiple
            },
            //所选返点值
            rebateVal(){
                return this.$store.getters.rebateVal
            },
            //当前用户最高奖金返点百分比
            percent(){
                return this.$store.getters.maxRebate
            },
            //用户服务协议
            user_web(){
                return this.$store.state.setting.user_service
            },
            //游戏服务协议
            web_service(){
                return this.$store.state.setting.web_service
            },
            //单位
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            //账户余额
            money(){
                return this.$store.state.userinfo.money
            },
            //是否开启佣金
            isGain(){
                return this.$store.state.setting.isGain
            },
            title(){
                return this.$route.query.title
            },
            name(){
                return this.$route.query.name
            },
            type(){
                return this.$route.query.type
            },
            cz:{
                get(){
                    return this.$route.query.cz
                },
                set(val){

                }
            },
            //投注文字显示处理
            text (){
                if(this.cz == 'syxw'){
                    const valObj = {
                        '13.1': '任一复式',
                        '13.3': '任一单式',
                        '1.1': '任二复式',
                        '1.2': '任二胆拖',
                        '1.3': '任二单式',
                        '2.1': '任三复式',
                        '2.2': '任三胆拖',
                        '2.3': '任三单式',
                        '3.1': '任四复式',
                        '3.2': '任四胆拖',
                        '3.3': '任四单式',
                        '4.1': '任五复式',
                        '4.2': '任五胆拖',
                        '4.3': '任五单式',
                        '5.1': '任六复式',
                        '5.2': '任六胆拖',
                        '5.3': '任六单式',
                        '6.1': '任七复式',
                        '6.2': '任七胆拖',
                        '6.3': '任七单式',
                        '7.1': '任八复式',
                        '7.2': '任八胆拖',
                        '7.3': '任八单式',
                        '8.1': '前一复式',
                        '8.3': '前一单式',
                        '9.1': '前二组选复式',
                        '9.2': '前二组选胆拖',
                        '9.3': '前二组选单式',
                        '10.1': '前二直选复式',
                        '10.3': '前二直选复式',
                        '11.1': '前三组选复式',
                        '11.2': '前三组选胆拖',
                        '11.3': '前三组选单式',
                        '12.1': '前三直选复式',
                        '12.3': '前三直选复式'
                    }
                    return valObj[this.type]
                }
                if(this.cz == 'ssc'){
                    const valObj = {
                        '1.1': '五星通选复式',
                        '1.2': '五星直选复式',
                        '1.3': '五星通选单式',
                        '1.4': '五星直选单式',
                        '2.1': '前三直选复式',
                        '2.2': '前三直选和值',
                        '2.3': '前三组三复式',
                        '2.4': '前三组三胆拖',
                        '2.5': '前三组三和值',
                        '2.6': '前三组六复式',
                        '2.7': '前三组六胆拖',
                        '2.8': '前三组六和值',
                        '2.11': '前三直选单式',
                        '2.12': '前三组三单式',
                        '2.13': '前三组六单式',
                        '3.1': '中三直选复式',
                        '3.2': '中三直选和值',
                        '3.3': '中三组三复式',
                        '3.4': '中三组三胆拖',
                        '3.5': '中三组三和值',
                        '3.6': '中三组六复式',
                        '3.7': '中三组六胆拖',
                        '3.8': '中三组六和值',
                        '3.11': '中三直选单式',
                        '3.12': '中三组三单式',
                        '3.13': '中三组六单式',
                        '4.1': '后三直选复式',
                        '4.2': '后三直选和值',
                        '4.3': '后三组三复式',
                        '4.4': '后三组三胆拖',
                        '4.5': '后三组三和值',
                        '4.6': '后三组六复式',
                        '4.7': '后三组六胆拖',
                        '4.8': '后三组六和值',
                        '4.11': '后三直选单式',
                        '4.12': '后三组三单式',
                        '4.13': '后三组六单式',
                        '5.1': '后二直选复式',
                        '5.2': '后二直选和值',
                        '5.3': '后二组选复式',
                        '5.4': '后二组选和值',
                        '5.5': '后二直选单式',
                        '5.6': '后二直选单式',
                        '10.1': '前二直选复式',
                        '10.2': '前二直选和值',
                        '10.3': '前二组选复式',
                        '10.4': '前二组选和值',
                        '10.5': '前二直选单式',
                        '10.6': '前二直选单式',
                        '6.1': '一星复式',
                        '6.2': '一星单式',
                        '7.1': '大小单双',
                        '8.1': '定位胆',
                        '9.1': '龙虎万千',
                        '9.2': '龙虎万百',
                        '9.3': '龙虎万十',
                        '9.4': '龙虎万个',
                        '9.5': '龙虎千百',
                        '9.6': '龙虎千十',
                        '9.7': '龙虎千个',
                        '9.8': '龙虎百十',
                        '9.9': '龙虎百个',
                        '9.11': '龙虎十个'
                    }
                    return valObj[this.type]
                }
                if(this.cz == 'pk10'){
                    const typeObj = {
                        1 : '猜冠军',
                        2 : '猜亚军',
                        3 : '猜季军',
                        4 : '猜前四',
                        5 : '猜前五',
                        6 : '猜前六',
                        7 : '猜前七',
                        8 : '猜前八',
                        9 : '猜前九',
                        10 : '猜前十',
                        11 : '定位胆',
                        13 : '龙虎斗'
                    }
                    if(this.type == 12.1){
                        return '前五大小单双';
                    }else if(this.type == 12.2){
                        return '后五大小单双';
                    }else if(this.type == 12.3){
                        return '冠亚和大小单双';
                    }else{
                        return typeObj[this.type]
                    }
                }
                if(this.cz == 'ks'){
                    const typeObj = {
                        1 : '和值',
                        2 : '三同号通选',
                        3 : '三同号单选',
                        4 : '三不同号',
                        5 : '三连号通选',
                        6 : '二同号复选',
                        7 : '二同号单选',
                        8 : '二不同号',
                    }
                    return typeObj[this.type]
                }
            },
            //当前期号
            expect(){
                return this.$store.state.lottery.info.expect
            },
            //当前期号
            sort_expect(){
                return this.$store.state.lottery.info.sort_expect
            },
            //每天总期数
            totalIssue(){
                return this.$store.state.lottery.info.totalIssue
            },
            todayTime(){
                return this.$store.state.lottery.info.todayTime
            },
            //期号类型
            expectType(){
                return this.$store.state.lottery.expect_type
            },
            //第一期
            firstIssue(){
                return this.$store.state.lottery.info.firstIssue
            },
            //已售期数
            issueAfter() {
                return this.expect - this.firstIssue;
            },
            //选号列表
            betArr(){
                return this.$store.state.lottery.betArr
            },
            //总注数
            totalNotes(){
                return this.$store.getters.getTotalNotes
            },
            //需要支付的总金额
            totalMoney(){
                var count = 0
                for(let i in this.betArr){
                    var a = 0
                    if(this.betModel == 1){
                        a = this.betArr[i].money
                    }else {
                        a = this.$bet.accMul(Number(this.betArr[i].money),Number(this.betArr[i].notes))
                    }
                    count = this.$bet.accAdd(Number(count),Number(a));
                }
                return count * this.multiple
            },
            //1倍投注金额（追号时使用）
            oneMoney(){
                var count = 0
                if(this.betModel == 1){
                    if(this.unitIsOpen){
                        for(let i in this.betArr){
                            var a = this.$bet.accAdd(count,this.$bet.accDiv(Number(this.betArr[i].money),Number(this.betArr[i].multiple)))
                            count = a;
                        }
                    }else {
                        count = this.$bet.accDiv(Number(this.totalMoney),Number(this.multiple))
                    }
                }
                return count
            },
            maxShare(){
                return Number(this.totalMoney)
            },
            //最小总份数
            minTotalShare () {
                return this.totalMoney ? 1 : 0
            },
            //每份金额 保留2位去掉
            perMoney(){
                return this.totalMoney ? this.$bet.accDiv(this.totalMoney,this.join.total_share,3) : 0
            },
            //最少购买份数 向上取整
            minJoinMoney() {
                return Number(Math.ceil(this.join.total_share * 0.05));
            },
            //已购买百分比 取整
            isbuy() {
                return !this.totalMoney ? 0 : Math.floor(this.join.buy_share / this.join.total_share * 10000) / 100
            },
            //最多可保底
            bdMax() {
                return this.join.total_share - this.join.buy_share;
            },
            //已保底百分比 取整
            bdPercent() {
                return !this.totalMoney ? 0 :  Math.floor(this.join.bd_share / this.join.total_share * 10000) / 100
            },
            //是否追号
            isChase(){
                return this.buyType == 3 ? true :false
            },
            //追号总金额
            chaseTotalMoney(){
                let m =0
                if(this.chase.chaseData.length){
                    for(let i in this.chase.chaseData){
                        m = this.$bet.accAdd(m,this.$bet.accMul(this.chase.chaseData[i].multiple ,this.oneMoney))
                    }
                }
                return m
            },
            //选号容器高度计算
            contentH(){
                let h = this.betModel == 1 && !this.unitIsOpen ? 91 : 52 //底部高度
                let a = this.cz == 'ks' ? 151 : 148
                return this.$store.state.clientHeight - a - h
            },
            //追号、合买容器高度
            joinContentH(){
                let h = this.buyType == 3 ? 130 : 92
                return this.$store.state.clientHeight - h
            },
            resultMoney(){
                if(this.buyType == 1){
                    return this.totalMoney
                }
                if(this.buyType == 2){
                    return this.countShareMoney(this.join.buy_share + this.join.bd_share)
                }
                if(this.buyType == 3){
                    return this.chaseTotalMoney
                }
            },
            maxChase(){
                let total = parseInt(this.totalIssue); //每天售卖期数
                let max_chase = this.$store.state.setting.max_chase //最大追号期数
                return max_chase ? max_chase > total ? total : max_chase : total //控制最多只能追第二天的期号
            }
        },
        watch:{
            //监听购买总金额
            totalMoney (val) {
                this.join.total_share = val
            },
            //监听总份数
            'join.total_share' (val) {
                this.join.buy_share = Math.ceil(val * 0.05);
                if (this.join.bd_share + this.join.buy_share > val) {
                    this.join.bd_share = this.bdMax;
                }
            },
            //监听认购份数
            'join.buy_share' (val) {
                if (val + this.join.bd_share > this.join.total_share) {
                    this.join.bd_share = this.bdMax;
                }
            },
            //监听追号期数
            'chase.chaseNum'(val){
                this.doChase();
            },
            buyType(val){
                if(val == 3){
                    this.doChase();
                }
            },
            multiple(val){
                this.chase.chaseMul = val;
                this.doChase();
            }
        },
        methods:{
            //合买
            toJoin(s){
                if(s){
                    this.joinVisible = true
                    this.buyType = 2
                    this.join.total_share = this.totalMoney
                }else {
                    this.joinVisible = false
                    this.buyType = 1
                }
            },
            //追号
            toChase(s){
                if(s){
                    this.chaseVisible = true
                    this.buyType = 3
                }else {
                    this.chaseVisible = false
                    this.buyType = 1
                }
            },
            //重置页面
            resetData(){
                this.$store.commit('clearBetNum'); //清除投注数据
                sessionStorage.removeItem('betinfo'); //清除sessionStorage投注数据
                this.buyType = '1';
                this.multiple = 1;
                this.expectArr =[];
                this.joinVisible = false
                this.chaseVisible = false
                this.join = {
                    infoTitle:'完全公开',
                    infoState: 0, //是否公开
                    total_share: 0, //总份数
                    buy_share: 0, //购买
                    bd_share: 0, //保底
                    gain: '0',
                    declaration: ''
                }
                this.chase = {
                    chaseNum: 4, //默认追号期数
                    chaseMul: 1, //追号倍数设置
                    isStop: false, //中奖是否停止追号
                    chaseData: [], //追号数据内容
                }
            },
            //返回大厅
            goHome(){
                this.$router.isBack = true
                this.$router.push({
                    path:'/game'
                })
            },
            //删除选号
            deleteBet(i){
                this.$store.commit('deleteBetArr',i)
            },
            //机选添加选号
            randomAddNum(betArr){
                let data = {}
                data['num'] = betArr[0]
                data['notes'] = betArr[1]
                data['type_text'] = betArr[3]
                data['type'] = betArr[2] //投注type
                if(this.rebateIsOpen && this.isRebate){
                    data['rebate'] = this.rebateVal
                }
                if(this.betModel == 1){ //模式1
                    if(this.unitIsOpen){ //元角分模式
                        data['multiple'] = this.multipleC//投注倍数
                        data['unit'] = this.label//投注单位
                        data['unit_value'] = this.value //投注单位值
                        data['money'] = this.$bet.accMul(this.$bet.accDiv(2,Number(this.scale)),Number(this.multipleC))  * betArr[1] //投注金额,
                    }else { //默认模式
                        data['multiple'] = this.multiple //投注倍数
                        data['money'] = 2  * betArr[1] //投注金额
                    }
                }
                if(this.betModel == 2){ //模式2
                    data['money'] = this.minMoney //投注金额
                }
                this.$store.commit('pushBetNum',data)
            },
            //11选5机选 @param {int} n  机选几注
            syxwRandom(n) {
                this.$refs.wrapper.scrollTop = 0;
                var items = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11'];
                for (var i = 0; i < n; i++) {
                    var num;
                    if (parseInt(this.type) < 8 || parseInt(this.type) == 13) {
                        //任选 任选2/3/4/5/6/7/8 普通投注
                        let m = parseInt(this.type) == 13  ? 1 : parseInt(this.type) + 1
                        num = this.$bet.getRandomArrayEle(items, m).sort().join(",");
                    } else if (parseInt(this.type) == 8) {
                        //前一 普通投注
                        num = this.$bet.getRandomArrayEle(items, 1).join(",");
                    } else if (parseInt(this.type) == 9) {
                        //前二组选 普通投注
                        num = this.$bet.getRandomArrayEle(items, 2).sort().join(",");
                    } else if (parseInt(this.type) == 11) {
                        //前三组选 普通投注
                        num = this.$bet.getRandomArrayEle(items, 3).sort().join(",");
                    } else if (parseInt(this.type) == 10) {
                        //前二直选
                        num = this.$bet.getRandomArrayEle(items, 2).sort().join("|");
                    } else if (parseInt(this.type) == 12) {
                        //前三直选
                        num = this.$bet.getRandomArrayEle(items, 3).sort().join("|");
                    }
                    let type_text =  this.text;
                    if(this.type.split('.')[1] == 2){
                        num = num.replace(',','#')
                    }
                    this.randomAddNum([num,1,this.type,this.text])
                }
            },
            //时时彩机选 @param {int} n  机选几注
            sscRandom(n){
                this.$refs.wrapper.scrollTop = 0;
                var items = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
                for (var p = 0; p < n; p++) {
                    var num = ''; //选号
                    var notes = 0; //注数
                    //五星通选、直选
                    if (parseInt(this.type) == 1) {
                        var wxcode = '';
                        for (var j = 0; j < 5; j++) {
                            var code = this.$bet.getRandomArrayEle(items, 1);
                            wxcode = wxcode + '|' + code;
                        }
                        notes = 1;
                        num = wxcode.substr(1);
                    }
                    //前三、中三、后三直选 复式/单式
                    if (this.type == 2.1 || this.type == 3.1 || this.type == 4.1 || this.type == 2.11 || this.type == 3.11 || this.type == 4.11) {
                        var wxcode = '';
                        for (var j = 0; j < 3; j++) {
                            var code = this.$bet.getRandomArrayEle(items, 1);
                            wxcode = wxcode + '|' + code;
                        }
                        notes = 1;
                        num = wxcode.substr(1);
                    }
                    //前三、中三、后三直选 和值
                    if(this.type == 2.2 || this.type == 3.2 || this.type == 4.2){
                        //和值
                        var hz = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27];
                        words = '和值';
                        num = this.$bet.getRandomArrayEle(hz, 1).join(",");
                        var plusVal = 0;
                        for (var i = 0; i <= 9; i++) {
                            for (var j = 0; j <= 9; j++) {
                                for (var k = 0; k <= 9; k++) {
                                    var hz = i + j + k;
                                    if (hz == num) {
                                        plusVal += 1;
                                    }
                                }
                            }
                        }
                        notes = plusVal;
                    }
                    //前三、中三、后三组三 复式、胆拖
                    if(this.type == 2.3 || this.type == 3.3 || this.type == 4.3 || this.type == 2.4 || this.type == 3.4 || this.type == 4.4 ||
                        this.type == 2.12 || this.type == 3.12 || this.type == 4.12){
                        this.$messagebox('提示','对不起"组三"不支持机选！')
                        return
                    }
                    //前三、中三、后三组三 和值
                    if(this.type == 2.5 || this.type == 3.5 || this.type == 4.5){
                        //和值
                        var hz = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26];
                        num = this.$bet.getRandomArrayEle(hz, 1).join(",");
                        var plusVal = 0;
                        for (var i = 0; i <= 9; i++) {
                            for (var j = 0; j <= 9; j++) {
                                if (i !== j) {
                                    var hz = i + i + j;
                                    if (hz == num) {
                                        plusVal += 1;
                                    }
                                }
                            }
                        }
                        notes = plusVal;
                    }
                    //前三、中三、后三组六 复式、胆拖/单式
                    if (this.type == 2.6 || this.type == 3.6 || this.type == 4.6 || this.type == 2.7 || this.type == 3.7 ||
                        this.type == 4.7|| this.type == 2.13 || this.type == 3.13 || this.type == 4.13) {
                        notes = 1;
                        num = this.$bet.getRandomArrayEle(items, 3).sort().join(",");
                    }
                    //前三、中三、后三组六 和值
                    if(this.type == 2.8 || this.type == 3.8 || this.type == 4.8){
                        var hz = [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24];
                        num = this.$bet.getRandomArrayEle(hz, 1).join(",");
                        var plusVal = 0;
                        for (var i = 0; i <= 9; i++) {
                            for (var j = i; j <= 9; j++) {
                                for (var k = j; k <= 9; k++) {
                                    if (i != j && j != k && i != k) {
                                        var hz = i + j + k;
                                        if (hz == num) {
                                            plusVal += 1;
                                        }
                                    }
                                }
                            }
                        }
                        notes = plusVal;
                    }
                    //胆拖替换
                    if(this.type == 2.5 || this.type == 2.7 || this.type == 3.5 || this.type == 3.7 || this.type == 4.5 || this.type == 4.7){
                        num = num.replace(',','#')
                    }
                    //二星直选、复式/单式
                    if (this.type == 5.1 || this.type == 5.5 || this.type == 10.1 || this.type == 10.5) {
                        var wxcode = '';
                        for (var j = 0; j < 2; j++) {
                            var code = this.$bet.getRandomArrayEle(items, 1);
                            wxcode = wxcode + '|' + code;
                        }
                        notes = 1;
                        num = wxcode.substr(1);
                    }
                    //二星直选 和值
                    if(this.type == 5.2 || this.type == 10.2){
                        var hz = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];
                        num = this.$bet.getRandomArrayEle(hz, 1).join(",");
                        var plusVal = 0;
                        for (var i = 0; i <= 9; i++) {
                            for (var j = 0; j <= 9; j++) {
                                var hz = i + j;
                                if (hz == num) {
                                    plusVal += 1;
                                }
                            }
                        }
                        notes = plusVal;
                    }
                    //二星组选 复式/单式
                    if (this.type == 5.3 || this.type == 5.6 || this.type == 10.3 || this.type == 10.6) {
                        notes = 1;
                        num = this.$bet.getRandomArrayEle(items, 2).sort().join(",");
                    }
                    //二星组选 和值
                    if(this.type == 5.4 || this.type == 10.4){
                        var hz = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];
                        num = this.$bet.getRandomArrayEle(hz, 1).join(",");
                        var plusVal = 0;
                        for (var i = 0; i <= 9; i++) {
                            for (var j = i; j <= 9; j++) {
                                var hz = i + j;
                                if (hz == num) {
                                    plusVal += 1;
                                }
                            }
                        }
                        notes = plusVal;
                    }
                    //一星
                    if (parseInt(this.type) == 6) {
                        notes = 1;
                        num = this.$bet.getRandomArrayEle(items, 1).join(',');
                    }
                    //大小单双
                    if (this.type == 7.1) {
                        var arr = ['大', '小', '单', '双'];
                        notes = 1;
                        var gw = this.$bet.getRandomArrayEle(arr, 1).join(",");
                        var sw = this.$bet.getRandomArrayEle(arr, 1).join(",");
                        num = sw + '|' + gw;
                    }
                    //定位胆
                    if (this.type == 8.1) {
                        //定位胆
                        var arr = [1,2,3,4,5];
                        notes = 1;
                        var w = this.$bet.getRandomArrayEle(arr, 1).join(',');
                        var b = this.$bet.getRandomArrayEle(items, 1).join(',');
                        var s = ''
                        for(var j=0 ; j<5 ;j++){
                            if((j+1) == w){
                                s = s + b + '|'
                            }else {
                                s = s + '-|';
                            }
                        }
                        num = s.slice(0,s.length-1);
                    }
                    //龙虎
                    if (parseInt(this.type) == 9) {
                        var arr = ['龙', '虎', '和', '大','小','单','双'];
                        notes = 1;
                        num = this.$bet.getRandomArrayEle(arr, 1).join(',');
                    }
                    this.randomAddNum([num,notes,this.type,this.text])
                }
            },
            //pk10机选 @param {int} n  机选几注
            pkRandom(n){
                this.$refs.wrapper.scrollTop = 0;
                for (var i = 0; i < n; i++) {
                    var num;
                    if(parseInt(this.type) == 12){
                        //大小单双
                        var arr = ['大', '小', '单', '双'];
                        if(this.type == 12.1 || this.type == 12.2){
                            var wz = [1,2,3,4,5]//投注位置选项
                            var w = this.$bet.getRandomArrayEle(wz, 1).join(",");
                            var b = this.$bet.getRandomArrayEle(arr, 1).join(",");
                            var s = ''
                            for(var j=0 ; j<5 ;j++){
                                if((j+1) == w){
                                    s = s + b + '|'
                                }else {
                                    s = s + '-|';
                                }
                            }
                            num = s.slice(0,s.length-1);
                        }else {
                            num = this.$bet.getRandomArrayEle(arr, 1).join(",");
                        }
                    }else if(this.type == 13){
                        //龙虎
                        var wz = [1,2,3,4,5]//投注位置选项
                        var arr = ['龙', '虎'];
                        var w = this.$bet.getRandomArrayEle(wz, 1).join(",");
                        var b = this.$bet.getRandomArrayEle(arr, 1).join(",");
                        var s = ''
                        for(var j=0 ; j<5 ;j++){
                            if((j+1) == w){
                                s = s + b + '|'
                            }else {
                                s = s + '-|';
                            }
                        }
                        num = s.slice(0,s.length-1);
                    }else {
                        var items = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10'];
                        num = this.$bet.getRandomArrayEle(items, this.type).join("|");
                    }

                    let sign = this.$route.query.sign //投注sign
                    let text = this.text
                    if(this.type < 11){
                        if(sign == 'YJ.2' || sign == 'JJ.2' || sign == 'QS.2'){
                            text = text + '精准'
                        }else {
                            text = text + '普通'
                        }
                    }
                    this.randomAddNum([num,1,sign,text])
                }
            },
            //清空选号列表
            clear(){
                this.$store.commit('clearBetNum')
            },
            //追号
            doChase() {
                if(this.isChase){
                    var arr = [];
                    //投注第二天
                    var nextDayVal = this.$bet.getDateStr(this.todayTime, 1); //计算第二天的日期
                    var total = parseInt(this.totalIssue);
                    var curExpect = parseInt(this.expect);

                    var string = ''
                    for(var i = 0 ; i< this.totalIssue.toString().length ; i++){
                        string +='0'
                    }
                    var nextVal = parseInt(nextDayVal + string);
                    var init = parseInt(this.issueAfter) + 1;

                    for (let i = 0; i < this.chase.chaseNum; i++) {
                        var chaseList = {};
                        if(this.expectType){ //累加型
                            //投注当天
                            chaseList['expect'] = curExpect + i;
                        }else {
                            if (i > (total - init)) {
                                //投注第二天（最多）
                                nextVal += 1;
                                chaseList['expect'] = nextVal;
                            } else {
                                //投注当天
                                chaseList['expect'] = curExpect + i;
                            }
                        }
                        chaseList['multiple'] = parseInt(this.chase.chaseMul);
                        arr.push(chaseList);
                    }
                    this.$set(this.chase, 'chaseData', arr);
                }
            },
            //订单信息确认
            submitOrder(){
                if(this.totalNotes < 1){
                    this.$toast('至少选择1注号码才能投注，请先选择方案！')
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
                this.loading = true
                let stop = this.chase.isStop ? 1: 0
                let isJoin = this.buyType == 2 ? 1: 0
                //处理post期号、倍数数组
                if (!this.isChase) {
                    //未追号
                    var arr = [];
                    var expect = {};
                    expect['expect'] = this.expect;
                    expect['multiple'] = this.multiple;
                    arr.push(expect);
                    this.$set(this, 'expectArr', arr);
                }else {
                    this.$set(this, 'expectArr', this.chase.chaseData);
                }
                let data = {
                    play_num: JSON.stringify(this.betArr),
                    expect: JSON.stringify(this.expectArr),
                    lottery_name: this.name,
                    is_hemai: isJoin,
                    is_stop: stop
                }
                if(isJoin){
                    data['total_share'] = this.join.total_share
                    data['buy_share'] = this.join.buy_share
                    data['bd_share'] = this.join.bd_share
                    data['gain'] = this.join.gain
                    data['show'] = this.join.infoState
                    data['declaration'] = this.join.declaration || '这个家伙很懒，只想中大奖！'
                }
                this.$axios.post('/web/' + this.cz + '/betting',data).then(({data}) =>{
                    if(!data.err){
                        this.$messagebox({
                            title: '提示',
                            message: data.msg,
                            showCancelButton: false
                        });
                        this.resetData();
                    }else {
                        setTimeout(()=>{
                            this.$messagebox({
                                title: '提示',
                                message: data.msg
                            });
                        },100)
                    }
                    this.orderVisible = false
                    this.loading = false
                }).catch((error)=> {
                    console.log(error.response.status);
                    this.$messagebox({
                        showClose: true,
                        message: '内部服务错误，请稍后再试',
                        type: "error"
                    });
                })
            },
            //合买设置
            //保密设置
            onValuesChange(picker, values) {
                this.join.infoState = values[0].value
                this.join.infoTitle = values[0].title
            },
            //盈利佣金
            onGainChange(picker, values) {
                this.join.gain = values[0]
            },
            //计算金额（每份金额*购买份数，保留2位小数）
            countShareMoney (share) {
                return this.$bet.accMul(this.perMoney,share,3)
            },
            //中奖停止解释
            showStop(){
                this.$messagebox({
                    title: '提示',
                    message: '勾选后，您的追号方案中的某一期中奖后，后续的追号订单将被撤销，资金返还您的账户中。如不勾选，系统一直帮您购买所有的追号投注任务。',
                    confirmButtonText:'我知道了'
                });
            }
        },
        create(){
            this.$store.commit('setKeepAlivePage','betOrder')
        },
        //返回首页时
        beforeRouteLeave(to, from, next){
            if(to.path =='/game' || to.path == '/lotteryMore'){
                this.$store.commit('delKeepAlivePage','betOrder')
                this.$store.commit('delKeepAlivePage','ssc')
                this.$store.commit('delKeepAlivePage','syxw')
                this.$store.commit('delKeepAlivePage','pk10')
                this.$store.commit('delKeepAlivePage','ks')
                this.$store.commit('delKeepAlivePage','pc28')
                this.$store.commit('clearDownTime'); //清除倒计时
                this.$store.commit('clearBetData'); //清除投注页面初始数据
                this.$store.commit('clearBetNum'); //清除投注数据
                sessionStorage.removeItem('betinfo'); //清除sessionStorage投注数据
                this.$store.commit('clearNewCode')  //清除获取开奖号码定时器
            }else{
                this.$store.commit('setKeepAlivePage','betOrder')
            }
            this.$store.commit('clearNewCode')  //清除获取开奖号码定时器
            this.$store.commit('clearRandomNum')  //清除开奖动画
            next();
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    body,html{
        background-color: #f9f8f1;
    }

    .join-visible ,.chase-visible{
        position: absolute;
        top:0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #ffffff;
        z-index: 1;
    }
    .ks-join-visible,.ks-chase-visible{
        background-color: #3f7555;
        .bet-join{
            padding: 10px;
            .info{
                background-color: #ffffff;
            }
        }
    }

    .loading{
        width: 28px;
        height: 28px;
        margin: 30px auto;
    }
    .fixed-cont{
        /*position: fixed;*/
        /*top: 64px;*/
        width: 100%;
        background-color: #f9f8f1;
        /*z-index: 50;*/
    }
    .top-info{
        background-color: #ffffff;
    }
    .bet-order-cont{
        .mint-button--small{
            height: 28px;
            background-color: #fff;
            margin: 0 auto;
        }
        .num-box{
            margin:0 10px;
            transition: all .2s;
            .num-list{
                word-break: break-all;
                line-height: 1.4;
            }
            ul{
                background-color: #ffffff;
                li{
                    padding: 8px 10px;
                    line-height: 1.8;
                    .del{
                        padding-right: 10px;
                    }
                }
            }
            .ml{
                margin-left: 5px;
            }
        }
        .box-wave{
            height: 4px;
            background: url(~assets/images/wave.png) repeat-x left top;
        }
    }
    .buy-type{
        margin:10px;
        .mint-navbar .mint-tab-item{
            padding: 10px 0;
        }
        .mint-navbar .mint-tab-item.is-selected{
            margin-bottom: 1px;
        }
    }
    .buy-type-cont{
        background-color: #ffffff;
    }
    .bet-foot{
        .multiple{
            text-align: center;
            background-color: #ffffff;
            .chase-box{
                padding: 5px 0;
            }
        }
    }
    //合买
    .bet-join{
        .info{
            font-size: 13px;
            height: 48px;
            .label{
                height: 48px;
                line-height: 48px;
                margin-right: 10px;
                width: 70px;
                text-align: center;
                background-color: #efefef;
            }
            .cont{
                flex: 1;
                padding-right: 2px;
                .select-type{
                    padding: 5px 10px;
                    display: inline-block;
                }
                .input{
                    border:.5px solid $color-border-three;
                    padding:8px 5px;
                    width: 95%;
                }
            }
            .join-tip{
                font-size: 12px;
                color: $color-font-secondary;
            }
        }
    }
    .mint-radio-core{
        width: 16px;
        height: 16px;
        &:after{
            top: 3px;
            left: 3px;
        }
    }

    .lottery-ks{
        $ks-color-base:#3f7555;
        $ks-color-pull:#36674b;
        $ks-color-dark: #184612;
        $ks-color-black: #252625;
        $ks-border-color:#74a78d;
        $ks-color-cont:#549263;
        $ks-color-yellow:#eaca52;
        background-color:$ks-color-base;
        .mint-header{
            background-color: $ks-color-black;
        }
        .top-info{
            background-color: $ks-color-base;
            color: #ffffff;
            .border-bottom-1px::after{
                height: 0;
            }
        }
        .fixed-cont{
            background-color: $ks-color-base;
            border-bottom: 2px solid #2d543d;
        }
        .bet-order-cont .mint-button--small{
            background-color: darken($ks-color-base,10%);
            color:#caebda;
            box-shadow: none;
        }
        .bet-foot{
            .multiple{
                background: #21422c;
                em{
                    color: #ffffff;
                }
            }
        }
        .ks-agree{
            color: #b6e0c7;
        }
    }
</style>
