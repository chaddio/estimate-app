<?php $this->load->view('header') ?>
<?php $this->load->view('nav') ?>
<div class="container">   
    <div class="page-header"></div>
    <div class="row">
        <?php if (isset($error) && $error): ?>
            <div class="alert fade in alert-<?php echo $alertLevel?>">
              <a class="close " data-dismiss="alert" href="#">×</a><?php if($error == 'invalid'){echo validation_errors();}else{echo $error;} ?>
            </div>
        <?php endif; ?>
        <?php if(!@$mobile) :?>
            <div class="well">
        <?php endif; ?>
            <table class="table table-hover">
                <thead>
                    <div class="dropdown">
                        <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <?php echo $viewTitle ?>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if(isset($params)) : ?>
                                <li><a href="<?php echo base_url() ?>users/"><span aria-hidden="true"  class="glyphicon glyphicon-user"></span> Return to <?php echo ucwords($class);?></a></li>
                            <?php else : ?>
                                <li><a  href="<?php echo base_url() . "$class/$method/" . ($active == 1? 0 : 1) ;?>"><span aria-hidden="true"  class="glyphicon glyphicon-off"></span> Show <?php echo ($active == 1 ? "Deactivated " : "Active ") . ucwords($class) ?></a></li>
                            <?php endif; ?>
                                <li class="divider"></li>
                                <li><a data-target="#myModal2" tabindex="-1" data-toggle="modal" href="#myModal2"><span aria-hidden="true"  class="glyphicon glyphicon-plus"></span> Add <?php echo ucwords(substr($class,0,-1)) ?></a></li>
                                <li><a data-target="#myModal" data-toggle="modal" tabindex="-1" href="#myModal"><span aria-hidden="true"  class="glyphicon glyphicon-search"></span> Search <?php echo ucwords($class) ?></a></li>
                        </ul>
                    </div>
                    <br>
                    <tr>
                        <?php if(isset($params)) : //use to check for search page?>
                            <?php if(count($users) > 0) :?>
                                <th>Edit</th>
                                <th>Status</th>
                                <th>Email/Username</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th>Zip Code</th>
                            <?php else: ?>
                                <th colspan="4"><em>Your selected search criteria returned no results</em></th>
                            <?php endif; ?>
                        <?php else : ?>
                            <?php if(count($users) > 0) :?>
                                <th>Edit</th>
                                <th><?php echo ($active == 0 ? "Activate" : "Disable")?></th>
                                <th><a href="<?php echo base_url() . "$class/$method/$active/$colUri1/$row"?>">Email/Username<?php echo $thI1?></a></th>
                                <th><a href="<?php echo base_url() . "$class/$method/$active/$colUri2/$row"?>">Fullname<?php echo $thI2?></a></th>
                            <?php else: ?>
                                <th colspan="4"><em>There are no <?php echo ($active == 0 ? "deactivated " : "active " ) . $class;?> currently in the system</em></th>
                            <?php endif; ?>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($params)) : ?>
                        <?php foreach( $users as $user ) : ?>
                            <tr>
                                <td><a href="<?php echo base_url() . $class;?>/edit/<?php echo $user['id'];?>" title="Click to edit user #<?php echo $user['id'];?>" ><span aria-hidden="true"  class="glyphicon glyphicon-edit"></span></a></td>
                                <?php if($user['active'] == 0):?>
                                    <td><a href="#" data-toggle="modal" data-action="Re-activate" data-info="<?php echo $user['email'];?>" data-target="#confirm-delete" data-href="<?php echo base_url() . $class;?>/activate/<?php echo $user['id'] . '/' . $row;?>" class="right" title="Click to re-activate user #<?php echo $user['id'];?>">Disabled</a></td>
                                <?php else :?>    
                                    <td><a href="#" data-toggle="modal" data-action="Deactivate" data-info="<?php echo $user['email'];?>" data-target="#confirm-delete" data-href="<?php echo base_url() . $class;?>/delete/<?php echo $user['id'] . '/' . $row;?>"  class="right" title="Click to deactivate user #<?php echo $user['id'];?>">Active</a></td>
                                <?php endif;?>
                                <td><?php echo $user['email'] ?></td>
                                <td><?php echo (trim($user['phone_number']) == '' ? '<em>Unregistered</em>' : substr($user['phone_number'],0,3) . '-' . substr($user['phone_number'],3,3) . '-' . substr($user['phone_number'],6,4))?></td>
                                <td><?php echo (trim($user['city']) == '' ? '<em>Unregistered</em>' : $user['city'] . ', ' . $user['state'])?></td>
                                <td><?php echo (trim($user['zip_code']) == '' ? '<em>Unregistered</em>' :  $user['zip_code'])?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <?php foreach( $users as $user ) : ?>
                            <tr>
                                <td><a href="<?php echo base_url() . $class;?>/edit/<?php echo $user['id'];?>" title="Click to edit user #<?php echo $user['id'];?>" ><span aria-hidden="true"  class="glyphicon glyphicon-edit"></span></a></td>
                                <?php if($active == 0):?>
                                <td><a href="#" data-toggle="modal" data-action="Re-activate" data-info="<?php echo $user['email'];?>" data-target="#confirm-delete" data-href="<?php echo base_url() . $class;?>/activate/<?php echo $user['id'] . '/' . $row;?>" class="right" title="Click to re-activate user #<?php echo $user['id'];?>"><span aria-hidden="true"  class="glyphicon glyphicon-off"></span></a></td>
                                <?php else :?>    
                                <td><a href="#" data-toggle="modal" data-action="Deactivate" data-info="<?php echo $user['email'];?>" data-target="#confirm-delete" data-href="<?php echo base_url() . $class;?>/delete/<?php echo $user['id'] . '/' . $row;?>" class="right" title="Click to deactivate user #<?php echo $user['id'];?>"><span aria-hidden="true"  class="glyphicon glyphicon-off"></span></a></td>
                                <?php endif;?>
                                <td><?php echo $user['email'] ?></td>
                                <td><?php echo (trim($user['full_name']) == '' ? '<em>Unregistered</em>' : $user['full_name'])?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif;?>
                </tbody>
            </table>
            <?php if(!empty($links)) : ?>
                    <?php echo $links;?>
            <?php endif; ?>
        <?php if(!@$mobile) :?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $this->load->view('users/confirm_delete') ?>
<?php $this->load->view('users/modal_users') ?>
<?php $this->load->view('footer') ?>