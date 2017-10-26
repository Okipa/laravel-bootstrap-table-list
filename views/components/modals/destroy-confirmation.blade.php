<div class="modal fade destroy-confirm-modal-{{ $entity->id }}"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myLargeModalLabel"
     style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-danger">
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title text-left" id="modal-title">
                    <i class="fa fa-exclamation-triangle"></i>
                    {{ trans('tablelist::tablelist.modal.title') }}
                </h4>
            </div>
            <div class="modal-body text-left">
                {!! trans('tablelist::tablelist.modal.question', [
                    'entity' => $entity->{$table->destroyAttribute}
                ]) !!}
            </div>
            <div class="modal-footer">
                @include('tablelist::components.buttons.cancel')
                @include('tablelist::components.buttons.confirm')
            </div>
        </div>
    </div>
</div>
