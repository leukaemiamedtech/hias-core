/*Dashboard Init*/
 
"use strict"; 

/*****Ready function start*****/
$(document).ready(function(){
	if( $('#employee_table').length > 0 ) {
		$('#employee_table').DataTable({
		 "bFilter": false,
		 "bLengthChange": false,
		 "bPaginate": false,
		 "bInfo": false,
		});
	}
});
/*****Ready function end*****/

/*****Load function start*****/
$(window).on("load",function(){
	window.setTimeout(function(){
		$.toast({
			heading: 'Welcome to Grandin',
			text: 'Use the predefined ones, or specify a custom position object.',
			position: 'bottom-left',
			loaderBg:'#e3c94b',
			icon: '',
			hideAfter: 3500, 
			stack: 6
		});
	}, 3000);
});
/*****Load function* end*****/

/*****E-Charts function start*****/
var echartsConfig = function() { 
	if( $('#e_chart_1').length > 0 ){
		var eChart_1 = echarts.init(document.getElementById('e_chart_1'));
		var size = 30;
		var yy = 200;
		var yy1 = 250;
		var option = {
			tooltip: {
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
			animationDuration: 3000,
			animationEasingUpdate: 'quinticInOut',
			series: [{
				name: 'a1',
				type: 'graph',
				layout: 'force',
				force: {
					//initLayout: ...,
					repulsion: 500,
					gravity: 0.1,
					edgeLength: 100,
					layoutAnimation: true,
				},
				data: [{
					"name": "a2",
					x: 100,
					y: yy,
					"symbolSize": size,
					"category": "a2",
					"draggable": "true",
					"value": 100,
					itemStyle: {
						normal: {
							color: ['#f742aa'],	
						}
					},

				}, {
					"name": "a3",
					"value": 200,
					x: 200,
					y: yy1,
					"symbolSize": size,
					"category": "a3",
					"draggable": "true",
					itemStyle: {
						normal: {
							color: ['#635bd6'],	
						}
					},
				}, {
					x: 300,
					y: yy,
					"name": "BRAS",
					"symbolSize": size,
					"category": "BRAS",
					"draggable": "true",
					"value": 1,
					itemStyle: {
						normal: {
							color: ['#f742aa'],	
						}
					},
				},{
					x: 300,
					y: 300,
					"name": "BRAS1",
					"symbolSize": 20,
					"category": "BRAS",
					"draggable": "true",
					"value": 1,
					itemStyle: {
						normal: {
							color: ['#958FEF'],	
						}
					},
				},{
					x: 300,
					y: 300,
					"name": "BRAS2",
					"symbolSize": 20,
					"category": "BRAS",
					"draggable": "true",
					"value": 1,
					itemStyle: {
						normal: {
							color: ['#635bd6'],	
						}
					},
				}, {
					x: 400,
					y: yy1,
					"name": "a4",
					"symbolSize": size,
					"category": "a4",
					"draggable": "true",
					"value": 1,
					itemStyle: {
						normal: {
							color: ['#958FEF'],	
						}
					},
				}, {
					x: 500,
					y: yy,
					"name": "OLT",
					"symbolSize": size,
					"category": "OLT",
					"draggable": "true",
					"value": 1,
					itemStyle: {
						normal: {
							color: ['#635bd6'],	
						}
					},
				}, {
					x: 600,
					y: yy1,
					"name": "p1",
					"symbolSize": size,
					"category": "p1",
					"draggable": "true",
					"value": 1,
					itemStyle: {
						normal: {
							color: ['#958FEF'],	
						}
					},
				}, ],
				links: [{
					"source": "a2",
					"target": "a3"
				}, {
					"source": "a3",
					"target": "BRAS"
				}, {
					"source": "BRAS",
					"target": "a4"
				}, {
					"source": "BRAS",
					"target": "BRAS1"
				}, {
					"source": "BRAS",
					"target": "BRAS2"
				}, {
					"source": "a4",
					"target": "OLT"
				}, {
					"source": "OLT",
					"target": "p1"
				}, ],
				categories: [{
					'name': 'a2'
				}, {
					'name': 'BRAS'
				}, {
					'name': 'a4'
				}, {
					'name': 'OLT'
				}, {
					'name': 'p1'
				}, {
					'name': 'a3'
				}],
				//focusNodeAdjacency: true,
				roam: false,
				label: {
					normal: {
					   show: false,
					}
				},
				
				lineStyle: {
					normal: {
						show:false
					}
				}
			}]
		};
		eChart_1.setOption(option);
		eChart_1.resize();
	}
	if( $('#e_chart_2').length > 0 ){
		var eChart_2 = echarts.init(document.getElementById('e_chart_2'));
		var option1 = {
			animation: false,
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
			color: ['#635bd6'],	
			grid: {
				top: 60,
				left:40,
				bottom: 30
			},
			xAxis: {
				type: 'value',
				position: 'top',
				axisLine: {
					show:false
				},
				axisLabel: {
					textStyle: {
						color: '#878787'
					}
				},
				splitLine: {
					show:false
				},
			},
			yAxis: {
				splitNumber: 25,
				type: 'category',
				axisLine: {
					show:false
				},
				axisLabel: {
					textStyle: {
						color: '#878787'
					}
				},
				axisTick: {
					show: true
				},
				splitLine: {
					show:false
				},
				data: ['Oct', 'Sep', 'Aug', 'July', 'June', 'May', 'Apr', 'Mar', 'Feb', 'Jan']
			},
			series: [{
				name: 'emp',
				type: 'bar',
				barGap: '-100%',
				label: {
					normal: {
						textStyle: {
							color: '#682d19'
						},
						position: 'left',
						show: false,
						formatter: '{b}'
					}
				},
				itemStyle: {
					normal: {
						color: '#635bd6',
					}
				},
				data: [190, 102, 160, 200, 110, 180, 280, 140, 220, 300]
			}, {
				type: 'line',
				silent: true,
				barGap: '-100%',
				data: [100, 100, 400, 170, 200, 300, 100, 200, 120, 200],
				itemStyle: {
					normal: {
						color: '#f742aa',

					}
				},

			}]
		}
		eChart_2.setOption(option1);
		eChart_2.resize();
	}
	if( $('#e_chart_3').length > 0 ){
		var eChart_3 = echarts.init(document.getElementById('e_chart_3'));
		var option3 = {
			timeline: {
				data: ['91', '92', '93', '94', '95', '96', '97', '98', '99', '91'],
				axisType: 'category',
				show: false,
				autoPlay: true,
				playInterval: 1000,
			},
			options: [{
				tooltip: {
					trigger: 'axis',
					backgroundColor: 'rgba(33,33,33,1)',
					borderRadius:0,
					padding:10,
					textStyle: {
						color: '#fff',
						fontStyle: 'normal',
						fontWeight: 'normal',
						fontFamily: "'Montserrat', sans-serif",
						fontSize: 12
					}	
				},
				calculable: true,
				grid: {
					show:false
				},
				xAxis: [{
					'type': 'category',
					axisLabel: {
						textStyle: {
							color: '#878787',
							fontStyle: 'normal',
							fontWeight: 'normal',
							fontFamily: "'Montserrat', sans-serif",
							fontSize: 12
						}
					},
					axisLine: {
						show:false
					},
					splitLine:{
						show:false
					},
					'data': [
						'x1', ' x2', 'x3', 'x4', 'x5', 'x6', 'x7', 'x8'
					]
				}],
				yAxis: [{
					'type': 'value',
					'max': 200,
					axisLine: {
						show:false
					},
					axisLabel: {
						textStyle: {
							color: '#878787',
							fontStyle: 'normal',
							fontWeight: 'normal',
							fontFamily: "'Montserrat', sans-serif",
							fontSize: 12
						}
					},
					splitLine: {
						show: false,
					},
				}, {
					'type': 'value',
					axisLine: {
						show:false
					},
					splitLine: {
						show: false,
					},
					axisLabel: {
						textStyle: {
							color: '#fff',
							fontStyle: 'normal',
							fontWeight: 'normal',
							fontFamily: "'Montserrat', sans-serif",
							fontSize: 12
						}
					},
				}],
				series: [{
					'name': 'tq',
					'yAxisIndex': 1,
					'type': 'line',
					'data': [5, 6, 8, 28, 8, 24, 11, 16],
					itemStyle: {
						normal: {
							color: new echarts.graphic.LinearGradient(
								0, 1, 0, 0, [{
									offset: 0,
									color: '#635bd6'
								}, {
									offset: 1,
									color: '#f742aa'
								}]
							),
							barBorderRadius: 4
						},
						emphasis: {
							color: new echarts.graphic.LinearGradient(
								0, 1, 0, 0, [{
									offset: 0,
									color: '#635bd6'
								}, {
									offset: 1,
									color: '#fff'
								}]
							),
							barBorderRadius: 4
						}
					},
					label: {
						normal: {
							show: true,
							position: 'top',
							formatter: '{c}',
							color: '#fff',
							fontStyle: 'normal',
							fontWeight: 'normal',
							fontFamily: "'Montserrat', sans-serif",
							fontSize: 12
						}
					},
				}]
			}, {
				series: [{
					'data': [45, 43, 64, 134, 188, 43, 109, 12]
				}]
			}, {
				series: [{
					'data': [110, 32, 111, 176, 73, 59, 181, 9]
				}]
			}, {
				series: [{
					'data': [94, 37, 64, 55, 56, 41, 70, 17]
				}]
			}, {
				series: [{
					'data': [5, 6, 5, 28, 8, 24, 11, 16]
				}]
			}, {
				series: [{
					'data': [45, 34, 64, 134, 188, 43, 109, 12]
				}]
			}, {
				series: [{
					'data': [5, 6, 34, 28, 8, 24, 11, 16]
				}]
			}, {
				series: [{
					'data': [94, 37, 64, 55, 56, 41, 70, 17]
				}]
			}, {
				series: [{
					'data': [45, 40, 64, 134, 188, 43, 109, 12]
				}]
			}, {
				series: [{
					'data': [5, 6, 10, 28, 8, 24, 11, 16]
				}]
			}, ]
		};
		eChart_3.setOption(option3);
		eChart_3.resize();
	}
	if( $('#e_chart_4').length > 0 ){
		var eChart_4 = echarts.init(document.getElementById('e_chart_4'));
		var data = [];
		for (var i = 0; i <= 10; i++) {
			var theta = i / 100 * 30;
			var r = 5 * (1 + Math.sin(theta / 180 * Math.PI));
			data.push([r, theta]);
		}
		var option4 = {
			polar: {},
			tooltip: {
				trigger: 'axis',
				backgroundColor: 'rgba(33,33,33,1)',
				borderRadius:0,
				padding:10,
				textStyle: {
					color: '#fff',
					fontStyle: 'normal',
					fontWeight: 'normal',
					fontFamily: "'Montserrat', sans-serif",
					fontSize: 12
				}	
			},
			angleAxis: {
				type: 'value',
				startAngle: 0,
				axisLine: {
					lineStyle: {
						color: '#878787'
					}
				},
				axisLabel: {
					textStyle: {
						color: '#878787',
						fontSize: 12,
						fontFamily: "'Montserrat', sans-serif",
					}
				},
			},
			radiusAxis: {
				axisLine: {
					lineStyle: {
						color: '#878787'
					}
				},
				axisLabel: {
					textStyle: {
						color: '#878787',
						fontSize: 12,
						fontFamily: "'Montserrat', sans-serif",
					}
				},
			},
			series: [{
				coordinateSystem: 'polar',
				name: 'line',
				type: 'line',
				lineStyle: {
					normal: {
						color: '#635bd6',
					}
				},
				itemStyle: {
					normal: {
						color: '#635bd6',
					}
				},
				 areaStyle: {
					normal: {
						color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
						   offset: 0,
						   color: '#635bd6'
						   }, {
						   offset: 1,
						   color: '#f742aa'
						}])
					}
					},
				
				data: data
			}]
		};
		eChart_4.setOption(option4);
		eChart_4.resize();
	}
}
/*****E-Charts function end*****/

/*****Sparkline function start*****/
var sparklineLogin = function() { 
	if( $('#sparkline_4').length > 0 ){
		$("#sparkline_4").sparkline([2,4,4,6,8,5,6,4,8,6,6,2 ], {
			type: 'line',
			width: '100%',
			height: '35',
			lineColor: '#fff',
			fillColor: '#fff',
			minSpotColor: '#fff',
			maxSpotColor: '#fff',
			spotColor: '#fff',
			highlightLineColor: '#fff',
			highlightSpotColor: '#fff'
		});
	}	
}
/*****Sparkline function end*****/

/*****Resize function start*****/
var sparkResize,echartResize;
$(window).on("resize", function () {
	/*Sparkline Resize*/
	clearTimeout(sparkResize);
	sparkResize = setTimeout(sparklineLogin, 200);
	
	/*E-Chart Resize*/
	clearTimeout(echartResize);
	echartResize = setTimeout(echartsConfig, 200);
}).resize(); 
/*****Resize function end*****/

/*****Function Call start*****/
sparklineLogin();
echartsConfig();
/*****Function Call end*****/