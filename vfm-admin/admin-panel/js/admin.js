 /**
 * bootstrap-multiselect.js
 * https://github.com/davidstutz/bootstrap-multiselect
 *
 * Copyright 2012 - 2014 David Stutz
 *
 * Dual licensed under the BSD-3-Clause and the Apache License, Version 2.0.
 */
!function(e){"use strict";function t(e){return ko.isObservable(e)&&!(e.destroyAll===undefined)}function n(t,n){this.options=this.mergeOptions(n);this.$select=e(t);this.originalOptions=this.$select.clone()[0].options;this.query="";this.searchTimeout=null;this.options.multiple=this.$select.attr("multiple")==="multiple";this.options.onChange=e.proxy(this.options.onChange,this);this.options.onDropdownShow=e.proxy(this.options.onDropdownShow,this);this.options.onDropdownHide=e.proxy(this.options.onDropdownHide,this);this.buildContainer();this.buildButton();this.buildSelectAll();this.buildDropdown();this.buildDropdownOptions();this.buildFilter();this.updateButtonText();this.updateSelectAll();this.$select.hide().after(this.$container)}if(typeof ko!=="undefined"&&ko.bindingHandlers&&!ko.bindingHandlers.multiselect){ko.bindingHandlers.multiselect={init:function(n,r,i,s,o){var u=i().selectedOptions,a=ko.utils.unwrapObservable(r());e(n).multiselect(a);if(t(u)){u.subscribe(function(t){var r=[],i=[];t.forEach(function(e){switch(e.status){case"added":r.push(e.value);break;case"deleted":i.push(e.value);break}});if(r.length>0){e(n).multiselect("select",r)}if(i.length>0){e(n).multiselect("deselect",i)}},null,"arrayChange")}},update:function(n,r,i,s,o){var u=i().options,a=e(n).data("multiselect"),f=ko.utils.unwrapObservable(r());if(t(u)){u.subscribe(function(t){e(n).multiselect("rebuild")})}if(!a){e(n).multiselect(f)}else{a.updateOriginalOptions()}}}}n.prototype={defaults:{buttonText:function(t,n){if(t.length===0){return this.nonSelectedText+' <b class="caret"></b>'}else{if(t.length>this.numberDisplayed){return t.length+" "+this.nSelectedText+' <b class="caret"></b>'}else{var r="";t.each(function(){var t=e(this).attr("label")!==undefined?e(this).attr("label"):e(this).html();r+=t+", "});return r.substr(0,r.length-2)+' <b class="caret"></b>'}}},buttonTitle:function(t,n){if(t.length===0){return this.nonSelectedText}else{var r="";t.each(function(){r+=e(this).text()+", "});return r.substr(0,r.length-2)}},label:function(t){return e(t).attr("label")||e(t).html()},onChange:function(e,t){},onDropdownShow:function(e){},onDropdownHide:function(e){},buttonClass:"btn btn-default",dropRight:false,selectedClass:"active",buttonWidth:"auto",buttonContainer:'<div class="btn-group" />',maxHeight:false,checkboxName:"multiselect",includeSelectAllOption:false,includeSelectAllIfMoreThan:0,selectAllText:" Select all",selectAllValue:"multiselect-all",enableFiltering:false,enableFilteringIfMoreThan:0,enableCaseInsensitiveFiltering:false,filterPlaceholder:"Search",filterBehavior:"text",preventInputChangeEvent:false,nonSelectedText:"None selected",nSelectedText:"selected",numberDisplayed:3,templates:{button:'<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"></button>',ul:'<ul class="multiselect-container dropdown-menu"></ul>',filter:'<div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span><input class="form-control multiselect-search" type="text"></div>',li:'<li><a href="javascript:void(0);"><label></label></a></li>',divider:'<li class="divider"></li>',liGroup:'<li><label class="multiselect-group"></label></li>'}},constructor:n,buildContainer:function(){this.$container=e(this.options.buttonContainer);this.$container.on("show.bs.dropdown",this.options.onDropdownShow);this.$container.on("hide.bs.dropdown",this.options.onDropdownHide)},buildButton:function(){this.$button=e(this.options.templates.button).addClass(this.options.buttonClass);if(this.$select.prop("disabled")){this.disable()}else{this.enable()}if(this.options.buttonWidth&&this.options.buttonWidth!=="auto"){this.$button.css({width:this.options.buttonWidth})}var t=this.$select.attr("tabindex");if(t){this.$button.attr("tabindex",t)}this.$container.prepend(this.$button)},buildDropdown:function(){this.$ul=e(this.options.templates.ul);if(this.options.dropRight){this.$ul.addClass("pull-right")}if(this.options.maxHeight){this.$ul.css({"max-height":this.options.maxHeight+"px","overflow-y":"auto","overflow-x":"hidden"})}this.$container.append(this.$ul)},buildDropdownOptions:function(){this.$select.children().each(e.proxy(function(t,n){var r=e(n).prop("tagName").toLowerCase();if(r==="optgroup"){this.createOptgroup(n)}else if(r==="option"){if(e(n).data("role")==="divider"){this.createDivider()}else{this.createOptionValue(n)}}},this));e("li input",this.$ul).on("change",e.proxy(function(t){var n=e(t.target);var r=n.prop("checked")||false;var i=n.val()===this.options.selectAllValue;if(this.options.selectedClass){if(r){n.parents("li").addClass(this.options.selectedClass)}else{n.parents("li").removeClass(this.options.selectedClass)}}var s=n.val();var o=this.getOptionByValue(s);var u=e("option",this.$select).not(o);var a=e("input",this.$container).not(n);if(i){var f=[];var l=e('li input[value!="'+this.options.selectAllValue+'"][data-role!="divider"]',this.$ul).filter(":visible");for(var c=0,h=l.length;c<h;c++){f.push(l[c].value)}if(r){this.select(f)}else{this.deselect(f)}}if(r){o.prop("selected",true);if(this.options.multiple){o.prop("selected",true)}else{if(this.options.selectedClass){e(a).parents("li").removeClass(this.options.selectedClass)}e(a).prop("checked",false);u.prop("selected",false);this.$button.click()}if(this.options.selectedClass==="active"){u.parents("a").css("outline","")}}else{o.prop("selected",false)}this.$select.change();this.options.onChange(o,r);this.updateButtonText();this.updateSelectAll();if(this.options.preventInputChangeEvent){return false}},this));e("li a",this.$ul).on("touchstart click",function(t){t.stopPropagation();var n=e(t.target);if(t.shiftKey){var r=n.prop("checked")||false;if(r){var i=n.parents("li:last").siblings('li[class="active"]:first');var s=n.parents("li").index();var o=i.index();if(s>o){n.parents("li:last").prevUntil(i).each(function(){e(this).find("input:first").prop("checked",true).trigger("change")})}else{n.parents("li:last").nextUntil(i).each(function(){e(this).find("input:first").prop("checked",true).trigger("change")})}}}n.blur()});this.$container.on("keydown",e.proxy(function(t){if(e('input[type="text"]',this.$container).is(":focus")){return}if((t.keyCode===9||t.keyCode===27)&&this.$container.hasClass("open")){this.$button.click()}else{var n=e(this.$container).find("li:not(.divider):visible a");if(!n.length){return}var r=n.index(n.filter(":focus"));if(t.keyCode===38&&r>0){r--}else if(t.keyCode===40&&r<n.length-1){r++}else if(!~r){r=0}var i=n.eq(r);i.focus();if(t.keyCode===32||t.keyCode===13){var s=i.find("input");s.prop("checked",!s.prop("checked"));s.change()}t.stopPropagation();t.preventDefault()}},this))},createOptionValue:function(t){if(e(t).is(":selected")){e(t).prop("selected",true)}var n=this.options.label(t);var r=e(t).val();var i=this.options.multiple?"checkbox":"radio";var s=e(this.options.templates.li);e("label",s).addClass(i);e("label",s).append('<input type="'+i+'" name="'+this.options.checkboxName+'" />');var o=e(t).prop("selected")||false;var u=e("input",s);u.val(r);if(r===this.options.selectAllValue){u.parent().parent().addClass("multiselect-all")}e("label",s).append(" "+n);this.$ul.append(s);if(e(t).is(":disabled")){u.attr("disabled","disabled").prop("disabled",true).parents("li").addClass("disabled")}u.prop("checked",o);if(o&&this.options.selectedClass){u.parents("li").addClass(this.options.selectedClass)}},createDivider:function(t){var n=e(this.options.templates.divider);this.$ul.append(n)},createOptgroup:function(t){var n=e(t).prop("label");var r=e(this.options.templates.liGroup);e("label",r).text(n);this.$ul.append(r);if(e(t).is(":disabled")){r.addClass("disabled")}e("option",t).each(e.proxy(function(e,t){this.createOptionValue(t)},this))},buildSelectAll:function(){var t=this.hasSelectAll();if(!t&&this.options.includeSelectAllOption&&this.options.multiple&&e('option[data-role!="divider"]',this.$select).length>this.options.includeSelectAllIfMoreThan){if(this.options.includeSelectAllDivider){this.$select.prepend('<option value="" disabled="disabled" data-role="divider">')}this.$select.prepend('<option value="'+this.options.selectAllValue+'">'+this.options.selectAllText+"</option>")}},buildFilter:function(){if(this.options.enableFiltering||this.options.enableCaseInsensitiveFiltering){var t=this.options.enableFilteringIfMoreThan;if(this.$select.find("option").length>=t){this.$filter=e(this.options.templates.filter);e("input",this.$filter).attr("placeholder",this.options.filterPlaceholder);this.$ul.prepend(this.$filter);this.$filter.val(this.query).on("click",function(e){e.stopPropagation()}).on("input keydown",e.proxy(function(t){clearTimeout(this.searchTimeout);this.searchTimeout=this.asyncFunction(e.proxy(function(){if(this.query!==t.target.value){this.query=t.target.value;e.each(e("li",this.$ul),e.proxy(function(t,n){var r=e("input",n).val();var i=e("label",n).text();var s="";if(this.options.filterBehavior==="text"){s=i}else if(this.options.filterBehavior==="value"){s=r}else if(this.options.filterBehavior==="both"){s=i+"\n"+r}if(r!==this.options.selectAllValue&&i){var o=false;if(this.options.enableCaseInsensitiveFiltering&&s.toLowerCase().indexOf(this.query.toLowerCase())>-1){o=true}else if(s.indexOf(this.query)>-1){o=true}if(o){e(n).show()}else{e(n).hide()}}},this))}},this),300,this)},this))}}},destroy:function(){this.$container.remove();this.$select.show();this.$select.data("multiselect",null)},refresh:function(){e("option",this.$select).each(e.proxy(function(t,n){var r=e("li input",this.$ul).filter(function(){return e(this).val()===e(n).val()});if(e(n).is(":selected")){r.prop("checked",true);if(this.options.selectedClass){r.parents("li").addClass(this.options.selectedClass)}}else{r.prop("checked",false);if(this.options.selectedClass){r.parents("li").removeClass(this.options.selectedClass)}}if(e(n).is(":disabled")){r.attr("disabled","disabled").prop("disabled",true).parents("li").addClass("disabled")}else{r.prop("disabled",false).parents("li").removeClass("disabled")}},this));this.updateButtonText();this.updateSelectAll()},select:function(t){if(!e.isArray(t)){t=[t]}for(var n=0;n<t.length;n++){var r=t[n];var i=this.getOptionByValue(r);var s=this.getInputByValue(r);if(this.options.selectedClass){s.parents("li").addClass(this.options.selectedClass)}s.prop("checked",true);i.prop("selected",true)}this.updateButtonText()},clearSelection:function(){var e=this.getSelected();if(e.length){var t=[];for(var n=0;n<e.length;n=n+1){t.push(e[n].value)}this.deselect(t);this.$select.change()}},deselect:function(t){if(!e.isArray(t)){t=[t]}for(var n=0;n<t.length;n++){var r=t[n];var i=this.getOptionByValue(r);var s=this.getInputByValue(r);if(this.options.selectedClass){s.parents("li").removeClass(this.options.selectedClass)}s.prop("checked",false);i.prop("selected",false)}this.updateButtonText()},rebuild:function(){this.$ul.html("");e('option[value="'+this.options.selectAllValue+'"]',this.$select).remove();this.options.multiple=this.$select.attr("multiple")==="multiple";this.buildSelectAll();this.buildDropdownOptions();this.buildFilter();this.updateButtonText();this.updateSelectAll()},dataprovider:function(e){var t="";e.forEach(function(e){t+='<option value="'+e.value+'">'+e.label+"</option>"});this.$select.html(t);this.rebuild()},enable:function(){this.$select.prop("disabled",false);this.$button.prop("disabled",false).removeClass("disabled")},disable:function(){this.$select.prop("disabled",true);this.$button.prop("disabled",true).addClass("disabled")},setOptions:function(e){this.options=this.mergeOptions(e)},mergeOptions:function(t){return e.extend(true,{},this.defaults,t)},hasSelectAll:function(){return e('option[value="'+this.options.selectAllValue+'"]',this.$select).length>0},updateSelectAll:function(){if(this.hasSelectAll()){var t=this.getSelected();if(t.length===e("option:not([data-role=divider])",this.$select).length-1){this.select(this.options.selectAllValue)}else{this.deselect(this.options.selectAllValue)}}},updateButtonText:function(){var t=this.getSelected();e("button",this.$container).html(this.options.buttonText(t,this.$select));e("button",this.$container).attr("title",this.options.buttonTitle(t,this.$select))},getSelected:function(){return e('option[value!="'+this.options.selectAllValue+'"]:selected',this.$select).filter(function(){return e(this).prop("selected")})},getOptionByValue:function(t){var n=e("option",this.$select);var r=t.toString();for(var i=0;i<n.length;i=i+1){var s=n[i];if(s.value===r){return e(s)}}},getInputByValue:function(t){var n=e("li input",this.$ul);var r=t.toString();for(var i=0;i<n.length;i=i+1){var s=n[i];if(s.value===r){return e(s)}}},updateOriginalOptions:function(){this.originalOptions=this.$select.clone()[0].options},asyncFunction:function(e,t,n){var r=Array.prototype.slice.call(arguments,3);return setTimeout(function(){e.apply(n||window,r)},t)}};e.fn.multiselect=function(t,r){return this.each(function(){var i=e(this).data("multiselect");var s=typeof t==="object"&&t;if(!i){i=new n(this,s);e(this).data("multiselect",i)}if(typeof t==="string"){i[t](r);if(t==="destroy"){e(this).data("multiselect",false)}}})};e.fn.multiselect.Constructor=n;e(function(){e("select[data-role=multiselect]").multiselect()})}(window.jQuery);

