var filmes = '';

var campo_search = document.getElementById('search-input');



// var genres= {};
// const apiKey = 'bf1ddac8920d395547c13e1bad46874c';

// // 1. Obter uma lista de filmes (por exemplo, filmes populares)
// const movieListUrl = `https://api.themoviedb.org/3/movie/popular?api_key=${apiKey}&language=pt-BR`;

// fetch(movieListUrl)
//   .then(response => {
//     if (response.status === 200) {
//       return response.json();
//     } else {
//       throw new Error('Falha ao obter a lista de filmes populares.');
//     }
//   })
//   .then(data => {
//     // Vamos supor que data.results é uma matriz de filmes
//     const movies = data.results;

//     // 2. Para cada filme, obtenha o nome do diretor, ator principal e outros detalhes
//     movies.forEach(movie => {
//       const movieId = movie.id;
//       const creditsUrl = `https://api.themoviedb.org/3/movie/${movieId}/credits?api_key=${apiKey}&language=pt-BR`;
//       const detailsUrl = `https://api.themoviedb.org/3/movie/${movieId}?api_key=${apiKey}&language=pt-BR`;

//       fetch(creditsUrl)
//         .then(response => {
//           if (response.status === 200) {
//             return response.json();
//           } else {
//             throw new Error('Falha ao obter os créditos do filme.');
//           }
//         })
//         .then(creditsData => {
//           const cast = creditsData.cast;
//           const crew = creditsData.crew;

//           // Encontre o diretor (normalmente, o diretor é creditado com o departamento 'Directing')
//           const diretor = crew.find(member => member.department === 'Directing');
//           const nomeDoDiretor = diretor ? diretor.name : 'Diretor desconhecido';

//           // Encontre o ator principal (normalmente, o ator principal é o primeiro da lista de elenco)
//           const atorPrincipal = cast.length > 0 ? cast[0].name : 'Ator principal desconhecido';

//           fetch(detailsUrl)
//             .then(response => {
//               if (response.status === 200) {
//                 return response.json();
//               } else {
//                 throw new Error('Falha ao obter os detalhes do filme.');
//               }
//             })
//             .then(movieDetails => {
//               const descricao = movieDetails.overview;
//               const categorias = movieDetails.genres.map(genre => genre.name).join(', ');
//               var categoria = categorias.split(',')
//               const imagemUrl = `https://image.tmdb.org/t/p/w500${movieDetails.poster_path}`;

//               console.log(`Filme: ${movie.title}`);
//               console.log(`Diretor: ${nomeDoDiretor}`);
//               console.log(`Ator Principal: ${atorPrincipal}`);
//               console.log(`Descrição: ${descricao}`);
//               console.log(`Categoria: ${categoria[0]}`);
//               console.log(`URL da imagem: ${imagemUrl}`);
//               console.log('---');
//             })
//             .catch(error => {
//               console.error(error);
//             });
//         })
//         .catch(error => {
//           console.error(error);
//         });
//     });
//   })
//   .catch(error => {
//     console.error(error);
//   });

// Use a função recursiva com parâmetros de gênero e ano

