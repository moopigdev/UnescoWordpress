<?php
/**
 * Timeline (Zone 4) — Figma `670:1551` "Desktop - zone 4" · DESIGN-SPEC §4.5 · §6.4 · CONTENT-MODEL §3 / §8.
 *
 * Header (with rule) · trail icon row `797:2110` (sh_trails('timeline_nav'), current trail 45×45) ·
 * horizontal scroller `Frame 114443` (Figma overflow HORIZONTAL_SCROLLING): illustration `1232:1711`
 * (one raster image, D8) above the card row `Frame 114464` (sh_timeline_items(), by sh_card_order)
 * (Reflection `1232:1807` is hidden in Figma → not rendered, D16) · Back `[160,1476]` outside the scroller.
 * No walking elephant (D12).
 * Scrolling: native + wheel / mouse drag / ‹ › buttons / keyboard (assets/js/timeline.js).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/template-parts/timeline/helpers.php';

get_header();

$sh_page_id = (int) get_queried_object_id();
$sh_api     = function_exists( 'sh_get' ) && function_exists( 'sh_timeline_items' ) && function_exists( 'sh_trails' );

$sh_trail = null;
$sh_items = array();
$sh_nav   = array();
$sh_ill   = 0;
if ( $sh_api ) {
	$sh_trail_id = absint( sh_get( 'sh_timeline_trail', $sh_page_id ) );
	$sh_term     = $sh_trail_id ? get_term( $sh_trail_id, 'trail' ) : null;
	$sh_trail    = $sh_term instanceof WP_Term ? $sh_term : null;
	$sh_items    = $sh_trail ? sh_timeline_items( $sh_trail ) : array();
	$sh_nav      = sh_trails( 'timeline_nav' );
	$sh_ill      = absint( sh_get( 'sh_timeline_illustration', $sh_page_id ) );
}

$sh_geo       = include __DIR__ . '/template-parts/timeline/geometry.php';
$sh_geo       = is_array( $sh_geo ) ? $sh_geo : array();
$sh_geo_cards = isset( $sh_geo['cards'] ) && is_array( $sh_geo['cards'] ) ? $sh_geo['cards'] : array();
$sh_geo_ill   = isset( $sh_geo['illustration'] ) && is_array( $sh_geo['illustration'] ) ? $sh_geo['illustration'] : array();
?>
<div class="sh-tl" data-sh-timeline>
	<h1 class="screen-reader-text"><?php echo esc_html( get_the_title( $sh_page_id ) ); ?></h1>

	<?php if ( ! empty( $sh_nav ) ) : ?>
		<ul class="sh-tl__trails" aria-label="<?php esc_attr_e( 'Trails', 'sharedheritage' ); ?>">
			<?php
			foreach ( $sh_nav as $sh_nav_term ) :
				$sh_is_current = $sh_trail && $sh_trail->term_id === $sh_nav_term->term_id;
				$sh_icon       = absint( sh_get_term( 'sh_icon_timeline', $sh_nav_term ) );
				$sh_icon_html  = $sh_icon ? wp_get_attachment_image(
					$sh_icon,
					'full',
					false,
					array(
						'class'     => 'sh-tl__trail-icon',
						'alt'       => $sh_nav_term->name,
						'loading'   => 'eager',
						'decoding'  => 'async',
						'draggable' => 'false',
					)
				) : '';
				?>
				<li class="sh-tl__trail<?php echo $sh_is_current ? ' is-current' : ''; ?>"<?php echo $sh_is_current ? ' aria-current="true"' : ''; ?>>
					<?php
					if ( '' !== $sh_icon_html ) {
						echo $sh_icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() escapes its attributes.
					} else {
						echo '<span class="sh-tl__trail-icon sh-tl__trail-icon--empty">' . esc_html( $sh_nav_term->name ) . '</span>';
					}
					?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<div class="sh-tl__viewport" tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Timeline', 'sharedheritage' ); ?>" data-sh-timeline-viewport>
		<div class="sh-tl__track">
			<div class="sh-tl__ill">
				<div class="sh-tl__ill-inner">
					<?php
					// Above the fold: load eagerly. Missing image → the box keeps its size (DOD B6).
					echo sh_timeline_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sh_timeline_image().
						$sh_ill,
						$sh_geo_ill,
						'sh-tl__ill-img',
						array(
							'loading'       => 'eager',
							'fetchpriority' => 'high',
							'alt'           => $sh_trail ? sprintf( /* translators: %s: trail name */ __( 'Timeline of the %s trail', 'sharedheritage' ), $sh_trail->name ) : '',
						)
					);
					?>
				</div>
			</div>

			<ol class="sh-tl__row">
				<?php
				foreach ( $sh_items as $sh_item ) {
					get_template_part(
						'template-parts/timeline/card',
						null,
						array(
							'item' => $sh_item,
							'geo'  => isset( $sh_geo_cards[ $sh_item->post_name ] ) ? $sh_geo_cards[ $sh_item->post_name ] : array(),
						)
					);
				}
				?>
			</ol>
		</div>
	</div>

	<button class="sh-tl__arrow sh-tl__arrow--prev" type="button" hidden data-sh-timeline-prev>
		<svg width="24" height="48" viewBox="0 0 24 48" aria-hidden="true" focusable="false"><polyline points="22,2 2,24 22,46" fill="none" stroke="currentColor" stroke-width="2.5"/></svg>
		<span class="screen-reader-text"><?php esc_html_e( 'Previous', 'sharedheritage' ); ?></span>
	</button>
	<button class="sh-tl__arrow sh-tl__arrow--next" type="button" hidden data-sh-timeline-next>
		<svg width="24" height="48" viewBox="0 0 24 48" aria-hidden="true" focusable="false"><polyline points="2,2 22,24 2,46" fill="none" stroke="currentColor" stroke-width="2.5"/></svg>
		<span class="screen-reader-text"><?php esc_html_e( 'Next', 'sharedheritage' ); ?></span>
	</button>

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
</div>
<?php
get_footer();
