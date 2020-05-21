<?php

include 'pbkdf2.php';
include 'Htpasswd.php';

class Core
{
    private $dbname, $dbusername, $dbpassword;
    public  $dbcon, $config = null;

    public function __construct()
    {
        $config = json_decode(file_get_contents("/fserver/var/www/Classes/Core/confs.json", true));

        $this->confs = $config;
        $this->key = $config->key;
        $this->dbname = $config->dbname;
        $this->dbusername = $config->dbusername;
        $this->dbpassword = $config->dbpassword;
        $this->connect();
    }

    function connect()
    {
        try
        {
            $this->dbcon = new PDO(
                'mysql:host=localhost'.';dbname='.$this->dbname,
                $this->dbusername,
                $this->dbpassword,
                [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]
            );
            $this->dbcon->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
            $this->dbcon->setAttribute(
                PDO::ATTR_EMULATE_PREPARES,
                false
            );
        }
        catch(PDOException $e)
        {
            die($e);
        }
    }
}

class COVID19{

    public function __construct(Core $core)
    {
        $this->confs = $core->confs;
        $this->key = $core->key;
        $this->conn = $core->dbcon;

        $this->dataURL = 'https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_daily_reports/';
    }     

    public function install()
    {

        $begin = new DateTime('2020-01-22');
        $end = new DateTime(date("Y-m-d"));

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        $total = 0;
        foreach ($period as $dt) {
            $rawURL = $this->dataURL . $dt->format("m-d-Y") . ".csv";
            $source = file_get_contents($rawURL);
            $output = "/fserver/var/www/html/Data-Analysis/COVID-19/Data/" . $dt->format("m-d-Y") . ".csv";
            $filedate = $dt->format("m-d-Y");
            file_put_contents($output, $source);
            echo "Importing COVID-19 statistics from " . $output . "\n";

            $csvFile = file($output);
            $data = [];
            $j = 0;
            foreach ($csvFile as $line) {
                $data[$j] = str_getcsv($line);
                if($j != 0):
                    if($filedate < "03-01-2020"):
                        $pdoQuery = $this->conn->prepare("
                            INSERT INTO covid19data (
                                `country`,
                                `province`,
                                `lat`,
                                `lng`,
                                `confirmed`,
                                `deaths`,
                                `recovered`,
                                `active`,
                                `file`,
                                `date`,
                                `timeadded`
                            )  VALUES (
                                :country,
                                :province,
                                :lat,
                                :lng,
                                :confirmed,
                                :deaths,
                                :recovered,
                                :active,
                                :file,
                                :date,
                                :timeadded
                            )
                        ");
                        $pdoQuery->execute([
                            ":country"=>$data[$j][1],
                            ":province"=>$data[$j][0],
                            ":lat"=> "",
                            ":lng"=> "",
                            ":confirmed"=>$data[$j][3] ? $data[$j][3] : 0,
                            ":deaths"=>$data[$j][4] ? $data[$j][4] : 0,
                            ":recovered"=>$data[$j][5] ? $data[$j][5] : 0,
                            ":active"=> 0,
                            ":file"=> $output,
                            ":date"=>date('Y-m-d h:i:s', strtotime($data[$j][2])),
                            ":timeadded"=>time()
                        ]);
                    elseif($filedate < "03-22-2020"):
                        $pdoQuery = $this->conn->prepare("
                            INSERT INTO covid19data (
                                `country`,
                                `province`,
                                `lat`,
                                `lng`,
                                `confirmed`,
                                `deaths`,
                                `recovered`,
                                `active`,
                                `file`,
                                `date`,
                                `timeadded`
                            )  VALUES (
                                :country,
                                :province,
                                :lat,
                                :lng,
                                :confirmed,
                                :deaths,
                                :recovered,
                                :active,
                                :file,
                                :date,
                                :timeadded
                            )
                        ");
                        $pdoQuery->execute([
                            ":country"=>$data[$j][1],
                            ":province"=>$data[$j][0],
                            ":lat"=> $data[$j][6],
                            ":lng"=> $data[$j][7],
                            ":confirmed"=>$data[$j][3] ? $data[$j][3] : 0,
                            ":deaths"=>$data[$j][4] ? $data[$j][4] : 0,
                            ":recovered"=>$data[$j][5] ? $data[$j][5] : 0,
                            ":active"=> 0,
                            ":file"=> $output,
                            ":date"=>date('Y-m-d h:i:s', strtotime($data[$j][2])),
                            ":timeadded"=>time()
                        ]);
                    else:
                        $pdoQuery = $this->conn->prepare("
                            INSERT INTO covid19data (
                                `country`,
                                `province`,
                                `lat`,
                                `lng`,
                                `confirmed`,
                                `deaths`,
                                `recovered`,
                                `active`,
                                `file`,
                                `date`,
                                `timeadded`
                            )  VALUES (
                                :country,
                                :province,
                                :lat,
                                :lng,
                                :confirmed,
                                :deaths,
                                :recovered,
                                :active,
                                :file,
                                :date,
                                :timeadded
                            )
                        ");
                        $pdoQuery->execute([
                            ":country"=>$data[$j][3],
                            ":province"=>$data[$j][2],
                            ":lat"=> $data[$j][5],
                            ":lng"=> $data[$j][6],
                            ":confirmed"=>$data[$j][7] ? $data[$j][7] : 0,
                            ":deaths"=>$data[$j][8] ? $data[$j][8] : 0,
                            ":recovered"=>$data[$j][9] ? $data[$j][9] : 0,
                            ":active"=> $data[$j][10] ? $data[$j][10] : 0,
                            ":file"=> $output,
                            ":date"=>date('Y-m-d h:i:s', strtotime($data[$j][4])),
                            ":timeadded"=>time()
                        ]);
                    endif;
                endif;
                $j++;
            }
            $total = $total + $j;
            echo "Imported " . $j . " rows of COVID-19 statistical data from " . $output . "\n";
        }

        $pdoQuery = $this->conn->prepare("
            INSERT INTO covid19pulls (
                `pulldate`,
                `datefrom`,
                `dateto`,
                `rows`
            )  VALUES (
                :pulldate,
                :datefrom,
                :dateto,
                :rows
            )
        ");
        $pdoQuery->execute([
            ":pulldate"=>date("Y-m-d"),
            ":datefrom"=>"2020-01-22",
            ":dateto"=>date("Y-m-d"),
            ":rows"=>$total
        ]);

        echo "Imported " . $total . " rows of COVID-19 statistical data.\n";
        return True;
    }

}

$Core  = new Core();
$COVID19 = new COVID19($Core);
$COVID19->install();

?>