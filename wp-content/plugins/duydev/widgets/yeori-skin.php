<?php
if (!defined('ABSPATH')) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Yeori_Skin_Widget extends \Elementor\Widget_Base {
	
	public function get_name() {
		return 'yeori_skin';
	}
	
	public function get_title() {
		return __('Yeori Skin', 'duydev');
	}
	
	public function get_icon() {
		return 'eicon-custom';
	}
	
	public function get_categories() {
		return ['duydev-cat'];
	}
	
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Content', 'duydev'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		
		
		$this->end_controls_section();
	}
	
	protected function render() {
		$settings = $this->get_settings_for_display();
		
		// Get the current post ID
		$post_id = get_the_ID();
		
		// Check if we have a valid post ID
		if (!$post_id) {
			echo '<p>No product found.</p>';
			return;
		}
		
		// Get the skin gallery repeater field
		$skin_gallery = get_field('skin_gallery', $post_id);
		$i = 0;
		?>
		<section class="yeori-skin">
			<div class="yeori-skin-wrapper">
				<?php foreach ($skin_gallery as $skin): ?>
					<div class="skin-loupe <?php echo $i===0 ? 'active' : ''; ?>">
						<div class="loupe-wrap" data-zoom="1" data-lens="576">
						<!-- BEFORE -->
						<img class="img-base" src="<?php echo $skin['skin_before']['url']; ?>" alt="Before">

						<!-- OVERLAY -->
						<div class="img-overlay">
							
						</div>

						<!-- AFTER (chỉ hiển thị trong lens) -->
						<img class="img-after" src="<?php echo $skin['skin_after']['url']; ?>" alt="After">

						<!-- Lens (viền rỗng) + tay cầm -->
						<div class="lens" aria-hidden="true">
							<div class="ring">
								<span class="after-label">After</span>
							</div>
							<div class="handle"></div>
						</div>

						<!-- Label góc -->
						<span class="before-label">Before</span>

						<button class="boom-out">
							<span>Giữ để xem kết quả</span>
							<img src="<?php echo DUYDEV_PLUGIN_IMG . 'hold.svg'; ?>" alt="">
						</button>
						</div>
					</div>
					<?php $i+=1; ?>
				<?php endforeach ?>    
			</div>
			<?php $i = 0?>
			<div class="yeori-skin-select">
				<div class="title koho">
					<p class="top">Kết quả sử dụng:</p>
					<p class="bottom">Giảm về năng trên da</p>
				</div>
				<div class="yeori-skin-select-wrapper">
					<div class="swiper yeori-skin-swiper">
						<div class="swiper-wrapper">
							<?php foreach($skin_gallery as $skin) : ?>
								<div class="swiper-slide <?php echo $i==0 ? 'active' : ''; ?>">
									<img class="skin-icon <?php echo $i==0 ? 'active' : ''; ?>" src="<?php echo $skin['skin_icon']['url']; ?>" alt="">
								</div>
								<?php $i += 1; ?>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</section>
        <?php
	}
}
