<?php
/**
 * Plugin Name: MPCBC Sermons
 * Description: Syncs sermon videos from the two MPCBC YouTube channels (RSS, no API key) and provides the [mpcbc_live] and [mpcbc_sermons] shortcodes.
 * Version: 0.1.0
 * Text Domain: mpcbc-sermons
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/parser.php';
require_once __DIR__ . '/selftest.php';
require_once __DIR__ . '/admin.php';
require_once __DIR__ . '/backfill.php';
require_once __DIR__ . '/live.php';

// Two channels, hard-coded on purpose (no settings page). 'zh' carries Cantonese + Mandarin.
const MPCBC_SERMONS_CHANNELS = array(
	'zh' => array( 'name' => 'Chinese channel', 'id' => 'UCnxu_A53vGhcNohchKzZ7hw', 'handle' => '@MPCBC', 'live' => 'https://www.youtube.com/@MPCBC/live' ),
	'en' => array( 'name' => 'English channel', 'id' => 'UCJx8Xl0QqiNkZwwfLXaASHQ', 'handle' => '@englishmpcbc8168', 'live' => 'https://www.youtube.com/@englishmpcbc8168/live' ),
);
// Slug => label, channel, service time (from the current Home page).
const MPCBC_SERMONS_MINISTRIES = array(
	'mandarin'  => array( 'Mandarin', 'zh', '9:00 AM' ),
	'cantonese' => array( 'Cantonese', 'zh', '11:00 AM' ),
	'english'   => array( 'English', 'en', '11:00 AM' ),
);
const MPCBC_SERMONS_CAPS = array( 'edit_sermons', 'edit_others_sermons', 'publish_sermons', 'read_private_sermons', 'delete_sermons', 'delete_private_sermons', 'delete_published_sermons', 'delete_others_sermons', 'edit_private_sermons', 'edit_published_sermons', 'manage_sermon_terms' );

/* ---------- Data model ---------- */

function mpcbc_sermons_register() {
	register_post_type( 'sermon', array(
		'labels'          => array( 'name' => 'Sermons', 'singular_name' => 'Sermon', 'menu_name' => 'Sermons', 'add_new_item' => 'Add sermon', 'edit_item' => 'Edit sermon' ),
		'public'          => true,
		'has_archive'     => false, // the archive is the [mpcbc_sermons] shortcode
		'rewrite'         => array( 'slug' => 'sermon' ),
		'show_in_rest'    => true,
		'menu_icon'       => 'dashicons-video-alt3',
		'supports'        => array( 'title', 'editor', 'custom-fields' ),
		'capability_type' => array( 'sermon', 'sermons' ),
		'map_meta_cap'    => true,
	) );
	// query_var false: ?ministry= / ?speaker= / ?series= belong to the shortcode form, not to WP's main query.
	foreach ( array( 'ministry' => 'Congregations', 'speaker' => 'Speakers', 'series' => 'Series' ) as $tax => $label ) {
		register_taxonomy( $tax, 'sermon', array(
			'label'        => $label,
			'public'       => false,
			'show_ui'      => true,
			'show_in_rest' => true,
			'query_var'    => false,
			'rewrite'      => false,
			'capabilities' => 'ministry' === $tax
				? array( 'assign_terms' => 'edit_sermons' )
				: array( 'manage_terms' => 'manage_sermon_terms', 'edit_terms' => 'manage_sermon_terms', 'delete_terms' => 'manage_sermon_terms', 'assign_terms' => 'edit_sermons' ),
		) );
	}
	foreach ( array( 'speaker', 'series' ) as $tax ) {
		register_term_meta( $tax, 'aliases', array( 'type' => 'string', 'single' => true, 'sanitize_callback' => 'sanitize_textarea_field' ) );
	}
}
add_action( 'init', 'mpcbc_sermons_register' );

/* ---------- Aliases (term edit screens) ---------- */

