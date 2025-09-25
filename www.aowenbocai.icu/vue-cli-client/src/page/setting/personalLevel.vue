<template>
    <div>
        <div class="level-head">
            <div class="flex-box">
                <div class="photo" :style="{backgroundImage:'url(' + userinfo.photo + ')'}"></div>
                <div class="info">
                    <p><em class="f-large">{{userinfo.username}}</em> <em class="yellow f-mini"><i class="iconfont icon-huangguan-copy"></i> {{userLevelDetail.userGrade}}</em></p>
                    <p><em class="f-sm">头衔：{{userLevelDetail.userTitle}}</em></p>
                    <p> <em class="yellow f-mini">当前成长值{{userLevelDetail.gradeGrow}}分</em></p>
                </div>
            </div>
            <template v-if="userLevelDetail.isVipMax">
                <div class="yellow">已到达最高等级</div>
            </template>
            <template v-else>
                <div class="level-tips">
                    距离下一级需要{{userLevelDetail.nextGrow}}分 每充值1元加1分
                </div>
                <div class="level-progress">
                    <mt-progress :value="userLevelDetail.perNum" :bar-height="14">
                        <div slot="start">{{userLevelDetail.userGrade}}</div>
                        <div slot="end">{{userLevelDetail.nextGrade}}</div>
                    </mt-progress>
                    <span class="progress-val">{{userLevelDetail.perNum}}%</span>
                </div>
            </template>
        </div>
        <div class="level-cont mt">
            <div class="cont-title card">等级机制</div>
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <th width="33%">等级</th>
                    <th width="33%">头衔</th>
                    <th width="33%">成长积分</th>
                </tr>
                <tr v-for="(item,index) in gradeList" :key="index">
                    <td>{{item.grade}}</td>
                    <td>{{item.gradeName}}</td>
                    <td>{{item.gradeGrow}}</td>
                </tr>
            </table>
        </div>
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
            userinfo(){
                return this.$store.state.userinfo
            },
            //用户信息
            userLevelDetail(){
                return this.userinfo.user_grade
            },
            //等级列表
            gradeList(){
                return JSON.parse(this.$store.state.setting.user_level)
            }
        },
        created(){

        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss" scoped type="text/scss">
    .level-head{
        padding: 15px;
        @include linear-gradient(#6a202d,#3b052c);
        .photo{
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-size: cover;
            margin-bottom: pxTorem(5);
            border:3px solid #ffffff;
            margin-right: 15px;
        }
        .info{
            color: #ffffff;
            line-height: 1.5;
        }
        .level-tips{
            color: #ffffff;
            font-size: 14px;
            margin-top: 15px;
        }
        .level-progress{
            color: #ffffff;
            font-size: 13px;
            position: relative;
            .progress-val{
                position: absolute;
                width: 100%;
                height: 30px;
                line-height: 30px;
                text-align: center;
                top: 0;
                color: #000000;
            }
        }
    }
    .level-cont{
        .cont-title{
            padding: 0 10px;
            height: 40px;
            line-height: 40px;
        }
        table{
            border:solid #efefef;
            border-width: 1px 0px 0px 1px;
            color: #666666;
            background-color: #ffffff;
            th{
                background-color: #f2f4f7;
                text-align: center;
                line-height: 38px;
                border:solid #ebebeb;
                border-width: 0 1px 1px 0;
            }
            td{
                text-align: center;
                line-height: 38px;
                border:solid #efefef;
                border-width: 0 1px 1px 0;
            }
        }
    }
</style>
