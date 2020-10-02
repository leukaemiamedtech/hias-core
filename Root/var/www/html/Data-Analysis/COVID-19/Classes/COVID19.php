<?php

require __DIR__ . '/../../../../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;

	class COVID19
	{

		function __construct($_GeniSys)
		{
			$this->_GeniSys = $_GeniSys;

			if(isSet($_SESSION["GeniSysAI"]["Active"])):
				$this->bcc = $this->getBlockchainConf();
				$this->web3 = $this->blockchainConnection();
				$this->contract = new Contract($this->web3->provider, $this->bcc["abi"]);
				$this->checkBlockchainPermissions();
			endif;

			$this->country = filter_input(INPUT_GET, "country", FILTER_SANITIZE_STRING) ?
urldecode(filter_input(INPUT_GET, "country", FILTER_SANITIZE_STRING)) : "Spain";
			$this->period = filter_input(INPUT_GET, "year", FILTER_SANITIZE_STRING) ? filter_input(INPUT_GET, "year", FILTER_SANITIZE_STRING) : "Year";
			$this->stat = filter_input(INPUT_GET, "stat", FILTER_SANITIZE_STRING) ? filter_input(INPUT_GET, "stat", FILTER_SANITIZE_STRING) : "Active";

			$this->dataURL = 'https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_daily_reports/';
		}

		public function getBlockchainConf()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT blockchain.*,
					contracts.contract,
					contracts.abi
				FROM blockchain blockchain
				INNER JOIN contracts contracts
				ON contracts.id = blockchain.dc
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		private function blockchainConnection()
		{
			if(isSet($_SESSION["GeniSysAI"]["Active"])):
				$web3 = new Web3($this->_GeniSys->_helpers->oDecrypt($this->_GeniSys->_confs["domainString"]) . "/Blockchain/API/", 30, $_SESSION["GeniSysAI"]["User"], $this->_GeniSys->_helpers->oDecrypt($_SESSION["GeniSysAI"]["Pass"]));
				return $web3;
			endif;
		}

		private function checkBlockchainPermissions()
		{
			$allowed = "";
			$this->contract->at($this->_GeniSys->_helpers->oDecrypt($this->bcc["contract"]))->call("identifierAllowed", "User", $_SESSION["GeniSysAI"]["Identifier"], ["from" => $_SESSION["GeniSysAI"]["BC"]["BCUser"]], function ($err, $resp) use (&$allowed) {
				if ($err !== null) {
					$allowed = "FAILED";
					return;
				}
				$allowed = $resp[0];
			});

			if($allowed != "true"):
				header('Location: /Logout');
			endif;
		}

		private function unlockBlockchainAccount()
		{
			$response = "";
			$personal = $this->web3->personal;
			$personal->unlockAccount($_SESSION["GeniSysAI"]["BC"]["BCUser"], $this->_GeniSys->_helpers->oDecrypt($_SESSION["GeniSysAI"]["BC"]["BCPass"]), function ($err, $unlocked) use (&$response) {
				if ($err !== null) {
					$response = "FAILED! " . $err;
					return;
				}
				if ($unlocked) {
					$response = "OK";
				} else {
					$response = "FAILED";
				}
			});

			return $response;
		}

		private function getBlockchainBalance()
		{
			$nbalance = "";
			$this->web3->eth->getBalance($_SESSION["GeniSysAI"]["BC"]["BCUser"], function ($err, $balance) use (&$nbalance) {
				if ($err !== null) {
					$response = "FAILED! " . $err;
					return;
				}
				$nbalance = $balance->toString();
			});

			return Utils::fromWei($nbalance, 'ether')[0];
		}

		private function storeUserHistory($action)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				INSERT INTO  history (
					`uid`,
					`action`,
					`time`
				)  VALUES (
					:uid,
					:action,
					:time
				)
			");
			$pdoQuery->execute([
				":uid" => $_SESSION["GeniSysAI"]["Uid"],
				":action" => $action,
				":time" => time()
			]);
			$txid = $this->_GeniSys->_secCon->lastInsertId();
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return $txid;
		}

		public function getCOVID19Countries($params = [])
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT country
				FROM covid19data
				GROUP BY country
				ORDER BY country ASC
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function getCOVID19Pulls($params = [])
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT *
				FROM covid19pulls
				ORDER BY id DESC
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function getCOVID19($params = [])
		{
			if($params["period"]=="Day"):
				$dater = "WHERE date > DATE_SUB(DATE(CURDATE()), INTERVAL 1 DAY)W ";
			elseif($params["period"]=="Week"):
				$dater = "WHERE date >= DATE(NOW()) - INTERVAL 7 DAY ";
			elseif($params["period"]=="Month"):
				$dater = "WHERE date > DATE_SUB(DATE(CURDATE()), INTERVAL 1 MONTH)  ";
			elseif($params["period"]=="Year"):
				$dater = "WHERE date > DATE_SUB(DATE(CURDATE()), INTERVAL 1 YEAR)  ";
			endif;

			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT date,
					sum(confirmed) as confirmed,
					sum(deaths) as deaths,
					sum(active) as active,
					sum(recovered) as recovered
				FROM covid19data
				$dater
				&& Country = :country
				GROUP BY Country, date
				ORDER BY date ASC
			");
			$pdoQuery->execute([
				":country" => $params["country"]
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;
			return $response;
		}

		public function getCOVID19Periods()
		{
			$year = $this->getCOVID19([
				"period" => "Year",
				"country" => $this->country
			]);

			$month = $this->getCOVID19([
				"period" => "Month",
				"country" => $this->country
			]);

			$week = $this->getCOVID19([
				"period" => "Week",
				"country" => $this->country
			]);

			$yeard = array_column($year, 'deaths');
			$yearddate = array_column($year, 'dates');

			$monthd = array_column($month, 'deaths');
			$monthddate = array_column($month, 'dates');

			$weekd = array_column($week, 'deaths');
			$weekddate = array_column($week, 'dates');

			$yeara = array_column($year, 'active');
			$montha = array_column($month, 'active');
			$weeka = array_column($week, 'active');

			return [$yeard, $monthd, $weekd, $yearddate, $monthddate, $weekddate];
		}

		public function getCOVID19MonthDeaths($month)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT sum(deaths) as deaths
				FROM covid19data
				WHERE MONTH(Date) = :date
				&& Country = :country
				GROUP BY Country, date
				ORDER BY date ASC
			");
			$pdoQuery->execute([
				":date" => $month,
				":country" => $this->country
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return array_column($response, 'deaths');

		}

		public function getCOVID19MonthActive($month)
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT sum(confirmed) as confirmed
				FROM covid19data
				WHERE MONTH(Date) = :date
				&& Country = :country
				GROUP BY Country, date
				ORDER BY date ASC
			");
			$pdoQuery->execute([
				":date" => $month,
				":country" => $this->country
			]);
			$response=$pdoQuery->fetchAll(PDO::FETCH_ASSOC);
			$pdoQuery->closeCursor();
			$pdoQuery = null;

			return array_column($response, 'confirmed');
		}

		public function getCOVID19Totals($params = [])
		{
			$covid19d = $this->getCOVID19([
				"period" => $this->period,
				"country" => $this->country
			]);

			$active = array_column($covid19d, 'active');
			$recovered = array_column($covid19d, 'recovered');
			$deaths = array_column($covid19d, 'deaths');
			$dates = array_column($covid19d, 'date');

			$periods = $this->getCOVID19Periods();

			if($this->stat == "Deaths"):
				$cstats = $deaths;
			elseif($this->stat == "Active"):
				$cstats = $active;
			elseif($this->stat == "Recovered"):
				$cstats = $recovered;
			endif;

			return [$cstats, $active[count($active)-1], $recovered[count($recovered)-1],
					$deaths[count($deaths)-1], $dates, $periods[0], $periods[1], $periods[2]];
		}

		public function updateCOVID()
		{
			$pdoQuery = $this->_GeniSys->_secCon->prepare("
				SELECT pulldate
				FROM covid19pulls
				ORDER BY id DESC
			");
			$pdoQuery->execute();
			$response=$pdoQuery->fetch(PDO::FETCH_ASSOC);

			$begin = new DateTime($response["pulldate"]);
			$end = new DateTime(date("Y-m-d"));

			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end);

			$total = 0;
			if(iterator_count($period)):
				foreach ($period as $dt) {

					$j = 0;
					$output = "";

					$formatted = $dt->format("m-d-Y");
					$rawURL = $this->dataURL . $formatted . ".csv";
					$filedate = $dt->format("m-d-Y");

					try {
						$source = file_get_contents($rawURL);
					} catch (Exception $e) {
						return [
							"Response" => "FAILED",
							"Message" =>  "No new data for " . $begin . " - " . $end . " currently available. "
						];
					}

					$output = "/fserver/var/www/html/Data-Analysis/COVID-19/Data/" . $formatted . ".csv";
					file_put_contents($output, $source);
					$csvFile = file($output);
					$data = [];
					if(count($csvFile)):
						foreach ($csvFile as $line) {
							$data[$j] = str_getcsv($line);
							if($j != 0):
								if($filedate < "03-01-2020"):
									$pdoQuery = $this->_GeniSys->_secCon->prepare("
										INSERT IGNORE INTO covid19data (
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
									$pdoQuery = $this->_GeniSys->_secCon->prepare("
										INSERT IGNORE INTO covid19data (
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
									$pdoQuery = $this->_GeniSys->_secCon->prepare("
										INSERT IGNORE INTO covid19data (
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
								$total = $total + $pdoQuery->rowCount();
							endif;
							$j++;
						}

						$pdoQuery = $this->_GeniSys->_secCon->prepare("
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
							":datefrom"=>$response["pulldate"],
							":dateto"=>date("Y-m-d"),
							":rows"=>$total
						]);

						$this->storeUserHistory("Updated COVID Data (" . $total . ") rows.");

						return [
							"Response" => "OK",
							"Message" =>  "Imported " . $total . " rows of COVID-19 statistical data from " . $output
						];

					else:
						$this->storeUserHistory("Updated COVID Data (" . $total . ") rows.");
						return [
							"Response" => "FAILED",
							"Message" =>  "No new data currently available. "
						];
					endif;

				}
			else:
				$this->storeUserHistory("Updated COVID Data (" . $total . ") rows.");
				return [
					"Response" => "FAILED",
					"Message" =>  "No new data currently available. "
				];
			endif;

		}

	}

	$COVID19 = new COVID19($_GeniSys);

	if(filter_input(INPUT_POST, "updateCOVID", FILTER_SANITIZE_NUMBER_INT)):
		die(json_encode($COVID19->updateCOVID()));
	endif;