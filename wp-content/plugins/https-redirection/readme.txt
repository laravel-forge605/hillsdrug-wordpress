=== Easy HTTPS Redirection (SSL) ===
Contributors: Tips and Tricks HQ
Donate link: https://www.tipsandtricks-hq.com/development-center
Tags: ssl, https, force ssl, insecure content, redirection, automatic redirection, htaccess, https redirection, ssl certificate, secure page, secure, force https
Requires at least: 6.5
Tested up to: 7.1
Stable tag: 2.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The plugin allows an automatic redirection to the "HTTPS" version/URL of the site. Make your site SSL compatible easily.

== Description ==

= Only use this plugin if you have installed SSL certificate on your site and HTTPS is working correctly =

Once you've installed an SSL certificate on your site, it's important to ensure that your webpages are accessed via their secure HTTPS URLs.

To improve SEO and user security, you want search engines and visitors to always use the HTTPS version of your pages. This plugin makes that easy by automatically redirecting users to the HTTPS version whenever they try to access the non-HTTPS (HTTP) version of a page.

=== Example ===

Let's say you want to ensure the following page is always accessed over HTTPS:

`https://www.example.com/checkout`

If a visitor tries to access:

`http://www.example.com/checkout`

The plugin will automatically redirect them to the secure version:

`https://www.example.com/checkout`

This ensures that visitors always access the HTTPS version of your pages or site.

You can choose to automatically redirect your entire domain to HTTPS, or selectively apply HTTPS redirection to specific pages.

=== Video Tutorials ===

https://www.youtube.com/watch?v=oyJgRFCM6u8

https://www.youtube.com/watch?v=LtyBraB64v8

=== Force Load Static Files Using HTTPS ===

If you started using SSL from day 1 of your site then all your static files are already embedded using HTTPS URL. You have no issue there.

However, if you have an existing website where you have a lot of static files that are embedded in your posts and pages using NON-HTTPS URL then you will need to change those. Otherwise, the browser will show an SSL warning to your visitors.

This plugin has an option that will allow you to force load those static files using HTTPS URL dynamically. 

This will help you make the webpage fully compatible with SSL.

=== Mixed Content Scanner & Database URL Fixer ===

After switching to HTTPS, your pages can still trigger browser "mixed content" warnings if old HTTP URLs remain saved in your database (in post content, custom post types, post meta, or WordPress options). This plugin includes a built-in scanner to find and update those insecure URLs to their HTTPS version - no manual database editing required.

Available from the **Mixed Contents** settings tab, the scanner lets you:

* **Scan selected post types** - Choose exactly which post types to scan and update, including posts, pages, and any custom post types registered by your other plugins (products, orders, downloads, subscriptions, and more).
* **Update other database tables** - Optionally include the WordPress options table in the scan.
* **Include post meta** - Extend the scan to post meta for the selected post types.
* **Choose your scan scope** - Run a quick "Scan Static Resources Only" pass, or a thorough "Scan All" to catch every non-HTTPS reference.

This makes cleaning up legacy HTTP links straightforward, helping you achieve a fully secure padlock with no mixed content errors.

=== SSL Certificate Expiry Notification ===

This plugin includes a feature that allows you to receive email notifications when your SSL certificate is about to expire. It helps ensure your website remains secure and accessible over HTTPS.

You can configure the recipient email address and specify how many days in advance the notification should be sent. By default, the notification is sent 7 days before expiry, but you can adjust this to suit your preference.

This feature is especially useful for site owners who may not frequently check their SSL status, or for those managing multiple websites. By receiving timely alerts, you can renew your SSL certificate in advance and prevent potential downtime or security warnings.

=== HTTP Strict Transport Security (HSTS) Support ===

Easy HTTPS Redirection includes built-in support for sending the HTTP Strict Transport Security (HSTS) response header.

HSTS instructs compatible web browsers to automatically access your website over HTTPS after a visitor has successfully visited your secure site. This helps strengthen your site's HTTPS enforcement and reduces the risk of users accidentally accessing the HTTP version of your website.

The plugin allows you to:

* Enable or disable the HSTS response header with a simple checkbox.
* Configure the HSTS max-age value.
* Apply the HSTS policy to all subdomains using the `includeSubDomains` directive.
* Include the `preload` directive for sites that intend to submit their domain to the browser HSTS preload list.

=== Features ===
* Automatically redirect all HTTP traffic to HTTPS
* Option to force HTTPS on the entire site
* Option to selectively apply HTTPS redirection to specific pages
* Helps search engines index the secure versions of your pages
* Improves site security and user trust
* Force load static files (images, js, css etc) using a HTTPS URL
* Built-in mixed content scanner to find and update non-HTTPS URLs across post content, post meta, custom post types, and WordPress options
* SSL certificate expiry notification - Option to send SSL expiry notifications to a specific email address
* Easily see which SSL certificates on your site are approaching their expiry date.
* HTTP Strict Transport Security (HSTS) support with configurable max-age, includeSubDomains, and preload options.

