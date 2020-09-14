<div class="modal fade create-position-{{ $rand }}" id="modal-create-position" style="display: none;">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('general.title.new', ['type' => trans_choice('payroll::general.positions', 1)]) }}</h4>
            </div>

            <div class="modal-body">
                {!! Form::open(['id' => 'form-create-position', 'role' => 'form', 'class' => 'form-loading-button']) !!}

                <div class="row">
                    {{ Form::textGroup('name', trans('general.name'), 'id-card-o') }}

                    {!! Form::hidden('enabled', '1', []) !!}
                </div>

                {!! Form::close() !!}
            </div>

            <div class="modal-footer">
                <div class="pull-left">
                    {!! Form::button('<span class="fa fa-save"></span> &nbsp;' . trans('general.save'), ['type' => 'button', 'id' =>'button-create-position', 'class' => 'btn btn-success button-submit', 'data-loading-text' => trans('general.loading')]) !!}

                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-times-circle"></span> {{ trans('general.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.create-position-{{ $rand }}#modal-create-position').modal('show');
    });

    $(document).on('click', '.create-position-{{ $rand }} #button-create-position', function (e) {
        $('.create-position-{{ $rand }}#modal-create-position .modal-header').before('<span id="span-loading" style="position: absolute; height: 100%; width: 100%; z-index: 99; background: #6da252; opacity: 0.4;"><i class="fa fa-spinner fa-spin" style="font-size: 10em !important;margin-left: 35%;margin-top: 8%;"></i></span>');

        $.ajax({
            url: '{{ url("payroll/modals/positions") }}',
            type: 'POST',
            dataType: 'JSON',
            data: $(".create-position-{{ $rand }} #form-create-position").serialize(),
            beforeSend: function () {
                $('.create-position-{{ $rand }} #button-create-position').button('loading');

                $(".create-position-{{ $rand }} .form-group").removeClass("has-error");
                $(".create-position-{{ $rand }} .help-block").remove();
            },
            complete: function() {
                $('.create-position-{{ $rand }} #button-create-position').button('reset');
            },
            success: function(json) {
                var data = json['data'];

                $('.create-position-{{ $rand }} #span-loading').remove();

                $('.create-position-{{ $rand }}#modal-create-position').modal('hide');

                $('#position_id').append('<option value="' + data.id + '" selected="selected">' + data.name + '</option>');
                $('#position_id').trigger('change');
                $('#position_id').select2('refresh');

                @if ($position_selector)
                $('{{ $position_selector }}').append('<option value="' + data.id + '" selected="selected">' + data.name + '</option>');
                $('{{ $position_selector }}').trigger('change');
                $('{{ $position_selector }}').select2('refresh');
                @endif
            },
            error: function(error, textStatus, errorThrown) {
                $('.create-position-{{ $rand }} #span-loading').remove();

                if (error.responseJSON.name) {
                    $(".create-position-{{ $rand }}#modal-create-position input[name='name']").parent().parent().addClass('has-error');
                    $(".create-position-{{ $rand }}#modal-create-position input[name='name']").parent().after('<p class="help-block">' + error.responseJSON.name + '</p>');
                }
            }
        });
    });
</script>
