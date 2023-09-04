<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }} - Controle de Filmes</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/card/card.css') }}">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/saiba_mais/saibamais.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/initial_page/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/edit_user/index.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-dark">
        <div class="container-fluid">
            <a href="{{ route('filmes.index') }}">
                <div class="div_bola bg-white"
                    style="background-image: url({{ asset('logos/logo.png') }});">
                </div>
            </a>

            @auth
            <div class="dropdown">
                <button class="dropbtn">
                    <img src="{{ asset('default_images/user.jpeg') }}" alt="Foto do Usuário" class="profile-image">
                </button>
                <div class="dropdown-content">
                    {{-- <a href="{{ route('perfil') }}" class="dropdown-item">Editar Perfil</a> --}}
                    <form action="{{ route('editar') }}" method="get">
                        @csrf
                        <button type="submit" class="btn-logout">Editar Perfil</button>
                    </form>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="btn-logout">Sair</button>
                    </form>
                </div>
            </div>
            @endauth

            @guest
            <a href="{{ route('login') }}" style="text-decoration: none;">
                <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-dark uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 bg-white">
                    Entrar
                </button>
            </a>
            @endguest
        </div>
    </nav>
<div class="container">
    <div class="w-100">
        <!-- Seu conteúdo aqui -->
        {{-- <h1>{{ $title }}</h1> --}}

        @isset($mensagemSucesso)
            <div class="alert alert-success">
                {{ $mensagemSucesso }}
            </div>
        @endisset

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </div>
</div>
</body>
</html>
