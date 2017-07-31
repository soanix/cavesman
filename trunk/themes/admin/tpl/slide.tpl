<tr id="slide-{$slide.slide_id}">
    <td>{$slide.slide_id}</td>
	<td><img src="/img/m/slides/t/{"slide_id_{$slide.slide_id}"|md5}.jpg?{$smarty.now}"></td>
    <td>{$slide.title}</td>
    <td class="actions">
        <i class="action-edit fa fa-pencil fa-2x"></i>
        <i class="action-delete fa fa-trash fa-2x"></i>
    </td>
</tr>
