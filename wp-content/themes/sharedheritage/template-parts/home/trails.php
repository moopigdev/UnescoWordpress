<?php
/**
 * Home section 5 — Trails (Frame All trails 886:1795 [160,1766 1120×777]).
 * Text block (title · tagline · "Read more >>" → two long paragraphs, Variant2 886:1771) then the stage AAMM-01
 * [160,1913 1120×630]: footprint video (walking.webm, D12) plays when scrolled into view, then the 17 trail icons
 * (sh_trails('home'), rows 6/5/6, 165×165 → hover 185) appear. Without video the icons show at once.
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args  = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_pid   = isset( $sh_args['page_id'] ) ? (int) $sh_args['page_id'] : 0;
$sh_title = (string) sh_get( 'sh_home_trails_title', $sh_pid );
$sh_tag   = (string) sh_get( 'sh_home_trails_tagline', $sh_pid );
$sh_more  = array_filter(
	array(
		(string) sh_get( 'sh_home_trails_paragraph_1', $sh_pid ),
		(string) sh_get( 'sh_home_trails_paragraph_2', $sh_pid ),
	),
	'strlen'
);

$sh_video_url  = '';
$sh_video_file = (int) sh_get( 'sh_home_trails_video_webm', $sh_pid );
if ( $sh_video_file > 0 ) {
	$sh_video_url = (string) wp_get_attachment_url( $sh_video_file );
}
$sh_poster_url = '';
$sh_poster_id  = (int) sh_get( 'sh_home_trails_video_poster', $sh_pid );
if ( $sh_poster_id > 0 ) {
	$sh_poster_url = (string) wp_get_attachment_image_url( $sh_poster_id, 'full' );
}

// Rows 6 / 5 / 6 (Frame 12793 · 12794 · 12795); any extra trail continues the same pattern.
$sh_trails = sh_trails( 'home' );
$sh_rows   = array();
$sh_sizes  = array( 6, 5, 6 );
$sh_i      = 0;
$sh_n      = 0;
while ( $sh_n < count( $sh_trails ) ) {
	$sh_take    = $sh_sizes[ $sh_i % 3 ];
	$sh_rows[]  = array_slice( $sh_trails, $sh_n, $sh_take );
	$sh_n      += $sh_take;
	++$sh_i;
}
?>
<section id="sh-home-trails" class="sh-home-trails sh-container" aria-labelledby="sh-home-trails-title">
	<div class="sh-home-trails__text">
		<?php if ( '' !== $sh_title ) : ?>
			<h2 id="sh-home-trails-title" class="sh-t-section-title"><?php echo esc_html( $sh_title ); ?></h2>
		<?php endif; ?>
		<?php if ( '' !== $sh_tag ) : ?>
			<p class="sh-t-lead"><?php echo esc_html( $sh_tag ); ?></p>
		<?php endif; ?>
		<?php if ( $sh_more ) : ?>
			<div id="sh-home-trails-more" class="sh-home-trails__more">
				<?php foreach ( $sh_more as $sh_para ) : ?>
					<p class="sh-t-body-home"><?php echo wp_kses( sh_home_lines( $sh_para ), array( 'br' => array() ) ); ?></p>
				<?php endforeach; ?>
			</div>
			<?php
			get_template_part(
				'template-parts/common/read-more',
				null,
				array(
					'url'      => '',
					'label'    => (string) get_option( 'sh_label_read_more', '' ),
					'variant'  => 'home',
					'align'    => 'center',
					'controls' => 'sh-home-trails-more',
					'class'    => 'sh-home-trails__toggle',
				)
			);
			?>
		<?php endif; ?>
	</div>

	<div class="sh-home-trails__stage"<?php echo '' !== $sh_video_url ? ' data-footprints' : ''; ?>>
		<?php if ( '' !== $sh_video_url ) : ?>
			<video class="sh-home-trails__video" muted playsinline loop preload="metadata" aria-hidden="true" tabindex="-1"<?php echo '' !== $sh_poster_url ? ' poster="' . esc_url( $sh_poster_url ) . '"' : ''; ?>>
				<source src="<?php echo esc_url( $sh_video_url ); ?>" type="video/webm">
			</video>
		<?php endif; ?>

		<?php if ( $sh_rows ) : ?>
			<div class="sh-home-trails__icons">
				<?php foreach ( $sh_rows as $sh_row ) : ?>
					<ul class="sh-home-trails__row">
						<?php
						foreach ( $sh_row as $sh_trail ) :
							$sh_link = get_term_link( $sh_trail );
							$sh_def  = sh_home_img(
								(int) sh_get_term( 'sh_icon_home', $sh_trail ),
								array(
									'alt_fallback' => $sh_trail->name,
									'class'        => 'sh-home-trail__img',
									'sizes'        => '165px',
								)
							);
							$sh_hov  = sh_home_img(
								(int) sh_get_term( 'sh_icon_home_hover', $sh_trail ),
								array(
									'alt_fallback' => '',
									'class'        => 'sh-home-trail__img sh-home-trail__img--hover',
									'sizes'        => '185px',
								)
							);
							?>
							<li class="sh-home-trail">
								<a class="sh-home-trail__link" href="<?php echo esc_url( is_wp_error( $sh_link ) ? '' : $sh_link ); ?>">
									<?php echo sh_home_kses( $sh_def ) . sh_home_kses( $sh_hov ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in sh_home_img() + kses. ?>
									<span class="screen-reader-text"><?php echo esc_html( $sh_trail->name ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
