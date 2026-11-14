<?php
/**
 * Evergreen guides under /voting/ (/voting/runoff-elections/, /es/voting/runoff-elections/), set November 10, 2026.
 *
 * Words and state rules live in build/guides/<slug>.json (English and Spanish side by side, each state rule
 * with its sources and checked date); build/guide_check.py validates them before every build. Standards:
 * docs/editorial-standards.md. Visuals: docs/guide-visuals-spec.md. Everything is HTML and CSS drawn here
 * at build time: no chart library, no animation, and the page works without JavaScript. Counts in the text
 * ({n_states} and the lists) are computed from the state rows, never typed.
 */

const VOTE_GUIDES = array( 'runoff-elections' );

function vote_guide_slug() { return defined( 'VOTE_GUIDE' ) && in_array( VOTE_GUIDE, VOTE_GUIDES, true ) ? VOTE_GUIDE : ''; }

function vote_guide_data() {
	static $d = null;
	if ( null === $d ) {
		$f = dirname( __DIR__ ) . '/build/guides/' . vote_guide_slug() . '.json';
		$d = is_file( $f ) ? json_decode( file_get_contents( $f ), true ) : array();
	}
	return $d;
}

// Words the components use, in both languages (kept here so the guide carries its own Spanish).
function vote_g_ui( $key, ...$args ) {
	static $ui = array(
		'by' => array( 'By Vote Sticker', 'Por Vote Sticker' ),
		'published' => array( 'Published %s', 'Publicado el %s' ),
		'updated' => array( 'Updated %s', 'Actualizado el %s' ),
		'reviewed' => array( 'Facts reviewed %s', 'Datos revisados el %s' ),
		'how_check' => array( 'How we check facts', 'Cómo revisamos los datos' ),
		'key_facts' => array( 'Key Facts', 'Datos clave' ),
		'example' => array( 'How a runoff gets triggered', 'Cómo se llega a una segunda vuelta' ),
		'passed' => array( 'Did the leader pass the threshold?', '¿El primer lugar pasó el porcentaje?' ),
		'th_title' => array( 'Share of the vote needed to win without a runoff', 'Porcentaje de votos para ganar sin segunda vuelta' ),
		'th_zone' => array( 'Wins outright', 'Gana sin segunda vuelta' ),
		'th_axis' => array( 'Share of the vote', 'Porcentaje de votos' ),
		'tm_title' => array( 'Weeks from the first election to the runoff', 'Semanas entre la primera elección y la segunda vuelta' ),
		'tm_axis' => array( 'Weeks after the first election', 'Semanas después de la primera elección' ),
		'weeks' => array( '%s weeks', '%s semanas' ),
		'weeks_range' => array( '%1$s to %2$s weeks', '%1$s a %2$s semanas' ),
		'tm_note' => array( 'A line shows a gap that changes with the calendar. Oklahoma and Texas set the runoff on a named Tuesday, so the gap shifts by a week from year to year.', 'Una línea indica un tiempo que cambia con el calendario. Oklahoma y Texas fijan la segunda vuelta en un martes específico, así que el tiempo cambia una semana de un año a otro.' ),
		'src_line' => array( 'Source: each state\'s election law, linked in Runoff Rules by State. Checked %s.', 'Fuente: la ley electoral de cada estado, enlazada en Reglas de segunda vuelta por estado. Revisado el %s.' ),
		'pick' => array( 'Your state', 'Tu estado' ),
		'pick_go' => array( 'Show my state', 'Ver mi estado' ),
		'pick_none' => array( 'Choose a state', 'Elige un estado' ),
		'pick_other' => array( 'My state isn\'t listed', 'Mi estado no aparece' ),
		'other_states' => array( 'Other states and DC', 'Otros estados y DC' ),
		'other_text' => array( 'NCSL lists no statewide primary or general election runoff in the other states and DC, and we haven\'t found one. Local races can still have runoffs. Your state\'s page links to its election office.', 'NCSL no menciona segunda vuelta estatal en las primarias ni en la elección general de los demás estados y DC, y nosotros tampoco la encontramos. Las elecciones locales pueden tenerla. La página de tu estado tiene el enlace a su oficina electoral.' ),
		'find_page' => array( 'Find your state\'s page', 'Busca la página de tu estado' ),
		'elections' => array( 'Which elections', 'Qué elecciones' ),
		'rule' => array( 'To win without a runoff', 'Para ganar sin segunda vuelta' ),
		'when' => array( 'When the runoff is', 'Cuándo es la segunda vuelta' ),
		'general' => array( 'After the November election', 'Después de la elección de noviembre' ),
		'no_general' => array( 'No statewide runoff', 'No hay segunda vuelta estatal' ),
		'note' => array( 'Also', 'Además' ),
		'who' => array( 'Who can vote', 'Quién puede votar' ),
		'cross' => array( 'Switch to another party\'s runoff', 'Cambiarte a la segunda vuelta de otro partido' ),
		'skip' => array( 'Vote after skipping the first round', 'Votar si no votaste en la primera ronda' ),
		'register' => array( 'Register between rounds', 'Registrarte entre una ronda y la otra' ),
		'sources' => array( 'Sources', 'Fuentes' ),
		'checked' => array( 'Checked %s', 'Revisado el %s' ),
		'law_says' => array( 'The law says', 'La ley dice' ),
		'state_page' => array( '%s voting dates and stickers', 'Fechas para votar y stickers de %s' ),
		'state_col' => array( 'State', 'Estado' ),
		'v_yes' => array( 'Can', 'Sí' ), 'v_no' => array( 'Can\'t', 'No' ), 'v_dep' => array( 'Depends', 'Depende' ), 'v_unk' => array( 'Not confirmed yet', 'Aún sin confirmar' ),
		'who_cap' => array( 'Who can vote in a primary runoff, by state', 'Quién puede votar en la segunda vuelta de una primaria, por estado' ),
		'who_key' => array( '“Not confirmed yet” means we haven\'t read the rule on an official page. It doesn\'t mean no.', '“Aún sin confirmar” quiere decir que todavía no leímos la regla en una página oficial. No quiere decir que no.' ),
		'make' => array( 'Make Your Sticker', 'Haz tu sticker' ),
		'make_btn' => array( 'Make a sticker for your state', 'Haz un sticker de tu estado' ),
		'changes' => array( 'Changes to This Guide', 'Cambios a esta guía' ),
		'translated' => array( '', 'Las citas de la ley están en inglés, como en el original.' ),
	);
	$s = $ui[ $key ][ vote_es() ? 1 : 0 ] ?? $key;
	return $args ? vsprintf( $s, $args ) : $s;
}

