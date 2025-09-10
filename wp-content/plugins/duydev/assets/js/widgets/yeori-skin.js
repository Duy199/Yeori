document.addEventListener('DOMContentLoaded', function() {
    // Get all loupe-wrap elements
    const wraps = document.querySelectorAll('.loupe-wrap');
    
    if (wraps.length === 0) return;
    

    // Loop through each loupe instance
    wraps.forEach(function(wrap, index) {
        // Self-contained function for each loupe instance
        (function() {
            const after = wrap.querySelector('.img-after');
            const ring = wrap.querySelector('.ring');
            const handle = wrap.querySelector('.handle');
            const lens = wrap.querySelector('.lens');
            const boomOutBtn = wrap.querySelector('.boom-out');
            
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
            let isMobileHolding = false;
            let currentRadius = 0;
            let targetRadius = 0;
            let animationId = null;
            let isMobile = window.innerWidth <= 767;
            
            // Initialize lens as hidden
            lens.style.opacity = '0';
            after.style.clipPath = `circle(0px at 50% 50%)`;
            
            let rect = wrap.getBoundingClientRect();
            
            // Update rect on window resize
            const updateRect = () => {
                rect = wrap.getBoundingClientRect();
                isMobile = window.innerWidth <= 767;
            };
            window.addEventListener('resize', updateRect);
            
            let raf = null;
            let _x = rect.left + rect.width * 0.6;
            let _y = rect.top + rect.height * 0.55;
            
            function animateRadius() {
                const speed = 0.15; // Animation speed (0-1)
                const diff = targetRadius - currentRadius;
                
                console.log('animateRadius called - current:', currentRadius, 'target:', targetRadius, 'diff:', diff);
                
                if (Math.abs(diff) > 1) {
                    currentRadius += diff * speed;
                    
                    if (isMobileHolding) {
                        // Mobile boom-out effect - expand to cover whole div
                        console.log('Mobile holding - setting clip-path to:', currentRadius);
                        after.style.clipPath = `circle(${currentRadius}px at 50% 50%)`;
                    } else {
                        // Desktop hover effect - follow mouse
                        const lx = _x - rect.left;
                        const ly = _y - rect.top;
                        after.style.clipPath = `circle(${currentRadius}px at ${lx}px ${ly}px)`;
                    }
                    
                    animationId = requestAnimationFrame(animateRadius);
                } else {
                    currentRadius = targetRadius;
                    console.log('Animation complete - final radius:', currentRadius);
                    
                    if (isMobileHolding) {
                        after.style.clipPath = `circle(${currentRadius}px at 50% 50%)`;
                    } else {
                        const lx = _x - rect.left;
                        const ly = _y - rect.top;
                        after.style.clipPath = `circle(${currentRadius}px at ${lx}px ${ly}px)`;
                    }
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
                
                // Update clip-path position if hovering (desktop only)
                if (isHovering && !isMobile) {
                    after.style.clipPath = `circle(${currentRadius}px at ${lx}px ${ly}px)`;
                }
                
                // Scale AFTER và dịch chuyển để giữ đúng điểm focus
                after.style.transform = `translate(${-lx*(zoom-1)}px, ${-ly*(zoom-1)}px) scale(${zoom})`;
            }
            
            // Update rect when entering a loupe (desktop only)
            wrap.addEventListener('mouseenter', function(e) {
                if (isMobile) return; // Skip on mobile
                
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
            
            // Hide lens and shrink circle when mouse leaves (desktop only)
            wrap.addEventListener('mouseleave', function(e) {
                if (isMobile) return; // Skip on mobile
                
                isHovering = false;
                targetRadius = 0;
                lens.style.opacity = '0';
                
                // Start shrinking animation
                if (!animationId) {
                    animateRadius();
                }
            });

            // Desktop mouse move
            wrap.addEventListener('mousemove', e => {
                if (!isMobile) {
                    moveTo(e.clientX, e.clientY);
                }
            });            // Mobile boom-out button functionality
            if (boomOutBtn) {
                console.log('Boom-out button found');
                
                let boomAnimationId = null;
                let boomCurrentRadius = 0;
                let boomTargetRadius = 0;
                let buttonCenterX = 0;
                let buttonCenterY = 0;
                
                function calculateButtonCenter() {
                    const buttonRect = boomOutBtn.getBoundingClientRect();
                    const wrapRect = wrap.getBoundingClientRect();
                    
                    // Vị trí center của button relative to wrap
                    buttonCenterX = buttonRect.left + buttonRect.width / 2 - wrapRect.left;
                    buttonCenterY = buttonRect.top + buttonRect.height / 2 - wrapRect.top;
                    
                    console.log('Button center:', buttonCenterX, buttonCenterY);
                }
                
                function animateBoomOut() {
                    const speed = 0.15; // Animation speed (0-1) - slower for smoother effect
                    const diff = boomTargetRadius - boomCurrentRadius;
                    
                    if (Math.abs(diff) > 1) {
                        boomCurrentRadius += diff * speed;
                        // Clip-path bắt đầu từ vị trí button
                        after.style.clipPath = `circle(${boomCurrentRadius}px at ${buttonCenterX}px ${buttonCenterY}px)`;
                        boomAnimationId = requestAnimationFrame(animateBoomOut);
                    } else {
                        boomCurrentRadius = boomTargetRadius;
                        after.style.clipPath = `circle(${boomCurrentRadius}px at ${buttonCenterX}px ${buttonCenterY}px)`;
                        boomAnimationId = null;
                    }
                }
                
                // Touch start - begin hold
                boomOutBtn.addEventListener('touchstart', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Boom-out touchstart triggered');
                    
                    updateRect();
                    calculateButtonCenter();
                    
                    // Tính radius cần thiết để phủ toàn bộ div từ vị trí button
                    const rectWidth = rect.width;
                    const rectHeight = rect.height;
                    
                    // Tính khoảng cách từ button đến các góc của div
                    const topLeft = Math.sqrt(buttonCenterX * buttonCenterX + buttonCenterY * buttonCenterY);
                    const topRight = Math.sqrt((rectWidth - buttonCenterX) * (rectWidth - buttonCenterX) + buttonCenterY * buttonCenterY);
                    const bottomLeft = Math.sqrt(buttonCenterX * buttonCenterX + (rectHeight - buttonCenterY) * (rectHeight - buttonCenterY));
                    const bottomRight = Math.sqrt((rectWidth - buttonCenterX) * (rectWidth - buttonCenterX) + (rectHeight - buttonCenterY) * (rectHeight - buttonCenterY));
                    
                    // Radius cần thiết là khoảng cách xa nhất
                    const maxRadius = Math.max(topLeft, topRight, bottomLeft, bottomRight) + 50; // +50 để đảm bảo phủ hết
                    
                    console.log('Starting boom animation from button to radius:', maxRadius);
                    
                    // Bắt đầu từ 0 và mở rộng
                    boomTargetRadius = maxRadius;
                    
                    if (!boomAnimationId) {
                        animateBoomOut();
                    }
                    
                }, { passive: false });
                
                // Touch end - release hold
                boomOutBtn.addEventListener('touchend', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Boom-out touchend triggered');
                    
                    // Co lại về 0 tại vị trí button
                    boomTargetRadius = 0;
                    
                    if (!boomAnimationId) {
                        animateBoomOut();
                    }
                    
                }, { passive: false });
                
                // Touch cancel - handle interrupted touch
                boomOutBtn.addEventListener('touchcancel', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Boom-out touchcancel triggered');
                    
                    // Co lại về 0 tại vị trí button
                    boomTargetRadius = 0;
                    
                    if (!boomAnimationId) {
                        animateBoomOut();
                    }
                    
                }, { passive: false });
                
                // Mouse events for desktop testing
                boomOutBtn.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Boom-out mousedown triggered');
                    
                    updateRect();
                    calculateButtonCenter();
                    
                    const rectWidth = rect.width;
                    const rectHeight = rect.height;
                    const topLeft = Math.sqrt(buttonCenterX * buttonCenterX + buttonCenterY * buttonCenterY);
                    const topRight = Math.sqrt((rectWidth - buttonCenterX) * (rectWidth - buttonCenterX) + buttonCenterY * buttonCenterY);
                    const bottomLeft = Math.sqrt(buttonCenterX * buttonCenterX + (rectHeight - buttonCenterY) * (rectHeight - buttonCenterY));
                    const bottomRight = Math.sqrt((rectWidth - buttonCenterX) * (rectWidth - buttonCenterX) + (rectHeight - buttonCenterY) * (rectHeight - buttonCenterY));
                    const maxRadius = Math.max(topLeft, topRight, bottomLeft, bottomRight) + 50;
                    
                    boomTargetRadius = maxRadius;
                    
                    if (!boomAnimationId) {
                        animateBoomOut();
                    }
                });
                
                boomOutBtn.addEventListener('mouseup', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Boom-out mouseup triggered');
                    
                    boomTargetRadius = 0;
                    
                    if (!boomAnimationId) {
                        animateBoomOut();
                    }
                });
                
                boomOutBtn.addEventListener('mouseleave', function(e) {
                    boomTargetRadius = 0;
                    
                    if (!boomAnimationId) {
                        animateBoomOut();
                    }
                });
                
            } else {
                console.log('Boom-out button NOT found');
            }

            // Touch events for the wrap (keep existing functionality for non-mobile)
            wrap.addEventListener('touchstart', e => {
                if (isMobile) return; // Skip on mobile, use boom-out button instead
                
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
                if (!isMobile) {
                    const t = e.touches[0];
                    moveTo(t.clientX, t.clientY);
                }
            }, { passive: true });
            
            wrap.addEventListener('touchend', e => {
                if (isMobile) return; // Skip on mobile
                
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
    $('.yeori-skin-select .swiper-slide').each(function(index) {
        $(this).on('click', function() {
            $('.skin-loupe').removeClass('active');
            $('.skin-loupe').eq(index).addClass('active');
            $('.yeori-skin-select .swiper-slide').removeClass('active');
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
