<?php
// One-time: writes content-source/translation-fields/ to Home 21, footer 16 and Give 65,
// and turns the 繁體/简体 Give pages (233, 236) into redirects to /give/?lang=…
// Run with WP-CLI eval-file after `node scripts/build-translation-fields.mjs`.
try {
	if ( untrailingslashit( home_url() ) !== 'http://mpcbc.local' ) throw new RuntimeException( 'Design A only.' );
	if ( ! function_exists( 'is_plugin_active' ) ) require_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( ! is_plugin_active( 'mpcbc-translations/mpcbc-translations.php' ) ) throw new RuntimeException( 'Activate MPCBC Translations first.' );
	$root = 'C:/Projects/mpcbc/content-source';
	foreach ( array( 21, 16, 65, 233, 236 ) as $id ) {
		if ( get_post_meta( $id, '_elementor_data', true ) !== file_get_contents( "$root/before-translation-fields/$id.json" ) ) {
			throw new RuntimeException( "Post $id changed since the backup; export it again and rebuild." );
		}
	}
	foreach ( array( 21, 16, 65 ) as $id ) {
		$json = file_get_contents( "$root/translation-fields/$id.json" );
		if ( ! is_array( json_decode( $json, true ) ) ) throw new RuntimeException( "Bad build output for $id." );
		update_post_meta( $id, '_elementor_data', wp_slash( $json ) );
	}
	foreach ( array( 233 => 'zh-hant', 236 => 'zh-hans' ) as $id => $locale ) {
		update_post_meta( $id, '_mpcbc_redirect_page', 65 );
		update_post_meta( $id, '_mpcbc_redirect_locale', $locale );
		delete_post_meta( $id, '_mpcbc_translation_group' );
		wp_update_post( array( 'ID' => $id, 'post_title' => 'Legacy redirect — ' . get_the_title( $id ) ) );
	}
	\Elementor\Plugin::$instance->files_manager->clear_cache();
	echo "Applied: Home 21, footer 16, Give 65; 233 and 236 now redirect to Give.\n";
} catch ( Throwable $e ) {
	fwrite( STDERR, $e->getMessage() );
	exit( 1 );
}
