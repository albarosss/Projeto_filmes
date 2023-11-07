var filmes = '';

var campo_search = document.getElementById('search-input');



var genres= {};
const apiKey = 'bf1ddac8920d395547c13e1bad46874c';

// 1. Obter uma lista de filmes (por exemplo, filmes populares)
const movieListUrl = `https://api.themoviedb.org/3/movie/popular?api_key=${apiKey}&language=pt-BR`;

fetch(movieListUrl)
  .then(response => {
    if (response.status === 200) {
      return response.json();
    } else {
      throw new Error('Falha ao obter a lista de filmes populares.');
    }
  })
  .then(data => {
    // Vamos supor que data.results é uma matriz de filmes
    const movies = data.results;

    // 2. Para cada filme, obtenha o nome do diretor, ator principal e outros detalhes
    movies.forEach(movie => {
      const movieId = movie.id;
      const creditsUrl = `https://api.themoviedb.org/3/movie/${movieId}/credits?api_key=${apiKey}&language=pt-BR`;
      const detailsUrl = `https://api.themoviedb.org/3/movie/${movieId}?api_key=${apiKey}&language=pt-BR`;

      fetch(creditsUrl)
        .then(response => {
          if (response.status === 200) {
            return response.json();
          } else {
            throw new Error('Falha ao obter os créditos do filme.');
          }
        })
        .then(creditsData => {
          const cast = creditsData.cast;
          const crew = creditsData.crew;

          // Encontre o diretor (normalmente, o diretor é creditado com o departamento 'Directing')
          const diretor = crew.find(member => member.department === 'Directing');
          const nomeDoDiretor = diretor ? diretor.name : 'Diretor desconhecido';

          // Encontre o ator principal (normalmente, o ator principal é o primeiro da lista de elenco)
          const atorPrincipal = cast.length > 0 ? cast[0].name : 'Ator principal desconhecido';

          fetch(detailsUrl)
            .then(response => {
              if (response.status === 200) {
                return response.json();
              } else {
                throw new Error('Falha ao obter os detalhes do filme.');
              }
            })
            .then(movieDetails => {
              const descricao = movieDetails.overview;
              const categorias = movieDetails.genres.map(genre => genre.name).join(', ');
              var categoria = categorias.split(',')
              const imagemUrl = `https://image.tmdb.org/t/p/w500${movieDetails.poster_path}`;

              console.log(`Filme: ${movie.title}`);
              console.log(`Diretor: ${nomeDoDiretor}`);
              console.log(`Ator Principal: ${atorPrincipal}`);
              console.log(`Descrição: ${descricao}`);
              console.log(`Categoria: ${categoria[0]}`);
              console.log(`URL da imagem: ${imagemUrl}`);
              console.log('---');
            })
            .catch(error => {
              console.error(error);
            });
        })
        .catch(error => {
          console.error(error);
        });
    });
  })
  .catch(error => {
    console.error(error);
  });



document.getElementById('button-random-film').addEventListener('click', function() {
    window.location.href = '/filmes/random';
});

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
    // mermaid.initialize({ startOnLoad: true });
    const categoriaButtons = document.querySelectorAll('.categoria-btn');
    const cardsContainer = document.querySelector('.cards-container');
    const semFilmesG = document.getElementById('no-films-message');
    categoriaButtons.forEach(async function (button) {
        button.addEventListener("click", async function (e) {
            e.preventDefault(); // Impede o comportamento padrão do link

            var genero = $(this).data("genero");
            await fetch(`http://127.0.0.1:8000/filmes/genero/${genero}`)
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Erro na requisição: ' + response.status);
                }
                return response.json(); // Isso irá ler e processar o corpo JSON
            })
            .then(function (data) {
                // Limpando o conteúdo atual
                cardsContainer.innerHTML = '';
                data.length == 0 ? semFilmesG.style.display = 'block' : semFilmesG.style.display = 'none';

                var currentUl; // Variável para acompanhar a lista <ul> atual

                // Iterando pelos novos dados e atualizando o DOM
                data.forEach(function (filme, index) {
                    // Criar um item da lista <li>
                    var cardListItem = document.createElement('li');
                    cardListItem.className = 'd-flex align-items-center';
                    cardListItem.style.margin = '10px';
                    cardListItem.style.padding = '5px';
                    cardListItem.style.height = 'fit-content';

                    var card = document.createElement('div');
                    card.className = 'card';
                    console.log(filme.urlimg);
                    var img = document.createElement('img');
                    img.src = filme.urlimg ? 'storage/' + filme.urlimg : 'storage/' + 'filmes_capa/capa_padrao.avif';
                    img.alt = 'Card Image';
                    img.style.width = '100%';

                    var cardContent = document.createElement('div');
                    cardContent.className = 'card-content';

                    var cardTitle = document.createElement('h3');
                    cardTitle.className = 'card-title';
                    cardTitle.textContent = filme.nome;

                    var cardDescription = document.createElement('p');
                    cardDescription.className = 'card-description';

                    var cardAvaliacao = document.createElement('b');
                    var cardAvaliacaoText = document.createElement('p');
                    cardAvaliacao.className = 'card-avaliacao';
                    cardAvaliacaoText.textContent = filme.media_avaliacao === 'Não Avaliado!' ? `Avaliação: ${filme.media_avaliacao} ` : `Avaliação: ${filme.media_avaliacao} ★`;
                    cardAvaliacao.appendChild(cardAvaliacaoText);

                    var cardButton = document.createElement('a');
                    cardButton.href = `{{ route('filmes.saiba_mais', ${filme.id}) }}`;
                    cardButton.className = 'card-button';
                    cardButton.textContent = 'Saiba mais';

                    cardContent.appendChild(cardTitle);
                    cardContent.appendChild(cardDescription);
                    cardContent.appendChild(cardAvaliacao);
                    cardContent.appendChild(cardButton);

                    card.appendChild(img);
                    card.appendChild(cardContent);

                    cardListItem.appendChild(card); // Anexar o card ao item da lista

                    if (index % 3 === 0) {
                        // Se este é o primeiro filme da nova lista, crie uma nova lista <ul>
                        currentUl = document.createElement('ul');
                        currentUl.className = 'list-group flex-row';
                        cardsContainer.appendChild(currentUl);
                    }

                    currentUl.appendChild(cardListItem); // Anexar o item da lista à lista <ul>
                });

                var isDropdownVisible = categoriaDropdownMenu.style.display === "block";
                categoriaDropdownMenu.style.display = isDropdownVisible ? "none" : "block";
            })
            .catch(function (error) {
                console.error(error);
            });
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





