<?php
namespace Mandy\Posttypes;

class Careers extends \Mandy\Custom_Post_Type {
	/** @var string */
	static $name = 'career';

	/** @var string */
	static $placeholder_text = 'Enter job name here';

	/** @var array */
	static $labels = [
		'menu_name' => 'Careers',
		'singular'  => 'Job',
		'plural'    => 'Jobs',
		'all_items' => 'All Jobs',
	];


	/** @var array */
	static $options = [
		'has_archive'        => true,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_rest'       => true,
		'menu_position'      => 20,
		'menu_icon'         => 'dashicons-groups',
		'rewrite'           => [
			'slug'       => 'career',
			'with_front' => false,
		],
		'supports'           => [
			'title',
			'editor',
			'custom-fields',
			'thumbnail',
			'excerpt',
		],
	];

	/** @var array */
	static $taxonomies = [
		'job_category' => [
			'public'            => true,
			'heirarchical'      => true,
			'show_in_nav_menus' => true,
			'labels'            => [
				'name'              => 'Job Categories',
				'singular'          => 'Job Category',
				'plural'            => 'Job Categories',
				'menu_name'         => 'Job Categories',
				'add_new_item'      => 'Add Job Category',
				'not_found'         => 'No Job Categories Found',
				'parent_item'       => 'Parent Job Categories',
				'parent_item_colon' => 'Parent Job Categories:',
			],
			'rewrite'           => [
				'slug'          => 'job-category',
				'with_front'    => false,
				'hierarchical'  => true,
			],
		],
	];

	/** @var array */
	static $admin_columns = [
		'job_category' => 'Job Categories',
	];

	/** @var array */
	static $admin_columns_to_remove = ['wpseo-score', 'wpseo-score-readability'];

	/**
	 * Passed into acf_add_local_field_group() during the acf/init action.
	 * Leave the location paramter out, it will automatically be set for you!
	 *
	 * @var array
	 */
	static $field_group = [
		'key'                   => 'group_67a50115b13c8',
		'title'                 => 'Job Fields',
		'menu_order'            => 0,
		'position'              => 'acf_after_title',
		'style'                 => 'seamless',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'fields'                => [
			[
				'key'           => 'field_cpt_career_banner',
				'label'         => 'Banner Image',
				'name'          => 'career_hero_banner',
				'type'          => 'image',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'return_format' => 'url',
				'wrapper'       => ['width' => '100%'],
			],
			[
				'key'           => 'field_67a50117e345c',
				'label'         => 'Designation',
				'name'          => 'job_designation_field',
				'type'          => 'text',
				'wrapper'       => ['width' => '50%'],
			],
			[
				'key'           => 'field_67a50280e17ef',
				'label'         => 'Location',
				'name'          => 'job_location_field',
				'type'          => 'text',
				'wrapper'       => ['width' => '50%'],
			],
			[
				'key'           => 'field_67a502f26564',
				'label'         => 'Short Description',
				'name'          => 'job_short_description_field',
				'type'          => 'textarea',
				'wrapper'       => ['width' => '100%'],
			],
			[
				'key'           => 'field_67b39b61e57ea',
				'label'         => 'Related Jobs',
				'name'          => 'related_jobs',
				'type'          => 'relationship',
				'post_type'     => [ 0 => 'career'],
				'post_status'   => [ 0 => 'publish'],
				'filters'       => [ 0 => 'search'],
				'return_format' => 'object',
				'min'           => '',
				'max'           => 4,
				'elements'      => [ 0 => 'featured_image'],
			],
		],
		'location'              => [
			[
				[
					'param'     => 'post_type',
					'operator'  => '==',
					'value'     => 'career',
				],
			],
		],
	];

	/**
	 * on a single resource page
	 * that is gated
	 * and that is not a thank you page
	 * --- add the landing-page class
	 *
	 * @param array $classes
	 * @return array
	 */
	public static function body_class($classes) {
		if (in_array('single-career', $classes)) {
			$classes[] = 'career-landing-page';
		}

		return $classes;
	}

	static function initialize() {
		parent::initialize();
		add_filter('body_class', [__CLASS__, 'body_class']);
		add_action('admin_menu', [__CLASS__, 'add_career_submenu']);
	}

	/**
	 * Add a submenu under the "Career" menu
	 */
	public static function add_career_submenu() {
		add_submenu_page(
			'edit.php?post_type=career',
			'Options',
			'Options',
			'manage_options',
			'career-settings',
			[__CLASS__, 'career_settings_page']
		);

		// Register Career settings
		add_action('admin_init', [__CLASS__, 'register_career_settings']);
	}

	/**
	 * Display the settings page
	 */
	public static function career_settings_page() {
		?>
		<div class="wrap">
			<h1>Job Settings</h1>
			<form method="post" action="options.php">
				<?php
				// Output nonce, action, and option_page fields
				settings_fields('career_settings_group');
				do_settings_sections('career_settings');
				?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="related_job_popup">Career Popup Form Pattern</label></th>
						<td>
							<?php
							$selected_post_id = get_option('related_job_popup');
							$custom_pattern_posts = get_posts(array(
								'post_type' => 'wp_block',
								'posts_per_page' => -1,
							));
							if (!empty($custom_pattern_posts)) {
								echo '<select name="related_job_popup">';
								foreach ($custom_pattern_posts as $post) {
									echo '<option value="' . esc_attr($post->ID) . '" ' . selected($selected_post_id, $post->ID, false) . '>' . esc_html($post->post_title) . '</option>';
								}
								echo '</select>';
							}
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="related_job_heading">Related Job Heading</label></th>
						<td>
							<input type="text" id="related_job_heading" name="related_job_heading" value="<?php echo esc_attr(get_option('related_job_heading')); ?>" class="regular-text" /></br>
							<span class="description"><?php _e( 'Add the heading for related job' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="related_job_content">Related Job Description</label></th>
						<td>
							<textarea name="related_job_content" id="related_job_content" rows="5" cols="50"
						class="large-text"><?php echo esc_attr(get_option('related_job_content')); ?></textarea>
						<span class="description"><?php _e( 'Add the related job description here.' ); ?></span>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register Career settings
	 */
	public static function register_career_settings() {
		// Settings to register
		$settings = [
			'related_job_popup',
			'related_job_heading',
			'related_job_content'
		];

		foreach ($settings as $setting) {
			register_setting(
				'career_settings_group',
				$setting
			);
		}
	}

}

add_action('after_setup_theme', ['\\Mandy\\Posttypes\\Careers', 'initialize']);
