<?php

/* Parent class for all admin menu classes */
abstract class EHSSL_Admin_Menu
{
    protected $is_ssl_installed;

    public function __construct() {
        $this->is_ssl_installed = EHSSL_SSL_Utils::is_ssl_installed();

        $this->render_menu_page();
    }

    /**
     * Shows postbox for settings menu
     *
     * @param string $id css ID for postbox
     * @param string $title title of the postbox section
     * @param string $content the content of the postbox
     */
    public function postbox($id, $title, $content)
    {
        echo 'Do not use this old method. Use new HTML code instead.';
    }

    /**
     * Display documentations links.
     */
    public function documentation_link_box(){
        ?>
        <div class="ehssl-blue-box">
            You can view the <a href="https://www.tipsandtricks-hq.com/wordpress-easy-https-redirection-plugin" target="_blank">Easy HTTPS Redirection & SSL</a> plugin details on our site.
            Check out our other <a href="https://www.tipsandtricks-hq.com/development-center" target="_blank">WordPress plugins</a>.
        </div>
        <?php
    }
}