<!--帮助中心-->
<template>
    <div ref="wrapper">
        <template v-for="(item,index) in helpNav">
            <mt-cell :title="item.title" @click.native="showCloum(item.id)">
                <span><b class="iconfont icon-xialajiantou c-4 rotateIcon" :class="show==item.id ? 'is-active':'no-active'" style="font-size: 20px"></b></span>
            </mt-cell>
            <div v-for="(arcticle,i) in item.article" v-show="show==item.id">
                <template v-if="item.title == '玩法介绍'">
                    <template v-for="nav in lotteryNav">
                        <template v-if="nav.label == arcticle.title">
                            <router-link tag='div' class="class-b" :to="'/news/detail?navid=' + item.id + '&id=' + arcticle.id">
                                <a>{{arcticle.title}}</a>
                            </router-link>
                        </template>
                    </template>
                </template>
                <router-link v-else tag='div' class="class-b" :to="'/news/detail?navid=' + item.id + '&id=' + arcticle.id">
                    {{i+1}}、<a>{{arcticle.title}}</a>
                </router-link>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        name: 'help',
        data () {
            return {
                show:-1
            }
        },
        computed:{
            helpNav(){
                return this.$store.state.helpData.nav
            },
            lotteryNav(){
                return this.$store.state.lotteryNav
            }
        },
        methods:{
            showCloum(id){
                this.show = this.show == -1 ? id : this.show == id ?  -1 : id
            }
        },
        created(){
            this.$store.dispatch('getHelpData')
            this.$store.commit('setKeepAlivePage','help')
        },
        beforeRouteLeave(to, from, next){
            if(to.path =='/news/detail'){
                this.$store.commit('setPageYOffset', this.$refs.wrapper.scrollTop)
                this.$store.commit('setKeepAlivePage','help')
            }else {
                this.$store.commit('delKeepAlivePage','help')
            }
            next();
        },
        activated(){
            this.$refs.wrapper.scrollTop = this.$store.state.pageYOffset;
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">
    .class-b{
        background-color: $color-bg;
        padding: 0 10px;
        font-size: 14px;
        height: 38px;
        color: #3349e8;
        line-height: 38px;
        a{
            color: #3349e8;
            text-decoration: underline;
        }
    }
</style>
