<?php
if (!defined('ABSPATH')) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Yeori_Slide_Widget extends \Elementor\Widget_Base {
	public function get_name() {
		return 'yeori_slide';
	}
	public function get_title() {
		return __('Yeori Slide', 'duydev');
	}
	public function get_icon() {
		return 'eicon-slider-push';
	}
	public function get_categories() {
		return ['duydev-cat'];
	}
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Slides', 'duydev'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'slides',
			[
				'label' => __('Slides', 'duydev'),
				'type' => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'heading',
						'label' => __('Heading', 'duydev'),
						'type' => Controls_Manager::TEXT,
						'default' => __('Section Title', 'duydev'),
					],
					[
						'name' => 'content',
						'label' => __('Content', 'duydev'),
						'type' => Controls_Manager::TEXTAREA,
						'default' => __('Section content here...', 'duydev'),
					],
					[
						'name' => 'background',
						'label' => __('Background Color', 'duydev'),
						'type' => Controls_Manager::COLOR,
						'default' => '#1e293b',
					],
					[
						'name' => 'background_image',
						'label' => __('Background Image', 'duydev'),
						'type' => Controls_Manager::MEDIA,
						'default' => [
							'url' => '',
						],
					],
				],
				'default' => [
					[ 'heading' => 'Section 1', 'content' => 'Scroll (vuốt) 1 phát là nhảy section', 'background' => '#1e293b' ],
					[ 'heading' => 'Section 2', 'content' => 'GSAP + Observer + ScrollTo', 'background' => '#0ea5e9' ],
					[ 'heading' => 'Section 3', 'content' => 'Không dính scroll, không double-jump', 'background' => '#10b981' ],
					[ 'heading' => 'Section 4', 'content' => 'Kết thúc demo', 'background' => '#f59e0b' ],
				],
			]
		);
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$slides = $settings['slides'];
		$maxSlide = count($slides);
		$uid = uniqid('yeori_slide_');
		?>
		<div class="smooth-wrapper">
			<div class="smooth-content" id="<?php echo esc_attr($uid); ?>">
				<?php foreach ($slides as $i => $slide): 
					$bg_image = !empty($slide['background_image']['url']) ? $slide['background_image']['url'] : '';
					$bg_color = !empty($slide['background']) ? $slide['background'] : '#1e293b';
					
					$bg_style = '';
					if ($bg_image) {
						$bg_style = "background-image: url('" . esc_url($bg_image) . "'); background-size: cover; background-position: center; background-repeat: no-repeat;";
					} else {
						$bg_style = "background-color: " . esc_attr($bg_color) . ";";
					}
				?>
					<section class="panel p<?php echo ($i+1); ?>" style="<?php echo $bg_style; ?>">
						<?php if ($bg_image): ?>
							<div class="panel-overlay" style="background-color: #00000080; position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;"></div>
						<?php endif; ?>
						<div class="count-section koho">
							<span class="num atext atext-slow">0<?php echo ($i+1); ?></span><span class="max">/0<?php echo esc_html($maxSlide); ?></span>
						</div>
						<div class="panel-content" style="position: relative; z-index: 2;">
							<h1 class="atext atext-slow"><?php echo esc_html($slide['heading']); ?></h1>
							<p class="atext atext-slow"><?php echo esc_html($slide['content']); ?></p>
						</div>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<script>
		const maxSlide = "<?php echo esc_js( $maxSlide ); ?>";
		(function($){
			// Wait for GSAP to load
			function initYeoriSlide() {
				if(typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined' || typeof ScrollToPlugin === 'undefined' || typeof Observer === 'undefined') {
					setTimeout(initYeoriSlide, 100); // retry after 100ms
					return;
				}
				
				// Register GSAP plugins
				gsap.registerPlugin(ScrollTrigger, ScrollToPlugin, Observer);
				console.log('All GSAP plugins loaded and registered!');
				
				var container = document.getElementById('<?php echo esc_js($uid); ?>');
				if(!container) return;
				var panels = gsap.utils.toArray('#<?php echo esc_js($uid); ?> .panel');
				var steps = Math.max(0, panels.length - 1);
				
				// Function to format numbers with leading zero
				function formatNumber(num) {
					return num < 10 ? '0' + num : num.toString();
				}
				
				// Variables for state management
				var current = 0;
				var locked = false;
				var isJumping = false; // Flag to prevent onUpdate interference during jumps
				
				// Pin container and animate panels
				var tl = gsap.timeline({
					scrollTrigger: {
						trigger: container,
						start: 'top top',
						end: function() { return '+=' + steps * window.innerHeight; },
						pin: true,
						scrub: true,
						onUpdate: function(self) {
							if (steps <= 0 || isJumping) return;
							current = Math.round(self.progress * steps);
						}
					}
				});
			if (steps > 0) tl.to(panels, { yPercent: -100 * steps, ease: 'none' });
			
			var st = tl.scrollTrigger;
			
			// Calculate positions for each section
			var positions = [];
			var computePositions = function() {
				if (!st) return;
				var start = st.start || 0;
				var end = st.end || start + 1;
				positions = panels.map(function(_, i) {
					return start + (end - start) * (steps ? i / steps : 0);
				});
			};
			computePositions();
			ScrollTrigger.addEventListener('refresh', computePositions);
			window.addEventListener('resize', computePositions);
			
			// Jump to index logic
			var current = 0;
			var locked = false;
			var gotoIndex = function(index) {
				if (locked || steps <= 0) return;
				var targetIndex = gsap.utils.clamp(0, steps, index);
				var y = positions[targetIndex];
				if (!Number.isFinite(y)) return;
				
				locked = true;
				isJumping = true; // Prevent onUpdate interference
				gsap.to(window, {
					duration: 0.5,
					scrollTo: { y: y, autoKill: false },
					ease: 'power3.out',
					onStart: function() {
						if (targetIndex === 0 || targetIndex === Number(maxSlide) - 1) {
							return;
						}
					},
					onComplete: function() {
						current = targetIndex;
						setTimeout(function() { 
							locked = false; 
							isJumping = false; // Re-enable onUpdate
						}, 1000);
					}
				});
			};
			
			// Observer for wheel/touch gestures
				var obs = Observer.create({
					target: window,
					type: 'wheel,touch,pointer',
					preventDefault: true,
					tolerance: 14,
					wheelSpeed: 1,
					onDown: function() { 
						// If at last slide, allow scroll to continue to about-us section
						if (current >= steps) {
							obs.disable();
							setTimeout(function() { obs.enable(); }, 1000);
							return;
						}
						gotoIndex(current + 1); 
					},
					onUp: function() { 
						// If at first slide, allow scroll to continue upward
						if (current <= 0) {
							obs.disable();
							setTimeout(function() { obs.enable(); }, 1000);
							return;
						}
						gotoIndex(current - 1); 
					}
				});
				
				window.addEventListener('load', function() { ScrollTrigger.refresh(); });
				
			} // end initYeoriSlide
			
			// Start initialization
			initYeoriSlide();
		})(jQuery);
		</script>
		<?php
	}
}
