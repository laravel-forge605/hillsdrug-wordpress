<?php

class EHSSL_Settings_Menu extends EHSSL_Admin_Menu
{
    public $menu_page_slug = EHSSL_SETTINGS_MENU_SLUG;

    // Specify all the tabs of this menu in the following array.
    public $dashboard_menu_tabs = array('general' => 'General', 'mixed-contents' => 'Mixed Contents');

    public function get_current_tab()
    {
        $tab = isset($_GET['tab']) ? $_GET['tab'] : array_keys($this->dashboard_menu_tabs)[0];
        return $tab;
    }

    /**
     * Renders our tabs of this menu as nav items
     */
    public function render_page_tabs()
    {
        $current_tab = $this->get_current_tab();
        foreach ($this->dashboard_menu_tabs as $tab_key => $tab_caption) {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
            echo '<a class="nav-tab ' . esc_attr( $active ) . '" href="?page=' . esc_attr( $this->menu_page_slug ) . '&tab=' . esc_attr( $tab_key ) . '">' . esc_html( $tab_caption ) . '</a>';
        }
    }

    /**
     * The menu rendering goes here
     */
    public function render_menu_page()
    {
        $tab = $this->get_current_tab();

        ?>
        <div class="wrap">
            <h2><?php esc_html_e("Settings", 'https-redirection')?></h2>
            <h2 class="nav-tab-wrapper"><?php $this->render_page_tabs();?></h2>
            <div id="poststuff"><div id="post-body">
            <?php

        switch ($tab) {
	        case 'mixed-contents':
		        //include_once('file-to-handle-this-tab-rendering.php');//If you want to include a file
		        $this->render_mixed_content_tab();
		        break;
	        case 'general':
	        default:
                //include_once('file-to-handle-this-tab-rendering.php');//If you want to include a file
                $this->render_general_tab();
                break;
        }
        ?>
            </div>
        </div>
        <?php $this->documentation_link_box();?>
        </div><!-- end or wrap -->
        <?php
    }

