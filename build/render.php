<?php
/**
 * Render one page of site/index.php to stdout for the static build (build/build_static.py).
 *
 *   php build/render.php            the hub
 *   php build/render.php texas      one state, by its folder name
 *   php build/render.php 404        the not-found page
 *
 * Same code path as the live PHP pages: index.php only sees VOTE_SLUG, as a state folder's stub sets it.
 */
if ( isset( $argv[1] ) && '404' === $argv[1] ) {
	define( 'VOTE_404', true );
} elseif ( isset( $argv[1] ) && $argv[1] !== '' ) {
	define( 'VOTE_SLUG', $argv[1] );
}
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__ ) . '/site/index.php';
