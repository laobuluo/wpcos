# WPCOS - 腾讯云对象存储插件

WordPress COS（简称 WPCOS），将 WordPress 附件自动同步到腾讯云 COS 对象存储，实现网站静态资源与主站分离，提升加载速度并降低服务器压力。

## 功能特点

- **自动同步上传**：媒体库上传的图片、附件自动同步到腾讯云 COS
- **自定义域名**：支持绑定 CDN 或自定义域名作为访问地址
- **自定义目录**：支持在 COS 中指定目录，一个存储桶可服务多个站点
- **本地可选保存**：可选择仅存 COS，不在本地保留，节省服务器存储
- **禁止缩略图**：可关闭 WordPress 默认缩略图裁剪，只上传原图
- **自动重命名**：上传时自动重命名，避免中文文件名和重名问题
- **远程同名检测**：上传前检测 COS 中是否存在同名文件，避免覆盖
- **删除同步**：媒体库删除附件时同步删除 COS 中对应文件
- **图片编辑支持**：在媒体库中编辑图片后，修改结果会同步到 COS
- **移动端适配**：设置页面支持移动端访问
- **上传优化**：对大图上传进行优化，降低“服务器无法处理图片”等错误概率

## 环境要求

- WordPress 6.0+
- PHP 7.4+
- 腾讯云 COS 存储桶（需使用 V5 版本，V4 兼容性较差）

## 安装

1. 将 `wpcos` 目录上传到 `/wp-content/plugins/`
2. 在 WordPress 后台 **插件** 中启用 WPCOS
3. 进入 **设置 → WPCOS 设置** 完成配置

## 配置说明

### 1. 腾讯云控制台准备

1. 登录 [腾讯云控制台](https://console.cloud.tencent.com/)
2. 创建 COS 存储桶，记录：
   - 存储桶名称
   - 所属地域（如 `ap-shanghai`）
   - 存储桶全称格式：`存储桶名称-APPID`（如 `my-bucket-1234567890`）
3. 在 [API 密钥管理](https://console.qcloud.com/cam/capi) 创建密钥，获取：
   - SecretId
   - SecretKey

### 2. 插件设置项

| 设置项 | 说明 |
|--------|------|
| 空间名称 | 存储桶全称，格式：`bucket名称-APPID`，如 `my-bucket-1234567890` |
| 所属地域 | 存储桶地域，如 `ap-shanghai`、`ap-guangzhou` |
| 访问域名 | COS 访问地址或自定义域名，如 `https://xxx.cos.ap-shanghai.myqcloud.com` |
| APPID | 腾讯云账号 APPID（可选） |
| SecretId | API 密钥 ID |
| SecretKey | API 密钥 Key |
| 自动重命名 | 上传时自动重命名，避免中文或重名问题 |
| 不在本地保存 | 仅存 COS，不保留本地文件，建议勾选 |
| 禁止缩略图 | 不上传 WordPress 默认缩略图，仅上传原图 |

### 3. 访问域名示例

```
# 使用 COS 默认域名
https://my-bucket-1234567890.cos.ap-shanghai.myqcloud.com

# 使用自定义目录
https://my-bucket-1234567890.cos.ap-shanghai.myqcloud.com/my-site

# 使用自定义域名（需在 COS 控制台绑定）
https://cdn.example.com
```

## 使用说明

1. **新站**：完成配置后，新上传的附件会自动同步到 COS
2. **老站迁移**：将原有 `wp-content/uploads` 中的文件上传到 COS 对应目录，再在数据库中完成路径替换（需自行处理或使用数据库搜索替换工具）
3. **停用插件**：停用时会恢复原 `upload_url_path`，不影响已上传至 COS 的文件访问

## 常见问题

### 1. 上传失败：提示“服务器无法处理图片”

- 建议在设置中开启 **禁止缩略图**
- 检查 PHP 配置：`memory_limit` 建议 ≥ 256M，`max_execution_time` 建议 ≥ 120
- 插件已内置上传资源优化，若仍失败，多为服务器资源限制

### 2. 附件上传后无法访问

- 确认 **访问域名** 填写正确且可公网访问
- 检查 COS 存储桶是否设为“公有读”或已配置相应访问策略

### 3. 删除附件后 COS 中文件仍在

- 插件会在媒体库删除附件时同步删除 COS 上的文件
- 若未删除，可能是 COS 配置有误或请求失败，可查看 WordPress 调试日志

### 4. 是否需要升级 COS 版本

- 插件基于腾讯云 COS PHP SDK V5 开发，请使用 V5 版本存储桶，V4 兼容性较差

## 文件结构

```
wpcos/
├── wpcos.php              # 主入口
├── wpcos_actions.php      # 核心逻辑
├── wpcos_setting_page.php # 设置页面
├── uninstall.php          # 卸载逻辑
├── sdk/                   # 腾讯云 COS SDK
├── layui/                 # 前端资源
└── README.md
```

## 更新日志

### 4.8
- 兼容 WordPress 6.9
- 优化代码安全与稳定性
- 优化大图上传错误处理
- 兼容 PHP 8+
- 设置页移动端适配

## 插件团队和技术支持

[乐在云](https://www.lezaiyun.com/)（老蒋和他的伙伴们），本着资源共享原则，在运营网站过程中用到的或者是有需要用到的主题、插件资源，有选择的免费分享给广大的网友站长，希望能够帮助到你建站过程中提高效率。

感谢团队成员，以及网友提出的优化工具的建议，才有后续产品的不断迭代适合且满足用户需要。不能确保100%的符合兼容网站，我们也仅能做到在工作之余不断的接近和满足你的需要。

| 类目            | 信息                                                         |
| --------------- | ------------------------------------------------------------ |
| 插件更新地址    | https://www.lezaiyun.com/1093.html                           |
| 团队成员        | [老蒋](https://www.laojiang.me/)、老赵、[CNJOEL](https://www.rakvps.com/)、木村 |
| 支持网站        | 乐在云、主机评价网、老蒋玩主机                               |
| 建站资源推荐    | [便宜VPS推荐](https://www.zhujipingjia.com/pianyivps.html)、[美国VPS推荐](https://www.zhujipingjia.com/uscn2gia.html)、[外贸建站主机](https://www.zhujipingjia.com/wordpress-hosting.html)、[SSL证书推荐](https://www.zhujipingjia.com/two-ssls.html)、[WordPress主机推荐](https://www.zhujipingjia.com/wpblog-host.html) |
| 提交WP官网（F） | https://cn.wordpress.org/plugins/wpcos/                      |

![](wechat.png)
