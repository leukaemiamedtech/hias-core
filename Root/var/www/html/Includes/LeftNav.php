
            
            <div class="fixed-sidebar-left">
                <ul class="nav navbar-nav side-nav nicescroll-bar">
                    
					<li>
						<div class="user-profile text-center">
							<img src="<?=$domain; ?>/Team/Media/Images/Uploads/<?=$_SESSION["GeniSysAI"]["Pic"]; ?>" alt="user_auth" class="user-auth-img img-circle"/>
							<div class="dropdown mt-5">
								<a href="#" class="dropdown-toggle pr-0 bg-transparent" data-toggle="dropdown"><?=$_SESSION["GeniSysAI"]["User"]; ?> <span class="caret"></span></a>
								<ul class="dropdown-menu user-auth-dropdown" data-dropdown-in="flipInX" data-dropdown-out="flipOutX">
									<li>
										<a href="profile"><i class="zmdi zmdi-account"></i><span>Profile</span></a>
									</li>
									<li>
										<a href="inbox.html"><i class="zmdi zmdi-email"></i><span>Inbox</span></a>
									</li>
									<li>
										<a href="#"><i class="zmdi zmdi-settings"></i><span>Settings</span></a>
									</li>
									<li class="divider"></li>
									<li class="sub-menu show-on-hover">
										<a href="#" class="dropdown-toggle pr-0 level-2-drp"><i class="zmdi zmdi-check text-success"></i> available</a>
										<ul class="dropdown-menu open-left-side">
											<li>
												<a href="#"><i class="zmdi zmdi-check text-success"></i><span>available</span></a>
											</li>
											<li>
												<a href="#"><i class="zmdi zmdi-circle-o text-warning"></i><span>busy</span></a>
											</li>
											<li>
												<a href="#"><i class="zmdi zmdi-minus-circle-outline text-danger"></i><span>offline</span></a>
											</li>
										</ul>	
									</li>
									<li class="divider"></li>
									<li>
										<a href="<?=$domain; ?>/Logout"><i class="zmdi zmdi-power"></i><span>Log Out</span></a>
									</li>
								</ul>
							</div>
						</div>
					</li>
                    <li class="navigation-header">
                        <span>Navigation</span> 
                        <i class="zmdi zmdi-more"></i>
                    </li>
                    <li>
                        <a href="<?=$domain; ?>/Dashboard" class="<?=$pageDetails["PageID"]=="Dashboard" ? "active" : ""; ?>"><div class="pull-left"><i class="zmdi zmdi-view-dashboard mr-20"></i><span class="right-nav-text">Dashboard</span></div><div class="clearfix"></div></a>
					</li>
					<li><hr class="light-grey-hr mb-10"/></li>
                    <li>
						<a href="javascript:void(0);" data-toggle="collapse" data-target="#server" class="<?=$pageDetails["PageID"]=="Server" ? "active" : ""; ?>"><div class="pull-left"><i class="fa fa-server fa-fw mr-20"></i><span class="right-nav-text">Server</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
						<ul id="server" class="<?=$pageDetails["PageID"]=="Server" ? "" : "collapse"; ?> collapse-level-1">
							<li>
								<a href="<?=$domain; ?>/Server/Control" class="<?=$pageDetails["SubPageID"]=="ServerControl" ? "active" : ""; ?>"><div class="pull-left"><i class="fa fa-server fa-fw mr-20"></i><span class="right-nav-text">Control</span></div><div class="clearfix"></div></a>
							</li>
							<li>
								<a href="<?=$domain; ?>/phpmyadmin" class="" target="_BLANK"><div class="pull-left"><i class="fa fa-database fa-fw mr-20"></i><span class="right-nav-text">Database</span></div><div class="clearfix"></div></a>
							</li>
							<li>
								<a href="javascript:void(0);" data-toggle="collapse" data-target="#dropdown_dr_lv2" class="<?=$pageDetails["SubPageID"]=="IoT" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-wifi fa-fw mr-20"></i></div> IoT<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
								<ul id="dropdown_dr_lv2" class="<?=$pageDetails["SubPageID"]=="IoT" ? "" : "collapse"; ?>  collapse-level-2">
									<li>
										<a href="<?=$domain; ?>/iotJumpWay/" class="<?=$pageDetails["LowPageID"]=="Location" ? "active" : ""; ?>">Location</a>
									</li>
									<li>
										<a href="<?=$domain; ?>/iotJumpWay/Zones" class="<?=$pageDetails["LowPageID"]=="Zones" ? "active" : ""; ?>">Zones</a>
									</li>
									<li>
										<a href="<?=$domain; ?>/iotJumpWay/Devices" class="<?=$pageDetails["LowPageID"]=="Devices" ? "active" : ""; ?>">Devices</a>
									</li>
									<li>
										<a href="<?=$domain; ?>/iotJumpWay/Sensors" class="<?=$pageDetails["LowPageID"]=="Sensors" ? "active" : ""; ?>">Sensors</a>
									</li>
									<li>
										<a href="<?=$domain; ?>/iotJumpWay/Applications" class="<?=$pageDetails["LowPageID"]=="Applications" ? "active" : ""; ?>">Applications</a>
									</li>
								</ul>
							</li>
						</ul>
                    </li>
					<li><hr class="light-grey-hr mb-10"/></li>
                    <li>
						<a href="javascript:void(0);" data-toggle="collapse" data-target="#security" class="<?=$pageDetails["PageID"]=="Security" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-id-card-alt fa-fw mr-20"></i><span class="right-nav-text">Security</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
						<ul id="security" class="<?=$pageDetails["PageID"]=="Security" ? "" : "collapse"; ?> collapse-level-1">
							<li>
								<a href="<?=$domain; ?>/TASS/" class="<?=$pageDetails["SubPageID"]=="TASS" ? "active" : ""; ?>"><div class="pull-left"><i class="fa fa-video-camera fa-fw mr-20"></i><span class="right-nav-text">Cameras</span></div><div class="clearfix"></div></a>
							</li>
						</ul>
                    </li>
					<li><hr class="light-grey-hr mb-10"/></li>
                    <li>
						<a href="javascript:void(0);" data-toggle="collapse" data-target="#hospital" class="<?=$pageDetails["PageID"]=="HIS" ? "active" : ""; ?>"><div class="pull-left"><i class="fa fa-hospital fa-fw mr-20"></i><span class="right-nav-text">HIS</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
						<ul id="hospital" class="<?=$pageDetails["PageID"]=="HIS" ? "" : "collapse"; ?> collapse-level-1">
							<li>
								<a href="javascript:void(0);" data-toggle="collapse" data-target="#staffdd" class="<?=$pageDetails["SubPageID"]=="Staff" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-users fa-fw mr-20"></i></div> Staff<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
								<ul id="staffdd" class="<?=$pageDetails["SubPageID"]=="Staff" ? "" : "collapse"; ?> collapse-level-2">
									<li>
										<a href="<?=$domain; ?>/Hospital/Staff" class="<?=$pageDetails["LowPageID"]=="List" ? "active" : ""; ?>">List</a>
									</li>
									<li>
										<a href="<?=$domain; ?>/Hospital/Staff/Create" class="<?=$pageDetails["LowPageID"]=="Create" ? "active" : ""; ?>">Create</a>
									</li>
								</ul>
							</li>
							<li>
								<a href="javascript:void(0);" data-toggle="collapse" data-target="#patientsdd" class="<?=$pageDetails["SubPageID"]=="Patients" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-hospital-user fa-fw mr-20"></i></div> Patients<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
								<ul id="patientsdd" class="<?=$pageDetails["SubPageID"]=="Patients" ? "" : "collapse"; ?> collapse-level-2">
									<li>
										<a href="<?=$domain; ?>/Hospital/Patients" class="<?=$pageDetails["SubPageID"]=="" ? "active" : ""; ?>">List</a>
									</li>
									<li>
										<a href="<?=$domain; ?>/Hospital/Patients/Create" class="<?=$pageDetails["SubPageID"]=="" ? "active" : ""; ?>">Create</a>
									</li>
								</ul>
							</li>
							<li>
								<a href="javascript:void(0);" data-toggle="collapse" data-target="#bedsdd" class="<?=$pageDetails["SubPageID"]=="Beds" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-bed fa-fw mr-20"></i></div> Beds<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
								<ul id="bedsdd" class="<?=$pageDetails["SubPageID"]=="Beds" ? "" : "collapse"; ?>  collapse-level-2">
									<li>
										<a href="<?=$domain; ?>/Hospital/Beds" class="<?=$pageDetails["SubPageID"]=="" ? "active" : ""; ?>">List</a>
									</li>
									<li>
										<a href="<?=$domain; ?>/Hospital/Beds/Create" class="<?=$pageDetails["SubPageID"]=="" ? "active" : ""; ?>">Create</a>
									</li>
								</ul>
							</li>
						</ul>
                    </li>
					<li><hr class="light-grey-hr mb-10"/></li>
                    <li>
						<a href="javascript:void(0);" data-toggle="collapse" data-target="#robotics" class="<?=$pageDetails["PageID"]=="Robotics" ? "active" : ""; ?>"><div class="pull-left"><i class="fa fa-robot fa-fw mr-20"></i><span class="right-nav-text">Robotics</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
						<ul id="robotics" class="<?=$pageDetails["PageID"]=="Robotics" ? "" : "collapse"; ?> collapse-level-1">
							<li>
								<a href="<?=$domain; ?>/EMAR/" class="<?=$pageDetails["SubPageID"]=="EMAR" ? "active" : ""; ?>"><div class="pull-left"><i class="fa fa-robot fa-fw mr-20"></i><span class="right-nav-text">EMAR</span></div><div class="clearfix"></div></a>
							</li>
						</ul>
                    </li>
					<li><hr class="light-grey-hr mb-10"/></li>
                    <li>
						<a href="javascript:void(0);" data-toggle="collapse" data-target="#diagnosis" class="<?=$pageDetails["PageID"]=="Diagnosis" ? "active" : ""; ?>"><div class="pull-left"><i class="fa fa-hospital fa-fw mr-20"></i><span class="right-nav-text">Diagnosis</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
						<ul id="diagnosis" class="<?=$pageDetails["PageID"]=="Diagnosis" ? "" : "collapse"; ?> collapse-level-1">
							<li>
								<a href="<?=$domain; ?>/Diagnosis/Leukemia" class="<?=$pageDetails["SubPageID"]=="Leukemia" ? "active" : ""; ?>"><div class="pull-left"><i class="fa fa-disease fa-fw mr-20"></i><span class="right-nav-text">Leukemia</span></div><div class="clearfix"></div></a>
							</li>
							<li>
								<a href="<?=$domain; ?>/Diagnosis/Covid19" class="<?=$pageDetails["SubPageID"]=="Covid19" ? "active" : ""; ?>"><div class="pull-left"><i class="fa fa-virus fa-fw mr-20"></i><span class="right-nav-text">COVID-19</span></div><div class="clearfix"></div></a>
							</li>
						</ul>
                    </li>
					<li><hr class="light-grey-hr mb-10"/></li>
                </ul>
            </div>