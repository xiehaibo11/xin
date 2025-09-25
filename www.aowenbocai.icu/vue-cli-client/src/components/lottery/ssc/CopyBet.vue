<!--单式投注-->
<template>
    <div class="copy-box">
        <div class="contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">从01-11中手动输入{{n}}个号码进行购买，从01-11共11个号码中选择{{n}}个号码进行购买,
                <em v-if="type == 1.3">三个奖级通吃，五次中奖机会，</em>
                <em v-if="type == 2.12 || type == 2.13 || type == 3.12 || type == 3.13 || type == 4.12 || type == 4.13 || type == 5.6 || type == 10.6 || type == 6.2">且不能有重复，</em>
                奖金<em class="red">{{gain}}</em>{{lotteryUnit}}。</div>
            <div class="copydiv">
                <div class="pasterIntro mf-sm">
                    <strong>格式说明：</strong>
                    <p>
                        (1)每位之间以英文字符逗号(",")或者空格分割；<br>
                        (2)每行填写一注投注号码；<br>
                        单注示例：
                        <template v-if="parseInt(type) == 1">1,2,4,5,6 或 1 2 4 5 6</template>
                        <template v-if="type == 2.11 || type == 3.11 || type == 4.11">3,4,5 或 3 4 5</template>
                        <template v-if="type == 2.12 || type == 3.12 || type == 4.12">3,4 或 3 4</template>
                        <template v-if="type == 2.13 || type == 3.13 || type == 4.13">3,4,5 或 3 4 5</template>
                        <template v-if="parseInt(type) == 5">3,4或 3 4</template>
                        <template v-if="parseInt(type) == 10">1,2或 1 2</template>
                        <template v-if="parseInt(type) == 6">3</template>
                    </p>
                </div>
                <mt-field placeholder="请按照格式说明输入或粘贴投注号码，最多输入500注。" type="textarea" rows="6" v-model="copyValue"></mt-field>
                <div class="copy_err red f-sm mt-sm" v-if = "errCont.length > 0 && show">
                    <div>共有{{errCont.length}}行错误！</div>
                    <div>
                        <p v-for="(item,index) in errCont">
                            <span>第{{item.row}}行</span> <span>{{item.num}}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!--底部 start-->
        <div class="bet-foot">
            <div class="gain-box f-sm border-top-1px">
                <div class="flex-box ks-unit">
                    <div class="flex">
                        <select-unit v-if="betModel == 1 && unitIsOpen"></select-unit><!--模式1 元角分厘模式开启时显示-->
                    </div>
                    <div class="flex" v-if="rebateIsOpen && isRebate">
                        <rebate-range :gain="gain"></rebate-range>
                    </div>
                </div>
            </div>
            <div class="flex-box notes-box">
                <div class="flex bet-detail">
                    <!--<button class="bet-btn btn-clear"></button>-->
                    <div class="cf">
                        <i class="iconfont icon-shanchu btn-icon-clear fl"  @click="clear"></i>
                        <span class="fl f-sm">
                            <em class="total-money">{{notes}}</em>注
                            最多500注
                        </span>
                    </div>
                </div>
                <div class="tc bet-basket" style="margin: 0 15px 0 5px">
                    <button class="bet-btn btn-basket" @click="addBetBasket">+ 号码篮 <mt-badge class="badge" type="error" size="small">{{badge}}</mt-badge></button>
                </div>
                <button class="bet-btn btn-sure" @click="submitOrder">立即投注</button>
            </div>
        </div>
        <!--底部 end-->
    </div>
</template>

