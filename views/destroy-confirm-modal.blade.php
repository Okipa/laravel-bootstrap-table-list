<div id="destroy-confirm-modal-{{ $entity->id }}"
     {{ classTag('modal', 'fade', config('tablelist.template.modal.container.class')) }}
     tabindex="-1"
     role="dialog"
     aria-labelledby="destroyConfirmationModal"
     aria-hidden="true">
    <div {{ classTag('modal-dialog', config('tablelist.template.modal.item.class')) }}
         role="document">
        <div class="modal-content">
            <div {{ classTag('modal-header', config('tablelist.template.modal.title.container.class')) }}>
                <h5 id="destroy-confirm-modal-label-{{ $entity->id }}"
                    {{ classTag('modal-title', config('tablelist.template.modal.title.item.class')) }}>
                    {!! config('tablelist.template.modal.title.item.icon') !!}
                    {{ trans('tablelist::tablelist.modal.title') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div {{ classTag('modal-body', config('tablelist.template.modal.body.item.class')) }}>
                {!! trans('tablelist::tablelist.modal.question', [
                    'entity' => $table->destroyAttributes->map(function($attribute) use ($entity) {
                            return $entity->{$attribute};
                        })->implode(' ')
                ]) !!}
            </div>
            <div {{ classTag('modal-footer', config('tablelist.template.modal.footer.item.class')) }}>
                <button type="button"
                        {{ classTag(config('tablelist.template.modal.footer.cancel.item.class')) }}
                        data-dismiss="modal">
                    {!! config('tablelist.template.modal.footer.cancel.item.icon') !!}
                    {{ trans('tablelist::tablelist.modal.action.cancel') }}
                </button>
                <button type="submit"
                    {{ classTag(config('tablelist.template.modal.footer.confirm.item.class')) }}>
                    {!! config('tablelist.template.modal.footer.confirm.item.icon') !!}
                    {{ trans('tablelist::tablelist.modal.action.confirm') }}
                </button>
            </div>
        </div>
    </div>
</div>
