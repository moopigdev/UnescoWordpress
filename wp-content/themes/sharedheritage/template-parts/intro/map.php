<?php
/**
 * Map slider (Frame 12767 · DESIGN-SPEC §4.2 / §4.7): map picture · ‹ › arrows · number buttons 1–4 ·
 * popup (opens from a number button, Figma ON_CLICK overlay) · slide text under the map.
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

			<?php foreach ( $sh_slides as $sh_i => $sh_slide ) : ?>
				<div class="sh-intro__popup" id="sh-intro-popup-<?php echo esc_attr( (string) $sh_slide['n'] ); ?>" role="dialog" aria-labelledby="sh-intro-popup-title-<?php echo esc_attr( (string) $sh_slide['n'] ); ?>" data-sh-map-popup="<?php echo esc_attr( (string) $sh_i ); ?>" hidden>
					<button class="sh-intro__popup-close" type="button" data-sh-map-close aria-label="<?php esc_attr_e( 'Close', 'sharedheritage' ); ?>">
						<svg width="8.59892" height="8.572" viewBox="0 0 8.59892 8.572" aria-hidden="true" focusable="false"><path fill="currentColor" d="M8.36971 1.29971C8.44137 1.23056 8.49855 1.14783 8.5379 1.05634C8.57725 0.964857 8.59798 0.866452 8.59889 0.766868C8.5998 0.667285 8.58088 0.568516 8.54321 0.476327C8.50554 0.384137 8.44989 0.300372 8.37951 0.22992C8.30912 0.159467 8.22541 0.103738 8.13325 0.0659841C8.0411 0.02823 7.94235 0.00920731 7.84276 0.0100254C7.74318 0.0108435 7.64476 0.0314862 7.55323 0.0707492C7.46171 0.110012 7.37893 0.167109 7.30971 0.238708L4.30371 3.24371L1.29871 0.238708C1.23005 0.165022 1.14725 0.105919 1.05525 0.0649275C0.963247 0.0239355 0.863934 0.0018935 0.763231 0.00011672C0.662528 -0.00166006 0.562499 0.0168648 0.469111 0.0545858C0.375723 0.0923068 0.290889 0.148451 0.21967 0.21967C0.148451 0.290889 0.0923068 0.375722 0.0545858 0.46911C0.0168648 0.562499 -0.00166006 0.662528 0.00011672 0.763231C0.0018935 0.863934 0.0239355 0.963247 0.0649275 1.05525C0.105919 1.14725 0.165022 1.23005 0.238708 1.29871L3.24171 4.30471L0.236709 7.30971C0.104229 7.45188 0.0321051 7.63993 0.0355333 7.83423C0.0389615 8.02853 0.117674 8.21392 0.255087 8.35133C0.3925 8.48874 0.577885 8.56745 0.772186 8.57088C0.966487 8.57431 1.15453 8.50219 1.29671 8.36971L4.30371 5.36471L7.30871 8.37071C7.45088 8.50319 7.63893 8.57531 7.83323 8.57188C8.02753 8.56846 8.21292 8.48974 8.35033 8.35233C8.48774 8.21492 8.56645 8.02953 8.56988 7.83523C8.57331 7.64093 8.50119 7.45288 8.36871 7.31071L5.36571 4.30471L8.36971 1.29971Z"/></svg>
					</button>
					<h3 class="sh-t-popup-title" id="sh-intro-popup-title-<?php echo esc_attr( (string) $sh_slide['n'] ); ?>"><?php echo esc_html( (string) $sh_slide['title'] ); ?></h3>
					<?php
					get_template_part(
						'template-parts/intro/paragraphs',
						null,
						array(
							'text'  => (string) $sh_slide['body'],
							'class' => 'sh-t-popup-body sh-intro__popup-p',
						)
					);
					?>
				</div>
			<?php endforeach; ?>
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
