<?php
/**
 * Vote Sticker (votesticker.org): voting stickers by state, sized for profile pictures.
 *
 * PHP renders the hub and each state page (titles, the state panel, links) and stamps asset URLs with
 * their modified time, so a deploy reaches every browser. The live site is static: build/build_static.py
 * runs this file once per page into dist/, which goes to Cloudflare Pages. Locally, `php -S` serves
 * site/ directly (the state folders hold two-line stubs that set VOTE_SLUG and load this file).
 * Source and notes: seo-command-center clients/hobby-sites/garrettn/docs/vote-cards/.
 */
header( 'Cache-Control: no-cache, max-age=0' );
header( 'Content-Type: text/html; charset=utf-8' );

function vote_json( $data ) {
	return json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG );
}

function vote_v( $file ) {
	$path = __DIR__ . '/' . $file;
	return VOTE_BASE . $file . '?v=' . ( is_file( $path ) ? filemtime( $path ) : '0' );
}

// Pages: the hub at / and one page per state at /<slug>/. Each state folder holds the same two-line
// index.php (written by build/state_pages.py) that sets VOTE_SLUG and loads this file.
// Rows come from states.js (one JSON object per line), the same data the cards use.
const VOTE_BASE = '/';
const VOTE_SITE = 'https://votesticker.org';
const VOTE_BRAND = 'Vote Sticker'; // the tool's name: header lockup, breadcrumbs, og:site_name, footer