foreach ( array( 'speaker', 'series' ) as $mpcbc_tax ) {
	add_action( "{$mpcbc_tax}_add_form_fields", function () {
		echo '<div class="form-field"><label for="mpcbc-aliases">Aliases</label><textarea name="mpcbc_aliases" id="mpcbc-aliases" rows="4"></textarea>';
		echo '<p>One spelling per line: Traditional, Simplified, English.</p></div>';
	} );
	add_action( "{$mpcbc_tax}_edit_form_fields", function ( $term ) {
		echo '<tr class="form-field"><th><label for="mpcbc-aliases">Aliases</label></th><td><textarea name="mpcbc_aliases" id="mpcbc-aliases" rows="4" class="large-text">' . esc_textarea( get_term_meta( $term->term_id, 'aliases', true ) ) . '</textarea>';
		echo '<p class="description">One spelling per line: Traditional, Simplified, English.</p></td></tr>';
	} );
	$save = function ( $term_id ) {
		if ( isset( $_POST['mpcbc_aliases'] ) && current_user_can( 'manage_sermon_terms' ) ) { // WP verifies its own term-form nonce before these hooks fire.
			update_term_meta( $term_id, 'aliases', sanitize_textarea_field( wp_unslash( $_POST['mpcbc_aliases'] ) ) );
		}
	};
	add_action( "created_{$mpcbc_tax}", $save );
	add_action( "edited_{$mpcbc_tax}", $save );
}

function mpcbc_sermons_aliases( $term_id ) {
	// One per line: names like "Rev. Caleb Cheung, PhD" contain commas.
	return array_filter( array_map( 'trim', preg_split( '/[\r\n]+/u', (string) get_term_meta( $term_id, 'aliases', true ) ) ) );
}

/** Term ID whose name or alias equals $text (whitespace/case-insensitive), else 0. */
function mpcbc_sermons_resolve( $tax, $text ) {
	$n = mpcbc_sermons_norm( $text );
	if ( '' === $n ) return 0;
	foreach ( get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false ) ) as $t ) {
		if ( mpcbc_sermons_norm( html_entity_decode( $t->name ) ) === $n ) return $t->term_id;
		foreach ( mpcbc_sermons_aliases( $t->term_id ) as $a ) {
			if ( mpcbc_sermons_norm( $a ) === $n ) return $t->term_id;
		}
	}
	return 0;
}

/* ---------- Activation: schedule, seed terms, role, first sync ---------- */

register_activation_hook( __FILE__, function () {
	mpcbc_sermons_register(); // init has already fired during an activation request
	foreach ( array( 'cantonese' => 'Cantonese', 'mandarin' => 'Mandarin', 'english' => 'English' ) as $slug => $name ) {
		if ( ! term_exists( $slug, 'ministry' ) ) wp_insert_term( $name, 'ministry', array( 'slug' => $slug ) );
	}
	$seed = array(
		'speaker' => array(
			'張健庭'      => array( '張健庭牧師博士', '张健庭牧师博士', 'Rev. Dr. Caleb Cheung' ),
			'李約翰'      => array( '李約翰牧師', '李约翰牧师' ),
			'路长久'      => array( '路长久弟兄' ),
			'梁紅源'      => array( '梁紅源姊妹', '梁红源姊妹' ),
			'Rev. Edward Lu' => array(),
		),
		'series'  => array(
			'重塑生命' => array( '重塑生命系列', 'Renovation of the Heart' ),
			'希伯來書' => array( '希伯來書系列' ),
		),
	);
	foreach ( $seed as $tax => $terms ) {
		foreach ( $terms as $name => $aliases ) {
			if ( mpcbc_sermons_resolve( $tax, $name ) ) continue;
			$t = wp_insert_term( $name, $tax );
			if ( ! is_wp_error( $t ) ) update_term_meta( $t['term_id'], 'aliases', implode( "\n", $aliases ) );
		}
	}
	// Role without manage_options; administrators get the sermon caps too.
	remove_role( 'mpcbc_sermon_editor' );
	add_role( 'mpcbc_sermon_editor', 'Sermon editor', array_merge( array( 'read' => true ), array_fill_keys( MPCBC_SERMONS_CAPS, true ) ) );
	$admin = get_role( 'administrator' );
	foreach ( MPCBC_SERMONS_CAPS as $cap ) $admin->add_cap( $cap );

	if ( ! wp_next_scheduled( 'mpcbc_sermons_sync' ) ) wp_schedule_event( time() + 3600, 'hourly', 'mpcbc_sermons_sync' );
	mpcbc_sermons_sync();
	flush_rewrite_rules();
} );
register_deactivation_hook( __FILE__, function () { wp_clear_scheduled_hook( 'mpcbc_sermons_sync' ); wp_clear_scheduled_hook( 'mpcbc_sermons_live_tick' ); } );
add_action( 'mpcbc_sermons_sync', 'mpcbc_sermons_sync' );

