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
                    <div id="pages-table" class="col-lg-12">
                        <div class="panel panel-grey">
                            <div class="panel-heading">pages <i id="add-page" class="fa fa-plus icon-add-table"></i></div>
                            <div class="panel-body">
                                <table id="pages_table" class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$pages.all item="page"}
                                            {include file="page.tpl"}
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="pages-form" class="col-lg-12 hidden">
                        <!-- COMERCIO -->
                        <div class="panel panel-green">
                            <div class="panel-heading">
                                Añadir/Editar page
                            </div>
                            <div class="panel-body pan">
                                <form id="create_pages" action="/ajax/pages/save" type="POST">
                                        <div class="form-body pal">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="page-name" class="control-label">Nombre descriptivo</label>
                                                        <div>
                                                            <input id="page-name" type="text" name="name" placeholder="" value="" class="form-control" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="page-section_footer" class="control-label">Sección pie de página</label>
                                                        <div>
                                                            <select id="page-section_footer" name="section_footer" class="form-control">
                                                                <option value="0">Sin asignar</option>
                                                                <option value="1">Empresa</option>
                                                                <option value="2">Legal</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <hr>
                                            <div id="pages-tabs">
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
                                                                    <label for="page-title-{$lang.lang_id}" class="control-label">Título de la promoción</label>
                                                                    <div class="input-icon right">
                                                                        <input id="page-title-{$lang.lang_id}" name="title[{$lang.lang_id}]" type="text" placeholder="" value="" class="form-control" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="page-seo_title-{$lang.lang_id}" class="control-label">Direccion web personalizada</label>
                                                                    <div class="input-icon right">
                                                                        <input id="page-seo_title-{$lang.lang_id}" name="seo_title[{$lang.lang_id}]" type="text" placeholder="" value="" class="form-control" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="page-description-{$lang.lang_id}" class="control-label">Descripción</label>
                                                                    <div>
                                                                        <textarea id="page-description-{$lang.lang_id}"  rows="12" name="description[{$lang.lang_id}]" class="form-control editor"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </div>
                                        <div class="form-actions text-right col-xs-12">
                                            <div class="co-xs-3 page-saving">
                                                <i id="page-loader" class="fa fa-circle-o-notch fa-spin fa-3x pull-left"></i>
                                                <div class="page-percent pull-left">
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
                                        <input type="hidden" name="page_id" id="page_id" value="0">
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
