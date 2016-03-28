<?php
/*
 * 以下内容由easyitlife添加，每段代码的作用都已经写了注释，为了省事就懒得加入后台选项面板了。你可以按照自己的需要来修改
 * 若是在使用过程中遇到问题，欢迎前往easyitlife的网站http://easyitlife.com留言或者直接联系easyitlife获取帮助。
 *
 * 2015年11月17日 14:45:21 修复内容如下
 * 修复因为疏忽导致的点赞于拍砖中点赞不计数问题
 * 
 * 2015年11月17日 12:48:03 修复内容如下
 * 修复点赞拍砖时出现的alert提示框
 * 修复缩略图导致会员中心文章列表加载错误的问题
 * 修复小工具带缩略图时显示错位的问题
 * 修复无限加载文章后导致缩略图不显示的问题
 * 顺便说明一下主题下载，演示等简码使用方式，假设需要一个主题演示，那么就输入简码如[demo]这是主题演示站点[/demo]，然后在文章下方输入一个自定义字段demo，里面的值就填入你需要演示的网址即可。
 * 其他如下载页面简码的使用方式可以到我网站http://easyitlife.com搜索  独立下载页面  那篇文章中easyitlife就介绍过
 
 * 若是使用中碰到bug或者有什么其他疑问欢迎加入easyitlife的QQ群：479928584和大家一起交流
*/

require get_stylesheet_directory() . '/inc/fn.php';

//设置AJAS评论的字数限制
/**
 * @param $commentdata
 * @return mixed
 */
function set_comments_length($commentdata) {
    $minCommentlength = 3;      //最少字數限制
    $maxCommentlength = 200;   //最多字數限制
    $pointCommentlength = mb_strlen($commentdata['comment_content'],'UTF8');    //mb_strlen 一个中文字符当做一个长度
    if ($pointCommentlength < $minCommentlength){
        err('抱歉，您评论的字数过少，请至少输入' . $minCommentlength .'个字（目前字数：'. $pointCommentlength .'个字）');
        exit;
    }
    if ($pointCommentlength > $maxCommentlength){
        err('抱歉，您评论的字数过多，请输入少于' . $maxCommentlength .'个字的评论（您目前输入了：'. $pointCommentlength .'个字）');
        exit;
    }
    return $commentdata;
}
add_filter('preprocess_comment', 'set_comments_length');

#图片链接修改
function auto_post_link($content) {
	global $post;
        $content = preg_replace('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', "<a class=\"dengxiang\" href=\"javascript:;\" data-scr=\"$2\"title=\"".$post->post_title."\" ><img src=\"$2\" alt=\"".$post->post_title."\" /></a>", $content);
	return $content;
}
add_filter ('the_content', 'auto_post_link',0);

#去除摘要的[...]
function Easyitlife_more() {
    return '';
}
add_filter('excerpt_more', 'Easyitlife_more');
#为摘要添加继续阅读
function Easyitlife_more_link($output) {
    if (!is_attachment()) {
        if (!has_excerpt()) {
            $output = mb_strimwidth($output, 0, 300);
        }
        $output .= '</p><a href="' . esc_url(get_permalink()) . '" class="gengduo">' . ' &rarr;[ 阅读全文 ] &larr; </a>';
    }
    return $output;
}
add_filter('get_the_excerpt', 'Easyitlife_more_link');

//搜索结果只有一个时直接跳转到文章
add_action('template_redirect', 'redirect_single_post');
function redirect_single_post() {
    if (is_search()) {
        global $wp_query;
        if ($wp_query->post_count == 1 && $wp_query->max_num_pages == 1) {
            wp_redirect( get_permalink( $wp_query->posts['0']->ID ) );
            exit;
        }
    }
}

