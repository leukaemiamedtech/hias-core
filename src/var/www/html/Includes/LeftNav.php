

			<div class="fixed-sidebar-left">
				<ul class="nav navbar-nav side-nav nicescroll-bar">

					<li>
						<div class="user-profile text-center">
							<img src="/Users/Staff/Media/Images/Uploads/<?=$_SESSION["HIAS"]["Pic"]; ?>" alt="user_auth" class="user-auth-img img-circle"/>
							<div class="dropdown mt-5">
								<a href="#" class="dropdown-toggle pr-0 bg-transparent" data-toggle="dropdown"><?=$_SESSION["HIAS"]["User"]; ?> <span class="caret"></span></a>
								<ul class="dropdown-menu user-auth-dropdown" data-dropdown-in="flipInX" data-dropdown-out="flipOutX">
									<li>
										<a href="/Users/Staff/<?=$_SESSION["HIAS"]["Uid"]; ?>"><i class="zmdi zmdi-settings"></i><span>Settings</span></a>
									</li>
									<li class="divider"></li>
									<li>
										<a href="/Logout"><i class="zmdi zmdi-power"></i><span>Log Out</span></a>
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
						<a href="/Dashboard" class="<?=$pageDetails["PageID"]=="Dashboard" ? "active" : ""; ?>"><div class="pull-left"><i class="zmdi zmdi-view-dashboard mr-20"></i><span class="right-nav-text">Dashboard</span></div><div class="clearfix"></div></a>
					</li>
					<?php if($_SESSION["HIAS"]["Admin"]): ?>
					<li><hr class="light-grey-hr mb-10"/></li>
					<li>
						<a href="javascript:void(0);" data-toggle="collapse" data-target="#server" class="<?=$pageDetails["PageID"]=="Server" ? "active" : ""; ?>"><div class="pull-left"><i class="fa fa-server fa-fw mr-20"></i><span class="right-nav-text">Server</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
						<ul id="server" class="<?=$pageDetails["PageID"]=="Server" ? "" : "collapse"; ?> collapse-level-1">
							<li>
								<a href="/Server/Settings" class="<?=isSet($pageDetails["SubPageID"]) &&  $pageDetails["SubPageID"]=="Settings" ? "active" : ""; ?>"><div class="pull-left"><i class="fa fa-cogs fa-fw mr-20"></i><span class="right-nav-text">Settings</span></div><div class="clearfix"></div></a>
							</li>
							<li><hr class="light-grey-hr mb-10"/></li>
							<li>
								<a href="/phpldapadmin" class="" target="_BLANK"><div class="pull-left"><i class="fas fa-id-card fa-fw mr-20"></i><span class="right-nav-text">LDAP</span></div><div class="clearfix"></div></a>
							</li>
							<li><hr class="light-grey-hr mb-10"/></li>
							<li>
								<a href="/phpmyadmin" class="" target="_BLANK"><div class="pull-left"><i class="fa fa-database fa-fw mr-20"></i><span class="right-nav-text">MySQL</span></div><div class="clearfix"></div></a>
							</li>
							<li><hr class="light-grey-hr mb-10"/></li>
							<li>
								<a href="https://cloud.mongodb.com/freemonitoring/cluster/4QQF5YWTEOIHPWOUWQYDYLGQCCMAWSHJ" class="" target="_BLANK"><div class="pull-left"><i class="fa fa-database fa-fw mr-20"></i><span class="right-nav-text">Mongo</span></div><div class="clearfix"></div></a>
							</li>
							<li><hr class="light-grey-hr mb-10"/></li>
						</ul>
					</li>
					<li><hr class="light-grey-hr mb-10"/></li>
					<li>
						<a href="javascript:void(0);" data-toggle="collapse" data-target="#blockchain" class="<?=$pageDetails["PageID"]=="HIASBCH" ? "active" : ""; ?>"><div class="pull-left"><i class="fab fa-ethereum fa-fw  mr-20"></i><span class="right-nav-text">HIASBCH</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
						<ul id="blockchain" class="<?=$pageDetails["PageID"]=="HIASBCH" ? "" : "collapse"; ?> collapse-level-1">
							<li>
								<a href="/HIASBCH/" class="<?=isSet($pageDetails["SubPageID"]) && $pageDetails["SubPageID"]=="Settings" ? "active" : ""; ?>"><div class="pull-left"><i class="fa fa-th-large fa-fw mr-20"></i></div> Dashboard<div class="pull-right"></div><div class="clearfix"></div></a>
							</li>
							<li>
								<a href="/HIASBCH/Settings" class="<?=isSet($pageDetails["SubPageID"]) && $pageDetails["SubPageID"]=="Settings" ? "active" : ""; ?>"><div class="pull-left"><i class="fa fa-cogs fa-fw mr-20"></i></div> Configuration<div class="pull-right"></div><div class="clearfix"></div></a>
							</li>
							<li>
								<a href="/HIASBCH/Entity" class="<?=isSet($pageDetails["SubPageID"]) && $pageDetails["SubPageID"]=="Entity" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-cogs fa-fw mr-20"></i></div> Entity<div class="pull-right"></div><div class="clearfix"></div></a>
							</li>
							<li>
								<a href="/HIASBCH/Contracts" class="<?=isSet($pageDetails["SubPageID"]) && $pageDetails["SubPageID"]=="Contracts" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-file-contract fa-fw mr-20"></i></div> Contracts<div class="pull-right"></div><div class="clearfix"></div></a>
							</li>
							<li>
								<a href="/HIASBCH/Transfer" class="<?=isSet($pageDetails["SubPageID"]) &&  $pageDetails["SubPageID"]=="Transfer" ? "active" : ""; ?>"><div class="pull-left"><i class="fa fa-paper-plane fa-fw mr-20"></i></div> Transfer<div class="pull-right"></div><div class="clearfix"></div></a>
							</li>
							<li>
								<a href="/HIASBCH/Console" class="<?=isSet($pageDetails["SubPageID"]) && $pageDetails["SubPageID"]=="Console" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-terminal fa-fw mr-20"></i></div> Console<div class="pull-right"></div><div class="clearfix"></div></a>
							</li>
						</ul>
					</li>
					<li><hr class="light-grey-hr mb-10"/></li>
					<li>
						<a href="javascript:void(0);" data-toggle="collapse" data-target="#hiascdi" class="<?=$pageDetails["PageID"]=="HIASCDI" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-info-circle fa-fw mr-20"></i></div> HIASCDI<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
						<ul id="hiascdi" class="<?=$pageDetails["PageID"]=="HIASCDI" ? "" : "collapse"; ?> collapse-level-2">
							<li>
								<a href="/HIASCDI/" class="<?=isSet($pageDetails["SubPageID"]) && $pageDetails["SubPageID"]=="Settings" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-cogs fa-fw mr-20"></i></div> Configuration<div class="pull-right"></div><div class="clearfix"></div></a>
							</li>
							<li>
								<a href="/HIASCDI/Entity" class="<?=isSet($pageDetails["SubPageID"]) && $pageDetails["SubPageID"]=="Entity" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-cogs fa-fw mr-20"></i></div> Entity<div class="pull-right"></div><div class="clearfix"></div></a>
							</li>
							<li>
								<a href="/HIASCDI/Console" class="<?=isSet($pageDetails["SubPageID"]) && $pageDetails["SubPageID"]=="Console" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-terminal fa-fw mr-20"></i></div> Console<div class="pull-right"></div><div class="clearfix"></div></a>
							</li>
						</ul>
					</li>
					<li><hr class="light-grey-hr mb-10"/></li>
					<li>
						<a href="javascript:void(0);" data-toggle="collapse" data-target="#hiashdi" class="<?=$pageDetails["PageID"]=="HIASHDI" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-archive fa-fw mr-20"></i></div> HIASHDI<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
						<ul id="hiashdi" class="<?=$pageDetails["PageID"]=="HIASHDI" ? "" : "collapse"; ?> collapse-level-2">
							<li>
								<a href="/HIASHDI/" class="<?=isSet($pageDetails["SubPageID"]) && $pageDetails["SubPageID"]=="HIASHDISettings" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-cogs fa-fw mr-20"></i></div> Configuration<div class="pull-right"></div><div class="clearfix"></div></a>
							</li>
							<li>
								<a href="/HIASHDI/Entity" class="<?=isSet($pageDetails["SubPageID"]) && $pageDetails["SubPageID"]=="HIASHDIEntity" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-cogs fa-fw mr-20"></i></div> Entity<div class="pull-right"></div><div class="clearfix"></div></a>
							</li>
							<li>
								<a href="/HIASHDI/Console" class="<?=isSet($pageDetails["SubPageID"]) && $pageDetails["SubPageID"]=="HIASHDIConsole" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-terminal fa-fw mr-20"></i></div> Console<div class="pull-right"></div><div class="clearfix"></div></a>
							</li>
						</ul>
					</li>
					<?php endif; ?>
					<li><hr class="light-grey-hr mb-10"/></li>
					<li>
						<a href="javascript:void(0);" data-toggle="collapse" data-target="#dropdown_dr_lv2" class="<?=$pageDetails["PageID"]=="IoT" ? "active" : ""; ?>"><div class="pull-left"><i class="fas zmdi zmdi-memory mr-20"></i></div> iotJumpWay<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
						<ul id="dropdown_dr_lv2" class="<?=$pageDetails["PageID"]=="IoT" ? "" : "collapse"; ?>  collapse-level-2">
							<li>
								<a href="javascript:void(0);" data-toggle="collapse" data-target="#hiasiot" class="<?=$pageDetails["PageID"]=="Brokers" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-network-wired fa-fw mr-20"></i></div> Brokers<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
								<ul id="hiasiot" class="<?=$pageDetails["PageID"]=="Brokers" ? "" : "collapse"; ?> collapse-level-2">
									<li>
										<a href="<?=$HIAS->domain; ?>:15671" class="" target="_BLANK"><div class="pull-left"><i class="fas fa-network-wired fa-fw mr-20"></i><span class="right-nav-text">AMQP</span></div><div class="clearfix"></div></a>
									</li>
								</ul>
							</li>
							<li>
								<a href="javascript:void(0);" data-toggle="collapse" data-target="#entities" class="<?=isSet($pageDetails["SubPageID"]) &&  $pageDetails["SubPageID"]=="Entities" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-cube fa-fw mr-20"></i></div> Entities<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
								<ul id="entities" class="<?=isSet($pageDetails["SubPageID"]) &&  $pageDetails["SubPageID"]=="Entities" ? "" : "collapse"; ?> collapse-level-2">
									<li>
										<a href="/iotJumpWay/" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Location" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-map-marker-alt fa-fw mr-20"></i></div> Location<div class="clearfix"></div></a>
									</li>
									<li>
										<a href="/iotJumpWay/Zones" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Zones" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-network-wired fa-fw mr-20"></i></div> Zones<div class="clearfix"></div></a>
									</li>
									<li>
										<a href="/iotJumpWay/Agents" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Agents" ? "active" : ""; ?>"><div class="pull-left"><i class="zmdi zmdi-memory fa-fw mr-20"></i></div> Agents<div class="pull-right"></div><div class="clearfix"></div></a>
									</li>
									<li>
										<a href="/iotJumpWay/Applications" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Applications" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-mobile-alt fa-fw mr-20"></i></div> Applications<div class="clearfix"></div></a>
									</li>
									<li>
										<a href="/iotJumpWay/Devices" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Devices" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-wifi fa-fw mr-20"></i></div> Devices<div class="clearfix"></div></a>
									</li>
									<li>
										<a href="/iotJumpWay/Things" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Things" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-upload fa-fw mr-20"></i></div> Things<div class="clearfix"></div></a>
									</li>
								</ul>
							</li>
							<li>
								<a href="javascript:void(0);" data-toggle="collapse" data-target="#datas" class="<?=isSet($pageDetails["SubPageID"]) &&  $pageDetails["SubPageID"]=="Data" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-chart-area fa-fw mr-20"></i></div> Data<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
								<ul id="datas" class="<?=isSet($pageDetails["SubPageID"]) &&  $pageDetails["SubPageID"]=="Data" ? "" : "collapse"; ?> collapse-level-2">
									<li>
										<a href="/iotJumpWay/Data" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Overview" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-chart-area fa-fw mr-20"></i></div> Overview<div class="clearfix"></div></a>
									</li>
									<li>
										<a href="/iotJumpWay/Data/Statuses" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Statuses" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-chart-area fa-fw mr-20"></i></div> Statuses<div class="clearfix"></div></a>
									</li>
									<li>
										<a href="/iotJumpWay/Data/Life" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Life" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-chart-area fa-fw mr-20"></i></div> Life<div class="clearfix"></div></a>
									</li>
									<li>
										<a href="/iotJumpWay/Data/Sensors" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Sensors" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-chart-area fa-fw mr-20"></i></div> Sensors<div class="clearfix"></div></a>
									</li>
									<li>
										<a href="/iotJumpWay/Data/Actuators" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Actuators" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-chart-area fa-fw mr-20"></i></div> Actuators<div class="clearfix"></div></a>
									</li>
									<li>
										<a href="/iotJumpWay/Data/Commands" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Commands" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-chart-area fa-fw mr-20"></i></div> Commands<div class="clearfix"></div></a>
									</li>
								</ul>
							</li>
							<li>
								<a href="/iotJumpWay/Console" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Console" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-terminal fa-fw mr-20"></i></div> Console<div class="clearfix"></div></a>
							</li>
						</ul>
					</li>
					<li><hr class="light-grey-hr mb-10"/></li>
					<li>
						<a href="javascript:void(0);" data-toggle="collapse" data-target="#ai" class="<?=$pageDetails["PageID"]=="AI" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-brain fa-fw mr-20"></i></div> AI<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
						<ul id="ai" class="<?=$pageDetails["PageID"]=="AI" ? "" : "collapse"; ?>  collapse-level-2">
							<li>
								<a href="javascript:void(0);" data-toggle="collapse" data-target="#models" class="<?=$pageDetails["SubPageID"]=="Models" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-brain fa-fw mr-20"></i></div> Models<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
								<ul id="models" class="<?=isSet($pageDetails["SubPageID"]) &&  $pageDetails["SubPageID"]=="Models" ? "" : "collapse"; ?> collapse-level-2">
									<li>
										<a href="/AI/" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="List" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-list fa-fw mr-20"></i></div> List<div class="pull-right"></div><div class="clearfix"></div></a>
									</li>
									<li>
										<a href="/AI/Create" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Create" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-plus fa-fw mr-20"></i></div> Create<div class="pull-right"></div></a>
									</li>
								</ul>
							</li>
							<li>
								<a href="javascript:void(0);" data-toggle="collapse" data-target="#aiagents" class="<?=$pageDetails["PageID"]=="AIAgents" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-brain fa-fw mr-20"></i></div> Agents<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
								<ul id="aiagents" class="<?=isSet($pageDetails["SubPageID"]) &&  $pageDetails["SubPageID"]=="AIAgents" ? "" : "collapse"; ?> collapse-level-2">
									<li>
										<a href="/AI/Agents" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="List" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-list fa-fw mr-20"></i></div> List<div class="pull-right"></div><div class="clearfix"></div></a>
									</li>
									<li>
										<a href="/AI/Agents/Create" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Create" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-plus fa-fw mr-20"></i></div> Create<div class="pull-right"></div></a>
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
								<a href="/Robotics/" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="List" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-list fa-fw mr-20"></i></div> List<div class="pull-right"></div><div class="clearfix"></div></a>
							</li>
							<li>
								<a href="/Robotics/Create" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Create" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-plus fa-fw mr-20"></i></div> Create<div class="pull-right"></div></a>
							</li>
						</ul>
					</li>
					<li><hr class="light-grey-hr mb-10"/></li>
					<li>
						<a href="javascript:void(0);" data-toggle="collapse" data-target="#dataa" class="<?=$pageDetails["PageID"]=="DataAnalysis" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-table fa-fw mr-20"></i><span class="right-nav-text">Data Analysis</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
						<ul id="dataa" class="<?=$pageDetails["PageID"]=="DataAnalysis" ? "" : "collapse"; ?> collapse-level-1">
							<li>
								<a href="/Data-Analysis/" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="List" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-list fa-fw mr-20"></i></div> List<div class="pull-right"></div><div class="clearfix"></div></a>
							</li>
						</ul>
					</li>
					<?php if($_SESSION["HIAS"]["Admin"]): ?>
					<li><hr class="light-grey-hr mb-10"/></li>
					<li>
						<a href="javascript:void(0);" data-toggle="collapse" data-target="#hospital" class="<?=$pageDetails["PageID"]=="HIS" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-hospital-user fa-fw mr-20"></i><span class="right-nav-text">Users</span></div><div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
						<ul id="hospital" class="<?=$pageDetails["PageID"]=="HIS" ? "" : "collapse"; ?> collapse-level-1">
							<li>
								<a href="javascript:void(0);" data-toggle="collapse" data-target="#staffdd" class="<?=isSet($pageDetails["SubPageID"]) &&  $pageDetails["SubPageID"]=="Staff" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-users fa-fw mr-20"></i></div> Staff<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div><div class="clearfix"></div></a>
								<ul id="staffdd" class="<?=isSet($pageDetails["SubPageID"]) &&  $pageDetails["SubPageID"]=="Staff" ? "" : "collapse"; ?> collapse-level-2">
									<li>
										<a href="/Users/Staff/" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Active" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-list fa-fw mr-20"></i></div> Active<div class="pull-right"></div><div class="clearfix"></div></a>
									</li>
									<li>
										<a href="/Users/Staff/Cancelled" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Cancelled" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-list fa-fw mr-20"></i></div> Cancelled<div class="pull-right"></div><div class="clearfix"></div></a>
									</li>
									<li>
										<a href="/Users/Staff/Create" class="<?=isSet($pageDetails["LowPageID"]) && $pageDetails["LowPageID"]=="Create" ? "active" : ""; ?>"><div class="pull-left"><i class="fas fa-plus fa-fw mr-20"></i></div> Create<div class="pull-right"></div></a>
									</li>
								</ul>
							</li>
						</ul>
					</li>
					<?php endif; ?>
					<li><hr class="light-grey-hr mb-10"/></li>
					<li><a href="https://github.com/AIIAL/HIAS-Core" target="_BLANK"><div class="pull-left"><i class="fa fa-github fa-fw mr-20"></i><span class="right-nav-text">Github</span></div><div class="clearfix"></div></a></li>
					<li><hr class="light-grey-hr mb-10"/></li>
					<li>
						<a href="https://github.com/AIIAL/HIAS-Core/docs" target="_BLANK"><div class="pull-left"><i class="fas fa-info-circle fa-fw mr-20"></i></div> Documentation<div class="pull-right"></div><div class="clearfix"></div></a>
					</li>
					<li><hr class="light-grey-hr mb-10"/></li>
				</ul>
			</div>