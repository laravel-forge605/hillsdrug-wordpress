<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Plugin 'New Features' settings page.
 */
class Settings_New_Features {

	/**
	 * Common root paths/directories.
	 *
	 * @var array<string, string>
	 */
	protected $module_roots;

	/**
	 * Custom plugin data.
	 *
	 * @var Constants
	 */
	protected $custom_plugin_data;

	/**
	 * Freemius upgrade URL.
	 *
	 * @var string
	 */
	protected $freemius_upgrade_url;

	/**
	 * Freemius discount upgrade URL.
	 *
	 * @var string
	 */
	protected $freemius_discount_upgrade_url;

	/**
	 * Utility data.
	 *
	 * @var Utility
	 */
	protected $utility;

	/**
	 * New plugin features.
	 *
	 * @var array
	 */
	protected $new_features_arr;

	/**
	 * New features slug.
	 *
	 * @var string
	 */
	protected $new_features_slug;

	/**
	 * Plugin data.
	 *
	 * @var array
	 */
	protected $plugin_data;

	/**
	 * Main class constructor.
	 *
	 * @param array $module_roots Root plugin path/dir.
	 * @param array $new_features_arr New plugin features.
	 * @param array $plugin_data Plugin data.
	 * @param Constants $custom_plugin_data Custom plugin data.
	 * @param Utility   $utility Utility data.
	 */
	public function __construct( $module_roots, $new_features_arr, $plugin_data, $custom_plugin_data, $utility ) {
		$this->module_roots                  = $module_roots;
		$this->custom_plugin_data            = $custom_plugin_data;
		$this->freemius_upgrade_url          = $this->custom_plugin_data->freemius_upgrade_url;
		$this->freemius_discount_upgrade_url = $this->custom_plugin_data->freemius_discount_upgrade_url;
		$this->utility                       = $utility;

		// $this->pro_attribute = '<span class="pro" title="Shortcode attribute available in ' . $this->custom_plugin_data->main_menu_label . ' Pro"><a href="' . $this->freemius_upgrade_url . '">PRO</a></span>';
		$this->new_features_arr = $new_features_arr;
		// $this->settings_slug = $this->custom_plugin_data->settings_pages['settings']['slug'];
		$this->new_features_slug = $this->custom_plugin_data->settings_pages['new-features']['slug'];
		// $this->welcome_slug = $this->custom_plugin_data->settings_pages['welcome']['slug'];
		$this->plugin_data = $plugin_data;

		add_action( 'admin_menu', array( &$this, 'add_options_page' ) );
	}

	/**
	 * Add menu page.
	 */
	public function add_options_page() {

		// @todo calc this in constants.php just once and pass it in.
		$opt_pfx             = $this->custom_plugin_data->db_option_prefix;
		$new_features_number = Upgrade::calc_new_features( $opt_pfx, $this->new_features_arr, $this->plugin_data );

		$title = 0 === $new_features_number ? __( 'New Features', 'simple-sitemap' ) : 'New Features <span class="update-plugins count-' . $new_features_number . '"><span class="plugin-count">' . $new_features_number . '</span></span>';

		$label = $title;
		if ( 'top' === $this->custom_plugin_data->menu_type || 'top-cpt' === $this->custom_plugin_data->menu_type ) {
			$label = $title;
		} elseif ( 'sub' === $this->custom_plugin_data->menu_type ) {
			$label = '<span class="fs-submenu-item fs-sub wpgo-plugins">' . $title . '</span>';
		}

		$hook = add_submenu_page(
			'simple-sitemap-menu',
			'New Features',
			$label,
			'manage_options',
			$this->new_features_slug,
			array( &$this, 'render_sub_menu_form' )
		);
	}

	/**
	 * Display the sub menu page.
	 */
	public function render_sub_menu_form() {

		$tab_classes = SITEMAP_FREEMIUS_NAVIGATION === 'tabs' ? ' fs-section fs-full-size-wrapper' : ' no-tabs';
		$is_premium  = $this->custom_plugin_data->is_premium;
		$opt_pfx     = $this->custom_plugin_data->db_option_prefix;
		?>
		<div class="wrap welcome new-features<?php echo esc_attr( $tab_classes ); ?>">
		<div class="wpgo-settings-inner">
			<h1 class="heading"><?php esc_html_e( 'What’s new in Simple Sitemap', 'simple-sitemap' ); ?></h1>
			<p style="font-size:18px;"><?php esc_html_e( 'See the features and improvements added in recent releases. For the complete release history, open the changelog. If you have an idea or a problem to solve, send it through Contact Us.', 'simple-sitemap' ); ?></p>
		<?php
		echo wp_kses_post( $this->render_new_features( $is_premium ) );
		?>
		</div>
	</div>
		<?php
	}

