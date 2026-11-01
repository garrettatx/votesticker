<?php
/**
 * The history of the I voted sticker (/history/, /es/history/).
 *
 * Words live in build/history.json (English and Spanish, each paragraph tied to the facts it rests on);
 * the evidence lives in build/history-facts.json (source URL, the sentence that supports the claim, the
 * check date). build/history_text.py checks one against the other and writes site/history-data.php,
 * which this file renders. The timeline, charts and state map are drawn here as plain HTML and SVG, so
 * the page works without JavaScript and every chart has a table.
 */

const VOTE_HISTORY_SLUG = 'history';

function vote_history_data() {
	static $d = null;
	if ( null === $d ) { $f = __DIR__ . '/history-data.php'; $d = is_file( $f ) ? require $f : array(); }
	return $d;
}

// A {en, es} pair in the page language.
function vote_h_tx( $pair ) { return is_array( $pair ) ? ( vote_es() && ! empty( $pair['es'] ) ? $pair['es'] : ( $pair['en'] ?? '' ) ) : (string) $pair; }

// Prose may carry [link text](https://...) links and nothing else; everything is escaped first.
function vote_h_md( $text ) {
	return preg_replace_callback( '/\[([^\]]+)\]\((https:\/\/[^)\s]+)\)/', function ( $m ) {
		return '<a href="' . $m[2] . '">' . $m[1] . '</a>';
	}, vote_e( $text ) );
}

function vote_history_meta() {
	return array(
		'title' => t( 'History of the I Voted Sticker: From 1920 Tags to Today' ),
		// A no-break space keeps "I Voted" on one line, so the H1 never ends a line on "I".
		'h1' => t( "Where the I Voted Sticker Came From" ),
		'eyebrow' => '', // the breadcrumb already says Sticker History
		// Oct 31 (pass 11, press release): the 1920 St. Louis tags lead the description and the lead. Always
		// "the earliest example we found", never the first or the origin (docs/outreach-plan.md).
		'lead' => t( 'No one knows who made the first “I Voted” sticker. The earliest example we found is from 1920: paper “I have voted” tags for a student election at a St. Louis school. Over time, a small giveaway became an Election Day tradition, and a chance for local artists, students and election offices to make their mark.' ),
		'desc' => t( 'I voted sticker history with a source for each step: 1920 “I have voted” tags at a St. Louis school (the earliest example we found), a 1985 Phoenix campaign, the 1987 flag oval and today\'s contests.' ),
		'index' => ! vote_es() || VOTE_ES_INDEX,
	);
}

function vote_history_ld( $page, $url ) {
	$d = vote_history_data();
	return array(
		'@context' => 'https://schema.org', '@type' => 'Article', 'headline' => $page['h1'], 'description' => $page['desc'],
		'url' => $url, 'mainEntityOfPage' => $url, 'inLanguage' => $GLOBALS['vote_lang'],
		'datePublished' => $d['published'] ?? '2026-10-22', 'dateModified' => $d['checked'] ?? '2026-10-22',
		'author' => array( '@type' => 'Organization', 'name' => 'Garrett Digital', 'url' => 'https://www.garrettdigital.com/' ),
		'publisher' => array( '@type' => 'Organization', 'name' => 'Garrett Digital', 'url' => 'https://www.garrettdigital.com/', 'logo' => array( '@type' => 'ImageObject', 'url' => VOTE_SITE . VOTE_BASE . 'assets/gd-logo.svg' ) ),
		'about' => array( '@type' => 'Thing', 'name' => 'I Voted sticker' ),
	);
}

// The hero art: two stickers drawn for this page, an oval in the classic shape and a round one in the
// style of today's contests. Our own drawings, not copies of any printed design.
function vote_history_art() {
	$es = vote_es();
	$a = $es ? 'YO VOTÉ' : 'I VOTED';
	$star = function ( $x, $y, $r, $fill ) {
		$p = array();
		for ( $i = 0; $i < 10; $i++ ) { $rr = $i % 2 ? $r * 0.42 : $r; $an = -M_PI / 2 + $i * M_PI / 5; $p[] = round( $x + $rr * cos( $an ), 1 ) . ',' . round( $y + $rr * sin( $an ), 1 ); }
		return '<polygon points="' . implode( ' ', $p ) . '" fill="' . $fill . '"/>';
	};
	$stars = '';
	foreach ( array( 128, 154, 180, 206, 232 ) as $x ) { $stars .= $star( $x, 78, 9, '#C1272D' ); }
	return '<div class="hist-art" aria-hidden="true">'
		. '<svg class="hist-art__oval" viewBox="0 0 360 240" width="360" height="240"><ellipse cx="180" cy="120" rx="172" ry="112" fill="#FBF6E8" stroke="#14295E" stroke-width="7"/><ellipse cx="180" cy="120" rx="156" ry="97" fill="none" stroke="#C1272D" stroke-width="2.5"/>'
		. $stars
		. '<text x="180" y="158" text-anchor="middle" font-family="Vote Display, Impact, sans-serif" font-size="78" fill="#14295E" letter-spacing="2">' . $a . '</text>'
		. '<rect x="96" y="172" width="168" height="5" rx="2" fill="#C1272D"/></svg>'
		. '<svg class="hist-art__disc" viewBox="0 0 240 240" width="240" height="240"><circle cx="120" cy="120" r="116" fill="#48203F"/><circle cx="120" cy="120" r="102" fill="none" stroke="#EFA978" stroke-width="3"/>'
		. '<text x="120" y="112" text-anchor="middle" font-family="Vote Display, Impact, sans-serif" font-size="58" fill="#FFE8C1">' . ( $es ? 'YO' : 'I' ) . '</text>'
		. '<text x="120" y="166" text-anchor="middle" font-family="Vote Display, Impact, sans-serif" font-size="58" fill="#FFE8C1">' . ( $es ? 'VOTÉ' : 'VOTED' ) . '</text>'
		. $star( 120, 52, 12, '#EFA978' ) . '</svg>'
		. '</div>';
}