/* ---------- Importing ---------- */

/** Parse a title and resolve speaker/series. Unknown speaker/series is never auto-created. */
function mpcbc_sermons_evaluate( $raw, $channel ) {
	$ev    = mpcbc_sermons_parse( $raw, $channel );
	$notes = array();
	if ( $ev['missing'] ) $notes[] = 'missing: ' . implode( ', ', $ev['missing'] );
	$ev['speaker_id'] = $ev['speaker'] ? mpcbc_sermons_resolve( 'speaker', $ev['speaker'] ) : 0;
	$ev['series_id']  = $ev['series'] ? mpcbc_sermons_resolve( 'series', $ev['series'] ) : 0;
	if ( $ev['speaker'] && ! $ev['speaker_id'] ) $notes[] = 'new speaker: ' . $ev['speaker'];
	if ( $ev['series'] && ! $ev['series_id'] ) $notes[] = 'new series: ' . $ev['series'];
	$ev['notes'] = implode( '; ', $notes );
	$ev['clean'] = ! $notes;
	return $ev;
}

/** Write an evaluation onto a post (title, fields, terms, status). Skips nothing: callers guard editor edits. */
function mpcbc_sermons_apply( $post_id, $ev, $raw ) {
	$ok = $ev['clean'];
	wp_update_post( wp_slash( array( 'ID' => $post_id, 'post_title' => ( $ev['title'] && $ev['ok'] ) ? $ev['title'] : $raw, 'post_status' => $ok ? 'publish' : 'pending' ) ) );
	update_post_meta( $post_id, '_mpcbc_scripture', wp_slash( $ev['scripture'] ) );
	update_post_meta( $post_id, '_mpcbc_note', wp_slash( $ev['notes'] ) );
	wp_set_object_terms( $post_id, $ev['ministries'], 'ministry' );
	wp_set_object_terms( $post_id, $ev['speaker_id'] ? array( $ev['speaker_id'] ) : array(), 'speaker' );
	wp_set_object_terms( $post_id, $ev['series_id'] ? array( $ev['series_id'] ) : array(), 'series' );
}

function mpcbc_sermons_find( $video_id ) {
	$ids = get_posts( array( 'post_type' => 'sermon', 'post_status' => array( 'publish', 'pending', 'draft', 'future', 'private', 'trash' ), 'meta_key' => '_mpcbc_video_id', 'meta_value' => $video_id, 'fields' => 'ids', 'posts_per_page' => 1 ) );
	return $ids ? $ids[0] : 0;
}

/**
 * Upsert by YouTube video ID. $iso = YouTube timestamp (UTC) used when the title has no date.
 * $unclear: 'review' keeps unclear titles as Needs review, 'skip' drops them.
 * @return string exists|skipped|published|review
 */
function mpcbc_sermons_add( $vid, $raw, $channel, $iso = '', $unclear = 'review', $fallback_date = '' ) {
	if ( ! preg_match( '/^[\w-]{11}$/', $vid ) ) return 'skipped';
	if ( $id = mpcbc_sermons_find( $vid ) ) {
		update_post_meta( $id, '_mpcbc_raw_title', wp_slash( $raw ) ); // editor-edited fields are never overwritten
		return 'exists';
	}
	$ev = mpcbc_sermons_evaluate( $raw, $channel );
	if ( ! $ev['clean'] && 'skip' === $unclear ) return 'skipped';
	// Title date (the service day) beats the upload time; never in the future, or WP would schedule it.
	$day = $ev['date'] ?: $fallback_date;
	$gmt = $day ? get_gmt_from_date( $day . ' 00:00:00' ) : ( $iso ? gmdate( 'Y-m-d H:i:s', strtotime( $iso ) ) : gmdate( 'Y-m-d H:i:s' ) );
	if ( $gmt > gmdate( 'Y-m-d H:i:s' ) ) $gmt = gmdate( 'Y-m-d H:i:s' );
	$pid = wp_insert_post( wp_slash( array(
		'post_type' => 'sermon', 'post_status' => 'pending', 'post_title' => $raw,
		'post_date_gmt' => $gmt, 'post_date' => get_date_from_gmt( $gmt ),
	) ) );
	if ( ! $pid || is_wp_error( $pid ) ) return 'skipped';
	update_post_meta( $pid, '_mpcbc_video_id', $vid );
	update_post_meta( $pid, '_mpcbc_raw_title', wp_slash( $raw ) );
	update_post_meta( $pid, '_mpcbc_channel', $channel );
	mpcbc_sermons_apply( $pid, $ev, $raw );
	return $ev['clean'] ? 'published' : 'review';
}

