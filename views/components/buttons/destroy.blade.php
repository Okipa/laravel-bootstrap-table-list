<button type="button"
        class="{!! config('tablelist.template.button.destroy.class') !!} @isset($class){{ $class }}@endisset"
        title="{{ trans('tablelist::tablelist.tbody.action.destroy') }}"
        @if(config('tablelist.template.button.destroy.trigger-bootrap-native-modal'))
            data-toggle="modal"
            data-target=".destroy-confirm-modal-{{ $entity->id }}"
        @endif>
    {!! config('tablelist.template.button.destroy.icon') !!}
</button>
