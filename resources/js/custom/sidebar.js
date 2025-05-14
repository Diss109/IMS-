/**
 * Custom sidebar script to ensure text is visible while preserving hover behavior
 */
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const sidebar = document.querySelector('.sidebar');

    if (sidebar) {
        // Add hover event listeners
        sidebar.addEventListener('mouseenter', function() {
            sidebar.classList.remove('collapsed');

            // Make text visible on hover
            document.querySelectorAll('.sidebar-link-text').forEach(text => {
                text.style.opacity = '1';
                text.style.transition = 'opacity 0.3s ease';
            });
        });

        sidebar.addEventListener('mouseleave', function() {
            if (!sidebar.classList.contains('active-navigation')) {
                sidebar.classList.add('collapsed');
            }
        });

        // Add click handler for sidebar toggle
        const toggleBtn = document.querySelector('.sidebar-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                sidebar.classList.toggle('active-navigation');
            });
        }
    }
});
