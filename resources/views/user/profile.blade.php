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
          <a class="item toggle-menu" data-toggle="appearence">
            <i class="theme left aligned icon"></i>
            @lang('user.profile.menu.appearence')
          </a>
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
  </script>
@endsection