function vote_g_tx( $pair ) { return is_array( $pair ) ? ( vote_es() ? ( $pair['es'] ?? '' ) : ( $pair['en'] ?? '' ) ) : (string) $pair; }

// A list of names in prose: "A, B and C" / "A, B y C".
function vote_g_join( $names ) {
	$names = array_values( $names );
	if ( count( $names ) < 2 ) { return implode( '', $names ); }
	$last = array_pop( $names );
	return implode( ', ', $names ) . ( vote_es() ? ' y ' : ' and ' ) . $last;
}

// Tokens computed from the state rows, so the counts in the words always match the table.
function vote_g_tokens() {
	static $tok = array();
	$lang = vote_es() ? 'es' : 'en';
	if ( isset( $tok[ $lang ] ) ) { return $tok[ $lang ]; }
	$rows = $GLOBALS['vote_rows']; $st = vote_guide_data()['states'] ?? array();
	$name = function ( $s ) use ( $rows ) { return vote_name( $rows[ $s['abbr'] ] ); };
	$gen = array_filter( $st, function ( $s ) { return 'G' === $s['cat']; } );
	$skip = array_filter( $st, function ( $s ) { return 'yes' === $s['who']['skip']['v']; } );
	$either = array_filter( $skip, function ( $s ) { return ! empty( $s['who']['skip']['either'] ); } );
	return $tok[ $lang ] = array(
		'{n_states}' => (string) count( $st ), '{n_general}' => (string) count( $gen ),
		'{general_list}' => vote_g_join( array_map( $name, $gen ) ),
		'{skip_yes_list}' => vote_g_join( array_map( $name, $skip ) ),
		'{skip_either_list}' => vote_g_join( array_map( $name, $either ) ),
	);
}