// What kind of evidence backs a timeline entry, said in words (never by color alone).
function vote_h_evidence( $kind ) {
	$labels = array(
		'artifact' => 'Museum object',
		'contemporary report' => 'News report at the time',
		'official record' => 'Election office record',
		'statute' => 'Law',
		'study' => 'Published study',
		'creator interview' => 'Interview with the designer',
		'secondary' => 'Later history, citing the original',
	);
	return t( $labels[ $kind ] ?? 'Source' );
}

function vote_h_src_name( $url ) {
	$d = vote_history_data();
	$host = (string) parse_url( $url, PHP_URL_HOST );
	$n = $d['sources'][ $host ] ?? $d['sources'][ preg_replace( '/^www\./', '', $host ) ] ?? null;
	$name = $n ? vote_h_tx( $n ) : preg_replace( '/^www\./', '', $host );
	return vote_es() ? preg_replace( '/^the /', '', $name ) : $name;
}

function vote_h_cite( $urls ) {
	// One link per source name: two pages from the same office read as one source.
	$byname = array();
	foreach ( array_filter( (array) $urls ) as $u ) { $n = vote_h_src_name( $u ); if ( ! isset( $byname[ $n ] ) ) { $byname[ $n ] = $u; } }
	if ( ! $byname ) { return ''; }
	$links = array();
	$lang = vote_es() ? ' lang="en"' : ''; // publication names stay in English
	foreach ( $byname as $n => $u ) { $links[] = '<a href="' . vote_e( $u ) . '" target="_blank" rel="noopener"' . $lang . '>' . vote_e( $n ) . '</a>'; }
	return '<p class="cite">' . vote_e( t( count( $links ) > 1 ? 'Sources:' : 'Source:' ) ) . ' ' . implode( ', ', $links ) . '</p>';
}

// A "Being checked" tag for anything whose fact the check hasn't confirmed (local draft previews only;
// the build refuses a draft, so this never reaches the live site).
function vote_h_pending( $item ) { return ! empty( $item['pending'] ) ? ' <span class="h-pending">' . vote_e( t( 'Being checked' ) ) . '</span>' : ''; }

// The marker for one kind of moment, as a small inline SVG. The shape says the kind; the key names it in
// words, so nothing rests on shape or color alone.
function vote_h_shape( $kind, $size = 16 ) {
	$c = $size / 2; $r = $size * 0.34;
	switch ( $kind ) {
		case 'precursor': $s = '<circle cx="' . $c . '" cy="' . $c . '" r="' . $r . '" class="tl-k tl-k--open"/>'; break;
		case 'uncertain': $s = '<circle cx="' . $c . '" cy="' . $c . '" r="' . $r . '" class="tl-k tl-k--dash"/>'; break;
		case 'digital': $d = round( $r * 1.25, 2 ); $s = '<rect x="' . ( $c - $d / 2 ) . '" y="' . ( $c - $d / 2 ) . '" width="' . $d . '" height="' . $d . '" transform="rotate(45 ' . $c . ' ' . $c . ')" class="tl-k"/>'; break;
		default: $s = '<ellipse cx="' . $c . '" cy="' . $c . '" rx="' . round( $r * 1.3, 2 ) . '" ry="' . round( $r * 0.92, 2 ) . '" class="tl-k"/>';
	}
	return '<svg class="tl-shape" viewBox="0 0 ' . $size . ' ' . $size . '" width="' . $size . '" height="' . $size . '" aria-hidden="true" focusable="false">' . $s . '</svg>';
}

// The three eras the overview groups events into. Event years decide membership; the ranges printed come from
// the events themselves, so a new entry moves them without an edit here.
function vote_h_eras( $events ) {
	$eras = array( array( 1981, 'Origins' ), array( 1990, 'The 1980s' ), array( 9999, 'Contests and the internet' ) );
	$out = array();
	foreach ( $eras as $i => $era ) { $out[ $i ] = array( 'name' => $era[1], 'events' => array() ); }
	foreach ( $events as $e ) { foreach ( $eras as $i => $era ) { if ( $e['year'] < $era[0] ) { $out[ $i ]['events'][] = $e; break; } } }
	return array_values( array_filter( $out, function ( $g ) { return $g['events']; } ) );
}

