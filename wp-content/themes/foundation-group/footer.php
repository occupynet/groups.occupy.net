			<footer role="contentinfo">
			
				<div class="twelve columns">

					<div class="row panel">

						<nav class="twelve columns clearfix">
						</nav>

						<div class="footer twelve columns">
							<?php dynamic_sidebar( 'footerbar' ); ?>
						</div>

					</div>

					<div class="row panel">
						<div class="footer four columns">
							<?php dynamic_sidebar( 'footerleft' ); ?>
						</div>

						<div class="footer four columns">
							<?php dynamic_sidebar( 'footercenter' ); ?>
						</div>

						<div class="footer four columns">
							<?php dynamic_sidebar( 'footerright' ); ?>
						</div>
					</div>

				</div>
			
				<div class="twelve columns">
					<?php $footer_text = of_get_option('footer_text'); ?>
					<p><?php echo $footer_text; ?></p>
				</div>
					
			</footer> <!-- end footer -->
		
		</div> <!-- end #container -->
		
		<!--[if lt IE 7 ]>
  			<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
  			<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
		<![endif]-->
		
		<?php wp_footer(); // js scripts are inserted using this function ?>

	</body>

</html>