// Prose may carry **bold** and [text](https://...) links; everything is escaped first.
function vote_g_md( $text ) {
	$s = vote_e( strtr( $text, vote_g_tokens() ) );
	$s = preg_replace( '/\*\*(.+?)\*\*/', '<strong>$1</strong>', $s );
	return preg_replace_callback( '/\[([^\]]+)\]\((https:\/\/[^)\s]+)\)/', function ( $m ) { return '<a href="' . $m[2] . '">' . $m[1] . '</a>'; }, $s );
}
function vote_g_p( $pair ) { return vote_g_md( vote_g_tx( $pair ) ); }

function vote_guide_dates() { $d = vote_guide_data(); return $d['dates'][ vote_es() ? 'es' : 'en' ] ?? array(); }

function vote_guide_meta() {
	$m = vote_guide_data()['meta'] ?? array();
	return array(
		'title' => vote_g_tx( $m['title'] ?? '' ), 'h1' => vote_g_tx( $m['h1'] ?? '' ), 'eyebrow' => '',
		'lead' => strtr( vote_g_tx( $m['lead'] ?? '' ), vote_g_tokens() ), 'desc' => vote_g_tx( $m['desc'] ?? '' ),
		'crumb' => vote_g_tx( $m['crumb'] ?? '' ), 'og_alt' => vote_g_tx( $m['og_alt'] ?? '' ),
		'index' => ! vote_es() || VOTE_ES_INDEX,
	);
}

function vote_guide_ld( $page, $url ) {
	$dt = vote_guide_dates();
	return array(
		'@context' => 'https://schema.org', '@type' => 'Article', 'headline' => $page['h1'], 'description' => $page['desc'],
		'url' => $url, 'mainEntityOfPage' => $url, 'inLanguage' => $GLOBALS['vote_lang'],
		'datePublished' => $dt['published'], 'dateModified' => $dt['updated'] ?: $dt['published'],
		'author' => array( '@type' => 'Organization', 'name' => VOTE_BRAND, 'url' => VOTE_SITE . VOTE_BASE . ( vote_es() ? 'es/' : '' ) . 'about/' ),
		'publisher' => array( '@type' => 'Organization', 'name' => 'Garrett Digital', 'url' => 'https://www.garrettdigital.com/', 'logo' => array( '@type' => 'ImageObject', 'url' => VOTE_SITE . VOTE_BASE . 'assets/gd-logo.svg' ) ),
		'about' => array( '@type' => 'Thing', 'name' => vote_guide_data()['about'] ?? $page['h1'] ),
	) + ( 0 === strpos( $GLOBALS['vote_og'] ?? '', 'assets/og/guides/' ) ? array( 'image' => VOTE_SITE . VOTE_BASE . $GLOBALS['vote_og'] ) : array() );
}

// The byline under the answer: who, when it went live, when the facts were last reviewed, and how.
function vote_guide_byline() {
	$dt = vote_guide_dates();
	$parts = array( vote_e( vote_g_ui( 'by' ) ), vote_e( vote_g_ui( 'published', vote_checked( $dt['published'] ) ) ) );
	if ( ! empty( $dt['updated'] ) && $dt['updated'] > $dt['published'] ) { $parts[] = vote_e( vote_g_ui( 'updated', vote_checked( $dt['updated'] ) ) ); }
	$parts[] = vote_e( vote_g_ui( 'reviewed', vote_checked( $dt['reviewed'] ) ) );
	$parts[] = '<a href="' . vote_pbase() . 'about/#facts-h">' . vote_e( vote_g_ui( 'how_check' ) ) . '</a>';
	return '<p class="byline">' . implode( ' <span aria-hidden="true">·</span> ', $parts ) . '</p>';
}

function vote_g_states() {
	$rows = $GLOBALS['vote_rows'];
	$st = vote_guide_data()['states'] ?? array();
	usort( $st, function ( $a, $b ) use ( $rows ) { return strcmp( vote_name( $rows[ $a['abbr'] ] ), vote_name( $rows[ $b['abbr'] ] ) ); } );
	return $st;
}
function vote_g_name( $s ) { return vote_name( $GLOBALS['vote_rows'][ $s['abbr'] ] ); }
function vote_g_latest_check() { return max( array_map( function ( $s ) { return $s['checked']; }, vote_guide_data()['states'] ) ); }

