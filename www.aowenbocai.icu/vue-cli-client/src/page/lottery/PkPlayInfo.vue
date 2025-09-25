<!--pk10中奖说明-->
<template>
    <div class="play-info" id="wrapper">
        <mt-header fixed title="中奖说明">
            <mt-button icon="back" slot="left" @click.native="back"></mt-button>
        </mt-header>
        <div class="t-height"></div>
        <div style="padding: 5px 10px;line-height: 1.5">
            <h4>开奖时间</h4>
            <div>{{timelong}}分钟一期，每天{{totalIssue}}期</div>
            <h4>奖项设置(<em class="f-sm c-2">单位：{{lotteryUnit}}</em>)</h4>
        </div>
        <table cellpadding="0" cellspacing="0" width="100%" class="table-list border-1px play-list">
            <tr>
                <th style="background-color: #f7f7f7"><div class="tc"  style="width: 100%;line-height: 1;">玩法</div></th>
                <th style="background-color: #f7f7f7"><div class="tc" style="width: 100%;line-height: 1">中0个</div></th>
                <th style="background-color: #f7f7f7"><div class="tc" style="width: 100%;line-height: 1">中1个</div></th>
                <th style="background-color: #f7f7f7"><div class="tc" style="width: 100%;line-height: 1">中2个</div></th>
                <th style="background-color: #f7f7f7"><div class="tc" style="width: 100%;line-height: 1">中3个</div></th>
                <th style="background-color: #f7f7f7"><div class="tc" style="width: 100%;line-height: 1">中4个</div></th>
                <th style="background-color: #f7f7f7"><div class="tc" style="width: 100%;line-height: 1">中5个</div></th>
                <th style="background-color: #f7f7f7"><div class="tc" style="width: 100%;line-height: 1">中6个</div></th>
                <th style="background-color: #f7f7f7"><div class="tc" style="width: 100%;line-height: 1">中7个</div></th>
                <th style="background-color: #f7f7f7"><div class="tc" style="width: 100%;line-height: 1">中8个</div></th>
                <th style="background-color: #f7f7f7"><div class="tc" style="width: 100%;line-height: 1">中9个</div></th>
                <th style="background-color: #f7f7f7"><div class="tc" style="width: 100%;line-height: 1">中10个</div></th>
            </tr>
            <tr v-if="playInfo.hasOwnProperty('GJ')">
                <td><span>{{playInfo.GJ.name}}</span></td>
                <td v-if="!playInfo.GJ[0]">--</td>
                <td v-else>{{handleGain(playInfo.GJ[0])}}</td>
                <td v-if="!playInfo.GJ[1]">--</td>
                <td v-else>{{handleGain(playInfo.GJ[1])}}</td>
                <td v-if="!playInfo.GJ[2]">--</td>
                <td v-else>{{handleGain(playInfo.GJ[2])}}</td>
                <td v-if="!playInfo.GJ[3]">--</td>
                <td v-else>{{handleGain(playInfo.GJ[3])}}</td>
                <td v-if="!playInfo.GJ[4]">--</td>
                <td v-else>{{handleGain(playInfo.GJ[4])}}</td>
                <td v-if="!playInfo.GJ[5]">--</td>
                <td v-else>{{handleGain(playInfo.GJ[5])}}</td>
                <td v-if="!playInfo.GJ[6]">--</td>
                <td v-else>{{handleGain(playInfo.GJ[6])}}</td>
                <td v-if="!playInfo.GJ[7]">--</td>
                <td v-else>{{handleGain(playInfo.GJ[7])}}</td>
                <td v-if="!playInfo.GJ[8]">--</td>
                <td v-else>{{handleGain(playInfo.GJ[8])}}</td>
                <td v-if="!playInfo.GJ[9]">--</td>
                <td v-else>{{handleGain(playInfo.GJ[9])}}</td>
                <td v-if="!playInfo.GJ[10]">--</td>
                <td v-else>{{handleGain(playInfo.GJ[10])}}</td>
            </tr>
            <tr v-if="playInfo.hasOwnProperty('YJ')">
                <td><span>{{playInfo.YJ.name}}</span></td>
                <td v-if="!playInfo.YJ[0]">--</td>
                <td v-else>{{handleGain(playInfo.YJ[0])}}</td>
                <td v-if="!playInfo.YJ[1]">--</td>
                <td v-else>{{handleGain(playInfo.YJ[1])}}</td>
                <td v-if="!playInfo.YJ[2]">--</td>
                <td v-else>{{handleGain(playInfo.YJ[2])}}</td>
                <td v-if="!playInfo.YJ[3]">--</td>
                <td v-else>{{handleGain(playInfo.YJ[3])}}</td>
                <td v-if="!playInfo.YJ[4]">--</td>
                <td v-else>{{handleGain(playInfo.YJ[4])}}</td>
                <td v-if="!playInfo.YJ[5]">--</td>
                <td v-else>{{handleGain(playInfo.YJ[5])}}</td>
                <td v-if="!playInfo.YJ[6]">--</td>
                <td v-else>{{handleGain(playInfo.YJ[6])}}</td>
                <td v-if="!playInfo.YJ[7]">--</td>
                <td v-else>{{handleGain(playInfo.YJ[7])}}</td>
                <td v-if="!playInfo.YJ[8]">--</td>
                <td v-else>{{handleGain(playInfo.YJ[8])}}</td>
                <td v-if="!playInfo.YJ[9]">--</td>
                <td v-else>{{handleGain(playInfo.YJ[9])}}</td>
                <td v-if="!playInfo.YJ[10]">--</td>
                <td v-else>{{handleGain(playInfo.YJ[10])}}</td>
            </tr>
            <tr v-if="playInfo.hasOwnProperty('JJ')">
                <td><span>{{playInfo.JJ.name}}</span></td>
                <td v-if="!playInfo.JJ[0]">--</td>
                <td v-else>{{handleGain(playInfo.JJ[0])}}</td>
                <td v-if="!playInfo.JJ[1]">--</td>
                <td v-else>{{handleGain(playInfo.JJ[1])}}</td>
                <td v-if="!playInfo.JJ[2]">--</td>
                <td v-else>{{handleGain(playInfo.JJ[2])}}</td>
                <td v-if="!playInfo.JJ[3]">--</td>
                <td v-else>{{handleGain(playInfo.JJ[3])}}</td>
                <td v-if="!playInfo.JJ[4]">--</td>
                <td v-else>{{handleGain(playInfo.JJ[4])}}</td>
                <td v-if="!playInfo.JJ[5]">--</td>
                <td v-else>{{handleGain(playInfo.JJ[5])}}</td>
                <td v-if="!playInfo.JJ[6]">--</td>
                <td v-else>{{handleGain(playInfo.JJ[6])}}</td>
                <td v-if="!playInfo.JJ[7]">--</td>
                <td v-else>{{handleGain(playInfo.JJ[7])}}</td>
                <td v-if="!playInfo.JJ[8]">--</td>
                <td v-else>{{handleGain(playInfo.JJ[8])}}</td>
                <td v-if="!playInfo.JJ[9]">--</td>
                <td v-else>{{handleGain(playInfo.JJ[9])}}</td>
                <td v-if="!playInfo.JJ[10]">--</td>
                <td v-else>{{handleGain(playInfo.JJ[10])}}</td>
            </tr>
            <tr v-if="playInfo.hasOwnProperty('QS')">
                <td><span>{{playInfo.QS.name}}</span></td>
                <td v-if="!playInfo.QS[0]">--</td>
                <td v-else>{{handleGain(playInfo.QS[0])}}</td>
                <td v-if="!playInfo.QS[1]">--</td>
                <td v-else>{{handleGain(playInfo.QS[1])}}</td>
                <td v-if="!playInfo.QS[2]">--</td>
                <td v-else>{{handleGain(playInfo.QS[2])}}</td>
                <td v-if="!playInfo.QS[3]">--</td>
                <td v-else>{{handleGain(playInfo.QS[3])}}</td>
                <td v-if="!playInfo.QS[4]">--</td>
                <td v-else>{{handleGain(playInfo.QS[4])}}</td>
                <td v-if="!playInfo.QS[5]">--</td>
                <td v-else>{{handleGain(playInfo.QS[5])}}</td>
                <td v-if="!playInfo.QS[6]">--</td>
                <td v-else>{{handleGain(playInfo.QS[6])}}</td>
                <td v-if="!playInfo.QS[7]">--</td>
                <td v-else>{{handleGain(playInfo.QS[7])}}</td>
                <td v-if="!playInfo.QS[8]">--</td>
                <td v-else>{{handleGain(playInfo.QS[8])}}</td>
                <td v-if="!playInfo.QS[9]">--</td>
                <td v-else>{{handleGain(playInfo.QS[9])}}</td>
                <td v-if="!playInfo.QS[10]">--</td>
                <td v-else>{{handleGain(playInfo.QS[10])}}</td>
            </tr>
            <tr v-if="playInfo.hasOwnProperty('QW')">
                <td><span>{{playInfo.QW.name}}</span></td>
                <td v-if="!playInfo.QW[0]">--</td>
                <td v-else>{{handleGain(playInfo.QW[0])}}</td>
                <td v-if="!playInfo.QW[1]">--</td>
                <td v-else>{{handleGain(playInfo.QW[1])}}</td>
                <td v-if="!playInfo.QW[2]">--</td>
                <td v-else>{{handleGain(playInfo.QW[2])}}</td>
                <td v-if="!playInfo.QW[3]">--</td>
                <td v-else>{{handleGain(playInfo.QW[3])}}</td>
                <td v-if="!playInfo.QW[4]">--</td>
                <td v-else>{{handleGain(playInfo.QW[4])}}</td>
                <td v-if="!playInfo.QW[5]">--</td>
                <td v-else>{{handleGain(playInfo.QW[5])}}</td>
                <td v-if="!playInfo.QW[6]">--</td>
                <td v-else>{{handleGain(playInfo.QW[6])}}</td>
                <td v-if="!playInfo.QW[7]">--</td>
                <td v-else>{{handleGain(playInfo.QW[7])}}</td>
                <td v-if="!playInfo.QW[8]">--</td>
                <td v-else>{{handleGain(playInfo.QW[8])}}</td>
                <td v-if="!playInfo.QW[9]">--</td>
                <td v-else>{{handleGain(playInfo.QW[9])}}</td>
                <td v-if="!playInfo.QW[10]">--</td>
                <td v-else>{{handleGain(playInfo.QW[10])}}</td>
            </tr>
            <tr v-if="playInfo.hasOwnProperty('QL')">
                <td><span>{{playInfo.QL.name}}</span></td>
                <td v-if="!playInfo.QL[0]">--</td>
                <td v-else>{{handleGain(playInfo.QL[0])}}</td>
                <td v-if="!playInfo.QL[1]">--</td>
                <td v-else>{{handleGain(playInfo.QL[1])}}</td>
                <td v-if="!playInfo.QL[2]">--</td>
                <td v-else>{{handleGain(playInfo.QL[2])}}</td>
                <td v-if="!playInfo.QL[3]">--</td>
                <td v-else>{{handleGain(playInfo.QL[3])}}</td>
                <td v-if="!playInfo.QL[4]">--</td>
                <td v-else>{{handleGain(playInfo.QL[4])}}</td>
                <td v-if="!playInfo.QL[5]">--</td>
                <td v-else>{{handleGain(playInfo.QL[5])}}</td>
                <td v-if="!playInfo.QL[6]">--</td>
                <td v-else>{{handleGain(playInfo.QL[6])}}</td>
                <td v-if="!playInfo.QL[7]">--</td>
                <td v-else>{{handleGain(playInfo.QL[7])}}</td>
                <td v-if="!playInfo.QL[8]">--</td>
                <td v-else>{{handleGain(playInfo.QL[8])}}</td>
                <td v-if="!playInfo.QL[9]">--</td>
                <td v-else>{{handleGain(playInfo.QL[9])}}</td>
                <td v-if="!playInfo.QL[10]">--</td>
                <td v-else>{{handleGain(playInfo.QL[10])}}</td>
            </tr>
            <tr v-if="playInfo.hasOwnProperty('QQ')">
                <td><span>{{playInfo.QQ.name}}</span></td>
                <td v-if="!playInfo.QQ[0]">--</td>
                <td v-else>{{handleGain(playInfo.QQ[0])}}</td>
                <td v-if="!playInfo.QQ[1]">--</td>
                <td v-else>{{handleGain(playInfo.QQ[1])}}</td>
                <td v-if="!playInfo.QQ[2]">--</td>
                <td v-else>{{handleGain(playInfo.QQ[2])}}</td>
                <td v-if="!playInfo.QQ[3]">--</td>
                <td v-else>{{handleGain(playInfo.QQ[3])}}</td>
                <td v-if="!playInfo.QQ[4]">--</td>
                <td v-else>{{handleGain(playInfo.QQ[4])}}</td>
                <td v-if="!playInfo.QQ[5]">--</td>
                <td v-else>{{handleGain(playInfo.QQ[5])}}</td>
                <td v-if="!playInfo.QQ[6]">--</td>
                <td v-else>{{handleGain(playInfo.QQ[6])}}</td>
                <td v-if="!playInfo.QQ[7]">--</td>
                <td v-else>{{handleGain(playInfo.QQ[7])}}</td>
                <td v-if="!playInfo.QQ[8]">--</td>
                <td v-else>{{handleGain(playInfo.QQ[8])}}</td>
                <td v-if="!playInfo.QQ[9]">--</td>
                <td v-else>{{handleGain(playInfo.QQ[9])}}</td>
                <td v-if="!playInfo.QQ[10]">--</td>
                <td v-else>{{handleGain(playInfo.QQ[10])}}</td>
            </tr>
            <tr v-if="playInfo.hasOwnProperty('QB')">
                <td><span>{{playInfo.QB.name}}</span></td>
                <td v-if="!playInfo.QB[0]">--</td>
                <td v-else>{{handleGain(playInfo.QB[0])}}</td>
                <td v-if="!playInfo.QB[1]">--</td>
                <td v-else>{{handleGain(playInfo.QB[1])}}</td>
                <td v-if="!playInfo.QB[2]">--</td>
                <td v-else>{{handleGain(playInfo.QB[2])}}</td>
                <td v-if="!playInfo.QB[3]">--</td>
                <td v-else>{{handleGain(playInfo.QB[3])}}</td>
                <td v-if="!playInfo.QB[4]">--</td>
                <td v-else>{{handleGain(playInfo.QB[4])}}</td>
                <td v-if="!playInfo.QB[5]">--</td>
                <td v-else>{{handleGain(playInfo.QB[5])}}</td>
                <td v-if="!playInfo.QB[6]">--</td>
                <td v-else>{{handleGain(playInfo.QB[6])}}</td>
                <td v-if="!playInfo.QB[7]">--</td>
                <td v-else>{{handleGain(playInfo.QB[7])}}</td>
                <td v-if="!playInfo.QB[8]">--</td>
                <td v-else>{{handleGain(playInfo.QB[8])}}</td>
                <td v-if="!playInfo.QB[9]">--</td>
                <td v-else>{{handleGain(playInfo.QB[9])}}</td>
                <td v-if="!playInfo.QB[10]">--</td>
                <td v-else>{{handleGain(playInfo.QB[10])}}</td>
            </tr>
            <tr v-if="playInfo.hasOwnProperty('QJ')">
                <td><span>{{playInfo.QJ.name}}</span></td>
                <td v-if="!playInfo.QJ[0]">--</td>
                <td v-else>{{handleGain(playInfo.QJ[0])}}</td>
                <td v-if="!playInfo.QJ[1]">--</td>
                <td v-else>{{handleGain(playInfo.QJ[1])}}</td>
                <td v-if="!playInfo.QJ[2]">--</td>
                <td v-else>{{handleGain(playInfo.QJ[2])}}</td>
                <td v-if="!playInfo.QJ[3]">--</td>
                <td v-else>{{handleGain(playInfo.QJ[3])}}</td>
                <td v-if="!playInfo.QJ[4]">--</td>
                <td v-else>{{handleGain(playInfo.QJ[4])}}</td>
                <td v-if="!playInfo.QJ[5]">--</td>
                <td v-else>{{handleGain(playInfo.QJ[5])}}</td>
                <td v-if="!playInfo.QJ[6]">--</td>
                <td v-else>{{handleGain(playInfo.QJ[6])}}</td>
                <td v-if="!playInfo.QJ[7]">--</td>
                <td v-else>{{handleGain(playInfo.QJ[7])}}</td>
                <td v-if="!playInfo.QJ[8]">--</td>
                <td v-else>{{handleGain(playInfo.QJ[8])}}</td>
                <td v-if="!playInfo.QJ[9]">--</td>
                <td v-else>{{handleGain(playInfo.QJ[9])}}</td>
                <td v-if="!playInfo.QJ[10]">--</td>
                <td v-else>{{handleGain(playInfo.QJ[10])}}</td>
            </tr>
            <tr v-if="playInfo.hasOwnProperty('QSHI')">
                <td><span>{{playInfo.QSHI.name}}</span></td>
                <td v-if="!playInfo.QSHI[0]">--</td>
                <td v-else>{{handleGain(playInfo.QSHI[0])}}</td>
                <td v-if="!playInfo.QSHI[1]">--</td>
                <td v-else>{{handleGain(playInfo.QSHI[1])}}</td>
                <td v-if="!playInfo.QSHI[2]">--</td>
                <td v-else>{{handleGain(playInfo.QSHI[2])}}</td>
                <td v-if="!playInfo.QSHI[3]">--</td>
                <td v-else>{{handleGain(playInfo.QSHI[3])}}</td>
                <td v-if="!playInfo.QSHI[4]">--</td>
                <td v-else>{{handleGain(playInfo.QSHI[4])}}</td>
                <td v-if="!playInfo.QSHI[5]">--</td>
                <td v-else>{{handleGain(playInfo.QSHI[5])}}</td>
                <td v-if="!playInfo.QSHI[6]">--</td>
                <td v-else>{{handleGain(playInfo.QSHI[6])}}</td>
                <td v-if="!playInfo.QSHI[7]">--</td>
                <td v-else>{{handleGain(playInfo.QSHI[7])}}</td>
                <td v-if="!playInfo.QSHI[8]">--</td>
                <td v-else>{{handleGain(playInfo.QSHI[8])}}</td>
                <td v-if="!playInfo.QSHI[9]">--</td>
                <td v-else>{{handleGain(playInfo.QSHI[9])}}</td>
                <td v-if="!playInfo.QSHI[10]">--</td>
                <td v-else>{{handleGain(playInfo.QSHI[10])}}</td>
            </tr>
        </table>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
            }
        },
        computed:{
            //返点设置是否开启
            rebateIsOpen(){
                return this.$store.state.setting.rebate_isOpen == 1 ? true : false
            },
            //当前用户最高奖金返点百分比
            percent(){
                return this.$store.getters.maxRebate
            },
            cz(){
                return this.$route.query.cz
            },
            totalIssue(){
                return this.$store.state.lottery.info.totalIssue
            },
            timelong(){
                return this.$store.state.lottery.info.timelong
            },
            playInfo(){
                return this.$store.state.lottery.pk10Bouns
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            model(){
                return this.$route.query.model || 1
            },
            divVal () {
                return this.$route.query.model == 1 ? 1 : 2//模式2下奖金减半
            },
        },
        methods:{
            back(){
                this.$router.goBack(-1);//返回上一层
            },
            //奖金计算方法
            handleGain(g) {
                return this.rebateIsOpen ? this.$bet.accDiv(this.$bet.accMul(Number(g),this.percent),this.divVal,5) : this.$bet.accDiv(Number(g),this.divVal,5)
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    /*
  * 强制横屏显示：通过竖屏时旋转解决横屏问题
  *
  */
    @media screen and  (orientation:  portrait){
        #wrapper {
            -webkit-transform:rotate(90deg);
            -webkit-transform-origin:0% 0%;/*1.重置旋转中心*/

            -moz-transform: rotate(90deg);
            -moz-transform-origin:0% 0%;

            -ms-transform: rotate(90deg);
            -ms-transform-origin:0% 0%;

            transform: rotate(90deg);
            transform-origin:0% 0%;

            width: 100vh;/*2.利用 vh 重置 ‘宽度’ */
            height: 100vw;/* 3.利用 vw 重置 ‘高度’ */

            top: 0;
            left: 100vw;/* 4.旋转后页面超出屏幕，重置页面定位位置 */
        }
    }
</style>