	/**
	 * Render recent feature cards.
	 *
	 * @param bool $is_premium Whether the premium edition is active.
	 * @return string
	 */
	private function render_new_features( $is_premium ) {
		ob_start();
		?>
		<ul class="wpgo-settings-grid-container">
			<?php foreach ( $this->new_features_arr as $new_feature ) : ?>
				<?php
				if ( ! is_object( $new_feature ) || empty( $new_feature->title ) ) {
					continue;
				}

				$type         = isset( $new_feature->type ) ? (string) $new_feature->type : '';
				$ribbon_text  = array(
					'fix'    => __( 'Fixed', 'simple-sitemap' ),
					'new'    => __( 'New', 'simple-sitemap' ),
					'update' => __( 'Updated', 'simple-sitemap' ),
				)[ $type ] ?? '';
				$license      = isset( $new_feature->license ) ? (string) $new_feature->license : 'free';
				$is_pro       = 'pro' === $license;
				$version      = isset( $new_feature->version ) ? (string) $new_feature->version : '';
				$is_current   = $version === $this->plugin_data['Version'] || 'latest' === $version;
				$learn_more   = isset( $new_feature->learn_more_url ) ? (string) $new_feature->learn_more_url : '';
				$show_upgrade = $is_pro && ! $is_premium;
				$banner       = isset( $new_feature->banner_url ) ? sanitize_file_name( $new_feature->banner_url ) : '';
				?>
				<li>
					<div class="wpgo-settings-card">
						<?php if ( $is_current && '' !== $ribbon_text ) : ?>
							<div class="ribbon-wrapper"><div class="ribbon <?php echo esc_attr( $type ); ?>"><?php echo esc_html( $ribbon_text ); ?></div></div>
						<?php endif; ?>
						<div class="image-wrapper">
							<?php if ( ! $is_premium ) : ?>
								<div class="<?php echo $is_pro ? 'pro-only' : 'free-only'; ?>">
									<?php if ( $is_pro ) : ?>
										<a href="<?php echo esc_url( $this->freemius_discount_upgrade_url ); ?>"><?php esc_html_e( 'Pro', 'simple-sitemap' ); ?></a>
									<?php else : ?>
										<?php esc_html_e( 'Free', 'simple-sitemap' ); ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ( '' !== $banner ) : ?>
								<img src="<?php echo esc_url( $this->module_roots['uri'] . '/lib/assets/images/new-features/' . $banner ); ?>" alt="">
							<?php endif; ?>
						</div>
						<div class="details">
							<?php /* translators: %s: plugin version number. */ ?>
							<div><?php echo esc_html( sprintf( __( 'Version: %s', 'simple-sitemap' ), $version ) ); ?></div>
							<div><?php echo esc_html( isset( $new_feature->date ) ? (string) $new_feature->date : '' ); ?></div>
						</div>
						<div class="card-content">
							<h2><?php echo esc_html( (string) $new_feature->title ); ?></h2>
							<?php echo wp_kses_post( isset( $new_feature->description ) ? (string) $new_feature->description : '' ); ?>
						</div>
						<?php if ( '' !== $learn_more || $show_upgrade ) : ?>
							<div class="permalink">
								<?php if ( '' !== $learn_more ) : ?>
									<a class="button left" href="<?php echo esc_url( Product_Links::tracked_url( $learn_more, 'new-features-learn-more' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Learn more', 'simple-sitemap' ); ?></a>
								<?php endif; ?>
								<?php if ( $show_upgrade ) : ?>
									<a class="button right" href="<?php echo esc_url( $this->freemius_discount_upgrade_url ); ?>"><?php esc_html_e( 'Upgrade', 'simple-sitemap' ); ?></a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
		return (string) ob_get_clean();
	}
} /* End class definition */
