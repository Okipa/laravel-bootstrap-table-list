<form role="form"
      method="POST"
      action="{{ $table->getRoute('destroy', ['id' => $entity->id]) }}">
    {!! csrf_field() !!}
    <input type="hidden" name="_method" value="DELETE">
    <button type="button"
            class="{!! config('tablelist.design.destroy.class') !!}"
            data-toggle="modal"
            data-target=".destroy-confirm-modal-{{ $entity->id }}"
            title="{{ trans('tablelist::tablelist.tbody.action.destroy') }}">
        {!! config('tablelist.design.destroy.icon') !!}
    </button>
    @include('tablelist::components.destroy-confirm-modal')
</form>