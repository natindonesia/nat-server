<?php

namespace App\Http\Controllers;

use App\Exports\SensorDataExport;
use App\Models\AppSettings;
use App\Models\SensorData;
use App\Models\State;
use App\Models\StateMeta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SensorDataController extends Controller
{

    /**
     * Guarantee to return same length for each sensor
     * @param string $deviceName based on AppSettings::$natwaveDevices
     * @param int $limit number of data to return
     * @param int|null $startTimestamp start timestamp
     * @param int|null $endTimestamp end timestamp
     * @param int $interval interval in seconds
     * @return array<string, array<string, array<int, mixed>>>
     */
    public static function getStats(string $deviceName, int $limit = 15, $startTimestamp = null, $endTimestamp = null, $interval = 60 * 30): array
    {
        $metadata = StateMeta::getMetadata($deviceName);


        $metadataIds = $metadata['metadataIds'];


        // Get stats for each metadata
        $sensors = [
            /**
             * sensor_1 => [
             *  data => [...]
             *  timestamp => [...]
             */
        ];


        if (request()->has('date')) {
            try {
                $date = request()->get('date');

                $startTimestamp = strtotime($date);
                $endTimestamp = strtotime($date . ' +1 day');
            } catch (NotFoundExceptionInterface $e) {
            } catch (ContainerExceptionInterface $e) {
            }
        }
        if (request()->has('interval')) {
            try {
                $interval = request()->get('interval');
                $interval = intval($interval);
                $interval = $interval > 0 ? $interval : 60 * 30;

            } catch (NotFoundExceptionInterface $e) {
            } catch (ContainerExceptionInterface $e) {
            }
        }
        // Laravel mad, we do one by one



        foreach ($metadataIds as $metadataId) {
            // Get latest state
            /**
             * SELECT
             * FROM_UNIXTIME(last_updated_ts) AS formatted_timestamp,
             * state
             * FROM
             * states
             * GROUP BY
             * FLOOR(last_updated_ts / (30 * 60))
             * ORDER BY
             * formatted_timestamp;
             */
            DB::statement("SET sql_mode = ''");
            $state = State::selectRaw('metadata_id, state, FROM_UNIXTIME(last_updated_ts) AS formatted_timestamp, last_updated_ts')
                ->where('metadata_id', $metadataId);

            if ($startTimestamp !== null) {
                $state = $state->where('last_updated_ts', '>=', $startTimestamp);
                $state = $state->where('last_updated_ts', '<', $endTimestamp);
            }

            if ($interval !== null) {
                $state = $state->groupBy(DB::raw('FLOOR(last_updated_ts / ' . $interval . ')'));
            }

            $state = $state->orderBy('formatted_timestamp', 'desc')
                ->take($limit);
            $state = $state->get();
            if (empty($state)) continue;
            $data = [];
            $timestamp = [];
            foreach ($state as $item) {
                $data[] = $item->state;
                $timestamp[] = date('Y-m-d H:i:s', $item->last_updated_ts);
            }
            $stateValue = $data[0] ?? 0.0;
            $sensors[$state->first()->metadata->entity_id] = [
                'data' => $data,
                'timestamp' => $timestamp,
                'format' => WaterpoolController::formatSensor($state->first()->metadata->entity_id, $stateValue),
            ];
        }


        return $sensors;
    }

    public function index()
    {
        $deviceName = request()->get('deviceName', AppSettings::$natwaveDevices[0]);
        //yes this is duplicate query, have problem ?
        $states = WaterpoolController::getStates($deviceName, 30);
        $stats = SensorDataController::getStats($deviceName, 30);


        $data = [
            'formatted_states' => WaterpoolController::formatStates($states),
            'stats' => $stats,
            'deviceName' => $deviceName,
        ];
        $data['formatted_state'] = $data['formatted_states'][0];



        $dataUpdate = SensorData::latest()->first();
        if ($dataUpdate !== null) {

            $dataUpdate->temp_current = $this->convertToDecimal($dataUpdate->temp_current);

            // dd($dataUpdate);
        } else {

            $defaultTemp = 0; // Set a default value or adjust as needed
            \Illuminate\Support\Facades\Log::info('No data found in sensor_data table.');


            $dataUpdate = new SensorData();
            $dataUpdate->temp_current = $defaultTemp;

            // dd($dataUpdate);
        }


        // $dataUpdate->temp_current = $this->convertToDecimal($dataUpdate->temp_current);
        // dd($dataUpdate);

        $originalPH = $dataUpdate->ph_current;
        $dataUpdate->ph_current = $this->convertToPercentage($originalPH);

        $max_date = State::max('last_updated_ts');
        $min_date = State::min('last_updated_ts');
        $data['date_filter'] = [
            'max' => date('Y-m-d', $max_date),
            'min' => date('Y-m-d', $min_date),
        ];



        return view('dashboards/detailed-dashboard', $data);
    }


    public function export()
    {
        $isPdf = request()->get('isPdf', false);

        $deviceName = request()->get('deviceName', AppSettings::$natwaveDevices[0]);
        // check if device name is valid
        if (!in_array($deviceName, AppSettings::$natwaveDevices)) {
            abort(404);
        }

        if ($isPdf) {
            return Excel::download(new SensorDataExport($deviceName), "sensor_data_{$deviceName}.pdf", \Maatwebsite\Excel\Excel::TCPDF);
        }
        return Excel::download(new SensorDataExport($deviceName), "sensor_data_{$deviceName}.xlsx");
    }

    private function convertToDecimal($value)
    {
        $intValue = intval($value);
        $decimalValue = $intValue / 10.0;
        return number_format($decimalValue, 1, '.', '');
    }

    private function convertToPercentage($value)
    {
        $intValue = intval($value);
        $decimalValue = $intValue / 100.0;
        return number_format($decimalValue, 2, '.', '');
    }

    private function getChartData()
    {
        $now = Carbon::now();
        $interval = 8; // Jam

        // Hitung waktu mulai untuk interval terakhir
        $lastIntervalStart = $now->copy()->subHours($interval);

        // dd($lastIntervalStart);
        $chartData = DB::table('sensor_data')
            ->select('created_at', 'temp_current', 'ph_current', 'tds_current', 'ec_current', 'salinity_current')
            ->whereBetween('created_at', [$lastIntervalStart, $now])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->reverse();


        // Debugging statements
        // dd($chartData);
        $formattedChartData = [
            'labels' => $chartData->pluck('created_at')->map(function ($timestamp) {
                return Carbon::parse($timestamp)->format('H:i');
            }),
            'temp' => $chartData->pluck('temp_current'),
            'ph' => $chartData->pluck('ph_current'),
            'tds' => $chartData->pluck('tds_current'),
            'ec' => $chartData->pluck('ec_current'),
            'salinity' => $chartData->pluck('salinity_current'),
        ];

        return $formattedChartData;
    }

    private function getWeeklyChartData()
    {
        $now = Carbon::now();
        $lastSevenDays = $now->subDays(7);


        $chartDataWeekly = DB::table('sensor_data')
            ->selectRaw('DATE(created_at) as date, AVG(temp_current) as temp_avg, AVG(ph_current) as ph_avg, AVG(tds_current) as tds_avg, AVG(ec_current) as ec_avg, AVG(salinity_current) as salinity_avg')
            ->where('created_at', '>=', $lastSevenDays)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // dd($chartDataWeekly);


        $formattedChartDataWeekly = [
            'labels' => $chartDataWeekly->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('D, M d');
            }),
            'temp' => $chartDataWeekly->pluck('temp_avg'),
            'ph' => $chartDataWeekly->pluck('ph_avg'),
            'tds' => $chartDataWeekly->pluck('tds_avg'),
            'ec' => $chartDataWeekly->pluck('ec_avg'),
            'salinity' => $chartDataWeekly->pluck('salinity_avg'),
        ];

        return $formattedChartDataWeekly;
    }
}
