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
            
            if (!after || !ring || !handle) return;
            
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
            
            let rect = wrap.getBoundingClientRect();
            
            // Update rect on window resize
            const updateRect = () => {
                rect = wrap.getBoundingClientRect();
            };
            window.addEventListener('resize', updateRect);
            
            let raf = null;
            let _x = rect.left + rect.width * 0.6;
            let _y = rect.top + rect.height * 0.55;
            
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
                
                // AFTER chỉ hiển thị trong vòng tròn
                after.style.clipPath = `circle(${lensSize/2}px at ${lx}px ${ly}px)`;
                
                // Scale AFTER và dịch chuyển để giữ đúng điểm focus
                after.style.transform = `translate(${-lx*(zoom-1)}px, ${-ly*(zoom-1)}px) scale(${zoom})`;
            }
            
            // Update rect when entering a loupe
            wrap.addEventListener('mouseenter', function(e) {
                updateRect();
                moveTo(e.clientX, e.clientY);
            });
            
            // Desktop
            wrap.addEventListener('mousemove', e => moveTo(e.clientX, e.clientY));
            
            // Touch
            wrap.addEventListener('touchstart', e => {
                updateRect();
                const t = e.touches[0];
                moveTo(t.clientX, t.clientY);
            }, { passive: true });
            
            wrap.addEventListener('touchmove', e => {
                const t = e.touches[0];
                moveTo(t.clientX, t.clientY);
            }, { passive: true });
            
            // Khởi tạo ở giữa
            render();
        })();
    });
});
