@extends('layouts.app')
@section('title', Auth::user()->username)

@section('content')
  <div class="ui container page-content">

    <h1 class="ui center aligned header">
      <img class="ui rounded image" src="{{ env('SKINS_DISPLAY_URL') }}/head/{{ Auth::user()->username }}">
      <div class="content">
        {{ Auth::user()->username }}
        <div class="sub header">@lang('user.profile.created.string', ['date' => Auth::user()->created_at->diffForHumans()])</div>
      </div>
    </h1>

    <div class="ui divider"></div>

    @role('restricted')
      <div class="ui red message">
        <i class="close icon"></i>
        <div class="header">
          @lang('user.role.restricted')
        </div>
        @lang('user.role.restricted.description')
      </div>
    @endrole

    @if(!$confirmedAccount && Auth::user()->can('user-user-send-confirmation-email'))
      <div class="ui warning message">
        <i class="close icon"></i>
        <div class="header">
          @lang('user.profile.confirmed.title')
        </div>
        @lang('user.profile.confirmed.description', ['url' => action('UserController@sendConfirmationMail')])
      </div>
    @endif

    @if($notifications)
      @foreach ($notifications as $notification)
        <div class="ui {{ $notification['type'] }} message">
          {!! $notification['message'] !!}
        </div>
      @endforeach
    @endif

    <div class="ui grid two column">
      <div class="ui four wide column">
        <div class="ui vertical menu">
          <a class="item toggle-menu active" data-toggle="infos">
            <i class="user left aligned icon"></i>
            @lang('user.profile.menu.infos')
          </a>
          @ability('', 'user-upload-skin,user-upload-cape')
            <a class="item toggle-menu" data-toggle="appearence">
              <i class="theme left aligned icon"></i>
              @lang('user.profile.menu.appearence')
            </a>
          @endability
          <a class="item toggle-menu" data-toggle="security">
            <i class="lock left aligned icon"></i>
            @lang('user.profile.menu.security')
            @if (!$twoFactorEnabled || !$findObsiGuardIPs)
              <i class="warning sign icon" style="color:#{{ $twoFactorEnabled ? 'FE9A76' : 'B03060' }}"></i>
            @endif
          </a>
          <a class="item toggle-menu" data-toggle="spendings">
            <i class="shopping basket left aligned icon"></i>
            @lang('user.profile.menu.spendings')
          </a>
          <a class="item toggle-menu" data-toggle="socials">
            <i class="twitter left aligned icon"></i>
            @lang('user.profile.menu.socials')
          </a>
        </div>
      </div>
      <div class="ui twelve wide column">
        <div class="menu-content">
          <div data-menu="infos">
            <div class="ui form">
              <h4 class="ui dividing header">@lang('user.profile.personnals.details')</h4>
              <div class="field">
                <label>@lang('user.field.username')</label>
                <div class="fields">
                  <div class="twelve wide field">
                    <input type="text" id="username" value="{{ Auth::user()->username }}" disabled>
                  </div>
                  @permission('user-edit-username')
                    <div class="four wide field" id="usernameBtn">
                      <button type="button" class="fluid ui primary button" onClick="$('.ui.modal#editUsername').modal({blurring: true}).modal('show')"><i class="edit icon"></i> @lang('user.profile.username.edit')</button>
                    </div>
                  @endpermission
                </div>
              </div>
              <div class="field">
                <label>@lang('user.field.email')</label>
                <div class="fields">
                  <div class="twelve wide field">
                    <input type="text" value="{{ Auth::user()->email }}" disabled>
                  </div>
                  @permission('user-request-edit-email')
                    <div class="four wide field">
                      <button type="button" class="fluid ui primary button" onClick="$('.ui.modal#editEmail').modal({blurring: true}).modal('show')"><i class="edit icon"></i> @lang('user.profile.email.edit')</button>
                    </div>
                  @endpermission
                </div>
              </div>
              @permission('user-edit-password')
                <div class="ui divider"></div>
                <div class="field">
                  <label>@lang('user.field.password')</label>
                  <form method="post" action="{{ url('/user/password') }}" data-ajax>
                    <div class="fields">
                      <div class="five wide field">
                        <input type="password" name="password" placeholder="@lang('user.profile.password.edit.placeholder')">
                      </div>
                      <div class="five wide field">
                        <input type="password" name="password_confirmation" placeholder="@lang('user.profile.password.edit.placeholder')">
                      </div>
                      <div class="six wide field">
                        <button type="submit" class="fluid ui red button"><i class="edit icon"></i> @lang('user.profile.password.edit')</button>
                      </div>
                    </div>
                  </form>
                </div>
              @endpermission
            </div>

            <br><br>

            <div class="ui divider"></div>

            <div class="ui three statistics">
              <div class="statistic">
                <div class="value">
                  <span id="money">{{ Auth::user()->money }}</span>
                  @permission('user-transfer-money')
                    <i onClick="$('.ui.modal#transferMoney').modal({blurring: true}).modal('show')" class="send icon"></i>
                  @endpermission
                </div>
                <div class="label">
                  @lang('user.money')
                </div>
              </div>
              <div class="statistic">
                <div class="value">
                  {{ $votesCount }}
                </div>
                <div class="label">
                  @lang('user.votes')
                </div>
              </div>
              <div class="statistic">
                <div class="value">
                  {{ $rewardsWaitedCount }}
                  <a href="{{ url('/vote/reward/get/waited') }}"><i class="hand rock icon"></i></a>
                </div>
                <div class="label">
                  @lang('user.rewards_waited')
                </div>
              </div>
            </div>

            <br><br>
          </div>

          <div data-menu="appearence" style="display:none;">

            @permission('user-upload-skin')
              <h3 class="ui dividing header">
                @lang('user.profile.appearence.skin')
              </h3>

              @if ($votesCount < 3)
                <div class="ui error message">
                  <div class="header">
                    @lang('form.error.title')
                  </div>
                  @lang('user.profile.appearence.skin.error.vote')
                </div>
              @else
                <div class="ui card" style="width:500px;">
                  <div class="content">
                    <h4 class="ui sub header">@lang('user.profile.appearence.specifics')</h4>
                    <div class="ui small feed">
                      <div class="ui list">
                        <a class="item">
                          <i class="resize horizontal icon"></i>
                          <div class="content">
                            <div class="header">@lang('user.profile.appearence.specifics.width')</div>
                            <div class="description">@lang('user.profile.appearence.specifics.width.subtitle', ['max_width' => env('SKINS_UPLOAD_MAX_WIDTH'), 'max_height' => env('SKINS_UPLOAD_MAX_HEIGHT')])</div>
                          </div>
                        </a>
                        <a class="item">
                          <i class="compress icon"></i>
                          <div class="content">
                            <div class="header">@lang('user.profile.appearence.specifics.size')</div>
                            <div class="description">@lang('user.profile.appearence.specifics.size.subtitle', ['max_size' => round(env('SKINS_UPLOAD_MAX_SIXE') / 1000000)])</div>
                          </div>
                        </a>
                      </div>
                    </div>
                  </div>
                  <div class="extra content text-center">
                    <form action="{{ url('/user/skin') }}" method="post" data-ajax data-ajax-upload-image>
                      <div style="display:inline-block">
                        <label for="skin" class="ui labeled icon button">
                          <i class="file image outline icon"></i>
                          <span class="filename">@lang('user.profile.appearence.skin.choose')</span>
                        </label>
                        <input type="file" name="image" accept="image/x-png,image/png" id="skin" style="display:none">
                      </div>
                      <button type="submit" class="ui primary button">@lang('user.profile.appearence.skin.send')</button>
                    </form>
                  </div>
                </div>
              @endif
            @endpermission

            @permission('user-upload-cape')
              <h3 class="ui dividing header">
                @lang('user.profile.appearence.cape')
              </h3>

              @if ($votesCount < 3)
                <div class="ui error message">
                  <div class="header">
                    @lang('form.error.title')
                  </div>
                  @lang('user.profile.appearence.cape.error.vote')
                </div>
              @elseif (Auth::user()->cape === 0)
                <div class="ui error message">
                  <div class="header">
                    @lang('form.error.title')
                  </div>
                  @lang('user.profile.appearence.cape.error.purchase')
                </div>
              @else
                <div class="ui card" style="width:500px;">
                  <div class="content">
                    <h4 class="ui sub header">@lang('user.profile.appearence.specifics')</h4>
                    <div class="ui small feed">
                      <div class="ui list">
                        <a class="item">
                          <i class="resize horizontal icon"></i>
                          <div class="content">
                            <div class="header">@lang('user.profile.appearence.specifics.width')</div>
                            <div class="description">@lang('user.profile.appearence.specifics.width.subtitle', ['max_width' => env('SKINS_UPLOAD_MAX_WIDTH'), 'max_height' => env('SKINS_UPLOAD_MAX_HEIGHT')])</div>
                          </div>
                        </a>
                        <a class="item">
                          <i class="compress icon"></i>
                          <div class="content">
                            <div class="header">@lang('user.profile.appearence.specifics.size')</div>
                            <div class="description">@lang('user.profile.appearence.specifics.size.subtitle', ['max_size' => round(env('SKINS_UPLOAD_MAX_SIXE') / 1000000)])</div>
                          </div>
                        </a>
                      </div>
                    </div>
                  </div>
                  <div class="extra content text-center">
                    <form action="{{ url('/user/cape') }}" method="post" data-ajax data-ajax-upload-image>
                      <div style="display:inline-block">
                        <label for="cape" class="ui labeled icon button">
                          <i class="file image outline icon"></i>
                          <span class="filename">@lang('user.profile.appearence.cape.choose')</span>
                        </label>
                        <input type="file" name="image" accept="image/x-png,image/png" id="cape" style="display:none">
                      </div>
                      <button type="submit" class="ui primary button">@lang('user.profile.appearence.cape.send')</button>
                    </form>
                  </div>
                </div>
              @endif
            @endpermission

          </div>

        </div>
      </div>
    </div>

  </div>

  @permission('user-request-edit-email')
    <div class="ui modal" id="editEmail">
      <i class="close icon"></i>
      <div class="header">
        @lang('user.profile.email.edit')
      </div>
      <div class="content">
        <form action="{{ url('/user/email') }}" method="post" data-ajax data-ajax-custom-callback="afterRequestedEditEmail">
        <div class="ui form">
          <h4 class="ui dividing header">@lang('user.profile.email.edit.subtitle')</h4>
          <div class="field">
            <label>@lang('user.field.email')</label>
            <input type="text" name="email">
          </div>
          <div class="field">
            <label>@lang('user.profile.email.edit.reason')</label>
            <textarea name="reason"></textarea>
          </div>
        </div>
      </div>
      <div class="actions">
        <button type="submit" class="ui green button">@lang('user.profile.email.edit.send')</button>
        </form>
      </div>
    </div>
  @endpermission
  @permission('user-edit-username')
    <div class="ui modal" id="editUsername">
      <i class="close icon"></i>
      <div class="header">
        @lang('user.profile.edit.username')
      </div>
      <div class="content">
        <div class="ui warning message">
          <i class="close icon"></i>
          <div class="header">
            @lang('global.warning')
          </div>
          @lang('user.profile.edit.username.warning')
        </div>
        <form action="{{ url('/user/username') }}" method="post" data-ajax data-ajax-custom-callback="afterRequestedEditUsername">
        <div class="ui form">
          <h4 class="ui dividing header">@lang('user.profile.edit.username.subtitle')</h4>
          <div class="field">
            <label>@lang('user.field.username')</label>
            <input type="text" name="username">
          </div>
          <div class="field">
            <label>@lang('user.field.password')</label>
            <input type="password" name="password">
          </div>
        </div>
      </div>
      <div class="actions">
        <button type="submit" class="ui green button">@lang('user.profile.edit.username.send')</button>
        </form>
      </div>
    </div>
  @endpermission
  @permission('user-transfer-money')
    <div class="ui modal" id="transferMoney">
      <i class="close icon"></i>
      <div class="header">
        @lang('user.profile.transfer.money')
      </div>
      <div class="content">
        <form action="{{ url('/user/money') }}" method="put" data-ajax data-ajax-custom-callback="afterRequestedTransferMoney">
        <div class="ui form">
          <h4 class="ui dividing header">@lang('user.profile.transfer.money.subtitle')</h4>
          <div class="field">
            <label>@lang('user.profile.transfer.money.field.amount')</label>
            <input type="number" name="amount">
          </div>
          <div class="field">
            <label>@lang('user.profile.transfer.money.field.to')</label>
            <input type="text" name="to">
          </div>
        </div>
      </div>
      <div class="actions">
        <button type="submit" class="ui green button">@lang('user.profile.transfer.money.send')</button>
        </form>
      </div>
    </div>
  @endpermission
