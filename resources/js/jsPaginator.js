let uri = 'api/beers';

let options = {
    method: 'GET'
}

let req = new Request (uri, options);

fetch(req)
    .then( (res) => {
        if (res.ok){
            return res.json();
        } else {
            throw new Error ("Bad HTTP");
        }
    })
    .then( (j) => {
        console.log(j);
    })
    .catch ((err) => {
        console.log('Error:', err.message);
    })