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

    // Order status color palette
    const statusColorPalette = {
        pending: '#4285F4',     // Blue
        assigned: '#FBBC05',    // Yellow
        inTransit: '#673AB7',   // Purple
        delivered: '#34A853',   // Green
        cancelled: 'tomato',    // Tomato
        returned: 'red',        // Red
        borders: [
            '#4285F4',  // Pending - Blue
            '#FBBC05',  // Assigned - Yellow
            '#673AB7',  // In-Transit - Purple
            '#34A853',  // Delivered - Green
            'tomato',   // Cancelled - Tomato
            'red'       // Returned - Red
        ],
        backgrounds: [
            'rgba(66, 133, 244, 0.7)',   // Pending - Blue
            'rgba(251, 188, 5, 0.7)',    // Assigned - Yellow
            'rgba(103, 58, 183, 0.7)',   // In-Transit - Purple
            'rgba(52, 168, 83, 0.7)',    // Delivered - Green
            'rgba(255, 99, 71, 0.7)',    // Cancelled - Tomato
            'rgba(255, 0, 0, 0.7)'       // Returned - Red
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

        // Default to daily orders stacked bar chart with status-specific data
        customStatisticsChart = new Chart(customChartCtx, {
            type: 'bar',
            data: {
                labels: dailyOrdersByStatusDates,
                datasets: [
                    {
                        label: 'Pending',
                        data: dailyOrdersPending,
                        backgroundColor: statusColorPalette.backgrounds[0],
                        borderColor: statusColorPalette.borders[0],
                        borderWidth: 1,
                        borderRadius: 4,
                        stack: 'Stack 0'
                    },
                    {
                        label: 'Assigned',
                        data: dailyOrdersAssigned,
                        backgroundColor: statusColorPalette.backgrounds[1],
                        borderColor: statusColorPalette.borders[1],
                        borderWidth: 1,
                        borderRadius: 4,
                        stack: 'Stack 0'
                    },
                    {
                        label: 'In Transit',
                        data: dailyOrdersInTransit,
                        backgroundColor: statusColorPalette.backgrounds[2],
                        borderColor: statusColorPalette.borders[2],
                        borderWidth: 1,
                        borderRadius: 4,
                        stack: 'Stack 0'
                    },
                    {
                        label: 'Delivered',
                        data: dailyOrdersDelivered,
                        backgroundColor: statusColorPalette.backgrounds[3],
                        borderColor: statusColorPalette.borders[3],
                        borderWidth: 1,
                        borderRadius: 4,
                        stack: 'Stack 0'
                    },
                    {
                        label: 'Cancelled',
                        data: dailyOrdersCancelled,
                        backgroundColor: statusColorPalette.backgrounds[4],
                        borderColor: statusColorPalette.borders[4],
                        borderWidth: 1,
                        borderRadius: 4,
                        stack: 'Stack 0'
                    },
                    {
                        label: 'Returned',
                        data: dailyOrdersReturned,
                        backgroundColor: statusColorPalette.backgrounds[5],
                        borderColor: statusColorPalette.borders[5],
                        borderWidth: 1,
                        borderRadius: 4,
                        stack: 'Stack 0'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2.8,
                plugins: {
                    legend: {
                        display: true, // Show legend for status colors
                        position: 'top',
                        labels: {
                            color: '#333333',
                            padding: 10,
                            font: {
                                size: 11
                            },
                            boxWidth: 12,
                            // Don't modify the label text (don't add counts)
                            generateLabels: function(chart) {
                                const datasets = chart.data.datasets;
                                return datasets.map(function(dataset, i) {
                                    return {
                                        text: dataset.label,
                                        fillStyle: dataset.backgroundColor,
                                        strokeStyle: dataset.borderColor,
                                        lineWidth: dataset.borderWidth,
                                        hidden: !chart.isDatasetVisible(i),
                                        index: i
                                    };
                                });
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#333333',
                        bodyColor: '#333333',
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            // Custom tooltip to show status color
                            labelColor: function(context) {
                                if (context.dataset && context.dataset.label) {
                                    // For stacked bar chart with multiple datasets
                                    switch (context.dataset.label) {
                                        case 'Pending':
                                            return {
                                                borderColor: statusColorPalette.borders[0],
                                                backgroundColor: statusColorPalette.backgrounds[0]
                                            };
                                        case 'Assigned':
                                            return {
                                                borderColor: statusColorPalette.borders[1],
                                                backgroundColor: statusColorPalette.backgrounds[1]
                                            };
                                        case 'In Transit':
                                            return {
                                                borderColor: statusColorPalette.borders[2],
                                                backgroundColor: statusColorPalette.backgrounds[2]
                                            };
                                        case 'Delivered':
                                            return {
                                                borderColor: statusColorPalette.borders[3],
                                                backgroundColor: statusColorPalette.backgrounds[3]
                                            };
                                        case 'Cancelled':
                                            return {
                                                borderColor: statusColorPalette.borders[4],
                                                backgroundColor: statusColorPalette.backgrounds[4]
                                            };
                                        case 'Returned':
                                            return {
                                                borderColor: statusColorPalette.borders[5],
                                                backgroundColor: statusColorPalette.backgrounds[5]
                                            };
                                        default:
                                            return {
                                                borderColor: statusColorPalette.borders[0],
                                                backgroundColor: statusColorPalette.backgrounds[0]
                                            };
                                    }
                                } else {
                                    const index = context.dataIndex;
                                    const statusIndex = index % statusColorPalette.backgrounds.length;
                                    return {
                                        borderColor: statusColorPalette.borders[statusIndex],
                                        backgroundColor: statusColorPalette.backgrounds[statusIndex]
                                    };
                                }
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: '#333333'
                        },
                        stacked: true // Enable stacking for bar chart
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: '#333333'
                        },
                        stacked: true // Enable stacking for bar chart
                    }
                }
            }
        });

        // Initialize the legend
        updateStatusLegend('Orders');
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

        // Choose color palette based on data type
        const useStatusColors = dataType === 'Orders';
        const colors = useStatusColors ? statusColorPalette : colorPalette;

        if (chartType === 'Bar Chart') {
            if (useStatusColors) {
                // For Orders data type, use stacked bar chart with status-specific data
                let statusData;
                let statusLabels;

                // Get the appropriate status data based on time period
                if (timePeriod === 'Daily') {
                    statusLabels = dailyOrdersByStatusDates;
                    datasets = [
                        {
                            label: 'Pending',
                            data: dailyOrdersPending,
                            backgroundColor: statusColorPalette.backgrounds[0],
                            borderColor: statusColorPalette.borders[0],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Assigned',
                            data: dailyOrdersAssigned,
                            backgroundColor: statusColorPalette.backgrounds[1],
                            borderColor: statusColorPalette.borders[1],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'In Transit',
                            data: dailyOrdersInTransit,
                            backgroundColor: statusColorPalette.backgrounds[2],
                            borderColor: statusColorPalette.borders[2],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Delivered',
                            data: dailyOrdersDelivered,
                            backgroundColor: statusColorPalette.backgrounds[3],
                            borderColor: statusColorPalette.borders[3],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Cancelled',
                            data: dailyOrdersCancelled,
                            backgroundColor: statusColorPalette.backgrounds[4],
                            borderColor: statusColorPalette.borders[4],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Returned',
                            data: dailyOrdersReturned,
                            backgroundColor: statusColorPalette.backgrounds[5],
                            borderColor: statusColorPalette.borders[5],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        }
                    ];
                    labels = statusLabels;
                } else if (timePeriod === 'Weekly') {
                    statusLabels = weeklyOrdersByStatusLabels;
                    datasets = [
                        {
                            label: 'Pending',
                            data: weeklyOrdersPending,
                            backgroundColor: statusColorPalette.backgrounds[0],
                            borderColor: statusColorPalette.borders[0],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Assigned',
                            data: weeklyOrdersAssigned,
                            backgroundColor: statusColorPalette.backgrounds[1],
                            borderColor: statusColorPalette.borders[1],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'In Transit',
                            data: weeklyOrdersInTransit,
                            backgroundColor: statusColorPalette.backgrounds[2],
                            borderColor: statusColorPalette.borders[2],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Delivered',
                            data: weeklyOrdersDelivered,
                            backgroundColor: statusColorPalette.backgrounds[3],
                            borderColor: statusColorPalette.borders[3],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Cancelled',
                            data: weeklyOrdersCancelled,
                            backgroundColor: statusColorPalette.backgrounds[4],
                            borderColor: statusColorPalette.borders[4],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Returned',
                            data: weeklyOrdersReturned,
                            backgroundColor: statusColorPalette.backgrounds[5],
                            borderColor: statusColorPalette.borders[5],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        }
                    ];
                    labels = statusLabels;
                } else if (timePeriod === 'Monthly') {
                    statusLabels = monthlyOrdersByStatusMonths;
                    datasets = [
                        {
                            label: 'Pending',
                            data: monthlyOrdersPending,
                            backgroundColor: statusColorPalette.backgrounds[0],
                            borderColor: statusColorPalette.borders[0],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Assigned',
                            data: monthlyOrdersAssigned,
                            backgroundColor: statusColorPalette.backgrounds[1],
                            borderColor: statusColorPalette.borders[1],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'In Transit',
                            data: monthlyOrdersInTransit,
                            backgroundColor: statusColorPalette.backgrounds[2],
                            borderColor: statusColorPalette.borders[2],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Delivered',
                            data: monthlyOrdersDelivered,
                            backgroundColor: statusColorPalette.backgrounds[3],
                            borderColor: statusColorPalette.borders[3],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Cancelled',
                            data: monthlyOrdersCancelled,
                            backgroundColor: statusColorPalette.backgrounds[4],
                            borderColor: statusColorPalette.borders[4],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Returned',
                            data: monthlyOrdersReturned,
                            backgroundColor: statusColorPalette.backgrounds[5],
                            borderColor: statusColorPalette.borders[5],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        }
                    ];
                    labels = statusLabels;
                } else if (timePeriod === 'Yearly') {
                    statusLabels = yearlyOrdersByStatusLabels;
                    datasets = [
                        {
                            label: 'Pending',
                            data: yearlyOrdersPending,
                            backgroundColor: statusColorPalette.backgrounds[0],
                            borderColor: statusColorPalette.borders[0],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Assigned',
                            data: yearlyOrdersAssigned,
                            backgroundColor: statusColorPalette.backgrounds[1],
                            borderColor: statusColorPalette.borders[1],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'In Transit',
                            data: yearlyOrdersInTransit,
                            backgroundColor: statusColorPalette.backgrounds[2],
                            borderColor: statusColorPalette.borders[2],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Delivered',
                            data: yearlyOrdersDelivered,
                            backgroundColor: statusColorPalette.backgrounds[3],
                            borderColor: statusColorPalette.borders[3],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Cancelled',
                            data: yearlyOrdersCancelled,
                            backgroundColor: statusColorPalette.backgrounds[4],
                            borderColor: statusColorPalette.borders[4],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        },
                        {
                            label: 'Returned',
                            data: yearlyOrdersReturned,
                            backgroundColor: statusColorPalette.backgrounds[5],
                            borderColor: statusColorPalette.borders[5],
                            borderWidth: 1,
                            borderRadius: 4,
                            stack: 'Stack 0'
                        }
                    ];
                    labels = statusLabels;
                }
            } else {
                // For other data types
                datasets = [{
                    label: dataType,
                    data: chartData,
                    backgroundColor: colorPalette.backgrounds[0],
                    borderColor: colorPalette.borders[0],
                    borderWidth: 1,
                    borderRadius: 4
                }];
            }
        } else if (chartType === 'Line Chart') {
            if (useStatusColors) {
                // For Orders data type with line chart, use multiple lines for each status
                if (timePeriod === 'Daily') {
                    labels = dailyOrdersByStatusDates;
                    datasets = [
                        {
                            label: 'Pending',
                            data: dailyOrdersPending,
                            backgroundColor: 'rgba(66, 133, 244, 0.1)',
                            borderColor: statusColorPalette.borders[0],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[0],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Assigned',
                            data: dailyOrdersAssigned,
                            backgroundColor: 'rgba(251, 188, 5, 0.1)',
                            borderColor: statusColorPalette.borders[1],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[1],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'In Transit',
                            data: dailyOrdersInTransit,
                            backgroundColor: 'rgba(103, 58, 183, 0.1)',
                            borderColor: statusColorPalette.borders[2],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[2],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Delivered',
                            data: dailyOrdersDelivered,
                            backgroundColor: 'rgba(52, 168, 83, 0.1)',
                            borderColor: statusColorPalette.borders[3],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[3],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Cancelled',
                            data: dailyOrdersCancelled,
                            backgroundColor: 'rgba(255, 99, 71, 0.1)',
                            borderColor: statusColorPalette.borders[4],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[4],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Returned',
                            data: dailyOrdersReturned,
                            backgroundColor: 'rgba(255, 0, 0, 0.1)',
                            borderColor: statusColorPalette.borders[5],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[5],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        }
                    ];
                } else if (timePeriod === 'Weekly') {
                    labels = weeklyOrdersByStatusLabels;
                    datasets = [
                        {
                            label: 'Pending',
                            data: weeklyOrdersPending,
                            backgroundColor: 'rgba(66, 133, 244, 0.1)',
                            borderColor: statusColorPalette.borders[0],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[0],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Assigned',
                            data: weeklyOrdersAssigned,
                            backgroundColor: 'rgba(251, 188, 5, 0.1)',
                            borderColor: statusColorPalette.borders[1],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[1],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'In Transit',
                            data: weeklyOrdersInTransit,
                            backgroundColor: 'rgba(103, 58, 183, 0.1)',
                            borderColor: statusColorPalette.borders[2],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[2],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Delivered',
                            data: weeklyOrdersDelivered,
                            backgroundColor: 'rgba(52, 168, 83, 0.1)',
                            borderColor: statusColorPalette.borders[3],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[3],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Cancelled',
                            data: weeklyOrdersCancelled,
                            backgroundColor: 'rgba(255, 99, 71, 0.1)',
                            borderColor: statusColorPalette.borders[4],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[4],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Returned',
                            data: weeklyOrdersReturned,
                            backgroundColor: 'rgba(255, 0, 0, 0.1)',
                            borderColor: statusColorPalette.borders[5],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[5],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        }
                    ];
                } else if (timePeriod === 'Monthly') {
                    labels = monthlyOrdersByStatusMonths;
                    datasets = [
                        {
                            label: 'Pending',
                            data: monthlyOrdersPending,
                            backgroundColor: 'rgba(66, 133, 244, 0.1)',
                            borderColor: statusColorPalette.borders[0],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[0],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Assigned',
                            data: monthlyOrdersAssigned,
                            backgroundColor: 'rgba(251, 188, 5, 0.1)',
                            borderColor: statusColorPalette.borders[1],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[1],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'In Transit',
                            data: monthlyOrdersInTransit,
                            backgroundColor: 'rgba(103, 58, 183, 0.1)',
                            borderColor: statusColorPalette.borders[2],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[2],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Delivered',
                            data: monthlyOrdersDelivered,
                            backgroundColor: 'rgba(52, 168, 83, 0.1)',
                            borderColor: statusColorPalette.borders[3],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[3],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Cancelled',
                            data: monthlyOrdersCancelled,
                            backgroundColor: 'rgba(255, 99, 71, 0.1)',
                            borderColor: statusColorPalette.borders[4],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[4],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Returned',
                            data: monthlyOrdersReturned,
                            backgroundColor: 'rgba(255, 0, 0, 0.1)',
                            borderColor: statusColorPalette.borders[5],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[5],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        }
                    ];
                } else if (timePeriod === 'Yearly') {
                    labels = yearlyOrdersByStatusLabels;
                    datasets = [
                        {
                            label: 'Pending',
                            data: yearlyOrdersPending,
                            backgroundColor: 'rgba(66, 133, 244, 0.1)',
                            borderColor: statusColorPalette.borders[0],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[0],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Assigned',
                            data: yearlyOrdersAssigned,
                            backgroundColor: 'rgba(251, 188, 5, 0.1)',
                            borderColor: statusColorPalette.borders[1],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[1],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'In Transit',
                            data: yearlyOrdersInTransit,
                            backgroundColor: 'rgba(103, 58, 183, 0.1)',
                            borderColor: statusColorPalette.borders[2],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[2],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Delivered',
                            data: yearlyOrdersDelivered,
                            backgroundColor: 'rgba(52, 168, 83, 0.1)',
                            borderColor: statusColorPalette.borders[3],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[3],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Cancelled',
                            data: yearlyOrdersCancelled,
                            backgroundColor: 'rgba(255, 99, 71, 0.1)',
                            borderColor: statusColorPalette.borders[4],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[4],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Returned',
                            data: yearlyOrdersReturned,
                            backgroundColor: 'rgba(255, 0, 0, 0.1)',
                            borderColor: statusColorPalette.borders[5],
                            borderWidth: 2,
                            pointBackgroundColor: statusColorPalette.borders[5],
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: false
                        }
                    ];
                }
            } else {
                // For other data types
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
            }
        }

        // Create chart options based on chart type
        let options = {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2.8,
            plugins: {
                legend: {
                    display: useStatusColors, // Show legend for status colors
                    position: 'top',
                    labels: {
                        color: '#333333',
                        padding: 10,
                        font: {
                            size: 11
                        },
                        boxWidth: 12,
                        // Don't modify the label text (don't add counts)
                        generateLabels: function(chart) {
                            const datasets = chart.data.datasets;
                            return datasets.map(function(dataset, i) {
                                return {
                                    text: dataset.label,
                                    fillStyle: dataset.backgroundColor,
                                    strokeStyle: dataset.borderColor,
                                    lineWidth: dataset.borderWidth,
                                    hidden: !chart.isDatasetVisible(i),
                                    index: i
                                };
                            });
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#333333',
                    bodyColor: '#333333',
                    borderColor: 'rgba(0, 0, 0, 0.1)',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            if (dataType === 'Sales') {
                                return '$' + context.raw.toFixed(2);
                            }
                            return context.raw;
                        },
                        // Custom tooltip to show status color for Orders
                        labelColor: function(context) {
                            if (useStatusColors) {
                                if (context.dataset && context.dataset.label) {
                                    // For stacked bar chart and line chart with multiple datasets
                                    switch (context.dataset.label) {
                                        case 'Pending':
                                            return {
                                                borderColor: statusColorPalette.borders[0],
                                                backgroundColor: statusColorPalette.backgrounds[0]
                                            };
                                        case 'Assigned':
                                            return {
                                                borderColor: statusColorPalette.borders[1],
                                                backgroundColor: statusColorPalette.backgrounds[1]
                                            };
                                        case 'In Transit':
                                            return {
                                                borderColor: statusColorPalette.borders[2],
                                                backgroundColor: statusColorPalette.backgrounds[2]
                                            };
                                        case 'Delivered':
                                            return {
                                                borderColor: statusColorPalette.borders[3],
                                                backgroundColor: statusColorPalette.backgrounds[3]
                                            };
                                        case 'Cancelled':
                                            return {
                                                borderColor: statusColorPalette.borders[4],
                                                backgroundColor: statusColorPalette.backgrounds[4]
                                            };
                                        case 'Returned':
                                            return {
                                                borderColor: statusColorPalette.borders[5],
                                                backgroundColor: statusColorPalette.backgrounds[5]
                                            };
                                        default:
                                            return {
                                                borderColor: statusColorPalette.borders[0],
                                                backgroundColor: statusColorPalette.backgrounds[0]
                                            };
                                    }
                                } else {
                                    // For single dataset bar chart
                                    const index = context.dataIndex;
                                    const statusIndex = index % statusColorPalette.backgrounds.length;
                                    return {
                                        borderColor: statusColorPalette.borders[statusIndex],
                                        backgroundColor: statusColorPalette.backgrounds[statusIndex]
                                    };
                                }
                            } else {
                                return {
                                    borderColor: colorPalette.borders[0],
                                    backgroundColor: colorPalette.backgrounds[0]
                                };
                            }
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: useStatusColors ? '#333333' : colorPalette.secondary,
                        callback: yAxisFormat
                    },
                    stacked: useStatusColors && chartType === 'Bar Chart' // Enable stacking for bar chart with status colors
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: useStatusColors ? '#333333' : colorPalette.secondary
                    },
                    stacked: useStatusColors && chartType === 'Bar Chart' // Enable stacking for bar chart with status colors
                }
            }
        };

        // Create new chart
        const customChartCtx = document.getElementById('customStatisticsChart').getContext('2d');
        customStatisticsChart = new Chart(customChartCtx, {
            type: chartType === 'Bar Chart' ? 'bar' : 'line',
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
        // Empty function - we're not using the custom legend anymore
        // We're using Chart.js built-in legend instead
        const legendContainer = document.getElementById('chartLegend');
        legendContainer.innerHTML = '';
    }
});
