/*Dashboard3 Init*/
 
"use strict"; 

/*****Ready function start*****/
$(document).ready(function(){
	$('#support_table').DataTable({
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
			position: 'top-left',
			icon: '',
			loaderBg:'#e3c94b',
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
		function varry(a, b, c) {
			var seriesd1 = [{
				value: a,
				name: 'A'
			}, {
				value: b,
				name: 'B'
			}, {
				value: c,
				name: 'C'
			}];
			return seriesd1;
		}
		function seriesvarry(seriesd){
			var seriesdata1 = [

				{
					data: seriesd
				},

				{
					data: seriesd
				}, {
					data: seriesd
				}, {
					data: seriesd
				}
			];
			return seriesdata1;
		}
		var seriesd1 = varry(3, 6, 9);
		var seriesdata1 = seriesvarry(seriesd1);
		var seriesd2 = varry(1, 2, 9);
		var seriesdata2 = seriesvarry(seriesd2);
		var seriesd3 = varry(7, 8, 9);
		var seriesdata3 = seriesvarry(seriesd3);
		var option = {
			baseOption: {
				color: ['#635bd6', '#f742aa', '#958FEF'],
				timeline: {
					axisType: 'category',
					playInterval: '1000',
					show:false,
					autoPlay: 'true',
					data: ['正方形?', '四角星?', '八边形?']
				},

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
				gap: 0,
				series: [
					{
						type: 'funnel',
						width: '30%',
						height: '40%',
						left: '50%',
						top: '40%',
						sort: 'descending',
						funnelAlign: 'left',
						label: {
							normal: {
								show: false
							}
						},
						labelLine: {
							normal: {
								show: false
							}
						}

					},
					{
						type: 'funnel',
						width: '30%',
						height: '40%',
						left: '20%',
						top: '40%',
						sort: 'descending',
						funnelAlign: 'right',
						label: {
							normal: {
								show: false
							}
						},
						labelLine: {
							normal: {
								show: false
							}
						}

					},
					{
						type: 'funnel',
						width: '30%',
						height: '40%',
						left: '20%',
						top: '0%',
						sort: 'ascending',
						funnelAlign: 'right',
						label: {
							normal: {
								show: false
							}
						},
						labelLine: {
							normal: {
								show: false
							}
						}

					}, {
						type: 'funnel',
						width: '30%',
						height: '40%',
						left: '50%',
						top: '0%',
						sort: 'ascending',
						funnelAlign: 'left',
						label: {
							normal: {
								show: false
							}
						},
						labelLine: {
							normal: {
								show: false
							}
						}

					}

				]
			},

			options: [{
					series: seriesdata1
				},
				//seriesdata1
				{
					series: seriesdata2
				},
				//seriesdata2
				{
					series: seriesdata3
				}
				//seriesdata3
			]
		};
		eChart_1.setOption(option);
		eChart_1.resize();
	}
	if( $('#e_chart_2').length > 0 ){
		var eChart_2 = echarts.init(document.getElementById('e_chart_2'));
		var option1 = {
			tooltip : {
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
			color: ['#635bd6', '#f742aa', '#958FEF'],
			series : [
				{
					name: 'task',
					type: 'pie',
					radius : '50%',
					center: ['50%', '50%'],
					roseType : '',
					tooltip : {
						trigger: 'item',
						formatter: "{a} <br/>{b} : {c} ({d}%)",
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
					data:[
						{value:335, name:'task 1'},
						{value:410, name:'task 2'},
						{value:334, name:'task 3'},
					],
					itemStyle: {
						emphasis: {
							shadowBlur: 10,
							shadowOffsetX: 0,
							shadowColor: 'rgba(0, 0, 0, 0.5)'
						}
					}
				}
			]
		};
		eChart_2.setOption(option1);
		eChart_2.resize();
	}
	if( $('#e_chart_3').length > 0 ){
		var eChart_3 = echarts.init(document.getElementById('e_chart_3'));
		var xAxisData = [];
		var data = [];
		var data2 = [];
		for (var i = 0; i < 50; i++) {
			xAxisData.push(i);
			data.push((Math.sin(i / 7) * (i / 5 -10) + i / 6) * 5);
			data2.push((Math.sin(i / 5) * (i / 2 + 10) + i / 6) * 3);
		}

		var option3 = {
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
				show: false,
				data: xAxisData
			}, {
				show: false,
				data: xAxisData
			}],
			visualMap: {
				show: false,
				min: 0,
				max: 50,
				dimension: 0,
				inRange: {
					color: ['#635bd6', '#f742aa', '#635bd6', '#f742aa', '#635bd6', '#f742aa']
				}
			},
			yAxis: {
				axisLine: {
					show: false
				},
				axisLabel: {
					textStyle: {
						color: '#4a657a'
					}  
				},
				splitLine: {
					show: false,
				},
				axisTick: {
					show: false
				}
			},
			series: [{
				name: 'back',
				type: 'line',
				data: data2,
				z: 1,
				itemStyle: {
					normal: {
						opacity: 0.4,
						barBorderRadius: 5,
						shadowBlur: 3,
						shadowColor: '#fff'
					}
				}
			}, {
				name: 'Simulate Shadow',
				type: 'line',
				data: data,
				z: 2,
				showSymbol: false,
				animationDelay: 0,
				animationEasing: 'linear',
				animationDuration: 1200,
				lineStyle: {
					normal: {
						color: 'transparent'
					}  
				},
				areaStyle: {
					normal: {
						shadowBlur: 5,
						shadowOffsetX: 0,
						shadowOffsetY: 5,
						shadowColor: 'rgba(0, 0, 0, 0.5)'
					}
				}
			}, {
				name: 'front',
				type: 'line',
				data: data,
				xAxisIndex: 1,
				z: 3,
				itemStyle: {
					normal: {
						barBorderRadius: 5
					}
				}
			}],
			animationEasing: 'elasticOut',
			animationEasingUpdate: 'elasticOut',
			animationDelay: function (idx) {
				return idx * 20;
			},
			animationDelayUpdate: function (idx) {
				return idx * 20;
			}
		};
		eChart_3.setOption(option3);
		eChart_3.resize();
	}
}
/*****E-Charts function end*****/

/*****Sparkline function start*****/
var sparklineLogin = function() { 
		if( $('#sparkline_4').length > 0 ){
			$("#sparkline_4").sparkline([2,4,4,6,8,5,6,4,8,6,6,2 ], {
				type: 'line',
				width: '100%',
				height: '45',
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
	var sparkResize;
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