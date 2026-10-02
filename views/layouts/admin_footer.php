            </main>
        </div>
    </div>

    <!-- Admin Global Scripts -->
    <script>
        // Theme Switcher (fallback if not already defined in admin_header)
        if (typeof window.toggleTheme !== 'function') {
            window.toggleTheme = function() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    try { localStorage.setItem('theme', 'light'); } catch (e) {}
                } else {
                    document.documentElement.classList.add('dark');
                    try { localStorage.setItem('theme', 'dark'); } catch (e) {}
                }
            };
        }

        // Mobile Sidebar Drawer Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar && overlay) {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            }
        }

        // Close sidebar on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('adminSidebar');
                const overlay = document.getElementById('sidebarOverlay');
                if (sidebar && sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('active');
                }
            }
        });

        // Auto dismiss flash alerts after 6s
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    if (alert && alert.parentElement) {
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateY(-10px)';
                        alert.style.transition = 'all 0.3s ease';
                        setTimeout(function() { alert.remove(); }, 300);
                    }
                }, 6000);
            });
        });
    </script>
</body>
</html>
