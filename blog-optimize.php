<?php
/*
Plugin Name: 博客优化
Author: 水脉烟香
Author URI: http://www.smyx.net/
Plugin URI: http://blogqun.com/blog-optimize.html
Description: WordPress优化、功能增强、使用SMTP发邮件、CDN加速、站点地图(sitemap，包括移动sitemap)、数据库清理等。
Version: 1.0
*/

define("BLOG_OPTIMIZE_VERSION", '1.0');
define("BLOG_OPTIMIZE_URL", plugins_url('blog-optimize'));

if (!function_exists('blog_optimize_zend')) {
	function blog_optimize_zend() {
		if (version_compare(PHP_VERSION, '5.5', '>=')) {
		} else {
			if (function_exists('zend_loader_enabled')) {
				if (version_compare(PHP_VERSION, '5.4', '>=')) {
					return '/zend2/';
				} elseif (version_compare(PHP_VERSION, '5.3', '>=')) {
					return '/zend/';
				} else {
					return '/';
				} 
			} 
		} 
	} 
} 

$blog_optimize_zend = blog_optimize_zend();

add_action('admin_menu', 'blog_optimize_add_page');
function blog_optimize_add_page() {
	if (function_exists('add_menu_page')) {
		add_menu_page('博客优化', '博客优化', 'manage_options', 'blog-optimize', 'blog_optimize_do_page');
	} 
}
function blog_optimize_do_page() {
	global $blog_optimize_zend;
	if (isset($_POST['submit_save'])) {
		echo '<div class="updated" id="saved_box"><p><strong>请购买后使用。</strong></p></div>';
	} 
	$wpurl = trim(get_bloginfo('wpurl'), '/');
	$optimize = get_option('blog_optimize');
	if (!$optimize && is_multisite()) { // WPMU
		$optimize = get_site_option('blog_optimize');
	}
	if (!$optimize) {
		$optimize = array('remove_head' => 1,
			'disable_xmlrpc' => 0,
			'remove_admin_bar' => 0,
			'disable_trackbacks' => 1,
			'diable_revision' => 1,
			'id_continuous'=> 1,
			'diable_update' => 0,
			'remove_dashboard' => 1, // 1- 新闻 2-所有
			'disallow_file_edit' => 0,
			'remove_meta_box' => array('commentsdiv', 'authordiv'),
			'replace_avatar' => 1,
			'remove_fonts' => 1, // 1 - 替换 2 - 删除
			'replace_ajax' => 1,
			'file_name' => 1,
			'home__not_in' => array(), // 'category'=> array(), 'posts'=> array()
			'orderby' => '', // modified
			'sort' => 0, // ?order=xxx
			//'from_name' => '',
			//'from_email' => '',
			'post_link' => 0, // 1-外链 2-内链
			'post_link_meta' => '_links_to',
			'add_column' => 1,
			'login_time' => 1,
			'db' => array(1,2,3,4,5,6,7,8)
			);
	}
	if (!$optimize['remove_meta_box']) $optimize['remove_meta_box'] = array();

	if (!$optimize['sitemap']) $optimize['sitemap'] = array('all' => 1, 'html' => 1);

	echo '<div class="updated"><p><strong>请到【<a href="http://blogqun.com/blog-optimize.html" target="_blank">点击这里</a>】购买插件，买后卸载本插件，<a href="http://blogqun.com/download" target="_blank">重新下载</a>安装后使用。</strong></p></div>';

	if (!$blog_optimize_zend) {
		if (version_compare(PHP_VERSION, '5.5', '>=')) {
			$zend_install_tips = '很遗憾，您正在使用PHP 5.5.x以上版本，暂时不能使用“付费插件”。请降到PHP5.4.x或者PHP5.3.x或者PHP5.2.x';
		} elseif (version_compare(PHP_VERSION, '5.3', '>=')) {
			$zend_install_tips = '很遗憾，您不能使用“付费插件”，php 5.3.x及以上版本请安装 <a href="http://www.zend.com/en/products/guard/downloads" target="_blank">Zend Guard Loader</a>';
		} else {
			$zend_install_tips = '很遗憾，您不能使用“付费插件”，请安装 <a href="http://www.zend.com/en/products/guard/downloads-prev" target="_blank">Zend Optimizer</a>';
		} 
		$zend_install_tips .= '<br />如果您正在使用Godaddy linux主机，可以自己开启zend，<a href="http://www.smyx.net/godaddy-linux-open-zend-optimizer.html" target="_blank">查看教程</a>';
		echo '<div class="updated"><p><strong>' . $zend_install_tips . '</strong></p></div>';
	}
	wp_register_style('blog-optimize-admin', BLOG_OPTIMIZE_URL . '/css/admin.css', array(), BLOG_OPTIMIZE_VERSION);
	wp_register_script("jquery-bqq", BLOG_OPTIMIZE_URL . "/js/jquery.ba-bbq.min.js", array("jquery"));
	wp_register_script("blog-optimize-admin", BLOG_OPTIMIZE_URL . "/js/admin.js", array(), BLOG_OPTIMIZE_VERSION);
	wp_print_styles('blog-optimize-admin');
	wp_print_scripts('jquery-bqq');
	wp_print_scripts('blog-optimize-admin');
?>
<form class="plugin_options" action="" method="post" id="plugin-options-panel">
  <div class="header">
	<div class="header_left">
	  <h3><a href="http://blogqun.com/blog-optimize.html" target="_blank" title="WordPress优化与增强">WordPress</a></h3>
	  <h5>优化与增强</h5>
	</div>
	<div class="header_right">
	  <div class="description">
		<h3>博客优化 v<?php echo BLOG_OPTIMIZE_VERSION;?></h3>
		<h5><a href="http://blogqun.com/author/smyx" target="_blank" title="查看水脉烟香所有作品">水脉烟香出品</a></h5>
	  </div>
	</div>
  </div>
  <div class="content clearfix">
	<ul class="menu">
	  <li><a href='#tab-base'<?php echo (!$authorize['authorize_code']) ? ' class="selected"' : '';?>>基本设置<span class="general"></span></a></li>
	  <li><a href="#tab-optimize"<?php echo ($authorize['authorize_code']) ? ' class="selected"' : '';?>>优化设置<span class="refresh"></span></a></li>
	  <li><a href="#tab-add">功能增强<span class="contact_form"></span></a>
		  <ul class="submenu" style="display: none;">
			 <li><a href="#tab-add_custom">自定义</a></li>
			 <li><a href="#tab-add_other">其他设置</a></li>
			 <li><a href="#tab-add_sitemap">Sitemap(站点地图)</a></li>
			 <li><a href="#tab-add_cdn">CDN</a></li>
			 <li><a href="#tab-add_smtp">SMTP(邮箱设置)</a></li>
		  </ul>
	  </li>
	  <li><a href="#tab-mysql">数据清理<span class="colors"></span></a></li>
	  <li><a href="#tab-open">常用设置<span class="setting"></span></a></li>
	  <li><a href="#tab-about">关于我们<span class="social"></span></a></li>
	</ul>
	<div id="tab-base" class="settings"<?php echo (!$authorize['authorize_code']) ? ' style="display:block"' : '';?>>
	  <h3>插件授权</h3>
	  <div class="option">
		<?php if ($is_network) { // WPMU
			echo '<span class="text-tips">该插件已经被 《<a href="' . get_site_option('siteurl') . '" target="_blank">' . get_site_option('site_name') . '</a>》授权允许在整个网络使用，插件提供者: <a href="http://blogqun.com/blog-optimize.html" target="_blank">水脉烟香</a>。</span>';
		} else {
			echo '<h4>填写插件授权码</h4><input type="text" class="inputs" name="authorize_code" size="50" value="' . $authorize['authorize_code'] . '" /> ' . $code_yes;
			if (is_multisite()) echo '<span class="text-tips">您正在使用WPMU，您可以在“管理网络”的插件页面“整个网络启用“博客优化”，然后在 管理网络 -> 设置 -> <a href="' . admin_url('network/settings.php?page=blog-optimize') . '">博客优化</a> 填写插件“根域名”授权码。<a href="http://blogqun.com/blog-optimize.html" target="_blank">如何获得根域名授权码</a></span>';
		}?>
	  </div>
    </div>
	<div id="tab-optimize" class="settings"<?php echo ($authorize['authorize_code']) ? ' style="display:block"' : '';?>>
	  <h3>优化设置</h3>
	  <div class="option">
		<h4>上传遇到中文名称时自动改名</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[file_name]" type="hidden" value="<?php echo $optimize['file_name']; ?>">
		<span class="text-tips">命名方式: 时间戳+随机数字</span>
	  </div>
	  <div class="option">
		<h4>新用户注册时禁止通知管理员</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[disable_send]" type="hidden" value="<?php echo $optimize['disable_send']; ?>">
		<span class="text-tips">默认情况下，新用户注册时会发送一封邮件通知管理员，如果不需要通知请开启。</span>
	  </div>
	  <div class="option">
		<h4>登录后跳转到首页</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[login_redirect]" type="hidden" value="<?php echo $optimize['login_redirect']; ?>">
		<span class="text-tips">如果在<code>wp-login.php</code>登录，默认是进入后台(仪表盘)，开启后会进入首页。只有在<code>redirect_to</code>没有值时生效。</span>
	  </div>
	  <div class="option">
		<h4>关闭升级</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[diable_update]" type="hidden" value="<?php echo $optimize['diable_update']; ?>">
		<span class="text-tips">开启后，将禁用WordPress升级、主题升级、插件升级，后台速度将大大提升。<strong>建议在想升级时开启，升级后关闭。</strong></span>
	  </div>
	  <div class="option">
		<h4>去掉头部多余代码</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[remove_head]" type="hidden" value="<?php echo $optimize['remove_head']; ?>">
	  </div>
	  <div class="option">
		<h4>关闭 Trackbacks</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[disable_trackbacks]" type="hidden" value="<?php echo $optimize['disable_trackbacks']; ?>">
		<span class="text-tips">Trackbacks会带来一些垃圾评论</span>
	  </div>
	  <div class="option">
		<h4>移除顶部工具栏</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[remove_admin_bar]" type="hidden" value="<?php echo $optimize['remove_admin_bar']; ?>">
	  </div>
	  <div class="option">
		<h4>禁止修订日志及自动保存</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[diable_revision]" type="hidden" value="<?php echo $optimize['diable_revision']; ?>">
	  </div>
	  <div class="option">
		<h4>文章ID连续</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[id_continuous]" type="hidden" value="<?php echo $optimize['id_continuous']; ?>">
		<span class="text-tips">发布文章时会对文章ID连续性做一些优化。需要开启【禁止修订日志及自动保存】，同时数据清理的[自动保存的文章]、[无效的菜单]的清理将自动失效。</span>
	  </div>
	  <div class="option">
		<h4>禁用 XML-RPC 接口</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[disable_xmlrpc]" type="hidden" value="<?php echo $optimize['disable_xmlrpc']; ?>">
		<span class="text-tips">如果你使用离线工具发布文章，请勿关闭。</span>
	  </div>
	  <div class="option">
		<h4>修改头像服务器</h4>
		<select name="optimize[replace_avatar]" class="option-select">
		  <option value=""<?php selected($optimize['replace_avatar'] == '');?>>选择</option>
		  <option value="2" <?php selected($optimize['replace_avatar'] == 2);?>>https://secure.gravatar.com</option>
		  <option value="3"<?php selected($optimize['replace_avatar'] == 3);?>>http://3.gravatar.com</option>
		  <option value="1"<?php selected($optimize['replace_avatar'] == 1);?>>http://cn.gravatar.com (推荐)</option>
		  <option value="ga"<?php selected($optimize['replace_avatar'] == 'ga');?>>http://ga.gravatar.com</option>
		  <option value="fr"<?php selected($optimize['replace_avatar'] == 'fr');?>>http://fr.gravatar.com</option>
		</select>
		<span class="text-tips">如果您的用户主要在国内，建议选择一个。</span>
	  </div>
	  <div class="option">
		<h4>替换Google字体库</h4>
		<select name="optimize[remove_fonts]" class="option-select">
		  <option value=""<?php selected($optimize['remove_fonts'] == '');?>>选择</option>
		  <option value="1"<?php selected($optimize['remove_fonts'] == 1);?>>替换成360 CDN</option>
		  <option value="2" <?php selected($optimize['remove_fonts'] == 2);?>>移除Google字体（不影响使用）</option>
		</select>
		<span class="text-tips">这是导致后台慢的原因之一。</span>
	  </div>
	  <div class="option">
		<h4>替换Google前端公共库为360 CDN</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[replace_ajax]" type="hidden" value="<?php echo $optimize['replace_ajax']; ?>">
	  </div>
	  <div class="option">
		<h4>移除后台首页模块</h4>
		<select name="optimize[remove_dashboard]" class="option-select">
		  <option value="1"<?php selected($optimize['remove_dashboard'] == 1);?>>移除WordPress 新闻</option>
		  <option value="2"<?php selected($optimize['remove_dashboard'] == 2);?>>移除所有模块</option>
		  <option value="" <?php selected($optimize['remove_dashboard'] == '');?>>不移除</option>
		</select>
	  </div>
	  <div class="option">
		<h4>移除文章发布页面模块</h4>
		<div class="option-check">
		  <label class="label_check"><input name="optimize[remove_meta_box][]" type="checkbox" value="commentsdiv" <?php checked(in_array('commentsdiv', $optimize['remove_meta_box']));?>/>评论（推荐，没必要到文章编辑页面查看评论吧）</label>
		  <label class="label_check"><input name="optimize[remove_meta_box][]" type="checkbox" value="authordiv" <?php checked(in_array('authordiv', $optimize['remove_meta_box']));?>/>作者（推荐，不需要修改文章作者吧）</label>
		  <label class="label_check"><input name="optimize[remove_meta_box][]" type="checkbox" value="postcustom" <?php checked(in_array('postcustom', $optimize['remove_meta_box']));?>/>自定义栏目（仅仅隐藏，功能并不影响）</label>
		</div>
		<span class="text-tips">无论何时，管理员仍然可以使用自定义栏目。</span>
	  </div>
    </div>
	<div id="tab-add" class="settings">
	<div id="tab-add_custom" class="subsettings">
	  <h3>自定义</h3>
	  <div class="option">
		<h4>首页不显示某些<a href="edit-tags.php?taxonomy=category" target="_blank">分类</a></h4>
		<input name="optimize[home__not_in][category]" class="inputs" type="text" size="60" value="<?php echo $optimize['home__not_in']['category'];?>" />
		<span class="text-tips">填写分类数字ID，多个用英文逗号<code>,</code>分开，为方便查看ID，可以在【其他功能】开启【文章、评论、用户等列表显示ID】</span>
	  </div>
	  <div class="option">
		<h4>首页不显示某些<a href="edit.php" target="_blank">文章</a></h4>
		<input name="optimize[home__not_in][posts]" class="inputs" type="text" size="60" value="<?php echo $optimize['home__not_in']['posts'];?>" />
		<span class="text-tips">填写文章数字ID，多个用英文逗号<code>,</code>分开，为方便查看ID，可以在【其他功能】开启【文章、评论、用户等列表显示ID】</span>
	  </div>
	  <div class="option">
		<h4>网站头部添加Meta代码</h4>
		<textarea name="wp_head" class="option-textarea" cols="180" rows="4"><?php echo get_option('blog_optimize_wp_head');?></textarea>
		<span class="text-tips">提示：可以用于各种Meta验证</span>
	  </div>
	  <div class="option">
		<h4>网站底部添加额外代码</h4>
		<textarea name="wp_footer" class="option-textarea" cols="180" rows="4"><?php echo get_option('blog_optimize_wp_footer');?></textarea>
		<span class="text-tips">比如：可以添加统计代码等</span>
	  </div>
	</div>
	<div id="tab-add_other" class="subsettings">
	  <h3>其他设置</h3>
	  <div class="option">
		<h4>记录用户上次登录时间</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[login_time]" type="hidden" value="<?php echo $optimize['login_time']; ?>">
		<span class="text-tips">开启后会在用户登录时记录时间，并在用户列表中显示。</span>
	  </div>
	  <div class="option">
		<h4>文章、评论、用户等列表显示ID</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[add_column]" type="hidden" value="<?php echo $optimize['add_column']; ?>">
		<span class="text-tips">包括文章、页面、评论、用户、分类、标签列表，其中，页面列表还会显示模板文件名，用户列表还会显示注册时间。</span>
	  </div>
	  <div class="option">
		<h4>禁止编辑主题或者插件</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[disallow_file_edit]" type="hidden" value="<?php echo $optimize['disallow_file_edit']; ?>">
		<span class="text-tips">关闭后，不能在后台不能直接编辑主题或者插件文件。</span>
	  </div>
	  <div class="option">
		<h4>首页文章按修改时间排序</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[orderby]" type="hidden" value="<?php echo ($optimize['orderby'] == 'modified') ? 1 : ''; ?>">
	  </div>
	  <div class="option">
		<h4>首页文章多种排序方式</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[sort]" type="hidden" value="<?php echo $optimize['sort']; ?>">
		<span class="text-tips">在首页链接后添加参数sort，可以排序文章，比如<?php echo home_url();?>/?sort=<code>modified</code> 表示按修改时间排序，当值为 <code>date</code> 表示按时间排序，当值为 <code>comment_count</code> 表示按评论数从多到少排序；当值为 <code>rand</code> 表示按随机排序；当值为 <code>views</code> 表示按访问数排序(需要安装相关插件)</span>
	  </div>
	  <div class="option">
		<h4>文章链接可以改为站外链接</h4>
		<select name="optimize[post_link]" class="option-select">
		  <option value=""<?php selected($optimize['post_link'] == '');?>>选择</option>
		  <option value="1"<?php selected($optimize['post_link'] == 1);?>>对外显示外链</option>
		  <option value="2" <?php selected($optimize['post_link'] == 2);?>>对外显示内链</option>
		</select>
		<span class="text-tips">开启后，无论对外显示外链还是内链，打开链接都将跳转到外链。</span>
	  </div>
	  <div class="option">
		<h4>文章使用站外链接的字段名</h4>
		<input name="optimize[post_link_meta]" class="inputs" type="text" size="60" value="<?php echo !empty($optimize['post_link_meta']) ? $optimize['post_link_meta'] : '_links_to';?>" />
		<span class="text-tips">在文章发布页面，添加自定义栏目，输入新栏目<code><?php echo !empty($optimize['post_link_meta']) ? $optimize['post_link_meta'] : '_links_to';?></code> 值为具体外链。</span>
	  </div>
	</div>
	<div id="tab-add_sitemap" class="subsettings">
	  <h3>Sitemap(站点地图)</h3>
	  <div class="option">
		<span class="text-tips">百度和Google站点地图通用，在移动Sitemap稍有不同，已经做了判断。<p>提交入口: <a href="http://www.google.com/webmasters/" target="_blank">Google网站站长工具</a> 、<a href="http://zhanzhang.baidu.com/sitemap/index" target="_blank">百度站长平台</a> (目前只有优质网站才能添加)</p>
		<?php
		if (function_exists('blog_optimize_sitemap_url') && $optimize['sitemap']['open']) {
			$sitemap_url = blog_optimize_sitemap_url();
			echo '<p><strong>您的站点地图（支持所有搜索引擎）：</strong><a href="' . $sitemap_url . '" target="_blank">' . $sitemap_url . '</a></p>';
			echo '<p>如果您不能在百度站长平台添加sitemap，两种方案：<br />1. 在根目录的robots.txt文件末尾新增一行<br/><code>Sitemap: ' . $sitemap_url . '</code><br />2. 可以考虑安装<a href="http://zhanzhang.baidu.com/dataplug/index" target="_blank">结构化数据插件</a>，可以同时开启本插件，并不冲突。<br/>提示：网站是否收录与页面质量相关。)</p>';
		} 
		?></span>
	  </div>
	  <div class="option">
		<h4>开启Sitemap</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[sitemap][open]" type="hidden" value="<?php echo $optimize['sitemap']['open']; ?>">
		<span class="text-tips">生成XML地图。</span>
	  </div>
	  <div class="option">
		<h4>生成Html地图</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[sitemap][html]" type="hidden" value="<?php echo $optimize['sitemap']['html']; ?>">
		<span class="text-tips">
		<?php
		if ($optimize['sitemap']['html'] && $sitemap_url) {
			$sitemap_url = str_replace('.xml', '.html', $sitemap_url);
			echo '<strong>您的Html地图：</strong><a href="' . $sitemap_url . '" target="_blank">' . $sitemap_url . '</a>';
		} else {
			echo '需要开启Sitemap';
		}
		?>
		</span>
	  </div>
	  <div class="option">
		<h4>内容包括</h4>
		<div class="option-check">
		  <label class="label_check"><input name="optimize[sitemap][posts]" type="checkbox" value="1" <?php checked($optimize['sitemap']['all'] || $optimize['sitemap']['posts']);?>/>文章（包括自定义文章类型）</label>
		  <label class="label_check"><input name="optimize[sitemap][pages]" type="checkbox" value="1" <?php checked($optimize['sitemap']['all'] || $optimize['sitemap']['pages']);?>/>页面</label>
		  <label class="label_check"><input name="optimize[sitemap][category]" type="checkbox" value="1" <?php checked($optimize['sitemap']['all'] || $optimize['sitemap']['category']);?>/>分类</label>
		  <label class="label_check"><input name="optimize[sitemap][post_tag]" type="checkbox" value="1" <?php checked($optimize['sitemap']['all'] || $optimize['sitemap']['post_tag']);?>/>标签</label>
		  <label class="label_check"><input name="optimize[sitemap][archives]" type="checkbox" value="1" <?php checked($optimize['sitemap']['all'] || $optimize['sitemap']['archives']);?>/>存档</label>
		  <label class="label_check"><input name="optimize[sitemap][authors]" type="checkbox" value="1" <?php checked($optimize['sitemap']['all'] || $optimize['sitemap']['authors']);?>/>作者</label>
		</div>
		<span class="text-tips">默认包括首页。</span>
	  </div>
	  <div class="option">
		<h3>移动Sitemap</h3>
		<span class="text-tips">将网址提交给移动搜索收录，如果下面两项不设置，将不做特别处理。</span>
	  </div>
	  <div class="option">
		<h4>网站支持自适应网页</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[sitemap][auto]" type="hidden" value="<?php echo $optimize['sitemap']['auto']; ?>">
		<span class="text-tips">在不同的设备会自适应网页</span>
	  </div>
	  <div class="option">
		<h4>移动端使用不同的网址</h4>
		<input name="optimize[sitemap][domain]" class="inputs" type="text" size="60" value="<?php echo $optimize['sitemap']['domain'];?>" />
		<span class="text-tips">格式包括<code>wap.xxx.com</code>或者<code>xxx.com/wap</code></span>
	  </div>
	</div>
	<div id="tab-add_cdn" class="subsettings">
	  <h3>CDN</h3>
	  <div class="option">
		<h4>使用CDN加速</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[cdn][open]" type="hidden" value="<?php echo $optimize['cdn']['open']; ?>">
	  </div>
	  <div class="option">
		<h4>需要替换的静态文件域名</h4>
		<input name="optimize[cdn][local]" class="inputs" type="text" size="60" value="<?php echo $optimize['cdn']['local'] ? $optimize['cdn']['local'] : $wpurl;?>" />
		<span class="text-tips">默认为本站域名，如果您为图片等启用了二级域名，可以在此填写。 <br />在CDN设置的镜像源应该是<code><?php echo $optimize['cdn']['local'] ? $optimize['cdn']['local'] : $wpurl;?></code></span>
	  </div>
	  <div class="option">
		<h4>CDN域名</h4>
		<input name="optimize[cdn][host]" class="inputs" type="text" size="60" value="<?php echo $optimize['cdn']['host'];?>" />
		<span class="text-tips">可以使用<a target="_blank" href="https://portal.qiniu.com/signup?code=3l9fjlzz0ahci">七牛云存储</a>或者其他CDN，如果您使用七牛云，当修改文件遇到无法更新，请进入七牛云后台删除相关文件。</span>
	  </div>
	  <div class="option">
		<h4>包含目录</h4>
		<input name="optimize[cdn][dirs]" class="inputs" type="text" size="60" value="<?php echo $optimize['cdn']['dirs'] ? $optimize['cdn']['dirs'] : 'wp-content|wp-includes';?>" />
		<span class="text-tips">多个使用<code>|</code>隔开</span>
	  </div>
	  <div class="option">
		<h4>包含后缀</h4>
		<input name="optimize[cdn][exts]" class="inputs" type="text" size="60" value="<?php echo $optimize['cdn']['exts'] ? $optimize['cdn']['exts'] : 'js|css|jpg|jpeg|png|gif|ico';?>" />
		<span class="text-tips">多个使用<code>|</code>隔开</span>
	  </div>
	</div>
	<?php $smtp = get_option('blog_optimize_smtp');?>
	<div id="tab-add_smtp" class="subsettings">
	  <h3>SMTP(邮箱设置)</h3>
	  <div class="option">
		<h4>使用SMTP发送邮件</h4>
		<div class="on-off"><span></span></div>
		<input name="optimize[smtp]" type="hidden" value="<?php echo $optimize['smtp'];?>">
		<span class="text-tips">开启后，整个网站都使用此方式发送邮件，包括注册、找回密码、回复通知等。</span>
	  </div>
	  <div class="option">
		<h4>发送者姓名</h4>
		<input name="smtp[name]" class="inputs" type="text" size="60" value="<?php echo $smtp['name'] ? $smtp['name'] : get_bloginfo('name');?>" />
		<span class="text-tips">可留空。</span>
	  </div>
	  <div class="option">
		<h4>发送者邮箱</h4>
		<input name="smtp[email]" class="inputs" type="text" size="60" value="<?php echo $smtp['email'] ? $smtp['email'] : get_bloginfo('admin_email');?>" />
		<span class="text-tips">可留空。主要用于用户回复您时的接收邮箱，可以跟下面的【邮箱账号】不同。</span>
	  </div>
	  <div class="option">
		<h4>常用邮箱</h4>
		<select name="smtp[site]" class="option-select" onchange="if(this.options[0].selected == true) document.getElementById('smtp_host').style.display=''; else document.getElementById('smtp_host').style.display='none'">
		  <option value="">自定义</option>
		  <option value="qq.com"<?php selected($smtp['site'] == 'qq.com');?>>QQ (mail.qq.com)</option>
		  <option value="163.com"<?php selected($smtp['site'] == '163.com');?>>163 (163.com)</option>
		  <option value="126.com"<?php selected($smtp['site'] == '126.com');?>>126 (126.com)</option>
		  <option value="sina.cn"<?php selected($smtp['site'] == 'sina.cn');?>>新浪邮箱 (sina.cn)</option>
		  <option value="sina.com"<?php selected($smtp['site'] == 'sina.com');?>>新浪邮箱 (sina.com)</option>
		  <option value="exmail.qq.com"<?php selected($smtp['site'] == 'exmail.qq.com');?>>腾讯企业邮 (exmail.qq.com)</option>
		  <option value="gmail.com"<?php selected($smtp['site'] == 'gmail.com');?>>Gmail</option>
		</select>
		<span class="text-tips">建议新注册一个邮箱帐号，邮箱需要开启SMTP功能</span>
	  </div>
	  <div id="smtp_host"<?php if($smtp['site']) echo ' style="display:none"';?>>
	  <div class="option">
		<h4>Host(服务器)</h4>
		<input name="smtp[host]" class="inputs" type="text" size="60" value="<?php echo $smtp['host'];?>" />
	  </div>
	  <div class="option">
		<h4>Port(端口)</h4>
		<input name="smtp[port]" class="inputs" type="text" size="60" value="<?php echo $smtp['port'];?>" />
	  </div>
	  <div class="option">
		<h4>加密</h4>
		<select name="smtp[ssl]" class="option-select">
		  <option value="ssl"<?php selected($smtp['ssl'] == 'ssl');?>>SSL (常用)</option>
		  <option value="tls"<?php selected($smtp['ssl'] == 'tls');?>>TLS</option>
		  <option value="0"<?php selected($smtp['ssl'] === 0);?>>无</option>
		</select>
	  </div>
	  </div>
	  <div class="option">
		<h4>邮箱账号</h4>
		<input name="smtp[user]" class="inputs" type="text" size="60" value="<?php echo $smtp['user'];?>" />
	  </div>
	  <div class="option">
		<h4>邮箱密码</h4>
		<input name="smtp[pass]" class="inputs" type="password" size="60" autocomplete="off" />
		<?php if($smtp['pass']) echo '<span class="text-tips">密码留空表示不修改</span>';?>
	  </div>
	  <div class="option">
		<h4>测试邮箱地址(用于接收)</h4>
		<input name="smtp[test]" class="inputs" type="text" size="60" value="<?php echo $smtp['test'];?>" />
		<input type="submit" name="optimize_smtp_test" class="button" value="发送一封测试邮件（请先保存设置）"> <?php echo $state['smtp'];?>
		<span class="text-tips">不能与【邮箱帐号】相同</span>
		<input type="text" style="display:none" />
		<input type="password" style="display:none" autocomplete="off" />
	  </div>
	</div>
	</div>
	<div id="tab-mysql" class="settings">
	  <h3>数据库清理</h3>
	  <div class="option">
		<h4>定时清理</h4>
		<select name="optimize[cron_clear]" class="option-select">
		  <option value=""<?php selected($optimize['cron_clear'] == '');?>>选择</option>
		  <option value="30" <?php selected($optimize['cron_clear'] == 30);?>>每月一次</option>
		  <option value="7" <?php selected($optimize['cron_clear'] == 7);?>>每周一次</option>
		  <option value="1"<?php selected($optimize['cron_clear'] == 1);?>>每天一次</option>
		</select>
		<span class="text-tips">开启后，会定时清理以下数据库信息。如需只定时清理部分冗余数据，可以勾选后【保存设置】</span>
	  </div>
	  <?php $dbdata = get_option('blog_optimize_data');?>
	  <div class="option">
		<h4>冗余数据</h4>
		<input type="submit" name="optimize_db_clean" class="button" value="点击清除冗余数据"> <?php echo $state['clean'];?>
	  </div>
	  <div class="option">
		<h4>数据库优化</h4>
		<input type="submit" name="optimize_db" class="button" value="点击优化数据库"> <?php echo $state['db'];?>
	  </div>
    </div>
	<div id="tab-open" class="settings">
	  <h3>常用设置</h3>
	  <?php if (!is_multisite()) { ?>
	  <div class="option">
		<h4><?php _e('Anyone can register') ?></h4>
		<div class="on-off"><span></span></div>
		<input name="wpoption[users_can_register]" type="hidden" value="<?php echo get_option('users_can_register'); ?>">
		<span class="text-tips">如果您正在使用<a target="_blank" href="http://blogqun.com/wp-connect.html">WordPress连接微博</a>，不开启也能注册。</span>
	  </div>
	  <div class="option">
		<h4><?php _e('New User Default Role'); ?></h4>
		<select name="wpoption[default_role]" class="option-select">
		  <?php wp_dropdown_roles( get_option('default_role') ); ?>
		</select>
	  </div>
	  <?php } ?>
	  <div class="option">
		<h4><?php _e('Show Avatars'); ?></h4>
		<div class="on-off"><span></span></div>
		<input name="wpoption[show_avatars]" type="hidden" value="<?php echo get_option('show_avatars'); ?>">
	  </div>
	  <div class="option">
		<h4><?php _e('Users must be registered and logged in to comment'); ?></h4>
		<div class="on-off"><span></span></div>
		<input name="wpoption[comment_registration]" type="hidden" value="<?php echo get_option('comment_registration'); ?>">
	  </div>
	  <div class="option">
		<h4><?php _e('Comment author must fill out name and e-mail'); ?></h4>
		<div class="on-off"><span></span></div>
		<input name="wpoption[require_name_email]" type="hidden" value="<?php echo get_option('require_name_email'); ?>">
	  </div>
	  <div class="option">
		<h4><?php _e('Store uploads in this folder'); ?></h4>
		<input name="wpoption[upload_path]" class="inputs" type="text" size="60" value="<?php echo esc_attr(get_option('upload_path')); ?>" />
		<span class="text-tips"><?php _e('Default is <code>wp-content/uploads</code>'); ?></span>
	  </div>
	  <div class="option">
		<h4><?php _e('Full URL path to files'); ?></h4>
		<input name="wpoption[upload_url_path]" class="inputs" type="text" size="60" value="<?php echo esc_attr(get_option('upload_url_path')); ?>" />
		<span class="text-tips"><?php _e('Configuring this is optional. By default, it should be blank.'); ?></span>
	  </div>
    </div>
	<div id="tab-about" class="settings">
	  <h3>关于我们</h3>
		<div class="option">
		<h2>升级日志</h2>
		<h2>1.1</h2>
		<p>2015/3/18</p>
		<p>新增：<a href="#tab-add_sitemap">Sitemap(站点地图)（百度和Google通用,支持百度移动Sitemap）</a></p>
		<p>新增：<a href="#tab-add_cdn">使用CDN加速</a></p>
		<p>新增：<a href="#tab-add_smtp">使用SMTP发邮件（整站通用，包括注册，找回密码等）</a></p>
		<p>新增：<a href="#tab-optimize">上传遇到中文名称时自动改名</a></p>
		<p>新增：<a href="#tab-optimize">新用户注册时禁止通知管理员</a></p>
		<p>新增：<a href="#tab-optimize">登录后跳转到首页</a></p>
		<p>修正一些bug及优化部分代码。</p>
		<h2>1.0</h2>
		<p>2015/2/13</p>
		<p>初始版本。</p>
		</div>
    </div>
  </div>
  <div class="footer">
	<div class="footer_left">
	  <ul class="social-list">
		<li><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=3249892&site=qq&menu=yes" class="social-list-qq" title="QQ:3249892"></a></li>
		<li><a target="_blank" href="http://weibo.com/smyx" class="social-list-weibo" title="新浪微博"></a></li>
		<li><a target="_blank" href="http://t.qq.com/smyxapp" class="social-list-tqq" title="腾讯微博"></a></li>
		<li><a target="_blank" href="https://twitter.com/smyx" class="social-list-twitter" title="Twitter"></a></li>
	  </ul>
	</div>
	<div class="footer_right">
	  <input type="hidden" name="submit_save" />
	  <input type="submit" name="optimize_submit" class="button-submit" value="保存设置" />
	</div>
  </div>
</form>
<?php
}