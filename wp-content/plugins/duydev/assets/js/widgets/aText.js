/**
 * AText Animation - Reusable text animation based on React AText component
 * Usage: Add class 'atext' to any element to enable animation
 * Replay: Call ATextManager.replay(element) or add/remove 'atext-trigger' class
 */

class ATextManager {
    constructor() {
        this.instances = new Map();
        this.observer = null;
        this.init();
    }

    init() {
        // Wait for SplitText to be available
        this.waitForSplitText(() => {
            this.setupObserver();
            this.initializeElements();
        });
        console.log('oke')
    }

    waitForSplitText(callback) {
        if (typeof SplitText !== 'undefined') {
            callback();
        } else {
            setTimeout(() => this.waitForSplitText(callback), 100);
        }
    }

    setupObserver() {
        // Create intersection observer for automatic triggering
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const element = entry.target;
                if (entry.isIntersecting) {
                    this.animateIn(element);
                } else {
                    this.animateOut(element);
                }
            });
        }, {
            threshold: 0.3,
            rootMargin: '0px 0px -10% 0px'
        });
    }

    initializeElements() {
        // Initialize all existing atext elements
        document.querySelectorAll('.atext').forEach(element => {
            this.createInstance(element);
        });

        // Watch for new atext elements
        const mutationObserver = new MutationObserver((mutations) => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) { // Element node
                        if (node.classList && node.classList.contains('atext')) {
                            this.createInstance(node);
                        }
                        // Check children too
                        const atextElements = node.querySelectorAll && node.querySelectorAll('.atext');
                        if (atextElements) {
                            atextElements.forEach(element => this.createInstance(element));
                        }
                    }
                });
            });
        });

        mutationObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    createInstance(element) {
        if (this.instances.has(element)) return;

        // Get animation settings from data attributes or use defaults
        const settings = {
            stagger: parseFloat(element.dataset.stagger || '0.02'),
            duration: parseFloat(element.dataset.duration || '1'),
            ease: element.dataset.ease || 'cubic-bezier(0.19, 1, 0.22, 1)',
            autoTrigger: element.dataset.autoTrigger !== 'false'
        };

        // Create SplitText instance
        const split = new SplitText(element, {
            type: 'chars,words',
            charsClass: 'char',
            wordsClass: 'word'
        });

        // Apply initial styles to characters
        split.chars.forEach((char, i) => {
            char.style.display = 'inline-block';
            char.style.transform = 'translateY(200%)';
            char.style.transition = `transform ${settings.duration}s ${settings.ease} ${i * settings.stagger}s`;
            char.style.willChange = 'transform';
        });

        // Store instance
        const instance = {
            element,
            split,
            settings,
            isAnimated: false
        };
        this.instances.set(element, instance);

        // Add to intersection observer if auto-trigger is enabled
        if (settings.autoTrigger) {
            this.observer.observe(element);
        }

        // Listen for manual trigger class changes
        this.watchTriggerClass(element);

        return instance;
    }

    watchTriggerClass(element) {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach(mutation => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (element.classList.contains('atext-trigger')) {
                        this.animateIn(element);
                    } else if (element.classList.contains('atext-reset')) {
                        this.animateOut(element);
                        element.classList.remove('atext-reset');
                    }
                }
            });
        });

        observer.observe(element, {
            attributes: true,
            attributeFilter: ['class']
        });
    }

    animateIn(element) {
        const instance = this.instances.get(element);
        if (!instance || instance.isAnimated) return;

        const { split, settings } = instance;
        
        split.chars.forEach((char, i) => {
            char.style.transitionDelay = `${i * settings.stagger}s`;
            char.style.transform = 'translateY(0%)';
        });

        instance.isAnimated = true;

        // Dispatch custom event
        element.dispatchEvent(new CustomEvent('atext:animateIn', {
            detail: { instance }
        }));
    }

    animateOut(element) {
        const instance = this.instances.get(element);
        if (!instance || !instance.isAnimated) return;

        const { split } = instance;
        
        split.chars.forEach((char) => {
            char.style.transitionDelay = '0s';
            char.style.transform = 'translateY(200%)';
        });

        instance.isAnimated = false;

        // Dispatch custom event
        element.dispatchEvent(new CustomEvent('atext:animateOut', {
            detail: { instance }
        }));
    }

    replay(element) {
        this.animateOut(element);
        setTimeout(() => {
            this.animateIn(element);
        }, 50);
    }

    destroy(element) {
        const instance = this.instances.get(element);
        if (!instance) return;

        // Revert SplitText
        instance.split.revert();
        
        // Remove from observer
        this.observer.unobserve(element);
        
        // Remove from instances
        this.instances.delete(element);
    }

    destroyAll() {
        this.instances.forEach((instance, element) => {
            this.destroy(element);
        });
        
        if (this.observer) {
            this.observer.disconnect();
        }
    }

    // Static methods for global access
    static replay(element) {
        if (window.aTextManager) {
            window.aTextManager.replay(element);
        }
    }

    static animateIn(element) {
        if (window.aTextManager) {
            window.aTextManager.animateIn(element);
        }
    }

    static animateOut(element) {
        if (window.aTextManager) {
            window.aTextManager.animateOut(element);
        }
    }

    static create(element) {
        if (window.aTextManager) {
            return window.aTextManager.createInstance(element);
        }
    }
}

// Auto-initialize when DOM is ready
function initAText() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.aTextManager = new ATextManager();
        });
    } else {
        window.aTextManager = new ATextManager();
    }
}

// Initialize
initAText();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ATextManager;
}
