<template>
    <div class="lottery-bg">
        <mt-header :title="title">
            <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)"></mt-button>
        </mt-header>
        <div class="head-info f-mini tc border-bottom-1px">
            第{{expect}}期 截止时间: <em class="red">{{endTime}}</em>
        </div>
        <div class="fixed-cont">
            <!--操作按钮组-->
            <div class="bet-order-cont">
                <div class="flex-box bd-v" style="padding: 10px">
                    <div>
                        <mt-button @click.native="$router.goBack(-1)" size="small" class="flex-box"><i slot="icon" class="iconfont icon-tianjia f-mini"></i> 继续选号</mt-button>
                    </div>
                    <div class="flex">
                        <mt-button @click.native="handleRandom" size="small" class="flex-box"><i slot="icon" class="iconfont icon-tianjia f-mini"></i> 机选一注</mt-button>
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
                                    <template v-if="name=='dlt'||name=='ssq'">
                                        <p class="org num_item"><em v-for="fore in filterDltCode(item.num).foreArr" style="padding-right: 4px;">{{fore}}</em></p>
                                        <p class="abled num_item"><em v-for="back in filterDltCode(item.num).backArr" style="padding-right: 4px;">{{back}}</em></p>
                                        <p class="c-3 f-mini">
                                            <span class="mr-sm">{{item.type_text}}</span><span class="mr-sm">{{item.notes}}注</span><span class="mr-sm">{{item.notes * per}}{{lotteryUnit}}</span>
                                        </p>
                                    </template>
                                    <template v-else>
                                        <p class="num_item org">{{item.num}}</p>
                                        <p class="c-3 f-mini">
                                            <span class="mr-sm">{{item.type_text}}</span><span class="mr-sm">{{item.notes}}注</span><span class="mr-sm">{{item.notes * 2}}{{lotteryUnit}}</span>
                                        </p>
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
            <bet-agree-checked v-model="checked"></bet-agree-checked>
            <!--投注协议 end-->
        </div>
        <!--底部 start-->
        <div class="bet-foot">
            <div class="multiple f-small border-top-1px flex-box" style="padding: 0 10px">
                <div class="f-sm flex-box c-2" v-if="name == 'dlt'">
                    <div class="flex-box">
                        <span class="input-checkbox" :class="{'checkbox-checked': isAppend}" @click="isAppend = !isAppend" ></span>
                        <span @click="isAppend = !isAppend" >全部追加</span>
                        <i @click="showAppend" class="iconfont icon-yiwen1 f-sm c-3 ml-sm tl"></i>
                    </div>
                </div>
                <div class="chase-box flex" :class="name == 'dlt' ? 'tr' : 'tc'"><em class="f-sm">投</em> <input-number v-model="multiple" :min="1" :step="1" size="small"></input-number> <em class="f-sm">倍</em></div>
            </div>
            <div class="flex-box notes-box">
                <div class="tc">
                    <button class="bet-btn btn-basket" @click="handleJoin" v-if="joinOpen">合买</button>
                    <button class="bet-btn btn-random" @click="chaseVisible=true">追号</button>
                </div>
                <div class="flex bet-basket tr" style="margin-right: 5px;">
                    <p>共 {{totalMoney}} {{lotteryUnit}}</p>
                    <p class="f-mini c-4">自购 {{totalNotes}}注 <em>{{multiple}}倍</em></p>
                </div>
                <button class="bet-btn btn-sure" @click="doBuy">确认提交</button>
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
                        <span class="name">投注倍数:</span>
                        <span class="info"><b class="red">{{multiple}}</b> 倍</span>
                    </div>
                    <div v-if="name == 'dlt'">
                        <span class="name">全部追加:</span>
                        <span class="info"><em :class="isAppend ? 'red' : 'c-3'">{{isAppend ? '是' : '否'}}</em></span>
                    </div>
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
        <!--合买-->
        <transition name="slide-left">
            <div v-if="joinVisible" class="join-visible">
                <order-join-set :total-money="totalMoney" v-model="joinVisible" :multiple="multiple" :total-notes="totalNotes" @submit-order="getJoinEmit"></order-join-set>
            </div>
        </transition>
        <!--合买 end-->
        <!--追号-->
        <transition name="slide-left">
            <div v-if="chaseVisible" class="chase-visible">
                <order-chase-set :one-money="oneMoney" :expect="expect" v-model="chaseVisible" @submit-order="getChaseEmit" :init-multiple="multiple" :total-notes="totalNotes"></order-chase-set>
            </div>
        </transition>
        <!--追号 end-->
    </div>
