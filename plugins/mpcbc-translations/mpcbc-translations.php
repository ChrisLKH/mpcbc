<?php
/**
 * Plugin Name: MPCBC Translations
 * Description: One page, three languages. /zh-hant/… and /zh-hans/… serve the same page in 繁體 or 简体, chosen on the server from the 繁體中文 / 简体中文 fields on Elementor Atomic Heading, Paragraph and Button. Empty 简体 is converted from 繁體 (OpenCC). Adds lang, hreflang and the language switcher links.
 * Version: 0.2.0
 * Text Domain: mpcbc-translations
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Textarea_Control;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;

const MPCBC_LANGS = array( 'en' => 'en', 'zh-hant' => 'zh-Hant', 'zh-hans' => 'zh-Hans' );

/* ---------- Language from the URL ---------- */

// Plugins load before WordPress reads the request: drop the language prefix so /zh-hant/about/ resolves to the About page.
$mpcbc_lang = 'en';
$mpcbc_base = untrailingslashit( (string) wp_parse_url( get_option( 'home' ), PHP_URL_PATH ) );
if ( ! is_admin() && isset( $_SERVER['REQUEST_URI'] )
	&& preg_match( '#^' . preg_quote( $mpcbc_base, '#' ) . '/(zh-han[ts])(?=[/?]|$)(.*)$#s', $_SERVER['REQUEST_URI'], $m ) ) {
	$mpcbc_lang = $m[1];
	$_SERVER['REQUEST_URI'] = $mpcbc_base . ( '' === $m[2] || '/' !== $m[2][0] ? '/' : '' ) . $m[2];
}
define( 'MPCBC_LANG', $mpcbc_lang );
// The request without home path or language prefix, e.g. "/about/?x=1".
define( 'MPCBC_PATH', (string) substr( $_SERVER['REQUEST_URI'] ?? '/', strlen( $mpcbc_base ) ) ?: '/' );
unset( $mpcbc_lang, $mpcbc_base, $m );

function mpcbc_lang() { return MPCBC_LANG; }

/** This request (or $path) in another language. */
function mpcbc_url( $lang, $path = MPCBC_PATH ) {
	return untrailingslashit( get_option( 'home' ) ) . ( 'en' === $lang ? '' : '/' . $lang ) . $path;
}

/** Hand-written 简体 that the page conversion must leave alone. */
function mpcbc_keep( $html ) { return '<!--mpcbc-keep-->' . $html . '<!--/mpcbc-keep-->'; }

// Every link WordPress builds (menus, permalinks, canonical) stays in the current language.
add_filter( 'home_url', function ( $url, $path, $scheme ) {
	if ( 'en' === MPCBC_LANG || is_admin() || preg_match( '#^/?wp-#', (string) $path ) ) return $url;
	$home = untrailingslashit( set_url_scheme( get_option( 'home' ), $scheme ) );
	if ( 0 !== strpos( $url, $home ) ) return $url;
	$rest = substr( $url, strlen( $home ) );
	return $home . '/' . MPCBC_LANG . ( '' === $rest || '/' !== $rest[0] ? '/' : '' ) . $rest;
}, 10, 3 );

// WordPress builds canonical redirects from the prefix-less request: put the prefix back, and never redirect to this same address.
add_filter( 'redirect_canonical', function ( $redirect ) {
	if ( 'en' === MPCBC_LANG || ! $redirect ) return $redirect;
	$home = untrailingslashit( set_url_scheme( get_option( 'home' ), wp_parse_url( $redirect, PHP_URL_SCHEME ) ) );
	$prefixed = $home . '/' . MPCBC_LANG;
	if ( 0 === strpos( $redirect, $home ) && 0 !== strpos( $redirect . '/', $prefixed . '/' ) ) {
		$rest = substr( $redirect, strlen( $home ) );
		$redirect = $prefixed . ( '' === $rest || '/' !== $rest[0] ? '/' : '' ) . $rest;
	}
	return $redirect === set_url_scheme( mpcbc_url( MPCBC_LANG ), wp_parse_url( $redirect, PHP_URL_SCHEME ) ) ? false : $redirect;
} );

