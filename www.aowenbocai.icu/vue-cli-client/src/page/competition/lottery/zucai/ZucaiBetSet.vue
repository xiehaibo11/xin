<template>
    <div class="jc-order-main">
        <mt-header :title="title">
            <mt-button icon="back" slot="left" @click.native="goHome">返回大厅</mt-button>
        </mt-header>
        <div class="tc border-bottom-1px btn-back flex-box" style="padding: 0 5px">
            <div @click="goBack" class="f-sm">+选择比赛</div>
            <div class="flex tr f-mini"><em class="c-3 f-mini">{{playMap[name]}},{{games}}场,第{{expect}}期,{{endtime}}截止</em></div>
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
                <div class="zucai-cont jc-bet-cont contentH" :style="{height:contentH + 'px'}" ref="wrapper">
                    <div class="border-bottom-1px zucai-list" v-for="(match,key,index) in tempMatchData">
                        <div class="flex-box jc-item">
                            <div class="jc-item-left" style="padding-top: 0;line-height: 1.2">
                                <p class="f-sm">{{match.mdata.simpleleague}}</p>
                                <p>{{index + 1}}</p>
                                <p>{{match.mdata.resultscore}}</p>
                            </div>
                            <div class="jc-item-right flex">
                                <div class="cf zucai-bet-item">
                                    <div class="fl bet-win item" :class="{'active' : curOpt(match.mdata.fid,'3')}" @click="selectDone(match.mdata.fid,'3',match.pdata.win)">
                                        <p><i class="c-4 f-mini" v-if="Number(match.mdata.homestanding)>0">[{{match.mdata.homestanding}}]</i>{{match.mdata.homename}}</p>
                                        <p><em class="pl">主胜{{match.pdata.win}}</em></p>
                                    </div>
                                    <div class="fl bet-draw item" :class="{'active' : curOpt(match.mdata.fid,'0')}" @click="selectDone(match.mdata.fid,'0',match.pdata.draw)">
                                        <p>vs</p>
                                        <p><em class="pl">平{{match.pdata.draw}}</em></p>
                                    </div>
                                    <div class="fl bet-lost item" :class="{'active' : curOpt(match.mdata.fid,'1')}" @click="selectDone(match.mdata.fid,'1',match.pdata.lost)">
                                        <p>{{match.mdata.awayname}}<i class="c-4 f-mini" v-if="Number(match.mdata.awaystanding)>0">[{{match.mdata.awaystanding}}]</i></p>
                                        <p><em class="pl">客胜{{match.pdata.lost}}</em></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <!--选号列表 end-->

        <!--底部-->
        <div class="jc-order-foot">
            <div class="foot-agree flex-box">
                <!--投注协议-->
                <bet-agree-checked v-model="checked"></bet-agree-checked>
                <!--投注协议 end-->
            </div>
            <div class="foot-set flex-box">
                <div class="tc flex"><em>投</em> <input-number v-model="multiple" :min="1" :step="1"></input-number> <em>倍</em></div>
            </div>
            <div class="bet-submit flex-box">
                <button v-if="joinOpen" @click="handleJoin" class="bet-btn btn-sure" style="font-size: 14px;margin-left: 0;background-color: #607D8B;padding: 8px 8px;">合买</button>
                <div class="acount-award flex tr">
                    <p class="f-sm">投注金额<b class="red">{{totalMoney}}</b>{{lotteryUnit}}</p>
                    <p class="f-mini c-3">共{{notes}}注</p>
                </div>
                <button class="bet-btn btn-sure" @click="doBuy">提交</button>
            </div>
        </div>
        <!--底部 end-->

        <!--订单信息确认-->
        <mt-popup
            v-model="orderVisible"
            position="bottom">
            <div class="jc-orders-info" v-if="orderVisible">
                <div class="order-title org border-bottom-1px">订单信息确认</div>
                <div class="order-info">
                    <div>
                        <span class="name">投注彩种:</span>
                        <span class="info">{{playMap[name]}}</span>
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
    import inputNumber from 'components/common/InputNumber.vue' //购买输入框组件
    import BetAgreeChecked from 'components/lottery/BetAgreeChecked.vue' //投注协议
    import OrderJoinSet from 'components/lottery/OrderJoinSet.vue' //合买
    export default {
        name: 'zucaiOrder',
        components:{
            inputNumber,
            BetAgreeChecked,
            OrderJoinSet
        },
        data () {
            return {
                buyType:1, //购买方式 1自购 2发起合买

                loading: false,
                plan: this.$store.state.zucai.plan || {}, //投注方案内容
                multiple : 1, //投注倍数

                checked : true,//投注协议确认
                orderVisible: false, //订单详情
                joinVisible : false, //合买设置

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
            needGames(){
                return this.name == 'sfc' ? 14 : 9
            },
            playMap(){
                return this.$store.state.zucai.playMap
            },
            //账户余额
            money(){
                return this.$store.state.userinfo.money
            },
            name(){
                return this.$route.query.name
            },
            expect(){
                return this.$route.query.expect
            },
            endtime(){
                return this.$route.query.endtime
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
                return this.$store.state.zucai.tempMatchData
            },
            //已选场数
            games(){
                return this.$_.size(this.plan)
            },
            //内容高度
            contentH(){
                return this.$store.state.clientHeight - 196
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
            //注数计算
            notes(){
                let _arr = []
                let note = 0
                for(let mid in this.plan){
                    _arr.push(this.$_.size(this.plan[mid])) //单场投注个数组
                }
                //计算注数
                let newArr = [];
                this.$bet.zucaiPlzh(0, 1, _arr, '', this.needGames, newArr); //排列组合方式
                for(let m in newArr){
                    let signArr = newArr[m].split('|')
                    let count = 1
                    for(let n in signArr){
                        count = count * Number(signArr[n])
                    }
                    note += Number(count)
                }

                return this.games < this.needGames ? 0 : note
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
            //清空投注数据
            clear(){
                this.$store.commit('clearZucaiBetData')
                this.resetTypeOptions();
                this.$set(this,'multiple',1)
                this.$set(this,'plan',{})
            },
            //返回大厅
            goHome(){
                if(this.games > 0){
                    this.$messagebox.confirm(
                        '返回大厅将清空所有已选的号码'
                    ).then(()=>{
                        this.$store.commit('delKeepAlivePage','zucai') //清除投注页面缓存
                        this.$store.commit('delKeepAlivePage','zucaiOrder') //清除当前页面缓存
                        this.$store.commit('clearZucaiBetData')
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
                this.$store.commit('setZucaiPlan',this.plan)
                this.$router.goBack(-1);//返回上一层
            },
            //检测选中状态
            checkStatus(obj,id,sign) {
                let res = false
                if(obj.hasOwnProperty(id)){
                    if(obj[id].hasOwnProperty(sign)){
                        res = true
                    }else {
                        res = false
                    }
                }else {
                    res = false
                }
                return res
            },
            //选择方案增减
            addPlan(obj,id,sign,pl){
                if(obj.hasOwnProperty(id)){
                    if(obj[id].hasOwnProperty(sign)){
                        this.$delete(obj[id],sign)
                        if(JSON.stringify(obj[id]) == '{}'){
                            this.$delete(obj,[id])
                        }
                    }else {
                        this.$set(obj[id],[sign], pl)
                    }
                }else{
                    this.$set(obj,[id], {})
                    this.$set(obj[id],[sign], pl)
                }
            },
            //方案选择(场次id,sign选号标识3主胜、1平、0主负,pl赔率)
            selectDone(id,sign,pl){
                this.addPlan(this.plan,id,sign,pl);
                console.log(JSON.stringify(this.plan))
            },
            //删除所选比赛
            deletMatch(mid){
                this.$delete(this.plan,mid)
                this.$delete(this.tempMatchData,mid)
                this.$store.commit('setZucaiPlan',this.plan)
                this.$store.commit('setZucaiTempMatchData',this.tempMatchData)
            },

            //订单信息确认
            submitOrder(){
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
                let data = {
                    plan: JSON.stringify(this.plan),
                    multiple: this.multiple,
                    name: this.name,
                    notes : this.notes,
                    total_money: this.totalMoney,
                    expect: this.expect,
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
//            this.$store.commit('setKeepAlivePage','zucaiOrder')
        },
//        //进入设置页面时缓存页面
//        beforeRouteLeave(to, from, next){
//            if(to.path == '/jc' && to.path == '/jczq'){
//                this.$store.commit('delKeepAlivePage','jcOrder')
//            }else {
//                this.$store.commit('setKeepAlivePage','jcOrder')
//            }
//            next();
//        }
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
