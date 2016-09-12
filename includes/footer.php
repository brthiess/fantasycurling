


<!--open footer-->
<?php if (!isset($page['no_footer']) || $page['no_footer'] == false){ ?>
 <footer>
 	<section class='contact-info'>
        <div class="sub-footer-container">
			<?php if ($user_loggedin){echo $Account->email . " - <a href='" . $sitemap[7]['page_url'] . "'>Logout</a><br/><br/>";} ?>
            <span>&copy; <?php echo date('Y') . " " . $global['company_name'];?></span>
            <span class="divider">/</span>
            <span>All Rights Reserved</span>
			<span class="divider">/</span>
			<span><?php echo "<a href='mailto:" . $global['contact_email'] . "'>" . $global['contact_email'] . "</a>";?></span>
        </div>       
    </section>
    <div class='footer-text' id='pixel-army'>Website Designed & Developed by Brad Thiessen</div>
</footer>
<?php } ?>
</section>
 
</div><!--close wrapper--> 

<script type="text/javascript" src="<?php echo $path?>js/script.js"></script>
