
function ajax(url, callback, data) {
    var request = (window.XMLHttpRequest ? new XMLHttpRequest() : (window.ActiveXObject ? new ActiveXObject('Microsoft.XMLHTTP') : false));
    if (request) {
        request.open((data ? 'POST' : 'GET'), url);

        request.onreadystatechange = function () {
            if (request.readyState === 4 && request.status === 200) {
                callback(request);
            }
        };
        request.send(data);
    }
    return request;
}


ajax("http://csfdapi.cz/movie/" + encodeURIComponent(document.getElementById("csfd_id").innerHTML), function (data) {
    var data = (typeof JSON.parse === 'function') ? JSON.parse(data.responseText) : eval('(' + data.responseText + ')');
    if (!data.length) {
        if (data.rating != null)
            document.getElementsByClassName("rating")[0].innerHTML = data.rating + "%";

        if (data.countries != null)
            document.getElementById("countries").innerHTML = data.countries.join(" / ") + " ";

        var directors = data.authors.directors;
        var d = new Array();
        for (var i = 0; i < directors.length; i++) {
            if (i == 25)
                break;
            d[i] = '<a href="' + directors[i].csfd_url + '">' + directors[i].name + '</a>';
        }
        document.getElementById("directors").innerHTML = "Režie: " + d.join(", ");

        var actors = data.authors.actors;
        var a = new Array();
        for (var i = 0; i < actors.length; i++) {
            if (i == 50)
                break;
            a[i] = '<a href="' + actors[i].csfd_url + '">' + actors[i].name + '</a>';
        }
        document.getElementById("actors").innerHTML = "Hrají: " + a.join(", ");
    }
});

ajax("http://www.omdbapi.com/?t=" + encodeURIComponent(document.getElementById("name_en").innerHTML) + "&y=" + encodeURIComponent(document.getElementById("year").innerHTML) + "&plot=short&r=json", function (data) {
    var data = (typeof JSON.parse === 'function') ? JSON.parse(data.responseText) : eval('(' + data.responseText + ')');
    if (!data.length) {

        if (data.imdbRating != null) {
            document.getElementsByClassName("rating")[1].innerHTML = data.imdbRating;
            document.getElementsByClassName("ratingIMDB")[0].style.display = "block";
        }

    }
});
