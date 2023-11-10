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
                                        <p class="text-white text-sm mb-0 text-capitalize font-weight-bold opacity-7">Detail
                                        </p>
                                        <h5 class="text-white font-weight-bolder mb-0">
                                            Kolam 1
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-2 mt-md-0 mt-4 ">
                    <div class="card">
                        <div class="card-body text-center">
                            <h1 class="text-gradient text-primary"><span id="status1" countto="21">21</span> <span
                                    class="text-lg ms-n2">°C</span></h1>
                            <h6 class="mb-0 font-weight-bolder">Termperature</h6>

                        </div>
                    </div>
                </div>
                <div class="col-md-2  mt-md-0 mt-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h1 class="text-gradient text-primary"> <span id="status2" countto="44">44</span> <span
                                    class="text-lg ms-n1">%</span></h1>
                            <h6 class="mb-0 font-weight-bolder">PH</h6>

                        </div>
                    </div>
                </div>
                <div class="col-md-2  mt-md-0 mt-4 ">
                    <div class="card">
                        <div class="card-body text-center">
                            <h1 class="text-gradient text-primary"><span id="status1" countto="21">21</span> <span
                                    class="text-lg ms-n2">°C</span></h1>
                            <h6 class="mb-0 font-weight-bolder">TDS</h6>

                        </div>
                    </div>
                </div>
                <div class="col-md-2  mt-md-0 mt-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h1 class="text-gradient text-primary"> <span id="status2" countto="44">44</span> <span
                                    class="text-lg ms-n1">%</span></h1>
                            <h6 class="mb-0 font-weight-bolder">EC</h6>

                        </div>
                    </div>
                </div>
                <div class="col-md-2  mt-md-0 mt-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h1 class="text-gradient text-primary"> <span id="status2" countto="44">44</span> <span
                                    class="text-lg ms-n1">%</span></h1>
                            <h6 class="mb-0 font-weight-bolder">Salinity</h6>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card z-index-2">
                <div class="card-header pb-0">
                    <h6>Trend dalam Hari/minggu/bulan</h6>
                    <p class="text-sm">
                        <i class="fa fa-arrow-up text-success" aria-hidden="true"></i>
                        <span class="font-weight-bold">4% more</span> in 2021
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-line" class="chart-canvas" height="450" width="1389"
                            style="display: block; box-sizing: border-box; height: 300px; width: 926px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                    <div class="dataTable-wrapper dataTable-loading no-footer sortable fixed-height fixed-columns">
                        <div class="dataTable-top">
                            <div class="dataTable-dropdown"><label>Show <select class="dataTable-selector">
                                        <option value="5">5</option>
                                        <option value="10" selected="">10</option>
                                        <option value="15">15</option>
                                        <option value="20">20</option>
                                        <option value="25">25</option>
                                    </select> entries</label></div>
                        </div>
                        <div class="dataTable-container" style="height: 497.292px;">
                            <table class="table table-flush dataTable-table" id="datatable-basic">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            data-sortable="" style="width: 19.8654%;"><a href="#"
                                                class="dataTable-sorter">Name</a></th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            data-sortable="" style="width: 27.5142%;"><a href="#"
                                                class="dataTable-sorter">Position</a></th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            data-sortable="" style="width: 16.5722%;"><a href="#"
                                                class="dataTable-sorter">Office</a></th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            data-sortable="" style="width: 8.17989%;"><a href="#"
                                                class="dataTable-sorter">Age</a></th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            data-sortable="" style="width: 14.8725%;"><a href="#"
                                                class="dataTable-sorter">Start date</a></th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                            data-sortable="" style="width: 12.9603%;"><a href="#"
                                                class="dataTable-sorter">Salary</a></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-sm font-weight-normal">Tiger Nixon</td>
                                        <td class="text-sm font-weight-normal">System Architect</td>
                                        <td class="text-sm font-weight-normal">Edinburgh</td>
                                        <td class="text-sm font-weight-normal">61</td>
                                        <td class="text-sm font-weight-normal">2011/04/25</td>
                                        <td class="text-sm font-weight-normal">$320,800</td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-weight-normal">Garrett Winters</td>
                                        <td class="text-sm font-weight-normal">Accountant</td>
                                        <td class="text-sm font-weight-normal">Tokyo</td>
                                        <td class="text-sm font-weight-normal">63</td>
                                        <td class="text-sm font-weight-normal">2011/07/25</td>
                                        <td class="text-sm font-weight-normal">$170,750</td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-weight-normal">Ashton Cox</td>
                                        <td class="text-sm font-weight-normal">Junior Technical Author</td>
                                        <td class="text-sm font-weight-normal">San Francisco</td>
                                        <td class="text-sm font-weight-normal">66</td>
                                        <td class="text-sm font-weight-normal">2009/01/12</td>
                                        <td class="text-sm font-weight-normal">$86,000</td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-weight-normal">Cedric Kelly</td>
                                        <td class="text-sm font-weight-normal">Senior Javascript Developer</td>
                                        <td class="text-sm font-weight-normal">Edinburgh</td>
                                        <td class="text-sm font-weight-normal">22</td>
                                        <td class="text-sm font-weight-normal">2012/03/29</td>
                                        <td class="text-sm font-weight-normal">$433,060</td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-weight-normal">Airi Satou</td>
                                        <td class="text-sm font-weight-normal">Accountant</td>
                                        <td class="text-sm font-weight-normal">Tokyo</td>
                                        <td class="text-sm font-weight-normal">33</td>
                                        <td class="text-sm font-weight-normal">2008/11/28</td>
                                        <td class="text-sm font-weight-normal">$162,700</td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-weight-normal">Brielle Williamson</td>
                                        <td class="text-sm font-weight-normal">Integration Specialist</td>
                                        <td class="text-sm font-weight-normal">New York</td>
                                        <td class="text-sm font-weight-normal">61</td>
                                        <td class="text-sm font-weight-normal">2012/12/02</td>
                                        <td class="text-sm font-weight-normal">$372,000</td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-weight-normal">Herrod Chandler</td>
                                        <td class="text-sm font-weight-normal">Sales Assistant</td>
                                        <td class="text-sm font-weight-normal">San Francisco</td>
                                        <td class="text-sm font-weight-normal">59</td>
                                        <td class="text-sm font-weight-normal">2012/08/06</td>
                                        <td class="text-sm font-weight-normal">$137,500</td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-weight-normal">Rhona Davidson</td>
                                        <td class="text-sm font-weight-normal">Integration Specialist</td>
                                        <td class="text-sm font-weight-normal">Tokyo</td>
                                        <td class="text-sm font-weight-normal">55</td>
                                        <td class="text-sm font-weight-normal">2010/10/14</td>
                                        <td class="text-sm font-weight-normal">$327,900</td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-weight-normal">Colleen Hurst</td>
                                        <td class="text-sm font-weight-normal">Javascript Developer</td>
                                        <td class="text-sm font-weight-normal">San Francisco</td>
                                        <td class="text-sm font-weight-normal">39</td>
                                        <td class="text-sm font-weight-normal">2009/09/15</td>
                                        <td class="text-sm font-weight-normal">$205,500</td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-weight-normal">Sonya Frost</td>
                                        <td class="text-sm font-weight-normal">Software Engineer</td>
                                        <td class="text-sm font-weight-normal">Edinburgh</td>
                                        <td class="text-sm font-weight-normal">23</td>
                                        <td class="text-sm font-weight-normal">2008/12/13</td>
                                        <td class="text-sm font-weight-normal">$103,600</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="dataTable-bottom">
                            <div class="dataTable-info">Showing 1 to 10 of 57 entries</div>
                            <nav class="dataTable-pagination">
                                <ul class="dataTable-pagination-list">
                                    <li class="pager"><a href="#" data-page="1">‹</a></li>
                                    <li class="active"><a href="#" data-page="1">1</a></li>
                                    <li class=""><a href="#" data-page="2">2</a></li>
                                    <li class=""><a href="#" data-page="3">3</a></li>
                                    <li class=""><a href="#" data-page="4">4</a></li>
                                    <li class=""><a href="#" data-page="5">5</a></li>
                                    <li class=""><a href="#" data-page="6">6</a></li>
                                    <li class="pager"><a href="#" data-page="2">›</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">

    </div>
