<p>Within this system, you will have the ability to complete a variety of tasks. To start editing, simply select the feature that you wish to manage from the navigation list on the left.</p>

<?php include("includes/widgets/dashboardcharts.php"); ?>

<?php 
$widgets = $CMSBuilder->get_widgets();
if(count($widgets) > 0){
?>
<div class="cms-overview panel f_left">
	<div class="panel-header">CMS Overview</div>
	<div class="panel-content clearfix nopadding">
        <table cellpadding="0" cellspacing="0" border="0">
        <?php
        foreach($widgets as $widget){
            echo '<tr>
				<td height="30px"><i class="fa fa-' .$widget['icon']. ' color-theme1"></i> &nbsp; ' .$widget['title']. '</td>
				<td>' .$widget['value']. '</td>
			</tr>';
        }
        ?>
        </table>
    </div>
</div>
<?php } ?>

<?php include("includes/widgets/dashboardseo.php"); ?>