/*! VFM - veno file manager main administration functions
 * ================
 *
 * @Author  Nicola Franchini
 * @Support <http://www.veno.es>
 * @Email   <support@veno.it>
 * @version 2.6.3
 * @license Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 */

//
// Modal window 
//
$(document).on('click', '.closealert', function () {
    $(this).parent().fadeOut();
});

$(document).on('click', '.vfmclose, .vfmx', function () {
    $(".modal").fadeOut();
    $('.overlay').fadeOut(function () {
        $(this).remove();
    });
});

$(document).on('click', '.overlay', function () {
    $(".modal").fadeOut();
    $(this).fadeOut(function () {
        $(this).remove();
    });
});

//
// toggle allow/reject extensions
//
function switchExtensions(){
    $('.toggle-extensions').each(function(){
        if($(this).find('.togglext').is(':checked')) {
             $(this).closest('.toggle-extensions').next().slideDown();
             $(this).find('.togglabel').addClass('bold');
        } else {
             $(this).closest('.toggle-extensions').next().slideUp();
             $(this).find('.togglabel').removeClass('bold');
        }
    });
}

//
// Start multiselects with translated options
//
function multiselectWithOptions(selectall, selected, available){
    $('.assignfolder').multiselect({
        buttonWidth: '100%',
        selectAllText: selectall,
        maxHeight: 300,
        enableFiltering: true,
        enableFilteringIfMoreThan: 10,
        filterPlaceholder: '',
        includeSelectAllOption: true,
        includeSelectAllIfMoreThan: 10,
        numberDisplayed: 1,
        nSelectedText: selected,
        buttonContainer: '<div class="btn-group btn-block" />',
        nonSelectedText : available,
        templates: {
        filter: '<div class="input-group"><span class="input-group-addon input-sm"><i class="glyphicon glyphicon-search"></i></span><input class="form-control multiselect-search input-sm" type="text"></div>'
        }
    });
}

