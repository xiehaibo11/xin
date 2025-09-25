<template>
    <div>
        <div class="detail" v-if="systemMsg.length">
            <div class="tr mt-sm mf-sm" style="padding-right: 10px">
                <mt-button type="default" size="small" @click="setStatus('all')">标记全部为已读</mt-button>
                <mt-button type="default" size="small" @click="delHandle('all',2)">删除所有已读</mt-button>
                <mt-button type="default" size="small" @click="delHandle('all')">删除全部</mt-button>
            </div>
            <mt-cell-swipe
                :title="item.content" v-for="(item,index) in systemMsg" :key="index"
                :label="item.create_time"
                :class="{'org':!item.status}"
                :right="[
                        {
                          content: '删除',
                          style: { background: 'red', color: '#fff' },
                          handler(){delHandle(item.id)}
                        },
                          {
                          content: '取消',
                          style: { background: '#c5c5c5', color: '#fff' },
                          handler(){cancleHandle() }
                        }
                    ]">
                <span v-if="!item.status"><mt-button type="default" size="small" @click="setStatus(item.id)">标记为已读</mt-button></span>
            </mt-cell-swipe>
        </div>
        <div class="tc c-3 mt" v-else>暂无系统消息</div>
    </div>
</template>
<script>
//    import pageItem from 'components/common/PageItem.vue';
    export default{
        data() {
            return {
//                url:'/web/user/getmessage'
            };
        },
        computed:{
            systemMsg(){
                return this.$store.state.systemMsg
            }
        },
        components: {
//            pageItem
        },
        methods:{
            //删除
            delHandle(id,type){
                let data
                if(type){
                    data = {
                        id:id,
                        type:type
                    }
                }else {
                    data = {
                        id:id
                    }
                }
                this.$axios.post('/web/User/messageDelete',data).then(({data}) =>{
                    if(!data.err){
                        this.$store.dispatch('getSystemMsg')
                        this.$toast(data.msg);
                    }else {
                        this.$toast(data.msg);
                    }
                }).catch(function (error) {
                    console.log(error);
                })
            },
            //取消
            cancleHandle(){
            },
            //标记为已读
            setStatus(id){
                this.$axios.post('/web/User/setStatus',{
                    id:id
                }).then(({data}) =>{
                    if(!data.err){
                        this.$store.dispatch('getSystemMsg')
                        this.$toast(data.msg);
                    }else {
                        this.$toast(data.msg);
                    }
                }).catch(function (error) {
                    console.log(error);
                })
            },
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .org{
        .mint-cell-swipe-button{
            padding: 0 20px;
            line-height: 48px;
        }
    }

</style>
