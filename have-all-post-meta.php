<?php
/**
 * Plugin Name: Have All Post Meta
 * Plugin URI: https://github.com/haveboard/have-all-post-meta 
 * Description: Quick and dirty way to add wp panel to display all post meta for a post type.
 * Version: 1
 * License: MIT
 * Author: haveboard
 * Author URI: https://jonathanfinnegan.com
 *
 */
/*

*/
//
if(!class_exists('Have_All_Post_Meta_Plugin'))
{
	class Have_All_Post_Meta_Plugin
	{
		public function __construct()
		{		
			add_action('admin_menu', array(&$this,'have_all_post_meta_register_box'));
		}

		function have_all_post_meta_register_box() {
		
			add_meta_box('have_all_post_meta', "All Post Meta",array(&$this, 'have_all_post_meta_box'), 'post', 'normal', 'high');
			add_meta_box('have_all_post_meta', "All Post Meta",array(&$this, 'have_all_post_meta_box'), 'page', 'normal', 'high');

			$get_cpt_args = array(
				'public'   => true,
				'_builtin' => false
			);
			$post_types = get_post_types( $get_cpt_args, 'object' ); // use 'names' if you want to get only name of the post type.

			if ( $post_types ) {
				foreach ( $post_types as $cpt_key => $cpt_val ) {
				   // do something.
					add_meta_box('have_all_post_meta', "All Post Meta",array(&$this, 'have_all_post_meta_box'), $cpt_key, 'normal', 'high');
				}
			}


		}
		
		
		function have_all_post_meta_box() {
			global $post,$wpdb;
			
			?>
			
				<div class="inside">
					<style>
						ul li
						{
							margin-left:10px;
						}
						ul li ul li
						{
							margin-left:10px;
						}
					</style>

				<?php
				echo '<p>ID:' . $post->ID . '</p>';
				/*
				post_id
				meta_key
				meta_value
				*/
				 $querystr = "
					SELECT * 
					FROM $wpdb->postmeta
					WHERE post_id = $post->ID
				 ";

				$pageposts = $wpdb->get_results($querystr, OBJECT);
				echo "<ul>";
				foreach( $pageposts as $each_postmeta){
					if($each_postmeta->meta_key == "_edit_lock" || $each_postmeta->meta_key == "_edit_last"){
						//dont show _edit_lock or _edit_last
					}else{
					//check if data is serialized
					$isserialized = @unserialize($each_postmeta->meta_value);
						if(is_array($each_postmeta->meta_value)){
							echo "<li>Meta_key".$each_postmeta->meta_key;
								echo "<ul>";
								foreach($each_postmeta->meta_value as $meta_array){
									echo "<li>";
										echo $meta_array;
									echo "</li>";
								}
								echo "</ul>";
							echo "</li>";
						}else{
							if ($each_postmeta->meta_value === 'b:0;' || $isserialized  !== false) {
								if(is_array($isserialized)){
									echo "<li>Meta_key: ".$each_postmeta->meta_key;
										echo "<ul>";
											foreach($isserialized as $serialized_array){
												echo "<li>";
													echo "<pre>";
														print_r($serialized_array);
													echo "</pre>";
												echo "</li>";
										}
										echo "</ul>";
									echo "</li>";
								}else{
									echo "<li>Meta_key: ".$each_postmeta->meta_key." | ";
										echo "<pre>";
											print_r((array) $isserialized);
										echo "</pre>";
									echo "</li>";
								}   
							} else {
								echo "<li>Meta_key: ".$each_postmeta->meta_key." | ".$each_postmeta->meta_value."</li>";
							}
						}
					}
					/*
						echo "<pre>";
						print_r($each_postmeta);
						echo "</pre>";
						echo "<hr />";
					*/
				}
				echo "</ul>";
				?>
				</div>
				<?php
		}
		
		public static function activate()
		{

		}

		public static function deactivate()
		{

		}

	}
}

if(class_exists('Have_All_Post_Meta_Plugin'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('Have_All_Post_Meta_Plugin', 'activate'));
	register_deactivation_hook(__FILE__, array('Have_All_Post_Meta_Plugin', 'deactivate'));
	// instantiate the plugin class
	$have_all_post_meta_plugin = new Have_All_Post_Meta_Plugin();
}
