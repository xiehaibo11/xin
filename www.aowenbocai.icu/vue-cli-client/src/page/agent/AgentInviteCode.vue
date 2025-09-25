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
                    <router-link :to="'/agent/invite?redirect='+ this.$route.query.redirect" tag="li">下级开户</router-link>
                    <li class="active">邀请码</li>
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
            </div>
            <page-item :url="url" class="mt" v-if="isrefresh">
                <template slot-scope="props">
                    <div class="table-report">
                        <div class="table-report-head">
                            <span class="code-num">邀请码</span>
                            <span class="time">生成时间</span>
                            <span class="status">状态</span>
                        </div>
                        <div v-for="(item,index) in props.data" class="table-report-list card"  @click="getRowData(item)">
                            <span class="code-num">{{item.code}}</span>
                            <span class="time">{{item.create_time}}</span>
                            <span class="status">注册<em :class="{'red':item.num > 0}">({{item.num}})</em></span>
                            <span class="mint-cell-allow-right"></span>
                        </div>
                    </div>
                </template>
            </page-item>
            <!--更多选项-->
            <mt-actionsheet
                :actions="actions"
                v-model="sheetVisible">
            </mt-actionsheet>
            <!--更多选项 end-->
            <!--生成二维码图片-->
            <mt-popup
                v-model="qrVisible"
                position="bottom" class="tc">
                <div class="visible-title border-bottom-1px">二维码</div>
                <vue-qr :text="qrValue" :size="185" :margin="10"></vue-qr>  <!--:logoSrc="config.logo"-->
            </mt-popup>
            <!--生成二维码图片 end-->
            <!--返点详情-->
            <mt-popup
                v-model="rebateVisible"
                position="bottom"
                class="tc">
                <div class="rebate-info">
                    <div class="visible-title border-bottom-1px">返点详情</div>
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
    import VueQr from 'vue-qr' //生成二维码图片

    export default {
        name: 'agentInviteCode',
        components: {
            pageItem,
            VueQr
        },
        data () {
            return {
                isrefresh: true,
                active : '2', //顶部tab
                type : 2, //开户类型 1代理类型 2玩家类型
                sheetVisible:false,
                rowData:{
                    title:'1'
                },//查看行数据
                qrVisible:false,//二维码显示否
                rebateVisible :false, //返点详情显示否
                rebateInfo:{ //返点详情
                    ssc: '0.0',
                    ks : '0.0',
                    syxw : '0.0',
                    pk10 : '0.0',
                    pc28 : '0.0'
                }
            }
        },
        computed:{
            actions(){
                let remark = this.rowData.remark || '未设置'
                return [{ //更多选项列表
                    name: '复制推广链接',
                    method: this.doCopy
                }, {
                    name: '生成二维码',
                    method: this.showQr
                },{
                    name: '查看返点',
                    method: this.viewRebateInfo
                },{
                    name: '(备注)' + remark,
                    method: this.editRemark
                },{
                    name: '删除邀请码',
                    method: this.handleDelete
                }]
            },
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
            },
            //数据地址
            url(){
                return '/web/agent/get_invite?type='+ this.type
            },
            //二维码生成文本
            qrValue(){
                return  'http://' + window.location.host + '/wap/#/reg?code='+ this.rowData.code || ''
            },
        },
        methods:{
            //返回
            goBack(){
                this.$router.isBack = true
                this.$router.push({
                    path:this.$route.query.redirect ? this.$route.query.redirect :  window.history.go(-1)
                })
            },
            //查看更多选项
            getRowData(row){
                this.rowData = row
                this.sheetVisible = true
                this.rebateInfo = row.rebate
            },
            //查看二维码
            showQr(){
                this.qrVisible = true
            },
            //复制链接
            doCopy() {
                let text = 'http://' + window.location.host + '/wap/#/reg?code='+ this.rowData.code
                this.$copyText(text).then((e)=> {
                    this.$messagebox('提示', '复制成功！链接地址：' + e.text + '');
                }, (e)=> {
                    this.$messagebox('提示', '复制失败！');
                })
            },
            //删除邀请码
            handleDelete(index){
                this.$messagebox.confirm('您确定要删除这条邀请码吗？').then((action) => {
                    this.$axios.post('/web/agent/delete_invite',{
                        id : this.rowData.id
                    }).then(({data})=>{
                        this.$messagebox('提示', data.msg);
                        if(!data.err){
                            this.isrefresh = false
                            this.$nextTick(()=>{
                                this.isrefresh = true
                            })
                        }
                    })
                }).catch(()=>{

                });
            },
            //查看返点
            viewRebateInfo(){
                this.rebateVisible = true
            },
            //修改备注
            editRemark(){
                this.$messagebox.prompt('请输入备注').then(({ value, action }) => {
                    this.$axios.post('/web/agent/add_invite_remark',{
                        id : this.rowData.id,
                        remark : value
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
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .top-tab{
        @include rounded(4px);
        border:1px solid #ffffff;
        width: 150px;
        margin: 0 auto;
        li{
            float: left;
            width: 74px;
            height: 28px;
            line-height: 28px;
            &.active{
                background-color: #ffffff;
                color: $bColor;
            }
        }
    }
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
    .table-report{
        .table-report-head{
            background-color: #e8e8e8;
            padding:10px 25px 10px 10px;;
            span{
                font-size: 16px;
            }
        }
        .table-report-list{
            position: relative;
            padding: 15px 25px 15px 10px;
            border-bottom: 1px solid #eeeeee;
        }
        span{
            display: inline-block;
            text-align: center;
            font-size: 14px;
        }
        .code-num{
            width: 26%;
        }
        .time{
            width: 48%;
        }
        .status{
            width: 21%;
        }
    }
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
</style>
