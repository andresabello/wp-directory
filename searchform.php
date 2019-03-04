<form action="/" method="get">
	<div class="input-group custom-search-form">
		<input type="text" name="s" id="search" value="<?php the_search_query(); ?>" class="form-control searchField">
		<span class="input-group-btn">
			<button class="btn btn-success btn-outline btn-search" type="submit">
				<span class="glyphicon glyphicon-search"></span>
			</button>
		</span>
	 </div>
 </form>