<template>
    <div>
        <div class="detail">
            <div class="border-bottom-1px">
                <div class="options flex-box" style="position: relative;">
                    <pull-select class="flex" :selectOptions="lotteryNav" :default-index="defaultIndex" v-model="gameid" :select-status="op1" @change-status="changeVisible1"></pull-select>
                    <pull-select v-if="$route.path == '/lotteryRecord'" class="flex" :selectOptions="orderOptions" v-model="join" :select-status="op2" @change-status="changeVisible2"></pull-select>
                    <pull-select class="flex" :selectOptions="statusOptions" v-model="bounsStatus" :select-status="op3" @change-status="changeVisible3"></pull-select>
                </div>
            </div>
            <div class="contentH" :style="{height: contentH + 'px'}" ref="wrapper">
                <page-item :url="url" v-if="isrefresh">
                    <template slot-scope="props">
                        <a class="mint-cell" v-for="(item,index) in props.data" :key="index" @click="toOrders(item.BuyInfo.lottery_id,item.buy_id,item.BuyInfo.isChase,item.BuyInfo.is_join)"
                           :class="{'c-3':readArr.indexOf(item.buy_id) > -1}">
                            <div class="mint-cell-left"></div>
                            <div class="mint-cell-wrapper">
                                <div class="mint-cell-title">
                                    <span class="mint-cell-text">{{item.ext_txt}} <i class="icon-ts icon-hm" v-if="item.BuyInfo.is_join">合</i><i class="icon-ts icon-zh" v-if="item.BuyInfo.isChase">追</i></span>
                                    <span class="mint-cell-label">{{item.create_time}}</span>
                                </div>
                                <div class="mint-cell-value is-link">
                                    <span v-if="item.BuyInfo.statusCode == 0" class="c-1">
                                        <template v-if="item.BuyInfo.is_join">
                                             <em v-if="!item.BuyInfo.finsh">合买中</em>
                                             <em v-else>未出票</em>
                                        </template>
                                        <template v-else>
                                            未出票
                                        </template>
                                    </span>
                                    <span v-if="item.BuyInfo.statusCode == 1" class="suc">等待开奖</span>
                                    <span v-if="item.BuyInfo.statusCode == 2 && item.bonus > 0" class="red">已中奖 ({{item.bonus}}{{lotteryUnit}})</span>
                                    <span v-if="item.BuyInfo.statusCode == 2 && item.bonus == 0" class="c-3">未中奖</span>
                                    <span v-if="item.BuyInfo.statusCode == 6" class="c-3">流产撤单</span>
                                    <span v-if="item.BuyInfo.statusCode == 7" class="c-3">系统撤单</span>
                                    <span v-if="item.BuyInfo.statusCode == 8" class="c-3">用户撤单</span>
                                    <span v-if="item.BuyInfo.statusCode <= 1 && !item.BuyInfo.is_join"><mt-button type="danger" size="small" @click.native.self.stop="cancelOrder(item.BuyInfo.lottery_id)">撤单</mt-button></span>
                                    <span class="mint-cell-label">投注<em class="org">{{item.money}}</em>{{lotteryUnit}}</span>
                                </div>
                                <i class="mint-cell-allow-right"></i>
                            </div>
                            <div class="mint-cell-right"></div>
                        </a>
                    </template>
                </page-item>
            </div>
        </div>
    </div>
