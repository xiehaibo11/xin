<!--奖金优化-->
<template>
    <div class="optimal-bonus">
        <mt-header :title="title">
            <mt-button icon="back" slot="left" @click.native="back"></mt-button>
            <mt-button slot="right" @click="agreeVisible = true">说明</mt-button>
        </mt-header>
        <div class="bonus-head tc">
            <em class="f-sm">计划购买：</em> <input-number v-model="money" :min="min" :step="2" size="small" @handle-up="changeNotes('up')"  @handle-down="changeNotes('down')"></input-number> <em class="f-sm">{{lotteryUnit}}</em>
        </div>
        <div class="bonus-cont contentH" :style="{height:contentH + 'px'}">
            <mt-navbar v-model="selected" @click.native="handleType">
                <mt-tab-item id="1">平均优化</mt-tab-item>
                <mt-tab-item id="2">博热优化</mt-tab-item>
                <mt-tab-item id="3">博冷优化</mt-tab-item>
            </mt-navbar>
            <div class="card bonus-gounp">
                <table cellspacing="0" cellpadding="0" border="0" width="100%" class="table-bonus-list">
                    <tr>
                        <th>过关</th>
                        <th>单注组合</th>
                        <th>注数</th>
                        <th>预测奖金</th>
                    </tr>
                    <tr v-for="(item,index) in splitBetList">
                        <td align="center">{{item.type_text}}</td>
                        <td>
                            <div class="flex-box grounp-item" v-for="match in item.zuhe">
                                <span class="flex tl">{{match.homename}}</span>
                                <span>{{match.playsign}}({{match.pl}})</span>
                            </div>
                        </td>
                        <td align="center">
                            <input class="money-input" type="tel" v-model="item.notes"
                                                  onkeyup="if(this.value.length==1){this.value=this.value.replace(/\D/g,'1')}else{this.value=this.value.replace(/\D/g,'1')}"
                                                  onafterpaste="if(this.value.length==1){this.value=this.value.replace(/\D/g,'1')}else{this.value=this.value.replace(/\D/g,'1')}">
                        </td>
                        <td align="center" :class="{'red' : listShowGain(item.gain,item.notes) >= money}">{{listShowGain(item.gain,item.notes)}}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="bet-multiple-set tc">
            <em class="f-sm">投</em> <input-number v-model="multiple" :min="1" :step="1" size="small"></input-number> <em class="f-sm">倍</em>
        </div>
        <div class="bet-submit flex-box">
            <div class="acount-award flex tr">
                <p class="f-sm">{{notes}}注 <b class="red">{{notes*2 * multiple}}</b>{{lotteryUnit}}</p>
                <p class="c-3 f-mini">预计奖金<em :class="{'red' : maxGain > 0}"><em v-if="minGain !== maxGain">{{minGain}}~</em>{{maxGain}}</em>{{lotteryUnit}}</p>
            </div>
            <button class="bet-btn btn-sure" @click="submitOrder">付款</button>
        </div>

        <!--说明-->
        <mt-popup
            class="agree-poput"
            v-model="agreeVisible"
            position="right">
            <div class="visible-cont">
                <mt-header title="奖金优化帮助">
                    <mt-button icon="back" slot="left" @click.native="agreeVisible = false">关闭</mt-button>
                </mt-header>
                <div class="service-cont" style="line-height: 1.7">
                    <div class="help-jjyh-box">
                        <div class="itm-col">
                            <h3 class="itm-hd">● 什么是奖金优化？</h3>
                            <div class="itm-bd">
                                <p class="mb10">
                                    奖金优化是本站开发的一种通过对方案注数进行调整，从而实现对方案金额及奖金进行优化的功能。用户可以根据自己的需要，灵活对自己的倍投复选方案进行注数调节，从而获得您满意的方案投注和方案回报。 </p>
                            </div>
                        </div>
                        <div class="itm-col mt">
                            <h3 class="itm-hd">● 优化方式说明</h3>
                            <div class="itm-bd">
                                <p class="mb10"><b>平均优化：</b>即按照用户选择的一定金额，让方案中的所有单注奖金趋于平均，这样不管原始方案的哪个选项中出，最后都可以收获差不多的奖金。即使命中都是最低赔，奖金也会远大于本金，不会再出现中奖不盈利的尴尬。
                                </p>
                                <p class="mb10"><b>博热优化：</b>即在其它单注奖金可以保本的前提下，将剩余注数全部分配到出现概率最高的单注上，即所有比赛一赔全出的单注上。既能保证命中其它热门注时无亏损，又可保证在搏冷门时，拿到最高价值的回报率。
                                </p>
                                <p class="mb10"><b>博冷优化：</b>即在其它单注奖金可以保本的前提下，将剩余注数全部分配到回报最高的单注上，即所有比赛最高赔全出的单注上。投注的本金可以足够多得投注在热门的场次上，让命中热门场次收益最大化。
                                </p>
                            </div>
                        </div>
                    </div>
                    <!--<div class="mf-sm">-->
                        <!--<h4>一、什么是奖金优化</h4>-->
                        <!--<p>奖金优化就是针对投注的复式倍投神器，用户可以根据自己的需求，自行对单注注数进行分配的功能，我们提供了三种快捷的优化方式：平均优化，博热优化，搏冷优化-->
                        <!--助您合理分配投注倍数，提高中奖收益。</p>-->
                        <!--<p>平均优化：以方案单注奖金趋于平均的规则进行优化。</p>-->
                        <!--<p>博热优化：在方案单注保本前提下，使概率最高的单注奖金最大化。</p>-->
                        <!--<p>博冷优化：在方案单注保本前提下，使奖金最高的单注奖金最大化。</p>-->
                    <!--</div>-->
                    <!--<div>-->
                        <!--<h4>二、奖金优化支持的过关方式</h4>-->
                        <!--<p>(1)最多支持对用户选择的15比赛串关进行优化。</p>-->
                        <!--<p>(2)在平均优化方案无法确保每种组合方案保本的情况下，博热优化和搏冷优化将保持同平均优化方案一致。</p>-->
                    <!--</div>-->
                </div>
            </div>
        </mt-popup>
        <!--说明 end-->

    </div>
