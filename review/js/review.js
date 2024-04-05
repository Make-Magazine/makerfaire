// here is our lightbox code
var modal = document.querySelector(".modal");
document.querySelector(".modal").addEventListener("click", function (e) {
    if ((e.target !== modal ) && e.target !== modal.querySelector(".close")) {
        return;    
    } else { 
        modal.classList.remove("show");
    }
});
// this clickListener can handle all elements, even if they haven't loaded on the page yet
function clickListener(event) {
    var element = event.target;
    // do the image modal
    if(element.tagName == 'IMG' && !element.classList.contains("grid-image")) {
        var image = document.querySelector("#imagemodal #imagepreview");
        image.src = element.src;
        modal.classList.add("show")
    }
}
