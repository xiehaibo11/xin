<template>
    <div class="setting">
        <!--<Header/>-->
        <div class="cell">
            <p class="mt-sm">
                <mt-cell class="photo" title="头像" is-link to="/setPhoto">
                    <i slot="icon" class="iconfont icon-profile"></i>
                    <span v-if="userInfo.photo" class="pic" :style="{backgroundImage:'url(' + userInfo.photo + ')'}"></span>
                </mt-cell>
                <mt-cell title="个人资料" is-link to="/setInfo">
                    <i slot="icon" class="iconfont icon-gerenziliao"></i>
                </mt-cell>
                <mt-cell title="等级头衔" is-link to="/personalLevel">
                    <i slot="icon" class="iconfont icon-dengji"></i>
                </mt-cell>
            </p>
            <p class="mt-sm">
                <mt-cell title="登录密码" is-link :to="lockInfo.username !== 1 ? '/changePwd' : '/setPwd'">
                    <i slot="icon" class="iconfont icon-mima2"></i>
                    <span :class="{'binded':lockInfo.username !== 1}" v-if="lockInfo.username !== 1">已设置</span>
                    <span class="org" v-else>未绑定用户名</span>
                </mt-cell>
                <mt-cell title="安全密码" is-link :to="lockInfo.safe_password !== 1 ? '/changeSafePwd' : '/setSafePwd'">
                    <i slot="icon" class="iconfont icon-ai-safe"></i>
                    <span :class="{'binded':lockInfo.username !== 1}" v-if="lockInfo.username !== 1">已设置</span>
                    <span class="org" v-else>未设置</span>
                </mt-cell>
                <mt-cell v-if="lockInfo.tel == 1" title="手机管理" is-link to="/setTel">
                    <i slot="icon" class="iconfont icon-shouji"></i>
                    <span class="org">未绑定</span>
                </mt-cell>
                <mt-cell v-else title="手机管理"  @click.native="changeTel" is-link>
                    <i slot="icon" class="iconfont icon-shouji"></i>
                    <span class="binded">{{lockInfo.tel}}</span>
                </mt-cell>
                <mt-cell v-if="lockInfo.email == 1" title="邮箱管理" is-link to="/setEmail">
                    <i slot="icon" class="iconfont icon-mail"></i>
                    <span class="org">未绑定</span>
                </mt-cell>
                <mt-cell v-else title="邮箱管理"  @click.native="changeEmail" is-link>
                    <i slot="icon" class="iconfont icon-mail"></i>
                    <span class="binded">{{lockInfo.email}}</span>
                </mt-cell>
                <template v-if="qq_checked == 1">
                    <template v-if="lockInfo.qq == 1">
                        <mt-cell title="绑定QQ" is-link @click.native="bindQq">
                            <i slot="icon" class="iconfont icon-QQ"></i>
                            <span class="org">未绑定</span>
                        </mt-cell>
                    </template>
                    <template v-else>
                        <mt-cell title="绑定QQ" is-link @click.native="unlockQW(1)">
                            <i slot="icon" class="iconfont icon-QQ"></i>
                            <span class="binded">已绑定 <a class="link">解绑</a></span>
                        </mt-cell>
                    </template>
                </template>
               <template v-if="wxsm_checked == 1">
                   <template v-if="lockInfo.wei == 1">
                       <mt-cell title="绑定微信" is-link @click.native="bindWeixin">
                           <i slot="icon" class="iconfont icon-weixin1"></i>
                           <span class="org">未绑定</span>
                       </mt-cell>
                   </template>
                   <template v-else>
                       <mt-cell title="绑定微信" is-link @click.native="unlockQW(2)">
                           <i slot="icon" class="iconfont icon-weixin1"></i>
                           <span class="binded">已绑定 <a class="link">解绑</a></span>
                       </mt-cell>
                   </template>
               </template>
            </p>
            <p class="mt-sm">
                <mt-cell title="实名认证" is-link to="/realName" v-if="bind_idcard">
                    <i slot="icon" class="iconfont icon-ai-ca"></i>
                    <span :class="{'binded':trueState}" v-if="trueState">{{lockInfo.is_name}}</span>
                    <span v-else class="org">未认证</span>
                </mt-cell>
                <mt-cell title="绑定账号" is-link @click.native="isTrue">
                    <i slot="icon" class="iconfont icon-bangding"></i>
                    <span>银行卡/支付宝</span>
                </mt-cell>
            </p>
            <div class="btn-box">
                <mt-button @click.native="sheetVisible = true" type="danger" size="large">
                    退出登录
                </mt-button>
            </div>
        </div>
        <!--退出提示-->
        <mt-actionsheet
            :actions="actions"
            v-model="sheetVisible">
        </mt-actionsheet>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
                sheetVisible: false,
                actions: [{
                    name: '确定退出？',
                    method: this.loginOut
                }]
            }
        },
        computed:{
            userInfo(){
                return this.$store.state.userinfo
            },
            lockInfo(){
                return this.$store.state.lockInfo
            },
            trueState(){
                return this.$store.state.lockInfo.is_num == 0 ? false : true
            },
            trueInfo(){
                return this.$store.state.trueInfo
            },
            qq_checked(){
                return this.$store.state.setting.qq_checked
            },
            wxsm_checked(){
                return this.$store.state.setting.wxsm_checked
            },
            lockTel(){
                return this.$store.state.lockInfo.tel !== 1 ? true : false //是否绑定手机号
            },
            lockEmail(){
                return this.$store.state.lockInfo.email !== 1 ? true : false //是否绑定邮箱
            },
            isYzm(){
                return Number(this.$store.state.setting.tel_checked) ? true: false //是否开启短信接口
            },
            isEmail(){
                return Number(this.$store.state.setting.email_checked) ? true: false //是否开启邮箱验证
            },
            bind_idcard(){//是否需要绑定身份证
                return Number(this.$store.state.setting.bind_idcard) ? true : false
            }
        },
        methods:{
            //退出登录
            loginOut(){
                this.$axios.get('/web/login/logout').then((data) =>{
                    if(!data.err){
                        this.$store.commit('clearUserInfo') //清空用户信息
                        localStorage.removeItem('loginToken') //清除记录
                        // 清除登录状态
                        this.$store.commit('setBauth', false);
                        // 跳转到登录页面而不是返回上一页
                        this.$router.push('/login');
                    }else {
                        this.$messagebox('提示',data.msg)
                    }
                })
            },
            //账号绑定
            isTrue(){
                if(this.bind_idcard){
                    if(this.lockInfo.is_num !== 0){
                        this.$router.push({
                            path:'/lockCard'
                        })
                    }else {
                        this.$messagebox.confirm(
                            '您还未进行实名认证，立即去认证?'
                        ).then(()=>{
                            this.$router.push({
                                path:'/realName'
                            })
                        }).catch((err)=>{

                        });
                    }
                }else {
                    if(this.lockInfo.is_name !== 0){
                        this.$router.push({
                            path:'/lockCard'
                        })
                    }else {
                        this.$messagebox.confirm(
                            '您还未完善个人资料，立即去完善?'
                        ).then(()=>{
                            this.$router.push({
                                path:'/setInfo'
                            })
                        }).catch((err)=>{

                        });
                    }
                }
            },
            //修改手机
            changeTel(){
                if(!this.isYzm && this.isEmail){
                    if(!this.lockEmail){
                        this.$messagebox.confirm(
                            '您还未绑定邮箱，立即去绑定?'
                        ).then(()=>{
                            this.$router.push({
                                path:'/setEmail'
                            })
                        }).catch((err)=>{

                        });
                        return
                    }
                }
                this.$router.push({
                    path:'/changeTel'
                })
            },
            //修改邮箱
            changeEmail(){
                if(this.isYzm && !this.isEmail){
                    if(!this.lockTel){
                        this.$messagebox.confirm(
                            '您还未绑定手机，立即去绑定?'
                        ).then(()=>{
                            this.$router.push({
                                path:'/setTel'
                            })
                        }).catch((err)=>{

                        });
                        return
                    }
                }
                this.$router.push({
                    path:'/changeEmail'
                })
            },
            //绑定QQ
            bindQq(){
                window.location.href = '/index/Qqlogin/toLogin?type=1'
            },
            //绑定微信
            bindWeixin(){
                this.$messagebox('提示','请前往电脑端进行绑定！')
            },
            //解绑微信、QQ
            unlockQW(way) {
                var words;
                if (way == 1) {
                    words = 'QQ';
                }
                if (way == 2) {
                    words = '微信';
                }
                this.$messagebox.confirm(
                    '此操作将解除已绑定' + words + ', 是否继续?'
                ).then(()=>{
                    this.$axios.post('/web/user/unlockQW',{
                        way:way
                    }).then(({data})=>{
                        if(!data.err){
                            this.$store.dispath('getLockInfo');//更新绑定信息
                        }
                        this.$toast(data.msg);
                    })
                }).catch((err)=>{
                    console.log(err)
                });
            }
        },
        created(){
            this.$store.dispatch('getBanks');
            this.$store.dispatch('getAlipay');
//            this.$store.dispatch('getWei');
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">
    .cell{
        span{
            font-size: $font-small;
            &.binded{
                color:$color-font-secondary;
            }
        }
    }
    .photo{
        min-height: 68px;
        span.pic{
            display: inline-block;
            width: 50px;
            height: 50px;
            background-size: cover;
            border-radius: 50%;
        }
    }
    a.link{
        text-decoration: underline;
    }
</style>
