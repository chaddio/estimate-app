<div class="navbar-bar navbar-default navbar-fixed-top">    
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="<?php echo base_url() . substr($_SERVER['REQUEST_URI'],1);?>" name="top"><?php echo $navTitle;?></a>
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown <?php echo (@$loginNavDropdown) ? 'active' : '' ;?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            
                    <?php if(isset($disableHelp)) : ?>
                        <span class="glyphicon glyphicon-off" aria-hidden="true"></span> Logout <span class="caret"></span>
                    </a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo base_url()?>login/logout"><span class="glyphicon glyphicon-off" aria-hidden="true"></span> Logout Now</a></li>
                        </ul>
                    <?php else : ?>
                        <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> <?php echo ucwords($class); ?> Help <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if(isset($addtDropLinks) && is_array($addtDropLinks)) : ?>
                            <?php foreach ($addtDropLinks as $key => $value) : ?>
                                <li><a href="<?php echo $addtDropLinks[$key]['href']?>"><span class="<?php echo $addtDropLinks[$key]['icon']?>" aria-hidden="true"></span> <?php echo $addtDropLinks[$key]['label']?></a></li>
                                <li class="divider"></li>
                            <?php endforeach; ?>
                        <?php endif ; ?>
                        <li><a href="<?php echo base_url()?>login/forgot_pwd"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Forgot Password</a></li>
                        <li class="divider"></li>
                        <li><a data-toggle="modal" href="" data-target="#myModal"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Forgot Login</a></li>
                    </ul>
                <?php endif ; ?>
                </li>
            </ul>
        </div>
    </div> 
</div>