<?php
/**
 * Plugin Name: MPCBC Translations
 * Description: Adds 繁體中文 and 简体中文 fields to Elementor Atomic Heading, Paragraph and Button. The header language switch shows the matching text; empty Simplified is converted from Traditional in the browser.
 * Version: 0.1.0
 * Text Domain: mpcbc-translations
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Textarea_Control;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;

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

/** Wrap the element's text in the variants the language switch chooses between (see the CSS below). */
add_filter( 'elementor/widget/render_content', function ( $html, $widget ) {
	$key = MPCBC_TRANSLATABLE[ $widget->get_name() ] ?? null;
	if ( ! $key ) return $html;
	$settings = $widget->get_atomic_settings();
	$hant = trim( (string) ( $settings['mpcbc_zh_hant'] ?? '' ) );
	$hans = trim( (string) ( $settings['mpcbc_zh_hans'] ?? '' ) );
	if ( '' === $hant && '' === $hans ) return $html;
	// Elementor prints the main text through striptags; find that exact string to wrap it.
	$main = trim( strip_tags( (string) ( $settings[ $key ] ?? '' ), '<b><strong><sup><sub><s><em><i><u><ul><ol><li><blockquote><a><del><span><br>' ) );
	$at = '' === $main ? false : strrpos( $html, $main );
	if ( false === $at ) return $html;
	$out = '<span class="mpcbc-i18n"' . ( '' !== $hant ? ' data-hant' : '' ) . ( '' !== $hans ? ' data-hans' : '' ) . '><span data-main>' . $main . '</span>';
	if ( '' !== $hant ) $out .= '<span data-zh-hant lang="zh-Hant">' . nl2br( wp_kses_post( $hant ) ) . '</span>';
	if ( '' !== $hans ) $out .= '<span data-zh-hans lang="zh-Hans">' . nl2br( wp_kses_post( $hans ) ) . '</span>';
	return substr_replace( $html, $out . '</span>', $at, strlen( $main ) );
}, 10, 2 );

// The theme sets html[data-mpcbc-reading-language] before the page paints.
// English: main text. 繁體: Traditional if set. 简体: Simplified if set, else Traditional (converted by the theme), else main text.
add_action( 'wp_head', function () {
	$t = 'html[data-mpcbc-reading-language="zh-hant"] .mpcbc-i18n';
	$s = 'html[data-mpcbc-reading-language="zh-hans"] .mpcbc-i18n';
	echo '<style id="mpcbc-translations">.mpcbc-i18n>[data-zh-hant],.mpcbc-i18n>[data-zh-hans],'
		. "{$t}[data-hant]>[data-main],{$s}[data-hans]>[data-main],{$s}[data-hant]:not([data-hans])>[data-main]{display:none}"
		. "{$t}>[data-zh-hant],{$s}[data-hans]>[data-zh-hans],{$s}[data-hant]:not([data-hans])>[data-zh-hant]{display:inline}</style>\n";
}, 20 );
