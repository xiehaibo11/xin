<template>
    <div>
        <mt-header :title="title">
            <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)"></mt-button>
            <div slot="right">
                <date-actions @change-date="changeDate"></date-actions>
            </div>
        </mt-header>
        <div class="contentH" :style="{height:contentH + 'px'}">
            <div class="statis-head">
                <div class="tc yellow">
                    <p>盈利金额</p>
                    <p class="award-money">{{staticInfo.gain}}</p>
                </div>
                <div class="gs"> 盈亏计算公式：中奖-投注+活动+返点（自身&amp;下级）</div>
            </div>
            <div class="statis-detail card mt">
                <ul class="cf">
                    <li><span>{{staticInfo.spend}}</span>投注金额</li>
                    <li><span>{{staticInfo.award}}</span>中奖金额</li>
                    <li><span>{{staticInfo.send}}</span>活动礼金</li>
                    <li><span>{{staticInfo.recharge}}</span>充值金额</li>
                    <li><span>{{staticInfo._change}}</span>兑换金额</li>
                    <li><span>{{staticInfo.rebate}}</span>返点(自身)</li>
                    <li class="border-none"><template v-if="isGain && joinIsOpen"><span>{{staticInfo.royalty}}</span>提成(合买提成)</template></li>
                    <li class="border-none"><span></span></li>
                    <li class="border-none"><span></span></li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script>
    import DateActions from 'components/common/DateActions.vue' //按时间查询
    export default{
        name:'static',
        components: {
            DateActions
        },
        data() {
            return {
                time : 1 ,//按时间查询
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
            url() {
                return '/web/details/get_self_statistics' + '?time=' + this.time
            },
            staticInfo(){
                return this.$store.state.static
            },
            isGain(){
                return this.$store.state.setting.isGain == 1 ? true : false
            },
            joinIsOpen(){
                return this.$store.state.setting.join_isOpen == 1 ? true : false
            }
        },
        watch:{
            time(){
                this.getStatic();
            }
        },
        methods:{
            //获取数据
            getStatic(){
                this.$store.commit('setLoadStatus',true)
                this.$axios.get(this.url).then(({data}) => {
                    this.$store.commit('setStatic', data);
                    this.$store.commit('setLoadStatus',false)
                });
            },
            //按照时间查询
            changeDate(emitVal){
                this.time = emitVal
            },
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
