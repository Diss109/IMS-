/**
 * KPI Dashboard Charts - Dynamic visualization for admin dashboard
 */

// Make sure we initialize after the page is fully loaded
window.addEventListener('load', function() {
    console.log('KPI Dashboard: Window loaded, initializing charts...');
    
    // Chart.js global configuration
    if (typeof Chart !== 'undefined') {
        console.log('Chart.js is loaded!');
        Chart.defaults.font.family = "'Segoe UI', 'Helvetica Neue', 'Arial', sans-serif";
        Chart.defaults.color = '#666';
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;
        
        // Initialize all charts
        setTimeout(function() {
            initializeCharts();
            // Setup event listeners
            setupEventListeners();
            // Fix rendering issues in some browsers
            fixChartRendering();
        }, 100);
        
        // Fix on window resize
        window.addEventListener('resize', fixChartRendering);
    } else {
        console.error('Chart.js is not loaded! Charts cannot be initialized.');
    }
});

/**
 * Initialize all charts if their containers exist
 */
function initializeCharts() {
    if (document.getElementById('complaints-trend-chart')) {
        createComplaintsTrendChart();
    }
    
    if (document.getElementById('complaints-type-chart')) {
        createComplaintsTypeChart();
    }
    
    if (document.getElementById('status-chart')) {
        createStatusChart();
    }
    
    if (document.getElementById('urgency-chart')) {
        createUrgencyChart();
    }
}

/**
 * Set up event listeners for dashboard interactions
 */
function setupEventListeners() {
    // Filter form submission
    const filterForm = document.getElementById('kpi-filter-form');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const dateRange = document.getElementById('date-range').value;
            const typeFilter = document.getElementById('type-filter').value;
            
            // Show notification (would be replaced with an AJAX call in production)
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = 1050;
            toast.innerHTML = `
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Filtres appliqués</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Période: ${dateRange} jours, Type: ${typeFilter === 'all' ? 'Tous' : typeFilter}
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 3000);
        });
    }
    
    // Export button
    const exportBtn = document.getElementById('export-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            // In a real application, this would generate a report
            alert('Export de rapport en cours...');
        });
    }
}

/**
 * Fix common Chart.js canvas rendering issues
 */
function fixChartRendering() {
    const allCanvases = document.querySelectorAll('canvas');
    allCanvases.forEach(canvas => {
        const parent = canvas.parentElement;
        if (parent && parent.style.height) {
            // Make sure canvas takes parent height
            canvas.style.width = '100%';
            canvas.style.height = '100%';
            canvas.height = parent.offsetHeight;
            canvas.width = parent.offsetWidth;
        }
    });
}

/**
 * Create complaints trend line chart
 */
function createComplaintsTrendChart() {
    const ctx = document.getElementById('complaints-trend-chart').getContext('2d');
    
    // Get dynamic data from the PHP variables passed to the view
    const labels = trendData.labels || [];
    const newComplaints = trendData.new || [];
    const resolvedComplaints = trendData.resolved || [];
    
    const trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Nouvelles Réclamations',
                    data: newComplaints,
                    borderColor: '#4776E6',
                    backgroundColor: 'rgba(71, 118, 230, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Réclamations Résolues',
                    data: resolvedComplaints,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

/**
 * Create complaints type distribution doughnut chart
 */
function createComplaintsTypeChart() {
    const ctx = document.getElementById('complaints-type-chart').getContext('2d');
    
    const typeLabels = ['Retard livraison', 'Retard chargement', 'Marchandise endommagée', 'Mauvais comportement', 'Autre'];
    
    const backgroundColors = [
        'rgba(54, 162, 235, 0.8)',
        'rgba(75, 192, 192, 0.8)',
        'rgba(255, 99, 132, 0.8)',
        'rgba(255, 159, 64, 0.8)',
        'rgba(153, 102, 255, 0.8)'
    ];
    
    const typeChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeof typeData !== 'undefined' ? typeData : [30, 25, 15, 20, 10],
                backgroundColor: backgroundColors,
                borderWidth: 1,
                borderColor: '#fff'
            }]
        },
        options: {
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Create status distribution bar chart
 */
function createStatusChart() {
    const ctx = document.getElementById('status-chart').getContext('2d');
    
    const statusLabels = ['En attente', 'Résolu', 'Non résolu'];
    
    const backgroundColors = [
        '#FF9800',  // En attente - Orange
        '#4CAF50',  // Résolu - Green
        '#F44336'   // Non résolu - Red
    ];
    
    const statusChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: statusLabels,
            datasets: [{
                label: 'Nombre de réclamations',
                data: typeof statusData !== 'undefined' ? statusData : [45, 30, 15],
                backgroundColor: backgroundColors,
                borderRadius: 6,
                borderWidth: 0,
                maxBarThickness: 60
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

/**
 * Create urgency distribution pie chart
 */
function createUrgencyChart() {
    const ctx = document.getElementById('urgency-chart').getContext('2d');
    
    const urgencyLabels = ['Critique', 'Élevée', 'Moyenne', 'Faible'];
    
    const backgroundColors = [
        '#F44336',  // Critique - Red
        '#FF9800',  // Élevée - Orange 
        '#4776E6',  // Moyenne - Blue
        '#4CAF50'   // Faible - Green
    ];
    
    const urgencyChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: urgencyLabels,
            datasets: [{
                data: typeof urgencyData !== 'undefined' ? urgencyData : [10, 25, 45, 20],
                backgroundColor: backgroundColors,
                borderWidth: 1,
                borderColor: '#fff'
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}
