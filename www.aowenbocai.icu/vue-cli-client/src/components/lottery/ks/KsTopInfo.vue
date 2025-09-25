<template>
    <div>
        <!--倒计时+ 期号-->
        <div class="ks-time flex-box">
            <div class="flex ks-kjCode" @click="recentShow = !recentShow">
                <div class="tc kj-title"><em v-if="expectType">{{lastIssue}}</em><em v-else>{{lastIssue | kjIssue}}</em>期开奖 :
                    <em v-if="!isGetCode">{{awardNumber[0]}}</em><em v-else>-</em>
                    <em v-if="!isGetCode">{{awardNumber[1]}}</em><em v-else>-</em>
                    <em v-if="!isGetCode">{{awardNumber[2]}}</em><em v-else>-</em>
                </div>
                <div class="tc flex-box re-open" style="justify-content: center">
                    <span v-if="!isGetCode" :class="!isGetCode ? 'open-dice dice-lg-'+ awardNumber[0] : ''"></span>
                    <span v-else class="open-dice rDice" :style="{backgroundPositionY: -positionY * 28 + 'px'}"></span>

                    <span v-if="!isGetCode" :class="!isGetCode ? 'open-dice dice-lg-'+ awardNumber[1] : ''"></span>
                    <span v-else class="open-dice rDice" :style="{backgroundPositionY: -positionY * 28 + 'px'}"></span>

                    <span class="dice-last" v-if="!isGetCode" :class="!isGetCode ? 'open-dice dice-lg-'+ awardNumber[2] : ''"><i class="iconfont icon-jiantou" :class="recentShow ? 'is-active':'no-active'" style="color: #7aaf93;"></i></span>
                    <span v-else class="open-dice rDice dice-last" :style="{backgroundPositionY: -positionY * 28 + 'px'}"><i class="iconfont icon-jiantou" :class="recentShow ? 'is-active':'no-active'" style="color: #7aaf93;"></i></span>
                </div>
            </div><!--近期开奖-->
            <div class="flex ks-downTime">
                <div class="tc kj-title tc">距 {{sortExpect}} 期截止</div>
                <div class="kj-time tc">{{timer}}</div>
            </div><!--倒计时-->
        </div>
        <!--倒计时+ 期号 end-->
        <!--近期开奖-->
        <div class="recent-open-box ks-recent-open-box" v-show="recentShow">
            <div class="layout" @click.self="recentShow = false" v-show="recentShow">
                <table cellpadding="0" cellspacing="0" class="ks-table-list">
                    <tr>
                        <th width="20%">期号</th>
                        <th width="22%">开奖号码</th>
                        <th width="13%">和值</th>
                        <th width="20%">形态</th>
                        <th width="20%">类型</th>
                    </tr>
                    <tr v-for="(item,index) in codeArr" :key="index">
                        <td v-if="!expectType">{{item.expect | subStr}}</td>
                        <td v-else>{{item.expect}}</td>
                        <td>
                            <b>{{item.code[0]}}</b> ,
                            <b>{{item.code[1]}}</b> ,
                            <b>{{item.code[2]}}</b>
                        </td>
                        <td>{{item.he}}</td>
                        <td>
                            <em :class="item.xt[0] == '大' ? 'da' : item.xt[0] == '小' ? 'xiao' : ''">{{item.xt[0]}}</em>
                            <em :class="item.xt[1] == '单' ? 'dan' : item.xt[1] == '双' ? 'shuang' : ''">{{item.xt[1]}}</em>
                        </td>
                        <td>{{item.lx}}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
                recentShow:false,//近期开奖
                positionY: 0,//开奖动画
            }
        },
        filters:{
            subStr(val){
                return val.toString().slice(4);
            },
            kjIssue(val){
                return val.toString().slice(8);
            }
        },
        computed:{
            //期号类型
            expectType(){
                return this.$store.state.lottery.expect_type
            },
            name(){ //游戏名eg:gd11
                return this.$route.query.name
            },
            cz(){
                let path = this.$route.path.replace('/','')
                return path == 'ks' ? path : this.$route.query.cz
            },
            //当期剩余时间
            downTime(){
                return this.$store.state.lottery.info.time
            },
            //当期期号（短）
            sortExpect(){
                return this.$store.state.lottery.info.sort_expect
            },
            //当期期号
            expect(){
                return this.$store.state.lottery.info.expect
            },
            //上一期期号
            lastIssue(){
                return this.$store.state.lottery.info.lastIssue
            },
            //倒计时显示格式化
            timer(){
                return this.downTime == 0 ? '00:00' : this.$bet.formatTime('i:s',this.downTime)
            },
            intervalTime(){
                return this.$store.state.intervalTime
            },
            url(){
                return '/index/' + this.cz + '/getIssueInfo/name/'+ this.name
            },
            //几分钟一期
            timelong(){
                return this.$store.state.lottery.timelong
            },
            //近期开奖数组
            codeArr(){
                return this.$store.state.lottery.recent_open
            },
            isGetCode(){
                return this.$store.state.lottery.isGetCode || false
            },
            //当前开奖号码
            awardNumber(){
                return this.$store.state.lottery.info.awardNumber.split(',')
            },
        },
        watch:{
            //监听倒计时为零时获取新期号信息
            downTime(val){
                if(val == 0 && val !== ''){
                    this.$store.commit('clearNewCode')  //清除获取开奖号码定时器
                    this.$store.commit('clearRandomNum')  //清除开奖动画
                    this.$axios(this.url).then(({data})=>{
                        this.$store.commit('setBetData',data.data)
                        this.$emit('change-time') //触发追号内容重写
                        this.$messagebox('提示','期次已更改，当前第' + this.expect + '期')
                        setTimeout(()=>{
                            this.$messagebox.close();
                        },10000)
                        this.$store.commit('setAwardNumber',data.data.awardNumber.code); //更新最新开奖号码状态
                        this.$store.commit('setRecentOpen',data.data.open) //更新近10期开奖数组
                        this.$store.commit('isGetNewCode',data.data.getnewcode);
                        this.$store.commit('setType',data.data.expect_type);
                        this.$store.commit('setFirstIssue',data.data.firstIssue);

                        this.rDiceMove(); //执行开奖动画

                        let timelong = data.data.timelong;
                        let j = timelong < 5 ? 10000 : 60000 //j秒后开始获取开奖号码
                        var ctime = setTimeout(()=>{
                            this.getNewCode();
                            clearTimeout(ctime);
                        },j)
                    })
                }
            }
        },
        methods:{
            //倒计时
            timeInterval(){
                this.$store.state.intervalTime = setInterval(()=>{
                    this.$store.commit('setDownTime');
                },1000)
            },
            //骰子动画
            rDiceMove(){
                this.$store.state.rNumTimer = setInterval(() => {
                    if(this.positionY == 3){
                        this.positionY = 0
                    }else {
                        this.positionY++;
                    }
                }, 100);
            },
            //获取开奖号码
            getNewCode(){
                let timer = this.timelong < 5 ? 5000 : 10000; //每隔timer秒获取一次
                this.$store.state.newCodeFun = setInterval(()=>{
                    this.$axios.get('/index/'+ this.cz + '/getNewCode',{
                        params:{
                            name:this.name,
                            issue: this.lastIssue
                        }
                    }).then(({data})=>{
                        if(!data.err){
                            this.$store.commit('setRecentOpen',data.data.tenCode);//更新近10期开奖号码组
                            this.$store.commit('setAwardNumber',data.data.codeOpen); //更新最新一期开奖号码
                            this.$store.commit('clearNewCode')  //清除获取开奖号码定时器
                            this.$store.commit('clearRandomNum')  //清除开奖动画
                            this.$store.commit('isGetNewCode',false);
                        }
                    }).catch(({error})=>{
                        console.log(error)
                    })
                },timer)
            },
            //初始数据
            initData(){
                if(this.$route.path == '/betOrder'){
                    this.$axios(this.url).then(({data})=>{
                        this.$store.commit('setBetData',data.data)
                        this.$store.commit('setAwardNumber',data.data.awardNumber.code)
                        this.$store.commit('setRecentOpen',data.data.open);
                        this.$store.commit('isGetNewCode',data.data.getnewcode);
                        this.$store.commit('clearDownTime'); //清除倒计时
                        this.timeInterval();
                        if(data.data.getnewcode){
                            this.$store.commit('clearNewCode'); //清除获取开奖号码
                            this.$store.commit('clearRandomNum')  //清除开奖动画
                            this.getNewCode();
                            this.rDiceMove();
                        }
                    }).catch(({error})=>{
                        console.log(error)
                    })
                }else {
                    this.$store.commit('clearDownTime'); //清除倒计时
                    this.timeInterval();
                    if(this.$store.state.lottery.isGetCode){
                        this.$store.commit('clearNewCode'); //清除获取开奖号码
                        this.$store.commit('clearRandomNum')  //清除开奖动画
                        this.getNewCode();
                        this.rDiceMove();
                    }
                }
            }
        },
        created(){
            this.initData();
        },
        activated(){
            this.$store.commit('clearRandomNum')  //清除开奖动画
            this.$store.commit('clearNewCode')  //清除获取开奖号码定时器
            if(this.$store.state.lottery.isGetCode){
                this.rDiceMove();
                this.getNewCode();
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .t-height{
        height: 100px;
    }
    .loading{
        width: 28px;
        height: 28px;
        margin: 30px auto;
    }
    $ks-color-base:#3f7555;
    $ks-color-pull:#36674b;
    $ks-color-dark: #184612;
    $ks-color-black: #252625;
    $ks-border-color:#74a78d;
    $ks-color-cont:#549263;
    $ks-color-yellow:#eaca52;
    .c-light{
        color: #caebda;
    }
    .c-light-2{
        color: #9bdbc2;
    }
    .lottery-ks{
        color: #ffffff;
        background-color:$ks-color-base;
        .icon-jiantou{
            color: #f8d447;
        }
        .mint-header{
            background-color: $ks-color-black;
        }
        .head-ks{
            .select-grounp{
                background-color: $ks-color-pull;
                border-bottom: 2px solid $ks-color-dark;
                padding: 5px;
                li{
                    width: 33.3333%;
                    float:left;
                    margin: 5px 0;
                    text-align: center;
                    a{
                        display: inline-block;
                        width: 87%;
                        background-color: #457a53;
                        border:2px solid $ks-border-color;
                        color: #ffffff;
                        height: 75px;
                        @include rounded(8px);
                        line-height: 20px;
                        position: relative;
                        &.active{
                            background-color: #305d3f;
                            border-color: $ks-color-yellow;
                            .gou{
                                position: absolute;
                                color: $ks-color-yellow;
                                font-size: 18px;
                                bottom: 0;
                                right: 1px;
                            }
                        }
                        .play-name{
                            font-size: 15px;
                            padding-top: 5px;
                        }
                        .play-gain{
                            color: #add3b9;
                        }
                        .p1{
                            position: relative;
                        }
                        .p1:after{
                            content: "+";
                            position: absolute;
                            font-size: 13px;
                            top: 2px;
                            right: -5px;
                        }
                    }
                }
            }
        }
        /*倒计时+近期开奖*/
        .ks-time{
            width: 100%;
            top: 40px;
            background-color: #36674b;
            border-bottom: 1px solid $ks-color-dark;
            .ks-kjCode{
                border-right: 1px solid $ks-color-dark;
                height: 60px;
                .dice-last{
                    position: relative;
                    i{
                        position: absolute;
                        right: -25px;
                        top: 3px;
                        font-size: 16px;
                        -webkit-transition: all 0.25s ease-in-out;
                        -moz-transition: all 0.25s ease-in-out;
                        -o-transition: all 0.25s ease-in-out;
                        transition: all 0.25s ease-in-out;
                        display: inline-block;
                        &.is-active{
                            -webkit-transform: rotate(180deg);
                            -moz-transform: rotate(180deg);
                            -ms-transform:rotate(180deg);
                            -o-transform:rotate(180deg);
                            transform: rotate(180deg);
                        }
                        &.no-active{
                            transform: rotate(0deg);
                        }
                    }
                }
                .open-dice{
                    width: 28px;
                    height: 28px;
                }
            }
            .ks-downTime{
                height: 60px;
                border-left: 1px solid #42795a;
            }
            .kj-title{
                padding: 4px 0 2px;
                color: #cae2d0;
                font-size: 15px;
            }
            .kj-time{
                font-size:26px;
                color: #ffffff;
                padding-top: 5px;
                letter-spacing: 3px;
                font-weight: 600;
            }
        }
    }
    .open-dice{
        display: inline-block;
        width: 26px;
        height: 26px;
        text-align: center;
        background: url("~assets/images/m_open_num.png") no-repeat top left;
        background-size: 200% 600%;
        margin-top: 2px;
    }
    $height:-26px;
    .dice1{
        background-position:0 0;
    }
    .dice2{
        background-position:0 $height;
    }
    .dice3{
        background-position:0 $height * 2;
    }
    .dice4{
        background-position:0 $height * 3;
    }
    .dice5{
        background-position:0 $height * 4;
    }
    .dice6{
        background-position:0 $height * 5;
    }
    .dice-lg-1{
        background-position:0 0;
    }
    $height-lg:-28px;
    .dice-lg-2{
        background-position:0  $height-lg;
    }
    .dice-lg-3{
        background-position:0  $height-lg * 2;
    }
    .dice-lg-4{
        background-position:0 $height-lg * 3;
    }
    .dice-lg-5{
        background-position:0 $height-lg * 4;
    }
    .dice-lg-6{
        background-position:0 $height-lg * 5;
    }
    .rDice{
        background-position:100% 0;
    }

    .bet-item-tips{
        padding: 0 15px;
    }
    .bet-item{
        /*padding: 20px 0 0;*/
    }
    //table 列表样式
    .ks-recent-open-box{
        top: 100px;
        .layout{
            position: fixed;
            top:100px;
            width: 100%;
            height: 100%;
            z-index: 50;
            left: 0;
        }
    }
    .ks-table-list{
        width: 100%;
        tr{
            td,th{
                font-size: 12px;
                text-align: center;
                padding: 3px 0;
                border:solid #396d4f;
                border-width: 0 .5px .5px 0;
                color: #ffffff;
                &:last-child{
                    border-width: 0 0 .5px 0;
                }
            }
            td{
                background-color: #2a523b;
            }
            th{
                padding: 5px 0;
                background-color:#2a523b;
            }
            &:nth-child(even) td{
                background-color: #2f5d42;
            }
        }
        em{
            display: inline-block;
            width: 22px;
            height: 22px;
            line-height: 22px;
            border-radius: 3px;
            background-color: #2f5d42;
            margin: 0 3px;
            &.xiao , &.shuang{
                background-color: #FF9800;
            }
            &.da , &.dan{
                background-color: #2196F3;
            }
        }
    }
</style>
