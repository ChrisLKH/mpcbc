<?php
/** One-time backfill, administrators only: Sermons -> Import past videos. */
defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function () {
	add_submenu_page( 'edit.php?post_type=sermon', 'Import past videos', 'Import past videos', 'manage_options', 'mpcbc-import', 'mpcbc_sermons_import_page' );
} );

const MPCBC_SERMONS_PAGES_PER_CLICK = 5; // 5 x 50 playlist items per click keeps each request short

function mpcbc_sermons_video_id( $url ) {
	return preg_match( '#(?:youtu\.be/|[?&]v=|/live/|/shorts/|/embed/)([\w-]{11})#', $url, $m ) ? $m[1] : '';
}

/** YouTube Data API GET. The key stays in the request only; error text never includes it. */
function mpcbc_sermons_api( $endpoint, $args ) {
	$res = wp_remote_get( add_query_arg( $args + array( 'key' => MPCBC_YT_API_KEY ), 'https://www.googleapis.com/youtube/v3/' . $endpoint ), array( 'timeout' => 20 ) );
	if ( is_wp_error( $res ) ) return 'Could not reach YouTube.';
	$body = json_decode( wp_remote_retrieve_body( $res ), true );
	return 200 === wp_remote_retrieve_response_code( $res ) && is_array( $body ) ? $body : 'YouTube API error: ' . ( $body['error']['message'] ?? 'HTTP ' . wp_remote_retrieve_response_code( $res ) );
}

/** Option A: one click = up to N playlist pages. @return array|string summary or error */
function mpcbc_sermons_import_api( $channel, $since, $token ) {
	$playlist = 'UU' . substr( MPCBC_SERMONS_CHANNELS[ $channel ]['id'], 2 );
	$sum      = array( 'published' => 0, 'exists' => 0, 'skipped' => 0, 'not_live' => 0, 'next' => '' );
	for ( $page = 0; $page < MPCBC_SERMONS_PAGES_PER_CLICK; $page++ ) {
		$list = mpcbc_sermons_api( 'playlistItems', array( 'part' => 'contentDetails', 'playlistId' => $playlist, 'maxResults' => 50, 'pageToken' => $token ) );
		if ( is_string( $list ) ) return $list;
		$ids = array();
		$old = false;
		foreach ( $list['items'] ?? array() as $it ) {
			if ( substr( $it['contentDetails']['videoPublishedAt'] ?? '', 0, 10 ) < $since ) { $old = true; continue; }
			$ids[] = $it['contentDetails']['videoId'];
		}
		if ( $ids ) {
			$vids = mpcbc_sermons_api( 'videos', array( 'part' => 'snippet,liveStreamingDetails', 'id' => implode( ',', $ids ), 'maxResults' => 50 ) );
			if ( is_string( $vids ) ) return $vids;
			foreach ( $vids['items'] ?? array() as $v ) {
				if ( empty( $v['liveStreamingDetails'] ) ) { $sum['not_live']++; continue; }
				$when = $v['liveStreamingDetails']['actualStartTime'] ?? $v['snippet']['publishedAt'];
				$sum[ mpcbc_sermons_add( $v['id'], $v['snippet']['title'], $channel, $when, 'skip' ) ]++;
			}
		}
		$token = $list['nextPageToken'] ?? '';
		if ( $old || '' === $token ) { $token = ''; break; }
	}
	$sum['next'] = $token;
	return $sum;
}

/** Option C: one pasted URL via the public oEmbed endpoint (no key). @return string result line */
function mpcbc_sermons_import_url( $url, $fallback_date ) {
	$vid = mpcbc_sermons_video_id( $url );
	if ( ! $vid ) return "$url: not a YouTube video link";
	$res = wp_remote_get( 'https://www.youtube.com/oembed?format=json&url=' . rawurlencode( mpcbc_sermons_url( $vid ) ), array( 'timeout' => 15 ) );
	$o   = is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ? null : json_decode( wp_remote_retrieve_body( $res ), true );
	if ( ! $o || empty( $o['title'] ) ) return "$vid: YouTube would not give details (private or removed?)";
	$channel = false !== stripos( $o['author_url'] ?? '', 'englishmpcbc8168' ) ? 'en' : 'zh';
	$r       = mpcbc_sermons_add( $vid, $o['title'], $channel, '', 'review', $fallback_date );
	$post    = mpcbc_sermons_find( $vid );
	if ( 'review' === $r && $post && ! mpcbc_sermons_parse( $o['title'], $channel )['date'] && ! $fallback_date ) update_post_meta( $post, '_mpcbc_note', get_post_meta( $post, '_mpcbc_note', true ) . '; no date in title' );
	return "$vid: " . array( 'published' => 'imported and published', 'review' => 'imported, needs review', 'exists' => 'already there', 'skipped' => 'skipped' )[ $r ] . ' - ' . $o['title'];
}

