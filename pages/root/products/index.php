<?
if(!isset($_COOKIE['userdata'])){
    echo "No user data";
    header('Location: http://localhost/keasyshoppe/');
} else {
    $userdata = json_decode($_COOKIE['userdata'], true);
    if(json_decode($userdata['userdata'], true)['accountType'] != 'ADMIN'){
        echo "Not an admin";
        header('Location: http://localhost/');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Products &middot; Keasyshoppe&mdash;Shop 'till you drop</title>
        <link rel="stylesheet" href="http://localhost/keasyshoppe/css/materialize.css">
        <link rel="stylesheet" href="http://localhost/keasyshoppe/css/materialdesignicons.css">
        <link rel="stylesheet" href="http://localhost/keasyshoppe/css/style.css">
    </head>

    <body>
        <div class="navbar-fixed">
            <nav class="nav-extended sad-crimson">
                <div class="nav-wrapper">

                </div>
                <div class="progress light-blue lighten-4" style="margin: 0 !important; display: none;" id="preloader">
                    <div class="indeterminate light-blue"></div>
                </div>
            </nav>
        </div>
        <main class="row section" id="main">
            <div class="col s12 center" id="emptyState">
                <p class="flow-text">You have no products yet.</p>
                <button style="display: inline;" onclick="$('#main').slideToggle(); $('#addProducts').slideToggle();" class="btn sad-crimson waves-effect waves-block">Add products</button>
            </div>
            <section class="row section" style="display: none;" id="showProducts">

            </section>
        </main>
        
        <main class="section" style="display: none;" id="addProducts">
            <form action="./addproduct.php" method="post" id="addProductForm" enctype="multipart/formdata">
                <div class="row section">
                    <div class="col s12 m4 l3">
                        <div class="card hoverable">
                            <div class="card-image">
                                <img src="http://localhost/keasyshoppe/images/user-offline-symbolic.svg" id="mainImageUserFile">
                                <!-- <p class="card-title">Add an image for your product</p> -->
                                <div class="progress" style="margin: 0 !important; display:none;" id="mainImage_progress">
                                    <div class="indeterminate"></div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="file-field input-field">
                                    <div class="btn-flat waves-effect light-blue-text">
                                        <span><i class="mdi mdi-image-multiple left"></i>
                                            <span id="file-button">Add</span>
                                        </span>
                                        <input type="file" accept="image/*" onchange="displayMainImage(this.files)">
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input class="file-path validate" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="col s12 btn-flat waves-effect sad-waves-light-blue light-blue-text" onclick="$('#secondaryImages').click()"><i class="mdi mdi-image-multiple left"></i> Add secondary images</button>
                        <input multiple accepts="images/*" id="secondaryImages" name="secondaryImages" hidden onchange="displaySecondaryImages(this.files)"
                            type="file" />
                        <div class="col s12 sad-cards-container"></div>
                    </div>
                    <div class="col s12 m8 l9">
                        <div class="row section">
                            <div class="input-field col s12 m7 l8">
                                <input type="text" name="prodName" id="prodName" />
                                <label for="prodName">Product Name</label>
                            </div>
                            <div class="input-field col s12 m5 l4">
                                <input type="number" name="prodprice" id="prodPrice" />
                                <label for="prodPrice">Price</label>
                            </div>
                            <div class="input-field col s12 m12 l12">
                                <textarea name="prodDesc" id="prodDesc" cols="30" rows="10" class="materialize-textarea"></textarea>
                                <label for="prodDesc">Product description</label>
                            </div>
                            <div class="input-field col s12 m4 l4">
                                <select name="prodCategory" id="prodCategory">
                                    <option value="0" selected disabled>Select category</option>
                                </select>
                            </div>
                            <div class="input-field col s12 m8 l8">
                                <div class="chips"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right" style="position: fixed; bottom: 0; right: 0; padding: 10px;">
                    <button type="reset" onclick="$('#main').slideToggle(); $('#addProducts').slideToggle();" class="btn-flat light-blue-text sad-waves-light-blue waves-effect">Cancel</button>
                    <button type="button" onclick="submitViaAjax();" class="btn sad-crimson waves-effect">Save</button>
                </div>
            </form>
        </main>
        <script src="http://localhost/keasyshoppe/js/jquery.min.js"></script>
        <script src="http://localhost/keasyshoppe/js/materialize.min.js"></script>
        <script src="http://localhost/keasyshoppe/js/script.js"></script>
        <script>
            /** 
             * This function displays the selected image to the hoverable card.
             * @argument images The image to be displayed.
             */
            function displayMainImage(images) {
                for (var index = 0; index < images.length; index++) {
                    var image = images[index];
                    var imageType = /^image\//;
                    if (!imageType.test(image.type)) {
                        continue;
                    }
                    var img = document.getElementById('mainImageUserFile');
                    img.file = image;
                    var reader = new FileReader();
                    reader.onload = (function (aimg) {
                        return function (e) {
                            aimg.src = e.target.result;
                        };
                    })(img);
                    reader.readAsDataURL(image);
                    $('#file-button').html('Change');
                    
                }
            }
            var images = 0;
            /** 
             * Displays secondary images of the product that are chosen by the user.
             * @param files an array of files that are to be uploaded
             * @author Francis Rubio
             */
            function displaySecondaryImages(files) {
                for (var index = 0; index < files.length; index++) {
                    var file = files[index];
                    var imageType = /^image\//;
                    if (!imageType.test(file.type)) {
                        continue;
                    }
                    var img = document.createElement('img');
                    img.file = file;
                    var reader = new FileReader();
                    reader.onload = (function (aimg) {
                        return function (e) {
                            aimg.src = e.target.result;
                        }
                    })(img);
                    reader.readAsDataURL(file);
                    var itemId = Date.now();
                    $('.sad-cards-container').append(
                        '<div class="card">' +
                        '<div class="card-image" id="cardImage' + itemId + '">' +
                        '<div class="close"></div>' +
                        '</div>' +
                        '</div>'
                    );
                    $('#cardImage' + itemId).append($(img));
                    console.log($('#secondaryImages').val());
                    uploadImagesViaAjax(file);
                }
            }
            $(document).ready(() => {
                $('.chips').material_chip({
                    placeholder: 'Press Enter to add keyword.',
                    secondaryPlaceholder: 'Press Enter to add',
                    autocompleteOptions: {
                        data: {
                            //Put here every keyword from the database
                        },
                        limit: Infinity,
                        minLength: 1
                    }
                });
                fetchProductsByAjax();
            });
            /**
             * A function that submits the form by AJAX
             * @author Francis Rubio
             */
            function submitViaAjax(){
                $('#preloader').slideDown();
                var prodName = $('#prodName').val();
                var prodPrice = $('#prodPrice').val();
                var prodDesc;
                $.ajax({
                    method: "POST",
                    url: "addproduct.php",
                    dataType: "json",
                    data: {
                        "prodName": $('#prodName').val(),
                        "prodprice": $('#prodPrice').val(),
                        "prodDesc": $('#prodDesc').val()
                    },
                    success: (data) => {
                        Materialize.toast("success: "+data["statusMessage"], 14000);
                        $('#preloader').slideUp();
                    },
                    error: (data) => {
                        Materialize.toast("Something went wrong.");
                        $('#preloader').slideUp();
                        
                    }
                });
            }

            /**
             * A function that uploads an image via AJAX
             * @param file The file to be uploaded.
             * @param progressBar The Materialize progress bar attached to the image card that will give the user information about the upload progress of the image
             */
            function uploadImagesViaAjax(file){
                var uri = './uploadphotos.php';
                var xhr = new XMLHttpRequest();
                var fd = new FormData();

                xhr.open('POST', uri, true);
                xhr.onreadystatechange = function(){
                    if(xhr.readyState == 4 && xhr.status == 200){
                        Materialize.toast("Success!", 4000);
                    }
                };
                fd.append('productPhoto', file);
                xhr.send(fd);
            }

            /**
             * A function that will fetch products by Ajax.
             */
            function fetchProductsByAjax(){
                $.ajax({
                    url: "./fetchproducts.php",
                    method: "POST",
                    dataType: "JSON",
                    success: (data)=>{
                        console.log(data);
                        var div = '<div class="col s12 m4 l3">'+
                        '<div class="card hoverable">'+
                        '<div class="card-image">'+
                        '<img src="'+data['prod_MainImage']+'" >'+
                        '</div>'+
                        '<div class="card-content">'+
                        '<span class="card-title">'+data['prod_name']+'</span>'+
                        '</div>'+
                        '</div>'+
                        '</div>';
                        $('#showProducts').append(div);
                        $('#showProducts').slideDown();
                        $('#emptyState').slideUp();
                    },
                    error: (data)=>{
                        Materialize.toast('Something went wrong. <a href="" class="btn-flat light-blue-text waves-effect waves-light">Learn More</a>', 5000);
                        console.log(data);
                    }
                });
            }
        </script>
    </body>

</html>