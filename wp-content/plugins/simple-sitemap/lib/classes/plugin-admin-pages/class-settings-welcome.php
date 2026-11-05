<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Plugin Home page and its safe starter-page actions.
 */
class Settings_Welcome {

	/** Documentation destination. */
	const DOCUMENTATION_URL = 'https://wpgoplugins.com/documentation/simple-sitemap/';

	/** Changelog destination. */
	const CHANGELOG_URL = 'https://wpgoplugins.com/documentation/simple-sitemap/changelog/';

	/** Public demo destination. */
	const DEMO_URL = 'https://demo.wpgoplugins.com/simple-sitemap/';

	/** Public support destination. */
	const SUPPORT_URL = 'https://wordpress.org/support/plugin/simple-sitemap/';

	/**
	 * Common root paths/directories.
	 *
	 * @var array<string, string>
	 */
	protected $module_roots;

	/**
	 * Plugin data.
	 *
	 * @var array
	 */
	protected $plugin_data;

	/**
	 * Custom plugin data.
	 *
	 * @var Constants
	 */
	protected $custom_plugin_data;

	/**
	 * Freemius discount upgrade URL.
	 *
	 * @var string
	 */
	protected $freemius_discount_upgrade_url;

	/**
	 * Welcome slug.
	 *
	 * @var string
	 */
	protected $welcome_slug;

	/**
	 * Main class constructor.
	 *
	 * @param array     $module_roots Root plugin path/dir.
	 * @param array     $plugin_data Plugin data.
	 * @param Constants $custom_plugin_data Custom plugin data.
	 */
	public function __construct( $module_roots, $plugin_data, $custom_plugin_data ) {
		$this->module_roots                  = $module_roots;
		$this->plugin_data                   = $plugin_data;
		$this->custom_plugin_data            = $custom_plugin_data;
		$this->freemius_discount_upgrade_url = $this->custom_plugin_data->freemius_discount_upgrade_url;
		$this->welcome_slug                  = $this->custom_plugin_data->settings_pages['welcome']['slug'];

		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_post_simple_sitemap_create_page', array( $this, 'create_sitemap_page' ) );
	}

	/**
	 * Add the Home submenu page.
	 */
	public function add_options_page() {
		$label = __( 'Home', 'simple-sitemap' );
		if ( 'sub' === $this->custom_plugin_data->menu_type ) {
			$label = '<span class="fs-submenu-item fs-sub wpgo-plugins">' . esc_html__( 'Home', 'simple-sitemap' ) . '</span>';
		}

		add_submenu_page(
			'simple-sitemap-menu',
			__( 'Simple Sitemap Home', 'simple-sitemap' ),
			$label,
			'manage_options',
			$this->welcome_slug,
			array( $this, 'render_sub_menu_form' )
		);
	}