// Dot positions on an era's track, in percent: placed by year, then pushed apart so two events in the same
// year (or a year apart) never sit on top of each other, then pulled back inside the track.
function vote_h_track_x( $events ) {
	$n = count( $events ); $lo = 5; $hi = 95;
	if ( 1 === $n ) { return array( 50 ); }
	$y0 = $events[0]['year']; $y1 = $events[ $n - 1 ]['year'];
	$gap = min( 9, ( $hi - $lo ) / ( $n - 1 ) * 0.9 );
	$x = array();
	foreach ( $events as $i => $e ) { $x[ $i ] = $y1 > $y0 ? $lo + ( $e['year'] - $y0 ) / ( $y1 - $y0 ) * ( $hi - $lo ) : $lo + $i * $gap; }
	for ( $i = 1; $i < $n; $i++ ) { $x[ $i ] = max( $x[ $i ], $x[ $i - 1 ] + $gap ); }
	$x[ $n - 1 ] = min( $x[ $n - 1 ], $hi );
	for ( $i = $n - 2; $i >= 0; $i-- ) { $x[ $i ] = min( $x[ $i ], $x[ $i + 1 ] - $gap ); }
	return array_map( function ( $v ) { return round( $v, 1 ); }, $x );
}

// The timeline overview: three eras side by side on a wide screen and stacked below that, each with a
// short track of dots (decoration; the shapes repeat in the list) and a list of links, one per event, to its
// entry in the full timeline below. Long quiet stretches between eras are breaks with the gap in words, not
// empty axis. Plain HTML, so labels wrap instead of colliding, in either language, at any width.
function vote_h_timeline( $events ) {
	if ( ! $events ) { return ''; }
	$eras = vote_h_eras( $events );
	$nav = '';
	foreach ( $eras as $i => $g ) {
		$ev = $g['events']; $first = $ev[0]['year']; $last = $ev[ count( $ev ) - 1 ]['year'];
		if ( $i ) {
			$prev = $eras[ $i - 1 ]['events'];
			$nav .= '<li class="tl-gap" aria-hidden="true"><span class="tl-gap__mark"></span><span class="tl-gap__txt">' . vote_e( t( '%s years later', $first - $prev[ count( $prev ) - 1 ]['year'] ) ) . '</span></li>';
		}
		$dots = '';
		foreach ( vote_h_track_x( $ev ) as $j => $x ) {
			$dots .= '<span class="tl-dot' . ( ! empty( $ev[ $j ]['mark'] ) ? ' tl-dot--key' : '' ) . '" style="left:' . $x . '%">' . vote_h_shape( $ev[ $j ]['kind'] ?? 'sticker', 14 ) . '</span>';
		}
		$items = '';
		foreach ( $ev as $e ) {
			$kind = $e['kind'] ?? 'sticker';
			$items .= '<li><a class="tl-i' . ( ! empty( $e['mark'] ) ? ' tl-i--key' : '' ) . '" href="#t-' . vote_e( $e['id'] ) . '">' . vote_h_shape( $kind )
				. '<span class="tl-i__y">' . vote_e( $e['year'] ) . '</span> '
				// `mark` is a turning point (bold, bigger dot); `chip` only names the entry on phones, where titles are hidden.
				. '<span class="tl-i__txt">' . ( ! empty( $e['mark'] ) || ! empty( $e['chip'] ) ? '<span class="tl-i__m">' . vote_e( vote_h_tx( $e['mark'] ?? $e['chip'] ) ) . '</span>' : '' )
				. '<span class="tl-i__t">' . vote_e( vote_h_tx( $e['title'] ) ) . '</span>'
				. ( 'uncertain' === $kind ? ' <span class="tl-i__q">' . vote_e( t( 'Evidence incomplete' ) ) . '</span>' : '' )
				. '</span></a></li>';
		}
		$nav .= '<li class="tl-era tl-era--' . $i . '"><p class="tl-era__h"><span class="tl-era__name">' . vote_e( t( $g['name'] ) ) . '</span> <span class="tl-era__span">' . $first . '–' . $last . ' · ' . vote_e( t( count( $ev ) > 1 ? '%s entries' : '%s entry', count( $ev ) ) ) . '</span></p>'
			. '<div class="tl-era__track" aria-hidden="true">' . $dots . '</div><ul class="tl-era__list">' . $items . '</ul></li>';
	}
	$key = '<p class="tl-key"><span>' . vote_h_shape( 'precursor', 14 ) . vote_e( t( 'Before stickers' ) ) . '</span>'
		. '<span>' . vote_h_shape( 'sticker', 14 ) . vote_e( t( 'Sticker' ) ) . '</span>'
		. '<span>' . vote_h_shape( 'digital', 14 ) . vote_e( t( 'Digital' ) ) . '</span>'
		. '<span>' . vote_h_shape( 'uncertain', 14 ) . vote_e( t( 'Evidence incomplete' ) ) . '</span>'
		. '<span class="tl-key__note">' . vote_e( t( 'Bold: turning points. Dots sit roughly by year within each era.' ) ) . '</span></p>';
	$out = '<figure class="tl-wrap"><nav aria-label="' . vote_e( t( 'Timeline overview' ) ) . '"><ol class="tl-eras">' . $nav . '</ol></nav><figcaption>' . $key . '</figcaption></figure>';
	// The full timeline: every entry with its story and source, under the same three era names.
	$list = '<ol class="tl">';
	foreach ( $eras as $i => $g ) {
		$ev = $g['events'];
		$list .= '<li class="tl__era" id="tl-era-' . $i . '"><p>' . vote_e( t( $g['name'] ) ) . ' <span>' . $ev[0]['year'] . '–' . $ev[ count( $ev ) - 1 ]['year'] . '</span></p></li>';
		foreach ( $ev as $e ) {
			$evd = empty( $e['pending'] )
				? '<a class="tl__badge" href="' . vote_e( $e['src'] ) . '" target="_blank" rel="noopener"' . ( vote_es() ? ' lang="en"' : '' ) . '>' . vote_e( ucfirst( preg_replace( '/^the /', '', vote_h_src_name( $e['src'] ) ) ) ) . '</a>' . ( 'secondary' === $e['evidence'] ? ' <span class="tl__kind">' . vote_e( t( 'a later account' ) ) . '</span>' : '' )
				: vote_h_pending( $e );
			$list .= '<li class="tl__item tl__item--' . vote_e( $e['kind'] ?? 'sticker' ) . '" id="t-' . vote_e( $e['id'] ) . '"><p class="tl__year">' . vote_e( $e['label'] ?? $e['year'] ) . '</p><div class="tl__body">'
				. '<h3 class="tl__title">' . vote_e( vote_h_tx( $e['title'] ) ) . '</h3>'
				. '<p>' . vote_h_md( vote_h_tx( $e['body'] ) ) . '</p>'
				. '<p class="tl__ev">' . $evd . '</p></div></li>';
		}
	}
	return $out . $list . '</ol>';
}

