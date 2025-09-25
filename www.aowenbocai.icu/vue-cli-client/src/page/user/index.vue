<template>
    <div>
        <div class="user">
            <div class="header card-pd">
                <div class="top tr">
                    <router-link to="/setting"><i class="iconfont icon-shezhi1" style="color: #ffffff"></i></router-link>
                </div>
                <div class="sign" v-if="signIsOpen">
                    <router-link to="/signIn" class="flex-box">
                        <i class="iconfont icon-qiandao" style="padding-right: 5px"></i>
                        <span v-if="!today_sign" class="red-point tips-point"></span>
                        <em class="f-sm" v-if="!today_sign">签到</em>
                        <em class="f-sm" v-else>已签到</em>
                    </router-link>
                </div>
                <div class="tc">
                    <p class="name f-large" style="margin-top: 20px;font-size: 20px">{{userinfo.username}}</p>
                    <p v-if="userinfo.photo" class="photo" :style="{backgroundImage:'url(' + userinfo.photo + ')'}"></p>
                    <p><em class="yellow f-mini"><i class="iconfont icon-huangguan-copy"></i> {{userinfo.user_grade.userGrade}}</em></p>
                </div>
                <div class="info-box" :class="{'info-box-alone' : !gameStatus}">
                    <div class="flex-box mt-sm money">
                        <div class="flex tc">
                            <p class="f-large c-1"> {{userinfo.money}}</p>
                            <p class="f-sm c-3">账户余额 ({{lotteryUnit}})</p>
                        </div>
                        <div class="flex tc" v-if="gameStatus">
                            <p class="f-large c-1">{{userinfo.game_money}}</p>
                            <p class="f-sm c-3">游戏账户 ({{gameUnit}})</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-box link">
                <router-link to="/pay"  class="router-link flex tc border-right-1px"><i class="iconfont icon-chongzhi4" style="color: #ffba5b"></i>充值</router-link>
                <a @click="checkTel" class="router-link flex tc border-right-1px"><i class="iconfont icon-duihuan1" style="color: #8038ff"></i>兑换</a>
                <router-link v-if="gameStatus" to="/transform" class="router-link flex tc"><i class="iconfont icon-zhuanru" style="color: rgb(231, 74, 69)"></i>转账</router-link>
            </div>
            <div class="cell mf-sm">
                <p class="mt-sm">
                    <mt-cell title="资金明细" is-link to="/detail">
                        <i slot="icon" class="iconfont icon-zhangdanmingxi-xiugai"></i>
                    </mt-cell>
                    <mt-cell v-if="lotteryStatus" title="投注记录" is-link to="/lotteryRecord">
                        <i slot="icon" class="iconfont icon-touzhujilu"></i>
                    </mt-cell>
                    <mt-cell v-if="lotteryStatus" title="追号记录" is-link to="/chaseRecord">
                        <i slot="icon" class="iconfont icon-zhuihaodingdan"></i>
                    </mt-cell>
                    <mt-cell v-if="gameStatus" title="游戏记录" is-link :to="{name:'gameRecord',params:{gameid:gameId}}">
                        <i slot="icon" class="iconfont icon-youxi-copy"></i>
                    </mt-cell>
                    <mt-cell title="盈亏统计" is-link to="/statis">
                        <i slot="icon" class="iconfont icon-shuju"></i>
                    </mt-cell>
                </p>
                <p class="mt-sm" v-if="userinfo.type == 0 || userinfo.type == 2">
                    <mt-cell title="代理中心" is-link :to="'/agent?redirect='+ this.$route.path">
                        <i slot="icon" class="iconfont icon-daili"></i>
                    </mt-cell>
                </p>
                <p class="mt-sm">
                    <mt-cell title="消息中心" is-link to="/systemMsg">
                        <i slot="icon" class="iconfont icon-xiaoxi"></i>
                        <span>未读消息 <mt-badge size="small">{{msgNum}}</mt-badge></span>
                    </mt-cell>
                </p>
                <p class="mt-sm">
                    <mt-cell v-if="gameStatus" title="我的背包" is-link to="/bag">
                        <i slot="icon" class="iconfont icon-menpiao"></i>
                    </mt-cell>
                    <mt-cell title="最近玩过" is-link to="/recPlay">
                        <i slot="icon" class="iconfont icon-dengdai"></i>
                    </mt-cell>
                </p>
                <p class="mt-sm">
                    <mt-cell title="联系我们" is-link to="/contact">
                        <i slot="icon" class="iconfont icon-kefu1"></i>
                    </mt-cell>
                </p>
            </div>
            <!--<div class="b-height mt-sm"></div>-->
        </div>
    </div>
</template>

