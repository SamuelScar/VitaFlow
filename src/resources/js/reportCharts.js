import Chart from 'chart.js/auto';

const charts = new WeakMap();

function renderReportChart(root) {
    const canvas = root?.querySelector('[data-report-chart]');
    const configElement = root?.querySelector('[data-report-chart-config]');

    if (!canvas || !configElement) {
        return;
    }

    const existingChart = charts.get(canvas) || Chart.getChart(canvas);

    if (existingChart) {
        existingChart.destroy();
    }

    const config = JSON.parse(configElement.textContent);
    const chart = new Chart(canvas, config);

    charts.set(canvas, chart);
}

function renderReportCharts(root = document) {
    if (root.matches?.('[data-report-chart-root]')) {
        renderReportChart(root);
    }

    root.querySelectorAll('[data-report-chart-root]').forEach((chartRoot) => {
        renderReportChart(chartRoot);
    });
}

export function registerReportCharts() {
    window.renderReportChart = renderReportChart;
    window.renderReportCharts = renderReportCharts;
}

export function registerLivewireReportChartListeners(Livewire) {
    Livewire.hook('morphed', ({ el }) => {
        requestAnimationFrame(() => renderReportCharts(el));
    });
}
