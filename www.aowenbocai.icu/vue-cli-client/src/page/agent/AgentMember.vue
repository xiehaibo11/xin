<template>
    <div>
        <mt-header :title="title">
            <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)" v-if="agentid">返回上一级</mt-button>
            <mt-button icon="back" slot="left" @click.native="goBack" v-else></mt-button>
            <mt-button slot="right" v-if="agentid"><router-link to="/agent/member" tag="span">我的</router-link></mt-button>
        </mt-header>
        <input-search placeholder="账号/昵称查询" v-model="value" @search="doSearch"></input-search>
        <div class="contentH" :style="{height:contentH + 'px'}">
            <page-item  :url="url" v-if="isrefresh">
                <template slot-scope="props">
                    <div class="table-member">
                        <div class="table-member-head">
                            <span class="username">账号</span>
                            <span class="type">类型</span>
                            <span class="num">下级人数</span>
                            <span class="yue">账户余额</span>
                        </div>
                        <div v-for="(item,index) in props.data" class="table-member-list card">
                            <span class="username">
                                <a class="link" v-if="item.agents_num > 0" @click="toSubMember(item.id,item.nickname)">{{item.nickname}}</a>
                                <a class="c-2" v-else>{{item.nickname}}</a>
                            </span>
                            <span class="type">
                                 <em v-if="item.type == 1" class="el-tag el-tag--success">普通会员</em>
                                <em v-if="item.type == 2" class="el-tag el-tag--danger">代理会员</em>
                                <em v-if="item.type == 0" class="el-tag el-tag--info">测试会员</em>
                            </span>
                            <span class="num">
                                <template v-if="item.type == 2 || item.type == 0">
                                     <em :class="{'red':item.agents_num > 0}">{{item.agents_num}}</em>
                                </template>
                                <template v-else>
                                    <em class="c-4">--</em>
                                </template>
                            </span>
                            <span class="yue">{{item.agents_money}}</span>
                            <span class="mint-cell-allow-right" @click="getRowData(item)"></span>
                        </div>
                    </div>
                </template>
            </page-item>
            <!--更多选项-->
            <mt-actionsheet
                :actions="actions"
                v-model="sheetVisible">
            </mt-actionsheet>
            <!--返点详情-->
            <mt-popup
                v-model="rebateVisible"
                position="bottom"
                class="tc">
                <div class="rebate-info">
                    <div class="visible-title border-bottom-1px">返点详情 ({{rowData.nickname}})</div>
                    <template v-for="(item,index) in lotteryList">
                        <div class="flex-box border-bottom-1px rebate-info-item" :key="index">
                            <span class="tl label">{{item.label}}</span>
                            <span>{{rebateInfo[item.type]}}</span>
                        </div>
                    </template>
                </div>
            </mt-popup>
            <!--返点详情 end-->
        </div>
    </div>
</template>
<script>
    import pageItem from 'components/common/PageItem.vue' //分页
    import InputSearch from 'components/common/InputSearch.vue' //搜索
    export default{
        name:'agentMember',
        components: {
            pageItem,
            InputSearch
        },
        data() {
            return {
                isrefresh: true,
                sheetVisible:false,
                rebateVisible:false,
                rowData:'',
                rebateInfo:{ //返点详情
                    ssc: '0.0',
                    ks : '0.0',
                    syxw : '0.0',
                    pk10 : '0.0',
                    pc28 : '0.0'
                },
                value:'',
                name:''
            };
        },
        computed:{
            agentRecharge(){
                return this.$store.state.setting.agent_recharge == 1 ? true : false
            },
            agentid(){
                return this.$route.query.id || ''
            },
            names(){
                return this.$route.query.name || ''
            },
            title(){
                let agentname = this.names ? ' (' + this.names + ')': '( 我的会员)'
                return this.$route.meta.title + agentname
            },
            //内容高度
            contentH(){
                return this.$store.state.clientHeight - 95
            },
            //数据地址
            url(){
                let id = this.agentid ? '?id='+ this.agentid : ''
                let name = this.name ? '&name=' + this.name : ''
                return '/web/agent/get_member' + id + name
            },
            //彩票列表
            lotteryList(){
                return this.$store.state.lotteryShow
            },
            actions(){
                let arr = [{
                    name: '投注明细',
                    method: this.toBetRecord
                },{
                    name: '资金明细',
                    method: this.toBillRecord
                },{
                    name: '注册时间：'+ this.rowData.create_time,
                    method:''
                },{
                    name: '最后登录：'+ this.rowData.action_time,
                    method:''
                }]
                if(this.agentRecharge && !this.agentid){
                    arr.unshift({
                        name: '给Ta充值',
                        method: this.handleAgentRecharge
                    })
                }
                if(this.rowData.rebate !== ''){
                    arr.unshift({
                        name: '查看返点',
                        method: this.viewRebateInfo
                    })
                }
                return arr
            },
        },
        methods:{
            doSearch(){
                this.name = this.value
            },
            //查看更多选项
            getRowData(row){
                this.rowData = row
                this.sheetVisible = true
                if(row.rebate){
                    this.rebateInfo = JSON.parse(row.rebate)
                }else {
                    this.rebateInfo = { //返点详情
                        ssc: '0.0',
                        ks : '0.0',
                        syxw : '0.0',
                        pk10 : '0.0',
                        pc28 : '0.0'
                    }
                }
            },
            //查看返点
            viewRebateInfo(){
                this.rebateVisible = true
            },
            //查看下级代理
            toSubMember(id,name){
                this.$set(this,'value','')
                this.$set(this,'name','')
                this.$router.push({
                    path : '/agent/member',
                    query:{
                        id:id,
                        name:name
                    }
                })
            },
            //投注明细
            toBetRecord(){
                this.$router.push({
                    path : '/agent/betRecord',
                    query:{
                        id : Number(this.rowData.id),
                        name : this.rowData.nickname
                    }
                })
            },
            //资金明细
            toBillRecord(){
                this.$router.push({
                    path : '/agent/billRecord',
                    query:{
                        id : Number(this.rowData.id),
                        name : this.rowData.nickname
                    }
                })
            },
            //充值
            handleAgentRecharge(){
                this.$messagebox.prompt('请输入充值金额').then(({ value, action }) => {
                    this.$axios.post('/web/agent/change_money',{
                        'userid' : this.rowData.id,
                        'money' : value
                    }).then(({data})=>{
                        this.$messagebox('提示', data.msg);
                        if(!data.err){
                            this.isrefresh = false
                            this.$nextTick(()=>{
                                this.isrefresh = true
                            })
                        }
                    })
                }).catch(()=>{})
            },
            //返回
            goBack(){
                this.$router.isBack = true
                this.$router.push({
                    path:this.$route.query.redirect ? this.$route.query.redirect :  window.history.go(-1)
                })
            },
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .visible-title{
        padding: 5px 0 10px 0px;
        text-align: left;
        margin-bottom: 10px;
    }
    .rebate-info{
        &-item{
            padding: 10px;
            .label{
                width: 150px;
                text-align: left;
            }
        }
    }
    .table-member{
        .table-member-head{
            background-color: #e8e8e8;
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
            width: 27%;
        }
        .type{
            width: 19%;
        }
        .yue{
            width: 27%;
        }
        .num{
            width: 20%;
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
</style>
