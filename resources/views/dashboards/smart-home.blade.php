
@extends('user_type.auth', ['parentFolder' => 'dashboards', 'childFolder' => 'none'])

@section('content')
    <div class="row mt-4">


        @foreach($devices as $device)
            <div class="" id="{{$device['name']}}">
                <div class="card m-2">
                    <div class="card-header pb-0 p-3">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0">{{$device['display_name']}}</h6>
                            <div class="ms-auto"> <!-- Menempatkan elemen-elemen di sebelah kanan -->
                                @foreach(\App\Models\AppSettings::$batterySensors as $sensor)
                                    @if(isset($device['state'][$sensor]))
                                        @php
                                            // Ambil nilai persentase baterai dan tentukan warna berdasarkan kondisi
                                            $valueBattery = intval($device['state'][$sensor]['value']);
                                            $color = '#30C873'; // Warna default: hijau
                                            if($valueBattery < 20){
                                                $color = '#FF0000'; // Warna: merah jika baterai kurang dari 20%
                                            } else if($valueBattery < 50){
                                                $color = '#DAA520'; // Warna: kuning jika baterai kurang dari 50%
                                            }
                                        @endphp
                                        <span class="text-sm mb-0 ms-2" style="color: {{$color}};">
                                            <i class="fas fa-battery-{{ $valueBattery < 20 ? 'empty' : ($valueBattery < 50 ? 'quarter' : 'full') }}"></i> {{$device['state'][$sensor]['value']}}%
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-icon-only btn-rounded btn-outline-secondary mb-0 ms-2 btn-sm d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{date('d M Y H:00:00', isset($device["ðŸ˜Ž"]['timestamp']) ? $device["ðŸ˜Ž"]['timestamp'] : 0)}}">
                                <i class="fas fa-info"></i>
                            </button>
                        </div>

                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-5 text-center">
                                {{-- @foreach(\App\Models\AppSettings::$batterySensors as $sensor)
                                    @if(isset($device['state'][$sensor]))
                                        @php
                                            // Green
                                            $valueBattery = $device['state'][$sensor]['value'];
                                            $valueBattery = intval($valueBattery);
                                            $color = '#30C873';
                                            if($valueBattery < 20){
                                                $color = '#FF0000';
                                            }else if($valueBattery < 50){
                                                $color = '#DAA520';
                                            }

                                        @endphp
                                        <span class="text-sm my-4" style="color: {{$color}};">
                                            <i class="fas fa-battery-{{ $valueBattery < 20 ? 'empty' : ($valueBattery < 50 ? 'quarter' : 'full') }}"></i> {{$device['state'][$sensor]['value']}}%
                                        </span>
                                    @endif
                                @endforeach --}}
                                <div class="chart">
                                    <canvas id="chart-consumption" cslass="chart-canvas" height="197"></canvas>
                                </div>

                                <h4 class="font-weight-bold mt-n10">

                                    {{-- @dd($device['scores']['ph']); --}}
                                    @if($device['final_score'] > $finalScoreDisplay['green'])
                                    <img src="{{ asset('images/green.png') }}" alt="baik" style="width: 70px; height: 70px; border-radius: 50%;">
                                    <h6 class="d-block text-sm">
                                        <span class="highlight-background" style="background-color: #d2fcd2; display: inline-block; padding: 5px; border-radius: 5px;">
                                            <span class="text-sm bold" style="color: #30C873;">Good {{ intval($device['final_score'] * 100) }}%</span>
                                        </span>
                                    </h6>

                                    @elseif($device['final_score'] > $finalScoreDisplay['yellow'])
                                    <img src="{{ asset('images/yellow.png') }}" alt="waspada" style="width: 70px; height: 70px; border-radius: 50%;">
                                    <h6 class="d-block text-sm">
                                        <span class="highlight-background" style="background-color: #FFFF00; display: inline-block; padding: 5px; border-radius: 5px;">
                                            <span class="text-sm" style="color: #DAA520;">Caution {{ intval($device['final_score'] * 100) }}%</span>
                                        </span>
                                    </h6>
                                @else
                                    <img src="{{ asset('images/red.png') }}" alt="buruk" style="width: 70px; height: 70px; border-radius: 50%;">
                                    <h6 class="d-block text-sm">
                                        <span class="highlight-background" style="background-color: #ffa1a1; display: inline-block; padding: 5px; border-radius: 5px;">
                                            <span class="text-sm" style="color: #FF0000;">Bad {{ intval($device['final_score'] * 100) }}%</span>
                                        </span>
                                    </h6>
                                @endif


                                    {{-- {{ intval($device['final_score'] * 100) }}% --}}

                                </h4>
                            </div>
                            <div class="col-7">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <tbody>
                                        @php
                                            $dataPerColumn = array_fill(0, 3, []); // Inisialisasi array untuk setiap kolom
                                            $columnCounter = 0;
                                        @endphp

                                        @foreach($device['state'] as $sensor => $state)
                                            @php
                                                // Periksa apakah sensor saat ini adalah bagian dari sensor baterai
                                                if (in_array($sensor, \App\Models\AppSettings::$batterySensors)) {
                                                    continue; // Lewati sensor baterai
                                                }

                                                // Menambahkan data ke dalam array sementara untuk setiap kolom
                                                $dataPerColumn[$columnCounter][] = [
                                                    'sensor' => $sensor,
                                                    'state' => $state,
                                                ];

                                                // Pindah ke kolom berikutnya setelah mencapai 3 baris
                                                if (count($dataPerColumn[$columnCounter]) == 3) {
                                                    $columnCounter++;
                                                }
                                            @endphp
                                        @endforeach

                                        @for ($row = 0; $row < 3; $row++)
                                            <tr>
                                                @foreach($dataPerColumn as $column)
                                                    @if(isset($column[$row]))
                                                        <td>
                                                            <div class="d-flex px-2 py-0">
                                                                @php
                                                                    $sensor = $column[$row]['sensor'];
                                                                    $state = $column[$row]['state'];
                                                                @endphp
                                                                @if($device['scores'][$sensor] > $parameterThresholdDisplay['green'])
                                                                    <span class="badge bg-success me-3"> </span>
                                                                @elseif($device['scores'][$sensor] > $parameterThresholdDisplay['yellow'])
                                                                    <span class="badge bg-warning me-3"> </span>
                                                                @else
                                                                    <span class="badge bg-danger me-3"> </span>
                                                                @endif
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <h6 class="mb-0 text-sm">{{ $state['label'] }}</h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    @else
                                                        <td></td> <!-- Jika tidak ada data, tambahkan sel kosong -->
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        @endforeach


    </div>