@endsection

@push('js')
    <script src="{{ URL::asset('assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/plugins/countup.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/plugins/chartjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/plugins/round-slider.min.js') }}"></script>
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
        if (document.getElementById('status6')) {
            const countUp = new CountUp('status6', document.getElementById("status6").getAttribute("countTo"));
            if (!countUp.error) {
                countUp.start();
            } else {
                console.error(countUp.error);
            }
        }

        //chart line
        var ctx2 = document.getElementById("chart-line").getContext("2d");

        var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

        gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
        gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
        gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

        var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);

        gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.2)');
        gradientStroke2.addColorStop(0.2, 'rgba(72,72,176,0.0)');
        gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)'); //purple colors

        new Chart(ctx2, {
            type: "line",
            data: {
                labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                        label: "Mobile apps",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#cb0c9f",
                        borderWidth: 3,
                        backgroundColor: gradientStroke1,
                        fill: true,
                        data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
                        maxBarThickness: 6

                    },
                    {
                        label: "Websites",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#3A416F",
                        borderWidth: 3,
                        backgroundColor: gradientStroke2,
                        fill: true,
                        data: [30, 90, 40, 140, 290, 290, 340, 230, 400],
                        maxBarThickness: 6
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



        // Chart Doughnut Consumption by room
        var ctx1 = document.getElementById("chart-consumption").getContext("2d");

        var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

        gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
        gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
        gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

        new Chart(ctx1, {
            type: "doughnut",
            data: {
                labels: ['Living Room', 'Kitchen', 'Attic', 'Garage', 'Basement'],
                datasets: [{
                    label: "Consumption",
                    weight: 9,
                    cutout: 90,
                    tension: 0.9,
                    pointRadius: 2,
                    borderWidth: 2,
                    backgroundColor: ['#FF0080', '#A8B8D8', '#21d4fd', '#98ec2d', '#ff667c'],
                    data: [15, 20, 13, 32, 20],
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
                }, ],
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
