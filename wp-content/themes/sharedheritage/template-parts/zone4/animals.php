<?php
/**
 * Zone 4 chooser: dashed-line background (`sh_cover_image` of the zone · [160,150 1120×540]) with the 17 trail animals
 * (`sh_chooser_image` of each trail) placed on it — DESIGN-SPEC §4.10 · seed `zone4-chooser.json` · D37 / D38 / D40.
 *
 * Positions are design geometry (theme constants, not content): PNG box [x, y, w] in the 1120×540 cover, from
 * §4.10 / `animals[].box_in_cover_1120` (png drawn at 0.14 × its pixels; height follows the image). Data decides
 * what is shown: a trail without `sh_chooser_image` is skipped; a trail with an image but no position here is not
 * placed (add its box below when a new animal is designed).
 * Link = the trail's `sh_timeline_page` (published) → only the elephant today; the others are not links (D37/D40).
 *
 * Args: zone (WP_Term).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_zone = isset( $sh_args['zone'] ) && $sh_args['zone'] instanceof WP_Term ? $sh_args['zone'] : null;
if ( ! $sh_zone ) {
	return;
}

// [x, y, w] of the PNG box inside the 1120×540 cover (DESIGN-SPEC §4.10 table, "png" column minus [160,150]).
$sh_boxes = array(
	'garuda'     => array( 54.6, 22.5, 131.5 ),
	'buffalo'    => array( 90.0, 143.9, 138.5 ),
	'naga'       => array( 286.3, 7.7, 145.3 ),
	'seashell'   => array( 477.0, 43.2, 133.4 ),
	'phoenix'    => array( 732.2, 53.8, 122.6 ),
	'hornbill'   => array( 891.9, 58.7, 111.7 ),
	'dragon'     => array( 19.4, 289.5, 125.6 ),
	'lion'       => array( 173.3, 247.9, 177.9 ),
	'fish'       => array( 377.4, 182.3, 166.0 ),
	'water-bird' => array( 531.7, 283.5, 128.5 ),
	'deer'       => array( 554.9, 157.5, 160.2 ),
	'makara'     => array( 744.0, 186.5, 185.8 ),
	'bird'       => array( 757.5, 291.8, 97.9 ),
	'rooster'    => array( 954.2, 153.2, 119.7 ),
	'horse'      => array( 306.1, 348.5, 170.1 ),
	'turtle'     => array( 643.5, 406.2, 108.8 ),
	'elephant'   => array( 863.8, 331.7, 210.6 ),
);

$sh_trails = array();
foreach ( sh_trails( 'home' ) as $sh_t ) {
	$sh_trails[ $sh_t->slug ] = $sh_t;
}

$sh_animals = array();
foreach ( $sh_boxes as $sh_slug => $sh_box ) {
	if ( ! isset( $sh_trails[ $sh_slug ] ) ) {
		continue;
	}
	$sh_trail = $sh_trails[ $sh_slug ];
	$sh_image = absint( sh_get_term( 'sh_chooser_image', $sh_trail ) );
	if ( ! $sh_image ) {
		continue;
	}
	$sh_name = wp_specialchars_decode( $sh_trail->name, ENT_QUOTES );
	$sh_html = wp_get_attachment_image(
		$sh_image,
		'full',
		false,
		array(
			'class'    => 'sh-z4__img',
			'alt'      => $sh_name,
			'loading'  => 'eager',
			'decoding' => 'async',
			'sizes'    => '(min-width: 1168px) ' . (int) ceil( $sh_box[2] ) . 'px, 34vw',
		)
	);
	if ( '' === $sh_html ) {
		continue;
	}
	$sh_page = absint( sh_get_term( 'sh_timeline_page', $sh_trail ) );
	$sh_url  = $sh_page && 'publish' === get_post_status( $sh_page ) ? (string) get_permalink( $sh_page ) : '';

	$sh_animals[] = array(
		'slug'  => $sh_slug,
		'html'  => $sh_html,
		'url'   => $sh_url,
		'style' => sprintf(
			'--sh-z4-x:%s%%;--sh-z4-y:%s%%;--sh-z4-w:%s%%',
			round( $sh_box[0] / 1120 * 100, 4 ),
			round( $sh_box[1] / 540 * 100, 4 ),
			round( $sh_box[2] / 1120 * 100, 4 )
		),
	);
}

$sh_cover      = absint( sh_get_term( 'sh_cover_image', $sh_zone ) );
$sh_cover_html = $sh_cover ? wp_get_attachment_image(
	$sh_cover,
	'full',
	false,
	array(
		'class'         => 'sh-z4__bg',
		'alt'           => '',
		'loading'       => 'eager',
		'decoding'      => 'async',
		'fetchpriority' => 'high',
		'sizes'         => '(min-width: 1168px) 1120px, 100vw',
	)
) : '';

if ( ! $sh_animals && '' === $sh_cover_html ) {
	return;
}
?>
<div class="sh-z4__stage<?php echo '' === $sh_cover_html ? ' sh-z4__stage--no-bg' : ''; ?>">
	<?php echo $sh_cover_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?>
	<?php if ( $sh_animals ) : ?>
		<ul class="sh-z4__animals" aria-label="<?php echo esc_attr__( 'Choose an animal', 'sharedheritage' ); ?>">
			<?php foreach ( $sh_animals as $sh_a ) : ?>
				<li class="sh-z4__animal<?php echo '' !== $sh_a['url'] ? ' sh-z4__animal--link' : ''; ?>" data-trail="<?php echo esc_attr( $sh_a['slug'] ); ?>" style="<?php echo esc_attr( $sh_a['style'] ); ?>">
					<?php if ( '' !== $sh_a['url'] ) : ?>
						<a class="sh-z4__link" href="<?php echo esc_url( $sh_a['url'] ); ?>"><?php echo $sh_a['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?></a>
					<?php else : ?>
						<?php echo $sh_a['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output. ?>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