function vote_h_num( $v ) { return number_format( $v ); }
function vote_h_val( $r ) { return ( ! empty( $r['approx'] ) ? '~' : '' ) . vote_h_num( $r['value'] ) . ( ! empty( $r['plus'] ) ? '+' : '' ); }
function vote_h_val_words( $r ) { return ( ! empty( $r['approx'] ) ? t( 'about' ) . ' ' : ( ! empty( $r['plus'] ) ? t( 'more than' ) . ' ' : '' ) ) . vote_h_num( $r['value'] ); }

// The same numbers as a table, visually hidden: the chart above is drawn for eyes, this is for readers.
function vote_h_table( $chart ) {
	$out = '<table class="bars__table"><caption>' . vote_e( vote_h_tx( $chart['title'] ) ) . '</caption>';
	foreach ( $chart['rows'] as $r ) { $out .= '<tr><th scope="row">' . vote_e( vote_h_tx( $r['label'] ) ) . '</th><td>' . vote_e( vote_h_val_words( $r ) ) . '</td></tr>'; }
	return $out . '</table>';
}

// A chart, drawn as HTML so it scales with the text. Bars: one row per measure, length to scale, the
// number always printed. Units: one square per `unit` (10,000 votes), so a 60,000-to-280,000 split
// reads as 6 squares against 28 without an axis.
function vote_h_chart( $chart ) {
	$out = '<figure class="bars"><figcaption><span class="bars__title">' . vote_e( vote_h_tx( $chart['title'] ) ) . '</span>' . vote_h_pending( $chart )
		. ( ! empty( $chart['sub'] ) ? '<span class="bars__sub">' . vote_e( vote_h_tx( $chart['sub'] ) ) . '</span>' : '' ) . '</figcaption>';
	if ( 'split' === ( $chart['type'] ?? '' ) ) {
		// One bar for the whole, split into its parts, each labeled with its number and share.
		$total = array_sum( array_map( function ( $r ) { return $r['value']; }, $chart['rows'] ) );
		$out .= '<div class="split" aria-hidden="true">';
		foreach ( $chart['rows'] as $r ) {
			$pct = round( $r['value'] / $total * 100 );
			$out .= '<div class="split__part' . ( ! empty( $r['key'] ) ? ' split__part--key' : '' ) . '" style="flex:' . $r['value'] . '"><span class="split__label">' . vote_e( vote_h_tx( $r['label'] ) ) . '</span><span class="split__bar">' . $pct . '%</span><span class="split__val">' . vote_e( vote_h_val( $r ) ) . '</span></div>';
		}
		$out .= '</div>';
	} elseif ( 'units' === ( $chart['type'] ?? '' ) ) {
		$out .= '<div class="units" aria-hidden="true">';
		foreach ( $chart['rows'] as $r ) {
			$n = (int) round( $r['value'] / $chart['unit'] );
			$out .= '<div class="units__row' . ( ! empty( $r['key'] ) ? ' units__row--key' : '' ) . '"><p class="units__label"><b>' . vote_e( vote_h_val( $r ) ) . '</b> ' . vote_e( vote_h_tx( $r['label'] ) ) . '</p><div class="units__grid">' . str_repeat( '<span></span>', $n ) . '</div></div>';
		}
		$out .= '<p class="units__key"><span class="units__one"></span> = ' . vote_e( t( '%s voters', vote_h_num( $chart['unit'] ) ) ) . '</p></div>';
	} else {
		$max = max( array_map( function ( $r ) { return $r['value']; }, $chart['rows'] ) );
		$out .= '<div class="bars__rows" aria-hidden="true">';
		foreach ( $chart['rows'] as $r ) {
			$out .= '<div class="bars__row' . ( ! empty( $r['key'] ) ? ' bars__row--key' : '' ) . '"><span class="bars__label">' . vote_e( vote_h_tx( $r['label'] ) ) . '</span><span class="bars__track"><span class="bars__bar" style="width:' . max( 2, round( $r['value'] / $max * 100, 1 ) ) . '%"></span></span><span class="bars__val">' . vote_e( vote_h_val( $r ) ) . '</span></div>';
		}
		$out .= '</div>';
	}
	$out .= vote_h_table( $chart );
	if ( ! empty( $chart['note'] ) ) { $out .= '<p class="bars__note">' . vote_h_md( vote_h_tx( $chart['note'] ) ) . '</p>'; }
	return $out . vote_h_cite( $chart['src'] ?? array() ) . '</figure>';
}

