/* Confirm function for category/account deletion	*/
/* Function taken from phpMyAdmin 				*/

/**
 * Displays an confirmation box before to submit a "DROP DATABASE" query.
 * This function is called while clicking links
 *
 * @param   object   the link
 * @param   object   the sql query to submit
 *
 * @return  boolean  whether to run the query or not
 */
function confirmLinkDropDB(theLink, theSqlQuery) {
    // Confirmation is not required in the configuration file
    // or browser is Opera (crappy js implementation)
    if (confirmMsg == '' || typeof(window.opera) != 'undefined') {
        return true;
    }

	var is_confirmed = confirm(confirmMsgDropDB + '\n\n' + confirmMsg + ' ' + theSqlQuery);
    if (is_confirmed) {
        theLink.href += '&is_js_confirmed=1';
    }

    return is_confirmed;
} // end of the 'confirmLinkDropDB()' function

function confirmLinkDropACC(theLink, theSqlQuery) {
    // Confirmation is not required in the configuration file
    // or browser is Opera (crappy js implementation)
    if (confirmMsg == '' || typeof(window.opera) != 'undefined') {
        return true;
    }

	var is_confirmed = confirm(confirmMsg + ' ' + theSqlQuery);
    if (is_confirmed) {
        theLink.href += '&is_js_confirmed=1';
    }

	return is_confirmed;
} // end of the 'confirmLinkDropACC()' function

// js form validation stuff
var errorMsg0   = '';
var errorMsg1   = '';
var noDropDbMsg = '';
var confirmMsg  = 'Are you sure you want to';
var confirmMsgDropDB  = 'Warning! This operation will delete all accounts in the current category!';

jQuery(document).ready(function($) {
    $('.hideMe').hide();

    $('.listing .h3toggle').click(function(){
        $(this).next('.hideMe').slideToggle('fast');
//        $('.reply<?php echo $urow['updateid'];?>').slideToggle('fast');
    });

	$('.stripeMe tr:odd').addClass('odd');

	// edit in place
	$('#editme3').click(function() {
	// only place a select box in this div if there is not one there already
		if($(this).children('select').length == 0) {
			var currentid = $(this).attr('rel');
			var str = '';
			var arr = new Array('high', 'medium', 'low', 'closed');

			//Turn the array into a select box. Show the current value as the selected value
			//Note that we put ids on the options. This is paramount in being able to display
			//the correct text back to the user
			for(i=0; i<arr.length; i++) {
				if (currentid == i) 
					str += "<option selected id='opt-"+i+"' value='"+i+"'>"+arr[i]+"</option>";
				else
					str += "<option value='"+i+"' id='opt-"+i+"'>"+arr[i]+"</option>";
			}

			str = "<select class='eip-priority-select m-btn mini'>"+str+"</select>";
			
			//Put the select box into the div
			$(this).html(str);

			$("select.eip-priority-select").focus();
			$("select.eip-priority-select").blur(function() {
				//Get the user chosen value
				var value = $(this).val();
				
				//Obtain the textual representation of the chosen value
				var valuetext = $(this).children('option#opt-'+value).text();
				
				//Replace the selectme div with the new id. This can be parsed 
				//for a database update, and allows the user to choose another
				//value if he or she clicks on the div again.
				$("#editme3").attr({'rel': value});
				
				$.ajax({
					type: 'POST',
					url: 'update-project-priority.php', 
					data: {id: currentid, priority: valuetext},
				});

				//Put the text into the select div.
				$("#editme3").text(valuetext);
			});
		}
	});


	// Tooltip only Text
	$('.masterTooltip').hover(function() {
		var title = $(this).attr('title');
		$(this).data('tipText', title).removeAttr('title');
		$('<p class="tooltip"></p>').text(title).appendTo('body').fadeIn('slow');
	}, function() {
		$(this).attr('title', $(this).data('tipText'));
		$('.tooltip').remove();
	}).mousemove(function(e) {
		var mousex = e.pageX + 20;
		var mousey = e.pageY + 10;
		$('.tooltip').css({ top: mousey, left: mousex })
	});

    $(".rdescription").focus(function(){
        $(this).animate({"height": "85px",}, "fast" );
        $(".button_block").slideDown("fast");
        return false;
    });
    $("#cancel").click(function(){
        $(".rdescription").animate({"height": "18px",}, "fast" );
        $(".button_block").slideUp("fast");
        return false;
    });
})

// UPDATE FUNCTIONS
function updateArchive(id) {
	$.ajax({
		type: 'POST',
		url: 'update-archive.php',
		data: { 'uid': id },
		success: function(data){ 
			$('.u'+id).slideUp('slow', function() {$(this).remove();});
		},
	});
}
function unUpdateArchive(id) {
	$.ajax({
		type: 'POST',
		url: 'unupdate-archive.php',
		data: { 'uid': id },
		success: function(data){ 
			$('.u'+id).slideUp('slow', function() {$(this).remove();});
		},
	});
}
function updateComplete(id) {
	$.ajax({
		type: 'POST',
		url: 'update-complete.php',
		data: { 'uid': id },
		success: function(data){ 
			$('.u' + id).fadeTo('slow', 0.5, function() {});
			$('.u' + id + ' .icn-only').addClass('green');
			$('.u' + id + ' .flagMe').html('&raquo; Changed to DONE');
			$('.reply' + id).hide();
		},
	});
}
function updateBump(id) {
	$.ajax({
		type: 'POST',
		url: 'update-bump.php',
		data: { 'uid': id },
		success: function(data){ 
			$('.u' + id).fadeTo('slow', 1, function() {});
			$('.u' + id + ' .icn-only').removeClass('green');
			$('.u' + id + ' .icn-only').addClass('red');
			$('.u' + id + ' .flagMe').html('&raquo; Changed to TASK');
			$('.reply' + id).hide();
		},
	});
}

function attachmentDelete(id) {
	$.post(
		'detail-avatar-delete.php',
			{aid: id},
		function(data){
			$('#a'+id).slideUp('slow', function() {});
		}
	);
}
