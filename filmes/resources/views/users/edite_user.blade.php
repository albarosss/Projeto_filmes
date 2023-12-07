{{-- <x-layout title="Filmes" :mensagem-sucesso="$mensagemSucesso">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 container-edite-user">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-center">{{ __('Editar Perfil') }}</div>

                    <div class="card-body">
                        <form method="POST" class='mt-2' action="{{ route('users.atualizar') }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group p-2">
                                <label for="name">{{ __('Nome') }}</label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', auth()->user()->name) }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group p-2">
                                <label for="email">{{ __('Email') }}</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', auth()->user()->email) }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group p-2">
                                <label for="password">{{ __('Nova Senha') }}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group p-2">
                                <label for="password-confirm">{{ __('Confirmação de Senha') }}</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                            </div>

                            <div class="form-group p-2 mt-4 d-flex align-items-center justify-content-center">
                                <button type="submit" class="enviar-btn">
                                    Atualizar Perfil
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    </x-layout> --}}