// Step flow: the same steps as an ordered list, with the yes and no branches side by side.
function vote_g_flow() {
	$out = '<figure class="gflow"><figcaption class="gfig__title">' . vote_e( vote_g_ui( 'example' ) ) . '</figcaption><ol class="gflow__steps">';
	foreach ( vote_guide_data()['flow'] as $step ) {
		if ( isset( $step['yes'] ) ) {
			$out .= '<li class="gflow__fork"><p class="gflow__q">' . vote_e( vote_g_ui( 'passed' ) ) . '</p><div class="gflow__branches">'
				. '<p class="gflow__b gflow__b--yes">' . vote_g_p( $step['yes'] ) . '</p><p class="gflow__b gflow__b--no">' . vote_g_p( $step['no'] ) . '</p></div></li>';
		} else {
			$out .= '<li class="gflow__step"><p>' . vote_g_p( $step ) . '</p></li>';
		}
	}
	return $out . '</ol></figure>';
}

// Threshold dot plot: one row per distinct rule on a 0 to 100% axis, the winning zone shaded, states named in
// the row label (direct labels, no legend). HTML and CSS, so text stays body size on a 320 px phone.
function vote_g_threshold() {
	$groups = array();
	foreach ( vote_g_states() as $s ) {
		$k = $s['threshold']['value'] . $s['threshold']['op'];
		$groups[ $k ] = $groups[ $k ] ?? array( 'value' => $s['threshold']['value'], 'words' => vote_g_tx( $s['threshold']['words'] ), 'states' => array() );
		$groups[ $k ]['states'][] = vote_g_name( $s );
	}
	uasort( $groups, function ( $a, $b ) { return $a['value'] <=> $b['value']; } );
	$out = '<figure class="gplot gplot--th"><figcaption class="gfig__title">' . vote_e( vote_g_ui( 'th_title' ) ) . '</figcaption><p class="gplot__key" aria-hidden="true"><i></i>' . vote_e( vote_g_ui( 'th_zone' ) ) . '</p><ul class="gplot__rows">';
	foreach ( $groups as $g ) {
		$out .= '<li class="gplot__row" style="--v:' . (int) $g['value'] . '"><p class="gplot__label"><strong>' . vote_e( $g['words'] ) . '</strong> <span>' . vote_e( vote_g_join( $g['states'] ) ) . '</span></p>'
			. '<div class="gplot__track" aria-hidden="true"><span class="gplot__zone"><span class="gplot__zlabel">' . vote_e( vote_g_ui( 'th_zone' ) ) . '</span></span><span class="gplot__dot"></span><span class="gplot__val">' . (int) $g['value'] . '%</span></div></li>';
	}
	$out .= '</ul><div class="gplot__axis" aria-hidden="true">';
	foreach ( array( 0, 25, 50, 75, 100 ) as $t ) { $out .= '<span style="--v:' . $t . '">' . $t . '%</span>'; }
	return $out . '</div><p class="gplot__axislabel" aria-hidden="true">' . vote_e( vote_g_ui( 'th_axis' ) ) . '</p><p class="cite">' . vote_e( vote_g_ui( 'src_line', vote_checked( vote_g_latest_check() ) ) ) . '</p></figure>';
}

