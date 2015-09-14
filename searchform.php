<form role="search" method="get" class="form-inline search-form" action="<?php echo home_url( '/' ); ?>">
	<label for="s" class="sr-only">Search:</label>
	<input type="text" value="<?php echo htmlentities($_GET['s']); ?>" name="s" class="form-control search-field" id="s" placeholder="Search College of Business">
	<button type="submit" class="search-submit">
		<span class="fa fa-fw fa-search"></span>
		<span class="sr-only">Search</span>
	</button>
</form>
