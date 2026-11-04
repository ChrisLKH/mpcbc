<?php
/**
 * Live and upcoming streams for the Home slips. Needs MPCBC_YT_API_KEY.
 * Budget: upcoming search Sat 8 PM + Sun 7 AM, live search 5 min after each service start
 * if nothing is known (100 units each, ~700/week); status of known IDs every 5 min on
 * Sunday mornings (1 unit each).
 */
defined( 'ABSPATH' ) || exit;

const MPCBC_SERMONS_LIVE_WINDOW = array( '08:00', '13:30' ); // Sunday, site time (Los Angeles)

add_filter( 'cron_schedules', function ( $s ) {
	$s['mpcbc_five_minutes'] = array( 'interval' => 300, 'display' => 'Every 5 minutes' );
	return $s;
} );
add_action( 'init', function () {
	if ( ! wp_next_scheduled( 'mpcbc_sermons_live_tick' ) ) wp_schedule_event( time() + 60, 'mpcbc_five_minutes', 'mpcbc_sermons_live_tick' );
} );
add_action( 'mpcbc_sermons_live_tick', 'mpcbc_sermons_live_tick' );

function mpcbc_sermons_live_has_key() { return defined( 'MPCBC_YT_API_KEY' ) && MPCBC_YT_API_KEY; }

/** Search each channel for live or upcoming streams and remember their IDs. */
function mpcbc_sermons_live_discover( $event ) {
	$ids = get_option( 'mpcbc_sermons_live_ids', array() );
	foreach ( MPCBC_SERMONS_CHANNELS as $ch => $c ) {
		$r = mpcbc_sermons_api( 'search', array( 'part' => 'id', 'channelId' => $c['id'], 'eventType' => $event, 'type' => 'video', 'maxResults' => 5 ) );
		if ( is_string( $r ) ) continue;
		foreach ( $r['items'] ?? array() as $it ) $ids[ $it['id']['videoId'] ] = $ch;
	}
	update_option( 'mpcbc_sermons_live_ids', $ids, false );
}

/** Check known IDs (1 unit) and store which stream each ministry has: live or upcoming. */
function mpcbc_sermons_live_poll() {
	$ids   = get_option( 'mpcbc_sermons_live_ids', array() );
	$state = array( 'checked' => time(), 'streams' => array() );
	if ( $ids ) {
		$r = mpcbc_sermons_api( 'videos', array( 'part' => 'snippet,liveStreamingDetails', 'id' => implode( ',', array_slice( array_keys( $ids ), 0, 50 ) ) ) );
		if ( is_string( $r ) ) return; // keep the last state; the shortcode ignores it once stale
		$keep = array();
		foreach ( $r['items'] ?? array() as $v ) {
			$kind = $v['snippet']['liveBroadcastContent'] ?? 'none';
			if ( 'none' === $kind ) continue; // ended: the hourly sync imports it as a sermon
			$start = strtotime( $v['liveStreamingDetails']['scheduledStartTime'] ?? $v['liveStreamingDetails']['actualStartTime'] ?? '' );
			if ( 'upcoming' === $kind && $start < time() - 3 * HOUR_IN_SECONDS ) continue; // never started
			$keep[ $v['id'] ] = $ids[ $v['id'] ];
			$ev = mpcbc_sermons_parse( $v['snippet']['title'], $ids[ $v['id'] ] );
			foreach ( $ev['ministries'] as $m ) {
				$cur = $state['streams'][ $m ] ?? null;
				if ( $cur && ( 'live' === $cur['kind'] || $cur['start'] <= $start ) ) continue; // live beats upcoming; the sooner upcoming wins
				$state['streams'][ $m ] = array( 'vid' => $v['id'], 'kind' => $kind, 'start' => $start, 'title' => $ev['ok'] ? $ev['title'] : $v['snippet']['title'] );
			}
		}
		update_option( 'mpcbc_sermons_live_ids', $keep, false );
	}
	update_option( 'mpcbc_sermons_live', $state, false );
}

function mpcbc_sermons_live_tick() {
	if ( ! mpcbc_sermons_live_has_key() ) return;
	$now  = current_datetime();
	$day  = $now->format( 'w' ); // 0 = Sunday, 6 = Saturday
	$hm   = $now->format( 'H:i' );
	$once = function ( $key, $fn ) use ( $now ) { // one call per slot per day
		$k = 'mpcbc_live_' . $key . '_' . $now->format( 'Ymd' );
		if ( get_transient( $k ) ) return;
		set_transient( $k, 1, DAY_IN_SECONDS );
		$fn();
	};
	if ( ( '6' === $day && $hm >= '20:00' ) || ( '0' === $day && $hm >= '07:00' && $hm < MPCBC_SERMONS_LIVE_WINDOW[1] ) ) {
		$once( 'upcoming' . ( '6' === $day ? 'sat' : 'sun' ), function () { mpcbc_sermons_live_discover( 'upcoming' ); mpcbc_sermons_live_poll(); } );
	}
	if ( '0' !== $day || $hm < MPCBC_SERMONS_LIVE_WINDOW[0] || $hm >= MPCBC_SERMONS_LIVE_WINDOW[1] ) return;
	// 5 minutes after a service starts with no live stream known for it: one live search.
	$state = get_option( 'mpcbc_sermons_live', array() );
	foreach ( MPCBC_SERMONS_MINISTRIES as $m => $info ) {
		$t = gmdate( 'H:i', strtotime( $info[2] ) + 300 );
		if ( $hm >= $t && 'live' !== ( $state['streams'][ $m ]['kind'] ?? '' ) ) $once( 'live' . str_replace( ':', '', $t ), function () { mpcbc_sermons_live_discover( 'live' ); } );
	}
	mpcbc_sermons_live_poll();
}

/** The stream to show for a ministry, or null. Stale data never claims "live". */
function mpcbc_sermons_live_for( $ministry ) {
	$state = get_option( 'mpcbc_sermons_live', array() );
	$s     = $state['streams'][ $ministry ] ?? null;
	if ( ! $s ) return null;
	$age = time() - ( $state['checked'] ?? 0 );
	if ( 'live' === $s['kind'] ) return $age < 20 * MINUTE_IN_SECONDS ? $s : null;
	return $age < 26 * HOUR_IN_SECONDS && $s['start'] > time() - 3 * HOUR_IN_SECONDS ? $s : null;
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'mpcbc-sermons live', function () {
		mpcbc_sermons_live_discover( 'live' );
		mpcbc_sermons_live_discover( 'upcoming' );
		mpcbc_sermons_live_poll();
		WP_CLI::log( wp_json_encode( get_option( 'mpcbc_sermons_live' ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
	} );
}
