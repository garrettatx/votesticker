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
function vote_long( $ymd ) { return date( 'F j', strtotime( $ymd ) ); }
function vote_short( $ymd ) { return date( 'M j', strtotime( $ymd ) ); }
function vote_day( $ymd ) { return date( 'l, F j', strtotime( $ymd ) ); }
// Gallery columns on wide screens: one row up to 6, then rows as even as possible (7 and 8 make 4s).
function vote_cols( $n ) { return $n <= 6 ? max( $n, 1 ) : ( $n <= 8 ? 4 : 5 ); }
function vote_verified( $st ) { return 'verified' === ( $st['status'] ?? '' ); }
function vote_files( $abbr ) {
	$files = array_map( 'basename', glob( __DIR__ . '/assets/states/' . strtolower( $abbr ) . '/*.jpg' ) ?: array() );
	natsort( $files );
	return array_map( function ( $f ) use ( $abbr ) { return vote_v( 'assets/states/' . strtolower( $abbr ) . '/' . $f ); }, array_values( $files ) );
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
	$name = 'DC' === $st['abbr'] ? 'Washington, DC' : $st['name'];
	$kind = vote_verified( $st ) ? ( $st['kind'] ?? 'none' ) : 'unchecked';
	$n = count( vote_files( $st['abbr'] ) );
	$save = $n ? 'Save one of ' . $n . ' ready-made stickers or make your own' : 'Make your own sticker';
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
		$range = vote_short( $st['start'] ) . '–' . ( $same ? date( 'j', strtotime( $st['end'] ) ) : vote_short( $st['end'] ) );
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
		'title' => $name . ' Vote Stickers: ' . $tag,
		'h1' => $name . ' Vote Stickers',
		'crumb' => $name,
		'ribbon' => $ribbon,
		'lead' => $lead,
		'desc' => $fact . ' ' . $save . ', sized for the profile-picture circle. Free and nonpartisan.',
		'status' => $status,
		'family' => $family,
		'index' => vote_verified( $st ),
	);
}

