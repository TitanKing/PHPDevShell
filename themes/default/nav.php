<div id="nav">
    <div id="menu" class="navbar navbar-static-top navbar-inverse">
        <div class="navbar-inner">
            <!-- MENU AREA -->
            <div>
                <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".mainnav-collapse">
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <ul class="nav pull-right">
                    <?php $template->outputAltNav() ?>
                    <li class="dropdown">
                        <a id="login-url" href="#" data-toggle="dropdown" class="dropdown-toggle">
                            <i class="icon-user icon-white"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <?php $template->outputLogin() ?>
                            </li>
                        </ul>
                    </li>
                    <?php $template->outputAltHome() ?>
                </ul>
                <div class="nav-collapse mainnav-collapse">
                    <ul id="main-nav" class="nav">
                        <?php $template->outputMenu() ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- SUBNAV MENU AREA -->
    <div id="subnav" class="navbar navbar-static-top">
        <div class="navbar-inner">
            <div>
                <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".subnav-collapse">
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <div class="nav-collapse mainnav-collapse">
                    <ul class="nav">
                        <?php $template->outputName() ?>
                    </ul>
                </div>
                <div class="nav-collapse subnav-collapse">
                    <ul class="nav pull-right">
                        <?php $template->outputSubnav() ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="ajax-loader-art">
    <div class="progress progress-striped active">
        <div class="bar" style="width: 100%;"></div>
    </div>
</div>
