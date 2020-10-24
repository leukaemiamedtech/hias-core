<div class="row">
	<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
		<div class="panel panel-default card-view pa-0 bg-gradient3">
			<div class="panel-wrapper collapse in">
				<div class="panel-body pa-0">
					<div class="sm-data-box">
						<div class="container-fluid">
							<div class="row">
								<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
									<span class="txt-light block counter">CPU<br /><span class="up_cpu"><?=$stats["cpu"]; ?></span>%</span>
									<span class="weight-500 uppercase-font block font-13 txt-light">SERVER</span>
								</div>
								<div class="col-xs-6 text-center  txt-light  pl-0 pr-0 pt-25 data-wrap-right">
									<i class="fas fa-microchip  data-right-rep-icon"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
		<div class="panel panel-default card-view pa-0 bg-gradient3">
			<div class="panel-wrapper collapse in">
				<div class="panel-body pa-0">
					<div class="sm-data-box">
						<div class="container-fluid">
							<div class="row">
								<div class="col-xs-6 text-center pl-0 pr-0 txt-light data-wrap-left">
									<span class="block counter">Memory<br /><span class="up_mem"><?=$stats["mem"]; ?></span>%</span>
									<span class="weight-500 uppercase-font block">SERVER</span>
								</div>
								<div class="col-xs-6 text-center  pl-0 pr-0 txt-light data-wrap-right">
									<i class="fas fa-memory data-right-rep-icon"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
		<div class="panel panel-default card-view pa-0 bg-gradient3">
			<div class="panel-wrapper collapse in">
				<div class="panel-body pa-0">
					<div class="sm-data-box">
						<div class="container-fluid">
							<div class="row">
								<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
									<span class="txt-light block counter">HDD<br /><span class="up_hdd"><?=$stats["hdd"]; ?></span>%</span>
									<span class="weight-500 uppercase-font block txt-light">SERVER</span>
								</div>
								<div class="col-xs-6 text-center  pl-0 pr-0  data-wrap-right">
									<i class="far fa-hdd data-right-rep-icon txt-light"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
		<div class="panel panel-default card-view pa-0 bg-gradient3">
			<div class="panel-wrapper collapse in">
				<div class="panel-body pa-0">
					<div class="sm-data-box">
						<div class="container-fluid">
							<div class="row">
								<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
									<span class="txt-light block counter">Temp<br /><span class="up_tempr"><?=$stats["tempr"]; ?></span>°C</span>
									<span class="weight-500 uppercase-font block txt-light">SERVER</span>
								</div>
								<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
									<i class="fa fa-thermometer-quarter data-right-rep-icon txt-light"
										aria-hidden="true"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>