<?php $this->load->view('header') ?>
<?php $this->load->view('nav') ?>
<div class="container">   
    <div class="page-header"></div>
    <div class="row">
        <?php if (isset($error) && $error): ?>
            <div class="alert fade in alert-<?php echo $alertLevel?>">
              <a class="close" data-dismiss="alert" href="#">×</a><?php if($error == 'invalid'){echo validation_errors();}else{echo $error;} ?>
            </div>
        <?php endif; ?>
        <?php if(!@$mobile): ?>
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
                                <li><a data-toggle="modal" href="<?php echo base_url() . "$class/" ?>"><span class="glyphicon glyphicon-user"></span> Return to <?php echo ucwords($class);?></a></li>
                            <?php else : ?>
                                <li><a role="menuitem" data-toggle="modal" tabindex="-1" href="<?php echo base_url() . "$class/$method/" . ($active == 1? 0 : 1) ;?>"><span class="glyphicon glyphicon-off"></span> Show <?php echo ($active == 1 ? "Archived " : "Active ") . ucwords($class) ?></a></li>
                            <?php endif; ?>
                                <li class="divider"></li>
                                <li><a role="menuitem" data-toggle="modal" tabindex="-1" href="#myModal2"><span class=" glyphicon glyphicon-plus"></span> New <?php echo ucwords(substr($class,0,-1)) ?></a></li>
                                <li><a role="menuitem" data-toggle="modal" tabindex="-1" href="#myModal"><span class="glyphicon glyphicon-search"></span> Search <?php echo ucwords($class) ?></a></li>
                        </ul>
                    </div>
                    <br>
                    <tr>
                        <?php if(isset($params)) : //use to check for search page?>
                            <?php if(count($items) > 0) :?>
                                <th>Edit</th>
                                <th>Status</th>
                                <th>Customer Email</th>
                                <th>Phone</th>
                            <?php if($this->session->userdata('app_userlevel') < 3) : ?>
                                <th>Salesperson</th>
                            <?php endif; ?>
                            <?php else: ?>
                                <th colspan="4"><em>Your selected search criteria returned no results</em></th>
                            <?php endif; ?>
                        <?php else : ?>
                            <?php if(count($items) > 0) :?>
                                <th>Edit</th>
                                <?php if(!@$mobile): ?>
                                    <th><?php echo ($active == 0 ? "Activate" : "Archive")?></th>
                                <?php endif ;?>
                                <?php if(!@$mobile): ?>
                                    <th><a href="<?php echo base_url() . "$class/$method/$active/$colUri1/$row"?>"><?php echo $thI1?>Customer Email<?php echo ($thI1 != '') ? '</span>' : ''?></a></th>
                                <?php endif; ?>
                                <th><a href="<?php echo base_url() . "$class/$method/$active/$colUri2/$row"?>"><?php echo $thI2?>Phone<?php echo ($thI2 != '') ? '</span>' : ''?></a></th>
                                <th><a href="<?php echo base_url() . "$class/$method/$active/$colUri3/$row"?>"><?php echo $thI3?>Name<?php echo ($thI3 != '') ? '</span>' : ''?></a></th>
                                <th><a href="<?php echo base_url() . "$class/$method/$active/$colUri4/$row"?>"><?php echo $thI4?><strong>Added</strong><?php echo ($thI4 != '') ? '</span>' : ''?></a></th>
                                <?php if($this->session->userdata('app_userlevel') < 3) : ?>
                                    <th><a href="<?php echo base_url() . "$class/$method/$active/$colUri5/$row"?>"><?php echo $thI5?>Salesperson<?php echo ($thI1 != '') ? '</span>' : ''?></a></th>
                                <?php endif ; ?>
                            <?php else: ?>
                                <th colspan="4"><em>There are no <?php echo ($active == 0 ? "archived " : "active " ) . $class;?> currently in the system</em></th>
                            <?php endif; ?>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($params)) : ?>
                        <?php foreach( $items as $item ) : ?>
                    <tr class='<?php echo @$item['rowClass'];?>'>
                                <td><a role="button" class="edit-link" data-view="<?php echo $item['pdf_hash'];?>" data-edit="<?php echo $item['id'];?>"  href="javascript:void(0);" title="Edit <?php echo substr(ucwords($class), 0, -1);?> #<?php echo $item['id'];?>" ><span class="glyphicon glyphicon-edit"></span></a></td>
                                <?php if($item['active'] == 0):?>
                                    <td><a href="#" data-toggle="modal" data-action="Un-archive" data-info="<?php echo $item['full_name'];?>" data-target="#confirm-delete" data-href="<?php echo base_url() . $class;?>/activate/<?php echo $item['id'] . '/' . $row;?>" class="right" title="Click to un-archive <?php echo substr($class, 0, -1);?> #<?php echo $item['id'];?>">Disabled</a></td>
                                <?php else :?>    
                                    <td><a href="#" data-toggle="modal" data-action="Archive" data-info="<?php echo $item['full_name'];?>" data-target="#confirm-delete" data-href="<?php echo base_url() . $class;?>/delete/<?php echo $item['id'] . '/' . $row;?>" class="right" title="Click to archive <?php echo substr($class, 0, -1);?> #<?php echo $item['id'];?>">Active</a></td>
                                <?php endif;?>
                                <td><?php echo $item['email'] ?></td>
                                <td><?php echo substr($item['phone_number'],0,3) . '-' . substr($item['phone_number'],3,3) . '-' . substr($item['phone_number'],6,4)?></td>
                                <?php if($this->session->userdata('app_userlevel') < 3) : ?>
                                    <td><?php echo $item['sales_person'];?></td>
                                <?php endif ; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <?php foreach( $items as $item ) : ?>
                            <tr class='<?php echo $item['rowClass'];?>'>
                                <td><a role="button" class="edit-link" tabindex="0" data-view="<?php echo $item['pdf_hash'];?>" data-edit="<?php echo $item['id'];?>"  data-closed="<?php echo $item['closed'];?>" href="javascript:void(0);" title="Edit <?php echo substr(ucwords($class), 0, -1);?> #<?php echo $item['id'];?>" ><span class="glyphicon glyphicon-edit"></span></a></td>
                                <?php if(!@$mobile): ?>
                                    <?php if($active == 0):?>
                                        <td><a href="#" data-toggle="modal" data-action="Un-archive" data-info="<?php echo $item['full_name'];?>" data-target="#confirm-delete" data-href="<?php echo base_url() . $class;?>/activate/<?php echo $item['id'] . '/' . $row;?>" class="right" title="Click to un-archive <?php echo substr($class, 0, -1);?> #<?php echo $item['id'];?>"><span class="glyphicon glyphicon-off"></span></a></td>
                                    <?php else :?>    
                                        <td><a href="#" data-toggle="modal" data-action="Archive" data-info="<?php echo $item['full_name'];?>" data-target="#confirm-delete" data-href="<?php echo base_url() . $class;?>/delete/<?php echo $item['id'] . '/' . $row;?>" class="right" title="Click to archive <?php echo substr($class, 0, -1);?> #<?php echo $item['id'];?>"><span class="glyphicon glyphicon-off"></span></a></td>
                                    <?php endif;?>
                                <?php endif; ?>
                                <?php if(!@$mobile): ?>
                                    <td><?php echo $item['email'] ?></td>
                                <?php endif; ?>
                                <td><?php echo $item['phone_number'] ?></td>
                                <td><?php echo trim(@$item['full_name']);?></td>
                                <td><?php echo $item['added'];?></td>
                                <?php if($this->session->userdata('app_userlevel') < 3) : ?>
                                    <td><?php echo $item['sales_person'];?></td>
                                <?php endif ; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif;?>
                </tbody>
            </table>
            <?php if(!empty($links)) : ?>
                <?php echo $links;?>
            <?php endif; ?>
            <div class="row">
                <div class="col-xs-3" >
                    <?php echo nbs(2)?><a role="button" tabindex="0" id="color-legend"><span class="glyphicon glyphicon-question-sign"></span>colors</a>
                </div>
            </div>
        <?php if(!@$mobile) :?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $this->load->view('estimates/confirm_delete') ?>
<?php $this->load->view('estimates/modal_estimates') ?>
<?php $this->load->view('estimates/popover') ?>
<?php $this->load->view('footer') ?>