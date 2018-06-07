<button type="button"
        {{ classTag(config('tablelist.template.button.destroy.class'), $entity->disabled ? 'disabled' : null) }}
        title="{{ trans('tablelist::tablelist.tbody.action.destroy') }}"
        @if(config('tablelist.template.button.destroy.trigger-bootrap-native-modal'))
            data-toggle="modal"
            data-target=".destroy-confirm-modal-{{ $entity->id }}"
        @endif
        @if($entity->disabled)disabled="disabled" @endif>
    {!! config('tablelist.template.button.destroy.icon') !!}
</button>
