<?php
/** Volunteer screen: Sermons -> Needs review (status box, Sync now, Re-check, per-row fixes). */
defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function () {
	$n = (int) wp_count_posts( 'sermon' )->pending;
	add_submenu_page( 'edit.php?post_type=sermon', 'Needs review', 'Needs review' . ( $n ? ' <span class="awaiting-mod">' . $n . '</span>' : '' ), 'edit_others_sermons', 'mpcbc-review', 'mpcbc_sermons_review_page' );
} );

function mpcbc_sermons_back( $msg, $type = 'success' ) {
	wp_safe_redirect( add_query_arg( array( 'mpcbc_msg' => rawurlencode( $msg ), 'mpcbc_type' => $type ), admin_url( 'edit.php?post_type=sermon&page=mpcbc-review' ) ) );
	exit;
}

function mpcbc_sermons_guard( $action ) {
	check_admin_referer( $action );
	if ( ! current_user_can( 'edit_others_sermons' ) ) wp_die( 'Not allowed.' );
}

add_action( 'admin_post_mpcbc_sync', function () {
	mpcbc_sermons_guard( 'mpcbc_sync' );
	if ( get_transient( 'mpcbc_sermons_sync_lock' ) ) mpcbc_sermons_back( 'Synced less than 5 minutes ago. Please wait a little.', 'warning' );
	set_transient( 'mpcbc_sermons_sync_lock', 1, 5 * MINUTE_IN_SECONDS );
	mpcbc_sermons_sync();
	mpcbc_sermons_back( 'Sync finished. See the result for each channel above.' );
} );

add_action( 'admin_post_mpcbc_recheck', function () {
	mpcbc_sermons_guard( 'mpcbc_recheck' );
	$done = 0;
	foreach ( get_posts( array( 'post_type' => 'sermon', 'post_status' => 'pending', 'numberposts' => -1, 'meta_query' => array( array( 'key' => '_mpcbc_edited', 'compare' => 'NOT EXISTS' ) ) ) ) as $p ) {
		$ev = mpcbc_sermons_evaluate( get_post_meta( $p->ID, '_mpcbc_raw_title', true ), get_post_meta( $p->ID, '_mpcbc_channel', true ) ?: 'zh' );
		mpcbc_sermons_apply( $p->ID, $ev, get_post_meta( $p->ID, '_mpcbc_raw_title', true ) );
		if ( $ev['clean'] ) $done++;
	}
	mpcbc_sermons_back( "Re-checked. $done record(s) now match and were published." );
} );

add_action( 'admin_post_mpcbc_review', function () {
	mpcbc_sermons_guard( 'mpcbc_review' );
	list( $id, $act ) = array_pad( explode( ':', sanitize_text_field( wp_unslash( $_POST['do'] ?? '' ) ) ), 2, '' );
	$id = absint( $id );
	if ( ! $id ) mpcbc_sermons_back( 'Nothing saved. Use the Publish, Hide or Save button on a row.', 'warning' ); // Enter key in a field
	if ( 'sermon' !== get_post_type( $id ) || ! current_user_can( 'edit_post', $id ) ) wp_die( 'Not allowed.' );
	$r        = isset( $_POST['r'][ $id ] ) ? (array) wp_unslash( $_POST['r'][ $id ] ) : array();
	$title    = sanitize_text_field( $r['title'] ?? '' );
	$ministry = array_values( array_intersect( array_map( 'sanitize_key', (array) ( $r['ministry'] ?? array() ) ), array_keys( MPCBC_SERMONS_MINISTRIES ) ) );
	$speaker  = absint( $r['speaker'] ?? 0 );
	$series   = absint( $r['series'] ?? 0 );
	if ( $speaker && ! term_exists( $speaker, 'speaker' ) ) $speaker = 0;
	if ( $series && ! term_exists( $series, 'series' ) ) $series = 0;

	if ( 'publish' === $act && ( '' === $title || ! $speaker || ! $ministry ) ) mpcbc_sermons_back( 'To publish, please fill in the title, congregation and speaker.', 'error' );
	wp_update_post( wp_slash( array( 'ID' => $id, 'post_title' => $title ?: get_the_title( $id ), 'post_status' => 'publish' === $act ? 'publish' : ( 'hide' === $act ? 'draft' : 'pending' ) ) ) );
	update_post_meta( $id, '_mpcbc_scripture', wp_slash( sanitize_text_field( $r['scripture'] ?? '' ) ) );
	update_post_meta( $id, '_mpcbc_edited', 1 ); // sync/re-check never overwrite after this
	if ( 'publish' === $act ) delete_post_meta( $id, '_mpcbc_note' );
	wp_set_object_terms( $id, $ministry, 'ministry' );
	wp_set_object_terms( $id, $speaker ? array( $speaker ) : array(), 'speaker' );
	wp_set_object_terms( $id, $series ? array( $series ) : array(), 'series' );
	mpcbc_sermons_back( array( 'publish' => 'Published.', 'hide' => 'Hidden (kept as a draft).' )[ $act ] ?? 'Saved.' );
} );