// Collection cards: one object or design each, with its date, place and maker on the label and the
// supporting sentence behind "See the evidence".
function vote_h_cards( $cards ) {
	$out = '<div class="hcards">';
	foreach ( $cards as $c ) {
		$out .= '<article class="hcard" id="' . vote_e( $c['id'] ) . '"><p class="hcard__label"><span>' . vote_e( vote_h_tx( $c['place'] ) ) . ' · ' . vote_e( $c['year'] ) . '</span></p>'
			. '<p class="hcard__maker">' . vote_e( vote_h_tx( $c['maker'] ) ) . '</p><h3 class="hcard__title">' . vote_e( vote_h_tx( $c['title'] ) ) . '</h3>'
			. '<p>' . vote_h_md( vote_h_tx( $c['body'] ) ) . '</p>';
		$out .= empty( $c['pending'] )
			? '<details class="hcard__ev"><summary>' . vote_e( t( 'See the evidence' ) ) . '</summary><blockquote lang="en">' . vote_e( $c['quote'] ) . '</blockquote>' . ( vote_es() ? '<p class="cite">' . vote_e( t( 'Quote from the source, in English.' ) ) . '</p>' : '' ) . '<p class="cite"><a href="' . vote_e( $c['src'] ) . '" target="_blank" rel="noopener"' . ( vote_es() ? ' lang="en"' : '' ) . '>' . vote_e( vote_h_src_name( $c['src'] ) ) . '</a></p></details>'
			: '<p class="hcard__ev">' . vote_h_pending( $c ) . '</p>';
		$out .= '</article>';
	}
	return $out . '</div>';
}

// From shirt to profile picture: three small scenes, the last one a real Vote Sticker in a profile circle.
function vote_h_scenes( $scenes ) {
	$art = array(
		'shirt' => '<svg viewBox="0 0 160 140" aria-hidden="true"><path d="M50 14 L20 30 L6 62 L30 72 L36 58 L36 132 L124 132 L124 58 L130 72 L154 62 L140 30 L110 14 Q80 34 50 14Z" fill="#DCE3F2" stroke="#14295E" stroke-width="3" stroke-linejoin="round"/><ellipse cx="104" cy="64" rx="17" ry="11" fill="#FBF6E8" stroke="#C1272D" stroke-width="2.5"/><rect x="94" y="62" width="20" height="4" rx="2" fill="#14295E"/></svg>',
		'local' => '<svg viewBox="0 0 160 140" aria-hidden="true"><circle cx="80" cy="70" r="58" fill="#0F5E66"/><circle cx="80" cy="70" r="50" fill="none" stroke="#E8B13A" stroke-width="3"/><path d="M40 92 L64 56 L78 74 L92 50 L120 92Z" fill="#FFF0D2"/><circle cx="106" cy="44" r="8" fill="#E8B13A"/><rect x="48" y="96" width="64" height="9" rx="4" fill="#E8B13A"/></svg>',
		'profile' => '<span class="hscene__pfp"><img src="' . vote_v( 'assets/states/tx/tx-vote-2.jpg' ) . '" alt="" width="1080" height="1080" loading="lazy"></span><span class="hscene__lines" aria-hidden="true"><i></i><i></i></span>',
	);
	$out = '<ol class="hscenes">';
	foreach ( $scenes as $i => $sc ) {
		$out .= '<li class="hscene"><div class="hscene__art">' . ( $art[ $sc['art'] ] ?? '' ) . '</div><p class="hscene__kicker">' . vote_e( vote_h_tx( $sc['kicker'] ?? '' ) ) . '</p><h3 class="hscene__title"><span class="hscene__n">' . ( $i + 1 ) . '</span> ' . vote_e( vote_h_tx( $sc['title'] ) ) . '</h3><p>' . vote_h_md( vote_h_tx( $sc['body'] ) ) . '</p></li>';
	}
	return $out . '</ol>';
}