    public function render_general_tab()
    {
	    $settings = get_option('httpsrdrctn_options', array());

	    // Save data for settings page.
	    if (isset($_REQUEST['httpsrdrctn_form_submit']) && check_admin_referer(plugin_basename(__FILE__), 'httpsrdrctn_nonce_name')) {
		    $settings['https'] = isset($_REQUEST['httpsrdrctn_https']) ? $_REQUEST['httpsrdrctn_https'] : 0;
		    $settings['https_domain'] = isset($_REQUEST['httpsrdrctn_https_domain']) ? $_REQUEST['httpsrdrctn_https_domain'] : 0;

		    $settings['hsts_enabled'] = isset($_REQUEST['hsts_enabled']) ? 1 : 0;
		    $settings['hsts_max_age'] = isset($_REQUEST['hsts_max_age']) && !empty($_REQUEST['hsts_max_age']) ? absint(sanitize_text_field($_REQUEST['hsts_max_age'])) : 31536000;
		    $settings['hsts_include_sub_domains'] = isset($_REQUEST['hsts_include_sub_domains']) ? 1 : 0;
		    $settings['hsts_preload'] = isset($_REQUEST['hsts_preload']) ? 1 : 0;

		    if (isset($_REQUEST['httpsrdrctn_https_pages_array'])) {
			    $settings['https_pages_array'] = array();
			    // var_dump($httpsrdrctn_options['https_pages_array']);
			    foreach ($_REQUEST['httpsrdrctn_https_pages_array'] as $httpsrdrctn_https_page) {
				    if (!empty($httpsrdrctn_https_page) && $httpsrdrctn_https_page != '') {
					    $httpsrdrctn_https_page = str_replace('https', 'http', $httpsrdrctn_https_page);
					    $settings['https_pages_array'][] = trim(str_replace(home_url(), '', $httpsrdrctn_https_page), '/');
				    }
			    }
		    }

            // Update options in the database.
            update_option('httpsrdrctn_options', $settings, '', 'yes');

            echo '<div class="notice notice-success"><p>'.esc_html__("Settings Saved.", 'https-redirection').'</p></div>';

            $httpsrdrctn_obj = new EHSSL_Htaccess();
            $httpsrdrctn_obj->write_to_htaccess();

            // clear caching plugins cache if needed
            // WP Fastest Cache
            if (isset($GLOBALS["wp_fastest_cache"])) {
                $wpfc = $GLOBALS["wp_fastest_cache"];
                if (method_exists($wpfc, 'deleteCache') && is_callable(array($wpfc, 'deleteCache'))) {
                    $wpfc->deleteCache(true);
                }
            }
            // httpsrdrctn_generate_htaccess();

	    }
	    $siteSSLurl = get_home_url(null, '', 'https');

        $hsta_enabled = isset($settings['hsts_enabled']) && !empty($settings['hsts_enabled']) ? 1 : 0;
        $hsta_max_age = isset($settings['hsts_max_age']) && !empty($settings['hsts_max_age']) ? absint(sanitize_text_field($settings['hsts_max_age'])) : 31536000;
        $hsta_include_sub_domains = isset($settings['hsts_include_sub_domains']) && !empty($settings['hsts_include_sub_domains']) ? 1 : 0;
        $hsta_preload = isset($settings['hsts_preload']) && !empty($settings['hsts_preload']) ? 1 : 0;

        // Save data for settings page.
        if (isset($_POST['ehssl_debug_log_form_submit']) && check_admin_referer('ehssl_debug_settings_nonce')) {
            $settings['enable_debug_logging'] = isset($_POST['enable_debug_logging']) ? esc_attr($_POST['enable_debug_logging']) : 0;

            update_option('httpsrdrctn_options', $settings)

            ?>
            <div class="notice notice-success">
                <p><?php esc_html_e("Settings Saved.", 'https-redirection');?></p>
            </div>
            <?php
        }

        $is_debug_logging_enabled = isset($settings['enable_debug_logging']) ? esc_attr($settings['enable_debug_logging']) : 0;

        if ( empty($this->is_ssl_installed) ) { ?>
        <div class="ehssl-yellow-box">
            <p>
			    <?php /* translators: %s: Link to the HTTPS version of this site. */
                    echo sprintf(esc_html__("When you enable the HTTPS redirection, the plugin will force redirect the URL to the HTTPS version of the URL. So before enabling this plugin's feature, visit your site's HTTPS URL %s to make sure the page loads correctly. Otherwise you may get locked out if your SSL certificate is not installed correctly on your site or the HTTPS URL is not working and this plugin is auto redirecting to the HTTPS URL.", 'https-redirection'), '<a href="' . esc_url( $siteSSLurl ) . '" target="_blank">' . esc_url( $siteSSLurl ) . '</a>'); ?>
            </p>
            <p>
                <span style="font-weight:bold; color:red;"><?php esc_html_e('Important!', 'https-redirection');?></span>
			    <?php esc_html_e("If you're using caching plugins similar to W3 Total Cache or WP Super Cache, you need to clear their cache after you enable or disable automatic redirection option. Failing to do so may result in mixed content warning from browser.", 'https-redirection');?>
            </p>
        </div>
        <?php } ?>

        <div class="postbox">
            <h3 class="hndle"><label for="title"><?php esc_html_e("HTTPS Redirection", 'https-redirection');?></label></h3>
            <div class="inside">
			    <?php
			    // Display form on the setting page.
			    if (get_option('permalink_structure')) {
				    // Pretty permalink is enabled. So allow HTTPS redirection feature.
				    ?>
                    <div id="httpsrdrctn_settings_notice" class="updated fade" style="display:none">
                        <p>
                            <strong><?php esc_html_e("Notice:", 'https-redirection');?></strong><?php esc_html_e("The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'https-redirection');?>
                        </p>
                    </div>

                    <form id="httpsrdrctn_settings_form" method="post" action="">
                        <div style="position: relative">
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row"><?php esc_html_e('Enable Automatic Redirection to HTTPS', 'https-redirection');?></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" id="httpsrdrctn-checkbox" name="httpsrdrctn_https" value="1" <?php if ('1' == $settings['https']) {echo "checked=\"checked\" ";}?> />
                                        </label>
                                        <br />
                                        <p class="description"><?php esc_html_e("Use this option to make your webpage(s) load in HTTPS version only. If someone enters a non-https URL in the browser's address bar then the plugin will automatically redirect to the HTTPS version of that URL.", 'https-redirection');?></p>
                                    </td>
                                </tr>
                            </table>

                            <div class="ehssl-enable-automatic-redirection httpsrdrctn-overlay <?php echo ($this->is_ssl_installed ? 'hidden' : ''); ?>"></div>
                        </div>

                        <div style="position: relative">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php esc_html_e('Apply HTTPS Redirection To:', 'https-redirection');?></th>
                                    <td>
                                        <div style="margin-bottom: 6px">
                                            <label><input type="radio" name="httpsrdrctn_https_domain" value="1" <?php if ('1' == $settings['https_domain']) {echo "checked=\"checked\" ";}?> /> <?php esc_html_e('The whole domain', 'https-redirection');?></label>
                                        </div>
                                        <div style="margin-bottom: 6px">
                                            <label><input type="radio" name="httpsrdrctn_https_domain" value="0" <?php if ('0' == $settings['https_domain']) {echo "checked=\"checked\" ";}?> /> <?php esc_html_e('A few pages', 'https-redirection');?></label>
                                        </div>
									    <?php foreach ($settings['https_pages_array'] as $https_page) { ?>
                                            <div style="margin-bottom: 5px">
											    <?php echo esc_url( str_replace("http://", "https://", home_url()) ); ?>/<input type="text" name="httpsrdrctn_https_pages_array[]" value="<?php echo esc_attr( $https_page ); ?>" /> <span class="button-secondary rewrite_item_delete_btn"><i class="dashicons dashicons-trash"></i></span> <span class="rewrite_item_blank_error"><?php esc_html_e('Please enter a page slug value in the field before adding it.', 'https-redirection');?></span>
                                            </div>
									    <?php } ?>
                                        <div class="rewrite_new_item">
										    <?php echo esc_url( str_replace("http://", "https://", home_url()) ); ?>/<input type="text" name="httpsrdrctn_https_pages_array[]" placeholder="<?php esc_attr_e('Enter the page slug','https-redirection'); ?>" value="" /> <span class="button-secondary rewrite_item_add_btn"><i class="dashicons dashicons-plus-alt2"></i></span> <span class="rewrite_item_blank_error"><?php esc_html_e('Please enter a page slug value in the field before adding it.', 'https-redirection');?></span>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div class="ehssl-apply-redirection-on httpsrdrctn-overlay <?php echo ($settings['https'] == 1 ? 'hidden' : ''); ?>"></div>
                        </div>

                        <hr>

                        <h3 style="font-size: 16px"><?php esc_html_e('HTTP Strict Transport Security (HSTS) (Optional)', 'https-redirection'); ?></h3>
                        <p class="description"><?php esc_html_e("Only enable the HSTS option if your entire website is fully accessible over HTTPS.", 'https-redirection');?></p>
                        <div style="position: relative">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php esc_html_e('Enable HTTP Strict Transport Security (HSTS):', 'https-redirection');?></th>
                                    <td>                                        
                                        <input type="checkbox" id="https-hsts-checkbox" name="hsts_enabled" <?php echo $hsta_enabled ? "checked" : ''; ?> min="0" />
                                        <p class="description"><?php esc_html_e("Once a visitor accesses your site over HTTPS, their browser will automatically use HTTPS for future visits for the configured period.", 'https-redirection');?></p>

                                        <div class="description" style="position: relative;">
                                            <p class="description">
                                                <?php esc_html_e('Max age (in seconds):', 'https-redirection');?>
                                                <input type="number" id="ehssl-hsts-max-age" name="hsts_max_age" value="<?php echo esc_attr($hsta_max_age); ?>" style="width: 140px"/>
                                                <?php esc_html_e(' Specifies how long browsers should remember to use HTTPS only. The recommended value is 31536000 seconds (1 year).', 'https-redirection');?>
                                            </p>
                                            <p class="description">
                                                <input type="checkbox" id="ehssl-hsts-include-sub-domain" name="hsts_include_sub_domains" <?php echo $hsta_include_sub_domains ? "checked" : ''; ?> />
                                                <?php esc_html_e("Apply the HSTS policy to all subdomains of this site.", 'https-redirection');?>
                                            </p>
                                            <p class="description">
                                                <input type="checkbox" id="https-hsts-preload" name="hsts_preload" <?php echo $hsta_preload ? "checked" : ''; ?> />
                                                <?php
                                                printf(
                                                    /* translators: %s: Link to the HSTS preload submission form. */
                                                    esc_html__("Include the preload directive. This alone does not add your site to browser preload lists, you must also manually submit your domain using the %s.", 'https-redirection'),
                                                    '<a href="https://hstspreload.org/#submission-form" target="_blank">link here</a>'
                                                );
                                                ?>
                                            </p>

                                            <div class="ehssl-apply-hsts-directives httpsrdrctn-overlay <?php echo ($hsta_enabled ? 'hidden' : ''); ?>"></div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div class="ehssl-apply-hsts httpsrdrctn-overlay <?php echo ($settings['https'] == 1 && $settings['https_domain'] == 1 ? 'hidden' : ''); ?>"></div>
                        </div>

                        <input type="hidden" name="httpsrdrctn_form_submit" value="submit" />

                        <p class="submit">
                            <input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'https-redirection' )?>"  <?php echo !is_ssl() ? 'disabled' : '' ?>/>
                        </p>
					    <?php wp_nonce_field(plugin_basename(__FILE__), 'httpsrdrctn_nonce_name');?>
                    </form>
                    <script>
                        jQuery('input#httpsrdrctn-checkbox').change(function() {
                            if (jQuery(this).is(':checked')) {
                                jQuery('div.ehssl-apply-redirection-on.httpsrdrctn-overlay').fadeOut('fast');
                            } else {
                                jQuery('div.ehssl-apply-redirection-on.httpsrdrctn-overlay').fadeIn('fast');
                            }
                        });

                        jQuery('input#httpsrdrctn-checkbox').on('change', function() {
                            if (jQuery(this).is(':checked') && jQuery('input[name="httpsrdrctn_https_domain"]:checked').val() == 1) {
                                jQuery('div.ehssl-apply-hsts.httpsrdrctn-overlay').fadeOut('fast');
                            } else {
                                jQuery('div.ehssl-apply-hsts.httpsrdrctn-overlay').fadeIn('fast');
                            }
                        });

                        jQuery('input[name="httpsrdrctn_https_domain"]').on('change', function() {
                            if (jQuery(this).val() == 1 && jQuery('input#httpsrdrctn-checkbox').is(':checked')) {
                                jQuery('div.ehssl-apply-hsts.httpsrdrctn-overlay').fadeOut('fast');
                            } else {
                                jQuery('div.ehssl-apply-hsts.httpsrdrctn-overlay').fadeIn('fast');
                            }
                        });

                        jQuery('input#https-hsts-checkbox').change(function() {
                            if (jQuery(this).is(':checked')) {
                                jQuery('div.ehssl-apply-hsts-directives.httpsrdrctn-overlay').fadeOut('fast');
                            } else {
                                jQuery('div.ehssl-apply-hsts-directives.httpsrdrctn-overlay').fadeIn('fast');
                            }
                        });
                    </script>
                    <style>
                        .httpsrdrctn-overlay {
                            position: absolute;
                            top: 10px;
                            background-color: white;
                            width: 100%;
                            height: 100%;
                            opacity: 0.5;
                            text-align: center;
                        }
                    </style>

                    <div class="ehssl-red-box">
                        <p><strong><?php esc_html_e("Notice:", 'https-redirection');?></strong> <?php esc_html_e("It is very important to be extremely attentive when making changes to .htaccess file.", 'https-redirection');?></p>
                        <p><?php esc_html_e('If after making changes your site stops functioning, do the following:', 'https-redirection');?></p>
                        <p><?php esc_html_e('Step #1: Open .htaccess file in the root directory of the WordPress install and delete everything between the following two lines', 'https-redirection');?></p>
                        <p style="border: 1px solid #ccc; padding: 10px;">
                            # BEGIN HTTPS Redirection Plugin<br />
                            # END HTTPS Redirection Plugin
                        </p>
                        <p><?php esc_html_e('Step #2: Save the htaccess file (this will erase any change this plugin made to that file).', 'https-redirection');?></p>
                        <p><?php esc_html_e("Step #3: Deactivate the plugin or rename this plugin's folder (which will deactivate the plugin).", 'https-redirection');?></p>

                        <p><?php esc_html_e('The changes will be applied immediately after saving the changes, if you are not sure - do not click the "Save changes" button.', 'https-redirection');?></p>
                    </div>

			    <?php } else {?>
                    <!-- pretty permalink is NOT enabled. This plugin can't work. -->
                    <div class="error">
                        <p><?php esc_html_e('HTTPS redirection only works if you have pretty permalinks enabled.', 'https-redirection');?></p>
                        <p><?php echo wp_kses_post( __('To enable pretty permalinks go to <em>Settings > Permalinks</em> and select any option other than "default".', 'https-redirection') );?></p>
                        <p><a href="options-permalink.php"><?php esc_html_e('Enable Permalinks', 'https-redirection');?></a></p>
                    </div>
			    <?php }?>
            </div>
        </div>
        <div class="postbox">
            <h3 class="hndle"><label for="title"><?php esc_html_e("Debug Logging", 'https-redirection');?></label></h3>
            <div class="inside">
            <p>
                <?php esc_html_e('Debug logging can be useful to troubleshoot issues on your site. keep it disabled unless you are troubleshooting.', 'https-redirection');?>
            </p>
            <form id="ehssl_debug_settings_form" method="post" action="">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="ehssl-debug-enable-checkbox">
                                <?php esc_html_e('Enable Debug Logging', 'https-redirection');?>
                            </label>
                        </th>
                        <td>
                            <input type="checkbox" id="ehssl-debug-enable-checkbox" name="enable_debug_logging" value="1" <?php if ('1' == $is_debug_logging_enabled) {echo "checked=\"checked\" ";}?> />
                            <br />
                            <p class="description"><?php esc_html_e("Check this option to enable debug logging.", 'https-redirection');?></p>
                            <p class="description">
                                <a href="<?php echo esc_url( wp_nonce_url(get_admin_url() . '?ehssl-debug-action=view_log', 'ehssl_view_log_nonce') ); ?>" target="_blank">
                                    <?php esc_html_e('Click here', 'https-redirection')?>
                                </a>
                                <?php esc_html_e(' to view log file.', 'https-redirection');?>
                                <br>
                                <a id="ehssl-reset-log" href="#0" style="color: red">
                                    <?php esc_html_e('Click here', 'https-redirection');?>
                                </a>
                                <?php esc_html_e(' to reset log file.', 'https-redirection');?>
                            </p>
                        </td>
                    </tr>
                </table>

                <input type="submit" name="ehssl_debug_log_form_submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'https-redirection' )?>" />
                <?php wp_nonce_field('ehssl_debug_settings_nonce');?>
            </form>
            </div><!-- end of inside -->
        </div><!-- end of postbox -->
        <script>
            jQuery( document ).ready( function( $ ) {
                const ehssl_ajaxurl = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
				const ehssl_ajax_nonce = <?php echo wp_json_encode( wp_create_nonce( 'ehssl_settings_ajax_nonce' ) ); ?>;
                $( '#ehssl-reset-log' ).on('click', function( e ) {
                    e.preventDefault();
                    $.post( ehssl_ajaxurl,
                            {
                                action: 'ehssl_reset_log',
                                nonce: ehssl_ajax_nonce
                            },
                            function( result ) {
                                if ( result === '1' ) {
                                    alert( '<?php esc_html_e('Log file has been reset.', 'https-redirection') ?>' );
                                } else {
                                    alert( '<?php esc_html_e('Error trying to reset log: ' , 'https-redirection') ?>' + result );
                                }
                            } );
                } );
            } );
        </script>
        <?php
    }

