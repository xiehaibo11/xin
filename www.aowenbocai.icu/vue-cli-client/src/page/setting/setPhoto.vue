<template>
    <div class="pic">
        <div class="tc mt mf">
            <span class="photo-pic" :style="{backgroundImage:'url(' + userInfo.photo + ')'}"></span>
        </div>
        <div class="tc">
            <mt-button size="small" type="danger" @click.native="handleUpload">点击上传头像</mt-button>
        </div>
        <input id="upload-input" type="file" name="image" accept="image/*"
               style="display: none"
               @change="setImage"
        />
        <!--<p>支持JPG、PNG格式图片，不超过5M。拖拽或缩放图中的虚线方格可调整头像，注意右侧小头像预览效果。</p>-->
        <div class="cropper-lay" v-show="show">
            <div>
                <vue-cropper
                    ref='cropper'
                    :guides="false"
                    :cropBoxMovable ="true"
                    :toggleDragModeOnDblclick  ="true"
                    :cropBoxResizable ="true"
                    :view-mode=1
                    :movable ="true"
                    :drag-mode="crop"
                    :auto-crop-area="0.8"
                    :min-container-width="sw"
                    :min-container-height="sh"
                    :can-scale= true
                    :background=true
                    :aspectRatio= 1/1
                    :src="imgSrc"
                    alt="Source Image"
                    :cropmove="cropImage"
                ></vue-cropper>
            </div>
            <!--<div class="tc mt mf">-->
            <!--<p><img :src="cropImg || imgSrc" class="photo-pic" alt="裁剪之后的图片"/></p>-->
            <!--<p class="f-sm mt-sm c-4">头像预览</p>-->
            <!--</div>-->
            <div class="tr clearfloat" style="width: 100%;position: fixed;bottom:0;">
                <mt-button  @click.native="cancel" class="btn_photo fl">取消</mt-button>
                <mt-button @click.native="photoSubmit" type="danger" class="btn_photo fr">保存</mt-button>
            </div>
        </div>
    </div>
</template>
<script>
    import VueCropper from 'vue-cropperjs';
    let w = document.body.offsetWidth ;
    let h = document.body.offsetHeight ;
    export default{
        data() {
            return {
                sw:w,
                sh:h,
                cropImg:'', //裁剪之后的图片
                crop:'',
                imgSrc:'',  //选择的图片
                show:false //图片裁剪层显示否
            };
        },
        computed:{
            userInfo(){
                return this.$store.state.userinfo
            }
        },
        methods:{
            handleUpload() {
                document.getElementById('upload-input').click()
            },
            setImage(e){
                const file = e.target.files[0];
                if (!file.type.includes('image/')) {
                    alert('Please select an image file');
                    return;
                }
                if (typeof FileReader === 'function') {
                    this.show = true
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        this.imgSrc = event.target.result;
                        this.$refs.cropper.replace(event.target.result);
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert('Sorry, FileReader API not supported');
                }
            },
            //图片裁剪
            cropImage () {
                this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
            },
            //保存图片
            photoSubmit(){
                this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
                this.$axios.post('/index/User/setPhoto',{
                    photo:this.cropImg
                }).then(({data}) =>{
                    if(!data.err){
                        this.$store.dispatch('getUserInfo').then(res=>{
                            this.$router.goBack(-1);//返回上一层
                        })
                    }
                    this.$toast({
                        message: data.msg,
                        duration: 1000
                    });
                }).catch(function (error) {
                    console.log(error);
                })
            },
            //取消设置
            cancel(){
                this.$router.goBack(-1);//返回上一层
            }
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .photo-pic{
        display: inline-block;
        background-size: cover;
        background-repeat: no-repeat;
        width:100px ;
        height: 100px;
        border-radius: 50%;
        border: 1px solid $color-border-one;
    }
    .cropper-lay{
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0px;
        left: 0;
        background-color: rgba(0, 0, 0, 0.9);
        z-index: 20000;
        img.cropper-hidden{height: 100%}
    }
    .btn_photo{
        margin: 20px;
    }
</style>
