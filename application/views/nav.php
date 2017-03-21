<div class="navbar-bar navbar-default navbar-fixed-top">    
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="#" name="top"><?php echo $navTitle;?></a>
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
            <ul class="nav navbar-nav">
                <li class="<?php echo (@$class == 'main') ? 'active': '';?>">
                    <?php if(@$lockNav) :?>
                        <a data-toggle="modal" data-target="#confirm-away" data-href="<?php echo base_url() ?>main/" href="#"><span class="glyphicon glyphicon-home" ></span> Home</a>
                    <?php else : ?>
                        <a href="<?php echo base_url() ?>main/" title="Return Home" ><span class="glyphicon glyphicon-home" ></span> Home</a>
                    <?php endif ;?>
                </li>
                <?php foreach ($app_pages as $key => $value) : ?>
                    <?php if(@$lockNav) :?>
                        <li class="<?php echo (@$class == $value['controller']) ? 'active': '';?>"><a data-toggle="modal" href="#" data-target="#confirm-away" data-href="<?php echo base_url() . $value['controller'] . '/' ?>" title="<?php echo $value['nav_title']?>"><span aria-hidden="true" class="<?php echo $value['icon_css']?>"></span> <?php echo $value['nav_heading']?></a></li>
                    <?php else : ?>
                        <li class="<?php echo (@$class == $value['controller']) ? 'active': '';?>"><a href="<?php echo base_url() . $value['controller'] . '/' ?>" title="<?php echo $value['nav_title']?>"><span aria-hidden="true" class="<?php echo $value['icon_css']?>"></span> <?php echo $value['nav_heading']?></a></li>
                    <?php endif ;?>
                <?php endforeach; ?>
                <?php if(isset($search)) : ?>
                    <li><a data-toggle="modal" href="#myModal"><span aria-hidden="true" class="glyphicon glyphicon-search"></span> Search <?php echo ucwords($class) ?></a></li>
                <?php endif;?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown <?php echo (@$class == 'users' || @$class == 'newdefault' || @$class == 'settings') ? 'active': '';?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <?php if ($isAdmin) : ?>
                            <span aria-hidden="true" class="glyphicon glyphicon-wrench"></span> Admin <span class="caret"></span></a>
                            <ul class="dropdown-menu">  
                                <li>
                                    <?php if(@$lockNav) :?>
                                        <a data-toggle="modal" data-target="#confirm-away" href="#" data-href="<?php echo base_url()?>settings/"><span aria-hidden="true" class="glyphicon glyphicon-cog"></span> Settings</a>
                                    <?php else : ?>
                                        <a href="<?php echo base_url() ?>settings/"><span aria-hidden="true" class="glyphicon glyphicon-cog"></span> Settings</a>
                                    <?php endif ;?>
                                </li>
                                <li>
                                    <?php if(@$lockNav) :?>
                                        <a data-toggle="modal" href="#" data-target="#confirm-away" data-href="<?php echo base_url()?>users/" ><span aria-hidden="true" class="glyphicon glyphicon-user"></span> Users</a></li>
                                    <?php else : ?>
                                        <a href="<?php echo base_url() ?>users/"><span aria-hidden="true" class="glyphicon glyphicon-user"></span> Users</a>
                                    <?php endif ;?>
                                <li class="divider"></li>
                                <li>
                                    <?php if(@$lockNav) :?>
                                        <a data-toggle="modal" data-target="#confirm-away" href="#" data-href="<?php echo base_url()?>login/logout"><span aria-hidden="true" class="glyphicon glyphicon-off"></span> Logout</a></li>
                                    <?php else : ?>
                                        <a href="<?php echo base_url() ?>login/logout"><span aria-hidden="true" class="glyphicon glyphicon-off"></span> Logout</a></li>
                                    <?php endif ; ?>
                            </ul>
                        <?php else : ?>
                            <span aria-hidden="true" class="glyphicon glyphicon-wrench"></span> <?php echo $fname;?> <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li>
                                    <?php if(@$lockNav) :?>
                                        <a href="#" data-toggle="modal" data-target="#confirm-away" data-href="<?php echo base_url()?>settings/"><span aria-hidden="true" class="glyphicon glyphicon-cog"></span> My Profile</a>
                                    <?php else : ?>
                                        <a href="<?php echo base_url() ?>settings/"><span aria-hidden="true" class="glyphicon glyphicon-cog"></span> My Profile</a>
                                    <?php endif ; ?>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <?php if(@$lockNav) :?>
                                        <a data-toggle="modal" href="#" data-target="#confirm-away" data-href="<?php echo base_url()?>login/logout"><span aria-hidden="true" class="glyphicon glyphicon-off"></span> Logout</a></li>
                                    <?php else : ?>
                                        <a href="<?php echo base_url() ?>login/logout"><span aria-hidden="true" class="glyphicon glyphicon-off"></span> Logout</a></li>
                                    <?php endif ; ?>
                                </li>
                            </ul>
                        <?php endif; ?>
                </li>
            </ul>      
        </div>
    </div>
 </div>