// Timing plot: one row per state, sorted by weeks, on a 0 to 13 week axis. A dot for a fixed gap, a capped
// line for a gap that moves with the calendar.
function vote_g_timing() {
	$st = vote_g_states();
	usort( $st, function ( $a, $b ) { return array( $a['timing']['min'], $a['timing']['max'] ) <=> array( $b['timing']['min'], $b['timing']['max'] ); } );
	$max = 13;
	$out = '<figure class="gplot gplot--tm"><figcaption class="gfig__title">' . vote_e( vote_g_ui( 'tm_title' ) ) . '</figcaption><ul class="gplot__rows">';
	foreach ( $st as $s ) {
		$t = $s['timing']; $range = $t['max'] > $t['min'];
		$val = $range ? vote_g_ui( 'weeks_range', $t['min'], $t['max'] ) : vote_g_ui( 'weeks', $t['min'] );
		$out .= '<li class="gplot__row" style="--a:' . round( $t['min'] / $max * 100, 2 ) . ';--b:' . round( $t['max'] / $max * 100, 2 ) . '"><p class="gplot__label"><a href="#state-' . strtolower( $s['abbr'] ) . '">' . vote_e( vote_g_name( $s ) ) . '</a> <span>' . vote_e( $val ) . '</span></p>'
			. '<div class="gplot__track" aria-hidden="true">' . ( $range ? '<span class="gplot__range"></span>' : '<span class="gplot__dot"></span>' ) . '</div></li>';
	}
	$out .= '</ul><div class="gplot__axis" aria-hidden="true">';
	foreach ( array( 0, 2, 4, 6, 8, 10, 12 ) as $w ) { $out .= '<span style="--v:' . round( $w / $max * 100, 2 ) . '">' . $w . '</span>'; }
	return $out . '</div><p class="gplot__axislabel" aria-hidden="true">' . vote_e( vote_g_ui( 'tm_axis' ) ) . '</p><p class="gplot__note">' . vote_e( vote_g_ui( 'tm_note' ) ) . '</p><p class="cite">' . vote_e( vote_g_ui( 'src_line', vote_checked( vote_g_latest_check() ) ) ) . '</p></figure>';
}

// A plain table that stacks into labeled cards on phones (each cell carries its column name).
function vote_g_table( $t, $class = '' ) {
	$cols = array_map( 'vote_g_tx', $t['cols'] );
	$out = '<div class="gtable-wrap"><table class="gtable ' . $class . '"><thead><tr>';
	foreach ( $cols as $c ) { $out .= '<th scope="col">' . vote_e( $c ) . '</th>'; }
	$out .= '</tr></thead><tbody>';
	foreach ( $t['rows'] as $r ) {
		$out .= '<tr>';
		foreach ( $r as $i => $cell ) { $out .= $i ? '<td data-label="' . vote_e( $cols[ $i ] ) . '">' . vote_g_p( $cell ) . '</td>' : '<th scope="row">' . vote_g_p( $cell ) . '</th>'; }
		$out .= '</tr>';
	}
	return $out . '</tbody></table></div>';
}

function vote_g_badge( $cat ) {
	$c = vote_guide_data()['categories'][ $cat ];
	return '<span class="gcat gcat--' . strtolower( $cat ) . '"><span class="gcat__mark" aria-hidden="true"><span>' . $cat . '</span></span>' . vote_e( vote_g_tx( $c ) ) . '</span>';
}

function vote_g_cell( $c ) {
	$sym = array( 'yes' => '✓', 'no' => '✕', 'dep' => '~', 'unk' => '?' )[ $c['v'] ];
	return '<span class="gv gv--' . $c['v'] . '"><span class="gv__sym" aria-hidden="true">' . $sym . '</span><span class="gv__word">' . vote_e( vote_g_ui( 'v_' . $c['v'] ) ) . '</span></span>' . ( 'unk' === $c['v'] ? '' : ' <span class="gv__text">' . vote_e( vote_g_tx( $c ) ) . '</span>' );
}

