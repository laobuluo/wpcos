<?php
	function wpcos_setting_page() {
	if (!current_user_can('manage_options')) {
		wp_die('Insufficient privileges!');
	}
	$wpcos_options = get_option('wpcos_options');
	if ( !$wpcos_options || !is_array($wpcos_options) ) {
		$wpcos_options = array(
			'bucket' => '', 'region' => '', 'app_id' => '', 'secret_id' => '', 'secret_key' => '',
			'no_local_file' => false, 'cos_url_path' => '',
			'opt' => array( 'auto_rename' => 0 ),
		);
	}
	if ($wpcos_options && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce']) && !empty($_POST)) {
		if($_POST['type'] == 'cos_info_set') {
			foreach ($wpcos_options as $k => $v) {
				if ($k =='no_local_file') {
					$wpcos_options[$k] = isset($_POST[$k]);
				} elseif($k =='opt') {
					$wpcos_options[$k]['auto_rename'] = (isset($_POST['auto_rename'])) ? 1 : 0;
				} else {
					if ($k != 'cos_url_path') {
						$wpcos_options[$k] = (isset($_POST[$k])) ? sanitize_text_field(trim(stripslashes($_POST[$k]))) : '';
					}
				}
			}
			$wpcos_options = wpcos_set_thumbsize($wpcos_options, isset($_POST['disable_thumb']) );

			update_option('wpcos_options', $wpcos_options);
			update_option('upload_url_path', esc_url_raw(trim(trim(stripslashes($_POST['upload_url_path'])))));
			?>
            <div class="notice notice-success settings-error is-dismissible"><p><strong>WPCOS插件设置已保存。</strong></p></div>
			<?php
		}
	}
	?>
	<style>
		.wpcosform .layui-form-label{width: 100px;}
		.wpcosform .layui-input{width: 350px;}
		.wpcosform .layui-form-mid{margin-left: 22px;}
		.wpcosform .layui-textarea{width: 350px; margin-left: 15px; margin-top:5px;}
		.laobuluo-wp-hidden {position: relative;}
		.laobuluo-wp-hidden .laobuluo-wp-eyes{padding: 5px;position:absolute;top:0;z-index: 999;display: none;}
		.laobuluo-wp-hidden i{font-size: 24px;}
		/* 移动端自适应：隐藏右侧关注公众号区域 */
		@media screen and (max-width: 768px) {
			.wpcos-sidebar { display: none !important; }
			.wpcos-main { width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; }
			.wpcosform .layui-input,
			.wpcosform .layui-textarea { width: 100% !important; max-width: 100% !important; }
			.wpcosform .layui-form-label { width: 100px; }
		}
	</style>
	<link rel="stylesheet" href="//at.alicdn.com/t/font_1923259_c7h9medvfjt.css" />
   <div class="container-laobuluo-main">
   <div class="laobuluo-wbs-header" style="margin-bottom: 15px;">
             <div class="laobuluo-wbs-logo"><span class="wbs-span">WPCOS - 腾讯云对象存储插件</span><span class="wbs-free">Free V4.8</span></div>
            <div class="laobuluo-wbs-btn">
                 <a class="layui-btn layui-btn-primary" href="https://www.lezaiyun.com/1093.html" target="_blank"><i class="layui-icon layui-icon-home"></i> 插件详情</a>
                 <a class="layui-btn layui-btn-primary" href="https://www.lezaiyun.com/contact/" target="_blank"><i class="layui-icon layui-icon-release"></i> 技术支持</a>
            </div>
       </div>
   </div>
   <!-- 内容 -->
   <div class="container-laobuluo-main">
       <div class="layui-container container-m">
           <div class="layui-row layui-col-space15">
			    <!-- 左边 -->
			   <div class="layui-col-md9 wpcos-main">
				    <div class="laobuluo-panel">
						 <div class="laobuluo-controw">
							  <fieldset class="layui-elem-field layui-field-title site-title">
							      <legend><a name="get">设置选项</a></legend>
							  </fieldset>
							   <form class="layui-form wpcosform" action="<?php echo wp_nonce_url('./admin.php?page=' . WPCOS_BASEFOLDER . '/wpcos_actions.php'); ?>" name="wpcosform" method="post">
							    <div class="layui-form-item">
									 <label class="layui-form-label">空间名称</label>
									 <div class="layui-input-block">
										   <input type="text"  placeholder="示范：laobuluo-xxxxxx"  name="bucket" value="<?php echo esc_attr($wpcos_options['bucket'] ?? ''); ?>" class="layui-input" style="width: 350px;">
										   <div class="layui-form-mid layui-word-aux">需要在腾讯云创建<code>bucket</code>存储桶。注意：填写"存储桶名称-对应ID". 示范： <span class="layui-badge layui-bg-orange">laobuluo-12345678</span></div>
									 </div>
								</div>
								<div class="layui-form-item">
									 <label class="layui-form-label">所属地域</label>
									 <div class="layui-input-block">
										 <input type="text" class="layui-input" name="region" value="<?php echo esc_attr($wpcos_options['region'] ?? ''); ?>" 
										        placeholder="示范：ap-shanghai"/>
										 <div class="layui-form-mid layui-word-aux">直接填写我们存储桶所属地区，示范：<span class="layui-badge layui-bg-orange">ap-shanghai</span></div>
									 </div>
								</div>
								<div class="layui-form-item">
									  <label class="layui-form-label">访问域名</label>
									  <div class="layui-input-block">
										   <input type="text"class="layui-input" name="upload_url_path" value="<?php echo esc_url(get_option('upload_url_path')); ?>" size="60"
										    placeholder="请输入COS远程地址/自定义目录"/>
											<div class="layui-form-mid layui-word-aux" style="line-height:30px;">
												<b>设置事项：</b></br>
												1. 一般我们是以：<code>http://{cos域名}</code>，不要用"<code>/</code>"结尾，支持自定义域名</br>
												2. 支持自定义COS目录，可实现<code>{cos域名}/自定义目录</code>格式</br>
												3. 示范1：<code>https://laojiang-xxxxx.cos.ap-shanghai.myqcloud.com</code></br>
												4. 示范2：<code>https://laojiang-xxxxx.cos.ap-shanghai.myqcloud.com/laobuluo</code></br>
											</div>
									  </div>
								</div>
								<div class="layui-form-item">
									 <label class="layui-form-label">APPID设置</label>
									 <div class="layui-input-block">
										 <input type="text" class="layui-input"  name="app_id" value="<?php echo esc_attr($wpcos_options['app_id'] ?? ''); ?>" size="40"
										        placeholder="APP ID"/>
									 </div>
								</div>
								<div class="layui-form-item">
									 <label class="layui-form-label">SecretId设置</label>
									 <div class="layui-input-block">
										 <div class="laobuluo-wp-hidden">
										    <input type="password" class="layui-input"  name="secret_id" value="<?php echo esc_attr($wpcos_options['secret_id'] ?? ''); ?>" size="50" placeholder="secretID"/>
									        <span class="laobuluo-wp-eyes"><i class="iconfont layui-extendbiyan"></i></span>
									    </div>
									 </div>
								</div>
								<div class="layui-form-item">
									<label class="layui-form-label">SecretKey设置</label>
									<div class="layui-input-block">
										<div class="laobuluo-wp-hidden">
											<input type="password" class="layui-input"  name="secret_key" value="<?php echo esc_attr($wpcos_options['secret_key'] ?? ''); ?>" size="50" placeholder="secretKey"/>
											<span class="laobuluo-wp-eyes"><i class="iconfont layui-extendbiyan"></i></span>
										</div>
										<div class="layui-form-mid layui-word-aux">登入 <a href="https://console.qcloud.com/cam/capi" target="_blank">API密钥管理</a> 可看到 <code>APPID | SecretId | SecretKey</code>。初次使用需创建 <code>新建密钥</code></div>
									</div>
								</div>
								<div class="layui-form-item">
									 <label class="layui-form-label">自动重命名</label>
									 <div class="layui-input-inline" style="width:60px;">
										  <input type="checkbox"
										         name="auto_rename" title="设置"
										  	<?php
										  	if ( !empty($wpcos_options['opt']['auto_rename'] ?? 0) ) {
										  		echo 'checked="TRUE"';
										  	}
										  	?>
										  />
									 </div>
									 <div class="layui-form-mid layui-word-aux">上传文件自动重命名，解决中文文件名或者重复文件名问题</div>
								</div>
								<div class="layui-form-item">
									 <label class="layui-form-label">不在本地保存</label>
									  <div class="layui-input-inline" style="width:60px;">
										  <input type="checkbox"
										         name="no_local_file" title="设置"
										  	<?php
										  	if ($wpcos_options['no_local_file']) {
										  		echo 'checked="TRUE"';
										  	}
										  	?>
										  />
									  </div>
									  <div class="layui-form-mid layui-word-aux">禁止文件保存本地。<span class="layui-badge layui-bg-orange">建议勾选</span> 本地不保存，减少服务器占用资源</div>
								</div>
								<div class="layui-form-item">
									<label class="layui-form-label">禁止缩略图</label>
									<div class="layui-input-inline" style="width:60px;">
										 <input type="checkbox" name="disable_thumb" title="禁止"
										 	<?php
												if ( isset($wpcos_options['opt']['thumbsize']) && is_array($wpcos_options['opt']['thumbsize']) ) {
													echo 'checked="TRUE"';
												}
										 	?>
										 >
									</div>
									<div class="layui-form-mid layui-word-aux">仅生成和上传主图，禁止缩略图裁剪。主题自带缩略图不禁止</div>
								</div>
								<div class="layui-form-item">
									  <div class="layui-input-block">
										    <button class="layui-btn" type="submit" name="submit" value="保存设置" lay-submit lay-filter="formDemo">保存设置</button>
									  </div>
								</div>
								<input type="hidden" name="type" value="cos_info_set">
							   </form>
						 </div>
					</div>
			   </div>
			    <!-- 左边 -->
				<!-- 右边  -->
				<div class="layui-col-md3 wpcos-sidebar">
					 <div id="nav">
						 <div class="laobuluo-panel">
                        <div class="laobuluo-panel-title">关注公众号</div>
                        <div class="laobuluo-code">
                            <img src="<?php echo plugin_dir_url(__FILE__); ?>layui/images/qrcode.png">
                            <p>微信扫码关注 <span class="layui-badge layui-bg-blue">老蒋朋友圈</span> 公众号</p>
                            <p><span class="layui-badge">优先</span> 获取插件更新 和 更多 <span class="layui-badge layui-bg-green">免费插件</span> </p>
                        </div>
                    </div>
                     
					 </div>
				</div>
				<!-- 右边 end -->
		  </div>
		</div>	   
    </div>
   <!-- 内容end -->
   <!-- footer -->
   <div class="container-laobuluo-main">
	   <div class="layui-container container-m">
		   <div class="layui-row layui-col-space15">
			   <div class="layui-col-md12">
				<div class="laobuluo-footer-code">
					 <span class="codeshow"></span>
				</div>
				<div class="laobuluo-links">
                    <a href="https://www.lezaiyun.com/"  target="_blank">乐在云工作室</a>
                    <a href="https://www.zhujipingjia.com/pianyivps.html" target="_blank">便宜VPS推荐</a>
                    <a href="https://www.zhujipingjia.com/hkcn2.html" target="_blank">香港VPS推荐</a>
                    <a href="hhttps://www.zhujipingjia.com/uscn2gia.html" target="_blank">美国VPS推荐</a>
                </div>
			   </div>
		   </div>
	   </div>
   </div>
   <!-- footer -->
   <script>
   
       layui.use(['form', 'element','jquery'], function() {
           var $ =layui.jquery;
		   var form = layui.form;
		   
           function menuFixed(id) {
             var obj = document.getElementById(id);
             var _getHeight = obj.offsetTop;
             var _Width= obj.offsetWidth
             window.onscroll = function () {
               changePos(id, _getHeight,_Width);
             }
           }
           function changePos(id, height,width) {
             var obj = document.getElementById(id);
             obj.style.width = width+'px';
             var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
             var _top = scrollTop-height;
             if (_top < 150) {
               var o = _top;
               obj.style.position = 'relative';
               o = o > 0 ? o : 0;
               obj.style.top = o +'px';
               
             } else {
               obj.style.position = 'fixed';
               obj.style.top = 50+'px';
           
             }
           }
           menuFixed('nav');
		   
		   var laobueys = $('.laobuluo-wp-hidden')
		  
		   laobueys.each(function(){
			   
			   var inpu = $(this).find('.layui-input');
			   var eyes = $(this).find('.laobuluo-wp-eyes')
			   var width = inpu.width();
			   
			   eyes.css('left',width+'px').show();
			   
			   eyes.click(function(){
				   if(inpu.attr('type') == "password"){
					   inpu.attr('type','text')
	                   eyes.html('<i class="iconfont layui-extendyanjing"></i>')
				   }else{
					   inpu.attr('type','password')
					   eyes.html('<i class="iconfont layui-extendbiyan"></i>')
				   }
			   })
		   })
		   
       })
   </script>
	<?php
}
?>