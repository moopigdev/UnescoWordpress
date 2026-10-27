<?php
/**
 * Introduction (Frame 12685) / Knowing Southeast Asia (Frame 12717): row = picture card + text column
 * (title · 2 summary paragraphs), then the long body. Fields: <prefix>title · paragraph_1 · paragraph_2 · body · image
 * (sh_intro_* for Introduction, sh_intro_knowing_* for Knowing Southeast Asia — CONTENT-MODEL §7).
 *
 * Args: 'page_id' · 'prefix' · 'class' · 'heading' (1|2) · 'image_first' (bool).
 *
 * @package sharedheritage
 */

defined( 'ABSPATH' ) || exit;

$sh_args    = ( isset( $args ) && is_array( $args ) ) ? $args : array();
$sh_page    = isset( $sh_args['page_id'] ) ? (int) $sh_args['page_id'] : 0;
$sh_prefix  = isset( $sh_args['prefix'] ) ? (string) $sh_args['prefix'] : '';
$sh_class   = isset( $sh_args['class'] ) ? (string) $sh_args['class'] : '';
$sh_tag     = ( isset( $sh_args['heading'] ) && 1 === (int) $sh_args['heading'] ) ? 'h1' : 'h2';
$sh_img_1st = ! empty( $sh_args['image_first'] );

$sh_title  = (string) sh_get( $sh_prefix . 'title', $sh_page );
$sh_p1     = (string) sh_get( $sh_prefix . 'paragraph_1', $sh_page );
$sh_p2     = (string) sh_get( $sh_prefix . 'paragraph_2', $sh_page );
$sh_body   = (string) sh_get( $sh_prefix . 'body', $sh_page );
$sh_img_id = (int) sh_get( $sh_prefix . 'image', $sh_page );

$sh_card = static function () use ( $sh_img_id ): void {
	?>
	<div class="sh-intro__wrap"><div class="sh-intro__card">
		<?php
		get_template_part(
			'template-parts/intro/image',
			null,
			array(
				'id'    => $sh_img_id,
				'class' => 'sh-intro__img',
				'sizes' => '(min-width: 768px) 549px, 100vw',
			)
		);
		?>
	</div></div>
	<?php
};
?>
<section class="sh-intro__section <?php echo esc_attr( $sh_class ); ?>">
	<div class="sh-intro__row">
		<?php
		if ( $sh_img_1st ) {
			$sh_card();
		}
		?>
		<div class="sh-intro__text">
			<?php if ( '' !== $sh_title ) : ?>
				<<?php echo esc_html( $sh_tag ); ?> class="sh-t-page-title sh-intro__title"><?php echo esc_html( $sh_title ); ?></<?php echo esc_html( $sh_tag ); ?>>
			<?php endif; ?>
			<?php
			get_template_part(
				'template-parts/intro/paragraphs',
				null,
				array(
					'text'  => $sh_p1,
					'class' => 'sh-t-body sh-intro__summary',
				)
			);
			get_template_part(
				'template-parts/intro/paragraphs',
				null,
				array(
					'text'  => $sh_p2,
					'class' => 'sh-t-body sh-intro__summary',
				)
			);
			?>
		</div>
		<?php
		if ( ! $sh_img_1st ) {
			$sh_card();
		}
		?>
	</div>
	<?php if ( '' !== trim( $sh_body ) ) : ?>
		<div class="sh-intro__body">
			<?php
			get_template_part(
				'template-parts/intro/paragraphs',
				null,
				array(
					'text'  => $sh_body,
					'class' => 'sh-t-body sh-intro__body-p',
				)
			);
			?>
		</div>
	<?php endif; ?>
</section>