const runMoviesFetching = async () =>
{
    const genres = {};
    const apiKey = 'bf1ddac8920d395547c13e1bad46874c';
    const moviesPerPage = 20;
    const delayBetweenRequests = 500;

    const fetchMovieDetails = async (movie) => {
        const movieId = movie.id;

        if (genres[movieId]) {
            console.log('Filme já processado. Ignorando:', movie.title);
            return;
        }

        const creditsUrl = `https://api.themoviedb.org/3/movie/${movieId}/credits?api_key=${apiKey}&language=pt-BR`;
        const detailsUrl = `https://api.themoviedb.org/3/movie/${movieId}?api_key=${apiKey}&language=pt-BR`;

        try {
            const [creditsResponse, detailsResponse] = await Promise.all([
                fetch(creditsUrl),
                fetch(detailsUrl)
            ]);

            if (!creditsResponse.ok || !detailsResponse.ok) {
                throw new Error('Falha ao obter os créditos ou detalhes do filme.');
            }

            const [creditsData, movieDetails] = await Promise.all([
                creditsResponse.json(),
                detailsResponse.json()
            ]);

            const cast = creditsData.cast;
            const crew = creditsData.crew;

            const diretor = crew.find(member => member.department === 'Directing');
            const nomeDoDiretor = diretor ? diretor.name : 'Diretor desconhecido';

            const atorPrincipal = cast.length > 0 ? cast[0].name : 'Ator principal desconhecido';

            // Tratando a descrição para evitar valores nulos
            const descricao = movieDetails.overview || 'Sem descrição disponível';

            const categorias = movieDetails.genres.map(genre => genre.name).join(', ');
            const categoria = categorias.split(',');

            // Verificar se a URL da imagem é nula antes de construir o URL
            const imagemUrl = movieDetails.poster_path ? `https://image.tmdb.org/t/p/w500${movieDetails.poster_path}` : null;

            // Se a URL da imagem for nula, ignore o filme
            if (!imagemUrl) {
                console.log('Imagem nula. Ignorando:', movie.title);
                return;
            }

            const movieObject = {
                nome: movie.title,
                diretor: nomeDoDiretor,
                atorPrincipal: atorPrincipal,
                descricao: descricao,
                categoria: categoria[0] || 'Sem categoria',
                imagemUrl: imagemUrl
            };

            genres[movieId] = movieObject;

        } catch (error) {
            console.error('Erro ao processar filme:', error);
        }
    };

    const fetchMovies = async (startPage, endPage, genreId, releaseYear) => {
        let pageNumber = startPage;
        let totalMoviesProcessed = 0;

        while (pageNumber <= endPage) {
            const movieListUrl = `https://api.themoviedb.org/3/discover/movie?api_key=${apiKey}&language=pt-BR&page=${pageNumber}&with_genres=${genreId}&primary_release_year=${releaseYear}`;

            try {
                const response = await fetch(movieListUrl);

                if (!response.ok) {
                    throw new Error(`Falha ao obter a lista de filmes. Página: ${pageNumber}`);
                }

                const data = await response.json();
                const movies = data.results;

                await Promise.all(movies.map(movie => fetchMovieDetails(movie)));

                totalMoviesProcessed += movies.length;
                pageNumber++;
            } catch (error) {
                console.error(`Erro ao obter filmes. Página: ${pageNumber}`, error);
                break;
            }

            // Um intervalo antes de fazer a próxima solicitação
            await new Promise(resolve => setTimeout(resolve, delayBetweenRequests));
        }

        return totalMoviesProcessed;
    };

    const fetchMoviesRecursively = async (startPage, endPage, targetMovieCount, genreId, releaseYear) => {
        if (startPage <= endPage) {
            const currentMovies = await fetchMovies(startPage, endPage, genreId, releaseYear);

            if (currentMovies !== undefined) {
                const currentMovieCount = currentMovies.length;

                if (currentMovieCount < targetMovieCount) {
                    await fetchMoviesRecursively(endPage + 1, endPage + 50, targetMovieCount, genreId, releaseYear);
                }
            } else {
                console.error('Erro ao obter filmes. A função fetchMovies não retornou dados.');
            }
        }
    };

    await fetchMoviesRecursively(50, 500, 10000, 28, 2022);


    // Exiba o objeto genres com todos os filmes
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const filmesArrays = {};

    // Itera sobre os filmes
    for (const id in genres) {
        const filme = genres[id];
        for (const propriedade in filme) {
            if (!filmesArrays[propriedade]) {
                filmesArrays[propriedade] = [];
            }
            filmesArrays[propriedade].push(filme[propriedade]);
        }
    }

    console.log(filmesArrays);

    fetch('filmes/createApi',
    {
        method: 'POST',
        headers:
        {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        redirect: 'follow',
        body: JSON.stringify(filmesArrays),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Erro na solicitação: ${response.status}`);
        }

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Resposta do servidor não é JSON.');
        }

        return response.json();
    })
        .then(data => {
            // resposta do servidor aqui
            console.log('Resposta do servidor:', data);
        })
        .catch(error => {
            console.error('Erro na solicitação:', error);

            // Adicionei logs adicionais para depuração
            console.log('Objeto genres:', genres);
            console.log('Filmes Arrays:', filmesArrays);

            if (error.response && error.response.text) {
                console.error('Corpo da resposta:', error.response.text());
            }
        });


};

runMoviesFetching();





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
    .then(function (data)
    {
        filmes = data;
    })
});

var categoriaDropdownButton = document.getElementById("categoriaDropdown");

var categoriaDropdownMenu = document.querySelector(".dropdown-menu");

categoriaDropdownButton.addEventListener("click", function () {
    var isDropdownVisible = categoriaDropdownMenu.style.display === "block";

    categoriaDropdownMenu.style.display = isDropdownVisible ? "none" : "block";
});


document.addEventListener("DOMContentLoaded", function ()
{
    const categoriaButtons = document.querySelectorAll('.categoria-btn');
    const cardsContainer = document.querySelector('.cards-container');
    const semFilmesG = document.getElementById('no-films-message');
    categoriaButtons.forEach(async function (button) {
        button.addEventListener("click", async function (e) {
            e.preventDefault();

            var genero = $(this).data("genero");
            await fetch(`http://127.0.0.1:8000/filmes/genero/${genero}`)
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Erro na requisição: ' + response.status);
                }
                return response.json();
            })
            .then(function (data) {
                cardsContainer.innerHTML = '';
                data.length == 0 ? semFilmesG.style.display = 'block' : semFilmesG.style.display = 'none';


                data.forEach(function (filme, index) {
                    var cardListItem = document.createElement('li');
                    cardListItem.className = 'd-flex align-items-center';
                    cardListItem.style.margin = '10px';
                    cardListItem.style.padding = '5px';
                    cardListItem.style.height = 'fit-content';

                    var card = document.createElement('div');
                    card.className = 'card';
                    console.log(filme.urlimg);
                    var img = document.createElement('img');
                    img.src = filme.urlimg;
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

                    cardListItem.appendChild(card);

                    if (index % 3 === 0) {
                        currentUl = document.createElement('ul');
                        currentUl.className = 'list-group flex-row';
                        cardsContainer.appendChild(currentUl);
                    }

                    currentUl.appendChild(cardListItem);
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

        const arrayDeFilmes = Object.values(filmes);

        const filmesEncontrados = arrayDeFilmes.filter((filme) =>
            filme.nome.toLowerCase().includes(searchTerm.toLowerCase())
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





