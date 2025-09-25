<template>
    <div class="news-prize flex-box" >
        <!--<div class="marquee_title">-->
            <!--<span><i class="iconfont icon-tongzhi"></i></span>-->
        <!--</div>-->
        <div class="marquee_box flex">
            <!--:class="{marquee_top:num}"-->
            <ul class="marquee_list" :class="{anim:animate==true}">
                <li v-for="(item, index) in items" :key="index">
                    <router-link tag='span' class="name" :to="'/news/detail?navid=' + item.nav_id + '&id=' + item.id">
                        <i class="iconfont icon-tongzhi"></i> {{item.title}}
                    </router-link>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                num:0,

                animate:false,
                items:[]
            }
        },
        computed:{
            //栏目id
            newNavClassId(){
                return this.$store.state.newNavClassId
            },
        },
        methods: {
            scroll(){
                this.animate=true;    // 因为在消息向上滚动的时候需要添加css3过渡动画，所以这里需要设置true
                setTimeout(()=>{      //  这里直接使用了es6的箭头函数，省去了处理this指向偏移问题，代码也比之前简化了很多
                    this.items.push(this.items[0]);  // 将数组的第一个元素添加到数组的
                    this.items.shift();               //删除数组的第一个元素
                    this.animate=false;  // margin-top 为0 的时候取消过渡动画，实现无缝滚动
                },500)
            }
        },
//        created(){
//            this.$store.dispatch('getNewsPrize');
//        },
        created(){
            this.$axios.get("/news/index/getnewlist/nav_id/" + this.newNavClassId.notice_id).then(({data}) => {
                this.$store.commit('setNewsPrize', data.data);
                this.$set(this,'items',data.data.slice(0))
//                this.showMarquee(this.num)
                setInterval(this.scroll,3000)
            });
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    #box{
        width: 300px;
        height: 32px;
        overflow: hidden;
        padding-left: 30px;
        border: 1px solid black;
    }
    .anim{
        transition: all 0.5s;
        margin-top: -30px;
    }
    #con1 li{
        list-style: none;
        line-height: 30px;
        height: 30px;
    }
    .news-prize{
        height: 34px;
        padding: 2px 10px;
        font-size:pxTorem(13);
        .marquee_title{
            padding-right: pxTorem(5);
        }
        .marquee_box{
            height: pxTorem(30);
            overflow: hidden;
            position: relative;
            .marquee_list{
                display: block;
                position: absolute;
                top: 0;
                left: 0;
                li{
                    height: pxTorem(30);
                    line-height: pxTorem(30);
                    i{
                        font-size: 14px;
                    }
                }
            }
            .marquee_top{transition: top 0.5s ;}
        }
    }
</style>
