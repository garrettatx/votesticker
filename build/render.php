<?php
/**
 * Render one page of site/index.php to stdout for the static build (build/build_static.py).
 *
 *   php build/render.php            the hub
 *   php build/render.php texas      one state, by its folder name
 *   php build/render.php 404        the not-found page
 *   php build/render.php about      the About page (also: es/about)
 *   php build/render.php history    the sticker history page (also: es/history)
 *   php build/render.php cal/texas  the Texas calendar file (also: es/cal/texas)
 *   php build/render.php es/texas   the Spanish page (also: es, es/404)
 *
 * Same code path as the live PHP pages: index.php only sees VOTE_SLUG, as a state folder's stub sets it.
 */
$arg = $argv[1] ?? '';
if ( 'es' === $arg || 0 === strpos( $arg, 'es/' ) ) {
	define( 'VOTE_LANG', 'es' );
	$arg = (string) substr( $arg, 3 );
}
if ( '404' === $arg ) {
	define( 'VOTE_404', true );
} elseif ( 0 === strpos( $arg, 'cal/' ) ) {
	define( 'VOTE_CAL', true );  // the state's .ics file (vote_ics() in index.php)
	define( 'VOTE_SLUG', substr( $arg, 4 ) );
} elseif ( 'about' === $arg || 'history' === $arg ) {
	define( 'VOTE_PAGE', $arg );
} elseif ( '' !== $arg ) {
	define( 'VOTE_SLUG', $arg );
}
$_SERVER['REQUEST_URI'] = defined( 'VOTE_LANG' ) ? '/es/' : '/';
require dirname( __DIR__ ) . '/site/index.php';