//压缩时绕过代码块
function unCompress($content) {
    if(preg_match_all('/(crayon-|<\/pre>)/i', $content, $matches)) {
        $content = '<!--wp-compress-html--><!--wp-compress-html no compression-->'.$content;
        $content.= '<!--wp-compress-html no compression--><!--wp-compress-html-->';
    }
    return $content;
}
add_filter( "the_content", "unCompress");
//压缩html代码 
function wp_compress_html(){
    function wp_compress_html_main ($buffer){
        $initial=strlen($buffer);
        $buffer=explode("<!--wp-compress-html-->", $buffer);
        $count=count ($buffer);
        for ($i = 0; $i <= $count; $i++){
            if (stristr($buffer[$i], '<!--wp-compress-html no compression-->')) {
                $buffer[$i]=(str_replace("<!--wp-compress-html no compression-->", " ", $buffer[$i]));
            } else {
                $buffer[$i]=(str_replace("\t", " ", $buffer[$i]));
                $buffer[$i]=(str_replace("\n\n", "\n", $buffer[$i]));
                $buffer[$i]=(str_replace("\n", "", $buffer[$i]));
                $buffer[$i]=(str_replace("\r", "", $buffer[$i]));
                while (stristr($buffer[$i], '  ')) {
                    $buffer[$i]=(str_replace("  ", " ", $buffer[$i]));
                }
            }
            $buffer_out.=$buffer[$i];
        }
        $final=strlen($buffer_out);   
        $savings=($initial-$final)/$initial*100;   
        $savings=round($savings, 2);   
        $buffer_out.="\n<!--压缩前的大小: $initial bytes; 压缩后的大小: $final bytes; 节约：$savings% -->";   
    return $buffer_out;
}
ob_start("wp_compress_html_main");
}
add_action('get_header', 'wp_compress_html');
//百度实时推送
/*
function mee_post_baidu($post_id,$post){
	$PostUrl = get_permalink($post_id);
	$urls=array($PostUrl);
	$api = '';//请输入你站长平台的主动推送api
	$ch = curl_init();//主机需要支持curl
	$options = array(
				CURLOPT_URL => $api,
				CURLOPT_POST => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POSTFIELDS => implode("\n", $urls),
				CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
			);
	curl_setopt_array($ch, $options);
	curl_exec($ch);
}
add_action('publish_post', 'mee_post_baidu');
*/
// Customize your functions
//评论分页的seo处理
function canonical_for_junke() {
        global $cpage, $post;
        if ( $cpage > 1 ) :
                echo "\n";
                echo "<link rel='canonical' href='";
                echo get_permalink( $post->ID );
                echo "' />\n";
                echo "<meta name=\"robots\" content=\"noindex,follow\">";
         endif;
}
add_action( 'wp_head', 'canonical_for_junke' );
//支持中文名注册，
function Easyitlife_zwuser ($username, $raw_username, $strict) {
  $username = wp_strip_all_tags( $raw_username );
  $username = remove_accents( $username );
  $username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
  $username = preg_replace( '/&.+?;/', '', $username ); // Kill entities
  if ($strict) {
    $username = preg_replace ('|[^a-z\p{Han}0-9 _.\-@]|iu', '', $username);
  }
  $username = trim( $username );
  $username = preg_replace( '|\s+|', ' ', $username );
  return $username;
}
add_filter ('sanitize_user', 'Easyitlife_zwuser', 10, 3);

//取消后台登陆错误的提示
function git_wps_login_error() {
        remove_action('login_head', 'wp_shake_js', 12);
}
add_action('login_head', 'git_wps_login_error');
//百度收录提示
/*
function baidu_check($url) {
    global $wpdb;
    $post_id = (null === $post_id) ? get_the_ID() : $post_id;
    $baidu_record = get_post_meta($post_id, 'baidu_record', true);
    if ($baidu_record != 1) {
        $url = 'http://www.baidu.com/s?wd=' . $url;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $rs = curl_exec($curl);
        curl_close($curl);
        if (!strpos($rs, '没有找到')) {
            if ($baidu_record == 0) {
                update_post_meta($post_id, 'baidu_record', 1);
            } else {
                add_post_meta($post_id, 'baidu_record', 1, true);
            }
            return 1;
        } else {
            if ($baidu_record == false) {
                add_post_meta($post_id, 'baidu_record', 0, true);
            }
            return 0;
        }
    } else {
        return 1;
    }
}*/
function baidu_record() {
   /*
   if (baidu_check(get_permalink()) == 1) {
        echo '<a target="_blank" title="点击查看" rel="external nofollow" href="http://www.baidu.com/s?wd=' . get_the_title() . '"><i class="fa fa-flag"></i>百度已收录</a>';
    } else {
        echo '<a rel="external nofollow" title="帮忙点击提交下，谢谢！" target="_blank" href="http://zhanzhang.baidu.com/linksubmit/url?sitename=' . get_permalink() . '"><i class="fa fa-flag"></i>百度抽风了</a>';
    }*/
}
//中文文件重命名
function git_upload_filter($file) {
    $time = date("YmdHis");
    $file['name'] = $time . "" . mt_rand(1, 100) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'git_upload_filter');

