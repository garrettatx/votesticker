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
		'title' => t( 'History of the I Voted Sticker: Where It Came From' ),
		// A no-break space keeps "I Voted" on one line, so the H1 never ends a line on "I".
		'h1' => t( "Where the I Voted Sticker Came From" ),
		'eyebrow' => '', // the breadcrumb already says Sticker History
		'lead' => t( 'No one knows who made the first “I Voted” sticker. But over time, a small giveaway at the polls became an Election Day tradition, and a chance for local artists, students and election offices to make their mark.' ),
		'desc' => t( 'The history of the I voted sticker: a 1950 Miami newspaper mention, a 1985 Phoenix campaign, the 1987 flag oval, and today\'s state and county design contests, with a source for each.' ),
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
	$a = $es ? 'YA VOTÉ' : 'I VOTED';
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
		. '<text x="120" y="112" text-anchor="middle" font-family="Vote Display, Impact, sans-serif" font-size="58" fill="#FFE8C1">' . ( $es ? 'YA' : 'I' ) . '</text>'
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

// The timeline: a track drawn to scale, with the three eras shaded (the long quiet stretch and the 1980s
// burst are the story), each mark linking to its entry in the list below. The list is the real content.
function vote_h_timeline( $events ) {
	if ( ! $events ) { return ''; }
	$y0 = 1880; $y1 = 2030; $x0 = 20; $x1 = 940; $mid = 92;
	$x = function ( $y ) use ( $y0, $y1, $x0, $x1 ) { return round( $x0 + ( $y - $y0 ) / ( $y1 - $y0 ) * ( $x1 - $x0 ), 1 ); };
	// The marker's shape says what kind of moment it is; the legend under the track names them.
	$shape = function ( $kind, $cx, $cy, $big ) {
		$r = $big ? 7 : 4.6;
		switch ( $kind ) {
			case 'precursor': return '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . $r . '" class="tl-k tl-k--open"/>';
			case 'uncertain': return '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . $r . '" class="tl-k tl-k--dash"/>';
			case 'digital': return '<rect x="' . ( $cx - $r * 0.8 ) . '" y="' . ( $cy - $r * 0.8 ) . '" width="' . ( $r * 1.6 ) . '" height="' . ( $r * 1.6 ) . '" transform="rotate(45 ' . $cx . ' ' . $cy . ')" class="tl-k"/>';
			default: return '<ellipse cx="' . $cx . '" cy="' . $cy . '" rx="' . ( $r * 1.35 ) . '" ry="' . $r . '" class="tl-k"/>';
		}
	};
	$svg = '<svg class="tl-track" viewBox="0 0 960 172" role="presentation" focusable="false">';
	// Three chapters, shaded behind the axis and named along the bottom.
	foreach ( array( array( 1880, 1981, 'Origins' ), array( 1981, 1989, 'The 1980s' ), array( 2004, 2030, 'Contests and the internet' ) ) as $i => $era ) {
		$svg .= '<rect x="' . $x( $era[0] ) . '" y="58" width="' . ( $x( $era[1] ) - $x( $era[0] ) ) . '" height="68" class="tl-era tl-era--' . $i . '"/>'
			. '<text x="' . round( ( $x( $era[0] ) + $x( $era[1] ) ) / 2 ) . '" y="164" text-anchor="middle" class="tl-era-label">' . vote_e( t( $era[2] ) ) . '</text>';
	}
	$svg .= '<line x1="' . $x0 . '" y1="' . $mid . '" x2="' . $x1 . '" y2="' . $mid . '" class="tl-axis"/>';
	foreach ( array( 1880, 1900, 1920, 1940, 1960, 1980, 2000, 2020 ) as $tick ) {
		$svg .= '<line x1="' . $x( $tick ) . '" y1="' . ( $mid + 34 ) . '" x2="' . $x( $tick ) . '" y2="' . ( $mid + 40 ) . '" class="tl-tick"/><text x="' . $x( $tick ) . '" y="' . ( $mid + 54 ) . '" text-anchor="middle" class="tl-tick-label">' . $tick . '</text>';
	}
	// Milestones (events with a `mark` label) get a big marker and a label on one of two rows above the
	// axis, so neighbors like 1985 and 1987 don't collide; every other event is a small marker.
	$row = 0;
	foreach ( $events as $e ) {
		$cx = $x( $e['year'] );
		$kind = $e['kind'] ?? 'sticker';
		$tip = '<title>' . vote_e( $e['year'] . ': ' . vote_h_tx( $e['title'] ) ) . '</title>';
		if ( empty( $e['mark'] ) ) {
			$svg .= '<a href="#t-' . vote_e( $e['id'] ) . '" class="tl-mark tl-mark--small">' . $tip . $shape( $kind, $cx, $mid, false ) . '</a>';
			continue;
		}
		$ly = $row ? 40 : 18; $row = 1 - $row;
		$anchor = $cx < 90 ? 'start' : ( $cx > 880 ? 'end' : 'middle' );
		$svg .= '<a href="#t-' . vote_e( $e['id'] ) . '" class="tl-mark">' . $tip . '<line x1="' . $cx . '" y1="' . ( $ly + 5 ) . '" x2="' . $cx . '" y2="' . ( $mid - 9 ) . '" class="tl-lead"/>'
			. '<text x="' . $cx . '" y="' . $ly . '" text-anchor="' . $anchor . '" class="tl-mark-label">' . vote_e( $e['year'] . ' ' . vote_h_tx( $e['mark'] ) ) . '</text>'
			. $shape( $kind, $cx, $mid, true ) . '</a>';
	}
	$svg .= '</svg>';
	$key = '<p class="tl-key" aria-hidden="true">'
		. '<span><svg viewBox="0 0 16 16" width="14" height="14"><circle cx="8" cy="8" r="5.5" class="tl-k tl-k--open"/></svg>' . vote_e( t( 'Before stickers' ) ) . '</span>'
		. '<span><svg viewBox="0 0 16 16" width="16" height="14"><ellipse cx="8" cy="8" rx="7" ry="5" class="tl-k"/></svg>' . vote_e( t( 'Sticker milestone' ) ) . '</span>'
		. '<span><svg viewBox="0 0 16 16" width="14" height="14"><rect x="4" y="4" width="8" height="8" transform="rotate(45 8 8)" class="tl-k"/></svg>' . vote_e( t( 'Digital' ) ) . '</span>'
		. '<span><svg viewBox="0 0 16 16" width="14" height="14"><circle cx="8" cy="8" r="5.5" class="tl-k tl-k--dash"/></svg>' . vote_e( t( 'Evidence incomplete' ) ) . '</span></p>';
	$list = '<ol class="tl">';
	foreach ( $events as $e ) {
		$ev = empty( $e['pending'] )
			? '<a class="tl__badge" href="' . vote_e( $e['src'] ) . '" target="_blank" rel="noopener"' . ( vote_es() ? ' lang="en"' : '' ) . '>' . vote_e( ucfirst( preg_replace( '/^the /', '', vote_h_src_name( $e['src'] ) ) ) ) . '</a>' . ( 'secondary' === $e['evidence'] ? ' <span class="tl__kind">' . vote_e( t( 'a later account' ) ) . '</span>' : '' )
			: vote_h_pending( $e );
		$list .= '<li class="tl__item" id="t-' . vote_e( $e['id'] ) . '"><p class="tl__year">' . vote_e( $e['label'] ?? $e['year'] ) . '</p><div class="tl__body">'
			. '<h3 class="tl__title">' . vote_e( vote_h_tx( $e['title'] ) ) . '</h3>'
			. '<p>' . vote_h_md( vote_h_tx( $e['body'] ) ) . '</p>'
			. '<p class="tl__ev">' . $ev . '</p></div></li>';
	}
	return '<div class="tl-wrap"><div class="tl-scroll">' . $svg . '</div>' . $key . '</div>' . $list . '</ol>';
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
		. '<span>' . vote_e( t( 'Who started the I voted sticker? Nobody knows for sure, but the people behind the designs are worth meeting.' ) ) . ' <a href="' . vote_pbase() . VOTE_HISTORY_SLUG . '/">' . vote_e( t( 'How the I voted sticker became a tradition' ) ) . '</a></span></p>';
}

