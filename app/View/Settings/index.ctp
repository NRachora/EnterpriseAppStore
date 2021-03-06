<?php

// Breadcrumbs
$this->Html->addCrumb('Settings', null);

$s = isset($settings) ? $settings : NULL;

function verVal($key, $settings) {
	return isset($settings[$key]) ? $settings[$key] : '';
}

function verValCh($key, $settings) {
	return isset($settings[$key]) ? 'checked="checked"' : '';
}

?>
<div class="widget">
	<form action="<?php echo $this->Html->url(array("controller" => "settings", "action" => "index")); ?>" method="post" enctype="multipart/form-data" role="form" class="form-horizontal">
		<div class="accordion" id="accordion2">
			<!-- <button type="submit" name="save" class="btn btn-primary pull-right save">Save</button> -->
			<div class="accordion-group widget-content-white glossed" style="clear:both;">
				<div class="padded">
					
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#company-information">
							<h3 class="form-title form-title-first"><i class="icon-hand-right"></i> Company information</h3>
						</a>
					</div>
					<div id="company-information" class="accordion-body collapse in">
						<div class="accordion-inner">
							<div class="form-group">
								<label class="col-md-3 control-label">Server name</label>
								<div class="col-md-9">
									<input type="text" name="settings[companyServerName]" class="form-control" placeholder="Name of this site / app" value="<?php echo verVal('companyServerName', $s); ?>" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Company name</label>
								<div class="col-md-9">
									<input type="text" name="settings[companyName]" class="form-control" placeholder="Company name" value="<?php echo verVal('companyName', $s); ?>" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Logo</label>
								<div class="col-md-7">
									<input type="file" name="file[logo]" class="form-control" accept="image/*"  />
								</div>
								<div class="col-md-2">
									<img src="<?php echo $this->Html->url('/', true); ?>Userfiles/Settings/Images/Logo?time=<?php echo time(); ?>" alt="Company logo" class="logo" style="max-width:180px; margin-top:6px;" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Touch icon</label>
								<div class="col-md-8">
									<input type="file" name="file[icon]" class="form-control" accept="image/*"  />
								</div>
								<div class="col-md-1">
									<img src="<?php echo $this->Html->url('/', true); ?>Userfiles/Settings/Images/Icon?time=<?php echo time(); ?>" alt="Touch icon" class="logo" style="max-width:120px; margin-top:6px;" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Support email</label>
								<div class="col-md-9">
									<input type="text" name="settings[companySupportEmail]" class="form-control" placeholder="support@my-company.com" value="<?php echo verVal('companySupportEmail', $s); ?>" />
								</div>
								<div class="col-md-offset-3 col-md-1">
									<input type="checkbox" name="settings[companySupportEmailSendDeviceNotifications]"<?php echo verValCh('companySupportEmailSendDeviceNotifications', $s); ?> class="form-control" />
								</div>
								<div class="col-md-8">
									<span>Send email to this email address when a new device is registered</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Description</label>
								<div class="col-md-9">
									<textarea type="text" name="settings[companyDescription]" class="form-control description" placeholder="Company description"><?php echo verVal('companyDescription', $s); ?></textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<!--<div class="accordion-group widget-content-white glossed">
				<div class="padded">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#invitation-message">
							<h3 class="form-title form-title-first"><i class="icon-envelope-alt"></i> Invitation message</h3>
						</a>
					</div>
					<div id="invitation-message" class="accordion-body collapse in">
						<div class="accordion-inner">
							<div class="form-group">
								<label class="col-md-3 control-label">User Invitation template</label>
								<div class="col-md-9">
									<textarea type="text" name="settings[invitationUserTemplate]" class="form-control description large" placeholder="HTML for user invitation email"><?php echo verVal('invitationUserTemplate', $s); ?></textarea>
									<a href="#" title="" onclick="return env.confirmation('Are you sure you want to revert to the original message?');">Reset to original template</a>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">MDM Enrollment Message</label>
								<div class="col-md-9">
									<textarea type="text" name="settings[invitationMDMTemplate]" class="form-control description large" placeholder="HTML for MDM invitation email"><?php echo verVal('invitationMDMTemplate', $s); ?></textarea>
									<a href="#" title="" onclick="return env.confirmation('Are you sure you want to revert to the original message?');">Reset to original template</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>-->
						
			<div class="accordion-group widget-content-white glossed">
				<div class="padded">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#user-self-registration">
							<h3 class="form-title form-title-first"><i class="icon-lock"></i> User Self-Registration</h3>
						</a>
					</div>
					<div id="user-self-registration" class="accordion-body collapse in">
						<div class="accordion-inner">
							<div class="form-group">
								<label class="col-md-3 control-label">Disable user registrations</label>
								<div class="col-md-1">
									<input type="checkbox" name="settings[sefRegDisable]"<?php echo verValCh('sefRegDisable', $s); ?> class="form-control" />
								</div>
								<div class="col-md-6">
									<span>Disable user registrations</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Email Domain Whitelist</label>
								<div class="col-md-9">
									<textarea type="text" name="settings[sefRegDomains]" id="sefRegDomains" class="form-control description" placeholder="my-company.co.uk"><?php echo verVal('sefRegDomains', $s); ?></textarea>
									<small>Place multiple domain entries each on a separate line, leave empty to keep this feature disabled</small>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="accordion-group widget-content-white glossed">
				<div class="padded">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#user-self-registration">
							<h3 class="form-title form-title-first"><i class="icon-archive"></i> Amazon S3 Hosting</h3>
						</a>
					</div>
					<div id="user-self-registration" class="accordion-body collapse in">
						<div class="accordion-inner">
							<div class="form-group">
								<label class="col-md-3 control-label">Enable Amazon S3</label>
								<div class="col-md-1">
									<input type="checkbox" name="settings[s3Enable]"<?php echo verValCh('s3Enable', $s); ?> class="form-control" />
								</div>
								<div class="col-md-6">
									<span>Enable Amazon S3 hosting for app binaries to lower the cost of hosting and save space on this server</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Bucket</label>
								<div class="col-md-9">
									<input type="text" name="settings[s3Bucket]" class="form-control" placeholder="Bucket name (" value="<?php echo verVal('s3Bucket', $s); ?>" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Access Key</label>
								<div class="col-md-9">
									<input type="text" name="settings[s3AccessKey]" class="form-control" placeholder="OTOSIASI472CWSRE33NA" value="<?php echo verVal('s3AccessKey', $s); ?>" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label">Secret Key</label>
								<div class="col-md-9">
									<input type="text" name="settings[s3SecretKey]" class="form-control" placeholder="!ziS35435DWHG$4OF5HOPfPccrXnxBBkdQYsdfQWRgoH" value="<?php echo verVal('s3SecretKey', $s); ?>" />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="accordion-group widget-content-white glossed">
				<div class="padded">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#user-self-registration">
							<h3 class="form-title form-title-first"><i class="icon-archive"></i> Development</h3>
						</a>
					</div>
					<div id="user-self-registration" class="accordion-body collapse in">
						<div class="accordion-inner">
							<div class="form-group">
								<label class="col-md-3 control-label">MySQL debugger</label>
								<div class="col-md-1">
									<input type="checkbox" name="settings[debugMySQL]"<?php echo verValCh('debugMySQL', $s); ?> class="form-control" />
								</div>
								<div class="col-md-6">
									<span>Enable MySQL debugger on the bottom of each page</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<button type="reset" name="save" class="btn btn-default">Reset</button>			
			<button type="submit" name="save" class="btn btn-primary pull-right save">Save</button>			
		</div>
	</form>
</div>