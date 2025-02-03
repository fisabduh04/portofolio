import './bootstrap';


import 'flowbite';
import { DataTable } from "simple-datatables";
import ImageKit from "imagekit-javascript";


// simple DataTable
function initDataTable(id) {
    const dataTable = new DataTable(id, {
        searchable: true,
        sortable: true,
    });
}

document.addEventListener('DOMContentLoaded', function() {
    console.log("masuk 2");
    initDataTable("#search-table");
});


// upload image
// document.getElementById("dropzone-file").addEventListener("change", function(event) {
//     upload(event);
// });


// API Image Kit
// SDK initialization


var imagekit = new ImageKit({
    publicKey : "public_WSkMnkBf0PViGDCip6CuNW4Q1XI=",
    urlEndpoint : "https://ik.imagekit.io/n2wxnicud",
    authenticationEndpoint : "http://www.yourserver.com/auth",
});

// URL generation
var imageURL = imagekit.url({
    path : "/default-image.jpg",
    transformation : [{
        "height" : "300",
        "width" : "400"
    }]
});

// Upload function internally uses the ImageKit.io javascript SDK
function upload(event) {
    // var file = document.getElementById("file1");
    console.log(event);
    imagekit.upload({
        file : event.target.files[0],
        fileName : "abc1.jpg",
        tags : ["tag1"]
    }, function(err, result) {
        console.log(arguments);
        console.log(err);

        // console.log(imagekit.url({
        //     src: result.url,
        //     transformation : [{ height: 300, width: 400}]
        // }));
    })
}