/** Import every entry of a parsed YouTube Atom feed; returns a one-line summary. */
function mpcbc_sermons_import_feed( $xml, $key ) {
	$n = array( 'published' => 0, 'review' => 0, 'exists' => 0, 'skipped' => 0 );
	foreach ( $xml->entry as $e ) {
		$yt = $e->children( 'http://www.youtube.com/xml/schemas/2015' );
		$n[ mpcbc_sermons_add( (string) $yt->videoId, (string) $e->title, $key, (string) $e->published ) ]++;
	}
	return "{$n['published']} published, {$n['review']} need review, {$n['exists']} already known";
}

/** Latest 15 uploads via the API (1 quota unit). YouTube 404s RSS for server requests. */
function mpcbc_sermons_sync_api( $key ) {
	$list = mpcbc_sermons_api( 'playlistItems', array( 'part' => 'snippet', 'playlistId' => 'UU' . substr( MPCBC_SERMONS_CHANNELS[ $key ]['id'], 2 ), 'maxResults' => 15 ) );
	if ( is_string( $list ) ) return array( 'time' => time(), 'ok' => false, 'msg' => $list );
	$n   = array( 'published' => 0, 'review' => 0, 'exists' => 0, 'skipped' => 0 );
	$ids = array_filter( array_map( fn( $it ) => $it['snippet']['resourceId']['videoId'] ?? '', $list['items'] ?? array() ) );
	// publishedAt is when a stream was made public (often the next day); the stream start is the service time (+1 unit).
	$vids = $ids ? mpcbc_sermons_api( 'videos', array( 'part' => 'liveStreamingDetails', 'id' => implode( ',', $ids ) ) ) : array();
	$start = is_array( $vids ) ? array_column( array_map( fn( $v ) => array( $v['id'], $v['liveStreamingDetails']['actualStartTime'] ?? '' ), $vids['items'] ?? array() ), 1, 0 ) : array();
	foreach ( $list['items'] ?? array() as $it ) {
		$s   = $it['snippet'];
		$vid = $s['resourceId']['videoId'] ?? '';
		$n[ mpcbc_sermons_add( $vid, $s['title'], $key, ( $start[ $vid ] ?? '' ) ?: ( $s['publishedAt'] ?? '' ) ) ]++;
	}
	return array( 'time' => time(), 'ok' => true, 'msg' => "{$n['published']} published, {$n['review']} need review, {$n['exists']} already known" );
}

/** Fetch both channels: API when a key is set, else RSS. A failed channel leaves existing records untouched. */
function mpcbc_sermons_sync() {
	$status = array();
	foreach ( MPCBC_SERMONS_CHANNELS as $key => $ch ) {
		if ( defined( 'MPCBC_YT_API_KEY' ) && MPCBC_YT_API_KEY ) {
			$status[ $key ] = mpcbc_sermons_sync_api( $key );
			continue;
		}
		$res = wp_remote_get( 'https://www.youtube.com/feeds/videos.xml?channel_id=' . $ch['id'], array( 'timeout' => 20 ) );
		$xml = false;
		if ( ! is_wp_error( $res ) && 200 === wp_remote_retrieve_response_code( $res ) ) {
			libxml_use_internal_errors( true );
			$xml = simplexml_load_string( wp_remote_retrieve_body( $res ) );
		}
		if ( ! $xml ) {
			$status[ $key ] = array( 'time' => time(), 'ok' => false, 'msg' => is_wp_error( $res ) ? $res->get_error_message() : 'Feed unavailable or unreadable (HTTP ' . wp_remote_retrieve_response_code( $res ) . ')' );
			continue;
		}
		$status[ $key ] = array( 'time' => time(), 'ok' => true, 'msg' => mpcbc_sermons_import_feed( $xml, $key ) );
	}
	update_option( 'mpcbc_sermons_sync_status', $status, false );
	return $status;
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'mpcbc-sermons selftest', function () {
		$fails = mpcbc_sermons_selftest();
		foreach ( array( '张健庭牧师博士' => 'speaker', 'rev. dr.  caleb cheung' => 'speaker', '希伯來書系列' => 'series', 'Renovation of the Heart' => 'series' ) as $text => $tax ) {
			if ( ! mpcbc_sermons_resolve( $tax, $text ) ) $fails[] = "resolve $tax '$text' failed (activate the plugin to seed terms)";
		}
		if ( mpcbc_sermons_resolve( 'speaker', 'Nobody Known' ) ) $fails[] = 'unknown speaker resolved';
		$fails ? WP_CLI::error( implode( "\n", $fails ) ) : WP_CLI::success( 'selftest passed (16 title cases + 5 resolver checks)' );
	} );
	WP_CLI::add_command( 'mpcbc-sermons sync', function () {
		foreach ( mpcbc_sermons_sync() as $k => $s ) WP_CLI::log( "$k: " . ( $s['ok'] ? 'ok' : 'FAILED' ) . ' - ' . $s['msg'] );
	} );
}

