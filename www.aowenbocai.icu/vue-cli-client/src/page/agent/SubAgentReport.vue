<template>
    <div>
        <mt-header :title="title">
            <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)" v-if="agentid">返回上一级</mt-button>
            <mt-button icon="back" slot="left" @click.native="goBack" v-else></mt-button>
            <div slot="right">
                <date-actions @change-date="changeDate"></date-actions>
                <!--<mt-button v-if="agentid"><router-link to="/agent/subReport" tag="span">我的</router-link></mt-button>-->
            </div>
        </mt-header>
        <!--<mt-header :title="title">-->
            <!--<mt-button icon="back" slot="left" @click.native="$router.goBack(-1)"></mt-button>-->
            <!--<div slot="right">-->
                <!--<date-actions @change-date="changeDate"></date-actions>-->
            <!--</div>-->
        <!--</mt-header>-->
        <page-item :url="url"  class=" contentH" :style="{height:contentH + 'px'}">
            <template slot-scope="props">
                <div class="table-member">
                    <div class="table-member-head">
                        <span class="username">账号</span>
                        <span class="type">类型</span>
                        <span class="num">投注金额</span>
                        <span class="yue">中奖金额</span>
                    </div>
                    <div v-for="(item,index) in props.data" class="table-member-list card">
                        <span class="username">
                            <a class="link" v-if="item.statistics.agent_num > 0" @click="toSubReport(item.id,item.nickname)">{{item.nickname}}</a>
                            <a class="c-2" v-else>{{item.nickname}}</a>
                        </span>
                        <span class="type">
                            <em v-if="item.type == 1" class="el-tag el-tag--success">普通会员</em>
                            <em v-if="item.type == 2" class="el-tag el-tag--danger">代理会员</em>
                            <em v-if="item.type == 0" class="el-tag el-tag--info">测试会员</em>
                        </span>
                        <span class="num">
                             {{item.statistics.spend}}
                        </span>
                        <span class="yue">{{item.statistics.award}}</span>
                        <span class="mint-cell-allow-right" @click="getRowData(item)"></span>
                    </div>
                </div>
            </template>
        </page-item>
        <!--报告详情-->
        <mt-popup
            v-model="allVisible"
            position="bottom"
            class="tc">
            <div class="report-info">
                <div class="visible-title border-bottom-1px">报表详情 ({{rowData.nickname}})</div>
                <ul class="list-grounp">
                    <li>
                        <span class="tit">用户类型：</span>
                        <span v-if="rowData.type == 1" class="el-tag el-tag--success">普通会员</span>
                        <span v-if="rowData.type == 2" class="el-tag el-tag--danger">代理会员</span>
                        <span v-if="rowData.type == 0" class="el-tag el-tag--info">测试会员</span>
                    </li>
                    <li><span class="tit">投注金额：</span><span class="cont">{{rowData.statistics.spend}}</span></li>
                    <li><span class="tit">中奖金额：</span><span class="cont">{{rowData.statistics.award}}</span></li>
                    <li><span class="tit">团队充值：</span><span class="cont">{{rowData.statistics.recharge}}</span></li>
                    <li><span class="tit">团队返点：</span><span class="cont">{{rowData.statistics.rebate}}</span></li>
                    <li><span class="tit">活动礼金：</span><span class="cont">{{rowData.statistics.send}}</span></li>
                    <li><span class="tit">团队盈利：</span><span class="cont">{{rowData.statistics.gain}}</span></li>
                    <li><span class="tit">兑换金额：</span><span class="cont">{{rowData.statistics._change}}</span></li>
                </ul>
            </div>
        </mt-popup>
        <!--报告详情-->
    </div>
</template>
<script>
    import pageItem from 'components/common/PageItem.vue' //分页
    import DateActions from 'components/common/DateActions.vue' //按时间查询
    export default{
        name:'subAgentReport',
        components: {
            pageItem,
            DateActions
        },
        data() {
            return {
                allVisible : false,
                time : 1 , //时间查询值
                rowData  : {
                    id: 0,
                    nickname: "",
                    statistics: {spend: 0, recharge: 0, _change: 0, send: 0, award: 0, money: 0, royalty: 0, rebate: 0, gain: 0,register_num: 0,self_rebate: 0},
                    type: 1,
                    username: ""}
            };
        },
        computed:{
            agentid(){
                return this.$route.query.id || ''
            },
            name(){
                return this.$route.query.name
            },
            title(){
                let agentname = this.name ? ' (' + this.name + ')': '( 我的会员)'
                return this.$route.meta.title + agentname
            },
            //内容高度
            contentH(){
                return this.$store.state.clientHeight - 40
            },
            //数据地址
            url(){
                let id = this.agentid ? '&userid='+ this.agentid : ''
                return '/web/agent/getStatisticsList?time=' + this.time + id
            }
        },
        methods:{
            //返回
            goBack(){
                this.$router.isBack = true
                this.$router.push({
                    path:this.$route.query.redirect ? this.$route.query.redirect :  window.history.go(-1)
                })
            },
            //按照时间查询
            changeDate(emitVal){
                this.time = emitVal
            },
            getRowData(row){
                this.rowData = row
                this.allVisible = true
            },
            //查看下级代理报表
            toSubReport(id,name){
                this.$router.push({
                    path : '/agent/subReport',
                    query:{
                        id:id,
                        name:name
                    }
                })
            },
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .table-member{
        .table-member-head{
            background-color: #e8e8e8;
            color: #6e7a82;
            padding:10px 25px 10px 10px;;
            span{
                font-size: 16px;
            }
        }
        .table-member-list{
            position: relative;
            padding: 15px 25px 15px 5px;
            border-bottom: 1px solid #eeeeee;
        }
        span{
            display: inline-block;
            text-align: center;
            font-size: 14px;
        }
        .username{
            width: 22%;
        }
        .type{
            width: 19%;
        }
        .yue{
            width: 25%;
        }
        .num{
            width: 25%;
        }
    }
    .el-tag {
        background-color: rgba(64,158,255,.1);
        display: inline-block;
        padding: 0 5px;
        height: 26px;
        line-height: 24px;
        font-size: 12px;
        border-radius: 4px;
        box-sizing: border-box;
        border: 1px solid rgba(64,158,255,.2);
    }
    .el-tag--danger {
        background-color: rgba(245,108,108,.1);
        border-color: rgba(245,108,108,.2);
        color: #f56c6c;
    }
    .el-tag--info {
        background-color: rgba(144,147,153,.1);
        border-color: rgba(144,147,153,.2);
    }
    .el-tag--success {
        background-color: rgba(103,194,58,.1);
        border-color: rgba(103,194,58,.2);
        color: #67c23a;
    }
    .report-info{
        .visible-title{
            text-align: left;
            padding-bottom: 8px;
        }
        .list-grounp{
            text-align: left;
            padding: 10px 0;
            li{
                line-height: 1.8;
                .tit{
                    display: inline-block;
                    width: 90px;
                    color: #888888;
                }
            }
        }
    }
</style>
