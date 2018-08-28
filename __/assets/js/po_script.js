//Editable Select
+(function ($) {
	// jQuery Editable Select
	EditableSelect = function (select, options) {
		var that     = this;
		
		this.options = options;
		this.$select = $(select);
		this.$input  = $('<input type="text" autocomplete="off">');
		this.$list   = $('<ul class="es-list">');
		this.utility = new EditableSelectUtility(this);
		
		if (['focus', 'manual'].indexOf(this.options.trigger) < 0) this.options.trigger = 'focus';
		if (['default', 'fade', 'slide'].indexOf(this.options.effects) < 0) this.options.effects = 'default';
		if (isNaN(this.options.duration) && ['fast', 'slow'].indexOf(this.options.duration) < 0) this.options.duration = 'fast';
		
		// create text input
		this.$select.replaceWith(this.$input);
		this.$list.appendTo(this.options.appendTo || this.$input.parent());
		
		// initalization
		this.utility.initialize();
		this.utility.initializeList();
		this.utility.initializeInput();
		this.utility.trigger('created');
	}
	EditableSelect.DEFAULTS = { filter: true, effects: 'default', duration: 'fast', trigger: 'focus' };
	EditableSelect.prototype.filter = function () {
		var hiddens = 0;
		var search  = this.$input.val().toLowerCase().trim();
		
		this.$list.find('li').addClass('es-visible').show();
		if (this.options.filter) {
			hiddens = this.$list.find('li').filter(function (i, li) { return $(li).text().toLowerCase().indexOf(search) < 0; }).hide().removeClass('es-visible').length;
			if (this.$list.find('li').length == hiddens) this.hide();
		}
	};
	EditableSelect.prototype.show = function () {
		this.$list.css({
			top:   this.$input.position().top + this.$input.outerHeight() - 1,
			left:  this.$input.position().left,
			width: this.$input.outerWidth()
		});
		
		if (!this.$list.is(':visible') && this.$list.find('li.es-visible').length > 0) {
			var fns = { default: 'show', fade: 'fadeIn', slide: 'slideDown' };
			var fn  = fns[this.options.effects];
			
			this.utility.trigger('show');
			this.$input.addClass('open');
			this.$list[fn](this.options.duration, $.proxy(this.utility.trigger, this.utility, 'shown'));
		}
	};
	EditableSelect.prototype.hide = function () {
		var fns = { default: 'hide', fade: 'fadeOut', slide: 'slideUp' };
		var fn  = fns[this.options.effects];
		
		this.utility.trigger('hide');
		this.$input.removeClass('open');
		this.$list[fn](this.options.duration, $.proxy(this.utility.trigger, this.utility, 'hidden'));
	};
	EditableSelect.prototype.select = function ($li) {
		if (!this.$list.has($li) || !$li.is('li.es-visible:not([disabled])')) return;
		this.$input.val($li.text());
		if (this.options.filter) this.hide();
		this.filter();
		this.utility.trigger('select', $li);
	};
	EditableSelect.prototype.add = function (text, index, attrs, data) {
		var $li     = $('<li>').html(text);
		var $option = $('<option>').text(text);
		var last    = this.$list.find('li').length;
		
		if (isNaN(index)) index = last;
		else index = Math.min(Math.max(0, index), last);
		if (index == 0) {
		  this.$list.prepend($li);
		  this.$select.prepend($option);
		} else {
		  this.$list.find('li').eq(index - 1).after($li);
		  this.$select.find('option').eq(index - 1).after($option);
		}
		this.utility.setAttributes($li, attrs, data);
		this.utility.setAttributes($option, attrs, data);
		this.filter();
	};
	EditableSelect.prototype.remove = function (index) {
		var last = this.$list.find('li').length;
		
		if (isNaN(index)) index = last;
		else index = Math.min(Math.max(0, index), last - 1);
		this.$list.find('li').eq(index).remove();
		this.$select.find('option').eq(index).remove();
		this.filter();
	};
	EditableSelect.prototype.clear = function () {
		this.$list.find('li').remove();
		this.$select.find('option').remove();
		this.filter();
	};
	EditableSelect.prototype.destroy = function () {
		this.$list.off('mousemove mousedown mouseup');
		this.$input.off('focus blur input keydown');
		this.$input.replaceWith(this.$select);
		this.$list.remove();
		this.$select.removeData('editable-select');
	};
	
	// Utility
	EditableSelectUtility = function (es) {
		this.es = es;
	}
	EditableSelectUtility.prototype.initialize = function () {
		var that = this;
		that.setAttributes(that.es.$input, that.es.$select[0].attributes, that.es.$select.data());
		that.es.$input.addClass('es-input').data('editable-select', that.es);
		that.es.$select.find('option').each(function (i, option) {
			var $option = $(option).remove();
			that.es.add($option.text(), i, option.attributes, $option.data());
			if ($option.attr('selected')) that.es.$input.val($option.text());
		});
		that.es.filter();
	};
	EditableSelectUtility.prototype.initializeList = function () {
		var that = this;
		that.es.$list
			.on('mousemove', 'li:not([disabled])', function () {
				that.es.$list.find('.selected').removeClass('selected');
				$(this).addClass('selected');
			})
			.on('mousedown', 'li', function (e) {
				if ($(this).is('[disabled]')) e.preventDefault();
				else that.es.select($(this));
			})
			.on('mouseup', function () {
				that.es.$list.find('li.selected').removeClass('selected');
			});
	};
	EditableSelectUtility.prototype.initializeInput = function () {
		var that = this;
		switch (this.es.options.trigger) {
			default:
			case 'focus':
				that.es.$input
					.on('focus', $.proxy(that.es.show, that.es))
					.on('blur', $.proxy(that.es.hide, that.es));
				break;
			case 'manual':
				break;
		}
		that.es.$input.on('input keydown', function (e) {
			switch (e.keyCode) {
				case 38: // Up
					var visibles = that.es.$list.find('li.es-visible:not([disabled])');
					var selectedIndex = visibles.index(visibles.filter('li.selected'));
					that.highlight(selectedIndex - 1);
					e.preventDefault();
					break;
				case 40: // Down
					var visibles = that.es.$list.find('li.es-visible:not([disabled])');
					var selectedIndex = visibles.index(visibles.filter('li.selected'));
					that.highlight(selectedIndex + 1);
					e.preventDefault();
					break;
				case 13: // Enter
					if (that.es.$list.is(':visible')) {
						that.es.select(that.es.$list.find('li.selected'));
						e.preventDefault();
					}
					break;
				case 9:  // Tab
				case 27: // Esc
					that.es.hide();
					break;
				default:
					that.es.filter();
					that.highlight(0);
					break;
			}
		});
	};
	EditableSelectUtility.prototype.highlight = function (index) {
		var that = this;
		that.es.show();
		setTimeout(function () {
			var visibles         = that.es.$list.find('li.es-visible');
			var oldSelected      = that.es.$list.find('li.selected').removeClass('selected');
			var oldSelectedIndex = visibles.index(oldSelected);
			
			if (visibles.length > 0) {
				var selectedIndex = (visibles.length + index) % visibles.length;
				var selected      = visibles.eq(selectedIndex);
				var top           = selected.position().top;
				
				selected.addClass('selected');
				if (selectedIndex < oldSelectedIndex && top < 0)
					that.es.$list.scrollTop(that.es.$list.scrollTop() + top);
				if (selectedIndex > oldSelectedIndex && top + selected.outerHeight() > that.es.$list.outerHeight())
					that.es.$list.scrollTop(that.es.$list.scrollTop() + selected.outerHeight() + 2 * (top - that.es.$list.outerHeight()));
			}
		});
	};
	EditableSelectUtility.prototype.setAttributes = function ($element, attrs, data) {
		$.each(attrs || {}, function (i, attr) { $element.attr(attr.name, attr.value); });
		$element.data(data);
	};
	EditableSelectUtility.prototype.trigger = function (event) {
		var params = Array.prototype.slice.call(arguments, 1);
		var args   = [event + '.editable-select'];
		args.push(params);
		this.es.$select.trigger.apply(this.es.$select, args);
		this.es.$input.trigger.apply(this.es.$input, args);
	};
	
	// Plugin
	Plugin = function (option) {
		var args = Array.prototype.slice.call(arguments, 1);
		return this.each(function () {
			var $this   = $(this);
			var data    = $this.data('editable-select');
			var options = $.extend({}, EditableSelect.DEFAULTS, $this.data(), typeof option == 'object' && option);
			
			if (!data) data = new EditableSelect(this, options);
			if (typeof option == 'string') data[option].apply(data, args);
		});
	}
	$.fn.editableSelect             = Plugin;
	$.fn.editableSelect.Constructor = EditableSelect;
	
})(jQuery);

