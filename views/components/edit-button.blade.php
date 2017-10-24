<form role="form"
      method="GET"
      action="{{ $table->getRoute('edit', ['id' => $entity->id]) }}">
    <button class="{!! config('tablelist.template.edit.class') !!}"
            type="submit"
            title="{{ trans('tablelist::tablelist.tbody.action.edit') }}">
        {!! config('tablelist.template.edit.icon') !!}
    </button>
</form>
