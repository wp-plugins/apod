<?php

/**
Plugin Name: APOD
Description: Each day a different image or photograph of our fascinating universe is featured, along with a brief explanation written by a professional astronomer. 
Version: 0.1
Author: Limeira Studio
Author URI: http://www.limeirastudio.com/
License: GPL2
Copyright: Limeira Studio
*/

function register_apod_widget()	{
	register_widget('APOD');
}
add_action('widgets_init', 'register_apod_widget');

class APOD extends WP_Widget {
		
	function __construct()	{
		$options = array(
            'description'   =>  'Astronomy Picture of the Day.',
            'name'          =>  'APOD'
        );
		
		parent::__construct('apod', '', $options);
	}
	
	public function form($instance)	{
		
		$defaults =  array(
			'title'	=> 'APOD',
			'img_title'=>'on',
			'feed'	=> 'http://apod.nasa.gov/apod.rss',
			'img_per_page'		=> '1'
		);

		$instance = wp_parse_args((array)$instance, $defaults);
		$title = ! empty($instance['title']) ? $instance['title'] : '';
		$img_title = ! empty($instance['img_title']) ? $instance['img_title'] : '';
		$feed = ! empty($instance['feed']) ? $instance['feed'] : '';
		$img_per_page = ! empty($instance['img_per_page']) ? $instance['img_per_page'] : '';
		?>
		<p>
			<label for="<?=$this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?=$this->get_field_id('title'); ?>" name="<?=$this->get_field_name('title'); ?>" type="text" value="<?=esc_attr($title); ?>">
		</p>
		<p>
		<input class="checkbox" type="checkbox" <?php checked($img_title, 'on'); ?> id="<?=$this->get_field_id('img_title'); ?>" name="<?=$this->get_field_name('img_title'); ?>" /> 
		<label for="<?=$this->get_field_id('img_title'); ?>"> Show Titles</label>
		</p>
		<?php
		
	}
	
	public function widget($args, $instance)	{

		$title = $instance['title'];
		$img_title = $instance['img_title'];
		$feed = $instance['feed'];
		$perpage = $instance['img_per_page'];

		$rss = fetch_feed('http://apod.nasa.gov/apod.rss');
		$maxitems = $rss->get_item_quantity($perpage); 
		$rss_items = $rss->get_items(0, 1);
		//print_r($rss_items);
		foreach($rss_items as $item)	{?>
			<?php if($img_title): ?>
			<h4><?=$item->get_title();?></h4>
			<?php endif; ?>
			<a href="http://apod.nasa.gov/apod/<?=$this->get_full_image($item->get_link());?>" target="_blank" title="<?=$item->get_title();?>"><img src="http://apod.nasa.gov/apod/<?=$this->get_full_image($item->get_link()); ?>" alt="<?=$item->get_title(); ?>" /></a>
			<?php
		}
		
		echo $args['before_widget'];?>
		
		<?php
		echo $args['after_widget'];
	}

	public function update($new_instance, $old_instance)	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['img_title'] = (!empty($new_instance['img_title'])) ? strip_tags($new_instance['img_title']) : '';
		$instance['feed'] = (isset($new_instance['feed'])) ? strip_tags($new_instance['feed']) : '';
		$instance['img_per_page'] = (!empty($new_instance['img_per_page'])) ? strip_tags($new_instance['img_per_page']) : '';
		
		return $instance;
	}
	
	private function get_full_image($url)	{

		$doc = new DOMDocument();
		@$doc->loadHTML(file_get_contents($url));
		
		$tags = $doc->getElementsByTagName('img');		
		foreach($tags as $tag)	{
			return $tag->getAttribute('src');
		}

	}

}

?>