@endsection

@push('js')

<script>
    // Fungsi untuk memuat ulang halaman setiap menit (60 * 1000 milidetik)
    function autoReload() {
        setTimeout(function() {
            location.reload();
        }, 30 * 60 * 1000); // 1 menit
    }
    
    // Panggil fungsi autoReload saat halaman dimuat
    window.onload = autoReload;
    </script>
    <script src="{{ URL::asset('assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/plugins/countup.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/plugins/chartjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/plugins/round-slider.min.js') }}"></script>
    <script>
        // Rounded slider
        const setValue = function (value, active) {
            document.querySelectorAll("round-slider").forEach(function (el) {
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

        document.querySelectorAll("round-slider").forEach(function (el) {
            el.addEventListener('value-changed', function (ev) {
                if (ev.detail.value !== undefined)
                    setValue(ev.detail.value, false);
                else if (ev.detail.low !== undefined)
                    setLow(ev.detail.low, false);
                else if (ev.detail.high !== undefined)
                    setHigh(ev.detail.high, false);
            });

            el.addEventListener('value-changing', function (ev) {
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
        if (document.getElementById('status6')) {
            const countUp = new CountUp('status6', document.getElementById("status6").getAttribute("countTo"));
            if (!countUp.error) {
                countUp.start();
            } else {
                console.error(countUp.error);
            }
        }

        // Chart Doughnut Consumption by room
        var ctx1 = document.getElementById("chart-consumption").getContext("2d");

        varStroke1 = ctx1.createLinea(0, 230, 0, 50);

        Stroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
        Stroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
        Stroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

        new Chart(ctx1, {
            type: "doughnut",
            data: {
                // labels: ['Living Room', 'Kitchen', 'Attic', 'Garage', 'Basement'],
                datasets: [{
                    label: "Consumption",
                    weight: 9,
                    cutout: 90,
                    tension: 0.9,
                    pointRadius: 2,
                    borderWidth: 2,
                    backgroundColor: ['#98ec2d'],
                    data: [100],
                    // backgroundColor: ['#FF0080', '#A8B8D8', '#21d4fd', '#98ec2d', '#ff667c'],
                    // data: [15, 20, 13, 32, 20],
                    fill: false
                }],
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
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                        },
                        ticks: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                        },
                        ticks: {
                            display: false,
                        }
                    },
                },
            },
        });

        // Chart Consumption by day
        var ctx = document.getElementById("chart-cons-week").getContext("2d");

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
                datasets: [{
                    label: "Watts",
                    tension: 0.4,
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                    backgroundColor: "#3A416F",
                    data: [150, 230, 380, 220, 420, 200, 70],
                    maxBarThickness: 6
                },],
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
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                        },
                        ticks: {
                            display: false
                        },
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false
                        },
                        ticks: {
                            beginAtZero: true,
                            font: {
                                size: 12,
                                family: "Open Sans",
                                style: 'normal',
                            },
                            color: "#9ca2b7"
                        },
                    },
                    y: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#9ca2b7'
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#9ca2b7'
                        }
                    },
                },
            },
        });
    </script>
@endpush