function mpcbc_sermons_import_page() {
	if ( ! current_user_can( 'manage_options' ) ) return;
	echo '<div class="wrap"><h1>Import past videos</h1>';
	$has_key = defined( 'MPCBC_YT_API_KEY' ) && MPCBC_YT_API_KEY;

	if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		check_admin_referer( 'mpcbc_import' );
		if ( isset( $_POST['mode'] ) && 'api' === $_POST['mode'] && $has_key ) {
			$ch    = isset( $_POST['channel'], MPCBC_SERMONS_CHANNELS[ $_POST['channel'] ] ) ? sanitize_key( $_POST['channel'] ) : 'zh';
			$since = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $_POST['since'] ?? '' ) ? $_POST['since'] : '';
			$res   = $since ? mpcbc_sermons_import_api( $ch, $since, sanitize_text_field( wp_unslash( $_POST['token'] ?? '' ) ) ) : 'Please choose a start date.';
			if ( is_string( $res ) ) {
				echo '<div class="notice notice-error"><p>' . esc_html( $res ) . '</p></div>';
			} else {
				echo '<div class="notice notice-success"><p>' . esc_html( "Imported and published: {$res['published']}. Already there: {$res['exists']}. Skipped because the title was unclear: {$res['skipped']}. Not livestreams: {$res['not_live']}." ) . '</p>';
				if ( $res['next'] ) {
					echo '<form method="post">'; wp_nonce_field( 'mpcbc_import' );
					printf( '<input type="hidden" name="mode" value="api"><input type="hidden" name="channel" value="%s"><input type="hidden" name="since" value="%s"><input type="hidden" name="token" value="%s">', esc_attr( $ch ), esc_attr( $since ), esc_attr( $res['next'] ) );
					submit_button( 'Continue (more videos remain)', 'primary', 'submit', true ); echo '</form>';
				} else {
					echo '<p>Reached the start date. Done.</p>';
				}
				echo '</div>';
			}
		} elseif ( isset( $_POST['urls'] ) ) {
			$day = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $_POST['fallback_date'] ?? '' ) ? $_POST['fallback_date'] : '';
			echo '<div class="notice notice-info"><ul>';
			foreach ( array_slice( array_filter( array_map( 'trim', explode( "\n", sanitize_textarea_field( wp_unslash( $_POST['urls'] ) ) ) ) ), 0, 25 ) as $u ) echo '<li>' . esc_html( mpcbc_sermons_import_url( $u, $day ) ) . '</li>';
			echo '</ul></div>';
		}
	}

	echo '<h2>A. Import from YouTube since a date</h2>';
	if ( ! $has_key ) {
		echo '<p>Turned off: add <code>define( \'MPCBC_YT_API_KEY\', \'...\' );</code> to this site\'s wp-config.php (never commit it). Only clearly formatted livestream titles are imported; the rest are skipped and counted.</p>';
	}
	echo '<form method="post">'; wp_nonce_field( 'mpcbc_import' );
	echo '<input type="hidden" name="mode" value="api"><p><label>Channel <select name="channel">';
	foreach ( MPCBC_SERMONS_CHANNELS as $k => $c ) echo '<option value="' . esc_attr( $k ) . '">' . esc_html( $c['name'] ) . '</option>';
	echo '</select></label> <label>Since <input type="date" name="since"></label></p>';
	submit_button( 'Import from YouTube', 'primary', 'submit', true, $has_key ? array() : array( 'disabled' => 'disabled' ) );
	echo '</form><h2>C. Paste YouTube links</h2><form method="post">'; wp_nonce_field( 'mpcbc_import' );
	echo '<p><textarea name="urls" rows="6" class="large-text" placeholder="One link per line (up to 25)"></textarea></p>';
	echo '<p><label>Date to use if the title has none <input type="date" name="fallback_date"></label></p>';
	submit_button( 'Import links' );
	echo '</form></div>';
}