</template>

<script>
    import inputNumber from 'components/common/InputNumber.vue' //购买输入框组件
    import BetAgreeChecked from 'components/lottery/BetAgreeChecked.vue' //投注协议
    import OrderJoinSet from 'components/lottery/OrderJoinSet.vue' //合买
    import OrderChaseSet from 'components/lottery/OrderChaseSet.vue' //追号
    export default {
        name:'shuzicaiBet',
        components:{
            inputNumber,
            BetAgreeChecked,
            OrderJoinSet,
            OrderChaseSet
        },
        data () {
            return {
                slideName:'',
                loading:false,
                multiple: 1, //投注倍数
                checked: true, //投注协议
                orderVisible:false, //订单详情

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
                isAppend: false ,//追加
            }
        },
        computed:{
            per(){
                return this.isAppend ? 3 : 2
            },
            title(){
                return this.$route.query.title ? this.$route.query.title  + '-投注确认': ''
            },
            expect(){
                return this.$route.query.expect
            },
            endTime(){
                return this.$route.query.end_time
            },
            name(){
                return this.$route.query.name
            },
            //单位
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            //账户余额
            money(){
                return this.$store.state.userinfo.money
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
                return ''
            },
            //选号列表
            betArr(){
                return this.$store.state.shuzicai.plan
            },
            //总注数
            totalNotes(){
                return this.$store.getters.getSzcTotalNotes
            },
            //需要支付的总金额
            totalMoney(){
                return this.totalNotes * this.per * this.multiple
            },
            //1倍投注金额（追号时使用）
            oneMoney(){
                return this.totalNotes * this.per
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
                return this.$store.state.clientHeight  - 203
            },
            //排列3玩法
            plsText(){
                let obj = {
                    '1.1':'直选复式',
                    '1.2':'直选单式',
                    '1.3':'直选和值',
                    '1.4':'直选胆拖',
                    '2.1':'组三复式',
                    '2.2':'组三单式',
                    '2.3':'组三和值',
                    '2.4':'组三胆拖',
                    '3.1':'组六复式',
                    '3.2':'组六单式',
                    '3.3':'组六和值',
                    '3.4':'组六胆拖'
                }
                return obj[this.type]
            },
            //是否开启合买
            joinOpen(){
                return this.$store.state.setting.join_isOpen == 1 ? true : false
            },
            //是否开启佣金
            isGain(){
                return this.$store.state.setting.isGain
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
            //大乐透投注号码解析
            filterDltCode(codeStr){
                let arr = codeStr.split('|')
                let foreArr = arr[0].split(',')
                let backArr = arr[1].split(',')
                return {'foreArr':foreArr,'backArr':backArr}
            },
            //重置页面
            resetData(){
                this.$store.commit('clearSzcBetNum'); //清除投注数据
                this.buyType = '1';
                this.multiple = 1;
                this.expectArr =[];
                this.joinVisible = false
                this.chaseVisible = false
                this.join = {
                    total_share:0,//份数
                    buy_share:0,//购买份数
                    bd_share:0,//保底份数
                    infoTitle:'完全公开',
                    infoState: 0, //是否公开
                    gain: '0',
                    declaration: ''
                }
                this.chase = {
                    chaseNum: 4, //默认追号期数
                    chaseMul: 1, //追号倍数设置
                    isStop: false, //中奖是否停止追号
                    chaseData: [], //追号数据内容
                }
                this.isAppend = false
            },
            //返回大厅
            goHome(){
                this.$router.isBack = true
                this.$router.push({
                    path:'/jc'
                })
            },
            //删除选号
            deleteBet(i){
                this.$store.commit('deleteSzcBetArr',i)
            },
            //机选添加选号
            handleRandom(){
                this.$refs.wrapper.scrollTop = 0
                let betArr = ''
                let notes = 1
                let type = ''
                let type_text = '标准选号'
                let list = {}
                let randomBall = ['0','1','2','3','4','5','6','7','8','9']
                //大乐透机选
                if(this.name == 'dlt'){
                    let foreBall = ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24',
                        '25','26','27','28','29','30','31','32','33','34','35']
                    let backBall = ['01','02','03','04','05','06','07','08','09','10','11','12']
                    let foreBet = this.$bet.getRandomArrayEle(foreBall, 5).sort()
                    let backBet = this.$bet.getRandomArrayEle(backBall, 2).sort()
                    betArr = foreBet.join(',') + '|' + backBet.join(',')
                    notes = 1
                    type = 1
                    type_text = '标准选号'
                }
                //双色球机选
                if(this.name == 'ssq'){
                    let foreBall = ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24',
                        '25','26','27','28','29','30','31','32','33']
                    let backBall = ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16']
                    let foreBet = this.$bet.getRandomArrayEle(foreBall, 6).sort()
                    let backBet = this.$bet.getRandomArrayEle(backBall, 1).sort()
                    betArr = foreBet.join(',') + '|' + backBet.join(',')
                    notes = 1
                    type = 1
                    type_text = '标准选号'
                }
                //排列3
                if(this.name == 'pls'){ //排列3
                    //直选单式、复式
                    if(this.type == 1.1 || this.type == 1.2){
                        let arr = []
                        for(let i = 0 ; i < 3 ;i ++ ){
                            arr.push(this.$bet.getRandomArrayEle(randomBall, 1))
                        }
                        betArr = arr.join('|')
                    }else if(this.type == 1.3){
                        //直选和值
                        var hz = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27];
                        betArr = this.$bet.getRandomArrayEle(hz, 1).join(",");
                        var plusVal = 0;
                        for (var i = 0; i <= 9; i++) {
                            for (var j = 0; j <= 9; j++) {
                                for (var k = 0; k <= 9; k++) {
                                    var hz = i + j + k;
                                    if (hz == betArr) {
                                        plusVal += 1;
                                    }
                                }
                            }
                        }
                        notes = plusVal;
                    }else if(this.type == 1.4){
                        //直选胆拖
                        betArr = this.$bet.getRandomArrayEle(randomBall, 3).join(',')
                        betArr = betArr.replace(',','#')//替换第一个,为#
                        notes = 6
                    }else if(this.type == 2.1 || this.type == 2.2){
                        //组三复式、单式
                        betArr = this.$bet.getRandomArrayEle(randomBall, 2).sort().join(',')
                        notes = 2
                    }else if(this.type == 2.3){
                        //组三和值
                        var hz = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26];
                        betArr = this.$bet.getRandomArrayEle(hz, 1).join(",");
                        var plusVal = 0;
                        for (var i = 0; i <= 9; i++) {
                            for (var j = 0; j <= 9; j++) {
                                if (i !== j) {
                                    var hz = i + i + j;
                                    if (hz == betArr) {
                                        plusVal += 1;
                                    }
                                }
                            }
                        }
                        notes = plusVal;
                    }else if(this.type == 2.4){
                        //组三胆拖
                        betArr = this.$bet.getRandomArrayEle(randomBall, 3).join(',')
                        betArr = betArr.replace(',','#')//替换第一个,为#
                        notes = 4
                    }else if(this.type == 3.1 || this.type == 3.2){
                        //组六复式、单式
                        betArr = this.$bet.getRandomArrayEle(randomBall, 3).sort().join(',')
                        notes = 1
                    }else if(this.type == 3.3){
                        //组六和值
                        var hz = [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24];
                        betArr = this.$bet.getRandomArrayEle(hz, 1).join(",");
                        var plusVal = 0;
                        for (var i = 0; i <= 9; i++) {
                            for (var j = i; j <= 9; j++) {
                                for (var k = j; k <= 9; k++) {
                                    if (i != j && j != k && i != k) {
                                        var hz = i + j + k;
                                        if (hz == betArr) {
                                            plusVal += 1;
                                        }
                                    }
                                }
                            }
                        }
                        notes = plusVal;
                    }else if(this.type == 3.4){
                        //组六胆拖
                        betArr = this.$bet.getRandomArrayEle(randomBall, 4).join(',')
                        betArr = betArr.replace(',','#')//替换第一个,为#
                        notes = 3
                    }
                    type = this.type
                    type_text = this.plsText
                }
                //排列5
                if(this.name == 'plw'){
                    let arr = []
                    for(let i = 0 ; i < 5 ;i ++ ){
                        arr.push(this.$bet.getRandomArrayEle(randomBall, 1))
                    }
                    betArr = arr.join('|')
                    type = 1
                }
                //七星彩
                if(this.name == 'qxc'){
                    let arr = []
                    for(let i = 0 ; i < 7 ;i ++ ){
                        arr.push(this.$bet.getRandomArrayEle(randomBall, 1))
                    }
                    betArr = arr.join('|')
                    type = 1
                }
                list['num'] = betArr;
                list['notes'] = notes
                list['type'] = type
                list['type_text'] = type_text
                this.$store.commit('pushSzcBetNum',list)
            },
            //清空选号列表
            clear(){
                this.$store.commit('clearSzcBetNum')
            },
            //合买订单提交
            getJoinEmit(emitVal){
                this.buyType = 2
                this.$set(this,'join',emitVal)
                this.submitOrder();
            },
            //追号订单提交
            getChaseEmit(emitVal){
                this.buyType = 3
                this.$set(this,'chase',emitVal)
                this.submitOrder();
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
                    is_hemai: isJoin,
                    is_stop: stop,
                    expect: JSON.stringify(this.expectArr),
                    lottery_name: this.name
                }
                if(this.buyType == 2){
                    data['total_share'] = this.join.total_share
                    data['buy_share'] = this.join.buy_share
                    data['bd_share'] = this.join.bd_share
                    data['gain'] = this.join.gain
                    data['show'] = this.join.infoState
                    data['declaration'] = this.join.declaration || '这个家伙很懒，只想中大奖！'
                }
                if(this.name == 'dlt'){
                    data['isAppend'] = this.isAppend ? 1 : 0
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
                }).catch(function (error) {
                    console.log(error);
                })
            },
            //追加解释
            showAppend(){
                this.$messagebox({
                    title: '提示',
                    message: '每注追加1' + this.lotteryUnit + '，最多可中1800万。',
                    confirmButtonText:'我知道了'
                });
            }
        },
        create(){
            this.$store.commit('setKeepAlivePage','betOrder')
        },
        //返回首页时
        beforeRouteLeave(to, from, next){
            if(to.path =='/dlt'||to.path =='/pls'||to.path =='/plw'||to.path =='/qxc'||to.path=='/ssq'){
                this.$store.commit('setKeepAlivePage','shuzicaiBet')
            }else{
                this.$store.commit('delKeepAlivePage','dlt')
                this.$store.commit('delKeepAlivePage','pls')
                this.$store.commit('delKeepAlivePage','plw')
                this.$store.commit('delKeepAlivePage','qxc')
                this.$store.commit('delKeepAlivePage','ssq')
                this.$store.commit('delKeepAlivePage','shuzicaiBet')
            }
            next();
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .org {
        color: #ff8b45;
    }
    .abled {
         color: #5db4f9;
     }
    .mint-button--small{
        margin: 0 auto;
        height: 28px;
    }
    .head-info{
        height: 25px;
        line-height: 25px;
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
                    .num_item{
                        word-break: break-word;
                        line-height: 1.2;
                        margin-bottom: 5px;
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
    .bet-foot{
        .multiple{
            text-align: center;
            background-color: #ffffff;
            .chase-box{
                padding: 5px 0;
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
</style>
