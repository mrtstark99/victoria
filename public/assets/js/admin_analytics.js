// Admin Analytics Chart.js Initialization
function initAnalyticsTrendChart(labels, localViewsData) {
    const canvas = document.getElementById('trendChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    // Theme colors
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#9ca3af' : '#4b5563';
    const gridColor = isDark ? '#262626' : '#f3f4f6';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Lượt xem Thực tế (Pageviews)',
                    data: localViewsData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.08)',
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: textColor,
                        font: {
                            family: 'Inter, system-ui, sans-serif',
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    padding: 12,
                    cornerRadius: 10,
                    bodyFont: {
                        family: 'Inter, system-ui, sans-serif'
                    },
                    titleFont: {
                        family: 'Inter, system-ui, sans-serif',
                        weight: '700'
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: gridColor
                    },
                    ticks: {
                        color: textColor,
                        font: {
                            family: 'Inter, system-ui, sans-serif',
                            size: 11
                        }
                    }
                },
                y: {
                    grid: {
                        color: gridColor
                    },
                    ticks: {
                        color: textColor,
                        stepSize: 1,
                        font: {
                            family: 'Inter, system-ui, sans-serif',
                            size: 11
                        }
                    }
                }
            }
        }
    });
}
