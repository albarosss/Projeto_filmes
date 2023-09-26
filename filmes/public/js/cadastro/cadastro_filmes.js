document.addEventListener("DOMContentLoaded", function () {
    const adicionarDiretorButton = document.getElementById("adicionarDiretor");
    adicionarDiretorButton.addEventListener("click", function () {
        const diretorNome = document.getElementById("diretorNome").value;
        console.log("AQ")
        if (diretorNome) {
            $.ajax({
                type: "POST",
                url: "{{ route('diretores.store') }}", // Substitua pela rota correta
                data: {
                    _token: "{{ csrf_token() }}",
                    nome: diretorNome
                },
                success: function (data) {
                    if (data.success) {
                        // Atualize o select de filmes
                        const diretorId = data.diretor.id;
                        const diretorNome = data.diretor.nome;

                        // Supondo que você tenha um select de filmes com ID "filmes"
                        const selectFilmes = document.getElementById("filmes");

                        // Crie uma nova opção para o diretor
                        const novaOpcao = document.createElement("option");
                        novaOpcao.value = diretorId;
                        novaOpcao.text = diretorNome;

                        // Adicione a nova opção ao select de filmes
                        selectFilmes.appendChild(novaOpcao);

                        // Limpe o campo de nome do diretor
                        document.getElementById("diretorNome").value = "";

                        alert("Diretor adicionado com sucesso!");
                    } else {
                        alert("Erro ao adicionar o diretor.");
                    }
                },
                error: function () {
                    alert("Erro ao adicionar o diretor.");
                }
            });
        } else {
            alert("Por favor, insira o nome do diretor.");
        }
    });
});