// The state sections: grouped links by category (the orientation a map would give), the optional picker, then
// one section per state with a stable #state-xx address that works with no JavaScript.
function vote_g_states_block() {
	$d = vote_guide_data(); $st = vote_g_states(); $meta = $GLOBALS['vote_meta'];
	$out = '<p class="gcover">' . vote_g_p( $d['coverage'] ) . ' <a href="' . vote_e( $d['coverage_src'] ) . '" target="_blank" rel="noopener" lang="en">NCSL, Primary Runoffs</a></p>';
	$out .= '<nav class="ggroups" aria-label="' . vote_e( vote_g_tx( $d['sections'][5]['h'] ) ) . '">';
	foreach ( array( 'G', 'P', 'L' ) as $cat ) {
		$in = array_filter( $st, function ( $s ) use ( $cat ) { return $cat === $s['cat']; } );
		$out .= '<div class="ggroups__g">' . vote_g_badge( $cat ) . '<ul>';
		foreach ( $in as $s ) { $out .= '<li><a href="#state-' . strtolower( $s['abbr'] ) . '">' . vote_e( vote_g_name( $s ) ) . '</a></li>'; }
		$out .= '</ul></div>';
	}
	$out .= '<div class="ggroups__g"><span class="gcat gcat--none">' . vote_e( vote_g_ui( 'other_states' ) ) . '</span><ul><li><a href="#state-other">' . vote_e( vote_g_ui( 'no_general' ) ) . '</a></li></ul></div></nav>';
	// The picker: hidden until the script below shows it; it only moves to a section that is already on the page.
	$out .= '<form class="gpick" id="gpick" hidden><label for="gpick-s">' . vote_e( vote_g_ui( 'pick' ) ) . '</label><span class="gpick__row"><select id="gpick-s"><option value="">' . vote_e( vote_g_ui( 'pick_none' ) ) . '</option>';
	foreach ( $st as $s ) { $out .= '<option value="state-' . strtolower( $s['abbr'] ) . '">' . vote_e( vote_g_name( $s ) ) . '</option>'; }
	$out .= '<option value="state-other">' . vote_e( vote_g_ui( 'pick_other' ) ) . '</option></select><button type="submit" class="btn btn--quiet">' . vote_e( vote_g_ui( 'pick_go' ) ) . '</button></span></form>';
	$out .= '<div class="gstates">';
	foreach ( $st as $s ) {
		$id = 'state-' . strtolower( $s['abbr'] ); $name = vote_g_name( $s ); $t = $s['threshold'];
		$row = function ( $k, $v ) { return '<div class="gstate__row"><dt>' . vote_e( vote_g_ui( $k ) ) . '</dt><dd>' . $v . '</dd></div>'; };
		$out .= '<section class="gstate" id="' . $id . '" aria-labelledby="' . $id . '-h" tabindex="-1"><header class="gstate__head"><h3 id="' . $id . '-h">' . vote_e( $name ) . '</h3>' . vote_g_badge( $s['cat'] ) . '</header><dl class="gstate__dl">'
			. $row( 'elections', vote_g_p( $s['offices'] ) )
			. $row( 'rule', '<strong>' . vote_e( vote_g_tx( $t['words'] ) ) . '.</strong> ' . vote_e( vote_g_ui( 'law_says' ) ) . ': <q lang="en">' . vote_e( $t['quote'] ) . '</q> (' . vote_e( $t['cite'] ) . ')' )
			. $row( 'when', vote_g_p( $s['timing']['words'] ) )
			. $row( 'general', $s['general'] ? vote_g_p( $s['general'] ) : vote_e( vote_g_ui( 'no_general' ) ) )
			. ( ! empty( $s['extra'] ) ? $row( 'note', vote_g_p( $s['extra'] ) ) : '' )
			. '</dl><h4 class="gstate__sub">' . vote_e( vote_g_ui( 'who' ) ) . '</h4><ul class="gstate__who">';
		foreach ( array( 'cross', 'skip', 'register' ) as $k ) { $out .= '<li><span class="gstate__q">' . vote_e( vote_g_ui( $k ) ) . '</span> ' . vote_g_cell( $s['who'][ $k ] ) . '</li>'; }
		$out .= '</ul><p class="gstate__src"><span>' . vote_e( vote_g_ui( 'sources' ) ) . ':</span> ';
		$out .= implode( ' · ', array_map( function ( $src ) { return '<a href="' . vote_e( $src['url'] ) . '" target="_blank" rel="noopener" lang="en">' . vote_e( $src['title'] ) . '</a>'; }, $s['sources'] ) );
		$out .= '. ' . vote_e( vote_g_ui( 'checked', vote_checked( $s['checked'] ) ) ) . '.</p>'
			. '<p class="gstate__more"><a href="' . vote_pbase() . $meta[ $s['abbr'] ]['slug'] . '/">' . vote_e( vote_g_ui( 'state_page', $name ) ) . '</a></p></section>';
	}
	$out .= '<section class="gstate gstate--other" id="state-other" aria-labelledby="state-other-h" tabindex="-1"><header class="gstate__head"><h3 id="state-other-h">' . vote_e( vote_g_ui( 'other_states' ) ) . '</h3></header><p>' . vote_e( vote_g_ui( 'other_text' ) ) . '</p><p class="gstate__more"><a href="' . vote_pbase() . '#states-h">' . vote_e( vote_g_ui( 'find_page' ) ) . '</a></p></section></div>';
	if ( vote_es() ) { $out .= '<p class="cite">' . vote_e( vote_g_ui( 'translated' ) ) . '</p>'; }
	// ?state=TX (from a state page) and the picker both jump to a section that is already on the page.
	$out .= '<script>(function(){var f=document.getElementById("gpick"),s=document.getElementById("gpick-s");if(!f||!s){return;}f.hidden=false;'
		. 'function go(id){var el=document.getElementById(id);if(!el){return;}if(location.hash!=="#"+id){history.replaceState(null,"","#"+id);}el.scrollIntoView();el.focus({preventScroll:true});document.querySelectorAll(".gstate--on").forEach(function(x){x.classList.remove("gstate--on");});el.classList.add("gstate--on");}'
		. 'f.addEventListener("submit",function(e){e.preventDefault();if(s.value){go(s.value);}});'
		. 'var q=(location.search.match(/[?&]state=([a-z]{2})\b/i)||[])[1];if(q&&!location.hash){var id="state-"+q.toLowerCase();if(document.getElementById(id)){s.value=id;go(id);}else{s.value="state-other";go("state-other");}}})();</script>';
	return $out;
}

