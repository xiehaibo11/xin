<!--图片懒加载组件-->
<template>
    <div class="lazy-image-wrapper" :style="wrapperStyle">
        <img 
            v-if="loaded"
            :src="src" 
            :alt="alt"
            :class="imageClass"
            :style="imageStyle"
            @load="onLoad"
            @error="onError"
        />
        <div 
            v-else-if="loading" 
            class="lazy-loading"
            :style="placeholderStyle"
        >
            <div class="loading-spinner"></div>
            <span v-if="showLoadingText">加载中...</span>
        </div>
        <div 
            v-else-if="error" 
            class="lazy-error"
            :style="placeholderStyle"
            @click="retry"
        >
            <i class="iconfont icon-tupian"></i>
            <span>加载失败，点击重试</span>
        </div>
        <div 
            v-else
            class="lazy-placeholder"
            :style="placeholderStyle"
        >
            <!-- 占位符内容 -->
        </div>
    </div>
</template>

<script>
export default {
    name: 'LazyImage',
    props: {
        src: {
            type: String,
            required: true
        },
        alt: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: ''
        },
        width: {
            type: [String, Number],
            default: 'auto'
        },
        height: {
            type: [String, Number],
            default: 'auto'
        },
        imageClass: {
            type: String,
            default: ''
        },
        imageStyle: {
            type: Object,
            default: () => ({})
        },
        showLoadingText: {
            type: Boolean,
            default: false
        },
        // 懒加载配置
        rootMargin: {
            type: String,
            default: '50px'
        },
        threshold: {
            type: Number,
            default: 0.1
        }
    },
    data() {
        return {
            loaded: false,
            loading: false,
            error: false,
            observer: null
        }
    },
    computed: {
        wrapperStyle() {
            return {
                width: typeof this.width === 'number' ? `${this.width}px` : this.width,
                height: typeof this.height === 'number' ? `${this.height}px` : this.height,
                position: 'relative',
                overflow: 'hidden'
            }
        },
        placeholderStyle() {
            return {
                width: '100%',
                height: '100%',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                flexDirection: 'column',
                backgroundColor: '#f5f5f5',
                color: '#999'
            }
        }
    },
    mounted() {
        this.initLazyLoad()
    },
    beforeDestroy() {
        this.destroyObserver()
    },
    methods: {
        initLazyLoad() {
            // 检查浏览器是否支持 Intersection Observer
            if ('IntersectionObserver' in window) {
                this.observer = new IntersectionObserver(
                    this.handleIntersection,
                    {
                        rootMargin: this.rootMargin,
                        threshold: this.threshold
                    }
                )
                this.observer.observe(this.$el)
            } else {
                // 降级方案：直接加载图片
                this.loadImage()
            }
        },
        handleIntersection(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadImage()
                    this.observer.unobserve(this.$el)
                }
            })
        },
        loadImage() {
            if (this.loading || this.loaded) return
            
            this.loading = true
            this.error = false
            
            const img = new Image()
            img.onload = () => {
                this.loaded = true
                this.loading = false
                this.$emit('load')
            }
            img.onerror = () => {
                this.error = true
                this.loading = false
                this.$emit('error')
            }
            img.src = this.src
        },
        onLoad() {
            this.$emit('loaded')
        },
        onError() {
            this.error = true
            this.loaded = false
            this.$emit('error')
        },
        retry() {
            this.error = false
            this.loadImage()
        },
        destroyObserver() {
            if (this.observer) {
                this.observer.disconnect()
                this.observer = null
            }
        }
    }
}
</script>

<style scoped>
.lazy-image-wrapper {
    display: inline-block;
}

.lazy-loading, .lazy-error, .lazy-placeholder {
    min-height: 60px;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.lazy-loading {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

.loading-spinner {
    width: 20px;
    height: 20px;
    border: 2px solid #ddd;
    border-top: 2px solid #007aff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 8px;
}

.lazy-error {
    background-color: #fafafa;
    border: 1px dashed #ddd;
    cursor: pointer;
    transition: background-color 0.3s;
}

.lazy-error:hover {
    background-color: #f0f0f0;
}

.lazy-error i {
    font-size: 24px;
    margin-bottom: 8px;
    color: #ccc;
}

.lazy-placeholder {
    background: #f8f8f8;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* 图片淡入效果 */
img {
    transition: opacity 0.3s ease;
    opacity: 0;
}

img[src] {
    opacity: 1;
}
</style>
