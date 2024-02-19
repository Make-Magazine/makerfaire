;(function($) {
	$( document ).ready(function($) {
		const links = document.querySelectorAll('.mpfy-copy-click');
		const cls = {
			copied: 'is-copied',
			hover: 'is-hovered'
		}

		const copyToClipboard = str => {
			const el = document.createElement('input');
			str.dataset.copyString ? el.value = str.dataset.copyString : el.value = str.text;
			el.setAttribute('readonly', '');
			el.style.position = 'absolute';
			el.style.opacity = 0;
			document.body.appendChild(el);
			el.select();
			document.execCommand('copy');
			document.body.removeChild(el);
		}

		const clickInteraction = (e) => {
			e.preventDefault();
			copyToClipboard(e.target);
			e.target.classList.add(cls.copied);
			setTimeout(() => e.target.classList.remove(cls.copied), 1000);
			setTimeout(() => e.target.classList.remove(cls.hover), 700);  
		}
		
		Array.from(links).forEach(link => {
			link.addEventListener('click', e => clickInteraction(e));
			link.addEventListener('keypress', e => {
				if (e.keyCode === 13) clickInteraction(e);
			});
			link.addEventListener('mouseover', e => e.target.classList.add(cls.hover));
			link.addEventListener('mouseleave', e => {
				if (!e.target.classList.contains(cls.copied)) {
				e.target.classList.remove(cls.hover); 
				}
			});
		});
	});	
})(jQuery);