    public function render_mixed_content_tab()
    {
        global $httpsrdrctn_options;

        $is_https_redirection_enabled = isset($httpsrdrctn_options['https']) && esc_attr($httpsrdrctn_options['https']) == '1' ? true : false;

        if (isset($_POST['ehssl_mixed_content_form_submit']) && check_admin_referer('ehssl_mixed_content_settings_nonce')) {
            $httpsrdrctn_options['force_resources'] = isset($_POST['httpsrdrctn_force_resources']) ? esc_attr($_POST['httpsrdrctn_force_resources']) : 0;
            update_option('httpsrdrctn_options', $httpsrdrctn_options)

            ?>
            <div class="notice notice-success">
                <p><?php esc_html_e("Settings Saved.", 'https-redirection');?></p>
            </div>
            <?php
        }
        ?>
        <div class="postbox">
            <h3 class="hndle"><label for="title"><?php esc_html_e("Static Resources", 'https-redirection');?></label></h3>
            <div class="inside">
                <?php if(!$is_https_redirection_enabled){ ?>
                    <div class="ehssl-yellow-box">
                        <p>
                            <?php esc_html_e("HTTPS redirection is turned off. Turn it on first to change these settings below!", 'https-redirection');?>
                        </p>
                    </div>
                <?php } ?>
                <form action="" method="POST">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('Force Resources to Use HTTPS URL', 'https-redirection');?></th>
                            <td>
                                <label>
                                    <input type="checkbox" <?php echo !$is_https_redirection_enabled ? "disabled" : ''; ?> name="httpsrdrctn_force_resources" value="1" <?php echo (isset($httpsrdrctn_options['force_resources']) && $httpsrdrctn_options['force_resources'] == '1') ? 'checked="checked"' : ''; ?> />
                                </label>
                                <br />
                                <p class="description"><?php esc_html_e('When checked, the plugin will force load HTTPS URL for any static resources in your content. Example: if you have have an image embedded in a post with a NON-HTTPS URL, this option will change that to a HTTPS URL.', 'https-redirection');?></p>
                            </td>
                        </tr>
                    </table>
                    <input type="submit" name="ehssl_mixed_content_form_submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'https-redirection' )?>" <?php if (!$is_https_redirection_enabled) {echo "disabled";}?>/>
                    <?php wp_nonce_field('ehssl_mixed_content_settings_nonce');?>
                </form>
            </div><!-- end of inside -->
        </div><!-- end of postbox -->
        <?php
        $post_types = get_post_types(
                array(
                        'show_ui' => true,
                ),
                'objects'
        );

        $exclude = array(
                'attachment',
                'revision',
                'nav_menu_item',
                'custom_css',
                'customize_changeset',
                'oembed_cache',
                'user_request',
                'wp_block',
                'wp_template',
                'wp_template_part',
                'wp_navigation',
                'wp_font_family',
                'wp_font_face',
        );

        $scannable_post_types = array();
        foreach ( $post_types as $post_type ) {
            if ( in_array( $post_type->name, $exclude ) ) {
                continue;
            }
            $scannable_post_types[] = array(
                    'type' => 'post_type',
                    'name' => $post_type->name,
                    'label' => $post_type->label,
            );
        }

        $other_tables = array(
                array(
                        'name' => 'wp_options_table',
                        'label' => 'WP Options',
                ),
        );

        $flags = array(
                array(
                        'name' => 'include_post_meta',
                        'label' => 'Include Post Meta for Selected Post Types',
                ),
        );

        $has_previous_scan_data = EHSSL_Non_HTTPS_Resources_Scan_Update::get_scan_results_count(true) > 0;
        $rescan_btn_text = __('Re-scan', 'https-redirection');
        $scan_btn_text = $has_previous_scan_data ? $rescan_btn_text :__('Scan', 'https-redirection');
        ?>

        <div class="postbox">
            <h3 class="hndle"><label for="title"><?php esc_html_e("Scan and Update Non-HTTPS URLs", 'https-redirection');?></label></h3>
            <div class="inside">
                <p class="description"><?php esc_html_e('Use this tool to scan for non-https URLs and update them to HTTPS version. Please take a backup of your database before updating the URLs.', 'https-redirection');?></p>
                <br>
                <form action="" method="POST" id="ehssl_non_https_resources_scan_form">
                    <fieldset>
                        <legend><strong><?php esc_html_e('Post Types:', 'https-redirection')?></strong></legend>
                        <ul>
                            <?php foreach ($scannable_post_types as $index => $item) { ?>
                                <li>
                                    <input
                                            id="<?php echo esc_attr('ehssl-'.$item['name'].'-'.$index) ?>"
                                            type="checkbox"
                                            name="ehssl_post_types[]"
                                            value="<?php echo esc_attr($item['name']); ?>"
                                    />
                                    <label for="<?php echo esc_attr('ehssl-'.$item['name'].'-'.$index) ?>"><?php echo esc_attr($item['label']) . ' ('.esc_attr($item['name']).')' ?></label>
                                </li>
                            <?php } ?>
                        </ul>
                    </fieldset>

                    <fieldset>
                        <legend><strong><?php esc_html_e('Other Database Tables:', 'https-redirection')?></strong></legend>
                        <ul>
                            <?php foreach ($other_tables as $index => $item) { ?>
                                <li>
                                    <input
                                            id="<?php echo esc_attr('ehssl-'.$item['name'].'-'.$index) ?>"
                                            type="checkbox"
                                            name="ehssl_other_tables[]"
                                            value="<?php echo esc_attr($item['name']); ?>"
                                    />
                                    <label for="<?php echo esc_attr('ehssl-'.$item['name'].'-'.$index) ?>"><?php echo esc_attr($item['label']) ?></label>
                                </li>
                            <?php } ?>
                        </ul>
                    </fieldset>

                    <fieldset>
                        <legend><strong><?php esc_html_e('Additional Flags:', 'https-redirection')?></strong></legend>
                        <ul>
                            <?php foreach ($flags as $index => $item) { ?>
                                <li>
                                    <input
                                            id="<?php echo esc_attr('ehssl-'.$item['name'].'-'.$index) ?>"
                                            type="checkbox"
                                            name="ehssl_additional_flags[]"
                                            value="<?php echo esc_attr($item['name']); ?>"
                                    />
                                    <label for="<?php echo esc_attr('ehssl-'.$item['name'].'-'.$index) ?>"><?php echo esc_attr($item['label']) ?></label>
                                </li>
                            <?php } ?>
                        </ul>
                    </fieldset>

                    <fieldset>
                        <legend><strong><?php esc_html_e('Scan Type:', 'https-redirection')?></strong></legend>
                        <ul>
                            <li>
                                <label>
                                    <input type="radio" name="ehssl_scan_type" value="scan_static_resources_only" checked>
                                    <?php esc_html_e('Scan Static Resources Only', 'https-redirection')?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="ehssl_scan_type" value="scan_all">
                                    <?php esc_html_e('Scan All', 'https-redirection')?>
                                </label>
                            </li>
                        </ul>

                    </fieldset>

                    <?php wp_nonce_field('ehssl_non_https_resources_scan_form_nonce');?>

                    <p class="description">
                        <button
                                type="submit"
                                id="ehssl_non_https_resources_scan_btn"
                                class="button-primary"
                        ><?php echo esc_attr($scan_btn_text) ?></button>
                    </p>

                </form>
                <div id="ehssl_scan_results" style="margin-top:20px;">
                    <!-- table renders here -->
                    <?php
                    if ($has_previous_scan_data){
                        EHSSL_Non_HTTPS_Resources_Scan_Update::render_http_scan_result_table();
                    }
                    ?>

                </div>
            </div><!-- end of inside -->
        </div><!-- end of postbox -->
        <?php
        wp_enqueue_script('ehssl_non_https_resources_scan_update', EASY_HTTPS_SSL_URL . '/js/ehssl-static-resources-scan-update.js', null, EASY_HTTPS_SSL_VERSION, array(
                'in_footer' => true,
                'strategy' => 'defer'
        ));

        wp_localize_script('ehssl_non_https_resources_scan_update', 'ehssl_non_https_resources_scan_update_js_data', array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'resultsNonce' => wp_create_nonce( 'ehssl_scan_results' ),
                'texts' => array(
                        'nothing_found' => __('Nothing Found!', 'https-redirection'),
                        'pls_select_an_item' => __('Please select an item to scan!', 'https-redirection'),
                        'confirm_update' => __('Are you sure? This will permanently update urls in databases.', 'https-redirection'),
                        'scan_btn_loading' => __('Scanning...', 'https-redirection'),
                        'rescan_btn' => $rescan_btn_text,
                        'update_btn_loading' => __('Updating...', 'https-redirection'),
                ),
        ));
    }
} // End class