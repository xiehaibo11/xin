<template>
    <div class="detail">
        <mt-header title="发现"></mt-header>
        <div class="contentH" :style="{height: contentH + 'px'}">
            <div class="mt">
                <router-link class="mint-cell" :to="'/news/notice?navid='+ newNavClassId.notice_id">
                    <div class="mint-cell-wrapper">
                        <div class="icon-svg"><img src="@/assets/images/notice.svg" alt="" width="30"></div>
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">网站公告</span>
                            <span class="mint-cell-label">网站最新活动、更新信息</span>
                        </div>
                        <i class="mint-cell-allow-right"></i>
                    </div>
                </router-link>
                <router-link class="mint-cell" :to="'/news/news?navid='+ newNavClassId.news_id">
                    <div class="mint-cell-wrapper">
                        <div class="icon-svg"><img src="@/assets/images/news.svg" alt="" width="30"></div>
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">游戏资讯</span>
                            <span class="mint-cell-label">精彩信息每日更新</span>
                        </div>
                        <i class="mint-cell-allow-right"></i>
                    </div>
                </router-link>
                <router-link class="mint-cell" to="/help">
                    <div class="mint-cell-wrapper">
                        <div class="icon-svg"><img src="@/assets/images/help.svg" alt="" width="32"></div>
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">帮助中心</span>
                            <span class="mint-cell-label">常见问题查询</span>
                        </div>
                        <i class="mint-cell-allow-right"></i>
                    </div>
                </router-link>
            </div>
            <div class="mt mf">
                <router-link class="mint-cell" :to="'/news/activity?navid='+ newNavClassId.activity_id" v-if="join_isOpen">
                    <div class="mint-cell-wrapper">
                        <div class="icon-svg"><img src="@/assets/images/yhhd.svg" alt="" width="36"></div>
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">优惠活动</span>
                            <span class="mint-cell-label">活动中心</span>
                        </div>
                        <i class="mint-cell-allow-right"></i>
                    </div>
                </router-link>
                <router-link class="mint-cell" to="/discover/winprize">
                    <div class="mint-cell-wrapper">
                        <div class="icon-svg"><img src="@/assets/images/zjks.svg" alt="" width="32"></div>
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">中奖快讯</span>
                            <span class="mint-cell-label">用户即时中奖信息</span>
                        </div>
                        <i class="mint-cell-allow-right"></i>
                    </div>
                </router-link>
                <router-link class="mint-cell" to="/discover/ranking">
                    <div class="mint-cell-wrapper">
                        <div class="icon-svg"><img src="@/assets/images/bang.svg" alt="" width="30"></div>
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">排行榜</span>
                            <span class="mint-cell-label">累计中奖前10排行榜</span>
                        </div>
                        <i class="mint-cell-allow-right"></i>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'discover',
        data () {
            return {
            }
        },
        computed:{
            //合买是否开启
            join_isOpen(){
                return this.$store.state.setting.join_isOpen == 1 ? true : false
            },
            //栏目id
            newNavClassId(){
                return this.$store.state.newNavClassId
            },
            //屏幕高度
            contentH(){
                return this.$store.state.clientHeight - 95
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">
    .icon-svg{
        width: 36px;
        margin-right: 10px;
    }
</style>
