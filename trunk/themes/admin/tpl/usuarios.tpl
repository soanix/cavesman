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
                    <div id="usuarios-table" class="col-lg-12">
                        <div class="panel panel-grey">
                            <div class="panel-heading">Usuarios <i id="add-usuario" class="fa fa-plus icon-add-table"></i>
								<div class="row">
									<div class="col-xs-12">
										<div class="form-group">
											<input type="text" id="usuarios-filter"  class="form-control" value="" placeholder="Buscar">
										</div>
									</div>
								</div>
							</div>
                            <div class="panel-body">
                                <table id="usuarios_table" class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Apellidos</th>
                                        <th>Activo</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$usuarios.all item="usuario"}
                                            {include file="usuario.tpl"}
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="usuarios-form" class="col-lg-12 hidden">
                        <!-- usuario -->
                        <div class="panel panel-green">
                            <div class="panel-heading">
                                Añadir/Editar usuario
                            </div>
                            <div class="panel-body pan">
                                <form id="create_usuarios" action="/ajax/usuarios/save" type="POST">
                                    <div class="form-body pal">
                                        <div id="usuarios-form-tabs">
                                            <ul>
                                                <li><a href="#usuario-info-tab">Información general</a></li>
												<li><a href="#permisos">Permisos</a></li>
                                            </ul>
											<div id="permisos">
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label class="control-label">Permisos</label>
															<div class="row">
																<div class="col-xs-4">
																	<input type="checkbox" class="permiso" id="permiso_1" name="permisos[]" value="1">
																	<label for="permiso_1">Panel de administración</label>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>

                                            <div id="usuario-info-tab">
												<div class="row">
                                                    <div class="col-xs-12">
														<div class="row">
															<div class="col-xs-12">
																<h3>Datos generales</h3>
																<hr>
															</div>
														    <div class="col-md-6">
														        <div class="form-group">
														            <label for="usuario-firstname" class="control-label">Nombre</label>
														            <div>
														                <input id="usuario-firstname" type="text" name="firstname" placeholder="" value="" class="form-control" />
														            </div>
														        </div>
														    </div>
														    <div class="col-md-6">
														        <div class="form-group">
														            <label for="usuario-lastname" class="control-label">Apellidos</label>
														            <div >
														                <input id="usuario-lastname"  type="text" name="lastname" placeholder="" value="" class="form-control" />
														            </div>
														        </div>
														    </div>
														</div>
														<div class="row">
															<div class="col-xs-12">
																<h3>Datos de acceso</h3>
																<hr>
															</div>

														    <div class="col-md-6">
														        <div class="form-group">
														            <label for="usuario-user" class="control-label">Usuario</label>
														            <div>
														                <input id="usuario-user" type="text" name="user" placeholder="" value="" class="form-control" />
														            </div>
														        </div>
														    </div>
														    <div class="col-md-6">
														        <div class="form-group">
														            <label for="usuario-password" class="control-label">Contraseña</label>
														            <div>
														                <input id="usuario-password"  type="password" name="password" placeholder="" value="" class="form-control" />
														            </div>
														        </div>
														    </div>
														</div>
													</div>
												</div>
                                            </div>
                                    	</div>
									</div>
                                    <div class="form-actions text-right pal pull-left col-xs-12">
                                        <div class="co-xs-3 usuario-saving">
                                            <i id="usuario-loader" class="fa fa-circle-o-notch fa-spin fa-3x pull-left"></i>
                                            <div class="usuario-percent pull-left">
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
                                    <input type="hidden" name="user_id" id="user_id" value="0">
                                </form>
                            </div>
                        </div>
                        <!-- END usuario -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
