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
function vote_sfield( $st, $key ) { return vote_es() ? ( $GLOBALS['vote_i18n']['states'][ $st['abbr'] ][ $key ] ?? '' ) : ( $st[ $key ] ?? '' ); }
const VOTE_MESES = array( 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre' );
const VOTE_MES = array( 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sept', 'oct', 'nov', 'dic' );
const VOTE_DIAS = array( 'domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado' );

function vote_states() {
	$rows = array();
	foreach ( file( __DIR__ . '/states.js' ) as $line ) {
		$row = json_decode( rtrim( trim( $line ), ',' ), true );
		if ( isset( $row['abbr'] ) ) { $rows[ $row['abbr'] ] = $row; }
	}
	return $rows;
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
	return array_map( function ( $f ) use ( $abbr ) { return vote_v( 'assets/states/' . strtolower( $abbr ) . '/' . $f ); }, array_values( $files ) );
}

// The state hero shows one standout ready-made, tilted (October 20, Garrett): the design named here,
// or the first in the set. Numbers match <abbr>-vote-N.jpg.
const VOTE_HERO = array( 'MS' => 5, 'MI' => 7, 'ID' => 7, 'NJ' => 5, 'OR' => 5, 'MN' => 6, 'NE' => 6, 'AL' => 6, 'AZ' => 6, 'CT' => 7, 'DE' => 5 );
function vote_hero_file( $abbr ) {
	$n = VOTE_HERO[ $abbr ] ?? 0;
	$k = strtolower( $abbr );
	if ( $n && is_file( __DIR__ . "/assets/states/$k/$k-vote-$n.jpg" ) && ! vote_es() ) { return vote_v( "assets/states/$k/$k-vote-$n.jpg" ); }
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
	$save = $n ? 'Save one of ' . $n . ' ready-made I voted and vote stickers or make your own' : 'Make your own I voted or vote sticker';
	$with = function ( $what ) use ( $n ) { return $n ? 'Save one of ' . $n . ' ready-made stickers with ' . $what . ', or make your own' : 'Make a sticker with ' . $what; };
	if ( 'mail' === $kind ) {
		$tag = 'Vote by Mail, Due ' . vote_short( $st['end'] );
		$ribbon = 'VOTE BY MAIL, DUE ' . strtoupper( vote_short( $st['end'] ) );
		$fact = $name . ' votes by mail, with ballots due ' . vote_long( $st['end'] ) . '.';
		$lead = $with( $name . '’s ballot deadline' ) . '. Each one fits the profile-picture circle on Facebook, Instagram, and LinkedIn.';
		$status = 'votes by mail, due ' . vote_short( $st['end'] );
		$family = 'mail';
	} elseif ( 'in-person' === $kind && ! empty( $st['start'] ) && empty( $st['dateLine'] ) ) {
		$same = date( 'n', strtotime( $st['start'] ) ) === date( 'n', strtotime( $st['end'] ) );
		$range = vote_range( $st['start'], $st['end'] );
		$tag = 'Early Voting ' . $range;
		$ribbon = 'EARLY VOTING ' . strtoupper( $range );
		$fact = 'Early voting in ' . $name . ' runs ' . vote_long( $st['start'] ) . ' to ' . ( $same ? date( 'j', strtotime( $st['end'] ) ) : vote_long( $st['end'] ) ) . '.';
		$lead = $with( $name . '’s early voting dates' ) . '. Then check the official link for places and hours.';
		$status = 'early voting ' . $range;
		$family = 'early';
	} elseif ( 'in-person' === $kind && ! empty( $st['end'] ) ) {
		$tag = 'Early Voting Through ' . vote_short( $st['end'] );
		$ribbon = 'EARLY VOTING THROUGH ' . strtoupper( vote_short( $st['end'] ) );
		$fact = 'Early voting in ' . $name . ' runs through ' . vote_long( $st['end'] ) . '.';
		$lead = $with( $name . '’s early voting dates' ) . '. Then check the official link for places and hours.';
		$status = 'early voting through ' . vote_short( $st['end'] );
		$family = 'early';
	} elseif ( 'none' === $kind ) {
		$tag = 'Vote on Election Day, Nov 3';
		$ribbon = 'VOTE ON ELECTION DAY, NOV 3';
		$fact = $name . ' doesn’t have early voting open to everyone. Election Day is Tuesday, November 3.';
		$lead = 'These stickers say Election Day, Tuesday, November 3. Can’t make it that day? Check whether you can vote absentee in ' . $name . '.';
		$status = 'Election Day, Nov 3';
		$family = 'eday';
	} else {
		$tag = 'Election Day Nov 3';
		$ribbon = 'ELECTION DAY, NOV 3';
		$fact = 'Election Day is Tuesday, November 3. Early voting dates for ' . $name . ' aren’t checked here yet.';
		$lead = 'Election Day is Tuesday, November 3. We haven’t confirmed ' . $name . '’s early voting dates yet, so these stickers say Election Day.';
		$status = 'dates not checked yet';
		$family = 'unchecked';
	}
	return array(
		'slug' => vote_slug( $st['name'] ),
		// "I voted sticker" is the term people search (Ahrefs, October 19); long names drop "and Vote" to fit.
		'title' => strlen( $name . ' I Voted and Vote Stickers: ' . $tag ) > 62 ? $name . ' I Voted Stickers: ' . $tag : $name . ' I Voted and Vote Stickers: ' . $tag,
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
	$save = $n ? 'Guarda uno de ' . $n . ' stickers listos para usar o crea el tuyo' : 'Crea tu propio sticker';
	$with = function ( $what ) use ( $n ) { return $n ? 'Guarda uno de ' . $n . ' stickers listos para usar con ' . $what . ' o crea el tuyo' : 'Crea un sticker con ' . $what; };
	if ( 'mail' === $kind ) {
		$tag = 'vota por correo hasta el ' . vote_short( $st['end'] );
		$ribbon = 'VOTA POR CORREO, HASTA EL ' . vote_upper( vote_short( $st['end'] ) );
		$fact = $name . ' vota por correo y las boletas deben entregarse a más tardar el ' . vote_long( $st['end'] ) . '.';
		$lead = $with( 'la fecha límite para votar por correo en ' . $name ) . '. Cada uno cabe en el círculo de la foto de perfil de Facebook, Instagram y LinkedIn.';
		$status = 'vota por correo, hasta el ' . vote_short( $st['end'] );
	} elseif ( 'in-person' === $kind && ! empty( $st['start'] ) && empty( $st['dateLine'] ) ) {
		$range = vote_range( $st['start'], $st['end'] );
		$same = date( 'n', strtotime( $st['start'] ) ) === date( 'n', strtotime( $st['end'] ) );
		$tag = 'votación anticipada ' . $range;
		$ribbon = 'VOTACIÓN ANTICIPADA ' . vote_upper( $range );
		$fact = 'La votación anticipada en ' . $name . ' es del ' . ( $same ? date( 'j', strtotime( $st['start'] ) ) : vote_long( $st['start'] ) ) . ' al ' . vote_long( $st['end'] ) . '.';
		$lead = $with( 'las fechas de votación anticipada de ' . $name ) . '. Después revisa el enlace oficial para ver lugares y horarios.';
		$status = 'votación anticipada ' . $range;
	} elseif ( 'in-person' === $kind && ! empty( $st['end'] ) ) {
		$tag = 'votación anticipada hasta el ' . vote_short( $st['end'] );
		$ribbon = 'VOTACIÓN ANTICIPADA HASTA EL ' . vote_upper( vote_short( $st['end'] ) );
		$fact = 'La votación anticipada en ' . $name . ' es hasta el ' . vote_long( $st['end'] ) . '.';
		$lead = $with( 'las fechas de votación anticipada de ' . $name ) . '. Después revisa el enlace oficial para ver lugares y horarios.';
		$status = 'votación anticipada hasta el ' . vote_short( $st['end'] );
	} elseif ( 'none' === $kind ) {
		$tag = 'vota el Día de las Elecciones, 3 nov';
		$ribbon = 'VOTA EL DÍA DE LAS ELECCIONES, 3 NOV';
		$fact = $name . ' no tiene votación anticipada abierta a todos. El Día de las Elecciones es el martes 3 de noviembre.';
		$lead = 'Estos stickers dicen Día de las Elecciones, martes 3 de noviembre. ¿No puedes ir ese día? Revisa si puedes votar en ausencia en ' . $name . '.';
		$status = 'Día de las Elecciones, 3 nov';
	} else {
		$tag = 'Día de las Elecciones, 3 nov';
		$ribbon = 'DÍA DE LAS ELECCIONES, 3 NOV';
		$fact = 'El Día de las Elecciones es el martes 3 de noviembre. Aún no hemos verificado las fechas de votación anticipada de ' . $name . '.';
		$lead = 'El Día de las Elecciones es el martes 3 de noviembre. Aún no hemos verificado las fechas de votación anticipada de ' . $name . ', así que estos stickers dicen Día de las Elecciones.';
		$status = 'fechas aún sin verificar';
	}
	return array(
		'slug' => vote_slug( $st['name'] ),
		'title' => 'Stickers para votar en ' . $name . ': ' . $tag,
		'h1' => 'Stickers para votar en ' . $name,
		'crumb' => $name,
		'ribbon' => $ribbon,
		'lead' => $lead,
		'desc' => $fact . ' ' . $save . ', del tamaño del círculo de la foto de perfil. Gratis y no partidista.',
		'status' => $status,
		'family' => $kind,
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
// The verified state questions for this page, in VOTE_FAQ_Q order: [question, answer, source URL].
function vote_state_faq( $st ) {
	static $all = null;
	if ( null === $all ) { $all = require __DIR__ . '/faq-states.php'; }
	$out = array();
	foreach ( VOTE_FAQ_Q as $key => $q ) {
		$a = $all[ $st['abbr'] ][ $key ] ?? null;
		if ( ! $a || empty( $a['src'] ) || empty( $a[ vote_es() ? 'es' : 'en' ] ) ) { continue; }
		$out[] = array( t( $q, vote_name( $st ) ), $a[ vote_es() ? 'es' : 'en' ], $a['src'] );
	}
	return $out;
}

// Suggested post text, one version per sticker message (rules and evidence: docs/post-text-rules.md).
// First person, one useful state fact, a plan question (never "tag a friend"), an invite back, and
// three hashtags that match the message. The maker swaps versions when the message changes.
const VOTE_POST_KINDS = array( 'pre_early', 'pre_mail', 'pre_day', 'post', 'post_early', 'post_mail' );

// The version that matches the state's default sticker (app.js stateHeadline()).
function vote_post_default( $st ) {
	if ( ! vote_verified( $st ) || ! in_array( $st['kind'], array( 'in-person', 'mail' ), true ) ) { return 'pre_day'; }
	return 'mail' === $st['kind'] ? 'pre_mail' : 'pre_early';
}

function vote_post( $st, $kind = null ) {
	$kind = $kind ?: vote_post_default( $st );
	$es = vote_es();
	$name = vote_name( $st );
	$open = $es
		? array( 'pre_early' => 'Voy a votar temprano.', 'pre_mail' => 'Voy a entregar mi boleta con tiempo.', 'pre_day' => 'Voy a votar el Día de las Elecciones.', 'post' => 'Ya voté.', 'post_early' => 'Ya voté temprano.', 'post_mail' => 'Ya entregué mi boleta.' )
		: array( 'pre_early' => "I'm voting early.", 'pre_mail' => "I'm getting my ballot in early.", 'pre_day' => "I'm voting on Election Day.", 'post' => 'I voted.', 'post_early' => 'I voted early.', 'post_mail' => 'My ballot is in.' );
	$ask = $es
		? array( 'pre_mail' => '¿Cuándo vas a devolver tu boleta?', 'post' => '¿Y tú, ya tienes tu plan para votar?', 'default' => '¿Ya tienes tu plan para votar?' )
		: array( 'pre_mail' => 'When are you sending yours back?', 'post' => "What's your plan to vote?", 'default' => "What's your plan to vote?" );
	$question = $ask[ 0 === strpos( $kind, 'post' ) ? 'post' : $kind ] ?? $ask['default'];
	// Full https:// URL so it links everywhere, LinkedIn and email included.
	$link = 'https://votesticker.org/' . ( $es ? 'es/' : '' ) . vote_slug( $st['name'] );
	$invite = $es
		? '¿Quieres uno? Haz gratis tu sticker para votar en ' . $name . ': ' . $link
		: 'Want one? Make your own free ' . $name . ' vote sticker: ' . $link;
	return $open[ $kind ] . ' ' . vote_post_body( $st ) . ' ' . $question . "\n\n" . $invite . "\n" . vote_hashtags( $st, $kind );
}

// Three tags: the message's own tag, the brand, and the state (#VoteTexas). #IVoted only after voting.
function vote_hashtags( $st, $kind ) {
	$state = 'DC' === $st['abbr'] ? 'DC' : str_replace( ' ', '', $st['name'] );
	$lead = vote_es()
		? array( 'pre_early' => '#VotaTemprano', 'pre_mail' => '#VotaPorCorreo', 'pre_day' => '#YoVoto' )
		: array( 'pre_early' => '#VoteEarly', 'pre_mail' => '#VoteByMail', 'pre_day' => '#ElectionDay' );
	$tag = $lead[ $kind ] ?? ( vote_es() ? '#YaVoté' : '#IVoted' );
	return $tag . ' #VoteSticker #Vote' . $state;
}

// Every version, for the maker to swap in (JSON on the textarea).
function vote_post_all( $st ) {
	$out = array();
	foreach ( VOTE_POST_KINDS as $k ) { $out[ $k ] = vote_post( $st, $k ); }
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
		if ( vote_verified( $st ) && 'none' === $st['kind'] ) { return 'El Día de las Elecciones en ' . $name . ' es el martes 3 de noviembre de 2026. Haz tu plan para votar. Los lugares de votación y las reglas del voto en ausencia están en ' . $label . '.'; }
		return 'El Día de las Elecciones es el martes 3 de noviembre de 2026. Haz tu plan para votar y revisa en vote411.org si puedes votar por anticipado en ' . $name . '.';
	}
	if ( vote_verified( $st ) && ! empty( $st['summary'] ) ) { return $st['summary'] . ' Details at ' . $label . '.'; }
	if ( vote_verified( $st ) && 'mail' === $st['kind'] ) { return $name . ' votes by mail. Return your ballot by ' . vote_long( $st['end'] ) . '. Details at ' . $label . '.'; }
	if ( vote_verified( $st ) && 'in-person' === $st['kind'] ) {
		$same = date( 'n', strtotime( $st['start'] ) ) === date( 'n', strtotime( $st['end'] ) );
		return 'Early voting in ' . $name . ' runs ' . vote_long( $st['start'] ) . ' to ' . ( $same ? date( 'j', strtotime( $st['end'] ) ) : vote_long( $st['end'] ) ) . '. Find your polling place at ' . $label . '.';
	}
	if ( vote_verified( $st ) && 'none' === $st['kind'] ) { return 'Election Day in ' . $name . ' is Tuesday, November 3, 2026. Make a plan to vote. Polling places and absentee rules are at ' . $label . '.'; }
	return 'Election Day is Tuesday, November 3, 2026. Make a plan to vote, and check whether you can vote early in ' . $name . ' at vote411.org.';
}

// The state panel, server-rendered; app.js infoHtml() draws the same markup when the menu changes.
function vote_info( $st ) {
	if ( vote_es() ) { return vote_info_es( $st ); }
	$eday = 'Election Day is Tuesday, November 3, 2026.';
	if ( ! vote_verified( $st ) ) {
		return '<h2>Vote by Nov 3</h2><p>We haven’t checked ' . vote_e( $st['name'] ) . '’s early voting dates yet, so these stickers say Election Day. ' .
			'Look up your dates and polling place at <a href="https://www.vote411.org/' . vote_slug( $st['name'] ) . '" target="_blank" rel="noopener">Vote411</a> ' .
			'(League of Women Voters) or your state election office.</p><p>' . $eday . '</p>';
	}
	$kind = $st['kind'];
	$html = '<h2>' . ( 'mail' === $kind ? 'Vote by mail' : ( 'none' === $kind ? 'Vote on Election Day' : 'Vote early' ) ) . '</h2><p><b>';
	if ( ! empty( $st['summary'] ) ) {
		$html .= vote_e( $st['summary'] );
	} elseif ( 'mail' === $kind ) {
		$html .= vote_e( $st['name'] ) . ' votes by mail. Return your ballot by ' . vote_day( $st['end'] ) . '.';
	} elseif ( 'none' === $kind ) {
		$html .= vote_e( $st['name'] ) . ' doesn’t have early voting open to everyone. Most voters vote on Election Day, Tuesday, November 3, 2026.';
	} else {
		$html .= 'Early voting in ' . vote_e( $st['name'] ) . ' runs ' . vote_day( $st['start'] ) . ' to ' . vote_day( $st['end'] ) . '.';
	}
	$html .= '</b> ' . ( 'none' === $kind ? '' : $eday ) . '</p>';
	if ( ! empty( $st['note'] ) ) { $html .= '<p>' . vote_e( $st['note'] ) . '</p>'; }
	$html .= '<p><a class="official" href="' . vote_e( $st['officialUrl'] ) . '" target="_blank" rel="noopener"><span><b>Official ' . vote_e( $st['name'] ) . ' voting information</b><small>' . ( 'none' === $kind ? 'Absentee rules and polling places at ' : 'Polling places and hours at ' ) . vote_e( $st['officialLabel'] ) . '</small></span></a></p>';
	$html .= '<p class="source">Dates from the <a href="' . vote_e( $st['sourceUrl'] ) . '" target="_blank" rel="noopener">state election office</a>, checked ' . date( 'F j, Y', strtotime( $st['checked'] ) ) . '.</p>';
	return $html;
}

function vote_info_es( $st ) {
	$name = vote_lname( $st );
	$eday = 'El Día de las Elecciones es el martes 3 de noviembre de 2026.';
	if ( ! vote_verified( $st ) ) {
		return '<h2>Vota a más tardar el 3 de nov</h2><p>Aún no hemos verificado las fechas de votación anticipada de ' . vote_e( $name ) . ', así que estos stickers dicen Día de las Elecciones. ' .
			'Busca tus fechas y tu lugar de votación en <a href="https://www.vote411.org/' . vote_slug( $st['name'] ) . '" target="_blank" rel="noopener">Vote411</a> ' .
			'(League of Women Voters) o en la oficina electoral de tu estado.</p><p>' . $eday . '</p>';
	}
	$kind = $st['kind'];
	$html = '<h2>' . ( 'mail' === $kind ? 'Vota por correo' : ( 'none' === $kind ? 'Vota el Día de las Elecciones' : 'Vota temprano' ) ) . '</h2><p><b>';
	$sum = vote_sfield( $st, 'summary' );
	if ( $sum ) {
		$html .= vote_e( $sum );
	} elseif ( 'mail' === $kind ) {
		$html .= vote_e( $name ) . ' vota por correo. Devuelve tu boleta a más tardar el ' . vote_day( $st['end'] ) . '.';
	} elseif ( 'none' === $kind ) {
		$html .= vote_e( $name ) . ' no tiene votación anticipada abierta a todos. La mayoría de los votantes vota el Día de las Elecciones, martes 3 de noviembre de 2026.';
	} else {
		$html .= 'La votación anticipada en ' . vote_e( $name ) . ' es del ' . vote_day( $st['start'] ) . ' al ' . vote_day( $st['end'] ) . '.';
	}
	$html .= '</b> ' . ( 'none' === $kind ? '' : $eday ) . '</p>';
	$note = vote_sfield( $st, 'note' );
	if ( $note ) { $html .= '<p>' . vote_e( $note ) . '</p>'; }
	$html .= '<p><a class="official" href="' . vote_e( $st['officialUrl'] ) . '" target="_blank" rel="noopener"><span><b>Información oficial para votar en ' . vote_e( $name ) . '</b><small>' . ( 'none' === $kind ? 'Reglas del voto en ausencia y lugares de votación en ' : 'Lugares y horarios de votación en ' ) . vote_e( $st['officialLabel'] ) . '</small></span></a></p>';
	$t = strtotime( $st['checked'] );
	$html .= '<p class="source">Fechas tomadas de la <a href="' . vote_e( $st['sourceUrl'] ) . '" target="_blank" rel="noopener">oficina electoral del estado</a>, verificadas el ' . vote_long( $st['checked'] ) . ' de ' . date( 'Y', $t ) . '. Algunos sitios oficiales están solo en inglés.</p>';
	return $html;
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
$vote_path = (string) parse_url( $_SERVER['REQUEST_URI'] ?? VOTE_BASE, PHP_URL_PATH );
// Unknown addresses get a real 404 with the finder, popular states, and the full state list
// (Nielsen Norman Group's error-page guidance: say it plainly, keep the site's navigation, offer search
// and the most useful links). build/build_static.py writes the same page to dist/404.html.
$vote_404 = defined( 'VOTE_404' ) || ( defined( 'VOTE_SLUG' ) && ! $vote_abbr ) || ( ! defined( 'VOTE_SLUG' ) && ! in_array( $vote_path, array( vote_pbase(), vote_pbase() . 'index.php' ), true ) );
if ( $vote_404 ) {
	http_response_code( 404 );
	$vote_abbr = '';
}
$vote_st = $vote_abbr ? $vote_rows[ $vote_abbr ] : null;
$vote_page = $vote_st ? $vote_meta[ $vote_abbr ] : array(
	'title' => t( 'I Voted Sticker Maker: Free Vote Stickers by State' ),
	'h1' => t( 'Make your plan. Share the sticker.' ),
	'eyebrow' => t( 'Voting stickers for all 50 states and DC' ),
	'lead' => t( 'Pick your state, choose a design, and save a profile-picture sticker with your voting dates. Each one links to your official election office.' ),
	'desc' => t( 'Make a free I voted sticker or vote sticker for your profile picture. Pick your state and a design, download a PNG, and check official links for early voting, vote by mail, or Election Day.' ),
	'index' => ! vote_es() || VOTE_ES_INDEX,
);
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
$vote_tail = $vote_st ? $vote_page['slug'] . '/' : '';
$vote_url = VOTE_SITE . vote_pbase() . $vote_tail;
// The same page in each language, for hreflang and the language switch.
$vote_alt = array( 'en' => VOTE_SITE . VOTE_BASE . $vote_tail, 'es' => VOTE_SITE . VOTE_BASE . 'es/' . $vote_tail );
$vote_other = vote_es() ? 'en' : 'es';
// Share image: one per state from build/og_states.mjs, the hub's from build/og_image.mjs.
$vote_og_dir = vote_es() ? 'assets/og/es/' : 'assets/og/';
$vote_og = $vote_st && is_file( __DIR__ . '/' . $vote_og_dir . $vote_page['slug'] . '.jpg' ) ? $vote_og_dir . $vote_page['slug'] . '.jpg' : ( vote_es() && is_file( __DIR__ . '/assets/og-hub-es.jpg' ) ? 'assets/og-hub-es.jpg' : 'assets/og-hub.jpg' );
$vote_og_alt = vote_es()
	? ( $vote_st ? $vote_page['h1'] . ': ' . $vote_page['status'] . '. Un sticker para votar de ' . $vote_page['crumb'] . ' sobre papel color crema.' : 'Vote Sticker: haz tu plan, comparte el sticker. Stickers para votar en los 50 estados y DC.' )
	: ( $vote_st ? $vote_page['h1'] . ': ' . $vote_page['status'] . '. A ' . $vote_page['crumb'] . ' voting sticker on cream paper.' : 'Vote Sticker: make your plan, share the sticker. Voting stickers for all 50 states and DC, with ready-made designs from Texas, Washington, Pennsylvania, and California.' );

// Breadcrumbs start at the tool's hub, never the WordPress site (Garrett: no links back to the root).
$vote_crumbs = array( array( VOTE_BRAND, VOTE_SITE . vote_pbase() ) );
if ( $vote_st ) { $vote_crumbs[] = array( $vote_page['crumb'], $vote_url ); }
$vote_share = '<p class="share"><button type="button" class="share__btn" id="share-link" data-url="' . vote_e( $vote_url ) . '" data-title="' . vote_e( $vote_page['title'] ) . '"><svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.5 1.5"/><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7L12 19"/></svg><span id="share-label">' . ( $vote_st ? vote_e( t( 'Copy link to this %s page', vote_lname( $vote_st ) ) ) : vote_e( t( 'Copy page link' ) ) ) . '</span></button></p>';

// The state finder: a type-ahead box (app.js fills the suggestions). Without JavaScript the full list of
// state links below the maker does the same job.
function vote_finder( $label, $placeholder ) {
	return '<div class="finder"><label for="state-find">' . vote_e( t( $label ) ) . '</label>'
		. '<div class="finder__box"><input id="state-find" type="text" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="state-options" autocomplete="off" autocapitalize="words" spellcheck="false" placeholder="' . vote_e( t( $placeholder ) ) . '">'
		. '<button type="button" class="btn btn--gold" id="state-go">' . vote_e( t( 'Find' ) ) . '</button>'
		. '<ul id="state-options" role="listbox" aria-label="' . vote_e( t( 'Matching states' ) ) . '" hidden></ul></div>'
		. '<p class="help" id="state-help"><a href="#states-h">' . vote_e( t( 'Or browse the full list of states' ) ) . '</a></p></div>';
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
<link rel="stylesheet" href="<?php echo vote_v( 'app.css' ); ?>">
<?php if ( ! $vote_404 ) : ?><script type="application/ld+json"><?php echo vote_json( $vote_ld ); ?></script><?php endif; ?>
<?php if ( ! $vote_st && ! $vote_404 ) : ?><script type="application/ld+json"><?php echo vote_json( array( '@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => VOTE_BRAND, 'alternateName' => 'VoteSticker.org', 'url' => VOTE_SITE . vote_pbase(), 'inLanguage' => $vote_lang ) ); ?></script><?php echo "\n"; endif; ?>
<!-- Google tag (gtag.js) -->
<script src="https://analytics.ahrefs.com/analytics.js" data-key="JWFR/FwEHfD5yA/gz1K/Jg" async></script>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-PZWZTXDSDE"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-PZWZTXDSDE');
</script>
</head>
<body>
<?php if ( ! vote_es() && ! $vote_st && ! $vote_404 ) : ?>
<div class="lang-suggest" id="lang-suggest" lang="es" hidden><div class="wrap"><p>¿Prefieres ver esta página en español?</p><a class="btn btn--gold" href="<?php echo vote_e( $vote_alt['es'] ); ?>" data-lang-choice="es">Ver en español</a><button type="button" class="btn btn--quiet" id="lang-suggest-no">No, gracias</button></div></div>
<?php endif; ?>
<header class="top<?php echo $vote_st ? "" : " top--hub"; ?>">
	<div class="wrap">
		<div class="top__bar">
		<a class="brand" href="<?php echo vote_pbase(); ?>"<?php echo $vote_st ? '' : ' aria-current="page"'; ?>>
			<svg class="brand__mark" aria-hidden="true" viewBox="0 0 40 40" width="40" height="40"><path d="M37.61 23.74A18 18 0 1 0 23.74 37.61Z" fill="#FBF6E8"/><path d="M37.61 23.74A18 18 0 1 0 23.74 37.61Z" fill="none" stroke="#14295E" stroke-width="1.6" transform="translate(20 20) scale(.8) translate(-20 -20)"/><path d="M37.61 23.74L23.74 37.61Q26.4 26.4 37.61 23.74Z" fill="#D9C99A"/><path d="M18.60 9.80 L20.77 15.61 L26.97 15.88 L22.12 19.74 L23.77 25.72 L18.60 22.30 L13.43 25.72 L15.08 19.74 L10.23 15.88 L16.43 15.61Z" fill="#C1272D"/>
</svg>
			<span class="brand__name">VOTE <span class="brand__hi">STICKER</span></span>
			<span class="brand__tag"><?php echo vote_e( t( "Profile-picture stickers with your state's voting info" ) ); ?></span>
		</a>
		<a class="lang-switch" href="<?php echo vote_e( $vote_404 ? VOTE_SITE . ( vote_es() ? VOTE_BASE : VOTE_BASE . 'es/' ) : $vote_alt[ $vote_other ] ); ?>" hreflang="<?php echo $vote_other; ?>" lang="<?php echo $vote_other; ?>" data-lang-choice="<?php echo $vote_other; ?>"><?php echo vote_es() ? 'English' : 'Español'; ?></a>
		</div>
		<?php if ( $vote_st ) : ?>
		<nav class="crumbs" aria-label="<?php echo vote_e( t( 'Breadcrumb' ) ); ?>"><ol>
			<?php foreach ( $vote_crumbs as $i => $c ) : $last = $i === count( $vote_crumbs ) - 1; ?>
			<li><?php if ( $last ) : ?><span aria-current="page" id="crumb-last"><?php echo vote_e( $c[0] ); ?></span><?php else : ?><a href="<?php echo vote_e( $c[1] ); ?>"><?php echo vote_e( $c[0] ); ?></a><?php endif; ?></li>
			<?php endforeach; ?>
		</ol></nav>
		<?php endif; ?>
		<?php if ( $vote_st ) : $vote_lead_img = vote_hero_file( $vote_abbr ); $vote_art = vote_es() ? 'data:image/gif;base64,R0lGODlhAQABAAAAACw=' : $vote_lead_img; ?>
		<div class="state-hero<?php echo $vote_art ? ' state-hero--art' : ''; ?>">
			<div>
				<h1 id="page-h1"><?php echo vote_e( $vote_page['h1'] ); ?></h1>
				<p class="ribbon"><?php echo vote_e( $vote_page['ribbon'] ); ?></p>
				<p class="lead" id="page-lead"><?php echo vote_e( $vote_page['lead'] ); ?></p>
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
		<div class="hero">
			<div class="hero__text">
				<p class="<?php echo $vote_404 ? 'ribbon ribbon--err' : 'eyebrow'; ?>"><?php echo vote_e( $vote_page['eyebrow'] ); ?></p>
				<h1 id="page-h1"><?php echo vote_e( $vote_page['h1'] ); ?></h1>
				<p class="lead" id="page-lead"><?php echo vote_e( $vote_page['lead'] ); ?></p>
				<?php echo vote_finder( 'Your state', 'Type a state, like Texas or TX' ); ?>
				<?php if ( $vote_404 ) : ?>
				<nav class="err-links" aria-labelledby="err-links-h">
					<h2 id="err-links-h"><?php echo vote_e( t( 'Try one of these' ) ); ?></h2>
					<ul>
						<li><a href="<?php echo vote_pbase(); ?>"><?php echo vote_e( t( 'Vote Sticker home page' ) ); ?></a></li>
						<?php foreach ( array( 'TX', 'CA', 'FL', 'NY', 'PA' ) as $a ) : ?><li><a href="<?php echo vote_pbase() . $vote_meta[ $a ]['slug']; ?>/"><?php echo vote_e( t( '%s stickers', vote_lname( $vote_rows[ $a ] ) ) ); ?></a></li><?php endforeach; ?>
						<li><a href="<?php echo vote_pbase(); ?>#faq-h"><?php echo vote_e( t( 'Common Questions' ) ); ?></a></li>
					</ul>
				</nav>
				<?php else : ?><?php echo $vote_share; ?><?php endif; ?>
			</div>
			<?php if ( $vote_404 ) : ?>
			<div class="err-sticker" aria-hidden="true"><span>404</span><small><?php echo vote_e( t( 'Page not found' ) ); ?></small></div>
			<?php else : ?>
			<div class="hero__art" aria-hidden="true">
				<?php foreach ( array( array( 'tx', 2 ), array( 'ca', 4 ), array( 'nc', 2 ) ) as $k => $h ) : if ( is_file( __DIR__ . "/assets/states/{$h[0]}/{$h[0]}-vote-{$h[1]}.jpg" ) ) : ?>
				<img class="hero__sticker hero__sticker--<?php echo $k + 1; ?>" src="<?php echo vote_v( "assets/states/{$h[0]}/{$h[0]}-vote-{$h[1]}.jpg" ); ?>" alt="" width="1080" height="1080"<?php echo $k ? ' loading="lazy"' : ''; ?><?php echo vote_es() ? ' data-es-state="' . strtoupper( $h[0] ) . '"' : ''; ?>>
				<?php endif; endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</header>

<main class="wrap">

	<?php
	// Landing showcase (October 19): one polished ready-made design per state, most populous first,
	// mixing early dates, "by" dates and vote by mail. Shown in the circle a profile picture gets.
	$vote_showcase = array( 'ca' => 4, 'tx' => 2, 'ny' => 2, 'pa' => 4, 'nc' => 2, 'va' => 5, 'wa' => 6, 'co' => 7 );
	$vote_showcase = array_filter( $vote_showcase, function ( $n, $abbr ) { return is_file( __DIR__ . "/assets/states/$abbr/$abbr-vote-$n.jpg" ); }, ARRAY_FILTER_USE_BOTH );
	?>
	<?php if ( $vote_showcase && ! $vote_st ) : ?>
	<section class="showcase" id="showcase" aria-labelledby="showcase-h">
		<h2 id="showcase-h" class="display-h"><?php echo vote_e( t( $vote_404 ? 'Popular States' : 'Ready-Made Stickers' ) ); ?></h2>
		<p class="section-intro"><?php echo vote_e( t( $vote_404 ? 'Ready-made stickers from the most populous states. Tap one to see that state’s page.' : "A few from the most populous states. Tap one to see that state's full set, or find your state above to make your own." ) ); ?></p>
		<div class="grid grid--showcase">
			<?php foreach ( $vote_showcase as $abbr => $n ) : ?>
			<a class="pick" href="<?php echo vote_pbase() . $vote_meta[ strtoupper( $abbr ) ]['slug']; ?>/">
				<img src="<?php echo vote_v( "assets/states/$abbr/$abbr-vote-$n.jpg" ); ?>" alt="<?php echo vote_e( t( '%s vote profile picture example', vote_lname( $vote_rows[ strtoupper( $abbr ) ] ) ) ); ?>" width="1080" height="1080" loading="lazy"<?php echo vote_es() ? ' data-es-state="' . strtoupper( $abbr ) . '"' : ''; ?>>
				<span class="pick__name"><?php echo vote_e( vote_lname( $vote_rows[ strtoupper( $abbr ) ] ) ); ?></span>
			</a>
			<?php endforeach; ?>
		</div>
	</section>
	<?php if ( ! $vote_404 ) : ?>
	<section class="how-wrap" aria-labelledby="how-h">
		<h2 id="how-h" class="display-h"><?php echo vote_e( t( 'How It Works' ) ); ?></h2>
		<ol class="how">
			<li><b><?php echo vote_e( t( 'Pick your state' ) ); ?></b> <?php echo vote_e( t( "Early voting, vote by mail, or Election Day. Your sticker starts with what's true where you vote." ) ); ?></li>
			<li><b><?php echo vote_e( t( 'Save your sticker' ) ); ?></b> <?php echo vote_e( t( 'Use a ready-made design or make your own. It fits the profile-picture circle.' ) ); ?></li>
			<li><b><?php echo vote_e( t( 'Check the official link' ) ); ?></b> <?php echo vote_e( t( 'Your election office has the final word on places, hours, and ID rules.' ) ); ?></li>
		</ol>
	</section>
	<section class="trust">
		<svg aria-hidden="true" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#F2C14E" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"><path d="M3 9l9-5 9 5"/><path d="M5 9v9M10 9v9M14 9v9M19 9v9M3 21h18"/></svg>
		<p><b><?php echo vote_e( t( 'Nonpartisan by design.' ) ); ?></b> <?php echo vote_e( t( 'We make the reminder. Your state or local election office has the final word. No account, and nothing is uploaded.' ) ); ?></p>
	</section>
	<?php endif; ?>
	<?php endif; ?>

	<?php $vote_files = $vote_st ? vote_files( $vote_abbr ) : array(); ?>
	<div id="maker"<?php echo $vote_st ? '' : ' hidden'; ?>>
		<section class="gallery" id="gallery"<?php echo $vote_files ? '' : ' hidden'; ?> aria-labelledby="gallery-h">
			<h2 id="gallery-h" class="display-h"><?php echo vote_e( $vote_st ? t( 'Ready-Made %s Stickers', vote_lname( $vote_st ) ) : t( 'Ready-Made Stickers' ) ); ?></h2>
			<p><?php echo vote_e( t( 'Tap one to save it. Each keeps its words inside the profile-picture circle.' ) ); ?></p>
			<div class="grid grid--gallery" id="gallery-grid" style="--cols: <?php echo vote_cols( count( $vote_files ) ); ?>"><?php foreach ( $vote_files as $i => $src ) : ?><a class="tile" href="<?php echo vote_e( $src ); ?>" download="vote-<?php echo strtolower( $vote_abbr ) . '-' . ( $i + 1 ); ?>.jpg"><img src="<?php echo vote_e( $src ); ?>" alt="<?php echo vote_e( t( '%s vote picture, hand-made design %s', vote_lname( $vote_st ), $i + 1 ) ); ?>" width="1080" height="1080" loading="lazy"></a><?php endforeach; ?></div>
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
			<div class="maker__preview" id="preview-wrap">
				<div class="sizes" role="group" aria-label="<?php echo vote_e( t( 'Picture size' ) ); ?>">
					<button type="button" class="size" data-size="square" aria-pressed="true"><?php echo vote_e( t( 'Square' ) ); ?></button>
					<button type="button" class="size" data-size="story" aria-pressed="false"><?php echo vote_e( t( 'Story' ) ); ?></button>
				</div>
				<div class="frame"><img id="preview" alt="<?php echo vote_e( t( 'Your profile picture' ) ); ?>" width="1080" height="1080"></div>
				<p class="summary" id="summary"></p>
				<label class="check" id="circle-check"><input type="checkbox" id="circle"> <?php echo vote_e( t( 'Preview circle crop' ) ); ?></label>
				<div class="actions">
					<a class="btn" id="save" href="#" download aria-disabled="true"><?php echo vote_e( t( 'Download picture' ) ); ?></a>
					<a class="link-quiet" id="open" href="#" target="_blank" rel="noopener"><?php echo vote_e( t( 'Open full image' ) ); ?></a>
				</div>
				<p class="hint" id="tip"></p>
				<details class="post" open>
					<summary><?php echo vote_e( t( 'Suggested post text' ) ); ?></summary>
					<textarea id="post-text" rows="6"<?php if ( $vote_st ) : ?> data-posts="<?php echo vote_e( vote_json( vote_post_all( $vote_st ) ) ); ?>"<?php endif; ?> aria-label="<?php echo vote_e( t( 'Suggested post text' ) ); ?>"><?php echo $vote_st ? vote_e( vote_post( $vote_st ) ) : ''; ?></textarea>
					<button type="button" class="btn btn--quiet" id="copy"><?php echo vote_e( t( 'Copy post text' ) ); ?></button>
				</details>
			</div>
			<section class="maker__designs" aria-labelledby="designs-h">
				<h2 id="designs-h"><?php echo vote_e( t( 'Pick a Design' ) ); ?></h2>
				<p class="help designs-help"><?php echo vote_e( t( 'Every design fits inside a profile-picture circle.' ) ); ?></p>
				<div class="grid" id="grid"></div>
			</section>
		</div>

		<h2 class="display-h before-h" id="before-h"><?php echo vote_e( $vote_st ? t( 'Before You Vote in %s', vote_es() ? vote_lname( $vote_st ) : ( 'DC' === $vote_abbr ? 'DC' : $vote_st['name'] ) ) : t( 'Before You Vote' ) ); ?></h2>
		<section class="info" id="info" aria-live="polite" aria-labelledby="before-h"><?php echo $vote_st ? vote_info( $vote_st ) : ''; ?></section>
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
		$vote_same_h = array( 'mail' => t( 'Other States That Vote by Mail' ), 'none' => t( 'Other States Without Early Voting for Everyone' ) )[ $vote_st['kind'] ]
			?? ( ! empty( $vote_st['start'] ) ? t( 'Other States Where Early Voting Opens %s', vote_long( $vote_st['start'] ) ) : '' );
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
			<li><a href="<?php echo vote_pbase() . $vote_meta[ $abbr ]['slug']; ?>/"><?php echo vote_e( vote_lname( $vote_rows[ $abbr ] ) ); ?></a><span><?php echo vote_e( mb_strtoupper( mb_substr( $vote_meta[ $abbr ]['status'], 0, 1 ) ) . mb_substr( $vote_meta[ $abbr ]['status'], 1 ) ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php endif; ?>
	<?php if ( $vote_near ) : ?>
	<section class="near" aria-labelledby="near-h">
		<h2 id="near-h"><?php echo vote_e( t( 'States Next Door' ) ); ?></h2>
		<ul>
			<?php foreach ( $vote_near as $abbr ) : ?>
			<li><a href="<?php echo vote_pbase() . $vote_meta[ $abbr ]['slug']; ?>/"><?php echo vote_e( vote_lname( $vote_rows[ $abbr ] ) ); ?></a><span><?php echo vote_e( mb_strtoupper( mb_substr( $vote_meta[ $abbr ]['status'], 0, 1 ) ) . mb_substr( $vote_meta[ $abbr ]['status'], 1 ) ); ?></span></li>
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
		<ul>
			<?php foreach ( $vote_rows as $abbr => $row ) : ?>
			<li><?php if ( $abbr === $vote_abbr ) : ?><span aria-current="page"><?php echo vote_e( vote_lname( $row ) ); ?></span><?php else : ?><a href="<?php echo vote_pbase() . $vote_meta[ $abbr ]['slug']; ?>/"><?php echo vote_e( vote_lname( $row ) ); ?></a><?php endif; ?></li>
			<?php endforeach; ?>
		</ul>
	</nav>
	</div>

	<?php if ( ! $vote_404 ) : ?>
	<section class="faq" aria-labelledby="faq-h">
		<h2 id="faq-h"><?php echo vote_e( t( 'Common Questions' ) ); ?></h2>
		<?php if ( $vote_st ) : foreach ( vote_state_faq( $vote_st ) as $vote_q ) : ?>
		<details class="faq__state"><summary><?php echo vote_e( $vote_q[0] ); ?></summary><p><?php echo vote_e( $vote_q[1] ); ?></p><p class="faq__src"><?php echo vote_e( t( 'Source:' ) ); ?> <a href="<?php echo vote_e( $vote_q[2] ); ?>" target="_blank" rel="noopener"><?php echo vote_e( preg_replace( '#^www\.#', '', parse_url( $vote_q[2], PHP_URL_HOST ) ) ); ?></a>, <?php echo vote_e( t( 'checked %s.', vote_es() ? '19 de octubre de 2026' : 'October 19, 2026' ) ); ?></p></details>
		<?php endforeach; else : ?>
		<details><summary><?php echo vote_e( t( 'Can I post a photo of my ballot?' ) ); ?></summary><p><?php echo vote_e( t( 'It depends on the state. Some states allow a picture of your own marked ballot, and some make it illegal to show one. Each state page says what that state allows, where we could confirm it. The stickers made here show no ballot.' ) ); ?></p></details>
		<?php endif; ?>
		<details><summary><?php echo vote_e( t( 'When should I switch to I voted?' ) ); ?></summary><p><?php echo vote_e( t( 'After you vote. Choose I voted in the Main message menu, or I voted early or I voted by mail if that fits.' ) ); ?></p></details>
		<details><summary><?php echo vote_e( t( 'How do I make it my profile picture?' ) ); ?></summary><p><?php echo vote_e( t( 'Tap Download picture, then open your profile settings in the app and choose the saved image as your photo. The design stays inside the circle that profile photos are cropped to.' ) ); ?></p></details>
		<details><summary><?php echo vote_e( t( 'Is this an official election site?' ) ); ?></summary><p><?php echo vote_e( t( 'No. Vote Sticker is an independent, nonpartisan tool made by one person. For registration, polling places, and deadlines, go to your state or local election office. Each state page links to it.' ) ); ?></p></details>
		<details><summary><?php echo vote_e( t( 'Where do the dates come from?' ) ); ?></summary><p><?php echo vote_e( t( "From official state sources: the election office's own pages or state law, checked and dated. Until a state's dates are confirmed, its stickers say Election Day." ) ); ?></p></details>
		<details><summary><?php echo vote_e( t( 'Why do some states say hours or start dates vary?' ) ); ?></summary><p><?php echo vote_e( t( 'In some states, counties or towns set their own early voting days, hours, and locations. Your local election office has the final details.' ) ); ?></p></details>
		<details><summary><?php echo vote_e( t( "What if my state doesn't have early voting?" ) ); ?></summary><p><?php echo vote_e( t( "Your sticker says VOTE NOV 3. If you can't vote in person on Election Day, you may qualify for an absentee ballot. Your state's page links to the rules." ) ); ?></p></details>
		<details><summary><?php echo vote_e( t( 'Do you keep my information?' ) ); ?></summary><p><?php echo vote_e( t( 'No. Your sticker is created in your browser and never uploaded.' ) ); ?></p></details>
	</section>
	<?php endif; ?>
</main>

<footer class="foot">
	<div class="wrap">
		<p><b><?php echo vote_e( t( 'This tool is nonpartisan.' ) ); ?></b> <?php echo vote_e( t( 'Voting dates and links come from official state election sources. Before you vote, check your registration, polling place, hours, and ID rules with your state or local election office.' ) ); ?> <a href="<?php echo vote_e( t( 'https://www.usa.gov/state-election-office' ) ); ?>"><?php echo vote_e( t( 'Find your state or local election office' ) ); ?></a>.</p>
		<p class="foot__made"><span><?php echo vote_e( t( 'VoteSticker.org built by' ) ); ?></span> <a class="foot__gd" href="https://www.garrettdigital.com/"><img src="<?php echo vote_v( 'assets/gd-logo.svg' ); ?>" alt="Garrett Digital" width="152" height="40"></a></p>
	</div>
</footer>

<?php
// Hand-made pictures: every JPG in assets/states/<abbr>/ shows under that state, in natural order.
$vote_gallery = array();
foreach ( glob( __DIR__ . '/assets/states/*', GLOB_ONLYDIR ) as $dir ) { $vote_gallery[ strtoupper( basename( $dir ) ) ] = vote_files( basename( $dir ) ); }
?>
<script>window.VOTE_GALLERY = <?php echo vote_json( $vote_gallery ); ?>;
window.VOTE_META = <?php echo vote_json( $vote_meta ); ?>;
window.VOTE_BASE = <?php echo vote_json( vote_pbase() ); ?>;
window.VOTE_LANG = <?php echo vote_json( $vote_lang ); ?>;
window.VOTE_T = <?php
// Maker strings, from the same dictionary as the page.
$vote_js = array();
foreach ( array( 'Tap Save to Photos, then Save Image. Then set it as your profile picture from Photos.', 'Tap Open image, press and hold the picture, and choose Save to Photos.', 'The picture saves to your Downloads. Set it as your profile picture from there.', 'The picture saves to your Downloads folder.', 'Save to Photos', 'Download picture', 'Copied', 'Copy post text', 'Link copied', 'Copy this: ', 'No state matches. Try the full name.', 'Official info: ', 'Profile picture: ', 'Election Day is %s.', 'Tuesday, November 3, 2026', 'Ready-Made %s Stickers', 'Or Make Your Own', 'Make Your %s Sticker', '%s vote picture, hand-made design %s', 'Sticker', 'Poster', 'Stamp', 'Big type', 'Badge', 'State colors', 'Cream and navy', 'Navy and red', 'Forest and gold', 'Sand and rust', 'Sky and navy', 'Sage and forest', 'Vote early', 'Vote by mail', 'Vote Nov 3', 'I voted', 'I voted by mail', 'I voted early', 'Vota temprano (español)', 'Vota por correo (español)', 'Vota 3 de nov (español)', 'Ya voté (español)', 'Ya voté temprano (español)', 'Ya voté por correo (español)', 'Voy a votar (español)' ) as $k ) { $vote_js[ $k ] = t( $k ); }
echo vote_json( $vote_js ); ?>;
window.VOTE_ASSETS = <?php echo vote_json( VOTE_BASE . 'assets/' ); ?>;
window.VOTE_PAGE_STATE = <?php echo vote_json( $vote_abbr ); ?>;</script>
<script src="<?php echo vote_v( 'states.js' ); ?>"></script>
<script src="<?php echo vote_v( 'state-shapes.js' ); ?>"></script>
<script src="<?php echo vote_v( 'app.js' ); ?>"></script>
</body>
</html>
