
            
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
                        <span>Main</span> 
                        <i class="zmdi zmdi-more"></i>
                    </li>
                    <li>
                        <a href="<?=$domain; ?>/Dashboard" class="<?=$pageDetails["PageID"]=="Dashboard" ? "active" : ""; ?>"><div class="pull-left"><i class="zmdi zmdi-view-dashboard mr-20"></i><span class="right-nav-text">Dashboard</span></div><div class="clearfix"></div></a>
					</li>
					<li><hr class="light-grey-hr mb-10"/></li>
                    <li>
                        <a href="<?=$domain; ?>/phpmyadmin" class="" target="_BLANK"><div class="pull-left"><i class="fa fa-database fa-fw mr-20"></i><span class="right-nav-text">Database</span></div><div class="clearfix"></div></a>
                    </li>
					<li><hr class="light-grey-hr mb-10"/></li><i class=""></i>
					<li>
                        <a href="https://github.com/COVID-19-AI-Research-Project/COVID19-Medical-Support-System-Server" class="<?=$pageDetails["PageID"]=="Github" ? "active" : ""; ?>" target="_BLANK"><div class="pull-left"><i class="fa fa-github mr-20"></i><span class="right-nav-text">Github</span></div><div class="clearfix"></div></a>
                    </li>
                </ul>
            </div>