//
// setup user panel in admin area
//
$(document).on('click', '.usrblock', function(e) {
    e.preventDefault();

    $('#modaluser .getuser').val('');
    var username = '';
    $(this).find('.send-userdata').each(function(){
        console.log($(this).data('key') + '-' + $(this).val());
        var key = $(this).data('key');
        $('#modaluser .getuser-'+key).val($(this).val());
        if (key === 'name') {
            username = $(this).val();
        }
    });

    $("#modaluser .modal-title .modalusername").html(username);
    $("#r-userpassnew").val('');

    var data = [];
    var hiddenfolders = $(this).find(".s-userfolders");
    hiddenfolders.each(function(){
        data.push($(this).val());
    });

    $("#r-userfolders").val(data);
    $(".coolselect").multiselect('refresh');
    $(".assignfolder").multiselect('refresh');
    
    if ($('#r-userfolders').val()){
        $('#modaluser .userquota').show();
    } else {
        $('#modaluser .userquota').hide();
    }
});

//
// Show / hide user quota menu when dropdown menu changes
//
function showHideQuota(subject){
    var parente = subject.closest('.row').find('.assignnew');
    if (subject.val() || parente.val()) {
        subject.closest('.row').next().find('.userquota').fadeIn();
    } else {
        subject.closest('.row').next().find('.userquota').fadeOut();
    }
}