// The state map: every state as a tile, filled where its Vote Sticker guide has a documented sticker
// tradition (the `sticker.tradition` fact). Each filled tile links to that state's story.
function vote_h_map() {
	// Columns and rows in tiles; the row under Colorado shifts half a tile, a brick pattern, so Colorado,
	// Kansas and Missouri no longer stack (October 22 review), and Michigan sits over Indiana and Ohio.
	$grid = array(
		'AK' => array( 0, 0 ), 'ME' => array( 11, 0 ),
		'VT' => array( 10, 1 ), 'NH' => array( 11, 1 ),
		'WA' => array( 1, 2 ), 'ID' => array( 2, 2 ), 'MT' => array( 3, 2 ), 'ND' => array( 4, 2 ), 'MN' => array( 5, 2 ), 'WI' => array( 6, 2 ), 'MI' => array( 7.5, 2 ), 'NY' => array( 9, 2 ), 'MA' => array( 10, 2 ), 'CT' => array( 11, 2 ),
		'OR' => array( 1, 3 ), 'NV' => array( 2, 3 ), 'WY' => array( 3, 3 ), 'SD' => array( 4, 3 ), 'IA' => array( 5, 3 ), 'IL' => array( 6, 3 ), 'IN' => array( 7, 3 ), 'OH' => array( 8, 3 ), 'PA' => array( 9, 3 ), 'NJ' => array( 10, 3 ), 'RI' => array( 11, 3 ),
		'CA' => array( 1, 4 ), 'UT' => array( 2, 4 ), 'CO' => array( 3, 4 ), 'NE' => array( 4, 4 ), 'MO' => array( 5, 4 ), 'KY' => array( 6, 4 ), 'WV' => array( 7, 4 ), 'VA' => array( 8, 4 ), 'MD' => array( 9, 4 ), 'DE' => array( 10, 4 ),
		'AZ' => array( 2.5, 5 ), 'NM' => array( 3.5, 5 ), 'KS' => array( 4.5, 5 ), 'AR' => array( 5.5, 5 ), 'TN' => array( 6.5, 5 ), 'NC' => array( 7.5, 5 ), 'SC' => array( 8.5, 5 ), 'DC' => array( 9.5, 5 ),
		'OK' => array( 4, 6 ), 'LA' => array( 5, 6 ), 'MS' => array( 6, 6 ), 'AL' => array( 7, 6 ), 'GA' => array( 8, 6 ),
		'HI' => array( 0, 7 ), 'TX' => array( 3.5, 7 ), 'FL' => array( 8.5, 7 ),
	);
	$rows = $GLOBALS['vote_rows']; $meta = $GLOBALS['vote_meta'];
	$has = array(); $tiles = ''; $list = '';
	foreach ( $grid as $abbr => $xy ) {
		$f = vote_fact( $rows[ $abbr ], 'sticker.tradition' );
		$name = vote_name( $rows[ $abbr ] );
		$style = ' style="grid-column:' . ( $xy[0] * 2 + 1 ) . ' / span 2;grid-row:' . ( $xy[1] * 2 + 1 ) . ' / span 2"';
		if ( $f ) {
			$has[ $abbr ] = $f;
			$tz = vote_history_data()['teasers'][ $abbr ] ?? null;
			$tiles .= '<a class="smap__tile smap__tile--on" href="' . vote_pbase() . $meta[ $abbr ]['slug'] . '/#sticker-h"' . $style
				. ' aria-label="' . vote_e( t( '%s: sticker story', $name ) ) . '" data-label="' . vote_e( $tz ? vote_h_tx( $tz['label'] ) : $name ) . '" data-teaser="' . vote_e( $tz ? vote_h_tx( $tz ) : '' ) . '">' . $abbr . '</a>';
		} else {
			$tiles .= '<span class="smap__tile"' . $style . ' aria-hidden="true" title="' . vote_e( t( '%s: no featured story yet', $name ) ) . '">' . $abbr . '</span>';
		}
	}
	ksort( $has );
	// Each entry leads with the story, under the place it belongs to (a county story says the county).
	$teasers = vote_history_data()['teasers'] ?? array();
	foreach ( $has as $abbr => $f ) {
		$tz = $teasers[ $abbr ] ?? null;
		$label = $tz ? vote_h_tx( $tz['label'] ) : vote_name( $rows[ $abbr ] );
		$text = $tz ? vote_h_tx( $tz ) : preg_split( '/(?<=[.!?])\s+/', $f['text'], 2 )[0];
		$list .= '<li><a href="' . vote_pbase() . $meta[ $abbr ]['slug'] . '/#sticker-h">' . vote_e( $label ) . '</a> ' . vote_e( $text ) . '</li>';
	}
	// Clicking a highlighted state previews its story under the map; the preview links on to the state
	// page. Without JavaScript the tiles are plain links to the same place.
	return '<figure class="smap"><figcaption><span class="bars__title">' . vote_e( t( 'Explore featured sticker stories by state' ) ) . '</span><span class="bars__sub">' . vote_e( t( 'Select a highlighted state to preview its story.' ) ) . '</span></figcaption>'
		. '<p class="smap__key"><span><span class="smap__swatch smap__swatch--on"></span> ' . vote_e( t( 'Featured story' ) ) . '</span><span><span class="smap__swatch"></span> ' . vote_e( t( 'No featured story yet' ) ) . '</span><span><span class="smap__swatch smap__swatch--sel"></span> ' . vote_e( t( 'Selected' ) ) . '</span></p>'
		. '<div class="smap__grid" role="group" aria-label="' . vote_e( t( 'States with a featured sticker story' ) ) . '">' . $tiles . '</div>'
		. '<div class="smap__preview" id="smap-preview" aria-live="polite" hidden><p class="smap__pname" id="smap-name"></p><p id="smap-teaser"></p><a id="smap-go" href="#">' . vote_e( t( 'Read the story' ) ) . '</a></div>'
		. '<script>(function(){var g=document.querySelector(".smap__grid"),b=document.getElementById("smap-preview");if(!g||!b){return;}g.addEventListener("click",function(e){var a=e.target.closest(".smap__tile--on");if(!a){return;}e.preventDefault();g.querySelectorAll("[aria-current]").forEach(function(x){x.removeAttribute("aria-current");});a.setAttribute("aria-current","true");document.getElementById("smap-name").textContent=a.dataset.label;document.getElementById("smap-teaser").textContent=a.dataset.teaser;var go=document.getElementById("smap-go");go.href=a.href;go.textContent=' . vote_json( t( 'Read the story' ) ) . '+" \u2192";b.hidden=false;});})();</script>'
		. '<details class="smap__list"><summary>' . vote_e( t( 'Browse all %s state stories', count( $has ) ) ) . '</summary><ul>' . $list . '</ul></details></figure>';
}

