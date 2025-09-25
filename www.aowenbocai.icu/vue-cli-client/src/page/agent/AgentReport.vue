<template>
    <div>
        <mt-header :title="title">
            <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)"></mt-button>
            <div slot="right">
                <date-actions @change-date="changeDate"></date-actions>
            </div>
        </mt-header>
        <div class="contentH" :style="{height:contentH + 'px'}">
            <!--<input-search placeholder="下级报表查询" v-model="value" @search="doSearch"></input-search>-->
            <div class="statis-detail card">
                <ul class="cf">
                    <li><span>{{agentReportStatic.spend || 0}}</span>投注金额</li>
                    <li><span>{{agentReportStatic.award || 0}}</span>中奖金额</li>
                    <li><span>{{agentReportStatic.send || 0}}</span>活动礼金</li>
                    <li><span>{{agentReportStatic.rebate || 0}}</span>团队返点</li>
                    <li><span>{{agentReportStatic.gain || 0}}</span>团队盈利</li>
                    <li><span>{{agentReportStatic.recharge || 0}}</span>充值金额</li>
                    <li><span>{{agentReportStatic._change || 0}}</span>兑换金额</li>
                    <li><span>{{agentReportStatic.register_num || 0}}</span>注册人数</li>
                    <li><span>{{agentReportStatic.agent_num || 0}}</span>下级人数</li>
                    <li class="border-none"><span>{{agentReportStatic.money || 0}}</span>团队余额</li>
                    <li class="border-none"><span>{{agentReportStatic.agent_rebate || 0}}</span>返点(下级)</li>
                    <li class="border-none"><span>{{agentReportStatic.self_rebate || 0}}</span>返点(自身)</li>
                    <!--<li class="border-none"><span></span></li>-->
                </ul>
            </div>
            <div class="tips mt-sm">温馨提示：代理报表最多可查询近半年。</div>
        </div>
    </div>
</template>
<script>
    import InputSearch from 'components/common/InputSearch.vue' //搜索
    import DateActions from 'components/common/DateActions.vue' //按时间查询
    export default{
        name:'agentReport',
        components: {
            InputSearch,
            DateActions
        },
        data() {
            return {
                time : 1 , //时间查询值
                value : '', //搜索值
                name:''
            };
        },
        computed:{
            title(){
                return this.$route.meta.title
            },
            //内容高度
            contentH(){
                return this.$store.state.clientHeight - 40
            },
            url(){
                let time = '?time='+this.time
                return '/web/agent/get_statistics' + time
            },
            //代理报表数据
            agentReportStatic(){
                return this.$store.state.agent.static
            },
        },
        watch:{
            url(){
                this.getStatic();
            }
        },
        methods:{
            //获取页面数据
            getStatic(){
                this.$store.commit('setLoadStatus',true)
                this.$axios.get(this.url).then(({data}) => {
                    this.$store.commit('setAgentReport', data);
                    this.$store.commit('setLoadStatus',false)
                });
            },
            //按照时间查询
            changeDate(emitVal){
                this.time = emitVal
            },
            //搜索
            doSearch(){
                this.name = this.value
            }
        },
        created(){
            this.getStatic()
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .statis-head{
        padding:20px 15px 0;
        @include linear-gradient(#6a202d,#3b052c);
        color: #ffffff;
        line-height: 2;
        .gs{
            line-height: 1.5;
            font-size: 15px;
            padding: 15px 0;
        }
        .award-money{
            font-size: 28px;
        }
    }
    .statis-detail{
        padding: 10px;
        ul>li{
            float: left;
            width: 33.3333%;
            padding: 28px 0;
            height: 110px;
            line-height: 1.8;
            text-align: center;
            border-bottom: 1px solid #e2e2e2;
            span{
                display: block;
                color: #ff6818;
                font-size: 14px;
            }
            &:nth-child(3n+1),&:nth-child(3n+2){
                border-right: 1px solid #e2e2e2;
            }
            &.border-none{
                border-bottom: none;
            }
        }
    }
</style>
