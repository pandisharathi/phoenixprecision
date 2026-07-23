document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const adminWrapper = document.querySelector('.admin-wrapper');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            
            // Optional: prevent scroll on body when sidebar is open on mobile
            if (window.innerWidth < 992) {
                if (sidebar.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 992 && 
            sidebar.classList.contains('active') && 
            !sidebar.contains(event.target) && 
            !sidebarToggle.contains(event.target)) {
            sidebar.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // Auto-scroll sidebar to active menu item
    const activeLink = document.querySelector('.sidebar .nav-link.active');
    if (activeLink) {
        activeLink.scrollIntoView({ block: 'nearest' });
    }
});
