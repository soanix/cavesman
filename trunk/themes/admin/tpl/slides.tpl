<div class="page-content">
    <div id="tab-general">
        <div class="row mbl">
            <div class="col-lg-12">

                <div class="col-md-12">
                    <div id="area-chart-spline" style="width: 100%; height: 300px; display: none;">
                    </div>
                </div>

            </div>

            <div class="col-lg-12">
                <div class="row">
                    <div id="slides-table" class="col-lg-12">
                        <div class="panel panel-grey">
                            <div class="panel-heading">slides <i id="add-slide" class="fa fa-plus icon-add-table"></i></div>
                            <div class="panel-body">
                                <table id="slides_table" class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$slides.all item="slide"}
                                            {include file="slide.tpl"}
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="slides-form" class="col-lg-12 hidden">
                        <!-- COMERCIO -->
                        <div class="panel panel-green">
                            <div class="panel-heading">
                                Añadir/Editar slide
                            </div>
                            <div class="panel-body pan">
                                <form id="create_slides" action="/ajax/slides/save" type="POST">
                                        <div class="form-body pal">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="slide-name" class="control-label">Nombre descriptivo</label>
                                                        <div>
                                                            <input id="slide-name" type="text" name="name" placeholder="" value="" class="form-control" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="slide-order" class="control-label">Posición</label>
                                                        <div>
                                                            <input id="slide-order" type="text" name="order" placeholder="" value="" class="form-control" />
                                                        </div>
                                                    </div>
                                                </div>
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="slide-searchbox" class="control-label">Caja de búsqueda</label>
														<br>
														<div>
	                                                        <select id="slide-searchbox" name="searchbox" class="form-control">
																  	<option value="1">Activada</option>
																	<option value="0">Desactivada</option>
															</select>
														</div>
                                                    </div>
                                                </div>
                                            </div>
											<div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="slide-image" class="control-label">Imagen de fondo</label>
                                                        <div>
                                                            <input id="slide-image" type="file" name="image" placeholder="" value="" class="form-control" />
                                                        </div>
                                                    </div>
                                                </div>
											</div>


                                            <hr>
                                            <div id="slides-tabs">
                                                <ul>
                                                	{foreach from=$lang_list item="lang"}
                                                    	<li><a href="#promo-tab-{$lang.lang_id}">{$lang.name}</a></li>
                                                    {/foreach}
                                                </ul>

                                                {foreach from=$lang_list item="lang"}
                                                    <div id="promo-tab-{$lang.lang_id}">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="slide-title-{$lang.lang_id}" class="control-label">Título de la promoción</label>
																	<div>
                                                                        <textarea id="slide-title-{$lang.lang_id}"  rows="12" name="title[{$lang.lang_id}]" class="form-control editor"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="slide-description-{$lang.lang_id}" class="control-label">Descripción</label>
                                                                    <div>
                                                                        <textarea id="slide-description-{$lang.lang_id}"  rows="12" name="description[{$lang.lang_id}]" class="form-control editor"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </div>
                                        <div class="form-actions text-right col-xs-12">
                                            <div class="co-xs-3 slide-saving">
                                                <i id="slide-loader" class="fa fa-circle-o-notch fa-spin fa-3x pull-left"></i>
                                                <div class="slide-percent pull-left">
                                                 0%
                                                </div>
                                            </div>

                                            <button type="button" class="btn btn-danger action-cancel">
                                                Cancelar
                                            </button>
                                             <button type="submit" class="btn btn-primary">
                                                Guardar
                                            </button>
                                        </div>
                                        <input type="hidden" name="slide_id" id="slide_id" value="0">
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- END COMERCIO -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