// The state panel, server-rendered; app.js infoHtml() draws the same markup when the menu changes.
function vote_info( $st ) {
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

$vote_rows = vote_states();
$vote_meta = array();
$vote_by_slug = array();
foreach ( $vote_rows as $abbr => $row ) {
	$vote_meta[ $abbr ] = vote_meta( $row );
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
$vote_404 = defined( 'VOTE_404' ) || ( defined( 'VOTE_SLUG' ) && ! $vote_abbr ) || ( ! defined( 'VOTE_SLUG' ) && ! in_array( $vote_path, array( VOTE_BASE, VOTE_BASE . 'index.php' ), true ) );
if ( $vote_404 ) {
	http_response_code( 404 );
	$vote_abbr = '';
}
$vote_st = $vote_abbr ? $vote_rows[ $vote_abbr ] : null;
$vote_page = $vote_st ? $vote_meta[ $vote_abbr ] : array(
	'title' => 'Voting Stickers by State | Vote Sticker',
	'h1' => 'Make your plan. Share the sticker.',
	'eyebrow' => 'Voting stickers for all 50 states and DC',
	'lead' => "Pick your state, choose a design, and save a profile-picture sticker with your voting dates. Each one links to your official election office.",
	'desc' => "Create a free voting sticker for your profile picture. Choose your state and a design, save it, and check official links for early voting, vote by mail, or Election Day.",
	'index' => true,
);
if ( $vote_404 ) {
	$vote_page = array(
		'title' => 'Page Not Found | Vote Sticker',
		'h1' => 'We can’t find that page.',
		'eyebrow' => 'Page not found',
		'lead' => 'The link may be old or have a typo. Every state has its own page of voting stickers. Find yours below.',
		'desc' => 'This page isn’t on Vote Sticker. Find your state for voting stickers and official election links.',
		'index' => false,
	);
}
$vote_url = VOTE_SITE . VOTE_BASE . ( $vote_st ? $vote_page['slug'] . '/' : '' );
// Share image: one per state from build/og_states.mjs, the hub's from build/og_image.mjs.
$vote_og = $vote_st && is_file( __DIR__ . '/assets/og/' . $vote_page['slug'] . '.jpg' ) ? 'assets/og/' . $vote_page['slug'] . '.jpg' : 'assets/og-hub.jpg';
$vote_og_alt = $vote_st ? $vote_page['h1'] . ': ' . $vote_page['status'] . '. A ' . $vote_page['crumb'] . ' voting sticker on cream paper.' : 'Vote Sticker: make your plan, share the sticker. Voting stickers for all 50 states and DC, with ready-made designs from Texas, Washington, Pennsylvania, and California.';

// Breadcrumbs start at the tool's hub, never the WordPress site (Garrett: no links back to the root).
$vote_crumbs = array( array( VOTE_BRAND, VOTE_SITE . VOTE_BASE ) );
if ( $vote_st ) { $vote_crumbs[] = array( $vote_page['crumb'], $vote_url ); }
$vote_share = '<p class="share"><button type="button" class="share__btn" id="share-link" data-url="' . vote_e( $vote_url ) . '" data-title="' . vote_e( $vote_page['title'] ) . '"><svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.5 1.5"/><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7L12 19"/></svg><span id="share-label">' . ( $vote_st ? 'Copy link to this ' . vote_e( $vote_rows[ $vote_abbr ]['name'] ) . ' page' : 'Copy page link' ) . '</span></button></p>';

// The state finder: a type-ahead box (app.js fills the suggestions). Without JavaScript the full list of
// state links below the maker does the same job.
function vote_finder( $label, $placeholder ) {
	return '<div class="finder"><label for="state-find">' . vote_e( $label ) . '</label>'
		. '<div class="finder__box"><input id="state-find" type="text" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="state-options" autocomplete="off" autocapitalize="words" spellcheck="false" placeholder="' . vote_e( $placeholder ) . '">'
		. '<button type="button" class="btn btn--gold" id="state-go">Find</button>'
		. '<ul id="state-options" role="listbox" aria-label="Matching states" hidden></ul></div>'
		. '<p class="help" id="state-help"><a href="#states-h">Or browse the full list of states</a></p></div>';
}

$vote_ld = array(
	'@context' => 'https://schema.org',
	'@type' => 'BreadcrumbList',
	'itemListElement' => array_map( function ( $c, $i ) { return array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1] ); }, $vote_crumbs, array_keys( $vote_crumbs ) ),
);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo vote_e( $vote_page['title'] ); ?></title>
<meta name="description" content="<?php echo vote_e( $vote_page['desc'] ); ?>">
<?php if ( ! $vote_404 ) : ?><link rel="canonical" href="<?php echo vote_e( $vote_url ); ?>"><?php echo "\n"; endif; ?>
<?php if ( ! $vote_page['index'] ) : ?><meta name="robots" content="noindex, follow"><?php echo "\n"; endif; ?>
<?php if ( ! $vote_404 ) : ?>
<meta property="og:title" content="<?php echo vote_e( $vote_page['title'] ); ?>">
<meta property="og:description" content="<?php echo vote_e( $vote_page['desc'] ); ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo vote_e( $vote_url ); ?>">
<meta property="og:image" content="<?php echo vote_e( VOTE_SITE . vote_v( $vote_og ) ); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="<?php echo vote_e( $vote_og_alt ); ?>">
<meta property="og:site_name" content="<?php echo vote_e( VOTE_BRAND ); ?>">
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" href="<?php echo VOTE_BASE; ?>assets/icons/favicon.ico" sizes="any">
<link rel="icon" type="image/svg+xml" href="<?php echo vote_v( 'assets/icons/favicon.svg' ); ?>">
<link rel="mask-icon" href="<?php echo VOTE_BASE; ?>assets/icons/mask-icon.svg" color="#14295E">
<link rel="manifest" href="<?php echo VOTE_BASE; ?>site.webmanifest">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo VOTE_BASE; ?>assets/icons/favicon-32.png">
<link rel="apple-touch-icon" href="<?php echo VOTE_BASE; ?>assets/icons/apple-touch-icon.png">
<meta name="theme-color" content="#0F2558">
<link rel="preload" href="<?php echo VOTE_BASE; ?>assets/fonts/anton.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?php echo VOTE_BASE; ?>assets/fonts/archivo-black.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?php echo vote_v( 'app.css' ); ?>">
<?php if ( ! $vote_404 ) : ?><script type="application/ld+json"><?php echo vote_json( $vote_ld ); ?></script><?php endif; ?>
<?php if ( ! $vote_st && ! $vote_404 ) : ?><script type="application/ld+json"><?php echo vote_json( array( '@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => VOTE_BRAND, 'alternateName' => 'VoteSticker.org', 'url' => VOTE_SITE . VOTE_BASE ) ); ?></script><?php echo "\n"; endif; ?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-PZWZTXDSDE"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-PZWZTXDSDE');
</script>
</head>
<body>
<header class="top<?php echo $vote_st ? "" : " top--hub"; ?>">
	<div class="wrap">
		<a class="brand" href="<?php echo VOTE_BASE; ?>"<?php echo $vote_st ? '' : ' aria-current="page"'; ?>>
			<svg class="brand__mark" aria-hidden="true" viewBox="0 0 40 40" width="40" height="40"><path d="M37.61 23.74A18 18 0 1 0 23.74 37.61Z" fill="#FBF6E8"/><path d="M37.61 23.74A18 18 0 1 0 23.74 37.61Z" fill="none" stroke="#14295E" stroke-width="1.6" transform="translate(20 20) scale(.8) translate(-20 -20)"/><path d="M37.61 23.74L23.74 37.61Q26.4 26.4 37.61 23.74Z" fill="#D9C99A"/><path d="M18.60 9.80 L20.77 15.61 L26.97 15.88 L22.12 19.74 L23.77 25.72 L18.60 22.30 L13.43 25.72 L15.08 19.74 L10.23 15.88 L16.43 15.61Z" fill="#C1272D"/>
</svg>
			<span class="brand__name">VOTE <span class="brand__hi">STICKER</span></span>
			<span class="brand__tag">Profile-picture stickers with your state's voting info</span>
		</a>
		<?php if ( $vote_st ) : ?>
		<nav class="crumbs" aria-label="Breadcrumb"><ol>
			<?php foreach ( $vote_crumbs as $i => $c ) : $last = $i === count( $vote_crumbs ) - 1; ?>
			<li><?php if ( $last ) : ?><span aria-current="page" id="crumb-last"><?php echo vote_e( $c[0] ); ?></span><?php else : ?><a href="<?php echo vote_e( $c[1] ); ?>"><?php echo vote_e( $c[0] ); ?></a><?php endif; ?></li>
			<?php endforeach; ?>
		</ol></nav>
		<?php endif; ?>
		<?php if ( $vote_st ) : $vote_lead_img = vote_files( $vote_abbr )[0] ?? ''; ?>
		<div class="state-hero<?php echo $vote_lead_img ? ' state-hero--art' : ''; ?>">
			<div>
				<h1 id="page-h1"><?php echo vote_e( $vote_page['h1'] ); ?></h1>
				<p class="ribbon"><?php echo vote_e( $vote_page['ribbon'] ); ?></p>
				<p class="lead" id="page-lead"><?php echo vote_e( $vote_page['lead'] ); ?></p>
				<p class="jump">
					<?php if ( $vote_lead_img ) : ?><a class="btn btn--gold" href="#gallery">See <?php echo vote_e( $vote_st['name'] ); ?> stickers <span aria-hidden="true">↓</span></a><?php endif; ?>
					<a class="btn <?php echo $vote_lead_img ? 'btn--line' : 'btn--gold'; ?>" href="#maker-h"><?php echo $vote_lead_img ? 'Make your own' : 'Make your sticker'; ?> <span aria-hidden="true">↓</span></a>
				</p>
				<div class="hero-meta">
					<?php if ( vote_verified( $vote_st ) ) : ?><a class="hero-meta__official" href="<?php echo vote_e( $vote_st['officialUrl'] ); ?>" target="_blank" rel="noopener">Official info: <?php echo vote_e( $vote_st['officialLabel'] ); ?> <span aria-hidden="true">↗</span></a>
					<?php else : ?><a class="hero-meta__official" href="https://www.vote411.org/<?php echo vote_slug( $vote_st['name'] ); ?>" target="_blank" rel="noopener">Check your dates at Vote411 <span aria-hidden="true">↗</span></a><?php endif; ?>
					<?php echo $vote_share; ?>
				</div>
			</div>
			<?php if ( $vote_lead_img ) : ?><img class="state-hero__sticker" src="<?php echo vote_e( $vote_lead_img ); ?>" alt="" width="1080" height="1080" aria-hidden="true"><?php endif; ?>
		</div>
		<?php else : ?>
		<div class="hero">
			<div class="hero__text">
				<p class="eyebrow"><?php echo vote_e( $vote_page['eyebrow'] ); ?></p>
				<h1 id="page-h1"><?php echo vote_e( $vote_page['h1'] ); ?></h1>
				<p class="lead" id="page-lead"><?php echo vote_e( $vote_page['lead'] ); ?></p>
				<?php echo vote_finder( 'Your state', 'Type a state, like Texas or TX' ); ?>
				<?php echo $vote_404 ? '<p class="home-link"><a href="' . VOTE_BASE . '">Go to the Vote Sticker home page</a></p>' : $vote_share; ?>
			</div>
			<div class="hero__art" aria-hidden="true">
				<?php foreach ( array( array( 'tx', 2 ), array( 'ca', 4 ), array( 'nc', 2 ) ) as $k => $h ) : if ( is_file( __DIR__ . "/assets/states/{$h[0]}/{$h[0]}-vote-{$h[1]}.jpg" ) ) : ?>
				<img class="hero__sticker hero__sticker--<?php echo $k + 1; ?>" src="<?php echo vote_v( "assets/states/{$h[0]}/{$h[0]}-vote-{$h[1]}.jpg" ); ?>" alt="" width="1080" height="1080"<?php echo $k ? ' loading="lazy"' : ''; ?>>
				<?php endif; endforeach; ?>
			</div>
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
		<h2 id="showcase-h" class="display-h"><?php echo $vote_404 ? 'Popular States' : 'Ready-Made Stickers'; ?></h2>
		<p class="section-intro"><?php echo $vote_404 ? 'Ready-made stickers from the most populous states. Tap one to see that state’s page.' : 'A few from the most populous states. Tap one to see that state\'s full set, or find your state above to make your own.'; ?></p>
		<div class="grid grid--showcase">
			<?php foreach ( $vote_showcase as $abbr => $n ) : ?>
			<a class="pick" href="<?php echo VOTE_BASE . $vote_meta[ strtoupper( $abbr ) ]['slug']; ?>/">
				<img src="<?php echo vote_v( "assets/states/$abbr/$abbr-vote-$n.jpg" ); ?>" alt="<?php echo vote_e( $vote_rows[ strtoupper( $abbr ) ]['name'] . ' vote profile picture example' ); ?>" width="1080" height="1080" loading="lazy">
				<span class="pick__name"><?php echo vote_e( $vote_rows[ strtoupper( $abbr ) ]['name'] ?? '' ); ?></span>
			</a>
			<?php endforeach; ?>
		</div>
	</section>
	<?php if ( ! $vote_404 ) : ?>
	<section class="how-wrap" aria-labelledby="how-h">
		<h2 id="how-h" class="display-h">How It Works</h2>
		<ol class="how">
			<li><b>Pick your state</b> Early voting, vote by mail, or Election Day. Your sticker starts with what's true where you vote.</li>
			<li><b>Save your sticker</b> Use a ready-made design or make your own. It fits the profile-picture circle.</li>
			<li><b>Check the official link</b> Your election office has the final word on places, hours, and ID rules.</li>
		</ol>
	</section>
	<section class="trust">
		<svg aria-hidden="true" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#F2C14E" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"><path d="M3 9l9-5 9 5"/><path d="M5 9v9M10 9v9M14 9v9M19 9v9M3 21h18"/></svg>
		<p><b>Nonpartisan by design.</b> We make the reminder. Your state or local election office has the final word. No account, and nothing is uploaded.</p>
	</section>
	<?php endif; ?>
	<?php endif; ?>

	<?php $vote_files = $vote_st ? vote_files( $vote_abbr ) : array(); ?>
	<div id="maker"<?php echo $vote_st ? '' : ' hidden'; ?>>
		<section class="gallery" id="gallery"<?php echo $vote_files ? '' : ' hidden'; ?> aria-labelledby="gallery-h">
			<h2 id="gallery-h" class="display-h"><?php echo $vote_st ? 'Ready-Made ' . vote_e( $vote_st['name'] ) . ' Stickers' : 'Ready-Made Stickers'; ?></h2>
			<p>Tap one to save it. Each keeps its words inside the profile-picture circle.</p>
			<div class="grid grid--gallery" id="gallery-grid" style="--cols: <?php echo vote_cols( count( $vote_files ) ); ?>"><?php foreach ( $vote_files as $i => $src ) : ?><a class="tile" href="<?php echo vote_e( $src ); ?>" download="vote-<?php echo strtolower( $vote_abbr ) . '-' . ( $i + 1 ); ?>.jpg"><img src="<?php echo vote_e( $src ); ?>" alt="<?php echo vote_e( $vote_st['name'] . ' vote picture, hand-made design ' . ( $i + 1 ) ); ?>" width="1080" height="1080" loading="lazy"></a><?php endforeach; ?></div>
		</section>

		<h2 class="maker-h display-h<?php echo $vote_files ? '' : ' maker-h--first'; ?>" id="maker-h"><?php echo $vote_files ? 'Or Make Your Own' : 'Make Your ' . vote_e( $vote_st['name'] ?? '' ) . ' Sticker'; ?></h2>
		<div class="controls controls--maker">
			<label>Main message
				<select id="headline"></select>
			</label>
			<label>Date text
				<span class="date-row"><input id="date" maxlength="24" autocomplete="off" spellcheck="false"><button type="button" class="btn btn--quiet" id="date-reset">Reset</button></span>
			</label>
		</div>

		<div class="maker">
			<div class="maker__preview" id="preview-wrap">
				<div class="sizes" role="group" aria-label="Picture size">
					<button type="button" class="size" data-size="square" aria-pressed="true">Square</button>
					<button type="button" class="size" data-size="story" aria-pressed="false">Story</button>
				</div>
				<div class="frame"><img id="preview" alt="Your profile picture" width="1080" height="1080"></div>
				<p class="summary" id="summary"></p>
				<label class="check" id="circle-check"><input type="checkbox" id="circle"> Preview circle crop</label>
				<div class="actions">
					<a class="btn" id="save" href="#" download aria-disabled="true">Download picture</a>
					<a class="link-quiet" id="open" href="#" target="_blank" rel="noopener">Open full image</a>
				</div>
				<p class="hint" id="tip"></p>
				<details class="post">
					<summary>Suggested post text</summary>
					<textarea id="post-text" rows="4" aria-label="Suggested post text"></textarea>
					<button type="button" class="btn btn--quiet" id="copy">Copy post text</button>
				</details>
			</div>
			<section class="maker__designs" aria-labelledby="designs-h">
				<h2 id="designs-h">Pick a Design</h2>
				<p class="help designs-help">Every design fits inside a profile-picture circle.</p>
				<div class="grid" id="grid"></div>
			</section>
		</div>

		<h2 class="display-h before-h" id="before-h">Before You Vote<?php echo $vote_st ? ' in ' . vote_e( 'DC' === $vote_abbr ? 'DC' : $vote_st['name'] ) : ''; ?></h2>
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
		$vote_same_h = array( 'mail' => 'Other States That Vote by Mail', 'none' => 'Other States Without Early Voting for Everyone' )[ $vote_st['kind'] ]
			?? ( ! empty( $vote_st['start'] ) ? 'Other States Where Early Voting Opens ' . vote_long( $vote_st['start'] ) : '' );
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
			<li><a href="<?php echo VOTE_BASE . $vote_meta[ $abbr ]['slug']; ?>/"><?php echo vote_e( $vote_rows[ $abbr ]['name'] ); ?></a><span><?php echo vote_e( ucfirst( $vote_meta[ $abbr ]['status'] ) ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php endif; ?>
	<?php if ( $vote_near ) : ?>
	<section class="near" aria-labelledby="near-h">
		<h2 id="near-h">States Next Door</h2>
		<ul>
			<?php foreach ( $vote_near as $abbr ) : ?>
			<li><a href="<?php echo VOTE_BASE . $vote_meta[ $abbr ]['slug']; ?>/"><?php echo vote_e( $vote_rows[ $abbr ]['name'] ); ?></a><span><?php echo vote_e( ucfirst( $vote_meta[ $abbr ]['status'] ) ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php endif; ?>

	<?php if ( $vote_st ) : ?>
	<nav class="pager" aria-label="Previous and next state">
		<?php if ( $vote_prev ) : ?><a rel="prev" href="<?php echo VOTE_BASE . $vote_meta[ $vote_prev ]['slug']; ?>/"><span>Previous state</span><?php echo vote_e( $vote_rows[ $vote_prev ]['name'] ); ?></a><?php else : ?><span></span><?php endif; ?>
		<?php if ( $vote_next ) : ?><a rel="next" class="pager__next" href="<?php echo VOTE_BASE . $vote_meta[ $vote_next ]['slug']; ?>/"><span>Next state</span><?php echo vote_e( $vote_rows[ $vote_next ]['name'] ); ?></a><?php endif; ?>
	</nav>
	<?php endif; ?>

	<nav class="states" aria-labelledby="states-h">
		<h2 id="states-h"><?php echo $vote_st ? 'All States' : 'Browse All States'; ?></h2>
		<ul>
			<?php foreach ( $vote_rows as $abbr => $row ) : ?>
			<li><?php if ( $abbr === $vote_abbr ) : ?><span aria-current="page"><?php echo vote_e( $row['name'] ); ?></span><?php else : ?><a href="<?php echo VOTE_BASE . $vote_meta[ $abbr ]['slug']; ?>/"><?php echo vote_e( $row['name'] ); ?></a><?php endif; ?></li>
			<?php endforeach; ?>
		</ul>
	</nav>
	</div>

	<?php if ( ! $vote_404 ) : ?>
	<section class="faq" aria-labelledby="faq-h">
		<h2 id="faq-h">Common Questions</h2>
		<details><summary>Is this an official election site?</summary><p>No. Vote Sticker is an independent, nonpartisan tool made by one person. For registration, polling places, and deadlines, go to your state or local election office. Each state page links to it.</p></details>
		<details><summary>Where do the dates come from?</summary><p>From official state sources: the election office's own pages or state law, checked and dated. Until a state's dates are confirmed, its stickers say Election Day.</p></details>
		<details><summary>Why do some states say hours or start dates vary?</summary><p>In some states, counties or towns set their own early voting days, hours, and locations. Your local election office has the final details.</p></details>
		<details><summary>What if my state doesn't have early voting?</summary><p>Your sticker says VOTE NOV 3. If you can't vote in person on Election Day, you may qualify for an absentee ballot. Your state's page links to the rules.</p></details>
		<details><summary>Do you keep my information?</summary><p>No. Your sticker is created in your browser and never uploaded.</p></details>
	</section>
	<?php endif; ?>
</main>

<footer class="foot">
	<div class="wrap">
		<p><b>This tool is nonpartisan.</b> Voting dates and links come from official state election sources. Before you vote, check your registration, polling place, hours, and ID rules with your state or local election office. <a href="https://www.usa.gov/state-election-office">Find your state or local election office</a>.</p>
		<p class="foot__made"><span><?php echo vote_e( VOTE_BRAND ); ?> is made by</span> <a class="foot__gd" href="https://www.garrettdigital.com/"><img src="<?php echo vote_v( 'assets/gd-logo.svg' ); ?>" alt="Garrett Digital" width="152" height="40"></a></p>
		<p>Your sticker is created in your browser. Nothing is uploaded.</p>
	</div>
</footer>

<?php
// Hand-made pictures: every JPG in assets/states/<abbr>/ shows under that state, in natural order.
$vote_gallery = array();
foreach ( glob( __DIR__ . '/assets/states/*', GLOB_ONLYDIR ) as $dir ) { $vote_gallery[ strtoupper( basename( $dir ) ) ] = vote_files( basename( $dir ) ); }
?>
<script>window.VOTE_GALLERY = <?php echo vote_json( $vote_gallery ); ?>;
window.VOTE_META = <?php echo vote_json( $vote_meta ); ?>;
window.VOTE_BASE = <?php echo vote_json( VOTE_BASE ); ?>;
window.VOTE_ASSETS = <?php echo vote_json( VOTE_BASE . 'assets/' ); ?>;
window.VOTE_PAGE_STATE = <?php echo vote_json( $vote_abbr ); ?>;</script>
<script src="<?php echo vote_v( 'states.js' ); ?>"></script>
<script src="<?php echo vote_v( 'state-shapes.js' ); ?>"></script>
<script src="<?php echo vote_v( 'app.js' ); ?>"></script>
</body>
</html>