</template>
<script>
    import pageItem from 'components/common/PageItem.vue' //分页
    import unitConvert from 'components/lottery/UnitConvert.vue' //彩票单位提示组件
    import PullSelect from 'components/common/PullSelect.vue' //下拉选项
    export default{
        name:'lotteryRecord',
        components: {
            pageItem,
            unitConvert,
            PullSelect
        },
        data() {
            return {
                op1:false,
                op2:false,
                op3:false,
                defaultIndex: 0,
                gameid:'', //彩种定位
                join:"", //购买方式
                bounsStatus:"", //方案状态

                readArr:[],

                isrefresh: true
            };
        },
        computed:{
            cz(){
                return this.$route.query.cz
            },
            //列表高度
            contentH(){
                return this.$store.state.clientHeight - 78
            },
            url(){
                let gameid = this.gameid ? '?gameid='+ this.gameid : '?gameid='
                let join = this.join ? '&join='+ this.join : ''
                let bounsStatus = this.bounsStatus ? '&bounsStatus='+ this.bounsStatus : ''
                let url = '/web/details/games'
                return  url + gameid + bounsStatus + join
            },
            lotteryUnit(){ //彩票单位
                return this.$store.state.setting.lottery_unit
            },
            lotteryNav(){ //彩种列表
                return this.$store.state.lotteryNav
            },
            name(){ //彩种导航
                for(let p in this.lotteryNav){
                    if(this.gameid == this.lotteryNav[p].value){
                        return this.lotteryNav[p].label
                    }
                }
            },
            //合买是否开启
            join_isOpen(){
                return this.$store.state.setting.join_isOpen == 1 ? true : false
            },
            //购买方式选项
            orderOptions(){
                return this.join_isOpen ? [{ value: '', label: '所有类型' },
                    { value: '0', label: '自购' },
                    { value: '3', label: '追号' },
                    { value: '1', label: '发起合买' },
                    { value: '2', label: '参与合买' }] : [{ value: '', label: '所有类型' },
                    { value: '0', label: '自购' },
                    { value: '3', label: '追号' }]
            },
            //方案状态选项
            statusOptions(){
                return this.join_isOpen ?  [
                    { value: '', label: '所有状态' },
                    { value: '311', label: '合买中' },
                    { value: '0', label: '等待开奖' },
                    { value: '1', label: '已中奖' },
                    { value: '2', label: '未中奖' },
                    { value: '8', label: '用户撤单' },
                    { value: '6', label: '流产撤单' },
                    { value: '7', label: '系统撤单' }] : [
                    { value: '', label: '所有状态' },
                    { value: '0', label: '等待开奖' },
                    { value: '1', label: '已中奖' },
                    { value: '2', label: '未中奖' },
                    { value: '8', label: '用户撤单' },
                    { value: '7', label: '系统撤单' }]
            }
        },
        methods:{
            changeVisible1(){
                this.op1 = !this.op1
                this.op2 = false
                this.op3 = false
            },
            changeVisible2(){
                this.op2 = !this.op2
                this.op1 = false
                this.op3 = false
            },
            changeVisible3(){
                this.op3 = !this.op3
                this.op1 = false
                this.op2 = false
            },
            toOrders(lottery_id,id,isChase,isJoin){ //进入订单详情页
                this.$store.commit('setPageYOffset', this.$refs.wrapper.scrollTop)
                if(this.readArr.indexOf(id) == -1){
                    this.readArr.push(id)
                }
                let path
                if(isChase && !isJoin){
                    path ='/chase'
                }else if(!isChase && !isJoin){
                    path ='/orders'
                }else if(isJoin){
                    path = '/ordersJoin'
                }
                this.$router.push({
                    path:path,
                    query:{
                        cz:this.cz,
                        lottery_id:lottery_id,
                        id:id
                    }
                })
            },
            //撤单
            cancelOrder(id){
                this.$messagebox.confirm(
                    '确定要撤销该订单吗?'
                ).then(()=>{
                    this.$axios.get('/web/orders/returnTicket',{
                        params:{
                            lottery_id : id
                        }
                    }).then(({data})=>{
                        if(!data.err){
                            this.$messagebox.alert(data.msg).then(action => {
                                this.isrefresh = false
                                this.$nextTick(()=>{
                                    this.isrefresh = true
                                })
                            });
                        }else {
                            this.$messagebox('提示',data.msg)
                        }
                    })
                }).catch((err)=>{
                });
                return
            }
        },
        created(){
            this.$store.commit('setKeepAlivePage','lotteryRecord')
            if(this.$route.path=='/chaseRecord'){
                this.join = "3"
            }
            //投注页面查看投注记录
            if(this.$route.query.name){
                this.gameid = this.$route.query.name
                for(let p in this.lotteryNav){
                    if(this.gameid == this.lotteryNav[p].value){
                        this.defaultIndex = p
                    }
                }
            }
        },
        beforeRouteLeave(to, from, next){
            if(to.path == '/ordersJoin' || to.path == '/orders' || to.path == '/chase'){
                this.$store.commit('setKeepAlivePage','lotteryRecord')
            }else {
                this.$store.commit('delKeepAlivePage','lotteryRecord')
            }
//            this.$store.commit('setPageLoad',true)   //进入详情页后禁止滚动加载事件
            next();
        },
        activated(){
            this.$refs.wrapper.scrollTop = this.$store.state.pageYOffset;
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .mint-button--small {
        display: inline-block;
        padding: 0 5px;
        height: 23px;
        font-size: 12px;
    }
    .detail{
        .mint-cell{
            .mint-cell-value{
                display: block;
                text-align: right;
            }
        }
    }
    .options{
        background-color: #ffffff;
        font-size: 13px;
        margin-bottom: 1px;
        color: $color-font-secondary;
        span{
            text-align: center;
            padding-bottom:8px;
        }
    }
    .p-fixed{
        position: fixed;
        top: 40px;
        left: 0;
        width: 100%;
        z-index: 50;
    }
    .icon-ts{
        color: #ffffff;
        font-size: 10px;
        display: inline-block;
        width: 16px;
        height: 16px;
        line-height: 16px;
        text-align: center;
        @include rounded(2px);
        background-color: #FF9800;
        margin-left: 3px;
    }
    .icon-zh{
        background-color: #00bcd4;
    }
</style>
