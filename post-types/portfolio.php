<?php
namespace Mandy\Posttypes;

class Portfolio extends \Mandy\Custom_Post_Type {
	/** @var string */
	static $name = 'portfolio';

	/** @var string */
	static $placeholder_text = 'Enter project name here';

	/** @var array */
	static $labels = [
		'menu_name' => 'Portfolios',
		'singular'  => 'Portfolio',
		'plural'    => 'Portfolios',
		'all_items' => 'All Portfolios',
	];


	/** @var array */
	static $options = [
		'has_archive'        => true,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_rest'       => true,
		'menu_position'      => 20,
		'menu_icon'         => 'dashicons-portfolio',
		'rewrite'           => [
			'slug'       => 'project',
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
		'portfolio_category' => [
			'public'            => true,
			'heirarchical'      => true,
			'show_in_nav_menus' => true,
			'labels'            => [
				'name'              => 'Categories',
				'singular'          => 'Category',
				'plural'            => 'Categories',
				'menu_name'         => 'Categories',
				'add_new_item'      => 'Add Category',
				'not_found'         => 'No Categories Found',
				'parent_item'       => 'Parent Categories',
				'parent_item_colon' => 'Parent Categories:',
			],
			'rewrite'           => [
				'slug'          => 'project-category',
				'with_front'    => false,
				'hierarchical'  => true,
			],
		],
	];

	/** @var array */
	static $admin_columns = [
		'portfolio_category' => 'Categories',
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
		'key'                   => 'group_67a50115b13c888',
		'title'                 => 'Portfolio Fields',
		'menu_order'            => 0,
		'position'              => 'acf_after_title',
		'style'                 => 'seamless',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'fields'                => [
			[
				'key'           => 'field_67a50117e345c88',
				'label'         => 'Project Type',
				'name'          => 'portfolio_project_type',
				'type'          => 'text',
				'wrapper'       => ['width' => '100%'],
			],
			[
				'key'           => 'field_67b39b61e57eaee',
				'label'         => 'Related Projects',
				'name'          => 'related_projects',
				'type'          => 'relationship',
				'post_type'     => [ 0 => 'portfolio'],
				'post_status'   => [ 0 => 'publish'],
				'filters'       => [ 0 => 'search'],
				'return_format' => 'object',
				'min'           => '',
				'max'           => 3,
				'elements'      => [ 0 => 'featured_image'],
			],
		],
		'location'              => [
			[
				[
					'param'     => 'post_type',
					'operator'  => '==',
					'value'     => 'portfolio',
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
		if (in_array('single-portfolio', $classes)) {
			$classes[] = 'portfolio-landing-page';
		}

		return $classes;
	}

	static function initialize() {
		parent::initialize();
		add_filter('body_class', [__CLASS__, 'body_class']);
		add_action('admin_menu', [__CLASS__, 'add_portfolio_submenu']);
	}

	/**
	 * Add a submenu under the "Portfolio" menu
	 */
	public static function add_portfolio_submenu() {
		add_submenu_page(
			'edit.php?post_type=portfolio',
			'Options',
			'Options',
			'manage_options',
			'portfolio-settings',
			[__CLASS__, 'portfolio_settings_page']
		);

		// Register Portfolio settings
		add_action('admin_init', [__CLASS__, 'register_portfolio_settings']);
	}

	/**
	 * Display the settings page
	 */
	public static function portfolio_settings_page() {
		?>
		<div class="wrap">
			<h1>Portfolio Settings</h1>
			<form method="post" action="options.php">
				<?php
				// Output nonce, action, and option_page fields
				settings_fields('portfolio_settings_group');
				do_settings_sections('portfolio_settings');
				?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="portfolio_archive_banner">Project Archive Banner</label></th>
						<td>
							<?php
							$selected_post_id = get_option('portfolio_archive_banner');
							$custom_pattern_posts = get_posts(array(
								'post_type' => 'wp_block',
								'posts_per_page' => -1,
							));
							if (!empty($custom_pattern_posts)) {
								echo '<select name="portfolio_archive_banner">';
								foreach ($custom_pattern_posts as $post) {
									echo '<option value="' . esc_attr($post->ID) . '" ' . selected($selected_post_id, $post->ID, false) . '>' . esc_html($post->post_title) . '</option>';
								}
								echo '</select>';
							}
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="related_project_heading">Related Project Heading</label></th>
						<td>
							<input type="text" id="related_project_heading" name="related_project_heading" value="<?php echo esc_attr(get_option('related_project_heading')); ?>" class="regular-text" /></br>
							<span class="description"><?php _e( 'Add the heading for related project' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="related_project_content">Related Project Description</label></th>
						<td>
							<textarea name="related_project_content" id="related_project_content" rows="5" cols="50"
						class="large-text"><?php echo esc_attr(get_option('related_project_content')); ?></textarea>
						<span class="description"><?php _e( 'Add the related project description here.' ); ?></span>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register Portfolio settings
	 */
	public static function register_portfolio_settings() {
		// Settings to register
		$settings = [
			'portfolio_archive_banner',
			'related_project_heading',
			'related_project_content'
		];

		foreach ($settings as $setting) {
			register_setting(
				'portfolio_settings_group',
				$setting
			);
		}
	}

}

add_action('after_setup_theme', ['\\Mandy\\Posttypes\\Portfolio', 'initialize']);
