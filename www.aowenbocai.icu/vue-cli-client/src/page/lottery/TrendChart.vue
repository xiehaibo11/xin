<template>
    <div>
        <mt-header :title="title">
            <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)"></mt-button>
        </mt-header>
        <template v-if="dataInfo.length">
            <div class="trend-num flex-box border-bottom-1px">
                <span class="flex" @click="changeData(30)" :class="{'active' : expect_num == 30}">最近30期</span>
                <span class="flex" @click="changeData(50)" :class="{'active' : expect_num == 50}">最近50期</span>
                <span class="flex" @click="changeData(100)" :class="{'active' : expect_num == 100}">最近100期</span>
            </div>
            <div class="trend-table-cont contentH" :style="{'height': contentH + 'px'}" ref="wrapper">
                <!--时时彩-->
                <table border="0" cellpadding="0" cellspacing="0" class="trend-table" v-if="cz == 'ssc'">
                    <tr>
                        <th  :width="expectType ? 56 : 36">期数</th>
                        <th width="50">奖号</th>
                        <th v-for="n in 10">{{n-1}}</th>
                    </tr>
                    <tr v-for="(item,index) in dataInfo">
                        <td  :width="expectType ? 56 : 36" class="c-1">{{item.sort_expect}}</td>
                        <td width="50" class="num"><em v-for="code in item.code">{{code}}</em></td>
                        <td v-for="n in 10" class="trend-balls c-3">
                            <span v-if="checkCode(n-1,item.code) > 1" class="num-small">{{checkCode(n-1,item.code)}}</span>
                            <span v-if="checkCode(n-1,item.code) > 0" class="ball-sm">{{n-1}}</span>
                            <span v-else>{{item.miss[n-1]}}</span>
                        </td>
                    </tr>
                </table>
                <!--时时彩 end-->
                <!--11选5-->
                <table border="0" cellpadding="0" cellspacing="0" class="trend-table" v-if="cz == 'syxw'">
                    <tr>
                        <th :width="expectType ? 56 : 42">期数</th>
                        <th v-for="n in 11">{{n | addZero}}</th>
                    </tr>
                    <tr v-for="(item,index) in dataInfo">
                        <td  :width="expectType ? 56 : 42" class="c-1">{{item.sort_expect}}</td>
                        <td v-for="n in 11" class="trend-balls c-3">
                            <span v-if="syxwCheckCode(n,item.code)" class="ball-sm">{{n | addZero}}</span>
                            <span v-else>{{item.miss[n-1]}}</span>
                        </td>
                    </tr>
                </table>
                <!--11选5 end-->
                <!--快3-->
                <table border="0" cellpadding="0" cellspacing="0" class="trend-table" v-if="cz == 'ks'">
                    <tr>
                        <th :width="expectType ? 56 : 36">期数</th>
                        <th width="50">奖号</th>
                        <th>和值</th>
                        <th v-for="n in 6">{{n}}</th>
                    </tr>
                    <tr v-for="(item,index) in dataInfo">
                        <td :width="expectType ? 56 : 36" class="c-1">{{item.sort_expect}}</td>
                        <td width="50" class="num"><em v-for="code in item.code">{{code}}</em></td>
                        <td>{{Number(item.code[0]) + Number(item.code[1]) + Number(item.code[2])}}</td>
                        <td v-for="n in 6" class="trend-balls c-3">
                            <span v-if="checkCode(n,item.code) > 1" class="num-small">{{checkCode(n,item.code)}}</span>
                            <span v-if="checkCode(n,item.code) > 0" class="ball-sm">{{n}}</span>
                            <span v-else>{{item.miss[n-1]}}</span>
                        </td>
                    </tr>
                </table>
                <!--快3 end-->
                <!--pc28-->
                <table border="0" cellpadding="0" cellspacing="0" class="trend-table" v-if="cz == 'pc28'">
                    <tr>
                        <th width="20%">期数</th>
                        <th width="30%">奖号</th>
                        <th width="15%">形态</th>
                        <th>极值</th>
                        <th>豹子</th>
                        <th width="15%">色波</th>
                    </tr>
                    <tr v-for="(item,index) in dataInfo">
                        <td width="20%" class="c-1">{{item.sort_expect}}期</td>
                        <td width="30%" class="num">{{Number(item.code[0])}} + {{Number(item.code[1])}} + {{Number(item.code[2])}} = <span class="w_color" :style="{'backgroundColor' : $store.getters.pcResult(item.code).bg_color}" style="width: 22px">{{$store.getters.pcResult(item.code).he}}</span></td>
                        <td width="15%">{{$store.getters.pcResult(item.code).xt}}</td>
                        <td>{{$store.getters.pcResult(item.code).jz}}</td>
                        <td>{{$store.getters.pcResult(item.code).bz}}</td>
                        <td width="15%"><span class="w_color" :style="{'backgroundColor' : $store.getters.pcResult(item.code).bg_color}">{{$store.getters.pcResult(item.code).text_color}}</span></td>
                    </tr>
                </table>
                <!--pc28 end-->
            </div>
        </template>
        <template v-else>
            <p class="tc c-3" style="padding: 30px" v-if="loading">暂无相关开奖数据</p>
        </template>
    </div>
