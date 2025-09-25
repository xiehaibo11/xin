<template>
    <div>
        <mt-header :title="title">
            <mt-button icon="back" slot="left" @click.native="goBack"></mt-button>
        </mt-header>
        <div class="contentH" :style="{height:contentH + 'px'}">
            <div class="agent-head tc card">
                <div v-if="userinfo.photo" class="pic" :style="{backgroundImage:'url(' + userinfo.photo + ')'}"></div>
                <div class="tc f-large">{{userinfo.username}}<!--<em class="c-3 f-mini">（上级：XXX）</em>--></div>
            </div>
            <div class="cell mf-sm agent-link">
                <p class="mt-sm">
                    <mt-cell title="代理说明" is-link to="/agent/explain">
                        <svg slot="icon" class="icon" aria-hidden="true">
                            <use xlink:href="#icon-shengyaceshi"></use>
                        </svg>
                    </mt-cell>
                </p>
                <p class="mt-sm">
                    <mt-cell title="代理报表" is-link to="/agent/report">
                        <svg slot="icon" class="icon" aria-hidden="true">
                            <use xlink:href="#icon-luqufenshubiao"></use>
                        </svg>
                    </mt-cell>
                    <mt-cell title="下级报表" is-link :to="'/agent/subReport?redirect='+ $router.currentRoute.fullPath">
                        <svg slot="icon" class="icon" aria-hidden="true">
                            <use xlink:href="#icon-zhiyuanbiao"></use>
                        </svg>
                    </mt-cell>
                    <mt-cell title="下级开户" is-link :to="'/agent/invite?redirect='+ $router.currentRoute.fullPath">
                        <svg slot="icon" class="icon" aria-hidden="true">
                            <use xlink:href="#icon-zhuanzheng"></use>
                        </svg>
                    </mt-cell>
                    <mt-cell title="会员管理" is-link :to="'/agent/member?redirect='+ $router.currentRoute.fullPath">
                        <svg slot="icon" class="icon" aria-hidden="true">
                            <use xlink:href="#icon-guanlirenyuan"></use>
                        </svg>
                    </mt-cell>
                </p>
                <p class="mt-sm">
                    <mt-cell title="投注明细" is-link to="/agent/betRecord">
                        <svg slot="icon" class="icon" aria-hidden="true">
                            <use xlink:href="#icon-qingjia"></use>
                        </svg>
                    </mt-cell>
                    <mt-cell title="资金明细" is-link to="/agent/billRecord">
                        <svg slot="icon" class="icon" aria-hidden="true">
                            <use xlink:href="#icon-icon_jinrong"></use>
                        </svg>
                    </mt-cell>
                </p>
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        name:'agent',
        data() {
            return {

            };
        },
        computed:{
            title(){
                return this.$route.meta.title
            },
            userinfo(){
                return this.$store.state.userinfo
            },
            //内容高度
            contentH(){
                return this.$store.state.clientHeight - 40
            },
        },
        methods:{
            //返回
            goBack(){
                this.$router.isBack = true
                this.$router.push({
                    path:this.$route.query.redirect ? this.$route.query.redirect :  window.history.go(-1)
                })
            }
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .agent-head{
        padding: 15px;
        background-color: #ffffff;
        .pic{
            width: 90px;
            height: 90px;
            margin: 0 auto 10px;
            border-radius: 100%;
            @include shadow(0,0,8px, lighten($bColor,30%));
            border:3px solid #ffffff;
            background-size: cover;
        }
    }
    .icon {
        margin-right: 10px;
        width: 28px;
        height: 28px;
        fill: currentColor;
        overflow: hidden;
    }
</style>
