jQuery(document).ready(function($){

    var substringMatcher = function(strs) {
	  return function findMatches(q, cb) {
	    var matches, substringRegex;

	    // an array that will be populated with substring matches
	    matches = [];

	    // regex used to determine if a string contains the substring `q`
	    substrRegex = new RegExp(q, 'i');

	    // iterate through the pool of strings and for any string that
	    // contains the substring `q`, add it to the `matches` array
	    $.each(strs, function(i, str) {
	      if (substrRegex.test(str)) {
	        matches.push(str);
	      }
	    });

	    cb(matches);
	  };
	};

	var counties = $('#str-counties').val();
	var coArr = counties.split(',');
	var cities = $('#str-cities').val();
	var ciArr = cities.split(',');
	var zips = $('#str-zips').val();
	var zipArr = zips.split(',');

	$('#ls').typeahead({
	  hint: true,
	  highlight: true,
	  minLength: 1
	},
	{
	  name: 'counties',
	  source: substringMatcher(coArr)
	},
	{
	  name: 'cities',
	  source: substringMatcher(ciArr)
	},
	{
	  name: 'zip',
	  source: substringMatcher(zipArr)
	});
});