</template>

<script>
    import { Indicator } from 'mint-ui';
    export default {
        name: 'trend',
        data () {
            return {
                expect_num : 30,
                dataInfo:[],
                loading: false
            }
        },
        computed:{
            name(){
                return this.$route.query.name
            },
            title(){
                return this.$route.query.title + '-基本走势'
            },
            cz(){
                return this.$route.query.cz
            },
            //期号类型
            expectType(){
                return this.$route.query.expect_type == 1 ? true : false
            },
            //时时彩检测开奖号码
            checkCode(){
                return (code,codeArr)=>{
                    let n = 0
                    for(let i in codeArr){
                        if(codeArr[i] == code){ n += 1}
                    }
                    return n
                }
            },
            //11选5检测开奖号码
            syxwCheckCode(){
                return (code,codeArr)=>{
                    let codeStr = code < 10 ? '0'+ code :code
                    return codeArr.indexOf(codeStr.toString()) > -1 ? true : false
                }
            },
            contentH(){
                return this.$store.getters.getScrollHeight(75);
            }
        },
        filters:{
            addZero(val){
                let str = val
                if(val < 10){
                    str = '0' + val
                }
                return str
            }
        },
        methods:{
            getData(){
                Indicator.open({
                    text: '加载中...',
                    spinnerType: 'fading-circle'
                });
                this.loading = false
                this.$axios.get("/web/Details/get_trend_data",{
                    params:{
                        name : this.name,
                        limit: this.expect_num
                    }
                }).then(({data})=>{
                    this.$set(this,'dataInfo',data.data)
                    Indicator.close();
                    this.loading = true
                })
            },
            changeData(n){
                this.expect_num = n
                this.getData();
                this.$refs.wrapper.scrollTop = 0
            },
        },
        created(){
            this.getData();
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .trend-num{
        height: 35px;
        line-height: 35px;
        padding: 0 15px;
        span{
            text-align: center;
            font-size: 14px;
            position: relative;
            &.active{
                color: $bColor;
                &:after{
                    content: '';
                    position: absolute;
                    display: block;
                    width: 30px;
                    height: 3px;
                    background-color: $bColor;
                    @include rounded(10px);
                    bottom: 0;
                    left: 50%;
                    margin-left: -15px;
                }
            }
        }
    }
    .ball-sm{
        margin-right: 0;
        width: 21px;
        height: 21px;
    }
    .trend-table{
        $td-heigth:30px;
        table-layout: fixed;
        width: 100%;
        th{
            font-weight: normal;
            text-align: center;
            height:  $td-heigth;
            border: 1px solid #e2e2e2;
            background: #ededed;
            color: #494949;
            font-size: 13px;
            border-bottom: none;
        }
        td{
            text-align: center;
            height:  $td-heigth;
            border: 1px solid #e2e2e2;
            font-size: 13px;
            &.num{
                color: #780000;
            }
        }
        tr{
            background-color: #ffffff;
            &:nth-child(odd){
                background: #f9f9f9;
            }
        }
        .trend-balls{
            position: relative;
            .num-small{
                width: 15px;
                height: 15px;
                line-height: 15px;
                font-size: 11px;
                @include rounded(50%);
                background-color: orange;
                color: #ffffff;
                text-align: center;
                position: absolute;
                border: 1px solid #fff;
                left: 50%;
                bottom:50%;
                margin-left: 4px;
                margin-bottom: 4px;
                z-index: 2;
                @include shadow(0,0,2px,#999);
            }
        }
    }
    .w_color{
        display: inline-block;
        width: 40px;
        height: 22px;
        line-height: 22px;
        @include rounded(11px);
        color: #ffffff;
        font-size: 12px;
    }
    .pc28-num{
        background-color: #666666;
    }
</style>