<script>
    export default {
        name: 'user',
        data () {
            return {
                signIsOpen : false
            }
        },
        computed:{
            userinfo(){
                return this.$store.state.userinfo
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            gameUnit(){
                return this.$store.state.setting.game_unit
            },
            today_sign(){
                return this.$store.state.signInfo.today_sign
            },
//            signIsOpen(){
//                return this.$store.state.signInfo.intr.length ? true : false
//            },
            msgNum(){
                return this.$store.getters.getMsgNum
            },
            gameId(){
                return this.$store.state.gameNav[0].id
            },
            lockInfo(){
                return this.$store.state.lockInfo
            },
            //屏幕高度
            contentH(){
                return this.$store.state.clientHeight - 95
            },
            //彩票开关
            lotteryStatus(){
                return Number(this.$store.state.setting.lottery_status)
            },
            //游戏开关
            gameStatus(){
                return Number(this.$store.state.setting.game_status)
            },
            trueState(){
                return this.$store.state.trueState
            },
            bankList(){
                return this.$store.state.lockBank.banks
            },
            alipayList(){
                return this.$store.state.lockBank.alipay
            },
            weiList(){
                return this.$store.state.lockBank.wei
            },
            bind_idcard(){//是否需要绑定身份证
                return Number(this.$store.state.setting.bind_idcard) ? true : false
            },
            //兑换条件剩余消费金额
            rechargeMoney(){
                return Number(this.$store.state.userinfo.recharge_money)
            }
        },
        methods:{
            checkTel(){
                this.$store.commit('setLoadStatus',true)
                this.$axios('/index/user/getinfo').then(({data}) => {
                    this.$store.commit('setUserInfo', data.data);
                    this.$store.commit('setLoadStatus',false)
                    if(this.rechargeMoney > 0){
                        this.$messagebox('提示','您还需消费'+ this.rechargeMoney + this.lotteryUnit + '才能兑换，投注的消费需要等到开奖后计入')
                        return
                    }
                    if(this.lockInfo.tel == 1){
                        this.$messagebox.confirm(
                            '您还未绑定手机号，立即去绑定?'
                        ).then(()=>{
                            this.$router.push({
                                path:'/setTel'
                            })
                        }).catch((err)=>{
                        });
                        return
                    }
                    if(this.bind_idcard){
                        if(this.lockInfo.is_num == 0){
                            this.$messagebox.confirm(
                                '您还未进行实名认证，立即去认证?'
                            ).then(()=>{
                                this.$router.push({
                                    path:'/realName'
                                })
                            }).catch((err)=>{
                            });
                            return
                        }
                    }else {
                        if(this.lockInfo.is_name == 0){
                            this.$messagebox.confirm(
                                '您还未完善个人资料，立即去完善?'
                            ).then(()=>{
                                this.$router.push({
                                    path:'/setInfo'
                                })
                            }).catch((err)=>{
                            });
                            return
                        }
                    }
//                if(!Number(this.lockInfo.bank_open) && !Number(this.lockInfo.wx_open) && !Number(this.lockInfo.zfb_open)){
//                    this.$messagebox.confirm(
//                        '您还绑定任何银行卡/微信/支付宝账号，立即去绑定?'
                    if(!Number(this.lockInfo.bank_open) && !Number(this.lockInfo.zfb_open)){
                        this.$messagebox.confirm(
                            '您还绑定任何银行卡/支付宝账号，立即去绑定?'
                        ).then(()=>{
                            this.$router.push({
                                path:'/lockCard'
                            })
                        }).catch((err)=>{
                        });
                        return
                    }
                    this.$router.push({
                        path:'/exchange'
                    })
                });
            }
        },
        activated(){
            this.$store.dispatch('getUserInfo');
            this.$store.dispatch('getSystemMsg');
//            this.$store.dispatch('getSignInfo'); //获取签到信息
            this.$axios('/index/User/signbefore').then(({data}) => {
                this.$store.commit('setSignInfo', data.data);
                this.$set(this,'signIsOpen',data.data.intr.length ? true : false)
            });
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">
    .user{
        .header{
            height: 210px;
            background: url(~@/assets/images/bg-tm.png) no-repeat bottom center #e74a45;
            background-size: contain;
            position: relative;
            color: #ffffff;
            margin-bottom: 40px;
            .top{
                position: absolute;
                top:20px;
                right: 15px;
            }
            .sign{
                position: absolute;
                left: 15px;
                top: 15px;
                height: 36px;
                line-height: 36px;
                padding: 0 15px;
                background-color: rgba(255, 255, 255, 0.37);
                border-radius: 18px;
                @include shadow(0,2px,5px, #d2413c);
                a{
                    color: #ffffff;
                }
                img{width: 20px;padding-right: 2px}
                .tips-point{
                    position: absolute;
                    right: 5px;
                    top: 5px;
                }
            }
            i{
                font-size: 20px;
            }
            .photo{
                width: 100px;
                height: 100px;
                border-radius: 50%;
                background-size: cover;
                margin-bottom: pxTorem(5);
                border:4px solid #ffffff;
                @include shadow(0,2px,5px, rgba(241, 74, 69, 0.67));
                position: absolute;
                top: 80px;
                left: 50%;
                margin-left: -50px;
                z-index: 1000;
            }
            .money{
                line-height: 1.8;
            }
        }
        .link{
            box-sizing: content-box;
            padding:7px 0;
            height: 34px;
            line-height: 34px;
            background-color: #ffffff;
            font-size: 18px;
            width: 96%;
            margin: 0 2%;
            border-radius: 5px;
            i{
                font-size: 20px;
                padding-right: pxTorem(8);
            }
            a.router-link{
                display: flex;
                justify-content: center;
            }
        }

        .info-box{
            position: absolute;
            top:140px;
            left: 0;
            background-color: #ffffff;
            @include shadow(0,1px,5px, #fdc2c1);
            width: 96%;
            margin: 0 2%;
            height: 100px;
            border-radius: 12px;
            z-index: 10;
            padding-top: 20px;
        }
        .info-box-alone{
            padding-top: 38px;
            .money{
                line-height: 1.5;
            }
        }
    }

</style>