// Who can vote: the same three questions for every state, with words in the cells (never color alone).
function vote_g_who() {
	$out = '<figure class="gwho"><figcaption class="gfig__title">' . vote_e( vote_g_ui( 'who_cap' ) ) . '</figcaption><div class="gtable-wrap"><table class="gtable gtable--who"><thead><tr><th scope="col">' . vote_e( vote_g_ui( 'state_col' ) ) . '</th>';
	foreach ( array( 'cross', 'skip', 'register' ) as $k ) { $out .= '<th scope="col">' . vote_e( vote_g_ui( $k ) ) . '</th>'; }
	$out .= '</tr></thead><tbody>';
	foreach ( vote_g_states() as $s ) {
		$out .= '<tr><th scope="row"><a href="#state-' . strtolower( $s['abbr'] ) . '">' . vote_e( vote_g_name( $s ) ) . '</a></th>';
		foreach ( array( 'cross', 'skip', 'register' ) as $k ) { $out .= '<td data-label="' . vote_e( vote_g_ui( $k ) ) . '">' . vote_g_cell( $s['who'][ $k ] ) . '</td>'; }
		$out .= '</tr>';
	}
	return $out . '</tbody></table></div><p class="cite">' . vote_e( vote_g_ui( 'who_key' ) ) . ' ' . vote_e( vote_g_ui( 'src_line', vote_checked( vote_g_latest_check() ) ) ) . '</p></figure>';
}

function vote_g_faq() {
	$out = '';
	foreach ( vote_guide_data()['faq'] as $f ) { $out .= '<h3>' . vote_e( vote_g_tx( $f['q'] ) ) . '</h3><p>' . vote_g_p( $f['a'] ) . '</p>'; }
	return $out;
}