// Language (October 19): English at /, Spanish at /es/ (the /es/ stubs define VOTE_LANG). Spanish strings,
// state names and state notes live in i18n/es.php, keyed by the English, so a missing key shows English
// and QA catches it. Spanish pages stay noindex, and hreflang stays off, until a native speaker has
// reviewed them; then set VOTE_ES_INDEX to true and rebuild.
const VOTE_ES_INDEX = true; // indexing on: Garrett, October 19 (native review to follow)
$vote_lang = defined( 'VOTE_LANG' ) ? VOTE_LANG : 'en';
$vote_i18n = 'es' === $vote_lang ? require __DIR__ . '/i18n/es.php' : array( 't' => array(), 'names' => array(), 'states' => array() );
function vote_es() { return 'es' === $GLOBALS['vote_lang']; }
function t( $s, ...$args ) { $o = $GLOBALS['vote_i18n']['t'][ $s ] ?? $s; return $args ? vsprintf( $o, $args ) : $o; }
// Page links carry the language prefix; images, scripts and fonts are shared (VOTE_BASE).
function vote_pbase() { return vote_es() ? VOTE_BASE . 'es/' : VOTE_BASE; }
// State names stay in English on Spanish pages too (Garrett, October 19): voters search and see them that way.
function vote_lname( $st ) { return $st['name']; }
// A state's name in titles and headings: English writes Washington, DC for the District.
function vote_name( $st ) { return 'DC' === $st['abbr'] ? 'Washington, DC' : $st['name']; }
function vote_sfield( $st, $key ) { if ( ( 'ended' === ( $st['kind'] ?? '' ) || 'post' === vote_phase() ) && in_array( $key, array( 'summary', 'note', 'ribbon' ), true ) ) { return ''; } return vote_es() ? ( $GLOBALS['vote_i18n']['states'][ $st['abbr'] ][ $key ] ?? '' ) : ( $st[ $key ] ?? '' ); }
const VOTE_MESES = array( 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre' );
const VOTE_MES = array( 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sept', 'oct', 'nov', 'dic' );
const VOTE_DIAS = array( 'domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado' );

function vote_states() {
	$rows = array();
	foreach ( file( __DIR__ . '/states.js' ) as $line ) {
		$row = json_decode( rtrim( trim( $line ), ',' ), true );
		if ( isset( $row['abbr'] ) ) { $rows[ $row['abbr'] ] = vote_close_early( $row ); }
	}
	return $rows;
}
// Early voting that has closed (October 23): from the day after a state's last early voting day through
// Election Day, the row's kind becomes 'ended'. Titles, ribbons, summaries, post text and the default
// sticker then point to Election Day; the dates table still lists the window. The date is the build's
// (the daily build runs at 6:05 a.m. Central), or VOTE_TODAY=YYYY-MM-DD for QA.
function vote_today() { return getenv( 'VOTE_TODAY' ) ?: date( 'Y-m-d' ); }
// The election's phase (docs/election-lifecycle-plan-20261025.md): 'pre' until November 1, 'eday' on
// November 2 and 3 (the dated Election Day banner goes out a day early so no midnight build is needed),
// 'post' from November 4. Pages say dates, never "today" or "open now".
function vote_phase() { $d = vote_today(); return vote_between() || $d >= '2026-11-04' ? 'post' : ( $d >= '2026-11-02' ? 'eday' : 'pre' ); }
// Between elections (docs/election-lifecycle-plan-20261025.md), off until Garrett's December 1 review:
// VOTE_BETWEEN=1 builds the post pages without the finished election's dates, ballot fixes and tracker,
// and with an evergreen intro. The flag, not the date, turns it on, since states finish at different times.
function vote_between() { return '1' === getenv( 'VOTE_BETWEEN' ); }
function vote_close_early( $row ) {
	$today = vote_today();
	if ( 'verified' === ( $row['status'] ?? '' ) && 'in-person' === ( $row['kind'] ?? '' ) && ! empty( $row['end'] ) && $row['end'] < $today ) {
		$row['kind'] = 'ended';
		unset( $row['summary'], $row['note'], $row['dateLine'] );
	}
	if ( 'post' === vote_phase() ) { unset( $row['summary'], $row['note'], $row['dateLine'], $row['ribbon'] ); }
	return $row;
}

function vote_slug( $name ) { return trim( preg_replace( '/[^a-z]+/', '-', strtolower( $name ) ), '-' ); }
function vote_e( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
// Dates: English "October 19", "Oct 19", "Monday, October 19"; Spanish "19 de octubre", "19 oct",
// "lunes 19 de octubre".
function vote_long( $ymd ) { $t = strtotime( $ymd ); return vote_es() ? date( 'j', $t ) . ' de ' . VOTE_MESES[ date( 'n', $t ) - 1 ] : date( 'F j', $t ); }
function vote_short( $ymd ) { $t = strtotime( $ymd ); return vote_es() ? date( 'j', $t ) . ' ' . VOTE_MES[ date( 'n', $t ) - 1 ] : date( 'M j', $t ); }
function vote_day( $ymd ) { $t = strtotime( $ymd ); return vote_es() ? VOTE_DIAS[ date( 'w', $t ) ] . ' ' . vote_long( $ymd ) : date( 'l, F j', $t ); }
function vote_range( $a, $b ) {
	$same = date( 'n', strtotime( $a ) ) === date( 'n', strtotime( $b ) );
	if ( vote_es() ) { return $same ? date( 'j', strtotime( $a ) ) . '–' . vote_short( $b ) : vote_short( $a ) . '–' . vote_short( $b ); }
	return vote_short( $a ) . '–' . ( $same ? date( 'j', strtotime( $b ) ) : vote_short( $b ) );
}
function vote_upper( $s ) { return mb_strtoupper( $s, 'UTF-8' ); }
// Gallery columns on wide screens: one row up to 6, then rows as even as possible (7 and 8 make 4s).
function vote_cols( $n ) { return $n <= 6 ? max( $n, 1 ) : ( $n <= 8 ? 4 : 5 ); }
function vote_verified( $st ) { return 'verified' === ( $st['status'] ?? '' ); }
// Ready-made sets are English images, so Spanish pages show none (Garrett, October 19); the Spanish
// maker draws its own Spanish stickers.
function vote_files( $abbr ) {
	if ( vote_es() ) { return array(); }
	$files = array_map( 'basename', glob( __DIR__ . '/assets/states/' . strtolower( $abbr ) . '/*.jpg' ) ?: array() );
	natsort( $files );
	$files = array_values( array_filter( $files, function ( $f ) use ( $abbr ) { return vote_file_current( $abbr, $f ); } ) );
	return array_map( function ( $f ) use ( $abbr ) { return vote_v( 'assets/states/' . strtolower( $abbr ) . '/' . $f ); }, $files );
}
// Whether a ready-made still says something true (October 25). Its alt text records the message: after a
// state's early voting closes, "Vote early" leaves the gallery; after Election Day only the I voted family
// and "[State] votes" stay. The files keep their URLs; they just aren't shown.
function vote_file_current( $abbr, $file ) {
	static $alts = null, $rows = null;
	if ( null === $alts ) { $f = __DIR__ . '/assets/states/alts.json'; $alts = is_file( $f ) ? json_decode( file_get_contents( $f ), true ) : array(); }
	if ( null === $rows ) { $rows = vote_states(); }
	$alt = $alts[ preg_replace( '/\.jpg$/', '', $file ) ] ?? '';
	if ( ! preg_match( '/reading (.*)$/i', $alt, $m ) ) { return true; }
	$says = strtolower( $m[1] );
	if ( 'post' === vote_phase() ) { return (bool) preg_match( '/^(i voted|my ballot is in|[a-z. ]+ votes\b)/', $says ); }
	if ( 'ended' === ( $rows[ strtoupper( $abbr ) ]['kind'] ?? '' ) ) { return 0 !== strpos( $says, 'vote early' ); }
	return true;
}

// The state hero shows one standout ready-made, tilted (October 20, Garrett): the design named here,
// or the first in the set. Numbers match <abbr>-vote-N.jpg.
const VOTE_HERO = array( 'MS' => 5, 'MI' => 7, 'ID' => 7, 'NJ' => 5, 'OR' => 5, 'MN' => 6, 'NE' => 6, 'AL' => 6, 'AZ' => 6, 'CT' => 7, 'DE' => 5, 'GA' => 7, 'FL' => 5, 'HI' => 6, 'IL' => 8, 'AR' => 6, 'IN' => 5, 'KY' => 8, 'ME' => 6, 'LA' => 6, 'IA' => 8, 'MA' => 5, 'MT' => 5, 'NV' => 8, 'NH' => 8 );
// What each ready-made sticker shows, for alt text (site/assets/states/alts.json, read from the images).
function vote_alt( $src, $st, $i ) {
	static $alts = null;
	if ( null === $alts ) { $f = __DIR__ . '/assets/states/alts.json'; $alts = is_file( $f ) ? json_decode( file_get_contents( $f ), true ) : array(); }
	$key = preg_replace( '#^.*/|\.jpg.*$#', '', $src );
	return $alts[ $key ] ?? t( '%s vote sticker, ready-made design %s', vote_lname( $st ), $i + 1 );
}
// A state's status line as label + date ("Early voting" muted, "Oct 24–31" darker).
function vote_status_html( $status ) {
	$p = explode( ' · ', $status, 2 );
	return '<span class="when__label">' . vote_e( $p[0] ) . '</span>' . ( isset( $p[1] ) ? ' <span class="when__date">' . vote_e( $p[1] ) . '</span>' : '' );
}
function vote_hero_file( $abbr ) {
	$n = VOTE_HERO[ $abbr ] ?? 0;
	$k = strtolower( $abbr );
	if ( $n && is_file( __DIR__ . "/assets/states/$k/$k-vote-$n.jpg" ) && ! vote_es() && vote_file_current( $abbr, "$k-vote-$n.jpg" ) ) { return vote_v( "assets/states/$k/$k-vote-$n.jpg" ); }
	return vote_files( $abbr )[0] ?? '';
}

// Bordering states, for the "Neighboring States" links (AK and HI have none).
const VOTE_NEIGHBORS = array(
	'AL' => 'FL GA MS TN', 'AZ' => 'CA CO NV NM UT', 'AR' => 'LA MS MO OK TN TX', 'CA' => 'AZ NV OR',
	'CO' => 'AZ KS NE NM OK UT WY', 'CT' => 'MA NY RI', 'DE' => 'MD NJ PA', 'DC' => 'MD VA', 'FL' => 'AL GA',
	'GA' => 'AL FL NC SC TN', 'ID' => 'MT NV OR UT WA WY', 'IL' => 'IN IA KY MO WI', 'IN' => 'IL KY MI OH',
	'IA' => 'IL MN MO NE SD WI', 'KS' => 'CO MO NE OK', 'KY' => 'IL IN MO OH TN VA WV', 'LA' => 'AR MS TX',
	'ME' => 'NH', 'MD' => 'DE PA VA WV DC', 'MA' => 'CT NH NY RI VT', 'MI' => 'IN OH WI', 'MN' => 'IA ND SD WI',
	'MS' => 'AL AR LA TN', 'MO' => 'AR IL IA KS KY NE OK TN', 'MT' => 'ID ND SD WY', 'NE' => 'CO IA KS MO SD WY',
	'NV' => 'AZ CA ID OR UT', 'NH' => 'ME MA VT', 'NJ' => 'DE NY PA', 'NM' => 'AZ CO OK TX', 'NY' => 'CT MA NJ PA VT',
	'NC' => 'GA SC TN VA', 'ND' => 'MN MT SD', 'OH' => 'IN KY MI PA WV', 'OK' => 'AR CO KS MO NM TX',
	'OR' => 'CA ID NV WA', 'PA' => 'DE MD NJ NY OH WV', 'RI' => 'CT MA', 'SC' => 'GA NC', 'SD' => 'IA MN MT NE ND WY',
	'TN' => 'AL AR GA KY MS MO NC VA', 'TX' => 'AR LA NM OK', 'UT' => 'AZ CO ID NV WY', 'VT' => 'MA NH NY',
	'VA' => 'KY MD NC TN WV DC', 'WA' => 'ID OR', 'WV' => 'KY MD OH PA VA', 'WI' => 'IL IA MI MN', 'WY' => 'CO ID MT NE SD UT',
);

// Title, description, H1, lead, the red ribbon and the short status used in links, from one state row.
// Pages fall into families by how the state votes (mail, early, Election Day only, not yet checked); each
// family has its own lead, written per the state page spec of October 19, 2026.
function vote_meta( $st ) {
	if ( vote_es() ) { return vote_meta_es( $st ); }
	$name = vote_name( $st );
	$kind = vote_verified( $st ) ? ( $st['kind'] ?? 'none' ) : 'unchecked';
	$n = count( vote_files( $st['abbr'] ) );
	$save = $n ? 'Save a ready-made I voted sticker or make your own' : 'Make your own I voted or vote sticker';
	// The lead says what to do and the one date that matters (Garrett's review, October 21): no counts, no repeats.
	$grab = $n ? 'Grab a ready-made sticker or make your own, then set it as your profile picture.' : 'Make your sticker, then set it as your profile picture.';
	// After Election Day the page is about the sticker (docs/post-election-release.md). The title keeps the
	// voting method's own sticker ("I voted early" is a search that outlives early voting).
	if ( 'post' === vote_phase() ) {
		$also = 'mail' === $kind ? 'I Voted by Mail' : ( 'ended' === $kind ? 'I Voted Early' : '' );
		$also_l = 'mail' === $kind ? ' and I voted by mail' : ( 'ended' === $kind ? ' and I voted early' : '' );
		return array(
			'slug' => vote_slug( $st['name'] ),
			'title' => $name . ( $also ? ' I Voted and ' . $also . ' Stickers' : ' I Voted Stickers' ),
			'h1' => $name . ' I Voted Stickers',
			'crumb' => $name,
			'ribbon' => 'FREE I VOTED STICKERS',
			'lead' => vote_between() ? ( $n ? 'Free ' . $name . ' I voted stickers. Pick a design below and save it, or make your own.' : 'Make a free ' . $name . ' I voted sticker, then save it and share it.' )
				: ( $n ? 'Voted? Get your sticker. Pick a design below and save it, or make your own.' : 'Voted? Make your ' . $name . ' sticker, then save it and share it.' ),
			'desc' => 'Download free ' . $name . ' I voted' . $also_l . ' stickers, or make your own. Find official ballot resources for ' . $name . '.',
			'status' => 'I voted stickers',
			'family' => 'post',
			'name' => $st['name'],
			'index' => true,
		);
	}
	if ( 'mail' === $kind ) {
		$tag = 'Vote by Mail, Due ' . vote_short( $st['end'] );
		// The state's own return rule where it's verified (drop box by 8 p.m., received by 7 p.m.): states.js `ribbon`.
		$ribbon = $st['ribbon'] ?? 'VOTE BY MAIL, DUE ' . strtoupper( vote_short( $st['end'] ) );
		$fact = $name . ' votes by mail, with ballots due ' . vote_long( $st['end'] ) . '.';
		$lead = 'Your ballot came in the mail. Pick a day to send it back, then grab a sticker so your friends remember theirs.';
		$status = 'Votes by mail';
		$family = 'mail';
	} elseif ( 'ended' === $kind ) {
		$tag = 'Vote on Election Day, Nov 3';
		$ribbon = 'EARLY VOTING HAS ENDED. VOTE NOV 3';
		$fact = 'Early voting in ' . $name . ' ended ' . vote_day( $st['end'] ) . '. Election Day is Tuesday, November 3.';
		$lead = $grab . ' Early voting ended ' . vote_long( $st['end'] ) . '; vote on Election Day, Tuesday, November 3.';
		$status = 'Election Day · Nov 3';
		$family = 'early_over';
	} elseif ( 'in-person' === $kind && ! empty( $st['start'] ) && empty( $st['dateLine'] ) ) {
		$same = date( 'n', strtotime( $st['start'] ) ) === date( 'n', strtotime( $st['end'] ) );
		$range = vote_range( $st['start'], $st['end'] );
		$tag = 'Early Voting ' . $range;
		$ribbon = 'EARLY VOTING ' . strtoupper( $range );
		$fact = 'Early voting in ' . $name . ' runs ' . vote_long( $st['start'] ) . ' to ' . ( $same ? date( 'j', strtotime( $st['end'] ) ) : vote_long( $st['end'] ) ) . '.';
		$lead = $grab . ' Early voting runs ' . $range . '.';
		$status = 'Early voting · ' . $range;
		$family = 'early';
	} elseif ( 'in-person' === $kind && ! empty( $st['end'] ) ) {
		$tag = 'Early Voting Through ' . vote_short( $st['end'] );
		$ribbon = 'EARLY VOTING THROUGH ' . strtoupper( vote_short( $st['end'] ) );
		$fact = 'Early voting in ' . $name . ' runs through ' . vote_long( $st['end'] ) . '.';
		$lead = $grab . ' Early voting runs through ' . vote_short( $st['end'] ) . '.';
		$status = 'Early voting · through ' . vote_short( $st['end'] );
		$family = 'early';
	} elseif ( 'none' === $kind ) {
		$tag = 'Vote on Election Day, Nov 3';
		$ribbon = 'VOTE ON ELECTION DAY, NOV 3';
		$fact = $name . ' doesn’t have early voting open to everyone. Election Day is Tuesday, November 3.';
		$lead = $grab . ' Election Day is Tuesday, November 3. Can’t make it that day? Check whether you can vote absentee in ' . $name . '.';
		$status = 'Election Day · Nov 3';
		$family = 'eday';
	} else {
		$tag = 'Election Day Nov 3';
		$ribbon = 'ELECTION DAY, NOV 3';
		$fact = 'Election Day is Tuesday, November 3. Early voting dates for ' . $name . ' aren’t checked here yet.';
		$lead = $grab . ' Election Day is Tuesday, November 3. We haven’t confirmed ' . $name . '’s early voting dates yet, so these stickers say Election Day.';
		$status = 'Election Day · Nov 3';
		$family = 'unchecked';
	}
	return array(
		'slug' => vote_slug( $st['name'] ),
		// "I voted sticker" is the term people search (Ahrefs, October 19); long names drop "and Vote" to fit.
		// Once early voting closes the title names the sticker people still look for (October 25 review).
		'title' => 'ended' === $kind ? ( strlen( $name . ' I Voted and I Voted Early Stickers: Vote Nov 3' ) > 65 ? $name . ' I Voted Early Stickers: Vote Nov 3' : $name . ' I Voted and I Voted Early Stickers: Vote Nov 3' )
			: ( strlen( $name . ' I Voted and Vote Stickers: ' . $tag ) > 62 ? $name . ' I Voted Stickers: ' . $tag : $name . ' I Voted and Vote Stickers: ' . $tag ),
		'h1' => $name . ' I Voted and Vote Stickers',
		'crumb' => $name,
		'ribbon' => $ribbon,
		'lead' => $lead,
		'desc' => $fact . ' ' . $save . ', sized for the profile-picture circle. Free and nonpartisan.',
		'status' => $status,
		'family' => $family,
		'name' => $st['name'],
		// Every state page is indexable (Garrett, October 19): each has a ready-made set and verified FAQ
		// answers even where early voting dates aren't checked yet.
		'index' => true,
	);
}

// Spanish: same families and facts, in tú (project glossary, docs/spanish-style.md; es-mx-localization skill). Titles say
// "Stickers para votar en [estado]"; date ranges are day-first.
function vote_meta_es( $st ) {
	$name = vote_name( $st );
	$kind = vote_verified( $st ) ? ( $st['kind'] ?? 'none' ) : 'unchecked';
	$n = count( vote_files( $st['abbr'] ) );
	$save = $n ? 'Guarda un sticker listo para usar o crea el tuyo' : 'Crea tu propio sticker';
	$grab = $n ? 'Toma un sticker listo para usar o crea el tuyo, y ponlo de foto de perfil.' : 'Crea tu sticker y ponlo de foto de perfil.';
	if ( 'post' === vote_phase() ) {
		$also = 'mail' === $kind ? ' y Ya voté por correo' : ( 'ended' === $kind ? ' y Ya voté temprano' : '' );
		return array(
			'slug' => vote_slug( $st['name'] ),
			'title' => 'Stickers de Ya voté' . $also . ' en ' . $name,
			'h1' => 'Stickers de Ya voté en ' . $name,
			'crumb' => $name,
			'ribbon' => 'STICKERS DE YA VOTÉ, GRATIS',
			'lead' => vote_between() ? 'Stickers de Ya voté gratis para ' . $name . '. Crea el tuyo, guárdalo y compártelo.' : '¿Ya votaste? Consigue tu sticker. Crea el tuyo para ' . $name . ', guárdalo y compártelo.',
			'desc' => 'Crea gratis tu sticker de Ya voté para ' . $name . '. Encuentra también los recursos oficiales sobre tu boleta en ' . $name . '.',
			'status' => 'Stickers de Ya voté',
			'family' => 'post',
			'name' => $name,
			'index' => VOTE_ES_INDEX,
		);
	}
	if ( 'mail' === $kind ) {
		$tag = 'vota por correo hasta el ' . vote_short( $st['end'] );
		$ribbon = vote_sfield( $st, 'ribbon' ) ?: 'VOTA POR CORREO, HASTA EL ' . vote_upper( vote_short( $st['end'] ) );
		$fact = $name . ' vota por correo y las boletas deben entregarse a más tardar el ' . vote_long( $st['end'] ) . '.';
		$lead = 'Tu boleta te llegó por correo. Escoge un día para devolverla y luego toma un sticker para que tus amigos se acuerden de la suya.';
		$status = 'Vota por correo';
	} elseif ( 'ended' === $kind ) {
		$tag = 'vota el Día de las Elecciones, 3 nov';
		$ribbon = 'TERMINÓ LA VOTACIÓN ANTICIPADA. VOTA EL 3 NOV';
		$fact = 'La votación anticipada en ' . $name . ' terminó el ' . vote_day( $st['end'] ) . '. El Día de las Elecciones es el martes 3 de noviembre.';
		$lead = $grab . ' La votación anticipada terminó el ' . vote_long( $st['end'] ) . '; vota el Día de las Elecciones, martes 3 de noviembre.';
		$status = 'Día de las Elecciones · 3 nov';
	} elseif ( 'in-person' === $kind && ! empty( $st['start'] ) && empty( $st['dateLine'] ) ) {
		$range = vote_range( $st['start'], $st['end'] );
		$same = date( 'n', strtotime( $st['start'] ) ) === date( 'n', strtotime( $st['end'] ) );
		$tag = 'votación anticipada ' . $range;
		$ribbon = 'VOTACIÓN ANTICIPADA ' . vote_upper( $range );
		$fact = 'La votación anticipada en ' . $name . ' es del ' . ( $same ? date( 'j', strtotime( $st['start'] ) ) : vote_long( $st['start'] ) ) . ' al ' . vote_long( $st['end'] ) . '.';
		$lead = $grab . ' La votación anticipada es del ' . ( $same ? date( 'j', strtotime( $st['start'] ) ) : vote_long( $st['start'] ) ) . ' al ' . vote_long( $st['end'] ) . '.';
		$status = 'Votación anticipada · ' . $range;
	} elseif ( 'in-person' === $kind && ! empty( $st['end'] ) ) {
		$tag = 'votación anticipada hasta el ' . vote_short( $st['end'] );
		$ribbon = 'VOTACIÓN ANTICIPADA HASTA EL ' . vote_upper( vote_short( $st['end'] ) );
		$fact = 'La votación anticipada en ' . $name . ' es hasta el ' . vote_long( $st['end'] ) . '.';
		$lead = $grab . ' La votación anticipada es hasta el ' . vote_long( $st['end'] ) . '.';
		$status = 'Votación anticipada · hasta el ' . vote_short( $st['end'] );
	} elseif ( 'none' === $kind ) {
		$tag = 'vota el Día de las Elecciones, 3 nov';
		$ribbon = 'VOTA EL DÍA DE LAS ELECCIONES, 3 NOV';
		$fact = $name . ' no tiene votación anticipada abierta a todos. El Día de las Elecciones es el martes 3 de noviembre.';
		$lead = $grab . ' El Día de las Elecciones es el martes 3 de noviembre. ¿No puedes ir ese día? Revisa si puedes votar en ausencia en ' . $name . '.';
		$status = 'Día de las Elecciones · 3 nov';
	} else {
		$tag = 'Día de las Elecciones, 3 nov';
		$ribbon = 'DÍA DE LAS ELECCIONES, 3 NOV';
		$fact = 'El Día de las Elecciones es el martes 3 de noviembre. Aún no hemos verificado las fechas de votación anticipada de ' . $name . '.';
		$lead = $grab . ' El Día de las Elecciones es el martes 3 de noviembre. Aún no hemos verificado las fechas de votación anticipada de ' . $name . ', así que estos stickers dicen Día de las Elecciones.';
		$status = 'Día de las Elecciones · 3 nov';
	}
	return array(
		'slug' => vote_slug( $st['name'] ),
		'title' => 'ended' === $kind ? 'Stickers de Ya voté y Ya voté temprano en ' . $name . ': vota el 3 nov' : 'Stickers para votar en ' . $name . ': ' . $tag,
		'h1' => 'Stickers para votar en ' . $name,
		'crumb' => $name,
		'ribbon' => $ribbon,
		'lead' => $lead,
		'desc' => $fact . ' ' . $save . ', del tamaño del círculo de la foto de perfil. Gratis y no partidista.',
		'status' => $status,
		'family' => 'ended' === $kind ? 'early_over' : $kind,
		'name' => $name,
		'index' => VOTE_ES_INDEX,
	);
}

// Suggested post text, in the person's own voice to friends (Spanish uses tú there).
const VOTE_FAQ_CHECKED = '2026-10-19';
const VOTE_FAQ_Q = array(
	'polls'  => 'When are polls open in %s on Election Day?',
	'mail'   => 'When is the ballot deadline in %s?',
	'id'     => 'Do I need ID to vote in %s?',
	'reg'    => 'Can I still register to vote in %s?',
	'selfie' => 'Can I take a picture of my ballot in %s?',
);
// A state FAQ answer in the page language, or its after-wording once its `until` date has passed
// (build/faq_text.py; "Can I still register?" in CO, NC, WA, WI).
function vote_faq_text( $a ) {
	$lang = vote_es() ? 'es' : 'en';
	return ! empty( $a['until'] ) && vote_today() > $a['until'] && ! empty( $a[ $lang . '_after' ] ) ? $a[ $lang . '_after' ] : ( $a[ $lang ] ?? '' );
}
// The verified state questions for this page, in VOTE_FAQ_Q order: [question, answer, source URL].
function vote_state_faq( $st ) {
	static $all = null;
	if ( null === $all ) { $all = require __DIR__ . '/faq-states.php'; }
	$out = array();
	// After Election Day (post phase), questions about voting that's over leave the page and the FAQ
	// markup: poll hours, ballot and registration deadlines, lines, late mail. ID, ballot photos,
	// provisional ballots, tracking and envelope fixes can still apply (docs/post-election-release.md).
	$gone = 'post' === vote_phase() ? array( 'polls', 'mail', 'reg', 'dates.mail_return', 'faq.line', 'faq.late_mail' ) : array();
	// Between elections, ballot tracking, envelope fixes and provisional ballots are about a count that's done.
	if ( vote_between() ) { $gone = array_merge( $gone, array( 'faq.track', 'faq.unsigned', 'faq.provisional' ) ); }
	foreach ( VOTE_FAQ_Q as $key => $q ) {
		if ( in_array( $key, $gone, true ) ) { continue; }
		$a = $all[ $st['abbr'] ][ $key ] ?? null;
		if ( ! $a || empty( $a['src'] ) || empty( $a[ vote_es() ? 'es' : 'en' ] ) ) { continue; }
		$out[] = array( t( $q, vote_name( $st ) ), vote_faq_text( $a ), $a['src'], VOTE_FAQ_CHECKED );
	}
	// Extra questions answered in the state's facts file (build/state-facts/), each with its own check date.
	foreach ( VOTE_FACT_Q as $path => $q ) {
		if ( in_array( $path, $gone, true ) ) { continue; }
		if ( 'dates.mail_return' === $path && ( $all[ $st['abbr'] ]['mail'] ?? null ) ) { continue; }  // same question as the mail FAQ
		$f = vote_fact( $st, $path );
		if ( $f ) { $out[] = array( t( $q, vote_name( $st ) ), $f['text'], $f['src'], $f['checked'] ); }
	}
	return $out;
}
const VOTE_FACT_Q = array(
	'dates.mail_return' => 'When is the mail ballot deadline in %s?',
	'faq.provisional'   => 'What is a provisional ballot in %s, and does it count?',
	'faq.line'          => 'What if I am in line when the polls close in %s?',
	'faq.track'         => 'How do I check that my mail ballot was counted in %s?',
	'faq.unsigned'      => 'What if I forgot to sign my ballot envelope in %s?',
	'faq.late_mail'     => 'Is it too late to mail my ballot in %s?',
);

// The voting guide facts for a state (build/facts_text.py writes site/facts-states.php from
// build/state-facts/<abbr>.json). vote_fact() returns array( text, src, checked ) in the page language,
// a bare string for URLs, labels and rules, or null when the state has no verified fact (or, on a
// Spanish page, no Spanish text yet).
function vote_facts( $abbr ) {
	static $all = null;
	if ( null === $all ) { $f = __DIR__ . '/facts-states.php'; $all = is_file( $f ) ? require $f : array(); }
	return $all[ $abbr ] ?? array();
}
function vote_fact( $st, $path ) {
	$f = vote_facts( $st['abbr'] )[ $path ] ?? null;
	if ( ! is_array( $f ) ) { return $f; }
	if ( ! isset( $f['src'] ) ) { return $f[ vote_es() ? 'es' : 'en' ] ?: $f['en']; }  // a label in both languages
	$lang = vote_es() ? 'es' : 'en';
	// A fact with "until" switches to its after-text the day after that date (the build runs daily).
	$text = ! empty( $f['until'] ) && vote_today() > $f['until'] && ! empty( $f[ $lang . '_after' ] ) ? $f[ $lang . '_after' ] : ( $f[ $lang ] ?? '' );
	return $text ? array( 'text' => $text, 'src' => $f['src'], 'checked' => $f['checked'] ) : null;
}
function vote_host( $url ) { return preg_replace( '#^www\.#', '', (string) parse_url( $url, PHP_URL_HOST ) ); }
function vote_checked( $ymd ) { $t = strtotime( $ymd ); return vote_es() ? date( 'j', $t ) . ' de ' . VOTE_MESES[ date( 'n', $t ) - 1 ] . ' de ' . date( 'Y', $t ) : date( 'F j, Y', $t ); }
// A source's readable name ("the Texas Secretary of State"), from the facts file's `sources` map; the host otherwise.
function vote_src_name( $st, $url ) {
	$host = vote_host( $url );
	$map = vote_facts( $st['abbr'] )['sources'] ?? array();
	$n = $map[ $host ] ?? $map[ 'www.' . $host ] ?? null;
	return $n ? ( $n[ vote_es() ? 'es' : 'en' ] ?: $n['en'] ) : $host;
}
// One check date for the whole page: the newest of the dates, the FAQ research and the facts file.
function vote_page_checked( $st ) {
	$d = array( $st['checked'] ?? '', VOTE_FAQ_CHECKED );
	foreach ( vote_facts( $st['abbr'] ) as $f ) { if ( is_array( $f ) && ! empty( $f['checked'] ) ) { $d[] = $f['checked']; } }
	return max( $d );
}
// One line per section (Garrett's review, October 21): the sources it rests on, by name, and the page's
// check date. Replaces a "Source:" stamp under every paragraph.
function vote_sources_line( $st, $urls ) {
	$seen = array();
	foreach ( $urls as $u ) { if ( $u ) { $n = vote_src_name( $st, $u ); if ( ! isset( $seen[ $n ] ) ) { $seen[ $n ] = $u; } } }
	if ( ! $seen ) { return ''; }
	$links = array();
	foreach ( $seen as $n => $u ) { $links[] = '<a href="' . vote_e( $u ) . '" target="_blank" rel="noopener">' . vote_e( $n ) . '</a>'; }
	$list = count( $links ) > 1 ? implode( ', ', array_slice( $links, 0, -1 ) ) . ( vote_es() ? ' y ' : ' and ' ) . end( $links ) : $links[0];
	return '<p class="cite">' . ( vote_es() ? 'Fuente: ' : 'From ' ) . $list . '. ' . vote_e( t( 'Last checked %s.', vote_checked( vote_page_checked( $st ) ) ) ) . '</p>';
}
// A verified FAQ answer for the summary box: array( text, src ) or null.
function vote_faq_raw( $st, $key ) {
	static $all = null;
	if ( null === $all ) { $all = require __DIR__ . '/faq-states.php'; }
	$a = $all[ $st['abbr'] ][ $key ] ?? null;
	return $a && ! empty( $a['src'] ) && ! empty( $a[ vote_es() ? 'es' : 'en' ] ) ? array( vote_faq_text( $a ), $a['src'] ) : null;
}

// One "go deeper" line to a Resist Now Learn guide (Garrett, October 21): only where the guide answers the next
// question a reader of that section has. English only, so Spanish pages say so.
function vote_rn( $path, $lead, $label ) {
	return '<p class="guide__more">' . vote_e( t( $lead ) ) . ' <a href="https://resistnow.net/learn/' . $path . '/">' . vote_e( t( $label ) ) . '</a>' . ( vote_es() ? ' (en inglés)' : '' ) . '.</p>';
}

// The guide under the summary box: dates, where to vote, ID, mail voting, rules at the polls, the state's
// sticker tradition. Headings read the way people search; each section ends with one source line.
// Every block shows only when its facts are verified; a state without a facts file shows none of it.
function vote_guide( $st ) {
	if ( ! vote_facts( $st['abbr'] ) ) { return ''; }
	$name = 'DC' === $st['abbr'] ? 'DC' : $st['name'];
	$h = function ( $id, $text ) { return '<h2 class="display-h guide__h" id="' . $id . '">' . vote_e( $text ) . '</h2>'; };
	$out = '';

	// Key dates, as a table, with a next step after a deadline that has passed.
	$rows = array(); $srcs = array();
	$add = function ( $label, $f, $extra = '' ) use ( &$rows, &$srcs ) { if ( $f ) { $rows[] = '<tr><th scope="row">' . vote_e( t( $label ) ) . '</th><td>' . vote_e( $f['text'] ) . $extra . '</td></tr>'; $srcs[] = $f['src']; } };
	$loc = vote_fact( $st, 'where.locator_url' );
	$add( 'Register to vote by', vote_fact( $st, 'dates.reg_deadline' ), $loc ? ' <a href="' . vote_e( $loc ) . '" target="_blank" rel="noopener">' . vote_e( t( 'Already registered? Check here' ) ) . '</a>.' : '' );
	if ( vote_verified( $st ) && in_array( $st['kind'], array( 'in-person', 'ended' ), true ) && ! empty( $st['start'] ) ) {
		$rows[] = '<tr><th scope="row">' . vote_e( t( 'ended' === $st['kind'] && 'post' !== vote_phase() ? 'Early voting (ended)' : 'Early voting' ) ) . '</th><td>' . vote_e( vote_es() ? 'Del ' . vote_day( $st['start'] ) . ' al ' . vote_day( $st['end'] ) . ' de 2026.' : vote_day( $st['start'] ) . ' to ' . vote_day( $st['end'] ) . ', 2026.' ) . '</td></tr>';
		$srcs[] = $st['sourceUrl'] ?? $st['officialUrl'];
	}
	$add( 'Early voting hours', vote_fact( $st, 'dates.early_hours' ) );
	$add( 'Request a mail ballot by', vote_fact( $st, 'dates.mail_request' ) );
	if ( 'mail' !== ( $st['kind'] ?? '' ) ) { $add( 'Return a mail ballot by', vote_fact( $st, 'dates.mail_return' ) ); }  // mail states lead the summary box with it
	$add( 'Fix a problem with a mail ballot by', vote_fact( $st, 'dates.cure' ) );
	$rows[] = '<tr><th scope="row">' . vote_e( t( 'Election Day' ) ) . '</th><td>' . vote_e( vote_es() ? 'Martes 3 de noviembre de 2026.' : 'Tuesday, November 3, 2026.' ) . '</td></tr>';
	if ( count( $rows ) > 1 && ! vote_between() ) {
		$out .= '<section class="guide" aria-labelledby="dates-h">' . $h( 'dates-h', t( 'post' === vote_phase() ? 'November 3, 2026 Election: Past Voting Dates' : 'Key Dates for Nov. 3' ) )
			. '<table class="dates"><caption class="sr-only">' . vote_e( t( '%s voting dates and deadlines for the November 3, 2026 election', $name ) ) . '</caption><tbody>' . implode( '', $rows ) . '</tbody></table>' . vote_sources_line( $st, $srcs )
			. ( vote_fact( $st, 'dates.reg_deadline' ) && 'post' !== vote_phase() ? vote_rn( 'voter-registration', 'Missed a registration deadline?', 'What you can still do, state by state' ) : '' ) . '</section>';
	}

	// A section is a list of facts; each adds its source to the section's line.
	$section = function ( $id, $heading, $keys, $after = '' ) use ( $st, $h ) {
		$body = ''; $srcs = array();
		foreach ( $keys as $k ) { $f = vote_fact( $st, $k ); if ( $f ) { $body .= '<p>' . vote_e( $f['text'] ) . '</p>'; $srcs[] = $f['src']; } }
		if ( '' === $body && '' === $after ) { return ''; }
		return array( '<section class="guide" aria-labelledby="' . $id . '">' . $h( $id, $heading ) . $body, $srcs );
	};

	// After Election Day (docs/post-election-release.md) the guide keeps what can still apply: the past
	// dates above, fixing and tracking a mail ballot, and the state's sticker tradition. Where to vote, ID
	// and the rules at the polls are instructions for a vote that's over.
	$post = 'post' === vote_phase();

	// Where to vote.
	$w = $post ? '' : $section( 'where-h', t( 'mail' === ( $st['kind'] ?? '' ) && vote_verified( $st ) ? 'Drop Boxes and Voting Centers' : 'Where You Can Vote' ), array( 'how.in_person', 'where.rule', 'how.first_time' ) );
	if ( ! $post && ( $w || $loc ) ) {
		list( $html, $srcs ) = $w ?: array( '<section class="guide" aria-labelledby="where-h">' . $h( 'where-h', t( 'mail' === ( $st['kind'] ?? '' ) && vote_verified( $st ) ? 'Drop Boxes and Voting Centers' : 'Where You Can Vote' ) ), array() );
		// The locator card sits in the summary box above; here only the county offices link.
		$offices = vote_fact( $st, 'where.local_offices_url' );
		if ( $offices ) { $html .= '<p class="guide__more"><a href="' . vote_e( $offices ) . '" target="_blank" rel="noopener">' . vote_e( t( '%s county and local election offices', $name ) ) . '</a></p>'; }
		$out .= $html . vote_sources_line( $st, $srcs ) . '</section>';
	}

	// Voter ID: the accepted list, then expiry and the no-ID path.
	$ids = vote_facts( $st['abbr'] )['id.accepted'] ?? null;
	$list = $ids ? ( vote_es() ? $ids['es'] : $ids['en'] ) : array();
	$id = ''; $srcs = array();
	if ( $list && ! $post ) {
		$id .= '<p>' . vote_e( t( 'Bring one of these:' ) ) . '</p><ul class="guide__list">';
		foreach ( $list as $item ) { $id .= '<li>' . vote_e( $item ) . '</li>'; }
		$id .= '</ul>'; $srcs[] = $ids['src'];
	}
	foreach ( $post ? array() : array( 'id.expired', 'id.without' ) as $k ) { $f = vote_fact( $st, $k ); if ( $f ) { $id .= '<p>' . vote_e( $f['text'] ) . '</p>'; $srcs[] = $f['src']; } }
	if ( $id ) { $out .= '<section class="guide" aria-labelledby="id-h">' . $h( 'id-h', t( 'What ID to Bring' ) ) . $id . vote_sources_line( $st, $srcs ) . vote_rn( 'voter-id-laws', 'Forgot your ID, or want to see how other states compare?', 'Voter ID rules in every state' ) . '</section>'; }

	// Voting by mail: who qualifies (a list where the state has conditions), then the rest.
	$mail = ''; $srcs = array();
	$who = $post ? null : vote_fact( $st, 'mail.who' );
	if ( $who ) { $mail .= '<p>' . vote_e( $who['text'] ) . '</p>'; $srcs[] = $who['src']; }
	$wl = $post ? null : ( vote_facts( $st['abbr'] )['mail.who_list'] ?? null );
	$wlist = $wl ? ( vote_es() ? $wl['es'] : $wl['en'] ) : array();
	if ( $wlist ) { $mail .= '<ul class="guide__list guide__list--one">'; foreach ( $wlist as $item ) { $mail .= '<li>' . vote_e( $item ) . '</li>'; } $mail .= '</ul>'; $srcs[] = $wl['src']; }
	foreach ( $post ? array( 'mail.cure' ) : array( 'mail.witness', 'mail.dropbox', 'mail.cure' ) as $k ) { $f = vote_fact( $st, $k ); if ( $f ) { $mail .= '<p>' . vote_e( $f['text'] ) . '</p>'; $srcs[] = $f['src']; } }
	$track = vote_fact( $st, 'mail.track_url' );
	// The tracker joins a section that has something to say; on its own it waits for the Election Day banner.
	if ( $track && $mail ) { $mail .= '<p class="guide__more"><a href="' . vote_e( $track ) . '" target="_blank" rel="noopener">' . vote_e( t( 'Track your %s mail ballot', $name ) ) . '</a></p>'; }
	if ( $mail && ! vote_between() ) { $out .= '<section class="guide" aria-labelledby="mail-h">' . $h( 'mail-h', t( $post ? 'Check or Fix Your Mail Ballot' : ( 'mail' === ( $st['kind'] ?? '' ) && vote_verified( $st ) ? 'Returning Your Ballot' : 'Who Can Vote by Mail' ) ) ) . $mail . vote_sources_line( $st, $srcs ) . vote_rn( 'mail-in-voting', 'Why mail ballots get rejected, and how to fix a signature problem:', 'the mail voting guide' ) . '</section>'; }

	// Rules at the polls, then the state's own sticker tradition, which leads back to the maker.
	$p = $post ? '' : $section( 'polls-h', t( 'Phones, Photos, and Campaign Gear' ), array( 'polls_rules.gear', 'polls_rules.phone', 'polls_rules.line' ) );
	if ( $p ) { $out .= $p[0] . vote_sources_line( $st, $p[1] ) . '</section>'; }
	// The tradition, then where to get a sticker when you vote early or by mail (October 23). Each answer
	// names the county or city it comes from; a state gets one only with an official source for it.
	$sticker = '';
	$ssrc = array();
	foreach ( array( 'sticker.tradition' => '', 'sticker.early' => 'Do I get an I voted sticker if I vote early in %s?', 'sticker.mail' => 'I voted by mail in %s. How do I get a sticker?' ) as $k => $q ) {
		$f = vote_fact( $st, $k );
		if ( ! $f ) { continue; }
		$sticker .= ( $q ? '<h3>' . vote_e( t( $q, $name ) ) . '</h3>' : '' ) . '<p>' . vote_e( $f['text'] ) . '</p>';
		$ssrc[] = $f['src'];
	}
	if ( $sticker ) {
		$out .= '<section class="guide" aria-labelledby="sticker-h">' . $h( 'sticker-h', t( '%s and the I Voted Sticker', $name ) ) . $sticker . vote_history_more( $st )
			. '<p><a href="#maker-h">' . vote_e( t( 'Make your own %s I voted sticker', $name ) ) . '</a></p>' . vote_sources_line( $st, $ssrc ) . '</section>';
	}
	return $out;
}

require __DIR__ . '/history.php';

// Election Day countdown (October 22): "Election Day: Tuesday, Nov. 3 · 12 days away". The build writes
// the count for its own date (it runs daily); a small script in the footer recounts by the visitor's
// calendar date, says Tomorrow and Today, and drops the count after Election Day.
const VOTE_ELECTION_DAY = '2026-11-03';
function vote_countdown_text( $days ) {
	if ( $days < 0 ) { return ''; }
	if ( 0 === $days ) { return t( 'Today' ); }
	if ( 1 === $days ) { return t( 'Tomorrow' ); }
	return t( '%s days away', $days );
}
function vote_countdown( $class = '' ) {
	// From November 2 the line keeps the date and drops "Today" and "Tomorrow"; after Election Day it goes.
	if ( 'post' === vote_phase() ) { return ''; }
	if ( 'eday' === vote_phase() ) { return '<p class="countdown' . ( $class ? ' ' . $class : '' ) . '"><span>' . vote_e( t( 'Election Day:' ) ) . ' <time datetime="' . VOTE_ELECTION_DAY . '">' . vote_e( t( 'Tuesday, Nov. 3' ) ) . '</time></span></p>'; }
	$days = (int) round( ( strtotime( VOTE_ELECTION_DAY ) - strtotime( vote_today() ) ) / 86400 );
	$rel = vote_countdown_text( $days );
	return '<p class="countdown' . ( $class ? ' ' . $class : '' ) . '" data-countdown data-today="' . vote_e( t( 'Today' ) ) . '" data-tomorrow="' . vote_e( t( 'Tomorrow' ) ) . '" data-away="' . vote_e( t( '%s days away' ) ) . '">'
		. '<span>' . vote_e( t( 'Election Day:' ) ) . ' <time datetime="' . VOTE_ELECTION_DAY . '">' . vote_e( t( 'Tuesday, Nov. 3' ) ) . '</time></span> '
		. '<span class="countdown__rel"' . ( $rel ? '' : ' hidden' ) . '><span data-countdown-label>' . vote_e( $rel ) . '</span></span></p>';
}

// "Early voting ends" countdown (final-week loop item 2, October 26): above the Election Day line on a
// state page whose verified early voting is still open or ahead. Same markup and footer script as the
// Election Day countdown; data-hide-after hides the whole line once the visitor's date passes the end,
// and the next daily build drops it (the state's kind turns 'ended').
function vote_early_countdown( $st ) {
	if ( ! $st || 'post' === vote_phase() || ! vote_verified( $st ) || 'in-person' !== $st['kind'] || empty( $st['end'] ) || $st['end'] < vote_today() ) { return ''; }
	$t = strtotime( $st['end'] );
	$day = vote_es() ? VOTE_DIAS[ date( 'w', $t ) ] . ' ' . date( 'j', $t ) . ' de ' . VOTE_MESES[ date( 'n', $t ) - 1 ] : date( 'l, M. j', $t );
	$days = (int) round( ( $t - strtotime( vote_today() ) ) / 86400 );
	$rel = vote_countdown_text( $days );
	return '<p class="countdown countdown--state countdown--early" data-countdown data-hide-after data-today="' . vote_e( t( 'Today' ) ) . '" data-tomorrow="' . vote_e( t( 'Tomorrow' ) ) . '" data-away="' . vote_e( t( '%s days away' ) ) . '">'
		. '<span>' . vote_e( t( 'Early voting ends:' ) ) . ' <time datetime="' . vote_e( $st['end'] ) . '">' . vote_e( $day ) . '</time></span> '
		. '<span class="countdown__rel"' . ( $rel ? '' : ' hidden' ) . '><span data-countdown-label>' . vote_e( $rel ) . '</span></span></p>';
}

// The About page (/about/, /es/about/): who makes the site, where its facts come from, how to send a
// correction. Written for people first; the AboutPage schema in the head names the publisher.
function vote_about() {
	$h = function ( $id, $text ) { return '<h2 class="display-h guide__h" id="' . $id . '">' . vote_e( t( $text ) ) . '</h2>'; };
	$p = function ( $text ) { return '<p>' . $text . '</p>'; };
	$out = '<div class="about">';
	$out .= '<section class="guide guide--first" aria-labelledby="why-h">' . $h( 'why-h', 'Why It Exists' )
		. $p( vote_e( t( 'A voting sticker is a small thing that starts a conversation. Most of those conversations happen online now, so Vote Sticker puts the date that matters in your state, whether that is early voting, a mail-ballot deadline or Election Day, on your profile picture, where your friends will see it.' ) ) ) . '</section>';
	$out .= '<section class="guide" aria-labelledby="facts-h">' . $h( 'facts-h', 'Where the Information Comes From' )
		. $p( vote_e( t( 'Every date and voting rule on a state page is checked against an official source: a state election office, a Secretary of State, or state election law. Each state page links to its sources and shows when they were last checked.' ) ) )
		. $p( vote_e( t( "If we can't confirm a detail from an official source, we leave it out. States we're still checking show Election Day only." ) ) )
		. $p( vote_e( t( 'Rules change, and counties often set their own locations and hours. Vote Sticker is a reminder. Your state or local election office is the final authority.' ) ) . ' <a href="' . vote_e( t( 'https://www.usa.gov/state-election-office' ) ) . '" target="_blank" rel="noopener">' . vote_e( t( 'Find your state or local election office' ) ) . '</a>.' ) . '</section>';
	$out .= '<section class="guide" aria-labelledby="nonpartisan-h">' . $h( 'nonpartisan-h', 'Free and Nonpartisan' )
		. $p( vote_e( t( "Vote Sticker doesn't support any candidate, party, ballot measure or position, and it never will. There are no accounts, no email list and nothing to buy." ) ) ) . '</section>';
	// Privacy and accessibility (loop item 13, October 26): what the code does, nothing more. Analytics
	// and events are listed in docs/analytics.md; the remembered choices are app.js load()/save().
	$out .= '<section class="guide" aria-labelledby="privacy-h">' . $h( 'privacy-h', 'Privacy and Accessibility' )
		. $p( vote_e( t( "Your sticker is drawn in your browser, on your own device. The picture is never uploaded, and the site never asks for a photo, a name or an email address." ) ) )
		. $p( vote_e( t( "To see which state pages and designs people use, the site runs Google Analytics and Ahrefs Web Analytics. Google Analytics sets cookies to tell new visits from repeat ones. When you save a sticker or copy the post text, it records the state, design and message you picked, never the words of your post. If the state finder can't match what you typed, that search is logged so we can add the spelling." ) ) )
		. $p( vote_e( t( 'Your browser remembers the last design and color you picked, and your language choice, so they are there next time. That stays on your device.' ) ) )
		. $p( vote_e( t( 'Every ready-made sticker has a text description for screen readers. The state finder, the design and color choices and the save buttons work with a keyboard and show a visible focus outline, and motion effects turn off when your device is set to reduce motion.' ) ) )
		. $p( vote_e( t( "If part of the site doesn't work with your screen reader, keyboard or other tool," ) ) . ' <a href="https://www.garrettdigital.com/contact/">' . vote_e( t( 'tell us through the Garrett Digital contact page' ) ) . '</a> ' . vote_e( t( "and we'll fix it." ) ) ) . '</section>';
	$out .= '<section class="guide" aria-labelledby="who-h">' . $h( 'who-h', 'Who Made It' )
		. $p( vote_e( t( 'Vote Sticker is an independent public-service project built and maintained by' ) ) . ' <a href="https://www.garrettdigital.com/">Garrett Digital</a>, ' . vote_e( t( 'an SEO and search marketing consultancy in Austin, Texas.' ) ) )
		. $p( vote_e( t( "I'm Garrett Nafzinger. I built Vote Sticker in October 2026 because a clear voting reminder that people can share seemed like a useful thing to put into the world. It isn't affiliated with any government office, campaign or party." ) ) )
		. $p( vote_e( t( 'Day to day, Garrett Digital helps healthcare practices, online stores and service businesses get found on Google, turn visits into leads, and measure what works, through SEO, Google Ads, websites and analytics.' ) ) ) . '</section>';
	$out .= '<section class="guide" aria-labelledby="fix-h">' . $h( 'fix-h', 'Corrections' )
		. $p( vote_e( t( 'Spot something wrong or out of date?' ) ) . ' <a href="https://www.garrettdigital.com/contact/">' . vote_e( t( 'Tell us through the Garrett Digital contact page' ) ) . '</a> ' . vote_e( t( "and include the official source. We'll check it, fix the page, and show the new check date." ) ) )
		. '<p class="jump"><a class="btn btn--gold" href="' . vote_pbase() . '#state-find">' . vote_e( t( 'Get my sticker' ) ) . '</a></p></section>';
	return $out . '</div>';
}


// Suggested post text, one version per sticker message (rules and evidence: docs/post-text-rules.md).
// First person, one useful state fact, a plan question (never "tag a friend"), an invite back, and
// three hashtags that match the message. The maker swaps versions when the message changes.
const VOTE_POST_KINDS = array( 'pre_early', 'pre_mail', 'pre_day', 'post', 'post_early', 'post_mail' );
// Four versions of each (October 21): the plan post, a short one for Instagram and X, one written for
// the person's friends in the state, and one that leads with where to find the official information.
// Every version ends with the invite to make a sticker and three or four matching hashtags.
const VOTE_POST_VARIANTS = array( 'plan' => 'Plan', 'short' => 'Short', 'friends' => 'Friends', 'official' => 'Official info' );

// The version that matches the state's default sticker (app.js stateHeadline()).
function vote_post_default( $st ) {
	if ( 'post' === vote_phase() ) { return 'post'; }
	if ( ! vote_verified( $st ) || ! in_array( $st['kind'], array( 'in-person', 'mail' ), true ) ) { return 'pre_day'; }
	return 'mail' === $st['kind'] ? 'pre_mail' : 'pre_early';
}

function vote_post( $st, $kind = null, $variant = 'plan' ) {
	$kind = $kind ?: vote_post_default( $st );
	$es = vote_es();
	$name = vote_name( $st );
	$after = 0 === strpos( $kind, 'post' );
	$label = $st['officialLabel'] ?? '';
	$open = $es
		? array( 'pre_early' => 'Voy a votar temprano.', 'pre_mail' => 'Voy a entregar mi boleta con tiempo.', 'pre_day' => 'Voy a votar el Día de las Elecciones.', 'post' => 'Ya voté.', 'post_early' => 'Ya voté temprano.', 'post_mail' => 'Ya entregué mi boleta.' )
		: array( 'pre_early' => "I'm voting early.", 'pre_mail' => "I'm getting my ballot in early.", 'pre_day' => "I'm voting on Election Day.", 'post' => 'I voted.', 'post_early' => 'I voted early.', 'post_mail' => 'My ballot is in.' );
	$ask = $es
		? array( 'pre_mail' => '¿Cuándo vas a devolver tu boleta?', 'post' => '¿Y tú, ya tienes tu plan para votar?', 'default' => '¿Ya tienes tu plan para votar?' )
		: array( 'pre_mail' => 'When are you sending yours back?', 'post' => "What's your plan to vote?", 'default' => "What's your plan to vote?" );
	$question = $ask[ $after ? 'post' : $kind ] ?? $ask['default'];
	// After Election Day: what the person did and where the sticker is. No plan question, no "haven't voted
	// yet" (docs/post-election-release.md).
	if ( 'post' === vote_phase() ) {
		$link = 'https://votesticker.org/' . ( $es ? 'es/' : '' ) . 'go/' . vote_slug( $st['name'] ) . '/' . $variant;
		$tags = vote_hashtags( $st, $kind, $variant );
		$lines = $es
			? array( 'plan' => 'Consigue gratis un sticker para ' . $name . ' o crea el tuyo: ' . $link, 'short' => 'Stickers gratis para ' . $name . ': ' . $link,
				'friends' => 'Amigos en ' . $name . ', un sticker como el mío, gratis: ' . $link, 'official' => 'Haz el tuyo gratis en un minuto, sin cuenta: ' . $link )
			: array( 'plan' => 'Get a free ' . $name . ' sticker or make your own: ' . $link, 'short' => 'Free ' . $name . ' stickers: ' . $link,
				'friends' => $name . ' friends, a sticker like mine, free: ' . $link, 'official' => 'Make yours free in about a minute, no account: ' . $link );
		return $open[ $kind ] . ' ' . ( $lines[ $variant ] ?? $lines['plan'] ) . "\n" . $tags;
	}
	// Full https:// URL so it links everywhere, LinkedIn and email included. /go/ is a short redirect to the
	// state page with UTM tags (written into dist/_redirects by the build), so a friend who taps the
	// link in someone's post shows in GA4 as source sticker_post, with the post version as utm_content.
	$link = 'https://votesticker.org/' . ( $es ? 'es/' : '' ) . 'go/' . vote_slug( $st['name'] ) . '/' . $variant;
	$invite = $es
		? '¿Quieres uno? Haz gratis tu sticker para votar en ' . $name . ': ' . $link
		: 'Want one? Make your own free ' . $name . ' vote sticker: ' . $link;
	$fact = vote_post_fact( $st, $after );
	$where = vote_verified( $st ) && $label
		? ( $es ? 'Lugares y horarios en ' . $label . '.' : 'Polling places and hours are at ' . $label . '.' )
		: ( $es ? 'Lugares y horarios para ' . $name . ' en vote411.org.' : 'Polling places and hours for ' . $name . ' are at vote411.org.' );
	$tags = vote_hashtags( $st, $kind, $variant );
	if ( 'short' === $variant ) {
		$make = $es ? 'Haz gratis tu sticker para votar en ' . $name . ': ' . $link : 'Make your free ' . $name . ' vote sticker: ' . $link;
		return $open[ $kind ] . ' ' . $fact . "\n" . $make . "\n" . $tags;
	}
	if ( 'friends' === $variant ) {
		if ( $es ) {
			$lead = $after ? $open[ $kind ] . ' Amigos en ' . $name . ' que todavía no han votado: ' : 'Amigos en ' . $name . ': ';
			$mine = 'Un sticker como el mío, gratis: ' . $link;
		} else {
			$lead = $after ? $open[ $kind ] . ' ' . $name . " friends who haven't voted yet: " : $name . ' friends: ';
			$mine = 'A sticker like mine, free: ' . $link;
		}
		return $lead . vote_post_fact( $st, $after, false ) . ' ' . $where . "\n\n" . $mine . "\n" . $tags;
	}
	if ( 'official' === $variant ) {
		if ( $es ) {
			$q = '';
			$info = vote_verified( $st ) && $label ? 'Dónde votar en ' . $name . ', horarios y reglas de identificación: ' . $label . '.' : 'Dónde votar en ' . $name . ' y horarios: vote411.org.';
			$mine = 'Haz el tuyo gratis en un minuto, sin cuenta: ' . $link;
		} else {
			$q = '';
			$info = vote_verified( $st ) && $label ? 'Where to vote in ' . $name . ', hours, and ID rules: ' . $label . '.' : 'Where to vote in ' . $name . ' and hours: vote411.org.';
			$mine = 'Make yours free in about a minute, no account: ' . $link;
		}
		return ( $after ? $open[ $kind ] . ' ' : '' ) . $q . $info . ' ' . $fact . "\n\n" . $mine . "\n" . $tags;
	}
	return $open[ $kind ] . ' ' . vote_post_body( $st ) . ' ' . $question . "\n\n" . $invite . "\n" . $tags;
}

// One short fact for the short, friends and official versions: the dates before voting, "still time"
// after. Same verified data as the page. With $named false the sentence skips the state's name and
// starts lowercase, for a lead that already names the state ("Posting this for my friends in Texas:").
function vote_post_fact( $st, $after, $named = true ) {
	$es = vote_es();
	$name = vote_name( $st );
	$ok = vote_verified( $st );
	$kind = $ok ? $st['kind'] : '';
	$in = $named ? ( $es ? ' en ' . $name : ' in ' . $name ) : '';
	// ID, ND, PA and VT have no single start date (already open, or set by county): "through <end>" only.
	// Before October 26 a missing start printed PHP warnings into data-posts and "January 1" into the text.
	$start = $st['start'] ?? '';
	if ( $es ) {
		if ( 'in-person' === $kind ) {
			$same = $start && date( 'n', strtotime( $start ) ) === date( 'n', strtotime( $st['end'] ) );
			$f = $after ? ( $named && 'pre' === vote_phase() ? 'Todavía hay tiempo: la' : 'la' ) . ' votación anticipada' . $in . ' sigue hasta el ' . vote_long( $st['end'] ) . ', y el 3 de noviembre abren las casillas.'
				: ( $start ? 'la votación anticipada' . $in . ' es del ' . ( $same ? date( 'j', strtotime( $start ) ) : vote_long( $start ) ) . ' al ' . vote_long( $st['end'] ) . '.'
					: 'la votación anticipada' . $in . ' sigue hasta el ' . vote_long( $st['end'] ) . '.' );
		} elseif ( 'mail' === $kind ) {
			$f = $after ? ( $named ? 'Si todavía no has entregado la tuya,' . $in . ' la' : 'la' ) . ' boleta se recibe a más tardar el ' . vote_long( $st['end'] ) . '.'
				: ( $named ? $name . ' vota por correo. La' : 'la' ) . ' boleta se entrega a más tardar el ' . vote_long( $st['end'] ) . '.';
		} else {
			$f = $after ? ( $named ? 'Si todavía no has votado,' . $in . ' las' : 'las' ) . ' casillas abren el martes 3 de noviembre.'
				: 'el Día de las Elecciones' . $in . ' es el martes 3 de noviembre.';
		}
	} elseif ( 'in-person' === $kind ) {
		$same = $start && date( 'n', strtotime( $start ) ) === date( 'n', strtotime( $st['end'] ) );
		$f = $after ? ( $named && 'pre' === vote_phase() ? "There's still time: early" : 'early' ) . ' voting' . $in . ' runs through ' . vote_long( $st['end'] ) . ', and polls are open November 3.'
			: ( $start ? 'early voting' . $in . ' runs ' . vote_long( $start ) . ' to ' . ( $same ? date( 'j', strtotime( $st['end'] ) ) : vote_long( $st['end'] ) ) . '.'
				: 'early voting' . $in . ' runs through ' . vote_long( $st['end'] ) . '.' );
	} elseif ( 'mail' === $kind ) {
		$f = $after ? ( $named ? "If yours isn't in yet, " . $name . ' ballots' : 'ballots' ) . ' are due ' . vote_long( $st['end'] ) . '.'
			: ( $named ? $name . ' votes by mail. Ballots' : 'ballots' ) . ' are due ' . vote_long( $st['end'] ) . '.';
	} else {
		$f = $after ? ( $named ? "If you haven't yet, polls" . $in : 'polls' ) . ' are open Tuesday, November 3.' : 'Election Day' . $in . ' is Tuesday, November 3.';
	}
	return $named ? vote_ucfirst( $f ) : $f;
}
function vote_ucfirst( $s ) { return mb_strtoupper( mb_substr( $s, 0, 1 ) ) . mb_substr( $s, 1 ); }

// Three or four tags: the message's own tag, the brand, and the state (#VoteTexas). #IVoted only after
// voting. The friends and official versions swap in #Election2026 and the plain state tag.
function vote_hashtags( $st, $kind, $variant = 'plan' ) {
	$state = 'DC' === $st['abbr'] ? 'DC' : str_replace( ' ', '', $st['name'] );
	$es = vote_es();
	$lead = $es
		? array( 'pre_early' => '#VotaTemprano', 'pre_mail' => '#VotaPorCorreo', 'pre_day' => '#YoVoto' )
		: array( 'pre_early' => '#VoteEarly', 'pre_mail' => '#VoteByMail', 'pre_day' => '#ElectionDay' );
	$tag = $lead[ $kind ] ?? ( $es ? '#YaVoté' : '#IVoted' );
	$year = $es ? '#Elecciones2026' : '#Election2026';
	if ( 'friends' === $variant ) { return $tag . ' #' . $state . ' ' . $year . ' #VoteSticker'; }
	if ( 'official' === $variant ) { return '#Vote' . $state . ' ' . $tag . ' ' . $year . ' #VoteSticker'; }
	return $tag . ' #VoteSticker #Vote' . $state;
}

// Every message in every version, for the maker to swap in (JSON on the textarea).
function vote_post_all( $st ) {
	$out = array();
	foreach ( VOTE_POST_KINDS as $k ) {
		if ( 'post' === vote_phase() && 0 !== strpos( $k, 'post' ) ) { continue; }  // no plan posts after Election Day
		$out[ $k ] = array();
		foreach ( array_keys( VOTE_POST_VARIANTS ) as $v ) { $out[ $k ][ $v ] = vote_post( $st, $k, $v ); }
	}
	return $out;
}

function vote_post_body( $st ) {
	$label = $st['officialLabel'] ?? '';
	$name = vote_name( $st );
	if ( vote_es() ) {
		$sum = vote_sfield( $st, 'summary' );
		if ( vote_verified( $st ) && $sum ) { return $sum . ' Detalles en ' . $label . '.'; }
		if ( vote_verified( $st ) && 'mail' === $st['kind'] ) { return $name . ' vota por correo. Hay que devolver la boleta a más tardar el ' . vote_long( $st['end'] ) . '. Detalles en ' . $label . '.'; }
		if ( vote_verified( $st ) && 'in-person' === $st['kind'] ) {
			$same = date( 'n', strtotime( $st['start'] ) ) === date( 'n', strtotime( $st['end'] ) );
			return 'La votación anticipada en ' . $name . ' es del ' . ( $same ? date( 'j', strtotime( $st['start'] ) ) : vote_long( $st['start'] ) ) . ' al ' . vote_long( $st['end'] ) . '. Busca tu lugar de votación en ' . $label . '.';
		}
		if ( vote_verified( $st ) && 'ended' === $st['kind'] ) { return 'La votación anticipada en ' . $name . ' terminó el ' . vote_long( $st['end'] ) . '. El Día de las Elecciones es el martes 3 de noviembre de 2026. Busca tu lugar de votación en ' . $label . '.'; }
		if ( vote_verified( $st ) && 'none' === $st['kind'] ) { return 'El Día de las Elecciones en ' . $name . ' es el martes 3 de noviembre de 2026. Haz tu plan para votar. Los lugares de votación y las reglas del voto en ausencia están en ' . $label . '.'; }
		return 'El Día de las Elecciones es el martes 3 de noviembre de 2026. Haz tu plan para votar y revisa en vote411.org si puedes votar por anticipado en ' . $name . '.';
	}
	if ( vote_verified( $st ) && ! empty( $st['summary'] ) ) { return $st['summary'] . ' Details at ' . $label . '.'; }
	if ( vote_verified( $st ) && 'mail' === $st['kind'] ) { return $name . ' votes by mail. Return your ballot by ' . vote_long( $st['end'] ) . '. Details at ' . $label . '.'; }
	if ( vote_verified( $st ) && 'in-person' === $st['kind'] ) {
		$same = date( 'n', strtotime( $st['start'] ) ) === date( 'n', strtotime( $st['end'] ) );
		return 'Early voting in ' . $name . ' runs ' . vote_long( $st['start'] ) . ' to ' . ( $same ? date( 'j', strtotime( $st['end'] ) ) : vote_long( $st['end'] ) ) . '. Find your polling place at ' . $label . '.';
	}
	if ( vote_verified( $st ) && 'ended' === $st['kind'] ) { return 'Early voting in ' . $name . ' ended ' . vote_long( $st['end'] ) . '. Election Day is Tuesday, November 3, 2026. Find your polling place at ' . $label . '.'; }
	if ( vote_verified( $st ) && 'none' === $st['kind'] ) { return 'Election Day in ' . $name . ' is Tuesday, November 3, 2026. Make a plan to vote. Polling places and absentee rules are at ' . $label . '.'; }
	return 'Election Day is Tuesday, November 3, 2026. Make a plan to vote, and check whether you can vote early in ' . $name . ' at vote411.org.';
}

// The Election Day banner (November 2 and 3; docs/election-lifecycle-plan-20261025.md). Dated, in the page
// flow, no countdown and no "today": it says the schedule, links the official lookup, and points people
// who already voted at the stickers. Mail states lead with their verified return rule.
function vote_eday_banner( $st ) {
	if ( 'eday' !== vote_phase() ) { return ''; }
	$loc = vote_fact( $st, 'where.locator_url' );
	$track = vote_fact( $st, 'mail.track_url' );
	$off = vote_verified( $st ) ? $st['officialUrl'] : 'https://www.vote411.org/' . vote_slug( $st['name'] );
	$mail = vote_verified( $st ) && 'mail' === ( $st['kind'] ?? '' );
	$ret = $mail ? vote_fact( $st, 'dates.mail_return' ) : null;
	$hours = vote_fact( $st, 'election_day.hours' );
	$varies = ! empty( ( vote_facts( $st['abbr'] )['election_day.hours'] ?? array() )['varies'] );
	$sticker = count( vote_files( $st['abbr'] ) ) ? '#gallery' : '#maker-h';
	if ( $ret ) {
		$eyebrow = t( 'November 3, 2026 · Ballot return deadline' );
		$head = t( 'Still have your ballot? Check how to return it.' );
		$line = $ret['text'];
		$go = $loc ?: $off; $go_label = $loc ? ( vote_fact( $st, 'where.locator_cta' ) ?: t( 'Find a drop box or voting location' ) ) : t( 'Official election information' );
		$second = $track ? array( $track, t( 'Already returned it? Track your ballot.' ), true ) : array( $sticker, t( 'Already voted? Get your sticker.' ), false );
	} else {
		$eyebrow = t( 'November 3, 2026 · Election Day' );
		$head = t( 'Find your Election Day voting location and hours.' );
		$line = $hours ? ( $varies ? $hours['text'] . ' ' . t( "Check your location's official hours before you go." ) : t( 'Scheduled voting hours:' ) . ' ' . $hours['text'] ) : t( "Check your location's official hours before you go." );
		$go = $loc ?: $off; $go_label = $loc ? t( 'Find my voting location' ) : t( 'Official election information' );
		$second = array( $sticker, t( 'Already voted? Get your sticker.' ), false );
	}
	return '<section class="eday" aria-labelledby="eday-h"><p class="eday__eyebrow">' . vote_e( $eyebrow ) . '</p><h2 class="eday__h" id="eday-h">' . vote_e( $head ) . '</h2><p class="eday__line">' . vote_e( $line ) . '</p>'
		. '<p class="eday__links"><a class="btn btn--gold" href="' . vote_e( $go ) . '" target="_blank" rel="noopener">' . vote_e( $go_label ) . ' <span aria-hidden="true">↗</span></a> <a class="eday__more" href="' . vote_e( $second[0] ) . '"' . ( $second[2] ? ' target="_blank" rel="noopener"' : '' ) . '>' . vote_e( $second[1] ) . '</a></p></section>';
}
// The home page's version points at the state picker; no national hours.
function vote_eday_banner_hub() {
	if ( 'eday' !== vote_phase() ) { return ''; }
	return '<section class="eday" aria-labelledby="eday-h"><p class="eday__eyebrow">' . vote_e( t( 'November 3, 2026 · Election Day' ) ) . '</p><h2 class="eday__h" id="eday-h">' . vote_e( t( "Find your state's voting locations, hours and ballot return rules." ) ) . '</h2>'
		. '<p class="eday__links"><a class="btn btn--gold" href="#state-find">' . vote_e( t( 'Choose your state' ) ) . '</a> <a class="eday__more" href="#states-h">' . vote_e( t( 'Already voted? Get your sticker.' ) ) . '</a></p></section>';
}

// The state panel, server-rendered; app.js infoHtml() draws the same markup when the menu changes.
// The summary box under the maker (id="info"; the card QA compares it with the page's own copy): the dates,
// Election Day hours, ID in one line, and where to vote. Most people need only this; the guide has the rest.
function vote_info( $st ) {
	$es = vote_es();
	$name = $es ? vote_lname( $st ) : $st['name'];
	$eday = $es ? 'El Día de las Elecciones es el martes 3 de noviembre de 2026.' : 'Election Day is Tuesday, November 3, 2026.';
	if ( 'post' === vote_phase() ) {
		// After Election Day: the sticker, a ballot tracker only where the state has a verified one, and the
		// state's official election page (docs/post-election-release.md). Nothing says a vote was counted.
		$html = '<h2>' . vote_e( t( vote_between() ? 'Free I voted stickers' : 'Voted? Get your sticker.' ) ) . '</h2><p>' . vote_e( t( 'Pick a design and save it, or make your own.' ) ) . '</p>';
		$track = vote_between() ? null : vote_fact( $st, 'mail.track_url' );
		if ( $track ) { $html .= '<p>' . vote_e( t( 'Returned a mail ballot? Check its status with your election office.' ) ) . ' <a href="' . vote_e( $track ) . '" target="_blank" rel="noopener">' . vote_e( t( 'Check my ballot status' ) ) . '</a></p>'; }
		$off = vote_verified( $st ) ? $st['officialUrl'] : t( 'https://www.usa.gov/state-election-office' );
		$html .= '<p><a class="official" href="' . vote_e( $off ) . '" target="_blank" rel="noopener"><span><b>' . vote_e( t( 'Official %s election information', $name ) ) . '</b><small>' . vote_e( vote_verified( $st ) ? $st['officialLabel'] : 'usa.gov' ) . '</small></span></a></p>';
		return $html;
	}
	if ( ! vote_verified( $st ) ) {
		return $es
			? '<h2>Vota a más tardar el 3 de nov</h2><p>Aún no hemos verificado las fechas de votación anticipada de ' . vote_e( $name ) . ', así que estos stickers dicen Día de las Elecciones. Busca tus fechas y tu lugar de votación en <a href="https://www.vote411.org/' . vote_slug( $st['name'] ) . '" target="_blank" rel="noopener">Vote411</a> (League of Women Voters) o en la oficina electoral de tu estado.</p><p>' . $eday . '</p>' . vote_cal_link( $st )
			: '<h2>Vote by Nov 3</h2><p>We haven’t checked ' . vote_e( $name ) . '’s early voting dates yet, so these stickers say Election Day. Look up your dates and polling place at <a href="https://www.vote411.org/' . vote_slug( $st['name'] ) . '" target="_blank" rel="noopener">Vote411</a> (League of Women Voters) or your state election office.</p><p>' . $eday . '</p>' . vote_cal_link( $st );
	}
	$kind = $st['kind'];
	$head = $es ? ( 'mail' === $kind ? 'Vota por correo' : ( in_array( $kind, array( 'none', 'ended' ), true ) ? 'Vota el Día de las Elecciones' : 'Vota temprano' ) ) : ( 'mail' === $kind ? 'Vote by mail' : ( in_array( $kind, array( 'none', 'ended' ), true ) ? 'Vote on Election Day' : 'Vote early' ) );
	$sum = $es ? vote_sfield( $st, 'summary' ) : ( $st['summary'] ?? '' );
	if ( $sum ) { $fact = vote_e( $sum ); }
	elseif ( 'mail' === $kind ) { $fact = $es ? vote_e( $name ) . ' vota por correo. Devuelve tu boleta a más tardar el ' . vote_day( $st['end'] ) . '.' : vote_e( $name ) . ' votes by mail. Return your ballot by ' . vote_day( $st['end'] ) . '.'; }
	elseif ( 'ended' === $kind ) { $fact = $es ? 'La votación anticipada en ' . vote_e( $name ) . ' terminó el ' . vote_day( $st['end'] ) . '.' : 'Early voting in ' . vote_e( $name ) . ' ended ' . vote_day( $st['end'] ) . '.'; }
	elseif ( 'none' === $kind ) { $fact = $es ? vote_e( $name ) . ' no tiene votación anticipada abierta a todos. La mayoría de los votantes vota el Día de las Elecciones, martes 3 de noviembre de 2026.' : vote_e( $name ) . ' doesn’t have early voting open to everyone. Most voters vote on Election Day, Tuesday, November 3, 2026.'; }
	else { $fact = $es ? 'La votación anticipada en ' . vote_e( $name ) . ' es del ' . vote_day( $st['start'] ) . ' al ' . vote_day( $st['end'] ) . '.' : 'Early voting in ' . vote_e( $name ) . ' runs ' . vote_day( $st['start'] ) . ' to ' . vote_day( $st['end'] ) . '.'; }
	$srcs = array( $st['sourceUrl'] ?? $st['officialUrl'] );
	$ret = 'mail' === $kind ? vote_fact( $st, 'dates.mail_return' ) : null;
	if ( $ret ) {
		// All-mail state with a verified return rule: the action first, said once (Washington review).
		$html = '<h2>' . ( $es ? 'Firma el sobre y devuélvela' : 'Sign the envelope, then send it back' ) . '</h2><p>' . vote_e( $ret['text'] ) . '</p>';
		$srcs[] = $ret['src'];
	} else {
		$html = '<h2>' . $head . '</h2><p><b>' . $fact . '</b> ' . ( 'none' === $kind ? '' : $eday ) . '</p>';
		$note = $es ? vote_sfield( $st, 'note' ) : ( $st['note'] ?? '' );
		if ( $note ) { $html .= '<p>' . vote_e( $note ) . '</p>'; }
	}
	// Hours and ID from the verified FAQ answers, as two short labeled lines.
	$polls = vote_faq_raw( $st, 'polls' );
	if ( $polls ) { $html .= '<p><b>' . vote_e( t( 'Hours:' ) ) . '</b> ' . vote_e( $polls[0] ) . '</p>'; $srcs[] = $polls[1]; }
	$id = vote_faq_raw( $st, 'id' );
	if ( $id ) { $html .= '<p><b>' . vote_e( t( 'ID:' ) ) . '</b> ' . vote_e( $id[0] ) . '</p>'; $srcs[] = $id[1]; }
	$loc = vote_fact( $st, 'where.locator_url' );
	if ( $loc ) {
		$html .= '<p><a class="official" href="' . vote_e( $loc ) . '" target="_blank" rel="noopener"><span><b>' . vote_e( vote_fact( $st, 'where.locator_cta' ) ?: t( 'Find my polling place' ) ) . '</b><small>' . vote_e( vote_fact( $st, 'where.locator_label' ) ) . '</small></span></a></p>';
		$html .= '<p class="guide__more"><a href="' . vote_e( $st['officialUrl'] ) . '" target="_blank" rel="noopener">' . ( $es ? 'Información oficial para votar en ' . vote_e( $name ) : 'Official ' . vote_e( $name ) . ' voting information' ) . '</a></p>';
	} else {
		$html .= '<p><a class="official" href="' . vote_e( $st['officialUrl'] ) . '" target="_blank" rel="noopener"><span><b>' . ( $es ? 'Información oficial para votar en ' . vote_e( $name ) : 'Official ' . vote_e( $name ) . ' voting information' ) . '</b><small>' . ( $es ? ( 'none' === $kind ? 'Reglas del voto en ausencia y lugares de votación en ' : 'Lugares y horarios de votación en ' ) : ( 'none' === $kind ? 'Absentee rules and polling places at ' : 'Polling places and hours at ' ) ) . vote_e( $st['officialLabel'] ) . '</small></span></a></p>';
	}
	$html .= vote_cal_link( $st );
	$html .= vote_sources_line( $st, $srcs );
	if ( $es ) { $html .= '<p class="source">Algunos sitios oficiales están solo en inglés.</p>'; }
	return $html;
}

// Add to calendar (final-week loop item 1, October 26): /cal/<state>.ics and /es/cal/<state>.ics, written
// by the build for its own date. All-day events only, each with a reminder at 9 a.m. the day before:
// the last day of early voting or the mail deadline where verified and still ahead, and Election Day
// (left out for a mail state whose deadline is Election Day, which the mail event already covers).
// No poll hours: they differ by state and county, so each event links to the official page instead.
// Nothing is stored or sent; the file is the same for everyone who opens it.
function vote_ics_events( $st ) {
	if ( 'post' === vote_phase() ) { return array(); }
	$es = vote_es();
	$name = vote_name( $st );
	$ok = vote_verified( $st );
	$kind = $ok ? $st['kind'] : '';
	$off = $ok ? $st['officialUrl'] : 'https://www.vote411.org/' . vote_slug( $st['name'] );
	$loc = $ok ? ( vote_fact( $st, 'where.locator_url' ) ?: $off ) : $off;
	$ev = array();
	$ahead = $ok && ! empty( $st['end'] ) && $st['end'] >= vote_today();
	if ( 'in-person' === $kind && $ahead ) {
		$ev[] = array( 'early', $st['end'], $es ? 'Último día de votación anticipada en ' . $name : 'Last day of early voting in ' . $name,
			$es ? 'La votación anticipada en ' . $name . ' sigue hasta el ' . vote_day( $st['end'] ) . '. Lugares y horarios: ' . $off
				: 'Early voting in ' . $name . ' runs through ' . vote_day( $st['end'] ) . '. Places and hours: ' . $off );
	}
	if ( 'mail' === $kind && $ahead ) {
		$ret = vote_fact( $st, 'dates.mail_return' );
		$ev[] = array( 'mail', $st['end'], $es ? 'Fecha límite de la boleta por correo en ' . $name : 'Mail ballot deadline in ' . $name,
			( $ret ? $ret['text'] : ( $es ? 'La boleta se entrega a más tardar el ' . vote_day( $st['end'] ) . '.' : 'Ballots are due ' . vote_day( $st['end'] ) . '.' ) )
				. ( $es ? ' Cómo devolverla: ' : ' How to return it: ' ) . $off );
	}
	if ( ! ( 'mail' === $kind && $ahead && VOTE_ELECTION_DAY === $st['end'] ) ) {
		$ev[] = array( 'eday', VOTE_ELECTION_DAY, $es ? 'Día de las Elecciones en ' . $name : 'Election Day in ' . $name,
			$es ? 'Las casillas abren el martes 3 de noviembre. Busca tu lugar de votación y el horario: ' . $loc
				: 'Polls are open Tuesday, November 3. Find your polling place and hours: ' . $loc );
	}
	return $ev;
}
function vote_ics( $st ) {
	$esc = function ( $t ) { return str_replace( array( '\\', ';', ',', "\n" ), array( '\\\\', '\;', '\,', '\n' ), $t ); };
	// RFC 5545 folds lines at 75 octets, never inside a UTF-8 character.
	$fold = function ( $line ) {
		$out = '';
		$max = 75;  // the first line; continuations start with a space, so 74 more
		while ( strlen( $line ) > $max ) {
			$cut = $max;
			while ( $cut > 0 && ( ord( $line[ $cut ] ) & 0xC0 ) === 0x80 ) { $cut--; }
			$out .= substr( $line, 0, $cut ) . "\r\n ";
			$line = substr( $line, $cut );
			$max = 74;
		}
		return $out . $line;
	};
	$stamp = gmdate( 'Ymd\THis\Z', strtotime( vote_today() . ' 12:00:00 UTC' ) );
	$lines = array( 'BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//votesticker.org//Vote Sticker//' . ( vote_es() ? 'ES' : 'EN' ), 'CALSCALE:GREGORIAN', 'METHOD:PUBLISH' );
	foreach ( vote_ics_events( $st ) as $e ) {
		list( $id, $day, $title, $desc ) = $e;
		$lines = array_merge( $lines, array(
			'BEGIN:VEVENT',
			'UID:' . strtolower( $st['abbr'] ) . '-' . $id . '-2026' . ( vote_es() ? '-es' : '' ) . '@votesticker.org',
			'DTSTAMP:' . $stamp,
			'DTSTART;VALUE=DATE:' . str_replace( '-', '', $day ),
			'DTEND;VALUE=DATE:' . date( 'Ymd', strtotime( $day . ' +1 day' ) ),
			'SUMMARY:' . $esc( $title ),
			'DESCRIPTION:' . $esc( $desc ),
			'URL:' . VOTE_SITE . vote_pbase() . vote_slug( $st['name'] ) . '/',
			'TRANSP:TRANSPARENT',
			'BEGIN:VALARM', 'ACTION:DISPLAY', 'DESCRIPTION:' . $esc( $title ), 'TRIGGER:-PT15H', 'END:VALARM',
			'END:VEVENT',
		) );
	}
	$lines[] = 'END:VCALENDAR';
	return implode( "\r\n", array_map( $fold, $lines ) ) . "\r\n";
}
// The state panel's calendar link. No `download` attribute: iPhone opens a text/calendar file in its
// Add to Calendar sheet only as a plain link; with `download` it goes to Files.
function vote_cal_link( $st ) {
	if ( ! vote_ics_events( $st ) ) { return ''; }
	return '<p class="cal"><a class="btn btn--quiet" href="' . vote_pbase() . 'cal/' . vote_slug( $st['name'] ) . '.ics">' . vote_e( t( 'Add to my calendar' ) ) . '</a>'
		. '<small>' . vote_e( t( 'Your voting dates in %s, with a reminder the day before. Opens in your phone or computer calendar.', vote_lname( $st ) ) ) . '</small></p>'
		. vote_plan_link( $st );
}
// "Make a voting plan" (search-and-share loop item 5, October 26): a link out, never a planner here (Garrett).
// The state election office's own plan page when its facts file has a verified where.plan_url, otherwise
// VOTE411's, from the League of Women Voters Education Fund (nonpartisan). Gone after Election Day with
// the calendar link.
const VOTE_PLAN_FALLBACK = 'https://www.vote411.org/make-your-plan';
function vote_plan_link( $st ) {
	$own = vote_fact( $st, 'where.plan_url' );
	$url = is_string( $own ) && $own ? $own : VOTE_PLAN_FALLBACK;
	$from = $url === VOTE_PLAN_FALLBACK
		? t( 'Planning help on VOTE411, from the nonpartisan League of Women Voters Education Fund.' )
		: t( 'Planning help from %s.', vote_src_name( $st, $url ) );
	return '<p class="cal cal--plan"><a class="btn btn--quiet" href="' . vote_e( $url ) . '" target="_blank" rel="noopener">' . vote_e( t( 'Make a voting plan' ) ) . ' <span aria-hidden="true">↗</span></a>'
		. '<small>' . vote_e( $from ) . '</small></p>';
}

$vote_rows = vote_states();
$vote_meta = array();
$vote_by_slug = array();
foreach ( $vote_rows as $abbr => $row ) {
	$vote_meta[ $abbr ] = vote_meta( $row );
	if ( isset( $GLOBALS['vote_i18n']['aliases'][ $abbr ] ) ) { $vote_meta[ $abbr ]['alias'] = $GLOBALS['vote_i18n']['aliases'][ $abbr ]; }
	$vote_by_slug[ $vote_meta[ $abbr ]['slug'] ] = $abbr;
}

// Old links (?state=tx, used until October 19) move permanently to the state's own page.
if ( ! defined( 'VOTE_SLUG' ) && isset( $_GET['state'] ) ) {
	$old = strtoupper( (string) $_GET['state'] );
	header( 'Location: ' . VOTE_SITE . VOTE_BASE . ( isset( $vote_meta[ $old ] ) ? $vote_meta[ $old ]['slug'] . '/' : '' ), true, 301 );
	exit;
}

$vote_abbr = defined( 'VOTE_SLUG' ) ? ( $vote_by_slug[ VOTE_SLUG ] ?? '' ) : '';
// Pages that aren't a state: /about/ (site/about/index.php sets VOTE_PAGE). Same template, no maker.
$vote_about = defined( 'VOTE_PAGE' ) && 'about' === VOTE_PAGE;
// /history/ (site/history.php): the sourced history of the sticker.
$vote_history = defined( 'VOTE_PAGE' ) && 'history' === VOTE_PAGE;
$vote_doc = $vote_about || $vote_history; // pages with no maker, finder or state
$vote_path = (string) parse_url( $_SERVER['REQUEST_URI'] ?? VOTE_BASE, PHP_URL_PATH );
// Unknown addresses get a real 404 with the finder, popular states, and the full state list
// (Nielsen Norman Group's error-page guidance: say it plainly, keep the site's navigation, offer search
// and the most useful links). build/build_static.py writes the same page to dist/404.html.
$vote_404 = ! $vote_doc && ( defined( 'VOTE_404' ) || ( defined( 'VOTE_SLUG' ) && ! $vote_abbr ) || ( ! defined( 'VOTE_SLUG' ) && ! in_array( $vote_path, array( vote_pbase(), vote_pbase() . 'index.php' ), true ) ) );
if ( $vote_404 ) {
	http_response_code( 404 );
	$vote_abbr = '';
}
$vote_st = $vote_abbr ? $vote_rows[ $vote_abbr ] : null;
if ( defined( 'VOTE_CAL' ) ) { if ( $vote_st ) { echo vote_ics( $vote_st ); } return; }  // build/render.php cal/<state>
$vote_page = $vote_st ? $vote_meta[ $vote_abbr ] : array(
	// October 26: title and H1 name what people search for ("i voted stickers by state", "i voted early sticker");
	// the brand line moved to the eyebrow. After Election Day the home page leads with the sticker
	// (docs/post-election-release.md); the title stays.
	'title' => t( 'Free I Voted and I Voted Early Stickers by State | Vote Sticker' ),
	'h1' => t( vote_between() ? 'Free I voted stickers for every state' : ( 'post' === vote_phase() ? 'Voted? Get your sticker.' : 'Free I voted stickers for every state' ) ),
	'eyebrow' => t( 'post' === vote_phase() ? 'Free voting stickers by state' : 'Make a voting plan. Share the sticker.' ),
	'lead' => t( 'post' === vote_phase() ? 'Pick your state, choose a design and save it. Get an I voted, I voted early or I voted by mail sticker, or make your own.' : 'Make an I voted sticker or share your state’s voting dates, sized for your profile picture or a post.' ),
	'desc' => t( 'post' === vote_phase() ? 'Get a free I voted, I voted early or I voted by mail sticker. Choose your state, save a design or make your own.' : 'Free I voted, I voted early and vote early stickers for every state. Pick yours, save a ready-made PNG or make your own for your profile picture, with official links for early voting, mail ballots and Election Day.' ),
	'index' => ! vote_es() || VOTE_ES_INDEX,
);
if ( $vote_about ) {
	$vote_page = array(
		'title' => t( 'About Vote Sticker: Who Made It and Where the Dates Come From' ),
		'h1' => t( 'About Vote Sticker' ),
		'eyebrow' => t( 'About Vote Sticker' ),
		'lead' => t( 'Vote Sticker is a free, nonpartisan way to put your state’s voting dates on your profile picture. Pick your state, make a sticker, and share it with people who might need the reminder.' ),
		'desc' => t( 'Vote Sticker is a free, nonpartisan sticker maker from Garrett Digital in Austin, Texas. Every voting date on the site comes from an official state source, shown with its link and check date.' ),
		'index' => ! vote_es() || VOTE_ES_INDEX,
	);
}
if ( $vote_history ) { $vote_page = vote_history_meta(); }
if ( $vote_404 ) {
	$vote_page = array(
		'title' => t( 'Page Not Found | Vote Sticker' ),
		'h1' => t( 'Page not found' ),
		'eyebrow' => t( 'Error 404' ),
		'lead' => t( 'There’s no page at this address. The link may be old or have a typo. Find your state below, or try one of these pages.' ),
		'desc' => t( 'This page isn’t on Vote Sticker. Find your state for voting stickers and official election links.' ),
		'index' => false,
	);
}
$vote_tail = $vote_st ? $vote_page['slug'] . '/' : ( $vote_about ? 'about/' : ( $vote_history ? VOTE_HISTORY_SLUG . '/' : '' ) );
$vote_url = VOTE_SITE . vote_pbase() . $vote_tail;
// The same page in each language, for hreflang and the language switch.
$vote_alt = array( 'en' => VOTE_SITE . VOTE_BASE . $vote_tail, 'es' => VOTE_SITE . VOTE_BASE . 'es/' . $vote_tail );
$vote_other = vote_es() ? 'en' : 'es';
// Share image: one per state from build/og_states.mjs, the hub's from build/og_image.mjs.
// Cards come in three sets (October 25), drawn by build/og_cards.mjs from a build of that phase, so a shared
// link never shows a reminder that has expired: the early voting set, 'ended' (the state's early voting has
// closed) and 'post' (after Election Day). A missing card falls back to the set before it.
$vote_og_set = 'post' === vote_phase() ? 'post/' : ( $vote_st && 'ended' === ( $vote_st['kind'] ?? '' ) ? 'ended/' : '' );
$vote_og_dir = vote_es() ? 'assets/og/es/' : 'assets/og/';
$vote_og = null;
foreach ( array_unique( array( $vote_og_set, '' ) ) as $vote_set ) {
	if ( $vote_st && is_file( __DIR__ . '/' . $vote_og_dir . $vote_set . $vote_page['slug'] . '.jpg' ) ) { $vote_og = $vote_og_dir . $vote_set . $vote_page['slug'] . '.jpg'; break; }
}
$vote_hub_og = ( vote_es() ? 'assets/og-hub-es' : 'assets/og-hub' ) . ( 'post' === vote_phase() ? '-post' : '' ) . '.jpg';
$vote_og = $vote_og ?: ( is_file( __DIR__ . '/' . $vote_hub_og ) ? $vote_hub_og : ( vote_es() && is_file( __DIR__ . '/assets/og-hub-es.jpg' ) ? 'assets/og-hub-es.jpg' : 'assets/og-hub.jpg' ) );
$vote_og_alt = vote_es()
	? ( $vote_st ? $vote_page['h1'] . ': ' . $vote_page['status'] . '. Un sticker para votar de ' . $vote_page['crumb'] . ' sobre papel color crema.' : 'Vote Sticker: muéstrales a tus amigos que vas a votar. Stickers para votar en los 50 estados y DC.' )
	: ( $vote_st ? $vote_page['h1'] . ': ' . $vote_page['status'] . '. A ' . $vote_page['crumb'] . ' voting sticker on cream paper.' : ( 'post' === vote_phase() ? 'Vote Sticker: voted? Get your sticker. Voting stickers for all 50 states and DC.' : "Vote Sticker: show your friends you're voting. Voting stickers for all 50 states and DC, with ready-made designs from Texas, Washington, Pennsylvania, and California." ) );

// Breadcrumbs start at the tool's hub, never the WordPress site (Garrett: no links back to the root).
$vote_crumbs = array( array( VOTE_BRAND, VOTE_SITE . vote_pbase() ) );
if ( $vote_st ) { $vote_crumbs[] = array( $vote_page['crumb'], $vote_url ); }
if ( $vote_about ) { $vote_crumbs[] = array( t( 'About' ), $vote_url ); }
if ( $vote_history ) { $vote_crumbs[] = array( t( 'Sticker History' ), $vote_url ); }
$vote_share = '<p class="share"><button type="button" class="share__btn" id="share-link" data-url="' . vote_e( $vote_url ) . '" data-title="' . vote_e( $vote_page['title'] ) . '"><svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.5 1.5"/><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7L12 19"/></svg><span id="share-label">' . ( $vote_st ? vote_e( t( 'Copy link to this %s page', vote_lname( $vote_st ) ) ) : vote_e( t( 'Copy page link' ) ) ) . '</span></button></p>';

// The state finder: a type-ahead box (app.js fills the suggestions). Without JavaScript the full list of
// state links below the maker does the same job.
function vote_finder( $label, $placeholder, $button = 'Find' ) {
	return '<div class="finder"><label for="state-find">' . vote_e( t( $label ) ) . '</label>'
		. '<div class="finder__box"><input id="state-find" type="text" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="state-options" autocomplete="off" autocapitalize="words" spellcheck="false" placeholder="' . vote_e( t( $placeholder ) ) . '">'
		. '<button type="button" class="btn btn--gold" id="state-go">' . vote_e( t( $button ) ) . '</button>'
		. '<ul id="state-options" role="listbox" aria-label="' . vote_e( t( 'Matching states' ) ) . '" hidden></ul></div>'
		. '<p class="help" id="state-help">' . ( 'Get my sticker' === $button ? '<span class="finder__free">' . vote_e( t( 'Free, no account.' ) ) . '</span> ' : '' ) . '<a href="#states-h">' . vote_e( t( 'Or browse the full list of states' ) ) . '</a></p></div>';
}

$vote_ld = array(
	'@context' => 'https://schema.org',
	'@type' => 'BreadcrumbList',
	'itemListElement' => array_map( function ( $c, $i ) { return array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1] ); }, $vote_crumbs, array_keys( $vote_crumbs ) ),
);
?>
<!doctype html>
<html lang="<?php echo $vote_lang; ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo vote_e( $vote_page['title'] ); ?></title>
<meta name="description" content="<?php echo vote_e( $vote_page['desc'] ); ?>">
<?php if ( ! $vote_st && ! $vote_404 && ! vote_es() ) : ?><meta name="msvalidate.01" content="AA4622D038EF44D13B72400468BB1E7B"><?php echo "\n"; endif; ?>
<?php if ( ! $vote_404 ) : ?><link rel="canonical" href="<?php echo vote_e( $vote_url ); ?>"><?php echo "\n"; endif; ?>
<?php if ( ! $vote_404 && VOTE_ES_INDEX ) : ?><link rel="alternate" hreflang="en" href="<?php echo vote_e( $vote_alt['en'] ); ?>">
<link rel="alternate" hreflang="es" href="<?php echo vote_e( $vote_alt['es'] ); ?>">
<link rel="alternate" hreflang="x-default" href="<?php echo vote_e( $vote_alt['en'] ); ?>"><?php echo "\n"; endif; ?>
<?php if ( ! $vote_page['index'] ) : ?><meta name="robots" content="noindex, follow"><?php echo "\n"; endif; ?>
<?php if ( ! $vote_404 ) : ?>
<meta property="og:title" content="<?php echo vote_e( $vote_page['title'] ); ?>">
<meta property="og:description" content="<?php echo vote_e( $vote_page['desc'] ); ?>">
<meta property="og:type" content="website">
<meta property="og:locale" content="<?php echo vote_es() ? 'es_MX' : 'en_US'; ?>">
<meta property="og:url" content="<?php echo vote_e( $vote_url ); ?>">
<meta property="og:image" content="<?php echo vote_e( VOTE_SITE . vote_v( $vote_og ) ); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="<?php echo vote_e( $vote_og_alt ); ?>">
<meta property="og:site_name" content="<?php echo vote_e( VOTE_BRAND ); ?>">
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" href="<?php echo vote_v( 'assets/icons/favicon.ico' ); ?>" sizes="any">
<link rel="icon" type="image/svg+xml" href="<?php echo vote_v( 'assets/icons/favicon.svg' ); ?>">
<link rel="mask-icon" href="<?php echo VOTE_BASE; ?>assets/icons/mask-icon.svg" color="#14295E">
<link rel="manifest" href="<?php echo VOTE_BASE; ?>site.webmanifest">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo vote_v( 'assets/icons/favicon-32.png' ); ?>">
<link rel="apple-touch-icon" href="<?php echo vote_v( 'assets/icons/apple-touch-icon.png' ); ?>">
<meta name="theme-color" content="#0F2558">
<link rel="preload" href="<?php echo VOTE_BASE; ?>assets/fonts/anton.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?php echo VOTE_BASE; ?>assets/fonts/archivo-black.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?php echo VOTE_BASE; ?>assets/fonts/luckiest-guy.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?php echo vote_v( 'app.css' ); ?>">
<?php if ( ! $vote_404 ) : ?><script type="application/ld+json"><?php echo vote_json( $vote_ld ); ?></script><?php endif; ?>
<?php if ( $vote_about ) : ?><script type="application/ld+json"><?php echo vote_json( array( '@context' => 'https://schema.org', '@type' => 'AboutPage', 'name' => $vote_page['title'], 'url' => $vote_url, 'inLanguage' => $vote_lang,
	'about' => array( '@type' => 'WebSite', 'name' => VOTE_BRAND, 'url' => VOTE_SITE . VOTE_BASE ),
	'publisher' => array( '@type' => 'Organization', 'name' => 'Garrett Digital', 'url' => 'https://www.garrettdigital.com/', 'logo' => VOTE_SITE . VOTE_BASE . 'assets/gd-logo.svg',
		'address' => array( '@type' => 'PostalAddress', 'addressLocality' => 'Austin', 'addressRegion' => 'TX', 'addressCountry' => 'US' ),
		'founder' => array( '@type' => 'Person', 'name' => 'Garrett Nafzinger' ) ) ) ); ?></script><?php echo "\n"; endif; ?>
<?php if ( $vote_st && ( $vote_faq_ld = vote_state_faq( $vote_st ) ) ) : ?><script type="application/ld+json"><?php echo vote_json( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'inLanguage' => $vote_lang, 'mainEntity' => array_map( function ( $q ) { return array( '@type' => 'Question', 'name' => $q[0], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $q[1] ) ); }, $vote_faq_ld ) ) ); ?></script><?php echo "\n"; endif; ?>
<?php if ( $vote_history ) : ?><script type="application/ld+json"><?php echo vote_json( vote_history_ld( $vote_page, $vote_url ) ); ?></script><?php echo "\n"; endif; ?>
<?php if ( ! $vote_st && ! $vote_404 && ! $vote_history ) : ?><script type="application/ld+json"><?php echo vote_json( array( '@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => VOTE_BRAND, 'alternateName' => 'VoteSticker.org', 'url' => VOTE_SITE . vote_pbase(), 'inLanguage' => $vote_lang ) ); ?></script><?php echo "\n"; endif; ?>
<!-- Google tag (gtag.js) and Ahrefs analytics: only on votesticker.org, and never in an automated browser
     (navigator.webdriver), so local previews and the QA scripts don't count as visits (October 23). -->
<script>
  if (location.hostname === 'votesticker.org' && !navigator.webdriver) {
    (function (d) {
      var add = function (src, key) { var s = d.createElement('script'); s.async = true; s.src = src; if (key) { s.setAttribute('data-key', key); } d.head.appendChild(s); };
      add('https://analytics.ahrefs.com/analytics.js', 'JWFR/FwEHfD5yA/gz1K/Jg');
      add('https://www.googletagmanager.com/gtag/js?id=G-PZWZTXDSDE');
    })(document);
    window.dataLayer = window.dataLayer || [];
    window.gtag = function () { dataLayer.push(arguments); };
    gtag('js', new Date());
    gtag('config', 'G-PZWZTXDSDE');
  }
</script>
</head>
<body>
<?php if ( ! vote_es() && ! $vote_st && ! $vote_404 && ! $vote_doc ) : ?>
<div class="lang-suggest" id="lang-suggest" lang="es" hidden><div class="wrap"><p>¿Prefieres ver esta página en español?</p><a class="btn btn--gold" href="<?php echo vote_e( $vote_alt['es'] ); ?>" data-lang-choice="es">Ver en español</a><button type="button" class="btn btn--quiet" id="lang-suggest-no">No, gracias</button></div></div>
<?php endif; ?>
<header class="top<?php echo $vote_st ? "" : " top--hub"; ?>">
	<div class="wrap">
		<div class="top__bar">
		<a class="brand" href="<?php echo vote_pbase(); ?>"<?php echo $vote_st ? '' : ' aria-current="page"'; ?>>
			<svg class="brand__mark" aria-hidden="true" viewBox="0 0 40 40" width="40" height="40"><path d="M37.61 23.74A18 18 0 1 0 23.74 37.61Z" fill="#FBF6E8"/><path d="M37.61 23.74A18 18 0 1 0 23.74 37.61Z" fill="none" stroke="#14295E" stroke-width="1.6" transform="translate(20 20) scale(.8) translate(-20 -20)"/><path d="M37.61 23.74L23.74 37.61Q26.4 26.4 37.61 23.74Z" fill="#D9C99A"/><path d="M18.60 9.80 L20.77 15.61 L26.97 15.88 L22.12 19.74 L23.77 25.72 L18.60 22.30 L13.43 25.72 L15.08 19.74 L10.23 15.88 L16.43 15.61Z" fill="#C1272D"/>
</svg>
			<span class="brand__name">VOTE <span class="brand__hi">STICKER</span></span>
			<span class="brand__tag"><?php echo vote_e( t( "Show your friends you're voting." ) ); ?></span>
		</a>
		<a class="lang-switch" href="<?php echo vote_e( $vote_404 ? VOTE_SITE . ( vote_es() ? VOTE_BASE : VOTE_BASE . 'es/' ) : $vote_alt[ $vote_other ] ); ?>" hreflang="<?php echo $vote_other; ?>" lang="<?php echo $vote_other; ?>" data-lang-choice="<?php echo $vote_other; ?>"><?php echo vote_es() ? 'English' : 'Español'; ?></a>
		</div>
		<?php if ( $vote_st || $vote_doc ) : ?>
		<nav class="crumbs" aria-label="<?php echo vote_e( t( 'Breadcrumb' ) ); ?>"><ol>
			<?php foreach ( $vote_crumbs as $i => $c ) : $last = $i === count( $vote_crumbs ) - 1; ?>
			<li><?php if ( $last ) : ?><span aria-current="page" id="crumb-last"><?php echo vote_e( $c[0] ); ?></span><?php else : ?><a href="<?php echo vote_e( $c[1] ); ?>"><?php echo vote_e( $c[0] ); ?></a><?php endif; ?></li>
			<?php endforeach; ?>
		</ol></nav>
		<?php endif; ?>
		<?php if ( $vote_st ) : $vote_lead_img = vote_hero_file( $vote_abbr ); $vote_art = vote_es() ? 'data:image/gif;base64,R0lGODlhAQABAAAAACw=' : $vote_lead_img; ?>
		<?php echo vote_eday_banner( $vote_st ); ?>
		<div class="state-hero<?php echo $vote_art ? ' state-hero--art' : ''; ?>">
			<div>
				<?php // "I Voted" and "Ya voté" never split across lines ("Washington I / Voted Stickers" on phones). ?>
				<h1 id="page-h1"><?php echo str_replace( array( 'I Voted', 'Ya voté' ), array( 'I&nbsp;Voted', 'Ya&nbsp;voté' ), vote_e( $vote_page['h1'] ) ); ?></h1>
				<p class="ribbon"><?php echo vote_e( $vote_page['ribbon'] ); ?></p>
				<p class="lead" id="page-lead"><?php echo vote_e( $vote_page['lead'] ); ?></p>
				<?php echo vote_early_countdown( $vote_st ); ?>
				<?php echo vote_countdown( 'countdown--state' ); ?>
				<p class="jump">
					<?php if ( $vote_lead_img ) : ?><a class="btn btn--gold" href="#gallery"><?php echo vote_e( t( 'See %s stickers', vote_lname( $vote_st ) ) ); ?> <span aria-hidden="true">↓</span></a><?php endif; ?>
					<a class="btn <?php echo $vote_lead_img ? 'btn--line' : 'btn--gold'; ?>" href="#maker-h"><?php echo vote_e( t( $vote_lead_img ? 'Make your own' : 'Make your sticker' ) ); ?> <span aria-hidden="true">↓</span></a>
				</p>
				<div class="hero-meta">
					<?php if ( vote_verified( $vote_st ) ) : ?><a class="hero-meta__official" href="<?php echo vote_e( $vote_st['officialUrl'] ); ?>" target="_blank" rel="noopener"><?php echo vote_e( t( 'Official info: %s', $vote_st['officialLabel'] ) ); ?> <span aria-hidden="true">↗</span></a>
					<?php else : ?><a class="hero-meta__official" href="https://www.vote411.org/<?php echo vote_slug( $vote_st['name'] ); ?>" target="_blank" rel="noopener"><?php echo vote_e( t( 'Check your dates at Vote411' ) ); ?> <span aria-hidden="true">↗</span></a><?php endif; ?>
					<?php echo $vote_share; ?>
				</div>
			</div>
			<?php if ( $vote_art ) : ?><img class="state-hero__sticker" src="<?php echo vote_e( $vote_art ); ?>" alt="" width="1080" height="1080" aria-hidden="true"<?php echo vote_es() ? ' data-es-state="' . $vote_abbr . '"' : ''; ?>><?php endif; ?>
		</div>
		<?php else : ?>
		<?php echo ( ! $vote_doc && ! $vote_404 ) ? vote_eday_banner_hub() : ''; ?>
		<div class="hero">
			<div class="hero__text">
				<?php if ( $vote_page['eyebrow'] ) : ?><p class="<?php echo $vote_404 ? 'ribbon ribbon--err' : 'eyebrow'; ?>"><?php echo vote_e( $vote_page['eyebrow'] ); ?></p><?php endif; ?>
				<h1 id="page-h1"><?php
				// One line per sentence, so no line ends mid-thought ("MAKE A VOTING / PLAN. SHARE").
				$vote_h1 = preg_split( '/(?<=\.)\s+/', $vote_page['h1'] );
				echo count( $vote_h1 ) > 1 ? implode( ' ', array_map( function ( $x ) { return '<span class="h1__line">' . vote_e( $x ) . '</span>'; }, $vote_h1 ) ) : vote_e( $vote_page['h1'] );
				?></h1>
				<p class="lead" id="page-lead"><?php echo vote_e( $vote_page['lead'] ); ?></p>
				<?php if ( ! $vote_doc ) { echo vote_finder( 'Your state', 'Type your state', $vote_404 ? 'Find' : 'Get my sticker' ); } ?>
				<?php if ( ! $vote_doc && ! $vote_404 ) : $vote_today = vote_today(); $vote_open = 0; foreach ( $vote_rows as $vote_r ) { if ( vote_verified( $vote_r ) && 'in-person' === ( $vote_r['kind'] ?? '' ) && ! empty( $vote_r['start'] ) && $vote_r['start'] <= $vote_today && $vote_today <= $vote_r['end'] ) { $vote_open++; } } ?>
				<?php echo vote_countdown(); ?>
				<?php if ( $vote_open && 'pre' === vote_phase() ) : ?><p class="hero__today"><?php echo vote_e( $vote_open > 1 ? t( 'Early voting is underway in %s states.', $vote_open ) : t( 'Early voting is underway in one state.' ) ); ?></p><?php endif; ?>
				<?php endif; ?>
				<?php if ( $vote_404 ) : ?>
				<nav class="err-links" aria-labelledby="err-links-h">
					<h2 id="err-links-h"><?php echo vote_e( t( 'Try one of these' ) ); ?></h2>
					<ul>
						<li><a href="<?php echo vote_pbase(); ?>"><?php echo vote_e( t( 'Vote Sticker home page' ) ); ?></a></li>
						<?php foreach ( array( 'TX', 'CA', 'FL', 'NY', 'PA' ) as $a ) : ?><li><a href="<?php echo vote_pbase() . $vote_meta[ $a ]['slug']; ?>/"><?php echo vote_e( t( '%s stickers', vote_lname( $vote_rows[ $a ] ) ) ); ?></a></li><?php endforeach; ?>
						<li><a href="<?php echo vote_pbase(); ?>#faq-h"><?php echo vote_e( t( 'Common Questions' ) ); ?></a></li>
					</ul>
				</nav>
				<?php else : ?><?php echo $vote_history ? vote_history_jump() : ''; ?><?php echo $vote_doc ? $vote_share : ''; ?><?php endif; ?>
			</div>
			<?php if ( $vote_404 ) : ?>
			<div class="err-sticker" aria-hidden="true"><span>404</span><small><?php echo vote_e( t( 'Page not found' ) ); ?></small></div>
			<?php elseif ( $vote_history ) : echo vote_history_art(); ?>
			<?php elseif ( ! $vote_about ) : ?>
			<div class="hero__art" aria-hidden="true">
				<?php
				// Four messages, four colors (October 21): vote early, I voted, my ballot is in, make a plan.
				// Spanish pages draw the same four messages in Spanish (app.js spanishArt()).
				foreach ( array( array( 'tx', 2, 'type', 'votatemprano', 'forest' ), array( 'ga', 7, 'type', 'yavote', 'maroon' ), array( 'or', 8, 'tilt', 'yavotecorreo', 'cobalt' ), array( 'az', 9, 'side', 'voyavotar', 'plum' ) ) as $k => $h ) : if ( is_file( __DIR__ . "/assets/states/{$h[0]}/{$h[0]}-vote-{$h[1]}.jpg" ) ) : ?>
				<img class="hero__sticker hero__sticker--<?php echo $k + 1; ?>" src="<?php echo vote_v( "assets/states/{$h[0]}/{$h[0]}-vote-{$h[1]}.jpg" ); ?>" alt="" width="1080" height="1080"<?php echo $k ? ' loading="lazy"' : ''; ?><?php echo vote_es() ? ' data-es-state="' . strtoupper( $h[0] ) . '" data-es-design="' . $h[2] . '" data-es-headline="' . $h[3] . '" data-es-palette="' . $h[4] . '"' : ''; ?>>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</header>

<main class="wrap">
<?php if ( $vote_about ) : echo vote_about(); elseif ( $vote_history ) : echo vote_history(); else : ?>

	<?php
	// Landing showcase (October 19): one polished ready-made design per state, most populous first,
	// mixing early dates, "by" dates and vote by mail. Shown in the circle a profile picture gets.
	$vote_showcase = array( 'ca' => 4, 'tx' => 2, 'ny' => 2, 'pa' => 4, 'nc' => 2, 'va' => 5, 'wa' => 6, 'co' => 7 );
	$vote_showcase = array_filter( $vote_showcase, function ( $n, $abbr ) { return is_file( __DIR__ . "/assets/states/$abbr/$abbr-vote-$n.jpg" ); }, ARRAY_FILTER_USE_BOTH );
	// Alphabetical by the state's name in the page language (Garrett, October 26): easier to scan for your own.
	$vote_by_name = function ( $a, $b ) use ( $vote_rows ) { return strcmp( vote_lname( $vote_rows[ strtoupper( $a ) ] ), vote_lname( $vote_rows[ strtoupper( $b ) ] ) ); };
	uksort( $vote_showcase, $vote_by_name );
	?>
	<?php if ( $vote_showcase && ! $vote_st ) : ?>
	<section class="showcase" id="showcase" aria-labelledby="showcase-h">
		<h2 id="showcase-h" class="display-h"><?php echo vote_e( t( $vote_404 ? 'Popular States' : 'Find a Sticker You Like' ) ); ?></h2>
		<p class="section-intro"><?php echo vote_e( t( $vote_404 ? 'Ready-made stickers from the most populous states. Tap one to see that state’s page.' : 'Tap one to see every design for that state.' ) ); ?></p>
		<div class="grid grid--showcase">
			<?php foreach ( $vote_showcase as $abbr => $n ) : ?>
			<a class="pick" href="<?php echo vote_pbase() . $vote_meta[ strtoupper( $abbr ) ]['slug']; ?>/">
				<img src="<?php echo vote_v( "assets/states/$abbr/$abbr-vote-$n.jpg" ); ?>" alt="<?php echo vote_e( t( '%s vote sticker example', vote_lname( $vote_rows[ strtoupper( $abbr ) ] ) ) ); ?>" width="1080" height="1080" loading="lazy"<?php echo vote_es() ? ' data-es-state="' . strtoupper( $abbr ) . '"' : ''; ?>>
				<span class="pick__name"><?php echo vote_e( vote_lname( $vote_rows[ strtoupper( $abbr ) ] ) ); ?></span>
			</a>
			<?php endforeach; ?>
		</div>
		<?php if ( ! $vote_404 ) : ?><p class="showcase__more"><a href="#states-h"><?php echo vote_e( t( 'Don’t see your state? See all 50 states and DC' ) ); ?> <span aria-hidden="true">↓</span></a></p><?php endif; ?>
	</section>
	<?php
	// I voted early (search-and-share loop item 2, October 26): the top search query, "i voted early sticker", was
	// spread over five state pages with no page made for it. Ready-made designs that exist in each state's own
	// gallery, linking there. English home page only: the images carry English text.
	// States not already in Find a Sticker You Like, so the two sections together show sixteen (October 26).
	$vote_early_picks = array( 'fl' => 'fl-vote-8', 'md' => 'md-i-voted-early', 'mi' => 'mi-vote-8', 'mn' => 'mn-i-voted-early', 'nj' => 'nj-vote-8', 'oh' => 'oh-i-voted-early', 'sc' => 'sc-i-voted-early', 'wi' => 'wi-i-voted-early' );
	$vote_early_alts = json_decode( (string) file_get_contents( __DIR__ . '/assets/states/alts.json' ), true );
	$vote_early_picks = array_filter( $vote_early_picks, function ( $f, $abbr ) { return is_file( __DIR__ . "/assets/states/$abbr/$f.jpg" ); }, ARRAY_FILTER_USE_BOTH );
	uksort( $vote_early_picks, $vote_by_name );
	if ( $vote_early_picks && ! $vote_404 && ! vote_es() && ! vote_between() ) : ?>
	<section class="showcase showcase--early" id="i-voted-early" aria-labelledby="early-h">
		<h2 id="early-h" class="display-h"><?php echo vote_e( t( 'I Voted Early Stickers' ) ); ?></h2>
		<p class="section-intro"><?php echo vote_e( t( 'Voted early? Save a ready-made I voted early sticker from your state, or choose I voted early in the sticker maker on your state’s page.' ) ); ?></p>
		<div class="grid grid--showcase">
			<?php foreach ( $vote_early_picks as $abbr => $f ) : ?>
			<a class="pick" href="<?php echo vote_pbase() . $vote_meta[ strtoupper( $abbr ) ]['slug']; ?>/#gallery">
				<img src="<?php echo vote_v( "assets/states/$abbr/$f.jpg" ); ?>" alt="<?php echo vote_e( $vote_early_alts[ $f ] ?? t( '%s vote sticker example', vote_lname( $vote_rows[ strtoupper( $abbr ) ] ) ) ); ?>" width="1080" height="1080" loading="lazy">
				<span class="pick__name"><?php echo vote_e( vote_lname( $vote_rows[ strtoupper( $abbr ) ] ) ); ?></span>
			</a>
			<?php endforeach; ?>
		</div>
		<?php if ( ! $vote_404 ) : ?><p class="showcase__more"><a href="#states-h"><?php echo vote_e( t( 'Don’t see your state? See all 50 states and DC' ) ); ?> <span aria-hidden="true">↓</span></a></p><?php endif; ?>
	</section>
	<?php endif; ?>
	<?php if ( ! $vote_404 ) : ?>
	<section class="how-wrap" aria-labelledby="how-h">
		<h2 id="how-h" class="display-h"><?php echo vote_e( t( 'How It Works' ) ); ?></h2>
		<ol class="how">
			<li><b><?php echo vote_e( t( 'Pick your state' ) ); ?></b> <?php echo vote_e( t( 'See its voting dates and sticker designs.' ) ); ?></li>
			<li><b><?php echo vote_e( t( 'Make it yours' ) ); ?></b> <?php echo vote_e( t( 'Choose a ready-made sticker or make your own.' ) ); ?></li>
			<li><b><?php echo vote_e( t( 'Save and share' ) ); ?></b> <?php echo vote_e( t( 'Download it for your profile picture or a post.' ) ); ?></li>
		</ol>
		<p class="how__more"><?php echo vote_e( t( 'Need polling places, hours or ID rules?' ) ); ?> <a href="<?php echo vote_e( t( 'https://www.usa.gov/state-election-office' ) ); ?>"><?php echo vote_e( t( 'Find your election office' ) ); ?></a>.</p>
	</section>
	<section class="trust">
		<svg aria-hidden="true" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#F2C14E" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"><path d="M3 9l9-5 9 5"/><path d="M5 9v9M10 9v9M14 9v9M19 9v9M3 21h18"/></svg>
		<p><b><?php echo vote_e( t( 'Nonpartisan, free, and private.' ) ); ?></b> <?php echo vote_e( t( 'No account, and nothing you make is uploaded. Dates come from official state election offices.' ) ); ?></p>
	</section>
	<?php endif; ?>
	<?php endif; ?>

	<?php $vote_files = $vote_st ? vote_files( $vote_abbr ) : array(); ?>
	<div id="maker"<?php echo $vote_st ? '' : ' hidden'; ?>>
		<section class="gallery" id="gallery"<?php echo $vote_files ? '' : ' hidden'; ?> aria-labelledby="gallery-h">
			<h2 id="gallery-h" class="display-h"><?php echo vote_e( $vote_st ? t( 'Pick a %s Sticker', vote_lname( $vote_st ) ) : t( 'Ready-Made Stickers' ) ); ?></h2>
			<p><?php echo vote_e( t( 'Tap a sticker to save it. The words stay inside the circle, so nothing gets cut off in your profile photo.' ) ); ?></p>
			<div class="grid grid--gallery" id="gallery-grid" style="--cols: <?php echo vote_cols( count( $vote_files ) ); ?>"><?php foreach ( $vote_files as $i => $src ) : ?><a class="tile" href="<?php echo vote_e( $src ); ?>" download="vote-<?php echo strtolower( $vote_abbr ) . '-' . ( $i + 1 ); ?>.jpg"><img src="<?php echo vote_e( $src ); ?>" alt="<?php echo vote_e( vote_alt( $src, $vote_st, $i ) ); ?>" width="1080" height="1080" loading="lazy"></a><?php endforeach; ?></div>
		</section>

		<?php $vote_maker_first = ! $vote_files; ?>
		<h2 class="maker-h display-h<?php echo $vote_maker_first ? ' maker-h--first' : ''; ?>" id="maker-h"><?php echo vote_e( $vote_maker_first ? t( 'Make Your %s Sticker', $vote_st ? vote_lname( $vote_st ) : '' ) : t( 'Or Make Your Own' ) ); ?></h2>
		<div class="controls controls--maker">
			<label><?php echo vote_e( t( 'Main message' ) ); ?>
				<select id="headline"></select>
			</label>
			<label><?php echo vote_e( t( 'Date text' ) ); ?>
				<span class="date-row"><input id="date" maxlength="24" autocomplete="off" spellcheck="false"><button type="button" class="btn btn--quiet" id="date-reset"><?php echo vote_e( t( 'Reset' ) ); ?></button></span>
			</label>
		</div>

		<div class="maker">
			<section class="maker__designs" aria-labelledby="designs-h">
				<h2 id="designs-h"><?php echo vote_e( t( 'Pick a Design' ) ); ?></h2>
				<p class="help designs-help"><?php echo vote_e( t( 'Every design fits inside a profile-picture circle.' ) ); ?></p>
				<div class="grid" id="grid"></div>
			</section>
			<div class="maker__preview" id="preview-wrap">
				<div class="sizes" role="group" aria-label="<?php echo vote_e( t( 'Picture size' ) ); ?>">
					<button type="button" class="size" data-size="square" aria-pressed="true"><?php echo vote_e( t( 'Square' ) ); ?></button>
					<button type="button" class="size" data-size="portrait" aria-pressed="false"><?php echo vote_e( t( 'Portrait' ) ); ?></button>
					<button type="button" class="size" data-size="story" aria-pressed="false"><?php echo vote_e( t( 'Story' ) ); ?></button>
				</div>
				<div class="frame"><img id="preview" alt="<?php echo vote_e( t( 'Your profile picture' ) ); ?>" width="1080" height="1080"></div>
				<?php // Profile-picture size (final-week item 3): the same image at 48 and 32 px, where friends see it in a feed and in comments. A visual duplicate, so hidden from screen readers. ?>
				<div class="tiny" id="tiny"><img src="data:," alt="" width="48" height="48" aria-hidden="true"><img src="data:," alt="" width="32" height="32" aria-hidden="true"><span><?php echo vote_e( t( 'How it looks as a profile picture' ) ); ?></span></div>
				<div class="colors">
					<div class="swatches" id="swatches" role="group" aria-label="<?php echo vote_e( t( 'Colors' ) ); ?>"></div>
					<p class="swatch-name" id="swatch-name" aria-live="polite"></p>
					<label class="check" id="rays-check"><input type="checkbox" id="rays"> <?php echo vote_e( t( 'Sunburst background' ) ); ?></label>
				</div>
				<div class="sizes tilts" id="tilts" role="group" aria-label="<?php echo vote_e( t( 'Angle' ) ); ?>">
					<button type="button" class="size" data-tilt="straight" aria-pressed="true"><?php echo vote_e( t( 'Straight' ) ); ?></button>
					<button type="button" class="size" data-tilt="left" aria-pressed="false" aria-label="<?php echo vote_e( t( 'Tilt left' ) ); ?>"><?php echo vote_e( t( 'Left' ) ); ?></button>
					<button type="button" class="size" data-tilt="right" aria-pressed="false" aria-label="<?php echo vote_e( t( 'Tilt right' ) ); ?>"><?php echo vote_e( t( 'Right' ) ); ?></button>
				</div>
				<p class="summary" id="summary"></p>
				<label class="check" id="circle-check"><input type="checkbox" id="circle"> <?php echo vote_e( t( 'Preview circle crop' ) ); ?></label>
				<label class="check" id="round-check"><input type="checkbox" id="round"> <?php echo vote_e( t( 'Round sticker with a see-through background (PNG)' ) ); ?></label>
				<div class="actions">
					<a class="btn" id="save" href="#" download aria-disabled="true"><?php echo vote_e( t( 'Download picture' ) ); ?></a>
					<a class="link-quiet" id="open" href="#" target="_blank" rel="noopener"><?php echo vote_e( t( 'Open full image' ) ); ?></a>
					<?php // Image description (final-week item 4): for people who add alt text when they post. ?>
					<button type="button" class="link-quiet" id="copy-alt"><?php echo vote_e( t( 'Copy image description' ) ); ?></button>
				</div>
				<p class="hint" id="tip"></p>
				<p class="hint after-save" id="after-save" hidden><?php echo vote_e( t( 'Curious where the I voted sticker came from?' ) ); ?> <a href="<?php echo vote_pbase() . VOTE_HISTORY_SLUG; ?>/"><?php echo vote_e( t( 'Read its history' ) ); ?></a></p>
				<?php // After an I voted sticker is saved (final-week item 5): one more reminder to someone else. The button reuses the page's share link and its share event; not after Election Day. ?>
				<?php if ( 'post' !== vote_phase() ) : ?><p class="hint after-save" id="nudge" hidden><?php echo vote_e( t( "Send this page to someone who hasn't voted yet." ) ); ?> <button type="button" class="link-quiet" id="nudge-share"><?php echo vote_e( t( 'Share the page' ) ); ?></button></p><?php endif; ?>
				<details class="post" open>
					<summary><?php echo vote_e( t( 'Suggested post text' ) ); ?></summary>
					<div class="post-picks" id="post-picks" role="group" aria-label="<?php echo vote_e( t( 'Post versions' ) ); ?>"><?php foreach ( VOTE_POST_VARIANTS as $vk => $vl ) : ?><button type="button" class="pick-chip" data-variant="<?php echo $vk; ?>" aria-pressed="<?php echo 'plan' === $vk ? 'true' : 'false'; ?>"><?php echo vote_e( t( $vl ) ); ?></button><?php endforeach; ?></div>
					<textarea id="post-text" rows="7"<?php if ( $vote_st ) : ?> data-posts="<?php echo vote_e( vote_json( vote_post_all( $vote_st ) ) ); ?>"<?php endif; ?> aria-label="<?php echo vote_e( t( 'Suggested post text' ) ); ?>"><?php echo $vote_st ? vote_e( vote_post( $vote_st ) ) : ''; ?></textarea>
					<button type="button" class="btn btn--quiet" id="copy"><?php echo vote_e( t( 'Copy post text' ) ); ?></button>
				</details>
			</div>
		</div>

		<h2 class="display-h before-h" id="before-h"><?php echo vote_e( t( 'post' === vote_phase() ? 'Official Election Information' : ( $vote_st && vote_verified( $vote_st ) && 'mail' === $vote_st['kind'] ? 'How to Get Your Ballot In' : 'When, Where, and What to Bring' ) ) ); ?></h2>
		<section class="info" id="info" aria-live="polite" aria-labelledby="before-h"><?php echo $vote_st ? vote_info( $vote_st ) : ''; ?></section>
		<?php echo $vote_st ? vote_guide( $vote_st ) : ''; ?>
	</div>

	<?php
	// Same rule elsewhere: other verified states that vote by mail, have no early voting for everyone,
	// or open early voting on the same day. Neighbors and previous/next follow.
	$vote_same = array();
	$vote_same_h = '';
	if ( $vote_st && vote_verified( $vote_st ) ) {
		foreach ( $vote_rows as $abbr => $row ) {
			if ( $abbr === $vote_abbr || ! vote_verified( $row ) || ( $row['kind'] ?? '' ) !== $vote_st['kind'] ) { continue; }
			if ( 'in-person' === $vote_st['kind'] && ( empty( $vote_st['start'] ) || ( $row['start'] ?? '' ) !== $vote_st['start'] ) ) { continue; }
			$vote_same[] = $abbr;
		}
		$vote_same_h = array( 'mail' => t( 'Other States That Vote by Mail' ), 'none' => t( 'Other States Without Early Voting for Everyone' ), 'ended' => t( 'Other States Where Early Voting Has Ended' ) )[ $vote_st['kind'] ]
			?? ( ! empty( $vote_st['start'] ) ? t( vote_today() >= $vote_st['start'] ? 'Other States Where Early Voting Began %s' : 'Other States Where Early Voting Opens %s', vote_long( $vote_st['start'] ) ) : '' );
	}
	$vote_near = $vote_st && isset( VOTE_NEIGHBORS[ $vote_abbr ] ) ? explode( ' ', VOTE_NEIGHBORS[ $vote_abbr ] ) : array();
	$vote_keys = array_keys( $vote_rows );
	$vote_pos = $vote_st ? array_search( $vote_abbr, $vote_keys, true ) : false;
	$vote_prev = false !== $vote_pos && $vote_pos > 0 ? $vote_keys[ $vote_pos - 1 ] : '';
	$vote_next = false !== $vote_pos && $vote_pos < count( $vote_keys ) - 1 ? $vote_keys[ $vote_pos + 1 ] : '';
	?>
	<div class="discover">
	<?php if ( $vote_st ) : ?><div class="discover__find"><?php echo vote_finder( 'Looking for another state?', 'Type a state name' ); ?></div><?php endif; ?>
	<?php if ( $vote_same && $vote_same_h ) : ?>
	<section class="near" aria-labelledby="same-h">
		<h2 id="same-h"><?php echo vote_e( $vote_same_h ); ?></h2>
		<ul>
			<?php foreach ( $vote_same as $abbr ) : ?>
			<li><a href="<?php echo vote_pbase() . $vote_meta[ $abbr ]['slug']; ?>/"><?php echo vote_e( vote_lname( $vote_rows[ $abbr ] ) ); ?></a><span><?php echo vote_status_html( $vote_meta[ $abbr ]['status'] ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php endif; ?>
	<?php if ( $vote_near ) : ?>
	<section class="near" aria-labelledby="near-h">
		<h2 id="near-h"><?php echo vote_e( t( 'States Next Door' ) ); ?></h2>
		<ul>
			<?php foreach ( $vote_near as $abbr ) : ?>
			<li><a href="<?php echo vote_pbase() . $vote_meta[ $abbr ]['slug']; ?>/"><?php echo vote_e( vote_lname( $vote_rows[ $abbr ] ) ); ?></a><span><?php echo vote_status_html( $vote_meta[ $abbr ]['status'] ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php endif; ?>

	<?php if ( $vote_st ) : ?>
	<nav class="pager" aria-label="<?php echo vote_e( t( 'Previous and next state' ) ); ?>">
		<?php if ( $vote_prev ) : ?><a rel="prev" href="<?php echo vote_pbase() . $vote_meta[ $vote_prev ]['slug']; ?>/"><span><?php echo vote_e( t( 'Previous state' ) ); ?></span><?php echo vote_e( vote_lname( $vote_rows[ $vote_prev ] ) ); ?></a><?php else : ?><span></span><?php endif; ?>
		<?php if ( $vote_next ) : ?><a rel="next" class="pager__next" href="<?php echo vote_pbase() . $vote_meta[ $vote_next ]['slug']; ?>/"><span><?php echo vote_e( t( 'Next state' ) ); ?></span><?php echo vote_e( vote_lname( $vote_rows[ $vote_next ] ) ); ?></a><?php endif; ?>
	</nav>
	<?php endif; ?>

	<nav class="states" aria-labelledby="states-h">
		<h2 id="states-h"><?php echo vote_e( t( $vote_st ? 'All States' : 'Browse All States' ) ); ?></h2>
		<details class="states__all"<?php echo $vote_st ? '' : ' open'; ?>><summary><?php echo vote_e( t( 'Show all 50 states and DC' ) ); ?></summary>
		<ul>
			<?php foreach ( $vote_rows as $abbr => $row ) : ?>
			<li><?php if ( $abbr === $vote_abbr ) : ?><span aria-current="page"><?php echo vote_e( vote_lname( $row ) ); ?></span><?php else : ?><a href="<?php echo vote_pbase() . $vote_meta[ $abbr ]['slug']; ?>/"><?php echo vote_e( vote_lname( $row ) ); ?></a><?php endif; ?><span class="states__when"><?php echo vote_status_html( $vote_meta[ $abbr ]['status'] ); ?></span></li>
			<?php endforeach; ?>
		</ul>
		</details>
	</nav>
	</div>

	<?php if ( ! $vote_st && ! $vote_404 ) : ?>
	<section class="why" aria-labelledby="why-h">
		<h2 id="why-h" class="display-h"><?php echo vote_e( t( 'Why This Exists' ) ); ?></h2>
		<p><?php echo vote_e( t( 'The paper I voted sticker is a small way of saying you showed up. These days more people see your profile picture than your shirt, so Vote Sticker puts that reminder where people look.' ) ); ?> <?php echo vote_e( t( 'Garrett Digital in Austin built it.' ) ); ?> <?php echo vote_e( t( "It's free, nonpartisan, and sells nothing." ) ); ?> <a href="<?php echo vote_pbase(); ?>about/"><?php echo vote_e( t( 'More about Vote Sticker' ) ); ?></a></p>
		<?php echo vote_history_why(); ?>
	</section>
	<?php endif; ?>
	<?php if ( ! $vote_404 ) : ?>
	<section class="faq" aria-labelledby="faq-h">
		<h2 id="faq-h"><?php echo vote_e( t( 'Questions People Ask' ) ); ?></h2>
		<?php if ( $vote_st && vote_state_faq( $vote_st ) ) : ?><p class="help"><?php echo vote_e( t( 'Answers come from official %s sources.', vote_lname( $vote_st ) ) ); ?> <?php echo vote_e( t( 'Last checked %s.', vote_checked( vote_page_checked( $vote_st ) ) ) ); ?></p><?php endif; ?>
		<?php if ( $vote_st ) : foreach ( vote_state_faq( $vote_st ) as $vote_q ) : ?>
		<details class="faq__state"><summary><?php echo vote_e( $vote_q[0] ); ?></summary><p><?php echo vote_e( $vote_q[1] ); ?></p><p class="faq__src"><?php echo vote_es() ? 'Fuente: ' : 'From '; ?><a href="<?php echo vote_e( $vote_q[2] ); ?>" target="_blank" rel="noopener"><?php echo vote_e( vote_src_name( $vote_st, $vote_q[2] ) ); ?></a>.</p></details>
		<?php endforeach; endif; ?>
		<details><summary><?php echo vote_e( t( 'How do I make it my profile picture?' ) ); ?></summary><p><?php echo vote_e( t( 'Tap Download picture, then open your profile settings in the app and choose the saved image as your photo. The design stays inside the circle that profile photos are cropped to.' ) ); ?></p></details>
		<?php if ( 'post' === vote_phase() ) : ?>
		<details><summary><?php echo vote_e( t( 'Can I still get an I voted sticker after the election?' ) ); ?></summary><p><?php echo vote_e( t( 'Yes. Save a free sticker, or make one with your own picture. The designs stay up after Election Day.' ) ); ?></p></details>
		<details><summary><?php echo vote_e( t( 'I voted early or by mail. Which sticker should I use?' ) ); ?></summary><p><?php echo vote_e( t( 'Pick the one that fits: I voted, I voted early or I voted by mail. They are all in the Main message menu.' ) ); ?></p></details>
		<?php else : ?>
		<details><summary><?php echo vote_e( t( 'When should I switch to I voted?' ) ); ?></summary><p><?php echo vote_e( t( 'After you vote. Choose I voted in the Main message menu, or I voted early or I voted by mail if that fits.' ) ); ?></p></details>
		<?php endif; ?>
		<?php if ( ! $vote_st ) : ?>
		<details><summary><?php echo vote_e( t( 'Is it really free?' ) ); ?></summary><p><?php echo vote_e( t( "Yes. There's nothing to buy, no account and no email list. Garrett Digital built it as a public service and pays for the hosting." ) ); ?></p></details>
		<details><summary><?php echo vote_e( t( 'Do you keep my information?' ) ); ?></summary><p><?php echo vote_e( t( 'Your sticker is made in your browser and never uploaded, and the site never asks for a photo, a name or an email address. Google Analytics and Ahrefs Web Analytics count visits; the About page lists exactly what they record.' ) ); ?></p></details>
		<?php endif; ?>
		<details><summary><?php echo vote_e( t( 'Is this an official election site?' ) ); ?></summary><p><?php echo vote_e( t( 'No. Vote Sticker is an independent, nonpartisan tool made by one person. For registration, polling places, and deadlines, go to your state or local election office. Each state page links to it.' ) ); ?></p></details>
		<details><summary><?php echo vote_e( t( 'Where do the dates come from?' ) ); ?></summary><p><?php echo vote_e( t( "From official state sources: the election office's own pages or state law, checked and dated. Until a state's dates are confirmed, its stickers say Election Day." ) ); ?></p></details>
		<?php $vote_mailst = $vote_st && vote_verified( $vote_st ) && 'mail' === $vote_st['kind']; if ( ! $vote_mailst && 'post' !== vote_phase() ) : ?>
		<details><summary><?php echo vote_e( t( 'Why do some states say hours or start dates vary?' ) ); ?></summary><p><?php echo vote_e( t( 'In some states, counties or towns set their own early voting days, hours, and locations. Your local election office has the final details.' ) ); ?></p></details>
		<details><summary><?php echo vote_e( t( "What if my state doesn't have early voting?" ) ); ?></summary><p><?php echo vote_e( t( "Your sticker says VOTE NOV 3. If you can't vote in person on Election Day, you may qualify for an absentee ballot. Your state's page links to the rules." ) ); ?></p></details>
		<?php endif; ?>
		<?php if ( $vote_st ) : ?>
		<details><summary><?php echo vote_e( t( 'Do you keep my information?' ) ); ?></summary><p><?php echo vote_e( t( 'Your sticker is made in your browser and never uploaded, and the site never asks for a photo, a name or an email address. Google Analytics and Ahrefs Web Analytics count visits; the About page lists exactly what they record.' ) ); ?></p></details>
		<?php else : ?>
		<details><summary><?php echo vote_e( t( 'Can I post a photo of my ballot?' ) ); ?></summary><p><?php echo vote_e( t( 'It depends on the state. Some states allow a picture of your own marked ballot, and some make it illegal to show one. Each state page says what that state allows, where we could confirm it. The stickers made here show no ballot.' ) ); ?></p></details>
		<details><summary><?php echo vote_e( t( 'Who made this?' ) ); ?></summary><p><?php echo vote_e( t( 'Garrett Digital, an SEO and search marketing consultancy in Austin, Texas.' ) ); ?> <a href="<?php echo vote_pbase(); ?>about/"><?php echo vote_e( t( 'More on the About page' ) ); ?></a>.</p></details>
		<?php endif; ?>
	</section>
	<?php endif; ?>
<?php endif; ?>
</main>

<footer class="foot">
	<div class="wrap">
		<p><b><?php echo vote_e( t( 'Vote Sticker is independent and nonpartisan.' ) ); ?></b> <?php echo vote_e( t( "It isn't affiliated with any government office, campaign or party." ) ); ?> <?php echo vote_e( t( 'Voting dates and links come from official election sources.' ) ); ?> <?php echo vote_e( t( $vote_st && vote_verified( $vote_st ) && 'mail' === $vote_st['kind'] ? 'Before you vote, check your registration, ballot status, and drop box locations with your county elections office.' : 'Before you vote, confirm your registration, polling place, hours, and ID requirements with your state or local election office.' ) ); ?> <a href="<?php echo vote_e( t( 'https://www.usa.gov/state-election-office' ) ); ?>"><?php echo vote_e( t( 'Find your election office' ) ); ?></a>.</p>
		<nav class="foot__links" aria-label="<?php echo vote_e( t( 'Site' ) ); ?>"><a href="<?php echo vote_pbase(); ?>#state-find"><?php echo vote_e( t( 'Make a sticker' ) ); ?></a> <a href="<?php echo vote_pbase(); ?>#states-h"><?php echo vote_e( t( 'State stickers' ) ); ?></a> <a href="<?php echo vote_pbase() . VOTE_HISTORY_SLUG; ?>/"<?php echo $vote_history ? ' aria-current="page"' : ''; ?>><?php echo vote_e( t( 'Sticker History' ) ); ?></a> <a href="<?php echo vote_pbase(); ?>about/"<?php echo $vote_about ? ' aria-current="page"' : ''; ?>><?php echo vote_e( t( 'About Vote Sticker' ) ); ?></a> <a href="<?php echo vote_pbase(); ?>about/#fix-h"><?php echo vote_e( t( 'Share a source or correction' ) ); ?></a></nav>
		<p class="foot__made"><span><?php echo vote_e( t( 'Built in Austin by' ) ); ?></span> <a class="foot__gd" href="https://www.garrettdigital.com/"><img src="<?php echo vote_v( 'assets/gd-logo.svg' ); ?>" alt="Garrett Digital" width="152" height="40"></a></p>
		<p class="foot__gd-line"><?php echo vote_e( t( 'An SEO and search marketing consultancy that builds useful websites, tools and measurement for businesses and nonprofits.' ) ); ?> <a href="https://www.garrettdigital.com/contact/"><?php echo vote_e( t( "Let's talk." ) ); ?></a></p>
	</div>
</footer>

<?php
// Hand-made pictures: every JPG in assets/states/<abbr>/ shows under that state, in natural order.
$vote_gallery = array();
foreach ( glob( __DIR__ . '/assets/states/*', GLOB_ONLYDIR ) as $dir ) { $vote_gallery[ strtoupper( basename( $dir ) ) ] = vote_files( basename( $dir ) ); }
?>
<script>window.VOTE_GALLERY = <?php echo vote_json( $vote_gallery ); ?>; window.VOTE_HEROES = <?php echo vote_json( array_combine( array_keys( $vote_gallery ), array_map( 'vote_hero_file', array_keys( $vote_gallery ) ) ) ); ?>;
window.VOTE_META = <?php echo vote_json( $vote_meta ); ?>;
window.VOTE_ALTS = <?php echo vote_json( $vote_st ? array_map( function ( $f, $i ) use ( $vote_st ) { return vote_alt( $f, $vote_st, $i ); }, vote_files( $vote_abbr ), array_keys( vote_files( $vote_abbr ) ) ) : array() ); ?>;
window.VOTE_BASE = <?php echo vote_json( vote_pbase() ); ?>;
window.VOTE_LANG = <?php echo vote_json( $vote_lang ); ?>;
window.VOTE_PHASE = <?php echo vote_json( vote_phase() ); ?>;
window.VOTE_T = <?php
// Maker strings, from the same dictionary as the page.
$vote_js = array();
foreach ( array( 'Tap Save to Photos, then Save Image. Then set it as your profile picture from Photos.', 'Tap Open image, press and hold the picture, and choose Save to Photos.', 'The picture saves to your Downloads. Set it as your profile picture from there.', 'The picture saves to your Downloads folder.', 'Save to Photos', 'Press and hold the picture, then tap Save to Photos.', 'Done', 'Download picture', 'Copied', 'Copy post text', 'Link copied', 'Copy this: ', 'No state matches. Try the full name.', 'Official info: ', 'Profile picture: ', 'Election Day is %s.', 'Tuesday, November 3, 2026', 'Pick a %s Sticker', 'Or Make Your Own', 'Make Your %s Sticker', '%s vote sticker, ready-made design %s', 'Sticker', 'Poster', 'Stamp', 'Big type', 'Tilt', 'Brush', 'Side by side', 'Arch', 'Plate', 'Split', 'Map below', 'Shadow', 'Ring', 'Half', 'Varsity', 'Masthead', 'State colors', 'Cream and navy', 'Navy and red', 'Forest and gold', 'Cobalt and chartreuse', 'Maroon and gold', 'Plum and cream', 'Teal and gold', 'Burnt orange and cream', 'Charcoal and gold', 'Vote early', 'Vote by mail', 'Vote Nov 3', 'I voted', 'I voted by mail', 'My ballot is in', 'I voted early', 'Vota temprano (español)', 'Vota por correo (español)', 'Vota 3 de nov (español)', 'Ya voté (español)', 'Ya voté temprano (español)', 'Ya voté por correo (español)', 'Voy a votar (español)', 'First-time voter', 'Mi primer voto (español)', 'Future voter', 'Yo también votaré (español)', 'Copy image description', 'Sticker in %s for %s reading %s.', 'Sticker in %s state colors reading %s.', '%s colors' ) as $k ) { $vote_js[ $k ] = t( $k ); }
echo vote_json( $vote_js ); ?>;
window.VOTE_ASSETS = <?php echo vote_json( VOTE_BASE . 'assets/' ); ?>;
window.VOTE_PAGE_STATE = <?php echo vote_json( $vote_abbr ); ?>;</script>
<script>(function(){var D=864e5;function n(d){return Date.UTC(d.getFullYear(),d.getMonth(),d.getDate())/D;}
document.querySelectorAll('[data-countdown]').forEach(function(el){var t=el.querySelector('time'),r=el.querySelector('.countdown__rel'),l=el.querySelector('[data-countdown-label]'),m=/^(\d{4})-(\d{2})-(\d{2})$/.exec(t?t.getAttribute('datetime'):'');if(!m||!r||!l){return;}
var e=new Date(+m[1],+m[2]-1,+m[3]);function u(){var d=n(e)-n(new Date());r.hidden=d<0;if(el.hasAttribute('data-hide-after')){el.hidden=d<0;}l.textContent=d<0?'':d===0?el.dataset.today:d===1?el.dataset.tomorrow:el.dataset.away.replace('%s',d);}u();setInterval(u,6e4);});})();</script>
<script src="<?php echo vote_v( 'states.js' ); ?>"></script>
<script src="<?php echo vote_v( 'state-shapes.js' ); ?>"></script>
<script src="<?php echo vote_v( 'app.js' ); ?>"></script>
</body>
</html>
