document.querySelector("form").addEventListener("submit",function(a) {
    a.preventDefault()
    b = '<h1 class="modal-title fs-5" id="staticBackdropLabel">Please wait..!!!</h1><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>'
    if(a.target.name === "img"){
        if(!a.target.getAttribute("data-id")){
            if(!document.querySelector(("#imgInput"))){
                document.querySelector("#loading").innerHTML = '<h1 class="modal-title fs-5 text-danger" id="staticBackdropLabel">error</h1>'
                document.querySelector("#closeModal").removeAttribute("disabled")
            }else{
                document.querySelector("#loading").innerHTML = b
                getForm(a.target)
            }
        }else{
            document.querySelector("#loading").innerHTML = b
            getForm(a.target)
        }
    }else{
        document.querySelector("#loading").innerHTML = b
        getForm(a.target)
    }
})