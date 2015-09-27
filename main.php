<?php
    if (!class_exists('WP_List_Table')) {
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
    }

    class Events_List extends WP_List_Table {
       /**
        * Constructor, we override the parent to pass our own arguments
        * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
        */
        function __construct() {
           parent::__construct( array(
          'singular'    => __( 'Event', 'sp' ), //Singular label
          'plural'      => __( 'Events', 'sp' ), //plural label, also this well be one of the table css class
          'ajax'        => false
          ));
        }

        public static function get_events( $per_page = 5, $page_number = 1 ) {
            global $wpdb;
            $sql = "SELECT * FROM {$wpdb->prefix}wpae_events";

            if (!empty( $_REQUEST['orderby'])) {
                $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
                $sql .= ! empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
            }

            $sql .= " LIMIT $per_page";
            $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

            $result = $wpdb->get_results( $sql, 'ARRAY_A' );

            return $result;
        }

        public static function delete_event($id) {
            global $wpdb;

            $wpdb->delete(
                "{$wpdb->prefix}wpae_events",
                ['ID' => $id],
                ['%d']
            );
        }

        public static function record_count() {
            global $wpdb;
            $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}wpae_events";
            return $wpdb->get_var( $sql );
        }

        public function no_items() {
            _e( 'No events avaliable.', 'sp' );
        }

        function column_name($item) {
            echo 'here?';
            die();

            // create a nonce
            $delete_nonce = wp_create_nonce('sp_delete_event');
            $title = '<strong>' . $item['name'] . '</strong>';

            $actions = [
                'delete' => sprintf('<a href="?page=%s&action=%s&event=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['ID']), $delete_nonce)
            ];

            return $title.$this->row_actions($actions);
        }

        public function column_default($item, $column_name) {
            echo '<pre>'.print_r($item, true).'</pre>';
            echo '<pre>'.print_r($column_name, true).'</pre>';
            return $item[$column_name];

            switch ($column_name) {
                case 'name':
                return $item[$column_name];
                default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
            }
        }

        function column_cb($item) {
            return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']);
        }

        function get_columns() {
            $columns = [
                'cb'      => '<input type="checkbox" />',
                'name'    => __('Name', 'sp')
            ];

            return $columns;
        }

        public function get_sortable_columns() {
            $sortable_columns = array(
                'name' => array( 'name', true )
            );
            return $sortable_columns;
        }

        public function get_bulk_actions() {
            $actions = [
                'bulk-delete' => 'Delete'
            ];

            return $actions;
        }

        public function prepare_items() {
            $this->_column_headers = $this->get_column_info();

            /** Process bulk action */
            $this->process_bulk_action();

            $per_page     = $this->get_items_per_page( 'events_per_page', 5 );
            $current_page = $this->get_pagenum();
            $total_items  = self::record_count();

            $this->set_pagination_args([
                'total_items' => $total_items, //WE have to calculate the total number of items
                'per_page'    => $per_page //WE have to determine how many items to show on a page
            ]);

            $this->items = self::get_events($per_page, $current_page);
        }



    }

    if(isset($_REQUEST['test'])) {
        var_dump($_REQUEST);
    }


$events_obj = new Events_List();
?>

<h2>Advanced Events</h2>
<form method="post">
    <?php
    $events_obj->prepare_items();
    $events_obj->display(); ?>
</form>




<form method="POST" action="<?php echo admin_url( 'admin.php?page='.$_REQUEST['page'] ); ?>">
    <input type="hidden" name="test" value="testing">
    <?php submit_button(); ?>
</form>