function vote_history() {
	$d = vote_history_data();
	if ( ! $d ) { return '<div class="about"><p>' . vote_e( t( 'This page is being prepared.' ) ) . '</p></div>'; }
	$out = '<div class="about hist">';
	if ( ! empty( $d['draft'] ) ) { $out .= '<p class="h-draft">' . vote_e( t( 'Draft preview. Yellow labels mark text that still needs review, such as a missing translation. Anything we cannot support is revised or removed before publication.' ) ) . '</p>'; }
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
		$out .= vote_h_paras( $sec['after'] ?? array() );
		$out .= vote_h_cite( $srcs );
		if ( ! empty( $sec['cta'] ) ) { $out .= '<p class="jump"><a class="btn btn--gold" href="' . vote_pbase() . '#state-find">' . vote_e( t( 'Make a sticker for your state' ) ) . '</a></p>'; }
		$out .= '</section>';
	}
	$out .= '<section class="guide" aria-labelledby="method-h"><h2 class="display-h guide__h" id="method-h">' . vote_e( t( 'How We Check the Story' ) ) . '</h2>'
		. '<p>' . vote_e( t( '“I Voted” sticker history is full of claims repeated so often that their original source gets lost. This page starts with museum records, election office materials, laws, original research and reporting from the time. When the best evidence is a later retelling, the page says so. If a claim can\'t be verified well enough, it isn\'t here.' ) ) . '</p>'
		. '<p>' . vote_e( t( 'Sources are linked throughout the page. Some entries rest on a museum object or an official record; others rely on later reporting when the original isn\'t available.' ) ) . '</p>'
		. '<p class="cite">' . vote_e( t( 'Last reviewed %s', vote_checked( $d['checked'] ) ) ) . ' · <a href="' . vote_pbase() . 'about/#fix-h">' . vote_e( t( 'Share a source or correction' ) ) . '</a></p></section>';
	return $out . '</div>';
}
