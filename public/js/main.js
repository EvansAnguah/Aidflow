/**
 * AidFlow - Core Javascript
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Dark Mode / Light Mode Theme Switching
    const themeToggle = document.getElementById('themeToggle');
    const currentTheme = localStorage.getItem('theme') || 'light';

    if (currentTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        if (themeToggle) {
            themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        }
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            let theme = document.documentElement.getAttribute('data-theme');
            if (theme === 'dark') {
                document.documentElement.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
            }
        });
    }

    // 2. DataTables Initialization
    const tables = document.querySelectorAll('.datatable');
    if (tables.length > 0 && typeof $ !== 'undefined') {
        tables.forEach(table => {
            $(table).DataTable({
                responsive: true,
                pageLength: 10,
                order: []
            });
        });
    }

    // 3. Notification Center AJAX Polling
    const bell = document.getElementById('notificationBell');
    const badge = document.getElementById('notificationBadge');
    const list = document.getElementById('notificationList');

    if (bell && badge && list) {
        // Fetch notifications on load
        fetchNotifications();

        // Refresh every 30 seconds
        setInterval(fetchNotifications, 30000);

        bell.addEventListener('click', function() {
            // Mark all read or show dropdown
        });
    }

    function fetchNotifications() {
        const baseUrl = '/AidFlow';
        fetch(`${baseUrl}/notification/getUnread`)
            .then(response => response.json())
            .then(data => {
                // Update badge
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }

                // Render latest notifications in dropdown
                list.innerHTML = '';
                if (data.notifications.length === 0) {
                    list.innerHTML = '<li><a class="dropdown-item text-center text-muted" href="#">No notifications</a></li>';
                } else {
                    data.notifications.forEach(notif => {
                        const activeClass = notif.status === 'Unread' ? 'fw-bold bg-light text-dark' : '';
                        const dateFormatted = new Date(notif.created_at).toLocaleString();
                        
                        list.innerHTML += `
                            <li class="border-bottom">
                                <a class="dropdown-item p-3 ${activeClass}" href="#" onclick="markRead(${notif.id}, event)">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-truncate">${notif.title}</span>
                                    </div>
                                    <p class="mb-1 text-muted small text-wrap">${notif.message}</p>
                                    <span class="text-muted extra-small font-italic">${dateFormatted}</span>
                                </a>
                            </li>
                        `;
                    });
                    list.innerHTML += `
                        <li><a class="dropdown-item text-center text-primary font-weight-bold" href="${baseUrl}/notification">View All History</a></li>
                    `;
                }
            })
            .catch(err => console.error("Error loading notifications: ", err));
    }

    // Global helper to mark single notification read
    window.markRead = function(id, event) {
        if(event) event.preventDefault();
        const baseUrl = '/AidFlow';
        fetch(`${baseUrl}/notification/read/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchNotifications();
                }
            });
    };

    // 4. Analytics Chart.js rendering
    const contributionChartCanvas = document.getElementById('contributionsChart');
    const categoriesChartCanvas = document.getElementById('categoriesChart');

    if (contributionChartCanvas || categoriesChartCanvas) {
        const baseUrl = '/AidFlow';
        fetch(`${baseUrl}/dashboard/getAnalyticsData`)
            .then(res => res.json())
            .then(data => {
                if (contributionChartCanvas) {
                    const ctx = contributionChartCanvas.getContext('2d');
                    
                    const labels = data.contributions.map(c => c.month_label);
                    const totals = data.contributions.map(c => parseFloat(c.total));

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Monthly Contributions ($)',
                                data: totals,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });
                }

                if (categoriesChartCanvas) {
                    const ctx = categoriesChartCanvas.getContext('2d');
                    
                    const labels = data.categories.map(c => c.label);
                    const counts = data.categories.map(c => parseInt(c.value));
                    const colors = [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1', '#ec4899', '#8b5cf6'
                    ];

                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: counts,
                                backgroundColor: colors.slice(0, labels.length)
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                }
            })
            .catch(err => console.error("Error loading analytics charts: ", err));
    }
});
