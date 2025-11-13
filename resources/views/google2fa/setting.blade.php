<style>
    .qr-code {
        text-align: center;
        margin-bottom: 20px;
    }
    .qr-code img {
        max-width: 200px;
        height: auto;
    }
</style>
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">两步验证</h3>
    </div>
    <div class="box-body">
        <div class="row">
            @if(!$isEnabled)
                <div class="col-md-4">
                    <div class="alert alert-info alert-dismissible">
                        <h4>
                            <i class="fa fa-info-circle"></i>
                        </h4>
                        <h5>
                            此功能用于增强您的账户安全保护。 
                            首先，您可以在手机上下载 Google Authenticator 应用程序。
                        </h5>
                        <h5>
                            要开始，请在手机上下载 Google Authenticator 应用程序。
                        </h5>
                        <h5>
                            然后按照以下步骤进行激活。
                        </h5>
                    </div>
                </div>
                <div class="col-md-8">
                    <form id="enable2FAForm">
                        @csrf
                        <ol>
                            <li>
                                <p class="mb-0"><strong>账户安全注册</strong></p>
                                <div class="text-left mb-3">
                                    <label for="img">使用 Google Authenticator 应用扫描下方二维码</label><br>
                                    <img class="img img-reponsive" src="data:image/png;base64,{{ $qrCodeUrl }}" alt="QR Code">
                                    <p class="mt-2 mb-1">或输入以下代码</p>
                                    <label for="img"><b>{{ $secret }}</b></label><br>
                                </div>
                            </li>
                            <li>
                                <p class="mb-0"><strong>激活两步验证</strong></p>
                                <div class="text-left mb-3">
                                    <label for="one_time_password">输入应用中显示的验证码</label><br>
                                    <div class="form-group">
                                        <input type="hidden" name="{{config('google2fa.otp_secret_column')}}" value="{{ $secret }}">
                                        <input type="text" class="form-control" name="{{config('google2fa.otp_input')}}" placeholder="输入6位验证码" required>
                                    </div>
                                    <button type="submit" class="btn btn-info submit btn-sm mt-2">
                                        <i class="fa fa-floppy"></i> 激活
                                    </button>
                                </div>
                            </li>
                            <li>
                                <p class="mb-3"><strong>接下来，您将被引导到验证页面，再次输入应用中显示的验证码</strong></p>
                            </li>
                            <li>
                                <p class="mb-3"><strong>账户安全设置完成</strong></p>
                            </li>
                        </ol>
                    </form>
                </div>
            @else
                <div class="col-md-12">
                    <div class="alert alert-success">
                        <i class="fa fa-check"></i> 您的账户已启用高级安全保护
                    </div>
                    <form id="disable2FAForm">
                        @csrf
                        <div class="form-group">
                            <label>要关闭两步验证，请输入 Google Authenticator 应用中的验证码</label>
                            <input type="text" class="form-control" name="{{config('google2fa.otp_input')}}" placeholder="输入6位验证码" required>
                        </div>
                        <button type="submit" class="btn btn-danger">关闭两步验证</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
$(function() {
    @if(!$isEnabled)
        $('#enable2FAForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: '{{ admin_url("2fa/enable") }}',
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $.pjax.reload('#pjax-container');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        toastr.error(xhr.responseJSON.message);
                    }
                }
            });
        });
    @else
        $('#disable2FAForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: '{{ admin_url("2fa/disable") }}',
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $.pjax.reload('#pjax-container');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        toastr.error(xhr.responseJSON.message);
                    }
                }
            });
        });
    @endif
});
</script>
