<button class="{!! config('tablelist.template.button.edit.class') !!}@isset($class){{ $class }}@endisset"
        type="submit"
        title="{{ trans('tablelist::tablelist.tbody.action.edit') }}">
    {!! config('tablelist.template.button.edit.icon') !!}
</button>