//屏蔽昵称，评论内容带链接的评论
function Googlolink($comment_data) {
    $links = '/http:\/\/|https:\/\/|www\./u';
    if (preg_match($links, $comment_data['comment_author']) || preg_match($links, $comment_data['comment_content'])) {
        err(__('在昵称和评论里面是不准发链接滴.'));
    }
    return ($comment_data);
}
add_filter('preprocess_comment', 'Googlolink');

//WordPress文字标签关键词自动内链
$match_num_from = 1; 
$match_num_to = 6; 
function tag_sort($a, $b) {
    if ($a->name == $b->name) return 0;
    return (strlen($a->name) > strlen($b->name)) ? -1 : 1;
}
function tag_link($content) {
    global $match_num_from, $match_num_to;
    $posttags = get_the_tags();
    if ($posttags) {
        usort($posttags, "tag_sort");
        foreach ($posttags as $tag) {
            $link = get_tag_link($tag->term_id);
            $keyword = $tag->name;
            $cleankeyword = stripslashes($keyword);
            $url = "<a href=\"$link\" title=\"" . str_replace('%s', addcslashes($cleankeyword, '$') , __('查看更多关于%s的文章')) . "\"";
            $url.= ' target="_blank"';
            $url.= ">" . addcslashes($cleankeyword, '$') . "</a>";
            $limit = rand($match_num_from, $match_num_to);
            $content = preg_replace('|(<a[^>]+>)(.*)(' . $ex_word . ')(.*)(</a[^>]*>)|U' . $case, '$1$2%&&&&&%$4$5', $content);
            $content = preg_replace('|(<img)(.*?)(' . $ex_word . ')(.*?)(>)|U' . $case, '$1$2%&&&&&%$4$5', $content);
            $cleankeyword = preg_quote($cleankeyword, '\'');
            $regEx = '\'(?!((<.*?)|(<a.*?)))(' . $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s' . $case;
            $content = preg_replace($regEx, $url, $content, $limit);
            $content = str_replace('%&&&&&%', stripslashes($ex_word) , $content);
        }
    }
    return $content;
}
add_filter('the_content', 'tag_link', 1);
/*相关图片文章图片调取*/
add_theme_support( 'post-thumbnails' );
 
function catch_that_image() {
global $post, $posts;
$first_img = '';
ob_start();
ob_end_clean();
$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
$first_img = $matches [1] [0];
if(empty($first_img)){
$popimg= get_stylesheet_directory_uri() . '/img/thumbnail.png';
$first_img = "$popimg";
}
return $first_img;
}
 
