<?php
/**
 * Detail text column — DESIGN-SPEC §4.8.1 `text_blocks[]` · `caption` (P-161/P-162).
 *
 * With layout text_blocks: blocks in Figma order, each with its own font/size/line-height/colour and
 * gap_before; content from the object fields (title = sh_title_raw/post title · meta/collection block =
 * the info rows of §4.8.2 #12: fixed order Period → Place of production / Findspot → Material →
 * Dimensions → Collection → Other information / Object biography, standard labels (700) + values (500,
 * or the weight Figma gives the value run), every field with a value (D42 · P-186), empty values
 * skipped, Section never shown · caption = sh_caption: caption.label / label_breaks, one <p> per
 * paragraph (any line break) with caption.paragraph_gap.px between (#11 · P-246), justified when Figma
 * says JUSTIFIED (#9) · paragraph_indent_nbsp) · plain_meta = text kept in the layout (lines[] or full
 * runs) · lorem_placeholder is never shown (P-213).
 * Without them (objects outside the 15 Final frames): title → metadata block (sh_meta_color set) or
 * Collection line → "Caption:" + paragraphs (generic rules, §2.2 levels).
 *
 * Args: post_id (int) · layout (sh_object_layout()).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args   = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_id     = isset( $sh_args['post_id'] ) ? (int) $sh_args['post_id'] : (int) get_the_ID();
$sh_layout = isset( $sh_args['layout'] ) && is_array( $sh_args['layout'] ) ? $sh_args['layout'] : array();

$sh_title_raw = trim( (string) sh_get( 'sh_title_raw', $sh_id ) );
$sh_title     = '' !== $sh_title_raw ? $sh_title_raw : get_the_title( $sh_id );
$sh_caption   = trim( (string) sh_get( 'sh_caption', $sh_id ) );
$sh_blocks    = array_values( array_filter( (array) ( $sh_layout['text_blocks'] ?? array() ), 'is_array' ) );
usort( $sh_blocks, static fn( $a, $b ) => (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ) );

$sh_hex = static function ( $v ): string {
	$c = is_string( $v ) ? sanitize_hex_color( $v ) : '';
	return is_string( $c ) ? $c : '';
};
$sh_n   = static fn( $v ): string => (string) round( is_numeric( $v ) ? (float) $v : 0.0, 3 );

/* Figma weight → CSS: Komet by PostScript name (tokens.css) · other families as in Figma. */
$sh_weight = static function ( $w, string $family = 'Komet' ): string {
	$map = array(
		400 => 'light',
		500 => 'regular',
		600 => 'medium',
		700 => 'bold',
		800 => 'heavy',
	);
	if ( 'Komet' !== $family ) {
		return (string) max( 100, min( 900, (int) $w ) );
	}
	return 'var(--sh-fw-komet-' . ( $map[ (int) $w ] ?? 'regular' ) . ')';
};
$sh_family = static function ( string $family ): string {
	$map = array(
		'Barlow Condensed' => 'var(--sh-font-barlow-condensed)',
		'Roboto Flex'      => 'var(--sh-font-roboto-flex)',
	);
	return $map[ $family ] ?? 'var(--sh-font-komet)';
};
/* Inline custom properties of one block (font · colour · gap above). */
$sh_block_style = static function ( array $b, string $color ) use ( $sh_weight, $sh_family, $sh_n ): string {
	$f   = isset( $b['font'] ) && is_array( $b['font'] ) ? $b['font'] : array();
	$fam = isset( $f['family'] ) && is_string( $f['family'] ) ? $f['family'] : 'Komet';
	return '--sh-d-ff:' . $sh_family( $fam ) . ';--sh-d-fw:' . $sh_weight( $f['weight'] ?? 500, $fam )
		. ';--sh-d-fs:' . $sh_n( $f['size'] ?? 18 ) . ';--sh-d-lh:' . $sh_n( $f['line_height'] ?? 24 )
		. ';--sh-d-bgap:' . $sh_n( $b['gap_before'] ?? 0 ) . ';' . ( '' !== $color ? 'color:' . $color . ';' : '' );
};
/* One styled run of text. */
$sh_run = static fn( string $text, $weight, string $color, string $cls = '' ): string => '<span class="' . esc_attr( trim( 'sh-detail__run ' . $cls ) ) . '" style="'
	. esc_attr( 'font-weight:' . $sh_weight( $weight ) . ';' . ( '' !== $color ? 'color:' . $color . ';' : '' ) ) . '">' . esc_html( $text ) . '</span>';

