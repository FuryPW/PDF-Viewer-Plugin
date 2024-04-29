<?php
/*
Plugin Name: PDF Viewer for WPBakery
Description: Embed a PDF viewer in WPBakery and select PDF from media library.
Version: 1.0
Author: Wesmont Flores
*/

// Enqueue Viewer.js scripts and styles
function pdf_viewer_enqueue_scripts() {
    wp_enqueue_style('viewerjs-css', plugin_dir_url(__FILE__) . 'assets/viewer.css');
    wp_enqueue_script('viewerjs', plugin_dir_url(__FILE__) . 'assets/viewer.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'pdf_viewer_enqueue_scripts');

// Shortcode to render PDF viewer using Viewer.js
function pdf_viewer_shortcode($atts) {
    $atts = shortcode_atts(array(
        'pdf_url' => '', // Change 'pdf' to 'pdf_url'
        'width' => '100%',
        'height' => '500px',
    ), $atts);

    $pdf_url = esc_url_raw($atts['pdf_url']); // Sanitize the PDF URL

    // Check if PDF URL is provided
    if (empty($pdf_url) && isset($_POST['pdf_id'])) {
        $pdf_id = $_POST['pdf_id'];
        $pdf_url = wp_get_attachment_url($pdf_id);
    }

    // Render the PDF viewer
    ob_start();
    ?>
    <div class="pdf-viewer" style="width: <?php echo esc_attr($atts['width']); ?>; height: <?php echo esc_attr($atts['height']); ?>;">
        <iframe src="<?php echo esc_url($pdf_url); ?>" width="100%" height="100%" style="border: none;"></iframe>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('pdf_viewer', 'pdf_viewer_shortcode');

// AJAX handler for selecting PDF from media library
add_action('wp_ajax_pdf_viewer_select_pdf', 'pdf_viewer_select_pdf_callback');
function pdf_viewer_select_pdf_callback() {
    if (!empty($_POST['pdf_id'])) {
        $pdf_url = wp_get_attachment_url($_POST['pdf_id']);
        echo esc_url($pdf_url);
    }
    wp_die();
}

// Register WPBakery element
if (function_exists('vc_map')) {
    vc_map(array(
        'name' => __('PDF Viewer', 'text-domain'),
        'base' => 'pdf_viewer',
        'category' => __('Content', 'text-domain'),
        'params' => array(
            array(
                'type' => 'attach_image',
                'heading' => __('Select PDF', 'text-domain'),
                'param_name' => 'pdf_select',
                'description' => __('Select PDF from media library.', 'text-domain'),
            ),
            array(
                'type' => 'textfield',
                'heading' => __('Width', 'text-domain'),
                'param_name' => 'width',
                'description' => __('Width of the PDF viewer.', 'text-domain'),
                'value' => '100%',
            ),
            array(
                'type' => 'textfield',
                'heading' => __('Height', 'text-domain'),
                'param_name' => 'height',
                'description' => __('Height of the PDF viewer.', 'text-domain'),
                'value' => '500px',
            ),
        ),
    ));
}
