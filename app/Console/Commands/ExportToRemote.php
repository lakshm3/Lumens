<?php

namespace App\Console\Commands;

use App\Models\Engine;
use GuzzleHttp\Client;
use App\Models\Settings;
use App\Models\EngineData;
use GuzzleHttp\Psr7\Request;
use Illuminate\Console\Command;

class ExportToRemote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:remote {--from_date= : Export from this date} {--limit= : limit number of measure to export} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export data to remote platform';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    private function getTSdb()
    {
        return config("database.default") == "sqlite" ? "strftime('%s', datetime)" : "UNIX_TIMESTAMP(datetime)";
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = microtime(true);
        $apiKey = Settings::where("name", "api_key")->first()->value;

        $options = $this->options();

        if ($options["from_date"] != null) {
            $lastDateSent = $options["from_date"];
        } else {
            if (Settings::where("name", "last_date_sent")->first() == null) {
                $lastDateSent = "2010-01-01 00:00:00";
            } else {
                $lastDateSent = Settings::where("name", "last_date_sent")->first()->value;
            }
        }

        echo "From Date = " . $lastDateSent . "\n";

        $engines = Engine::all();
        $final = [];
        $minTs = strptime($lastDateSent, '%Y-%m-%d %H:%M:%S');
        $limit = $options["limit"] ?? 10;
        foreach ($engines as $e) {

            $result = EngineData::where("engine_id", $e->id)
                ->where("datetime", ">", $lastDateSent)
                ->orderBy("datetime", "ASC")->select("datetime", "data")->limit($limit)->get();

            if (count($result) > 0) {
                if ($minTs == 0) $minTs = $result[count($result) - 1]->ts;
                else $minTs = min($result[count($result) - 1]->ts, $minTs);

                foreach ($result as $k => $r) {
                    $row = [
                        "hardware_id" => $e->id,
                        "type" => "vibox",
                        "ts" => $r->ts,
                        "data" => $r->data,
                        "process_type" => $e->process_type,
                        "engine_vib_id" => $e->engine_vib_id,
                        "hardware_name" => $e->name,
                        "last_status" => $e->last_status,
                    ];

                    $final[] = $row;
                }
            }
        }

        $payload = json_encode($final);

        echo "Payload Ready in " . (microtime(true) - $start) . "seconds\n";
        $start = microtime(true);

        $url = config("app.remote_platform_url");
        $endpoint = $url . "/api/storeData";
        $headers = [
            "ApiToken" => $apiKey,
            'Content-Type' => 'application/json',
            'Content-Length' => strlen($payload)
        ];

        $request = new Request("POST", $endpoint, $headers, $payload);

        $client = new Client();
        $response = $client->send($request);
        $output = $response->getBody();
        $r = \json_decode($output, true);

        echo "Sent in " . (microtime(true) - $start) . "seconds to " . $endpoint . "\n";

        if ($r["success"]) {
            echo $output . "\n";

            $date = date("Y-m-d H:i:s", $minTs);
            echo "minDt = " . $date . " \n";
            $setting = Settings::where("name", "last_date_sent")->first();
            if ($setting == null) $setting = new Settings();
            $setting->name = "last_date_sent";
            $setting->value = $date;
            $setting->save();
        } else {
            die($output);
        }

        //on post la dernière heure à la platforme distante
    }
}
