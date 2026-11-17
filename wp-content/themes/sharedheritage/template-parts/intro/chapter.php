<?php
/**
 * One Introduction chapter (DESIGN-SPEC §9.4 C map + D content) — map, then the sections of sh_intro_ch<n>_layout.
 * Text blocks read sh_intro_ch<n>_text_<slot> · picture blocks sh_intro_ch<n>_image_<slot> (+ _animation) through
 * sh_intro_chapters(). Figma geometry becomes flow (sh_intro_group_rows(): rows → columns → blocks, Figma gaps as margins,
 * section height from the content) so longer admin text pushes the rest down; assets/css/intro.css lays rows/columns out
 * at ≥1280 and stacks the blocks below. "decor" blocks (crop pictures of the mock-up scrollbar) are not rendered (P-273).
 *
 * Args: chapter (one item of sh_intro_chapters()).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_ch   = isset( $sh_args['chapter'] ) && is_array( $sh_args['chapter'] ) ? $sh_args['chapter'] : array();
$sh_n    = (int) ( $sh_ch['n'] ?? 0 );
$sh_lay  = isset( $sh_ch['layout'] ) && is_array( $sh_ch['layout'] ) ? $sh_ch['layout'] : array();
if ( $sh_n < 1 ) {
	return;
}

$sh_map     = isset( $sh_lay['map'] ) && is_array( $sh_lay['map'] ) ? $sh_lay['map'] : array();
$sh_map_fit = isset( $sh_map['fill_mode'] ) && 'FIT' === $sh_map['fill_mode'];
$sh_accent  = sanitize_hex_color( (string) ( $sh_ch['accent'] ?? '' ) );
$sh_map_img = sh_intro_img(
	(int) ( $sh_ch['map_image'] ?? 0 ),
	(int) ( $sh_ch['map_animation'] ?? 0 ),
	array(
		'class' => 'sh-intro__fill',
		'sizes' => '(min-width: 1440px) 1440px, 100vw',
	)
);
$sh_map_vars = sh_intro_vars(
	array(
		'bw' => sh_intro_num( $sh_map['box'][0] ?? null ),
		'bh' => sh_intro_num( $sh_map['box'][1] ?? null ),
	)
);
?>
<article class="sh-intro__chapter" id="chapter-<?php echo esc_attr( (string) $sh_n ); ?>" role="tabpanel" aria-labelledby="sh-intro-tab-<?php echo esc_attr( (string) $sh_n ); ?>" data-sh-chapter="<?php echo esc_attr( (string) $sh_n ); ?>" style="<?php echo esc_attr( sh_intro_vars( array( 'accent' => is_string( $sh_accent ) ? $sh_accent : '' ) ) ); ?>">
	<div class="sh-intro__map<?php echo $sh_map_fit ? ' sh-intro__map--fit' : ''; ?>" style="<?php echo esc_attr( $sh_map_vars ); ?>">
		<?php echo $sh_map_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() escapes. ?>
	</div>
	<?php
	foreach ( (array) ( $sh_lay['sections'] ?? array() ) as $sh_sec ) :
		if ( ! is_array( $sh_sec ) || ! isset( $sh_sec['rel'][1], $sh_sec['rel'][3] ) ) {
			continue;
		}
		$sh_sec_y  = (float) $sh_sec['rel'][1];
		$sh_sec_bg = sanitize_hex_color( (string) ( $sh_sec['bg'] ?? '' ) );

		// Blocks that have content (text slot / picture slot filled) · decor blocks never.
		$sh_items = array();
		foreach ( (array) ( $sh_sec['blocks'] ?? array() ) as $sh_b ) {
			if ( ! is_array( $sh_b ) || ! isset( $sh_b['rel'][0], $sh_b['rel'][1], $sh_b['box'][0], $sh_b['box'][1] ) ) {
				continue;
			}
			$sh_kind = (string) ( $sh_b['kind'] ?? '' );
			$sh_slot = (int) ( $sh_b['slot'] ?? 0 );
			$sh_item = array(
				'x'    => (float) $sh_b['rel'][0],
				'y'    => (float) $sh_b['rel'][1],
				'w'    => (float) $sh_b['box'][0],
				'h'    => (float) $sh_b['box'][1],
				'kind' => $sh_kind,
			);
			if ( 'text' === $sh_kind && isset( $sh_ch['texts'][ $sh_slot ] ) ) {
				$sh_item['heading'] = isset( $sh_b['role'] ) && 'heading' === $sh_b['role'];
				$sh_item['paras']   = (array) $sh_ch['texts'][ $sh_slot ];
				$sh_item['font']    = sh_intro_font_vars( $sh_b['font'] ?? array() );
			} elseif ( 'image' === $sh_kind && empty( $sh_b['decor'] ) && isset( $sh_ch['images'][ $sh_slot ] ) ) {
				$sh_img  = $sh_ch['images'][ $sh_slot ];
				$sh_fit  = isset( $sh_b['fill_mode'] ) && 'FIT' === $sh_b['fill_mode'];
				$sh_fill = isset( $sh_b['fill_mode'] ) && 'FILL' === $sh_b['fill_mode'];
				$sh_html = sh_intro_img(
					(int) $sh_img['id'],
					(int) $sh_img['animation'],
					array(
						'class' => $sh_fit || $sh_fill ? 'sh-intro__fill' : 'sh-intro__crop',
						'style' => $sh_fit || $sh_fill ? '' : sh_intro_crop_style( $sh_b['crop'] ?? null ),
						'sizes' => '(min-width: 1440px) ' . (int) $sh_b['box'][0] . 'px, 100vw',
					)
				);
				if ( '' === $sh_html ) {
					continue;
				}
				$sh_item['img'] = $sh_html;
				$sh_item['fit'] = $sh_fit;
			} else {
				continue;
			}
			$sh_items[] = $sh_item;
		}

		$sh_grid = sh_intro_group_rows( $sh_items, $sh_sec_y, (float) $sh_sec['rel'][3] );
		$sh_sec_st = sh_intro_vars(
			array(
				'pt' => sh_intro_num( $sh_grid['pt'] ),
				'pb' => sh_intro_num( $sh_grid['pb'] ),
				'bg' => is_string( $sh_sec_bg ) ? $sh_sec_bg : '',
			)
		);
		// Every echo below is built by sh_intro_block_markup() (escaped there).
		?>
		<section class="sh-intro__sec" style="<?php echo esc_attr( $sh_sec_st ); ?>">
			<div class="sh-intro__sec-in">
				<?php
				foreach ( $sh_grid['over'] as $sh_it ) {
					echo sh_intro_block_markup( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$sh_it,
						array(
							'x' => sh_intro_num( $sh_it['x'] ),
							'y' => sh_intro_num( $sh_it['y'] - $sh_sec_y ),
						)
					);
				}
				foreach ( $sh_grid['rows'] as $sh_row ) :
					?>
					<div class="sh-intro__row" style="<?php echo esc_attr( sh_intro_vars( array( 'mt' => sh_intro_num( $sh_row['mt'] ) ) ) ); ?>">
						<?php foreach ( $sh_row['cols'] as $sh_col ) : ?>
							<div class="sh-intro__col" style="<?php echo esc_attr( sh_intro_vars( array( 'ml' => sh_intro_num( $sh_col['ml'] ), 'cw' => sh_intro_num( $sh_col['w'] ) ) ) ); ?>">
								<?php
								foreach ( $sh_col['blocks'] as $sh_blk ) {
									echo sh_intro_block_markup( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										$sh_blk['item'],
										array(
											'mt' => sh_intro_num( $sh_blk['mt'] ),
											'ox' => sh_intro_num( $sh_blk['ox'] ),
										)
									);
								}
								?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endforeach; ?>
</article>
