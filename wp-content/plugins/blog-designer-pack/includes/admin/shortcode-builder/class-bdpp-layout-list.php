<?php
/**
 * Shortcode Layout List Class
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Taking some variables
$add_layout_page = add_query_arg( array('page' => 'bdpp-layout'), admin_url('admin.php') );

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BDPP_Layout_List extends WP_List_Table {

	/**
	 * Number of results to show per page
	 *
	 * @var int
	 */
	private $per_page = 15;

	/**
	 * Meta Prefix
	 */
	private $meta_prefix = BDP_META_PREFIX;

	function __construct() {

		// Set parent defaults
		parent::__construct( array(
									'singular'  => 'bdpp_layout',	// singular name of the listed records
									'plural'    => 'bdpp_layouts',	// plural name of the listed records
									'ajax'      => false			// does this table support ajax?
								));

		$this->per_page	= apply_filters( 'bdpp_layout_list_per_page', $this->per_page ); // Per page
	}

	/**
	 * Displaying shortcode templates
	 * Does prepare the data for displaying the products in the table.
	 * 
	 * @since 3.4.2
	 */
	function bdpp_get_layout_list_data() {

		// Taking some data
		$data			= array();
		$paged 			= ! empty( $_GET['paged'] )		? bdp_clean_number( $_GET['paged'] )	: 1;
		$orderby 		= isset( $_GET['orderby'] )		? urldecode( $_GET['orderby'] )			: 'ID';
		$order			= isset( $_GET['order'] )		? bdp_clean( $_GET['order'] )			: 'DESC';
		$search 		= isset( $_GET['s'] ) 			? bdp_clean( $_GET['s'] )				: null;
		$status			= isset( $_GET['post_status'] )	? bdp_clean( $_GET['post_status'] )		: 'any';

		$args = array(
						'post_type'			=> BDP_LAYOUT_POST_TYPE,
						'post_status'		=> $status,
						'posts_per_page'	=> $this->per_page,
						'paged'				=> $paged,
						'orderby'			=> $orderby,
						'order'				=> $order,
						's'					=> $search,
					);

		$args = apply_filters( 'bdpp_layout_list_query', $args );

		// Query to retrive data
		$list_data = new WP_Query( $args );

		if( ! empty( $list_data->posts ) ) {
			foreach( $list_data->posts as $layout_key => $layout ) {

				$layout->post_title 		= ! empty( $layout->post_title ) ? $layout->post_title : __('Layout', 'blog-designer-pack').' - '.$layout->ID;
				$layout->layout_shrt_type	= get_post_meta( $layout->ID, $this->meta_prefix.'layout_shrt_type', true );
			}
		}

		$result_arr['data']		= ! empty( $list_data->posts ) ? $list_data->posts : array();
		$result_arr['total']	= isset($list_data->found_posts) ? $list_data->found_posts : ''; // Total no of data

		return $result_arr;
	}

	/**
	 * Manage column data
	 * Default column for listing table
	 * 
	 * @since 3.4.2
	 */
	function column_default( $item, $column_name ) {

		switch( $column_name ) {
			case 'shortcode' :
				$column_data = '<span class="bdpp-shrt-preview">[bdpp_tmpl layout_id="'.esc_attr( $item->ID ).'"]</span>
								<span class="bdpp-copy" title="'.esc_attr__('Copy', 'blog-designer-pack').'" data-clipboard-text="'.esc_attr( '[bdpp_tmpl layout_id="'.esc_attr( $item->ID ).'"]' ).'"><i class="dashicons dashicons-admin-page"></i> <span class="bdpp-copy-success bdpp-hide">Copied!</span><span>';
				break;

			case 'layout_type' :
				$registered_shortcodes 	= bdp_registered_shortcodes();
				$column_data			= isset( $item->layout_shrt_type ) ? esc_html( $registered_shortcodes[ $item->layout_shrt_type ] ) : $item->layout_shrt_type;
				break;

			case 'enable' :
				$column_data = ( 'publish' == $item->post_status ) ? '<i class="dashicons dashicons-yes"></i>' : '<i class="dashicons dashicons-no"></i>';
				break;

			case 'date' :
				$column_data = sprintf(
					/* translators: 1: Post date, 2: Post time. */
					__( '%1$s at %2$s' ),
					/* translators: Post date format. See https://www.php.net/manual/datetime.format.php */
					get_the_time( __( 'Y/m/d' ), $item ),
					/* translators: Post time format. See https://www.php.net/manual/datetime.format.php */
					get_the_time( __( 'g:i a' ), $item )
				);
				break;

			default:
				$column_data = isset( $item->$column_name ) ? $item->$column_name : '';
				break;
		}
		return apply_filters( 'bdpp_layout_list_column_value', $column_data, $column_name, $item );
	}
	
	/**
	 * Handles checkbox HTML
	 * 
	 * @since 3.4.2
	 **/
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],	// Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/ $item->ID					// The value of the checkbox should be the record's id
		);
	}
	
	/**
	 * Manage Post Title Column
	 * 
	 * @since 3.4.2
	 */
	function column_name( $item ) {
		
		$edit_link 	= add_query_arg( array( 'page' => 'bdpp-layout', 'shortcode' => $item->layout_shrt_type, 'action' => 'edit', 'id' => $item->ID ), admin_url('admin.php') );
		$del_link	= add_query_arg( array( 'page' => 'bdpp-layouts', 'action' => 'delete', 'bdpp_layout' => $item->ID, '_wpnonce' => wp_create_nonce('bulk-bdpp_layouts') ), admin_url('admin.php') );

		$row_actions = array(
							'edit' 		=> sprintf( '<a class="bdpp-layout-edit-link" href="%s">'.__('Edit', 'blog-designer-pack').'</a>', $edit_link ),
							'delete'	=> sprintf( '<a class="bdpp-layout-del-link bdpp-confirm" href="%s">'.__('Delete', 'blog-designer-pack').'</a>', $del_link ),
						);

		$row_actions = apply_filters( 'bdpp_layout_list_row_actions', $row_actions, $item ); // Filter to add row actions

		// Row actions.
		$actions = $this->row_actions( $row_actions );

		$title = '<a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $item->post_title ) . '</a>';

		// Concatenate & return the results.
		return $title . $actions;
	}

	/**
	 * Display Columns
	 * Handles which columns to show in table
	 * 
	 * @since 3.4.2
	 */
	function get_columns() {
		
		$columns = array(
							'cb'			=> '<input type="checkbox" />',
							'name'			=> __( 'Title', 'blog-designer-pack' ),
							'shortcode' 	=> __( 'Shortcode', 'blog-designer-pack' ),
							'layout_type'	=> __( 'Layout Type', 'blog-designer-pack' ),
							'enable'		=> __( 'Live', 'blog-designer-pack' ),
							'date'			=> __( 'Date', 'blog-designer-pack' ),
						);
		return apply_filters('bdpp_layout_list_columns', $columns);
	}

	/**
	 * Sortable Columns
	 * Handles soratable columns of the table
	 * 
	 * @since 3.4.2
	 */
	function get_sortable_columns() {
		
		$sortable_columns = array(
								'name' => array( 'post_title', true ),
								'date' => array( 'date', false ),
							);
		
		return apply_filters( 'bdpp_layout_list_sortable_columns', $sortable_columns );
	}
	
	/**
	 * Message to show when no records in database table
	 *
	 * @since 3.4.2
	 */
	function no_items() {
		echo apply_filters( 'bdpp_layout_list_no_item_msg', esc_html__('No shortcode layout found.', 'blog-designer-pack') );
	}

	/**
	 * Retrieve the view types
	 *
	 * @access public
	 * @since 3.4.2
	 * @return array $views All the views available
	 */
	public function get_views() {

		$layout_counts	= wp_count_posts( BDP_LAYOUT_POST_TYPE );
		$enable_count	= isset( $layout_counts->publish )	? $layout_counts->publish : 0;
		$disable_count	= isset( $layout_counts->pending )	? $layout_counts->pending : 0;
		$all_counts		= ( $enable_count + $disable_count );
		$current		= isset( $_GET['post_status'] )		? bdp_clean( $_GET['post_status'] ) : '';
		$page_url		= add_query_arg( array('page' => 'bdpp-layouts'), admin_url('admin.php') );

		$views = array(
			'all'		=> sprintf( '<a href="%s" %s>%s</a>', $page_url, $current === 'all' || $current == '' ? ' class="current"' : '', __('All', 'blog-designer-pack' ) . ' <span class="count">(' . $all_counts . ')</span>' ),
			'publish'	=> sprintf( '<a href="%s" %s>%s</a>', add_query_arg( array( 'post_status' => 'publish' ), $page_url ), $current === 'publish' ? 'class="current"' : '', __('Enabled', 'blog-designer-pack' ) . ' <span class="count">(' . $enable_count . ')</span>' ),
			'pending'	=> sprintf( '<a href="%s" %s>%s</a>', add_query_arg( array( 'post_status' => 'pending' ), $page_url ), $current === 'pending' ? 'class="current"' : '', __('Disabled', 'blog-designer-pack' ) . ' <span class="count">(' . $disable_count . ')</span>' ),
		);

		return apply_filters( 'bdpp_layout_list_views', $views );
	}

	/**
	 * Show the search field
	 *
	 * @param string $text Label for the search box
	 * @param string $input_id ID of the search box
	 *
	 * @since 3.4.2
	 */
	public function search_box( $text, $input_id ) {

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		}
		?>
		<div id="bdpp-layout-list-search" class="bdpp-layout-list-search bdpp-clearfix">
			<p class="search-box">
				<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $text ); ?>:</label>
				<input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" />
				<?php submit_button( $text, 'button', false, false, array('id' => 'search-submit') ); ?>
			</p>
		</div><!-- end .bdpp-layout-list-search -->
		<?php
	}
	
	/**
	 * Bulk actions field
	 * Handles Bulk Action combo box values
	 * 
	 * @since 3.4.2
	 */
	function get_bulk_actions() {
		$actions = array(
							'delete' => __('Delete', 'blog-designer-pack')
						);
		return apply_filters('bdpp_layout_list_bulk_actions', $actions);
	}
	
	/**
	 * Add Filter for Sorting
	 * 
	 * @since 3.4.2
	 **/
	function extra_tablenav( $which ) {
		
		if( $which == 'top' ) {
			
			$html = '<div class="alignleft actions bdpp-layout-list-act">';
			
			// Action for third party filter
			ob_start();
			do_action( 'bdpp_layout_list_filter' );
			$html .= ob_get_clean();
			
			$html .= '</div><!-- end .bdpp-layout-list-act -->';
			
			echo $html; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}
	}
	
	/**
	 * Prepare Items to display
	 * 
	 * @since 3.4.2
	 **/
	function prepare_items() {
		
		// Get All, Hidden, Sortable columns
		$columns	= $this->get_columns();
		$hidden 	= array();
		$sortable 	= $this->get_sortable_columns();
		
		// Get final column header
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		// Get Data of particular page
		$data_res 	= $this->bdpp_get_layout_list_data();
		$data 		= $data_res['data'];

		// Get total count
		$total_items = $data_res['total'];

		// Get page items
		$this->items = $data;
		
		// Register pagination options and calculations.
		$this->set_pagination_args( array(
												'total_items'	=> $total_items,							// Calculate the total number of items
												'per_page'		=> $this->per_page,							// Determine how many items to show on a page
												'total_pages'	=> ceil( $total_items / $this->per_page )	// Calculate the total number of pages
											));
	}
}

