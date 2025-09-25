<!--签到-->
<template>
    <div>
        <div class="gradient">
            <mt-header fixed title="每日签到">
                <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)"></mt-button>
            </mt-header>
            <div class="t-height"></div>
            <div class="sign-box">
                <h3 class="tc title">您已经连续签到 <em class="f-large">{{signInfo.times}}</em> 天</h3>
                <div class="sign-content mt">
                    <div>
                        <ul class="clearfloat">
                            <li v-for="(item,index) in signInfo.intr" style="position: relative">
                                <p class="coins-border">
                                    <span class="coins-day" v-if="index < signInfo.intr.length-1">第{{index+1}}天</span>
                                    <span class="coins-day" v-if="index == signInfo.intr.length-1">{{index}}天以上</span>
                                </p>
                                <!--<p class="tc coins-img"></p>-->
                                <p class="coins-num" style=" height: 65px;line-height: 65px;">+{{item}}<em class="f-sm">{{lotteryUnit}}</em></p>
                                <template v-if="signInfo.times < signInfo.intr.length"> <!--小于n天-->
                                    <p class="sign-do" v-if="index < signInfo.times">
                                        <i class="iconfont icon-yiqiandao1"></i>
                                    </p>
                                </template>
                                <template v-else> <!--大于n天-->
                                    <template v-if="today_sign">
                                        <p class="sign-do">
                                            <i class="iconfont icon-yiqiandao1"></i>
                                        </p>
                                    </template>
                                    <template v-else>
                                        <p class="sign-do" v-if="index < signInfo.intr.length - 1">
                                            <i class="iconfont icon-yiqiandao1"></i>
                                        </p>
                                    </template>
                                </template>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="btn-box tc">
                    <mt-button size="large" v-if="today_sign" type="danger" disabled>今日已签到</mt-button>
                    <mt-button size="large" v-else type="danger" @click.native.once="signIn">立即签到</mt-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
            }
        },
        computed:{
            //签到信息
            signInfo(){
                return this.$store.state.signInfo
            },
            today_sign(){
                return this.$store.state.signInfo.today_sign
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            }
        },
        methods:{
            //签到
            signIn(){
                this.$axios.get('/index/User/sign').then(({data}) =>{
                    if(!data.err){
                        this.$messagebox('签到成功','金豆 +' + this.signInfo.intr[data.data.times - 1]);
                        this.$store.dispatch('getSignInfo') //更新签到信息
                    }else {
                        this.$messagebox('提示','签到出错！');
                    }
                }).catch(function (error) {
                    console.log(error);
                })
            },
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">
    .gradient{
        min-height: 100vh;
        background: #916efd;
        background: -moz-linear-gradient(left, #916efd 0%, #5e50f2 80%, #594df1 100%);
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#916efd), color-stop(100%,#5e50f2),color-stop(100%,#594df1));
        background: -webkit-linear-gradient(left,  #916efd 0%,#5e50f2 80%, #594df1 100%);
        background: -o-linear-gradient(left,  #916efd 0%,#5e50f2 80%,#594df1 100%);
        background: -ms-linear-gradient(left,  #916efd 0%,#5e50f2 80%,#594df1 100%);
        background: linear-gradient(to right,  #916efd 0%,#5e50f2 80%,#594df1 100%);
        //filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#000000', endColorstr='#ffffff',GradientType=0 );
    }
    :root .gradient{filter:none;}

    .mint-header{
        background:none;
    }

    .sign-box{
        .title{
            height: 35px;
            line-height: 35px;
            @include linear-gradient-deg90(#9172ff, #5d4ff9);
            background-size: cover;
            border-radius: 4px;
            color: #fbe08e;
            margin: 20px 10px 0;
        }
        .sign-info{
            color: #5ac3ff;
            padding-top: 5px;
        }
        .sign-content{
            margin: 10px 7px 0;
            padding-bottom: 8px;
            ul>li {
                float: left;
                width: 23%;
                margin: 10px 1% 0;
                text-align: center;
                background-color: #fff;
                border-radius: 5px;
                box-shadow: 0 0px 1px #2a1eb7;
                .coins-border{
                    height: 25px;
                    font-size: 14px;
                    font-weight: bold;
                    background-color: #ff4ba0;
                    color: #ffffff;
                    line-height: 25px;
                }
                .coins-img{
                    padding: 8px 0 5px;
                    height: 40px;
                }
                .coins-num{
                    font-weight: 800;
                    @include linear-gradient-deg135(#c66dfd, #2a1eb7);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    height: 25px;
                    line-height: 20px;
                }
                .sign-do{
                    position: absolute;top: 25px;width: 100%;height: 65px;line-height:65px;background-color: rgba(0,0,0,0.4);
                    font-size: 30px;color: #FFEB3B;
                    border-bottom-left-radius: 5px;
                    border-bottom-right-radius: 5px;
                    i{
                        font-size: 30px
                    }
                }
            }
        }
    }
</style>
