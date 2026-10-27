<?php
/**
 * Map slider (Frame 12767 · DESIGN-SPEC §4.2): map picture · ‹ › arrows · number buttons 1–4 · slide text under the map.
 * No popup/overlay (D21): number buttons and arrows only switch the slide.
 * Fields: sh_intro_map_image · sh_intro_map_slide_{1..4}_title / _body (CONTENT-MODEL §7). No pin coordinates exist
 * in the data (DEFERRED), so the number buttons are the pins. Behaviour: assets/js/intro.js (page works without JS: slide 1).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_page = isset( $sh_args['page_id'] ) ? (int) $sh_args['page_id'] : 0;

$sh_slides = array();
for ( $sh_n = 1; $sh_n <= 4; $sh_n++ ) {
	$sh_title = (string) sh_get( "sh_intro_map_slide_{$sh_n}_title", $sh_page );
	$sh_body  = (string) sh_get( "sh_intro_map_slide_{$sh_n}_body", $sh_page );
	if ( '' === $sh_title && '' === trim( $sh_body ) ) {
		continue;
	}
	$sh_slides[] = array(
		'n'     => $sh_n,
		'title' => $sh_title,
		'body'  => $sh_body,
	);
}
$sh_many = count( $sh_slides ) > 1;
$sh_map  = (int) sh_get( 'sh_intro_map_image', $sh_page );
?>
<section class="sh-intro__map-section" aria-label="<?php esc_attr_e( 'Map of Southeast Asia', 'sharedheritage' ); ?>" data-sh-map>
	<div class="sh-intro__map">
		<?php
		get_template_part(
			'template-parts/intro/image',
			null,
			array(
				'id'    => $sh_map,
				'class' => 'sh-intro__img',
				'sizes' => '(min-width: 1168px) 1120px, 100vw',
			)
		);
		?>

		<?php if ( $sh_many ) : ?>
			<button class="sh-intro__arrow sh-intro__arrow--prev" type="button" data-sh-map-prev aria-label="<?php esc_attr_e( 'Previous', 'sharedheritage' ); ?>">
				<svg width="18.4095" height="31.82" viewBox="0 0 18.4095 31.82" aria-hidden="true" focusable="false"><path fill="currentColor" fill-rule="evenodd" d="M17.6775 17.6775L3.535 31.82L0 28.285L12.375 15.91L0 3.535L3.535 0L17.6775 14.1425C18.1462 14.6113 18.4095 15.2471 18.4095 15.91C18.4095 16.5729 18.1462 17.2087 17.6775 17.6775Z"/></svg>
			</button>
			<button class="sh-intro__arrow sh-intro__arrow--next" type="button" data-sh-map-next aria-label="<?php esc_attr_e( 'Next', 'sharedheritage' ); ?>">
				<svg width="18.4095" height="31.82" viewBox="0 0 18.4095 31.82" aria-hidden="true" focusable="false"><path fill="currentColor" fill-rule="evenodd" d="M17.6775 17.6775L3.535 31.82L0 28.285L12.375 15.91L0 3.535L3.535 0L17.6775 14.1425C18.1462 14.6113 18.4095 15.2471 18.4095 15.91C18.4095 16.5729 18.1462 17.2087 17.6775 17.6775Z"/></svg>
			</button>
		<?php endif; ?>

		<?php if ( ! empty( $sh_slides ) ) : ?>
			<ul class="sh-intro__pins" role="list">
				<?php foreach ( $sh_slides as $sh_i => $sh_slide ) : ?>
					<li>
						<button class="sh-intro__pin sh-t-map-number<?php echo 0 === $sh_i ? ' is-active' : ''; ?>" type="button" data-sh-map-pin="<?php echo esc_attr( (string) $sh_i ); ?>" aria-controls="sh-intro-slide-<?php echo esc_attr( (string) $sh_slide['n'] ); ?>" aria-current="<?php echo 0 === $sh_i ? 'true' : 'false'; ?>">
							<span class="screen-reader-text"><?php esc_html_e( 'Slide', 'sharedheritage' ); ?> </span><?php echo esc_html( (string) $sh_slide['n'] ); ?>
						</button>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>

	<?php foreach ( $sh_slides as $sh_i => $sh_slide ) : ?>
		<div class="sh-intro__slide" id="sh-intro-slide-<?php echo esc_attr( (string) $sh_slide['n'] ); ?>" data-sh-map-slide="<?php echo esc_attr( (string) $sh_i ); ?>"<?php echo 0 === $sh_i ? '' : ' hidden'; ?>>
			<?php if ( '' !== $sh_slide['title'] ) : ?>
				<h2 class="sh-t-page-title sh-intro__title"><?php echo esc_html( (string) $sh_slide['title'] ); ?></h2>
			<?php endif; ?>
			<?php
			get_template_part(
				'template-parts/intro/paragraphs',
				null,
				array(
					'text'  => (string) $sh_slide['body'],
					'class' => 'sh-t-body sh-intro__summary',
				)
			);
			?>
		</div>
	<?php endforeach; ?>
</section>
