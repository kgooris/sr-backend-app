var $collectionHolder;

// setup an "add a tag" link
var $addTagLink = $('<td colspan=3><div class"container"><a class="btn btn-default add_drink_link" href="#"><span class="glyphicon glyphicon-plus" aria-hidden="true"> Lijn Toevoegen</span></a></div></td>');
var $newLinkLi = $('<tr></tr>').append($addTagLink);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    $collectionHolder = $('table#od');

    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);
    
    
    // add a delete link to all of the existing tag form li elements
   // $("#od_table_body").find('tr').each(function() {
    	$collectionHolder.find('tr').each(function() {
    		//alert($(this));
    		//alert($('table tbody tr').index($(this)));
    		if ($('table tbody tr').index($(this)) > 0)
    			{
    				if($(this).children().length > 1)
    				{
    					addTagFormDeleteLink($(this));
    				}
    			}
    	//});
    });
    if ($('table tbody tr').length === 1) {
        	addTagForm($collectionHolder, $newLinkLi);
    }
    
    $addTagLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addTagForm($collectionHolder, $newLinkLi);
    });
    
    function addTagForm($collectionHolder, $newLinkLi) {
        // Get the data-prototype explained earlier
        var prototype = $collectionHolder.data('prototype');

        // get the new index
        var index = $collectionHolder.data('index');

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = prototype.replace(/__name__/g, index);

        // increase the index with one for the next item
        $collectionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add a tag" link li
        var $newFormLi = $('<tr></tr>').append(newForm);
        $newLinkLi.before($newFormLi);
        
     // add a delete link to the new form
       // var $newFormLi = $('<td></td>');
        if ($('table tbody tr').length >= 3) {
        	addTagFormDeleteLink($newFormLi);
        } 
        
    }
    function addTagFormDeleteLink($tagFormLi) {
        var $removeFormA = $('<td><a class="btn btn-default" href="#"><span class="glyphicon glyphicon-trash" aria-hidden="true"> Lijn Verwijderen</span></a></td>');
        $tagFormLi.append($removeFormA);

        $removeFormA.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // remove the li for the tag form
            $tagFormLi.remove();
            if ($('table tbody tr').length === 1) {
            	addTagForm($collectionHolder, $newLinkLi);
            }
        });
    }
});
