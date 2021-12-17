jsconv.marginLeft = parseInt(jsconv.marginLeft);
jsconv.marginRight = parseInt(jsconv.marginRight);
jsconv.marginTop = parseInt(jsconv.marginTop);
jsconv.marginBottom = parseInt(jsconv.marginBottom);
document.body.classList.add('dce-pdf-printing');
// Reset the browser url so that the version that downloads the pdf isn't shared.
var newURL = window.location.href.replace(/.downloadPDF=.*/g, "");
window.history.replaceState({}, document.title, newURL);
var templateEl = null;
if (jsconv.isTemplate) {
	templateEl = document.getElementById("dce-pdftemplate");
	templateEl.parentElement.removeChild(templateEl);
	// hide all other elements, so that the template is alone in the page.
	let prevBody = document.querySelectorAll('body > *');
	prevBody.forEach((e) => {
		e.dataset.dcePrevDisplay = e.style.display;
		e.style.display = 'none';
	});
	document.body.prepend(templateEl);
}
function pdfBeforeRendering() {}
function pdfAfterRendering() {
	if (templateEl) {
		document.body.removeChild(templateEl);
		let prevBody = document.querySelectorAll('body > *');
		prevBody.forEach((e) => {
			e.style.display = e.dataset.dcePrevDisplay;
		});
	}
	document.body.classList.remove('dce-pdf-printing');
}

let selectedElement = document.querySelector(jsconv.selector);
// Remove responsive images attributes, otherwise html2canvas can crop results
// after changing viewport.
let imgs = selectedElement.querySelectorAll('img');
imgs.forEach((img) => {
	img.removeAttribute("srcset");
	img.removeAttribute("sizes");
});
// Hide the pdf button:
let pdfButton = selectedElement.getElementsByClassName("elementor-button-pdf-wrapper");
if (pdfButton.length > 0) {
		var pdfButtonDisplay = pdfButton[0].style.display;
		pdfButton[0].style.display = 'none';
}
function downloadPDF() {
	if (! selectedElement) {
		alert("Could not find the selected element in the page.");
		return;
	}
	pdfBeforeRendering();
	const doc = new jspdf.jsPDF({
		format: jsconv.pageSize,
		orientation: jsconv.orientation,
		unit: jsconv.marginUnit,
		compress: true
	});
	const pdfWidth = doc.internal.pageSize.getWidth();
	const pdfHeight = doc.internal.pageSize.getHeight();
	const pdfElWidth = pdfWidth - jsconv.marginLeft - jsconv.marginRight;
	const pdfElHeight = pdfHeight - jsconv.marginTop - jsconv.marginBottom;
	html2canvas(selectedElement, { windowWidth: 1024, windowHeight: 768 }).then(canvas => {
		const imgData = canvas.toDataURL('image/png');
		// The following is necesary for the case where canvas is so long that
		// it doesn't fit in one page.
		// https://stackoverflow.com/questions/24069124/how-to-save-a-image-in-multiple-pages-of-pdf-using-jspdf
		// Get canvas height that can be filled in a page, in pixels.
		const pageHeight = pdfElHeight * canvas.width / pdfElWidth;
		for (let hposition = 0; hposition < canvas.height; hposition+=pageHeight) {
			// The final slice height could be less than the pageHeight.
			const sliceHeight = Math.min(canvas.height - hposition, pageHeight);
			const slicePdfHeight = (sliceHeight * pdfElHeight) / pageHeight;
			// In order to get the image slice we have to do the drawing on another canvas.
			const onePageCanvas = document.createElement("canvas");
			onePageCanvas.setAttribute('width', canvas.width);
			onePageCanvas.setAttribute('height', sliceHeight);
			const ctx = onePageCanvas.getContext('2d');
			ctx.drawImage(canvas, 0, hposition, canvas.width, sliceHeight, 0, 0,
				canvas.width, sliceHeight);
			const sliceData = onePageCanvas.toDataURL("image/png");
			// At the beginning of the loop we already have a page.
			if (hposition != 0) {
				doc.addPage();
			}
			doc.addImage( sliceData, '', jsconv.marginLeft,
				jsconv.marginTop, pdfElWidth, slicePdfHeight);
		}
		doc.save(jsconv.title);
	});
}
jQuery(window).trigger('dce/jsconvpdf/before');
downloadPDF();
if (pdfButton.length > 0) {
		pdfButton[0].style.display = pdfButtonDisplay;
}
pdfAfterRendering();
jQuery(window).trigger('dce/jsconvpdf/after');
