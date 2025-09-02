/*
====================================
🎨 CREATIVE DESIGN INTERACTIONS
====================================
ملف JavaScript للتأثيرات الإبداعية والتفاعلات
*/

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔥 Creative Design System - Loading...');
    
    // 🔥 Hide Loading Screen with Fire Effect
    setTimeout(() => {
        const loadingScreen = document.getElementById('loading-screen');
        const app = document.getElementById('app');
        
        if (loadingScreen && app) {
            loadingScreen.style.opacity = '0';
            app.style.opacity = '1';
            
            setTimeout(() => {
                loadingScreen.style.display = 'none';
            }, 500);
        }
        
        // Initialize animations
        initializeAnimations();
        console.log('✨ Creative animations initialized!');
    }, 1500);
    
    // 🌟 Initialize Creative Animations
    function initializeAnimations() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                }
            });
        }, observerOptions);
        
        // Observe elements with reveal-on-scroll class
        document.querySelectorAll('.reveal-on-scroll, .fade-in-up, .bounce-in, .slide-in-right').forEach(el => {
            observer.observe(el);
        });
        
        // Initialize staggered animations
        const staggeredElements = document.querySelectorAll('.bounce-in');
        staggeredElements.forEach((el, index) => {
            el.style.animationDelay = `${index * 0.1}s`;
        });
    }
    
    // 🎭 Mobile Menu Toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
            
            // Animate menu items
            const menuItems = mobileMenu.querySelectorAll('a, button');
            menuItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.transform = 'translateX(0)';
                    item.style.opacity = '1';
                }, index * 50);
            });
        });
    }
    
    // ✨ Sparkle Effect Generator
    function createSparkle(element) {
        const sparkle = document.createElement('div');
        sparkle.innerHTML = '✨';
        sparkle.style.position = 'absolute';
        sparkle.style.pointerEvents = 'none';
        sparkle.style.fontSize = '1rem';
        sparkle.style.color = '#f97316';
        sparkle.style.zIndex = '9999';
        sparkle.style.animation = 'sparkle 1s ease-out forwards';
        
        const rect = element.getBoundingClientRect();
        sparkle.style.left = (rect.left + Math.random() * rect.width) + 'px';
        sparkle.style.top = (rect.top + Math.random() * rect.height) + 'px';
        
        document.body.appendChild(sparkle);
        
        setTimeout(() => {
            if (sparkle.parentNode) {
                sparkle.parentNode.removeChild(sparkle);
            }
        }, 1000);
    }
    
    // 🔥 Fire Trail Effect
    document.querySelectorAll('.fire-trail').forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.overflow = 'hidden';
            this.style.position = 'relative';
        });
    });
    
    // 🎯 Interactive Elements
    document.querySelectorAll('.interactive-element, .interactive-card').forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05) translateY(-5px)';
            
            // Add sparkle effect randomly
            if (Math.random() > 0.7) {
                createSparkle(this);
            }
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) translateY(0)';
        });
        
        element.addEventListener('click', function() {
            // Create ripple effect
            const ripple = document.createElement('div');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(249, 115, 22, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.left = '50%';
            ripple.style.top = '50%';
            ripple.style.width = '100px';
            ripple.style.height = '100px';
            ripple.style.marginLeft = '-50px';
            ripple.style.marginTop = '-50px';
            ripple.style.pointerEvents = 'none';
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => {
                if (ripple.parentNode) {
                    ripple.parentNode.removeChild(ripple);
                }
            }, 600);
        });
    });
    
    // 🌈 Color Palette Interactions
    document.querySelectorAll('.color-swatch').forEach(swatch => {
        swatch.addEventListener('click', function() {
            const color = window.getComputedStyle(this).backgroundColor;
            
            // Copy to clipboard if supported
            if (navigator.clipboard) {
                navigator.clipboard.writeText(color).then(() => {
                    showNotification('تم نسخ اللون! 🎨', 'success');
                });
            }
            
            // Visual feedback
            const originalTransform = this.style.transform;
            this.style.transform = 'scale(1.2)';
            setTimeout(() => {
                this.style.transform = originalTransform;
            }, 200);
        });
    });
    
    // 🎉 Notification System
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-4 rounded-2xl shadow-xl z-50 
                                 transition-all duration-300 transform translate-x-full opacity-0`;
        
        // Set notification type styling
        switch(type) {
            case 'success':
                notification.className += ' bg-green-500 text-white';
                break;
            case 'error':
                notification.className += ' bg-red-500 text-white';
                break;
            case 'warning':
                notification.className += ' bg-yellow-500 text-white';
                break;
            default:
                notification.className += ' bg-gradient-primary text-white';
        }
        
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="ml-2">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" 
                        class="text-white hover:text-gray-200 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        }, 100);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
    
    // 🎭 Tab Navigation for Pills
    document.querySelectorAll('.nav-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            // Remove active class from all pills
            document.querySelectorAll('.nav-pill').forEach(p => {
                p.classList.remove('bg-gradient-primary', 'text-white', 'shadow-lg');
                p.classList.add('text-gray-600');
            });
            
            // Add active class to clicked pill
            this.classList.add('bg-gradient-primary', 'text-white', 'shadow-lg');
            this.classList.remove('text-gray-600');
            
            // Handle tab content switching
            const target = this.dataset.tab;
            if (target) {
                document.querySelectorAll('[data-tab-content]').forEach(content => {
                    content.classList.add('hidden');
                });
                
                const targetContent = document.querySelector(`[data-tab-content="${target}"]`);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                    
                    // Animate content
                    targetContent.style.opacity = '0';
                    targetContent.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        targetContent.style.opacity = '1';
                        targetContent.style.transform = 'translateY(0)';
                    }, 50);
                }
            }
        });
    });
    
    // 🌊 Parallax Effect for Hero Sections
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.parallax-bg');
        
        parallaxElements.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            const yPos = -(scrolled * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });
        
        // Update floating elements
        const floatingElements = document.querySelectorAll('.floating');
        floatingElements.forEach((element, index) => {
            const offset = Math.sin(scrolled * 0.01 + index) * 10;
            element.style.transform = `translateY(${offset}px)`;
        });
    });
    
    // 🎨 Dynamic Color Theme Based on Time
    function setTimeBasedTheme() {
        const hour = new Date().getHours();
        const root = document.documentElement;
        
        if (hour >= 6 && hour < 12) {
            // Morning theme - lighter oranges
            root.style.setProperty('--primary-500', '#fb923c');
            root.style.setProperty('--gradient-primary', 'linear-gradient(135deg, #fb923c 0%, #f97316 100%)');
        } else if (hour >= 12 && hour < 18) {
            // Afternoon theme - vibrant oranges
            root.style.setProperty('--primary-500', '#f97316');
            root.style.setProperty('--gradient-primary', 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)');
        } else {
            // Evening/Night theme - deeper oranges
            root.style.setProperty('--primary-500', '#ea580c');
            root.style.setProperty('--gradient-primary', 'linear-gradient(135deg, #ea580c 0%, #c2410c 100%)');
        }
    }
    
    // Apply time-based theme
    setTimeBasedTheme();
    
    // 🚀 Performance Optimization
    // Lazy load images
    const lazyImages = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    lazyImages.forEach(img => imageObserver.observe(img));
    
    // 🎊 Easter Egg - Konami Code
    let konamiCode = [];
    const konamiSequence = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65]; // ↑↑↓↓←→←→BA
    
    document.addEventListener('keydown', function(e) {
        konamiCode.push(e.keyCode);
        
        if (konamiCode.length > konamiSequence.length) {
            konamiCode.shift();
        }
        
        if (konamiCode.length === konamiSequence.length && 
            konamiCode.every((code, index) => code === konamiSequence[index])) {
            
            // Fire effect
            for (let i = 0; i < 20; i++) {
                setTimeout(() => {
                    createSparkle(document.body);
                }, i * 100);
            }
            
            showNotification('🔥 Fire Mode Activated! 🔥', 'success');
            document.body.classList.add('fire-mode');
            
            setTimeout(() => {
                document.body.classList.remove('fire-mode');
            }, 5000);
        }
    });
    
    console.log('🎨 Creative Design System - Ready! ✨');
});

// 🎬 Additional CSS Animations
const additionalStyles = `
<style>
/* 🎭 Additional Creative Animations */
@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

