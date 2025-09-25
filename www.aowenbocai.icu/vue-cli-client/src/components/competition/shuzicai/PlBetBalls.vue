<template>
    <div class="bet-box">
        <div class="flex-box fast-select-box">
            <div class="fast-title">{{wzText}}</div>
            <ul class="flex-box fast-list">
                <li class="flex" @click="choseAll">全</li>
                <li class="flex" @click="choseBig">大</li>
                <li class="flex" @click="choseSmall">小</li>
                <li class="flex" @click="choseJs">奇</li>
                <li class="flex" @click="choseOs">偶</li>
                <li class="flex" @click="clear">清</li>
            </ul>
        </div>
        <div class="flex-box bet-box">
            <div class="label">
                <p>选号</p>
                <p>遗 漏</p>
            </div>
            <div class="flex">
                <ul class="balls-box">
                    <li v-for="(item,index) in ball" :key="index">
                        <a class="balls" :class="{'selected' : item.selected}" @click="doBet(item.num,index)">{{item.num}}</a>
                        <template v-if="miss !== 0">
                            <i class="f-mini c-3" :class="{'red':0}">1</i>
                        </template>
                        <template v-else>
                            <i class="f-mini c-3">--</i>
                        </template>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props:['betNumP','miss','wzText'],
        model:{
            prop: 'betNumP',
            event: 'change'
        },
        data () {
            return {
                ball: [
                    {num:'0',selected:this.betNumP.indexOf('0')>-1 ? 1 : 0},
                    {num:'1',selected:this.betNumP.indexOf('1')>-1 ? 1 : 0},
                    {num:'2',selected:this.betNumP.indexOf('2')>-1 ? 1 : 0},
                    {num:'3',selected:this.betNumP.indexOf('3')>-1 ? 1 : 0},
                    {num:'4',selected:this.betNumP.indexOf('4')>-1 ? 1 : 0},
                    {num:'5',selected:this.betNumP.indexOf('5')>-1 ? 1 : 0},
                    {num:'6',selected:this.betNumP.indexOf('6')>-1 ? 1 : 0},
                    {num:'7',selected:this.betNumP.indexOf('7')>-1 ? 1 : 0},
                    {num:'8',selected:this.betNumP.indexOf('8')>-1 ? 1 : 0},
                    {num:'9',selected:this.betNumP.indexOf('9')>-1 ? 1 : 0}
                ],
                betNum:this.betNumP
            }
        },
        computed:{

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
                    if(i%2 !==0){
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
                    if(i%2 ==0){
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
<style scoped type="text/scss" lang="scss">
    .bet-box ul.balls-box{
        padding: 0 10px;
    }
    .bet-box ul.balls-box li{
        width: 20%;
    }
</style>
