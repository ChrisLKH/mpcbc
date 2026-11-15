<?php
// No WordPress here, so scripts/check-t2s.mjs can run it with plain PHP.

/** OpenCC Hong Kong → Simplified, same dictionaries and order as opencc-js Converter({ from: 'hk', to: 'cn' }). */
function mpcbc_t2s( $text ) {
	static $d;
	if ( ! preg_match( '/\p{Han}/u', $text ) ) return $text;
	$d = $d ?: require __DIR__ . '/t2s-data.php';
	$text = strtr( strtr( $text, $d['normalize'] ), $d['segments'] );
	$text = str_replace( "\x1F", '', strtr( strtr( $text, $d['step1'] ), $d['step2'] ) );
	// Word swaps from the build brief. ponytail: 国语堂 (the congregation name) keeps its short form; ask leadership if that's wrong.
	return preg_replace( '/国语(?![（(]普通话|堂)/u', '国语（普通话）', $text );
}
