document.addEventListener('DOMContentLoaded', function() {
    // Mobile Sidebar Toggle
    const mobileToggle = document.getElementById('mobile-toggle');
    const sidebar = document.querySelector('.sidebar');
    const adminLayout = document.querySelector('.admin-layout');
    
    if(mobileToggle && sidebar) {
        mobileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');
            if(adminLayout) adminLayout.classList.toggle('sidebar-active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992 && sidebar.classList.contains('active')) {
                if (!sidebar.contains(e.target) && e.target !== mobileToggle) {
                    sidebar.classList.remove('active');
                    if(adminLayout) adminLayout.classList.remove('sidebar-active');
                }
            }
        });
    }
});
