<?php //namespace Hanariu; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
  </head>

  <body>
 
	<?php echo $content; ?>
	
	<hr>
	<footer>
        <p>&copy; Company 2012</p>
	</footer>
  </body>
<?php if ( \Hanariu::$environment == \Hanariu::DEVELOPMENT) echo "Rendered in {execution_time} with {memory_usage} of memory.". \Core\View::factory('profiler/stats') ?>
</html>