/* ---------- Shortcodes ---------- */

function mpcbc_sermons_style() { wp_enqueue_style( 'mpcbc-sermons', plugins_url( 'mpcbc-sermons.css', __FILE__ ), array(), '0.2.0' ); }

/** English + Traditional + Simplified copies; the theme CSS hides the inactive ones by id prefix. */
function mpcbc_sermons_t( $en, $hant, $hans ) {
	static $n = 0;
	$n++;
	return sprintf( '<span>%s</span> <span id="mpcbc-copy-zh-hant-sermons-%d">%s</span><span id="mpcbc-copy-zh-hans-sermons-%d">%s</span>', esc_html( $en ), $n, esc_html( $hant ), $n, esc_html( $hans ) );
}

function mpcbc_sermons_thumb( $vid ) { return 'https://i.ytimg.com/vi/' . rawurlencode( $vid ) . '/hqdefault.jpg'; }
function mpcbc_sermons_url( $vid ) { return 'https://www.youtube.com/watch?v=' . rawurlencode( $vid ); }

add_shortcode( 'mpcbc_live', function ( $atts ) {
	$a = shortcode_atts( array( 'ministry' => 'mandarin', 'part' => 'media' ), $atts );
	if ( ! isset( MPCBC_SERMONS_MINISTRIES[ $a['ministry'] ] ) ) return '';
	mpcbc_sermons_style();
	// A live or scheduled stream beats the latest sermon.
	$stream = mpcbc_sermons_live_for( $a['ministry'] );
	$when   = $stream ? wp_date( 'D g:i A', $stream['start'] ) : ''; // "Sun 9:00 AM"; the pill table below builds every state
	if ( $stream ) {
		$vid   = $stream['vid'];
		$title = $stream['title'];
	} else {
		$latest = get_posts( array( 'post_type' => 'sermon', 'post_status' => array( 'publish', 'pending' ), 'posts_per_page' => 1, 'tax_query' => array( array( 'taxonomy' => 'ministry', 'field' => 'slug', 'terms' => $a['ministry'] ) ) ) );
		if ( ! $latest ) return '';
		$post  = $latest[0];
		$vid   = get_post_meta( $post->ID, '_mpcbc_video_id', true );
		$title = get_the_title( $post );
	}
	$kind = $stream['kind'] ?? 'latest';
	if ( 'status' === $a['part'] ) {
		// The pill already says live / next and when, so the line is just the stream's title.
		if ( 'latest' !== $kind ) return '<p class="mpcbc-slip__status">' . esc_html( $title ) . '</p>';
		$speaker = wp_get_object_terms( $post->ID, 'speaker', array( 'fields' => 'names' ) );
		$line    = esc_html( $title . ', ' . get_the_date( 'M j, Y', $post ) . ( $speaker ? ', ' . $speaker[0] : '' ) );
		return '<p class="mpcbc-slip__status">' . mpcbc_sermons_t( 'Latest', '最新', '最新' ) . ': ' . $line . '</p>';
	}
	$pill = array(
		'live'     => array( 'is-live', '<span class="mpcbc-slip__dot" aria-hidden="true"></span>' . mpcbc_sermons_t( 'Live now', '直播中', '直播中' ) ),
		'upcoming' => array( 'is-next', mpcbc_sermons_t( 'Next · ' . $when, '下一場', '下一场' ) ),
		'latest'   => array( '', '<span aria-hidden="true">▶</span> ' . mpcbc_sermons_t( 'Play', '播放', '播放' ) ),
	)[ $kind ];
	wp_enqueue_script( 'mpcbc-sermons', plugins_url( 'mpcbc-sermons.js', __FILE__ ), array(), '0.2.0', array( 'in_footer' => true, 'strategy' => 'defer' ) );
	// Click-to-play: a link to YouTube that the script swaps for the embedded player (no script: opens YouTube).
	return '<div class="mpcbc-slip"><a class="mpcbc-slip__thumb" href="' . esc_url( mpcbc_sermons_url( $vid ) ) . '" target="_blank" rel="noopener" data-mpcbc-play="' . esc_attr( $vid ) . '" data-title="' . esc_attr( $title ) . '">'
		. '<img src="' . esc_url( mpcbc_sermons_thumb( $vid ) ) . '" alt="' . esc_attr( $title ) . '" loading="lazy">'
		. '<span class="mpcbc-slip__pill ' . $pill[0] . '">' . $pill[1] . '</span></a></div>';
} );

