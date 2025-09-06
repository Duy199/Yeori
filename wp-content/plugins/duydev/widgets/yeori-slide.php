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
						'type' => Controls_Manager::TEXTAREA,
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
					[
						'name' => 'video_url',
						'label' => __('Video URL', 'duydev'),
						'type' => Controls_Manager::URL,
						'placeholder' => __('https://your-video-url.mp4', 'duydev'),
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
					$video_url = !empty($slide['video_url']['url']) ? $slide['video_url']['url'] : '';
					
					$bg_style = '';
					if ($bg_image) {
						$bg_style = "background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('" . esc_url($bg_image) . "'); background-size: cover; background-position: center; background-repeat: no-repeat;";
					} else {
						$bg_style = "background-color: " . esc_attr($bg_color) . ";";
					}
				?>
					<section class="panel p<?php echo ($i+1); ?>" style="<?php echo $bg_style; ?>">
						<p class="title">✧ Highlights công nghệ ✧</p>
						<div class="panel-detail">
							<div class="panel-content-1">
								<h2 class="slide-text heading"><?php echo esc_html($slide['heading']); ?></h2>
								<div class="mouse">
									<p>Tiếp tục cuộn chuột</p>
									<img src="<?php echo DUYDEV_PLUGIN_IMG . 'Scroll Prompt.svg'; ?>" alt="mouse">
								</div>
								<p class="title">✧ Highlights công nghệ ✧</p>
							</div>
							<div class="panel-content-2">
								<!-- <p class="title">✧ Highlights công nghệ ✧</p> -->
								<?php if ($video_url): ?>
									<div class="video-wrapper">
										<div class="video-section">
											<video autoplay muted loop>
												<source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
												Your browser does not support the video tag.
											</video>
										</div>
										<div class=line-wrapper>
											<div class="line"></div>
										</div>
									</div>
								<?php endif; ?>
							</div>
							<div class="panel-content-3">
								<div class="count-section koho">
									<p class="num slide-text">0<?php echo ($i+1); ?></p>
									<p class="max">/0<?php echo esc_html($maxSlide); ?></p>
								</div>
								<div class="content-wrapper">
									<p class="slide-text content"><?php echo esc_html($slide['content']); ?></p>
									<a href="" class="button-link">
										<span>Tìm hiểu thêm</span>
										<div class="vector">
											<img src="<?php echo DUYDEV_PLUGIN_IMG . 'Icon.svg'; ?>" alt="vector-link">
										</div>
									</a>
								</div>
							</div>
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
				
				// Check if screen is below 1024px
				var isMobile = window.innerWidth <= 1024;
				
				if (isMobile) {
					// Mobile: Horizontal scroll with pin control similar to the YouTube reference
					console.log('Mobile mode: Using horizontal scroll with pin control');
					
					// Clear any existing ScrollTriggers
					ScrollTrigger.getAll().forEach(function(st) { st.kill(); });
					
					// Add a class to help with styling
					container.classList.add('horizontal-scroll');
					
					// Calculate the section height to ensure proper space is reserved
					const sectionHeight = window.innerHeight;
					
					// Style the container and panels for horizontal scrolling
					gsap.set(container, { 
						width: (panels.length * 100) + 'vw',
						height: sectionHeight + 'px',
						display: 'flex',
						position: 'relative'
					});
					
					// Set each panel to full viewport width
					gsap.set(panels, {
						width: '100vw',
						height: '100%'
					});
					
					// Create the horizontal scroll animation with pin
					const horizontalScroll = gsap.timeline({
						scrollTrigger: {
							trigger: container,
							start: 'top top',
							end: () => '+=' + (container.scrollWidth - window.innerWidth),
							pin: true,
							anticipatePin: 1,
							scrub: 0.8,
							invalidateOnRefresh: true,
							snap: {
								snapTo: 1/(panels.length-1),
								duration: {min: 0.2, max: 0.5},
								delay: 0.1,
								ease: 'power1.inOut'
							},
							onLeaveBack: () => {
								console.log('Entering horizontal scroll section from above');
							},
							onLeave: () => {
								console.log('Leaving horizontal scroll section - should see About Us section next');
							},
							pinSpacing: true,
							markers: false
						}
					});
					
					// Add the horizontal movement
					horizontalScroll.to(container, {
						x: () => -(container.scrollWidth - window.innerWidth),
						ease: 'none'
					});
					
					// Add animations for each panel's content
					panels.forEach((panel, i) => {
						// Get text elements in this panel
						const texts = panel.querySelectorAll('.slide-text');
						
						// Set up CSS transitions and initial state
						texts.forEach((text, index) => {
							text.style.transition = `opacity 0.8s ease-out ${index * 0.1}s, transform 0.8s ease-out ${index * 0.1}s`;
							text.style.opacity = '0';
							text.style.transform = 'translateY(30px)';
						});
						
						// Animate text elements when panel is in view
						ScrollTrigger.create({
							trigger: panel,
							containerAnimation: horizontalScroll,
							start: 'left center',
							end: 'right center',
							onEnter: () => {
								// Animate in with CSS transitions
								texts.forEach(text => {
									text.style.opacity = '1';
									text.style.transform = 'translateY(0px)';
								});
							},
							onLeave: () => {
								// Animate out with CSS transitions
								texts.forEach(text => {
									text.style.opacity = '0';
									text.style.transform = 'translateY(30px)';
								});
							},
							onEnterBack: () => {
								// Animate in when coming back
								texts.forEach(text => {
									text.style.opacity = '1';
									text.style.transform = 'translateY(0px)';
								});
							},
							onLeaveBack: () => {
								// Animate out when leaving back
								texts.forEach(text => {
									text.style.opacity = '0';
									text.style.transform = 'translateY(30px)';
								});
							}
						});
					});
					
					// Helper function to find the "About Us" section or any section after this widget
					function findNextSection() {
						let nextSection = container.parentNode;
						while (nextSection && nextSection.nextElementSibling) {
							nextSection = nextSection.nextElementSibling;
							// Check if this is a section element
							if (nextSection.tagName === 'SECTION' || 
								nextSection.classList.contains('elementor-section')) {
								return nextSection;
							}
						}
						return null;
					}
					
					// Make sure the About Us section is visible after scrolling through all panels
					const nextSection = findNextSection();
					if (nextSection) {
						console.log('Found next section after slide widget:', nextSection);
						
						// Make sure no CSS is hiding the next section
						gsap.set(nextSection, { 
							visibility: 'visible',
							display: 'block',
							opacity: 1
						});
					}
					
				} else {
					// Desktop: Full scroll animation experience
					console.log('Desktop mode: Using full scroll animation');
					
					// Add text animations for each panel
					panels.forEach((panel, i) => {
						const texts = panel.querySelectorAll('.slide-text');
						
						// Set up CSS transitions and initial state
						texts.forEach((text, index) => {
							text.style.transition = `opacity 0.8s ease-out ${index * 0.1}s, transform 0.8s ease-out ${index * 0.1}s`;
							text.style.opacity = '0';
							text.style.transform = 'translateY(30px)';
						});
						
						// Create ScrollTrigger for this panel's text animation
						ScrollTrigger.create({
							trigger: panel,
							start: 'top center',
							end: 'bottom center',
							onEnter: () => {
								// Animate in with CSS transitions
								texts.forEach(text => {
									text.style.opacity = '1';
									text.style.transform = 'translateY(0px)';
								});
							},
							onLeave: () => {
								// Animate out with CSS transitions
								texts.forEach(text => {
									text.style.opacity = '0';
									text.style.transform = 'translateY(30px)';
								});
							},
							onEnterBack: () => {
								// Animate in when coming back
								texts.forEach(text => {
									text.style.opacity = '1';
									text.style.transform = 'translateY(0px)';
								});
							},
							onLeaveBack: () => {
								// Animate out when leaving back
								texts.forEach(text => {
									text.style.opacity = '0';
									text.style.transform = 'translateY(30px)';
								});
							}
						});
					});
					
					// Function to format numbers with leading zero
					function formatNumber(num) {
						return num < 10 ? '0' + num : num.toString();
					}
					
					// Variables for state management
					var current = 0;
					var locked = false;
					var isJumping = false; // Flag to prevent onUpdate interference during jumps
					var touchTime = 0;
					
					// Pin container and animate panels
					var tl = gsap.timeline({
						scrollTrigger: {
							trigger: container,
							start: 'top top',
							end: function() { return '+=' + steps * window.innerHeight; },
							pin: true,
							scrub: true,
							markers: false,
							onUpdate: function(self) {
								if (steps <= 0 || isJumping) return;
								current = Math.round(self.progress * steps);
							},
							onLeave: function() {
								touchTime = 0;
								console.log('onLeave, touchTime reset to 0');
							},
							onLeaveBack: function() {
								touchTime = 0;
								console.log('onLeaveBack, touchTime reset to 0');
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
					
					// Observer for wheel/touch gestures (desktop only)
					var obs = Observer.create({
						target: window,
						type: 'wheel,touch,pointer',
						preventDefault: true,
						tolerance: 14,
						wheelSpeed: 1,
						onDown: function() { 
							// If at last slide, allow scroll to continue to about-us section
							if (current >= steps) {
								// At last panel: allow natural page scrolling
								obs.disable();
								setTimeout(function() { obs.enable(); }, 400); // enough time for 1-2 scroll notches to exit
								return;
							}
							if (current === 0 && touchTime === 0) {
								++touchTime;
								gotoIndex(current); 
								return;
							}
							gotoIndex(current + 1); 
						},
						onUp: function() { 
							// If at first slide, allow scroll to continue upward
							if (current <= 0) {
								obs.disable();
								setTimeout(function() { obs.enable(); }, 400);
								return;
							}
							if (current === maxSlide - 1 && touchTime === 0) {
								++touchTime;
								gotoIndex(current);
								return;
							}
							gotoIndex(current - 1); 
						}
					});
				}
				
				// Handle window resize to switch between modes
				window.addEventListener('resize', function() {
					var newIsMobile = window.innerWidth < 1024;
					if (newIsMobile !== isMobile) {
						// Screen size changed, reload to apply correct mode
						location.reload();
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
