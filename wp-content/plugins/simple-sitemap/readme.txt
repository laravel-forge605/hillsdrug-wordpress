=== Simple Sitemap – Responsive HTML Sitemap for WordPress ===
Contributors: dgwyer, wpgoplugins
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FBAG4ZHA4TTUC
Tags: html sitemap, sitemap, responsive sitemap, seo sitemap, block
Requires at least: 6.3
Requires PHP: 7.4
Tested up to: 7.1
Stable tag: 3.9.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Build a responsive HTML sitemap in the block editor with live preview, flexible sorting, tabs, and shortcode support.

== Description ==

Simple Sitemap adds a visible, visitor-friendly HTML sitemap to your WordPress
site. Add a block, choose what to show, and preview the result directly in the
editor before you publish.

It is designed for HTML sitemaps that help people browse your content. It does
not replace the XML sitemap used by search engines.

= Build your sitemap in the editor =

The free edition includes:

* Three editor blocks: Simple Sitemap, Simple Sitemap Group, and Child Pages.
* Live preview while you configure the sitemap.
* Standard lists and responsive tabbed layouts.
* Posts and pages with ascending or descending ordering.
* Sorting by title, author, date, ID, menu order, comment count, or random order.
* Optional excerpts, post-type labels, and linked or plain-text items.
* Include only selected posts or pages, or exclude individual IDs.
* Display a published child-page hierarchy for a selected parent page.
* Optional linked pagination for content lists and individual grouped sections.
* Page-depth and parent-page-link controls.
* Multiple sitemaps on the same page.
* Global item and nested-level spacing presets, with optional per-block and
  per-shortcode overrides.
* An optional linked parent at the root of the Child Pages block.
* The `[simple-sitemap]` and `[simple-sitemap-group]` shortcodes for classic
  content and page-builder workflows.

Sitemap styles load only on pages that contain a sitemap. Each sitemap also
receives its own ID, so multiple instances can be configured and styled
independently.

= Go further with Simple Sitemap Pro =

