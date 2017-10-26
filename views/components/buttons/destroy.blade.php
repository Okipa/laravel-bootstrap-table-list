<button type="button"
        class="{!! config('tablelist.template.button.destroy.class') !!}"
        data-toggle="modal"
        data-target=".destroy-confirm-modal-{{ $entity->id }}"
        title="{{ trans('tablelist::tablelist.tbody.action.destroy') }}">
    {!! config('tablelist.template.button.destroy.icon') !!}
</button>
