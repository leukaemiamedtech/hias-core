/*Dashboard Init*/
 
"use strict"; 

/*****Ready function start*****/
$(document).ready(function(){
	$('#statement').DataTable({
		"bFilter": false,
		"bLengthChange": false,
		"bPaginate": false,
		"bInfo": false,
	});
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
		var myData = ['a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7'];
		var databeast = {
			2006: [389, 259, 262, 324, 232, 176, 196],
			2007: [111, 315, 139, 375, 204, 352, 163],
			2008: [227, 210, 328, 292, 241, 110, 130],
			2009: [100, 350, 300, 250, 200, 150, 100],
			2010: [280, 128, 255, 254, 313, 143, 360],
			2011: [121, 388, 233, 309, 133, 308, 297],
			2012: [200, 350, 300, 250, 200, 150, 100],
			2013: [380, 129, 173, 101, 310, 393, 386],
			2014: [363, 396, 388, 108, 325, 120, 180],
			2015: [300, 350, 300, 250, 200, 150, 100],
		};
		var databeauty = {
			2006: [121, 388, 233, 309, 133, 308, 297],
			2007: [200, 350, 300, 250, 200, 150, 100],
			2008: [380, 129, 173, 101, 310, 393, 386],
			2009: [363, 396, 388, 108, 325, 120, 180],
			2010: [300, 350, 300, 250, 200, 150, 100],
			2011: [100, 350, 300, 250, 200, 150, 100],
			2012: [280, 128, 255, 254, 313, 143, 360],
			2013: [389, 259, 262, 324, 232, 176, 196],
			2014: [111, 315, 139, 375, 204, 352, 163],
			2015: [227, 210, 328, 292, 241, 110, 130],
		};
		var timeLineData = [2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2014, 2015];
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
						type: 'line',
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
		eChart_1.setOption(option);
		eChart_1.resize();
	}
	if( $('#e_chart_2').length > 0 ){
		var eChart_2 = echarts.init(document.getElementById('e_chart_2'));
		var option1 = {
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
			yAxis: {
				type: 'value',
				axisTick: {
					show: false
				},
				axisLine: {
					show: false,
					lineStyle: {
						color: '#878787',
					}
				},
				splitLine: {
					show: false,
				},
			},
			xAxis: [{
					type: 'category',
					axisTick: {
						show: false
					},
					axisLine: {
						show: true,
						lineStyle: {
							color: '#878787',
						}
					},
					data: ['Dt1', 'Dt2', 'Dt3']
				}, {
					type: 'category',
					axisLine: {
						show: false
					},
					axisTick: {
						show: false
					},
					axisLabel: {
						show: false
					},
					splitArea: {
						show: false
					},
					splitLine: {
						show: false
					},
					data: ['Dt1', 'Dt2', 'Dt3']
				},

			],
			series: [{
					name: 'Appoinment1',
					type: 'bar',
					xAxisIndex: 1,

					itemStyle: {
						normal: {
							show: true,
							color: '#958FEF',
							barBorderRadius: 50,
							borderWidth: 0,
							borderColor: '#878787',
						}
					},
					barWidth: '20%',
					data: [1000, 1000, 1000]
				}, {
					name: 'Appoinment2',
					type: 'bar',
					xAxisIndex: 1,

					itemStyle: {
						normal: {
							show: true,
							color: '#958FEF',
							barBorderRadius: 50,
							borderWidth: 0,
							borderColor: '#fff',
						}
					},
					barWidth: '20%',
					barGap: '100%',
					data: [1000, 1000, 1000]
				}, {
					name: 'Appoinment3',
					type: 'bar',
					itemStyle: {
						normal: {
							show: true,
							color: '#958FEF',
							barBorderRadius: 50,
							borderWidth: 0,
							borderColor: '#878787',
						}
					},
					label: {
						normal: {
							show: true,
							position: 'top',
							textStyle: {
								color: '#fff'
							}
						}
					},
					barWidth: '20%',
					data: [398, 419, 452]
				}, {
					name: 'Appoinment4',
					type: 'bar',
					barWidth: '20%',
					itemStyle: {
						normal: {
							show: true,
							color: '#f742aa',
							barBorderRadius: 50,
							borderWidth: 0,
							borderColor: '#878787',
						}
					},
					label: {
						normal: {
							show: true,
							position: 'top',
							textStyle: {
								color: '#fff'
							}
						}
					},
					barGap: '100%',
					data: [425, 437, 484]
				}

			]
		};
		
		eChart_2.setOption(option1);
		eChart_2.resize();
	}
	if( $('#e_chart_3').length > 0 ){
		var eChart_3 = echarts.init(document.getElementById('e_chart_3'));
		var option3 = {
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
			series: [
				{
					name:'',
					type:'pie',
					radius: ['52%', '55%'],
					color: ['#958FEF', '#635bd6'],
					label: {
						normal: {
							formatter: '{b}\n{d}%'
						},
				  
					},
					data:[
						{value:435, name:''},
						{value:679, name:''},
					]
				}
			]
		};
		eChart_3.setOption(option3);
		eChart_3.resize();
	}
}
/*****E-Charts function end*****/

/*****Sparkline function start*****/
var sparklineLogin = function() { 
	if( $('#sparkline_1').length > 0 ){
		$("#sparkline_1").sparkline([2,4,4,6,8,5,6,4,8,6,6,2 ], {
			type: 'line',
			width: '100%',
			height: '35',
			lineColor: '#fff',
			fillColor: 'transparent',
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