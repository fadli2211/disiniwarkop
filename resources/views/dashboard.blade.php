@extends('template.layout')

@section('content')
<div class="container">
    <h2 class="mb-4">Dashboard</h2>
    <div class="mb-4 row">
        <h4 class="mb-2">Laporan Harian</h4>
        <!-- Total Pendapatan Hari Ini -->
        <div class="col-md-6 col-xl-3">
            <div class="card" style="min-height: 160px;">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Total Pendapatan Hari Ini</h6>
                    <h4 class="mb-3" id="totalIncome">
                        0
                        <span class="badge" id="totalIncomeTrend"></span>
                    </h4>
                    <p class="mb-0 text-muted text-sm" id="totalIncomeCaption">
                        Pendapatan hari ini: 
                        <span class="text-primary" id="totalIncomeDiff">0</span> dibanding hari sebelumnya.
                    </p>                                   
                </div>
            </div>
        </div>
    
        <!-- Total Pesanan Hari Ini -->
        <div class="col-md-6 col-xl-3">
            <div class="card" style="min-height: 160px;">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Total Pesanan Hari Ini</h6>
                    <h4 class="mb-3" id="totalOrders">
                        0
                        <span class="badge" id="totalOrdersTrend"></span>
                    </h4>
                    <p class="mb-0 text-muted text-sm" id="totalOrdersCaption">
                        Pesanan hari ini: 
                        <span class="text-danger" id="totalOrdersDiff">0</span> dibanding hari kemarin.
                    </p>
                </div>
            </div>
        </div>
    
        <!-- Pesanan Yang Sedang Berjalan Hari Ini -->
        <div class="col-md-6 col-xl-3">
            <div class="card" style="min-height: 160px;">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Sedang berjalan</h6>
                    <h4 class="mb-3" id="ongoingOrders">0</h4>
                    <p class="mb-0 text-muted text-sm">
                        Pesanan yang sedang diproses atau menunggu pembayaran dihari ini.
                    </p>
                </div>
            </div>
        </div>
    
        <!-- Pesanan Yang Selesai Hari Ini -->
        <div class="col-md-6 col-xl-3">
            <div class="card" style="min-height: 160px;">
                <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Selesai</h6>
                    <h4 class="mb-3" id="completedOrders">0</h4>
                    <p class="mb-0 text-muted text-sm">
                        Pesanan yang sudah selesai dan sudah dibayar dihari ini.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div>
        <h4 class="mb-2">Laporan Pendapatan Tahunan</h4>
        <form method="GET" class="row mb-4">
            <div class="col-md-4">
                <select name="year" id="year" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
        </form>

        <div class="card" style="min-height: 400px;">
            <div class="card-body">
                <h6 class="mb-3 f-w-400 text-muted">Grafik Pendapatan Tahunan</h6>
                <div style="width:100%; height:300px;">
                    <canvas id="barChartPendapatan"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.min.js"></script>
<script>
    let chart;

    function loadChart(year) {
        fetch(`/admin/dashboard/chart-data?year=${year}`)
            .then(res => res.json())
            .then(res => {
                const ctx = document.getElementById('barChartPendapatan').getContext('2d');
                if (chart) chart.destroy();

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: res.labels,
                        datasets: [
                            {
                                label: `Total Pendapatan Tahun ${res.year}`,
                                data: res.data,
                                backgroundColor: res.colors,
                                borderWidth: 1,
                                barThickness: 40,
                                maxBarThickness: 60
                            },
                            {
                                label: "Turun",
                                type: "line",
                                borderColor: "rgba(255, 99, 132, 0.6)",
                                backgroundColor: "rgba(255, 99, 132, 0.6)",
                                data: res.data.map((v, i) => res.colors[i] === "rgba(255, 99, 132, 0.6)" ? v : null),
                                pointRadius: 0,
                                borderWidth: 0,
                                showLine: false
                            },
                            {
                                label: "Naik",
                                type: "line",
                                borderColor: "rgba(54, 162, 235, 0.6)",
                                backgroundColor: "rgba(54, 162, 235, 0.6)",
                                data: res.data.map((v, i) => res.colors[i] === "rgba(54, 162, 235, 0.6)" ? v : null),
                                pointRadius: 0,
                                borderWidth: 0,
                                showLine: false
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            x: {
                                barPercentage: 0.6,
                                categoryPercentage: 0.8
                            },
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: { onClick: () => {} }
                        }
                    }
                });
            })
            .catch(err => console.error(err));
    }


    // Load awal
    loadChart({{ $year }});

    // Ganti tahun
    document.getElementById('year').addEventListener('change', function () {
        loadChart(this.value);
    });
    
    function loadDailyStats() {
        fetch("{{ route('admin.dashboard.daily-stats') }}")
            .then(res => res.json())
            .then(res => {
                res.cards.forEach(card => {
                    let h4 = document.getElementById(card.id);
                    let diffEl = document.getElementById(card.id + 'Diff');
                    let trendEl = document.getElementById(card.id + 'Trend');
                    let captionEl = null;

                    // Tentukan caption sesuai card
                    if(card.id === 'totalIncome') captionEl = document.getElementById('totalIncomeCaption');
                    if(card.id === 'totalOrders') captionEl = document.getElementById('totalOrdersCaption');

                    // Update value
                    h4.innerHTML = card.value;

                    if(card.trend) {
                        if(card.trend === 'up') {
                            trendEl.className = card.id === 'totalIncome'
                                ? 'badge bg-light-primary border border-primary ms-2'
                                : 'badge bg-light-primary border border-primary ms-2';
                            trendEl.innerHTML = `<i class="ti ti-trending-up"></i> + ${card.diff}`;
                            diffEl.className = 'text-primary';
                            diffEl.innerText = card.diff;

                            if(captionEl) {
                                captionEl.innerHTML = card.id === 'totalIncome'
                                    ? `Pendapatan hari ini meningkat <span class="text-primary">${card.diff}</span> dibanding hari sebelumnya`
                                    : `Pesanan hari ini meningkat <span class="text-primary">${card.diff}</span> dibanding hari kemarin`;
                            }

                        } else if(card.trend === 'down') {
                            trendEl.className = 'badge bg-light-danger border border-danger ms-2';
                            trendEl.innerHTML = `<i class="ti ti-trending-down"></i> - ${card.diff}`;
                            diffEl.className = 'text-danger';
                            diffEl.innerText = card.diff;

                            if(captionEl) {
                                captionEl.innerHTML = card.id === 'totalIncome'
                                    ? `Pendapatan hari ini menurun <span class="text-danger">${card.diff}</span> dibanding hari sebelumnya`
                                    : `Pesanan hari ini menurun <span class="text-danger">${card.diff}</span> dibanding hari kemarin`;
                            }

                        } else {
                            trendEl.className = 'badge bg-light-secondary border border-secondary ms-2';
                            trendEl.innerHTML = '0';
                            diffEl.className = 'text-muted';
                            diffEl.innerText = '0';

                            if(captionEl) {
                                captionEl.innerHTML = card.id === 'totalIncome'
                                    ? 'Pendapatan hari ini tidak berubah'
                                    : 'Pesanan hari ini tidak berubah';
                            }
                        }
                        h4.appendChild(trendEl);
                    }
                });
            })
            .catch(err => console.error(err));
    }



    // Load awal
    loadDailyStats();


</script>
@endpush
