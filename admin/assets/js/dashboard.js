// Dashboard Charts

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Color palette for charts (soft beige/brown colors)
    const colorPalette = {
        primary: '#5d4037',
        secondary: '#8d6e63',
        tertiary: '#a1887f',
        quaternary: '#bcaaa4',
        quinary: '#d7ccc8',
        background: 'rgba(217, 199, 171, 0.2)',
        borders: [
            '#5d4037',
            '#8d6e63',
            '#a1887f',
            '#bcaaa4',
            '#d7ccc8',
            '#efebe9'
        ],
        backgrounds: [
            'rgba(93, 64, 55, 0.7)',
            'rgba(141, 110, 99, 0.7)',
            'rgba(161, 136, 127, 0.7)',
            'rgba(188, 170, 164, 0.7)',
            'rgba(215, 204, 200, 0.7)',
            'rgba(239, 235, 233, 0.7)'
        ]
    };

    // Product Type Distribution Chart
    const productTypeCtx = document.getElementById('productTypeChart').getContext('2d');
    const productTypeChart = new Chart(productTypeCtx, {
        type: 'doughnut',
        data: {
            labels: productTypes,
            datasets: [{
                data: productCounts,
                backgroundColor: colorPalette.backgrounds,
                borderColor: colorPalette.borders,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: colorPalette.secondary,
                        padding: 15,
                        font: {
                            size: 12
                        },
                        boxWidth: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: colorPalette.primary,
                    bodyColor: colorPalette.secondary,
                    borderColor: colorPalette.quinary,
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8
                }
            },
            cutout: '65%'
        }
    });

    // Custom Statistics Chart
    let customStatisticsChart;

    // Initialize custom statistics chart
    function initCustomStatisticsChart() {
        const customChartCtx = document.getElementById('customStatisticsChart').getContext('2d');

        // Default to daily orders bar chart
        customStatisticsChart = new Chart(customChartCtx, {
            type: 'bar',
            data: {
                labels: dailyOrdersDates,
                datasets: [{
                    label: 'Orders',
                    data: dailyOrdersData,
                    backgroundColor: colorPalette.backgrounds[0],
                    borderColor: colorPalette.borders[0],
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2.8,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: colorPalette.primary,
                        bodyColor: colorPalette.secondary,
                        borderColor: colorPalette.quinary,
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(93, 64, 55, 0.05)'
                        },
                        ticks: {
                            color: colorPalette.secondary
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(93, 64, 55, 0.05)'
                        },
                        ticks: {
                            color: colorPalette.secondary
                        }
                    }
                }
            }
        });
    }

    // Initialize the custom chart
    initCustomStatisticsChart();

    // Handle data type change
    document.getElementById('dataType').addEventListener('change', updateCustomChart);

    // Handle chart type change
    document.getElementById('chartType').addEventListener('change', updateCustomChart);

    // Handle time period change
    document.getElementById('timePeriod').addEventListener('change', updateCustomChart);

    // Update custom chart based on selections
    function updateCustomChart() {
        const dataType = document.getElementById('dataType').value;
        const chartType = document.getElementById('chartType').value;
        const timePeriod = document.getElementById('timePeriod').value;

        // Update chart title
        const chartTitle = document.getElementById('customChartTitle');
        chartTitle.textContent = `${dataType} by ${timePeriod} Period`;

        // Destroy existing chart
        customStatisticsChart.destroy();

        // Get data based on data type
        let chartData;
        let yAxisFormat = null;

        switch(dataType) {
            case 'Orders':
                if (timePeriod === 'Daily') {
                    chartData = dailyOrdersData;
                } else if (timePeriod === 'Weekly') {
                    chartData = weeklyOrdersData;
                } else if (timePeriod === 'Yearly') {
                    chartData = yearlyOrdersData;
                } else {
                    // Default to monthly
                    chartData = monthlyOrdersData;
                }
                break;
            case 'Sales':
                if (timePeriod === 'Daily') {
                    chartData = dailySalesData;
                } else if (timePeriod === 'Weekly') {
                    chartData = weeklySalesData;
                } else if (timePeriod === 'Yearly') {
                    chartData = yearlySalesData;
                } else {
                    // Default to monthly
                    chartData = monthlySalesData;
                }
                yAxisFormat = value => '$' + value;
                break;
            case 'Customers':
                if (timePeriod === 'Daily') {
                    chartData = dailyCustomersData;
                } else if (timePeriod === 'Weekly') {
                    chartData = weeklyCustomersData;
                } else if (timePeriod === 'Yearly') {
                    chartData = yearlyCustomersData;
                } else {
                    // Default to monthly
                    chartData = monthlyCustomersData;
                }
                break;
            case 'Products':
                if (timePeriod === 'Daily') {
                    chartData = dailyProductsData;
                } else if (timePeriod === 'Weekly') {
                    chartData = weeklyProductsData;
                } else if (timePeriod === 'Yearly') {
                    chartData = yearlyProductsData;
                } else {
                    // Default to monthly
                    chartData = monthlyProductsData;
                }
                break;
            default:
                if (timePeriod === 'Daily') {
                    chartData = dailyOrdersData;
                } else if (timePeriod === 'Weekly') {
                    chartData = weeklyOrdersData;
                } else if (timePeriod === 'Yearly') {
                    chartData = yearlyOrdersData;
                } else {
                    // Default to monthly
                    chartData = monthlyOrdersData;
                }
        }

        // Get labels based on time period
        let labels;
        switch(timePeriod) {
            case 'Daily':
                // Use the appropriate dates based on data type
                switch(dataType) {
                    case 'Orders':
                        labels = dailyOrdersDates;
                        break;
                    case 'Sales':
                        labels = dailySalesDates;
                        break;
                    case 'Customers':
                        labels = dailyCustomersDates;
                        break;
                    case 'Products':
                        labels = dailyProductsDates;
                        break;
                    default:
                        labels = dailyOrdersDates;
                }
                break;
            case 'Weekly':
                // Use the appropriate week labels based on data type
                switch(dataType) {
                    case 'Orders':
                        labels = weeklyOrdersLabels;
                        break;
                    case 'Sales':
                        labels = weeklySalesLabels;
                        break;
                    case 'Customers':
                        labels = weeklyCustomersLabels;
                        break;
                    case 'Products':
                        labels = weeklyProductsLabels;
                        break;
                    default:
                        labels = weeklyOrdersLabels;
                }
                break;
            case 'Monthly':
                labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                break;
            case 'Yearly':
                // Use the appropriate year labels based on data type
                switch(dataType) {
                    case 'Orders':
                        labels = yearlyOrdersLabels;
                        break;
                    case 'Sales':
                        labels = yearlySalesLabels;
                        break;
                    case 'Customers':
                        labels = yearlyCustomersLabels;
                        break;
                    case 'Products':
                        labels = yearlyProductsLabels;
                        break;
                    default:
                        labels = yearlyOrdersLabels;
                }
                break;
            default:
                labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        }

        // Create datasets based on chart type and data type
        let datasets = [];

        if (chartType === 'Bar Chart') {
            datasets = [{
                label: dataType,
                data: chartData,
                backgroundColor: colorPalette.backgrounds,
                borderColor: colorPalette.borders,
                borderWidth: 1,
                borderRadius: 4
            }];
        } else if (chartType === 'Line Chart') {
            datasets = [{
                label: dataType,
                data: chartData,
                backgroundColor: 'rgba(93, 64, 55, 0.1)',
                borderColor: colorPalette.primary,
                borderWidth: 2,
                pointBackgroundColor: colorPalette.primary,
                pointBorderColor: '#fff',
                pointBorderWidth: 1,
                pointRadius: 4,
                tension: 0.3,
                fill: true
            }];
        } else if (chartType === 'Pie Chart') {
            datasets = [{
                label: dataType,
                data: chartData,
                backgroundColor: colorPalette.backgrounds,
                borderColor: colorPalette.borders,
                borderWidth: 1
            }];
        }

        // Create chart options based on chart type
        let options = {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: chartType === 'Pie Chart' ? 1.8 : 2.8,
            plugins: {
                legend: {
                    display: chartType === 'Pie Chart',
                    position: 'right',
                    labels: {
                        color: colorPalette.secondary,
                        padding: 15,
                        font: {
                            size: 12
                        },
                        boxWidth: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: colorPalette.primary,
                    bodyColor: colorPalette.secondary,
                    borderColor: colorPalette.quinary,
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            if (dataType === 'Sales') {
                                return '$' + context.raw.toFixed(2);
                            }
                            return context.raw;
                        }
                    }
                }
            }
        };

        // Add scales for bar and line charts
        if (chartType !== 'Pie Chart') {
            options.scales = {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(93, 64, 55, 0.05)'
                    },
                    ticks: {
                        color: colorPalette.secondary,
                        callback: yAxisFormat
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(93, 64, 55, 0.05)'
                    },
                    ticks: {
                        color: colorPalette.secondary
                    }
                }
            };
        } else {
            options.cutout = '65%';
        }

        // Create new chart
        const customChartCtx = document.getElementById('customStatisticsChart').getContext('2d');
        customStatisticsChart = new Chart(customChartCtx, {
            type: chartType === 'Bar Chart' ? 'bar' : (chartType === 'Line Chart' ? 'line' : 'doughnut'),
            data: {
                labels: labels,
                datasets: datasets
            },
            options: options
        });

        // Update legend for status colors if showing order status data
        updateStatusLegend(dataType);
    }

    // Update status legend based on data type
    function updateStatusLegend(dataType) {
        const legendContainer = document.getElementById('chartLegend');
        legendContainer.innerHTML = '';

        if (dataType === 'Orders') {
            // Create legend for order statuses
            const statuses = ['Pending', 'Processing', 'Assigned', 'In-Transit', 'Delivered', 'Cancelled', 'Returned'];
            const colors = colorPalette.backgrounds;

            statuses.forEach((status, index) => {
                if (index < colors.length) {
                    const legendItem = document.createElement('div');
                    legendItem.className = 'legend-item';

                    const colorBox = document.createElement('div');
                    colorBox.className = 'legend-color';
                    colorBox.style.backgroundColor = colors[index];

                    const label = document.createElement('span');
                    label.className = 'legend-label';
                    label.textContent = status;

                    legendItem.appendChild(colorBox);
                    legendItem.appendChild(label);
                    legendContainer.appendChild(legendItem);
                }
            });
        }
    }
});
