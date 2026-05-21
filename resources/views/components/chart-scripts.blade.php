<script data-navigate-once>
    window.FinanceCharts = window.FinanceCharts || (() => {
        const money = (value) => new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value || 0);

        const theme = () => {
            const dark = document.documentElement.classList.contains('dark');

            return {
                text: dark ? '#e4e4e7' : '#3f3f46',
                muted: dark ? '#71717a' : '#d4d4d8',
                tooltip: dark ? 'dark' : 'light',
            };
        };

        const normalizeCategoryData = (data) => {
            if (!data?.values?.length) {
                return { labels: ['Sem dados'], values: [1], empty: true };
            }

            return { labels: data.labels, values: data.values, empty: false };
        };

        const destroy = (charts) => Object.values(charts).forEach((chart) => chart?.destroy?.());

        const dashboardCharts = { monthly: null, yearly: null, categories: null };
        const flowCharts = { monthly: null, categories: null };

        let dashboardData = null;
        let flowData = null;
        let listenersStarted = false;

        const waitForApex = (callback) => {
            if (window.ApexCharts) {
                callback();
                return;
            }

            setTimeout(() => waitForApex(callback), 50);
        };

        const renderDashboard = (data) => waitForApex(() => {
            if (!data) return;

            const yearlyEl = document.querySelector('[data-dashboard-chart="yearly"]');
            const monthlyEl = document.querySelector('[data-dashboard-chart="monthly"]');
            const categoriesEl = document.querySelector('[data-dashboard-chart="categories"]');

            if (!yearlyEl && !monthlyEl && !categoriesEl) return;

            dashboardData = data;
            destroy(dashboardCharts);

            const colors = theme();
            const categoryData = normalizeCategoryData(data.categories);

            if (yearlyEl) {
                dashboardCharts.yearly = new ApexCharts(yearlyEl, {
                    chart: { type: 'area', height: 306, toolbar: { show: false }, foreColor: colors.text, parentHeightOffset: 0, redrawOnParentResize: false },
                    series: [
                        { name: 'Receitas', data: data.yearly?.income || [] },
                        { name: 'Despesas', data: data.yearly?.expenses || [] },
                    ],
                    colors: ['#10b981', '#f43f5e'],
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    fill: { type: 'gradient', gradient: { opacityFrom: 0.28, opacityTo: 0.04 } },
                    xaxis: { categories: data.yearly?.labels || [] },
                    yaxis: { labels: { formatter: money } },
                    grid: { borderColor: colors.muted, strokeDashArray: 4 },
                    tooltip: { theme: colors.tooltip, y: { formatter: money } },
                    legend: { position: 'top', horizontalAlign: 'right' },
                });
                dashboardCharts.yearly.render();
            }

            if (monthlyEl) {
                dashboardCharts.monthly = new ApexCharts(monthlyEl, {
                    chart: { type: 'bar', height: 142, toolbar: { show: false }, foreColor: colors.text, parentHeightOffset: 0, redrawOnParentResize: false },
                    series: [{ name: 'Valor', data: [data.monthly?.income || 0, data.monthly?.expenses || 0, data.monthly?.balance || 0] }],
                    colors: ['#10b981', '#f43f5e', '#6366f1'],
                    plotOptions: { bar: { borderRadius: 8, distributed: true, columnWidth: '52%' } },
                    dataLabels: { enabled: false },
                    xaxis: { categories: ['Receitas', 'Despesas', 'Saldo'] },
                    yaxis: { labels: { formatter: money } },
                    grid: { borderColor: colors.muted, strokeDashArray: 4 },
                    tooltip: { theme: colors.tooltip, y: { formatter: money } },
                    legend: { show: false },
                });
                dashboardCharts.monthly.render();
            }

            if (categoriesEl) {
                dashboardCharts.categories = new ApexCharts(categoriesEl, {
                    chart: { type: 'donut', height: 168, foreColor: colors.text, parentHeightOffset: 0, redrawOnParentResize: false },
                    series: categoryData.values,
                    labels: categoryData.labels,
                    colors: categoryData.empty ? ['#a1a1aa'] : ['#6366f1', '#14b8a6', '#f59e0b', '#f43f5e', '#8b5cf6', '#06b6d4'],
                    dataLabels: { enabled: !categoryData.empty },
                    tooltip: { theme: colors.tooltip, y: { formatter: categoryData.empty ? () => 'Sem dados' : money } },
                    legend: { show: !categoryData.empty, position: 'bottom' },
                    stroke: { width: 0 },
                });
                dashboardCharts.categories.render();
            }
        });

        const renderFlow = (data) => waitForApex(() => {
            if (!data) return;

            const monthlyEl = document.querySelector('[data-flow-chart="monthly"]');
            const categoriesEl = document.querySelector('[data-flow-chart="categories"]');

            if (!monthlyEl && !categoriesEl) return;

            flowData = data;
            destroy(flowCharts);

            const colors = theme();
            const categoryData = normalizeCategoryData(data.categories);
            const accent = data.type === 'income' ? '#10b981' : '#f43f5e';

            if (monthlyEl) {
                flowCharts.monthly = new ApexCharts(monthlyEl, {
                    chart: { type: 'area', height: 275, toolbar: { show: false }, foreColor: colors.text, parentHeightOffset: 0, redrawOnParentResize: false },
                    series: [{ name: data.type === 'income' ? 'Receitas' : 'Despesas', data: data.monthly?.values || [] }],
                    colors: [accent],
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    fill: { type: 'gradient', gradient: { opacityFrom: 0.28, opacityTo: 0.04 } },
                    xaxis: { categories: data.monthly?.labels || [] },
                    yaxis: { labels: { formatter: money } },
                    grid: { borderColor: colors.muted, strokeDashArray: 4 },
                    tooltip: { theme: colors.tooltip, y: { formatter: money } },
                });
                flowCharts.monthly.render();
            }

            if (categoriesEl) {
                flowCharts.categories = new ApexCharts(categoriesEl, {
                    chart: { type: 'donut', height: 275, foreColor: colors.text, parentHeightOffset: 0, redrawOnParentResize: false },
                    series: categoryData.values,
                    labels: categoryData.labels,
                    colors: categoryData.empty ? ['#a1a1aa'] : [accent, '#6366f1', '#14b8a6', '#f59e0b', '#8b5cf6', '#06b6d4'],
                    dataLabels: { enabled: !categoryData.empty },
                    tooltip: { theme: colors.tooltip, y: { formatter: categoryData.empty ? () => 'Sem dados' : money } },
                    legend: { show: !categoryData.empty, position: 'bottom' },
                    stroke: { width: 0 },
                });
                flowCharts.categories.render();
            }
        });

        const startListeners = () => {
            if (listenersStarted) return;
            listenersStarted = true;

            window.addEventListener('dashboard:charts-updated', (event) => {
                const payload = Array.isArray(event.detail) ? event.detail[0] : event.detail;
                renderDashboard(payload || dashboardData);
            });

            window.addEventListener('flow-report:charts-updated', (event) => {
                const payload = Array.isArray(event.detail) ? event.detail[0] : event.detail;
                renderFlow(payload || flowData);
            });

            new MutationObserver(() => {
                if (document.querySelector('[data-dashboard-chart]')) renderDashboard(dashboardData);
                if (document.querySelector('[data-flow-chart]')) renderFlow(flowData);
            }).observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class'],
            });
        };

        const readPayload = (selector) => {
            const element = document.querySelector(selector);
            if (!element) return null;

            try {
                return JSON.parse(element.textContent || '{}');
            } catch {
                return null;
            }
        };

        return {
            dashboard(data) {
                startListeners();
                requestAnimationFrame(() => renderDashboard(data));
            },
            dashboardFromPage() {
                startListeners();
                requestAnimationFrame(() => renderDashboard(readPayload('[data-dashboard-chart-payload]')));
            },
            flow(data) {
                startListeners();
                requestAnimationFrame(() => renderFlow(data));
            },
            flowFromPage() {
                startListeners();
                requestAnimationFrame(() => renderFlow(readPayload('[data-flow-chart-payload]')));
            },
        };
    })();
</script>