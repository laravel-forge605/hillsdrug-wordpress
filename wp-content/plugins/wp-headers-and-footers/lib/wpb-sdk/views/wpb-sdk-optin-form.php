<?php
/**
 * Shared WPBrigade SDK opt-in splash (modern default for all bundled products).
 *
 * Expects variables set by wpb_sdk_render_optin_form() before include.
 *
 * @package wpbrigade_sdk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wpb_sdk_optin_user = wp_get_current_user();
$wpb_sdk_optin_name = empty( $wpb_sdk_optin_user->user_firstname )
	? ( ! empty( $wpb_sdk_optin_user->display_name ) ? $wpb_sdk_optin_user->display_name : 'there' )
	: $wpb_sdk_optin_user->user_firstname;

$wpb_product_title = ! empty( $wpb_sdk_product_name ) ? $wpb_sdk_product_name : 'Plugin';
?>

<style>
    .wpb-sdk-optin-container {
        /* Primary colors */
        --wpb-sdk-primary: #3458EA;
        --wpb-sdk-primary-dark: #3C50E0;

        /* Text colors */
        --wpb-sdk-text: #191A1E;
        --wpb-sdk-heading: #07003B;
        --wpb-sdk-muted: #515E81;

        /* Background colors */
        --wpb-sdk-page-bg: #F2F5F6;
        --wpb-sdk-white: #FFFFFF;
        --wpb-sdk-light-blue: #E9EFFF;
        --wpb-sdk-primary-light: #5A78FF0A;

        /* Border colors */
        --wpb-sdk-border: #515E8138;
        --wpb-sdk-border-light: #515E8133;

        /* Shadow colors */
        --wpb-sdk-card-shadow: rgba(7, 0, 59, 0.08);
        --wpb-sdk-primary-shadow: rgba(60, 80, 224, 0.12);
        --wpb-sdk-primary-shadow-strong: rgba(60, 80, 224, 0.25);

        /* Spinner colors */
        --wpb-sdk-spinner-light: rgba(255, 255, 255, 0.3);
        --wpb-sdk-spinner-dark: rgba(81, 94, 129, 0.3);
    }

    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap");

    #wpwrap {
        background-color: var(--wpb-sdk-page-bg);
    }

    #wpcontent {
        padding: 0 !important;
    }

    #wpbody {
        padding-right: 0;
    }

    /* Keep admin notices from competing with the splash / jumping on accordion */
    #wpbody-content > .notice,
    #wpbody-content > .updated,
    #wpbody-content > .error,
    #wpbody-content > .update-nag {
        display: none !important;
    }

    .wpb-sdk-optin-container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 40px 15px;
        font-family: Poppins, sans-serif;
        box-sizing: border-box;
    }

    .wpb-sdk-optin-card {
        background: var(--wpb-sdk-white);
        border-radius: 16px;
        box-shadow: 0 12px 36px var(--wpb-sdk-card-shadow);
        max-width:800px;
        width: 100%;
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
    }

    /* Top subtle wave backdrop */
    .wpb-sdk-top-curve {
        position: absolute;
        top: 0;
        right: 0;
        width: 240px;
        height: 120px;
        pointer-events: none;
        z-index: 0;
    }

    /* Bottom subtle wave backdrop */
    .wpb-sdk-bottom-curve {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 200px;
        height: 110px;
        pointer-events: none;
        z-index: 0;
    }

    /* Card Header */
    .wpb-sdk-card-header {
        position: relative;
        z-index: 1;
        padding: 30px 35px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid var(--wpb-sdk-border);
    }

	.wpb-sdk-card-header img{
		max-height: 100px;
		width: auto;
		height: auto;
		object-fit: contain;
	}


    .wpb-sdk-logo-fallback {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 20px;
        font-weight: 700;
        color: var(--wpb-sdk-heading);
    }

    .wpb-sdk-logo-fallback svg {
        width: 28px;
        height: 28px;
        color: var(--wpb-sdk-primary-dark);
    }

    /* Card Body Split */
    .wpb-sdk-card-body {
        position: relative;
        z-index: 1;
        padding: 30px 35px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .wpb-sdk-body-left {
        flex: 1;
    }

    .wpb-sdk-welcome-title {
        margin: 0 0 11px;
        font-size: 34px;
        font-weight: 600;
        color: var(--wpb-sdk-text);
        line-height: 1.3;
    }

    .wpb-sdk-welcome-title span {
        color: var(--wpb-sdk-primary);
    }

    .wpb-sdk-accent-bar {
        width: 80px;
        height: 2px;
        background: var(--wpb-sdk-primary);
        margin-bottom: 30px;
    }

    .wpb-sdk-greeting {
        margin: 0 0 13px;
        font-size: 18px;
        font-weight: 400;
        color: var(--wpb-sdk-text);
        line-height: 1.3;
    }
	.wpb-sdk-greeting strong{
		font-weight: 500;
	}

    .wpb-sdk-optin-text {
        margin: 0 0 16px;
        font-size: 16px;
        font-weight: 400;
        color: var(--wpb-sdk-text);
        line-height: 1.6;
    }

    .wpb-sdk-optin-text .wpb-highlight {
        color: var(--wpb-sdk-primary);
        font-weight: 500;
        display: block;
    }

    .wpb-sdk-help-line {
        margin: 0 0 16px;
        font-size: 18px;
        line-height: 1.3;
        color: var(--wpb-sdk-text);
    }

    .wpb-sdk-help-line strong {
        color: var(--wpb-sdk-primary);
        font-weight: 600;
    }

    /* Action Buttons */
    .wpb-sdk-action-row {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 30px;
		flex-wrap: wrap;
    }

    .wpb-sdk-btn-allow {
        background: var(--wpb-sdk-primary);
        color: var(--wpb-sdk-white) !important;
        border: 1px solid var(--wpb-sdk-primary);
        border-radius: 4px;
        padding: 13px 28px;
        font-family: Poppins, sans-serif;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s;
        box-shadow: none;
        text-decoration: none;
        text-align: center;
        line-height: 24px;
        text-transform: capitalize;
    }

    .wpb-sdk-btn-allow:hover,
    .wpb-sdk-btn-allow:focus {
        background: var(--wpb-sdk-white);
        color: var(--wpb-sdk-primary-dark) !important;
        border-color: var(--wpb-sdk-primary-dark);
    }

    .wpb-sdk-btn-allow:hover .wpb-sdk-spinner,
    .wpb-sdk-btn-allow:focus .wpb-sdk-spinner {
        border-color: var(--wpb-sdk-primary-shadow-strong);
        border-top-color: var(--wpb-sdk-primary-dark);
    }

    .wpb-sdk-btn-allow:disabled,
    .wpb-sdk-btn-skip:disabled {
        opacity: 0.7;
        cursor: wait;
    }

    .wpb-sdk-btn-skip {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        color: #333 !important;
        font-family: Poppins, sans-serif;
        font-size: 18px;
        font-weight: 500;
        line-height: 1.3;
        text-decoration: underline;
        text-decoration-style: dashed;
        text-underline-offset: 4px;
        cursor: pointer;
        box-shadow: none !important;
        display: inline-flex;
        align-items: center;
    }

    .wpb-sdk-btn-skip:hover {
        color: var(--wpb-sdk-muted) !important;
        text-decoration-style: solid;
    }

    .wpb-sdk-spinner {
        width: 16px;
        height: 16px;
        border: 2px solid var(--wpb-sdk-spinner-light);
        border-top-color: var(--wpb-sdk-white);
        border-radius: 50%;
        animation: wpb-spin 0.7s linear infinite;
        display: inline-block;
    }

    .wpb-sdk-spinner-dark {
        width: 14px;
        height: 14px;
        border: 2px solid var(--wpb-sdk-spinner-dark);
        border-top-color: var(--wpb-sdk-muted);
        border-radius: 50%;
        animation: wpb-spin 0.7s linear infinite;
        display: inline-block;
    }

    @keyframes wpb-spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Illustration Right */
    .wpb-sdk-body-right {
        flex: 0 0 240px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .wpb-sdk-shield-graphic {
        width: 100%;
        max-width: 140px;
        height: auto;
        display: block;
        object-fit: contain;
    }

    .wpb-sdk-permissions-section:has(.is-closed) {
        padding-bottom: 35px;
    }

    .wpb-sdk-permissions-toggle {
        background: none;
        border: none;
        padding: 17px 35px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 17px;
        color: var(--wpb-sdk-primary);
        font-family: Poppins, sans-serif;
        font-size: 20px;
        font-weight: 500;
        cursor: pointer;
        outline: none !important;
        background: var(--wpb-sdk-primary-light);
        border-top: 1px solid var(--wpb-sdk-border-light);
        border-bottom: 1px solid var(--wpb-sdk-border-light);
		text-transform: capitalize;
	}

    .wpb-sdk-permissions-toggle.is-closed {
        margin-bottom: 0;
    }

    .wpb-sdk-permissions-toggle:hover {
        color: var(--wpb-sdk-text);
    }

    .wpb-sdk-chevron {
        width: 16px;
        height: 16px;
        transition: transform 0.25s ease;
    }

    .wpb-sdk-permissions-toggle:not(.is-closed) svg {
        transform: rotate(180deg);
    }

    .wpb-sdk-perm-dropdown {
        display: none;
        padding: 30px 35px;
    }

    /* 2x2 Grid */
    .wpb-sdk-perm-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .wpb-sdk-perm-item {
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }

    .wpb-sdk-perm-icon {
        width: 55px;
        height: 55px;
        border-radius: 8px;
        background: var(--wpb-sdk-light-blue);
        color: var(--wpb-sdk-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .wpb-sdk-perm-icon svg {
        width: 24px;
        height: 24px;
    }

    .wpb-sdk-perm-content h4 {
        margin: 0 0 6px;
        font-size: 18px;
        font-weight: 600;
        color: var(--wpb-sdk-heading);
        line-height: 1.5;
    }

    .wpb-sdk-perm-content p {
        margin: 0;
        font-size: 14px;
        line-height: 1.5;
        color: var(--wpb-sdk-muted);
    }

    /* Bottom Flow Graphic */
    .wpb-sdk-bottom-flow {
        position: relative;
        margin-top: -70px;
        height: 56px;
        width: 100%;
    }

    .wpb-sdk-flow-badge-wrap-lock {
        position: absolute;
        left: 65px;
        bottom: 4px;
        z-index: 2;
    }

    .wpb-sdk-permissions-bottom-object {
        width: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: -72px;
        margin-bottom: -32px;
    }

    .wpb-sdk-flow-badge-wrap-check {
        position: absolute;
        left: 215px;
        bottom: 0;
        z-index: 2;
    }

    .wpb-sdk-flow-lock {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: var(--wpb-sdk-light-blue);
        color: var(--wpb-sdk-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px var(--wpb-sdk-primary-shadow);
    }

    .wpb-sdk-flow-check {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--wpb-sdk-primary-dark);
        color: var(--wpb-sdk-white);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px var(--wpb-sdk-primary-shadow-strong);
    }

    .wpb-sdk-flow-curve-svg {
        position: absolute;
        left: 80px;
        bottom: 12px;
        width: 155px;
        height: 48px;
        pointer-events: none;
        z-index: 1;
    }

    .wpb-btn-arrow {
        line-height: 15px;
    }

    /* Responsive */
    @media (max-width: 620px) {
        .wpb-sdk-card-body {
            flex-direction: column;
            align-items: flex-start;
        }

        .wpb-sdk-body-left {
            max-width: 100%;
        }

        .wpb-sdk-body-right {
            align-self: center;
            margin-top: 12px;
            flex: 1;
        }

        .wpb-sdk-action-row {
            flex-wrap: wrap;
        }

        .wpb-sdk-perm-grid {
            grid-template-columns: 1fr;
        }

        .wpb-sdk-permissions-bottom-object {
            margin-block: 12px -30px;
            width: 100%;
        }

        .wpb-sdk-perm-dropdown,
        .wpb-sdk-card-header,
        .wpb-sdk-card-body {
            padding: 20px 15px;
        }

        .wpb-sdk-welcome-title {
            font-size: 24px;
        }

        .wpb-sdk-perm-content h4 {
            font-size: 16px;
        }

        .wpb-sdk-btn-skip,
        .wpb-sdk-optin-text {
            font-size: 16px;
        }

        .wpb-sdk-action-row {
            flex-direction: column;
            align-items: center;
        }

        .wpb-sdk-perm-icon {
            width: 42px;
            height: 42px;
            border-radius: 6px;
        }

        .wpb-sdk-permissions-toggle {
            font-size: 16px;
            padding: 12px 15px;
        }

        .wpb-sdk-perm-icon svg {
            width: 20px;
            height: 20px;
        }
    }
</style>

<div class="wpb-sdk-optin-container">
	<div class="wpb-sdk-optin-card">
		<div class="wpb-sdk-card-header">
			<?php if ( ! empty( $wpb_sdk_logo_url ) ) : ?>
				<img
					class="wpb-sdk-logo-img"
					src="<?php echo esc_url( $wpb_sdk_logo_url ); ?>"
					alt="<?php echo esc_attr( $wpb_product_title ); ?>"
				/>
			<?php else : ?>
				<div class="wpb-sdk-logo-fallback">
					<svg viewBox="0 0 24 24" fill="currentColor">
						<path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
					</svg>
					<span><?php echo esc_html( $wpb_product_title ); ?></span>
				</div>
			<?php endif; ?>
		</div>

		<!-- Card Body (Text + Illustration) -->
		<div class="wpb-sdk-card-body">
			<div class="wpb-sdk-body-left">
				<h1 class="wpb-sdk-welcome-title">
					<?php
					printf(
						/* translators: %s: product name */
						esc_html__( 'Welcome To %s', 'wpbrigade-sdk' ),
						'<span>' . esc_html( $wpb_product_title ) . '</span>'
					);
					?>
				</h1>
				<div class="wpb-sdk-accent-bar"></div>

				<p class="wpb-sdk-greeting">
					<?php
					printf(
						/* translators: %s: user name */
						esc_html__( 'Hey %s,', 'wpbrigade-sdk' ),
						'<strong>' . esc_html( $wpb_sdk_optin_name ) . '</strong>'
					);
					?>
				</p>

				<p class="wpb-sdk-optin-text">
					<?php
					printf(
						/* translators: 1: product name, 2: product name */
						esc_html__( 'If you opt-in some data about your installation of %1$s will be sent to WPBrigade.com (This doesn\'t include stats) and You will receive new feature updates, security notifications etc. %2$s', 'wpbrigade-sdk' ),
						esc_html( $wpb_product_title ),
						'<span class="wpb-highlight">' . esc_html__( 'No Spam, I promise.', 'wpbrigade-sdk' ) . '</span>'
					);
					?>
				</p>

				<p class="wpb-sdk-help-line">
					<?php
					printf(
						/* translators: %s: product name */
						esc_html__( 'Help us %s', 'wpbrigade-sdk' ),
						'<strong>' . sprintf( esc_html__( 'Improve %s', 'wpbrigade-sdk' ), esc_html( $wpb_product_title ) ) . '</strong>'
					);
					?>
				</p>

				<?php
				$wpb_sdk_is_already_opted_in = ( ! empty( $wpb_sdk_optin_option ) && 'yes' === get_option( $wpb_sdk_optin_option ) )
					|| ( ! empty( $wpb_sdk_plugin_slug ) && function_exists( 'wpb_sdk_get_optin_decision' ) && 'yes' === wpb_sdk_get_optin_decision( $wpb_sdk_plugin_slug ) );

				if ( ! $wpb_sdk_is_already_opted_in ) :
				?>
				<div class="wpb-sdk-action-row">
					<button type="button" id="wpb-sdk-optin-submit-btn" class="wpb-sdk-btn-allow">
						<span><?php esc_html_e( 'Allow and Continue', 'wpbrigade-sdk' ); ?></span>
						<span class="wpb-btn-arrow"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.927734 7.42847H13.9277" stroke="currentColor" stroke-width="1.85714" stroke-linecap="round" stroke-linejoin="round"/><path d="M7.42773 0.928467L13.9277 7.42847L7.42773 13.9285" stroke="currentColor" stroke-width="1.85714" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
						<span class="wpb-sdk-spinner" style="display:none;"></span>
					</button>

					<button type="button" id="wpb-sdk-optin-skip-btn" class="wpb-sdk-btn-skip">
						<span><?php esc_html_e( 'Skip', 'wpbrigade-sdk' ); ?></span>
						<span class="wpb-sdk-spinner-dark" style="display:none; margin-left: 6px;"></span>
					</button>
				</div>
				<?php else : ?>
				<div class="wpb-sdk-action-row">
					<a href="<?php echo esc_url( $wpb_sdk_redirect_url ); ?>" class="wpb-sdk-btn-allow">
						<span><?php esc_html_e( 'Continue to Settings', 'wpbrigade-sdk' ); ?></span>
						<span class="wpb-btn-arrow"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.927734 7.42847H13.9277" stroke="currentColor" stroke-width="1.85714" stroke-linecap="round" stroke-linejoin="round"></path><path d="M7.42773 0.928467L13.9277 7.42847L7.42773 13.9285" stroke="currentColor" stroke-width="1.85714" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>
					</a>
				</div>
				<?php endif; ?>
			</div>

			<!-- Right Illustration (Shield) -->
			<div class="wpb-sdk-body-right">
				<?php
				$wpb_sdk_shield_file = dirname( __DIR__ ) . '/assets/shield.svg';
				$wpb_sdk_shield_url  = '';
				if ( is_readable( $wpb_sdk_shield_file ) && defined( 'WP_PLUGIN_DIR' ) ) {
					$wpb_sdk_shield_rel = ltrim(
						str_replace(
							wp_normalize_path( WP_PLUGIN_DIR ),
							'',
							wp_normalize_path( $wpb_sdk_shield_file )
						),
						'/'
					);
					if ( '' !== $wpb_sdk_shield_rel ) {
						$wpb_sdk_shield_url = plugins_url( $wpb_sdk_shield_rel );
					}
				}
				?>
				<?php if ( '' !== $wpb_sdk_shield_url ) : ?>
					<img
						class="wpb-sdk-shield-graphic"
						src="<?php echo esc_url( $wpb_sdk_shield_url ); ?>"
						alt=""
						width="144" height="161"
						decoding="async"
					/>
				<?php else : ?>
					<svg class="wpb-sdk-shield-graphic" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M36 90C32 58 56 30 92 26C112 24 126 38 144 34C168 28 188 50 184 78C182 94 192 106 188 126C182 156 152 176 122 178C104 180 94 166 78 170C52 176 30 154 28 126C27 110 40 102 36 90Z" fill="#E9EFFF"/>
						<path d="M100 40L148 62V108C148 140 126 164 100 174C74 164 52 140 52 108V62L100 40Z" fill="#D2DDF2"/>
						<path d="M100 52L136 70V108C136 134 120 154 100 162C80 154 64 134 64 108V70L100 52Z" fill="#3C50E0"/>
						<path d="M84 108L96 120L120 92" stroke="#07003B" stroke-width="9" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				<?php endif; ?>
			</div>
		</div>

		<!-- Permissions Accordion Section -->
		<div class="wpb-sdk-permissions-section">
			<button type="button" class="wpb-sdk-permissions-toggle is-closed" id="wpb-sdk-perm-toggle-btn">
				<span><?php esc_html_e( 'What permissions are being granted?', 'wpbrigade-sdk' ); ?></span>
				<svg width="17" height="10" viewBox="0 0 17 10" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M15.166 1.1665L8.16602 8.1665L1.16602 1.1665" stroke="currentColor" stroke-width="2.33333" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>

			</button>

			<div class="wpb-sdk-perm-dropdown" id="wpb-sdk-perm-content">
				<div class="wpb-sdk-perm-grid">
					<!-- Permission 1: Website Overview -->
					<div class="wpb-sdk-perm-item">
						<div class="wpb-sdk-perm-icon">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<circle cx="12" cy="12" r="10"></circle>
								<line x1="2" y1="12" x2="22" y2="12"></line>
								<path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
							</svg>
						</div>
						<div class="wpb-sdk-perm-content">
							<h4><?php esc_html_e( 'Your Website Overview', 'wpbrigade-sdk' ); ?></h4>
							<p><?php esc_html_e( 'Your Site URL, WordPress & PHP version, plugins & themes. This data lets us make sure this plugin always stays compatible with the most popular plugins and themes.', 'wpbrigade-sdk' ); ?></p>
						</div>
					</div>

					<!-- Permission 2: Profile Overview -->
					<div class="wpb-sdk-perm-item">
						<div class="wpb-sdk-perm-icon">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
								<circle cx="12" cy="7" r="4"></circle>
							</svg>
						</div>
						<div class="wpb-sdk-perm-content">
							<h4><?php esc_html_e( 'Your Profile Overview', 'wpbrigade-sdk' ); ?></h4>
							<p><?php esc_html_e( 'Your name and email address.', 'wpbrigade-sdk' ); ?></p>
						</div>
					</div>

					<!-- Permission 3: Admin Notices -->
					<div class="wpb-sdk-perm-item">
						<div class="wpb-sdk-perm-icon">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
								<polyline points="22,6 12,13 2,6"></polyline>
							</svg>
						</div>
						<div class="wpb-sdk-perm-content">
							<h4><?php esc_html_e( 'Admin Notices', 'wpbrigade-sdk' ); ?></h4>
							<p><?php esc_html_e( 'Updates, Announcement, Marketing. No Spam, I promise.', 'wpbrigade-sdk' ); ?></p>
						</div>
					</div>

					<!-- Permission 4: Plugin Actions (Matching Reference Circular Plug Icon) -->
					<div class="wpb-sdk-perm-item">
						<div class="wpb-sdk-perm-icon">
						<svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M1.62501 12.9974C1.62552 6.71555 6.71891 1.62393 13.0011 1.62494C19.2828 1.62545 24.3752 6.7186 24.3741 13C24.3731 19.2814 19.2802 24.3735 12.9981 24.373C10.605 24.3727 8.27305 23.6177 6.33399 22.2154C6.06281 22.0189 5.98309 21.5558 6.33094 21.2079L9.25596 18.283C11.5203 19.977 14.686 19.75 16.6853 17.7499L18.9832 15.4506C19.3006 15.1332 19.3006 14.6188 18.9832 14.302L17.8345 13.1533L20.368 10.6139C20.6904 10.3016 20.6986 9.78718 20.3863 9.46524C20.074 9.14279 19.5595 9.13467 19.2376 9.44696L19.2178 9.46677L16.6843 12.0047L14.3864 9.70695L16.9214 7.16901C17.2515 6.86484 17.2723 6.35096 16.9682 6.02089C16.664 5.69083 16.1501 5.67001 15.82 5.97418L15.7722 6.02191L13.2372 8.5568L12.0886 7.4097C11.7712 7.09233 11.2568 7.09233 10.9399 7.4097L8.64201 9.70746C6.64274 11.7066 6.41574 14.8707 8.1088 17.1349L5.18378 20.0598C4.24128 21.0023 4.17628 22.6597 5.38031 23.5321C11.1973 27.7401 19.3244 26.4356 23.5322 20.6189C27.7399 14.8021 26.4359 6.67544 20.6188 2.46788C14.8018 -1.73968 6.67524 -0.436182 2.46697 5.38057C0.863742 7.59677 0.000461 10.2622 0 12.9974C0 13.4463 0.363596 13.8099 0.812505 13.8099C1.26141 13.8099 1.62501 13.4463 1.62501 12.9974Z" fill="#3458EA"/>
</svg>

						</div>
						<div class="wpb-sdk-perm-content">
							<h4><?php esc_html_e( 'Plugin Actions', 'wpbrigade-sdk' ); ?></h4>
							<p>
								<?php
								printf(
									/* translators: %s: product name */
									esc_html__( 'Active, Deactive, Uninstallation and How you use this plugin\'s features and settings. This is limited to usage data. It does not include any of your sensitive %s data, such as traffic. This data helps us learn which features are most popular, so we can improve the plugin further.', 'wpbrigade-sdk' ),
									esc_html( $wpb_product_title )
								);
								?>
							</p>
						</div>
					</div>
				</div>
				<div class="wpb-sdk-permissions-bottom-object">
				<svg width="289" height="101" viewBox="0 0 289 101" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect x="247" y="42.5" width="40" height="40" rx="20" fill="#3458EA"/>
<path d="M258.916 63.1901C258.775 63.0484 258.775 62.8191 258.917 62.6782L260.632 60.9753C260.775 60.8336 261.005 60.8355 261.146 60.9795L264.296 64.2122C264.436 64.3566 264.668 64.3581 264.81 64.2156L272.772 56.2538C272.913 56.1126 273.142 56.1126 273.284 56.2538L275.085 58.0548C275.226 58.1958 275.226 58.4243 275.085 58.5656L264.961 68.7443C264.819 68.8864 264.589 68.8865 264.448 68.7445L258.916 63.1901Z" fill="white"/>
<path d="M16.5576 93.3691C15.6037 95.8664 14.7173 98.3979 13.9014 100.96L12.7891 100.604C13.6117 98.0216 14.5032 95.4697 15.4648 92.9521L16.5576 93.3691ZM285.532 92.9521C286.494 95.4697 287.385 98.0216 288.208 100.604L287.654 100.782L287.653 100.781L287.097 100.96C286.281 98.3979 285.394 95.8664 284.44 93.3691L285.532 92.9521ZM22.6113 78.4316H22.6104L23.1309 78.7012C21.9011 81.0768 20.7361 83.4915 19.6377 85.9424L18.5713 85.4648C19.6788 82.9936 20.8538 80.5594 22.0938 78.1641L22.6113 78.4316ZM278.903 78.1641C280.143 80.5594 281.318 82.9936 282.426 85.4648L281.36 85.9424C280.262 83.4915 279.097 81.0768 277.867 78.7012L278.387 78.4316L278.903 78.1641ZM31.3105 64.8643C29.8212 67.0889 28.3921 69.3577 27.0264 71.668L26.0195 71.0723C27.3963 68.7433 28.8384 66.4575 30.3398 64.2148L31.3105 64.8643ZM270.657 64.2148C272.159 66.4575 273.601 68.7433 274.978 71.0723H274.979L273.972 71.668C272.606 69.3577 271.177 67.0889 269.688 64.8643L270.657 64.2148ZM40.9863 52.0264C39.2567 54.0726 37.5833 56.1685 35.9688 58.3105L35.0352 57.6074C36.3193 55.9037 37.6395 54.2288 38.9961 52.585L40.0928 51.2715L40.9863 52.0264ZM260.904 51.2715C262.648 53.3345 264.334 55.4478 265.962 57.6074L265.029 58.3105C263.415 56.1685 261.741 54.0726 260.012 52.0264L260.904 51.2715ZM52.0322 40.3467C50.0846 42.1887 48.1887 44.0855 46.3467 46.0332L45.498 45.2295C47.3546 43.2665 49.2655 41.3556 51.2285 39.499L52.0322 40.3467ZM249.769 39.499C251.732 41.3556 253.642 43.2665 255.499 45.2295L254.652 46.0332C252.81 44.0855 250.913 42.1896 248.966 40.3477L249.769 39.499ZM64.3096 29.9697C62.1675 31.5843 60.0716 33.2577 58.0254 34.9873L57.2705 34.0938C59.3335 32.35 61.4468 30.664 63.6064 29.0361L64.3096 29.9697ZM237.391 29.0361C239.55 30.664 241.664 32.35 243.727 34.0938H243.728L242.973 34.9873C240.926 33.2577 238.831 31.5843 236.688 29.9697L237.391 29.0361ZM77.667 21.0273C75.3567 22.393 73.0879 23.8222 70.8633 25.3115L70.2139 24.3408C72.4549 22.8405 74.7391 21.3994 77.0664 20.0234L77.0713 20.0205L77.667 21.0273ZM223.926 20.0205C226.255 21.3973 228.541 22.8394 230.783 24.3408L230.135 25.3115C227.91 23.8222 225.641 22.393 223.331 21.0273L223.926 20.0205ZM91.9414 13.6387C89.4905 14.7371 87.0759 15.902 84.7002 17.1318L84.1631 16.0947C86.5584 14.8548 88.9927 13.6798 91.4639 12.5723L91.9414 13.6387ZM209.533 12.5723C212.004 13.6798 214.439 14.8548 216.834 16.0947L216.298 17.1318C213.922 15.902 211.508 14.7371 209.057 13.6387L209.533 12.5723ZM106.959 7.90234C104.397 8.71828 101.865 9.60472 99.3682 10.5586L98.9512 9.46582C101.469 8.50419 104.021 7.61263 106.604 6.79004L106.959 7.90234ZM194.394 6.79004C196.976 7.61262 199.528 8.50421 202.046 9.46582L201.63 10.5586C199.133 9.60472 196.601 8.71828 194.039 7.90234L194.394 6.79004ZM122.408 3.30762L122.525 3.89844C119.886 4.42056 117.272 5.01481 114.688 5.67969L114.396 4.54688L114.396 4.5459C117.002 3.87555 119.637 3.27935 122.298 2.75293L122.408 3.30762ZM178.699 2.75293C181.36 3.27933 183.995 3.87557 186.602 4.5459V4.54688L186.311 5.67969C183.726 5.01481 181.112 4.42056 178.473 3.89844L178.699 2.75293ZM138.44 1.66992C135.756 1.89354 133.092 2.191 130.451 2.56055L130.289 1.40234C132.951 1.0298 135.636 0.731291 138.343 0.505859L138.44 1.66992ZM162.654 0.505859C165.361 0.731273 168.046 1.02982 170.708 1.40234L170.547 2.56055C167.906 2.191 165.242 1.89354 162.558 1.66992L162.654 0.505859ZM150.499 0C151.858 0 153.212 0.0193843 154.562 0.0566406L154.547 0.640625L154.546 0.639648L154.53 1.22559C153.191 1.18861 151.847 1.16992 150.499 1.16992C149.151 1.16992 147.807 1.18861 146.468 1.22559L146.451 0.639648V0.640625L146.436 0.0566406C147.786 0.0193663 149.14 0 150.499 0Z" fill="#3458EA"/>
<path d="M0 51.4091C0 48.6979 2.19788 46.5 4.90909 46.5H40.0909C42.8021 46.5 45 48.6979 45 51.4091V86.5909C45 89.3021 42.8021 91.5 40.0909 91.5H4.90909C2.19788 91.5 0 89.3021 0 86.5909V51.4091Z" fill="#DAE2FF"/>
<path d="M28.7033 68.1138H16.2942C15.3152 68.1138 14.5215 68.9074 14.5215 69.8865V76.091C14.5215 77.0701 15.3152 77.8638 16.2942 77.8638H28.7033C29.6824 77.8638 30.476 77.0701 30.476 76.091V69.8865C30.476 68.9074 29.6824 68.1138 28.7033 68.1138Z" stroke="#3458EA" stroke-width="1.63636" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M18.0703 68.1135V64.568C18.0703 63.3927 18.5372 62.2654 19.3684 61.4343C20.1995 60.6032 21.3267 60.1362 22.5021 60.1362C23.6775 60.1362 24.8048 60.6032 25.6359 61.4343C26.467 62.2654 26.9339 63.3927 26.9339 64.568V68.1135" stroke="#3458EA" stroke-width="1.63636" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

				</div>
			</div>
		</div>
	</div>
</div>

<input type="hidden" id="wpb-sdk-optin-page-nonce" value="<?php echo esc_attr( $wpb_sdk_optin_nonce ); ?>" />

<script>
	jQuery(function($) {
		var nonce      = $('#wpb-sdk-optin-page-nonce').val();
		var redirect   = <?php echo wp_json_encode( $wpb_sdk_redirect_url ); ?>;
		var ajaxurl    = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
		var ajaxPrefix = <?php echo wp_json_encode( $wpb_sdk_ajax_prefix ); ?>;

		// Accordion Toggle
		$('#wpb-sdk-perm-toggle-btn').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			$btn.toggleClass('is-closed');
			var $dropdown = $('#wpb-sdk-perm-content');
			$dropdown.slideToggle(200, function() {
				$btn.toggleClass('is-closed', !$dropdown.is(':visible'));
			});
		});

		// Submit (Allow & Continue)
		$('#wpb-sdk-optin-submit-btn').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			$btn.prop('disabled', true);
			$btn.find('.wpb-btn-arrow').hide();
			$btn.find('.wpb-sdk-spinner').show();
			$('#wpb-sdk-optin-skip-btn').prop('disabled', true);

			$.post(ajaxurl, {
				action: ajaxPrefix + '_optin_yes',
				optin_yes_nonce: nonce
			}).always(function() {
				window.location.href = redirect;
			});
		});

		// Skip
		$('#wpb-sdk-optin-skip-btn').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			$btn.prop('disabled', true);
			$btn.find('.wpb-sdk-spinner-dark').show();
			$('#wpb-sdk-optin-submit-btn').prop('disabled', true);

			$.post(ajaxurl, {
				action: ajaxPrefix + '_optin_skip',
				optin_skip_nonce: nonce
			}).always(function() {
				window.location.href = redirect;
			});
		});
	});
</script>
