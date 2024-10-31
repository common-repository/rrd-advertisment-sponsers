var j = jQuery.noConflict();

j(document).ready(function() {
	//alert('ready');
	
	//Admin sponser add time assign advertisement and partition	
	j('#admin_sponser_adv').change(function(){
		var site_name = j('#sitename').val();
		var myurl = site_name+'/wp-admin/admin-ajax.php';
		var advertiseId = j(this).val();
		var partition = j("#sponspartition").val();
		var curradvertiseId = j("#curradvertiseId").val();
							
		j.ajax({
		type: 'POST',
		url: myurl,
		async: false,
		data: { action: 'AddSponserAdvPartList', advertiseId : advertiseId, partition : partition, curradvertiseId : curradvertiseId },
		success: function(data)
		{				
			//alert(data);
			j('#partid').html('');
			j('#partid').html(data);			
		}
		});
	});
	

	j('div [id="adv_sposer"]').click(function()
	{
		if(j(this).prop('checked') == true)
		{
			var chk = 1;
		}
		else
		{
			var chk = 2;
			j('#'+stxt).removeClass('err');
			j('#'+stxt).val('');
			return false;			
		}

		var stxt = j(this).attr('lang');
		if(j('#'+stxt).val().length <= 0)
		{
			j('#'+stxt).addClass('err');
			alert('Add Partition value first');
			return false;
		}
		else
		{
			j('#'+stxt).removeClass('err');			
		}

		var div = j(this);
		var partition = j('#'+stxt).val();
		var postId = j('#postId').val();
		var site_name = j('#site_name').val();
		var myurl = site_name+'/wp-admin/admin-ajax.php';
		var sponser = j(this).val();
					
		j.ajax({
		type: 'POST',
		url: myurl,
		async: false,
		data: { action: 'AddAdvSponser', postId : postId, sponser : sponser, chk : chk, partition : partition },
		success: function(data)
		{				
			if(data.length > 0)					
			{
				j('#spons_resp').html(data);
				j(div).prop('checked',false);				
				j('#'+stxt).val('');
				return false;
			}
			else
			{
				j('#spons_resp').html('');
				j('#'+stxt).attr('readonly','readonly');
			}
		}
		});
		
	});
	
	j('#notlogin').click(function(){
		j( "#dialog-not-loggin" ).dialog({
		height: 140,
		modal: true
		});
	});	
	
	j('#createtable').click(function(){		
		mytable = j('<table id="frontadv" ></table>');
		var myheight =  j('#heightcount').val()*10;
		var mywidth = j('#widthcount').val()*10;	
		mytable.css('height',myheight+'px !important');
		mytable.css('width',mywidth+'px !important');	
		var rows = new Number(j("#rowcount").val());
		var cols = new Number(j("#columncount").val());
		var prices = '';
		var tr = [];
		var counter = 1;
		for (var t = 0; t < rows; t++) 
		{
			var row = j('<tr></tr>').attr({ class: ["class1", "class2", "class3"].join(' ') }).appendTo(mytable);
			for (var k = 0; k < cols; k++) 
			{
				j('<td align="center">&nbsp;'+counter+'&nbsp;</td>').appendTo(row);
				prices += '<tr><td>Part '+counter+' Price $</td><td><input type="text" name="price[]" id="price"></td></tr>';
				counter++;
			}			
		}		
		
		j("#box").html('');
		j("#pricesdiv").html('');
		j("#pricesdiv").append(prices);
		j("#box").append(mytable);
	});
	
	function updateTips( t ) 
	{
		tips = j( ".validateTips" );
		tips.text( t ).addClass( "ui-state-highlight" );
		setTimeout(function() 
		{
			tips.removeClass( "ui-state-highlight", 1500 );
		}, 500 );
	}
	
	function checkLength( field, min, max ) {
		var o = j('#'+field);		
		if ( o.val().length > max || o.val().length < min ) 
		{
			o.addClass( "ui-state-error" );
			updateTips( "Length of " + field + " must be between " +
			min + " and " + max + "." );
			return false;
		} 
		else 
		{
			o.removeClass( "ui-state-error" );
			return true;
		}
	}
	
	function checkRegexp( field, regexp, n ) 
	{
		var o = j('#'+field);
		if ( !( regexp.test( o.val() ) ) ) 
		{
			o.addClass( "ui-state-error" );
			updateTips( n );
			return false;
		} 
		else 
		{
			o.removeClass( "ui-state-error" );
			return true;
		}
	}
	
	function chkblank(field)
	{
		var o = j('#'+field);
		if(o.val().length <= 0)
		{
			o.addClass( "ui-state-error" );
			updateTips( "Please chose Item." );
			return false;
		}
		else
		{
			o.removeClass( "ui-state-error" );
			return true;
		}
	}
	
	
	j('#AddAdvertise').click(function(){
		
		j('#resp').html('');
		
		var prices = new Array();
		var name		= j('#name').val();			
		var description	= j('#description').val();
		var height		= j('#heightcount').val();
		var width		= j('#widthcount').val();
		var row			= j('#rowcount').val();
		var column		= j('#columncount').val();
		var form_item		= j('#form_item').val();
		prices 		= j('input[name="price[]"]').map(function(){ return this.value }).get()

		allFields = j( [] ).add( name ).add( description ).add( height ).add( width ).add( row ).add( prices ),
		
		j( ".validateTips" ).html('');
		allFields.removeClass( "ui-state-error" );
		
		j( "#dialog-add-advertise" ).dialog({
			
			height: 650,
			width: 600,
			modal: true,
			buttons: {
			"Reserve": function() 
			{		
				var bValid = true;
				allFields.removeClass( "ui-state-error" );
				bValid = bValid && checkLength( "name", 25, 150 );
				bValid = bValid && checkLength( "description", 30, 500 );
				bValid = bValid && chkblank("form_item");
				bValid = bValid && checkLength( "heightcount", 1, 2 );					
				//bValid = bValid && checkRegexp( "name", /^[0-9a-z_]+j/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );				
				//bValid = bValid && checkRegexp( "description", /^[0-9]+j/, "Password field only allow : a-z 0-9" );
				bValid = bValid && checkRegexp( "heightcount", /^[0-9]/i, "Height field only allow : 0-9" );
				bValid = bValid && checkRegexp( "widthcount", /^[0-9]/i, "width field only allow : 0-9" );
				bValid = bValid && checkRegexp( "rowcount", /^[0-9]/i, "Rows field only allow : 0-9" );
				bValid = bValid && checkRegexp( "columncount", /^[0-9]/i, "Columns field only allow : 0-9" );
				if ( bValid ) 
				{
					if(j("#box").html().length < 5)
					{
						updateTips( "Please click Generate button." );
						bValid = false;
					}

					j('input[name="price[]"]').each(function (){
						if ( this.value.length > 2 || this.value.length < 1 ) 
						{
							j(this).addClass( "ui-state-error" );
							updateTips( "Length of price must be between 1 and 2 ." );
							
							bValid = false;
							
						} 
						else
						{
							
							j(this).removeClass( "ui-state-error" );
							bValid = true;
						}
						
						var o = j(this);
						var regexp = /^[0-9]/i;
						if ( !( regexp.test( o.val() ) ) ) 
						{
							o.addClass( "ui-state-error" );
							updateTips( "Price field only allow : 0-9" );
							bValid = false;
						} 
						else 
						{
							o.removeClass( "ui-state-error" );
							bValid = true;
						}						
					});
					
					if(bValid == false)			
					{
						return false;	
					}
					
					var site_name = j('#site_name').val();
					var myurl = site_name+'/wp-admin/admin-ajax.php';
					
					var prices = new Array();
					var name		= j('#name').val();			
					var description	= j('#description').val();
					var height		= j('#heightcount').val();
					var width		= j('#widthcount').val();
					var row			= j('#rowcount').val();
					var column		= j('#columncount').val();
					var form_item	= j('#form_item').val();
					prices 		= j('input[name="price[]"]').map(function(){ return this.value }).get()
				
					j.ajax({
					type: 'POST',
					url: myurl,
					async: false,
					data: { action: 'AddAdvertisement', name : name, description : description, height : height, width : width, row : row, column : column, prices : prices, form_item : form_item },
					success: function(data)
					{									
						j('#resp').html(data);
						j('#box').html(' ');
						j('#pricesdiv').html(' ');
						j('#name').val('');			
						j('#description').val('');
						j('#heightcount').val('');
						j('#widthcount').val('');
						j('#rowcount').val('');
						j('#columncount').val('');
						j("#form_item").val(j("#form_item option:first").val());
					}
					});
				}
			},
			Cancel: function() 
			{
				j( this ).dialog( "close" );
				
				j('#box').html(' ');
				j('#pricesdiv').html(' ');
				j('#name').val('');			
				j('#description').val('');
				j('#heightcount').val('');
				j('#widthcount').val('');
				j('#rowcount').val('');
				j('#columncount').val('');
				j("#form_item").val(j("#form_item option:first").val());
				
				j("input").removeClass( "ui-state-error" );		
			}
			},
			close: function() 
			{
				//allFields.val( "" ).removeClass( "ui-state-error" );
				
				j('#box').html(' ');
				j('#pricesdiv').html(' ');
				j('#name').val('');			
				j('#description').val('');
				j('#heightcount').val('');
				j('#widthcount').val('');
				j('#rowcount').val('');
				j('#columncount').val('');	
				j('#form_item').val('');
				j("#form_item").val(j("#form_item option:first").val());		
				
				j("input").removeClass( "ui-state-error" );			
				
			}
			
			});
	});
	
    j(function() 
	{
		var name = j( "#name" ),
		email = j( "#email" ),
		contact = j( "#contact" ),
		description = j( "#description" ),
		banner = j( "#banner" )
		allFields = j( [] ).add( name ).add( email ).add( contact ).add( description ).add( banner ),
		tips = j( ".validateTips" );
		
		function updateTips( t ) 
		{
			tips.text( t ).addClass( "ui-state-highlight" );
			setTimeout(function() 
			{
				tips.removeClass( "ui-state-highlight", 1500 );
			}, 500 );
		}
		
		function checkLength( o, n, min, max ) {
			
			if(n == 'banner')
			{
				var field = o.val();
				if(field == '')
				{
					o.addClass( "ui-state-error" );
					updateTips( "Please Upload Image." );
					return false;	
				}
			}
			else
			{			
				if ( o.val().length > max || o.val().length < min ) 
				{
					o.addClass( "ui-state-error" );
					updateTips( "Length of " + n + " must be between " +
					min + " and " + max + "." );
					return false;
				} 
				else 
				{
					return true;
				}
			}
		}
		
		function checkRegexp( o, regexp, n ) 
		{
			if ( !( regexp.test( o.val() ) ) ) 
			{
				o.addClass( "ui-state-error" );
				updateTips( n );
				return false;
			} 
			else 
			{
				return true;
			}
		}
		function imagevalid(o,name)
		{
			
			var fileName = o.val();
			var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
			if(ext == "gif" || ext == "GIF" || ext == "JPEG" || ext == "jpeg" || ext == "jpg" || ext == "JPG" || ext == "png" || ext == "PNG")
			{
				return true;
			} 
			else
			{
				o.addClass( "ui-state-error" );
				updateTips( "Upload Gif,Png or Jpg images only" );
				return false;
			}
		}
		
		/* Upload image from front end when advertisement patition will be booked */
		j('#upload').click(function(){	
			j("#preview").html('');
			j("#preview").html('<img src="loader.gif" alt="Uploading...."/>');
			j("#imageform").ajaxForm({
			target: '#preview'
			}).submit();
						
			
			setTimeout(function(){j('.close').trigger('click');},1000);
			
		});
		function NumericValidation(eventObj)
		{
			var keycode;
		 
			if(eventObj.keyCode) //For IE
				keycode = eventObj.keyCode;
			else if(eventObj.Which)
				keycode = eventObj.Which;  // For FireFox
			else
				keycode = eventObj.charCode; // Other Browser
		 
			if (keycode!=8) //if the key is the backspace key
			{
				if (keycode<48||keycode>57) //if not a number
					return false; // disable key press
				else
					return true; // enable key press
			 }        
		 }
		
		j('.close').click(function(){
			j( "#name" ).val('');
				j( "#email" ).val('');
				j( "#contact" ).val('');
				j( "#description" ).val('');
				j( "#banner" ).val('');
			});
		
		j( "#dialog-form" ).dialog(
		{
			autoOpen: false,
			height: 550,
			width: 350,
			modal: true,
			buttons: {
			"Reserve": function() 
			{
				var partition = j('.selected').attr('lang');				
				var bValid = true;
				allFields.removeClass( "ui-state-error" );
				bValid = bValid && checkLength( name, "name", 10, 50 );
				bValid = bValid && checkLength( email, "email", 6, 200  );
				bValid = bValid && NumericValidation( contact );
				bValid = bValid && checkLength( contact, "contact", 7, 15  );
				bValid = bValid && checkLength( description, "description", 25, 500 );
				bValid = bValid && imagevalid( banner, "banner");		
				
				//bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+j/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );
				// From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
				bValid = bValid && checkRegexp( email, /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/ , "email should be eg. ui@jquery.com" );
				
				if ( bValid ) 
				{
					
					var data = j('#FrmSponser').serialize();
					
					var title = name.val();
					var myemail = email.val();
					var mycontact = contact.val();
					var mydesc = description.val();
					var mybanner = banner.val();
					var site_name = j('#site_name').val();
					var myurl = site_name+'/wp-admin/admin-ajax.php';
					var postId = j("#postId").val();
					j.ajax({
					type: 'POST',
					url: myurl,
					async: false,					
					data: { action: 'AddSponser', name : title, description : mydesc, email : myemail, contact : mycontact, banner : mybanner, postId : postId, partition : partition},
					success: function(data)
					{
						var parts = data.split('|');
						j('#sponser').val(parts[1]);
						j('#upload').click();
						updateTips( parts[0] );
						/*j( "#name" ).val('');
						j( "#email" ).val('');
						j( "#contact" ).val('');
						j( "#description" ).val('');
						j( "#banner" ).val('');*/
					}
					});
					//j( this ).dialog( "close" );
				}
			},
			Cancel: function() 
			{
				j( this ).dialog( "close" );
			}
			},
			close: function() 
			{
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
			});
	
			j('td[id="create-user"]').click(function() 
			{
				j('td[id="create-user"]').removeClass('selected');
				j(this).addClass('selected');
				j( "#dialog-form" ).dialog( "open" );
				j( "#basicTable" ).selectable();
			});
		
	});
	
	
	
			
			
});



