<?php
/**
 * Pure YouTube-title parser (no WordPress calls, so selftest.php can run it with plain php).
 * Pattern: [Series (n):] "Title"  Scripture ~ Speaker  [congregation marker + date]
 */

function mpcbc_sermons_clean( $s ) {
	return trim( preg_replace( '/\s+/u', ' ', (string) $s ) );
}

/** @return array series, title, scripture, speaker, ministries[], date (Y-m-d or ''), missing[], ok */
function mpcbc_sermons_parse( $raw, $channel = 'zh' ) {
	$out = array( 'series' => '', 'title' => '', 'scripture' => '', 'speaker' => '', 'ministries' => array(), 'date' => '', 'missing' => array() );
	$s   = trim( preg_replace( '/[\x{00A0}\x{3000}]/u', ' ', (string) $raw ) );
	$q   = '"\x{201C}\x{201D}\x{FF02}';

	// Older English titles: "03.19.2023 CONNECT MINISTRY- “Title” ..." -> keep the date, drop the prefix.
	if ( preg_match( '/^(\d{1,2})\.(\d{1,3})\.\s*(\d{4})\s*(?:(?:MPCBC\s+)?CONNECT MINISTRY(?:\s+WORSHIP)?\s*[-–]?\s*)?(.*)$/iu', $s, $m ) ) {
		$out['date'] = sprintf( '%04d-%02d-%02d', $m[3], $m[1], (int) $m[2] );
		$s           = $m[4];
	}
	// 2021 Cantonese: "<marker + date> 題目: Title 講員: Speaker" -> "Title ~ Speaker <marker + date>".
	if ( preg_match( '/^(.*?)\s*[題题]目\s*[:：]\s*(.+?)\s*[講讲]員\s*[:：]\s*(.+)$/su', $s, $m ) ) {
		$s = "{$m[2]} ~ {$m[3]} 慕道園 {$m[1]}";
	}
	// Bilingual joint service: "English" ~ Speaker  "中文" ~ 講員 -> both titles, the first speaker.
	$joint = preg_match( '/^\s*[' . $q . '](.+?)[' . $q . ']\s*[~～]\s*(.+?)\s+[' . $q . '](.+?)[' . $q . ']\s*[~～]\s*(.+)$/su', $s, $m );
	if ( $joint ) $s = '"' . mpcbc_sermons_clean( $m[1] ) . ' / ' . mpcbc_sermons_clean( $m[3] ) . '" ~ ' . $m[2] . ' 慕道園 ' . $m[4];

	// Ministry: English channel is English; Chinese channel needs a congregation marker.
	// A bilingual or unqualified joint/combined service is all three congregations.
	if ( $joint || ( preg_match( '/[聯联]合崇拜|Combined Service/iu', $s ) && ! preg_match( '/[國国粵粤]/u', $s ) ) ) {
		$out['ministries'] = array( 'cantonese', 'mandarin', 'english' );
	} elseif ( 'en' === $channel ) {
		$out['ministries'] = array( 'english' );
	} elseif ( preg_match( '/[聯联]合/u', $s ) && preg_match( '/[國国]/u', $s ) && preg_match( '/[粵粤]/u', $s ) ) {
		$out['ministries'] = array( 'cantonese', 'mandarin' );
	} else {
		if ( preg_match( '/[粵粤][語语]堂/u', $s ) ) $out['ministries'][] = 'cantonese';
		if ( preg_match( '/[國国][語语]堂/u', $s ) ) $out['ministries'][] = 'mandarin';
	}

	// Date from the title: MM/DD/YYYY (or YY) or YYYY年 M月 D日.
	if ( $out['date'] ) {
		// already taken from the English prefix
	} elseif ( preg_match( '#(\d{1,2})/(\d{1,2})/(\d{4}|\d{2})\b#', $s, $m ) ) {
		$out['date'] = sprintf( '%04d-%02d-%02d', strlen( $m[3] ) === 2 ? 2000 + $m[3] : $m[3], $m[1], $m[2] );
	} elseif ( preg_match( '/(\d{4})\s*年\s*(\d{1,2})\s*月\s*(\d{1,2})\s*日/u', $s, $m ) ) {
		$out['date'] = sprintf( '%04d-%02d-%02d', $m[1], $m[2], $m[3] );
	}

	// Speaker follows the last "~" outside quotes ("~" also appears inside some quoted titles).
	$parts = preg_match( '/^(.*[' . $q . '][^' . $q . ']*?|[^' . $q . ']*?)\s*[~～]\s*([^~～]*)$/su', $s, $m ) ? array( $m[1], $m[2] ) : array( $s );
	$left  = $parts[0];
	if ( isset( $parts[1] ) ) { // Speaker: up to the congregation marker, a date, or the end.
		$cut = preg_split( '/\s*(?:慕道[園园]|\d{1,2}\/\d{1,2}\/\d{2,4}|\d{4}\s*年|\bMPCBC\b)/u', $parts[1], 2 );
		$out['speaker'] = rtrim( mpcbc_sermons_clean( preg_replace( '/^Speaker\s*:\s*/i', '', $cut[0] ) ), ' (' );
	}

	// Series prefix "Name (n):" (the quote exclusion stops "(一)" inside a quoted title matching).
	if ( preg_match( '/^([^' . $q . ']+?)\s*[\(（]\s*[^)）]{1,8}\s*[\)）]\s*[:：]\s*(.*)$/su', $left, $m ) ) {
		$out['series'] = mpcbc_sermons_clean( $m[1] );
		$left          = $m[2];
	}

	$quoted = preg_match( '/^[' . $q . ']\s*(.+?)\s*[' . $q . ']\s*(.*)$/su', $left, $m );
	if ( $quoted ) {
		$out['title']     = mpcbc_sermons_clean( $m[1] );
		$out['scripture'] = mpcbc_sermons_clean( $m[2] );
	} else { // ponytail: unquoted titles split on the last run of 2+ spaces; a single-spaced one stays whole.
		$bits = preg_split( '/\s{2,}/u', trim( $left ) );
		if ( count( $bits ) > 1 ) $out['scripture'] = mpcbc_sermons_clean( array_pop( $bits ) );
		$out['title'] = mpcbc_sermons_clean( implode( ' ', $bits ) );
	}

	if ( '' === $out['title'] ) $out['missing'][] = 'title';
	if ( '' === $out['speaker'] ) $out['missing'][] = 'speaker';
	if ( ! $out['ministries'] ) $out['missing'][] = 'congregation';
	$out['ok'] = ! $out['missing'];
	return $out;
}

function mpcbc_sermons_norm( $s ) {
	return mb_strtolower( mpcbc_sermons_clean( $s ), 'UTF-8' );
}
