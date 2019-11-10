@extends('layouts.temp')

@section('titel', ucfirst(__('ui.titel.settings')).' von '.Auth::user()->name)

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.settings')).' von '.Auth::user()->name }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.titel.settings')).' von ' }}
            </h1>
            <h4>
                {{ Auth::user()->name }}
            </h4>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
    </div>
    <div class="row justify-content-center">
            <div class="col-3">
                <div class="card">
                    <div class="card-header">
                        {{ __('ui.personalSettings.title') }}
                    </div>
                    <div class="card-body">
                        <div class="nav flex-column nav-pills" id="settings-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" id="settings-profile-tab" data-toggle="pill" href="#settings-profile" role="tab" aria-controls="settings-profile" aria-selected="true">{{ __('ui.personalSettings.profile') }}</a>
                            <a class="nav-link" id="settings-account-tab" data-toggle="pill" href="#settings-account" role="tab" aria-controls="settings-account" aria-selected="false">{{ __('ui.personalSettings.account') }}</a>
                            <a class="nav-link" id="settings-connection-tab" data-toggle="pill" href="#settings-connection" role="tab" aria-controls="settings-connection" aria-selected="false">{{ __('ui.personalSettings.connection') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-9">
                <div class="card">
                    <div class="card-header">
                        <h5 id="settings-card-title" class="card-title">{{ __('ui.personalSettings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="settings-tabContent">
                            <div class="tab-pane fade show active" id="settings-profile" role="tabpanel" aria-labelledby="settings-profile-tab" data-title="{{ __('ui.personalSettings.profile') }}">
                                {{ __('ui.personalSettings.profile') }}
                                <div class="text-center">
                                    <div class="btn-group position-absolute p-1" role="group">
                                        <button id="btnGroupDrop1" type="button" class="btn btn-dark dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-user-edit"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                            <a class="dropdown-item" href="#">
                                                {{ __('ui.personalSettings.uploadeImage') }}
                                                <input id="imgUpload" type="file" name="file" style="position: absolute;font-size: 20px;opacity: 0;right: 0;top: 0; width: 265px"/>
                                            </a>
                                            <a class="dropdown-item" href="#" onclick="destroy()">
                                                {{ __('ui.personalSettings.deleteImage') }}
                                            </a>
                                        </div>
                                    </div>
                                    <img id="avatarImage" src="{{ Auth::user()->avatarPath() }}" class="rounded img-thumbnail" alt="" style="width: 200px; height: 200px">
                                    <div id="avatar-errors" class="text-danger"></div>
                                </div>
                                <form>
                                    <div class="form-group">
                                        <label for="name">{{ __('user.name') }}</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="name" placeholder="{{ Auth::user()->name }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text text-danger" id="basic-addon2">{{ __('ui.personalSettings.workInProgress') }}</span>
                                            </div>
                                            <div id="name-errors" class="text-danger"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">{{ __('user.mailAddress') }}</label>
                                        <div class="input-group">
                                            <input type="email" class="form-control" id="email" placeholder="{{ Auth::user()->email }}" aria-describedby="basic-addon2" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text text-danger" id="basic-addon2">{{ __('ui.personalSettings.workInProgress') }}</span>
                                            </div>
                                            <div id="email-errors" class="text-danger"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="birthday">{{ __('ui.personalSettings.birthday') }}</label>
                                        <input id="date_picker" name="birthday" class="form-control" type="text">
                                        <div id="birthday-errors" class="text-danger"></div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="settings-account" role="tabpanel" aria-labelledby="settings-account-tab" data-title="{{ __('ui.personalSettings.account') }}">
                                <form>
                                    <div class="form-group">
                                        <label for="skype">{{ __('ui.personalSettings.skype') }} <i class="fab fa-skype h3" style="color: #00AFF0;"></i></label>
                                        <input type="text" class="form-control" id="skype" placeholder="SkypeName" value="{{ Auth::user()->profile->skype }}">
                                        <div id="skype-errors" class="text-danger"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="discord">{{ __('ui.personalSettings.discord') }} <i class="fab fa-discord h3" style="color: #738ADB;"></i></label>
                                        <input type="email" class="form-control" id="discord" placeholder="DiscordName#1234" value="{{ Auth::user()->profile->discord }}">
                                        <div id="discord-errors" class="text-danger"></div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="settings-connection" role="tabpanel" aria-labelledby="settings-connection-tab" data-title="{{ __('ui.personalSettings.connection') }}">
                                @foreach($serversList as $list)
                                    <h5><span class="flag-icon flag-icon-{{ $list->flag }}"></span> <a href="{{$list->url}}">{{ $list->url }}</a></h5>
                                    <ul class="list-group mb-5">
                                        <li class="list-group-item">
                                            <div class="row">
                                                <div class="col-3 my-auto text-center"><b>{{ __('ui.server.worlds') }}</b></div>
                                                <div class="col-6 my-auto text-center"><b>{{ __('ui.table.player') }}</b></div>
                                                <div class="col-3 text-center"><b>{{ __('global.action') }}</b></div>
                                            </div>
                                        </li>
                                        @foreach($list->worlds()->where('active', '!=', null)->get() as $listWorld)
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-3 my-auto text-center">{{ $listWorld->displayName() }}</div>
                                                    <div class="col-6 my-auto text-center"><b>skatecram</b></div>
                                                    <div class="col-3 text-center"><button type="button" class="btn btn-success">{{ __('global.add') }}</button></div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plugin/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $("#date_picker").datepicker({
                language: 'all',
                format:'dd.mm.yyyy',
                weekStart:1,
            })
        });

        $.fn.datepicker.dates['all'] = {
            days: ["{{ __('datepicker.Sunday') }}", "{{ __('datepicker.Monday') }}", "{{ __('datepicker.Tuesday') }}", "{{ __('datepicker.Wednesday') }}", "{{ __('datepicker.Thursday') }}", "{{ __('datepicker.Friday') }}", "{{ __('datepicker.Saturday') }}"],
            daysShort: ["{{ __('datepicker.Sun') }}", "{{ __('datepicker.Mon') }}", "{{ __('datepicker.Tue') }}", "{{ __('datepicker.Wed') }}", "{{ __('datepicker.Thu') }}", "{{ __('datepicker.Fri') }}", "{{ __('datepicker.Sat') }}"],
            daysMin: ["{{ __('datepicker.Su') }}", "{{ __('datepicker.Mo') }}", "{{ __('datepicker.Tu') }}", "{{ __('datepicker.We') }}", "{{ __('datepicker.Th') }}", "{{ __('datepicker.Fr') }}", "{{ __('datepicker.Sa') }}"],
            months: ["{{ __('datepicker.January') }}", "{{ __('datepicker.February') }}", "{{ __('datepicker.March') }}", "{{ __('datepicker.April') }}", "{{ __('datepicker.May') }}", "{{ __('datepicker.June') }}", "{{ __('datepicker.July') }}", "{{ __('datepicker.August') }}", "{{ __('datepicker.September') }}", "{{ __('datepicker.October') }}", "{{ __('datepicker.November') }}", "{{ __('datepicker.December') }}"],
            monthsShort: ["{{ __('datepicker.Jan') }}", "{{ __('datepicker.Feb') }}", "{{ __('datepicker.Mar') }}", "{{ __('datepicker.Apr') }}", "{{ __('datepicker.May') }}", "{{ __('datepicker.Jun') }}", "{{ __('datepicker.Jul') }}", "{{ __('datepicker.Aug') }}", "{{ __('datepicker.Sep') }}", "{{ __('datepicker.Oct') }}", "{{ __('datepicker.Nov') }}", "{{ __('datepicker.Dec') }}"],
            today: "{{ __('datepicker.Today') }}",
            monthsTitle: "{{ __('datepicker.months') }}",
            clear: "{{ __('datepicker.Clear') }}",
        };

        $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            console.log($(this)) // newly activated tab
            $('#settings-card-title').html($($(this).attr('href')).data('title'))
        });

        $(document).on('change','#imgUpload' , function(){
            const file = this.files[0];

            var formData = new FormData();
            formData.append("file", file);
            axios.post('{{ route('user.uploadeImage') }}', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then((response) => {
                    $('#avatarImage').attr('src', '/storage/app/' + response.data.img);
                    $('#avatar-errors').html('');
                })
                .catch((error) => {
                    var errors = error.response.data.errors['file'];
                    $('#avatar-errors').html(errors[0].replace('file', '{{ __('global.file') }}'));
                });
        });

        function destroy() {
            axios.post('{{ route('user.destroyImage') }}')
                .then((response) => {
                    $('#avatarImage').attr('src', '{{ asset('images/default/user.png') }}')
                })
                .catch((error) => {
                });
        }
    </script>
@stop
