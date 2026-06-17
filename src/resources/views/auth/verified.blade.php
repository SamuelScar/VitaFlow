<x-layouts.public title="E-mail Verificado">
    <div class="container py-5 my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 text-center">
                <i class="bi bi-check-circle-fill text-success mb-4" style="font-size: 5rem;"></i>
                <h1 class="h2 fw-bold mb-3">E-mail verificado com sucesso!</h1>
                <p class="text-secondary fs-5 mb-5">
                    Sua conta agora está validada e você já pode aproveitar todas as funcionalidades do sistema, como a emissão da sua carteirinha de doador.
                </p>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                        Acessar meu painel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                        Fazer login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</x-layouts.public>
