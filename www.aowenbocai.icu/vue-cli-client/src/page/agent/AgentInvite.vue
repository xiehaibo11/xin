<template>
    <div>
        <header class="mint-header">
            <div class="mint-header-button is-left"  @click="goBack">
                <button class="mint-button mint-button--default mint-button--normal">
                    <span class="mint-button-icon"><i class="mintui mintui-back"></i></span>
                </button>
            </div>
            <h1 class="mint-header-title">
                <ul class="top-tab cf">
                    <li class="active">下级开户</li>
                    <router-link  :to="'/agent/inviteCode?redirect='+ this.$route.query.redirect" tag="li">邀请码</router-link>
                </ul>
            </h1>
            <div class="mint-header-button is-right"></div>
        </header>
        <div class="contentH" :style="{height:contentH + 'px'}">
            <div class="card mt invite-head">
                <div class="flex-box invite-item">
                    <div class="label">开户类型</div>
                    <div class="flex-box invite-radio" @click="type = 2">
                        <span class="mint-radio">
                            <input type="radio" class="mint-radio-input" :checked="type == 2 ? 'checked' : '' ">
                            <span class="mint-radio-core"></span>
                        </span>
                        <span class="mint-radio-label">代理类型</span>
                    </div>
                    <div class="flex-box invite-radio" @click="type = 1">
                        <span class="mint-radio">
                            <input type="radio" class="mint-radio-input" :checked="type == 1 ? 'checked' : '' ">
                            <span class="mint-radio-core"></span>
                        </span>
                        <span class="mint-radio-label">玩家类型</span>
                    </div>
                </div>
                <div class="invite-item">
                    请先为下级设置返点，<router-link to='/agent/rebateOdds'>点击查看返点赔率表 </router-link>
                </div>
            </div>
            <div class="invite-rebate-set card mt input-auto">
                <template v-for="(item,index) in lotteryList">
                    <mt-field :key="index"
                              :label="item.label"
                              :placeholder="'自身返点'+ userRebate[item.type] + ',可设置返点0.0-' + userRebate[item.type]"
                              v-model="setRebate[item.type]"
                              type="number"
                              :attr="{step: 0.1 ,min:0.0, max:userRebate[item.type]}"  @change="change(item.type,setRebate[item.type],userRebate[item.type])">
                    </mt-field>
                </template>
            </div>
            <div class="btn-box">
                <mt-button size="large" type="danger" @click="addInviteCode">生成邀请码</mt-button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'agentInvite',
        data () {
            return {
                active : '1', //顶部tab
                type : 2, //开户类型 1玩家类型 2代理类型
                setRebate:{ //返点设置
                    ssc: '',
                    ks : '',
                    syxw : '',
                    pk10 : '',
                    pc28 : ''
                }
            }
        },
        computed:{
            title(){
                return this.$route.meta.title
            },
            //内容高度
            contentH(){
                return this.$store.state.clientHeight - 40
            },
            //用户对应返点
            userRebate(){
                return this.$store.state.userRebate
            },
            //彩票列表
            lotteryList(){
                return this.$store.state.lotteryShow
            }
        },
        methods:{
            reset(){
                this.setRebate.ssc = ''
                this.setRebate.ks = ''
                this.setRebate.syxw = ''
                this.setRebate.pk10 = ''
                this.setRebate.pc28 = ''
            },
            change(t,v,m){
                if(Number(v) > Number(m)){
                    this.setRebate[t] = m
                }else {
                    this.setRebate[t] = this.$base.formateSmallNumber(v,1) //保留一位小数
                }
            },
            //返回
            goBack(){
                this.$router.isBack = true
                this.$router.push({
                    path:this.$route.query.redirect ? this.$route.query.redirect :  window.history.go(-1)
                })
            },
            //生成邀请码
            addInviteCode(){
                for(let i in this.lotteryList){
                    if(!this.setRebate[this.lotteryList[i].type]){
                        this.$messagebox('提示','请输入正确的'+ this.lotteryList[i].label + '返点值')
                        return
                    }
                }
                this.$axios.post('/web/agent/add_invite',{
                    type: this.type,
                    rebate: JSON.stringify(this.setRebate)
                }).then(({data})=>{
                    if(!data.err){
                        this.$messagebox.confirm('', {
                            message: '生成邀请码成功！',
                            title: '提示',
                            confirmButtonText: '查看邀请码',
                            cancelButtonText: '继续添加'
                        }).then(action => {
                            this.$router.push({
                                path:'/agent/inviteCode'
                            })
                        }).catch(err => {
                           this.reset();
                        });
                    }
                })
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .invite-head{
        padding:0 10px;
        .invite-item{
            padding: 10px 0;
            .label{
                padding-right: 15px;
            }
            .invite-radio{
                max-width: 130px;
            }
            a{
                color: #ef4f4f;
            }
        }
    }
</style>
