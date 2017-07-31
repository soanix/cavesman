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
                            <div class="panel-heading">Traducciones de ES -> {$translates.iso|upper}<i id="add-page" class="fa fa-plus icon-add-table"></i></div>
                            <div class="panel-body">
								<table id="translates" class="table table-row" data-lang="{$translates.lang_id}">
									<thead>
										<th>ID</th>
										<th>Original</th>
										<th>Traducción</th>
										<th style="width: 25px;">Tra.</th>
									</thead>
									<tbody>
									    {foreach from=$translates.all item="translate"}
											<tr id="translate-{$translate.translate_id}">
												<td>{$translate.translate_id}</td>
												<td>{$translate.string}</td>
												<td style="width: 50%">
													<textarea style="overflow: hidden;resize: none;height: 100%; width: 100%;" name="{$translate.translate_id}">{$translate.translate}</textarea>
													<span id="literal">{$translate.translate}</span>
												</td>
												<td style="width: 25px;"><div class="traducido {if $translate.traducido}on{else}off{/if}"></div></td>
											</tr>
										{/foreach}
									</tbody>
								</table>
                            </div>
                        </div>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