/** Term IDs whose name or any alias contains the query (so "caleb" finds all three spellings' sermons). */
function mpcbc_sermons_term_hits( $q ) {
	$n    = mpcbc_sermons_norm( $q );
	$hits = array();
	foreach ( array( 'speaker', 'series' ) as $tax ) {
		foreach ( get_terms( array( 'taxonomy' => $tax, 'hide_empty' => true ) ) as $t ) {
			foreach ( array_merge( array( $t->name ), mpcbc_sermons_aliases( $t->term_id ) ) as $s ) {
				if ( false !== mb_strpos( mpcbc_sermons_norm( html_entity_decode( $s ) ), $n ) ) { $hits[ $tax ][] = $t->term_id; break; }
			}
		}
	}
	return $hits;
}

add_shortcode( 'mpcbc_sermons', function () {
	global $wpdb;
	mpcbc_sermons_style();
	$q      = isset( $_GET['sq'] ) ? mpcbc_sermons_clean( sanitize_text_field( wp_unslash( $_GET['sq'] ) ) ) : '';
	$min    = isset( $_GET['ministry'] ) && isset( MPCBC_SERMONS_MINISTRIES[ $_GET['ministry'] ] ) ? sanitize_key( $_GET['ministry'] ) : '';
	$sp     = isset( $_GET['speaker'] ) ? absint( $_GET['speaker'] ) : 0;
	$se     = isset( $_GET['series'] ) ? absint( $_GET['series'] ) : 0;
	$paged  = isset( $_GET['spage'] ) ? max( 1, absint( $_GET['spage'] ) ) : 1;
	$args   = array( 'post_type' => 'sermon', 'post_status' => 'publish', 'posts_per_page' => 12, 'paged' => $paged, 'tax_query' => array() );
	if ( $min ) $args['tax_query'][] = array( 'taxonomy' => 'ministry', 'field' => 'slug', 'terms' => $min );
	if ( $sp ) $args['tax_query'][] = array( 'taxonomy' => 'speaker', 'field' => 'term_id', 'terms' => $sp );
	if ( $se ) $args['tax_query'][] = array( 'taxonomy' => 'series', 'field' => 'term_id', 'terms' => $se );
	if ( '' !== $q ) { // title LIKE, plus sermons of any speaker/series whose name or alias matches
		$ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'sermon' AND post_status = 'publish' AND post_title LIKE %s", '%' . $wpdb->esc_like( $q ) . '%' ) );
		foreach ( mpcbc_sermons_term_hits( $q ) as $tax => $term_ids ) {
			$ids = array_merge( $ids, get_objects_in_term( $term_ids, $tax ) );
		}
		$args['post__in'] = $ids ? array_map( 'absint', $ids ) : array( 0 );
	}
	$query = new WP_Query( $args );
	$base  = get_permalink();
	$sel   = function ( $name, $tax, $current ) {
		$o = '<option value="">' . esc_html( 'All / 全部' ) . '</option>';
		foreach ( get_terms( array( 'taxonomy' => $tax, 'hide_empty' => true ) ) as $t ) {
			$o .= '<option value="' . (int) $t->term_id . '"' . selected( $current, $t->term_id, false ) . '>' . esc_html( $t->name ) . '</option>';
		}
		return '<select name="' . $name . '" id="mpcbc-f-' . $name . '">' . $o . '</select>';
	};
	$out  = '<div class="mpcbc-sermons"><form class="mpcbc-sermons__form" method="get" action="' . esc_url( $base ) . '">';
	if ( ! get_option( 'permalink_structure' ) || ! is_singular() ) { // plain permalinks keep the page in the query string
		foreach ( array( 'page_id', 'p', 'preview' ) as $k ) if ( isset( $_GET[ $k ] ) ) $out .= '<input type="hidden" name="' . $k . '" value="' . esc_attr( sanitize_text_field( wp_unslash( $_GET[ $k ] ) ) ) . '">';
	}
	$out .= '<label for="mpcbc-f-sq">' . mpcbc_sermons_t( 'Search', '搜尋', '搜索' ) . '</label><input type="search" name="sq" id="mpcbc-f-sq" value="' . esc_attr( $q ) . '">';
	$ms   = '<option value="">' . esc_html( 'All / 全部' ) . '</option>';
	foreach ( MPCBC_SERMONS_MINISTRIES as $slug => $m ) $ms .= '<option value="' . $slug . '"' . selected( $min, $slug, false ) . '>' . esc_html( $m[0] . ( 'mandarin' === $slug ? ' 國語/国语' : ( 'cantonese' === $slug ? ' 粵語/粤语' : '' ) ) ) . '</option>';
	$out .= '<label for="mpcbc-f-ministry">' . mpcbc_sermons_t( 'Congregation', '堂別', '堂别' ) . '</label><select name="ministry" id="mpcbc-f-ministry">' . $ms . '</select>';
	$out .= '<label for="mpcbc-f-speaker">' . mpcbc_sermons_t( 'Speaker', '講員', '讲员' ) . '</label>' . $sel( 'speaker', 'speaker', $sp );
	$out .= '<label for="mpcbc-f-series">' . mpcbc_sermons_t( 'Series', '系列', '系列' ) . '</label>' . $sel( 'series', 'series', $se );
	$out .= '<button type="submit">' . mpcbc_sermons_t( 'Search', '搜尋', '搜索' ) . '</button></form>';

	if ( ! $query->have_posts() ) {
		$out .= '<p class="mpcbc-sermons__empty">' . mpcbc_sermons_t( 'No sermons found.', '找不到講道。', '找不到讲道。' ) . '</p>';
	} else {
		$out .= '<ul class="mpcbc-sermons__grid">';
		foreach ( $query->posts as $p ) {
			$vid  = get_post_meta( $p->ID, '_mpcbc_video_id', true );
			$meta = array();
			foreach ( array( 'speaker', 'series', 'ministry' ) as $tax ) {
				$names = wp_get_object_terms( $p->ID, $tax, array( 'fields' => 'names' ) );
				if ( $names ) $meta[] = '<span class="mpcbc-sermons__' . $tax . '">' . esc_html( implode( ' + ', $names ) ) . '</span>';
			}
			if ( $sc = get_post_meta( $p->ID, '_mpcbc_scripture', true ) ) $meta[] = '<span class="mpcbc-sermons__scripture">' . esc_html( $sc ) . '</span>';
			$meta[] = '<time datetime="' . esc_attr( get_the_date( 'c', $p ) ) . '">' . esc_html( get_the_date( 'M j, Y', $p ) ) . '</time>';
			$out   .= '<li class="mpcbc-sermons__card"><a class="mpcbc-sermons__thumb" href="' . esc_url( mpcbc_sermons_url( $vid ) ) . '" target="_blank" rel="noopener"><img src="' . esc_url( mpcbc_sermons_thumb( $vid ) ) . '" alt="" loading="lazy"></a>'
				. '<h3><a href="' . esc_url( mpcbc_sermons_url( $vid ) ) . '" target="_blank" rel="noopener">' . esc_html( get_the_title( $p ) ) . '</a></h3><p>' . implode( '<br>', $meta ) . '</p></li>';
		}
		$out .= '</ul>';
		$keep = array_filter( array( 'sq' => $q, 'ministry' => $min, 'speaker' => $sp, 'series' => $se ) );
		$out .= '<nav class="mpcbc-sermons__pager">' . paginate_links( array( 'base' => add_query_arg( 'spage', '%#%', add_query_arg( $keep, $base ) ), 'format' => '', 'current' => $paged, 'total' => $query->max_num_pages, 'type' => 'plain' ) ) . '</nav>';
	}
	return $out . '</div>';
} );
