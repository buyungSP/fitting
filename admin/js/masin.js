$(document).ready(() => {
    $(document).click(a => {
        if(a.target.classList.contains("nav-link")){
            for(b of document.querySelectorAll(".nav-link")){
                b.classList.remove("active")
            }
            a.target.classList.add("active")
        }else if(a.target.parentElement.classList.contains("nav-link")){
            for(b of document.querySelectorAll(".nav-link")){
                b.classList.remove("active")
            }
            a.target.parentElement.classList.add("active")
        }else if(a.target.attributes["data-button"].value === "back"){
            window.history.back()
        }else if(a.target.attributes["data-button"].value === "send"){
            b = {
                section : a.target.attributes["data-section"].value,
                id : a.target.attributes["data-id"].value
            }
            document.querySelector("#loading").innerHTML = '<h1 class="modal-title fs-5" id="staticBackdropLabel">Please wait..!!!</h1><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>'
            $.ajax({
                url: 'save.php?delete',
                type: "post",
                data:  b,
                success: a => {
                    if(a === "error"){
                        document.querySelector("#loading").innerHTML = '<h1 class="modal-title fs-5 text-danger" id="staticBackdropLabel">error</h1>'
                        document.querySelector("#closeModal").removeAttribute("disabled")
                    }else{
                        document.querySelector("#img" + a).remove()
                        document.querySelector("#loading").innerHTML = '<h1 class="modal-title fs-5 text-success" id="staticBackdropLabel">success</h1>'
                        document.querySelector("#closeModal").removeAttribute("disabled")
                    }
                },
                error: (a,b,c) => {
                    document.querySelector("#loading").innerHTML = '<h1 class="modal-title fs-5 text-danger" id="staticBackdropLabel">error</h1>'
                    document.querySelector("#closeModal").removeAttribute("disabled")
                    responseError(a,b,c)
                }
            });
        }else if(a.target.attributes["data-button"].value === "delete"){
            b = document.querySelector("#send")
            b.setAttribute("data-id", a.target.attributes["data-id"].value)
            b.setAttribute("data-section", a.target.attributes["data-section"].value)
        }else if(a.target.attributes["data-button"].value === "phone"){
            $.ajax({
                url: 'save.php?delete=phone',
                type: "post",
                data:  {id:a.target.attributes["data-id"].value},
                success: a => {
                    window.location.reload()
                },
                error: (a,b,c) => {
                    responseError(a,b,c)
                }
            });
        }else if(a.target.attributes["data-button"].value === "menu"){
            $.ajax({
                url: 'save.php?delete=link',
                type: "post",
                data:  {id:a.target.attributes["data-id"].value},
                success: a => {
                    window.location.reload()
                },
                error: (a,b,c) => {
                    responseError(a,b,c)
                }
            });
        }
    })
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle')
    const dropdownList = [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl))
    responseError = (jqXHR, textStatus, errorThrown) => console.log(textStatus, errorThrown)
    $('.dropfile').each(function() {
        var $dropbox = $(this);
        if (typeof window.FileReader === 'undefined') {
            $('small', this).html('File API & FileReader API not supported').addClass('text-danger');
            return;
        }

        this.ondragover = function() { $dropbox.addClass('hover'); return false; };
        this.ondragend = function() { $dropbox.removeClass('hover'); return false; };
        this.ondrop = function(e) {
            e.preventDefault();
            $dropbox.removeClass('hover').html('');
            var file = e.dataTransfer.files[0],
                reader = new FileReader();
            reader.onload = function(event) {
                $dropbox.append($('<img id="imgInput" onload="imgSize(this)" class="col-sm-12">').attr('src', event.target.result));
                $dropbox.append($('<small>Drag and Drop file here</small>'));
            };
            reader.readAsDataURL(file);
            return false;
        };
    });
    post = a => {
        $.ajax({
            url: 'save.php',
            type: "post",
            data:  a,
            success: a => {
                document.querySelector("#loading").innerHTML = '<h1 class="modal-title fs-5 text-success" id="staticBackdropLabel">success</h1>'
                document.querySelector("#closeModal").removeAttribute("disabled")
            },
            error: (a,b,c) => {
                document.querySelector("#loading").innerHTML = '<h1 class="modal-title fs-5 text-danger" id="staticBackdropLabel">error</h1>'
                document.querySelector("#closeModal").removeAttribute("disabled")
                responseError(a,b,c)
            }
        });
    }
    imgSize = a => {
        console.log(a.src,a.naturalWidth,a.naturalHeight)
    }
    getForm = a =>{
        b = new FormData(a)
        b.append("img",a.name === "img"? 1 : 0)
        b.append("section",a.attributes["data-section"].value)
        if(a.hasAttribute("data-id")){
            b.append("id",a.attributes["data-id"].value)
        }
        if(a.name === "img"){
            if(document.querySelector("#imgInput")){
                b.append("file",document.querySelector("#imgInput").src)
                b.append("name",!a.attributes["data-file"].value? Date.now() + ".webp" : a.attributes["data-file"].value)
            }
        }else{
            b.append("edit",a.attributes["data-file"].value)
        }
        d = {}
        for(const c of b){
            d[c[0]] = c[1]
        }
        post(d)
    }
})
