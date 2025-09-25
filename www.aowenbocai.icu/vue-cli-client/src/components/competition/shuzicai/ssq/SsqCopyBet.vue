<!--双色球单式投注-->
<template>
    <div class="copy-box">
        <div class="contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">红球区从01-33中手动输入6个号码(不能重复),篮球区从01-16中手动输入1个号码(不能重复)组成1注进行购买。</div>
            <div class="copydiv">
                <div class="pasterIntro mf-sm">
                    <strong>格式说明：</strong>
                    <p>
                        (1)红球区与篮球区之间以竖线("|")分隔，每位之间以英文字符逗号(",")或者空格分隔；<br>
                        (2)每行填写一注投注号码；<br>
                        单注示例：01,02,03,04,05,06|01或01 02 03 04 05 06|01
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
                已输入<em :class="{'red':notes> 0}">{{notes}}</em>注,最多500注
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
        name: '',
        props:['type','text','expect','endTime','title','name'],
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
                var betArr = this.copyValue.replace(/(^\s*)|(\s*$)/g, "").split(/[(\r\n)\r\n]+/) || [] //去掉首尾空白字符 以回车分割
                //检测每注的合法性
                console.log(betArr)
                var errData
                var foreBetNumArr = ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24',
                    '25','26','27','28','29','30','31','32','33']//红球区号码选号组
                var backBetNumArr = ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16']//篮球区号码选号组
                for(var i = 0 ; i < betArr.length; i++){
                    let noSx = false //是否有|
                    if(betArr[i].indexOf('|') > -1){
                        let foreNum = betArr[i].split('|')[0]
                        let backNum = betArr[i].split('|')[1]
                        //检测每个号码
                        var hasSame1 = false //红球区是否有相同的号码
                        var hasSame2 = false //篮球区是否有相同的号码
                        var hasErrNum1 = false //红球区是否有不合法的号码
                        var hasErrNum2 = false //篮球区是否有不合法的号码
                        var str1 = foreNum.replace(/(^\s*)|(\s*$)/g, "").replace(/\s/g,',').split(',') || []
                        var str2 = backNum.replace(/(^\s*)|(\s*$)/g, "").replace(/\s/g,',').split(',') || []
                        //红球区号码检测
                        for(var j = 0;j < str1.length ; j++){
                            if (str1[j] == str1[j + 1]) {
                                hasSame1 = true
                            }//不能有相同的
                            if(!this.$base.isExit(foreBetNumArr,str1[j])){hasErrNum1 = true}
                        }
                        //篮球区号码检测
                        for(var j = 0;j < str2.length ; j++){
                            if (str2[j] == str2[j + 1]) {
                                hasSame2 = true
                            }//不能有相同的
                            if(!this.$base.isExit(backBetNumArr,str2[j])){hasErrNum2 = true}
                        }
                    }else {
                        noSx = true
                    }
                    if(hasErrNum1 || hasSame1 || hasErrNum2 || hasSame2 || noSx || str1.length !== 6 || str2.length !== 1){
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
                betNum = betNum.replace(/ /g,',')
                let data = {}
                data['num'] = betNum
                data['notes'] = 1
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
                        name:'dlt',
                        title: this.title,
                        expect:this.expect,
                        end_time: this.endTime
                    }
                })
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