// Create an instance of Log class
$bdpp_layout_list = new BDPP_Layout_List();

// Fetch, prepare, sort, and filter data
$bdpp_layout_list->prepare_items();
?>

<div class="wrap bdpp-layout-list-wrp">

	<h1 class="wp-heading-inline"><?php esc_html_e( 'All Layouts', 'blog-designer-pack' ); ?></h1>
	<a href="<?php echo esc_url( $add_layout_page ); ?>" class="page-title-action"><?php esc_html_e('Add New', 'blog-designer-pack'); ?></a>
	<hr class="wp-header-end">

	<?php
	if( ! empty( $_GET['message'] ) && $_GET['message'] == 1 ) {
		echo '<div class="updated notice notice-success is-dismissible">
				<p><strong>'.esc_html__('Shortcode layout(s) deleted successfully.', 'blog-designer-pack').'</strong></p>
			  </div>';
	}

	// Showing sorting links on the top of the list
	$bdpp_layout_list->views();
	?>

	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="bdpp-layout-list-form" class="bdpp-layout-list-form" method="get" action="">

		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
		<input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ); ?>" />
		
		<?php if( isset( $_GET['post_status'] ) ) { ?>
		<input type="hidden" name="post_status" value="<?php echo esc_attr( $_GET['post_status'] ); ?>" />
		<?php } ?>

		<?php
		// Search Title
		$bdpp_layout_list->search_box( esc_html__( 'Search', 'blog-designer-pack' ), 'bdpp-layout' );

		// Now we can render the completed list table
		$bdpp_layout_list->display();
		?>

	</form><!-- end .bdpp-layout-list-form -->

</div><!-- end .wrap -->