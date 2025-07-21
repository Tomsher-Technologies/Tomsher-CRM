<!-- delete Modal -->
<div id="delete-modal" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{trans('messages.delete_confirmation')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mt-1">{{trans('messages.are_you_sure_delete')}}</p>
                <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{trans('messages.cancel')}}</button>
                <a href="" id="delete-link" class="btn btn-primary mt-2">{{trans('messages.delete')}}</a>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
