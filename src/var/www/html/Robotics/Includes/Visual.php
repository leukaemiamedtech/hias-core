

				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default card-view">
							<div class="panel-wrapper collapse in">
								<div class="panel-body">

									<div class="row">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="row <?=$dev1Off; ?>" id="cam2">
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<img src="/Robotics/Assets/Images/EMAR-Offline.png" style="width: 100%;" />
												</div>
												<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
													<img src="/Robotics/Assets/Images/EMAR-Offline.png" style="width: 100%;" />
												</div>
											</div>
											<img src="<?=$HIAS->domain; ?>/Robotics/Unit/<?=$robotic["deviceModel"]["value"]; ?>/<?=$robotic["endpoint"]["value"]; ?>/<?=$robotic["streamFile"]["value"]; ?>" id="cam2on" class="<?=$dev1On; ?>" style="width: 100%;" onerror="Robotics.imgError('cam2');" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>