function vote_h_paras( $ps ) {
	$out = '';
	foreach ( $ps as $p ) { $out .= ( ! empty( $p['h3'] ) ? '<h3 class="hist-h3">' . vote_e( vote_h_tx( $p['h3'] ) ) . '</h3>' : '' ) . '<p>' . vote_h_md( vote_h_tx( $p ) ) . vote_h_pending( $p ) . '</p>'; }
	return $out;
}

// State pages: one link after the state's own sticker story, worded for what that story is about and
// pointing at the matching section of the history page.
function vote_history_more( $st ) {
	$f = vote_facts( $st['abbr'] )['sticker.tradition'] ?? null;
	$en = is_array( $f ) ? ( $f['en'] ?? '' ) : '';
	if ( ! $en ) { return ''; }
	$base = vote_pbase() . VOTE_HISTORY_SLUG . '/';
	if ( preg_match( '/contest|students|design the/i', $en ) ) { $link = array( 'contests-h', 'How sticker design contests spread across the country' ); }
	elseif ( preg_match( '/download|digital|virtual|print at home/i', $en ) ) { $link = array( 'profile-h', 'From polling-place stickers to profile pictures' ); }
	elseif ( preg_match( '/artist|painted|designed by/i', $en ) ) { $link = array( 'makers-h', 'Meet the people behind other famous stickers' ); }
	else { $link = array( 'answer-h', 'Where the I voted sticker came from' ); }
	return '<p class="guide__more"><a href="' . $base . '#' . $link[0] . '">' . vote_e( t( $link[1] ) ) . '</a></p>';
}

// The hub's "Why This Exists": a small sticker-on-a-shirt next to a profile picture, and one text link.
// The history page's hero buttons: the timeline for readers, a quiet way into the maker for everyone else.
function vote_history_jump() {
	return '<p class="jump"><a class="btn btn--gold" href="#timeline-h">' . vote_e( t( 'Explore the history' ) ) . ' <span aria-hidden="true">↓</span></a> <a class="btn btn--line" href="' . vote_pbase() . '#state-find">' . vote_e( t( 'Make your sticker' ) ) . '</a></p>';
}

function vote_history_why() {
	return '<p class="why__hist"><span class="why__pic" aria-hidden="true"><svg viewBox="0 0 160 140" width="56" height="49"><path d="M50 14 L20 30 L6 62 L30 72 L36 58 L36 132 L124 132 L124 58 L130 72 L154 62 L140 30 L110 14 Q80 34 50 14Z" fill="#DCE3F2" stroke="#14295E" stroke-width="5" stroke-linejoin="round"/><ellipse cx="104" cy="64" rx="19" ry="12" fill="#FBF6E8" stroke="#C1272D" stroke-width="4"/></svg><span class="why__arrow">→</span><img src="' . vote_v( 'assets/states/tx/tx-vote-2.jpg' ) . '" alt="" width="48" height="48" loading="lazy"></span>'
		. '<span>' . vote_e( t( 'Who started the I voted sticker? Nobody knows for sure. The waving American flag oval many people picture was drawn in 1987 by Janet Boudreau for Intab, an election supply company. The Smithsonian\'s National Museum of American History keeps a 1920 ribbon that marked a woman\'s first vote.' ) ) . ' <a href="' . vote_pbase() . VOTE_HISTORY_SLUG . '/">' . vote_e( t( 'How the I voted sticker became a tradition' ) ) . '</a></span></p>';
}

