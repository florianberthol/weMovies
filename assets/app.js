/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
const $ = require('jquery');
const bootstrap = require('bootstrap');

$(document).ready(function () {
    $('#search').on('click', function() {
        $('#result').html('');
        $('.genre').prop( "checked", false );
        $.get('/search?search=' + $('#search-value').val(), updateMovieListe);
    });

    $('.container').on('click', '.detail', function () {
        var id = $(this).data('id');
        $.get('/trailer/' + id, function (data) {
            console.log(data);
            if (data.name && data.youtube_url) {
                $('#modal .modal-body').html(
                    '<iframe width="560" height="315" src="' + data['youtube_url'] + '" title="YouTube video player"></iframe>' +
                    '<h3>' + data['name'] + '</h3>'
                );

                var modal = new bootstrap.Modal(document.getElementById('modal'));
                modal.toggle();
            } else {
                $('#modal .modal-body').html(
                    '<h3>Pas de trailer</h3>'
                );

                var modal = new bootstrap.Modal(document.getElementById('modal'));
                modal.toggle();
            }

        });
    });

    $('.genre').on('click', function () {
        $('#search-value').val('')
        var genres = $("input[name='genres[]']:checked").map(function () {
            return $(this).val();
        }).toArray();

        genres = genres.join('|');
        $.get('/movies?genres=' + genres, updateMovieListe);
    });
});

function updateMovieListe(data) {
    $('#result').html('');
    data.forEach(function (data){
        $('#result').append('<h2 class="d-inline-block">' + data.title + '</h2>&nbsp;&nbsp;&nbsp;&nbsp;' + data.vote_average + ' (votes ' + data.vote_count + ')');
        $('#result').append(
            '<div class="row">' +
            '<div class="col-4">' + '<img src="https://www.themoviedb.org/t/p/w150_and_h225_bestv2' + data.poster_path + '"/></div>' +
            '<p class="col-8">' + data.overview + '</p>' +
            '<div><div data-id="' + data.id + '" class="detail float-end btn btn-primary btn-lg" role="button" aria-disabled="true">Lire le detail</div></div>' +
            '</div>'
        );
        $('#result').append('<hr>');
    });
}

// any CSS you import will output into a single css file (app.css in this case)
import 'bootstrap/dist/css/bootstrap.css';
import './styles/app.css';