<script>
    import SelectUnit from 'components/lottery/SelectUnit.vue' //倍数、单位设置组件
    import RebateRange from 'components/lottery/RebateRange.vue' //返点设置
    export default {
        components:{
            SelectUnit,
            RebateRange
        },
        name: 'copyBet',
        props:['type','n','gain','betModel','title','text','name'],
        data:function(){
            return{
                copyValue:'', //textarea内容
                errCont:[], //错误号码内容组
                resArr:[],//最终投注号码组
                notes:0,
                show:false
            }
        },
        watch:{
            copyValue(){
                this.show = false
                this.check();
            }
        },
        computed:{
            //返点设置是否开启
            rebateIsOpen(){
                return this.$store.state.setting.rebate_isOpen == 1 ? true : false
            },
            //用户返点为零时不显示
            isRebate(){
                return this.$store.state.lottery.userRebate == 0 ? false : true
            },
            //是否开启合买
            joinOpen(){
                return this.$store.state.setting.join_isOpen == 1 ? true : false
            },
            //模式1下 元角分模式是否开启
            unitIsOpen(){
                return this.$store.state.setting.unit_isOpen == 1 ? true : false
            },
            //模式2下每注的最低金额
            minMoney(){
                return this.$store.state.setting.mode2_min_money
            },
            //返点值
            rebateVal(){
                return this.$store.getters.rebateVal
            },
            //单位
            label(){
                return this.$store.state.lottery.label
            },
            //单位值
            value(){
                return this.$store.state.lottery.value
            },
            //比例
            scale(){
                return this.$store.getters.getScale
            },
            //倍数
            multiple(){
                return this.$store.state.lottery.multiple
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit;
            },
            //选号容器高度计算
            contentH(){
                let h = this.betModel == 1 && this.unitIsOpen || this.rebateIsOpen && this.isRebate ? 89 : 52 //底部高度
//                let a = this.notes > 0 && this.betModel == 1 ? 30 : 0
                return this.$store.state.clientHeight - 100 - h
            },
            //选号篮数量
            badge(){
                return this.$store.state.lottery.betArr.length
            },
        },
        methods:{
            check(){
                this.$set(this,'errCont',[])
                this.$set(this,'resArr',[])
                this.notes = 0
                var betArr = this.copyValue.replace(/\s+$/g,'').split(/[(\r\n)\r\n]+/) || [] //去掉空白字符 以回车分割
                //检测每注的合法性
                var errData
                var betNumArr = ['0','1','2','3','4','5','6','7','8','9']//号码选号组
                for(var i = 0 ; i < betArr.length; i++){
                    //检测每个号码
                    var hasSame = false
                    var hasErrNum = false
                    var str = betArr[i].replace(/\s/g,',').split(',') || []
                    for(var j = 0;j < str.length ; j++){
                        if(this.type == 2.12 || this.type == 2.13 || this.type == 3.12 || this.type == 3.13 || this.type == 4.12 || this.type == 4.13 || this.type == 5.6 || this.type == 10.6 || this.type == 6.2) {
                            if (str[j] == str[j + 1]) {
                                hasSame = true
                            }
                        }//组选不能有相同的
                        if(!this.$base.isExit(betNumArr,str[j])){hasErrNum = true}
                    }
                    if(hasErrNum || hasSame || str.length !== this.n){
                        errData = {
                            'row' : i + 1,
                            'num' : betArr[i]
                        }
                        this.errCont.push(errData)
                    }else {
                        this.resArr.push(betArr[i])
                        this.notes +=1
                    }
                }
            },
            clear() {
                this.$set(this,'errCont',[])
                this.$set(this,'resArr',[])
                this.$set(this,'copyValue','')
            },
            addNum(betNum){
                if(this.type == 2.12 || this.type == 2.13 || this.type == 3.12 || this.type == 3.13 || this.type == 4.12 || this.type == 4.13 || this.type == 5.6 || this.type == 10.6 || this.type == 6.2){ //组选
                    betNum = betNum.replace(/ /g,',')
                }else {
                    betNum =betNum.replace(/ |,/g, (matchStr)=> {
                        var tokenMap = {
                            ' ': '|',
                            ',': '|'
                        };
                        return tokenMap[matchStr];
                    });
                }
                let data = {}
                data['num'] = betNum
                data['notes'] = this.type == 2.12 || this.type == 3.12 || this.type == 4.12 ? 2 : 1
                data['type_text'] = this.text
                data['type'] = this.type
                if(this.rebateIsOpen && this.isRebate){
                    data['rebate'] =this.rebateVal
                }
                if(this.betModel == 1){ //模式1
                    if(this.unitIsOpen){ //元角分模式
                        data['multiple'] = this.multiple//投注倍数
                        data['unit'] = this.label//投注单位
                        data['unit_value'] = this.value //投注单位值
                        data['money'] = this.$bet.accMul(this.$bet.accDiv(2,this.scale),this.multiple)  * this.notes //投注金额,
                    }else { //默认模式
                        data['multiple'] = this.multiple //投注倍数
                        data['money'] = 2  * this.notes //投注金额
                    }
                }
                if(this.betModel == 2){ //模式2
                    data['money'] = this.minMoney //投注金额
                }
                this.$store.commit('pushBetNum',data)
            },
            //添加到号码篮
            addBetBasket(){
                if(!this.copyValue.length){
                    this.$messagebox('提示','请先复制投注号码！')
                    return false
                }
                if(this.notes > 500){
                    this.$messagebox('提示','最多只能输入500注！')
                    return false
                }
                this.check();
                this.show = true
                if(this.errCont.length > 0){
                    this.$messagebox.confirm ("所输入号码中有 <b class='red'>" + this.errCont.length + "</b> 行错误，过滤错误号码继续添加？") .then(() =>{
                        for(let i in this.resArr){
                            this.addNum(this.resArr[i])
                        }
                        this.clear();
                    }).catch(()=>{

                    });
                }else {
                    for(let i in this.resArr){
                        this.addNum(this.resArr[i])
                    }
                    this.clear();
                }
            },
            // 立即投注
            submitOrder(){
                if(this.badge < 1 && this.notes < 1){
                    this.$messagebox({
                        title: '提示',
                        message: '至少选择1注投注号码！',
                        confirmButtonText:'我知道了'
                    });
                    return
                }
                if(this.notes > 0){
                    this.addBetBasket();
                }
                this.$router.push({
                    path:'/betOrder',
                    query:{
                        cz:(this.$route.path).replace("/",""),
                        type:this.type,
                        name:this.name,
                        title:this.title,
                        betModel:this.betModel
                    }
                })
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
