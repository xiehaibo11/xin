<!--双色球 标准选号-->
<template>
    <div>
        <div class="contentH" :style="{'height':betHeight + 'px'}">
            <div class="fore">
                <div class="flex-box head">
                    <span class="c-2">红球区</span>
                    <span class="f-sm flex tl ml-sm c-3">至少选6个</span>
                    <div class="random-btn">
                        <span class="random-item random-num" @click="foreShow = !foreShow">{{foreNum}} <i class="iconfont icon-xialajiantou rotateIcon" :class="foreShow ? 'is-active' : 'no-active'"></i></span>
                        <span class="random-item" @click="random(1)">机选</span>
                    </div>
                    <!--红球区机选选项-->
                    <transition name="fade">
                        <div class="random-select" v-show="foreShow">
                            <div class="jc-info-arrow"><i class="ico-arrow2"></i></div>
                            <ul class="cf">
                                <li v-for="n in 18" v-if="n>=6" @click="changeNum(1,n)">{{n}}</li>
                            </ul>
                        </div>
                    </transition>
                    <!--红球区机选选项 end-->
                </div>
                <div class="fore-bet-wrapper bet-box">
                    <ul class="balls-box">
                        <li v-for="(n,i) in foreBall">
                            <a class="balls" :class="{'selected' : checkForePlan(n)}" @click="doBet(1,n)">{{n}}</a>
                            <i class="c-3 f-mini" :class="{'red':miss.fore[i] > 26}">{{miss.fore[i]}}</i>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="after">
                <div class="flex-box head">
                    <span class="c-2">篮球区</span>
                    <span class="f-sm flex tl ml-sm c-3">至少选1个</span>
                    <div class="random-btn">
                        <span class="random-item random-num" @click="backShow = !backShow">{{backNum}} <i class="iconfont icon-xialajiantou rotateIcon" :class="backShow ? 'is-active' : 'no-active'"></i></span>
                        <span class="random-item" @click="random(0)">机选</span>
                    </div>
                    <!--篮球区机选选项-->
                    <transition name="fade">
                        <div class="random-select" v-show="backShow">
                            <div class="jc-info-arrow"><i class="ico-arrow2"></i></div>
                            <ul class="cf">
                                <li v-for="n in 16" @click="changeNum(0,n)">{{n}}</li>
                            </ul>
                        </div>
                    </transition>
                    <!--篮球区机选选项 end-->
                </div>
                <div class="back-bet-wrapper bet-box">
                    <ul class="balls-box">
                        <li v-for="(n,i) in backBall">
                            <a class="balls" :class="{'selected' : checkBackPlan(n)}" @click="doBet(0,n)">{{n}}</a>
                            <i class="c-3 f-mini" :class="{'red':miss.back[i] > 26}">{{miss.back[i]}}</i>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--底部 start-->
        <div class="bet-foot">
            <div class="yl-box f-mini border-top-1px tc c-3">
                <template v-if="!notes">
                    红球区至少选择6个号码，篮球区至少选择1个号码
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
                <div class="tc bet-basket" style="margin: 0 16px 0 6px">
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
                foreShow: false,//红球区机选选择
                foreNum: 6, //红球区默认机选个数
                backShow: false,//篮球区机选选择
                backNum: 1, //篮球区默认机选个数
                foreBall:['01','02','03','04','06','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24',
                    '25','26','27','28','29','30','31','32','33'],
                backBall:['01','02','03','04','06','06','07','08','09','10','11','12','13','14','15','16'],
                forePlan:[],//红球区投注数据
                backPlan:[],//篮球区投注数据
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
            //红球区选号状态
            checkForePlan(){
                return (val)=>{
                    return this.forePlan.indexOf(val) > -1 ? true : false
                }
            },
            //篮球区选号状态
            checkBackPlan(){
                return (val)=>{
                    return this.backPlan.indexOf(val) > -1 ? true : false
                }
            },
            //计算注数
            notes(){
                let foreNote = this.$bet.combination(this.forePlan.length,6);
                let backNote = this.$bet.combination(this.backPlan.length,1);
                return foreNote * backNote
            },
        },
        methods:{
            //选号
            doBet(flag,num){
                let obj = flag ? this.forePlan : this.backPlan
                if(obj.indexOf(num) > -1){
                    obj.splice(obj.indexOf(num),1)
                }else {
                    obj.push(num)
                }
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
                this.$set(this,'forePlan',[])
                this.$set(this,'backPlan',[])
            },
            //添加到号码篮
            addBetBasket(){
                if(this.notes < 1){
                    this.$messagebox('提示','至少选择1注投注号码！')
                    return
                }
                let list = {}
                list['num'] = this.forePlan.sort().join(',') + '|' + this.backPlan.sort().join(',');
                list['notes'] = this.notes
                list['type'] = this.type
                list['type_text'] = this.text
                this.$store.commit('pushSzcBetNum',list)
                this.clear(); //清空当前选号
            },
            // 立即投注
            submitOrder(){
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

