<?php
/**
 * One Introduction chapter (DESIGN-SPEC §9.4 C map + D content) — map, then the sections of sh_intro_ch<n>_layout.
 * Text blocks read sh_intro_ch<n>_text_<slot> · picture blocks sh_intro_ch<n>_image_<slot> (+ _animation) through
 * sh_intro_chapters(). Geometry (x, y inside the section, width, picture aspect) is inline custom properties from the
 * layout; assets/css/intro.css places the blocks at ≥1280 and stacks them below. "decor" blocks (crop pictures of the
 * mock-up scrollbar) are not rendered (P-273).
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
		$sh_sec_st = sh_intro_vars(
			array(
				'h'  => sh_intro_num( $sh_sec['rel'][3] ),
				'bg' => is_string( $sh_sec_bg ) ? $sh_sec_bg : '',
			)
		);
		?>
		<section class="sh-intro__sec" style="<?php echo esc_attr( $sh_sec_st ); ?>">
			<div class="sh-intro__sec-in">
				<?php
				foreach ( (array) ( $sh_sec['blocks'] ?? array() ) as $sh_b ) :
					if ( ! is_array( $sh_b ) || ! isset( $sh_b['rel'][0], $sh_b['rel'][1], $sh_b['box'][0], $sh_b['box'][1] ) ) {
						continue;
					}
					$sh_kind = (string) ( $sh_b['kind'] ?? '' );
					$sh_slot = (int) ( $sh_b['slot'] ?? 0 );
					$sh_geo  = array(
						'x'  => sh_intro_num( $sh_b['rel'][0] ),
						'y'  => sh_intro_num( (float) $sh_b['rel'][1] - $sh_sec_y ),
						'bw' => sh_intro_num( $sh_b['box'][0] ),
						'bh' => sh_intro_num( $sh_b['box'][1] ),
					);

					if ( 'text' === $sh_kind && isset( $sh_ch['texts'][ $sh_slot ] ) ) :
						$sh_is_h = isset( $sh_b['role'] ) && 'heading' === $sh_b['role'];
						$sh_st   = sh_intro_vars( $sh_geo + sh_intro_font_vars( $sh_b['font'] ?? array() ) );
						if ( $sh_is_h ) :
							?>
							<h3 class="sh-intro__block sh-intro__h" style="<?php echo esc_attr( $sh_st ); ?>"><?php echo wp_kses_post( implode( ' ', (array) $sh_ch['texts'][ $sh_slot ] ) ); ?></h3>
						<?php else : ?>
							<div class="sh-intro__block sh-intro__p" style="<?php echo esc_attr( $sh_st ); ?>">
								<?php foreach ( (array) $sh_ch['texts'][ $sh_slot ] as $sh_para ) : ?>
									<p><?php echo wp_kses_post( $sh_para ); ?></p>
								<?php endforeach; ?>
							</div>
							<?php
						endif;
					elseif ( 'image' === $sh_kind && empty( $sh_b['decor'] ) && isset( $sh_ch['images'][ $sh_slot ] ) ) :
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
						if ( '' !== $sh_html ) :
							?>
							<div class="sh-intro__block sh-intro__fig<?php echo $sh_fit ? ' sh-intro__fig--fit' : ''; ?>" style="<?php echo esc_attr( sh_intro_vars( $sh_geo ) ); ?>"><?php echo $sh_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() escapes. ?></div>
							<?php
						endif;
					endif;
				endforeach;
				?>
			</div>
		</section>
	<?php endforeach; ?>
</article>
