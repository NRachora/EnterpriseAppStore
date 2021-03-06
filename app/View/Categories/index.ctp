<?php

// Breadcrumbs
$this->Html->addCrumb('Categories', null);

?><div class="widget">
	<div class="widget-content-white glossed">
		<div class="padded">
			<p>
				<?php if (Me::minAdmin()) { ?>
				<a href="<?= $this->Html->url(array('controller' => 'categories', 'action' => 'edit', 'new')); ?>" class="btn btn-primary pull-right new">New category <i class="fa icon-plus"></i></a>
				<?php } ?>
			</p>
			<table class="table table-striped table-bordered table-hover">
				<thead>
				    <tr>
				        <th class="icon">&nbsp;</th>
				        <th class="name">Name</th>
				    </tr>
				</thead>
				<tbody>
				    <?php
				    if (!empty($categories)) foreach ($categories as $category) {
						$count = (int)$category[0]['appsCount'];   
						if ($count == 0) {
							$style = ' style="color:#AAA;"';
							$styleBadge = ' style="background-color:#AAA;"';
						}
						else {
							$style = '';
							$styleBadge = '';
						}
				    ?>
				    <tr class="clickable"<?= $style; ?>>
				        <td class="icon"><i class="fa <?= $category['Category']['icon']; ?>"></i></td>
				        <td class="name">
							<!-- Begin Edit & delete buttons -->
							<?php if (Me::minDev()) echo $this->element('Admin/Tables/edit', array('controller'=>'categories', 'item'=>$category['Category'])); ?>
							<!-- End Edit & delete buttons -->
							<span class="label label-default"<?= $styleBadge; ?>><?= $count; ?></span>
				            <?php
				            if ($count > 0) echo $this->Html->link(
				            	$category['Category']['name'],
				            	array(
				            		'controller' => 'categories',
				            		'action' => 'view',
									$category['Category']['id'],
									TextHelper::safeText($category['Category']['name'])
								),
				            	array(
				            		'class' => 'view'
								)
				            );
				            else echo $category['Category']['name'];
				            ?>
				            <br />
				            <small><?php if (strlen($category['Category']['description']) > 2) echo '('.$category['Category']['description'].')'; ?></small>
				        </td>
				    </tr>
				    <?php
				    }
				    else {
				    ?>
					<tr>
						<td colspan="4" height="120" valign="middle" align="center"<?php if (Me::minDev()) echo ' class="empty-cell"'; ?>>
							<p style="margin-top:45px;">No categories were found.</p>
						</td>
					</tr>
				    <?php
				    }
				    unset($categories);
				    ?>
				</tbody>
			</table>
		</div>
	</div>
</div>