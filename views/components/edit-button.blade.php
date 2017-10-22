<form role="form"
      method="GET"
      action="{{ $table->getRoute('edit', ['id' => $entity->id]) }}">
    <button class="{!! config('tablelist.design.edit.class') !!}"
            type="submit"
            title="{{ trans('tablelist::tablelist.tbody.action.edit') }}">
        {!! config('tablelist.design.edit.icon') !!}
    </button>
</form>
