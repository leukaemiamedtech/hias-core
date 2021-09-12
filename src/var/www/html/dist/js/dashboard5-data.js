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
			loaderBg:'#e58b25',
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
			xAxis: {
				type: 'time',
				boundaryGap: false,
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
			},
			yAxis: {
				data: ['1', '2', '3'],
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
				boundaryGap: [0, '100%']
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
			series: [
				{
					name: 'ab1',
					type: 'bar',
					stack: '1',
					barWidth: 20,
					itemStyle: {
						normal: {
							color: '#f742aa',
							barBorderRadius: 50,
							shadowBlur: 5,
							shadowColor: 'rgba(0, 0, 0, .5)'
						},
					},
					data: [
						new Date("2015/09/2"),
						new Date("2015/09/8"),
						new Date("2015/09/18"),
					]
				},{
					name: 'ab2',
					type: 'bar',
					stack: '1',
					itemStyle: {
						normal: {
							color: '#f742aa',
							barBorderRadius: 50,
							barWidth: 30,
							shadowBlur: 5,
							shadowColor: 'rgba(0, 0, 0, .5)'
						}
					},
					data: [
						new Date("2015/09/19"),
						new Date("2015/09/29"),
						new Date("2015/09/28"),
					]
				}
			]
		};
		eChart_1.setOption(option);
		eChart_1.resize();
	}
	if( $('#e_chart_2').length > 0 ){
		var eChart_2 = echarts.init(document.getElementById('e_chart_2'));
		var data = [];

		for (var i = 0; i <= 100; i++) {
			var theta = i / 100 * 30;
			var r = 5 * (1 + Math.sin(theta / 180 * Math.PI));
			data.push([r, theta]);
		}

		var option1 = {
			polar: {},
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
				}
			},
			angleAxis: {
				type: 'value',
				startAngle: 0,
				axisLine: {
					lineStyle: {
						color: 'rgba(33, 33, 33, 0.1)'
					},
					textStyle: {
						color: '#878787',
						fontSize: 12,
						fontFamily: "'Montserrat', sans-serif",
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
						color: 'rgba(33, 33, 33, 0.1)'
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
				data: data,
				itemStyle: {
					normal: {
						color: '#635bd6',
					}
				},
			}]
		};
		eChart_2.setOption(option1);
		eChart_2.resize();
	}
	if( $('#e_chart_3').length > 0 ){
		var eChart_3 = echarts.init(document.getElementById('e_chart_3'));
		var option3 = {
			  series: [{
				type: 'liquidFill',
				data: [0.5, 0.4],
				radius: '100%',
				shape: 'circle',
				color: ['#958FEF','#f742aa'],
				backgroundStyle: {
					borderWidth: 0,
					color: 'rgba(255,255,255,0)',
					shadowBlur: 0
				},
				itemStyle: {
					normal: {
						shadowBlur: 5,
						shadowColor: 'rgba(0, 0, 0, .5)'
					}
				},
				outline: {
					borderDistance: 1,
					itemStyle: {
						borderWidth: 0,
						borderColor: '#fff',
						shadowBlur: 0,
					}
				},
				label: {
					normal: {
						fontSize: 20
					}
				}
			}]
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
			lineColor: '#635bd6',
			fillColor: '#635bd6',
			minSpotColor: '#635bd6',
			maxSpotColor: '#635bd6',
			spotColor: '#635bd6',
			highlightLineColor: '#635bd6',
			highlightSpotColor: '#635bd6'
		});
	}	
	if( $('#sparkline_2').length > 0 ){
		$("#sparkline_2").sparkline([0,2,8,6,8], {
			type: 'line',
			width: '100%',
			height: '35',
			lineColor: '#635bd6',
			fillColor: '#635bd6',
			minSpotColor: '#635bd6',
			maxSpotColor: '#635bd6',
			spotColor: '#635bd6',
			highlightLineColor: '#635bd6',
			highlightSpotColor: '#635bd6'
		});
	}	
	if( $('#sparkline_3').length > 0 ){
		$("#sparkline_3").sparkline([0, 23, 43, 35, 44, 45, 56, 37, 40, 45, 56, 7, 10], {
			type: 'line',
			width: '100%',
			height: '35',
			lineColor: '#635bd6',
			fillColor: '#635bd6',
			minSpotColor: '#635bd6',
			maxSpotColor: '#635bd6',
			spotColor: '#635bd6',
			highlightLineColor: '#635bd6',
			highlightSpotColor: '#635bd6'
		});
	}
	if( $('#sparkline_4').length > 0 ){
		$("#sparkline_4").sparkline([0,2,8,6,8,5,6,4,8,6,6,2 ], {
			type: 'bar',
			width: '100%',
			height: '50',
			barWidth: '5',
			barSpacing: '5',
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