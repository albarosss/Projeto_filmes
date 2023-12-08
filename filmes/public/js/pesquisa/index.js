var filmes = '';

var campo_search = document.getElementById('search-input');

const cardContainer = document.querySelector('.cards-container');

const filmesNoContainer = cardContainer && cardContainer.querySelectorAll('.card');

const runMoviesFetching = async () =>
{
    const genres = {};
    const apiKey = 'bf1ddac8920d395547c13e1bad46874c';

    const fetchMovieDetails = async (movie) => {
        const movieId = movie.id;

        if (genres[movieId]) {
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

            if (!movieDetails.overview || movieDetails.overview.trim() === '') {
                return;
            }
            const cast = creditsData.cast;
            const crew = creditsData.crew;

            const diretor = crew.find(member => member.department === 'Directing');
            const nomeDoDiretor = diretor ? diretor.name : 'Diretor desconhecido';

            const atorPrincipal = cast.length > 0 ? cast[0].name : 'Ator principal desconhecido';

            const descricao = movieDetails.overview || 'Sem descrição disponível';

            const categorias = movieDetails.genres.map(genre => genre.name).join(', ');
            const categoria = categorias.split(',');

            const imagemUrl = movieDetails.poster_path ? `https://image.tmdb.org/t/p/w500${movieDetails.poster_path}` : null;

            if (!imagemUrl) {
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

    const fetchMovies = async (pageNumber) => {
        const movieListUrl = `https://api.themoviedb.org/3/discover/movie?api_key=${apiKey}&language=pt-BR&page=${pageNumber}`;

        try {
            const response = await fetch(movieListUrl);

            if (!response.ok) {
                throw new Error(`Falha ao obter a lista de filmes. Página: ${pageNumber}`);
            }

            const data = await response.json();
            const movies = data.results;

            for (const movie of movies) {
                await fetchMovieDetails(movie);

                if (Object.keys(genres).length >= 1000) {
                    return; // Stop fetching if already collected 1000 movies
                }
            }
        } catch (error) {
            console.error(`Erro ao obter filmes. Página: ${pageNumber}`, error);
        }
    };

    const fetchMoviesRecursively = async (startPage, endPage,) => {
        let pageNumber = startPage;

        while (pageNumber <= endPage && Object.keys(genres).length < 1000) {
            await fetchMovies(pageNumber);
            pageNumber++;
        }
    };


    await fetchMoviesRecursively(1, 5);

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const filmesArrays = {};

    for (const id in genres) {
        const filme = genres[id];
        for (const propriedade in filme) {
            if (!filmesArrays[propriedade]) {
                filmesArrays[propriedade] = [];
            }
            filmesArrays[propriedade].push(filme[propriedade]);
        }
    }

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
            localStorage.setItem('moviesRequestProcessed', 'true');

            window.location.reload();
        })
        .catch(error => {
            console.error('Erro na solicitação:', error);


            if (error.response && error.response.text) {
                console.error('Corpo da resposta:', error.response.text());
            }
        });
};


if (!filmesNoContainer || filmesNoContainer.length < 10) {
    runMoviesFetching();
} else {
    console.log('Já existem filmes no card-container.');
}


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


document.addEventListener("DOMContentLoaded", async function ()
{
    const categoriaButtons = document.querySelectorAll('.categoria-btn');

    categoriaButtons.forEach(async function (button)
    {
        button.addEventListener("click", async function (e) {
            e.preventDefault();

            var genero = $(this).data("genero");

            if (window.location.pathname !== '/filmes') {
                window.location.href = '/filmes';
                localStorage.setItem('generoParaFiltrar', genero);
            }else{
                await filtrarFilmes(genero);
            }

        });
    });

    const generoArmazenado = localStorage.getItem('generoParaFiltrar');
    if (generoArmazenado)
    {
        localStorage.removeItem('generoParaFiltrar');
        await filtrarFilmes(generoArmazenado);

        var isDropdownVisible = categoriaDropdownMenu.style.display === "block";
        categoriaDropdownMenu.style.display = isDropdownVisible ? "none" : "block";
    }



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

    document.body.addEventListener("click", function (e) {
        if (!filmeDropdown.contains(e.target) && e.target !== searchInput) {
            filmeDropdown.style.display = "none";
        }
    });

    filmeDropdown.addEventListener("click", function (e) {
    });

    filmeDropdown.addEventListener("transitionend", function () {
        if (filmeDropdown.style.display === "none") {
            document.body.removeEventListener("click", outsideClickHandler);
        }
    });

    function outsideClickHandler(e) {
        if (!filmeDropdown.contains(e.target) && e.target !== searchInput) {
            filmeDropdown.style.display = "none";
            document.body.removeEventListener("click", outsideClickHandler);
        }
    }

    function showDropdown() {
        filmeDropdown.style.display = "block";
        document.body.addEventListener("click", outsideClickHandler);
    }

    function hideDropdown() {
        filmeDropdown.style.display = "none";
        document.body.removeEventListener("click", outsideClickHandler);
    }

    searchInput.addEventListener("click", function (e) {
        e.stopPropagation();
        showDropdown();
    });
});







async function filtrarFilmes(genero, fora = false)
{
    const cardsContainer = document.querySelector('.cards-container');
    const semFilmesG = document.getElementById('no-films-message');

    await fetch(`http://127.0.0.1:8000/filmes/genero/${genero}`)
    .then(function (response)
    {
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
            var img = document.createElement('img');
            img.src = filme.urlimg.startsWith("https") ? filme.urlimg : `storage/${filme.urlimg}`;
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
            console.log(filme)
            cardAvaliacaoText.textContent = filme.media_avaliacao === 'Não Avaliado!' ? `Avaliação: ${filme.media_avaliacao} ` : `Avaliação: ${filme.media_avaliacao} ★`;
            cardDescription.textContent = filme.descricao;
            cardAvaliacao.appendChild(cardAvaliacaoText);

            var cardButton = document.createElement('a');
            cardButton.href = `/filmes/${filme.id}/filmes`;
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
}