function mpcbc_sermons_review_page() {
	if ( ! current_user_can( 'edit_others_sermons' ) ) return;
	$post_url = esc_url( admin_url( 'admin-post.php' ) );
	echo '<div class="wrap"><h1>Sermons: needs review</h1>';
	if ( isset( $_GET['mpcbc_msg'] ) ) {
		$type = in_array( $_GET['mpcbc_type'] ?? '', array( 'success', 'warning', 'error' ), true ) ? $_GET['mpcbc_type'] : 'success';
		echo '<div class="notice notice-' . esc_attr( $type ) . '"><p>' . esc_html( sanitize_text_field( wp_unslash( rawurldecode( $_GET['mpcbc_msg'] ) ) ) ) . '</p></div>';
	}

	// Status box
	$status = get_option( 'mpcbc_sermons_sync_status', array() );
	echo '<div class="card" style="max-width:none"><h2 style="margin-top:0">YouTube sync</h2><ul>';
	foreach ( MPCBC_SERMONS_CHANNELS as $key => $ch ) {
		$s = $status[ $key ] ?? null;
		echo '<li><strong>' . esc_html( $ch['name'] ) . ':</strong> ' . ( $s
			? esc_html( ( $s['ok'] ? 'OK' : 'Failed' ) . ' - ' . $s['msg'] . ' (' . human_time_diff( $s['time'] ) . ' ago)' ) : 'not synced yet' ) . '</li>';
	}
	echo '</ul><p>The site checks YouTube automatically every hour.</p>';
	foreach ( array( 'mpcbc_sync' => 'Sync now', 'mpcbc_recheck' => 'Re-check needs review' ) as $action => $label ) {
		echo '<form method="post" action="' . $post_url . '" style="display:inline-block;margin-right:8px"><input type="hidden" name="action" value="' . $action . '">';
		wp_nonce_field( $action );
		submit_button( $label, 'secondary', 'submit', false );
		echo '</form>';
	}
	echo '<p class="description">Added a new speaker, series or alias? Press Re-check to publish records that now match. Sync now works once every 5 minutes.</p></div>';

	// Records waiting
	$posts = get_posts( array( 'post_type' => 'sermon', 'post_status' => 'pending', 'numberposts' => 50, 'orderby' => 'date', 'order' => 'DESC' ) );
	if ( ! $posts ) { echo '<p>Nothing is waiting for review.</p></div>'; return; }
	$terms = array();
	foreach ( array( 'speaker', 'series' ) as $tax ) $terms[ $tax ] = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false ) );
	echo '<p>Fix what is missing, then press Publish. Hide keeps a record out of the website. To add a speaker or series, use <a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=speaker&post_type=sermon' ) ) . '">Speakers</a> or <a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=series&post_type=sermon' ) ) . '">Series</a> (put every spelling in Aliases).</p>';
	echo '<form method="post" action="' . $post_url . '"><input type="hidden" name="action" value="mpcbc_review">';
	wp_nonce_field( 'mpcbc_review' );
	echo '<button type="submit" name="do" value="0:" hidden></button>'; // makes Enter harmless
	echo '<table class="widefat striped"><thead><tr><th>Video</th><th>YouTube title and note</th><th>Fix</th><th></th></tr></thead><tbody>';
	foreach ( $posts as $p ) {
		$id    = $p->ID;
		$vid   = get_post_meta( $id, '_mpcbc_video_id', true );
		$mins  = wp_get_object_terms( $id, 'ministry', array( 'fields' => 'slugs' ) );
		$cur   = array( 'speaker' => wp_get_object_terms( $id, 'speaker', array( 'fields' => 'ids' ) ), 'series' => wp_get_object_terms( $id, 'series', array( 'fields' => 'ids' ) ) );
		echo '<tr><td><a href="' . esc_url( mpcbc_sermons_url( $vid ) ) . '" target="_blank" rel="noopener"><img src="' . esc_url( mpcbc_sermons_thumb( $vid ) ) . '" width="120" alt=""></a></td>';
		echo '<td><strong>' . esc_html( get_post_meta( $id, '_mpcbc_raw_title', true ) ) . '</strong><br>' . esc_html( get_the_date( 'Y-m-d', $p ) );
		echo '<br><em>' . esc_html( get_post_meta( $id, '_mpcbc_note', true ) ?: 'Please check' ) . '</em></td><td>';
		echo '<p><label>Title <input type="text" class="large-text" name="r[' . $id . '][title]" value="' . esc_attr( $p->post_title ) . '"></label></p>';
		echo '<p><label>Scripture <input type="text" name="r[' . $id . '][scripture]" value="' . esc_attr( get_post_meta( $id, '_mpcbc_scripture', true ) ) . '"></label></p><p>';
		foreach ( MPCBC_SERMONS_MINISTRIES as $slug => $m ) echo '<label style="margin-right:10px"><input type="checkbox" name="r[' . $id . '][ministry][]" value="' . $slug . '"' . checked( in_array( $slug, $mins, true ), true, false ) . '> ' . esc_html( $m[0] ) . '</label>';
		echo '</p><p>';
		foreach ( array( 'speaker' => 'Speaker', 'series' => 'Series' ) as $tax => $label ) {
			echo '<label>' . $label . ' <select name="r[' . $id . '][' . $tax . ']"><option value="0">-</option>';
			foreach ( $terms[ $tax ] as $t ) echo '<option value="' . (int) $t->term_id . '"' . selected( in_array( $t->term_id, $cur[ $tax ], true ), true, false ) . '>' . esc_html( $t->name ) . '</option>';
			echo '</select></label> ';
		}
		echo '</p></td><td>';
		foreach ( array( 'publish' => array( 'Publish', 'primary' ), 'hide' => array( 'Hide', 'secondary' ), 'save' => array( 'Save', 'secondary' ) ) as $act => $b ) {
			echo '<p><button type="submit" name="do" value="' . $id . ':' . $act . '" class="button button-' . $b[1] . '">' . $b[0] . '</button></p>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table></form></div>';
}
