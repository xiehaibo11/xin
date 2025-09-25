<template>
    <div>

        <div v-if="extList.length" v-for="(item,index) in extList" :key="index" class="extlist border-bottom-1px card">
            <div class="mint-cell-allow-right card-pd flex-box" @click="toGame(item.info.type,item.info.name)">
                <p class="extlist-img"  :style="{backgroundImage:'url(' + item.info.image + ')'}"></p>
                <p style="padding-left: 10px">{{item.info.title}}</p>
                <p class="flex tr" style="padding-right: 20px"> <mt-button size="small" plain>进入游戏</mt-button></p>
            </div>
        </div>
        <div v-else class="tc c-3" style="padding: 50px">您还未玩任何游戏，<router-link to="/game" class="link">立即去大厅体验</router-link></div>
    </div>
</template>
<script>
    export default{
        data() {
            return {
            };
        },
        computed:{
            extList(){
                return this.$store.state.extList
            }
        },
        methods:{
            toGame(type,name){
                if(type == 1){
                    let nameA = name.indexOf('/')>-1 ? name.replace("/","") : name;
                    let path
                    if(name.indexOf('11')>-1){
                        path = 'syxw'
                    }else if(name.indexOf('ssc')>-1){
                        path = 'ssc'
                    }else if(name.indexOf('10')>-1){
                        path = 'pk10'
                    }else if(name.indexOf('28')>-1){
                        path = 'pc28'
                    }else if(name.indexOf('ks')>-1){
                        path = 'ks'
                    }
                    this.$router.push({
                        path:'/'+ path,
                        query:{
                            name:nameA
                        }
                    })
                }else {
                    this.$router.push({
                        path:'/iframe',
                        query:{
                            u:name
                        }
                    })
                }
            }
        },
        created(){
            this.$store.dispatch('getExtList') //最近玩过的游戏
        }
    }

</script>

<style scoped type="text/scss" lang="scss">
    .extlist-img{
        width: 60px;
        height: 60px;
        background-size: cover;
    }
</style>