</template>

<script>
    import inputNumber from 'components/common/InputNumber.vue' //购买输入框组件
    export default {
        name: 'optimalBonus',
        components:{
            inputNumber
        },
        data () {
            return {
                agreeVisible:false,
                min: JSON.parse(sessionStorage.getItem("jczq_gg_type"))['money'], //最少购买金额
                money: JSON.parse(sessionStorage.getItem("jczq_gg_type"))['money'], //计划购买金额
                selected : '1', //优化选项
                multiple : 1, //倍数
                type:JSON.parse(sessionStorage.getItem("jczq_gg_type"))['type'] || [] ,//过关方式

                betMap:{
                    269 : '非让球',
                    354 : '主让球',
                    270 : '总进球',
                    271 : '比分',
                    272 : '半全场'
                },
                splitMatchArr:[], //拆分-投注赛事数组
                splitBetList:[], //拆分-投注赛事列表
            }
        },
        computed:{
            contentH(){
                return this.$store.state.clientHeight - 173
            },
            //单位
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            title(){
                return this.$route.meta.title
            },
            plan(){
                return this.$store.state.jczq.plan
            },
            //所选赛事列表
            tempMatchData(){
                return this.$store.state.jczq.tempMatchData
            },
            //过关赛事组合（计算注数+解析投注数据，更新奖金组、注数组）
            notes(){
                let num = 0
                for(let i in this.splitBetList){
                    num += Number(this.splitBetList[i].notes)
                }
                return num
            },
            //最小奖金
            minGain(){
                return this.$bet.accMul(Number(this.$bet.getMaxMin(this.gainArr,'min')),this.multiple,3) || 0
            },
            //最大奖金
            maxGain(){
                let res = 0
                for(let i in this.gainArr){
                    res = this.$bet.accAdd(res,Number(this.gainArr[i]),3)
                }
                return this.$bet.accMul(res,this.multiple,3)
            },
            //单注预计奖金
            listShowGain(){
                return (g,n)=>{
                    return this.$bet.accMul(Number(g),Number(n),3)
                }
            },

            //优化使用的相关数组
            gainArr(){ //预计奖金组
                let gain_arr = []
                for(let i in this.splitBetList){
                    let curPl = Number(this.splitBetList[i].gain)
                    let curNotes = Number(this.splitBetList[i].notes)
                    let count = this.$bet.accMul(curPl,curNotes,3)
                    gain_arr.push(count)
                }
                return gain_arr
            },
            //注数组
            noteArr(){
                let arr = []
                for(let i in this.splitBetList){
                    let curNotes = Number(this.splitBetList[i].notes)
                    arr.push(curNotes)
                }
                return arr
            },
            //赔率组
            plArr(){
                let arr = []
                for(let i in this.splitBetList){
                    let curPl = Number(this.splitBetList[i].gain)
                    arr.push(curPl)
                }
                return arr
            },
            //奖金小于购买金额的
            lessTmoney(){
                let arr = []
                for(let i in this.splitBetList){
                    let curPl = Number(this.splitBetList[i].gain)
                    let curNotes = Number(this.splitBetList[i].notes)
                    let count = this.$bet.accMul(curPl,curNotes,3)
                    if(count < this.money+2){
                        arr.push(count)
                    }
                }
                return arr
            },
            //拥有最高注数的奖金组
            maxNoteGain(){
                let arr = []
                for(let i in this.splitBetList){
                    let curNotes = Number(this.splitBetList[i].notes)
                    let curPl = Number(this.splitBetList[i].gain)
                    let count = this.$bet.accMul(curPl,curNotes,3)
                    let maxNotes = this.$bet.getMaxMin(this.noteArr,'max') //获取最高注数
                    if(curNotes == maxNotes && maxNotes !== 1){ //提取最高注数对应的奖金组
                        arr.push(count)
                    }
                }
                return arr
            },
        },
        watch:{
            money(val){
                console.log(val)
                if(val % 2 !==0){
                    this.money = this.money + 1
                }
                this.resetBetList();
                for(let j = this.min ; j < this.money ; j = j + 2){
                    this.changeNotes('up');
                }
            }
        },
        methods:{
            back(){
                this.$router.goBack(-1);//返回上一层
            },
            //解析赛事列表
            playMap(playsign){
                let spfMap = {
                    0 : '主负',
                    1 : '平',
                    3 : '主胜',
                }
                return spfMap.hasOwnProperty(playsign) ? spfMap[playsign] : playsign
            },
            //单场赛事组合
            getMatchArr(){
                let arr = []
                //处理投注数据
                for(let mid in this.plan){
                    let str = []
                    for(let p in this.plan[mid]){
                        for(let info in this.plan[mid][p]){
                            str.push(mid + '#' + p + '#'+ info + '#' + this.plan[mid][p][info])
                        }
                    }
                    arr.push(str);
                }
                let resArr = [] //单注组合
                for(let t in this.type){
                    let _selfs = new Array(this.type[t]);
                    var _indexs = [0,1,2,3,4,5,6,7,8];
                    var _where = 0;
                    let newArr = [];
                    let sBetArr = []
                    this.$bet.plzh(_selfs, arr, _indexs, newArr, _where); //赛事组合
                    for(let n in newArr){
                        //单注玩法组合
                        let res = this.$bet.serialArray(newArr[n])
                        for(let m in res){
                            sBetArr.push(res[m])
                        }
                    }
                    for(let r in sBetArr){
                        resArr.push(sBetArr[r])
                    }
                }
                this.$set(this,'splitMatchArr',resArr);
            },
            //赛事组合解析
            getMatchList(){
                console.time();
                this.getMatchArr();
                //解析投注数据，用于奖金优化和奖金计算
                let arr = [];
                for(let i in this.splitMatchArr){
                    let match = {}
                    let iArr = this.splitMatchArr[i].split('&')
                    match['type_text'] = iArr.length + '串1'
                    match['type'] = iArr.length + '_1'
                    match['plan'] = this.splitMatchArr[i]
                    match['zuhe'] = []
                    for(let m in iArr){
                        let mArr = iArr[m].split('#')
                        let list = {}
                        list['homename'] = this.tempMatchData[mArr[0]].homename //主战队名
                        list['playname'] = this.betMap[mArr[1]] //玩法
                        list['playsign'] = mArr[1] == 270 ? mArr[2] + '球' : this.playMap(mArr[2]) //标识
                        list['pl'] = mArr[3] //赔率
                        match['zuhe'].push(list)
                    }
                    let gain = 1
                    for(let p in match['zuhe']){
                        gain = this.$bet.accMul(Number(gain),Number(match['zuhe'][p].pl))
                    }
                    match['gain'] = this.$bet.accMul(gain,2,3)
                    match['notes'] = 1
                    arr.push(match)
                }
                this.$set(this,'splitBetList',arr)
                console.timeEnd();
            },
            //改变计划金额时更改列表数据（平均优化）
            changeNotes(e){
                let n = 0
                let resVal = '' //最高或者最低赔率
                if(this.selected == 2){ resVal = this.$bet.getMaxMin(this.plArr,'min')} //博热优化(最小赔率)
                if(this.selected == 3){ resVal = this.$bet.getMaxMin(this.plArr,'max')} //博冷优化(最大赔率)

                for(let i in this.splitBetList){
                    let curNotes = Number(this.splitBetList[i].notes)
                    let curPl = Number(this.splitBetList[i].gain)
                    let count = this.$bet.accMul(curPl,curNotes,3) //当前奖金 单注奖金 * 注数

                    //增加计划金额
                    if(e == 'up'){
                        //平均优化
                        if(this.selected == 1){
                            if(count == this.minGain){
                                this.splitBetList[i].notes +=1
                                return
                            }
                        }else {
                            //保证每注盈利
                            if(this.lessTmoney.length > 0){
                                if(count == this.minGain){ //同平均优化
                                    this.splitBetList[i].notes +=1
                                    return
                                }
                            }else {
                                if(curPl == resVal){
                                    this.splitBetList[i].notes += 1
                                    return
                                }
                            }
                        }
                    }
                    //减少计划金额
                    if(e == 'down'){
                        if(this.min == this.money){
                            this.$toast('最少投' + this.min/2 + '注')
                            return
                        }
                        if(this.selected == 1){ //平均优化
                            if(count == this.$bet.getMaxMin(this.maxNoteGain,'max')){ //最高注数中的最高奖金减少
                                this.splitBetList[i].notes -=1
                                return
                            }
                        }else {
                            if(curPl !== resVal){ //判断其他赔率是否有可减少1注的情况
                                if(this.$bet.accMul(curPl,curNotes - 1,3) >= (this.money-2) && this.splitBetList[i].notes > 1){
                                    this.splitBetList[i].notes -=1
                                    n +=1
                                    return
                                }
                            }
                        }
                    }
                }
                if(!n){
                    //减少赔率最大或最小注数
                    for(let i in this.splitBetList){
                        let curPl = Number(this.splitBetList[i].gain)
                        if(curPl == resVal && this.splitBetList[i].notes !== 1){ //减少最高或最低赔率的购买注数
                            this.splitBetList[i].notes -=1
                            return
                        }
                    }
                }
            },
            //重置注数为1
            resetBetList(){
                for(let i in this.splitBetList){
                    this.splitBetList[i].notes = 1
                }
            },
            //切换优化方式
            handleType(){
                this.resetBetList();
                for(let j = this.min ; j < this.money ; j = j + 2){
                    this.changeNotes('up');
                }
            },
            //提交订单
            submitOrder(){
                this.$axios.post('/web/ssc/betting',{
                    plan: JSON.stringify(this.splitBetList),
                    multiple: this.multiple
                }).then(({data})=>{
                    console.log(data)
                })
            },
            //自定义单注注数
            change(val){
                console.log(val)
                val = !val ? 1 : parseInt(val)
                this.money = this.notes * 2
            }
        },
        created(){
            this.$nextTick(()=>{
                this.getMatchList();
            })
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .bonus-head{
        padding: 10px;
    }
    .bet-multiple-set{
        padding: 5px 0;
        border-top:1px solid #eee;
    }
    .bet-submit{
        padding: 3px 3px 3px 10px;
        border-top:1px solid #eeeeee;
        background-color: #ffffff;
        .acount-award{
            line-height: 1.5;
        }
        .btn-sure{
            background-color: $bColor;
            color: #ffffff;
            margin-left: 5px;
            padding: 8px 15px;
        }
    }
    .table-bonus-list{
        margin-top: 3px;
        font-size: 14px;
        th{
            background-color: #f4f4f4;
            padding:8px 5px;
        }
        td{
            padding: 5px;
            border:1px solid #eeeeee;
        }
        .grounp-item{
            color: #888888;
            font-size: 12px;
            line-height: 1.4;
        }
    }
    .money-input{
        width: 50px;height: 26px;line-height: 26;border-radius: 4px;background-color: #efefef;
        border: none ;text-align: center
    }
</style>
