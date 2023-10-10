var filmes = '';

var campo_search = document.getElementById('search-input');

campo_search.addEventListener("click", function () {
    fetch('http://127.0.0.1:8000/filmes/search')
     .then(function (response) {
         if (!response.ok) {
             throw new Error('Erro na requisição: ' + response.status);
         }
         return response.json();
     })
     .then(function (data) {
         filmes = data; // Armazene os dados dos filmes na variável
     })
});

var categoriaDropdownButton = document.getElementById("categoriaDropdown");

var categoriaDropdownMenu = document.querySelector(".dropdown-menu");

categoriaDropdownButton.addEventListener("click", function () {
    // Verifica se o menu de dropdown está visível
    var isDropdownVisible = categoriaDropdownMenu.style.display === "block";

    categoriaDropdownMenu.style.display = isDropdownVisible ? "none" : "block";
});


document.addEventListener("DOMContentLoaded", function ()
{
    const categoriaButtons = document.querySelectorAll('.categoria-btn');
    const filmesContainer = document.querySelector('.list-group.flex-row');
    const noFilmsMessage = document.getElementById('no-films-message'); // Elemento da mensagem
    const filmesOriginais = filmesContainer.innerHTML; // Armazene a lista original de filmes

    categoriaButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            filmesContainer.innerHTML = filmesOriginais;
            const generoSelecionado = button.getAttribute('data-genero');
            const filmesC = document.querySelectorAll('.filmeC');

            filmesContainer.innerHTML = ''; // Limpa o conteúdo atual

            let filmesExibidos = false; // Variável para controlar se filmes foram exibidos

            filmesC.forEach(function (filme) {
                const generoDoFilme = filme.getAttribute('data-genero');

                if (generoSelecionado === generoDoFilme || generoSelecionado === "todos") {
                    const li = document.createElement('li');
                    li.classList.add('d-flex', 'align-items-center');
                    li.style.margin = '10px';
                    li.style.padding = '5px';
                    li.style.width = 'fit-content';
                    li.style.height = 'fit-content';
                    li.appendChild(filme);
                    filmesContainer.appendChild(li);
                    filmesExibidos = true; // Filmes foram exibidos
                }
            });

            // Exiba a mensagem "Não há filmes" quando nenhum filme for exibido
            if (!filmesExibidos) {
                noFilmsMessage.style.display = 'block';
            } else {
                noFilmsMessage.style.display = 'none';
            }

            if (generoSelecionado === "todos") {
                filmesContainer.innerHTML = filmesOriginais;
                noFilmsMessage.style.display = 'none'; // Oculta a mensagem quando "todos" é selecionado
            }
        });
    });



    const searchInput = document.getElementById("search-input");
    const filmeDropdown = document.getElementById("filme-dropdown");
    const containerDrop = document.getElementsByClassName('dropdown-filme');


    searchInput.addEventListener("input", function ()
    {
        const searchTerm = searchInput.value.toLowerCase();

        if (searchTerm.length > 0)
        {
            filmeDropdown.style.display = "block";
        } else
        {
            filmeDropdown.style.display = "none";
        }

        filmeDropdown.innerHTML = "";
        const filmesEncontrados = filmes.filter((filme) =>
            filme.nome.toLowerCase().includes(searchTerm)
        );
        if (filmesEncontrados.length === 0)
        {
            containerDrop[0].style.right= "43.8%"
            const mensagemErro = document.createElement("a");
            mensagemErro.textContent = "Nenhum filme encontrado.";
            mensagemErro.style.padding = "12px 16px";
            mensagemErro.style.fontWeight = "bold";
            mensagemErro.style.whiteSpace = "nowrap";

            if (searchTerm.length === 0)
            {
                mensagemErro.style.color = "#777";
            }

            filmeDropdown.appendChild(mensagemErro);
        }else
        {
            containerDrop[0].style.right= "44.8%"
            filmesEncontrados.forEach((filme) =>
            {
                const link = document.createElement("a");
                link.textContent = filme.nome;
                link.href = `/filmes/${filme.id}/filmes`;
                filmeDropdown.appendChild(link);
            });
        }
    });
});