$(document).on('change', '.assignfolder', function() {
    showHideQuota($(this));
});

//
// confirm user deletion
//
$(document).on('click', '.remove', function(e) {
    //e.preventDefault();
    var todelete = $(this).closest(".removegroup").find(".deleteme").val();
    var answer = confirm('Are you sure you want to delete: ' + todelete + '?')
    if (answer == true) {
        $(".remove").find(".delme").val(todelete);
    }
    return answer;

});

//
// confirm language deletion
//
$(document).on('click', '.delete', function() {
    var answer = confirm('Are you sure you want entirely to delete this language?');
    return answer;
});

//
// Upload custom logo
//
$(document).on('change', '.btn-file :file', function() {
    var input = $(this),
    numFiles = input.get(0).files ? input.get(0).files.length : 1,
    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});

//
// Show / Hide New User notification checkbox
//
$('.newusermail').keyup(function() {
    if($(this).val().length>0){
        $('.usernotif').fadeIn();
    } else {
        $('.usernotif').fadeOut();
   }
});

//
// Update background for analytics panel
//
$(document).on('change', '.checkstats', function() {
    if(this.checked) {
        $(this).closest('.info-box').addClass('bg-green').removeClass('bg-green-active');
    } else {
        $(this).closest('.info-box').addClass('bg-green-active').removeClass('bg-green');
    }
});

