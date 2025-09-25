<template>
    <div style="position: relative">
        <mt-header :title="title">
            <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)"></mt-button>
            <div slot="right">
                {{label}}
            </div>
        </mt-header>
        <div class="odds-head">
            <ul class="">
                <li v-for="(item,index) in lotteryList" :class="{'active' : type == item.type}" @click="choseCz(item.type)">
                    {{item.label}}
                </li>
            </ul>
            <div class="odds-layout contentH" v-show="show" @click.self="show = false" :style="{height:contentH + 'px'}">
                <template v-for="(item,index) in lotteryList">
                    <ul class="list-B cf" v-show="type == item.type">
                        <li v-for="(data,index) in item.data" :class="{'active' : name == data.link}" @click="choseLottery(data.link,data.label)">
                            {{data.label}}
                        </li>
                    </ul>
                </template>
            </div>
        </div>
        <div class="odds-tips"></div>
        <div class="odds-cont flex-box contentH"  :style="{height:contentH + 'px'}">
            <div class="cont-left">
                <ul class="odds-item-list">
                    <li>玩法</li>
                    <li v-for="(item,index) in rebateOddsList.columsNameList" class="tc">
                        {{item}}
                    </li>
                </ul>
            </div>
            <div class="cont-right flex" style="overflow-x: scroll">
                <div style="width: 8800%">
                    <ul v-for="(item,key) in rebateOddsList.playIdRebateValList" :key="key" class="odds-item-list">
                        <li v-for="(list,k) in item" :key="k" :class="{'tc':k == 0}">{{list}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'agentRebateOdds',
        data () {
            return {
                type:'' , //选中的彩种类
                label:'',//选中的彩票名
                name:'', //选中的彩票name
                show:false,

                rebateOddsList : {
                    columsNameList: [],
                    playIdRebateValList:[]
                }
            }
        },
        computed:{
            title(){
                return this.$route.meta.title
            },
            //彩票列表
            lotteryList(){
                return this.$store.state.lotteryShow
            },
            contentH(){
                return this.$store.state.clientHeight - 85
            },
            url() {
                return "/web/agent/get_rebate?name=" + this.name
            }
        },
        methods:{
            //获取数据 *********
            getData(){
                this.$store.commit('setLoadStatus',true)
                this.$axios.get(this.url).then(({data}) => {
                    this.$set(this.rebateOddsList,'columsNameList',data.type)
                    this.$set(this.rebateOddsList,'playIdRebateValList',data.gain)
                    this.$store.commit('setLoadStatus',false)
                    this.show = false
                    this.type = ''
                });
            },
            //选择彩种类
            choseCz(type){
                this.show = true
                this.type = type
            },
            //选择具体彩种
            choseLottery(name,label){
                this.name = name
                this.label = label
                this.getData();
            },
        },
        created(){
            this.label = this.$store.state.lotteryShow[0].data[0].label
            this.name = this.$store.state.lotteryShow[0].data[0].link
            this.getData();
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .odds-head{
        background-color: #000000;
        width: 100%;
        ul{
            white-space: nowrap;
            overflow-x: scroll;
            li{
                height: 45px;
                line-height: 45px;
                background-color: #232323;
                color: #999999;
                padding: 0 20px;
                display: inline-block;
                &.active{
                    background-color: #000000;
                }
            }
        }
    }
    .odds-layout{
        position: absolute;
        top: 85px;
        left: 0;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.46);
        z-index: 100;
        ul.list-B{
            background-color: #ffffff;
            padding: 10px;
            line-height: 1;
            li{
                width: 33.333%;
                height: 32px;
                line-height: 32px;
                margin: 2px 0;
                background:none;
                padding: 0;
                text-align: center;
                &.active{
                    background-color: $bColor;
                    color: #ffffff;
                    @include rounded(4px);
                }
            }
        }
    }
    .odds-cont{
        align-items: flex-start;
        .cont-left{
            @include shadow(6px,0px,6px, #d3d3d3);
            min-width: 140px;
            z-index: 1;
        }
        .cont-right{
            white-space: nowrap;
            overflow-x: scroll;
            ul{
                float: left;
            }
        }
        .odds-item-list{
            li{
                height: 42px;
                line-height: 42px;
                font-size: 14px;
                min-width: 113px;
                text-align: center;
                &:nth-child(odd){
                    background-color: #eeeeee;
                }
                &:first-child{
                    background-color: #e3e3e3;
                }
            }
        }
    }
</style>
