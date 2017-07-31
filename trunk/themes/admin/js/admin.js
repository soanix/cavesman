function makeSlug(str) {
  str = str.replace(/^\s+|\s+$/g, ''); // trim
  str = str.toLowerCase();

  // remove accents, swap ñ for n, etc
  var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
  var to   = "aaaaaeeeeeiiiiooooouuuunc------";
  for (var i=0, l=from.length ; i<l ; i++) {
    str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
  }

  str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
    .replace(/\s+/g, '-') // collapse whitespace and replace by -
    .replace(/-+/g, '-'); // collapse dashes

  return str;
}
$(document).ready(function(){
	$(window).load(function(){
		$("body").addClass("loaded");
		$(".loader-container").addClass("hidden");
	});
	$( document ).ajaxSuccess(function( event, xhr, settings ) {
	  	data = xhr.responseJSON;
		if(data && typeof data['error'] != "undefined"){
			bootbox.alert({
			  size: "small",
			  title: "Atención",
			  message: data["error"],
			  onEscape: true,
			  callback: function(){ /* your callback code */ }
			});
		}
	});
    /* GLOBAL */
    var inAjax = false;
     $('.datepicker').datepicker({
        format: "dd-mm-yyyy",
        maxViewMode: 0,
        todayBtn: "linked",
        clearBtn: true,
        language: "es",
        calendarWeeks: true,
        autoclose: true,
        toggleActive: true
    });
	$("a.pop").click(function(e){
		e.preventDefault();
		$a = $(this);
		$.fancybox({
		   helpers: {
			   overlay: {
				   locked: false
			   }
		   },
			 "type": "iframe",
		   'showNavArrows' : true,
		   "href" : $a.attr("href"),
		   'padding' : 0,
		   'autoScale' : true,
		   'autoSize' : true,
		   afterClose : function() {

		   },
		   onCancel: function(){

		   }
	   });
	});
	tinyMCE.init({
        // General options
		selector: "textarea.editor",
		theme: "modern",
		skin: 'lightgray',
		link_assume_external_targets: true,
		relative_urls : 0,
		remove_script_host : 0,
		plugins: [
		    'advlist autolink lists link image charmap print preview anchor',
		    'searchreplace visualblocks code fullscreen',
		    'insertdatetime media table contextmenu paste code',
			'textcolor colorpicker'
		  ],
		  toolbar: 'insertfile undo redo | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fullscreen',
		  content_css: [
		    '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
		    '//www.tinymce.com/css/codepen.min.css'
		],
        // Theme options

        // Example content CSS (should be your site CSS)
        content_css : "/cdn/css/editor.css",
	});
	$.expr[":"].contains = $.expr.createPseudo(function(arg) {
	    return function( elem ) {
	        return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
	    };
	})

	$("#user-almacenes-filter").on("keyup", function(){
		value = $(this).val();
		$("#almacenes-list .almacen-item").hide();
		$("#almacenes-list .almacen-item:contains('"+value+"')").show();
	});
	$("#usuarios-filter").on("keyup", function(){
		value = $(this).val();
		$("#usuarios_table tr").hide();
		$("#usuarios_table tr:contains('"+value+"')").show();
	});
	$(this).on("change reload", "#pais_id", function(){
        pais_id = $(this).find("option:selected").val();
        $.ajax({
            url: "/ajax/tools/getRegionsByPais",
            dataType: "JSON",
            type: "POST",
            data: {
                pais_id: pais_id
            },
            success: function(data){
                $("select#region_id").empty().append('<option value="">Selecciona una region</option>');
                $.each(data, function(i, region){
                    $("select#region_id").append('<option value="'+region['region_id']+'">'+region['name']+'</option>');
                });

            }
        })
    });
    $(this).on("change reload", "#region_id", function(){
        region_id = $(this).find("option:selected").val();
        $.ajax({
            url: "/ajax/tools/getProvinciasByRegion",
            dataType: "JSON",
            type: "POST",
            data: {
                region_id: region_id
            },
            success: function(data){
                $("select#provincia_id").empty().append('<option value="">Selecciona una provincia</option>');
                $.each(data, function(i, provincia){
                    $("select#provincia_id").append('<option value="'+provincia['provincia_id']+'">'+provincia['name']+'</option>');
                });

            }
        })
    });
    $(this).on("change reload", "#provincia_id", function(){
        provincia_id = $(this).find("option:selected").val();
        $.ajax({
            url: "/ajax/tools/getComarcasByProvincia",
            dataType: "JSON",
            type: "POST",
            data: {
                provincia_id: provincia_id
            },
            success: function(data){
                $("select#comarca_id").empty().append('<option value="">Selecciona una comarca</option>');
                $.each(data, function(i, comarca){
                    $("select#comarca_id").append('<option value="'+comarca['comarca_id']+'">'+comarca['name']+'</option>');
                });

            }
        });
    });
    $(this).on("change reload", "#comarca_id", function(){
        comarca_id = $(this).find("option:selected").val();
        $.ajax({
            url: "/ajax/tools/getLocalidadesByComarca",
            dataType: "JSON",
            type: "POST",
            data: {
                comarca_id: comarca_id
            },
            success: function(data){
                $("select#localidad_id").empty().append('<option value="">Selecciona una localidad</option>');
                $.each(data, function(i, localidad){
                    $("select#localidad_id").append('<option value="'+localidad['localidad_id']+'">'+localidad['name']+'</option>');
                });

            }
        })
    });
    $(this).on("change reload", "#localidad_id", function(){
        localidad_id = $(this).find("option:selected").val();
        $.ajax({
            url: "/ajax/tools/getParentsByLocalidad",
            dataType: "JSON",
            type: "POST",
            data: {
                localidad_id: localidad_id
            },
            success: function(data){
                var localidad_info = data;
                $.ajax({
                    url: "/ajax/tools/getRegions",
                    dataType: "JSON",
                    type: "POST",
                    success: function(info){
                        $("select#region_id").empty().append('<option value="">Selecciona una región</option>');
                        $.each(info, function(i, region){
                            $("select#region_id").append('<option value="'+region['region_id']+'">'+region['name']+'</option>');
                        });
                        $("select#region_id option[value='"+localidad_info['region_id']+"']").prop("selected", true);
                        $.ajax({
                            url: "/ajax/tools/getProvinciasByRegion",
                            dataType: "JSON",
                            type: "POST",
                            data: {
                                region_id: localidad_info['region_id']
                            },
                            success: function(data){
                                $("select#provincia_id").empty().append('<option value="">Selecciona una provincia</option>');
                                $.each(data, function(i, provincia){
                                    $("select#provincia_id").append('<option value="'+provincia['provincia_id']+'">'+provincia['name']+'</option>');
                                });
                                $("select#provincia_id option[value='"+localidad_info['provincia_id']+"']").prop("selected", true);
                                $.ajax({
                                    url: "/ajax/tools/getComarcasByProvincia",
                                    dataType: "JSON",
                                    type: "POST",
                                    data: {
                                        provincia_id: localidad_info['provincia_id']
                                    },
                                    success: function(data){
                                        $("select#comarca_id").empty().append('<option value="">Selecciona una comarca</option>');
                                        $.each(data, function(i, comarca){
                                            $("select#comarca_id").append('<option value="'+comarca['comarca_id']+'">'+comarca['name']+'</option>');
                                        });
                                        $("select#comarca_id option[value='"+localidad_info['comarca_id']+"']").prop("selected", true);
                                        $.ajax({
                                            url: "/ajax/tools/getLocalidadesByComarca",
                                            dataType: "JSON",
                                            type: "POST",
                                            data: {
                                                comarca_id: localidad_info['comarca_id']
                                            },
                                            success: function(data){
                                                $("select#localidad_id").empty().append('<option value="">Selecciona una localidad</option>');
                                                $.each(data, function(i, localidad){
                                                    $("select#localidad_id").append('<option value="'+localidad['localidad_id']+'">'+localidad['name']+'</option>');
                                                });
                                                $("select#localidad_id option[value='"+localidad_info['localidad_id']+"']").prop("selected", true);
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    }
                });
            }
        })
    });

	$(this).on("click", "#add-page", function(){
		$("#create_pages").get(0).reset();
        $("#create_pages input#pages_id").val(0);
		$(".page-saving").hide();
        $("#pages-table").addClass("hidden");
        $("#pages-form").removeClass("hidden");
    });



	/* PAGES */
	$(this).on("click", "#pages_table tr .action-edit", function(){
        $id = $(this).parents("tr").attr("id").replace("page-", "");
        $.ajax({
            url: "/ajax/pages/edit",
            data:  {
                page_id: $id
            },
            dataType: "json",
            type: "POST",
            success: function(data){
				$("#create_pages input#page-name").val(data['name']);
				$("#create_pages select#page-section_footer option[value='"+data['section_footer']+"']").prop("selected", true);
                $.each(data['idiomas'], function(i, lang){
                    $("#create_pages input#page-title-"+i).val(lang['title']);
					$("#create_pages input#page-seo_title-"+i).val(lang['seo_title']);
                    $("#create_pages textarea#page-description-"+i).val(lang['description']);
					tinyMCE.get("page-description-"+i).setContent(lang['description']);
                });
                $("#create_pages input#page_id").val(data['page_id']);
				$(".page-saving").hide();
                $("#pages-table").addClass("hidden");
                $("#pages-form").removeClass("hidden");

            }
        })
    });
	$(this).on("submit", "#create_pages", function(e){
        e.preventDefault();
        form = $(this);
        form.ajaxSubmit({
            url: form.attr("action"),
            dataType: "json",
            type: "POST",
            beforeSubmit: function(){
                $(".page-saving").show();
            },
            uploadProgress: function(event, position, total, percentComplete) {
                $(".page-percent").html(percentComplete+"%")
            },
            success: function(data){
                form.get(0).reset();
                 $("#create_pages input#page_id").val(0);
                 if ($("#pages_table tr#page-"+data['page_id']).length)
                    $("#pages_table tr#page-"+data['page_id']).replaceWith(data['html']);
                else
                    $("#pages_table tbody").append(data['html']);
            },
            complete: function(xhr) {
                $(".pages-table-saving").hide();
				$("#pages-table").removeClass("hidden");
		        $("#pages-form").addClass("hidden");

            }
        })
    });
	$(".add-location").on("click", function(){
		button = $(this);
		section = button.attr("id").replace("location-", "");
		value = prompt("Introdzca el nombre a continuación", "");
		if(section == 'localidad')
			$parent = $("#comarca_id");
		else if(section == 'comarca')
			$parent = $("#provincia_id");
		else if(section == 'provincia')
			$parent = $("#region_id");
		else if(section == 'region')
			$parent = $("#pais_id");
	    if (value != null) {
	        $.ajax({
				url: "/ajax/tools/addLocation",
				data:  {
					section: button.attr("id").replace("location-", ""),
					value: value,
					parent: $parent.find("option:selected").val()
				},
				dataType: "json",
				type: "post",
				success: function(data){
					$parent.trigger("reload");
				}
			});
	    }
	});
	$(this).on("click", "#create_pages .action-cancel", function(e){
        $("#create_pages").get(0).reset();
        $("#create_pages input#page_id").val(0);
        $("#pages-table").removeClass("hidden");
        $("#pages-form").addClass("hidden");
    });
	$( "#pages-tabs" ).tabs();
    /** usuarios **/
    $("#usuarios-form-tabs" ).tabs();
	$("#usuarios-tabs" ).tabs();
    $(this).on("click", "#add-usuario", function(){
        $("#usuarios-table").addClass("hidden");
        $("#usuarios-form").removeClass("hidden");
		$("#pais_id option:first-child").prop("selected", true);
		$("#pais_id").trigger("change");
		tinyMCE.get("usuario-description_extended").setContent("");
    });
	$("#gen-password").on("click", function(){
		randomstring = Math.random().toString(36).slice(-8);
		$("#usuario-password").val(randomstring);
		if($("#usuario-username").val() == '')
			$("#usuario-username").val(makeSlug($("#usuario-name").val()));
	});
    $(this).on("click", "#usuarios_table tr .action-edit", function(){
        $id = $(this).parents("tr").attr("id").replace("usuario-", "");
        $.ajax({
            url: "/ajax/usuarios/edit",
            data:  {
                user_id: $id
            },
            dataType: "json",
			beforeSend: function(){
				$(".loader-container").removeClass("hidden");
			},
            type: "POST",
            success: function(data){
                $("#create_usuarios input#usuario-firstname").val(data['firstname']);
				$("#create_usuarios input#usuario-lastname").val(data['lastname']);
                $("#create_usuarios input#usuario-user").val(data['user']);
				$("#create_usuarios input#usuario-password").val("");
				$("#create_usuarios input#user_id").val(data['user_id']);

				/*if(data["permisos"]){
					$.each(data["caracteristicas"], function(index, caracteristica){

						if(caracteristica['checked'] == "1"){
							$("input#caracteristica_"+caracteristica['caracteristica_id']).iCheck("check");
						}else if(caracteristica['checked'] == "0"){
							$("input#caracteristica_"+caracteristica['caracteristica_id']).iCheck("uncheck");

						}
					});
				}*/
				$("input.almacen").iCheck("uncheck");
				$("#user-almacenes-filter").val("");
				if(data["tiendas"]){
					$.each(data["tiendas"].split(","), function(index, almacen){
						$("input#almacen_"+almacen).iCheck("check");
					});
				}
				$("#almacenes-list .almacen-item").show();
				$("input.permiso").iCheck("uncheck");
				if(data["permisos"]){
					$.each(data["permisos"].split(","), function(index, permiso){
						$("input#permiso_"+permiso).iCheck("check");
					});
				}
                $("#usuarios-table").addClass("hidden");
                $("#usuarios-form").removeClass("hidden");

            },
            complete: function(){
				$(".usuario-saving").addClass("hidden");
				$(".loader-container").addClass("hidden");
            }
        })
    });

    $(this).on("click", "#usuarios_table tr .action-delete", function(){
        $id = $(this).parents("tr").attr("id").replace("usuario-", "");
        $.ajax({
            url: "/ajax/usuarios/delete",
            data:  {
                user_id: $id
            },
            dataType: "json",
            type: "POST",
            success: function(data){
                $("#usuarios_table tr#usuario-"+$id).remove();
            }
        })
    });
    $(this).on("click", "#create_usuarios .action-cancel", function(e){
        $("#create_usuarios").get(0).reset();
		$("#create_usuarios input[type=checkbox]").iCheck("update");
        $("#create_usuarios input#user_id").val(0);
        $("#create_usuarios input#usuario-image").replaceWith($("#create_usuarios input#usuario-image").clone(0));
        $("#create_usuarios img#usuario-image-preview").attr("src", $("#create_usuarios img#usuario-image-preview").data("defimg"));
        $("#usuarios-table").removeClass("hidden");
        $("#usuarios-form").addClass("hidden");
    });
    /*$(this).on("change", "#create_usuarios #usuario-image", function(){
        /*file    = $(this).get(0).files[0];
        reader  = new FileReader();
        reader.onloadend = function () {
            $("#usuario-image-preview").attr("src", reader.result);
            updateImageusuarios();
        }

        if (file) {
          reader.readAsDataURL(file);
        } else {
          preview.src = "";
	  }*/
		/*$("#create_usuarios").ajaxSubmit({
			url: "/ajax/usuarios/getIMageBlob",
			dataType: "json",
            type: "POST",
            beforeSubmit: function(){
                $(".usuario-saving").show();
            },
            uploadProgress: function(event, position, total, percentComplete) {
                $(".usuario-percent").html(percentComplete+"%")
            },
			success: function(data){
				$("#usuario-image-preview").attr("src", data['image']);
				updateImageusuarios();
			},
			complete: function(xhr) {
                $(".usuario-saving").hide();
			}
		});
    });*/
    $(this).on("submit", "#create_usuarios", function(e){
        e.preventDefault();
        form = $(this);
        $.ajax({
            url: form.attr("action"),
            dataType: "json",
            type: "POST",
			data: form.serialize(),
            success: function(data){
				if(typeof data['error'] != "undefined"){

				}else{
                	form.get(0).reset();
					$("#create_usuarios input[type=checkbox]").iCheck("update");
	                $("#create_usuarios input#user_id").val(0);
	                if ($("#usuarios_table tr#usuario-"+data['user_id']).length)
	                    $("#usuarios_table tr#usuario-"+data['user_id']).replaceWith(data['html']);
	                else
	                    $("#usuarios_table tbody").append(data['html']);

	                $("#usuarios-table").removeClass("hidden");
	                $("#usuarios-form").addClass("hidden");
	                $("#create_usuarios img#usuario-image-preview").attr("src", $("#create_usuarios img#usuario-image-preview").data("defimg"));
				}
				$(".usuario-saving").hide();
            },
            complete: function(xhr) {

            }
        })
    });

	/* SLIDES */
	$(this).on("click", "#slides_table tr .action-delete", function(){
        $id = $(this).parents("tr").attr("id").replace("slide-", "");
        $.ajax({
            url: "/ajax/slides/delete",
            data:  {
                slide_id: $id
            },
            dataType: "json",
            type: "POST",
            success: function(data){
                $("#slides_table tr#slide-"+$id).remove();
            }
        })
    });
	$(this).on("click", "#add-slide", function(){
        $("#slides-table").addClass("hidden");
        $("#slides-form").removeClass("hidden");
    });
	$(this).on("click", "#slides_table tr .action-edit", function(){
        $id = $(this).parents("tr").attr("id").replace("slide-", "");
        $.ajax({
            url: "/ajax/slides/edit",
            data:  {
                slide_id: $id
            },
            dataType: "json",
            type: "POST",
            success: function(data){
				$("#create_slides input#slide-name").val(data['name']);
				$("#create_slides input#slide-order").val(data['order']);
				$("#create_slides select#slide-searchbox option[value='"+data['searchbox']+"']").prop("selected", true);
				//$("#create_slides select#slide-section_footer option[value='"+data['section_footer']+"']").prop("selected", true);
                $.each(data['idiomas'], function(i, lang){
                    tinyMCE.get("slide-title-"+i).setContent(lang['title']);
					//$("#create_slides input#slide-seo_title-"+i).val(lang['seo_title']);
                    //$("#create_slides textarea#slide-description-"+i).val(lang['description']);
					tinyMCE.get("slide-description-"+i).setContent(lang['description']);
                });
                $("#create_slides input#slide_id").val(data['slide_id']);
				$(".slide-saving").hide();
                $("#slides-table").addClass("hidden");
                $("#slides-form").removeClass("hidden");

            }
        })
    });
	$(this).on("submit", "#create_slides", function(e){
        e.preventDefault();
        form = $(this);
        form.ajaxSubmit({
            url: form.attr("action"),
            dataType: "json",
            type: "POST",
            beforeSubmit: function(){
                $(".slide-saving").show();
            },
            uploadProgress: function(event, position, total, percentComplete) {
                $(".slide-percent").html(percentComplete+"%")
            },
            success: function(data){
                form.get(0).reset();
                 $("#create_slides input#slide_id").val(0);
                 if ($("#slides_table tr#slide-"+data['slide_id']).length)
                    $("#slides_table tr#slide-"+data['slide_id']).replaceWith(data['html']);
                else
                    $("#slides_table tbody").append(data['html']);
            },
            complete: function(xhr) {
                $(".slides-table-saving").hide();
				$("#slides-table").removeClass("hidden");
		        $("#slides-form").addClass("hidden");

            }
        })
    });
	$(".add-location").on("click", function(){
		button = $(this);
		section = button.attr("id").replace("location-", "");
		value = prompt("Introdzca el nombre a continuación", "");
		if(section == 'localidad')
			$parent = $("#comarca_id");
		else if(section == 'comarca')
			$parent = $("#provincia_id");
		else if(section == 'provincia')
			$parent = $("#region_id");
		else if(section == 'region')
			$parent = $("#pais_id");
	    if (value != null) {
	        $.ajax({
				url: "/ajax/tools/addLocation",
				data:  {
					section: button.attr("id").replace("location-", ""),
					value: value,
					parent: $parent.find("option:selected").val()
				},
				dataType: "json",
				type: "post",
				success: function(data){
					$parent.trigger("reload");
				}
			});
	    }
	});
	$(this).on("click", "#create_slides .action-cancel", function(e){
        $("#create_slides").get(0).reset();
        $("#create_slides input#slide_id").val(0);
        $("#slides-table").removeClass("hidden");
        $("#slides-form").addClass("hidden");
    });
	$( "#slides-tabs" ).tabs();

	/*** LANGUAGE *****/
	$("table#translates textarea")
	.change(function(){
		var text = $(this).val();
		var translate_id = $(this).parents("tr").attr("id").replace("translate-", "");
		var lang_id = $("table#translates").data("lang");

		$.ajax({
			url: "/ajax/translates/translate",
			data: {
				tlang: lang_id,
				tid: translate_id,
				translate: text
			},
			type: "POST",
			dataType: "json",
			success: function(json) {
				console.log(json);
			}
		});
	});
	$("table#translates .traducido").click(function(){
		$this = $(this);
		traducido = ($this.hasClass("on")) ? 0 : 1;
		translate_id = $this.parents("tr").attr("id").replace("translate-", "");
		lang_id = $("table#translates").data("lang");

		$.ajax({
			url: "/ajax/translates/change_traducido",
			data: {
				tlang: lang_id,
				tid: translate_id,
				traducido: traducido
			},
			type: "POST",
			dataType: "json",
			success: function(json) {
				if(!traducido) {
					$this.removeClass("on").addClass("off");
				}else{
					$this.removeClass("off").addClass("on");
				}
			}
		});
	});
	$('table#translates textarea').focus(function(){
		$(this).autosize();
	});
});