//
// Update background for registration panel
//
$(document).on('change', '.checkregs', function() {
    if(this.checked) {
        $(this).closest('.info-box').addClass('bg-aqua').removeClass('bg-aqua-active');
    } else {
        $(this).closest('.info-box').addClass('bg-aqua-active').removeClass('bg-aqua');
    }
});

//
// Update adminstration color scheme on the fly
//
$(document).on('change', '.adminscheme input', function() {

    $('.adminscheme input').each(function(){
        var removeskin = $(this).val();
        $('body').removeClass('skin-'+removeskin);
    })
    $('.minilayout').removeClass('active');

    if(this.checked) {
        var addskin = $(this).val();
        $('body').addClass('skin-'+addskin);
        $(this).closest('.minilayout').addClass('active');
    }
});
//
// switch header logo alignment on the fly
//
$(document).on('change', '.select-logo-alignment input:radio', function() {
    var value = 'text-left';
    switch($(this).val()) {
        case 'left':
            value = 'text-left';
            break;
        case 'center':
            value = 'text-center';
            break;
        case 'right':
            value = 'text-right';
            break;

        default:
            value = 'text-left';
    }
    $('.placeheader').removeClass('text-left').removeClass('text-center').removeClass('text-right').addClass(value);
});

//
// toggle allow/reject extensions on the fly
//
$(document).on('change', '.togglext', function() {
    switchExtensions();
});

//
// change individual progress bar color on the fly
//
function updateSingleBar($newclass) {
    $('.progress-single .progress-bar').removeClass().addClass('progress-bar').addClass($newclass);
}

function updateDefaultBar($newclass) {
    $('.first-progress').data('color', $newclass);
    $('.first-progress').next().find('.progress-bar').removeClass().addClass('progress-bar').addClass($newclass);

    if ($('.first-progress').is(':checked')) {
        updateSingleBar($newclass);
    }
}
$(document).on('change', '.pro input:radio', function() {
    var newclass = $(this).data('color');
    updateSingleBar(newclass);
});

$(document).on('change', '.skinswitch', function() {
    var newclass = $(this).find(':selected').data('color');
    updateDefaultBar(newclass);
});

