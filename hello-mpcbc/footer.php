<?php
/** Render the selected native, editable Elementor footer. */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( class_exists( '\Elementor\Plugin' ) ) {
    $footer_id = hello_mpcbc_footer_template_id();
    if ( $footer_id ) echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $footer_id );
}
wp_footer();
?>
</body>
</html>