	/**
	 * Create a safe draft page containing the selected sitemap block.
	 */
	public function create_sitemap_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to create a sitemap page.', 'simple-sitemap' ) );
		}

		check_admin_referer( 'simple_sitemap_create_page' );

		$layout          = isset( $_GET['layout'] ) ? sanitize_key( wp_unslash( $_GET['layout'] ) ) : 'posts-pages';
		$premium_layouts = array_column( $this->get_specialist_features(), 'layout' );
		if ( in_array( $layout, $premium_layouts, true ) && ! $this->custom_plugin_data->is_premium ) {
			wp_die( esc_html__( 'This starter is available in Simple Sitemap Pro.', 'simple-sitemap' ) );
		}

		if ( in_array( $layout, array( 'products', 'product-categories', 'sale-products', 'in-stock-products', 'featured-products' ), true ) && ! $this->has_woocommerce() ) {
			$redirect_url = add_query_arg( 'simple_sitemap_product_unavailable', '1', $this->custom_plugin_data->welcome_url );
			wp_safe_redirect( $redirect_url );
			exit;
		}

		$starter = $this->get_starter_page( $layout );
		$post_id = wp_insert_post(
			array(
				'post_title'   => $starter['title'],
				/*
				 * Dynamic block attributes can contain JSON strings. wp_insert_post()
				 * unslashes its input, so preserve the serialized block escapes here.
				 */
				'post_content' => wp_slash( $starter['content'] ),
				'post_status'  => 'draft',
				'post_type'    => 'page',
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			$redirect_url = add_query_arg( 'simple_sitemap_create_error', '1', $this->custom_plugin_data->welcome_url );
			wp_safe_redirect( $redirect_url );
			exit;
		}

		$edit_url = get_edit_post_link( $post_id, 'raw' );
		wp_safe_redirect( $edit_url ? $edit_url : admin_url( 'edit.php?post_type=page' ) );
		exit;
	}

	/**
	 * Display the Home page.
	 */
	public function render_sub_menu_form() {
		$tab_classes = SITEMAP_FREEMIUS_NAVIGATION === 'tabs' ? ' fs-section fs-full-size-wrapper' : ' no-tabs';
		$is_premium  = $this->custom_plugin_data->is_premium;
		$plugin_lbl  = $this->custom_plugin_data->main_menu_label;
		$image_root  = trailingslashit( $this->module_roots['uri'] ) . 'lib/assets/images/';

		$core_actions = array(
			array(
				'icon'        => 'admin-page',
				'title'       => __( 'Pages sitemap', 'simple-sitemap' ),
				'description' => __( 'Create an alphabetical sitemap of your published Pages.', 'simple-sitemap' ),
				'url'         => $this->get_create_page_url( 'pages' ),
				'action'      => __( 'Create draft', 'simple-sitemap' ),
			),
			array(
				'icon'        => 'admin-post',
				'title'       => __( 'Posts sitemap', 'simple-sitemap' ),
				'description' => __( 'Create a browsable list of your published blog posts.', 'simple-sitemap' ),
				'url'         => $this->get_create_page_url( 'posts' ),
				'action'      => __( 'Create draft', 'simple-sitemap' ),
			),
			array(
				'icon'        => 'clock',
				'title'       => __( 'Recent posts first', 'simple-sitemap' ),
				'description' => __( 'List blog posts in reverse date order so recent content appears first.', 'simple-sitemap' ),
				'url'         => $this->get_create_page_url( 'recent-posts' ),
				'action'      => __( 'Create draft', 'simple-sitemap' ),
			),
			array(
				'icon'        => 'excerpt-view',
				'title'       => __( 'Pages with summaries', 'simple-sitemap' ),
				'description' => __( 'Add excerpts beneath Page links to help visitors choose where to go.', 'simple-sitemap' ),
				'url'         => $this->get_create_page_url( 'page-summaries' ),
				'action'      => __( 'Create draft', 'simple-sitemap' ),
			),
			array(
				'icon'        => 'text-page',
				'title'       => __( 'Posts with summaries', 'simple-sitemap' ),
				'description' => __( 'Pair each post link with an excerpt for a more informative index.', 'simple-sitemap' ),
				'url'         => $this->get_create_page_url( 'post-summaries' ),
				'action'      => __( 'Create draft', 'simple-sitemap' ),
			),
			array(
				'icon'        => 'columns',
				'title'       => __( 'Tabbed posts and Pages', 'simple-sitemap' ),
				'description' => __( 'Keep posts and Pages easy to scan in responsive tabs.', 'simple-sitemap' ),
				'url'         => $this->get_create_page_url( 'posts-pages' ),
				'action'      => __( 'Create draft', 'simple-sitemap' ),
			),
			array(
				'icon'        => 'welcome-view-site',
				'title'       => __( 'Paginated posts', 'simple-sitemap' ),
				'description' => __( 'Split a long posts sitemap into smaller pages that are easier to browse.', 'simple-sitemap' ),
				'url'         => $this->get_create_page_url( 'paginated-posts' ),
				'action'      => __( 'Create draft', 'simple-sitemap' ),
			),
			array(
				'icon'        => 'editor-ul',
				'title'       => __( 'Combined content list', 'simple-sitemap' ),
				'description' => __( 'Show posts and Pages together in one continuous sitemap.', 'simple-sitemap' ),
				'url'         => $this->get_create_page_url( 'combined-list' ),
				'action'      => __( 'Create draft', 'simple-sitemap' ),
			),
			array(
				'icon'        => 'networking',
				'title'       => __( 'Posts grouped by category', 'simple-sitemap' ),
				'description' => __( 'Help visitors browse posts under familiar category headings.', 'simple-sitemap' ),
				'url'         => $this->get_create_page_url( 'grouped' ),
				'action'      => __( 'Create grouped draft', 'simple-sitemap' ),
			),
			array(
				'icon'        => 'list-view',
				'title'       => __( 'Child Page hierarchy', 'simple-sitemap' ),
				'description' => __( 'Create a structured sitemap beneath a parent Page you choose.', 'simple-sitemap' ),
				'url'         => $this->get_create_page_url( 'child-pages' ),
				'action'      => __( 'Create hierarchy draft', 'simple-sitemap' ),
			),
			array(
				'icon'        => 'admin-settings',
				'title'       => __( 'Shortcodes and defaults', 'simple-sitemap' ),
				'description' => __( 'Review established shortcode options and plugin-wide defaults.', 'simple-sitemap' ),
				'url'         => $this->custom_plugin_data->main_settings_url,
				'action'      => __( 'Open settings', 'simple-sitemap' ),
			),
		);

		foreach ( $core_actions as &$core_action ) {
			$core_action['edition'] = 'free';
		}
		unset( $core_action );

		$specialist_features = $this->get_specialist_features();

		$resource_links    = $this->get_resource_links( $is_premium );
		$feature_copy      = $this->get_feature_section_copy( $is_premium );
		$feature_actions   = $core_actions;
		$companion_plugins = Product_Links::companion_plugins( 'home-companions' );

		if ( $is_premium ) {
			foreach ( $specialist_features as $specialist_feature ) {
				$feature_actions[] = array(
					'icon'        => $specialist_feature['icon'],
					'title'       => $specialist_feature['title'],
					'description' => $specialist_feature['description'],
					'url'         => $this->get_create_page_url( $specialist_feature['layout'] ),
					'action'      => __( 'Create starter', 'simple-sitemap' ),
					'edition'     => 'pro',
				);
			}
		}
		?>
		<div class="wrap<?php echo esc_attr( $tab_classes ); ?> simple-sitemap-home">
			<div class="ss-home-shell">
				<h1 class="screen-reader-text"><?php echo esc_html( $plugin_lbl ); ?> <?php esc_html_e( 'Home', 'simple-sitemap' ); ?></h1>
				<?php if ( isset( $_GET['simple_sitemap_create_error'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
					<div class="notice notice-error"><p><?php esc_html_e( 'WordPress could not create the draft sitemap page. Please try again.', 'simple-sitemap' ); ?></p></div>
				<?php endif; ?>
				<?php if ( isset( $_GET['simple_sitemap_product_unavailable'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
					<div class="notice notice-warning"><p><?php esc_html_e( 'The WooCommerce Product Sitemap starter needs WooCommerce to be active.', 'simple-sitemap' ); ?></p></div>
				<?php endif; ?>

				<header class="ss-home-header">
					<div class="ss-home-brand">
						<img src="<?php echo esc_url( $image_root . 'simple-sitemap.svg' ); ?>" alt="" width="68" height="68" />
						<div>
							<div class="ss-home-title-row">
								<div class="ss-home-heading" aria-hidden="true"><?php echo esc_html( $plugin_lbl ); ?> <?php esc_html_e( 'Home', 'simple-sitemap' ); ?></div>
							</div>
							<div class="ss-home-meta">
								<a class="ss-home-version" href="<?php echo esc_url( self::CHANGELOG_URL ); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr( $is_premium ? __( 'Open Pro changelog', 'simple-sitemap' ) : __( 'Open changelog', 'simple-sitemap' ) ); ?>">
									v<?php echo esc_html( $this->plugin_data['Version'] ); ?>
									<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'simple-sitemap' ); ?></span>
								</a>
								<span class="ss-home-badge ss-home-badge--<?php echo $is_premium ? 'pro' : 'free'; ?>"><?php echo $is_premium ? esc_html__( 'Pro', 'simple-sitemap' ) : esc_html__( 'Free', 'simple-sitemap' ); ?></span>
							</div>
						</div>
					</div>
					<aside class="ss-home-feedback" aria-label="<?php esc_attr_e( 'Simple Sitemap feedback', 'simple-sitemap' ); ?>">
						<span class="ss-home-feedback__icon dashicons dashicons-format-chat" aria-hidden="true"></span>
						<span class="ss-home-feedback__copy">
							<strong><?php esc_html_e( 'Help us improve Simple Sitemap', 'simple-sitemap' ); ?></strong>
							<span><?php esc_html_e( 'Share your feedback and suggestions.', 'simple-sitemap' ); ?></span>
						</span>
						<a class="button ss-home-feedback__button" href="<?php echo esc_url( $this->get_feedback_url() ); ?>"><?php esc_html_e( 'Share feedback', 'simple-sitemap' ); ?></a>
					</aside>
				</header>

				<div class="ss-home-top-grid">
					<section class="ss-home-panel ss-home-start" aria-labelledby="ss-home-start-title">
						<h2 id="ss-home-start-title"><?php esc_html_e( 'Start here', 'simple-sitemap' ); ?></h2>
						<div class="ss-home-start__body">
							<div class="ss-home-start__media">
								<img src="<?php echo esc_url( $image_root . 'customise-sitemap.png' ); ?>" alt="<?php esc_attr_e( 'A populated Simple Sitemap block in the WordPress editor.', 'simple-sitemap' ); ?>" />
							</div>
							<div class="ss-home-start__content">
								<h3><?php esc_html_e( 'Create your sitemap page', 'simple-sitemap' ); ?></h3>
								<p><?php esc_html_e( 'We’ll add a ready-to-edit sitemap block to a new draft Page. Choose the content, review the preview, and publish only when you’re happy.', 'simple-sitemap' ); ?></p>
								<a class="button button-primary button-hero" href="<?php echo esc_url( $this->get_create_page_url( 'posts-pages' ) ); ?>"><?php esc_html_e( 'Create sitemap page', 'simple-sitemap' ); ?></a>
								<p class="ss-home-starter-hint"><?php esc_html_e( 'Or choose a ready-made sitemap starter below.', 'simple-sitemap' ); ?></p>
							</div>
						</div>
					</section>

					<aside class="ss-home-panel ss-home-links" aria-labelledby="ss-home-links-title">
						<h2 id="ss-home-links-title"><?php esc_html_e( 'Quick links', 'simple-sitemap' ); ?></h2>
						<nav aria-label="<?php echo esc_attr( $is_premium ? __( 'Simple Sitemap Pro resources', 'simple-sitemap' ) : __( 'Simple Sitemap resources', 'simple-sitemap' ) ); ?>">
							<?php foreach ( $resource_links as $resource_link ) : ?>
								<?php if ( $resource_link['external'] ) : ?>
									<a href="<?php echo esc_url( $resource_link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
								<?php else : ?>
									<a href="<?php echo esc_url( $resource_link['url'] ); ?>">
								<?php endif; ?>
									<span class="dashicons dashicons-<?php echo esc_attr( $resource_link['icon'] ); ?>" aria-hidden="true"></span>
									<span><?php echo esc_html( $resource_link['label'] ); ?></span>
									<span class="dashicons dashicons-<?php echo $resource_link['external'] ? 'external' : 'arrow-right-alt2'; ?>" aria-hidden="true"></span>
									<?php if ( $resource_link['external'] ) : ?>
										<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'simple-sitemap' ); ?></span>
									<?php endif; ?>
								</a>
							<?php endforeach; ?>
						</nav>
					</aside>
				</div>

				<section class="ss-home-section" aria-labelledby="ss-home-quick-start-title">
					<div class="ss-home-section-heading">
						<div><span class="ss-home-eyebrow"><?php esc_html_e( 'Quick start', 'simple-sitemap' ); ?></span><h2 id="ss-home-quick-start-title"><?php esc_html_e( 'From draft to published sitemap', 'simple-sitemap' ); ?></h2></div>
						<a href="<?php echo esc_url( self::DOCUMENTATION_URL ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $is_premium ? __( 'Read the Pro guide', 'simple-sitemap' ) : __( 'Read the full guide', 'simple-sitemap' ) ); ?> <span aria-hidden="true">↗</span><span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'simple-sitemap' ); ?></span></a>
					</div>
					<div class="ss-home-step-grid">
						<article class="ss-home-step-card"><span class="ss-home-step-number">1</span><div class="ss-home-step-image"><img src="<?php echo esc_url( $image_root . 'create-sitemap-page.png' ); ?>" alt="<?php esc_attr_e( 'A new WordPress Page titled Sitemap.', 'simple-sitemap' ); ?>" /></div><h3><?php esc_html_e( 'Create automatically', 'simple-sitemap' ); ?></h3><p><?php esc_html_e( 'Start with a safe draft and the right sitemap block already in place.', 'simple-sitemap' ); ?></p></article>
						<article class="ss-home-step-card"><span class="ss-home-step-number">2</span><div class="ss-home-step-image"><img src="<?php echo esc_url( $image_root . 'add-sitemap-block.png' ); ?>" alt="<?php esc_attr_e( 'Simple Sitemap blocks in the WordPress block inserter.', 'simple-sitemap' ); ?>" /></div><h3><?php esc_html_e( 'Choose what to show', 'simple-sitemap' ); ?></h3><p><?php esc_html_e( 'Pick the sitemap type that matches how your visitors browse.', 'simple-sitemap' ); ?></p></article>
						<article class="ss-home-step-card"><span class="ss-home-step-number">3</span><div class="ss-home-step-image"><img src="<?php echo esc_url( $image_root . 'customise-sitemap.png' ); ?>" alt="<?php esc_attr_e( 'Sitemap preview and block controls in the WordPress editor.', 'simple-sitemap' ); ?>" /></div><h3><?php esc_html_e( 'Review and publish', 'simple-sitemap' ); ?></h3><p><?php esc_html_e( 'Fine-tune the block, preview the public Page, then publish when ready.', 'simple-sitemap' ); ?></p></article>
					</div>
				</section>

				<section class="ss-home-section" aria-labelledby="ss-home-features-title">
					<div class="ss-home-section-heading">
						<div><span class="ss-home-eyebrow ss-home-eyebrow--free"><?php echo esc_html( $feature_copy['eyebrow'] ); ?></span><h2 id="ss-home-features-title"><?php echo esc_html( $feature_copy['title'] ); ?></h2><p><?php echo esc_html( $feature_copy['description'] ); ?></p></div>
					</div>
					<div class="ss-home-action-grid">
						<?php foreach ( $feature_actions as $feature_action ) : ?>
							<article class="ss-home-action-card">
								<?php if ( ! $is_premium ) : ?>
									<span class="ss-home-action-edition ss-home-action-edition--free"><?php esc_html_e( 'Free', 'simple-sitemap' ); ?></span>
								<?php endif; ?>
								<div class="ss-home-action-icon"><span class="dashicons dashicons-<?php echo esc_attr( $feature_action['icon'] ); ?>" aria-hidden="true"></span></div>
								<h3><?php echo esc_html( $feature_action['title'] ); ?></h3>
								<p><?php echo esc_html( $feature_action['description'] ); ?></p>
								<div class="ss-home-action-footer ss-home-action-footer--create"><a class="button" href="<?php echo esc_url( $feature_action['url'] ); ?>"><?php echo esc_html( $feature_action['action'] ); ?></a></div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>

				<?php if ( ! $is_premium ) : ?>
					<section class="ss-home-section ss-home-pro-showcase" aria-labelledby="ss-home-pro-title">
						<div class="ss-home-section-heading">
							<div><span class="ss-home-eyebrow ss-home-eyebrow--pro"><?php esc_html_e( 'Available in Pro', 'simple-sitemap' ); ?></span><h2 id="ss-home-pro-title"><?php esc_html_e( 'Do more with Pro', 'simple-sitemap' ); ?></h2><p><?php esc_html_e( 'Unlock specialist sitemap types and create a ready-to-edit starter Page for each one.', 'simple-sitemap' ); ?></p></div>
						</div>
						<div class="ss-home-pro-grid">
							<?php foreach ( $specialist_features as $specialist_feature ) : ?>
								<article class="ss-home-action-card ss-home-action-card--pro">
									<span class="ss-home-action-edition ss-home-action-edition--pro"><?php esc_html_e( 'Pro', 'simple-sitemap' ); ?></span>
									<div class="ss-home-action-icon ss-home-action-icon--pro"><span class="dashicons dashicons-<?php echo esc_attr( $specialist_feature['icon'] ); ?>" aria-hidden="true"></span></div>
									<h3><?php echo esc_html( $specialist_feature['title'] ); ?></h3>
									<p><?php echo esc_html( $specialist_feature['description'] ); ?></p>
									<div class="ss-home-action-footer ss-home-action-footer--pro ss-home-action-footer--create">
										<a class="button" href="<?php echo esc_url( $this->freemius_discount_upgrade_url ); ?>"><?php esc_html_e( 'Explore Pro', 'simple-sitemap' ); ?></a>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
					</section>

					<section class="ss-home-upgrade" aria-labelledby="ss-home-upgrade-title">
						<div class="ss-home-upgrade-intro"><span class="ss-home-upgrade-mark dashicons dashicons-star-filled" aria-hidden="true"></span><div><h2 id="ss-home-upgrade-title"><?php esc_html_e( 'Ready to do more with your sitemap?', 'simple-sitemap' ); ?></h2><p><?php esc_html_e( 'Move to Pro when your site needs specialist sitemaps or deeper presentation controls.', 'simple-sitemap' ); ?></p></div></div>
						<ul>
							<li><span class="dashicons dashicons-screenoptions" aria-hidden="true"></span><span><strong><?php esc_html_e( 'Custom content', 'simple-sitemap' ); ?></strong><?php esc_html_e( 'Post types and taxonomies', 'simple-sitemap' ); ?></span></li>
							<li><span class="dashicons dashicons-layout" aria-hidden="true"></span><span><strong><?php esc_html_e( 'Specialist layouts', 'simple-sitemap' ); ?></strong><?php esc_html_e( 'Purpose-built sitemap blocks', 'simple-sitemap' ); ?></span></li>
							<li><span class="dashicons dashicons-art" aria-hidden="true"></span><span><strong><?php esc_html_e( 'Deeper styling', 'simple-sitemap' ); ?></strong><?php esc_html_e( 'Colours, spacing, and more', 'simple-sitemap' ); ?></span></li>
						</ul>
						<div class="ss-home-upgrade-actions"><a class="button button-primary" href="<?php echo esc_url( $this->freemius_discount_upgrade_url ); ?>"><?php esc_html_e( 'Explore Pro', 'simple-sitemap' ); ?></a><a class="ss-home-upgrade-demo" href="<?php echo esc_url( $this->get_demo_url( 'pro-overview' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View Pro demo', 'simple-sitemap' ); ?> <span aria-hidden="true">↗</span><span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'simple-sitemap' ); ?></span></a></div>
					</section>
				<?php endif; ?>

				<section class="ss-home-section ss-home-companions" aria-labelledby="ss-home-companions-title">
					<div class="ss-home-section-heading">
						<div>
							<span class="ss-home-eyebrow"><?php esc_html_e( 'More from WPGO Plugins', 'simple-sitemap' ); ?></span>
							<h2 id="ss-home-companions-title"><?php esc_html_e( 'Turn useful content into clearer charts and tables', 'simple-sitemap' ); ?></h2>
							<p><?php esc_html_e( 'When a sitemap leads visitors to data-heavy content, these companion plugins help present the next step clearly inside WordPress.', 'simple-sitemap' ); ?></p>
						</div>
					</div>
					<div class="ss-home-companion-grid">
						<?php foreach ( $companion_plugins as $companion_plugin ) : ?>
							<article class="ss-home-companion-card">
								<div class="ss-home-companion-icon"><span class="dashicons dashicons-<?php echo esc_attr( $companion_plugin['icon'] ); ?>" aria-hidden="true"></span></div>
								<div class="ss-home-companion-copy">
									<h3><?php echo esc_html( $companion_plugin['title'] ); ?></h3>
									<p><?php echo esc_html( $companion_plugin['description'] ); ?></p>
								</div>
								<a class="button" href="<?php echo esc_url( $companion_plugin['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $companion_plugin['action'] ); ?> <span aria-hidden="true">↗</span><span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'simple-sitemap' ); ?></span></a>
							</article>
						<?php endforeach; ?>
					</div>
				</section>
			</div>
		</div>
		<?php
	}

	/**
	 * Build a nonce-protected starter action URL.
	 *
	 * @param string $layout Starter layout key.
	 * @return string
	 */
	private function get_create_page_url( $layout ) {
		$url = add_query_arg(
			array(
				'action' => 'simple_sitemap_create_page',
				'layout' => $layout,
			),
			admin_url( 'admin-post.php' )
		);

		return wp_nonce_url( $url, 'simple_sitemap_create_page' );
	}

	/**
	 * Return the Contact Us URL with the Freemius feedback fields prefilled.
	 *
	 * @return string
	 */
	private function get_feedback_url() {
		$url = add_query_arg( 'topic', 'feature_request', $this->custom_plugin_data->contact_us_url );

		return add_query_arg( 'summary', 'Simple Sitemap plugin feedback', $url );
	}

	/**
	 * Return specialist feature cards with their matching live-demo examples.
	 *
	 * @return array<int, array{icon: string, title: string, description: string, layout: string, demo_url: string}>
	 */
	private function get_specialist_features() {
		$features = array(
			array(
				'icon'        => 'search',
				'title'       => __( 'Searchable content directory', 'simple-sitemap' ),
				'description' => __( 'Help visitors filter the sitemap items currently displayed as they type.', 'simple-sitemap' ),
				'layout'      => 'searchable-content',
				'demo_url'    => $this->get_demo_url( 'searchable-content' ),
			),
			array(
				'icon'        => 'screenoptions',
				'title'       => __( 'Custom content types', 'simple-sitemap' ),
				'description' => __( 'Include portfolios, events, documentation, and other public post types.', 'simple-sitemap' ),
				'layout'      => 'custom-content',
				'demo_url'    => $this->get_demo_url( 'custom-content' ),
			),
			array(
				'icon'        => 'category',
				'title'       => __( 'Taxonomy Terms', 'simple-sitemap' ),
				'description' => __( 'Publish a clear hierarchy of categories, tags, or custom terms.', 'simple-sitemap' ),
				'layout'      => 'taxonomy-terms',
				'demo_url'    => $this->get_demo_url( 'taxonomy-terms' ),
			),
			array(
				'icon'        => 'menu',
				'title'       => __( 'Navigation Menu sitemap', 'simple-sitemap' ),
				'description' => __( 'Turn a curated WordPress menu into a visitor-friendly sitemap.', 'simple-sitemap' ),
				'layout'      => 'navigation-menu',
				'demo_url'    => $this->get_demo_url( 'navigation-menu' ),
			),
			array(
				'icon'        => 'archive',
				'title'       => __( 'Archive Links', 'simple-sitemap' ),
				'description' => __( 'List useful date, author, post-type, and taxonomy archives.', 'simple-sitemap' ),
				'layout'      => 'archive-links',
				'demo_url'    => $this->get_demo_url( 'archive-links' ),
			),
			array(
				'icon'        => 'cart',
				'title'       => __( 'WooCommerce Products', 'simple-sitemap' ),
				'description' => __( 'Create bounded product lists or group products by category.', 'simple-sitemap' ),
				'layout'      => 'products',
				'demo_url'    => $this->get_demo_url( 'products' ),
			),
			array(
				'icon'        => 'art',
				'title'       => __( 'Advanced layouts and styling', 'simple-sitemap' ),
				'description' => __( 'Use horizontal layouts, images, separators, colours, and spacing controls.', 'simple-sitemap' ),
				'layout'      => 'advanced-styling',
				'demo_url'    => $this->get_demo_url( 'advanced-styling' ),
			),
			array(
				'icon'        => 'format-image',
				'title'       => __( 'Visual content directory', 'simple-sitemap' ),
				'description' => __( 'Combine featured images, links, and excerpts in a richer content index.', 'simple-sitemap' ),
				'layout'      => 'visual-content',
				'demo_url'    => $this->get_demo_url( 'visual-content' ),
			),
			array(
				'icon'        => 'images-alt2',
				'title'       => __( 'Compact child Page directory', 'simple-sitemap' ),
				'description' => __( 'Add small thumbnails to a compact child Page hierarchy.', 'simple-sitemap' ),
				'layout'      => 'compact-child-pages',
				'demo_url'    => $this->get_demo_url( 'compact-child-pages' ),
			),
			array(
				'icon'        => 'chart-bar',
				'title'       => __( 'Categories with post counts', 'simple-sitemap' ),
				'description' => __( 'Show a category hierarchy with a useful content count for each term.', 'simple-sitemap' ),
				'layout'      => 'taxonomy-counts',
				'demo_url'    => $this->get_demo_url( 'taxonomy-counts' ),
			),
			array(
				'icon'        => 'calendar-alt',
				'title'       => __( 'Monthly post archive', 'simple-sitemap' ),
				'description' => __( 'Create a concise monthly archive with post counts.', 'simple-sitemap' ),
				'layout'      => 'monthly-archive',
				'demo_url'    => $this->get_demo_url( 'monthly-archive' ),
			),
			array(
				'icon'        => 'menu-alt3',
				'title'       => __( 'Horizontal menu sitemap', 'simple-sitemap' ),
				'description' => __( 'Present a selected navigation menu as a compact horizontal sitemap.', 'simple-sitemap' ),
				'layout'      => 'horizontal-menu',
				'demo_url'    => $this->get_demo_url( 'horizontal-menu' ),
			),
			array(
				'icon'        => 'category',
				'title'       => __( 'Products by category', 'simple-sitemap' ),
				'description' => __( 'Group WooCommerce products by category with images and prices.', 'simple-sitemap' ),
				'layout'      => 'product-categories',
				'demo_url'    => $this->get_demo_url( 'product-categories' ),
			),
			array(
				'icon'        => 'tag',
				'title'       => __( 'Products on sale', 'simple-sitemap' ),
				'description' => __( 'Build a focused product index containing current offers.', 'simple-sitemap' ),
				'layout'      => 'sale-products',
				'demo_url'    => $this->get_demo_url( 'sale-products' ),
			),
			array(
				'icon'        => 'yes-alt',
				'title'       => __( 'In-stock products', 'simple-sitemap' ),
				'description' => __( 'List products that are currently available to buy, with images and prices.', 'simple-sitemap' ),
				'layout'      => 'in-stock-products',
				'demo_url'    => $this->get_demo_url( 'in-stock-products' ),
			),
			array(
				'icon'        => 'star-filled',
				'title'       => __( 'Featured products', 'simple-sitemap' ),
				'description' => __( 'Showcase featured products with images, prices, and stock status.', 'simple-sitemap' ),
				'layout'      => 'featured-products',
				'demo_url'    => $this->get_demo_url( 'featured-products' ),
			),
		);

		return $features;
	}

	/**
	 * Whether WooCommerce can render the Product Sitemap block.
	 *
	 * @return bool
	 */
	private function has_woocommerce() {
		return post_type_exists( 'product' ) && function_exists( 'wc_get_product' );
	}

	/**
	 * Return a stable live-demo destination for a Home-page feature.
	 *
	 * @param string $feature Feature mapping key.
	 * @return string
	 */
	private function get_demo_url( $feature ) {
		$anchors = array(
			'searchable-content'  => 'content-sitemap-block',
			'custom-content'      => 'simple-sitemap-container-demo-project-directory',
			'taxonomy-terms'      => 'taxonomy-terms-block',
			'navigation-menu'     => 'navigation-menu-block',
			'archive-links'       => 'archive-links-block',
			'products'            => 'woocommerce-products-block',
			'advanced-styling'    => 'simple-sitemap-container-demo-horizontal-posts',
			'visual-content'      => 'simple-sitemap-container-demo-project-directory',
			'compact-child-pages' => 'child-pages-shortcode',
			'taxonomy-counts'     => 'taxonomy-terms-block',
			'monthly-archive'     => 'archive-links-block',
			'horizontal-menu'     => 'navigation-menu-block',
			'product-categories'  => 'woocommerce-products-block',
			'sale-products'       => 'woocommerce-products-block',
			'in-stock-products'   => 'woocommerce-products-block',
			'featured-products'   => 'woocommerce-products-block',
			'pro-overview'        => 'taxonomy-terms-block',
		);

		if ( ! isset( $anchors[ $feature ] ) ) {
			return self::DEMO_URL;
		}

		return Product_Links::tracked_url( self::DEMO_URL . '#' . $anchors[ $feature ], 'home-demo-' . $feature );
	}

	/**
	 * Return edition-appropriate Home resource links.
	 *
	 * @param bool $is_premium Whether the premium edition is active.
	 * @return array<int, array{icon: string, label: string, url: string, external: bool}>
	 */
	private function get_resource_links( $is_premium ) {
		return array(
			array(
				'icon'     => 'book',
				'label'    => $is_premium ? __( 'Pro documentation', 'simple-sitemap' ) : __( 'Documentation', 'simple-sitemap' ),
				'url'      => Product_Links::tracked_url( self::DOCUMENTATION_URL, 'home-documentation' ),
				'external' => true,
			),
			array(
				'icon'     => 'visibility',
				'label'    => $is_premium ? __( 'Pro live demo', 'simple-sitemap' ) : __( 'Live demo', 'simple-sitemap' ),
				'url'      => Product_Links::tracked_url( self::DEMO_URL, 'home-live-demo' ),
				'external' => true,
			),
			array(
				'icon'     => 'media-document',
				'label'    => $is_premium ? __( 'Pro changelog', 'simple-sitemap' ) : __( 'Changelog', 'simple-sitemap' ),
				'url'      => Product_Links::tracked_url( self::CHANGELOG_URL, 'home-changelog' ),
				'external' => true,
			),
			array(
				'icon'     => 'admin-settings',
				'label'    => __( 'Settings', 'simple-sitemap' ),
				'url'      => $this->custom_plugin_data->main_settings_url,
				'external' => false,
			),
			array(
				'icon'     => 'sos',
				'label'    => $is_premium ? __( 'Contact Pro support', 'simple-sitemap' ) : __( 'Support', 'simple-sitemap' ),
				'url'      => $is_premium ? $this->custom_plugin_data->contact_us_url : self::SUPPORT_URL,
				'external' => ! $is_premium,
			),
		);
	}

	/**
	 * Return edition-appropriate feature section copy.
	 *
	 * @param bool $is_premium Whether the premium edition is active.
	 * @return array{eyebrow: string, title: string, description: string}
	 */
	private function get_feature_section_copy( $is_premium ) {
		if ( $is_premium ) {
			return array(
				'eyebrow'     => __( 'Your sitemap toolkit', 'simple-sitemap' ),
				'title'       => __( 'Explore every sitemap feature', 'simple-sitemap' ),
				'description' => __( 'Each option creates a private starter draft, not a published Page. Configure and preview it in the editor when you are ready.', 'simple-sitemap' ),
			);
		}

		return array(
			'eyebrow'     => __( 'Included with Free', 'simple-sitemap' ),
			'title'       => __( 'What you can do with Free', 'simple-sitemap' ),
			'description' => __( 'Each sitemap option creates a private starter draft, not a published Page. The settings card opens the controls you need.', 'simple-sitemap' ),
		);
	}

	/**
	 * Return the title and serialized block content for one starter layout.
	 *
	 * @param string $layout Starter layout key.
	 * @return array{title: string, content: string}
	 */
	private function get_starter_page( $layout ) {
		$starter = $this->get_starter_definition( $layout );
		$content = serialize_block(
			array(
				'blockName'    => $starter['block_name'],
				'attrs'        => $starter['attributes'],
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);

		return array(
			'title'   => $starter['title'],
			'content' => $content,
		);
	}

	/**
	 * Return the block definition for one starter layout.
	 *
	 * @param string $layout Starter layout key.
	 * @return array{title: string, block_name: string, attributes: array<string, mixed>}
	 */
	private function get_starter_definition( $layout ) {
		$block_name = 'wpgoplugins/simple-sitemap-block';
		$attributes = array(
			'block_post_types' => wp_json_encode(
				array(
					array(
						'value' => 'post',
						'label' => __( 'Posts', 'simple-sitemap' ),
					),
					array(
						'value' => 'page',
						'label' => __( 'Pages', 'simple-sitemap' ),
					),
				)
			),
			'render_tab'       => true,
		);
		$title      = __( 'Sitemap', 'simple-sitemap' );

		switch ( $layout ) {
			case 'pages':
				$attributes = array(
					'block_post_types' => wp_json_encode(
						array(
							array(
								'value' => 'page',
								'label' => __( 'Pages', 'simple-sitemap' ),
							),
						)
					),
				);
				break;

			case 'posts':
				$title      = __( 'Posts Sitemap', 'simple-sitemap' );
				$attributes = array(
					'block_post_types' => wp_json_encode(
						array(
							array(
								'value' => 'post',
								'label' => __( 'Posts', 'simple-sitemap' ),
							),
						)
					),
				);
				break;

			case 'recent-posts':
				$title      = __( 'Recent Posts Sitemap', 'simple-sitemap' );
				$attributes = array(
					'block_post_types' => wp_json_encode(
						array(
							array(
								'value' => 'post',
								'label' => __( 'Posts', 'simple-sitemap' ),
							),
						)
					),
					'orderby'          => 'date',
					'order'            => 'desc',
				);
				break;

			case 'page-summaries':
				$title      = __( 'Page Directory', 'simple-sitemap' );
				$attributes = array(
					'block_post_types' => wp_json_encode(
						array(
							array(
								'value' => 'page',
								'label' => __( 'Pages', 'simple-sitemap' ),
							),
						)
					),
					'show_excerpt'     => true,
				);
				break;

			case 'post-summaries':
				$title      = __( 'Post Directory', 'simple-sitemap' );
				$attributes = array(
					'block_post_types' => wp_json_encode(
						array(
							array(
								'value' => 'post',
								'label' => __( 'Posts', 'simple-sitemap' ),
							),
						)
					),
					'show_excerpt'     => true,
				);
				break;

			case 'combined-list':
				$title                    = __( 'Content Directory', 'simple-sitemap' );
				$attributes['render_tab'] = false;
				break;

			case 'paginated-posts':
				$title      = __( 'Paginated Posts Sitemap', 'simple-sitemap' );
				$attributes = array(
					'block_post_types' => wp_json_encode(
						array(
							array(
								'value' => 'post',
								'label' => __( 'Posts', 'simple-sitemap' ),
							),
						)
					),
					'paginate'         => true,
					'page_size'        => 25,
				);
				break;

			case 'grouped':
				$title      = __( 'Posts by Category', 'simple-sitemap' );
				$block_name = 'wpgoplugins/simple-sitemap-group-block';
				$attributes = array();
				break;

			case 'child-pages':
				$title      = __( 'Child Pages Sitemap', 'simple-sitemap' );
				$block_name = 'wpgoplugins/simple-sitemap-child-pages-block';
				$attributes = array();
				break;

			case 'custom-content':
				$title      = __( 'Custom Content Sitemap', 'simple-sitemap' );
				$attributes = array();
				break;

			case 'searchable-content':
				$title      = __( 'Searchable Content Directory', 'simple-sitemap' );
				$attributes = array(
					'block_post_types' => $attributes['block_post_types'],
					'render_tab'       => false,
					'search'           => true,
				);
				break;

			case 'taxonomy-terms':
				$title      = __( 'Taxonomy Terms Sitemap', 'simple-sitemap' );
				$block_name = 'wpgoplugins/simple-sitemap-taxonomy-terms-block';
				$attributes = array();
				break;

			case 'navigation-menu':
				$title      = __( 'Navigation Menu Sitemap', 'simple-sitemap' );
				$block_name = 'wpgoplugins/simple-sitemap-navigation-menu-block';
				$attributes = array();
				break;

			case 'archive-links':
				$title      = __( 'Archive Links Sitemap', 'simple-sitemap' );
				$block_name = 'wpgoplugins/simple-sitemap-archive-links-block';
				$attributes = array();
				break;

			case 'products':
				$title      = __( 'Product Sitemap', 'simple-sitemap' );
				$block_name = 'wpgoplugins/simple-sitemap-product-block';
				$attributes = array();
				break;

			case 'advanced-styling':
				$title      = __( 'Styled Sitemap', 'simple-sitemap' );
				$attributes = array(
					'block_post_types'     => $attributes['block_post_types'],
					'horizontal'           => true,
					'horizontal_separator' => ' · ',
					'list_icon'            => false,
				);
				break;

			case 'visual-content':
				$title      = __( 'Visual Content Directory', 'simple-sitemap' );
				$attributes = array(
					'block_post_types' => $attributes['block_post_types'],
					'image'            => true,
					'image_size'       => 48,
					'show_excerpt'     => true,
				);
				break;

			case 'compact-child-pages':
				$title      = __( 'Compact Child Page Directory', 'simple-sitemap' );
				$block_name = 'wpgoplugins/simple-sitemap-child-pages-block';
				$attributes = array(
					'image'          => true,
					'image_size'     => 24,
					'spacing_preset' => 'compact',
				);
				break;

			case 'taxonomy-counts':
				$title      = __( 'Category Directory', 'simple-sitemap' );
				$block_name = 'wpgoplugins/simple-sitemap-taxonomy-terms-block';
				$attributes = array(
					'taxonomy'   => 'category',
					'show_count' => true,
				);
				break;

			case 'monthly-archive':
				$title      = __( 'Monthly Post Archive', 'simple-sitemap' );
				$block_name = 'wpgoplugins/simple-sitemap-archive-links-block';
				$attributes = array(
					'source'     => 'monthly',
					'limit'      => 12,
					'show_count' => true,
				);
				break;

			case 'horizontal-menu':
				$title      = __( 'Horizontal Menu Sitemap', 'simple-sitemap' );
				$block_name = 'wpgoplugins/simple-sitemap-navigation-menu-block';
				$attributes = array(
					'horizontal_separator' => ' · ',
					'list_icon'            => false,
				);
				break;

			case 'product-categories':
				$title      = __( 'Products by Category', 'simple-sitemap' );
				$block_name = 'wpgoplugins/simple-sitemap-product-block';
				$attributes = array(
					'layout'            => 'category',
					'show_image'        => true,
					'show_price'        => true,
					'show_stock_status' => true,
				);
				break;

			case 'sale-products':
				$title      = __( 'Products on Sale', 'simple-sitemap' );
				$block_name = 'wpgoplugins/simple-sitemap-product-block';
				$attributes = array(
					'on_sale_only'      => true,
					'show_image'        => true,
					'show_price'        => true,
					'show_stock_status' => true,
				);
				break;

			case 'in-stock-products':
				$title      = __( 'In-stock Products', 'simple-sitemap' );
				$block_name = 'wpgoplugins/simple-sitemap-product-block';
				$attributes = array(
					'stock_status'      => 'instock',
					'show_image'        => true,
					'show_price'        => true,
					'show_stock_status' => true,
				);
				break;

			case 'featured-products':
				$title      = __( 'Featured Products', 'simple-sitemap' );
				$block_name = 'wpgoplugins/simple-sitemap-product-block';
				$attributes = array(
					'featured_only'     => true,
					'show_image'        => true,
					'show_price'        => true,
					'show_stock_status' => true,
				);
				break;
		}

		return array(
			'title'      => $title,
			'block_name' => $block_name,
			'attributes' => $attributes,
		);
	}
} /* End class definition */
