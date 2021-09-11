/*Dashboard2 Init*/
"use strict"; 

/*****Ready function start*****/
$(document).ready(function(){
	if( $('#datable_1').length > 0 )
		$('#datable_1').DataTable({
			"aLengthMenu": [[4, 8, 12, -1], [4, 8, 12, "All"]],
			"iDisplayLength": 4
		});
});
/*****Ready function end*****/

/*****E-Charts function start*****/
var echartsConfig = function() { 
	if( $('#e_chart_1').length > 0 ){
		var eChart_1 = echarts.init(document.getElementById('e_chart_1'));
		var dataBJ = [
			[55,9,56,0.46,18,6,1],
			[25,11,21,0.65,34,9,2],
			[56,7,63,0.3,14,5,3],
			[33,7,29,0.33,16,6,4],
			[42,24,44,0.76,40,16,5],
			[82,58,90,1.77,68,33,6],
			[74,49,77,1.46,48,27,7],
			[78,55,80,1.29,59,29,8],
			[267,216,280,4.8,108,64,9],
			[185,127,216,2.52,61,27,10],
			[39,19,38,0.57,31,15,11],
			[41,11,40,0.43,21,7,12],
			[64,38,74,1.04,46,22,13],
			[108,79,120,1.7,75,41,14],
			[108,63,116,1.48,44,26,15],
			[33,6,29,0.34,13,5,16],
			[94,66,110,1.54,62,31,17],
			[186,142,192,3.88,93,79,18],
			[57,31,54,0.96,32,14,19],
			[22,8,17,0.48,23,10,20],
			[39,15,36,0.61,29,13,21],
			[94,69,114,2.08,73,39,22],
			[99,73,110,2.43,76,48,23],
			[31,12,30,0.5,32,16,24],
			[42,27,43,1,53,22,25],
			[154,117,157,3.05,92,58,26],
			[234,185,230,4.09,123,69,27],
			[160,120,186,2.77,91,50,28],
			[134,96,165,2.76,83,41,29],
			[52,24,60,1.03,50,21,30],
			[46,5,49,0.28,10,6,31]
		];

		var dataGZ = [
			[26,37,27,1.163,27,13,1],
			[85,62,71,1.195,60,8,2],
			[78,38,74,1.363,37,7,3],
			[21,21,36,0.634,40,9,4],
			[41,42,46,0.915,81,13,5],
			[56,52,69,1.067,92,16,6],
			[64,30,28,0.924,51,2,7],
			[55,48,74,1.236,75,26,8],
			[76,85,113,1.237,114,27,9],
			[91,81,104,1.041,56,40,10],
			[84,39,60,0.964,25,11,11],
			[64,51,101,0.862,58,23,12],
			[70,69,120,1.198,65,36,13],
			[77,105,178,2.549,64,16,14],
			[109,68,87,0.996,74,29,15],
			[73,68,97,0.905,51,34,16],
			[54,27,47,0.592,53,12,17],
			[51,61,97,0.811,65,19,18],
			[91,71,121,1.374,43,18,19],
			[73,102,182,2.787,44,19,20],
			[73,50,76,0.717,31,20,21],
			[84,94,140,2.238,68,18,22],
			[93,77,104,1.165,53,7,23],
			[99,130,227,3.97,55,15,24],
			[146,84,139,1.094,40,17,25],
			[113,108,137,1.481,48,15,26],
			[81,48,62,1.619,26,3,27],
			[56,48,68,1.336,37,9,28],
			[82,92,174,3.29,0,13,29],
			[106,116,188,3.628,101,16,30],
			[118,50,0,1.383,76,11,31]
		];

		var dataSH = [
			[91,45,125,0.82,34,23,1],
			[65,27,78,0.86,45,29,2],
			[83,60,84,1.09,73,27,3],
			[109,81,121,1.28,68,51,4],
			[106,77,114,1.07,55,51,5],
			[109,81,121,1.28,68,51,6],
			[106,77,114,1.07,55,51,7],
			[89,65,78,0.86,51,26,8],
			[53,33,47,0.64,50,17,9],
			[80,55,80,1.01,75,24,10],
			[117,81,124,1.03,45,24,11],
			[99,71,142,1.1,62,42,12],
			[95,69,130,1.28,74,50,13],
			[116,87,131,1.47,84,40,14],
			[108,80,121,1.3,85,37,15],
			[134,83,167,1.16,57,43,16],
			[79,43,107,1.05,59,37,17],
			[71,46,89,0.86,64,25,18],
			[97,71,113,1.17,88,31,19],
			[84,57,91,0.85,55,31,20],
			[87,63,101,0.9,56,41,21],
			[104,77,119,1.09,73,48,22],
			[87,62,100,1,72,28,23],
			[168,128,172,1.49,97,56,24],
			[65,45,51,0.74,39,17,25],
			[39,24,38,0.61,47,17,26],
			[39,24,39,0.59,50,19,27],
			[93,68,96,1.05,79,29,28],
			[188,143,197,1.66,99,51,29],
			[174,131,174,1.55,108,50,30],
			[187,143,201,1.39,89,53,31]
		];

		var lineStyle = {
			normal: {
				wiDth: 1,
				opacity: 0.5
			}
		};
		var option = {
			tooltip: {},
			visualMap: {
				color: ['#635bd6 ', '#DCEDC8']
			},
			splitArea: {
				areaStyle: {
					color: '#DCEDC8',
					opacity: 0,
				}
			},
			radar: {
			   indicator : [
				   { text: 'IE8-', max: 400},
				   { text: 'IE9+', max: 400},
				   { text: 'Safari', max: 400},
				   { text: 'Firefox', max: 400},
				   { text: 'Chrome', max: 400}
				],
				name: {
					textStyle: {
						color: '#878787',
						fontFamily: "'Montserrat', sans-serif",
						fontSize: 12
					}
				},
				splitLine: {
					lineStyle: {
						color: [
							'rgba(33, 33, 33, 0.1)', 'rgba(33, 33, 33, 0.1)',
							'rgba(33, 33, 33, 0.1)', 'rgba(33, 33, 33, 0.1)',
							'rgba(33, 33, 33, 0.1)', 'rgba(33, 33, 33, 0.1)'
						].reverse()
					}
				},
			},
			series : (function (){
				var series = [];
				for (var i = 1; i <= 28; i++) {
					series.push({
						name: i + 2000,
						type: 'radar',
						symbol: 'none',
						itemStyle: {
							normal: {
								lineStyle: {
								  width:1
								}
							},
							emphasis : {
								areaStyle: {color:'rgba(0,250,0,0.3)'}
							}
						},
						data:[
						  {
							value:[
								(40 - i) * 10,
								(38 - i) * 4 + 60,
								i * 5 + 10,
								i * 9,
								i * i /2
							],
							name:i + 2000
						  }
						]
					});
				}
				return series;
			})()
		};
		eChart_1.setOption(option);
		eChart_1.resize();
	}
	if( $('#e_chart_2').length > 0 ){
		var eChart_2 = echarts.init(document.getElementById('e_chart_2'));
		var data = [];
		var labelData = [];
		for (var i = 0; i < 27; ++i) {
			data.push({
				value: Math.random() * 70 + 10 - Math.abs(i - 12),
				name: i + ':00'
			});
			labelData.push({
				value: 1,
				name: i + ':00'
			});
		}

		var option1 = {
			color: ['#635bd6'],
			tooltip: {
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
			series: [{
				type: 'pie',
				data: data,
				roseType: 'area',
				itemStyle: {
					normal: {
						color: 'transparent',
						borderColor: '#635bd6'
					}
				},
				labelLine: {
					normal: {
						show: false
					}
				},
				label: {
					normal: {
						show: false
					}
				}
			}, {
				type: 'pie',
				data: labelData,
				radius: ['75%', '65%'],
				zlevel: -2,
				itemStyle: {
					normal: {
						color: '#635bd6',
						borderColor: 'white'
					}
				},
				label: {
					normal: {
						show:false
					}
				}
			}]
		};

		
		eChart_2.setOption(option1);
		eChart_2.resize();
	}
	if( $('#e_chart_3').length > 0 ){
		var eChart_3 = echarts.init(document.getElementById('e_chart_3'));
		var colors = ['#635bd6 ', '#f742aa', '#635bd6', '#f742aa', '#635bd6', '#f742aa', '#635bd6', '#635bd6', '#f742aa', '#635bd6', '#635bd6', '#635bd6', '#f742aa', '#635bd6'];
		var aCategorys = ['', '1', '2', ' 3', '4', '5', '6', '7', '8','9','10'];
		var topdata = [0,96, 96, 97, 95, 98, 94, 89, 94, 80];//峰值data
		var aSeries = [{
			name: 'abc',
			type: 'line',
			symbol:'circle',
			symbolSize:2,
			itemStyle: {
				normal: {
					color: '#635bd6',
					borderColor:"#ffffff",
					borderWidth:2
				}
			},
			lineStyle: {
				normal: {
					opacity: 0
				}
			},
			
			data: ['-',96, 96, 97, 95, 98, 75, 94,90,89,'-']
		}, {
			name: 'def',
			type: 'line',
			symbol:'circle',
			symbolSize:5,
			lineStyle: {
				normal: {
					opacity: 0
				}
			},
			itemStyle: {
				normal: {
					color: '#f742aa',
					borderColor:"#ffffff",
					borderWidth:2
				}
			},
			
			data: ['-',77, 89, 79, 70, 69, 90, 84,70,69,'-']
		}];
		aCategorys.forEach(function(v, i, a) {
			var name = v;
			if (v !== '') {
				var data = [];
				for (var j = 0; j < aCategorys.length; j++) {
					data.push('-');
				}
				data[i - 1] = 0;
				data[i] = topdata[i];
				data[i + 1] = 0;
				aSeries.push({
					name: name,
					type: 'pictorialBar',
					smooth: false,
					legendHoverLink:false,
					symbol: 'path://M0,10 L10,10 C5.5,10 5.5,5 5,0 C4.5,5 4.5,10 0,10 z',
					barCategoryGap: '-130%',
					label:{
						normal:{
							show:false
						},
						emphasis:{
							show:false
						}
					},
					areaStyle: {
						normal: {
							color: {
								type: 'linear',
								x: 0,
								y: 0,
								x2: 0,
								y2: 1,
								colorStops: [{
									offset: 0,
									color: colors[i - 1] // 0% 处的颜色
								}, {
									offset: 1,
									color: colors[i - 1] // 100% 处的颜色
								}],
								globalCoord: false // 缺省为 false
							}
						}
					},
					data: data,
				});
			}
		});
		var option3 = {
			color: colors,
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
			
			xAxis: [{
				type: 'category',
				boundaryGap: false,
				data: aCategorys,
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
			}],
			grid: {
				left: '3%',
				right: '4%',
				bottom: '3%',
				containLabel: true
			},
			yAxis: [{
				type: 'value',
				boundaryGap: false,
				data: aCategorys,
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
			}],
			series: aSeries
		};
		eChart_3.setOption(option3);
		eChart_3.resize();
	}
	if( $('#e_chart_4').length > 0 ){
		var eChart_4 = echarts.init(document.getElementById('e_chart_4'));
		function detectionData(str) {
		var color = '#f742aa';
		if (str >= 30 && str <= 60) {
			color = '#f742aa';
		} else if (str > 60) {
			color = '#f742aa';
		}
		return color;
		}
		var option4 = {
			"tooltip": {
				"formatter": "{a} <br/>{b} : {c}%"
			},
			"series": [{
				"name": "traffic",
				"type": "gauge",
				"splitNumber": 5,
				"axisLine": {
					"lineStyle": {
						"color": [
							[0.31, "#f4f4f4"],
							[1, "#f4f4f4"]
						],
						"width": 6
					}
				},
				"axisTick": {
					"lineStyle": {
						"color": '#878787', 
						"width": 0
					},
					"length": -25,
					"splitNumber": 1
				},
				"axisLabel": {
					"distance": -80,
					"textStyle": {
						"color": "#878787"
					}
				},
				"splitLine": {
					"show": false
				},
				"itemStyle": {
					"normal": {
						"color": "#635bd6"
					}
				},
				"detail": {
					"formatter": "{value}%",
					"offsetCenter": [0, "60%"],
					"textStyle": {
						"fontSize": 12,
						"color": "#878787"
					}
				},
				"title": {
					"offsetCenter": [0, "100%"]
				},
				"data": [{
					"name": "",
					"value": 31
					
				}]
			}]
		}
		var app = [];
		app.timeTicket = setInterval(function() {
			var value = (Math.random() * 100).toFixed(2) - 0;
			option4.series[0].data[0].value = value;
			option4.series[0].axisLine.lineStyle.color[0][0] = value / 100;
			option4.series[0].axisLine.lineStyle.color[0][1] = detectionData(value);
			eChart_4.setOption(option4, true);
		}, 500);

		eChart_4.setOption(option4);
		eChart_4.resize();
	}	
	if( $('#e_chart_5').length > 0 ){
	var eChart_5 = echarts.init(document.getElementById('e_chart_5'));
	var xData = function(){
    var data = [];
    for(var i=1;i<6;i++){
     data.push(i);   
		}
		return data;
	}();

	var option5 = {
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
		"grid": {
			show:false,
			top: 30,
			bottom: 10,
			containLabel: true,
		}, 
		"legend": {
			"x": "right", 
			"data": [ ]
		}, 
		"calculable": true, 
		"xAxis": [
			{
				type: "category", 
				splitLine: {
					"show": false
				}, 
				axisLine: {
					show:false
				},
				axisLabel: {
					textStyle: {
						color: '#878787'
					}
				},
				axisTick: {
					"show": false
				}, 
				splitArea: {
					"show": false
				}, 
				data: xData,
			}
		], 
		"yAxis": [
			{
				type: "value", 
				splitLine: {
					"show": false
				}, 
				axisLine: {
					show:false
				},
				axisLabel: {
					textStyle: {
						color: '#878787'
					}
				},
				axisTick: {
					"show": false
				}, 
				splitArea: {
					"show": false
				}
			}
		], 
		"series": [
			{
				"name": "male", 
				"type": "bar", 
				"stack": "split", 
				"barMaxWidth": 50, 
				"barGap": "10%", 
				"itemStyle": {
					"normal": {
						"barBorderRadius": 0, 
						"color": '#635bd6', 
						"label": {
							"show": true, 
							"textStyle": {
								"color": "#fff"
							}, 
							"position": "insideTop",
							formatter : function(p) {
								return p.value > 0 ? (p.value ): '';
							}
						}
					}
				}, 
				"data": [
					370, 
					241, 
					755, 
					555, 
					260, 
				], 
			}, 
			{
				"name": "female", 
				"type": "bar", 
				"stack": "split", 
				"itemStyle": {
					"normal": {
						"color": '#f742aa', 
						"barBorderRadius": 0, 
						"label": {
							"show": true, 
							"position": "top",
							formatter : function(p) {
								return p.value > 0 ? ('▼'
										+ p.value + '')
										: '';
							}
						}
					}
				}, 
				"data": [
					386, 
					20, 
					122, 
					261, 
					171, 
				]
			}, 
		]
	}
	eChart_5.setOption(option5);
	eChart_5.resize();
	}	

}
/*****E-Charts function end*****/

/*****Load function start*****/
$(window).on("load",function(){
	window.setTimeout(function(){
		$.toast({
			heading: 'Welcome to Grandin',
			text: 'Use the predefined ones, or specify a custom position object.',
			position: 'top-left',
			loaderBg:'#e3c94b',
			icon: '',
			hideAfter: 3500, 
			stack: 6
		});
	}, 3000);
});
/*****Load function* end*****/

/*****Sparkline function start*****/
var sparklineLogin = function() { 
		if( $('#sparkline_4').length > 0 ){
			$("#sparkline_4").sparkline([2,4,4,6,8,5,6,4,8,6,6,2 ], {
				type: 'line',
				width: '100%',
				height: '45',
				lineColor: '#fff',
				fillColor: 'transparent',
				minSpotColor: '#fff',
				maxSpotColor: '#fff',
				spotColor: '#fff',
				highlightLineColor: 'rgba(0, 0, 0, 0.6)',
				highlightSpotColor: '#fff'
			});
		}	
		if( $('#sparkline_5').length > 0 ){
			$("#sparkline_5").sparkline([0,2,8,6,8], {
				type: 'bar',
				width: '100%',
				height: '45',
				barWidth: '10',
				resize: true,
				barSpacing: '10',
				barColor: '#fff',
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