View more details on the [HTTPS Redirection plugin](https://www.tipsandtricks-hq.com/wordpress-easy-https-redirection-plugin) page.

== Installation ==

1. Upload `https-redirection` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. The plugin settings can be found in the left-hand menu of the Admin Dashboard under 'Easy HTTPS & SSL'.

== Frequently Asked Questions ==

= How will the plugin work with the existing .htaccess file?=

If the file exists, the plugin will update existing .htaccess file.

= What should I do if the .htaccess file does not exist? =

The plugin will store the settings in the database and add all the necessary conditions to the settings of WordPress automatically.

= What should I do if after making changes in the .htaccess file with the help of the plugin my site stops working? =

The.htaccess is located in the site root. With your FTP program or via cPanel go to the site root, open the .htaccess file and delete the necessary strings manually.
Please make use of the following information: https://codex.wordpress.org/FTP_Clients

= How to use the other language files with the HTTPS Redirection? = 

Here is an example for German language files.

1. In order to use another language for WordPress it is necessary to set the WP version to the required language and in configuration wp file - `wp-config.php` in the line `define('WPLANG', '');` write `define('WPLANG', 'de_DE');`. If everything is done properly the admin panel will be in German.

2. Make sure that there are files `de_DE.po` and `de_DE.mo` in the plugin (the folder languages in the root of the plugin).

3. If there are no such files it will be necessary to copy other files from this folder (for example, for Russian or Italian language) and rename them (you should write `de_DE` instead of `ru_RU` in the both files).

4. The files are edited with the help of the program Poedit - https://poedit.net/download - please load this program, install it, open the file with the help of this program (the required language file) and for each line in English you should write translation in German.

5. If everything has been done properly all the lines will be in German in the admin panel and on frontend.

== Screenshots ==

1. Plugin settings page.

== Changelog ==

= v2.0.2 =
* Security improvement: Added capability and nonce checks to mixed-content scanner AJAX actions to prevent unauthorized access to scan results and URL updates. Thanks to Mohammed for reporting this issue.
* Security improvement: Added capability and nonce checks to dashboard widget order saving.
* Plugin check reported error issues fixed.

= v2.0.1 =
- New feature: Mixed content scanner tool to find and update non-HTTPS URLs.
- Added support for sending the HTTP Strict Transport Security (HSTS) response header via the plugin settings.
- WordPress 7 related UI fix.
- Fixed a small PHP deprecation warning.

= v2.0.0 =
- The plugin has gone through significant updates and improvements in this version.
- If you have any issues after you upgrade to this version, please roll back to the previous version and contact us for support.
- Here is the download link for the previous version: https://downloads.wordpress.org/plugin/https-redirection.1.9.2.zip
- The plugin now has it's own admin menu labeled "Easy HTTPS & SSL".
- Added a new option to send SSL expiry notifications to a specific email address.
- Added a new option to specify how many days in advance the notification should be sent.
- Added debug logging feature.
- Updated the translation POT file.

= v1.9.2 =
- Added rule to handle sites that are sitting behind a reverse-proxy. Thanks to @canadiannaginata for pointing it out.

= v1.9.1 =
- WP 5.3 warning fix for the add_submenu_page() function call. Thanks to @vfontj for pointing this out.

= v1.9 =
- WP Fastest Cache cache is automatically cleared when plugin settings are changed. This is to prevent "mixed content" warning from browsers.
- Fixed rare conflict with WP Fastest Cache (thanks to emrevona).

= v1.8 =
- Apply HTTPS redirection on the whole domain will be the default selected option after plugin install. You an change this option when you actually go to enable the feature.

= v1.7 =
- Additional options are only accessible when "Enable automatic redirection to the "HTTPS" is enabled.
- https://www.yoursite.com/some-page is replaced with site's actual https address in Settings information box.
- Added reminder for user to clear cache of optimization plugins similar to W3 Total Cache or WP Super Cache.

= v1.6 =
- Improved the "Force Load Static Files Using HTTPS" feature.
- The htaccess redirection is now detected based on SERVER_PORT (this is should work better on most servers).

= v1.5 =
- WordPress 4.6 compatibility.

= v1.4 =
- Improved the settings area to only show the options if pretty permalink feature is enabled.

= v1.3 =
- Updated the htaccess rules for HTTPS redirection to be more robust to prevent errors on some servers.

= v1.2 =
- Added a new option to automatically force load static files using HTTPS URL.

= v1.1 =
- Fixed a bug with the settings page.

= v1.0 =
* First commit to WordPress repository

== Upgrade Notice ==

None