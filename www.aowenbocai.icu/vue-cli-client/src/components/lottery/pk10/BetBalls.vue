<template>
    <div class="bet-box">
        <div class="flex-box fast-select-box" v-if="type < 6 || type == 11">
            <div class="fast-title">{{text}}</div>
            <ul class="flex-box fast-list">
                <li class="flex" @click="choseAll">全</li>
                <li class="flex" @click="choseBig">大</li>
                <li class="flex" @click="choseSmall">小</li>
                <li class="flex" @click="choseJs">奇</li>
                <li class="flex" @click="choseOs">偶</li>
                <li class="flex" @click="clear">清</li>
            </ul>
        </div>
        <div class="bet-box flex-box">
            <div class="label">
                <p v-if="type < 6 || type == 11"><em style="letter-spacing: 12px">选</em>号</p>
                <p v-else>{{text}}</p>
                <p><em style="letter-spacing: 12px">遗</em>漏</p>
            </div>
            <div class="flex">
                <ul class="balls-box">
                    <li v-for="(item,index) in ball" :key="index">
                        <a class="balls" :class="{'selected' : item.selected}" @click="doBet(item.num,index)">{{item.num}}</a>
                        <template v-if="miss !== 0">
                            <i class="f-mini c-3" v-if="wz==1" :class="{'red':miss['pt'][0][index]>10}">{{miss['pt'][0][index]}}</i>
                            <i class="f-mini c-3" v-if="wz==2" :class="{'red':miss['pt'][1][index]>10}">{{miss['pt'][1][index]}}</i>
                            <i class="f-mini c-3" v-if="wz==3" :class="{'red':miss['pt'][2][index]>10}">{{miss['pt'][2][index]}}</i>
                            <i class="f-mini c-3" v-if="wz==4" :class="{'red':miss['pt'][3][index]>10}">{{miss['pt'][3][index]}}</i>
                            <i class="f-mini c-3" v-if="wz==5" :class="{'red':miss['pt'][4][index]>10}">{{miss['pt'][4][index]}}</i>
                            <i class="f-mini c-3" v-if="wz==6" :class="{'red':miss['pt'][5][index]>10}">{{miss['pt'][5][index]}}</i>
                            <i class="f-mini c-3" v-if="wz==7" :class="{'red':miss['pt'][6][index]>10}">{{miss['pt'][6][index]}}</i>
                            <i class="f-mini c-3" v-if="wz==8" :class="{'red':miss['pt'][7][index]>10}">{{miss['pt'][7][index]}}</i>
                            <i class="f-mini c-3" v-if="wz==9" :class="{'red':miss['pt'][8][index]>10}">{{miss['pt'][8][index]}}</i>
                            <i class="f-mini c-3" v-if="wz==10" :class="{'red':miss['pt'][9][index]>10}">{{miss['pt'][9][index]}}</i>
                        </template>
                        <template v-else>
                            <i class="f-mini c-3">--</i>
                        </template>
                    </li>
                    <!--<mt-button size="small" plain class="chose-all" @click.native="choseAll" v-if="type < 6 || type == 11">全选</mt-button>-->
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props:['betNumP','miss','wz','type'],
        model:{
            prop: 'betNumP',
            event: 'change'
        },
        data () {
            return {
                ball: [
                    {num:'01',selected:this.betNumP.indexOf('01')>-1 ? 1 : 0},
                    {num:'02',selected:this.betNumP.indexOf('02')>-1 ? 1 : 0},
                    {num:'03',selected:this.betNumP.indexOf('03')>-1 ? 1 : 0},
                    {num:'04',selected:this.betNumP.indexOf('04')>-1 ? 1 : 0},
                    {num:'05',selected:this.betNumP.indexOf('05')>-1 ? 1 : 0},
                    {num:'06',selected:this.betNumP.indexOf('06')>-1 ? 1 : 0},
                    {num:'07',selected:this.betNumP.indexOf('07')>-1 ? 1 : 0},
                    {num:'08',selected:this.betNumP.indexOf('08')>-1 ? 1 : 0},
                    {num:'09',selected:this.betNumP.indexOf('09')>-1 ? 1 : 0},
                    {num:'10',selected:this.betNumP.indexOf('10')>-1 ? 1 : 0}
                ],
                betNum:this.betNumP
            }
        },
        computed:{
            text(){
                if(this.wz==1){
                    return '第一位'
                }
                if(this.wz==2){
                    return '第二位'
                }
                if(this.wz==3){
                    return '第三位'
                }
                if(this.wz==4){
                    return '第四位'
                }
                if(this.wz==5){
                    return '第五位'
                }
                if(this.wz==6){
                    return '第六位'
                }
                if(this.wz==7){
                    return '第七位'
                }
                if(this.wz==8){
                    return '第八位'
                }
                if(this.wz==9){
                    return '第九位'
                }
                if(this.wz==10){
                    return '第十位'
                }
            }
        },
        watch:{
            betNumP(val){
                if(!val.length){
                    this.betNum = []
                    for(let i in this.ball){
                        this.ball[i].selected = 0;
                    }
                }
                this.$emit('change',val)
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
                this.$emit('change',this.betNum)
            },
            //全选
            choseAll(){
                this.clear();
                for(let i in this.ball){
                    let a = this.betNum.indexOf(this.ball[i].num)
                    if(a == -1){
                        this.betNum.push(this.ball[i].num)
                        this.ball[i].selected = 1;
                    }
                }
                this.$emit('change',this.betNum)
            },
            //大
            choseBig(){
                this.clear();
                for(var i in this.ball){
                    if(i > 4){
                        this.betNum.push(this.ball[i].num)
                        this.ball[i].selected = 1
                    }else {
                        this.ball[i].selected = 0
                    }
                }
                this.$emit('change',this.betNum)
            },
            //小
            choseSmall(){
                this.clear();
                for(var i in this.ball){
                    if(i <= 4){
                        this.betNum.push(this.ball[i].num)
                        this.ball[i].selected = 1
                    }else {
                        this.ball[i].selected = 0
                    }
                }
                this.$emit('change',this.betNum)
            },
            //奇
            choseJs(){
                this.clear();
                for(var i in this.ball){
                    if(i%2 ==0){
                        this.betNum.push(this.ball[i].num)
                        this.ball[i].selected = 1
                    }else {
                        this.ball[i].selected = 0
                    }
                }
                this.$emit('change',this.betNum)
            },
            //偶
            choseOs(){
                this.clear();
                for(var i in this.ball){
                    if(i%2 !==0){
                        this.betNum.push(this.ball[i].num)
                        this.ball[i].selected = 1
                    }else {
                        this.ball[i].selected = 0
                    }
                }
                this.$emit('change',this.betNum)
            },
            clear(){
                this.betNum = [];
                for(let i in this.ball){
                    this.ball[i].selected = 0;
                }
                this.$emit('change',this.betNum)
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