function mmimg($postID) {
 $cti = catch_that_image();
 $showimg = $cti;
 has_post_thumbnail();
 if ( has_post_thumbnail() ) { 
 $thumbnail_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail');
 $shareimg = $thumbnail_image_url[0];
 } else { 
 $shareimg = $showimg;
 };
 return $shareimg;
} 
/*相关图片文章图片调取end*/
add_filter('pre_option_link_manager_enabled','__return_true');
/*输出缩略图*/
function _get_post_thumbnail() {
	global $post;
	if (has_post_thumbnail ()) {
		//如果存在缩略图
		$domsxe = simplexml_load_string ( get_the_post_thumbnail () );
		$thumbnailsrc = $domsxe->attributes()->src;
		echo '<img data-src="' . $thumbnailsrc . '" class="thumb">';
	} else {
		//读取第一张图片
		$content = $post->post_content;
		preg_match_all ( '/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER );
		$n = count ( $strResult [1] );
		if ($n > 0) {
			//文章第一张图片
			echo '<img data-src="' . $strResult [1] [0] . '" class="thumb">';
		} else {
			//如果文章没有图片则读取默认图片
			echo '<img data-src="' . get_stylesheet_directory_uri() . '"/img/thumbnail.png" class="thumb">';
		}
	}
}
//拍砖
add_action('wp_ajax_nopriv_junke_pz', 'junke_pz');
add_action('wp_ajax_junke_pz', 'junke_pz');
function junke_pz() {
    global $wpdb, $post;
    $id = $_POST["um_id"];
    $action = $_POST["um_action"];
    if ($action == 'cai') {
        $bigfa_raters = get_post_meta($id, 'junke_cai', true);
        $expire = time() + 3600;
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        setcookie('junke_cai_' . $id, $id, $expire, '/', $domain, false);
        if (!$bigfa_raters || !is_numeric($bigfa_raters)) {
            update_post_meta($id, 'junke_cai', 1);
        } else {
            update_post_meta($id, 'junke_cai', ($bigfa_raters + 1));
        }
        echo get_post_meta($id, 'junke_cai', true);
    }
    die;
}
//点赞
add_action('wp_ajax_nopriv_bigfa_like', 'bigfa_like');
add_action('wp_ajax_bigfa_like', 'bigfa_like');
function bigfa_like() {
    global $wpdb, $post;
    $id = $_POST["um_id"];
    $action = $_POST["um_action"];
    if ($action == 'ding') {
        $bigfa_raters = get_post_meta($id, 'bigfa_ding', true);
        $expire = time() + 3600;
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false; 
        setcookie('bigfa_ding_' . $id, $id, $expire, '/', $domain, false);
        if (!$bigfa_raters || !is_numeric($bigfa_raters)) {
            update_post_meta($id, 'bigfa_ding', 1);
        } else {
            update_post_meta($id, 'bigfa_ding', ($bigfa_raters + 1));
        }
        echo get_post_meta($id, 'bigfa_ding', true);
    }
    die;
}
/*
* 作者：easyitlife(Easyitlife)
* 标题：纯代码为自己的WordPress网站添加简码(Shortcode)功能!
* 地址：http://easyitlife.com/chundaimajianmawordpress.html
* 若是使用中遇到疑问，请到easyitlife(Easyitlife)网站留言
* 步骤一：添加简码的功能
*/
#主题演示与下载
function Easyitlife_zhuti($atts, $content = null) {
	return '<div class="themes-download"><span class="on">' . $content . '</span><span><i class="fa fa-desktop"></i><a target="_blank" href="'.site_url().'/yanshi1?pid='.get_the_ID().'" rel="nofollow" title="点此查看主题实际效果">在线演示</a></span><span><i class="fa fa-download"></i><a target="_blank" href="'.site_url().'/xiazai?pid='.get_the_ID().'" rel="nofollow" title="点此前往easyitlife提供的下载页面">立即下载</a></span></div>';
}
add_shortcode('zhuti', 'Easyitlife_zhuti');
##网站演示
function Easyitlife_yanshi($atts, $content = null) {
	return '<div class="themes-download"><span class="on">' . $content . '</span><span class="url"><i class="fa fa-desktop"></i><a target="_blank" href="'.site_url().'/yanshi1?pid='.get_the_ID().'" rel="nofollow" title="点此查看实际效果">在线演示</a></span></div>';
}
add_shortcode('yanshi', 'Easyitlife_yanshi');
##下载页面
function Easyitlife_xiazai($atts, $content = null) {
	return '<div class="themes-download"><span class="on">' . $content . '</span><span class="bdpan"><i class="fa fa-download"></i><a target="_blank" href="'.site_url().'/xiazai?pid='.get_the_ID().'" rel="nofollow" title="点此前往easyitlife提供的下载页面">立即下载</a></span></div>';
}
add_shortcode('xiazai', 'Easyitlife_xiazai');
add_action('admin_head','Easyitlife_html_quicktags');
function Easyitlife_html_quicktags() {
        $output = "<script type='text/javascript'>\n
        /* <![CDATA[ */ \n";
        wp_print_scripts( 'quicktags' );
        $buttons[] = array(
                'name' => 'xiazai',
                'options' => array(
                        'display_name' => '文件下载',
                        'open_tag' => '\n[xiazai]',
                        'close_tag' => '[/xiazai]\n',
                        'key' => ''
        ));
		$buttons[] = array(
                'name' => 'yanshi',
                'options' => array(
                        'display_name' => '网站演示',
                        'open_tag' => '\n[yanshi]',
                        'close_tag' => '[/yanshi]\n',
                        'key' => ''
        ));
		$buttons[] = array(
                'name' => 'zhuti',
                'options' => array(
                        'display_name' => '网站演示与文件下载',
                        'open_tag' => '\n[zhuti]',
                        'close_tag' => '[/zhuti]\n',
                        'key' => ''
        ));
        /*
         * easyitlife(Easyitlife)
         * 若要添加更多简码，请按照上面easyitlife给出的格式添加。
         */
        for ($i=0; $i <= (count($buttons)-1); $i++) {
                $output .= "edButtons[edButtons.length] = new edButton('ed_{$buttons[$i]['name']}'
                        ,'{$buttons[$i]['options']['display_name']}'
                        ,'{$buttons[$i]['options']['open_tag']}'
                        ,'{$buttons[$i]['options']['close_tag']}'
                        ,'{$buttons[$i]['options']['key']}'
                ); \n";
        }
        $output .= "\n /* ]]> */ \n
        </script>";
        echo $output;
}
/*简码功能结束*/
