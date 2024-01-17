@extends('user_type.auth', ['parentFolder' => 'dashboards', 'childFolder' => 'none'])

@section('content')
    <div class="row">
        <div class="col-xl-12  mt-xl-0 mt-4">
            <div class="row">
                <div class="col-12">
                    <div class="card bg-gradient-primary">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8 my-auto">
                                    <div class="numbers">
                                        <p class="text-white text-sm mb-0 text-capitalize font-weight-bold opacity-7">
                                            Detail
                                        </p>
                                        <h5 class="text-white font-weight-bolder mb-0">
                                            {{ \App\Models\AppSettings::translateDeviceName($deviceName) }}
                                        </h5>

                                        <div class="col-8 md-3 ml-auto">
                                            <h5 class="text-white font-weight-bolder mb-0">
                                                <span>{{ $formatted_state['timestamp'] }}</span>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">

                @php
                    unset($formatted_state['timestamp']);
                @endphp
                @foreach($formatted_state as $key => $state)

                <div class="col-md-2 mt-md-0 mt-4 ">
                    <div class="card">
                        <div class="card-body text-center">
                            <h1 class="text-gradient text-primary">
                                <span id="{{$key}}_state">
                                    {{ $state['value'] }}
                                </span>

                                <span class="text-lg ms-n2">{{$state['unit']}}</span>
                            </h1>
                            <h6 class="mb-0 font-weight-bolder">{{$state['label']}}</h6>

                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @foreach($stats as $key => $stat)
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card z-index-2">
                <div class="card-header pb-0">
                    <h6>{{\App\Models\AppSettings::translateSensorKey($key)}}</h6>
                    <p class="text">
                        {{ $stat['timestamp'][0]}}
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart-week">
                        <canvas id="{{$key}}" class="chart-canvas" height="450" width="1389"
                            style="display: block; box-sizing: border-box; height: 300px; width: 926px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <hr class="horizontal dark my-5">
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <!-- Card header -->
                <div class="card-header">
                    <h5 class="mb-0">Detail data yang diterima</h5>
                    <p class="text-sm mb-0">
                        Data yang diterima secara periodik
                    </p>
                </div>
                <div class="table-responsive">
                    <div class="m-3">
                        <a href="{{route('detailed-dashboard.export')}}" class="btn bg-gradient-primary btn-sm mb-0"
                           type="button">Export Excel</a>
                        <a href="{{route('detailed-dashboard.export', ['isPdf=1'])}}"
                           class="btn bg-gradient-primary btn-sm mb-0" type="button">Export PDF</a>
                    </div>
                    @if ($errors->get('msgError'))
                        <div class="m-3  alert alert-warning alert-dismissible fade show" role="alert">
                            <span class="alert-text text-white">
                                {{ $errors->first() }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </button>
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="m-3  alert alert-success alert-dismissible fade show" id="alert-success"
                            role="alert">
                            <span class="alert-text text-white">
                                {{ session('success') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </button>
                        </div>
                    @endif
                    <table class="table table-flush" id="items-list">
                        <thead class="thead-light">
                            <tr>

                                @foreach($formatted_state as $key => $value)

                                    <th class="text-sm">{{ \App\Models\AppSettings::translateSensorKey($key) }}</th>
                                @endforeach
                                <th class="text-sm">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if (count($formatted_states) > 0)
                            @foreach ($formatted_states as $state)

                                    <tr>
                                        @foreach($state as $key => $sta)

                                            @if($key == 'timestamp')
                                                <td class="text-sm">{{ $sta }}</td>
                                            @else
                                                <td class="text-sm">{{ $sta['value'] }}</td>
                                            @endif
                                        @endforeach
                                    </tr>

                                @endforeach
                            @else
                                <tr> no content </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
    </div>

@endsection

@push('js')
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
    <script src="{{ URL::asset('assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/plugins/countup.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/plugins/chartjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/plugins/round-slider.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/plugins/datatables.js') }}"></script>
    <script>
        if (document.getElementById('items-list')) {
            const dataTableSearch = new simpleDatatables.DataTable("#items-list", {
                searchable: true,
                fixedHeight: false,
                perPage: 7
            });

            document.querySelectorAll(".export").forEach(function(el) {
                el.addEventListener("click", function(e) {
                    var type = el.dataset.type;

                    var data = {
                        type: type,
                        filename: "soft-ui-" + type,
                    };

                    if (type === "csv") {
                        data.columnDelimiter = "|";
                    }

                    dataTableSearch.export(data);
                });
            });
        };
    </script>
    <script>
        $(document).ready(function() {
            $("#alert-success").delay(3000).slideUp(300);

        });
    </script>
    <script>
        // Rounded slider
        const setValue = function(value, active) {
            document.querySelectorAll("round-slider").forEach(function(el) {
                if (el.value === undefined) return;
                el.value = value;
            });
            const span = document.querySelector("#value");
            span.innerHTML = value;
            if (active)
                span.style.color = 'red';
            else
                span.style.color = 'black';
        }

        document.querySelectorAll("round-slider").forEach(function(el) {
            el.addEventListener('value-changed', function(ev) {
                if (ev.detail.value !== undefined)
                    setValue(ev.detail.value, false);
                else if (ev.detail.low !== undefined)
                    setLow(ev.detail.low, false);
                else if (ev.detail.high !== undefined)
                    setHigh(ev.detail.high, false);
            });

            el.addEventListener('value-changing', function(ev) {
                if (ev.detail.value !== undefined)
                    setValue(ev.detail.value, true);
                else if (ev.detail.low !== undefined)
                    setLow(ev.detail.low, true);
                else if (ev.detail.high !== undefined)
                    setHigh(ev.detail.high, true);
            });
        });

        // Count To
        if (document.getElementById('status1')) {
            const countUp = new CountUp('status1', document.getElementById("status1").getAttribute("countTo"));
            if (!countUp.error) {
                countUp.start();
            } else {
                console.error(countUp.error);
            }
        }
        if (document.getElementById('status2')) {
            const countUp = new CountUp('status2', document.getElementById("status2").getAttribute("countTo"));
            if (!countUp.error) {
                countUp.start();
            } else {
                console.error(countUp.error);
            }
        }
        if (document.getElementById('status3')) {
            const countUp = new CountUp('status3', document.getElementById("status3").getAttribute("countTo"));
            if (!countUp.error) {
                countUp.start();
            } else {
                console.error(countUp.error);
            }
        }
        if (document.getElementById('status4')) {
            const countUp = new CountUp('status4', document.getElementById("status4").getAttribute("countTo"));
            if (!countUp.error) {
                countUp.start();
            } else {
                console.error(countUp.error);
            }
        }
        if (document.getElementById('status5')) {
            const countUp = new CountUp('status5', document.getElementById("status5").getAttribute("countTo"));
            if (!countUp.error) {
                countUp.start();
            } else {
                console.error(countUp.error);
            }
        }


        //chart line per 8 jam sehari

        @foreach($stats as $key => $stat)
        var ctx2 = document.getElementById("{{$key}}").getContext("2d");

        var gradientStroke1 = ctx2.createLinearGradient(72, 221, 71, 50);
        gradientStroke1.addColorStop(1, 'rgba(72, 221, 71, 100)');
        gradientStroke1.addColorStop(0.2, 'rgba(72,221,71,0.0)');
        gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors


        var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);
        gradientStroke2.addColorStop(1, 'rgba(215,70,192,100)');
        gradientStroke2.addColorStop(0.2, 'rgba(215,70,192,0.0)');
        gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)'); //purple colors

        var gradientStroke3 = ctx2.createLinearGradient(0, 230, 0, 50);
        gradientStroke2.addColorStop(1, 'rgba(101,140,216,100)');
        gradientStroke2.addColorStop(0.2, 'rgba(101,140,216,0.0)');
        gradientStroke2.addColorStop(0, 'rgba(101,140,216,0)'); //purple colors

        var gradientStroke4 = ctx2.createLinearGradient(0, 230, 0, 50);
        gradientStroke2.addColorStop(1, 'rgba(248,219,71,100)');
        gradientStroke2.addColorStop(0.2, 'rgba(248,219,71, 0.0)');
        gradientStroke2.addColorStop(0, 'rgba(248,219,71,0)');

        var gradientStroke5 = ctx2.createLinearGradient(0, 230, 0, 50);
        gradientStroke2.addColorStop(1, 'rgba(255,69,69,100)');
        gradientStroke2.addColorStop(0.2, 'rgba(255,69,69,0.0)');
        gradientStroke2.addColorStop(0, 'rgba(255,69,69,0)'); //purple colors

        new Chart(ctx2, {
            type: "line",
            data: {
                // labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                labels: @json($stat['timestamp']),
                datasets: [{
                    label: "{{$stat['format']['label']}}",
                        tension: 0.4,
                        borderColor: "#48DD47",
                        borderWidth: 3,
                        backgroundColor: gradientStroke1,

                    data: @json($stat['data']),
                        maxBarThickness: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            precision: 2,
                            display: true,
                            padding: 10,
                            color: '#b2b9bf',
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            color: '#b2b9bf',
                            padding: 20,
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        })
        @endforeach

        //trend perminggu
        var ctxWeekly = document.getElementById("chart-line-week").getContext("2d");

        var gradientStroke1 = ctxWeekly.createLinearGradient(72, 221, 71, 50);
        gradientStroke1.addColorStop(1, 'rgba(72, 221, 71, 100)');
        gradientStroke1.addColorStop(0.2, 'rgba(72,221,71,0.0)');
        gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors


        var gradientStroke2 = ctxWeekly.createLinearGradient(0, 230, 0, 50);
        gradientStroke2.addColorStop(1, 'rgba(215,70,192,100)');
        gradientStroke2.addColorStop(0.2, 'rgba(215,70,192,0.0)');
        gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)'); //purple colors

        var gradientStroke3 = ctxWeekly.createLinearGradient(0, 230, 0, 50);
        gradientStroke2.addColorStop(1, 'rgba(101,140,216,100)');
        gradientStroke2.addColorStop(0.2, 'rgba(101,140,216,0.0)');
        gradientStroke2.addColorStop(0, 'rgba(101,140,216,0)'); //purple colors

        var gradientStroke4 = ctxWeekly.createLinearGradient(0, 230, 0, 50);
        gradientStroke2.addColorStop(1, 'rgba(248,219,71,100)');
        gradientStroke2.addColorStop(0.2, 'rgba(248,219,71, 0.0)');
        gradientStroke2.addColorStop(0, 'rgba(248,219,71,0)');

        var gradientStroke5 = ctxWeekly.createLinearGradient(0, 230, 0, 50);
        gradientStroke2.addColorStop(1, 'rgba(255,69,69,100)');
        gradientStroke2.addColorStop(0.2, 'rgba(255,69,69,0.0)');
        gradientStroke2.addColorStop(0, 'rgba(255,69,69,0)'); //purple colors

        new Chart(ctxWeekly, {
            type: "line",
            data: {
                // labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                labels: @json($chartDataWeekly['labels']),
                datasets: [{
                        label: "Temperatur",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#48DD47",
                        borderWidth: 3,
                        backgroundColor: gradientStroke1,
                        fill: true,
                        data: @json($chartDataWeekly['temp']),
                        maxBarThickness: 6,
                    },
                    {
                        label: "pH",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#D746C0",
                        borderWidth: 3,
                        backgroundColor: gradientStroke2,
                        fill: true,
                        data: @json($chartDataWeekly['ph']),
                        maxBarThickness: 6,
                    },
                    {
                        label: "TDS",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#658CD8",
                        borderWidth: 3,
                        backgroundColor: gradientStroke3,
                        fill: true,
                        data: @json($chartDataWeekly['tds']),
                        maxBarThickness: 6,
                    },
                    {
                        label: "EC",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#F8DB47",
                        borderWidth: 3,
                        backgroundColor: gradientStroke4,
                        fill: true,
                        data: @json($chartDataWeekly['ec']),
                        maxBarThickness: 6,
                    },
                    {
                        label: "Salinitas",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#FF4545",
                        borderWidth: 3,
                        backgroundColor: gradientStroke5,
                        fill: true,
                        data: @json($chartDataWeekly['salinity']),
                        maxBarThickness: 6,
                    },
                ],

            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            precision: 2,
                            display: true,
                            padding: 10,
                            color: '#b2b9bf',
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            color: '#b2b9bf',
                            padding: 20,
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        })
    </script>
@endpush
