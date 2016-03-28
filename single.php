<?php get_header(); ?>
<section class="container">
	<div class="content-wrap">
	<div class="content">
		<?php while (have_posts()) : the_post(); ?>
		<header class="article-header">
			<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
			<div class="fabiao"><strong>本文由 <?php echo get_the_author(); ?> 于 <?php echo get_the_time('Y-m-d G:i:s'); ?> 发表</strong></div>
			<div class="article-meta">
				<span class="item"><?php echo get_the_time('Y-m-d'); ?></span>
				<?php _moloader('mo_get_post_from', false); ?>
				<?php if( mo_get_post_from() ){ ?><span class="item"><?php echo mo_get_post_from(); ?></span><?php } ?>
				<span class="item"><?php echo '分类：';the_category(' / '); ?></span>
				<?php $p_meta = _hui('post_plugin'); ?>
				<?php if( $p_meta['view'] ){ ?><span class="item post-views"><?php echo _get_post_views() ?></span><?php } ?>
				<span class="item"><?php echo _get_post_comments() ?></span>
				<span class="item"><?php edit_post_link('[编辑]'); ?></span>
			</div>
		</header>
		<article class="article-content">
			<?php _the_ads($name='ads_post_01', $class='asb-post asb-post-01') ?>
			<?php the_content(); ?>
		</article>
		<?php wp_link_pages('link_before=<span>&link_after=</span>&before=<div class="article-paging">&after=</div>&next_or_number=number'); ?>
		<?php endwhile; ?>
		<?php  
			if( _hui('post_link_single_s') ){
				_moloader('mo_post_link');
			}
		?>
		<div class="post-like">
				<a href="javascript:;" data-action="ding" data-id="<?php the_ID(); ?>" class="favorite<?php if(isset($_COOKIE['bigfa_ding_'.$post->ID])) echo ' done';?>" <?php if(isset($_COOKIE['bigfa_ding_'.$post->ID])){echo 'title="请勿重复点赞，每个小时内只能点赞一次！"';} else {echo 'title="感谢您的点赞！"';}?>><i class="fa fa-heart"></i>点赞 (<span class="count"><?php if( get_post_meta($post->ID,'bigfa_ding',true) ){
                    echo get_post_meta($post->ID,'bigfa_ding',true);
                 } else {
                    echo '0';
                 }?></span>)</a><span class="or">or</span><a class="paizhuan" href="javascript:;" data-action="cai" data-id="<?php the_ID(); ?>" class="paizhuan post-paizhuan<?php if(isset($_COOKIE['junke_cai_'.$post->ID])) echo ' done';?>" <?php if(isset($_COOKIE['junke_cai_'.$post->ID])){echo 'title="您已经拍过砖了！"';} else {echo 'title="砖头很硬的~求不拍~~~~！"';}?>><i class="fa fa-heart-o"></i>拍砖 (<span class="count"><?php if( get_post_meta($post->ID,'junke_cai',true) ){
                    echo get_post_meta($post->ID,'junke_cai',true);
                 } else {
                    echo '0';
                 }?></span>)</a>
		</div>		
		<div class="action-share bdsharebuttonbox">
			<?php _moloader('mo_share'); ?>
		</div>
		<div class="article-tags"><?php the_tags('标签：','',''); ?></div>
		<?php _the_ads($name='ads_post_02', $class='asb-post asb-post-02') ?>
		<nav class="pager" role="navigation">
			<li class="previous">
				<?php $prev_post = get_previous_post(); get_previous_post() ? print '<a title="上一篇：'.$prev_post->post_title.'" href="'.get_permalink( $prev_post->ID ).'">上一篇：'.$prev_post->post_title.'</a>' : print '<a>很抱歉，这已经是最后的一篇文章了！</a>';?>
			</li>
			<li class="next">
				<?php $next_post = get_next_post(); get_next_post() ? print '<a title="下一篇：'.$next_post->post_title.'" href="'.get_permalink( $next_post->ID ).'">下一篇：'.$next_post->post_title.'</a>' : print '<a>很抱歉，这已经是最新的一篇文章了！</a>';?>
			</li>
		</nav>
		<div class="pads">
<?php
global $post;
$post_tags = wp_get_post_tags($post->ID);
if ($post_tags) {
  foreach ($post_tags as $tag) {
    // 获取标签列表
    $tag_list[] .= $tag->term_id;
  }
  // 随机获取标签列表中的一个标签
  $post_tag = $tag_list[ mt_rand(0, count($tag_list) - 1) ];
  // 该方法使用 query_posts() 函数来调用相关文章，以下是参数列表
  $args = array(
        'tag__in' => array($post_tag),
        'category__not_in' => array(NULL),  // 不包括的分类ID
        'post__not_in' => array($post->ID),
        'showposts' => 4,                           // 显示相关文章数量
        'caller_get_posts' => 1
    );
  query_posts($args);
	if (have_posts()) {
		echo "	<ul id=\"tags_related\">\n";
	while (have_posts()) {
		the_post(); update_post_caches($posts); ?>
  			<li> <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" target="_blank"><img src="<?php echo mmimg(get_the_ID()); ?>" height="150" alt="<?php the_title_attribute(); ?>" /><h4><?php the_title(); ?></h4><time><?php echo get_the_time("m-d"); ?></time></a></li>
<?php
    }
  }
  else {
	echo "<li></li>";
  }
  wp_reset_query(); 
echo "</ul>";
}
?></div>
		<?php 
			if( _hui('post_related_s') ){
				_moloader('mo_posts_related', false); 
				mo_posts_related(_hui('related_title'), _hui('post_related_n'));
			}
		?>
		<?php _the_ads($name='ads_post_03', $class='asb-post asb-post-03') ?>
		<?php comments_template('', true); ?>
	</div>
	</div>
	<?php get_sidebar() ?>
</section>

<?php get_footer(); 

