<!--排列3单式投注-->
<template>
    <div class="copy-box">
        <div class="contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">前区从0-9中手动输入{{n}}个号码组成一注号码进行购买，奖金<em class="red">{{gain}}</em>{{lotteryUnit}}。</div>
            <div class="copydiv">
                <div class="pasterIntro mf-sm">
                    <strong>格式说明：</strong>
                    <p>
                        (1)每位之间以英文字符逗号(",")或者空格分割；<br>
                        (2)每行填写一注投注号码；<br>
                        单注示例：
                        <template v-if="name=='pls'">
                            <template v-if="type == 1.2 || type == 3.2">1,2,3或1 2 3</template><!--直选、组六单式-->
                            <template v-if="type == 2.2">1,2或1 2</template><!--组三单式-->
                        </template>
                        <template v-if="name=='plw'">
                            1,2,3,4,5或1 2 3 4 5
                        </template>
                        <template v-if="name=='qxc'">
                            1,2,3,4,5,6,7或1 2 3 4 5 6 7
                        </template>
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
            <div class="yl-box f-mini border-top-1px tc c-3">
                <em class="red">{{notes}}</em>注，共<em class="red">{{notes * 2}}</em>{{lotteryUnit}},最多500注
            </div>
            <div class="flex-box notes-box">
                <div class="flex bet-detail">
                    <div class="c-4" @click="clear">
                        <i class="iconfont icon-shanchu btn-icon-clear"></i>清空
                    </div>
                </div>
                <div class="tc bet-basket" style="margin: 0 15px 0 5px">
                    <button class="bet-btn btn-basket" @click="addBetBasket">+ 号码篮 <mt-badge class="badge" type="error" size="small">{{badge}}</mt-badge></button>
                </div>
                <button class="bet-btn btn-sure" @click="submitOrder">选好了</button>
            </div>
        </div>
        <!--底部 end-->
    </div>
</template>

<script>
    export default {
        name: 'PlsCopyBet',
        props:['type','gain','title','text','expect','endTime','n'],
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
            name(){
                return this.$route.path.replace('/','')
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit;
            },
            //选号容器高度计算
            contentH(){
                return this.$store.state.clientHeight - 152
            },
            //选号篮数量
            badge(){
                return this.$store.state.shuzicai.plan.length
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
                        //排列3中组选不能有相同的
                        if(this.name == 'pls' && this.type == 2.2 || this.name == 'pls' && this.type == 3.2) {
                            if (str[j] == str[j + 1]) {
                                hasSame = true
                            }
                        }
                        //检测号码合法性
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
                if(this.name == 'pls' && this.type == 2.2 || this.name == 'pls' && this.type == 3.2){ //组选
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
                data['notes'] = this.type == 2.2 ? 2 : 1
                data['type_text'] = this.text
                data['type'] = this.type
                this.$store.commit('pushSzcBetNum',data)
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
                    path:'/shuzicai/bet',
                    query:{
                        type:this.type,
                        name:this.name,
                        title:this.title,
                        expect:this.expect,
                        end_time : this.endTime
                    }
                })
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
