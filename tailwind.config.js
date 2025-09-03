import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import animate from 'tailwindcss-animate';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Filament/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],

    theme: {
        container: {
          center: true,
          padding: "2rem",
          screens: {
            "2xl": "1400px",
          },
        },
        extend: {
            fontFamily: {
                sans: ['Cairo', ...defaultTheme.fontFamily.sans],
                display: ['Cairo', 'Tajawal', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // 🔥 Enhanced Orange Palette
                primary: {
                    50: '#fff7ed',
                    100: '#ffedd5',
                    200: '#fed7aa',
                    300: '#fdba74',
                    400: '#fb923c',
                    500: '#f97316',  // Main Orange
                    600: '#ea580c',
                    700: '#c2410c',
                    800: '#9a3412',
                    900: '#7c2d12',
                    950: '#431407',
                    DEFAULT: '#f97316',
                    foreground: '#ffffff',
                },
                
                // 🌟 Creative Orange Variations
                'orange': {
                    50: '#fff7ed',
                    100: '#ffedd5', 
                    200: '#fed7aa',
                    300: '#fdba74',
                    400: '#fb923c',
                    500: '#f97316',  // Default orange
                    600: '#ea580c',
                    700: '#c2410c',
                    800: '#9a3412',
                    900: '#7c2d12',
                    950: '#431407',
                },
                
                // Original shadcn colors
                border: "hsl(var(--border))",
                input: "hsl(var(--input))",
                ring: "hsl(var(--ring))",
                background: "hsl(var(--background))",
                foreground: "hsl(var(--foreground))",
                secondary: {
                  DEFAULT: "hsl(var(--secondary))",
                  foreground: "hsl(var(--secondary-foreground))",
                },
                destructive: {
                  DEFAULT: "hsl(var(--destructive))",
                  foreground: "hsl(var(--destructive-foreground))",
                },
                muted: {
                  DEFAULT: "hsl(var(--muted))",
                  foreground: "hsl(var(--muted-foreground))",
                },
                accent: {
                  DEFAULT: "hsl(var(--accent))",
                  foreground: "hsl(var(--accent-foreground))",
                },
                popover: {
                  DEFAULT: "hsl(var(--popover))",
                  foreground: "hsl(var(--popover-foreground))",
                },
                card: {
                  DEFAULT: "hsl(var(--card))",
                  foreground: "hsl(var(--card-foreground))",
                },
            },
            
            // 🎨 Enhanced Gradients
            backgroundImage: {
                'gradient-fire': 'linear-gradient(135deg, #ff5722 0%, #f97316 100%)',
                'gradient-sunset': 'linear-gradient(135deg, #ff7043 0%, #ff5722 100%)',
                'gradient-warm': 'linear-gradient(135deg, #fdba74 0%, #f97316 100%)',
                'gradient-soft': 'linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%)',
                'gradient-primary': 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)',
                'gradient-radial': 'radial-gradient(circle at center, #fb923c 0%, #ea580c 100%)',
            },
            
            // 💫 Enhanced Shadows
            boxShadow: {
                'glow': '0 0 30px rgba(249, 115, 22, 0.3)',
                'fire': '0 10px 30px rgba(249, 115, 22, 0.2)',
                '2xl': '0 25px 50px -12px rgba(249, 115, 22, 0.25)',
                'sm': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                'md': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                'lg': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                'xl': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
            },
            
            borderRadius: {
                lg: "var(--radius)",
                md: "calc(var(--radius) - 2px)",
                sm: "calc(var(--radius) - 4px)",
                'none': '0',
                'xs': '0.125rem',
                'DEFAULT': '0.25rem',
                'xl': '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
                'full': '9999px',
            },
            
            keyframes: {
                // Original
                "accordion-down": {
                  from: { height: 0 },
                  to: { height: "var(--radix-accordion-content-height)" },
                },
                "accordion-up": {
                  from: { height: "var(--radix-accordion-content-height)" },
                  to: { height: 0 },
                },
                
                // 🎭 Creative Animations
                "floating": {
                    '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                    '50%': { transform: 'translateY(-15px) rotate(1deg)' },
                },
                "sparkle": {
                    '0%, 100%': { transform: 'scale(0) rotate(0deg)', opacity: '0' },
                    '50%': { transform: 'scale(1) rotate(180deg)', opacity: '1' },
                },
                "bounceIn": {
                    '0%': { transform: 'scale(0.3) rotate(-5deg)', opacity: '0' },
                    '50%': { transform: 'scale(1.1) rotate(2deg)' },
                    '70%': { transform: 'scale(0.9) rotate(-1deg)' },
                    '100%': { transform: 'scale(1) rotate(0deg)', opacity: '1' },
                },
                "slideInRight": {
                    'from': { transform: 'translateX(100%) rotate(5deg)', opacity: '0' },
                    'to': { transform: 'translateX(0) rotate(0deg)', opacity: '1' },
                },
                "fadeInUp": {
                    'from': { transform: 'translateY(40px)', opacity: '0' },
                    'to': { transform: 'translateY(0)', opacity: '1' },
                },
                "pulseOrange": {
                    '0%': { boxShadow: '0 0 0 0 rgba(249, 115, 22, 0.7)' },
                    '70%': { boxShadow: '0 0 0 10px rgba(249, 115, 22, 0)' },
                    '100%': { boxShadow: '0 0 0 0 rgba(249, 115, 22, 0)' },
                },
            },
            
            animation: {
                // Original
                "accordion-down": "accordion-down 0.2s ease-out",
                "accordion-up": "accordion-up 0.2s ease-out",
                
                // 🎨 Creative Animations
                "floating": "floating 4s ease-in-out infinite",
                "sparkle": "sparkle 2s ease-in-out infinite",
                "bounce-in": "bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)",
                "slide-in-right": "slideInRight 0.6s ease-out",
                "fade-in-up": "fadeInUp 0.8s ease-out",
                "pulse-orange": "pulseOrange 2s infinite",
            },
            
            // 📏 Enhanced Spacing
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },
        },
    },

    plugins: [
        forms,
        animate,
    ],
};