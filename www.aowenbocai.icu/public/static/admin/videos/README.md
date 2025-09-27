# 管理系统视频资源目录

## 目录说明
此目录用于存放管理系统相关的视频文件，主要用于：
- 登录页面背景视频
- 系统介绍视频
- 操作指导视频
- 其他管理系统相关的视频资源

## 访问路径
视频文件可通过以下路径访问：
```
https://www.aowenbocai.icu/static/admin/videos/your-video-file.mp4
```

## 支持的视频格式
推荐使用以下格式以确保最佳兼容性：
- **MP4** (推荐) - 最佳兼容性
- **WebM** - 现代浏览器支持
- **OGV** - 开源格式

## 文件命名规范
为了便于管理，请遵循以下命名规范：
- 使用小写字母和连字符
- 避免使用空格和特殊字符
- 示例：`login-background.mp4`, `system-intro.mp4`

## 文件大小建议
- 登录背景视频：建议不超过10MB
- 介绍视频：建议不超过50MB
- 考虑网络加载速度，适当压缩视频

## 使用示例

### 在HTML中引用视频
```html
<!-- 作为背景视频 -->
<video autoplay muted loop>
    <source src="/static/admin/videos/login-background.mp4" type="video/mp4">
    您的浏览器不支持视频播放。
</video>

<!-- 作为内容视频 -->
<video controls width="800">
    <source src="/static/admin/videos/system-intro.mp4" type="video/mp4">
    您的浏览器不支持视频播放。
</video>
```

### 在CSS中引用（作为背景）
```css
.video-background {
    background: url('/static/admin/videos/login-background.mp4');
}
```

## 注意事项
1. 上传视频前请确保文件已经过适当压缩
2. 考虑提供多种格式以支持不同浏览器
3. 大文件建议使用CDN或视频流服务
4. 定期清理不再使用的视频文件

## 维护记录
- 2025-09-27: 创建视频资源目录
- 目录创建者: 系统管理员

---
*此目录由管理系统自动创建和维护*
