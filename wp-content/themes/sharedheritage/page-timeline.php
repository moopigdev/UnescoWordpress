<?php
/**
 * Timeline (Zone 4) v3 — Section 50 / Google Slides 31–54 (D26 · D27 · DESIGN-SPEC §4.5c · §6.4c).
 *
 * One stage (1440×723 under the header, scaled by k to fit the screen — §4.5c.5):
 *   trail icons ×17 · mini-map (15 thumbnails + cream viewport frame) · ‹ › · fixed time line ·
 *   objects on the line (all move together; the one nearest the centre grows + gets its title) ·
 *   under the line: the centred object's photo + metadata (fixed at the centre, fade in by position).
 * State = c (continuous index of the object at the centre), driven by assets/js/timeline.js.
 * Content: sh_timeline_items() by sh_card_order (CONTENT-MODEL §3) · layout constants: template-parts/timeline/geometry.php.
 * Reflection hidden (D16) · Back §3.3 (D27) · no Read more (title + photo of the centre object link to the object).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/template-parts/timeline/helpers.php';

get_header();

$sh_page_id = (int) get_queried_object_id();
$sh_api     = function_exists( 'sh_get' ) && function_exists( 'sh_timeline_items' ) && function_exists( 'sh_trails' );
$sh_trail   = null;
$sh_posts   = array();
$sh_nav     = array();
if ( $sh_api ) {
	$sh_term  = get_term( absint( sh_get( 'sh_timeline_trail', $sh_page_id ) ), 'trail' );
	$sh_trail = $sh_term instanceof WP_Term ? $sh_term : null;
	$sh_posts = $sh_trail ? sh_timeline_items( $sh_trail ) : array();
	$sh_nav   = sh_trails( 'timeline_nav' );
}

$sh_geo   = include __DIR__ . '/template-parts/timeline/geometry.php';
$sh_geo   = is_array( $sh_geo ) ? $sh_geo : array();
$sh_items = array();
foreach ( $sh_posts as $sh_post ) {
	$sh_data = sh_tl_item( $sh_post, $sh_geo );
	if ( $sh_data ) {
		$sh_items[] = $sh_data;
	}
}
$sh_count = count( $sh_items );
$sh_label = $sh_trail ? sprintf( /* translators: %s: trail name */ __( '%s timeline', 'sharedheritage' ), $sh_trail->name ) : __( 'Timeline', 'sharedheritage' );
?>
<section class="sh-tl" aria-roledescription="carousel" aria-label="<?php echo esc_attr( $sh_label ); ?>" data-sh-tl style="--n:<?php echo esc_attr( (string) $sh_count ); ?>">
	<h1 class="screen-reader-text"><?php echo esc_html( get_the_title( $sh_page_id ) ); ?></h1>
	<div class="sh-tl__fit">
		<div class="sh-tl__stage" tabindex="0" aria-label="<?php esc_attr_e( 'Use the left and right arrow keys to move along the timeline', 'sharedheritage' ); ?>" data-sh-tl-stage>
			<div class="sh-tl__top">
				<?php if ( ! empty( $sh_nav ) ) : ?>
					<ul class="sh-tl__trails" aria-label="<?php esc_attr_e( 'Trails', 'sharedheritage' ); ?>">
						<?php
						foreach ( $sh_nav as $sh_nav_term ) :
							$sh_icon = absint( sh_get_term( 'sh_icon_timeline', $sh_nav_term ) );
							$sh_img  = $sh_icon ? wp_get_attachment_image( $sh_icon, 'full', false, array( 'class' => 'sh-tl__trail-img', 'alt' => $sh_nav_term->name, 'loading' => 'eager', 'decoding' => 'async', 'draggable' => 'false', 'sizes' => '26px' ) ) : '';
							$sh_img  = '' !== $sh_img ? $sh_img : '<span class="sh-tl__trail-img screen-reader-text">' . esc_html( $sh_nav_term->name ) . '</span>';
							$sh_link = sh_trail_objects( $sh_nav_term ) ? get_term_link( $sh_nav_term ) : '';
							$sh_cur  = $sh_trail && $sh_trail->term_id === $sh_nav_term->term_id;
							?>
							<li class="sh-tl__trail">
								<?php if ( is_string( $sh_link ) && '' !== $sh_link ) : ?>
									<a class="sh-tl__trail-link" href="<?php echo esc_url( $sh_link ); ?>"<?php echo $sh_cur ? ' aria-current="page"' : ''; ?>><?php echo $sh_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() / esc_html() above. ?></a>
								<?php else : ?>
									<span class="sh-tl__trail-link"><?php echo $sh_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- as above. ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $sh_count ) : ?>
					<div class="sh-tl__mini" data-sh-tl-mini>
						<span class="sh-tl__mini-frame" aria-hidden="true"></span>
						<span class="sh-tl__mini-line" aria-hidden="true"></span>
						<ol class="sh-tl__mini-list" aria-hidden="true">
							<?php foreach ( $sh_items as $sh_i => $sh_item ) : ?>
								<li class="sh-tl__mini-item" style="<?php echo esc_attr( sh_tl_style( array( '--i' => (string) ( $sh_i + 1 ), '--mw' => sh_tl_num( $sh_item['mini'][0] ), '--ar' => sh_tl_num( $sh_item['ratio'] ) ) ) ); ?>">
									<button class="sh-tl__mini-btn" type="button" tabindex="-1" data-sh-tl-go="<?php echo esc_attr( (string) ( $sh_i + 1 ) ); ?>"><?php echo sh_tl_pic( $sh_item, (float) $sh_item['mini'][0], '', 'eager' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built in sh_tl_pic() (escaped there). ?></button>
								</li>
							<?php endforeach; ?>
						</ol>
					</div>

					<button class="sh-tl__arrow sh-tl__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous object', 'sharedheritage' ); ?>" data-sh-tl-prev>
						<svg viewBox="0 0 25 48" aria-hidden="true" focusable="false"><polyline points="23.5,1.5 2,24 23.5,46.5" fill="none" stroke="currentColor" stroke-width="3"/></svg>
					</button>
					<button class="sh-tl__arrow sh-tl__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next object', 'sharedheritage' ); ?>" data-sh-tl-next>
						<svg viewBox="0 0 25 48" aria-hidden="true" focusable="false"><polyline points="1.5,1.5 23,24 1.5,46.5" fill="none" stroke="currentColor" stroke-width="3"/></svg>
					</button>

					<div class="sh-tl__track" data-sh-tl-track>
						<span class="sh-tl__line" aria-hidden="true"></span>
						<ol class="sh-tl__items">
							<?php foreach ( $sh_items as $sh_i => $sh_item ) : ?>
								<li class="sh-tl__item" style="<?php echo esc_attr( sh_tl_style( array( '--i' => (string) ( $sh_i + 1 ), '--rw' => sh_tl_num( $sh_item['rest'][0] ), '--ar' => sh_tl_num( $sh_item['ratio'] ), '--s' => sh_tl_num( $sh_item['scale'] ) ) ) ); ?>" data-sh-tl-item>
									<p class="sh-tl__title">
										<?php if ( '' !== $sh_item['url'] ) : ?>
											<a href="<?php echo esc_url( $sh_item['url'] ); ?>" tabindex="-1"><?php echo nl2br( esc_html( $sh_item['title'] ), false ); ?></a>
										<?php else : ?>
											<span><?php echo nl2br( esc_html( $sh_item['title'] ), false ); ?></span>
										<?php endif; ?>
									</p>
									<span class="sh-tl__obj" data-sh-tl-go="<?php echo esc_attr( (string) ( $sh_i + 1 ) ); ?>"><?php echo sh_tl_pic( $sh_item, $sh_item['rest'][0] * $sh_item['scale'], $sh_item['title'], $sh_i < 5 ? 'eager' : 'lazy', ( 1 === $sh_i || 2 === $sh_i ) ? 'high' : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built in sh_tl_pic() (escaped there). ?></span>
								</li>
							<?php endforeach; ?>
						</ol>
						<ol class="sh-tl__dots" aria-label="<?php esc_attr_e( 'Objects on the timeline', 'sharedheritage' ); ?>">
							<?php foreach ( $sh_items as $sh_i => $sh_item ) : ?>
								<li class="sh-tl__dot-item" style="--i:<?php echo esc_attr( (string) ( $sh_i + 1 ) ); ?>">
									<button class="sh-tl__dot" type="button" aria-label="<?php echo esc_attr( $sh_item['title'] ); ?>" data-sh-tl-go="<?php echo esc_attr( (string) ( $sh_i + 1 ) ); ?>" data-sh-tl-dot></button>
								</li>
							<?php endforeach; ?>
						</ol>
					</div>
				<?php endif; ?>
			</div>

			<div class="sh-tl__below">
				<?php foreach ( $sh_items as $sh_i => $sh_item ) : ?>
					<div class="sh-tl__info" style="<?php echo esc_attr( sh_tl_style( array( '--pw' => sh_tl_num( $sh_item['photo'][0] ), '--ar' => sh_tl_num( $sh_item['ratio'] ) ) ) ); ?>" data-sh-tl-info data-title="<?php echo esc_attr( $sh_item['title'] ); ?>" data-period="<?php echo esc_attr( $sh_item['period'] ); ?>">
						<?php
						$sh_pic = sh_tl_pic( $sh_item, (float) $sh_item['photo'][0], '', 'lazy' );
						if ( '' !== $sh_item['url'] ) {
							echo '<a class="sh-tl__photo" href="' . esc_url( $sh_item['url'] ) . '" tabindex="-1" aria-hidden="true">' . $sh_pic . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $sh_pic built in sh_tl_pic().
						} else {
							echo '<span class="sh-tl__photo">' . $sh_pic . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- as above.
						}
						?>
						<?php if ( ! empty( $sh_item['lines'] ) ) : ?>
							<p class="sh-tl__meta"><?php echo implode( '<br>', array_map( 'esc_html', $sh_item['lines'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- each line escaped. ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<?php
	get_template_part(
		'template-parts/common/back-button',
		null,
		array(
			'class' => 'sh-tl__back',
			'label' => function_exists( 'sh_get_option' ) ? (string) sh_get_option( 'sh_label_back' ) : '',
		)
	);
	?>
	<p class="screen-reader-text" aria-live="polite" data-sh-tl-live></p>
</section>
<?php
get_footer();
