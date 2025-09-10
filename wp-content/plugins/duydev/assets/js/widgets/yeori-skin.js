document.addEventListener('DOMContentLoaded', function() {
    // Get all loupe-wrap elements
    const wraps = document.querySelectorAll('.loupe-wrap');
    
    if (wraps.length === 0) return;
    
    if(window.innerWidth <= 767) {
        return;
    } 

    // Loop through each loupe instance
    wraps.forEach(function(wrap, index) {
        // Self-contained function for each loupe instance
        (function() {
            const after = wrap.querySelector('.img-after');
            const ring = wrap.querySelector('.ring');
            const handle = wrap.querySelector('.handle');
            const lens = wrap.querySelector('.lens');
            
            if (!after || !ring || !handle || !lens) return;
            
            console.log('Initialized skin loupe #' + (index + 1));
            
            // ===== Config =====
            const zoom = Number(wrap.dataset.zoom || 1);     // độ phóng đại
            let lensSize = Number(wrap.dataset.lens || 360);   // đường kính lens (px)
            
            // for mobile and ipad

            if (window.innerWidth <= 1024) {
                lensSize = 360;
            }
            
            // Sync CSS var với data-lens
            ring.style.setProperty('--size', lensSize + 'px');
            
            // Set initial state
            let isHovering = false;
            let currentRadius = 0;
            let targetRadius = 0;
            let animationId = null;
            
            // Initialize lens as hidden
            lens.style.opacity = '0';
            after.style.clipPath = `circle(0px at 50% 50%)`;
            
            let rect = wrap.getBoundingClientRect();
            
            // Update rect on window resize
            const updateRect = () => {
                rect = wrap.getBoundingClientRect();
            };
            window.addEventListener('resize', updateRect);
            
            let raf = null;
            let _x = rect.left + rect.width * 0.6;
            let _y = rect.top + rect.height * 0.55;
            
            function animateRadius() {
                const speed = 0.15; // Animation speed (0-1)
                const diff = targetRadius - currentRadius;
                
                if (Math.abs(diff) > 1) {
                    currentRadius += diff * speed;
                    
                    const lx = _x - rect.left;
                    const ly = _y - rect.top;
                    
                    // Update clip-path with current radius
                    after.style.clipPath = `circle(${currentRadius}px at ${lx}px ${ly}px)`;
                    
                    animationId = requestAnimationFrame(animateRadius);
                } else {
                    currentRadius = targetRadius;
                    const lx = _x - rect.left;
                    const ly = _y - rect.top;
                    after.style.clipPath = `circle(${currentRadius}px at ${lx}px ${ly}px)`;
                    animationId = null;
                }
            }
            
            function moveTo(clientX, clientY) {
                _x = Math.max(rect.left, Math.min(clientX, rect.right));
                _y = Math.max(rect.top, Math.min(clientY, rect.bottom));
                if (!raf) raf = requestAnimationFrame(render);
            }
            
            function render() {
                raf = null;

                const lx = _x - rect.left;   // vị trí trong container
                const ly = _y - rect.top;

                // Đặt tâm ring tại con trỏ
                ring.style.left = lx + 'px';
                ring.style.top  = ly + 'px';
                
                // Handle dính mép phải của lens
                handle.style.left = (lx + lensSize / 2) + 'px';
                handle.style.top = ly + 'px';
                
                // Update clip-path position if hovering
                if (isHovering) {
                    after.style.clipPath = `circle(${currentRadius}px at ${lx}px ${ly}px)`;
                }
                
                // Scale AFTER và dịch chuyển để giữ đúng điểm focus
                after.style.transform = `translate(${-lx*(zoom-1)}px, ${-ly*(zoom-1)}px) scale(${zoom})`;
            }
            
            // Update rect when entering a loupe
            wrap.addEventListener('mouseenter', function(e) {
                updateRect();
                isHovering = true;
                targetRadius = lensSize / 2;
                lens.style.opacity = '1';
                
                // Start animation if not already running
                if (!animationId) {
                    animateRadius();
                }
                
                moveTo(e.clientX, e.clientY);
            });
            
            // Hide lens and shrink circle when mouse leaves
            wrap.addEventListener('mouseleave', function(e) {
                isHovering = false;
                targetRadius = 0;
                lens.style.opacity = '0';
                
                // Start shrinking animation
                if (!animationId) {
                    animateRadius();
                }
            });
            
            // Desktop
            wrap.addEventListener('mousemove', e => moveTo(e.clientX, e.clientY));
            
            // Touch
            wrap.addEventListener('touchstart', e => {
                updateRect();
                isHovering = true;
                targetRadius = lensSize / 2;
                lens.style.opacity = '1';
                
                // Start animation if not already running
                if (!animationId) {
                    animateRadius();
                }
                
                const t = e.touches[0];
                moveTo(t.clientX, t.clientY);
            }, { passive: true });
            
            wrap.addEventListener('touchmove', e => {
                const t = e.touches[0];
                moveTo(t.clientX, t.clientY);
            }, { passive: true });
            
            wrap.addEventListener('touchend', e => {
                isHovering = false;
                targetRadius = 0;
                lens.style.opacity = '0';
                
                // Start shrinking animation
                if (!animationId) {
                    animateRadius();
                }
            }, { passive: true });
            
            // Don't initialize in the center by default (lens is hidden)
            // render();
        })();
    });
});

jQuery(document).ready(function($){
    // Initialize skin icon click handlers
    $('.yeori-skin-select .skin-icon').each(function(index) {
        $(this).on('click', function() {
            $('.skin-loupe').removeClass('active');
            $('.skin-loupe').eq(index).addClass('active');
            $('.skin-icon').removeClass('active');
            $(this).addClass('active');
        })
    });

    
    // Initialize Swiper for mobile (below 767px)
    function initSwiper() {
        if (typeof Swiper !== 'undefined') {
            const skinSwiper = new Swiper('.yeori-skin-swiper', {
                slidesPerView: 'auto',
                spaceBetween: 12,
                freeMode: true,
                grabCursor: true,
                speed: 800,
            });
        } 
    }
    
    
    // Initialize on page load
    initSwiper();
    
    // Re-initialize on window resize
    $(window).on('resize', function() {
        // Destroy existing swiper instance if viewport changes
        if (window.innerWidth > 767) {
            $('.yeori-skin-swiper').each(function() {
                if (this.swiper) {
                    this.swiper.destroy(true, true);
                }
            });
        } else {
            setTimeout(initMobileSwiper, 100);
        }
    });
});