add_filter( 'language_attributes', function ( $attributes ) {
	return is_admin() ? $attributes : preg_replace( '/\blang="[^"]*"/', 'lang="' . MPCBC_LANGS[ MPCBC_LANG ] . '"', $attributes );
} );

add_action( 'wp_head', function () {
	if ( is_404() || is_search() ) return;
	$path = strtok( MPCBC_PATH, '?' );
	foreach ( MPCBC_LANGS as $lang => $tag ) {
		printf( '<link rel="alternate" hreflang="%s" href="%s">' . "\n", esc_attr( $tag ), esc_url( mpcbc_url( $lang, $path ) ) );
	}
	printf( '<link rel="alternate" hreflang="x-default" href="%s">' . "\n", esc_url( mpcbc_url( 'en', $path ) ) );
}, 1 );

/* ---------- 繁 → 简 ---------- */

require_once __DIR__ . '/t2s.php';

/** The whole 简体 page is converted at once; hand-written 简体 (mpcbc_keep) is skipped. */
function mpcbc_simplify_html( $html ) {
	$parts = preg_split( '#(<!--mpcbc-keep-->.*?<!--/mpcbc-keep-->)#s', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
	foreach ( $parts as $i => $part ) {
		if ( 0 === $i % 2 ) $parts[ $i ] = mpcbc_t2s( $part );
	}
	return implode( '', $parts );
}

/** Links saved as full addresses (menu custom links, Elementor buttons) skip home_url, so prefix them here. */
function mpcbc_prefix_links( $html ) {
	$host = preg_quote( (string) wp_parse_url( get_option( 'home' ), PHP_URL_HOST ), '#' );
	return preg_replace_callback( '#<a\s[^>]*>#i', function ( $tag ) use ( $host ) {
		if ( false !== stripos( $tag[0], 'hreflang=' ) ) return $tag[0]; // language switcher links point at a language on purpose
		return preg_replace_callback( '#\bhref=(["\'])((?:https?:)?//' . $host . ')?(/(?!/)[^"\']*)\1#i', function ( $m ) {
			$path = strtok( $m[3], '?#' );
			if ( preg_match( '#^/(?:zh-han[ts]|wp-)(?:[/?\#]|$)|\.[a-z0-9]{2,5}$#i', $m[3] ) || preg_match( '#\.[a-z0-9]{2,5}$#i', (string) $path ) ) return $m[0];
			return 'href=' . $m[1] . $m[2] . '/' . MPCBC_LANG . $m[3] . $m[1];
		}, $tag[0] );
	}, $html );
}

add_action( 'template_redirect', function () {
	if ( 'en' === MPCBC_LANG ) return;
	ob_start( function ( $html ) {
		$html = mpcbc_prefix_links( $html );
		return 'zh-hans' === MPCBC_LANG ? mpcbc_simplify_html( $html ) : $html;
	} );
}, 0 );

/* ---------- Chinese fields on Elementor Atomic widgets ---------- */

// Element type => the setting that holds its main (usually English) text.
const MPCBC_TRANSLATABLE = array( 'e-heading' => 'title', 'e-paragraph' => 'paragraph', 'e-button' => 'text' );

// ponytail: these Atomic filters are internal in Elementor 4.3; re-check the editor panel after each Elementor update.
add_filter( 'elementor/atomic-widgets/props-schema', function ( $schema ) {
	if ( ! array_intersect_key( $schema, array_flip( MPCBC_TRANSLATABLE ) ) ) return $schema;
	$schema['mpcbc_zh_hant'] = String_Prop_Type::make();
	$schema['mpcbc_zh_hans'] = String_Prop_Type::make();
	return $schema;
} );

add_filter( 'elementor/atomic-widgets/controls', function ( $controls, $element ) {
	if ( ! isset( MPCBC_TRANSLATABLE[ $element->get_name() ] ) ) return $controls;
	array_splice( $controls, 1, 0, array(
		Section::make()
			->set_label( __( 'Chinese 中文', 'mpcbc-translations' ) )
			->set_id( 'mpcbc-translation' )
			->set_items( array(
				Textarea_Control::bind_to( 'mpcbc_zh_hant' )
					->set_label( __( '繁體中文', 'mpcbc-translations' ) )
					->set_placeholder( __( 'Empty: the main text shows', 'mpcbc-translations' ) ),
				Textarea_Control::bind_to( 'mpcbc_zh_hans' )
					->set_label( __( '简体中文 (only if the automatic conversion is wrong)', 'mpcbc-translations' ) )
					->set_placeholder( __( 'Empty: converted from 繁體中文', 'mpcbc-translations' ) ),
			) ),
	) );
	return $controls;
}, 10, 2 );

/** Swap the element's main text for the current language's text. */
add_filter( 'elementor/widget/render_content', function ( $html, $widget ) {
	$key = MPCBC_TRANSLATABLE[ $widget->get_name() ] ?? null;
	if ( ! $key || 'en' === MPCBC_LANG ) return $html;
	$settings = $widget->get_atomic_settings();
	$hant = trim( (string) ( $settings['mpcbc_zh_hant'] ?? '' ) );
	$hans = trim( (string) ( $settings['mpcbc_zh_hans'] ?? '' ) );
	// 简体: hand-written if set, else 繁體 (converted with the page), else the main text.
	$text = 'zh-hans' === MPCBC_LANG && '' !== $hans ? mpcbc_keep( nl2br( wp_kses_post( $hans ) ) ) : ( '' !== $hant ? nl2br( wp_kses_post( $hant ) ) : '' );
	if ( '' === $text ) return $html;
	// Elementor prints the main text through striptags; find that exact string to replace it.
	$main = trim( strip_tags( (string) ( $settings[ $key ] ?? '' ), '<b><strong><sup><sub><s><em><i><u><ul><ol><li><blockquote><a><del><span><br>' ) );
	$at = '' === $main ? false : strrpos( $html, $main );
	return false === $at ? $html : substr_replace( $html, $text, $at, strlen( $main ) );
}, 10, 2 );

/* ---------- Language switcher (the menu item whose children are English / 繁體中文 / 简体中文) ---------- */

const MPCBC_LANGUAGE_LABELS = array( 'English' => 'en', '繁體中文' => 'zh-hant', '简体中文' => 'zh-hans' );

add_filter( 'wp_nav_menu_objects', function ( $items ) {
	$parents = array();
	foreach ( $items as $item ) {
		if ( in_array( $item->title, array( '繁體中文', '简体中文' ), true ) ) $parents[] = (int) $item->menu_item_parent;
	}
	foreach ( $items as $item ) {
		if ( in_array( (int) $item->ID, $parents, true ) ) {
			// The parent shows the current language and links to this page.
			$item->classes[] = 'mpcbc-language-menu';
			$item->title = mpcbc_keep( esc_html( array_search( MPCBC_LANG, MPCBC_LANGUAGE_LABELS, true ) ) );
			$item->url = mpcbc_url( MPCBC_LANG );
		} elseif ( in_array( (int) $item->menu_item_parent, $parents, true ) && isset( MPCBC_LANGUAGE_LABELS[ $item->title ] ) ) {
			$lang = MPCBC_LANGUAGE_LABELS[ $item->title ];
			$item->url = mpcbc_url( $lang );
			$item->classes[] = 'mpcbc-locale-' . $lang;
			$item->title = mpcbc_keep( esc_html( $item->title ) );
		}
	}
	return $items;
} );

add_filter( 'nav_menu_link_attributes', function ( $atts, $item ) {
	foreach ( MPCBC_LANGS as $lang => $tag ) {
		if ( ! in_array( 'mpcbc-locale-' . $lang, (array) $item->classes, true ) ) continue;
		$atts['data-mpcbc-locale'] = $lang;
		$atts['hreflang'] = $tag;
		$atts['lang'] = $tag;
		// WordPress marks the "/" English link as the current page on Home; only the current language is current.
		if ( MPCBC_LANG === $lang ) $atts['aria-current'] = 'true';
		else unset( $atts['aria-current'] );
	}
	return $atts;
}, 10, 2 );