$(document).ready(function(){ 
    var count = 0;

    $('#item_dialog').dialog({
     autoOpen:false,
     width:400
    });
   
    $('#add_item').click(function(){
       clearAddDialog();
       $('#item_dialog').dialog('option', 'title', 'Add Item');
       $('#item_dialog').dialog('open');
       $('#item_type').hide();
       $('#item_color').hide();
       $('#item_custom').hide();
       $('#save').text('Save');
    });
   
    $('input[name="product"]').click(function(){
       $('#item_type').show();
       $('#item_color').show();
       fetch_colors();
       $('#item_custom').hide();
       $('input[name="type"]').prop('checked',false);
    });
    
    $('#csm').click(function(){
       $('#item_custom').show();
       type=$('input[name="product"]:checked').val();
       userid=$('input[id="uid"]').val();
       fetch_products(type, userid);
    });

    $('#pln').click(function(){
       $('#item_custom').hide();
    });
   
   $(document).on('click', '.view_details', function(){
     var row_id = $(this).attr("id");
     var first_name = $('#first_name'+row_id+'').val();
     var last_name = $('#last_name'+row_id+'').val();
     $('#first_name').val(first_name);
     $('#last_name').val(last_name);
     $('#save').text('Edit');
     $('#hidden_row_id').val(row_id);
     $('#item_dialog').dialog('option', 'title', 'Edit Item');
     $('#item_dialog').dialog('open');
    });
   
    $(document).on('click', '.remove_details', function(){
     var row_id = $(this).attr("id");
     if(confirm("Are you sure you want to remove this row data?"))
     {
      $('#row_'+row_id+'').remove();
     }
     else
     {
      return false;
     }
    });
    
    $('#save').click(function(){
       var err=0;
       var color = "";
       var custom = "";
       var product = "";
       var type = "";
       var quantity = "";
       var note   = "";
   
       if($("input[name='product']").is(":checked")==false){
           $('#item_product').addClass('has-error');
           err+=1;
       }else{
           if($("input[name='product']:checked").val()=='HH')
           product = "Helmet Holder";
           else
           product = "Ticket Holder";
       }
   
       if($("input[name='type']").is(":checked")==false){
           $('#item_type').addClass('has-error');
           err+=1;
       }else{
           type = $("input[name='type']:checked").val();
       }
       if($("input[name='quantity']").val()=="" || $("input[name='quantity']").val()<1){
           $('#item_quantity').addClass('has-error');
           err+=1;
       }else{
           quantity = $("input[name='quantity']").val();
       }

       if(!$('#custom').val() && $("input[name='type']:checked").val()=="custom") { 
            $('#item_custom').addClass('has-error');
            err+=1;
       }

       if(err!=0){
           $('#dialog_warn').text('Please fill-in required fields');
       }
       note = $('textarea[name="note"]').val();
   
       if (err==0){
           if($('#save').text() == 'Save'){
               count = count + 1;
               color  = $("#colors option:selected").val();
               custom = $("#custom option:selected").val();
               product = $("input[name='product']:checked").val();
               type   = $("input[name='type']:checked").val();
               quantity = $("input[name='quantity']").val();
               note     = $("textarea[name='note']").val();
               if(product=="HH")       prodname = "Helmet Holder";
               else if(product=="TH")  prodname = "Ticket Holder";

               output = '<tr id="row_'+count+'">';
               output += '<td></td>';
               if(type=='custom'){
                   output += '<td><b>'+prodname+' - '+ $("#custom option:selected").text() +'</b>';
               }
               else
                   output += '<td><b>'+prodname+' - '+type+'</b>';
               if(note=="")
                   output += "";
               else
                   output += '<br/><span>'+note+'</span>'           
               
               
               output += '</td>';
               output += '<td>'+ $("#colors option:selected").text() +'</td>';
               output += '<td>'+quantity+'</td>';
               //output += '<td><button type="button" name="view_details" class="btn btn-warning btn-xs view_details" id="'+count+'">View</button>';

               output += '<td><button type="button" name="remove_details" class="btn btn-danger btn-xs remove_details" id="'+count+'">Remove</button>';
               output += '<input type="hidden" name="color[]" value="' + color + '" />';
               output += '<input type="hidden" name="custom[]" value="' + custom + '" />';
               output += '<input type="hidden" name="product[]" value="' + product + '" />';
               output += '<input type="hidden" name="type[]" value="' + type + '" />';
               output += '<input type="hidden" name="quantity[]" value="' + quantity + '" />';
               output += '<input type="hidden" name="note[]" value="' + note + '" />';
               output += '</td>';
               $('#po_table').append(output);
               
               /*
               count = count + 1;
               output = '<div class="row row_'+count+'">';
               output += '<div class="col-md-1">';
               output += '<span class="glyphicon glyphicon-picture"></span>';
               output += '</div>';
               output += '<div class="col-md-7">';
               output += '<div>title</div>';
               output += '<div>type - custom</div>';
               output += '</div>';
               output += '<div class="col-md-2">';
               output += '<div>Qty: 1</div>';
               output += '</div>';
               output += '<div class="col-md-2">';
               output += '<div><a href="#" class="btn btn-warning btn-xs">View</a><a href="#" class="btn btn-danger btn-xs">Delete</a></div>';
               output += '</div>';
               output += '</div>';
               $('#po_table').append(output);
               */    
                   /*output = '<tr id="row_'+count+'">';
                   output += '<td>'+first_name+' <input type="hidden" name="hidden_first_name[]" id="first_name'+count+'" class="first_name" value="'+first_name+'" /></td>';
                   output += '<td>'+last_name+' <input type="hidden" name="hidden_last_name[]" id="last_name'+count+'" value="'+last_name+'" /></td>';
                   output += '<td><button type="button" name="view_details" class="btn btn-warning btn-xs view_details" id="'+count+'">View</button></td>';
                   output += '<td><button type="button" name="remove_details" class="btn btn-danger btn-xs remove_details" id="'+count+'">Remove</button></td>';
                   output += '</tr>';
                   */


               }
               else
               {
                   var row_id = $('#hidden_row_id').val();
                   output = '<td>'+first_name+' <input type="hidden" name="hidden_first_name[]" id="first_name'+row_id+'" class="first_name" value="'+first_name+'" /></td>';
                   output += '<td>'+last_name+' <input type="hidden" name="hidden_last_name[]" id="last_name'+row_id+'" value="'+last_name+'" /></td>';
                   output += '<td><button type="button" name="view_details" class="btn btn-warning btn-xs view_details" id="'+row_id+'">View</button></td>';
                   output += '<td><button type="button" name="remove_details" class="btn btn-danger btn-xs remove_details" id="'+row_id+'">Remove</button></td>';
                   $('#row_'+row_id+'').html(output);
               }
               $('#item_dialog').dialog('close');
               $('#addItemModal').modal('toggle');
       }
    });
   
   
   function clearAddDialog(){
       $('input[name="product"]').prop('checked',false);
       $('input[name="type"]').prop('checked',false);
       $('select[name="color"]').empty();
       $('select[name="custom"]').empty();
       $('input[name="quantity"]').val('');
       $('.form-group').removeClass('has-error');
       $('#dialog_warn').text('');
       $("textarea[name='note']").val('');
   }
   
   function fetch_products(type, id){
     $.ajax({
        url:"objects/functions/fetch_productitems.php",
        method:"POST",
        data:{type:type, id:id},
        success:function(data){
            $('#custom').html(data);
            //$('#item_custom .col-sm-9 div').html(data);
        }
     })
   }
   
   function fetch_colors(type){
    $.ajax({
        url:"objects/functions/fetch_colors.php",
        method:"POST",
        success:function(data){
            $('#colors').html(data);
        }
    })
   }
   
});


