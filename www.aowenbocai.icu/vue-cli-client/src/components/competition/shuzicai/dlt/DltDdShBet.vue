<!--大乐透 定胆杀号-->
<template>
    <div>
        <div class="contentH" :style="{'height':betHeight + 'px'}">
            <div class="fore">
                <div class="flex-box head">
                    <span class="c-2">前区</span>
                    <span class="f-sm flex tl ml-sm c-3">最多4个胆码,多余的胆码将会自动做杀号处理</span>
                </div>
                <div class="fore-bet-wrapper bet-box">
                    <ul class="balls-box">
                        <li v-for="(n,i) in foreBall">
                            <a class="balls" :class="checkStatus(1,n) == 2 ? 'selected' : checkStatus(1,n) == 3 ? 'killed' : ''" @click="doBet(1,n)">{{n}}</a>
                            <i class="c-3 f-mini" :class="{'red':miss.fore[i] > 25}">{{miss.fore[i]}}</i>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="after">
                <div class="flex-box head">
                    <span class="c-2">后区</span>
                </div>
                <div class="back-bet-wrapper bet-box">
                    <ul class="balls-box">
                        <li v-for="(n,i) in backBall">
                            <a class="balls" :class="checkStatus(0,n) == 2 ? 'selected' : checkStatus(0,n) == 3 ? 'killed' : ''" @click="doBet(0,n)">{{n}}</a>
                            <i class="c-3 f-mini" :class="{'red':miss.back[i] > 25}">{{miss.back[i]}}</i>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--底部 start-->
        <div class="bet-foot">
            <div class="random-box">
                <div class="tc random-head" @click="foreShow=false;backShow=false;numShow=false;randomShow = !randomShow;">
                    机选条件 <i class="iconfont icon-xialajiantou rotateIcon" :class="randomShow ? 'is-active' : 'no-active'"></i>
                </div>
                <div style="padding: 0 5px;">
                    <div  style="position: relative;">
                        <!--前区个数选项-->
                        <transition name="fade">
                            <div class="random-select" v-show="foreShow">
                                <div class="jc-info-arrow" style="float: left"><i class="ico-arrow2"></i></div>
                                <ul class="cf">
                                    <li v-for="n in foreMax" v-if="n >= 5 - f_dm_n" @click="changeNum(1,n)">{{n}}</li>
                                </ul>
                            </div>
                        </transition>
                        <!--前区个数选项 end-->
                        <!--后区个数选项-->
                        <transition name="fade">
                            <div class="random-select" v-show="backShow">
                                <div class="jc-info-arrow" style="width: 50%;margin-left: 25px;float: left;text-align: right;"><i class="ico-arrow2"></i></div>
                                <ul class="cf">
                                    <li v-for="n in backMax" v-if="n-1 >= 2 - b_dm_n" @click="changeNum(2,n-1)">{{n-1}}</li>
                                </ul>
                            </div>
                        </transition>
                        <!--后区个数选项 end-->
                        <!--组数选项-->
                        <transition name="fade">
                            <div class="random-select" v-show="numShow" style="top: 66px;">
                                <div class="jc-info-arrow" style="float: left"><i class="ico-arrow2"></i></div>
                                <ul class="cf">
                                    <li v-for="n in randomArr" @click="changeNum(3,n)">{{n}}</li>
                                </ul>
                            </div>
                        </transition>
                        <!--组数选项 end-->
                    </div>
                </div>
                <div class="random-cont cf" v-show="randomShow">
                    <!--前区个数设置-->
                    <div class="random-btn-set">
                        <span class="random-item random-num" @click="foreShow = !foreShow;backShow=false;numShow=false"><em>{{foreNum}}</em><i class="iconfont icon-xialajiantou rotateIcon" :class="foreShow ? 'is-active' : 'no-active'"></i></span>
                        <span class="random-item">前区号码</span>
                    </div>
                    <!--前区个数设置 end-->

                    <!--后区个数设置-->
                    <div class="random-btn-set">
                        <span class="random-item random-num" @click="backShow = !backShow;foreShow=false;numShow=false"><em>{{backNum}}</em><i class="iconfont icon-xialajiantou rotateIcon" :class="backShow ? 'is-active' : 'no-active'"></i></span>
                        <span class="random-item">后区号码</span>
                    </div>
                    <!--后区个数设置 end-->

                    <!--机选组数设置-->
                    <div class="random-btn-set">
                        <span class="random-item random-num" @click="numShow = !numShow;foreShow=false;backShow=false"><em>{{randomNum}}</em><i class="iconfont icon-xialajiantou rotateIcon" :class="numShow ? 'is-active' : 'no-active'"></i></span>
                        <span class="random-item">组号码</span>
                    </div>
                    <!--机选组数设置 end-->
                </div>
            </div>
            <div class="yl-box f-mini border-top-1px tc c-3">
                <template v-if="!notes">
                    前区胆码<em class="red">{{forePlanDm.length}}</em>个,杀号<em class="red">{{foreKill.length}}</em>个,
                    后区胆码<em class="red">{{backPlanDm.length}}</em>个,杀号<em class="red">{{backKill.length}}</em>个
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
                randomArr:[1,2,5,10,20,50,100],

                foreBall:['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24',
                    '25','26','27','28','29','30','31','32','33','34','35'],//前区选号
                backBall:['01','02','03','04','05','06','07','08','09','10','11','12'],//后区选号
                forePlanDm:[],//前区胆码投注数据
                foreKill:[],//前区杀号
                backPlanDm:[],//后区胆码投注数据
                backKill:[],//后区杀号

                foreNum:5,//前区号码个数
                backNum:2,//后区号码个数
                randomNum:1,//机选几组
                randomShow: false,//机选设置
                foreShow: false,
                backShow: false,
                numShow: false,

                resForePlan:[],
                resBackPlan:[],
            }
        },
        computed:{
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            //选号区高度
            betHeight(){
                let h = this.randomShow ? 78 : 0
                return this.$store.state.clientHeight - 182 - h
            },
            //选号篮数量
            badge(){
                return this.$store.state.shuzicai.plan.length
            },
            //选号状态 flag 1前区 0后区
            // val 1 默认 2定胆 3杀号
            checkStatus(){
                return (flag,val)=>{
                    let state = 1
                    let dmObj = flag ? this.forePlanDm : this.backPlanDm
                    let killObj = flag ? this.foreKill : this.backKill
                    if(dmObj.indexOf(val) > -1){
                        state = 2
                    }
                    if(killObj.indexOf(val) > -1){
                        state = 3
                    }
                    return state
                }
            },
            f_dm_n(){
                return this.forePlanDm.length  //前区胆码数量
            },
            f_kill_n(){
                return this.foreKill.length  //前区杀号数量
            },
            b_dm_n(){
                return this.backPlanDm.length  //后区胆码数量
            },
            b_kill_n(){
                return this.backKill.length  //后区杀号数量
            },
            //前区机选最大值
            foreMax(){
                return 35 - this.f_dm_n - this.f_kill_n >= 18 ? 18 : 35 - this.f_dm_n - this.f_kill_n
            },
            //后区机选最大值(n-1)
            backMax(){
                return 13 - this.b_dm_n - this.b_kill_n
            },
            //计算注数
            notes(){
                let foreNote = this.$bet.combination(this.resForePlan.length,5);
                let backNote = this.$bet.combination(this.resBackPlan.length,2);
                return foreNote * backNote
            },
        },
        watch:{
            //前区根据胆码数量改变机选个数
            f_dm_n(val){
                this.foreNum = 5 - val
            },
            //后区根据胆码数量改变机选个数
            b_dm_n(val){
                this.backNum = 2 - val > 0 ? 2 - val : 0
            },
            //前区机选个数最大值修改
            foreMax(val){
                if(this.foreNum > val){this.foreNum = val}
            },
            //后区机选个数最大值修改
            backMax(val){
                if(this.backNum > val){this.backNum = val}
            }
        },
        methods:{
            //定胆&&杀号 flag 1 前区 0 后区
            doBet(flag,num){
                let dmObj = flag ? this.forePlanDm : this.backPlanDm
                let killObj = flag ? this.foreKill : this.backKill

                let dmStatus = dmObj.indexOf(num)
                let killStatus = killObj.indexOf(num)

                if(dmStatus == -1 && killStatus == -1){
                    if(flag){
                        if(this.f_dm_n < 4){
                            dmObj.push(num)
                        }else {
                            if(this.f_kill_n >= 30){
                                this.$messagebox('提示','前区最多只能杀30个号码')
                            }else {
                                killObj.push(num)
                            }
                        }
                    }else {
                        dmObj.push(num)
                    }
                }else if(dmStatus > -1){
                    if(flag){
                        if(this.f_kill_n >= 30){
                            this.$messagebox('提示','前区最多只能杀30个号码')
                        }else {
                            dmObj.splice(dmStatus,1)
                            killObj.push(num)
                        }
                    }else {
                        if(this.b_kill_n >= 10){
                            this.$messagebox('提示','后区最多只能杀10个号码')
                        }else {
                            dmObj.splice(dmStatus,1)
                            killObj.push(num)
                        }
                    }
                }else{
                    killObj.splice(killStatus,1)
                }
                dmObj = dmObj.sort()
                killObj = dmObj.sort()
            },
            //机选
            random(){
                for(let p=0;p<this.randomNum;p++){
                    let f = this.foreNum //前区个数
                    let b = this.backNum //后区个数
                    let fore_randomArr = this.foreBall.slice(0) //复制前区机选选项
                    let back_randomArr = this.backBall.slice(0) //复制后区机选选项
                    let temp_fore = this.forePlanDm.concat(this.foreKill) //前区胆拖+杀号
                    let temp_back = this.backPlanDm.concat(this.backKill) //后区胆拖+杀号
                    //前区机选选项数组计算
                    for(var i=0;i<fore_randomArr.length;i++){
                        for(var j=0;j<fore_randomArr.length;j++){
                            if(fore_randomArr[j]==temp_fore[i]){
                                fore_randomArr.splice(j,1);
                                j--;
                            }
                        }
                    }
                    //后区机选选项数组计算
                    for(var i=0;i<back_randomArr.length;i++){
                        for(var j=0;j<back_randomArr.length;j++){
                            if(back_randomArr[j]==temp_back[i]){
                                back_randomArr.splice(j,1);
                                j--;
                            }
                        }
                    }
                    let fore = this.$bet.getRandomArrayEle(fore_randomArr, f)
                    let back = this.$bet.getRandomArrayEle(back_randomArr, b)

                    this.$set(this,'resForePlan',fore.concat(this.forePlanDm).sort())
                    this.$set(this,'resBackPlan',back.concat(this.backPlanDm).sort())
                    let list = {}
                    list['num'] = this.resForePlan.join(',') + '|' + this.resBackPlan.sort().join(',');
                    list['notes'] = this.notes
                    list['type'] = this.type
                    list['type_text'] = this.text
                    this.$store.commit('pushSzcBetNum',list)
                }
            },
            //改变机选个数
            changeNum(flag,n){
                if(flag == 1){
                    this.$set(this,'foreNum',n)
                    this.foreShow = false
                }else if(flag == 2){
                    this.$set(this,'backNum',n)
                    this.backShow = false
                }else if(flag == 3){
                    this.$set(this,'randomNum',n)
                    this.numShow = false
                }
            },
            //清空
            clear(){
                this.$set(this,'forePlanDm',[])
                this.$set(this,'foreKill',[])
                this.$set(this,'backPlanDm',[])
                this.$set(this,'backKill',[])
                this.$set(this,'resForePlan',[])
                this.$set(this,'resBackPlan',[])
            },
            //添加到号码篮
            addBetBasket(){
                if(!this.f_dm_n && !this.b_dm_n && !this.f_kill_n && !this.b_kill_n){
                    this.$messagebox.confirm('是否按照机选条件机选号码?').then((action) => {
                        this.random()
                        this.clear(); //清空当前选号
                    });
                }else {
                    this.random()
                    this.clear(); //清空当前选号
                }
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
<style lang="scss" scoped type="text/scss">

</style>