@endsection
@section('style')
  <style media="screen">
    .ui.vertical.menu .left.aligned.icon {
      float: left;
      margin: 0 .5em 0 0;
    }
    .statistic .value .icon {
      font-size: 30px;
      line-height: 10px;
      cursor: pointer;
      color: #000;
    }
  </style>
@endsection
@section('script')
  <script type="text/javascript">
    function afterRequestedEditEmail(req, res) {
      $('.ui.modal#editEmail form .ui.form').slideUp(150)
      $('.ui.modal#editEmail .actions').remove()
    }
    function afterRequestedEditUsername(req, res) {
      $('.ui.modal#editUsername').modal('hide')
      $('#username').val(req.username)
      $('#usernameBtn').remove()
      toastr.success(res.success)
    }
    function afterRequestedTransferMoney(req, res) {
      $('.ui.modal#transferMoney').modal('hide')
      $('#money').html(res.money)
      toastr.success(res.success)
    }

    $(document).ready(function () {
      $('.toggle-menu[data-toggle]').on('click', function () {
        var btn = $(this)
        var menuName = btn.attr('data-toggle')
        var menu = $('.menu-content [data-menu="' + menuName +'"]')

        $('.toggle-menu[data-toggle].active').removeClass('active')
        btn.addClass('active')
        $('.menu-content [data-menu]:visible').fadeOut(150)
        setTimeout(function () {
          menu.fadeIn(100)
        }, 100)
      })
    })
    $(document).ready(function() {
      $('input[type="file"]').on('change', function () {
        var input = $(this)
        var label = input.parent().find('label')
        var filePath = input.val()

        if (filePath) {
          var startIndex = (filePath.indexOf('\\') >= 0 ? filePath.lastIndexOf('\\') : filePath.lastIndexOf('/'))
          var filename = filePath.substring(startIndex)
          if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0)
            filename = filename.substring(1)
          label.find('span.filename').html(filename)
        }
      })
    })
  </script>
@endsection
