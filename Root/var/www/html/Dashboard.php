<?php session_start();

$pageDetails = [
	"PageID" => "Home"
];


include dirname(__FILE__) . '/../Classes/Core/init.php';
include dirname(__FILE__) . '/../Classes/Core/GeniSys.php';
include dirname(__FILE__) . '/iotJumpWay/Classes/iotJumpWay.php';
include dirname(__FILE__) . '/AI/TassAI/Classes/TassAI.php';
include dirname(__FILE__) . '/Data-Analysis/COVID-19/Classes/COVID19.php';

$country = "Spain";
$period = "Year";
$stat = "Deaths";

$_GeniSysAi->checkSession();
$TDevice = $iotJumpWay->getDevice(2);
$stats = $_GeniSysAi->getStats();

$covid19d = $COVID19->getCOVID19Totals();

$cstats = $covid19d[0];
$active = $covid19d[1];
$recovered = $covid19d[2];
$deaths = $covid19d[3];
$dates = $covid19d[4];
$yeard = $covid19d[5];
$monthd = $covid19d[6];
$weekd = $covid19d[7];

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<meta name="robots" content="noindex, nofollow" />

		<title><?=$_GeniSys->_confs["meta_title"]; ?></title>
		<meta name="description" content="<?=$_GeniSys->_confs["meta_description"]; ?>" />
		<meta name="keywords" content="" />
		<meta name="author" content="hencework"/>

		<script src="https://kit.fontawesome.com/58ed2b8151.js" crossorigin="anonymous"></script>

		<link type="image/x-icon" rel="icon" href="<?=$domain; ?>/img/favicon.png" />
		<link type="image/x-icon" rel="shortcut icon" href="<?=$domain; ?>/img/favicon.png" />
		<link type="image/x-icon" rel="apple-touch-icon" href="<?=$domain; ?>/img/favicon.png" />

		<link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?=$domain; ?>/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?=$domain; ?>/vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
		<link href="<?=$domain; ?>/dist/css/style.css" rel="stylesheet" type="text/css">
		<link href="<?=$domain; ?>/AI/GeniSysAI/Media/CSS/GeniSys.css" rel="stylesheet" type="text/css">
	</head>

	<body>

		<div class="preloader-it">
			<div class="la-anim-1"></div>
		</div>

		<div class="wrapper theme-6-active pimary-color-pink">

			<?php include dirname(__FILE__) . '/Includes/Nav.php'; ?>
			<?php include dirname(__FILE__) . '/Includes/LeftNav.php'; ?>
			<?php include dirname(__FILE__) . '/Includes/RightNav.php'; ?>

			<div class="page-wrapper">
			<div class="container-fluid pt-25">

				<?php include dirname(__FILE__) . '/Includes/Stats.php'; ?>

				<div class="row">
					<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<?php include dirname(__FILE__) . '/Includes/Weather.php'; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<?php include dirname(__FILE__) . '/iotJumpWay/Includes/iotJumpWay.php'; ?>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view panel-refresh">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">COVID-19 <?=$COVID19->stat; ?> this <?=$COVID19->period; ?> in <?=$COVID19->country; ?></h6>
								</div>
								<div class="pull-right">
									<div class="pull-left inline-block dropdown">
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div id="e_chart_1" class="" style="height: 375px;"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<img src="<?=$domain; ?>/TassAI/<?=$TDevice["context"]["Data"]["proxy"]["endpoint"]; ?>/<?=$TDevice["context"]["Data"]["stream"]["file"]; ?>" style="width: 100%;" />
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view pa-0 bg-gradient">
							<div class="panel-wrapper collapse in">
								<div class="panel-body pa-0">
									<div class="sm-data-box">
										<div class="container-fluid">
											<div class="row">
												<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
													<span class="txt-light block counter"><span class=""><?=$active; ?></span></span>
													<span class="weight-500 uppercase-font block font-13 txt-light">Active Patients</span>
												</div>
												<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
													<i class="fas fa-hospital-alt data-right-rep-icon txt-light"></i>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view pa-0 bg-gradient">
							<div class="panel-wrapper collapse in">
								<div class="panel-body pa-0">
									<div class="sm-data-box">
										<div class="container-fluid">
											<div class="row">
												<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
													<span class="txt-light block counter"><span class=""><?=$recovered; ?></span></span>
													<span class="weight-500 uppercase-font block txt-light">Recovered Patients</span>
												</div>
												<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
													<i class="fas fa-hospital-alt  data-right-rep-icon txt-light"></i>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default card-view pa-0 bg-gradient">
							<div class="panel-wrapper collapse in">
								<div class="panel-body pa-0">
									<div class="sm-data-box">
										<div class="container-fluid">
											<div class="row">
												<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left txt-light">
													<span class="block counter"><span class=""><?=$deaths; ?></span></span>
													<span class="weight-500 uppercase-font block">Deaths</span>
												</div>
												<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
													<i class="fas fa-hospital-alt data-right-rep-icon txt-light"></i>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel card-view bg-gradient3">
							<div class="panel-heading small-panel-heading relative">
								<div class="pull-left">
									<h6 class="panel-title txt-light"></h6>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body row pa-0">
									<div class="sm-data-box">
										<div class="container-fluid">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-9 col-md-6 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">COVID-19 Cases & Deaths</h6>
								</div>
								<div class="pull-right">
									<div class="pull-left form-group mb-0 sm-bootstrap-select mr-15">
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<ul class="flex-stat mb-10 ml-15">
										<li class="text-left auto-width mr-60">
											<span class="block">This Last Year</span>
											<span class="block txt-dark weight-500 font-18"><span class=""><?=$yeard[count($yeard)-1]; ?></span></span>
											<span class="block txt-success mt-5">
												<span class="weight-500"></span>
											</span>
											<div class="clearfix"></div>
										</li>
										<li class="text-left auto-width mr-60">
											<span class="block">This Last Month</span>
											<span class="block txt-dark weight-500 font-18"><span class=""><?=$monthd[count($monthd)-1] - $monthd[0]; ?></span></span>
											<span class="block txt-success mt-5">
												<span class="weight-500"></span>
											</span>
											<div class="clearfix"></div>
										</li>
										<li class="text-left auto-width">
											<span class="block">This Last Week</span>
											<span class="block txt-dark weight-500 font-18"><span class=""><?=$weekd[count($weekd)-1] - $weekd[0]; ?></span></span>
											<span class="block txt-dark mt-5">
												<span class="weight-500"></span>
											</span>
											<div class="clearfix"></div>
										</li>
									</ul>
									<div id="hos_chart" class="" style="height: 350px;"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php include dirname(__FILE__) . '/Includes/Footer.php'; ?>

		</div>

		<?php  include dirname(__FILE__) . '/Includes/JS.php'; ?>

		<script type="text/javascript" src="<?=$domain; ?>/vendors/bower_components/echarts/dist/echarts-en.min.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/vendors/echarts-liquidfill.min.js"></script>
		<script type="text/javascript">

			var eChart_1 = echarts.init(document.getElementById('e_chart_1'));

			var option = {
				tooltip: {
					trigger: 'axis',
					backgroundColor: 'rgba(33,33,33,1)',
					borderRadius: 0,
					padding: 10,
					axisPointer: {
						type: 'cross',
						label: {
							backgroundColor: 'rgba(33,33,33,1)'
						}
					},
					textStyle: {
						color: '#fff',
						fontStyle: 'normal',
						fontWeight: 'normal',
						fontFamily: "'Montserrat', sans-serif",
						fontSize: 12
					}
				},
				color: ['#635bd6'],
				xAxis: {
					type: 'category',
					axisLabel: {
						textStyle: {
							color: '#ffffff'
						},
						interval: 1,
						rotate: 45
					},
					data: <?=json_encode($dates); ?>
				},
				yAxis: {
					axisLabel: {
						textStyle: {
							color: '#ffffff'
						}
					},
					type: 'value'
				},
				grid: {
					top: 10,
					left: 0,
					right: 0,
					bottom: 100,
					containLabel: true
				},
				series: [{
					data: <?=json_encode($cstats); ?>,
					type: 'line',
					smooth: true
				}]
			};
			eChart_1.setOption(option);
			eChart_1.resize();


			var hos_chart_v = echarts.init(document.getElementById('hos_chart'));
			var option = {
				title: {
					text: 'Awesome Chart'
				},
				xAxis: {
					data: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
				},
				yAxis: {},
				series: [{
					type: 'line',
					data:[220, 182, 191, 234, 290, 330, 310]
				}]
			};
			var myData = ['', '', '', '', '', '', ''];
			var databeast = {
				"Jan": <?=json_encode($COVID19->getCOVID19MonthActive(01)); ?>,
				"Feb": <?=json_encode($COVID19->getCOVID19MonthActive(02)); ?>,
				"Mar": <?=json_encode($COVID19->getCOVID19MonthActive(03)); ?>,
				"Apr": <?=json_encode($COVID19->getCOVID19MonthActive(04)); ?>,
				"May": <?=json_encode($COVID19->getCOVID19MonthActive(05)); ?>,
			};
			var databeauty = {
				"Jan": <?=json_encode($COVID19->getCOVID19MonthDeaths(01)); ?>,
				"Feb": <?=json_encode($COVID19->getCOVID19MonthDeaths(02)); ?>,
				"Mar": <?=json_encode($COVID19->getCOVID19MonthDeaths(03)); ?>,
				"Apr": <?=json_encode($COVID19->getCOVID19MonthDeaths(04)); ?>,
				"May": <?=json_encode($COVID19->getCOVID19MonthDeaths(05)); ?>,
			};
			var timeLineData = ["Jan", "Feb", "Mar", "Apr", "May"];
			option = {
				baseOption: {
					timeline: {
						show: true,
						axisType: 'category',
						tooltip: {
							show: true,
							formatter: function(params) {
								console.log(params);
								return params.name + 'xyz';
							}
						},
						autoPlay: true,
						currentIndex: 6,
						playInterval: 1000,
						checkpointStyle: {
							color: 'transparent',
							borderColor: '#635bd6'
						},
						itemStyle: {
							normal: {
								color: '#635bd6'
							},
							emphasis: {
								color: '#635bd6'
							}
						},
						controlStyle: {
							show:false
						},
						lineStyle: {
							color: '#635bd6'
						},
						label: {
							normal: {
								show: true,
								interval: 'auto',
								formatter: '{value}',
								textStyle: {
									color: '#878787',
									fontStyle: 'normal',
									fontWeight: 'normal',
									fontFamily: "'Montserrat', sans-serif",
									fontSize: 12
								}
							},
						},
						data: [],
					},
					tooltip: {
					trigger: 'axis',
					backgroundColor: 'rgba(33,33,33,1)',
					borderRadius:0,
					padding:10,
					axisPointer: {
						type: 'cross',
						label: {
							backgroundColor: 'rgba(33,33,33,1)'
						}
					},
					textStyle: {
						color: '#fff',
						fontStyle: 'normal',
						fontWeight: 'normal',
						fontFamily: "'Montserrat', sans-serif",
						fontSize: 12
					}
				},
				grid: [{
						show: false,
						left: '4%',
						top: 30,
						bottom: 60,
						containLabel: true,
						width: '46%',
					}, {
						show: false,
						left: '50.5%',
						top: 30,
						bottom: 60,
						width: '0%',
					}, {
						show: false,
						right: '4%',
						top: 30,
						bottom: 60,
						containLabel: true,
						width: '46%',
					}, ],

					xAxis: [
						{
						type: 'value',
						inverse: true,
						axisLine: {
							show: false,
						},
						axisTick: {
							show: false,
						},
						position: 'top',
						axisLabel: {
							show: true,
							textStyle: {
								color: '#878787',
								fontStyle: 'normal',
								fontWeight: 'normal',
								fontFamily: "'Montserrat', sans-serif",
								fontSize: 12
							},
						},
						splitLine: {
							show: false,
						},
					}, {
						gridIndex: 1,
						show: false,
					}, {
						gridIndex: 2,
						type: 'value',
						axisLine: {
							show: false,
						},
						axisTick: {
							show: false,
						},
						position: 'top',
						axisLabel: {
							show: true,
							textStyle: {
								color: '#878787',
								fontStyle: 'normal',
								fontWeight: 'normal',
								fontFamily: "'Montserrat', sans-serif",
								fontSize: 12
							},
						},
						splitLine: {
							show: false,
						},
					}, ],
					yAxis: [{
						type: 'category',
						inverse: true,
						position: 'right',
						axisLine: {
							show: false
						},
						axisTick: {
							show: false
						},
						axisLabel: {
							show: false,
							margin: 8,
							textStyle: {
								color: '#878787',
								fontStyle: 'normal',
								fontWeight: 'normal',
								fontFamily: "'Montserrat', sans-serif",
								fontSize: 12
							},

						},
						data: myData,
					}, {
						gridIndex: 1,
						type: 'category',
						inverse: true,
						position: 'left',
						axisLine: {
							show: false
						},
						axisTick: {
							show: false
						},
						axisLabel: {
							show: true,
							textStyle: {
								color: '#878787',
								fontStyle: 'normal',
								fontWeight: 'normal',
								fontFamily: "'Montserrat', sans-serif",
								fontSize: 12
							},

						},
						data: myData.map(function(value) {
							return {
								value: value,
								textStyle: {
									align: 'center',
								}
							}
						}),
					}, {
						gridIndex: 2,
						type: 'category',
						inverse: true,
						position: 'left',
						axisLine: {
							show: false
						},
						axisTick: {
							show: false
						},
						axisLabel: {
							show: false,
							textStyle: {
								color: '#878787',
								fontStyle: 'normal',
								fontWeight: 'normal',
								fontFamily: "'Montserrat', sans-serif",
								fontSize: 12
							},

						},
						data: myData,
					}, ],
					series: [],

				},

				options: [],
			};
			for (var i = 0; i < timeLineData.length; i++) {
				option.baseOption.timeline.data.push(timeLineData[i]);
				option.options.push({
				series: [{
							name: 's1',
							type: 'bar',
							barGap: 20,
							barWidth: 20,
							label: {
								normal: {
									show: false,
								},
								emphasis: {
									show: true,
									position: 'left',
									offset: [0, 0],
									textStyle: {
										color: '#fff',
										fontSize: 14,
									},
								},
							},
							itemStyle: {
								normal: {
									color: '#f742aa',
								},
								emphasis: {
									color: '#f742aa',
								},
							},
							data: databeast[timeLineData[i]],
						},


						{
							name: 's2',
							type: 'bar',
							barGap: 20,
							barWidth: 20,
							xAxisIndex: 2,
							yAxisIndex: 2,
							label: {
								normal: {
									show: false,
								},
								emphasis: {
									show: true,
									position: 'right',
									offset: [0, 0],
									textStyle: {
										color: '#fff',
										fontSize: 14,
									},
								},
							},
							itemStyle: {
								normal: {
									color: '#635bd6',
								},
								emphasis: {
									color: '#635bd6',
								},
							},
							data: databeauty[timeLineData[i]],
						}
					]
				});
			}
			hos_chart_v.setOption(option);
			hos_chart_v.resize();
		</script>

		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/mqttws31.js"></script>
		<script type="text/javascript" src="<?=$domain; ?>/iotJumpWay/Classes/iotJumpWay.js"></script>

	</body>
</html>
