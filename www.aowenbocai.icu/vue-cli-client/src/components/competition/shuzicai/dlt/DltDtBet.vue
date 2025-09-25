<!--大乐透 胆拖投注-->
<template>
    <div>
        <div class="contentH" :style="{'height':betHeight + 'px'}">
            <div class="fore">
                <!--胆码-->
                <div>
                    <div class="flex-box head" @click="foreShowDm = !foreShowDm">
                        <span class="c-2">前区胆码</span>
                        <span class="f-sm flex tl ml-sm c-3">我认为必出的号码<em class="red">(至多4个)</em></span>
                        <i class="iconfont icon-xialajiantou rotateIcon" :class="foreShowDm ? 'is-active' : 'no-active'"></i>
                    </div>
                    <div class="fore-bet-wrapper bet-box" v-show="foreShowDm">
                        <ul class="balls-box">
                            <li v-for="(n,i) in foreBall">
                                <a class="balls" :class="{'selected' : checkForePlan('dm',n)}" @click="doBetDm(1,n,4)">{{n}}</a>
                                <i class="c-3 f-mini" :class="{'red':miss.fore[i] > 25}">{{miss.fore[i]}}</i>
                            </li>
                        </ul>
                    </div>
                    <div class="fore-bet-wrapper bet-box" v-show="!foreShowDm">
                        <ul class="balls-box">
                            <li v-for="n in forePlanDm">
                                <a class="balls selected" @click="foreShowDm = true">{{n}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!--拖码-->
                <div class="mt">
                    <div class="flex-box head" @click="foreShowTm = !foreShowTm">
                        <span class="c-2">前区拖码</span>
                        <span class="f-sm flex tl ml-sm c-3">我认为可能出的号码<em class="red">(至少2个)</em></span>
                        <i class="iconfont icon-xialajiantou rotateIcon" :class="foreShowTm ? 'is-active' : 'no-active'"></i>
                    </div>
                    <div class="fore-bet-wrapper bet-box" v-show="foreShowTm">
                        <ul class="balls-box">
                            <li v-for="(n,i) in foreBall">
                                <a class="balls" :class="{'selected' : checkForePlan('tm',n)}" @click="doBetTm(1,n)">{{n}}</a>
                                <i class="c-3 f-mini" :class="{'red':miss.fore[i] > 25}">{{miss.fore[i]}}</i>
                            </li>
                        </ul>
                    </div>
                    <div class="fore-bet-wrapper bet-box" v-show="!foreShowTm">
                        <ul class="balls-box">
                            <li v-for="n in forePlanTm">
                                <a class="balls selected" @click="foreShowTm=true">{{n}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="after">
                <!--胆码-->
                <div>
                    <div class="flex-box head" @click="backShowDm = !backShowDm">
                        <span class="c-2">后区胆码</span>
                        <span class="f-sm flex tl ml-sm c-3">我认为必出的号码<em class="red">(至多1个)</em></span>
                        <i class="iconfont icon-xialajiantou rotateIcon" :class="backShowDm ? 'is-active' : 'no-active'"></i>
                    </div>
                    <div class="back-bet-wrapper bet-box" v-show="backShowDm">
                        <ul class="balls-box">
                            <li v-for="(n,i) in backBall">
                                <a class="balls" :class="{'selected' : checkBackPlan('dm',n)}" @click="doBetDm(0,n,1)">{{n}}</a>
                                <i class="c-3 f-mini" :class="{'red':miss.back[i] > 25}">{{miss.back[i]}}</i>
                            </li>
                        </ul>
                    </div>
                    <div class="back-bet-wrapper bet-box" v-show="!backShowDm">
                        <ul class="balls-box">
                            <li v-for="n in backPlanDm">
                                <a class="balls selected" @click="backShowDm = true">{{n}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!--拖码-->
                <div class="mt">
                    <div class="flex-box head" @click="backShowTm = !backShowTm">
                        <span class="c-2">后区拖码</span>
                        <span class="f-sm flex tl ml-sm c-3">我认为可能出的号码<em class="red">(至少2个)</em></span>
                        <i class="iconfont icon-xialajiantou rotateIcon" :class="backShowTm ? 'is-active' : 'no-active'"></i>
                    </div>
                    <div class="back-bet-wrapper bet-box" v-show="backShowTm">
                        <ul class="balls-box">
                            <li v-for="(n,i) in backBall">
                                <a class="balls" :class="{'selected' : checkBackPlan('tm',n)}" @click="doBetTm(0,n)">{{n}}</a>
                                <i class="c-3 f-mini" :class="{'red':miss.back[i] > 25}">{{miss.back[i]}}</i>
                            </li>
                        </ul>
                    </div>
                    <div class="back-bet-wrapper bet-box" v-show="!backShowTm">
                        <ul class="balls-box">
                            <li v-for="n in backPlanTm">
                                <a class="balls selected" @click="backShowTm = true">{{n}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--底部 start-->
        <div class="bet-foot">
            <div class="yl-box f-mini border-top-1px tc c-3">
                <template v-if="!notes">
                    前区胆码<em class="red">{{f_dm_n}}</em>个,拖码<em class="red">{{f_tm_n}}</em>个,
                    后区胆码<em class="red">{{b_dm_n}}</em>个,拖码<em class="red">{{b_tm_n}}</em>个
                </template>
                <template v-else>
                    已选<em class="red">{{notes}}</em>注，共<em class="red">{{notes * 2}}</em>{{lotteryUnit}}
                </template>
            </div>
            <div class="flex-box notes-box">
                <div class="flex bet-detail">
                    <div class="c-4" @click="clear">
                        <i class="iconfont icon-shanchu btn-icon-clear"></i>清空
                    </div>
                </div>
                <div class="tc bet-basket" style="margin: 0 15px 0 5px">
                    <button class="bet-btn btn-basket" @click="addBetBasket">+ 号码篮 <mt-badge class="badge" type="error" size="small">{{badge}}</mt-badge></button>
                </div>
                <button class="bet-btn btn-sure" @click="submitOrder">选好了</button>
            </div>
        </div>
        <!--底部 end-->
    </div>
</template>

<script>
    export default {
        name: '',
        props:['type','text','miss','expect','endTime','title','name'],
        data () {
            return {
                foreShowDm: true,
                foreShowTm: true,
                backShowDm: true,
                backShowTm: true,
                foreBall:['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24',
                    '25','26','27','28','29','30','31','32','33','34','35'],//前区选号
                backBall:['01','02','03','04','05','06','07','08','09','10','11','12'],//后区选号
                forePlanDm:[],//前区胆码投注数据
                forePlanTm:[],//前区拖码投注数据
                backPlanDm:[],//后区胆码投注数据
                backPlanTm:[],//后区拖码投注数据
            }
        },
        computed:{
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            //选号区高度
            betHeight(){
                return this.$store.state.clientHeight - 152
            },
            //选号篮数量
            badge(){
                return this.$store.state.shuzicai.plan.length
            },
            //前区选号状态
            checkForePlan(){
                return (flag,val)=>{
                    if(flag == 'dm'){
                        return this.forePlanDm.indexOf(val) > -1 ? true : false
                    }
                    if(flag == 'tm'){
                        return this.forePlanTm.indexOf(val) > -1 ? true : false
                    }
                }
            },
            //后区选号状态
            checkBackPlan(){
                return (flag,val)=>{
                    if(flag == 'dm'){
                        return this.backPlanDm.indexOf(val) > -1 ? true : false
                    }
                    if(flag == 'tm'){
                        return this.backPlanTm.indexOf(val) > -1 ? true : false
                    }
                }
            },
            f_dm_n(){
                return this.forePlanDm.length  //前区胆码数量
            },
            f_tm_n(){
                return this.forePlanTm.length  //前区拖码数量
            },
            b_dm_n(){
                return this.backPlanDm.length  //后区胆码数量
            },
            b_tm_n(){
                return this.backPlanTm.length  //后区拖码数量
            },
            //计算注数
            notes(){
                if(!this.b_dm_n && !this.f_dm_n || this.f_dm_n + this.f_tm_n < 6 || this.b_tm_n < 2){
                    return 0
                }else {
                    let foreNote = this.$bet.combination(this.f_tm_n,5 - this.f_dm_n);
                    let backNote = this.$bet.combination(this.b_tm_n,2 - this.b_dm_n);
                    return foreNote * backNote
                }
            }
        },
        methods:{
            //胆码选号 flag 1前区 0后区
            doBetDm(flag,num,max){
                let dmObj = flag ? this.forePlanDm : this.backPlanDm
                let tmObj = flag ? this.forePlanTm : this.backPlanTm

                let dmStatus = dmObj.indexOf(num)
                let tmStatus = tmObj.indexOf(num)
                if(dmStatus > -1){
                    dmObj.splice(dmStatus,1)
                }else {
                    //胆码最多选择个数提示
                    let text = flag ? '前区':'后区'
                    if(dmObj.length >= max){
                        this.$messagebox('提示', text +  '最多' + max + '个胆码')
                        return
                    }
                    dmObj.push(num)
                    if(tmStatus > -1){
                        tmObj.splice(tmStatus,1)
                    }
                }
                dmObj = dmObj.sort()
            },
            //拖码选号
            doBetTm(flag,num){
                let dmObj = flag ? this.forePlanDm : this.backPlanDm
                let tmObj = flag ? this.forePlanTm : this.backPlanTm
                let dmStatus = dmObj.indexOf(num)
                let tmStatus = tmObj.indexOf(num)
                if(tmStatus > -1){
                    tmObj.splice(tmStatus,1)
                }else {
                    tmObj.push(num)
                    if(dmStatus > -1){
                        dmObj.splice(dmStatus,1)
                    }
                }
                tmObj = tmObj.sort()
            },
            //机选
            random(flag){
                let obj = flag ? this.foreBall : this.backBall
                let num = flag ? this.foreNum : this.backNum
                let arr = this.$bet.getRandomArrayEle(obj, Number(num)).sort()
                if(flag){
                    this.$set(this,'forePlan',arr)
                }else {
                    this.$set(this,'backPlan',arr)
                }
            },
            //改变机选个数
            changeNum(flag,n){
                if(flag){
                    this.$set(this,'foreNum',n)
                    this.foreShow = false
                }else {
                    this.$set(this,'backNum',n)
                    this.backShow = false
                }
                this.random(flag)
            },
            //清空
            clear(){
                this.$set(this,'forePlanDm',[])
                this.$set(this,'forePlanTm',[])
                this.$set(this,'backPlanDm',[])
                this.$set(this,'backPlanTm',[])
            },
            //添加到号码篮
            addBetBasket(){
                if(this.notes < 1){
                    if(!this.b_dm_n && !this.f_dm_n){
                        this.$messagebox('提示','前区和后区胆码不能同时为空！')
                        return
                    }
                    if(this.f_dm_n + this.f_tm_n < 6){
                        this.$messagebox('提示','前区胆码和拖码的总个数至少6个！')
                        return
                    }
                    if(this.b_tm_n < 2){
                        this.$messagebox('提示','后区至少选择2个拖码！')
                        return
                    }
                    return
                }
                let list = {}
                list['num'] = this.forePlanDm.sort().join(',') + '#' + this.forePlanTm.sort().join(',') + '|' + this.backPlanDm.sort().join(',') + '#' + this.backPlanTm.sort().join(',');
                list['notes'] = this.notes
                list['type'] = this.type
                list['type_text'] = this.text
                this.$store.commit('pushSzcBetNum',list)
                this.clear(); //清空当前选号
            },
            // 立即投注
            submitOrder(){
                if(this.badge < 1 && this.notes < 1){
                    this.$messagebox({
                        title: '提示',
                        message: '至少选择1注投注号码！',
                        confirmButtonText:'我知道了'
                    });
                    return
                }
                if(this.notes > 0){
                    this.addBetBasket();
                }
                this.$router.push({
                    path:'/shuzicai/bet',
                    query:{
                        name:this.name,
                        title: this.title,
                        expect:this.expect,
                        end_time: this.endTime
                    }
                })
            }
        }
    }
</script>
