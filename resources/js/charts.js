// Chart Management Utilities (using Chart.js)
class ChartManager {
  static initChart(canvasId, type, data, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    const defaultOptions = {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          position: 'bottom',
        },
      },
    };

    const finalOptions = { ...defaultOptions, ...options };

    return new Chart(ctx, {
      type: type,
      data: data,
      options: finalOptions,
    });
  }

  static createLineChartData(labels, values, label = 'Data') {
    return {
      labels: labels,
      datasets: [
        {
          label: label,
          data: values,
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          borderWidth: 2,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#3b82f6',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 4,
        },
      ],
    };
  }

  static createBarChartData(labels, values, label = 'Data') {
    return {
      labels: labels,
      datasets: [
        {
          label: label,
          data: values,
          backgroundColor: [
            '#3b82f6',
            '#0ea5e9',
            '#06b6d4',
            '#10b981',
            '#f59e0b',
            '#ef4444',
          ],
          borderColor: '#e5e7eb',
          borderWidth: 1,
        },
      ],
    };
  }

  static createPieChartData(labels, values, colors = null) {
    const defaultColors = [
      '#10b981',
      '#f59e0b',
      '#ef4444',
      '#3b82f6',
      '#6366f1',
      '#8b5cf6',
    ];

    return {
      labels: labels,
      datasets: [
        {
          data: values,
          backgroundColor: colors || defaultColors.slice(0, values.length),
          borderColor: '#fff',
          borderWidth: 2,
        },
      ],
    };
  }

  static createDoughnutChartData(labels, values, colors = null) {
    return this.createPieChartData(labels, values, colors);
  }

  static lineChartOptions() {
    return {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          position: 'bottom',
        },
        filler: {
          propagate: true,
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0, 0, 0, 0.05)',
          },
        },
        x: {
          grid: {
            display: false,
          },
        },
      },
    };
  }

  static barChartOptions() {
    return {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          position: 'bottom',
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0, 0, 0, 0.05)',
          },
        },
        x: {
          grid: {
            display: false,
          },
        },
      },
    };
  }

  static pieChartOptions() {
    return {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          position: 'right',
        },
      },
    };
  }
}
