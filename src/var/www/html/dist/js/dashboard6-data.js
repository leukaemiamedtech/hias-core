/*Dashboard Init*/
 
"use strict"; 

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
			xAxis: {
				data: ['a', 'b', 'c', 'd'],
				axisTick: {show: false},
				axisLine: {
					show:false
				},
				axisLabel: {
					formatter: 'barGap: \'-100%\'',
					textStyle: {
						color: '#878787',
						fontStyle: 'normal',
						fontWeight: 'normal',
						fontFamily: "'Montserrat', sans-serif",
						fontSize: 12
					}
				}
				
			},
			yAxis: {
				splitLine: {show: false},
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
				}
			},
			animationDurationUpdate: 1200,
			series: [{
				type: 'bar',
				itemStyle: {
					normal: {
						color: '#f742aa',
						shadowBlur: 5,
						shadowColor: 'rgba(0, 0, 0, .2)'
					}
				},
				silent: true,
				barWidth: 20,
				barGap: '-100%', // Make series be overlap
				data: [60, 60, 60, 60]
			}, {
				type: 'bar',
				itemStyle: {
					normal: {
						color: '#635bd6',
						shadowBlur: 5,
						shadowColor: 'rgba(0, 0, 0, .5)'
					}
				},
				barWidth: 40,
				z: 10,
				data: [45, 60, 13, 25]
			}]
		};

		var barGaps = ['30%', '-100%'];
		var loopIndex = 0;

		setInterval(function () {
			var barGap = barGaps[loopIndex];

			eChart_1.setOption({
				xAxis: {
					axisLabel: {
						formatter: 'barGap: \'' + barGap + '\''
					}
				},
				series: [{
					barGap: barGap
				}]
			});
			loopIndex = (loopIndex + 1) % barGaps.length;

		}, 2000);
		
		eChart_1.setOption(option);
		eChart_1.resize();
	}
}
/*****E-Charts function end*****/

/*****Sparkline function start*****/
var sparklineLogin = function() { 
	if( $('#sparkline_6').length > 0 ){
		$("#sparkline_6").sparkline([12,4,7,3,8,6,8,5,6,4,8,6,6,2 ], {
			type: 'line',
			width: '100%',
			height: '124',
			lineColor: '#fff',
			fillColor: '#fff',
			minSpotColor: '#fff',
			maxSpotColor: '#fff',
			spotColor: '#fff',
			highlightLineColor: '#fff',
			highlightSpotColor: '#fff'
		});
	}	
	if( $('#sparkline_7').length > 0 ){
		$("#sparkline_7").sparkline([20,4,4], {
			type: 'pie',
			width: '100',
			height: '100',
			sliceColors: ['#635bd6', '#f742aa','#958FEF']
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
	echartResize = setTimeout(echartsConfig, 300);
}).resize(); 
/*****Resize function end*****/

/*****Function Call start*****/
sparklineLogin();
echartsConfig();
/*****Function Call end*****/