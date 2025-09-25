Vue.component('modal-component', {
    props: {
        modalTitle: {
            type: String,
            default: 'modalTitle设置标题'
        }
    },
    data(){
        return{
            open : true
        }
    },
    template: `
            <div v-if="open">
                <transition name="modal-opacity">
                    <div class="v-modal-overlay"></div>
                </transition>
                <transition name="modal-zoom">
                    <div class="v-modal-p" @click="close">
                        <div class="v-modal">
                            <div class="v-modal-header">
                                <div class="m-title">
                                    <img src="/static/theme/default/svg/modal_bg_top.png" alt="">
                                    <span class="m-tit-name">· {{modalTitle}} ·</span>
                                </div>
                                <div class="m-close" @click="close">
                                    <img src="/static/theme/default/svg/modal_btn_close.png" alt="" width="45">
                                </div>
                            </div>
                            <div class="v-modal-content">
                                <slot name="modal-content">内容区域</slot>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
            `,
    methods: {
        close(){
            this.open = false;
            $('body').css('overflow', 'auto');
            $('html').css('overflow', 'auto');
        }
    },
})