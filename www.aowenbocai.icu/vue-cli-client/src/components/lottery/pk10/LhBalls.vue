<template>
    <div class="bet-box flex-box">
        <div class="label" style="width: 80px;margin-right: 10px">
            <p style="color: #333">{{text}}</p>
            <p><em style="letter-spacing: 40px">赔</em>率</p>
        </div>
        <div class="flex">
            <ul class="balls-box">
                <li v-for="(item,index) in ball" :key="index">
                    <a class="balls" :class="{'selected' : item.selected}" @click="doBet(item.num,index)">{{item.num}}</a>
                    <i class="f-mini c-3">{{gain}}</i>
                </li>
                <!--<mt-button size="small" plain class="chose-all" @click.native="choseAll">全选</mt-button>-->
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        props:['betNum','gain','wz'],
        data () {
            return {
                ball: [
                    {num:'龙',selected:0},
                    {num:'虎',selected:0}
                ],
            }
        },
        computed:{
            text(){
                if(this.wz==1){
                    return '冠军 vs 第十'
                }
                if(this.wz==2){
                    return '亚军 vs 第九'
                }
                if(this.wz==3){
                    return '季军 vs 第八'
                }
                if(this.wz==4){
                    return '第四 vs 第七'
                }
                if(this.wz==5){
                    return '第五 vs 第六'
                }
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
</style>
