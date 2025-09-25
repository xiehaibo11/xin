<template>
    <div class="bet-box flex-box">
        <div class="label">
            <p>{{text}}</p>
            <p v-if="wz==11"><em style="letter-spacing: 12px">赔</em>率</p>
            <p v-else><em style="letter-spacing: 12px">遗</em>漏</p>
        </div>
        <div class="flex">
            <ul class="balls-box">
                <li v-for="(item,index) in ball" :key="index" :class="{'l-width' : wz == 11}">
                    <a class="balls" :class="{'selected' : item.selected}" @click="doBet(item.num,index)">{{item.num}}</a>
                    <template v-if="miss !== 0">
                        <template v-if="wz == 1">
                            <i v-if="index==0" class="f-mini c-3" :class="{'red':miss['dx'][0]['da']>10}">{{miss['dx'][0]['da']}}</i>
                            <i v-if="index==1" class="f-mini c-3" :class="{'red':miss['dx'][0]['sm']>10}">{{miss['dx'][0]['sm']}}</i>
                            <i v-if="index==2" class="f-mini c-3" :class="{'red':miss['dx'][0]['dan']>10}">{{miss['dx'][0]['dan']}}</i>
                            <i v-if="index==3" class="f-mini c-3" :class="{'red':miss['dx'][0]['shuang']>10}">{{miss['dx'][0]['shuang']}}</i>
                        </template>
                        <template v-if="wz == 2">
                            <i v-if="index==0" class="f-mini c-3" :class="{'red':miss['dx'][1]['da']>10}">{{miss['dx'][1]['da']}}</i>
                            <i v-if="index==1" class="f-mini c-3" :class="{'red':miss['dx'][1]['sm']>10}">{{miss['dx'][1]['sm']}}</i>
                            <i v-if="index==2" class="f-mini c-3" :class="{'red':miss['dx'][1]['dan']>10}">{{miss['dx'][1]['dan']}}</i>
                            <i v-if="index==3" class="f-mini c-3" :class="{'red':miss['dx'][1]['shuang']>10}">{{miss['dx'][1]['shuang']}}</i>
                        </template>
                        <template v-if="wz == 3">
                            <i v-if="index==0" class="f-mini c-3" :class="{'red':miss['dx'][2]['da']>10}">{{miss['dx'][2]['da']}}</i>
                            <i v-if="index==1" class="f-mini c-3" :class="{'red':miss['dx'][2]['sm']>10}">{{miss['dx'][2]['sm']}}</i>
                            <i v-if="index==2" class="f-mini c-3" :class="{'red':miss['dx'][2]['dan']>10}">{{miss['dx'][2]['dan']}}</i>
                            <i v-if="index==3" class="f-mini c-3" :class="{'red':miss['dx'][2]['shuang']>10}">{{miss['dx'][2]['shuang']}}</i>
                        </template>
                        <template v-if="wz == 4">
                            <i v-if="index==0" class="f-mini c-3" :class="{'red':miss['dx'][3]['da']>10}">{{miss['dx'][3]['da']}}</i>
                            <i v-if="index==1" class="f-mini c-3" :class="{'red':miss['dx'][3]['sm']>10}">{{miss['dx'][3]['sm']}}</i>
                            <i v-if="index==2" class="f-mini c-3" :class="{'red':miss['dx'][3]['dan']>10}">{{miss['dx'][3]['dan']}}</i>
                            <i v-if="index==3" class="f-mini c-3" :class="{'red':miss['dx'][3]['shuang']>10}">{{miss['dx'][3]['shuang']}}</i>
                        </template>
                        <template v-if="wz == 5">
                            <i v-if="index==0" class="f-mini c-3" :class="{'red':miss['dx'][4]['da']>10}">{{miss['dx'][4]['da']}}</i>
                            <i v-if="index==1" class="f-mini c-3" :class="{'red':miss['dx'][4]['sm']>10}">{{miss['dx'][4]['sm']}}</i>
                            <i v-if="index==2" class="f-mini c-3" :class="{'red':miss['dx'][4]['dan']>10}">{{miss['dx'][4]['dan']}}</i>
                            <i v-if="index==3" class="f-mini c-3" :class="{'red':miss['dx'][4]['shuang']>10}">{{miss['dx'][4]['shuang']}}</i>
                        </template>
                        <template v-if="wz == 6">
                            <i v-if="index==0" class="f-mini c-3" :class="{'red':miss['dx'][5]['da']>10}">{{miss['dx'][5]['da']}}</i>
                            <i v-if="index==1" class="f-mini c-3" :class="{'red':miss['dx'][5]['sm']>10}">{{miss['dx'][5]['sm']}}</i>
                            <i v-if="index==2" class="f-mini c-3" :class="{'red':miss['dx'][5]['dan']>10}">{{miss['dx'][5]['dan']}}</i>
                            <i v-if="index==3" class="f-mini c-3" :class="{'red':miss['dx'][5]['shuang']>10}">{{miss['dx'][5]['shuang']}}</i>
                        </template>
                        <template v-if="wz == 7">
                            <i v-if="index==0" class="f-mini c-3" :class="{'red':miss['dx'][6]['da']>10}">{{miss['dx'][6]['da']}}</i>
                            <i v-if="index==1" class="f-mini c-3" :class="{'red':miss['dx'][6]['sm']>10}">{{miss['dx'][6]['sm']}}</i>
                            <i v-if="index==2" class="f-mini c-3" :class="{'red':miss['dx'][6]['dan']>10}">{{miss['dx'][6]['dan']}}</i>
                            <i v-if="index==3" class="f-mini c-3" :class="{'red':miss['dx'][6]['shuang']>10}">{{miss['dx'][6]['shuang']}}</i>
                        </template>
                        <template v-if="wz == 8">
                            <i v-if="index==0" class="f-mini c-3" :class="{'red':miss['dx'][7]['da']>10}">{{miss['dx'][7]['da']}}</i>
                            <i v-if="index==1" class="f-mini c-3" :class="{'red':miss['dx'][7]['sm']>10}">{{miss['dx'][7]['sm']}}</i>
                            <i v-if="index==2" class="f-mini c-3" :class="{'red':miss['dx'][7]['dan']>10}">{{miss['dx'][7]['dan']}}</i>
                            <i v-if="index==3" class="f-mini c-3" :class="{'red':miss['dx'][7]['shuang']>10}">{{miss['dx'][7]['shuang']}}</i>
                        </template>
                        <template v-if="wz == 9">
                            <i v-if="index==0" class="f-mini c-3" :class="{'red':miss['dx'][8]['da']>10}">{{miss['dx'][8]['da']}}</i>
                            <i v-if="index==1" class="f-mini c-3" :class="{'red':miss['dx'][8]['sm']>10}">{{miss['dx'][8]['sm']}}</i>
                            <i v-if="index==2" class="f-mini c-3" :class="{'red':miss['dx'][8]['dan']>10}">{{miss['dx'][8]['dan']}}</i>
                            <i v-if="index==3" class="f-mini c-3" :class="{'red':miss['dx'][8]['shuang']>10}">{{miss['dx'][8]['shuang']}}</i>
                        </template>
                        <template v-if="wz == 10">
                            <i v-if="index==0" class="f-mini c-3" :class="{'red':miss['dx'][9]['da']>10}">{{miss['dx'][9]['da']}}</i>
                            <i v-if="index==1" class="f-mini c-3" :class="{'red':miss['dx'][9]['sm']>10}">{{miss['dx'][9]['sm']}}</i>
                            <i v-if="index==2" class="f-mini c-3" :class="{'red':miss['dx'][9]['dan']>10}">{{miss['dx'][9]['dan']}}</i>
                            <i v-if="index==3" class="f-mini c-3" :class="{'red':miss['dx'][9]['shuang']>10}">{{miss['dx'][9]['shuang']}}</i>
                        </template>
                        <template v-if="wz == 11">
                            <!--<i v-if="index==0" class="f-mini c-3" :class="{'red':miss['dx']['gyh']['da']>10}">{{miss['dx']['gyh']['da']}}</i>-->
                            <!--<i v-if="index==1" class="f-mini c-3" :class="{'red':miss['dx']['gyh']['sm']>10}">{{miss['dx']['gyh']['sm']}}</i>-->
                            <!--<i v-if="index==2" class="f-mini c-3" :class="{'red':miss['dx']['gyh']['dan']>10}">{{miss['dx']['gyh']['dan']}}</i>-->
                            <!--<i v-if="index==3" class="f-mini c-3" :class="{'red':miss['dx']['gyh']['shuang']>10}">{{miss['dx']['gyh']['shuang']}}</i>-->
                            <i v-if="index==0" class="f-mini c-3">{{gyhGain[0]}}</i>
                            <i v-if="index==1" class="f-mini c-3">{{gyhGain[1]}}</i>
                            <i v-if="index==2" class="f-mini c-3">{{gyhGain[2]}}</i>
                            <i v-if="index==3" class="f-mini c-3">{{gyhGain[3]}}</i>
                        </template>
                    </template>
                    <template v-else>
                        <i class="f-mini c-3">--</i>
                    </template>
                </li>
                <!--<mt-button size="small" plain class="chose-all" @click.native="choseAll" v-if="type < 6">全选</mt-button>-->
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        props:['betNum','miss','wz','gyhGain'],
        data () {
            return {
                ball: [
                    {num:'大',selected:0},
                    {num:'小',selected:0},
                    {num:'单',selected:0},
                    {num:'双',selected:0}
                ],
            }
        },
        computed:{
            text(){
                const wzVal = {
                    1 : '第一位',
                    2 : '第二位',
                    3 : '第三位',
                    4 : '第四位',
                    5 : '第五位',
                    6 : '第六位',
                    7 : '第七位',
                    8 : '第八位',
                    9 : '第九位',
                    10 : '第十位',
                    11 : '冠亚和'
                }
                return wzVal[this.wz]
            }
        },
        watch:{
            betNum(val){
                if(!val.length){
                    for(let i in this.ball){
                        this.ball[i].selected = 0;
                    }
                }
            }
        },
        methods:{
            //投注选号状态
            doBet(num,i){
                var chose = this.betNum.indexOf(num)
                if(chose > -1){
                    this.betNum.splice(chose, 1)
                }else {
                    this.betNum.push(num)
                }
                //改变球的选中状态
                this.ball[i].selected = this.ball[i].selected ? 0 : 1
            },
            //全选
            choseAll(){
                for(let i in this.ball){
                    let a = this.betNum.indexOf(this.ball[i].num)
                    if(a == -1){
                        this.betNum.push(this.ball[i].num)
                        this.ball[i].selected = 1;
                    }
                }
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
    .bet-box ul.balls-box li.l-width{
        width: 60px;
    }
</style>
