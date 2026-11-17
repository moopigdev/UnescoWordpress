<?php
/**
 * ZONE 1–5 bar hotspots (D56 · D40): five transparent links over the tab row of the bar image (labels are baked into
 * the picture · D8). x = 0/20/40/60/80 % · tab row y 833–876 of the bar 710–876 (→ from 74.1 %, see CSS).
 * Put it inside a `position: relative` box that has the bar's size. The zone's `sh_link_page` is the target when it is
 * published, else the term link. The current zone is `aria-current="page"` and has no link; with no current zone
 * (Home) all five link.
 *
 * Args: current (WP_Term|null) — the zone page being shown.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args    = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_current = isset( $sh_args['current'] ) && $sh_args['current'] instanceof WP_Term ? $sh_args['current'] : null;

$sh_tabs = array();
foreach ( sh_zones() as $sh_z ) {
	$sh_n = (int) sh_get_term( 'sh_number', $sh_z );
	if ( $sh_n < 1 || $sh_n > 5 ) {
		continue;
	}
	$sh_is_current = $sh_current && $sh_z->term_id === $sh_current->term_id;
	$sh_url        = '';
	if ( ! $sh_is_current ) {
		$sh_page = absint( sh_get_term( 'sh_link_page', $sh_z ) );
		if ( $sh_page && 'publish' === get_post_status( $sh_page ) ) {
			$sh_url = (string) get_permalink( $sh_page );
		} else {
			$sh_link = get_term_link( $sh_z );
			$sh_url  = is_string( $sh_link ) ? $sh_link : '';
		}
	}
	$sh_tabs[ $sh_n ] = array(
		'name'    => $sh_z->name,
		'url'     => $sh_url,
		'current' => $sh_is_current,
	);
}
ksort( $sh_tabs );
if ( ! $sh_tabs ) {
	return;
}
?>
<nav class="sh-zbar__nav" aria-label="<?php echo esc_attr__( 'Zones', 'sharedheritage' ); ?>">
	<ul class="sh-zbar__tabs">
		<?php foreach ( $sh_tabs as $sh_n => $sh_tab ) : ?>
			<?php
			$sh_label = sprintf(
				/* translators: 1: zone number, 2: zone name. */
				__( 'Zone %1$d: %2$s', 'sharedheritage' ),
				$sh_n,
				wp_specialchars_decode( $sh_tab['name'], ENT_QUOTES )
			);
			?>
			<li class="sh-zbar__tab" style="<?php echo esc_attr( '--sh-zbar-i:' . ( $sh_n - 1 ) ); ?>">
				<?php if ( $sh_tab['current'] ) : ?>
					<span class="sh-zbar__hit" aria-current="page"><span class="screen-reader-text"><?php echo esc_html( $sh_label ); ?></span></span>
				<?php elseif ( '' !== $sh_tab['url'] ) : ?>
					<a class="sh-zbar__hit sh-zbar__hit--link" href="<?php echo esc_url( $sh_tab['url'] ); ?>"><span class="screen-reader-text"><?php echo esc_html( $sh_label ); ?></span></a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
