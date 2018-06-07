<button type="submit"
        {{ classTag(config('tablelist.template.button.edit.class'), $entity->disabled ? 'disabled' : null) }}
        title="{{ trans('tablelist::tablelist.tbody.action.edit') }}"
        @isset($disabled)disabled="disabled" @endisset>
    {!! config('tablelist.template.button.edit.icon') !!}
</button>