[Explore Simple Sitemap Pro](https://wpgoplugins.com/plugins/simple-sitemap/)
when you need more control for a larger or more structured site.

Pro adds:

* Optional visitor search for Content and Grouped sitemaps, using blocks or
  shortcodes. Visitors can filter the entries currently displayed while
  matching items retain their hierarchy or topic context.
* Custom post types and custom taxonomies.
* Taxonomy-term filters and automatic exclusion of child pages.
* Advanced responsive tab controls and horizontal layouts.
* Dedicated Taxonomy Terms, Navigation Menu, Archive Links, and WooCommerce
  Product Sitemap blocks.
* Enhanced Child Pages controls for subtree exclusions, excerpts, images, and
  nofollow links, including hierarchical custom post types.
* Optional separators and compact featured-image thumbnails for child-page
  sitemaps, including the `[simple-sitemap-child]` shortcode.
* Per-section Content Sitemap ordering, limits, and include/exclude rules.
* Optional noindex filtering for Yoast SEO, Rank Math, and SEOPress, plus
  current-language filtering for WPML and Polylang.
* Additional shortcodes for child pages, taxonomy terms, and navigation menus.
* Interface controls for sitemap colours and presentation.
* Filters for custom development requirements.

Both editions keep the same WordPress-first workflow, so you can start with the
free plugin and upgrade when the site requires the additional controls.

[Try Pro visitor search in the live demo](https://demo.wpgoplugins.com/simple-sitemap/grouped-sitemap-block/#simple-sitemap-container-demo-grouped-search).
When linked pagination is enabled, search filters the current result page;
it does not search other result pages or the whole site.

== Installation ==

1. In WordPress, go to Plugins > Add New and search for Simple Sitemap.
2. Install and activate the plugin.
3. Edit a page and add the Simple Sitemap, Simple Sitemap Group, or Child Pages
   block.
4. Choose the content, ordering, labels, and layout in the block settings.
5. Publish or update the page.

For classic content or a page builder, use `[simple-sitemap]` or
`[simple-sitemap-group]` instead.

== Frequently Asked Questions ==

= Where can I see the available settings? =

Select a sitemap block and review its settings in the editor sidebar. Licensed
users can also use [WPGO premium support](https://wpgoplugins.com/premium-plugin-support/).

= Can I generate an XML sitemap too? =

No. This plugin creates a visible HTML sitemap for visitors. WordPress or an SEO
plugin can continue handling your XML sitemap for search engines.

= Do the shortcodes still work? =

Yes. The blocks provide the visual editor workflow, while the existing
`[simple-sitemap]` and `[simple-sitemap-group]` shortcodes remain available.

= What is included in Pro? =

Simple Sitemap Pro adds optional visitor search, custom post types and
taxonomies, taxonomy-term and child-page filtering, advanced responsive and
horizontal layouts, extra sitemap
tools, and styling controls. Basic post/page ID filtering is included in Free.
[Compare the editions](https://wpgoplugins.com/plugins/simple-sitemap/).

= Did basic post and page exclusions move to Pro? =

No. Basic include-only and exclude-by-ID controls are available in the Free
edition for both blocks and shortcodes. Sites upgrading from a release where
those controls were unavailable can keep their existing sitemap content and
configure the restored controls without purchasing Pro.

= How do I manage or cancel a Pro renewal? =

Open Simple Sitemap > Account in WordPress or use the secure customer account
link in your purchase email to manage billing and renewals. Deactivating a
license on a site does not cancel its subscription. If you need help, contact
[WPGO premium support](https://wpgoplugins.com/premium-plugin-support/).

= Why does WordPress say the plugin cannot be deleted because it is active on the main site? =

That message comes from WordPress Multisite. In Network Admin, deactivate Simple
Sitemap for the network and confirm it is not active on the main site before
deleting it. Deactivating a license under Simple Sitemap > Account is separate
from deactivating the plugin under Plugins.

== Screenshots ==

1. Configure a standard sitemap and preview it directly in the editor.
2. Switch to a tabbed sitemap from the same block settings.
3. Responsive sitemap tabs adapt to narrow screens.
4. Group posts by a category, tag, or another available taxonomy.
5. Publish a clear HTML list of posts and pages for visitors.
6. Display the tabbed layout on the front end.
7. Simple Sitemap Pro adds compact horizontal sitemap layouts.
== Changelog ==
= 3.9.2 - September 30, 2026 =

* [new] Added a ready-made paginated posts starter to the plugin Home screen. The starter creates a private draft for review.

= 3.8.0 - September 1, 2026 =

* [new] Added more ready-made Home starters for recent posts, summaries, combined content, grouped posts, and child Pages. Every starter creates a private draft.
* [new] Added clearer editor guidance when a Content or Child Pages block has no content to preview.
* [new] Added links to ChartQuill and TableQuill for users who also need to present data as charts or tables.

= 3.7.0 - August 21, 2026 =

* [new] Added global Compact, Comfortable, and Custom sitemap spacing controls, plus per-block and per-shortcode item and nested-level overrides.
* [new] Added an optional linked parent at the root of the Child Pages block.
* [update] Unified spacing inheritance across every included sitemap block and shortcode while retaining existing output until a setting or instance override is selected.
* [update] Expanded the product guide, complete reference, product page, and live demos for the new controls.

= 3.6.3 - August 20, 2026 =

* [new] Replaced the legacy welcome screen with a professional Home dashboard, guided sitemap creation, Quick Start steps, and clear Free feature actions.
* [new] Added direct links from Pro feature cards to matching live examples and a concise feedback prompt with a Simple Sitemap feedback summary ready to send.
* [update] Improved sitemap spacing, hierarchy, tab presentation, link contrast, and responsive readability without changing saved content or renderer markup.

= 3.6.2 - August 11, 2026 =

* [update] Made the plugin self-contained, with no separate framework plugin required.
* [new] Basic include/exclude post and page ID filters are now available in the Free edition.
* [new] Added searchable content selection and a dedicated Child Pages block.
* [new] Added opt-in bounded linked pagination to Content and Grouped Sitemap blocks and shortcodes.
* [new] Added privacy-conscious support diagnostics and clearer support destinations.
* [new] Added three starter patterns and a task-first Quick Start for creating a sitemap page.
* [fix] Improved output escaping and guarded malformed saved block settings to prevent editor failures.
* [fix] Restored the taxonomy upgrade prompt and corrected accessible admin/editor controls.
* [fix] Added semantic keyboard and ARIA behavior to tabbed sitemaps without changing their saved content or server-rendered HTML.
* [fix] Kept custom post-type labels attached to the selected post type in the editor.
* [update] Improved Child Pages previews and compatibility with the current block editor.

= 3.6.1 - May 20, 2025 =

* [fix] Updated nonce checks on post duplication feature.

= 3.6.0 - May 7, 2025 =

* [fix] Localization issues.
* [fix] Corrected validation of saved settings.

= 3.5.14 - Jun 7, 2024 =

* [update] Updated plugin to be compatible with WordPress 6.5.
* [fix] Included reported security fixes.
* [fix] Fixed sitemap group block/shortcode.

= 3.5.12 - Feb 19, 2024 =

* [fix] Restored reliable sitemap previews in the block editor.

= 3.5.11 - Feb 17, 2024 =

* [fix] Corrected a missing editor dependency.

= 3.5.10 - July 26, 2023 =

* [fix] Included a security update for licensing and plugin updates.

= 3.5.9 - January 11, 2023 =

* [fix] Security related fixes and updates.

= 3.5.8 - January 1, 2023 =

* [new] Allow post type labels to be customized via the sitemap block.
* [fix] Fixed bug affecting include/exclude term attribute. Affected edge cases when category slugs used with 'hyphens'.
* [fix] Enqueue error on admin widgets page.
* [fix] Sanitize non-supported sitemap shortcode attributes.
* [update] Sticky Post was always included when including a single post in a sitemap shortcode or block.

= 3.5.7 - July 15, 2022 =

* [fix] Styles not enqueued for sitemap shortcodes.
* [update] Added compatibility with WP 6.x.

= 3.5.6 - July 12, 2022 =

* You can now override the dynamically generated sitemap ID with a static ID of your choosing.
* Block CSS is now only loaded on pages that include a sitemap block.

= 3.5.5 - March 4, 2022 =

* Included a security update for licensing and plugin updates.

= 3.5.4 - November 23, 2021 =

* Added new 'Random Order', 'Menu Order', and 'Comment Count' options to the sitemap block Orderby drop down.
* Added new 'num_terms' shortcode attribute to optionally limit the number of taxonomy terms displayed.
* Added a preview for sitemap and sitemap group blocks in the editor window.

= 3.5.3 - November 6, 2021 =


= 3.5.2 - February 26, 2021 =

* Fixed compatibility issue with PHP 8.0.

= 3.5.1 - August 29, 2020 =

* Improved sitemap block rendering (using new ServerSideRender component).

= 3.5 - February 25, 2020 =

* FIX: Nofollow links were always enabled. These are now OFF by default and can optionally be enabled if required. 

= 3.4 - August 10, 2019 =

* FIX: Undefined index 'page_excerpt_length'.

= 3.3 - July 22, 2019 =

* NEW: Added details to plugin settings page about a recent update that included breaking changes.
* Fix: Page depth setting not saving.
* Fix: Plugin settings heading styles broken because of EDD license renewal nag notice.

= 3.2 - July 12, 2019 =

* Some minor bug fixes and improvements.

= 3.1 - July 12, 2019 =

* Complete plugin overhaul.
* Two new blocks added to replace existing shortcodes.
* Shortcodes are still available though.
* Tab support added to sitemaps!
* New plugin options available via settings page.

= 2.7 - March 6, 2019 =

* Added link to sitemap plugin settings page directly from main plugin index page.
* Removed redirect to settings page after activation.
* Removed admin notice popup displayed after activation.

= 2.6 - September 21, 2018 =

* Fixed bug with [simple-sitemap-group] shortcode. Was previously buffering output twice causing display issues.

= 2.4 - OCTOBER 9, 2017 =

* Added live sitemap demo gallery on plugin settings page.

= 2.3 - SEPTEMBER 25, 2017 =

* New 'container_tag' shortcode attribute added to all shortcodes to output the sitemap as an ordered list, or unordered list. See plugin settings page for more information.
* Added clearer shortcode information for the new <code>[simple-sitemap-group]</code> shortcode.
* Improved shortcode attribute validation checks.

*2.1*

* Fixed broken image links on plugin settings page.

*1.9 update*

* Fixed compatibility bug with WordPress 4.7.

*1.83 update*

* Updated the docs in plugin options for the 'orderby' shortcode attribute. A link to the full list of available attributes is included.

*1.82 update*

* Better security.
* Fix: Some pretty permalinks weren't being displayed properly for posts.

*1.8 update*

* Plugin completely rewritten to include a range of shortcode attributes to make rendering the sitemap much more flexible!
* All previous plugin options removed from the plugin settings page. Use the new shortcode attributes instead. See the plugin settings page for full deatils.
* New, cleaner HTML and CSS. New CSS classes used.

*1.7 update*

* Translation support added!

*1.63*

* Fixed bug with CPT links.

*1.62*

* Sitemap shortcode now works in text widgets.

*1.61*

* Fixed bug limiting CPT posts to displaying a maximum of 5 each.

*1.6*

* Links on Plugins page updated.
* Removed front end drop downs. Sitemap rendering now solely controlled via plugin settings.
* Support for Custom Post Types added!

*1.54*

* Security issue addressed.

*1.53*

* All functions now properly name-spaced.
* Added $wpdb->prepare() to SQL query.

*1.52*

* Now works nicely in sidebars (via a Text widget)!
* Fixed bug where existing Plugin users saw no posts/pages on the sitemap after upgrade to 1.51.
* Added a 'Settings' link to the main Plugins page, next to the 'Deactivate' link to allow easy navigation to the Simple Sitemap Plugin options page.

*1.5*

* Updated for WordPress 3.5.1.
* Minor CSS bug fixed.
* ALL Plugin styles affecting the sitemap have been removed to allow the current theme to control the styles. This enables the sitemap to blend in with the current theme, and allows for easy customisation of the CSS as there are plenty of sitemap classes to hook into.
* All sitemap content is now listed in a single column to allow for additional listings for CPT to be added later.
* New Plugin options to show/hide posts or pages.

*1.4*

* Plugin option added to exclude pages by ID!
* Bug fix: ALL posts are now listed and are not restricted by the Settings -> Reading value.

*1.3.1*

* Fixed HTML bug. Replaced deprecated function.

*1.3*

* Dropdown sort boxes on the front end now work much better in all browsers. Thanks to Matt Bailey for this fix.

*1.28*

* Changed the .sticky CSS class to be .ss_sticky to avoid conflict with the WordPress .sticky class.

*1.27*

* Fixed minor bug in 'Posts' view, when displaying the date. There was an erroneous double quotes in the dates link.

*1.26*

* Fixed CSS bug. Was affecting the size of some themes Nav Menu font sizes.

*1.25*

* Now supports WordPress 3.0.3
* Fixed issue: http://wordpress.org/support/topic/plugin-simple-sitemap-duplicated-id-post_item
* Fixed issue: http://wordpress.org/support/topic/plugin-simple-sitemap-empty-span-when-post-is-not-sticky

*1.20*

* Added Plugin admin options page
* Fixed several small bugs
* Sitemap layout tweaked and generally improved
* Added new rendering of sitemap depending on drop-down options
* New options to sort by category, author, tags, and date improved significantly

*1.10 Fixed so that default permalink settings work fine on drop-down filter*

*1.01 Minor amendments*

*1.0 Initial release*
