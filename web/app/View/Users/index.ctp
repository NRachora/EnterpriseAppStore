<?php

// Breadcrumbs
$this->Html->addCrumb('Users', null);

?><div class="widget">
	<div class="widget-content-white glossed">
		<div class="padded">
			<p>
				<a href="<?= $this->Html->url(array('controller' => 'users', 'action' => 'edit', 'new')); ?>" class="btn btn-primary pull-right new">New user <i class="fa icon-plus"></i></a>
			</p>
			<table class="table table-striped table-bordered table-hover">
				<thead>
				    <tr>
				        <th class="icon">&nbsp;</th>
				        <th class="name">User</th>
				        <th class="edit">Edit</th>
				    </tr>
				</thead>
				<tbody>
				    <?php
				    foreach ($users as $user) {
				    	$user = $user['User'];
				    ?>
				    <tr>
				        <td class="icon">
					        <img src="<?= $user['gravatar_url']; ?>?s=56" alt="<?= $user['lastname'].', '.$user['firstname']; ?>" />
				        </td>
				        <td class="name">
				            <?= $this->Html->link($user['lastname'].', '.$user['firstname'], array('controller' => 'users', 'action' => 'view', $user['id'], $user['username'])); ?><br />
				            <small>Email <?php if (strlen($user['email']) > 2) echo '<a href="mailto:'.$user['email'].'" title="Email user '.$user['firstname'].' '.$user['lastname'].'">'.$user['email'].'</a>'; ?></small>
				        </td>
				        <td class="edit">
				        	<a href="<?= $this->Html->url(array("controller" => 'users', 'action' => 'edit', $user['id'], $user['username'])); ?>">
				        		<i class="fa icon-edit"><span> Edit</span></i>
				        	</a>
				        	<?php
				        	if ($user['role'] != 'owner') {
				        	?>
				        	<br />
				        	<a href="<?= $this->Html->url(array("controller" => 'users', 'action' => 'delete', $user['id'], $user['username'])); ?>" onclick="return env.confirmation('Are you sure you want to delete user <?= $user['firstname'].' '.$user['lastname']; ?>?');">
				        		<i class="fa icon-ban-circle"><span> Delete</span></i>
				        	</a>
				        	<?php
				        	}
				        	?>
				        </td>
				    </tr>
				    <?php
				    }
				    unset($users);
				    ?>
				</tbody>
			</table>
		</div>
	</div>
</div>