@keyframes float-up {
    0% { transform: translateY(100px); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
}

.fire-mode {
    animation: glow-pulse 0.5s ease-in-out infinite alternate;
}

.fire-glow-hover:hover {
    box-shadow: 0 0 30px rgba(249, 115, 22, 0.5) !important;
    transform: translateY(-5px) !important;
}

.lazy {
    filter: blur(5px);
    transition: filter 0.3s;
}

.parallax-bg {
    will-change: transform;
}

/* 📱 Enhanced Mobile Responsiveness */
@media (max-width: 768px) {
    .floating-animation {
        animation-duration: 2s;
    }
    
    .interactive-element:hover {
        transform: scale(1.02) !important;
    }
    
    .glow-effect {
        animation: none; /* Disable glow on mobile for performance */
    }
}

/* 🎨 Print Styles */
@media print {
    .no-print, .sparkle, .fire-glow, .floating {
        display: none !important;
    }
    
    * {
        animation: none !important;
        transition: none !important;
    }
}

/* 🌗 Dark Mode Enhancements */
@media (prefers-color-scheme: dark) {
    .fire-glow {
        box-shadow: 0 0 20px rgba(249, 115, 22, 0.6) !important;
    }
}

/* ♿ Accessibility Enhancements */
@media (prefers-reduced-motion: reduce) {
    .floating-animation,
    .sparkle-effect,
    .glow-effect {
        animation: none !important;
    }
    
    .interactive-element {
        transition: none !important;
    }
}

/* 🔍 Focus Styles for Accessibility */
button:focus,
a:focus,
input:focus,
select:focus,
textarea:focus {
    outline: 3px solid rgba(249, 115, 22, 0.5) !important;
    outline-offset: 2px !important;
}
</style>
`;

// Inject additional styles
document.head.insertAdjacentHTML('beforeend', additionalStyles);
