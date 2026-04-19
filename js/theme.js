(function() {
    // 1. Check local storage for theme
    const savedTheme = localStorage.getItem('sg-theme') || 'dark';
    
    // 2. Apply theme immediately to root
    if (savedTheme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
    } else {
        document.documentElement.removeAttribute('data-theme');
    }

    // 3. Wait for DOM content loaded to attach listeners
    document.addEventListener('DOMContentLoaded', () => {
        const themeToggles = document.querySelectorAll('.theme-toggle-btn');
        
        // Update icon based on current theme
        const updateIcons = (theme) => {
            themeToggles.forEach(btn => {
                // Remove any existing i or svg
                btn.innerHTML = '';
                const icon = document.createElement('i');
                if (theme === 'light') {
                    icon.setAttribute('data-lucide', 'moon');
                } else {
                    icon.setAttribute('data-lucide', 'sun');
                }
                btn.appendChild(icon);
            });
            // Re-render lucide icons
            if (window.lucide) {
                window.lucide.createIcons();
            }
        };

        updateIcons(savedTheme);

        themeToggles.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                let currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
                let newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                if (newTheme === 'light') {
                    document.documentElement.setAttribute('data-theme', 'light');
                } else {
                    document.documentElement.removeAttribute('data-theme');
                }
                
                localStorage.setItem('sg-theme', newTheme);
                updateIcons(newTheme);
            });
        });
    });
})();