function vote_guide_page() {
	$d = vote_guide_data();
	if ( ! $d ) { return ''; }
	$out = '<div class="about gguide">';
	$out .= '<aside class="gkey" aria-labelledby="key-h"><h2 class="gkey__h" id="key-h">' . vote_e( vote_g_ui( 'key_facts' ) ) . '</h2><ul>';
	foreach ( $d['key_facts'] as $k ) { $out .= '<li>' . vote_g_p( $k ) . '</li>'; }
	$out .= '</ul></aside>';
	foreach ( $d['sections'] as $i => $sec ) {
		$hid = $sec['id'] . '-h';
		$out .= '<section class="guide' . ( $i ? '' : ' guide--first' ) . '" aria-labelledby="' . $hid . '"><h2 class="display-h guide__h" id="' . $hid . '">' . vote_e( vote_g_tx( $sec['h'] ) ) . '</h2>';
		foreach ( $sec['paras'] ?? array() as $p ) { $out .= '<p>' . vote_g_p( $p ) . '</p>'; }
		switch ( $sec['component'] ?? '' ) {
			case 'flow': $out .= vote_g_flow(); break;
			case 'threshold': $out .= vote_g_threshold(); break;
			case 'timing': $out .= vote_g_timing(); break;
			case 'kinds': $out .= vote_g_table( $d['kinds'], 'gtable--kinds' ); break;
			case 'compare': $out .= vote_g_table( $d['compare'] ); break;
			case 'states': $out .= vote_g_states_block(); break;
			case 'who': $out .= vote_g_who(); break;
			case 'faq': $out .= vote_g_faq(); break;
		}
		if ( ! empty( $sec['list'] ) ) { $out .= '<ul class="guide__list guide__list--one">' . implode( '', array_map( function ( $x ) { return '<li>' . vote_g_p( $x ) . '</li>'; }, $sec['list'] ) ) . '</ul>'; }
		foreach ( $sec['after'] ?? array() as $p ) { $out .= '<p>' . vote_g_p( $p ) . '</p>'; }
		$out .= '</section>';
	}
	$out .= '<section class="guide" aria-labelledby="make-h"><h2 class="display-h guide__h" id="make-h">' . vote_e( vote_g_ui( 'make' ) ) . '</h2><p>' . vote_g_p( $d['cta'] ) . '</p>'
		. '<p class="jump"><a class="btn btn--gold" href="' . vote_pbase() . '#state-find">' . vote_e( vote_g_ui( 'make_btn' ) ) . '</a></p></section>';
	$changes = $d['changes'][ vote_es() ? 'es' : 'en' ] ?? array();
	if ( $changes ) {
		$out .= '<section class="guide" aria-labelledby="changes-h"><h2 class="display-h guide__h" id="changes-h">' . vote_e( vote_g_ui( 'changes' ) ) . '</h2><ul class="guide__list guide__list--one">';
		foreach ( $changes as $c ) { $out .= '<li>' . vote_e( vote_checked( $c['date'] ) ) . ': ' . vote_g_md( $c['text'] ) . '</li>'; }
		$out .= '</ul></section>';
	}
	return $out . '</div>';
}

// State pages: a short section linking a runoff state to its part of the guide, with the rule in one line.
function vote_guide_state_section( $st ) {
	static $rows = null;
	if ( null === $rows ) {
		$f = dirname( __DIR__ ) . '/build/guides/runoff-elections.json';
		$rows = is_file( $f ) ? array_column( json_decode( file_get_contents( $f ), true )['states'], null, 'abbr' ) : array();
	}
	$r = $rows[ $st['abbr'] ] ?? null;
	if ( ! $r ) { return ''; }
	$es = vote_es(); $name = vote_name( $st ); $tx = function ( $p ) use ( $es ) { return $p[ $es ? 'es' : 'en' ]; };
	$line = $es
		? sprintf( 'Para ganar sin segunda vuelta: %s. %s.', mb_strtolower( $tx( $r['threshold']['words'] ) ), $tx( $r['timing']['words'] ) )
		: sprintf( 'To win without a runoff: %s. %s.', strtolower( $tx( $r['threshold']['words'] ) ), $tx( $r['timing']['words'] ) );
	return '<section class="guide" aria-labelledby="runoff-h"><h2 class="display-h guide__h" id="runoff-h">' . vote_e( $es ? 'Segunda vuelta en ' . $name : 'Runoff Elections in ' . $name ) . '</h2>'
		. '<p>' . vote_e( $tx( $r['offices'] ) ) . '. ' . vote_e( $line ) . '</p>'
		. '<p class="cite">' . vote_e( $es ? 'Fuente:' : 'Source:' ) . ' <a href="' . vote_e( $r['sources'][0]['url'] ) . '" target="_blank" rel="noopener" lang="en">' . vote_e( $r['sources'][0]['title'] ) . '</a> · ' . vote_e( vote_g_ui( 'checked', vote_checked( $r['checked'] ) ) ) . '</p>'
		. '<p class="guide__more"><a href="' . vote_pbase() . 'voting/runoff-elections/#state-' . strtolower( $st['abbr'] ) . '">' . vote_e( $es ? 'Quién puede votar en una segunda vuelta en ' . $name : 'Who can vote in a ' . $name . ' runoff, and the law behind it' ) . '</a></p></section>';
}
