<!--顶部期号+ 倒计时-->
<template>
    <div class="recent-open-box">
        <template v-if="cz == 'syxw'">
            <table cellpadding="0" cellspacing="0" class="table-list" v-show="show">
                <tr>
                    <th width="20%">期号</th>
                    <th width="50%">开奖号码</th>
                    <th width="15%">大小比</th>
                    <th width="15%">奇偶比</th>
                </tr>
                <tr v-for="(item,index) in codeArr" :key="index">
                    <td>{{item.expect | subStr}}</td>
                    <td>
                        <span v-for="num in item.code" class="code">{{num}}</span>
                    </td>
                    <td>{{item.dx}}</td>
                    <td>{{item.jo}}</td>
                </tr>
            </table>
        </template>
        <template v-if="cz == 'ssc'">
            <table cellpadding="0" cellspacing="0" class="table-list" v-show="show">
                <tr>
                    <th width="20%">期号</th>
                    <th width="35%">开奖号码</th>
                    <th width="15%">十位</th>
                    <th width="15%">个位</th>
                    <th width="15%">后三</th>
                </tr>
                <tr v-for="(item,index) in codeArr" :key="index">
                    <td>{{item.expect | subStr}}</td>
                    <td>
                        <span v-for="num in item.code" class="code">{{num}}</span>
                    </td>
                    <td>{{item.shi}}</td>
                    <td>{{item.ge}}</td>
                    <td>{{item.hs}}</td>
                </tr>
            </table>
        </template>
        <template v-if="cz == 'pk10'">
            <table cellpadding="0" cellspacing="0" class="table-list" v-show="show">
                <tr>
                    <th width="25%">期号</th>
                    <th width="75%">开奖号码</th>
                </tr>
                <tr v-for="(item,index) in codeArr" :key="index">
                    <td>{{item.expect}}</td>
                    <td>
                        <span v-for="num in item.codeInfo" class="code">{{num}}</span>
                    </td>
                </tr>
            </table>
        </template>
        <div @click="show= !show" class="f-mini show-more">
            <span class="border-1px">近期开奖 <i class="iconfont icon-xialajiantou" :class="{'rotate': show}"></i></span>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                show:false
            }
        },
        computed:{
            name(){ //游戏名eg:gd11
                return this.$route.query.name
            },
            //当期剩余时间
            downTime(){
                return this.$store.state.lottery.info.time
            },
            //当期期号（短）
            sortExpect(){
                return this.$store.state.lottery.info.sort_expect
            },
            //近期开奖数组
            codeArr(){
                return this.$store.state.lottery.recent_open
            },
            //倒计时显示格式化
            timer(){
                return this.downTime == 0 ? '00:00' : this.$bet.formatTime('i:s',this.downTime)
            },
            intervalTime(){
                return this.$store.state.intervalTime
            },
            cz(){
                let path = this.$route.path.replace('/','')
                return path == 'betOrder' ? this.$route.query.cz : path
            },
        },
        filters:{
            subStr(val){
                return val.toString().slice(4);
            }
        },

        methods:{

        },
        created(){

        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style  lang="scss" scoped type="text/scss">
    .code{
        padding: 0 5px;
    }
    .show-more{
        height:20px;
        position: relative;
        span{
            display: inline-block;
            width: 90px;
            top: -1px;
            left: 50%;
            margin-left: -45px;
            position: absolute;
            height: 20px;
            border-top-width:0;
            line-height: 20px;
            text-align: center;
            background-color: #f9f8f1;
            color: $color-font-secondary;
            padding-left: 5px;
            i.rotate{
                transform: rotate(180deg);
            }
        }
    }
</style>
