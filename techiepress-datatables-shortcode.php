<?php
/**
 * Plugin Name: techiepress datatables shortcode
 * Plugin URI: https://github.com/yttechiepress/techiepress-datatables-shortcode
 * Author: Techiepress
 * Author URI: https://github.com/yttechiepress/techiepress-datatables-shortcode
 * Description: This plugin adds a shortcode Datatables embed on pages in WordPress
 * Version: 0.1.0
 * License: GPL2 or later.
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: techiepress-datatables-shortcode
*/

//Basic security.
defined( 'ABSPATH' ) or die( 'Unauthorized Access' );

/**
 * Add a menu page for Datatables.
 */
add_action( 'admin_menu', 'techiepress_dtsh_menu_item' );

function techiepress_dtsh_menu_item() {

    add_menu_page(
        __( 'Datatables Shortcode Results', 'techiepress-datatables-shortcode' ),
        __( 'Datatables', 'techiepress-datatables-shortcode' ),
        'manage_options',
        'techiepress-dtsh',
        'techiepress_dtsh_show_page',
        'dashicons-chart-line',
        16,
    );
}

/**
 * Add admin css and scripts for datatables
 */
add_action( 'admin_enqueue_scripts', 'techiepress_enqueue_scripts' );

function techiepress_enqueue_scripts( $hook ) {

    // Escape function if not on datatables page in admin.
    if ( 'toplevel_page_techiepress-dtsh' !== $hook ) {
        return;
    }

    wp_enqueue_style( 'dtsh_css', plugin_dir_url( __FILE__ ) . 'assets/css/jquery.dataTables.min.css', array(), '1.0.0', 'all' );
    wp_enqueue_script( 'dtsh_js', plugin_dir_url( __FILE__ ) . 'assets/js/jquery.dataTables.min.js', array( 'jquery' ), '1.0.0', true );
    wp_enqueue_script( 'dtsh_js_init', plugin_dir_url( __FILE__ ) . 'assets/js/init-tables.js', array( 'dtsh_js' ), '1.0.0', true );

}

/**
 * Add table to admin area.
 *
 * @return void
 */
function techiepress_dtsh_show_page() {

    /**
     * Add a call to the API provider to store the info in the WP_options table.
     */

    $url = 'https://raw.githubusercontent.com/Uganda-Open-Data/kalulu/master/district_lookup/uganda_districts_2020.json';
    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json'
        )
    );

    $data_option = get_option( 'techiepress_json_results' );
    // var_dump($data_option);

    if ( false == $data_option ) {
        $response = wp_remote_get($url, $args);
        $body = wp_remote_retrieve_body( $response );

        if ( NULL != $body || $body || !empty($body) ) {
            update_option( 'techiepress_json_results', $body );
        }
        return;
    }

    $data = json_decode( $data_option );

    // var_dump($data);


    ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Datatables Javascript in WordPress</h1>

            <table id="techiepress-datatables-shortcode" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>district code</th>
                        <th>district name</th>
                        <th>region code</th>
                        <th>region name</th>
                    </tr>
                </thead>
                <tbody>
                        <?php
                            foreach( $data as $data_line ) {
                                echo '<tr>';
                                echo '<td>' . $data_line->district_code . '</td>';
                                echo '<td>' . $data_line->district_name . '</td>';
                                echo '<td>' . $data_line->region_code . '</td>';
                                echo '<td>' . $data_line->region_name . '</td>';
                                echo '</tr>';
                            }
                        ?>
                </tbody>
                <!-- <tfoot>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Office</th>
                        <th>Age</th>
                        <th>Start date</th>
                        <th>Salary</th>
                    </tr>
                </tfoot> -->
            </table>
        </div>
    <?php
}