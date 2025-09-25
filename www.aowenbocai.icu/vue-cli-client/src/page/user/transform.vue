<template>
    <div>
        <mt-header title="转账">
            <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)"></mt-button>
            <router-link to="/transformDetail" slot="right"><div  class="f-sm">转账明细</div></router-link>
        </mt-header>
        <div class="tips">
            1{{lotteryUnit}} = {{scale}}{{gameUnit}}
        </div>
        <div class="transform-box contentH" :style="{height: contentH + 'px'}">
            <div class="icon mt" @click="type = type == 1 ? 2 : 1"><i class="iconfont icon-zhuanhuan"></i> 交换</div>
            <div>
                <a class="mint-cell mint-field">
                    <div class="mint-cell-wrapper"><div class="mint-cell-title">
                        <span class="mint-cell-text">转出账户</span>
                    </div>
                        <div class="mint-cell-value">
                            <span v-if="type == 1">
                                <em>账户余额</em> <em class="f-sm">(余额:{{money}}{{lotteryUnit}})</em>
                            </span>
                            <span v-if="type == 2">
                                 <em>游戏账户</em> <em class="f-sm">(余额:{{gameMoney}}{{gameUnit}})</em>
                            </span>
                        </div>
                    </div>
                </a>
                <a class="mint-cell mint-field">
                    <div class="mint-cell-wrapper">
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">转入账户</span>
                        </div>
                        <div class="mint-cell-value">
                            <span v-if="type == 1">
                                <em>游戏账户</em> <em class="f-sm">(余额:{{gameMoney}}{{gameUnit}})</em>
                            </span>
                            <span v-if="type == 2">
                                <em>账户余额</em> <em class="f-sm">(余额:{{money}}{{lotteryUnit}})</em>
                            </span>
                        </div>
                    </div>
                </a>
                <mt-field label="转账数额" placeholder="请输入转账数额" :value = "num" type="text" @change="checkNum" class="num"></mt-field>
            </div>
            <div class="fast-chsoe mt tr"><span class="c-3 f-sm">快捷选择：</span>
                <mt-button size="small" @click.native="choseMoney(type == 1 ? money : gameMoney)">全部</mt-button>
                <mt-button size="small" @click.native="choseMoney(type==1? 100 : scale)">{{type==1? 100 : scale}}</mt-button>
                <mt-button size="small" @click.native="choseMoney(type==1? 1000 : scale * 10)">{{type==1? 1000 : scale * 10}}</mt-button>
                <mt-button size="small" @click.native="choseMoney(type==1? 10000 : scale * 100)">{{type==1? 10000 : scale * 100}}</mt-button>
            </div>
            <div class="btn-box">
                <mt-button @click.native="sumbit" type="danger" size="large">
                    <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                    确认转账
                </mt-button>
            </div>
            <div class="tc red">{{err}}</div>
            <div class="tc suc">{{sucMsg}}</div>
        </div>
    </div>
</template>
<script>
    export default{
        name:'transform',
        data() {
            return {
                loading: false,

                type: 1 ,//转账模式 1 账户=>游戏 2游戏=>账户
                num: '',//转账数额
                err:'',
                sucMsg:''
            };
        },
        watch:{
            type (val) {
                this.checkNum(this.num);
            }
        },
        computed:{
            //话费选项高度
            contentH(){
                return this.$store.state.clientHeight - 70
            },
            //游戏币比例
            scale(){
                return this.$store.state.setting.recharge_award
            },
            //游戏账户余额
            gameMoney(){
                return this.$store.state.userinfo.game_money
            },
            //账户余额
            money(){
                return this.$store.state.userinfo.money
            },
            gameUnit(){
                return this.$store.state.setting.game_unit
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            }
        },
        methods:{
            //验证输入数额合法性
            checkNum (val) {
                this.err = ''
                this.sucMsg = ''
                if(val){
                    var n = this.scale.length - 1
                    this.num = this.type == 1 ? this.$base.formateSmallNumber(val,n) : parseInt(val); //保留n位小数
                    var moneyObj = {
                        1 : this.money,
                        2 : this.gameMoney
                    }
                    if(Number(this.num) > moneyObj[this.type]){
                        this.err =  '账户余额不足'
                        return
                    }
                    if(Number(this.num) <= 0){
                        this.err =  '金额不能为0'
                        return
                    }
                    this.sucMsg = this.type == 1 ? "可转换" + this.$base.formatMoney(this.$bet.accMul(this.num,this.scale)) + this.gameUnit :
                        "可转换" + this.$base.formatMoney(this.$bet.accDiv(this.num,this.scale)) +  this.lotteryUnit
                }
            },
            //快捷设置数额
            choseMoney (val) {
                this.num = val
                this.checkNum(this.num);
            },
            //清空
            clear () {
                this.num = '',
                this.err = ''
                this.sucMsg = ''
            },
            //确认转账
            sumbit(){
                if(this.err.length){
                    this.$messagebox('提示',this.err);
                    return
                }
                this.loading = true
                this.$axios.post('/web/user/transform',{
                    type: this.type,
                    num : this.num
                }).then(({data}) =>{
                    if(!data.err){
                        this.$store.dispatch('getUserInfo'); //更新个人信息
                        this.clear();
                    }else {

                    }
                    this.$messagebox('提示',data.msg);
                    this.loading = false
                }).catch(function (error) {
                    console.log(error);
                })
            }
        },
        created(){
            this.$store.commit('setKeepAlivePage','transform')
        },
        beforeRouteLeave(to, from, next){
            if(to.path =='/transformDetail' || to.path == '/pay'){
                this.$store.commit('setKeepAlivePage','transform')
            }else {
                this.$store.commit('delKeepAlivePage','transform')
            }
            next();
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .transform-box{
        .icon{
            padding: 0 10px 5px;
            i{
                color: $Danger;
            }
        }
        .fast-chsoe{
            padding: 0 10px;
        }
    }
</style>