/* Info rows under the title (DESIGN-SPEC §4.8.2 #12): fixed order · standard labels · no Section line. */
$sh_info_rows = array(
	'period'              => __( 'Period:', 'sharedheritage' ),
	'place_of_production' => __( 'Place of production / Findspot:', 'sharedheritage' ),
	'material'            => __( 'Material:', 'sharedheritage' ),
	'dimensions'          => __( 'Dimensions:', 'sharedheritage' ),
	'collection'          => __( 'Collection:', 'sharedheritage' ),
	'object_biography'    => __( 'Other information / Object biography:', 'sharedheritage' ),
);

if ( ! $sh_blocks ) {
	get_template_part( 'template-parts/detail/text-generic', null, array( 'post_id' => $sh_id ) );
	return;
}
?>
<div class="sh-detail__text sh-detail__text--blocks">
	<?php
	$sh_has_title = false;
	foreach ( $sh_blocks as $sh_b ) {
		$sh_role = (string) ( $sh_b['role'] ?? '' );
		$sh_col  = $sh_hex( $sh_b['color'] ?? '' );

		if ( 'title' === $sh_role && ! $sh_has_title ) {
			$sh_has_title = true;
			$sh_auto      = 'WIDTH_AND_HEIGHT' === ( $sh_b['auto_resize'] ?? '' ) ? ' sh-detail__title--auto' : '';
			/*
			 * D42 (P-185): the Figma title box may be wider than the text column when every panel image
			 * starts below it (1633 · 1506 · 1509) → that width; otherwise / when it would still hit an
			 * image or the screen edge (detail.js adds --fit) it wraps in the column (D28).
			 */
			$sh_wide_w = 0.0;
			$sh_tbox   = isset( $sh_b['bbox'] ) && is_array( $sh_b['bbox'] ) ? array_values( $sh_b['bbox'] ) : array();
			$sh_tcol_w = $sh_layout['text_col']['bbox'][2] ?? null;
			$sh_img_y  = array();
			foreach ( (array) ( $sh_layout['images'] ?? array() ) as $sh_img ) {
				if ( is_array( $sh_img ) && isset( $sh_img['bbox'][1] ) && is_numeric( $sh_img['bbox'][1] ) ) {
					$sh_img_y[] = (float) $sh_img['bbox'][1];
				}
			}
			if ( 4 === count( $sh_tbox ) && is_numeric( $sh_tbox[0] ) && is_numeric( $sh_tbox[1] ) && is_numeric( $sh_tbox[2] ) && is_numeric( $sh_tbox[3] )
				&& is_numeric( $sh_tcol_w ) && (float) $sh_tbox[2] > (float) $sh_tcol_w + 0.5
				&& $sh_img_y && min( $sh_img_y ) >= (float) $sh_tbox[1] + (float) $sh_tbox[3] ) {
				$sh_wide_w = min( (float) $sh_tbox[2], 1120.0 - max( 0.0, (float) $sh_tbox[0] - 160.0 ) ); // never past the 1120 container.
			}
			printf(
				'<h1 id="sh-detail-title" class="sh-detail__block sh-detail__title%1$s" style="%2$s">%3$s</h1>',
				esc_attr( $sh_auto . ( $sh_wide_w > 0 ? ' sh-detail__title--wide' : '' ) ),
				esc_attr( $sh_block_style( $sh_b, '' !== $sh_hex( sh_get( 'sh_title_color', $sh_id ) ) ? '' : $sh_col ) . ( $sh_wide_w > 0 ? '--sh-d-title-w:' . $sh_n( $sh_wide_w ) . ';' : '' ) ), // field colour (CSS var) wins.
				esc_html( $sh_title )
			);
		} elseif ( ( 'meta' === $sh_role || 'collection' === $sh_role ) && ! empty( $sh_b['rows'] ) && is_array( $sh_b['rows'] ) ) {
			// Figma rows by field (colours · weights) — D42 (P-186): every field with a value is shown, not only these.
			$sh_by_field = array();
			foreach ( $sh_b['rows'] as $sh_row ) {
				$sh_field = is_array( $sh_row ) ? (string) ( $sh_row['field'] ?? '' ) : '';
				if ( isset( $sh_info_rows[ $sh_field ] ) && ! isset( $sh_by_field[ $sh_field ] ) ) {
					$sh_by_field[ $sh_field ] = $sh_row;
				}
			}
			// Weight of the value run after each label run.
			$sh_after = array();
			$sh_runs  = isset( $sh_b['runs'] ) && is_array( $sh_b['runs'] ) ? array_values( $sh_b['runs'] ) : array();
			foreach ( $sh_runs as $sh_k => $sh_r ) {
				if ( ! is_array( $sh_r ) || ! isset( $sh_r['text'] ) || ! is_string( $sh_r['text'] ) || '' === trim( $sh_r['text'] ) ) {
					continue;
				}
				// Next run with visible text (1510 has a lone " " run in another weight between label and value).
				for ( $sh_n2 = $sh_k + 1; isset( $sh_runs[ $sh_n2 ] ); $sh_n2++ ) {
					$sh_nx = $sh_runs[ $sh_n2 ];
					$sh_tx = is_array( $sh_nx ) ? (string) ( $sh_nx['text'] ?? ( $sh_nx['text_head'] ?? 'x' ) ) : '';
					if ( '' !== trim( $sh_tx ) ) {
						if ( isset( $sh_nx['weight'] ) ) {
							$sh_after[ trim( $sh_r['text'] ) ] = $sh_nx['weight'];
						}
						break;
					}
				}
			}
			// Rows Figma does not list: weights of the block's first row, colours = block colour.
			$sh_first = reset( $sh_by_field );
			$sh_first = is_array( $sh_first ) ? $sh_first : array();
			$sh_def   = array(
				'label_weight' => $sh_first['label_weight'] ?? 700,
				'value_weight' => $sh_after[ trim( (string) ( $sh_first['label_in_figma'] ?? $sh_first['label'] ?? '' ) ) ] ?? ( $sh_first['value_weight'] ?? 500 ),
			);
			$sh_out = '';
			foreach ( $sh_info_rows as $sh_field => $sh_label ) {
				$sh_value = trim( (string) sh_get( 'sh_' . $sh_field, $sh_id ) );
				if ( '' === $sh_value ) {
					continue;
				}
				$sh_row  = $sh_by_field[ $sh_field ] ?? $sh_def;
				$sh_fig  = trim( (string) ( $sh_row['label_in_figma'] ?? $sh_row['label'] ?? '' ) );
				$sh_vw   = $sh_after[ $sh_fig ] ?? ( $sh_row['value_weight'] ?? 500 );
				$sh_out .= '<span class="sh-detail__row">'
					. $sh_run( $sh_label, $sh_row['label_weight'] ?? 700, $sh_hex( $sh_row['label_color'] ?? '' ) ) . ' '
					. $sh_run( $sh_value, $sh_vw, $sh_hex( $sh_row['value_color'] ?? '' ) ) . '</span>';
			}
			if ( '' !== $sh_out ) {
				$sh_default = 'meta' === $sh_role ? 'var(--sh-d-meta, inherit)' : 'var(--sh-d-coll, inherit)';
				// Runs are escaped by $sh_run().
				echo '<p class="sh-detail__block sh-detail__meta" style="' . esc_attr( $sh_block_style( $sh_b, '' !== $sh_col ? $sh_col : $sh_default ) ) . '">' . $sh_out . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		} elseif ( 'plain_meta' === $sh_role ) {
			$sh_out = '';
			if ( ! empty( $sh_b['lines'] ) && is_array( $sh_b['lines'] ) ) {
				foreach ( $sh_b['lines'] as $sh_line ) {
					$sh_bul  = is_array( $sh_line ) && 'NONE' !== ( $sh_line['list'] ?? 'NONE' );
					$sh_out .= '<span class="sh-detail__line' . ( $sh_bul ? ' sh-detail__line--bullet' : '' ) . '" style="--sh-d-indent:'
						. esc_attr( (string) max( 0, (int) ( $sh_line['indent'] ?? 0 ) ) ) . '">' . esc_html( (string) ( $sh_line['text'] ?? '' ) ) . '</span>';
				}
			} elseif ( ! empty( $sh_b['runs'] ) && is_array( $sh_b['runs'] ) ) {
				foreach ( $sh_b['runs'] as $sh_r ) {
					if ( ! is_array( $sh_r ) || ! isset( $sh_r['text'] ) ) { // text_head only = truncated copy: show nothing.
						$sh_out = '';
						break;
					}
					$sh_out .= $sh_run( (string) $sh_r['text'], $sh_r['weight'] ?? 500, $sh_hex( $sh_r['color'] ?? '' ) );
				}
			}
			if ( '' !== $sh_out ) {
				// Lines/runs are escaped above.
				echo '<p class="sh-detail__block sh-detail__plain" style="' . esc_attr( $sh_block_style( $sh_b, $sh_col ) ) . '">' . $sh_out . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		} elseif ( 'caption' === $sh_role && '' !== $sh_caption ) {
			$sh_cap    = isset( $sh_layout['caption'] ) && is_array( $sh_layout['caption'] ) ? $sh_layout['caption'] : array();
			$sh_info   = isset( $sh_layout['info_layout'] ) && is_array( $sh_layout['info_layout'] ) ? $sh_layout['info_layout'] : array();
			$sh_font   = isset( $sh_b['font'] ) && is_array( $sh_b['font'] ) ? $sh_b['font'] : array();
			$sh_label  = array_key_exists( 'label', $sh_cap ) ? $sh_cap['label'] : __( 'Caption:', 'sharedheritage' );
			$sh_breaks = isset( $sh_cap['label_breaks'] ) ? max( 0, min( 3, (int) $sh_cap['label_breaks'] ) ) : 2;
			$sh_indent = isset( $sh_cap['paragraph_indent_nbsp'] ) && is_array( $sh_cap['paragraph_indent_nbsp'] ) ? array_values( $sh_cap['paragraph_indent_nbsp'] ) : array();
			$sh_lh     = is_numeric( $sh_font['line_height'] ?? null ) ? (float) $sh_font['line_height'] : 24.0;
			// #11: paragraphs one blank line apart (24px) — every break in the text, single "\n" too (P-246).
			$sh_pgap = $sh_cap['paragraph_gap']['px'] ?? ( $sh_info['caption_paragraph_gap_px'] ?? $sh_lh );
			$sh_pgap = is_numeric( $sh_pgap ) ? max( 0.0, (float) $sh_pgap ) : $sh_lh;
			// #9: Figma JUSTIFIED → text-align: justify · hyphens: auto.
			$sh_just = 'JUSTIFIED' === ( $sh_info['caption_align'] ?? ( $sh_font['align'] ?? '' ) );
			$sh_paras = preg_split( '/[ \t]*\R(?:[ \t]*\R)*/u', $sh_caption );
			$sh_paras = array_values( array_filter( array_map( 'rtrim', is_array( $sh_paras ) ? $sh_paras : array() ), static fn( $p ) => '' !== trim( $p ) ) );
			$sh_body_c = $sh_hex( sh_get( 'sh_text_color', $sh_id ) );
			$sh_body_c = '' !== $sh_body_c ? $sh_body_c : $sh_hex( $sh_cap['body_color'] ?? '' );
			$sh_head   = is_string( $sh_label ) && '' !== $sh_label
				? $sh_run( $sh_label, $sh_cap['label_weight'] ?? 700, $sh_hex( $sh_cap['label_color'] ?? '' ), 'sh-detail__caption-label' )
				: '';
			$sh_body = '';
			foreach ( $sh_paras as $sh_i => $sh_para ) {
				$sh_pad   = isset( $sh_indent[ $sh_i ] ) ? max( 0, min( 40, (int) $sh_indent[ $sh_i ] ) ) : 0;
				$sh_text  = str_repeat( '&nbsp;', $sh_pad ) . esc_html( $sh_para );
				$sh_body .= 0 === $sh_i && '' !== $sh_head && 0 === $sh_breaks
					? '<p>' . $sh_head . ' ' . $sh_text . '</p>' // label on the first line.
					: '<p>' . $sh_text . '</p>';
			}
			if ( '' !== $sh_head && $sh_breaks > 0 ) {
				$sh_body = '<p class="sh-detail__caption-head">' . $sh_head . '</p>' . $sh_body;
			}
			$sh_style = $sh_block_style( $sh_b, $sh_body_c ) . '--sh-d-fw:' . $sh_weight( $sh_cap['body_weight'] ?? 400 ) . ';'
				. '--sh-d-pgap:' . $sh_n( $sh_pgap ) . ';--sh-d-lgap:' . $sh_n( max( 0, $sh_breaks - 1 ) * $sh_lh ) . ';';
			// Label run and paragraphs are escaped above.
			echo '<div class="sh-detail__block sh-detail__caption' . ( $sh_just ? ' sh-detail__caption--justify' : '' ) . '" style="' . esc_attr( $sh_style ) . '">' . $sh_body . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
	if ( ! $sh_has_title ) {
		echo '<h1 id="sh-detail-title" class="screen-reader-text">' . esc_html( $sh_title ) . '</h1>';
	}
	?>
</div>