// The finding a reader from the press release came for (Oct 31, pass 11): the 1920 Blewett tags, what they
// are and aren't, and the report they come from. Words and facts in build/history.json (`callout`).
function vote_h_callout( $co ) {
	if ( ! $co || ! empty( $co['pending'] ) ) { return ''; }
	$lang = vote_es() ? ' lang="en"' : '';
	return '<aside class="hist-find" aria-labelledby="find-h"><p class="hist-find__year" aria-hidden="true"><span class="hist-find__num">1920</span><span class="hist-find__place">' . vote_e( vote_h_tx( $co['place'] ) ) . '</span></p>'
		. '<div class="hist-find__txt"><h2 class="hist-find__h" id="find-h">' . vote_e( vote_h_tx( $co['h'] ) ) . '</h2>'
		. '<p>' . vote_h_md( vote_h_tx( $co['body'] ) ) . '</p>'
		. '<p class="hist-find__limits">' . vote_h_md( vote_h_tx( $co['limits'] ) ) . '</p>'
		. '<p class="hist-find__src">' . vote_e( t( 'Source:' ) ) . ' <a href="' . vote_e( $co['src'] ) . '" target="_blank" rel="noopener"' . $lang . '>' . vote_e( vote_h_tx( $co['source'] ) ) . '</a> · <a href="#t-' . vote_e( $co['event'] ) . '">' . vote_e( t( 'See it in the timeline' ) ) . '</a></p>'
		. '</div></aside>';
}

function vote_history() {
	$d = vote_history_data();
	if ( ! $d ) { return '<div class="about"><p>' . vote_e( t( 'This page is being prepared.' ) ) . '</p></div>'; }
	$out = '<div class="about hist">';
	if ( ! empty( $d['draft'] ) ) { $out .= '<p class="h-draft">' . vote_e( t( 'Draft preview. Yellow labels mark text that still needs review, such as a missing translation. Anything we cannot support is revised or removed before publication.' ) ) . '</p>'; }
	$out .= vote_h_callout( $d['callout'] ?? null );
	foreach ( $d['sections'] as $i => $sec ) {
		$hid = $sec['id'] . '-h';
		$out .= '<section class="guide' . ( $i ? '' : ' guide--first' ) . '" aria-labelledby="' . $hid . '"><h2 class="display-h guide__h" id="' . $hid . '">' . vote_e( vote_h_tx( $sec['h'] ) ) . '</h2>';
		$srcs = array();
		foreach ( array( 'paras', 'after' ) as $g ) { foreach ( $sec[ $g ] ?? array() as $p ) { $srcs = array_merge( $srcs, $p['src'] ?? array() ); } }
		$out .= vote_h_paras( $sec['paras'] ?? array() );
		if ( ! empty( $sec['answer'] ) ) {
			$out .= '<table class="dates hist-answer"><tbody>';
			foreach ( $sec['answer'] as $row ) { $srcs = array_merge( $srcs, $row['src'] ?? array() ); $out .= '<tr><th scope="row">' . vote_e( vote_h_tx( $row['q'] ) ) . '</th><td>' . vote_h_md( vote_h_tx( $row['a'] ) ) . vote_h_pending( $row ) . '</td></tr>'; }
			$out .= '</tbody></table>';
		}
		if ( ! empty( $sec['timeline'] ) ) { $out .= vote_h_timeline( $d['events'] ); }
		if ( ! empty( $sec['cards'] ) ) { $out .= vote_h_cards( $sec['cards'] ); }
		if ( ! empty( $sec['scenes'] ) ) { $out .= vote_h_scenes( $sec['scenes'] ); }
		foreach ( $sec['charts'] ?? array() as $c ) { $out .= vote_h_chart( $d['charts'][ $c ] ); }
		if ( ! empty( $sec['map'] ) ) { $out .= vote_h_map(); }
		// The answer section's follow-up ("What We Still Don't Know") is a set-apart note, so the open questions
		// read as part of the evidence rather than more narrative.
		$after = vote_h_paras( $sec['after'] ?? array() );
		$out .= ( 'answer' === $sec['id'] && $after ) ? '<div class="hist-open">' . $after . '</div>' : $after;
		$out .= vote_h_cite( $srcs );
		if ( ! empty( $sec['cta'] ) ) { $out .= '<p class="jump"><a class="btn btn--gold" href="' . vote_pbase() . '#state-find">' . vote_e( t( 'Make a sticker for your state' ) ) . '</a></p>'; }
		$out .= '</section>';
	}
	$out .= '<section class="guide" aria-labelledby="method-h"><h2 class="display-h guide__h" id="method-h">' . vote_e( t( 'How We Check the Story' ) ) . '</h2>'
		. '<p>' . vote_e( t( '“I Voted” sticker history is full of claims repeated so often that their original source gets lost. This page starts with museum records, election office materials, laws, original research and reporting from the time. When the best evidence is a later retelling, the page says so. If a claim can\'t be verified well enough, it isn\'t here.' ) ) . '</p>'
		. '<p>' . vote_e( t( 'Sources are linked throughout the page.' ) ) . '</p>'
		. '<p class="cite">' . vote_e( t( 'Last reviewed %s', vote_checked( $d['checked'] ) ) ) . ' · <a href="' . vote_pbase() . 'about/#fix-h">' . vote_e( t( 'Share a source or correction' ) ) . '</a></p></section>';
	return $out . '</div>';
}