function checkFixedlabel(){
    var scroll = $(window).scrollTop();
    var lab = $('.fixed-label');
    var labw = lab.width();
    if (scroll > 180) {
            $('.fixed-label').css('right', 0);
    } else {
        $('.fixed-label').css('right', -labw);
    }
}

$(window).scroll(function (event) {
    checkFixedlabel()
});
 /** 
 * ****************************************
 * Veno file manager Admin DocReady calls
 * ****************************************
 */
$(document).ready(function () {

    checkFixedlabel()
    //
    // toggle allow/reject extensions
    //
    switchExtensions();

    //
    // toggle percent % in progress bar on the fly
    //
    $("#percent").change(function() {
        var $input = $(this);
        if ($input.is( ":checked" )) {
            $('.radio').addClass('fullp');
        } else {
            $('.radio').removeClass('fullp');
        }
    }).change();

    //
    // toggle users panel
    //
    $('.toggle').each(function(){
        if (!$(this).find('input[type=checkbox]').prop('checked')){
            $(this).closest('.toggle').next().slideToggle();
        }
    });
    $('.toggle').find('input[type=checkbox]').change(function(){
        console.log('ciao');
        $(this).closest('.toggle').next().slideToggle();
    });

    $('.toggle-reverse').each(function(){
        if ($(this).find('input[type=checkbox]').prop('checked')){
            $(this).closest('.toggle-reverse').prev().slideToggle();
        }
    });
    $('.toggle-reverse').find('input[type=checkbox]').change(function(){
        $(this).closest('.toggle-reverse').prev().slideToggle();
    });

    //
    // activate tooltips
    //
    $('.tooltipper').tooltip();

    //
    // info (?) popover 
    //
    $('.pop').popover();

    //
    // logo uploader
    //
    $('.pop').popover();
    $('.btn-file :file').on('fileselect', function (event, numFiles, label) {
        var input = $(this).parents('.input-group').find(':text');  
        input.val(label);
    });

    //
    // Show / hide user quota menu when new user folder input text changes
    //
    // stupid IE < 9
    var propertyChange = false;
    $(".assignnew").on("propertychange", function(e) {
        if (e.originalEvent.propertyName == "value") {
            
            var parente = $(this).closest('.row').find('.assignfolder');
            if ($(this).val() || parente.val()) {
                $(this).closest('.row').next().find('.userquota').fadeIn();
            } else {
                $(this).closest('.row').next().find('.userquota').fadeOut();
            }
        }
    });

    // standard mode
    $(".assignnew").on("input", function() {
        if (!propertyChange) {
            $(".assignnew").unbind("propertychange");
            propertyChange = true;
        }
        
        var parente = $(this).closest('.row').find('.assignfolder');
        if ($(this).val() || parente.val()) {
            $(this).closest('.row').next().find('.userquota').fadeIn();
        } else {
            $(this).closest('.row').next().find('.userquota').fadeOut();
        }
    });
    $('#newuserpanel .userquota').hide();

    //
    // set z-index to selected cooldrop
    //
    $('.cooldrop, .assignfolder').click(function(){
        $('.cooldropgroup').css('z-index', '99');
        $(this).closest('.cooldropgroup').css('z-index', '100');
    });

    //
    // Start multiselects
    //
    $('.coolselect').multiselect({
        buttonWidth: '100%',
        buttonContainer: '<div class="btn-group btn-block" />'
    });

    //
    // Start summernote WYSIWYG rich text editor for description
    //
    $('.summernote').summernote({
        height: 50,
        minHeight: 20,
        maxHeight: 150,
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['paragraph']],
            ['inset', ['link']],
            ['misc', ['codeview']],
        ]
    });

    //
    // Update background for analytics panel
    //
    if($('.checkstats').prop('checked')) {
        $('.checkstats').closest('.info-box').addClass('bg-green').removeClass('bg-green-active');
    } else {
        $('.checkstats').closest('.info-box').addClass('bg-green-active').removeClass('bg-green');
    }

    //
    // Update background for registration panel
    //
    if($('.checkregs').prop('checked')) {
        $('.checkregs').closest('.info-box').addClass('bg-aqua').removeClass('bg-aqua-active');
    } else {
        $('.checkregs').closest('.info-box').addClass('bg-aqua-active').removeClass('bg-aqua');
    }

    //
    // Smooth scroll admin area
    //
    $('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
            if (target.length) {
                $('html,body').animate({
                  scrollTop: target.offset().top
                }, 800);
                return false;
            }
        }
    });
});